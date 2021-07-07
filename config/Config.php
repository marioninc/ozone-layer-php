<?php
/**
 * Config
 */
class Config{
    // Directorys //
    const local_root = '/ozone-layer-php/'; // ロカールテストする際、WEBルートと違う場合に使用
    // Json //
    const json_encode = 'encode'; // encode | un_encode
    
    // System Database //
    const db_config = [
        'database_name' => '',
        'host' => '',
        'user' => '',
        'pass' => '',
    ];
   
    // Proxy //
    const proxy_flg = false;
    const proxy_url = "http://0.0.0.0:8080";

    // SMTP //
    const default_smtp = [
        'host' => '',
        'port' => '',
        'SMTPSecure' => '',
        'auth' => '',
        'user' => '',
        'pass' => '',
        'from' => '',
        'from_name' => ''
    ];

    // 以下メソッド
    /**
    *   json_mode
    */
    public static function json_mode($mode = self::json_encode){
        switch ($mode) {
            case 'encode':
                return JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE;
                break;
            case 'un_encode':
            default:
                return 0;
                break;
        }
    }
}
?>