<?php
#
# DB core v1.0.3
#
class Database{
    // プロパティ
    public $databaseConfig = [];
    public $db_connect_status = false;
    private $dbh = null;

    function __construct($config = null) {
        if (!empty($config)) {
            $this->databaseConfig = [
                'database_name' => $config['database_name'],
                'host' => $config['host'],
                'user' => $config['user'],
                'pass' => $config['pass'],
            ];
            if (isset($config['port'])) {
                $this->databaseConfig['port'] = $config['port'];
            }
        }else {
            $this->databaseConfig = Config::db_config;
        }
        $this->db_connect_status = $this->open_connection();
    }

    public function open_connection(){
        //接続
        try{
            $dsn = "mysql:dbname={$this->databaseConfig['database_name']};host={$this->databaseConfig['host']};charset=utf8";
            if (isset($this->databaseConfig['port'])) {
                $dsn .= ";port={$this->databaseConfig['port']}";
            }
            $user = $this->databaseConfig['user'];
            $password = $this->databaseConfig['pass'];
            $this->dbh = new PDO($dsn, $user, $password);
            if (empty($this->dbh)) {
                return false;
            }else{
                return true;
            }
        }
        catch (PDOException $e) {
            return false;
        }
    }
    public function close_connection(){
        $this->dbh = null;
    }

    public function begin(){
        $this->dbh->beginTransaction();
    }
    
    public function commit(){
        $this->dbh->commit();
    }
    
    public function rollBack(){
        $this->dbh->rollBack();
    }
    
    /**
    * insertSQL テーブル名と[column=>value]の配列でインサートをしてくれます。
    *
    * @param  string       tablename インサートするテーブル名
    * @param  array        data      インサートするデーター
    * @return bool | int   id        失敗場合false(bool)成功した場合は挿入した行のIDを返す 
    */
    public function insertSQL($tablename,$data){
        //nullチェック
        if(empty($tablename) || empty($data)){
            //データがなければ中断
            return false;
        }
        

        //データ作成
        $columns = null;
        $insert_spease = null;
        $insert_data = [];
        foreach ($data as $key => $value) {
            $columns .= "`{$key}`,";
            $insert_spease .= '?,';
            $insert_data[] = $value;
        }

        //登録情報の一部を自動生成する
        $now = nowtime();
        //作成日時の指定がなければ現在時刻を指定
        if (!isset($data['create'])) {
            $columns .= "`".Config::created_column_name."`,";
            $insert_spease .= '?,';
            $insert_data[] = $now;
        }
        //更新日時の指定がなければ現在時刻を指定
        if (!isset($data['update'])) {
            $columns .= "`".Config::updated_column_name."`,";
            $insert_spease .= '?,';
            $insert_data[] = $now;
        }
        $columns = rtrim($columns,',');
        $insert_spease = rtrim($insert_spease,',');
        
        //insert
        try{
            $sql = "INSERT INTO `{$tablename}`({$columns}) VALUES ({$insert_spease})";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute($insert_data);
            $errorinfo = $stmt->errorinfo();
            // debug($sql);
            // debug($insert_data);
            // debug($errorinfo);
            $id = $this->dbh->lastInsertId(Config::id_column_name);
            unset($insert_data);
            if($errorinfo[1] != null) return false;
            return $id;
        }
        catch (PDOException $e) {
            return false;
        }
    }

    /**
    * updateSQL テーブル名と、ID、[column=>value]の配列でアップデートをしてくれます。
    *
    * @param  string  tablename アップデートするテーブル名
    * @param  int     id        アップデートするcolumnのID
    * @param  array   data      アップデートするデーター
    * @param  string  key_name  id扱いにするkeyカラム
    * @return bool    result    成功かどうか 
    */
    public function updateSQL($tablename,$id,$data,$key_name = null){
        if($key_name == null){
            $key_name = Config::id_column_name;
        }
        //nullチェック
        if(empty($tablename) || empty($id) || empty($data)){
            //データがなければ中断
            return false;
        }
        
        //データ作成
        $update_data = [];
        $set_str = null;
        foreach ($data as $key => $value) {
            $set_str .= " `{$key}`=?,";
            $update_data[] = $value;
        }

        if (!isset($data['update'])) {
            $set_str .= "`".Config::updated_column_name."`=?,";
            $update_data[] = nowtime();
        }
        $set_str = rtrim($set_str,',');
        if(is_array($id) && is_array($key_name) && count($id) == count($key_name)){
            foreach ($id as $key => $value) {
                $where = "`{$key_name[$key]}` =? AND";
                $update_data[] = $id[$key];
            }
            $where = rtrim($set_str,'AND');
        }else{
            $where = "`{$key_name}` =?";
            $update_data[] = $id;
        }

        // update
        try{
            $sql = "UPDATE `{$tablename}` SET{$set_str} WHERE {$where};";
            $stmt = $this->dbh -> prepare($sql);
            $r = $stmt -> execute($update_data);
            $errorinfo = $stmt->errorinfo();
            // debug($sql);
            // debug($errorinfo);
            if($errorinfo[1] != null) return false;
            $dbh = null;
            return $r;

        }
        catch (PDOException $e) {
            return false;
        }   
    }

