<?php
require '../autoload.php';

use App\Controllers\UserController;
use App\Database\Database;

$db = new Database();
$pdo = $db->getConnection();

$userController = new UserController($pdo);
if (!$userController->isAuthenticated()) {
    header('Location: ../login/');
    exit;
}

$settings = ['error' => ['message' => 'Settings unavailable']];

if (file_exists("../core/conf/setting.json")) {
    $settings = json_decode(file_get_contents("../core/conf/setting.json"), true);
    if ($settings === null && json_last_error() !== JSON_ERROR_NONE) {
        echo "JSON decode error: " . json_last_error_msg();
    }
}

$systemInfo = [
    'PHP Version' => phpversion(),
    'MySQL Version' => $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
    'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
    'PHP Max Execution Time' => ini_get('max_execution_time') . ' sec',
    'PHP Memory Limit' => ini_get('memory_limit'),
    'Upload Max Size' => ini_get('upload_max_filesize'),
    'Post Max Size' => ini_get('post_max_size'),
    'Timezone' => date_default_timezone_get(),
    'Current Time' => date('Y-m-d H:i:s')
];

$settings['php'] = $systemInfo;

$userData = $userController->getUserData();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../source/css/pages/dashboard/index.css">
</head>

<body>
    <main class="main">
        <div class="main--header">
            <h1>
                <span>🏛️</span>
                Dashboard
            </h1>

            <div>
                <button onclick="transit('../')">Main page</button>
                <button onclick="transit('../logout/')">Logout</button>
            </div>
        </div>

        <div class="main--content">
            <h2>User data</h2>
            <div class="main--table-wrapper">
                <table class="main--table" id="userDataTable">
                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($userData['success'] && !empty($userData['data'])): ?>
                            <?php foreach ($userData['data'] as $field => $value): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $field)), ENT_QUOTES, 'UTF-8'); ?></strong>
                                    </td>
                                    <td>
                                        <?php
                                        if ($field === 'created_at') {
                                            echo htmlspecialchars(date('F j, Y, g:i a', strtotime($value)), ENT_QUOTES, 'UTF-8');
                                        } else {
                                            echo htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" style="text-align: center; color: #999;">
                                    <?php echo htmlspecialchars($userData['error'] ?? 'No user data available', ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <h2 style="margin-top: 30px;">System Settings</h2>
            <div class="main--table-wrapper">
                <table class="main--table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Key</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($settings as $category => $items): ?>
                            <?php foreach ($items as $key => $value): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars(ucfirst($category), ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                    <td><?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <?php
                                        if ($key === 'github'): ?>
                                            <a href="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
                                                <?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>
                                            </a>
                                        <?php elseif ($key === 'help'): ?>
                                            <code><pre><?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?></pre></code>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script src="../source/js/dashboard/index.js"></script>
</body>

</html>