var actionUrl2 = ''

//アップデートイベント初期化
function initCSV(url1) {

	actionUrl2 = url1;

	$("csvButton").observe("click",clickCSVButton);
}

//更新ボタンクリックイベントハンドラ
function clickCSVButton(event) {

	//GETの送信データを作る
	var executeUrl = actionUrl2 + "?YMD_FROM=" + $("YMD_FROM_H").innerHTML + "&YMD_TO=" + $("YMD_TO_H").innerHTML + "&KOTEI=" + $("KOTEI").innerHTML
	                 + "&TIME_FROM=" + $("TIME_FROM").innerHTML + "&TIME_TO=" + $("TIME_TO").innerHTML + "&NINZU=" + $("NINZU").innerHTML + "&JIKAN=" + $("JIKAN").innerHTML + "&KOTEI2=" + $("KOTEI2").innerHTML;

	location.href = executeUrl;


}

