//日付変換
function fmtDate(strVal) {
	return strVal.split("/").join("");  
}
//日付変換
function prsDate(strVal) {
	// 数値以外の場合は何もしない
	if (isNaN(strVal)) {
		return strVal;
	}
	
	// 数値の文字列長が7文字以下の場合は何もしない
	if (strVal.length <= 7) {
		return strVal;
	}
	
	return strVal.substring(0, 4) + "/" + strVal.substring(4, 2) + "/" + strVal.substring(6);	
	  
}

//時間変換
function fmtTime(strVal) {
	return strVal.split(":").join("");  
}
//時間変換
function prsTime(strVal) {
	
	// 数値以外の場合は何もしない
	if (isNaN(strVal)) {
		return strVal;
	}
	
	// 数値の文字列長が2文字以下の場合は何もしない
	if (strVal.length <= 1) {
		return strVal;
	}
	
	switch (strVal.length) {
		case 2:
			// 時１桁　分１桁
			return "0" + (strVal.substring(0, 1) + ":0" + strVal.substring(1));
			break;
		case 3:
			// 時１桁　分２桁
			return "0" + (strVal.substring(0, 1) + ":" + strVal.substring(1));
			break;
		case 4:
			// 時２桁　分２桁
			return (strVal.substring(0, 2) + ":" + strVal.substring(2));
			break;
	}
		  
}