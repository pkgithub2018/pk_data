<!DOCTYPE html>
<html>
<head>
    <title>PHP Upload Test</title>
</head>
<body>
    <h1>PHP Upload Test</h1>
    <p>Current server time: <?php echo date('Y-m-d H:i:s'); ?></p>
    <p>PHP Version: <?php echo phpversion(); ?></p>
    <p>If you can see the time and PHP version above, then:</p>
    <ul>
        <li>✅ SFTP upload is working</li>
        <li>✅ PHP is working on the server</li>
        <li>✅ File permissions are correct</li>
    </ul>
</body>
</html>