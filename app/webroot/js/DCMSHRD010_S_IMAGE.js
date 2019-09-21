//画像関連の初期化
function initImage() {
	
	$("refButton").observe("click",clickRef);
	
	if ($("imagePath").getValue() != "") {
		$("imageUploadButton").click();
	}
}

//参照ボタン
function clickRef(event) {
	$("imagePath").click();
}

//画像を表示する
function showImage(encodedString) {
	
	$("upload-image").writeAttribute("src","data:image/png;base64," + encodedString)
}