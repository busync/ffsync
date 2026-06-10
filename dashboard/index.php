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
</head>
<body>
    <main>
        <div class="left">
            <nav>
                <button>Главная</button>
                <button>Заказы</button>
                <button>Товары</button>
                <button>Web</button>
                <button>Статистика</button>
                <button>Настройки</button>
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
                
            </div>
        </div>
    </main>
</body>
</html>