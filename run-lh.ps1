$ErrorActionPreference = 'Continue'

$env:EDGE_PATH = "C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe"
$env:TMP = "C:\Users\VJ_Rodriguguez\AppData\Local\Temp"
$env:TEMP = "C:\Users\VJ_Rodriguguez\AppData\Local\Temp"

Write-Host "Environment:"
Write-Host "  EDGE_PATH: $env:EDGE_PATH"
Write-Host "  TMP: $env:TMP"

$lighthouse = "C:\Users\VJ_Rodriguguez\Desktop\Repository\architect-ai\node_modules\lighthouse\cli\index.js"
$url = "http://localhost:8081/dashboard"
$output = "C:\Users\VJ_Rodriguguez\Desktop\Repository\architect-ai\reports\lh-dashboard.html"

Write-Host "Starting Lighthouse audit..."
Write-Host "  URL: $url"
Write-Host "  Output: $output"

node $lighthouse $url `
  --output=html `
  --output-path=$output `
  --chrome-flags="--headless --no-sandbox --disable-gpu --disable-dev-shm-usage" `
  --preset=desktop `
  --quiet

if ($LASTEXITCODE -eq 0) {
    Write-Host "SUCCESS: Lighthouse audit completed"
} else {
    Write-Host "FAILED: Lighthouse audit exited with code $LASTEXITCODE"
}
