<?php
// Simple PHP Error Log Viewer
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>XAMPP Error Log Viewer</title>
    <style>
        body { font-family: monospace; background: #f0f0f0; margin: 20px; }
        .log-container { background: #fff; border: 1px solid #ccc; padding: 15px; border-radius: 5px; }
        .log-entry { margin: 5px 0; padding: 5px; }
        .error { background-color: #ffe6e6; border-left: 4px solid #ff0000; }
        .warning { background-color: #fff3cd; border-left: 4px solid #ffc107; }
        .notice { background-color: #e6f3ff; border-left: 4px solid #007bff; }
        .custom { background-color: #e6ffe6; border-left: 4px solid #28a745; font-weight: bold; }
        h1 { color: #333; }
        .refresh-btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 15px; }
        .refresh-btn:hover { background: #0056b3; }
    </style>
    <script>
        function refreshLogs() {
            location.reload();
        }
        
        // Auto-refresh every 5 seconds
        setTimeout(refreshLogs, 5000);
    </script>
</head>
<body>
    <h1>📋 XAMPP Error Log Viewer</h1>
    <button class="refresh-btn" onclick="refreshLogs()">🔄 Refresh Logs</button>
    <p><em>Auto-refreshes every 5 seconds</em></p>
    
    <div class="log-container">
        <?php
        $error_log_file = "C:\xampp\apache\logs\error.log";
        
        if (file_exists($error_log_file)) {
            $lines = file($error_log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $recent_lines = array_slice($lines, -50); // Last 50 lines
            
            foreach ($recent_lines as $line) {
                $class = 'log-entry';
                
                if (stripos($line, 'error') !== false) {
                    $class .= ' error';
                } elseif (stripos($line, 'warning') !== false) {
                    $class .= ' warning';
                } elseif (stripos($line, 'notice') !== false) {
                    $class .= ' notice';
                }
                
                // Highlight your custom messages
                if (stripos($line, 'AJAX Request') !== false || 
                    stripos($line, 'Processing') !== false || 
                    stripos($line, 'Save inspection data') !== false ||
                    stripos($line, 'Parsed values') !== false) {
                    $class .= ' custom';
                }
                
                echo "<div class='$class'>" . htmlspecialchars($line) . "</div>\n";
            }
        } else {
            echo "<p>❌ Error log file not found at: $error_log_file</p>";
        }
        ?>
    </div>
</body>
</html>