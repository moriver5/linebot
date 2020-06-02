
/*
 * decimalで指定した小数点以下を切り捨て
 * args     : 符号付き数字, 小数点から第何位まで切り捨てないのかの数字
 * return   : float型の数字
 */
function getFloor(number, decimal){
    //小数点を含んだ数字の正規表現　例：12.3465、1234.543
    var NUMBER_MACTH = new RegExp("^(\\-?\\d+\\.\\d{"+ decimal +"})\\d+$");
 
    return parseFloat(number.toString().replace(NUMBER_MACTH, "$1"));
}

/* 下記の文字をエスケープする
 * &→&amp;
 * <→&lt;
 * >→&gt;
 * "→&quot;
 * '→&#39;
 */
function escapeHtml(str) {
    str = str.replace(/&/g, '&amp;');
    str = str.replace(/</g, '&lt;');
    str = str.replace(/>/g, '&gt;');
    str = str.replace(/"/g, '&quot;');
    str = str.replace(/'/g, '&#39;');
    return str;
}

/*
 * javascriptの開始終了タグをエスケープする
 */
function escapeJsTag(str) {
    str = str.replace(/<(.*?)script(.*?)>/g, '&lt;$1script$2&gt;');
    return str;
}


