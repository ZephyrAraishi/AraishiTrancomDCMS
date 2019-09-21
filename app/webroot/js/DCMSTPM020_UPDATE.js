//アップデートイベント初期か
function initUpdate() {

	$("updateButton").observe("click",clickUploadButton);
}

function clickUploadButton(event) {

	//全体を隠すカーテンをつける
	$$("body")[0].insert(createUpdatingCurtain());

	var resultArray = new Array();

	//ポストの送信データを作る
	//JSONへ変更

	var postData = Object.toJSON(resultArray);

    //ポップアップを読み込む
    new Ajax.Request( '../TPM020/dataUpdate', {
	  method : 'post',
	  parameters: postData,

	  onSuccess : onSuccessEvent,

	  onFailure : function( event )  {
	    location.href = "/DCMS/TPM020/index?return_cd=91&message=" + Base64.encode("更新データの送信に失敗しました。");
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/TPM020/index?return_cd=92&message=" + Base64.encode("更新データの送信中に例外が発生しました。");
	  }
	});
}

function onSuccessEvent(res) {

	var result = res.responseText.strip();

	location.href = "/DCMS/TPM020/index?" + result;

}
