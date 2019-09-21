
function initLink() {

	//行全体を取得
	var rows = $("lstTable").getElementsBySelector(".tableRow");

	//イベントを設定
    for(i=0; i < rows.length; i++)
	{

		rows[i].observe("click",clickLink);
		rows[i].observe("mouseover",overRow);
		rows[i].observe("mouseout",leaveRow);

	}
}

//行のオーバーイベントハンドラ
//フォーカスマークをつける
function overRow(event) {

	//選択行を取得
	var row = Event.element(event).up("div");

	if (row.hasClassName("tableRow") == false) {

		while (true) {

			row = row.up("div");

			if (row.hasClassName("tableRow") == true) {

				break;
			}
		}
	}

	//選択行が見つからなければリターン
	if (row == null)
	{
		return;
	}

	//選択行の色を設定
	setSelectedRowColor(row);

}

//行のリーブイベントハンドラ
//フォーカスマークを外す
function leaveRow(event) {

	//選択行を取得
	var row = Event.element(event).up("div");

	if (row.hasClassName("tableRow") == false) {

		while (true) {

			row = row.up("div");

			if (row.hasClassName("tableRow") == true) {

				break
			}
		}
	}

	if (row == null)
	{
		return;
	}

	//行の色を設定
	var data = row.getElementsBySelector(".hiddenData");
	var val = data[0].innerHTML.trim();
	
	if (val == "1") {
		
		setDeletedRowColor(row)
	} else {
		
		setRowColor("0", row)
	}
}

//行のクリックイベントハンドラ
function clickLink(event) {



	//選択行を取得
	var row = Event.element(event).up("div");

	if (row.hasClassName("tableRow") == false) {

		while (true) {

			row = row.up("div");

			if (row.hasClassName("tableRow") == true) {

				break
			}
		}
	}

	//選択行を保持
	var selectedRow = row;

	var staff_cd = row.getElementsBySelector(".tableCell")[0].innerHTML.trim();
	
		//ロケーションの変更
	location.href = "/DCMS/HRD010/kihon?staff_cd=" + staff_cd;

}
