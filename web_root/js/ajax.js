//-----------------------------------------------
// jquery使わない式のajax
// @param url   リクエスト先
// @param requestJson リクエストデータ(json形式)
// @param callback 取得後実行処理
// @param cbparam 取得後実行処理に対する引数
//-----------------------------------------------
const ajax = function(url, callback, requestJson = null, method = "GET", cbparam = null){
    if(method == 'POST'){
        fetch(url,{
            method : method,
            body : JSON.stringify(requestJson),
            headers: {"Content-Type": "application/json; charset=utf-8"},
        })
        .then(function(res){
            return res.json(); 
        })
        .then(function(data){
            // 返されたデータ(json) 
            if(callback) callback(data, cbparam);
        })
        .catch(function(err){
            // エラー処理
            console.log(err);
        });
    }else if(method == 'GET'){
        fetch(url,{
            method : method,
            headers: {"Content-Type": "application/json; charset=utf-8"},
        })
        .then(function(res){
            return res.json(); 
        })
        .then(function(data){
            // 返されたデータ(json) 
            if(callback) callback(data, cbparam);
        })
        .catch(function(err){
            // エラー処理
            console.log(err);
        });
    }else if(method == 'DELETE'){
        fetch(url,{
            method : method,
            headers: {"Content-Type": "application/json; charset=utf-8"},
        })
        .then(function(res){
            return res.json(); 
        })
        .then(function(data){
            // 返されたデータ(json) 
            if(callback) callback(data, cbparam);
        })
        .catch(function(err){
            // エラー処理
            console.log(err);
        });
    }
}
