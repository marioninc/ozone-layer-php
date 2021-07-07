<?php
/*
このページは各Classメソッドを自動読み込むためのトップページです。
実態は、page Directory内の各ファイルの各ファイル各メソッドです。
*/
// コアデータ呼び出し
require './core/bootload.php';
if (empty($_GET)) {
    HttpError::set_error(404,false);
    exit;
}

$database = new Database();
if (!$database->db_connect_status) {
    HttpError::set_error(500,false);
    exit;
}

// 基本のGETパラメータを取得
if (empty($_GET['method']) || empty($_GET['page'])) {
    HttpError::set_error(404);
    echo PushJSON::error(3004);
    exit;
}
$class = $_GET['method'];
$method = $_GET['page'];
$param = null;
if (!empty($_GET['param'])) {
    $param = $_GET['param'];
}

// ファイルが存在チェック
if (!file_exists('./page/'.$class.'.php')) {
    HttpError::set_error(404,false);
    exit;
}

// クラスファイルの呼び出し
require './page/Page.php';
require './page/'.$class.'.php';
$className = __NAMESPACE__ . $class;
$page_class = new $className($method,$param,$database);

// メソッドの存在チェック
if(!method_exists($page_class, $method)) {
    HttpError::set_error(404,false);
    exit;
}

$page_class->$method();