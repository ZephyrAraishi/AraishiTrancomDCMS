var actionUrl2 = ''
var actionUrl5 = ''

//アップデートイベント初期化
function initCSV(url1,url2) {

	actionUrl2 = url1;
	actionUrl5 = url2;

	$("csvButton").observe("click",clickCSVButton);
	$("csvButton2").observe("click",clickCSVButton2);
}

//更新ボタンクリックイベントハンドラ
function clickCSVButton(event) {

	//GETの送信データを作る
	var executeUrl = actionUrl2 + "?YMD_FROM=" + $("YMD_FROM_H").innerHTML + "&YMD_TO=" + $("YMD_TO_H").innerHTML + "&KOTEI=" + $("KOTEI").innerHTML;

	location.href = executeUrl;


}

function clickCSVButton2(event) {

	//GETの送信データを作る
	var executeUrl = actionUrl5 + "?YMD_FROM=" + $("YMD_FROM_H").innerHTML + "&YMD_TO=" + $("YMD_TO_H").innerHTML + "&KOTEI=" + $("KOTEI").innerHTML;

	location.href = executeUrl;

}
