while ($true) {
    git add .
    $status = git status --porcelain
    if ($status) {
        git commit -m "Auto commit at $(Get-Date -Format 'HH:mm:ss')"
        git push origin main
    }
    Start-Sleep -Seconds 30
}
