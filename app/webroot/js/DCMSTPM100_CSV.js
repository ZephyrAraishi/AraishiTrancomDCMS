var actionUrl2 = ''
var actionUrl3 = ''

function initCSV(url1) {

	actionUrl2 = url1;

	$("csvButton").observe("click",clickCSVButton);
}

function initLabelCSV(url2) {

	actionUrl3 = url2;

	$("LabelCsvButton").observe("click",clickLabelCSVButton);
}

function clickCSVButton(event) {

	//GETの送信データを作る
	var executeUrl = actionUrl2 + "?ymd_from=" + $("YMD_FROM_H").innerHTML + "&ymd_to=" + $("YMD_TO_H").innerHTML + "&ymd_from_n=" + $("YMD_FROM_N").innerHTML + "&ymd_to_n=" + $("YMD_TO_N").innerHTML + "&tokuisaki=" + $("TOKUISAKI").innerHTML + "&homen=" + $("HOMEN").innerHTML + "&tenpo=" + $("TENPO").innerHTML;

	location.href = executeUrl;

}

function clickLabelCSVButton(event) {

	//GETの送信データを作る
	var executeUrl = actionUrl3 + "?ymd_from=" + $("YMD_FROM_H").innerHTML + "&ymd_to=" + $("YMD_TO_H").innerHTML + "&ymd_from_n=" + $("YMD_FROM_N").innerHTML + "&ymd_to_n=" + $("YMD_TO_N").innerHTML + "&tokuisaki=" + $("TOKUISAKI").innerHTML + "&homen=" + $("HOMEN").innerHTML + "&tenpo=" + $("TENPO").innerHTML;

	location.href = executeUrl;

}
