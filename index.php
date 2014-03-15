<?php
require_once "vendor/autoload.php";

$app = new Slim();

// routes
$app->get('/','getHome');
$app->get('/article-total-after/:id','getArticleTotalAfter');
$app->post('/article', 'addArticle');
$app->run();

// Home page
function getHome(){
    echo '<p><center>hallo selamat datang di api untuk website <a href="http://rumaysho.com">http://rumaysho.com</a> api ini dikerjakan oleh kami tim dari <a href="dotorpixel.com">dotorpixel.com</a> api ini bersifat terbuka, bila anda ingin memanfaatkan juga silahkan hubungi ~~~~</center></p>';
};

// Post add article from crawler
function addArticle(){
    global $app;
    $req = $app->request(); // Getting parameter with names
    $articleTitle = $req->params('title');
    $articleDate = $req->params('date');
    $articleAuthor = $req->params('author');
    $articleCategory = $req->params('category');
    $articleContent = $req->params('content');
    $articleUrl = $req->params('url');

    $sql = "INSERT INTO articles (title, date, author, category, content, url) VALUES (:title, :date, :author, :category, :content, :url)";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("title", $articleTitle);
        $stmt->bindParam("date", $articleDate);
        $stmt->bindParam("author", $articleAuthor);
        $stmt->bindParam("category", $articleCategory);
        $stmt->bindParam("content", $articleContent);
        $stmt->bindParam("url", $articleUrl);
        $stmt->execute();
        $articleid = $db->lastInsertId();
        $db = null;
        echo json_encode((array("id" => $articleid)));
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getArticleTotalAfter($id) {
    $sql = "SELECT count(*) as total FROM articles WHERE id>:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $article = $stmt->fetchObject();
        $db = null;
        echo json_encode($article);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}


 
function getConnection() {
    $dbhost="127.0.0.1";
    $dbuser="root";
    $dbpass="";
    $dbname="dop_rumaysho";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}