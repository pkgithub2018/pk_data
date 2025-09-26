$serverHost = "ephyto.info"
$username = "ephytoin"
$password = "pkCpanel@2025"
$remotePath = "/home/ephytoin/public_html"

# Create a simple test file with timestamp
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

$testContent | Out-File -FilePath "powershell-test.html" -Encoding UTF8

Write-Host "Test file created: powershell-test.html"
Write-Host "Attempting to upload via SCP..."

# Try SCP upload
& scp -o StrictHostKeyChecking=no powershell-test.html "${username}@${serverHost}:${remotePath}/"

Write-Host "Upload attempt completed. Check https://ephyto.info/powershell-test.html"