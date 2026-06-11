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
        <div class="left">
            <nav>
                <button><img src="../source/imges/main.png" alt="">Главная</button>
                <button><img src="../source/imges/orders.png" alt="">Заказы</button>
                <button><img src="../source/imges/items.png" alt="">Товары</button>
                <button><img src="../source/imges/web.png" alt="">Web</button>
                <button><img src="../source/imges/statistic.png" alt="">Статистика</button>
                <button><img src="../source/imges/setting.png" alt="">Настройки</button>
            </nav>
        </div>

        <div class="right">
            <div class="right--top--panel">
                <form class="right--top--panel--search">
                    <input type="text" required placeholder="Поиск по аккаунту">
                    <button><img src="../source/imges/search.png" alt=""></button>
                </form>

                <div class="right--top--panel--system">
                    <button onclick="window.location = ''">
                        <img src="../source/imges/sun.png" alt="">
                    </button>
                    <button onclick="window.location = ''">
                        <img src="../source/imges/person.png" alt="">
                    </button>
                </div>
            </div>

            <div class="right--center--panel">
                <div class="panel--header">
                    <p>Привет, <?= $userData['data']['username'] ?></p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>