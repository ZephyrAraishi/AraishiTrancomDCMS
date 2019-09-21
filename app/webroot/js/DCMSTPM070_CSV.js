var actionUrl2 = ''

//アップデートイベント初期化
function initCSV(url1) {

	actionUrl2 = url1;

	$("csvButton").observe("click",clickCSVButton);
}

//更新ボタンクリックイベントハンドラ
function clickCSVButton(event) {

	//GETの送信データを作る
	var executeUrl = actionUrl2 + "?ymd_from=" + $("YMD_FROM_H").innerHTML + "&ymd_to=" + $("YMD_TO_H").innerHTML + "&BatchNo_from=" + $("BATCHNO_FROM_H").innerHTML
	                 + "&BatchNo_to=" + $("BATCHNO_TO_H").innerHTML + "&Tokuisaki=" + $("TOKUISAKI").innerHTML + "&homen=" + $("HOMEN").innerHTML + "&shomikigen_henko=" + $("SHOMIKIGEN_HENKO").innerHTML + "&Shohin=" + $("Shohin").innerHTML;

	location.href = executeUrl;


}

