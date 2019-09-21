var actionUrl2;

//アップデートイベント初期化
function initUpdate(url) {

	actionUrl2 = url;

	updateButton.observe("click",clickUploadButton);
	
	$$("body")[0].setStyle({
		background: "lightyellow"
	});
}

//更新ボタンクリックイベントハンドラ
function clickUploadButton(event) {

	//チェックアラート
	var date = $F("dateA");
	var time = $F("timeA");
	var seisansei = $F("seisanseiA");
	
	if (date == "") {

		alert(DCMSMessage.format("CMNE000001", $("nameA").innerHTML.trim() + "の日付"));
		return;
	}
	
	if (time == "" ) {

		alert(DCMSMessage.format("CMNE000001", $("nameA").innerHTML.trim() + "の時間"));
		return;
	}

	if (!seisansei.match(/^[0-9]+/)) {

		alert(DCMSMessage.format("CMNE000001", $("nameA").innerHTML.trim() + "のソーター生産性"));
		return;
	}

	if (DCMSValidation.checkDate(date) == false) {
		alert(DCMSMessage.format("CMNE000007", $("nameA").innerHTML.trim() + "のソーター開始時間の日付"));
		return;
	}
	
	var seisanseiA = seisansei;

	
	var sorter_date = computeDate(date.substr(0,4),date.substr(4,2),date.substr(6,2),0);
	
	var today_string = $("timeStamp").innerHTML.trim();
	var base_date = computeDate(today_string.substr(0,4),today_string.substr(5,2),today_string.substr(8,2),-1);
	
	if(base_date.getTime() > sorter_date.getTime()) {
	    alert($("nameA").innerHTML.trim() + "のソーター開始時間の日付は前日以降に設定してください。");
		return;
	}

		
	if (!time.match(/^[0-9]{4}/)) {
		alert(DCMSMessage.format("CMNE000004", $("nameA").innerHTML.trim() + "のソーター開始時間の時刻", "4"));
		return;
	}
	
	if (DCMSValidation.checkHour(time.substr(0,2)) == false ) {

		alert(DCMSMessage.format("CMNE000007",　$("nameA").innerHTML.trim() + "のソーター開始時間の時刻"));
		return;
	}

	if (DCMSValidation.checkTime(time.substr(2,2)) == false ) {

		alert(DCMSMessage.format("CMNE000007",　$("nameA").innerHTML.trim() + "のソーター開始時間の時刻"));
		return;
	}

	
	date = $F("dateB");
	time = $F("timeB");
	seisansei = $F("seisanseiB");
	
	if (date == "") {

		alert(DCMSMessage.format("CMNE000001", $("nameB").innerHTML.trim() + "の日付"));
		return;
	}
	
	if (time == "" ) {

		alert(DCMSMessage.format("CMNE000001", $("nameB").innerHTML.trim() + "の時間"));
		return;
	}

	if (!seisansei.match(/^[0-9]+/)) {

		alert(DCMSMessage.format("CMNE000001", $("nameB").innerHTML.trim() + "のソーター生産性"));
		return;
	}

	if (DCMSValidation.checkDate(date) == false) {
		alert(DCMSMessage.format("CMNE000007", $("nameB").innerHTML.trim() + "のソーター開始時間の日付"));
		return;
	}
	
	var seisanseiB = seisansei;

	
	sorter_date = computeDate(date.substr(0,4),date.substr(4,2),date.substr(6,2),0);
	
	today_string = $("timeStamp").innerHTML.trim();
	base_date = computeDate(today_string.substr(0,4),today_string.substr(5,2),today_string.substr(8,2),-1);
	
	if(base_date.getTime() > sorter_date.getTime()) {
	    alert($("nameB").innerHTML.trim() + "のソーター開始時間の日付は前日以降に設定してください。");
		return;
	}

		
	if (!time.match(/^[0-9]{4}/)) {
		alert(DCMSMessage.format("CMNE000004", $("nameB").innerHTML.trim() + "のソーター開始時間の時刻", "4"));
		return;
	}
	
	if (DCMSValidation.checkHour(time.substr(0,2)) == false ) {

		alert(DCMSMessage.format("CMNE000007",　$("nameB").innerHTML.trim() + "のソーター開始時間の時刻"));
		return;
	}

	if (DCMSValidation.checkTime(time.substr(2,2)) == false ) {

		alert(DCMSMessage.format("CMNE000007",　$("nameB").innerHTML.trim() + "のソーター開始時間の時刻"));
		return;
	}
	
	date = $F("dateC");
	time = $F("timeC");
	seisansei = $F("seisanseiC");
	
	if (date == "") {

		alert(DCMSMessage.format("CMNE000001", $("nameC").innerHTML.trim() + "の日付"));
		return;
	}
	
	if (time == "" ) {

		alert(DCMSMessage.format("CMNE000001", $("nameC").innerHTML.trim() + "の時間"));
		return;
	}

	if (!seisansei.match(/^[0-9]+/)) {

		alert(DCMSMessage.format("CMNE000001", $("nameC").innerHTML.trim() + "のソーター生産性"));
		return;
	}

	if (DCMSValidation.checkDate(date) == false) {
		alert(DCMSMessage.format("CMNE000007", $("nameC").innerHTML.trim() + "のソーター開始時間の日付"));
		return;
	}
	
	var seisanseiC = seisansei;

	
	sorter_date = computeDate(date.substr(0,4),date.substr(4,2),date.substr(6,2),0);
	
	today_string = $("timeStamp").innerHTML.trim();
	base_date = computeDate(today_string.substr(0,4),today_string.substr(5,2),today_string.substr(8,2),-1);
	
	if(base_date.getTime() > sorter_date.getTime()) {
	    alert($("nameC").innerHTML.trim() + "のソーター開始時間の日付は前日以降に設定してください。");
		return;
	}

		
	if (!time.match(/^[0-9]{4}/)) {
		alert(DCMSMessage.format("CMNE000004", $("nameC").innerHTML.trim() + "のソーター開始時間の時刻", "4"));
		return;
	}
	
	if (DCMSValidation.checkHour(time.substr(0,2)) == false ) {

		alert(DCMSMessage.format("CMNE000007",　$("nameC").innerHTML.trim() + "のソーター開始時間の時刻"));
		return;
	}

	if (DCMSValidation.checkTime(time.substr(2,2)) == false ) {

		alert(DCMSMessage.format("CMNE000007",　$("nameC").innerHTML.trim() + "のソーター開始時間の時刻"));
		return;
	}

	//全体を隠すカーテンをつける
	$$("body")[0].insert(createUpdatingCurtain());

	//データを取得する
	var resultSorterElement1 = new Array();
	var resultSorterElement2 = new Array();
	var resultSorterElement3 = new Array();
	var resultSorterElement4 = new Array();

	var panels = $(DIV_PANEL_AREA_ID).getElementsBySelector("." + DIV_PANEL_CLASS);

	for (var i=0; i < panels.length; i++) {

		var left = panels[i].getStyle("left").replace("px", "");

		if (left == "0") {

			resultSorterElement1[resultSorterElement1.length] = panels[i];
		} else if (left =="330") {

			resultSorterElement2[resultSorterElement2.length] = panels[i];
		} else if (left =="660") {

			resultSorterElement3[resultSorterElement3.length] = panels[i];
		} else if (left =="990") {

			resultSorterElement4[resultSorterElement4.length] = panels[i];
		}
	}

	var resultSorter1 = new Array();
	var resultSorter2 = new Array();
	var resultSorter3 = new Array();
	var resultSorter4 = new Array();

	for (var i=0; i < resultSorterElement1.length; i++) {

		var S_BATCH_NO_CD;
		var TOP_PRIORITY_FLG;
		var YMD_SYORI;
		var CURRENT_ORDER;
		var CURRENT_SORTER;
		var OLD_SORTER;

		var data = resultSorterElement1[i].getElementsBySelector(".hiddenData");
		
		if (data[7].innerHTML.trim() == SORTER_CD_4) {
			
			OLD_SORTER = "";
		} else {
			OLD_SORTER = data[7].innerHTML.trim();
		}

		var array = {

			"S_BATCH_NO_CD" : data[0].innerHTML.trim(),
			"TOP_PRIORITY_FLG" : data[3].innerHTML.trim(),
			"YMD_SYORI" : data[6].innerHTML.trim(),
			"OLD_SORTER" : OLD_SORTER,
			"SORTER" : data[9].innerHTML.trim(),
			"ORDER" : data[10].innerHTML.trim(),
			"WMS_FLG" : data[11].innerHTML.trim()
		};

		resultSorter1[resultSorter1.length] = array;
	}

	for (var i=0; i < resultSorterElement2.length; i++) {

		var S_BATCH_NO_CD;
		var TOP_PRIORITY_FLG;
		var YMD_SYORI;
		var CURRENT_ORDER;
		var CURRENT_SORTER;
		var OLD_SORTER;

		var data = resultSorterElement2[i].getElementsBySelector(".hiddenData");
		
		if (data[7].innerHTML.trim() == SORTER_CD_4) {
			
			OLD_SORTER = "";
		} else {
			OLD_SORTER = data[7].innerHTML.trim();
		}

		var array = {

			"S_BATCH_NO_CD" : data[0].innerHTML.trim(),
			"TOP_PRIORITY_FLG" : data[3].innerHTML.trim(),
			"YMD_SYORI" : data[6].innerHTML.trim(),
			"OLD_SORTER" : OLD_SORTER,
			"SORTER" : data[9].innerHTML.trim(),
			"ORDER" : data[10].innerHTML.trim(),
			"WMS_FLG" : data[11].innerHTML.trim()
		};
		
		resultSorter2[resultSorter2.length] = array;
	}

	for (var i=0; i < resultSorterElement3.length; i++) {

		var S_BATCH_NO_CD;
		var TOP_PRIORITY_FLG;
		var YMD_SYORI;
		var CURRENT_ORDER;
		var CURRENT_SORTER;
		var OLD_SORTER;

		var data = resultSorterElement3[i].getElementsBySelector(".hiddenData");
		
		if (data[7].innerHTML.trim() == SORTER_CD_4) {
			
			OLD_SORTER = "";
		} else {
			OLD_SORTER = data[7].innerHTML.trim();
		}

		var array = {

			"S_BATCH_NO_CD" : data[0].innerHTML.trim(),
			"TOP_PRIORITY_FLG" : data[3].innerHTML.trim(),
			"YMD_SYORI" : data[6].innerHTML.trim(),
			"OLD_SORTER" : OLD_SORTER,
			"SORTER" : data[9].innerHTML.trim(),
			"ORDER" : data[10].innerHTML.trim(),
			"WMS_FLG" : data[11].innerHTML.trim()
		};
		
		resultSorter3[resultSorter3.length] = array;
	}
	
	for (var i=0; i < resultSorterElement4.length; i++) {

		var S_BATCH_NO_CD;
		var TOP_PRIORITY_FLG;
		var YMD_SYORI;
		var CURRENT_ORDER;
		var CURRENT_SORTER;
		var OLD_SORTER;

		var data = resultSorterElement4[i].getElementsBySelector(".hiddenData");
		
		if (data[7].innerHTML.trim() == SORTER_CD_4) {
			
			OLD_SORTER = "";
		} else {
			OLD_SORTER = data[7].innerHTML.trim();
		}

		var array = {

			"S_BATCH_NO_CD" : data[0].innerHTML.trim(),
			"TOP_PRIORITY_FLG" : data[3].innerHTML.trim(),
			"YMD_SYORI" : data[6].innerHTML.trim(),
			"OLD_SORTER" : OLD_SORTER,
			"SORTER" : "",
			"ORDER" : data[10].innerHTML.trim(),
			"WMS_FLG" : data[11].innerHTML.trim()
		};
		
		resultSorter4[resultSorter4.length] = array;
	}
	
	var datetimeA = "";
	var datetimeB = "";
	var datetimeC = "";
	
	date = $F("dateA");
	time = $F("timeA");

	
	if (date != "" && time != "") {
		
		datetimeA = date.substr(0,4) + "-" + date.substr(4,2) + "-" + date.substr(6,2) + " " +
					time.substr(0,2) + ":" + time.substr(2,2) + ":00";
	}
	
	date = $F("dateB");
	time = $F("timeB");

	
	if (date != "" && time != "") {
		
		datetimeB = date.substr(0,4) + "-" + date.substr(4,2) + "-" + date.substr(6,2) + " " +
					time.substr(0,2) + ":" + time.substr(2,2) + ":00";
	}
	
	date = $F("dateC");
	time = $F("timeC");

	
	if (date != "" && time != "") {
		
		datetimeC = date.substr(0,4) + "-" + date.substr(4,2) + "-" + date.substr(6,2) + " " +
					time.substr(0,2) + ":" + time.substr(2,2) + ":00";
	}

	var resultArray = {

			"DateTimeA" : datetimeA,
			"DateTimeB" : datetimeB,
			"DateTimeC" : datetimeC,
			"A" : resultSorter1,
			"B" : resultSorter2,
			"C" : resultSorter3,
			"K" : resultSorter4,
			"TimeStamp" : $("timeStamp").innerHTML,
			"SeisanseiA" : seisanseiA,
			"SeisanseiB" : seisanseiB,
			"SeisanseiC" : seisanseiC
		};

	//ポストの送信データを作る
	//JSONへ変更

	var postData = Object.toJSON(resultArray);

    //ポップアップを読み込む
    new Ajax.Request( actionUrl2, {
	  method : 'post',
	  parameters: postData,

	  onSuccess : onSuccessEvent,

	  onFailure : function( event )  {
	    location.href = "/DCMS/TPM040/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/TPM040/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
	  }
	});
}

function onSuccessEvent(res) {

	var result = escapePHP(res.responseText.trim());

	location.href = "/DCMS/TPM040/index?" + result + "&display=true";

}
