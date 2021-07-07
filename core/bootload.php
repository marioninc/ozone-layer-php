<?php
// クロスサイト対応したい場合以下のコメントアウト削除
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Headers: X-Requested-With, X-token");
// header("Access-Control-Allow-Methods: GET,POST,OPTIONS,DELETE");

// コアモジュールを追加した場合以下に追加
require './core/database.php';
require './config/Config.php';
require './core/helper.php';
require './core/element.php';
require './core/HttpError.php';
require './core/PushJSON.php';
