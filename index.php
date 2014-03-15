<?php
require_once "vendor/autoload.php";

$dsn = "mysql:dbname=dop_rumaysho;host=localhost";
$username = "root";
$password = "";

$pdo = new PDO($dsn, $username, $password);
$db = new NotORM($pdo);

$app = new Slim();

$app->get('/', 'getHome');
$app->run();

function getHome(){
    echo '<p><center>hallo selamat datang di api untuk website <a href="http://rumaysho.com">http://rumaysho.com</a> api ini dikerjakan oleh kami tim dari <a href="dotorpixel.com">dotorpixel.com</a> api ini bersifat terbuka, bila anda ingin memanfaatkan juga silahkan hubungi ~~~~</center></p>';
}
