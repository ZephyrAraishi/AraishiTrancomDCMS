var actionUrl2 = ''

//アップデートイベント初期化
function initCSV(url1) {

	actionUrl2 = url1;

	$("csvButton").observe("click",clickCSVButton);
}

//更新ボタンクリックイベントハンドラ
function clickCSVButton(event) {

	//GETの送信データを作る
	var executeUrl = actionUrl2 + "?ymd_from=" + $("ymd_from").value;
	executeUrl = executeUrl + "&ymd_to=" + $("ymd_to").value;
	location.href = executeUrl;


}

