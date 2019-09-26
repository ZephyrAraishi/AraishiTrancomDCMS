var actionUrl2 = ''

//アップデートイベント初期化
function initCSV(url1) {

	actionUrl2 = url1;

	$("csvButton").observe("click",clickCSVButton);
}

//更新ボタンクリックイベントハンドラ
function clickCSVButton(event) {

	//GETの送信データを作る
//	var executeUrl = actionUrl2 + "?ymd_from=" + $("YMD_FROM_H").innerHTML + "&ymd_to=" + $("YMD_TO_H").innerHTML + "&tokuisaki=" + $("TOKUISAKI").innerHTML + "&homen=" + $("HOMEN").innerHTML + "&tenpo=" + $("TENPO").innerHTML;
	var executeUrl = actionUrl2 + "?ymd_from=" + $("YMD_FROM_H").innerHTML + "&ymd_to=" + $("YMD_TO_H").innerHTML + "&homen=" + $("HOMEN").innerHTML + "&shohin_code=" + $("SHOHIN_C").
	innerHTML + "&shohin_mei=" + $("SHOHIN_M").innerHTML + "&tenpo=" + $("TENPO").innerHTML + "&kanban_barcode=" + $("KANBAN").innerHTML;


	location.href = executeUrl;


}

