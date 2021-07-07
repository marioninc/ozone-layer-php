<?php
/**
 * Sample
 */
class Sample extends API {
    function __construct($method,$param,$database){
        parent::__construct();
        // if_login();
        $this->method = $method;
        $this->param = $param;
        $this->database = $database;
        $this->table = 't_distribute_item';
    }

    public function get_list(){
        $this->isGet(); // GETのみ提供
        $samples = $this->database->selectSQL($this->table); // 全てのデータを出力
        echo PushJSON::success($samples); // 成功をJSONを発行
    }

    public function new(){
        $this->isPost();
        $save_data = h($_POST);
        // ここにバリデーションチェックを書く
        $line = $this->database->insertSQL($this->table,$save_data); // insertSQLは 自動的に、登録日時と更新日時に現在時刻を追加してDBに書き込む
        if(!$line){ // lineには失敗したらfalse成功なら行数が返る
            HttpError::set_error(500); // HTTPステータスを変更
            echo PushJSON::error(2002); // エラーJSONを発行
            exit;
        }
        echo PushJSON::success(); // 成功をJSONを発行
    }

    public function change(){
        $this->isPost();
        // $this->paramは、URLの3つ目の項目で以下のように指定できる
        // http://ozone-layer-php/api/sample/change/{id}/
        $id = $this->param;
        $save_data = h($_POST);
        // ここにバリデーションチェックを書く
        $r = $this->database->updateSQL($this->table,$id,$save_data); // updateSQLは自動で気に更新日時に現在時刻を追加して行を更新する
        if(!$r){ // lineには失敗したらfalse成功ならtrueが返る
            HttpError::set_error(500); // HTTPステータスを変更
            echo PushJSON::error(2002); // エラーJSONを発行
            exit;
        }
        echo PushJSON::success(); // 成功をJSONを発行
        return;

        // 自分のテーブルならこれでも可
        $this->update($this->param,$save_data); 
    }

    public function delete(){
        $this->isDelete();
        $this->del($this->param);
    }
}