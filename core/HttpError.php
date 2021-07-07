<?php
/**
 * Error
 */
class HttpError{
    public $code = 0;
    public $status = 0;
    public static function set_error($code,$is_api = true){
        $status = '';
        switch ($code) {
            case '200':
                $status = 'OK';
                break;
            case '400':
                $status = 'Bad Request';
                break;
            case '401':
                $status = 'Unauthorized';
                break;
            case '403':
                $status = 'Forbidden';
                break;
            case '404':
                $status = 'Not Found';
                break;
            case '405':
                $status = 'Method Not Allowed';
                break;
            case '501':
                $status = 'Not Implemented';
                break;
            default:
                $code = '500';
                $status = 'Internal Server Error';
                break;
        }
        $code;
        $status;
        // ob_clean();
        header("HTTP/1.0 {$code} {$status}");
        if(!$is_api){
            $root_dir = get_root_dir();
            require $root_dir.'/page/error.php';
            exit;
        }
    }
}
?>
