<?php
/**
 * SUPA Live Environment Database & Cache Diagnostic Utility
 * Upload this file to your public_html (or public) directory and open it in your browser:
 * e.g., https://www.supa.ac.tz/db-repair.php
 */

header('Content-Type: text/html; charset=utf-8');

// Potential base paths for Laravel
$possibleBasePaths = [
    dirname(__DIR__),                  // standard laravel root (one level up from public)
    __DIR__,                           // if laravel is placed directly inside public_html
    dirname(dirname(__DIR__)),         // if public is nested two levels
    '/home/' . get_current_user() . '/new_supa',
    '/home/' . get_current_user() . '/supa',
];

$laravelRoot = null;
$envPath = null;
$cacheConfigPath = null;

foreach ($possibleBasePaths as $path) {
    if (file_exists($path . '/artisan') || file_exists($path . '/bootstrap/app.php')) {
        $laravelRoot = $path;
        break;
    }
}

if (!$laravelRoot) {
    // Fallback: check where .env is
    foreach ($possibleBasePaths as $path) {
        if (file_exists($path . '/.env')) {
            $laravelRoot = $path;
            break;
        }
    }
}

if ($laravelRoot) {
    $envPath = $laravelRoot . '/.env';
    $cacheConfigPath = $laravelRoot . '/bootstrap/cache/config.php';
    $cacheRoutesPath = $laravelRoot . '/bootstrap/cache/routes-v7.php';
    $cacheServicesPath = $laravelRoot . '/bootstrap/cache/services.php';
    $cachePackagesPath = $laravelRoot . '/bootstrap/cache/packages.php';
}

$actionMessage = '';
$actionType = '';

// Handle actions
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'clear_cache' && $laravelRoot) {
        $cleared = [];
        $filesToClear = [
            $laravelRoot . '/bootstrap/cache/config.php',
            $laravelRoot . '/bootstrap/cache/routes-v7.php',
            $laravelRoot . '/bootstrap/cache/routes.php',
            $laravelRoot . '/bootstrap/cache/events.php',
            $laravelRoot . '/bootstrap/cache/services.php',
            $laravelRoot . '/bootstrap/cache/packages.php',
        ];
        foreach ($filesToClear as $f) {
            if (file_exists($f)) {
                if (@unlink($f)) {
                    $cleared[] = basename($f);
                }
            }
        }
        if (!empty($cleared)) {
            $actionMessage = "Successfully deleted cached files: " . implode(', ', $cleared);
            $actionType = "success";
        } else {
            $actionMessage = "No cache files found to delete (cache was already clear).";
            $actionType = "info";
        }
    }

    if ($_POST['action'] === 'self_delete') {
        @unlink(__FILE__);
        echo "<!DOCTYPE html><html><body style='font-family:sans-serif;padding:40px;text-align:center;'><h2>Diagnostic file (db-repair.php) successfully deleted.</h2><p><a href='/'>Go to Website</a></p></body></html>";
        exit;
    }
}

// Parse .env manually
$envVars = [];
if ($envPath && file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || str_starts_with($line, '#')) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val);
            $val = trim($val, "\"'");
            $envVars[$key] = $val;
        }
    }
}

$dbHost = $envVars['DB_HOST'] ?? '127.0.0.1';
$dbPort = $envVars['DB_PORT'] ?? '3306';
$dbName = $envVars['DB_DATABASE'] ?? '';
$dbUser = $envVars['DB_USERNAME'] ?? '';
$dbPass = $envVars['DB_PASSWORD'] ?? '';

// Test MySQL connection
$dbStatus = 'NOT_TESTED';
$dbError = '';
$tablesList = [];

