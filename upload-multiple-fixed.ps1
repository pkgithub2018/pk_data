# Upload multiple files to ephyto.info
param(
    [Parameter(Mandatory=$true)]
    [string[]]$FilePaths
)

$server = "ephyto.info"
$user = "ephytoin"
$remotePath = "/home/ephytoin/public_html"

Write-Host "Uploading $($FilePaths.Count) files to ephyto.info..." -ForegroundColor Yellow

$successCount = 0
$failCount = 0

foreach ($filePath in $FilePaths) {
    if (-not (Test-Path $filePath)) {
        Write-Host "⚠ File not found: $filePath" -ForegroundColor Yellow
        $failCount++
        continue
    }
    
    $fileName = Split-Path $filePath -Leaf
    Write-Host "Uploading: $fileName" -NoNewline
    
    & scp -o StrictHostKeyChecking=no "$filePath" "${user}@${server}:${remotePath}/$fileName" 2>$null
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host " ✓" -ForegroundColor Green
        $successCount++
    } else {
        Write-Host " ✗" -ForegroundColor Red
        $failCount++
    }
}

Write-Host "`nUpload Summary:" -ForegroundColor Cyan
Write-Host "✓ Successful: $successCount" -ForegroundColor Green
Write-Host "✗ Failed: $failCount" -ForegroundColor Red