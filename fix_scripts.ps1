$files = @(
  "sections/intro.php",
  "sections/profile-matches.php",
  "sections/how-it-works.php",
  "sections/why-bestlife.php",
  "sections/emotional.php",
  "sections/for-families.php",
  "sections/faq.php",
  "sections/advertise.php",
  "sections/marquee.php",
  "sections/marquee2.php"
)

foreach ($f in $files) {
  Write-Host "=== $f ==="
  (Get-Content $f) | ForEach-Object { Write-Host $_ }
  Write-Host "`n"
}