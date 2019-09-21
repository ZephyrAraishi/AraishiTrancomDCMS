/**
 * 入力チェック用の関数
 *
 */
var DCMSValidation = {

/**
 * 時間チェック
 *
 * @param minutes 分
 */
checkTime : function (minutes) {

	if ( parseInt(minutes, 10) >= 60) {

		return false;
	}

    return true;
},

/**
 * 時間チェック
 *
 * @param minutes 分
 */
checkHour : function (hour) {

	if ( parseInt(hour, 10) >= 24) {

		return false;
	}

    return true;
},

/**
 * 日付チェック（yyyymmddかどうか）
 *
 * @param datestr 日付
 */
checkDate : function (datestr) {
	// 正規表現による書式チェック
	if(!datestr.match(/^\d{4}\d{2}\d{2}$/)){
	    return false;
	}
	var vYear = datestr.substr(0, 4) - 0;
	var vMonth = datestr.substr(4, 2) - 1; // Javascriptは、0-11で表現
	var vDay = datestr.substr(6, 2) - 0;
	// 月,日の妥当性チェック
	if(vMonth >= 0 && vMonth <= 11 && vDay >= 1 && vDay <= 31){
	    var vDt = new Date(vYear, vMonth, vDay);
	    if(isNaN(vDt)){
	        return false;
	    }else if(vDt.getFullYear() == vYear && vDt.getMonth() == vMonth && vDt.getDate() == vDay){
	        return true;
	    }else{
	        return false;
	    }
	}else{
	    return false;
	}
},

/**
 * 日付チェック（yyyymmかどうか）
 *
 * @param datestr 日付
 */
checkDateYm : function (datestr) {
	// 正規表現による書式チェック
	if(!datestr.match(/^\d{4}\d{2}$/)){
	    return false;
	}
	var vYear = datestr.substr(0, 4) - 0;
	var vMonth = datestr.substr(4, 2) - 1; // Javascriptは、0-11で表現

	// 月,日の妥当性チェック
	if(vMonth >= 0 && vMonth <= 11){
	    var vDt = new Date(vYear, vMonth);
	    if(isNaN(vDt)){
	        return false;
	    }else if(vDt.getFullYear() == vYear && vDt.getMonth() == vMonth){
	        return true;
	    }else{
	        return false;
	    }
	}else{
	    return false;
	}
},

/**
 * 入力チェック
 *
 * @param val 値
 */
notEmpty : function (val){
	if(val != null && String(val).length > 0) {
		return true;
	} else {
		return false;
	}
},

/**
 * 数字チェック
 *
 * 半角数字、整数のみに対応しています。
 *
 * @param val 値
 */
numeric : function (val){
	if(val == undefined || val == null) {
		return false;
	}
	return val.match(/^[0-9]+$/);
},

/**
 * 桁数チェック
 *
 * @param val 値
 * @param len 桁数
 */
maxLength : function (val, len){
	if(val == undefined || val == null) {
		return false;
	}
	if(len == undefined || len == null) {
		return false;
	}
	return val.length <= len;
},

/**
 * 数字と桁数のチェック
 *
 * @param val 値
 * @param len 桁数
 */
numLength : function (val, len) {
	if(val == undefined || val == null) {
		return false;
	}

	var reg = new RegExp("^[0-9]\{" + len + "\}$");
	return reg.test(val);
},

/**
 * 少数付数字チェック
 *
 * 数字かどうか及び整数部と小数部の桁数をチェックします。
 * 小数点が存在しない場合は整数部桁数のみをチェックします。
 *
 * @param intLen 整数部桁数
 * @param decLen 小数部桁数
 */
decLength : function (val, intLen, decLen){
	if (!val.match(/^[0-9]+(\.[0-9]+)?$/)) {
		return; false;
	} else {
		var errFlg = false;
		if(val.indexOf('.') < 0){
			if(val.length > intLen) {
				errFlg = true;
			}
		} else {
			// 整数部取得
			var seisu = val.substring(0, val.indexOf('.'));
			// 少数部取得
			var syousu = val.substring(val.indexOf('.') + 1);
			if(seisu.length > intLen){
				errFlg = true;
			} else if(syousu.length > decLen){
				errFlg = true;
			}
		}
		if(errFlg){
			return false;
		}
	}
	return true;
},

}

