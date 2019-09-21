
function initFileUpload() {

	$("refButton").observe("click",refClick);
	$("uploadForm").observe("submit",clickFileSelect);

}

//参照ボタン
function refClick(event) {
	$("refText").click();
}

//取込ボタン
function clickFileSelect(event) {

	//全体を隠すカーテンをつける
	$$("body")[0].insert(createUpdatingCurtain());

}