if ($dbName && $dbUser) {
    try {
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $dbStatus = 'CONNECTED';

        $stmt = $pdo->query("SHOW TABLES");
        $tablesList = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        $dbStatus = 'FAILED';
        $dbError = $e->getMessage();
    }
} else {
    $dbStatus = 'MISSING_ENV_VALUES';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUPA Database & Environment Diagnostics</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #0f172a; color: #f8fafc; padding: 24px; }
        .container { max-width: 860px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); border: 1px solid #334155; }
        h1 { font-size: 24px; color: #38bdf8; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .badge { padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .badge-success { background: #059669; color: #ecfdf5; }
        .badge-danger { background: #dc2626; color: #fef2f2; }
        .badge-warning { background: #d97706; color: #fffbeb; }
        .badge-info { background: #0284c7; color: #f0f9ff; }
        
        .card { background: #0f172a; border-radius: 8px; border: 1px solid #334155; padding: 20px; margin-bottom: 20px; }
        .card h2 { font-size: 16px; color: #94a3b8; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
        
        table { width: 100%; border-collapse: collapse; }
        table td { padding: 10px 12px; border-bottom: 1px solid #1e293b; font-size: 14px; }
        table td:first-child { color: #94a3b8; font-weight: 600; width: 220px; }
        table td:last-child { font-family: monospace; color: #f1f5f9; word-break: break-all; }
        
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 10px 18px; font-size: 14px; font-weight: 600; border-radius: 6px; cursor: pointer; border: none; transition: background 0.2s; text-decoration: none; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }
        .btn-primary { background: #0ea5e9; color: white; }
        .btn-primary:hover { background: #0284c7; }
        .btn-success { background: #10b981; color: white; }
        .btn-success:hover { background: #059669; }
        
        .alert { padding: 14px 18px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; }
        .alert-success { background: #064e3b; color: #a7f3d0; border: 1px solid #059669; }
        .alert-danger { background: #7f1d1d; color: #fecaca; border: 1px solid #dc2626; }
        .alert-info { background: #0c4a6e; color: #bae6fd; border: 1px solid #0284c7; }
        .alert-warning { background: #78350f; color: #fde68a; border: 1px solid #d97706; }
        
        .actions-row { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 15px; }
    </style>
</head>
<body>
<div class="container">
    <h1>
        <span>⚙️ Laravel Environment & DB Diagnostic</span>
    </h1>

    <?php if ($actionMessage): ?>
        <div class="alert alert-<?= $actionType ?>">
            <?= htmlspecialchars($actionMessage) ?>
        </div>
    <?php endif; ?>

    <!-- 1. Database Connection Status -->
    <div class="card">
        <h2>1. Database Connection Test</h2>
        <?php if ($dbStatus === 'CONNECTED'): ?>
            <div class="alert alert-success">
                <strong>✓ Connection Successful!</strong> Connected to database <code><?= htmlspecialchars($dbName) ?></code> with user <code><?= htmlspecialchars($dbUser) ?></code>.
                <br>Total tables found: <strong><?= count($tablesList) ?></strong>
                <?php if (in_array('sessions', $tablesList)): ?>
                    <span style="color:#a7f3d0;"> (✓ <code>sessions</code> table exists)</span>
                <?php else: ?>
                    <span style="color:#fde68a;"> (⚠️ <code>sessions</code> table not found, you may need to run migrations or import database)</span>
                <?php endif; ?>
            </div>
        <?php elseif ($dbStatus === 'FAILED'): ?>
            <div class="alert alert-danger">
                <strong>✕ Connection Failed:</strong><br>
                <code><?= htmlspecialchars($dbError) ?></code>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <strong>⚠️ Missing Database Configuration</strong>: DB_DATABASE or DB_USERNAME is empty in the loaded .env file.
            </div>
        <?php endif; ?>

        <table>
            <tr>
                <td>Configured DB_HOST</td>
                <td><?= htmlspecialchars($dbHost) ?></td>
            </tr>
            <tr>
                <td>Configured DB_PORT</td>
                <td><?= htmlspecialchars($dbPort) ?></td>
            </tr>
            <tr>
                <td>Configured DB_DATABASE</td>
                <td><strong><?= htmlspecialchars($dbName ?: '(EMPTY)') ?></strong></td>
            </tr>
            <tr>
                <td>Configured DB_USERNAME</td>
                <td><strong><?= htmlspecialchars($dbUser ?: '(EMPTY)') ?></strong></td>
            </tr>
            <tr>
                <td>Password Provided?</td>
                <td><?= !empty($dbPass) ? '<span class="badge badge-success">YES ('.strlen($dbPass).' characters)</span>' : '<span class="badge badge-danger">NO (EMPTY PASSWORD)</span>' ?></td>
            </tr>
        </table>
    </div>

    <!-- 2. Detected Paths & Cached Config Status -->
    <div class="card">
        <h2>2. Active Laravel Path & Configuration Cache</h2>
        <table>
            <tr>
                <td>Diagnostic Script Path</td>
                <td><?= htmlspecialchars(__FILE__) ?></td>
            </tr>
            <tr>
                <td>Detected Laravel Root</td>
                <td><?= htmlspecialchars($laravelRoot ?: 'NOT DETECTED') ?></td>
            </tr>
            <tr>
                <td>Active .env File Path</td>
                <td>
                    <?= htmlspecialchars($envPath ?: 'NOT FOUND') ?>
                    <?php if ($envPath && file_exists($envPath)): ?>
                        <span class="badge badge-success">EXISTS & READABLE</span>
                    <?php else: ?>
                        <span class="badge badge-danger">NOT FOUND</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>Cached config.php</td>
                <td>
                    <?php if ($cacheConfigPath && file_exists($cacheConfigPath)): ?>
                        <span class="badge badge-danger">PRESENT (BLOCKING .ENV UPDATES!)</span>
                        <p style="color:#f87171;font-size:12px;margin-top:4px;">When this file exists, Laravel ignores all changes made to your .env file.</p>
                    <?php else: ?>
                        <span class="badge badge-success">CLEARED (Laravel reads .env directly)</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <div class="actions-row">
            <form method="POST" style="display:inline;">
                <input type="hidden" name="action" value="clear_cache">
                <button type="submit" class="btn btn-primary">🧹 Clear Bootstrap Cache (Force Laravel to read .env)</button>
            </form>
            <a href="/admin/dashboard" target="_blank" class="btn btn-success">🔗 Test Admin Dashboard</a>
        </div>
    </div>

    <!-- 3. Troubleshooting Help -->
    <div class="card">
        <h2>3. What to do if connection fails</h2>
        <ul style="padding-left: 20px; line-height: 1.8; font-size: 14px; color: #cbd5e1;">
            <li><strong>Access denied for user:</strong> In cPanel, go to <strong>MySQL Databases</strong> &rarr; find <strong>Add User To Database</strong> &rarr; select your user and your database &rarr; click <strong>Add</strong> &rarr; check <strong>ALL PRIVILEGES</strong> &rarr; Make Changes.</li>
            <li><strong>Database name prefix:</strong> Remember cPanel databases look like <code>username_supa</code>, not just <code>supa</code>.</li>
            <li><strong>Username prefix:</strong> Remember cPanel users look like <code>username_admin</code>, not <code>root</code>.</li>
            <li><strong>Host:</strong> Keep <code>DB_HOST=127.0.0.1</code> or <code>localhost</code>.</li>
        </ul>
    </div>

    <div style="text-align:center;margin-top:20px;">
        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this diagnostic tool?');">
            <input type="hidden" name="action" value="self_delete">
            <button type="submit" class="btn btn-danger">🗑️ Delete This Diagnostic File (db-repair.php)</button>
        </form>
    </div>
</div>
</body>
</html>
