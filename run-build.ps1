$ErrorActionPreference = 'Continue'
cd "C:\Users\VJ_Rodriguguez\Desktop\Repository\architect-ai"

Write-Host "Running Vite production build..."
npm run build

if ($LASTEXITCODE -eq 0) {
    Write-Host "Build completed successfully"
} else {
    Write-Host "Build failed with exit code: $LASTEXITCODE"
}
