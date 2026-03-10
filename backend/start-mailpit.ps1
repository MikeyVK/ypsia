param(
    [string]$ConfigPath = (Join-Path $PSScriptRoot 'mailpit.env'),
    [switch]$DryRun
)

$ErrorActionPreference = 'Stop'

$mailpitExe = Join-Path $PSScriptRoot '.tools\mailpit\mailpit.exe'
$dataDir = Join-Path $PSScriptRoot 'data\mailpit'
$localDatabasePath = Join-Path $dataDir 'mailpit.db'

if (-not (Test-Path $mailpitExe)) {
    throw "Mailpit executable not found at $mailpitExe"
}

if (-not (Test-Path $ConfigPath)) {
    throw "Mailpit config not found at $ConfigPath"
}

New-Item -ItemType Directory -Force -Path $dataDir | Out-Null

Get-Content $ConfigPath | ForEach-Object {
    $line = $_.Trim()

    if ($line -eq '' -or $line.StartsWith('#')) {
        return
    }

    $parts = $line.Split('=', 2)
    if ($parts.Count -ne 2) {
        throw "Invalid config line in ${ConfigPath}: $line"
    }

    $name = $parts[0].Trim()
    $value = $parts[1].Trim()
    [Environment]::SetEnvironmentVariable($name, $value, 'Process')
}

if ([string]::IsNullOrWhiteSpace($env:MP_DATABASE) -or $env:MP_DATABASE.StartsWith('/data/')) {
    [Environment]::SetEnvironmentVariable('MP_DATABASE', $localDatabasePath, 'Process')
}

$resolved = [pscustomobject]@{
    Executable = $mailpitExe
    ConfigPath = $ConfigPath
    UiBindAddr = $env:MP_UI_BIND_ADDR
    SmtpBindAddr = $env:MP_SMTP_BIND_ADDR
    Database = $env:MP_DATABASE
}

if ($DryRun) {
    $resolved | Format-List | Out-String | Write-Output
    exit 0
}

Write-Output "Starting Mailpit with config $ConfigPath"
& $mailpitExe
