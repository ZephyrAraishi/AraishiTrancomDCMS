var actionUrl2 = '';

//アップデートイベント初期か
function initUpdate(url) {

	actionUrl2 = url;

	$("updateButton").observe("click",clickUploadButton);
}

function clickUploadButton(event) {

	//全体を隠すカーテンをつける
	$$("body")[0].insert(createUpdatingCurtain());

	var resultArray = new Array();

	//データを取得する
	var resultRows = $("lstTable").getElementsBySelector(".tableRow");

	for (var i=0; i < resultRows.length; i++) {

		var data = resultRows[i].getElementsBySelector(".hiddenData");


		if (data[8].innerHTML.trim() == "1") {

			var array = {

				"S_BATCH_NO_CD" : data[1].innerHTML.trim(),
				"S_KANRI_NO" : data[3].innerHTML.trim(),
				"KBN_STATUS" : data[5].innerHTML.trim(),
				"YMD_SYORI" : data[6].innerHTML.trim()
			};

			resultArray[resultArray.length] = array;

		}
	}

	//ポストの送信データを作る
	//JSONへ変更
	var postData = Object.toJSON(resultArray);

    //ポップアップを読み込む
    new Ajax.Request( actionUrl2, {
	  method : 'post',
	  parameters: postData,

	  onSuccess : onSuccessEvent,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WPO020/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WPO020/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
	  }
	});
}

//更新処理の後、帰ってきたデータの処理
function onSuccessEvent(res) {

	//レスポンス
	var result = escapePHP(res.responseText.trim());

	var batch_nm_cd = '';
	var s_batch_no = '';
	var s_kanri_no = '';
	var zone = '';
	var staff_nm = '';
	var kbn_status = '';
	var pageID = '1';

	//ゲットデータを取得
	var get = getRequest();

	//ゲットの長さを確認
	if (get.length != 0) {

		if (get["batch_nm_cd"] != null) {
			batch_nm_cd = get["batch_nm_cd"];
		}

		if (get["s_batch_no"] != null) {
			s_batch_no = decodeURI(get["s_batch_no"]);
		}

		if (get["s_kanri_no"] != null) {
			s_kanri_no = decodeURI(get["s_kanri_no"]);
		}

		if (get["zone"] != null) {

			zone = get["zone"];
		}

		if (get["staff_nm"] != null) {
			staff_nm = decodeURI(get["staff_nm"]);
		}

		if (get["kbn_status"] != null) {
			kbn_status = get["kbn_status"];
		}
		
		if (get["pageID"] != null) {
			pageID = decodeURI(get["pageID"]);
		} 

	}

	location.href = "/DCMS/WPO023/index?" + result + "&batch_nm_cd=" + batch_nm_cd +
													 "&s_batch_no=" + s_batch_no +
													 "&s_kanri_no=" + s_kanri_no +
													 "&zone=" + zone +
													 "&staff_nm=" + staff_nm +
													 "&kbn_status=" + kbn_status +
													 "&pageID=" + pageID +
													 "&display=true";
}
