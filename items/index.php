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
    <title>Товары | FFsync</title>
    <link rel="stylesheet" href="../source/css/pages/dashboard/base.css">
    <link rel="stylesheet" href="../source/css/pages/items/index.css">
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
                        <img src="../source/imges/person.png" alt="">
                    </button>
                </div>
            </div>

            <div class="right--center--panel">
                <!--
                <div class="panel-new">

                </div>
                -->

                <div class="panel-setting">
                    <h2>Внешний вид</h2>

                    <div class="settings">
                        <fieldset>
                            <legend>Столбцов</legend>
                            <input type="range" id="columns-range" min="3" max="10" value="10">
                            <span id="columns-value">5</span>
                        </fieldset>
                    </div>
                </div>

                <div class="panel-items" id="panel-item">
                    <button class="container new"><span>+</span></button>
                    <button class="container item">
                        <img src="../source/imges/GitHub_Invertocat_Black_Clearspace.png" alt="">
                        <p>Название товара</p>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script src="../source/js/items/visual.js"></script>
</body>

</html>