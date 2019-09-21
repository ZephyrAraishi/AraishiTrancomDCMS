var actionUrl2 = ''

//アップデートイベント初期か
function initImport(url) {

	actionUrl2 = url;

	$("importButton").observe("click",clickImportButton);
}

function clickImportButton(event) {

	if ( document.getElementById("shukka_file").value.length == 0 ) {
		alert("ファイルを指定してください");
		return;
	}

	//全体を隠すカーテンをつける
	$$("body")[0].insert(createUpdatingCurtain());

	//ポストの送信データを作る
	var formData = new FormData(document.getElementById("shukka_form"));
	

    //リクエスト送信
    new Ajax.Request( actionUrl2, {
	  method : 'post',
	  postBody: formData,

	  onSuccess : onSuccessEvent2,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WPO080/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WPO080/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
	  }
	});
}

function onSuccessEvent2(res) {

	var result = escapePHP(res.responseText.trim());
	location.href = "/DCMS/WPO080/index?" + result + "&display=true";

}
