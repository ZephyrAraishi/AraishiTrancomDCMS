var actionUrl3 = ''

//アップデートイベント初期か
function initUpdate(url) {

	actionUrl3 = url;

	$("updateButton").observe("click",clickUpdateButton);
}

function clickUpdateButton(event) {

	//全体を隠すカーテンをつける
	$$("body")[0].insert(createUpdatingCurtain());

	//ポストの送信データを作る
	var formData = new FormData();

    //リクエスト送信
    new Ajax.Request( actionUrl3, {
	  method : 'post',
	  postBody: formData,

	  onSuccess : onSuccessEvent3,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WPO070/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WPO070/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
	  }
	});
}

function onSuccessEvent3(res) {

	var result = escapePHP(res.responseText.trim());
	location.href = "/DCMS/WPO070/index?" + result + "&display=true";
}
