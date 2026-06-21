<?php

declare(strict_types=1);

/**
 * Invoxaco standalone setup wizard.
 * Runs before .env exists, so it cannot use app/bootstrap.php.
 */

session_start();

$rootDir = dirname(__DIR__, 2);
$envPath = $rootDir . '/.env';
$envExamplePath = $rootDir . '/.env.example';

if (file_exists($envPath)) {
    http_response_code(403);
    echo '<p style="font-family:sans-serif">Invoxaco is already installed. Delete <code>.env</code> manually if you need to reinstall.</p>';
    exit;
}

$step = $_GET['step'] ?? '1';
$errors = [];
$old = $_POST;

function sqlStatements(string $sql): array
{
    $statements = [];
    foreach (explode(";\n", $sql) as $chunk) {
        $lines = array_filter(
            explode("\n", $chunk),
            static fn (string $line): bool => !str_starts_with(trim($line), '--')
        );
        $statement = trim(implode("\n", $lines));
        if ($statement !== '') {
            $statements[] = $statement;
        }
    }

    return $statements;
}

function requirementChecks(string $rootDir): array
{
    return [
        'PHP >= 8.3' => version_compare(PHP_VERSION, '8.3.0', '>='),
        'PDO extension' => extension_loaded('pdo'),
        'PDO MySQL driver' => extension_loaded('pdo_mysql'),
        'mbstring extension' => extension_loaded('mbstring'),
        'JSON extension' => extension_loaded('json'),
        'storage/ writable' => is_writable($rootDir . '/storage'),
        'Root directory writable (.env)' => is_writable($rootDir),
        'vendor/ installed' => is_dir($rootDir . '/vendor'),
    ];
}

