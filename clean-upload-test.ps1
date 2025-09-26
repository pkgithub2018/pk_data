# Test upload script for ephyto.info
$server = "ephyto.info"
$user = "ephytoin"
$remotePath = "/home/ephytoin/public_html"

# Create test file
$testContent = @"
<!DOCTYPE html>
<html>
<head>
    <title>PowerShell Upload Test</title>
</head>
<body>
    <h1>PowerShell Upload Test - SUCCESS!</h1>
    <p>This file was uploaded via PowerShell script.</p>
    <p>Upload time: $(Get-Date)</p>
    <p>If you can see this, the upload method is working!</p>
</body>
</html>
"@

$testContent | Out-File -FilePath "powershell-upload-test.html" -Encoding UTF8

Write-Host "Test file created: powershell-upload-test.html"
Write-Host "Attempting to upload via SCP..."

# Try SCP upload - will prompt for password
& scp -o StrictHostKeyChecking=no powershell-upload-test.html "${user}@${server}:${remotePath}/"

Write-Host "Upload completed. Check https://ephyto.info/powershell-upload-test.html"