<?php
require_once __DIR__ . '/bootstrap.php';

set_time_limit(0);

$isCli = PHP_SAPI === 'cli';
$dryRun = false;

if ($isCli) {
    $args = $argv ?? [];
    $dryRun = in_array('--dry-run', $args, true);
} else {
    $dryRun = isset($_GET['dry']) && $_GET['dry'] === '1';
    header('Content-Type: text/plain; charset=utf-8');
}

if (!function_exists('create_watermarked_variant')) {
    echo "Virhe: create_watermarked_variant()-funktiota ei löydy.\n";
    exit(1);
}

$db = Database::getInstance()->getConnection();
$stmt = $db->query('SELECT id, auction_id, image_path FROM auction_images ORDER BY id ASC');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = count($rows);
$updated = 0;
$generatedWatermarks = 0;
$generatedMinis = 0;
$createdOriginalCopies = 0;
$alreadyWatermarked = 0;
$skippedRemote = 0;
$skippedMissing = 0;
$failed = 0;

function to_abs_path(string $imagePath): string
{
    $relative = ltrim($imagePath, '/');
    return rtrim(BASE_PATH, '/') . '/' . $relative;
}

function to_public_path(string $absPath): string
{
    $normalizedBase = rtrim(str_replace('\\', '/', BASE_PATH), '/');
    $normalizedAbs = str_replace('\\', '/', $absPath);
    if (str_starts_with($normalizedAbs, $normalizedBase)) {
        return '/' . ltrim(substr($normalizedAbs, strlen($normalizedBase)), '/');
    }
    return $normalizedAbs;
}

function is_remote_path(string $path): bool
{
    return (bool)preg_match('#^https?://#i', $path);
}

echo "fix_imgs.php käynnistyi" . ($dryRun ? " (dry-run)" : "") . "\n";
echo "Kuvia yhteensä: {$total}\n\n";

$updateStmt = $db->prepare('UPDATE auction_images SET image_path = ? WHERE id = ?');

