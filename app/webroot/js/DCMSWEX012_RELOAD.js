var refreshIntervalId;

var actionUrl3 = "";

//
//リロード時の初期処理
function initReload() {

	//更新メッセージ
	var get = getRequest();

	if (get.length != 0) {

		if (get["ninusi_cd"] != null) {
			$("mninusiCombo").setValue(get["ninusi_cd"]);
		}

		if (get["sosiki_cd"] != null) {
			$("WEX012ModelSosikiCd").setValue(get["sosiki_cd"]);
		}

		if (get["sosiki_nm"] != null) {
			$("WEX012ModelSosikiNm").setValue(decodeURI(get["sosiki_nm"]));
		}

		if (get["return_cd"] != null) {

			//画面上部にメッセージを表示
			if (get["return_cd"] == "0") {

					$("infoMsg").update(Base64.decode(get["message"]));
			}  else {

					$("errorMsg").update(Base64.decode(get["message"]));

					//
					var resultRows = $$("body")[0].getElementsBySelector(".tableRow");

					for(var i=0 ; i< resultRows.length; i++) {

						var flg = resultRows[i].getElementsBySelector(".hiddenData")[1].innerHTML;

						if (parseInt(flg,10) < 3) {

							setRowColor(flg,resultRows[i]);
						} else {

							setDeletedRowColor(resultRows[i])
						}
					}
			}
		}

	}

}




//
//アップデートイベント初期化
function initUpdate(url) {

	actionUrl3 = url;

	$("updateButton").observe("click",clickUploadButton);
}


var updatingCurtain = null;

//
//更新ボタンクリックイベント
function clickUploadButton(event) {

      // 確認ダイアログを表示
      var res = confirm(DCMSMessage.format("CMNQ000001"));
      // 選択結果で分岐
      if( res == false ) {
	    return;
      }

	//全体を隠すカーテンをつける
	$$("body")[0].insert(createUpdatingCurtain());

	//渡す配列
	var resultArray = new Array();

	//データを取得する
	var resultRows = $("lstTable").getElementsBySelector(".tableRow");

	for (var i=0; i < resultRows.length; i++) {

		//番号
		var bango = resultRows[i].getElementsBySelector(".tableCellBango")[0].innerHTML.strip();

		//セル配列
		var cellsArray = new Array();
		var cells = resultRows[i].getElementsBySelector(".tableCell");

		for (var m=0; m < cells.length; m++) {

			cellsArray[m] = cells[m].innerHTML.strip();
		}

		//データ配列
		var dataArray = new Array();
		var data = resultRows[i].getElementsBySelector(".hiddenData");

		for (var m=0; m < data.length; m++) {

			dataArray[m] = data[m].innerHTML.strip();
		}

		//行配列
		var rowArray = {
			'bango' : bango,
			'cells' : cellsArray,
			'data' : dataArray
		};

		//行追加
		resultArray[i] = rowArray;
	}


	//ポストの送信データを作る
	//JSONへ変更

	var sendArray = {

		'data' : resultArray,
		'timestamp' : $("timestamp").innerHTML
	}

	var postData = Object.toJSON(sendArray);

    //ポップアップを読み込む
    new Ajax.Request( actionUrl3, {
	  method : 'post',
	  parameters: postData,

	  onSuccess : onSuccessEvent,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WEX012/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WEX012/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
	  }
	});
}

//
//更新成功時
function onSuccessEvent(res) {

	//レスポンス
	var result = escapePHP(res.responseText.trim());
//	var result = res.responseText.strip();

	var ninusi_cd = '';
	var sosiki_cd = '';
	var sosiki_nm = '';

	var pageID    = '1';

	var get = getRequest();

	if (get.length != 0) {

		if (get["ninusi_cd"] != null) {
			ninusi_cd = get["ninusi_cd"];
		}

		if (get["sosiki_cd"] != null) {
			sosiki_cd = get["sosiki_cd"];
		}

		if (get["sosiki_nm"] != null) {
			sosiki = get["sosiki_nm"];
		}

	}

	location.href = "/DCMS/WEX012/index?" + result + "&ninusi_cd=" + ninusi_cd +
													 "&sosiki_cd=" + sosiki_cd +
													 "&sosiki_nm=" + sosiki_nm +
													 "&pageID=" + pageID +
													 "&display=true";

}
