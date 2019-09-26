var actionUrl3 = ''

function initOracleload(url) {

	actionUrl3 = url;
	
	$("refButton").observe("click",refClick);

}

function refClick(event) {
	
	$("reloadingMessage").setStyle({
		display:"block"
	});
	
    //ポップアップを読み込む
    new Ajax.Request( actionUrl3, {
    	
  	  method : 'post',
	  parameters: '',

	  onSuccess : onSuccessEvent,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WPO010/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WPO010/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
	  }
	});
}

//更新処理の後、帰ってきたデータの処理
function onSuccessEvent(res) {

	//レスポンス
	var result = escapePHP(res.responseText.trim());
	location.href = "/DCMS/WPO010/index?" + result + "&display=true";
													
}


