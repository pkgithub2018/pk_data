<?php
require("php-bin/connection.php");

echo "=== CHECKING MODULE CODES IN DATABASE ===\n\n";

// Get all module codes
$sql = "SELECT id, code, title, enabled FROM tbmodules ORDER BY code";
$result = pg_query($con, $sql);

if ($result) {
    echo str_pad('ID', 5) . str_pad('Code', 35) . str_pad('Title', 50) . str_pad('Enabled', 10) . "\n";
    echo str_repeat('-', 100) . "\n";
    
    while ($row = pg_fetch_assoc($result)) {
        echo str_pad($row['id'], 5) . 
             str_pad($row['code'], 35) . 
             str_pad($row['title'], 50) . 
             str_pad($row['enabled'], 10) . "\n";
    }
}

echo "\n\n=== CHECKING WHAT main.php IS LOOKING FOR ===\n\n";

$checksInMainPhp = [
    'PG-MAIN' => 'Main Dashboard',
    'APP-ENTITY' => 'Application (Entity related?)',
    'APP-INSPECT' => 'Inspection',
    'APP-CERT' => 'Certificate',
    'FRM - ENTITY' => 'Entity Form',
    'FRM-MASTER DATA' => 'Master Data (without space)',
    'FRM-USERGROUP' => 'User Group',
    'FRM-USERS_PERMIT' => 'User Permits',
    'FRM-USERS' => 'Users',
    'FRM-MODULES' => 'Modules'
];

foreach ($checksInMainPhp as $code => $description) {
    echo "Looking for: " . str_pad($code, 25) . " ($description)\n";
    
    $codeEsc = pg_escape_string($con, $code);
    $sqlCheck = "SELECT id, code, title FROM tbmodules WHERE code = '$codeEsc' OR title = '$codeEsc'";
    $resultCheck = pg_query($con, $sqlCheck);
    
    if ($resultCheck && pg_num_rows($resultCheck) > 0) {
        $row = pg_fetch_assoc($resultCheck);
        echo "  FOUND: ID={$row['id']}, Code={$row['code']}, Title={$row['title']}\n";
    } else {
        echo "  NOT FOUND in tbmodules\n";
        
        // Try with space normalization
        $codeWithSpaces = str_replace('-', ' - ', $code);
        if ($codeWithSpaces !== $code) {
            $codeWithSpacesEsc = pg_escape_string($con, $codeWithSpaces);
            $sqlCheck2 = "SELECT id, code, title FROM tbmodules WHERE code = '$codeWithSpacesEsc' OR title = '$codeWithSpacesEsc'";
            $resultCheck2 = pg_query($con, $sqlCheck2);
            
            if ($resultCheck2 && pg_num_rows($resultCheck2) > 0) {
                $row2 = pg_fetch_assoc($resultCheck2);
                echo "  FOUND WITH SPACES: ID={$row2['id']}, Code={$row2['code']}, Title={$row2['title']}\n";
            }
        }
    }
}

pg_close($con);
?>