if ($step === '2' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = trim((string) ($_POST['db_host'] ?? ''));
    $dbPort = trim((string) ($_POST['db_port'] ?? '3306'));
    $dbName = trim((string) ($_POST['db_database'] ?? ''));
    $dbUser = trim((string) ($_POST['db_username'] ?? ''));
    $dbPass = (string) ($_POST['db_password'] ?? '');
    $appUrl = trim((string) ($_POST['app_url'] ?? ''));
    $adminName = trim((string) ($_POST['admin_name'] ?? ''));
    $adminEmail = trim((string) ($_POST['admin_email'] ?? ''));
    $adminPassword = (string) ($_POST['admin_password'] ?? '');

    if ($dbHost === '' || $dbName === '' || $dbUser === '') {
        $errors[] = 'Database host, name, and username are required.';
    }
    if ($appUrl === '' || !filter_var($appUrl, FILTER_VALIDATE_URL)) {
        $errors[] = 'A valid Application URL is required.';
    }
    if ($adminName === '' || $adminEmail === '' || !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid admin name and email are required.';
    }
    if (strlen($adminPassword) < 8) {
        $errors[] = 'Admin password must be at least 8 characters.';
    }

    $pdo = null;
    if (empty($errors)) {
        try {
            $pdo = new PDO(
                "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4",
                $dbUser,
                $dbPass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '', $dbName) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            $pdo->exec('USE `' . str_replace('`', '', $dbName) . '`');
        } catch (\PDOException $e) {
            $errors[] = 'Could not connect to the database: ' . $e->getMessage();
        }
    }

    if (empty($errors) && $pdo !== null) {
        try {
            $schemaSql = file_get_contents($rootDir . '/database/schema.sql');
            $seedSql = file_get_contents($rootDir . '/database/seed.sql');

            foreach (sqlStatements($schemaSql) as $statement) {
                $pdo->exec($statement);
            }

            $adminCount = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
            if ($adminCount === 0) {
                foreach (sqlStatements($seedSql) as $statement) {
                    $pdo->exec($statement);
                }

                $hashed = password_hash($adminPassword, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare(
                    'INSERT INTO users (name, email, password, role, plan, email_verified_at) VALUES (:name, :email, :password, :role, :plan, NOW())'
                );
                $stmt->execute([
                    'name' => $adminName,
                    'email' => $adminEmail,
                    'password' => $hashed,
                    'role' => 'admin',
                    'plan' => 'premium',
                ]);
            }
        } catch (\PDOException $e) {
            $errors[] = 'Database setup failed: ' . $e->getMessage();
        }
    }

    if (empty($errors)) {
        $appKey = base64_encode(random_bytes(32));
        $envContent = str_replace(
            ['APP_URL=https://invoxaco.com', 'APP_KEY=', 'DB_HOST=localhost', 'DB_DATABASE=invoxaco', 'DB_USERNAME=', 'DB_PASSWORD=', 'ADMIN_EMAIL=admin@invoxaco.com', 'ADMIN_PASSWORD='],
            [
                'APP_URL=' . $appUrl,
                'APP_KEY=' . $appKey,
                'DB_HOST=' . $dbHost,
                'DB_DATABASE=' . $dbName,
                'DB_USERNAME=' . $dbUser,
                'DB_PASSWORD=' . $dbPass,
                'ADMIN_EMAIL=' . $adminEmail,
                'ADMIN_PASSWORD=' . $adminPassword,
            ],
            (string) file_get_contents($envExamplePath)
        );
        $envContent = preg_replace('/^DB_PORT=.*/m', 'DB_PORT=' . $dbPort, $envContent);
        $envContent = preg_replace('/^ADMIN_NAME=.*/m', 'ADMIN_NAME="' . $adminName . '"', $envContent);

        file_put_contents($envPath, $envContent);

        $step = '3';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoxaco Setup</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:680px;">
  <h2 class="fw-bold mb-1">Invoxaco Setup</h2>
  <p class="text-secondary mb-4">Step <?= htmlspecialchars($step) ?> of 3</p>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
          <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($step === '1'): ?>
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Requirements Check</h5>
        <ul class="list-unstyled mb-4">
          <?php $allOk = true; foreach (requirementChecks($rootDir) as $label => $ok): $allOk = $allOk && $ok; ?>
            <li class="d-flex justify-content-between border-bottom py-2">
              <span><?= htmlspecialchars($label) ?></span>
              <span class="<?= $ok ? 'text-success' : 'text-danger' ?> fw-bold"><?= $ok ? 'OK' : 'FAIL' ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
        <a href="?step=2" class="btn btn-primary <?= $allOk ? '' : 'disabled' ?>">Continue</a>
        <?php if (!$allOk): ?><p class="text-danger small mt-2">Resolve the failed requirements above before continuing.</p><?php endif; ?>
      </div>
    </div>
  <?php elseif ($step === '2'): ?>
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body p-4">
        <form method="POST" action="?step=2">
          <h5 class="fw-bold mb-3">Database</h5>
          <div class="row g-3 mb-3">
            <div class="col-md-8">
              <label class="form-label">DB Host</label>
              <input type="text" name="db_host" class="form-control" value="<?= htmlspecialchars((string) ($old['db_host'] ?? 'localhost')) ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">DB Port</label>
              <input type="text" name="db_port" class="form-control" value="<?= htmlspecialchars((string) ($old['db_port'] ?? '3306')) ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Database Name</label>
              <input type="text" name="db_database" class="form-control" value="<?= htmlspecialchars((string) ($old['db_database'] ?? 'invoxaco')) ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">DB Username</label>
              <input type="text" name="db_username" class="form-control" value="<?= htmlspecialchars((string) ($old['db_username'] ?? '')) ?>" required>
            </div>
            <div class="col-12">
              <label class="form-label">DB Password</label>
              <input type="password" name="db_password" class="form-control" value="<?= htmlspecialchars((string) ($old['db_password'] ?? '')) ?>">
            </div>
          </div>

          <hr>
          <h5 class="fw-bold mb-3">Application</h5>
          <div class="mb-3">
            <label class="form-label">Application URL</label>
            <input type="text" name="app_url" class="form-control" placeholder="https://example.com" value="<?= htmlspecialchars((string) ($old['app_url'] ?? '')) ?>" required>
          </div>

          <hr>
          <h5 class="fw-bold mb-3">Admin Account</h5>
          <div class="mb-3">
            <label class="form-label">Admin Name</label>
            <input type="text" name="admin_name" class="form-control" value="<?= htmlspecialchars((string) ($old['admin_name'] ?? '')) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Admin Email</label>
            <input type="email" name="admin_email" class="form-control" value="<?= htmlspecialchars((string) ($old['admin_email'] ?? '')) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Admin Password</label>
            <input type="password" name="admin_password" class="form-control" minlength="8" required>
          </div>

          <button class="btn btn-primary mt-2">Install</button>
        </form>
      </div>
    </div>
  <?php else: ?>
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body p-4 text-center">
        <h5 class="fw-bold mb-3">Installation Complete</h5>
        <p class="text-secondary">Your Invoxaco site has been installed and the admin account created.</p>
        <p class="text-secondary small">For security, delete the <code>setup/</code> directory now.</p>
        <a href="/login" class="btn btn-primary">Go to Login</a>
      </div>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
