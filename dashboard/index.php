<?php
define("RootPath", "../");
require RootPath . 'autoload.php';

use App\Controllers\UserController;
use App\Database\Database;

$db = new Database();
$pdo = $db->getConnection();

$userController = new UserController($pdo);
if (!$userController->isAuthenticated()) {
    header('Location: ' . RootPath . 'login/');
    exit;
}

$userData = $userController->getUserData();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная | FFsync</title>
    <link rel="stylesheet" href="../source/css/pages/dashboard/base.css">
    <link rel="stylesheet" href="../source/css/pages/dashboard/index.css">
</head>

<body>
    <main>
        <?php include(RootPath . "blade/left-slide-nav.php") ?>

        <div class="right">
            <?php include(RootPath . "blade/top-nav.php") ?>

            <div class="right--center--panel">
                <div class="panel--header">
                    <p>Привет, <?= $userData['data']['username'] ?></p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>