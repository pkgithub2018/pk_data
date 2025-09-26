# Auto-sync script for ephyto.info deployment
param(
    [string]$Action = "menu"
)

$server = "ephyto.info"
$user = "ephytoin"
$remotePath = "/home/ephytoin/public_html"
$localPath = "C:\xampp\htdocs"

function Upload-SingleFile {
    param([string]$FilePath)
    
    $relativePath = $FilePath.Replace($localPath, "").Replace("\", "/")
    if ($relativePath.StartsWith("/")) {
        $relativePath = $relativePath.Substring(1)
    }
    
    $remoteFilePath = "$remotePath/$relativePath"
    $remoteDir = Split-Path $remoteFilePath -Parent
    
    Write-Host "Uploading: $FilePath -> $remoteFilePath"
    
    try {
        # Create directory structure if needed
        & ssh -o StrictHostKeyChecking=no "${user}@${server}" "mkdir -p '$remoteDir'" 2>$null
        
        # Upload file
        & scp -o StrictHostKeyChecking=no "$FilePath" "${user}@${server}:${remoteFilePath}"
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "Successfully uploaded: $relativePath" -ForegroundColor Green
        } else {
            Write-Host "Failed to upload: $relativePath" -ForegroundColor Red
        }
    } catch {
        Write-Host "Error uploading: $relativePath - $($_.Exception.Message)" -ForegroundColor Red
    }
}

function Test-Upload {
    $testFile = "sync-test.html"
    $testContent = @"
<!DOCTYPE html>
<html>
<head>
    <title>Sync Test</title>
</head>
<body>
    <h1>Auto-Sync Test - $(Get-Date)</h1>
    <p>This file was uploaded by the auto-sync script.</p>
</body>
</html>
"@
    
    $testContent | Out-File -FilePath $testFile -Encoding UTF8
    Upload-SingleFile -FilePath (Get-Item $testFile).FullName
    Write-Host "Test file uploaded. Check: https://ephyto.info/$testFile"
}

function Sync-AllFiles {
    Write-Host "Starting full sync to ephyto.info..." -ForegroundColor Yellow
    
    $filesToSync = Get-ChildItem -Path $localPath -Recurse -File | Where-Object {
        $_.Extension -match '\.(php|html|css|js|png|jpg|jpeg|gif|ico|svg|json|xml|txt|md)$' -and
        $_.FullName -notmatch '\\\.vscode\\' -and
        $_.FullName -notmatch '\\dashboard\\' -and
        $_.FullName -notmatch '\\webalizer\\' -and
        $_.FullName -notmatch '\\xampp\\' -and
        $_.FullName -notmatch '\\schemas\\' -and
        $_.Name -notmatch '^(upload-test|clean-upload-test|ftp-simple-test|powershell.*test|sync-test).*'
    }
    
    $totalFiles = $filesToSync.Count
    $currentFile = 0
    
    foreach ($file in $filesToSync) {
        $currentFile++
        Write-Host "[$currentFile/$totalFiles] " -NoNewline
        Upload-SingleFile -FilePath $file.FullName
    }
    
    Write-Host "Full sync completed!" -ForegroundColor Green
}

# Execute based on action
switch ($Action) {
    "test" { Test-Upload }
    "sync" { Sync-AllFiles }
    "menu" {
        Write-Host "Ephyto.info Auto-Sync Tool" -ForegroundColor Cyan
        Write-Host "=========================="
        Write-Host "Run with parameters:"
        Write-Host "  -Action test   : Test upload single file"
        Write-Host "  -Action sync   : Full sync all files"
    }
}