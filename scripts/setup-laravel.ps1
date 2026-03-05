param(
    [string]$LaravelVersion = "^10.0"
)

$ErrorActionPreference = "Stop"

$projectRoot = Split-Path -Parent $PSScriptRoot
$appPath = Join-Path $projectRoot "lifelink-app"

if (-not (Test-Path $appPath)) {
    New-Item -ItemType Directory -Path $appPath | Out-Null
}

$hasFiles = (Get-ChildItem -Path $appPath -Force | Measure-Object).Count -gt 0
if ($hasFiles) {
    Write-Host "lifelink-app already exists and is not empty. Skipping Laravel bootstrap."
    exit 0
}

$cmd = "composer create-project laravel/laravel lifelink-app $LaravelVersion"

docker run --rm -v "${projectRoot}:/workspace" -w /workspace composer:2 sh -lc $cmd
