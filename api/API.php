<?php
/**
 * API
 */
class API{
	public $request_body;
	public $request_json;
    public $table;
    function __construct(){
        // if_login();
        $this->request_body = file_get_contents("php://input");
        if(!empty($this->request_body)){
        	$this->request_json = json_decode($this->request_body,true);
        }
        if (!isset($_SESSION)) {
            session_start();
            @session_regenerate_id(true);
        }
    }   
	static function IsGet($is_return = false){
		if($_SERVER["REQUEST_METHOD"] == "GET") return true;
        if($is_return) return false;
		HttpError::set_error(405);
        echo PushJSON::error(3003);
        exit;
	}
	static function IsPost($is_return = false){
		if($_SERVER["REQUEST_METHOD"] == "POST") return true;
        if($is_return) return false;
		HttpError::set_error(405);
        echo PushJSON::error(3003);
        exit;
	}
	static function IsDelete($is_return = false){
		if($_SERVER["REQUEST_METHOD"] == "DELETE") return true;
        if($is_return) return false;
		HttpError::set_error(405);
        echo PushJSON::error(3003);
        exit;
	}

    public function del($id,$table = null, $end = true){
        if($table == null){
            $table = $this->table;
        }
        if(empty($id)){
            HttpError::set_error(400);
            echo PushJSON::error(3001);
            exit;
        }
        $rs = $this->database->updateSQL($table,$id,['status' => 0]);
        if (!$rs) {
            HttpError::set_error(500);
            echo PushJSON::error(2002);
            exit;
        }
        if($end) echo PushJSON::success();
        return true;
    }
    public function update($id,$data,$table = null, $end = true){
        if($table == null){
            $table = $this->table;
        }
        if(empty($id)){
            HttpError::set_error(400);
            echo PushJSON::error(3001);
            exit;
        }
        $rs = $this->database->updateSQL($table,$id,$data);
        if (!$rs) {
            HttpError::set_error(500);
            echo PushJSON::error(2002);
            exit;
        }
        if($end) echo PushJSON::success();
        return true;
    }


}