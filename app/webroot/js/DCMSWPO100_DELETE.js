var actionUrl2 = ''

//アップデートイベント初期化
function initDELETE(url1) {

	actionUrl2 = url1;

	$("DeleteButton").observe("click",clickDeleteButton);
}

//更新ボタンクリックイベントハンドラ
function clickDeleteButton(event) {
	binFlag = false;

	// 検索条件と一覧チェック
	if ( document.getElementById("ymd_from").value != $("YMD_FROM_H").innerHTML ) {
		binFlag = true;
	}
	if ( document.getElementById("BatchNo_from").value != $("BATCHNO_FROM_H").innerHTML ) {
		binFlag = true;
	}
	if ( document.getElementById("homen").value != $("HOMEN").innerHTML ) {
		binFlag = true;
	}
	if (binFlag==true) {
		alert("一覧表示された検索条件と入力された検索条件が異なります。再度検索ボタンを押して削除データが正しいか確認ください。");
		return;
	}

	//GETの送信データを作る
	var executeUrl = actionUrl2 + "?ymd_from=" + $("YMD_FROM_H").innerHTML + "&BatchNo_from=" + $("BATCHNO_FROM_H").innerHTML + "&homen=" + $("HOMEN").innerHTML;

	location.href = executeUrl;

}

