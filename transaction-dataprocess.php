<?php 

require("php-bin/connection.php");
//require("php-bin/supports.php");

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