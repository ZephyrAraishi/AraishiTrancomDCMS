var actionUrl3 = "";

//
//リロード時の初期処理
function initReload() {



	//更新メッセージ
	var get = getRequest();

	if (get.length != 0) {

		if (get["return_cd"] != null) {

			//画面上部にメッセージを表示
			if (get["return_cd"] == "0") {

					$("infoMsg").update(Base64.decode(get["message"]));
			}  else {

					$("errorMsg").update(Base64.decode(get["message"]));
			}
		}

	}



}




//
//アップデートイベント初期か
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

	var ninusi_nm = $F("ninusi_nm");				// 荷主名
	var ninusi_ryaku = $F("ninusi_ryaku");			// 会社名略
	var ninusi_post_no = $F("ninusi_post_no");		// 荷主郵便番号
	var ninusi_addr_1 = $F("ninusi_addr_1");		// 住所１
	var ninusi_addr_2 = $F("ninusi_addr_2");		// 住所２
	var ninusi_addr_3 = $F("ninusi_addr_3");		// 住所３
	var ninusi_tel = $F("ninusi_tel");				// TEL
	var ninusi_fax = $F("ninusi_fax");				// FAX
	var ninusi_dai_nm = $F("ninusi_dai_nm");		// 代表者
	var ninusi_est_ym = $F("ninusi_est_ym");		// 設立
	var ninusi_sihon = $F("ninusi_sihon");			// 資本金
	var ninusi_jigyo = $F("ninusi_jigyo");			// 事業内容
	var sosiki_nm = $F("sosiki_nm");				// 倉庫部署名
	var sosiki_ryaku = $F("sosiki_ryaku");			// 倉庫部署名略
	var sosiki_post_no = $F("sosiki_post_no");		// 組織郵便番号
	var sosiki_addr_1 = $F("sosiki_addr_1");		// 住所１
	var sosiki_addr_2 = $F("sosiki_addr_2");		// 住所２
	var sosiki_addr_3 = $F("sosiki_addr_3");		// 住所３
	var sosiki_tel = $F("sosiki_tel");				// TEL
	var sosiki_fax = $F("sosiki_fax");				// FAX
	var sosiki_dai_nm = $F("sosiki_dai_nm");		// 倉庫責任者
	var seikyu_busyo_nm = $F("seikyu_busyo_nm");	// 請求部署名
	var seikyu_post_no = $F("seikyu_post_no");		// 請求部署郵便番号
	var seikyu_addr_1 = $F("seikyu_addr_1");		// 請求部署住所１
	var seikyu_addr_2 = $F("seikyu_addr_2");		// 請求部署住所２
	var seikyu_addr_3 = $F("seikyu_addr_3");		// 請求部署住所３
	var seikyu_tel = $F("seikyu_tel");				// 請求部署TEL
	var seikyu_fax = $F("seikyu_fax");				// 請求部署FAX
	var seikyu_dai_nm = $F("seikyu_dai_nm");		// 請求担当者

	var resultArray = {
			"ninusi_nm" : ninusi_nm,
			"ninusi_ryaku" : ninusi_ryaku,
			"ninusi_post_no" : ninusi_post_no,
			"ninusi_addr_1" : ninusi_addr_1,
			"ninusi_addr_2" : ninusi_addr_2,
			"ninusi_addr_3" : ninusi_addr_3,
			"ninusi_tel" : ninusi_tel,
			"ninusi_fax" : ninusi_fax,
			"ninusi_dai_nm" : ninusi_dai_nm,
			"ninusi_est_ym" : ninusi_est_ym,
			"ninusi_sihon" : ninusi_sihon,
			"ninusi_jigyo" : ninusi_jigyo,
			"sosiki_nm" : sosiki_nm,
			"sosiki_ryaku" : sosiki_ryaku,
			"sosiki_post_no" : sosiki_post_no,
			"sosiki_addr_1" : sosiki_addr_1,
			"sosiki_addr_2" : sosiki_addr_2,
			"sosiki_addr_3" : sosiki_addr_3,
			"sosiki_tel" : sosiki_tel,
			"sosiki_fax" : sosiki_fax,
			"sosiki_dai_nm" : sosiki_dai_nm,
			"seikyu_busyo_nm" : seikyu_busyo_nm,
			"seikyu_post_no" : seikyu_post_no,
			"seikyu_addr_1" : seikyu_addr_1,
			"seikyu_addr_2" : seikyu_addr_2,
			"seikyu_addr_3" : seikyu_addr_3,
			"seikyu_tel" : seikyu_tel,
			"seikyu_fax" : seikyu_fax,
			"seikyu_dai_nm" : seikyu_dai_nm,
		};


	//ポストの送信データを作る
	//JSONへ変更
	var postData = Object.toJSON(resultArray);

    //ポップアップを読み込む
    new Ajax.Request( actionUrl3, {
	  method : 'post',
	  parameters: postData,

	  onSuccess : onSuccessEvent,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WEX010/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WEX010/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
	  }
	});
}

//
//更新成功時
function onSuccessEvent(res) {

	//レスポンス
	var result = escapePHP(res.responseText.trim());

	location.href = "/DCMS/WEX010/index?" + result + "&display=true";

}