    /**
    * selectSQL テーブル名と条件(任意)でSELECTし[column=>value]で配列をしてくれます。
    * limitの開始指定がある場合は"1 , 2"のように指定
    *
    * @param  string       tablename  セレクトするテーブル名
    * @param  array        conditions セレクトする条件(任意)
    *      ├  array        [column]   セレクトするカラム
    *      ├  array        [where]    条件
    *      ├  string/array [order]    ソートするカラム
    *      |   └  string   [asc]      降順、昇順   指定の売場合はASC
    *      └  string       [limit]    範囲(1を指定すると、返す値の全体を配列で囲わない)
    * @return array        result     読み込まれたデータ 
    *
    * エスケープ未実装！このままでは使用不能
    */
    public function selectSQL($tablename,$conditions = null, $manual = false){
        //nullチェック
        if(empty($tablename)){
            //データがなければ中断
            return false;
        }
        try{
            if ($manual) {
                // マニュアルモードの場合は、conditionsをそのままコピー
                $sql = $conditions;
                $execute = [];
            }else{
                //カラムを指定する
                $column = '*';
                if(!empty($conditions['column'])){
                    $column = null;
                    foreach ($conditions['column'] as $key => $value) {
                        $column .= "{$value},";
                    }
                    $column = rtrim($column,',');
                }

                // テーブルを設定
                if (is_array($tablename)) {
                    $tables = null;
                    foreach ($tablename as $key => $value) {
                        $tables .= "`{$value}`,";
                    }
                    $tables = ltrim($tables,'`');
                    $tables = rtrim($tables,'`,');
                }else{
                    $tables = $tablename;
                }
                $sql = "SELECT {$column} FROM `{$tables}`";
                $execute = [];
                //leftの追記
                if(!empty($conditions['join'])){
                    $join = "";
                    foreach ($conditions['join'] as $key => $value) {
                        $join .= " JOIN ".$value['table']." ON ".$value['where']." ";
                    }
                    $sql .= $join;
                }
                //leftの追記
                if(!empty($conditions['left'])){
                    $left = "";
                    foreach ($conditions['left'] as $key => $value) {
                        $left .= " LEFT JOIN ".$value['table']." ON ".$value['on']." ";
                    }
                    $sql .= $left;
                }
                //条件があれば追記
                if(!empty($conditions['where'])){
                    $where = ' WHERE';
                    foreach ($conditions['where'] as $key => $value) {
                        $mode = '';
                        $key_split = explode(' ', $key);
                        if(count($key_split) == 2){
                            $mode = $key_split[1];
                        }
                        if ($key === 'or' || $key === 'OR') {
                            $or = '(';
                            foreach ($value as $or_key => $or_value) {
                                $or_mode = '';
                                $or_key_split = explode(' ', $or_key);
                                if(count($or_key_split) == 2){
                                    $or_mode = $or_key_split[1];
                                }
                                if (is_numeric($or_key)) {
                                    $or .= ' '.$or_value.' OR';
                                }elseif (is_array($or_value)) {
                                    foreach ($or_value as $or_key2 => $or_value2) {
                                        $or .= ' '.$or_key2.' = ? OR';
                                        $execute[] = $or_value2;
                                    }
                                }elseif($or_mode == 'LIKE'){
                                    $or .= ' '.$or_key_split[0].' LIKE ? OR';
                                    $execute[] = '%'.$or_value.'%';
                                }else{
                                    $or .= ' '.$or_key.' = ? OR';
                                    $execute[] = $or_value;
                                }
                            }
                            $or = rtrim($or,' OR');
                            $where .= $or.') AND';
                            continue;
                        }
                        if (is_numeric($key)) {
                            $where .= ' '.$value.' AND';
                        } elseif ($value === null){
                            $where .= ' '.$key.' IS NULL AND';
                        }elseif($mode == 'LIKE'){
                            $where .= ' '.$key_split[0].' LIKE ? AND';
                            $execute[] = '%'.$value.'%';
                        }else{
                            $where .= ' '.$key.' = ? AND';
                            $execute[] = $value;
                        }
                }
                    $sql .= rtrim($where,' AND');
                }else{
                    $sql .=  ' WHERE 1';
                }
                //GROUPがあれば指定する
                if (!empty($conditions['group'])) {
                    $sql .= ' GROUP BY '.$conditions['group'];
                }
                //ORDERがあれば指定する
                if (!empty($conditions['order'])) {
                    if (!is_array($conditions['order'])) {
                        $sql .= ' ORDER BY '.$conditions['order'];
                    } else {
                        $sql .= ' ORDER BY ';
                        foreach ($conditions['order'] as $key => $value) {
                            if (is_numeric($key)) {
                                $sql .= $value.' ASC,';
                            } else {
                                $sql .= $key.' '.$value.',';
                            }
                        }
                        $sql = rtrim($sql,',');
                    }
                }
                //LIMITがあれば追記
                if (!empty($conditions['limit'])) {
                    $sql .= ' LIMIT '.$conditions['limit'];
                }
            }
            //select
            // debug($sql);
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute($execute);
            // debug($stmt->errorinfo());
            $result = null;
            if (!empty($conditions['limit']) && $conditions['limit'] == 1) {
                //Limitが1の場合は全体を配列で囲まない
                $result = $stmt -> fetch(PDO::FETCH_ASSOC);
            }else{
                while (true) {
                    $rec = $stmt -> fetch(PDO::FETCH_ASSOC);
                    if($rec==false) break;
                    $result[] = $rec;
                }   
            }
            return $result;
        }
        catch (PDOException $e) {
            return false;
        }
    }
}
?>