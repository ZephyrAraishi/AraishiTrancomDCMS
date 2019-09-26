var actionUrl = ''

//アップデートイベント初期化
function initUpdate(url) {

	actionUrl = url;

	$("updateButton").observe("click",clickUploadButton);
}

//更新ボタンクリックイベントハンドラ
function clickUploadButton(event) {

	//更新アラート
	if(!window.confirm(DCMSMessage.format("CMNQ000001"))){
		return;
	}

	//全体を隠すカーテンをつける
	$$("body")[0].insert(createUpdatingCurtain());
	
	//拠点選択
	var rows = $("lstTable1").getElementsBySelector(".tableRow");
	
	var image = $("upload-image").readAttribute("src");
	
	if (image != "/DCMS/img/no-image.gif") {
		
		image = image.replace("data:image/png;base64,", "")
	} else {
		
		image = "";
	}
	
	
	var kyotenArray = new Array();
	
	for (var i=0; i < rows.length; i++) {
		
		var cells = rows[i].getElementsBySelector(".tableCell");
		var data = rows[i].getElementsBySelector(".hiddenData");
		
		var cellsArray = new Array();
		
		
		
		cellsArray[0] = cells[0].getElementsBySelector("input")[0].checked;
		cellsArray[1] = cells[1].getElementsBySelector("input")[0].checked;
		cellsArray[2] = cells[2].innerHTML.trim();
		cellsArray[3] = cells[3].innerHTML.trim();
		
		var dataArray = new Array();
		
		dataArray[0] = data[0].innerHTML.trim();
		dataArray[1] = data[1].innerHTML.trim();
		
		kyotenArray[i] = {
			"cells" : cellsArray,
			"data" : dataArray
		}
	}
	
	//優先ゾーン
	rows = $("lstTable2").getElementsBySelector(".tableRow");
	
	var yusenzoneArray = new Array();
	
	for (var i=0; i < rows.length; i++) {
		
		var cells = rows[i].getElementsBySelector(".tableCell");
		var data = rows[i].getElementsBySelector(".hiddenData");
		
		var cellsArray = new Array();
		
		cellsArray[0] = cells[0].getElementsBySelector("input")[0].checked;
		cellsArray[1] = cells[1].innerHTML.trim();
		
		var dataArray = new Array();
		
		dataArray[0] = data[0].innerHTML.trim();
		
		yusenzoneArray[i] = {
			"cells" : cellsArray,
			"data" : dataArray
		}
	}

	//渡す配列
	var resultArray = {
			'STAFF_CD' : $("staff_cd").innerHTML.trim(),
			'STAFF_NM' : $("staff_nm").getValue(),
			'PASSWORD' : $("password").getValue(),
			'IC_NO' : $("ic_no").getValue(),
			'KBN_SEX' : $("kbn_sex").getValue(),
			'YMD_BIRTH' : $("ymd_birth").getValue(),
			'HAKEN_KAISYA_CD' : $("haken_kaisya_cd").getValue(),
			'IMAGE' : image,
			'HAKEN_KAISYA_CD' : $("haken_kaisya_cd").getValue(),
			'KYOTEN' : kyotenArray,
			'KBN_PST' : $("kbn_pst").getValue(),
			'KBN_KEIYAKU' : $("kbn_keiyaku").getValue(),
			'KBN_PICK' : $("kbn_pick").getValue(),
			'YUSEN_ZONE' : yusenzoneArray,
			'RANK' : $("rank").getValue(),
			'KIN_SYOTEI' : $("kin_syotei").getValue(),
			'KIN_H_KST' : $("kin_h_kst").getValue(),
			'KIN_H_KST_SNY' : $("kin_h_kst_sny").getValue(),
			'KIN_JIKANGAI' : $("kin_jikangai").getValue(),
			'KIN_SNY' : $("kin_sny").getValue(),
			'KIN_KST' : $("kin_kst").getValue(),
			'SUN_TIME_ST' : $("sun_time_st").getValue(),
			'SUN_TIME_ED' : $("sun_time_ed").getValue(),
			'SUN_BIKO' : $("sun_biko").getValue(),
			'MON_TIME_ST' : $("mon_time_st").getValue(),
			'MON_TIME_ED' : $("mon_time_ed").getValue(),
			'MON_BIKO' : $("mon_biko").getValue(),
			'TUE_TIME_ST' : $("tue_time_st").getValue(),
			'TUE_TIME_ED' : $("tue_time_ed").getValue(),
			'TUE_BIKO' : $("tue_biko").getValue(),
			'WED_TIME_ST' : $("wed_time_st").getValue(),
			'WED_TIME_ED' : $("wed_time_ed").getValue(),
			'WED_BIKO' : $("wed_biko").getValue(),
			'THU_TIME_ST' : $("thu_time_st").getValue(),
			'THU_TIME_ED' : $("thu_time_ed").getValue(),
			'THU_BIKO' : $("thu_biko").getValue(),
			'FRI_TIME_ST' : $("fri_time_st").getValue(),
			'FRI_TIME_ED' : $("fri_time_ed").getValue(),
			'FRI_BIKO' : $("fri_biko").getValue(),
			'SAT_TIME_ST' : $("sat_time_st").getValue(),
			'SAT_TIME_ED' : $("sat_time_ed").getValue(),
			'SAT_BIKO' : $("sat_biko").getValue(),
			'YMD_SYUGYO_ST' : $("ymd_syugyo_st").getValue(),
			'YMD_SYUGYO_ED' : $("ymd_syugyo_ed").getValue(),
			'SYUKIN_NISU_MAX' : $("syukin_nisu_max").getValue(),
			'SYUKIN_TIME_MAX' : $("syukin_time_max").getValue(),
			'CHINGIN_MAX' : $("chingin_max").getValue(),
			'TUIKI_JIKO' : $("tuiki_jiko").getValue(),
			'DEL_FLG' : $("del_flg").getValue()
		};



	var postData = Object.toJSON(resultArray);

    //ポップアップを読み込む
    new Ajax.Request( actionUrl, {
	  method : 'post',
	  parameters: postData,

	  onSuccess : onSuccessEvent,

	  onFailure : function( event )  {
	    location.href = "/DCMS/HRD010/kihon?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/HRD010/kihon?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
	  }
	});
}

//更新処理の後、帰ってきたデータの処理
function onSuccessEvent(res) {
	
	//レスポンス
	var result = escapePHP(res.responseText.trim());
	
	var staff_cd;
	
	//ゲットデータを取得
	var get = getRequest();
	
	if (get["staff_cd"] != null) {
		staff_cd = decodeURI(get["staff_cd"]);
	}

	//ロケーションの変更
	location.href = "/DCMS/HRD010/kihon?" + result + "&staff_cd=" + staff_cd +
													 "&display=true";
}
