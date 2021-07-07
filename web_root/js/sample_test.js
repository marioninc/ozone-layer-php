// グローバルな変数
document.addEventListener('DOMContentLoaded', init);
function init() {
    table_init();
    event_init();
}

function table_init(){
    const table = document.getElementById('table');
    ajax(api_endpoint+'sample/get_list/', function(res, cbparam){
        table.innerHTML = "";
        for (var i = 0; i < res.data.length; i++) {
            var tr = document.createElement('tr');
            var row = res.data[i];
            console.log(row)
            Object.keys(row).forEach(function(row_key){
                var td = document.createElement('td');
                td.textContent = row[row_key];
                tr.appendChild(td);
            });
            table.appendChild(tr);
        }
    });
}
// イベント
function event_init(){
}