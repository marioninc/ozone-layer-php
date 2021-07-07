<?php
/**
* if_login ログインの確認を行い、ログインしてなければログインページに飛ばすか、boolを返す
* 
* @param  bool mast_login ログイン必須のページかどうか指定がなければtrue
* @return bool result     ログインしているかどうか。mast_loginがtrueの場合はfalseは返さない。
*/
function if_login($mast_login = true){
    if (!isset($_SESSION)) {
        session_start();
        // session_regenerate_id(true);
    }
    if(!isset($_SESSION['is_login']) || $_SESSION['is_login'] == 0){
        //ログインされていない
        if ($mast_login) {
            // 権限エラーを出す
            HttpError::set_error(403);
            echo PushJSON::error(4001);
            exit;   
        }else {
            return false;
        }
    }
    return true;
}
/**
* nowtime 現在時刻を返します。
* @param  int    type    欲しい時刻のフォーマットを記入します。
*                        0 = Y-m-d H:i:s
*                        1 = Y年m月d日 H時i分s秒
*                        2 = YmdHis
*                        3 = Ymd
*                        4 = Y/m
* @return string         フォーマット済みの時刻を返します。
*/
function nowtime($type = 0){
    $format = ['Y-m-d H:i:s','Y年m月d日 H時i分s秒','YmdHis','Ymd','Y/m'];
    $datetime = new datetime();
    return $datetime->format($format[$type]);
}
/**
* format_time 現在時刻を返します。
* @param  string time    変換したい時刻を指定する。
* @param  int    type    欲しい時刻のフォーマットを記入します。
*                        0 = Y-m-d H:i:s
*                        1 = m/d H:i
*                        2 = H:i
*                        3 = Y年m月d日 H時i分s秒
*                        4 = Y年m月d日
*                        9 = 個別指定  未実装
* @return string         フォーマット済みの時刻を返します。
*/
function format_time($time,$type = 0){
    $format = ['Y-m-d H:i:s','m/d H:i','H:i','Y年m月d日 H時i分s秒','Y年m月d日','Y-m-d'];
    $datetime = new datetime($time);
    return $datetime->format($format[$type]);
}
/**
* debug デバッグを表示するときにわかりやすく表示してくれる。
*
* @param  mix    data    デバッグで表示したいもの
* @param  string messege 一緒に表示しておきたい文字列（ループの回数などを渡す）
* @return void
*/
function debug($data,$messege = ""){
    $debug_backtrace = debug_backtrace();
    // var_dump($debug_backtrace);
    echo "{$debug_backtrace[0]['file']} line: {$debug_backtrace[0]['line']} {$messege}<br />\n";
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
}
/**
* h htmlをエスケープする
* @param  array data   エスケープする文字列の入った配列
* @return array return エスケープ後の配列
*/
function h($data){
    $return = null;
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $return[$key] = htmlspecialchars($value);
        }
        return $return;
    }else{
        return htmlspecialchars($data);
    }
}
/**
* get_base_url WEBページのルートを取得する。
*
* @return string ページのルートURL
*/
function get_base_url(){
    $base ="{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}";
    if($_SERVER['HTTP_HOST'] == 'localhost'){
        $base .= Config::local_root;
    }
    return $base;
}
/**
* get_root_dir ドキュメントルートを取得する。
*
* @return string ページのルートディレクトリ
*/
function get_root_dir(){
    $root = $_SERVER['CONTEXT_DOCUMENT_ROOT'];
    if($_SERVER['HTTP_HOST'] == 'localhost'){
        $root .= Config::local_root;
    }
    return $root;
}
?>