<?php
// BestLife Matrimony — Real watermark helper (GD)
// Overlays the project logo onto profile photos so saved/shipped images carry the mark.
// Used at upload time (profile.php) and optionally for on-the-fly rendering.

function watermark_apply_to_raw(string $raw, string $mime): array {
  // Returns [watermarkedRaw, mime] or original if GD/logo unavailable.
  if (!extension_loaded('gd') || $raw === '') return [$raw, $mime];
  $logoPath = __DIR__ . '/../assets/images/logo.png';
  if (!file_exists($logoPath)) return [$raw, $mime];

  $src = @imagecreatefromstring($raw);
  if (!$src) return [$raw, $mime];
  $srcW = imagesx($src);
  $srcH = imagesy($src);
  if ($srcW < 80 || $srcH < 80) {
    imagedestroy($src);
    return [$raw, $mime];
  }

  $logo = @imagecreatefrompng($logoPath);
  if (!$logo) { imagedestroy($src); return [$raw, $mime]; }
  $logoW = imagesx($logo);
  $logoH = imagesy($logo);
  if ($logoW <= 0 || $logoH <= 0) { imagedestroy($src); imagedestroy($logo); return [$raw, $mime]; }

  // Target size: ~28% of photo width, clamped 60..180px wide
  $targetW = (int) round($srcW * 0.28);
  if ($targetW < 60) $targetW = 60;
  if ($targetW > 180) $targetW = 180;
  $targetH = (int) round($logoH * ($targetW / $logoW));

  // Ensure watermark not taller than 18% of photo height
  $maxH = (int) round($srcH * 0.18);
  if ($targetH > $maxH && $maxH > 0) {
    $targetH = $maxH;
    $targetW = (int) round($logoW * ($targetH / $logoH));
  }

  // Resize logo with alpha preserved
  $resized = imagecreatetruecolor($targetW, $targetH);
  imagealphablending($resized, false);
  imagesavealpha($resized, true);
  $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
  imagefilledrectangle($resized, 0, 0, $targetW, $targetH, $transparent);
  imagecopyresampled($resized, $logo, 0, 0, 0, 0, $targetW, $targetH, $logoW, $logoH);
  imagedestroy($logo);

  // Reduce opacity to ~38% visible (62% transparent) — adjust alpha channel
  // GD alpha: 0 = opaque, 127 = fully transparent.
  $opacity = 38; // 0-100 visible
  $opacityFactor = $opacity / 100;
  for ($x = 0; $x < $targetW; $x++) {
    for ($y = 0; $y < $targetH; $y++) {
      $col = imagecolorat($resized, $x, $y);
      $alpha = ($col >> 24) & 0x7F;
      if ($alpha >= 127) continue; // fully transparent pixel — keep as is
      // New alpha = 127 - (127 - oldAlpha) * opacityFactor
      // e.g. opaque pixel (0) with 38% => 127 -127*0.38 = ~79 (more transparent)
      $newAlpha = (int) round(127 - (127 - $alpha) * $opacityFactor);
      if ($newAlpha < 0) $newAlpha = 0;
      if ($newAlpha > 127) $newAlpha = 127;
      $r = ($col >> 16) & 0xFF;
      $g = ($col >> 8) & 0xFF;
      $b = $col & 0xFF;
      $newCol = imagecolorallocatealpha($resized, $r, $g, $b, $newAlpha);
      imagesetpixel($resized, $x, $y, $newCol);
    }
  }

  // Position: bottom-right with 10px margin + slight top/left safe zone
  $margin = 10;
  $dstX = $srcW - $targetW - $margin;
  $dstY = $srcH - $targetH - $margin;
  if ($dstX < 0) $dstX = 0;
  if ($dstY < 0) $dstY = 0;

  // Add subtle shadow behind logo for legibility on light photos
  // Draw a semi-transparent dark rounded-rect behind watermark (lightly)
  // Create shadow layer same size + 8px padding
  $shadowPad = 6;
  $shadow = imagecreatetruecolor($targetW + $shadowPad*2, $targetH + $shadowPad*2);
  imagealphablending($shadow, false);
  imagesavealpha($shadow, true);
  $shadowTrans = imagecolorallocatealpha($shadow, 0, 0, 0, 127);
  imagefilledrectangle($shadow, 0, 0, $targetW + $shadowPad*2, $targetH + $shadowPad*2, $shadowTrans);
  // Shadow color: black 22% opacity -> alpha ~99
  $shadowCol = imagecolorallocatealpha($shadow, 0, 0, 0, 100);
  // Rounded rect approximation: filled rect with slight alpha
  imagefilledrectangle($shadow, 0, 0, $targetW + $shadowPad*2, $targetH + $shadowPad*2, $shadowCol);
  // Copy shadow first (slightly offset), then logo on top
  imagealphablending($src, true);
  imagesavealpha($src, true);
  // Blend shadow (very light)
  imagecopy($src, $shadow, $dstX - (int)($shadowPad/2), $dstY - (int)($shadowPad/2), 0, 0, $targetW + $shadowPad*2, $targetH + $shadowPad*2);
  imagedestroy($shadow);

  // Overlay logo (corner only — centered watermark removed per requirement)
  imagecopy($src, $resized, $dstX, $dstY, 0, 0, $targetW, $targetH);
  imagedestroy($resized);

  // Encode back to same mime (fallback to jpeg)
  $outMime = $mime;
  if (stripos($mime, 'png') !== false) $outMime = 'image/png';
  elseif (stripos($mime, 'webp') !== false && function_exists('imagewebp')) $outMime = 'image/webp';
  else $outMime = 'image/jpeg';

  ob_start();
  $ok = false;
  if ($outMime === 'image/png') {
    imagealphablending($src, false); imagesavealpha($src, true);
    $ok = imagepng($src, null, 6);
  } elseif ($outMime === 'image/webp' && function_exists('imagewebp')) {
    $ok = imagewebp($src, null, 82);
  } else {
    // JPEG: flatten onto white background if src has alpha
    $bg = imagecreatetruecolor($srcW, $srcH);
    $white = imagecolorallocate($bg, 255,255,255);
    imagefilledrectangle($bg, 0,0,$srcW,$srcH,$white);
    imagecopy($bg, $src, 0,0,0,0,$srcW,$srcH);
    $ok = imagejpeg($bg, null, 85);
    imagedestroy($bg);
    $outMime = 'image/jpeg';
  }
  $data = ob_get_clean();
  imagedestroy($src);
  if (!$ok || $data === '' || $data === false) return [$raw, $mime];
  return [$data, $outMime];
}

function watermark_data_uri(string $dataUri): string {
  if (strpos($dataUri, 'data:') !== 0) return $dataUri;
  $comma = strpos($dataUri, ',');
  if ($comma === false) return $dataUri;
  $meta = substr($dataUri, 5, $comma - 5); // e.g. image/jpeg;base64
  $b64 = substr($dataUri, $comma + 1);
  $raw = base64_decode($b64, true);
  if ($raw === false) return $dataUri;
  $mime = 'image/jpeg';
  if (preg_match('#^image/[^;]+#', $meta, $m)) $mime = $m[0];
  [$newRaw, $newMime] = watermark_apply_to_raw($raw, $mime);
  if ($newRaw === $raw) return $dataUri; // no change
  return 'data:' . $newMime . ';base64,' . base64_encode($newRaw);
}