foreach ($rows as $row) {
    $id = (int)($row['id'] ?? 0);
    $imagePath = trim((string)($row['image_path'] ?? ''));

    if ($id <= 0 || $imagePath === '') {
        $failed++;
        echo "[ID {$id}] Ohitettu: puuttuva ID tai image_path\n";
        continue;
    }

    if (is_remote_path($imagePath)) {
        $skippedRemote++;
        echo "[ID {$id}] Ohitettu: etä-URL ({$imagePath})\n";
        continue;
    }

    $absPath = to_abs_path($imagePath);
    if (!is_file($absPath)) {
        $skippedMissing++;
        echo "[ID {$id}] Ohitettu: tiedostoa ei löytynyt ({$absPath})\n";
        continue;
    }

    $dir = dirname($absPath);
    $fileName = basename($absPath);
    $ext = strtolower((string)pathinfo($fileName, PATHINFO_EXTENSION));
    $nameNoExt = (string)pathinfo($fileName, PATHINFO_FILENAME);

    if ($ext === '') {
        $failed++;
        echo "[ID {$id}] Virhe: tiedoston pääte puuttuu ({$fileName})\n";
        continue;
    }

    $isWm = str_contains($nameNoExt, '_wm');

    if ($isWm) {
        $alreadyWatermarked++;

        $origNameNoExt = preg_replace('/_wm$/', '_orig', $nameNoExt) ?: $nameNoExt . '_orig';
        $origAbsPath = $dir . '/' . $origNameNoExt . '.' . $ext;
        $minAbsPath = $dir . '/' . (preg_replace('/_wm$/', '_min', $nameNoExt) ?: ($nameNoExt . '_min')) . '.' . $ext;

        if (!is_file($origAbsPath)) {
            if ($dryRun) {
                echo "[ID {$id}] Dry-run: luotaisiin puuttuva _orig kopio ({$origAbsPath})\n";
                $createdOriginalCopies++;
            } else {
                if (copy($absPath, $origAbsPath)) {
                    $createdOriginalCopies++;
                    echo "[ID {$id}] Luotu puuttuva _orig kopio\n";
                } else {
                    $failed++;
                    echo "[ID {$id}] Virhe: _orig kopion luonti epäonnistui\n";
                }
            }
        }

        if ($dryRun) {
            echo "[ID {$id}] Dry-run: regeneroitaisiin vesileima ({$absPath})\n";
            $generatedWatermarks++;
        } else {
            if (!create_watermarked_variant($origAbsPath, $absPath)) {
                $failed++;
                echo "[ID {$id}] Virhe: vesileiman uudelleengenerointi epäonnistui\n";
                continue;
            }
            $generatedWatermarks++;
        }

        if ($dryRun) {
            echo "[ID {$id}] Dry-run: luotaisiin/päivitettäisiin _min ({$minAbsPath})\n";
            $generatedMinis++;
        } else {
            if (!create_listing_thumbnail($origAbsPath, $minAbsPath)) {
                $failed++;
                echo "[ID {$id}] Virhe: _min kuvan luonti epäonnistui\n";
                continue;
            }
            $generatedMinis++;
        }

        continue;
    }

    $origNameNoExt = str_ends_with($nameNoExt, '_orig') ? $nameNoExt : ($nameNoExt . '_orig');
    $wmNameNoExt = preg_replace('/_orig$/', '_wm', $origNameNoExt) ?: ($origNameNoExt . '_wm');

    $origAbsPath = $dir . '/' . $origNameNoExt . '.' . $ext;
    $wmAbsPath = $dir . '/' . $wmNameNoExt . '.' . $ext;

    if (!is_file($origAbsPath)) {
        if ($dryRun) {
            echo "[ID {$id}] Dry-run: luotaisiin _orig ({$origAbsPath})\n";
            $createdOriginalCopies++;
        } else {
            if (!copy($absPath, $origAbsPath)) {
                $failed++;
                echo "[ID {$id}] Virhe: _orig kopion luonti epäonnistui\n";
                continue;
            }
            $createdOriginalCopies++;
        }
    }

    if ($dryRun) {
        echo "[ID {$id}] Dry-run: generoidaan vesileima {$wmAbsPath}\n";
        $generatedWatermarks++;
    } else {
        if (!create_watermarked_variant($origAbsPath, $wmAbsPath)) {
            $failed++;
            echo "[ID {$id}] Virhe: vesileimakuvan luonti epäonnistui\n";
            continue;
        }
        $generatedWatermarks++;
    }

    $minAbsPath = $dir . '/' . (preg_replace('/_orig$/', '_min', $origNameNoExt) ?: ($origNameNoExt . '_min')) . '.' . $ext;
    if ($dryRun) {
        echo "[ID {$id}] Dry-run: generoidaan _min {$minAbsPath}\n";
        $generatedMinis++;
    } else {
        if (!create_listing_thumbnail($origAbsPath, $minAbsPath)) {
            $failed++;
            echo "[ID {$id}] Virhe: _min kuvan luonti epäonnistui\n";
            continue;
        }
        $generatedMinis++;
    }

    $newPublicPath = to_public_path($wmAbsPath);

    if ($imagePath !== $newPublicPath) {
        if ($dryRun) {
            echo "[ID {$id}] Dry-run: päivitettäisiin DB-polku -> {$newPublicPath}\n";
            $updated++;
        } else {
            $updateStmt->execute([$newPublicPath, $id]);
            $updated++;
            echo "[ID {$id}] Päivitetty DB-polku -> {$newPublicPath}\n";
        }
    } else {
        echo "[ID {$id}] Ei muutosta (polku jo oikein)\n";
    }
}

echo "\n=== Yhteenveto ===\n";
echo "Kuvia käsitelty: {$total}\n";
echo "DB-päivitykset: {$updated}\n";
echo "Vesileimoja generoitu: {$generatedWatermarks}\n";
echo "_min kuvia generoitu: {$generatedMinis}\n";
echo "_orig kopioita luotu: {$createdOriginalCopies}\n";
echo "Jo vesileimallisia: {$alreadyWatermarked}\n";
echo "Ohitettu etä-URL: {$skippedRemote}\n";
echo "Ohitettu puuttuva tiedosto: {$skippedMissing}\n";
echo "Virheitä: {$failed}\n";

echo $dryRun
    ? "\nDry-run valmis. Aja ilman --dry-run, kun haluat tehdä muutokset.\n"
    : "\nValmis.\n";
