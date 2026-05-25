<?php 

require("php-bin/connection.php");
// Prevent PHP warnings from being sent to the HTTP response (keeps JSON responses valid)
@ini_set('display_errors', '0');
@ini_set('display_startup_errors', '0');
error_reporting(E_ALL);
//require("php-bin/supports.php");

// Handle save multiple products request
if (isset($_GET['action']) && $_GET['action'] === 'save_multiple_products') {
    header('Content-Type: application/json');
    
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Log incoming data for debugging
    error_log("Save multiple products - Raw input: " . $input);
    error_log("Save multiple products - Decoded data: " . print_r($data, true));
    
    if (!$data || !isset($data['appid']) || !isset($data['products'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid data received',
            'debug' => [
                'hasData' => !empty($data),
                'hasAppid' => isset($data['appid']),
                'hasProducts' => isset($data['products'])
            ]
        ]);
        exit;
    }
    
    $appid = $data['appid'];
    // Validate appid
    if ($appid === null || $appid === '' || !is_numeric($appid)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid application id'
        ]);
        exit;
    }
    $products = $data['products'];
    
    if (empty($products)) {
        echo json_encode([
            'success' => false,
            'message' => 'No products to save'
        ]);
        exit;
    }
    
    // Start transaction
    $beginResult = pg_query($con, "BEGIN");
    if (!$beginResult) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to start transaction: ' . pg_last_error($con)
        ]);
        exit;
    }
    
    try {
        // Delete existing products for this application
        $sqlDelete = "DELETE FROM tbmultiple_product WHERE application_id = $1";
        $deleteResult = pg_query_params($con, $sqlDelete, [$appid]);
        
        if (!$deleteResult) {
            throw new Exception('Failed to delete existing products: ' . pg_last_error($con));
        }
        
        error_log("Deleted existing products for appid: $appid");
        
        // Insert new products
        $successCount = 0;
        foreach ($products as $index => $product) {
            // Validate product data - check if fields exist and are not empty
            if (empty($product['productId']) || empty($product['unitId'])) {
                error_log("Product at index $index missing required fields: " . print_r($product, true));
                continue;
            }
            
            // Convert empty strings to appropriate values for numeric fields
            $netQuantity = (isset($product['netQuantity']) && $product['netQuantity'] !== '') 
                ? floatval($product['netQuantity']) : 0;
            $grossQuantity = (isset($product['grossQuantity']) && $product['grossQuantity'] !== '') 
                ? floatval($product['grossQuantity']) : 0;
            
            $sqlInsert = "INSERT INTO tbmultiple_product 
                         (application_id, product_id, number_description, quantity_net, quantity_gross, unit_id) 
                         VALUES ($1, $2, $3, $4, $5, $6)";
            
            $result = pg_query_params($con, $sqlInsert, [
                $appid,
                intval($product['productId']),
                $product['numberDescription'] ?? '',
                $netQuantity,
                $grossQuantity,
                intval($product['unitId'])
            ]);
            
            if ($result) {
                $successCount++;
                error_log("Inserted product $successCount: " . $product['productId']);
            } else {
                $error = pg_last_error($con);
                error_log("Failed to insert product at index $index: $error");
                throw new Exception("Failed to insert product: $error");
            }
        }
        
        // Update application multi_item flag
        $updateSql = "UPDATE tbapplication SET multi_item = '1' WHERE id = $1";
        $updateResult = pg_query_params($con, $updateSql, [$appid]);
        
        if (!$updateResult) {
            error_log("Warning: Failed to update multi_item flag: " . pg_last_error($con));
        }
        
        // Commit transaction
        $commitResult = pg_query($con, "COMMIT");
        
        if (!$commitResult) {
            throw new Exception('Failed to commit transaction: ' . pg_last_error($con));
        }
        
        error_log("Successfully saved $successCount products for appid: $appid");
        
        echo json_encode([
            'success' => true,
            'message' => "Successfully saved $successCount products",
            'count' => $successCount
        ]);
        
    } catch (Exception $e) {
        // Rollback on error
        pg_query($con, "ROLLBACK");
        
        error_log("Error saving products: " . $e->getMessage());
        
        echo json_encode([
            'success' => false,
            'message' => 'Error saving products: ' . $e->getMessage()
        ]);
    }
    
    exit;
}

// Handle delete single product request
if (isset($_GET['action']) && $_GET['action'] === 'delete_multiple_product') {
    header('Content-Type: application/json');
    
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || !isset($data['id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid data received'
        ]);
        exit;
    }
    
    $id = $data['id'];
    
    try {
        $sqlDelete = "DELETE FROM tbmultiple_product WHERE id = $1";
        $result = pg_query_params($con, $sqlDelete, [$id]);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete product'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error deleting product: ' . $e->getMessage()
        ]);
    }
    
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code       = $_POST['product_code'] ?? '';
    $name       = $_POST['product_name'] ?? '';
    $scientific = $_POST['scientific_name'] ?? '';
    $desc       = $_POST['product_desc'] ?? '';
    $hs         = $_POST['hs_code'] ?? '';
    $group      = $_POST['product_group'] ?? '';

    header('Content-Type: application/json');

    // Check for duplicate code
    $sqlproduct = "SELECT code FROM tbproduct WHERE code = $1";
    $result = pg_query_params($con, $sqlproduct, [$code]);
    if (pg_num_rows($result) > 0) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Product code already exists. Please choose a different product code.'
        ]);
        exit;
    }

    // Insert new product
    $sqladdproduct = "INSERT INTO tbproduct (code, name, name_scientific, \"desc\", hscode, productgroup, enabled)
                      VALUES ($1, $2, $3, $4, $5, $6, 'yes') RETURNING id";
    $result = pg_query_params($con, $sqladdproduct, [$code, $name, $scientific, $desc, $hs, $group]);

    if ($result) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'New product is added',
            'name'    => $name,
            'code'    => $code,
            'scientific' => $scientific,
            'desc'       => $desc,
            'hs'         => $hs,
            'group'      => $group
        ]);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Failed to add product'
        ]);
    }
}

?>