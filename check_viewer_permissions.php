<?php
require("php-bin/connection.php");

echo "=== VIEWER GROUP PERMISSIONS CHECK ===\n\n";

// Check if VIEWER group exists
$sqlGroup = "SELECT * FROM tbusergroup WHERE title = 'VIEWER'";
$resultGroup = pg_query($con, $sqlGroup);
if ($resultGroup && pg_num_rows($resultGroup) > 0) {
    $group = pg_fetch_assoc($resultGroup);
    echo "VIEWER Group Found:\n";
    echo "  ID: " . $group['id'] . "\n";
    echo "  Title: " . $group['title'] . "\n";
    echo "  Enabled: " . $group['enabled'] . "\n\n";
    
    $groupId = $group['id'];
    
    // Get all permissions for VIEWER group
    $sql = "SELECT gp.id, g.title as group_name, m.id as module_id, m.code as module_code, m.title as module_title, 
            gp.pread, gp.padd, gp.pupdate, gp.pdelete, m.enabled as module_enabled
            FROM tbgrouppermits gp 
            INNER JOIN tbusergroup g ON g.id = gp.gid 
            INNER JOIN tbmodules m ON m.id = gp.mid 
            WHERE g.title = 'VIEWER' 
            ORDER BY m.code";
    
    $result = pg_query($con, $sql);
    
    if ($result) {
        echo "VIEWER Group Permissions:\n";
        echo str_pad('ID', 5) . str_pad('Module ID', 12) . str_pad('Module Code', 30) . str_pad('Module Title', 40) . str_pad('Read', 8) . str_pad('Add', 8) . str_pad('Update', 8) . str_pad('Delete', 8) . str_pad('Enabled', 10) . "\n";
        echo str_repeat('-', 140) . "\n";
        
        $count = 0;
        while ($row = pg_fetch_assoc($result)) {
            $count++;
            echo str_pad($row['id'], 5) . 
                 str_pad($row['module_id'], 12) . 
                 str_pad($row['module_code'], 30) . 
                 str_pad($row['module_title'], 40) . 
                 str_pad($row['pread'], 8) . 
                 str_pad($row['padd'], 8) . 
                 str_pad($row['pupdate'], 8) . 
                 str_pad($row['pdelete'], 8) . 
                 str_pad($row['module_enabled'], 10) . "\n";
        }
        
        echo "\nTotal permissions found: $count\n\n";
        
        // Now test specific module lookups that main.php uses
        echo "=== TESTING SPECIFIC MODULE LOOKUPS ===\n\n";
        
        $testModules = [
            'PG-MAIN',
            'APP-ENTITY',
            'APP-INSPECT',
            'APP-CERT',
            'FRM - ENTITY',
            'FRM-MASTER DATA',
            'FRM-USERGROUP',
            'FRM-USERS_PERMIT',
            'FRM-USERS',
            'FRM-MODULES'
        ];
        
        foreach ($testModules as $moduleRef) {
            echo "Testing module: $moduleRef\n";
            
            $moduleEscaped = pg_escape_string($con, $moduleRef);
            $moduleFile = basename($moduleRef);
            $moduleFileEscaped = pg_escape_string($con, $moduleFile);
            $moduleCode = pathinfo($moduleFile, PATHINFO_FILENAME);
            $moduleCodeEscaped = pg_escape_string($con, $moduleCode);
            
            $sqlTest = "SELECT gp.mid, m.code AS module_code, m.title AS module_title, gp.pread, gp.padd, gp.pupdate, gp.pdelete
                      FROM tbgrouppermits gp
                      INNER JOIN tbmodules m ON m.id = gp.mid
                      INNER JOIN tbusergroup g ON g.id = gp.gid
                      WHERE gp.gid='$groupId' 
                      AND (m.code='$moduleEscaped' OR m.title='$moduleEscaped' 
                           OR m.code='$moduleFileEscaped' OR m.title='$moduleFileEscaped' 
                           OR m.code='$moduleCodeEscaped' OR m.title='$moduleCodeEscaped')
                      AND m.enabled='yes' AND g.enabled='yes'
                      LIMIT 1";
            
            $resultTest = pg_query($con, $sqlTest);
            
            if ($resultTest && pg_num_rows($resultTest) > 0) {
                $rowTest = pg_fetch_assoc($resultTest);
                echo "  FOUND: Code={$rowTest['module_code']}, Title={$rowTest['module_title']}, Read={$rowTest['pread']}\n";
            } else {
                echo "  NOT FOUND\n";
            }
        }
        
    } else {
        echo "Error querying permissions: " . pg_last_error($con) . "\n";
    }
    
} else {
    echo "VIEWER group not found in tbusergroup table!\n";
}

pg_close($con);
?>
