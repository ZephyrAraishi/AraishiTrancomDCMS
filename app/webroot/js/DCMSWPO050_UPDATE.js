var actionUrl3 = '';
var actionUrl4 = '';
var actionUrl5 = '';
var actionUrl6 = '';

//アップデートイベント初期化
function initUpdate(url1,url2,url3,url4) {

	actionUrl3 = url1;
	actionUrl4 = url2;
	actionUrl5 = url3;
	actionUrl6 = url4;

	$("updateButton").observe("click",clickUploadButton);
}

//更新ボタンクリックイベントハンドラ
function clickUploadButton(event) {


	//全体を隠すカーテンをつける
	$$("body")[0].insert(createUpdatingCurtain());

		//行配列
	var resultArray = {
		'date' : $F("ymd_sime")
	};

	var postData = Object.toJSON(resultArray);

    //ポップアップを読み込む
    new Ajax.Request( actionUrl3, {
	  method : 'post',
	  parameters: postData,

	  onSuccess : onSuccessEvent,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WPO050/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WPO050/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
	  }
	});
}

//入力チェック・存在チェック後 -> 警告確認へ
function onSuccessEvent(res) {
	
	//レスポンス
	var result = escapePHP(res.responseText.trim());

	var ymd_sime = $F("ymd_sime");

	if(result != "return_cd=0") {

		//ロケーションの変更
		location.href = "/DCMS/WPO050/index?" + result + "&ymd_sime=" + ymd_sime +
														 "&display=true";
	} else {
		
		//確認メッセージ用AJAX
		var resultArray = {
			'date' : ymd_sime
		};
		
		var postData = Object.toJSON(resultArray);
			
	    //ポップアップを読み込む
	    new Ajax.Request( actionUrl4, {
		  method : 'post',
		  parameters: postData,
	
		  onSuccess : onSuccessEvent2,
	
		  onFailure : function( event )  {
		    location.href = "/DCMS/WPO050/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
		  },
		  onException : function( event, ex )  {
		    location.href = "/DCMS/WPO050/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
		  }
		});
	
	}
}

//警告確認後 -> 上書き確認へ
function onSuccessEvent2(res) {

	//レスポンス
	var result = escapePHP(res.responseText.trim());
	
	var ymd_sime = $F("ymd_sime");
	
	var return_cd = getParameterByName("return_cd",result);
	
	if (return_cd == "1") {
	
		//ロケーションの変更
		location.href = "/DCMS/WPO050/index?" + result + "&ymd_sime=" + ymd_sime +
														 "&display=true";
														 
		return;
	} else if (return_cd == "0_1") {
	
		var message = getParameterByName("message",result).replace(/%2B/g, "+"); ;
	
		if(!window.confirm(Base64.decode(message))){
	
			location.href = "/DCMS/WPO050/index?&ymd_sime=" + ymd_sime;
			return;
		}
		
	}
	
	//確認メッセージ用AJAX
	var resultArray = {
		'date' : ymd_sime,
	};
	
	var postData = Object.toJSON(resultArray);
		
    //ポップアップを読み込む
    new Ajax.Request( actionUrl5, {
	  method : 'post',
	  parameters: postData,

	  onSuccess : onSuccessEvent3,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WPO050/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WPO050/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
	  }
	});

}

//上書き確認後 -> 更新処理へ
function onSuccessEvent3(res) {

	//レスポンス
	var result = escapePHP(res.responseText.trim());
	
	var ymd_sime = $F("ymd_sime");
	
	var updateFlg = "0";
	
	if(result == "return_cd=0_1") {
		
		if(!window.confirm(DCMSMessage.format("CMNE000024"))){
	
			location.href = "/DCMS/WPO050/index?&ymd_sime=" + ymd_sime;
			return;
		}
		
		updateFlg = "1";
	}
	
	//確認メッセージ用AJAX
	var resultArray = {
		'date' : ymd_sime,
		'updateFlg' : updateFlg
	};
	
	var postData = Object.toJSON(resultArray);
		
    //ポップアップを読み込む
    new Ajax.Request( actionUrl6, {
	  method : 'post',
	  parameters: postData,

	  onSuccess : onSuccessEvent4,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WPO050/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WPO050/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
	  }
	});

}

//更新処理の後、帰ってきたデータの処理
function onSuccessEvent4(res) {

	//レスポンス
	var result = escapePHP(res.responseText.trim());
	
	var ymd_sime = $F("ymd_sime");
	

	//ロケーションの変更
	location.href = "/DCMS/WPO050/index?" + result + "&ymd_sime=" + ymd_sime + "&display=true";
	
}

//ゲットの値を取得
function getParameterByName(name,str) {
    
    var params = str.split("&");
    
    var result = "";
    
    for (var i=0; i < params.length; i++) {
	    
	    var params2 = params[i].split("=");
	    
	    if (params2[0] == name) {
		    
		    result = params2[1];
		    break;
	    }
    }
    
    return result;
}