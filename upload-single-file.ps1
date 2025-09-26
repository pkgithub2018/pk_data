# Upload a single file to ephyto.info
param(
    [Parameter(Mandatory=$true)]
    [string]$FilePath
)

$server = "ephyto.info"
$user = "ephytoin"
$remotePath = "/home/ephytoin/public_html"

# Check if file exists
if (-not (Test-Path $FilePath)) {
    Write-Host "File not found: $FilePath" -ForegroundColor Red
    exit 1
}

# Get the filename
$fileName = Split-Path $FilePath -Leaf

Write-Host "Uploading $fileName to ephyto.info..." -ForegroundColor Yellow

# Upload using SCP
& scp -o StrictHostKeyChecking=no "$FilePath" "${user}@${server}:${remotePath}/$fileName"

if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Upload successful!" -ForegroundColor Green
    Write-Host "File available at: https://ephyto.info/$fileName" -ForegroundColor Cyan
} else {
    Write-Host "✗ Upload failed!" -ForegroundColor Red
}