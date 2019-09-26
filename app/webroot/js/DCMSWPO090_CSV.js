var actionUrl4 = ''

//アップデートイベント初期化
function initCSV(url1) {

	actionUrl4 = url1;

	$("csvButton").observe("click",clickCSVButton);
}

//更新ボタンクリックイベントハンドラ
function clickCSVButton(event) {

	//GETの送信データを作る
	var executeUrl = actionUrl4;

	location.href = executeUrl;

}

