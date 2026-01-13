<?php
// Debug page to test UpdateEntityExport function
require("php-bin/connection.php");
require("php-bin/supports.php");

echo "<h2>Debug UpdateEntityExport Function</h2>";

// Check if we have any entities in the database
$sql_check = "SELECT id, title, business_type, entity_type FROM tbentity_export ORDER BY id DESC LIMIT 5";
$result_check = pg_query($con, $sql_check);

echo "<h3>Current Entities in Database:</h3>";
if (pg_num_rows($result_check) > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Title</th><th>Business Type</th><th>Entity Type</th><th>Test Update</th></tr>";
    while ($row = pg_fetch_assoc($result_check)) {
        $id = $row['id'];
        $title = $row['title'];
        $business_type = $row['business_type'];
        $entity_type = $row['entity_type'];
        echo "<tr>";
        echo "<td>$id</td>";
        echo "<td>$title</td>";
        echo "<td>$business_type</td>";
        echo "<td>$entity_type</td>";
        echo "<td><a href='debug_entity_update.php?test_update=$id&uid=admin@admin.com'>Test Update</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No entities found in database.</p>";
}

// Test update functionality
if (isset($_GET['test_update'])) {
    $test_id = $_GET['test_update'];
    $userid = $_GET['uid'] ?? 'admin@admin.com';
    
    echo "<h3>Testing Update for Entity ID: $test_id</h3>";
    
    // First, get the current entity data
    $entityData = EntityExportInfo($test_id, $con);
    if ($entityData) {
        echo "<h4>Current Entity Data:</h4>";
        echo "<pre>" . print_r($entityData, true) . "</pre>";
        
        // Now test the update with modified title
        $new_title = $entityData['title'] . " [Updated " . date('H:i:s') . "]";
        
        echo "<h4>Attempting to update title to: $new_title</h4>";
        
        // Call UpdateEntityExport
        UpdateEntityExport(
            $test_id,
            $entityData['business_type'],
            $entityData['entity_type'],
            $new_title,
            $entityData['address'],
            $entityData['zipcode'],
            $entityData['province'],
            $entityData['district'],
            $entityData['phone'],
            $entityData['email'],
            $entityData['contact_name'],
            $entityData['registered'],
            "'" . $entityData['registered_date_from'] . "'",
            "'" . $entityData['registered_date_to'] . "'",
            $entityData['check_list_registered'],
            $entityData['gap'],
            $entityData['license_export'],
            $userid,
            $con
        );
        
    } else {
        echo "<p>Entity with ID $test_id not found.</p>";
    }
}

echo "<p><a href='entity.php?entity=export&uid=admin@admin.com'>Back to Entity List</a></p>";
?>