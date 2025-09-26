# Upload files maintaining directory structure
param(
    [Parameter(Mandatory=$true)]
    [string]$SourcePath,
    
    [string]$RemoteSubPath = ""
)

$server = "ephyto.info"
$user = "ephytoin"
$basePath = "/home/ephytoin/public_html"
$localRoot = "C:\xampp\htdocs"

if (-not (Test-Path $SourcePath)) {
    Write-Host "Source path not found: $SourcePath" -ForegroundColor Red
    exit 1
}

# Calculate remote path
$relativePath = $SourcePath.Replace($localRoot, "").Replace("\", "/")
if ($relativePath.StartsWith("/")) { $relativePath = $relativePath.Substring(1) }

$remotePath = "$basePath"
if ($RemoteSubPath) {
    $remotePath += "/$RemoteSubPath"
}
if ($relativePath) {
    $remotePath += "/$relativePath"
}

Write-Host "Uploading to: $remotePath" -ForegroundColor Cyan

if (Test-Path $SourcePath -PathType Container) {
    # Upload directory
    Write-Host "Creating remote directory structure..." -ForegroundColor Yellow
    & ssh -o StrictHostKeyChecking=no "${user}@${server}" "mkdir -p '$remotePath'" 2>$null
    
    # Upload all files in directory
    $files = Get-ChildItem -Path $SourcePath -File -Recurse
    foreach ($file in $files) {
        $relativeFilePath = $file.FullName.Replace($SourcePath, "").Replace("\", "/")
        if ($relativeFilePath.StartsWith("/")) { $relativeFilePath = $relativeFilePath.Substring(1) }
        
        $remoteFilePath = "$remotePath/$relativeFilePath"
        $remoteFileDir = Split-Path $remoteFilePath -Parent
        
        # Create remote directory for file
        & ssh -o StrictHostKeyChecking=no "${user}@${server}" "mkdir -p '$remoteFileDir'" 2>$null
        
        Write-Host "Uploading: $($file.Name)" -NoNewline
        & scp -o StrictHostKeyChecking=no "$($file.FullName)" "${user}@${server}:${remoteFilePath}" 2>$null
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host " ✓" -ForegroundColor Green
        } else {
            Write-Host " ✗" -ForegroundColor Red
        }
    }
} else {
    # Upload single file
    $fileName = Split-Path $SourcePath -Leaf
    $remoteFileDir = Split-Path $remotePath -Parent
    
    # Create remote directory
    & ssh -o StrictHostKeyChecking=no "${user}@${server}" "mkdir -p '$remoteFileDir'" 2>$null
    
    Write-Host "Uploading: $fileName" -NoNewline
    & scp -o StrictHostKeyChecking=no "$SourcePath" "${user}@${server}:${remotePath}" 2>$null
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host " ✓" -ForegroundColor Green
        Write-Host "File available at: https://ephyto.info/$RemoteSubPath/$fileName" -ForegroundColor Cyan
    } else {
        Write-Host " ✗" -ForegroundColor Red
    }
}