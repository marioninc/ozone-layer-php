<?php
/**
 * PushJSON
 */
class PushJSON{
    public const ERROR_CODE = [
        1001 => 'DBに接続できません',
        1002 => 'DBの応答待ち中に、タイムアウトしました',

        2001 => '現在停止中です',
        2002 => '処理に失敗しました',
        2003 => '例外が発生しました',
        2004 => '処理がタイムアウトしました',

        3001 => 'パラメータが不足しています',
        3002 => 'パラメータが正しくありません(バリエーションエラー)',
        3003 => 'HTTPメソッドが違います',
        3004 => '存在しないリクエストです(404エラー)',
        3005 => 'このメソッドは削除されました。（永続的）',
        3006 => 'このメソッドは移動されました。（永続的）',
        3007 => 'データをすべて受け取れませんでしたもしくはリクエストが停止されました',

        4001 => 'ログインしてください',
        4002 => '権限がありません',
        4003 => '使用可能な時間ではありません',

        5001 => 'ユーザーは機能が制限されています',
        5002 => 'リクエストされたうちの1部のデーターのみレスポンスしています',
        5003 => '代替的データをレスポンスしました',
        5004 => '新しいメソッドがあります（内部エイリアス）',
        5005 => '空のデータです（正常）',

        6001 => '外部システム連携エラーです'
    ];

    public static function error($code,$detail_msg = null, $data = null){
        $return = [
            'status' => 'error',
            'error_code' => $code,
            'error_msg' => self::ERROR_CODE[$code],
            'error_detail' => $detail_msg
        ];
        if ($data !== null) {
            $return['data'] = $data;
        }
        return json_encode($return,Config::json_mode());
    }

    public static function success($data =null){
        $return = [
            'status' => 'success',
            'data' => $data
        ];
        return json_encode($return,Config::json_mode());
    }
}
?>
