$ErrorActionPreference = 'Continue'
$tempPath = 'C:\Users\VJ_Rodriguguez\Desktop\Repository\architect-ai\temp'
New-Item -ItemType Directory -Force -Path $tempPath | Out-Null
$env:TEMP = $tempPath
$env:TMP = $tempPath

Write-Host "TEMP set to: $env:TEMP"

node_modules\.bin\lighthouse http://localhost:8081/dashboard `
  --output=html `
  --output-path='C:\Users\VJ_Rodriguguez\Desktop\Repository\architect-ai\reports\baseline-desktop.html' `
  --chrome-flags='--headless --no-sandbox --disable-gpu --disable-dev-shm-usage' `
  --preset=desktop `
  --quiet

if ($LASTEXITCODE -eq 0) {
    Write-Host "Lighthouse audit completed successfully"
} else {
    Write-Host "Lighthouse audit failed with exit code: $LASTEXITCODE"
}
