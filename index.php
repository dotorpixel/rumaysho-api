<?php
require_once "vendor/autoload.php";

$app = new Slim();

// routes
$app->get('/','getHome');
$app->get('/article-total-after/:id','getArticleTotalAfter');
$app->get('/article/:limit/:offset','getArticleLimitOffset');
$app->get('/article/:id','getArticle');
$app->get('/article-search-by/:fild/:value/','articleFindBy');
$app->get('/article-search-by/:fild/:value/:offset/:limit','articleFindBy');
$app->post('/article', 'addArticle');
$app->run();

// Home page
function getHome()
{
    echo '<p><center>hallo selamat datang di api untuk website <a href="http://rumaysho.com">http://rumaysho.com</a> api ini dikerjakan oleh kami tim dari <a href="dotorpixel.com">dotorpixel.com</a> api ini bersifat terbuka, bila anda ingin memanfaatkan juga silahkan hubungi ~~~~</center></p>';
};

// Post add article from crawler
function addArticle()
{
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

// get total artickel after :id
function getArticleTotalAfter($id)
{
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

// get article :limit :offset
function getArticleLimitOffset($limit = 0,$offset = 0)
{
    $thisLimit = intval($limit);
    $thisOffset = intval($offset);
    $sql = "select * FROM articles ORDER BY id LIMIT :offset , :limit";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("limit", $thisLimit, PDO::PARAM_INT);
        $stmt->bindParam("offset", $thisOffset, PDO::PARAM_INT);
        $stmt->execute();
        $article = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"article": ' . json_encode($article) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

// get article detail with :id
function getArticle($id = 0)
{
    $sql = "SELECT * FROM articles WHERE id=:id";
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

// search article by :fild :value
function articleFindBy($fild , $value , $offset = 0 , $limit = 10)
{
    $thisLimit = intval($limit);
    $thisOffset = intval($offset);
    $sql = "SELECT * FROM articles WHERE ".$fild." LIKE :value ORDER BY id LIMIT :offset , :limit";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $value = "%".$value."%";
        $stmt->bindParam("value", $value);
        $stmt->bindParam("limit", $thisLimit, PDO::PARAM_INT);
        $stmt->bindParam("offset", $thisOffset, PDO::PARAM_INT);
        $stmt->execute();
        $article = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"article": ' . json_encode($article) . '}';
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