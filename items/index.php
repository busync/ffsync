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
    <title>Товары | FFsync</title>
    <link rel="stylesheet" href="../source/css/pages/dashboard/base.css">
    <link rel="stylesheet" href="../source/css/pages/items/index.css">
</head>

<body>
    <main>
        <?php include(RootPath . "blade/left-slide-nav.php") ?>

        <div class="right">
            <?php include(RootPath . "blade/top-nav.php") ?>

            <div class="right--center--panel">
                <!--
                <div class="panel-new">

                </div>
                -->

                <div class="panel-items">
                    <div class="item-table">
                        <table>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Название</th>
                                    <th>Описание</th>
                                    <th>Дата добавления</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img src=" ../source/imges/GitHub_Invertocat_Black_Clearspace.png" alt="">
                                    </td>
                                    <td>Товар 1</td>
                                    <td>Этот товар предназначен для определенной группы людей</td>
                                    <td>12.06.2026</td>
                                </tr>
                                <tr>
                                    <td><img src=" ../source/imges/GitHub_Invertocat_Black_Clearspace.png" alt="">
                                    </td>
                                    <td>Товар 2</td>
                                    <td>Этот товар предназначен для определенной группы людей</td>
                                    <td>12.06.2026</td>
                                </tr>
                                <tr>
                                    <td><img src=" ../source/imges/GitHub_Invertocat_Black_Clearspace.png" alt="">
                                    </td>
                                    <td>Товар 2</td>
                                    <td>Этот товар предназначен для определенной группы людей</td>
                                    <td>12.06.2026</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>