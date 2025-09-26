# Auto-sync script for ephyto.info deployment
# This script watches for file changes and uploads them automatically

$server = "ephyto.info"
$user = "ephytoin"
$remotePath = "/home/ephytoin/public_html"
$localPath = "C:\xampp\htdocs"

# Function to upload a single file
function Upload-File {
    param(
        [string]$FilePath
    )
    
    $relativePath = $FilePath.Replace($localPath, "").Replace("\", "/")
    if ($relativePath.StartsWith("/")) {
        $relativePath = $relativePath.Substring(1)
    }
    
    $remoteFilePath = "$remotePath/$relativePath"
    $remoteDir = Split-Path $remoteFilePath -Parent
    
    Write-Host "Uploading: $FilePath -> $remoteFilePath"
    
    try {
        # Create directory structure if needed
        & ssh -o StrictHostKeyChecking=no "$user@$server" "mkdir -p '$remoteDir'" 2>$null
        
        # Upload file
        & scp -o StrictHostKeyChecking=no "$FilePath" "${user}@${server}:${remoteFilePath}"
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "✓ Successfully uploaded: $relativePath" -ForegroundColor Green
        } else {
            Write-Host "✗ Failed to upload: $relativePath" -ForegroundColor Red
        }
    } catch {
        Write-Host "✗ Error uploading: $relativePath - $($_.Exception.Message)" -ForegroundColor Red
    }
}

# Function to sync all files
function Sync-AllFiles {
    Write-Host "Starting full sync to ephyto.info..." -ForegroundColor Yellow
    
    $filesToSync = Get-ChildItem -Path $localPath -Recurse -File | Where-Object {
        $_.Extension -match '\.(php|html|css|js|png|jpg|jpeg|gif|ico|svg|json|xml|txt|md)$' -and
        $_.FullName -notmatch '\\\.vscode\\' -and
        $_.FullName -notmatch '\\dashboard\\' -and
        $_.FullName -notmatch '\\webalizer\\' -and
        $_.FullName -notmatch '\\xampp\\' -and
        $_.FullName -notmatch '\\schemas\\' -and
        $_.Name -notmatch '^(upload-test|clean-upload-test|ftp-simple-test|powershell.*test).*'
    }
    
    $totalFiles = $filesToSync.Count
    $currentFile = 0
    
    foreach ($file in $filesToSync) {
        $currentFile++
        Write-Host "[$currentFile/$totalFiles] " -NoNewline
        Upload-File -FilePath $file.FullName
    }
    
    Write-Host "Full sync completed!" -ForegroundColor Green
}

# Function to watch for file changes
function Start-FileWatcher {
    Write-Host "Starting file watcher for auto-sync..." -ForegroundColor Yellow
    Write-Host "Press Ctrl+C to stop watching"
    
    $watcher = New-Object System.IO.FileSystemWatcher
    $watcher.Path = $localPath
    $watcher.Filter = "*.*"
    $watcher.IncludeSubdirectories = $true
    $watcher.EnableRaisingEvents = $true
    
    # Define what to do when a file is changed
    $action = {
        $path = $Event.SourceEventArgs.FullPath
        $changeType = $Event.SourceEventArgs.ChangeType
        
        # Filter files we want to sync
        if ($path -match '\.(php|html|css|js|png|jpg|jpeg|gif|ico|svg|json|xml|txt|md)$' -and
            $path -notmatch '\\\.vscode\\' -and
            $path -notmatch '\\dashboard\\' -and
            $path -notmatch '\\webalizer\\' -and
            $path -notmatch '\\xampp\\' -and
            $path -notmatch '\\schemas\\' -and
            $(Split-Path $path -Leaf) -notmatch '^(upload-test|clean-upload-test|ftp-simple-test|powershell.*test).*') {
            
            Write-Host "File ${changeType}: $path"
            Start-Sleep -Seconds 1  # Wait a moment for file to be fully written
            Upload-File -FilePath $path
        }
    }
    
    Register-ObjectEvent -InputObject $watcher -EventName "Changed" -Action $action
    Register-ObjectEvent -InputObject $watcher -EventName "Created" -Action $action
    Register-ObjectEvent -InputObject $watcher -EventName "Renamed" -Action $action
    
    try {
        while ($true) {
            Start-Sleep -Seconds 1
        }
    } finally {
        $watcher.EnableRaisingEvents = $false
        $watcher.Dispose()
    }
}

# Main menu
Write-Host "Ephyto.info Auto-Sync Tool" -ForegroundColor Cyan
Write-Host "=========================="
Write-Host "1. Full sync (upload all files)"
Write-Host "2. Start file watcher (auto-sync on changes)"
Write-Host "3. Test upload (single file)"

$choice = Read-Host "Enter your choice (1-3)"

switch ($choice) {
    "1" { Sync-AllFiles }
    "2" { Start-FileWatcher }
    "3" { 
        $testFile = "sync-test.html"
        "<!DOCTYPE html><html><head><title>Sync Test</title></head><body><h1>Auto-Sync Test - $(Get-Date)</h1></body></html>" | Out-File -FilePath $testFile -Encoding UTF8
        Upload-File -FilePath (Get-Item $testFile).FullName
        Write-Host "Test file uploaded. Check: https://ephyto.info/$testFile"
    }
    default { Write-Host "Invalid choice" -ForegroundColor Red }
}