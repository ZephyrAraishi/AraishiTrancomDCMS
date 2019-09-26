var actionUrl3 = ''

//アップデートイベント初期化
function initPDF(url1) {

	actionUrl3 = url1;

	$("pdfButton").observe("click",clickPDFButton);
}

//更新ボタンクリックイベントハンドラ
function clickPDFButton(event) {

		$ymd_from = '';
		$ymd_to = '';
		$kaisya_cd = '';
		$staff_cd_nm = '';
		$bnri_cyu_cd = '';
		$syaryo_cd = '';
		$area_cd = '';
		$more = '';
		$jikan_from = '';
		$jikan_to = '';
		$kyori_from = '';
		$kyori_to = '';
		
	//GETの送信データを作る
	var executeUrl = actionUrl3 + "?ymd_from=" + $("h_ymd_from").innerHTML
								+ "&ymd_to=" + $("h_ymd_to").innerHTML
								+ "&kaisya_cd=" + $("h_kaisya_cd").innerHTML
								+ "&staff_cd_nm=" + $("h_staff_cd_nm").innerHTML
								+ "&bnri_cyu_cd=" + $("h_bnri_cyu_cd").innerHTML
								+ "&area_cd=" + $("h_area_cd").innerHTML
        						+ "&syaryo_cd=" + $("h_syaryo_cd").innerHTML
								+ "&more=" + $("h_more").innerHTML
								+ "&jikan_from=" + $("h_jikan_from").innerHTML
								+ "&jikan_to=" + $("h_jikan_to").innerHTML
								+ "&kyori_from=" + $("h_kyori_from").innerHTML
								+ "&kyori_to=" + $("h_kyori_to").innerHTML;

	location.href = executeUrl;


}

