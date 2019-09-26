<?php
/**
 * M_Common
 *
 * 共通処理
 *
 * @category      Common
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('DCMSAppModel', 'Model');

class MCommon extends DCMSAppModel {

	public $name = 'MCommon';
	public $useTable = false;


	/**
	 * メッセージ取得処理
	 * @access   public
	 * @param    サブシステムID
	 * @param    メッセージ区分
	 * @param    メッセージID
	 * @return   スタッフ情報
	 */
	public function getTimestamp() {

		$pdo = null;

		try{

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "CALL P_GET_TIMESTAMP(";
			$sql .= "@return";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,"タイムスタンプ　取得");

			$sql = "";
			$sql .= "SELECT";
			$sql .= "@return";
			$this->queryWithPDOLog($stmt,$pdo,$sql,"タイムスタンプ　取得");

			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			$pdo = null;

			return $result['@return'];

		} catch (Exception $e){

			$pdo = null;
			throw $e;
		}

		return date('Y-m-d H:i:s');

	}

	/**
	 * 日時フォーマット変換　yyyy-mm-dd hh:mm:ss -> hh:mm
	 * @access   public
	 * @param    基準日
	 * @param    変換元時間
	 * @return   返還後時間
	 */
	function changeDateTime($baseDate, $time) {

		if ($time == null || $time == '') {
			return '';
		}


		$baseDate = new DateTime($baseDate . " 00:00:00");
		$timeDate = new DateTime($time);

		$interval = $timeDate->diff($baseDate);

		$day = $interval->format('%d');
		$hour = $interval->format('%h');

		$sumHour = $hour + $day * 24;

		return str_pad($sumHour, 2, "0", STR_PAD_LEFT) .$interval->format(':%I');
	}

	/**
	 * 日時フォーマット変換　hh:mm:ss -> yyyy-mm-dd hh:mm:ss
	 * @access   public
	 * @param    基準日
	 * @param    変換元時間
	 * @return   返還後時間
	 */
	function rechangeDateTime($baseDate, $time) {

		if ($time == null || $time == '') {
			return '';
		}

		if (count(split(":", $time)) < 3) {

			return '';
		}

		$hourOrig = intval(split(":", $time)[0]);

		$minutesResult = split(":", $time)[1];
		$secondResult= split(":", $time)[2];

		$hourResult = str_pad($hourOrig % 24, 2 , "0", STR_PAD_LEFT);
		$hourKiriage = floor($hourOrig / 24);

		$dt = new DateTime($baseDate . " " . $hourResult .":" . $minutesResult .":" . $secondResult);

		if ($hourKiriage > 0) {

			$dt->add(new DateInterval('P' . $hourKiriage . 'D'));
		}

		return $yoso_time = $dt->format('Y-m-d H:i:s');
	}

	/**
	 * Javascriptでリプレースした文字を元に戻す
	 * @access   public
	 * @param
	 * @return   置換文字列
	 */
	public function escapePHP($str) {

		$str = str_replace("%2B","+", $str);

		return $str;

	}

	/**
	 * 日付チェック（yyyyMMddの存在する日付をＯＫとする)
	 * @param unknown $data
	 * @return boolean
	 */
	public function isDateYMD($data) {
		//フォーマットチェック
		if (!$this->isDateFormatYMD($data)) {
			return false;
		}

		return $this->isValidDate($data);
	}

	/**
	 * 日付チェック（yyyyMMの存在する日付をＯＫとする）
	 * @param unknown $data
	 * @return boolean
	 */
	public function isDateYM($data) {
		//フォーマットチェック
		if (!$this->isDateFormatYM($data)) {
			return false;
		}

		return $this->isValidDate($data . '01');
	}

	/**
	 * 日付フォーマットチェック（yyyyMMddをＯＫとする）
	 * @param unknown $data
	 * @return true..OK
	 */
	public function isDateFormatYMD($data) {
		if (preg_match("/^[0-9]{8}$/",$data)) {
			return true;
		}

		return false;
	}

	/**
	 * 日付フォーマットチェック（yyyyMMをＯＫとする）
	 * @param unknown $data
	 * @return true..OK
	 */
	public function isDateFormatYM($data) {
		if(preg_match("/^[0-9]{6}$/",$data)) {
			return true;
		}

		return false;
	}

	/**
	 * 存在する日付かどうかををチェック
	 * @param unknown $data
	 * @return true..存在する
	 */
	public function isValidDate($data) {
		$year = substr($data, 0,4);
		$month = substr($data, 4,2);
		$day = substr($data, 6,2);

		if (checkdate($month, $day, $year)) {
			return true;
		}

		return false;
	}

	/**
	 * ２つの日付を比較する
	 * 比較演算子は、Validation::comparisonの仕様に準ずる
	 *
	 * @param string $check1 日付1文字列
	 * @param string $operator 比較演算子
	 * @param string $check2 日付2文字列（指定しない場合現在日付とする）
	 */
	function comparisonDate($check1, $operator, $check2 = '') {
		$date1 = preg_replace('/(\/|-)/', '', $check1);

		if (!$this->isDateYMD($date1) && !$this->isDateYM($date1)) {
			throw new InvalidArgumentException('This function only accepts string of a format of the date. Input was: ' .$check1);
		}

		if ($this->isDateYM($date1)) {
			$date1 .= '01';
		}
		$date1 = date_create($date1);

		if (!empty($check2)) {
			$date2 = preg_replace('/(\/|-)/', '', $check2);
			if (!$this->isDateYMD($date2) && !$this->isDateYM($date2)) {
				throw new InvalidArgumentException('This function only accepts string of a format of the date. Input was: ' .$check2);
			}

			if ($this->isDateYM($check2)) {
				$date2 .= '01';
			}
		}
		$date2 = date_create($date2);

		$operator = str_replace(array(' ', "\t", "\n", "\r", "\0", "\x0B"), '', strtolower($operator));

		switch ($operator) {
			case 'isgreater':
			case '>':
				if ($date1 > $date2) {
					return true;
				}
				break;
			case 'isless':
			case '<':
				if ($date1 < $date2) {
					return true;
				}
				break;
			case 'greaterorequal':
			case '>=':
				if ($date1 >= $date2) {
					return true;
				}
				break;
			case 'lessorequal':
			case '<=':
				if ($date1 <= $date2) {
					return true;
				}
				break;
			case 'equalto':
			case '==':
				if ($date1 == $date2) {
					return true;
				}
				break;
			case 'notequal':
			case '!=':
				if ($date1 != $date2) {
					return true;
				}
				break;
			default:
				throw new InvalidArgumentException('operator is invalid. Input was: ' .$operator);
		}

		return false;
	}

	/**
	 * 文字列を、指定された文字で囲む
	 * @param unknown $str
	 * @param string $quote
	 */
	public function encloseString($str, $quote = "'") {
		return $quote . $str . $quote;
	}

	/**
	 * リクエストされているページ番号が検査し、妥当なページ番号を返却する
	 * @param unknown $pager ページャーオブジェクト
	 * @param unknown $requestPageID
	 * @return number|unknown
	 */
	public function getValidPageID($pager, $requestPageID) {
		if (!preg_match("/\d{1,}/", $requestPageID)) {
			// そもそも数字じゃない場合は１ページ目を表示
			return 1;
		}

		if ($requestPageID > $pager->_totalPages) {
			// 最後のページ番号より大きい値を指定されている場合は、最後のページを表示
			return $pager->_totalPages;
		}

		if ($requestPageID < 1) {
			// 0以下の数字を指定されている場合は1ページ目を表示
			return 1;
		}

		// 妥当なページ番号だった場合は、そのまま使用
		return $requestPageID;
	}

	/**
	 * TIME型に時間を追加する
	 * @param unknown $str
	 * @param string $quote
	 */
	public function addTimeForMySQL($time, $hour, $minutes ,$second) {

		if (count(split(":", $time)) < 3) {

			return "";
		}

		$hourOrig = intval(split(":", $time)[0]);
		$minutesOrig = intval(split(":", $time)[1]);
		$secondOrig = intval(split(":", $time)[2]);

		$secondResult = str_pad(($second + $secondOrig) % 60, 2 , "0", STR_PAD_LEFT);

		$secondKiriage = floor(($second + $secondOrig) / 60);

		$minutesResult = str_pad(($minutes + $minutesOrig + $secondKiriage) % 60, 2 , "0", STR_PAD_LEFT);

		$minutesKiriage = floor(($minutes + $minutesOrig + $secondKiriage) / 60);

		$hourResult = str_pad($hour + $hourOrig + $minutesKiriage, 2 , "0", STR_PAD_LEFT);

		return $hourResult . ":" . $minutesResult . ":" . $secondResult;
	}

	/**
	 * 稼働コスト算出
	 * @param 締年月日
	 * @param 就業実績配列
	 * @param 就業ヘッダ実績情報
	 * @return コスト配列
	 */
	public function getKadoCost($ymd_sime,$jissekiMArray,$jissekiHArray = null) {

		$pdo = null;
		$resultArray = array();

		try {

			//初期情報取得

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);

			//システムマスタ
			$sql  = "SELECT ";
			$sql .= "   TNK_CALC_SYOTEI_TIME_CNT,";
			$sql .= "   TNK_CALC_SYOTEI_REST_TIME_CNT,";
			$sql .= "   TNK_CALC_SNY_TEKIYO_TIME_ST,";
			$sql .= "   TNK_CALC_SNY_TEKIYO_TIME_ED";
			$sql .= " FROM";
			$sql .= "   M_SYSTEM";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "'";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"コスト算出情報　取得");

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$TNK_CALC_SYOTEI_TIME_CNT = $result['TNK_CALC_SYOTEI_TIME_CNT'];
			$TNK_CALC_SYOTEI_REST_TIME_CNT = $result['TNK_CALC_SYOTEI_REST_TIME_CNT'];
			$TNK_CALC_SNY_TEKIYO_TIME_ST = $result['TNK_CALC_SNY_TEKIYO_TIME_ST'];
			$TNK_CALC_SNY_TEKIYO_TIME_ED = $result['TNK_CALC_SNY_TEKIYO_TIME_ED'];


			//スタッフ基本情報
			$STAFF_CDArray = array();

			if ($jissekiHArray == null) {

				foreach($jissekiMArray as $jissekiM) {

					$STAFF_CDArray[] = $jissekiM["STAFF_CD"];
				}

			} else {

				foreach($jissekiHArray as $jissekiH) {

					$STAFF_CDArray[] = $jissekiH["STAFF_CD"];
				}

			}

			array_unique($STAFF_CDArray);


			$sql  = "SELECT ";
			$sql .= "   STAFF_CD,";
			$sql .= "   KIN_SYOTEI,";
			$sql .= "   KIN_JIKANGAI,";
			$sql .= "   KIN_SNY,";
			$sql .= "   KIN_H_KST,";
			$sql .= "   KIN_KST,";
			$sql .= "   KIN_H_KST_SNY";
			$sql .= " FROM";
			$sql .= "   M_STAFF_KIHON";

			if (count($STAFF_CDArray) != 0) {

				$firstFlg = 0;

				foreach ($STAFF_CDArray as $staff_cd) {

					if ($firstFlg == 0) {

						$sql .= " WHERE";
					} else {
						$sql .= " OR";
					}

					$sql .= " STAFF_CD='" . $staff_cd . "'";

					$firstFlg = 1;
				}
			}

			$sql .= " ORDER BY";
			$sql .= "   STAFF_CD";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"スタッフ単価情報　取得");

			$MStaffArray = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {

				$array = array();

				$array["STAFF_CD"] = $result["STAFF_CD"];
				$array["KIN_SYOTEI"] = $result["KIN_SYOTEI"];
				$array["KIN_JIKANGAI"] = $result["KIN_JIKANGAI"];
				$array["KIN_SNY"] = $result["KIN_SNY"];
				$array["KIN_H_KST"] = $result["KIN_H_KST"];
				$array["KIN_KST"] = $result["KIN_KST"];
				$array["KIN_H_KST_SNY"] = $result["KIN_H_KST_SNY"];

				$MStaffArray[] = $array;
			}

			//算出工程マスタ取得
			$sql  = "SELECT ";
			$sql .= "   NINUSI_CD,";
			$sql .= "   SOSIKI_CD,";
			$sql .= "   BNRI_DAI_CD,";
			$sql .= "   BNRI_CYU_CD,";
			$sql .= "   BNRI_SAI_CD";
			$sql .= " FROM";
			$sql .= "   M_CALC_TGT_KOTEI";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "'";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"算出工程マスタ　取得");

			$MCalcTGTKoteiArray = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {

				$array = array(
						"NINUSI_CD" => $result["NINUSI_CD"],
						"SOSIKI_CD" => $result["SOSIKI_CD"],
						"BNRI_DAI_CD" => $result["BNRI_DAI_CD"],
						"BNRI_CYU_CD" => $result["BNRI_CYU_CD"],
						"BNRI_SAI_CD" => $result["BNRI_SAI_CD"]
				);

				$MCalcTGTKoteiArray[] = $array;
			}

			if ($jissekiHArray != null) {

				//工程間空き情報
				$sql  = "SELECT ";
				$sql .= "   BNRI_DAI_CD,";
				$sql .= "   BNRI_CYU_CD,";
				$sql .= "   BNRI_SAI_CD";
				$sql .= " FROM";
				$sql .= "   M_CALC_TGT_KOTEI";
				$sql .= " WHERE";
				$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
				$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
				$sql .= "   KOTEI_KBN=2";

				$this->queryWithPDOLog($stmt,$pdo,$sql,"空き時間工程CD　取得");

				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$BASE_BNRI_DAI_CD = $result['BNRI_DAI_CD'];
				$BASE_BNRI_CYU_CD = $result['BNRI_CYU_CD'];
				$BASE_BNRI_SAI_CD = $result['BNRI_SAI_CD'];

				$akiArray = array();

				foreach($jissekiHArray as $jissekiH) {

					$BASE_NINUSI_CD  = $jissekiH['NINUSI_CD'];
					$BASE_SOSIKI_CD  = $jissekiH['SOSIKI_CD'];
					$BASE_YMD_SYUGYO  = $jissekiH['YMD_SYUGYO'];
					$BASE_STAFF_CD  = $jissekiH['STAFF_CD'];
					$BASE_DT_SYUGYO_ST = $jissekiH['DT_SYUGYO_ST'];

					$tmpJissekiM = null;
					$countMeisai = 0;

					foreach($jissekiMArray as $jissekiM) {

						if ($BASE_NINUSI_CD == $jissekiM['NINUSI_CD'] &&
						$BASE_SOSIKI_CD == $jissekiM['SOSIKI_CD'] &&
						$BASE_YMD_SYUGYO == $jissekiM['YMD_SYUGYO'] &&
						$BASE_STAFF_CD == $jissekiM['STAFF_CD'] &&
						$BASE_DT_SYUGYO_ST == $jissekiM['DT_SYUGYO_ST']) {

							if ($countMeisai == 0) {

								if ($jissekiH['DT_SYUGYO_ST_H'] != $jissekiM['DT_KOTEI_ST_H']) {

									$array = array(
											"NINUSI_CD" => $BASE_NINUSI_CD,
											"SOSIKI_CD" => $BASE_SOSIKI_CD,
											"YMD_SYUGYO" => $BASE_YMD_SYUGYO,
											"STAFF_CD" => $BASE_STAFF_CD,
											"BNRI_DAI_CD" => $BASE_BNRI_DAI_CD,
											"BNRI_CYU_CD" => $BASE_BNRI_CYU_CD,
											"BNRI_SAI_CD" => $BASE_BNRI_SAI_CD,
											"DT_KOTEI_ST_H" => $jissekiH['DT_SYUGYO_ST_H'],
											"DT_KOTEI_ED_H" => $jissekiM['DT_KOTEI_ST_H'],
											"DT_SYUGYO_ST" => $BASE_DT_SYUGYO_ST
									);

									$akiArray[] = $array;

								}

							} else {

								if ($tmpJissekiM['DT_KOTEI_ED_H'] != $jissekiM['DT_KOTEI_ST_H']) {

									$array = array(
											"NINUSI_CD" => $BASE_NINUSI_CD,
											"SOSIKI_CD" => $BASE_SOSIKI_CD,
											"YMD_SYUGYO" => $BASE_YMD_SYUGYO,
											"STAFF_CD" => $BASE_STAFF_CD,
											"BNRI_DAI_CD" => $BASE_BNRI_DAI_CD,
											"BNRI_CYU_CD" => $BASE_BNRI_CYU_CD,
											"BNRI_SAI_CD" => $BASE_BNRI_SAI_CD,
											"DT_KOTEI_ST_H" => $tmpJissekiM['DT_KOTEI_ED_H'],
											"DT_KOTEI_ED_H" => $jissekiM['DT_KOTEI_ST_H'],
											"DT_SYUGYO_ST" => $BASE_DT_SYUGYO_ST
									);

									$akiArray[] = $array;

								}

							}

							$countMeisai++;
							$tmpJissekiM = $jissekiM;
						}
					}

					if ($tmpJissekiM != null) {

						if ($tmpJissekiM['DT_KOTEI_ED_H'] != $jissekiH['DT_SYUGYO_ED_H']) {

							$array = array(
									"NINUSI_CD" => $BASE_NINUSI_CD,
									"SOSIKI_CD" => $BASE_SOSIKI_CD,
									"YMD_SYUGYO" => $BASE_YMD_SYUGYO,
									"STAFF_CD" => $BASE_STAFF_CD,
									"BNRI_DAI_CD" => $BASE_BNRI_DAI_CD,
									"BNRI_CYU_CD" => $BASE_BNRI_CYU_CD,
									"BNRI_SAI_CD" => $BASE_BNRI_SAI_CD,
									"DT_KOTEI_ST_H" => $tmpJissekiM['DT_KOTEI_ED_H'],
									"DT_KOTEI_ED_H" => $jissekiH['DT_SYUGYO_ED_H'],
									"DT_SYUGYO_ST" => $BASE_DT_SYUGYO_ST
							);

							$akiArray[] = $array;

						}

					}

				}

				$jissekiMArray = array_merge($jissekiMArray,$akiArray);
			}

			
			$NINUSI_CD = array();
			$SOSIKI_CD =  array();
			$YMD_SYUGYO =  array();
			$STAFF_CD =  array();
			$DT_SYUGYO_ST =  array();
			$DT_KOTEI_ST_H =  array();

			foreach ($jissekiMArray as $key => $row) {
				$NINUSI_CD[$key]  = $row['NINUSI_CD'];
				$SOSIKI_CD[$key]  = $row['SOSIKI_CD'];
				$YMD_SYUGYO[$key]  = $row['YMD_SYUGYO'];
				$STAFF_CD[$key]  = $row['STAFF_CD'];
				$DT_SYUGYO_ST[$key]  = $row['DT_SYUGYO_ST'];
				$DT_KOTEI_ST_H[$key]  = $row['DT_KOTEI_ST_H'];
			}

			array_multisort($NINUSI_CD,SORT_ASC,
			$SOSIKI_CD,SORT_ASC,
			$YMD_SYUGYO,SORT_ASC,
			$STAFF_CD,SORT_ASC,
			$DT_SYUGYO_ST,SORT_ASC,
			$DT_KOTEI_ST_H,SORT_ASC,
			$jissekiMArray);

			//コスト算出

			//該当データ以外は除外
			$tmpJissekiMArray = array();

			foreach($jissekiMArray as $jissekiM) {

				$BASE_NINUSI_CD  = $jissekiM['NINUSI_CD'];
				$BASE_SOSIKI_CD  = $jissekiM['SOSIKI_CD'];
				$BASE_BNRI_DAI_CD = $jissekiM['BNRI_DAI_CD'];
				$BASE_BNRI_CYU_CD = $jissekiM['BNRI_CYU_CD'];
				$BASE_BNRI_SAI_CD = $jissekiM['BNRI_SAI_CD'];


				foreach($MCalcTGTKoteiArray as $array) {

					if ($BASE_NINUSI_CD == $array['NINUSI_CD'] &&
					$BASE_SOSIKI_CD == $array['SOSIKI_CD'] &&
					$BASE_BNRI_DAI_CD == $array['BNRI_DAI_CD'] &&
					$BASE_BNRI_CYU_CD == $array['BNRI_CYU_CD'] &&
					(empty($BASE_BNRI_SAI_CD) || $BASE_BNRI_SAI_CD== $array['BNRI_SAI_CD'])) {
						$tmpJissekiMArray[] = $jissekiM;
						break;

					}
				}
			}

			$jissekiMArray = $tmpJissekiMArray;
			
			//スタッフ単位初期設定
			foreach($MStaffArray as $MStaff) {

				//1. 保持項目
				$BASE_STAFF_CD  = $MStaff['STAFF_CD'];//スタッフコード

				//3.スタッフ単価
				$BASE_KIN_SYOTEI = ($MStaff["KIN_SYOTEI"] != null && $MStaff["KIN_SYOTEI"] != "" ? $MStaff["KIN_SYOTEI"] : 0);
				$BASE_KIN_JIKANGAI = ($MStaff["KIN_JIKANGAI"] != null && $MStaff["KIN_JIKANGAI"] != "" ? $MStaff["KIN_JIKANGAI"] : 0);
				$BASE_KIN_SNY = ($MStaff["KIN_SNY"] != null && $MStaff["KIN_SNY"] != "" ? $MStaff["KIN_SNY"] : 0);
				$BASE_KIN_H_KST = ($MStaff["KIN_H_KST"] != null && $MStaff["KIN_H_KST"] != "" ? $MStaff["KIN_H_KST"] : 0);
				$BASE_KIN_KST = ($MStaff["KIN_KST"] != null && $MStaff["KIN_KST"] != "" ? $MStaff["KIN_KST"] : 0);
				$BASE_KIN_H_KST_SNY = ($MStaff["KIN_H_KST_SNY"] != null && $MStaff["KIN_H_KST_SNY"] != "" ? $MStaff["KIN_H_KST_SNY"] : 0);


				//工程毎のループ
				foreach($jissekiMArray as $jissekiM) {

					if ($BASE_STAFF_CD == $jissekiM['STAFF_CD']) {

						//保持項目
						$BASE_NINUSI_CD  = $jissekiM['NINUSI_CD'];
						$BASE_SOSIKI_CD  = $jissekiM['SOSIKI_CD'];
						$BASE_YMD_SYUGYO  = $jissekiM['YMD_SYUGYO'];
						$BASE_DT_SYUGYO_ST = $jissekiM['DT_SYUGYO_ST'];//就業開始時刻 ・　所定範囲開始時間

						//2.所定時間範囲、深夜時間帯

						//所定時間範囲終了時間
						$BASE_DT_SYOTEI_ED = $this->addTimeForMySQL($BASE_DT_SYUGYO_ST,
								$TNK_CALC_SYOTEI_TIME_CNT + $TNK_CALC_SYOTEI_REST_TIME_CNT,0,0);

						//単価適用時間範囲算出

						//1 工程時刻範囲　時間内・時間外　分割
						$JIKANNAI_KAISHI_TIME = null;
						$JIKANNAI_SYURYO_TIME = null;
						$JIKANGAI_KAISHI_TIME = null;
						$JIKANGAI_SYURYO_TIME = null;

						$KAISHI_HANINAI_FLG = 0;
						$SYURYO_HANINAI_FLG = 0;

						//工程開始時刻について
						if ((strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$BASE_DT_SYUGYO_ST)) <=
								strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ST_H']))) &&
								(strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ST_H'])) <=
										strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$BASE_DT_SYOTEI_ED)))
						) {

							$JIKANNAI_KAISHI_TIME = $jissekiM['DT_KOTEI_ST_H'];

						} else {

							$JIKANGAI_KAISHI_TIME = $jissekiM['DT_KOTEI_ST_H'];
							$KAISHI_HANINAI_FLG = 1;
						}

						//工程終了時刻について
						if ((strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$BASE_DT_SYUGYO_ST)) <=
								strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ED_H']))) &&
								(strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ED_H'])) <=
										strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$BASE_DT_SYOTEI_ED)))
						) {

							$JIKANNAI_SYURYO_TIME = $jissekiM['DT_KOTEI_ED_H'];

						} else {

							$JIKANGAI_SYURYO_TIME = $jissekiM['DT_KOTEI_ED_H'];
							$SYURYO_HANINAI_FLG = 1;
						}

						//フラグ判定
						if ($KAISHI_HANINAI_FLG == 0 &&
						$SYURYO_HANINAI_FLG == 0) {

							//何もしない
						} else if ($KAISHI_HANINAI_FLG == 0 &&
						$SYURYO_HANINAI_FLG == 1) {

							$JIKANNAI_SYURYO_TIME = $BASE_DT_SYOTEI_ED;
							$JIKANGAI_KAISHI_TIME = $BASE_DT_SYOTEI_ED;

						} else if ($KAISHI_HANINAI_FLG == 1 &&
						$SYURYO_HANINAI_FLG == 1) {

							//何もしない
						}

						//2 深夜判定
						$SINYA_TIME_ST = $TNK_CALC_SNY_TEKIYO_TIME_ST;
						$SINYA_TIME_ED = $TNK_CALC_SNY_TEKIYO_TIME_ED;

						$NISSU_COUNT = 1;
						$SHINYA_MINUTES = 0;

						while (strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ED_H'])) >
								strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$SINYA_TIME_ST))) {


							$SHINYA_KAISHI_TIME = null;
							$SHINYA_SYURYO_TIME = null;

							//工程開始時刻について
							if ((strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$SINYA_TIME_ST)) <=
									strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ST_H']))) &&
									(strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ST_H'])) <=
											strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$SINYA_TIME_ED)))
							) {

								$SHINYA_KAISHI_TIME = $jissekiM['DT_KOTEI_ST_H'];
							}


							//工程終了時刻について
							if ((strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$SINYA_TIME_ST)) <=
									strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ED_H']))) &&
									(strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ED_H'])) <=
											strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$SINYA_TIME_ED)))
							) {

								$SHINYA_SYURYO_TIME = $jissekiM['DT_KOTEI_ED_H'];
							}

							if ($SHINYA_KAISHI_TIME == null &&
							$SHINYA_SYURYO_TIME != null) {

								$SHINYA_KAISHI_TIME = $SINYA_TIME_ST;
							}

							if ($SHINYA_KAISHI_TIME != null &&
							$SHINYA_SYURYO_TIME == null) {

								$SHINYA_SYURYO_TIME = $SINYA_TIME_ED;
							}

							if ($SHINYA_KAISHI_TIME == null &&
							$SHINYA_SYURYO_TIME == null) {


								if ((strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ST_H'])) <
										strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$SINYA_TIME_ST))) &&
										(strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ED_H'])) >
												strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$SINYA_TIME_ED)))
								) {

									$SHINYA_KAISHI_TIME = $SINYA_TIME_ST;
									$SHINYA_SYURYO_TIME = $SINYA_TIME_ED;
								}

							}

							if ($SHINYA_KAISHI_TIME != null && $SHINYA_SYURYO_TIME != null) {

								$dt1 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$SHINYA_KAISHI_TIME),0,17) . "00");
								$dt2 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$SHINYA_SYURYO_TIME),0,17) . "00");

								$interval = $dt1->diff($dt2);
								$day = $interval->format('%d');
								$hour = $interval->format('%h');
								$minutes = $interval->format('%i');

								$SHINYA_MINUTES = $SHINYA_MINUTES + $hour * 60 + $day * 24 * 60 + $minutes;
							}

							$SINYA_TIME_ST = $this->addTimeForMySQL($SINYA_TIME_ST,24 * $NISSU_COUNT,0,0);
							$SINYA_TIME_ED = $this->addTimeForMySQL($SINYA_TIME_ED,24 * $NISSU_COUNT,0,0);

							$NISSU_COUNT++;
						}

						//その他
						$H_KST_TIME_ST = null;
						$H_KST_TIME_ED = null;
						$KST_TIME_ST = null;
						$KST_TIME_ED = null;


						//単価適用時間算出
						$JIKANNAI_MINUTES = 0;
						$JIKANGAI_MINUTES = 0;
						$H_KST_MINUTES = 0;
						$KST_MINUTES = 0;
						$H_KST_SNY_MINUTES = 0;

						//時間内適用時間
						if ($JIKANNAI_KAISHI_TIME != null && $JIKANNAI_SYURYO_TIME != null) {

							$dt1 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$JIKANNAI_KAISHI_TIME),0,17) . "00");
							$dt2 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$JIKANNAI_SYURYO_TIME),0,17) . "00");

							$interval = $dt1->diff($dt2);
							$day = $interval->format('%d');
							$hour = $interval->format('%h');
							$minutes = $interval->format('%i');

							$JIKANNAI_MINUTES = $hour * 60 + $day * 24 * 60 + $minutes;
						}

						//時間外適用時間
						if ($JIKANGAI_KAISHI_TIME != null && $JIKANGAI_SYURYO_TIME != null) {

							$dt1 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$JIKANGAI_KAISHI_TIME),0,17) . "00");
							$dt2 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$JIKANGAI_SYURYO_TIME),0,17) . "00");

							$interval = $dt1->diff($dt2);
							$day = $interval->format('%d');
							$hour = $interval->format('%h');
							$minutes = $interval->format('%i');

							$JIKANGAI_MINUTES = $hour * 60 + $day * 24 * 60 + $minutes;
						}


						//法廷休出時間
						if ($H_KST_TIME_ST != null && $H_KST_TIME_ED != null) {

							$dt1 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$H_KST_TIME_ST),0,17) . "00");
							$dt2 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$H_KST_TIME_ED),0,17) . "00");

							$interval = $dt1->diff($dt2);
							$day = $interval->format('%d');
							$hour = $interval->format('%h');
							$minutes = $interval->format('%i');

							$H_KST_MINUTES = $hour * 60 + $day * 24 * 60 + $minutes;
						}

						//休出時間
						if ($KST_TIME_ST != null && $KST_TIME_ED != null) {

							$dt1 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$KST_TIME_ST),0,17) . "00");
							$dt2 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$KST_TIME_ED),0,17) . "00");

							$interval = $dt1->diff($dt2);
							$day = $interval->format('%d');
							$hour = $interval->format('%h');
							$minutes = $interval->format('%i');
							$second = $interval->format('%s');

							$KST_MINUTES = $hour * 60 + $day * 24 * 60 + $minutes;
						}

						//コスト算出
						$JIKANNAI_COST = 0;
						$JIKANGAI_COST = 0;
						$SHINYA_COST = 0;
						$H_KST_COST = 0;
						$KST_COST = 0;
						$H_KST_SNY_COST = 0;
						$TOTAL_COST = 0;


						$JIKANNAI_COST = round($BASE_KIN_SYOTEI * ($JIKANNAI_MINUTES / 60));
						$JIKANGAI_COST = round($BASE_KIN_JIKANGAI * ($JIKANGAI_MINUTES / 60));
						$SHINYA_COST = round($BASE_KIN_SNY * ($SHINYA_MINUTES / 60));
						$H_KST_COST = round($BASE_KIN_H_KST * ($H_KST_MINUTES / 60));
						$KST_COST = round($BASE_KIN_KST * ($KST_MINUTES / 60));
						$H_KST_SNY_COST = round($BASE_KIN_H_KST_SNY * ($H_KST_MINUTES / 60));

						$TMP_COST = ($BASE_KIN_SYOTEI * ($JIKANNAI_MINUTES / 60)) +
						($BASE_KIN_JIKANGAI * ($JIKANGAI_MINUTES / 60)) +
						($BASE_KIN_SNY * ($SHINYA_MINUTES / 60)) +
						($BASE_KIN_H_KST * ($H_KST_MINUTES / 60)) +
						($BASE_KIN_KST * ($KST_MINUTES / 60)) +
						($BASE_KIN_H_KST_SNY * ($H_KST_MINUTES / 60));

						$TOTAL_COST = round($TMP_COST);

						//単価算出
						$TANKA = 0;

						if ( ( $JIKANNAI_MINUTES +
								$JIKANGAI_MINUTES +
								$SHINYA_MINUTES +
								$H_KST_MINUTES +
								$KST_MINUTES +
								$H_KST_SNY_MINUTES ) != 0) {

							$TANKA = round(($TMP_COST / ( $JIKANNAI_MINUTES +
									$JIKANGAI_MINUTES +
									$SHINYA_MINUTES +
									$H_KST_MINUTES +
									$KST_MINUTES +
									$H_KST_SNY_MINUTES)) * 60);
						} else {

							$TANKA = 0;
						}



						//工程就業時間算出
						$KOTEI_SYUGYO_TIME = 0;


						$dt1 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ST_H']),0,17) . "00");
						$dt2 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ED_H']),0,17) . "00");

						$interval = $dt1->diff($dt2);
						$day = $interval->format('%d');
						$hour = $interval->format('%h');
						$minutes = $interval->format('%i');

						$KOTEI_SYUGYO_TIME = $hour * 60 + $day * 24 * 60 + $minutes;
						
						if ($KOTEI_SYUGYO_TIME == 0) {
							continue;
						}

						$array = array (

								"NINUSI_CD" => $BASE_NINUSI_CD,
								"SOSIKI_CD" => $BASE_SOSIKI_CD,
								"YMD_SIME" => $ymd_sime,
								"STAFF_CD" => $BASE_STAFF_CD,
								"YMD_SYUGYO" => $BASE_YMD_SYUGYO,
								"BNRI_DAI_CD" => $jissekiM['BNRI_DAI_CD'],
								"BNRI_CYU_CD" => $jissekiM['BNRI_CYU_CD'],
								"BNRI_SAI_CD" => $jissekiM['BNRI_SAI_CD'],
								"TIME_KOTEI_ST" => $jissekiM['DT_KOTEI_ST_H'],
								"TIME_KOTEI_ED" => $jissekiM['DT_KOTEI_ED_H'],
								"TIME_KOTEI_SYUGYO" => $KOTEI_SYUGYO_TIME,
								"TIME_SYUGYO_ST" => $BASE_DT_SYUGYO_ST,
								"TIME_SYUGYO_ED" => $BASE_DT_SYOTEI_ED,
								"TIME_SNY_TEKIYO_ST" => $TNK_CALC_SNY_TEKIYO_TIME_ST,
								"TIME_SNY_TEKIYO_ED" => $TNK_CALC_SNY_TEKIYO_TIME_ED,
								"KIN_SYOTEI" => $BASE_KIN_SYOTEI,
								"KIN_JIKANGAI" => $BASE_KIN_JIKANGAI,
								"KIN_SNY" => $BASE_KIN_SNY,
								"KIN_KST" => $BASE_KIN_KST,
								"KIN_H_KST" => $BASE_KIN_H_KST,
								"KIN_H_KST_SNY" => $BASE_KIN_H_KST_SNY,
								"TIME_SYOTEI_ST" => $JIKANNAI_KAISHI_TIME,
								"TIME_SYOTEI_ED" => $JIKANNAI_SYURYO_TIME,
								"TIME_JIKANGAI_ST" => $JIKANGAI_KAISHI_TIME,
								"TIME_JIKANGAI_ED" => $JIKANGAI_SYURYO_TIME,
								"TIME_KST_ST" => $KST_TIME_ST,
								"TIME_KST_ED" => $KST_TIME_ED,
								"TIME_H_KST_ST" => $H_KST_TIME_ST,
								"TIME_H_KST_ED" => $H_KST_TIME_ED,
								"TIME_CNT_SYOTEI" => $JIKANNAI_MINUTES,
								"TIME_CNT_JIKANGAI" => $JIKANGAI_MINUTES,
								"TIME_CNT_SNY" => $SHINYA_MINUTES,
								"TIME_CNT_KST" => $KST_MINUTES,
								"TIME_CNT_H_KST" => $H_KST_MINUTES,
								"TIME_CNT_H_KST_SNY" => $H_KST_SNY_MINUTES,
								"COST_SYOTEI" => $JIKANNAI_COST,
								"COST_JIKANGAI" => $JIKANGAI_COST,
								"COST_SNY" => $SHINYA_COST,
								"COST_KST" => $KST_COST,
								"COST_H_KST" => $H_KST_COST,
								"COST_H_KST_SNY" => $H_KST_SNY_COST,
								"COST" => $TOTAL_COST,
								"TNK" => $TANKA

						);

						$resultArray[] = $array;

					}


				}


			}

			$pdo = null;

			return $resultArray;

		} catch (Exception $e) {

			$pdo = null;
			throw $e;
		}

	}

	/**
	 * 稼働コスト算出
	 * @param 締年月日
	 * @param 就業実績配列
	 * @param 就業ヘッダ実績情報
	 * @return コスト配列
	 */
	public function getKadoCost2($ymd_sime,$jissekiMArray,$jissekiHArray = null,$sosiki_cd,$ninusi_cd) {

		$pdo = null;
		$resultArray = array();

		try {

			//初期情報取得

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);

			//システムマスタ
			$sql  = "SELECT ";
			$sql .= "   TNK_CALC_SYOTEI_TIME_CNT,";
			$sql .= "   TNK_CALC_SYOTEI_REST_TIME_CNT,";
			$sql .= "   TNK_CALC_SNY_TEKIYO_TIME_ST,";
			$sql .= "   TNK_CALC_SNY_TEKIYO_TIME_ED";
			$sql .= " FROM";
			$sql .= "   M_SYSTEM";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $sosiki_cd. "'";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"コスト算出情報　取得");

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$TNK_CALC_SYOTEI_TIME_CNT = $result['TNK_CALC_SYOTEI_TIME_CNT'];
			$TNK_CALC_SYOTEI_REST_TIME_CNT = $result['TNK_CALC_SYOTEI_REST_TIME_CNT'];
			$TNK_CALC_SNY_TEKIYO_TIME_ST = $result['TNK_CALC_SNY_TEKIYO_TIME_ST'];
			$TNK_CALC_SNY_TEKIYO_TIME_ED = $result['TNK_CALC_SNY_TEKIYO_TIME_ED'];


			//スタッフ基本情報
			$STAFF_CDArray = array();

			if ($jissekiHArray == null) {

				foreach($jissekiMArray as $jissekiM) {

					$STAFF_CDArray[] = $jissekiM["STAFF_CD"];
				}

			} else {

				foreach($jissekiHArray as $jissekiH) {

					$STAFF_CDArray[] = $jissekiH["STAFF_CD"];
				}

			}

			array_unique($STAFF_CDArray);


			$sql  = "SELECT ";
			$sql .= "   STAFF_CD,";
			$sql .= "   KIN_SYOTEI,";
			$sql .= "   KIN_JIKANGAI,";
			$sql .= "   KIN_SNY,";
			$sql .= "   KIN_H_KST,";
			$sql .= "   KIN_KST,";
			$sql .= "   KIN_H_KST_SNY";
			$sql .= " FROM";
			$sql .= "   M_STAFF_KIHON";

			if (count($STAFF_CDArray) != 0) {

				$firstFlg = 0;

				foreach ($STAFF_CDArray as $staff_cd) {

					if ($firstFlg == 0) {

						$sql .= " WHERE";
					} else {
						$sql .= " OR";
					}

					$sql .= " STAFF_CD='" . $staff_cd . "'";

					$firstFlg = 1;
				}
			}

			$sql .= " ORDER BY";
			$sql .= "   STAFF_CD";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"スタッフ単価情報　取得");

			$MStaffArray = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {

				$array = array();

				$array["STAFF_CD"] = $result["STAFF_CD"];
				$array["KIN_SYOTEI"] = $result["KIN_SYOTEI"];
				$array["KIN_JIKANGAI"] = $result["KIN_JIKANGAI"];
				$array["KIN_SNY"] = $result["KIN_SNY"];
				$array["KIN_H_KST"] = $result["KIN_H_KST"];
				$array["KIN_KST"] = $result["KIN_KST"];
				$array["KIN_H_KST_SNY"] = $result["KIN_H_KST_SNY"];

				$MStaffArray[] = $array;
			}

			//算出工程マスタ取得
			$sql  = "SELECT ";
			$sql .= "   NINUSI_CD,";
			$sql .= "   SOSIKI_CD,";
			$sql .= "   BNRI_DAI_CD,";
			$sql .= "   BNRI_CYU_CD,";
			$sql .= "   BNRI_SAI_CD";
			$sql .= " FROM";
			$sql .= "   M_CALC_TGT_KOTEI";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $sosiki_cd . "'";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"算出工程マスタ　取得");

			$MCalcTGTKoteiArray = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {

				$array = array(
					"NINUSI_CD" => $result["NINUSI_CD"],
					"SOSIKI_CD" => $result["SOSIKI_CD"],
					"BNRI_DAI_CD" => $result["BNRI_DAI_CD"],
					"BNRI_CYU_CD" => $result["BNRI_CYU_CD"],
					"BNRI_SAI_CD" => $result["BNRI_SAI_CD"]
				);

				$MCalcTGTKoteiArray[] = $array;
			}

			if ($jissekiHArray != null) {

				//工程間空き情報
				$sql  = "SELECT ";
				$sql .= "   BNRI_DAI_CD,";
				$sql .= "   BNRI_CYU_CD,";
				$sql .= "   BNRI_SAI_CD";
				$sql .= " FROM";
				$sql .= "   M_CALC_TGT_KOTEI";
				$sql .= " WHERE";
				$sql .= "   NINUSI_CD='" . $ninusi_cd . "' AND";
				$sql .= "   SOSIKI_CD='" . $sosiki_cd . "' AND";
				$sql .= "   KOTEI_KBN=2";

				$this->queryWithPDOLog($stmt,$pdo,$sql,"空き時間工程CD　取得");

				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$BASE_BNRI_DAI_CD = $result['BNRI_DAI_CD'];
				$BASE_BNRI_CYU_CD = $result['BNRI_CYU_CD'];
				$BASE_BNRI_SAI_CD = $result['BNRI_SAI_CD'];

				$akiArray = array();

				foreach($jissekiHArray as $jissekiH) {

					$BASE_NINUSI_CD  = $jissekiH['NINUSI_CD'];
					$BASE_SOSIKI_CD  = $jissekiH['SOSIKI_CD'];
					$BASE_YMD_SYUGYO  = $jissekiH['YMD_SYUGYO'];
					$BASE_STAFF_CD  = $jissekiH['STAFF_CD'];
					$BASE_DT_SYUGYO_ST = $jissekiH['DT_SYUGYO_ST'];

					$tmpJissekiM = null;
					$countMeisai = 0;

					foreach($jissekiMArray as $jissekiM) {

						if ($BASE_NINUSI_CD == $jissekiM['NINUSI_CD'] &&
							$BASE_SOSIKI_CD == $jissekiM['SOSIKI_CD'] &&
							$BASE_YMD_SYUGYO == $jissekiM['YMD_SYUGYO'] &&
							$BASE_STAFF_CD == $jissekiM['STAFF_CD'] &&
							$BASE_DT_SYUGYO_ST == $jissekiM['DT_SYUGYO_ST']) {

							if ($countMeisai == 0) {

								if ($jissekiH['DT_SYUGYO_ST_H'] != $jissekiM['DT_KOTEI_ST_H']) {

									$array = array(
										"NINUSI_CD" => $BASE_NINUSI_CD,
										"SOSIKI_CD" => $BASE_SOSIKI_CD,
										"YMD_SYUGYO" => $BASE_YMD_SYUGYO,
										"STAFF_CD" => $BASE_STAFF_CD,
										"BNRI_DAI_CD" => $BASE_BNRI_DAI_CD,
										"BNRI_CYU_CD" => $BASE_BNRI_CYU_CD,
										"BNRI_SAI_CD" => $BASE_BNRI_SAI_CD,
										"DT_KOTEI_ST_H" => $jissekiH['DT_SYUGYO_ST_H'],
										"DT_KOTEI_ED_H" => $jissekiM['DT_KOTEI_ST_H'],
										"DT_SYUGYO_ST" => $BASE_DT_SYUGYO_ST
									);

									$akiArray[] = $array;

								}

							} else {

								if ($tmpJissekiM['DT_KOTEI_ED_H'] != $jissekiM['DT_KOTEI_ST_H']) {

									$array = array(
										"NINUSI_CD" => $BASE_NINUSI_CD,
										"SOSIKI_CD" => $BASE_SOSIKI_CD,
										"YMD_SYUGYO" => $BASE_YMD_SYUGYO,
										"STAFF_CD" => $BASE_STAFF_CD,
										"BNRI_DAI_CD" => $BASE_BNRI_DAI_CD,
										"BNRI_CYU_CD" => $BASE_BNRI_CYU_CD,
										"BNRI_SAI_CD" => $BASE_BNRI_SAI_CD,
										"DT_KOTEI_ST_H" => $tmpJissekiM['DT_KOTEI_ED_H'],
										"DT_KOTEI_ED_H" => $jissekiM['DT_KOTEI_ST_H'],
										"DT_SYUGYO_ST" => $BASE_DT_SYUGYO_ST
									);

									$akiArray[] = $array;

								}

							}

							$countMeisai++;
							$tmpJissekiM = $jissekiM;
						}
					}

					if ($tmpJissekiM != null) {

						if ($tmpJissekiM['DT_KOTEI_ED_H'] != $jissekiH['DT_SYUGYO_ED_H']) {

							$array = array(
								"NINUSI_CD" => $BASE_NINUSI_CD,
								"SOSIKI_CD" => $BASE_SOSIKI_CD,
								"YMD_SYUGYO" => $BASE_YMD_SYUGYO,
								"STAFF_CD" => $BASE_STAFF_CD,
								"BNRI_DAI_CD" => $BASE_BNRI_DAI_CD,
								"BNRI_CYU_CD" => $BASE_BNRI_CYU_CD,
								"BNRI_SAI_CD" => $BASE_BNRI_SAI_CD,
								"DT_KOTEI_ST_H" => $tmpJissekiM['DT_KOTEI_ED_H'],
								"DT_KOTEI_ED_H" => $jissekiH['DT_SYUGYO_ED_H'],
								"DT_SYUGYO_ST" => $BASE_DT_SYUGYO_ST
							);

							$akiArray[] = $array;

						}

					}

				}

				$jissekiMArray = array_merge($jissekiMArray,$akiArray);
			}


			$NINUSI_CD = array();
			$SOSIKI_CD =  array();
			$YMD_SYUGYO =  array();
			$STAFF_CD =  array();
			$DT_SYUGYO_ST =  array();
			$DT_KOTEI_ST_H =  array();

			foreach ($jissekiMArray as $key => $row) {
				$NINUSI_CD[$key]  = $row['NINUSI_CD'];
				$SOSIKI_CD[$key]  = $row['SOSIKI_CD'];
				$YMD_SYUGYO[$key]  = $row['YMD_SYUGYO'];
				$STAFF_CD[$key]  = $row['STAFF_CD'];
				$DT_SYUGYO_ST[$key]  = $row['DT_SYUGYO_ST'];
				$DT_KOTEI_ST_H[$key]  = $row['DT_KOTEI_ST_H'];
			}

			array_multisort($NINUSI_CD,SORT_ASC,
				$SOSIKI_CD,SORT_ASC,
				$YMD_SYUGYO,SORT_ASC,
				$STAFF_CD,SORT_ASC,
				$DT_SYUGYO_ST,SORT_ASC,
				$DT_KOTEI_ST_H,SORT_ASC,
				$jissekiMArray);

			//コスト算出

			//該当データ以外は除外
			$tmpJissekiMArray = array();

			foreach($jissekiMArray as $jissekiM) {

				$BASE_NINUSI_CD  = $jissekiM['NINUSI_CD'];
				$BASE_SOSIKI_CD  = $jissekiM['SOSIKI_CD'];
				$BASE_BNRI_DAI_CD = $jissekiM['BNRI_DAI_CD'];
				$BASE_BNRI_CYU_CD = $jissekiM['BNRI_CYU_CD'];
				$BASE_BNRI_SAI_CD = $jissekiM['BNRI_SAI_CD'];


				foreach($MCalcTGTKoteiArray as $array) {

					if ($BASE_NINUSI_CD == $array['NINUSI_CD'] &&
						$BASE_SOSIKI_CD == $array['SOSIKI_CD'] &&
						$BASE_BNRI_DAI_CD == $array['BNRI_DAI_CD'] &&
						$BASE_BNRI_CYU_CD == $array['BNRI_CYU_CD'] &&
						(empty($BASE_BNRI_SAI_CD) || $BASE_BNRI_SAI_CD== $array['BNRI_SAI_CD'])) {
						$tmpJissekiMArray[] = $jissekiM;
						break;

					}
				}
			}

			$jissekiMArray = $tmpJissekiMArray;

			//スタッフ単位初期設定
			foreach($MStaffArray as $MStaff) {

				//1. 保持項目
				$BASE_STAFF_CD  = $MStaff['STAFF_CD'];//スタッフコード

				//3.スタッフ単価
				$BASE_KIN_SYOTEI = ($MStaff["KIN_SYOTEI"] != null && $MStaff["KIN_SYOTEI"] != "" ? $MStaff["KIN_SYOTEI"] : 0);
				$BASE_KIN_JIKANGAI = ($MStaff["KIN_JIKANGAI"] != null && $MStaff["KIN_JIKANGAI"] != "" ? $MStaff["KIN_JIKANGAI"] : 0);
				$BASE_KIN_SNY = ($MStaff["KIN_SNY"] != null && $MStaff["KIN_SNY"] != "" ? $MStaff["KIN_SNY"] : 0);
				$BASE_KIN_H_KST = ($MStaff["KIN_H_KST"] != null && $MStaff["KIN_H_KST"] != "" ? $MStaff["KIN_H_KST"] : 0);
				$BASE_KIN_KST = ($MStaff["KIN_KST"] != null && $MStaff["KIN_KST"] != "" ? $MStaff["KIN_KST"] : 0);
				$BASE_KIN_H_KST_SNY = ($MStaff["KIN_H_KST_SNY"] != null && $MStaff["KIN_H_KST_SNY"] != "" ? $MStaff["KIN_H_KST_SNY"] : 0);


				//工程毎のループ
				foreach($jissekiMArray as $jissekiM) {

					if ($BASE_STAFF_CD == $jissekiM['STAFF_CD']) {

						//保持項目
						$BASE_NINUSI_CD  = $jissekiM['NINUSI_CD'];
						$BASE_SOSIKI_CD  = $jissekiM['SOSIKI_CD'];
						$BASE_YMD_SYUGYO  = $jissekiM['YMD_SYUGYO'];
						$BASE_DT_SYUGYO_ST = $jissekiM['DT_SYUGYO_ST'];//就業開始時刻 ・　所定範囲開始時間

						//2.所定時間範囲、深夜時間帯

						//所定時間範囲終了時間
						$BASE_DT_SYOTEI_ED = $this->addTimeForMySQL($BASE_DT_SYUGYO_ST,
							$TNK_CALC_SYOTEI_TIME_CNT + $TNK_CALC_SYOTEI_REST_TIME_CNT,0,0);

						//単価適用時間範囲算出

						//1 工程時刻範囲　時間内・時間外　分割
						$JIKANNAI_KAISHI_TIME = null;
						$JIKANNAI_SYURYO_TIME = null;
						$JIKANGAI_KAISHI_TIME = null;
						$JIKANGAI_SYURYO_TIME = null;

						$KAISHI_HANINAI_FLG = 0;
						$SYURYO_HANINAI_FLG = 0;

						//工程開始時刻について
						if ((strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$BASE_DT_SYUGYO_ST)) <=
								strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ST_H']))) &&
							(strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ST_H'])) <=
								strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$BASE_DT_SYOTEI_ED)))
						) {

							$JIKANNAI_KAISHI_TIME = $jissekiM['DT_KOTEI_ST_H'];

						} else {

							$JIKANGAI_KAISHI_TIME = $jissekiM['DT_KOTEI_ST_H'];
							$KAISHI_HANINAI_FLG = 1;
						}

						//工程終了時刻について
						if ((strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$BASE_DT_SYUGYO_ST)) <=
								strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ED_H']))) &&
							(strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ED_H'])) <=
								strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$BASE_DT_SYOTEI_ED)))
						) {

							$JIKANNAI_SYURYO_TIME = $jissekiM['DT_KOTEI_ED_H'];

						} else {

							$JIKANGAI_SYURYO_TIME = $jissekiM['DT_KOTEI_ED_H'];
							$SYURYO_HANINAI_FLG = 1;
						}

						//フラグ判定
						if ($KAISHI_HANINAI_FLG == 0 &&
							$SYURYO_HANINAI_FLG == 0) {

							//何もしない
						} else if ($KAISHI_HANINAI_FLG == 0 &&
							$SYURYO_HANINAI_FLG == 1) {

							$JIKANNAI_SYURYO_TIME = $BASE_DT_SYOTEI_ED;
							$JIKANGAI_KAISHI_TIME = $BASE_DT_SYOTEI_ED;

						} else if ($KAISHI_HANINAI_FLG == 1 &&
							$SYURYO_HANINAI_FLG == 1) {

							//何もしない
						}

						//2 深夜判定
						$SINYA_TIME_ST = $TNK_CALC_SNY_TEKIYO_TIME_ST;
						$SINYA_TIME_ED = $TNK_CALC_SNY_TEKIYO_TIME_ED;

						$NISSU_COUNT = 1;
						$SHINYA_MINUTES = 0;

						while (strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ED_H'])) >
							strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$SINYA_TIME_ST))) {


							$SHINYA_KAISHI_TIME = null;
							$SHINYA_SYURYO_TIME = null;

							//工程開始時刻について
							if ((strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$SINYA_TIME_ST)) <=
									strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ST_H']))) &&
								(strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ST_H'])) <=
									strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$SINYA_TIME_ED)))
							) {

								$SHINYA_KAISHI_TIME = $jissekiM['DT_KOTEI_ST_H'];
							}


							//工程終了時刻について
							if ((strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$SINYA_TIME_ST)) <=
									strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ED_H']))) &&
								(strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ED_H'])) <=
									strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$SINYA_TIME_ED)))
							) {

								$SHINYA_SYURYO_TIME = $jissekiM['DT_KOTEI_ED_H'];
							}

							if ($SHINYA_KAISHI_TIME == null &&
								$SHINYA_SYURYO_TIME != null) {

								$SHINYA_KAISHI_TIME = $SINYA_TIME_ST;
							}

							if ($SHINYA_KAISHI_TIME != null &&
								$SHINYA_SYURYO_TIME == null) {

								$SHINYA_SYURYO_TIME = $SINYA_TIME_ED;
							}

							if ($SHINYA_KAISHI_TIME == null &&
								$SHINYA_SYURYO_TIME == null) {


								if ((strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ST_H'])) <
										strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$SINYA_TIME_ST))) &&
									(strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ED_H'])) >
										strtotime($this->rechangeDateTime($BASE_YMD_SYUGYO,$SINYA_TIME_ED)))
								) {

									$SHINYA_KAISHI_TIME = $SINYA_TIME_ST;
									$SHINYA_SYURYO_TIME = $SINYA_TIME_ED;
								}

							}

							if ($SHINYA_KAISHI_TIME != null && $SHINYA_SYURYO_TIME != null) {

								$dt1 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$SHINYA_KAISHI_TIME),0,17) . "00");
								$dt2 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$SHINYA_SYURYO_TIME),0,17) . "00");

								$interval = $dt1->diff($dt2);
								$day = $interval->format('%d');
								$hour = $interval->format('%h');
								$minutes = $interval->format('%i');

								$SHINYA_MINUTES = $SHINYA_MINUTES + $hour * 60 + $day * 24 * 60 + $minutes;
							}

							$SINYA_TIME_ST = $this->addTimeForMySQL($SINYA_TIME_ST,24 * $NISSU_COUNT,0,0);
							$SINYA_TIME_ED = $this->addTimeForMySQL($SINYA_TIME_ED,24 * $NISSU_COUNT,0,0);

							$NISSU_COUNT++;
						}

						//その他
						$H_KST_TIME_ST = null;
						$H_KST_TIME_ED = null;
						$KST_TIME_ST = null;
						$KST_TIME_ED = null;


						//単価適用時間算出
						$JIKANNAI_MINUTES = 0;
						$JIKANGAI_MINUTES = 0;
						$H_KST_MINUTES = 0;
						$KST_MINUTES = 0;
						$H_KST_SNY_MINUTES = 0;

						//時間内適用時間
						if ($JIKANNAI_KAISHI_TIME != null && $JIKANNAI_SYURYO_TIME != null) {

							$dt1 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$JIKANNAI_KAISHI_TIME),0,17) . "00");
							$dt2 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$JIKANNAI_SYURYO_TIME),0,17) . "00");

							$interval = $dt1->diff($dt2);
							$day = $interval->format('%d');
							$hour = $interval->format('%h');
							$minutes = $interval->format('%i');

							$JIKANNAI_MINUTES = $hour * 60 + $day * 24 * 60 + $minutes;
						}

						//時間外適用時間
						if ($JIKANGAI_KAISHI_TIME != null && $JIKANGAI_SYURYO_TIME != null) {

							$dt1 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$JIKANGAI_KAISHI_TIME),0,17) . "00");
							$dt2 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$JIKANGAI_SYURYO_TIME),0,17) . "00");

							$interval = $dt1->diff($dt2);
							$day = $interval->format('%d');
							$hour = $interval->format('%h');
							$minutes = $interval->format('%i');

							$JIKANGAI_MINUTES = $hour * 60 + $day * 24 * 60 + $minutes;
						}


						//法廷休出時間
						if ($H_KST_TIME_ST != null && $H_KST_TIME_ED != null) {

							$dt1 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$H_KST_TIME_ST),0,17) . "00");
							$dt2 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$H_KST_TIME_ED),0,17) . "00");

							$interval = $dt1->diff($dt2);
							$day = $interval->format('%d');
							$hour = $interval->format('%h');
							$minutes = $interval->format('%i');

							$H_KST_MINUTES = $hour * 60 + $day * 24 * 60 + $minutes;
						}

						//休出時間
						if ($KST_TIME_ST != null && $KST_TIME_ED != null) {

							$dt1 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$KST_TIME_ST),0,17) . "00");
							$dt2 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$KST_TIME_ED),0,17) . "00");

							$interval = $dt1->diff($dt2);
							$day = $interval->format('%d');
							$hour = $interval->format('%h');
							$minutes = $interval->format('%i');
							$second = $interval->format('%s');

							$KST_MINUTES = $hour * 60 + $day * 24 * 60 + $minutes;
						}

						//コスト算出
						$JIKANNAI_COST = 0;
						$JIKANGAI_COST = 0;
						$SHINYA_COST = 0;
						$H_KST_COST = 0;
						$KST_COST = 0;
						$H_KST_SNY_COST = 0;
						$TOTAL_COST = 0;


						$JIKANNAI_COST = round($BASE_KIN_SYOTEI * ($JIKANNAI_MINUTES / 60));
						$JIKANGAI_COST = round($BASE_KIN_JIKANGAI * ($JIKANGAI_MINUTES / 60));
						$SHINYA_COST = round($BASE_KIN_SNY * ($SHINYA_MINUTES / 60));
						$H_KST_COST = round($BASE_KIN_H_KST * ($H_KST_MINUTES / 60));
						$KST_COST = round($BASE_KIN_KST * ($KST_MINUTES / 60));
						$H_KST_SNY_COST = round($BASE_KIN_H_KST_SNY * ($H_KST_MINUTES / 60));

						$TMP_COST = ($BASE_KIN_SYOTEI * ($JIKANNAI_MINUTES / 60)) +
							($BASE_KIN_JIKANGAI * ($JIKANGAI_MINUTES / 60)) +
							($BASE_KIN_SNY * ($SHINYA_MINUTES / 60)) +
							($BASE_KIN_H_KST * ($H_KST_MINUTES / 60)) +
							($BASE_KIN_KST * ($KST_MINUTES / 60)) +
							($BASE_KIN_H_KST_SNY * ($H_KST_MINUTES / 60));

						$TOTAL_COST = round($TMP_COST);

						//単価算出
						$TANKA = 0;

						if ( ( $JIKANNAI_MINUTES +
								$JIKANGAI_MINUTES +
								$SHINYA_MINUTES +
								$H_KST_MINUTES +
								$KST_MINUTES +
								$H_KST_SNY_MINUTES ) != 0) {

							$TANKA = round(($TMP_COST / ( $JIKANNAI_MINUTES +
										$JIKANGAI_MINUTES +
										$SHINYA_MINUTES +
										$H_KST_MINUTES +
										$KST_MINUTES +
										$H_KST_SNY_MINUTES)) * 60);
						} else {

							$TANKA = 0;
						}



						//工程就業時間算出
						$KOTEI_SYUGYO_TIME = 0;


						$dt1 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ST_H']),0,17) . "00");
						$dt2 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ED_H']),0,17) . "00");

						$interval = $dt1->diff($dt2);
						$day = $interval->format('%d');
						$hour = $interval->format('%h');
						$minutes = $interval->format('%i');

						$KOTEI_SYUGYO_TIME = $hour * 60 + $day * 24 * 60 + $minutes;

						if ($KOTEI_SYUGYO_TIME == 0) {
							continue;
						}

						$array = array (

							"NINUSI_CD" => $BASE_NINUSI_CD,
							"SOSIKI_CD" => $BASE_SOSIKI_CD,
							"YMD_SIME" => $ymd_sime,
							"STAFF_CD" => $BASE_STAFF_CD,
							"YMD_SYUGYO" => $BASE_YMD_SYUGYO,
							"BNRI_DAI_CD" => $jissekiM['BNRI_DAI_CD'],
							"BNRI_CYU_CD" => $jissekiM['BNRI_CYU_CD'],
							"BNRI_SAI_CD" => $jissekiM['BNRI_SAI_CD'],
							"TIME_KOTEI_ST" => $jissekiM['DT_KOTEI_ST_H'],
							"TIME_KOTEI_ED" => $jissekiM['DT_KOTEI_ED_H'],
							"TIME_KOTEI_SYUGYO" => $KOTEI_SYUGYO_TIME,
							"TIME_SYUGYO_ST" => $BASE_DT_SYUGYO_ST,
							"TIME_SYUGYO_ED" => $BASE_DT_SYOTEI_ED,
							"TIME_SNY_TEKIYO_ST" => $TNK_CALC_SNY_TEKIYO_TIME_ST,
							"TIME_SNY_TEKIYO_ED" => $TNK_CALC_SNY_TEKIYO_TIME_ED,
							"KIN_SYOTEI" => $BASE_KIN_SYOTEI,
							"KIN_JIKANGAI" => $BASE_KIN_JIKANGAI,
							"KIN_SNY" => $BASE_KIN_SNY,
							"KIN_KST" => $BASE_KIN_KST,
							"KIN_H_KST" => $BASE_KIN_H_KST,
							"KIN_H_KST_SNY" => $BASE_KIN_H_KST_SNY,
							"TIME_SYOTEI_ST" => $JIKANNAI_KAISHI_TIME,
							"TIME_SYOTEI_ED" => $JIKANNAI_SYURYO_TIME,
							"TIME_JIKANGAI_ST" => $JIKANGAI_KAISHI_TIME,
							"TIME_JIKANGAI_ED" => $JIKANGAI_SYURYO_TIME,
							"TIME_KST_ST" => $KST_TIME_ST,
							"TIME_KST_ED" => $KST_TIME_ED,
							"TIME_H_KST_ST" => $H_KST_TIME_ST,
							"TIME_H_KST_ED" => $H_KST_TIME_ED,
							"TIME_CNT_SYOTEI" => $JIKANNAI_MINUTES,
							"TIME_CNT_JIKANGAI" => $JIKANGAI_MINUTES,
							"TIME_CNT_SNY" => $SHINYA_MINUTES,
							"TIME_CNT_KST" => $KST_MINUTES,
							"TIME_CNT_H_KST" => $H_KST_MINUTES,
							"TIME_CNT_H_KST_SNY" => $H_KST_SNY_MINUTES,
							"COST_SYOTEI" => $JIKANNAI_COST,
							"COST_JIKANGAI" => $JIKANGAI_COST,
							"COST_SNY" => $SHINYA_COST,
							"COST_KST" => $KST_COST,
							"COST_H_KST" => $H_KST_COST,
							"COST_H_KST_SNY" => $H_KST_SNY_COST,
							"COST" => $TOTAL_COST,
							"TNK" => $TANKA

						);

						$resultArray[] = $array;

					}


				}


			}

			$pdo = null;

			return $resultArray;

		} catch (Exception $e) {

			$pdo = null;
			throw $e;
		}

	}

	/**
	 * 売上算出
	 * @param ABCデータベース日別配列
	 * @return 売上算出結果配列
	 */
	public function getUriage($hibetuArray) {

		$pdo = null;
		$resultArray = array();

		try {

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);

			//算出工程マスタ取得
			$sql  = "SELECT ";
			$sql .= "   A.NINUSI_CD,";
			$sql .= "   A.SOSIKI_CD,";
			$sql .= "   A.BNRI_DAI_CD,";
			$sql .= "   A.BNRI_CYU_CD,";
			$sql .= "   A.BNRI_SAI_CD,";
			$sql .= "   B.KBN_URIAGE";
			$sql .= " FROM";
			$sql .= "   M_CALC_TGT_KOTEI A";
			$sql .= "    INNER JOIN M_BNRI_CYU B ON";
			$sql .= "      A.NINUSI_CD=B.NINUSI_CD AND";
			$sql .= "      A.SOSIKI_CD=B.SOSIKI_CD AND";
			$sql .= "      A.BNRI_DAI_CD=B.BNRI_DAI_CD AND";
			$sql .= "      A.BNRI_CYU_CD=B.BNRI_CYU_CD AND";
			$sql .= "      B.KBN_URIAGE<>'00'";
			$sql .= " WHERE";
			$sql .= "   A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   A.SOSIKI_CD='" . $this->_sosiki_cd . "'";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"算出工程マスタ　取得");

			$MCalcTGTKoteiArray = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {

				$array = array(
						"NINUSI_CD" => $result["NINUSI_CD"],
						"SOSIKI_CD" => $result["SOSIKI_CD"],
						"BNRI_DAI_CD" => $result["BNRI_DAI_CD"],
						"BNRI_CYU_CD" => $result["BNRI_CYU_CD"],
						"BNRI_SAI_CD" => $result["BNRI_SAI_CD"],
						"KBN_URIAGE" => $result["KBN_URIAGE"],
				);

				$MCalcTGTKoteiArray[] = $array;
			}

			//荷主契約情報取得
			$sql  = "SELECT ";
			$sql .= "   NINUSI_CD,";
			$sql .= "   SOSIKI_CD,";
			$sql .= "   BNRI_DAI_CD,";
			$sql .= "   BNRI_CYU_CD,";
			$sql .= "   SURYO,";
			$sql .= "   HIYOU";
			$sql .= " FROM";
			$sql .= "   M_NINUSI_KEIYAKU";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   HIYOU IS NOT NULL AND";
			$sql .= "   SURYO IS NOT NULL";
			$sql .= " ORDER BY";
			$sql .= "   BNRI_DAI_CD,";
			$sql .= "   BNRI_CYU_CD";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"荷主契約情報　取得");

			$MNinusiKeiyakuArray = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {

				$array = array(
						"NINUSI_CD" => $result["NINUSI_CD"],
						"SOSIKI_CD" => $result["SOSIKI_CD"],
						"BNRI_DAI_CD" => $result["BNRI_DAI_CD"],
						"BNRI_CYU_CD" => $result["BNRI_CYU_CD"],
						"SURYO" => $result["SURYO"],
						"HIYOU" => $result["HIYOU"],
				);

				$MNinusiKeiyakuArray[] = $array;
			}

			foreach ($MNinusiKeiyakuArray as $MNinusiKeiyaku ) {

				$BASE_NINUSI_CD = $MNinusiKeiyaku['NINUSI_CD'];
				$BASE_SOSIKI_CD = $MNinusiKeiyaku['SOSIKI_CD'];
				$BASE_BNRI_DAI_CD = $MNinusiKeiyaku["BNRI_DAI_CD"];
				$BASE_BNRI_CYU_CD = $MNinusiKeiyaku["BNRI_CYU_CD"];
				
				//按分基準値
				$BASE_ANBUN = 0;
				
				//契約単位物量
				$BASE_BUTURYO = 0;
				
				$recordCount = 0;

				foreach($hibetuArray as $hibetu) {


					if (($BASE_NINUSI_CD == $hibetu['NINUSI_CD'] &&
						 $BASE_SOSIKI_CD == $hibetu['SOSIKI_CD'] &&
						 $BASE_BNRI_DAI_CD == $hibetu['BNRI_DAI_CD'] &&
						 $BASE_BNRI_CYU_CD == $hibetu['BNRI_CYU_CD'] &&
						 $BASE_BNRI_CYU_CD != "000") ||
						($BASE_NINUSI_CD == $hibetu['NINUSI_CD'] &&
						 $BASE_SOSIKI_CD == $hibetu['SOSIKI_CD'] &&
						 $BASE_BNRI_DAI_CD == $hibetu['BNRI_DAI_CD'] &&
						 $BASE_BNRI_CYU_CD == "000")) {


						$foundFlg = 0;

						//売上区分
						$KBN_URIAGE = null;

						//算出対象工程かどうか
						foreach($MCalcTGTKoteiArray as $array) {

							if ($BASE_NINUSI_CD == $array['NINUSI_CD'] &&
							$BASE_SOSIKI_CD == $array['SOSIKI_CD'] &&
							$hibetu['BNRI_DAI_CD'] == $array['BNRI_DAI_CD'] &&
							$hibetu['BNRI_CYU_CD'] == $array['BNRI_CYU_CD'] &&
							(empty($hibetu['BNRI_SAI_CD']) || $hibetu['BNRI_SAI_CD'] == $array['BNRI_SAI_CD'])) {

								$KBN_URIAGE = $array['KBN_URIAGE'];

								$foundFlg = 1;
								break;

							}
						}

						if ($foundFlg == 0) {
							continue;
						}


						$COST = $hibetu['ABC_COST'];

						if ($KBN_URIAGE == '01') {

							$BUTURYO = 1;

						} else {

							$BUTURYO = $hibetu['BUTURYO'];
						}
						
						//物量０は無視
						if ($BUTURYO == 0) {
							continue;
						}
						
						//按分値算出
						$ANBUN = $COST / $BUTURYO;
						
						//按分基準値に追加
						$BASE_ANBUN = $BASE_ANBUN + $ANBUN;
						
						//契約単位での物量を算出
						if ($hibetu['KEIYAKU_BUTURYO_FLG'] == 1) {
								$BASE_BUTURYO = $BUTURYO;
							
						}
						
						$recordCount++;
					}
				}

				if ($recordCount == 0) {

					continue;
				}
				
				
				if ($BASE_BUTURYO == 0 ) {

					continue;
				}

				$KEIYAKU_URIAGE = 0;
				

				//契約単価売り上げ金額算出
				if ($BASE_BUTURYO != 0 && $MNinusiKeiyaku["SURYO"] != 0) {
					$KEIYAKU_URIAGE = round($BASE_BUTURYO * ($MNinusiKeiyaku["HIYOU"] / $MNinusiKeiyaku["SURYO"]));
				}

				if ($KEIYAKU_URIAGE != 0 && $BASE_ANBUN != 0) {

					//抽出データ単位の単位売り上げ金額算出

					//売り上げ金額の最高額と、売り上げ合計
					$KOTEI_URIAGE_TOP = 0;
					$TOTAL_KOTEI_URIAGE = 0;

					foreach($hibetuArray as $hibetu) {

						if (($BASE_NINUSI_CD == $hibetu['NINUSI_CD'] &&
								$BASE_SOSIKI_CD == $hibetu['SOSIKI_CD'] &&
								$BASE_BNRI_DAI_CD == $hibetu['BNRI_DAI_CD'] &&
								$BASE_BNRI_CYU_CD == $hibetu['BNRI_CYU_CD'] &&
								$BASE_BNRI_CYU_CD != "000") ||
								($BASE_NINUSI_CD == $hibetu['NINUSI_CD'] &&
										$BASE_SOSIKI_CD == $hibetu['SOSIKI_CD'] &&
										$BASE_BNRI_DAI_CD == $hibetu['BNRI_DAI_CD'] &&
										$BASE_BNRI_CYU_CD == "000")) {


							//売上区分
							$KBN_URIAGE = null;

							//算出対象工程かどうか
							foreach($MCalcTGTKoteiArray as $array) {

								if ($BASE_NINUSI_CD == $array['NINUSI_CD'] &&
								$BASE_SOSIKI_CD == $array['SOSIKI_CD'] &&
								$hibetu['BNRI_DAI_CD'] == $array['BNRI_DAI_CD'] &&
								$hibetu['BNRI_CYU_CD'] == $array['BNRI_CYU_CD'] &&
								(empty($hibetu['BNRI_SAI_CD']) || $hibetu['BNRI_SAI_CD'] == $array['BNRI_SAI_CD'])) {

									$KBN_URIAGE = $array['KBN_URIAGE'];

									break;

								}
							}

							$ANBUN = 1;

							if ($KBN_URIAGE == '01') {

								$ANBUN = $hibetu['ABC_COST'] / 1;

							} else {

								if ($hibetu['BUTURYO'] != 0) {

									$ANBUN = $hibetu['ABC_COST'] / $hibetu['BUTURYO'];
								} else {

									$ANBUN = 0;
								}

							}

							if ($ANBUN != 0 && $BASE_ANBUN != 0) {

								$KOTEI_URIAGE = round($KEIYAKU_URIAGE * round($ANBUN / $BASE_ANBUN,4));

								$TOTAL_KOTEI_URIAGE = $TOTAL_KOTEI_URIAGE + $KOTEI_URIAGE;

								if ($KOTEI_URIAGE > $KOTEI_URIAGE_TOP) {

									$KOTEI_URIAGE_TOP = $KOTEI_URIAGE;
								}

							}

						}
					}

					//差額
					$SAGAKU = $KEIYAKU_URIAGE - $TOTAL_KOTEI_URIAGE;

					$sagakuFlg = 0;

					//結果作成
					foreach($hibetuArray as $hibetu) {

						if (($BASE_NINUSI_CD == $hibetu['NINUSI_CD'] &&
								$BASE_SOSIKI_CD == $hibetu['SOSIKI_CD'] &&
								$BASE_BNRI_DAI_CD == $hibetu['BNRI_DAI_CD'] &&
								$BASE_BNRI_CYU_CD == $hibetu['BNRI_CYU_CD'] &&
								$BASE_BNRI_CYU_CD != "000") ||
								($BASE_NINUSI_CD == $hibetu['NINUSI_CD'] &&
										$BASE_SOSIKI_CD == $hibetu['SOSIKI_CD'] &&
										$BASE_BNRI_DAI_CD == $hibetu['BNRI_DAI_CD'] &&
										$BASE_BNRI_CYU_CD == "000")) {

							//売上区分
							$KBN_URIAGE = null;

							//算出対象工程かどうか
							foreach($MCalcTGTKoteiArray as $array) {

								if ($BASE_NINUSI_CD == $array['NINUSI_CD'] &&
								$BASE_SOSIKI_CD == $array['SOSIKI_CD'] &&
								$hibetu['BNRI_DAI_CD'] == $array['BNRI_DAI_CD'] &&
								$hibetu['BNRI_CYU_CD'] == $array['BNRI_CYU_CD'] &&
								(empty($hibetu['BNRI_SAI_CD']) || $hibetu['BNRI_SAI_CD'] == $array['BNRI_SAI_CD'])) {

									$KBN_URIAGE = $array['KBN_URIAGE'];

									break;

								}
							}


							$ANBUN = 1;

							if ($KBN_URIAGE == '01') {

								$ANBUN = $hibetu['ABC_COST'] / 1;

							} else {

								if ($hibetu['BUTURYO'] != 0) {

									$ANBUN = $hibetu['ABC_COST'] / $hibetu['BUTURYO'];
								} else {

									$ANBUN = 0;
								}

							}

							if ($ANBUN != 0 && $BASE_ANBUN != 0) {

								$KOTEI_URIAGE = round($KEIYAKU_URIAGE * round($ANBUN / $BASE_ANBUN,4));

								if ($KOTEI_URIAGE == $KOTEI_URIAGE_TOP && $sagakuFlg == 0) {

									$KOTEI_URIAGE = $SAGAKU + $KOTEI_URIAGE;
									$sagakuFlg = 1;
								}
							}


							$dataArray = array(

								 "NINUSI_CD" => $BASE_NINUSI_CD,
								 "SOSIKI_CD" => $BASE_SOSIKI_CD,
								 "BNRI_DAI_CD" => $hibetu['BNRI_DAI_CD'],
								 "BNRI_CYU_CD" => $hibetu['BNRI_CYU_CD'],
								 "BNRI_SAI_CD" => $hibetu['BNRI_SAI_CD'],
								 "ABC_COST" => $hibetu["ABC_COST"],
								 "BUTURYO" => $hibetu["BUTURYO"],
								 "KBN_URIAGE" => $KBN_URIAGE,
								 "KEIYAKU_BNRI_DAI_CD" => $BASE_BNRI_DAI_CD,
								 "KEIYAKU_BNRI_CYU_CD" => $BASE_BNRI_CYU_CD,
								 "KEIYAKU_BUTURYO" => $BASE_BUTURYO,
								 "KEIYAKU_URIAGE" => $KEIYAKU_URIAGE,
								 "ANBUN" => $ANBUN / $BASE_ANBUN,
								 "URIAGE" => $KOTEI_URIAGE

							);

							$resultArray[] = $dataArray;
						}
					}
				}
			}


			$pdo = null;

			return $resultArray;

		} catch (Exception $e) {

			$pdo = null;
			throw $e;
		}

		return array();
	}

	/**
	 * 物量妥当性チェック
	 * @param 年月日(自)
	 * @param 年月日(至)
	 * @param 動作モード
	 * @param 物量配列
	 * @return 結果配列
	 */
	public function checkButuryo($ymd_from,$ymd_to,$mode,$buturyoArray) {

		$pdo = null;
		$resultArray = array();

		try {

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);

			if ($mode == "0") {

				$sql  = "SELECT ";
				$sql .= "   A.NINUSI_CD,";
				$sql .= "   A.SOSIKI_CD,";
				$sql .= "   B.YMD,";
				$sql .= "   A.BNRI_DAI_CD AS KEIYAKU_BNRI_DAI_CD,";
				$sql .= "   A.BNRI_CYU_CD AS KEIYAKU_BNRI_CYU_CD,";
				$sql .= "   C.BNRI_DAI_RYAKU,";
				$sql .= "   D.BNRI_CYU_RYAKU,";
				$sql .= "   B.BNRI_DAI_CD,";
				$sql .= "   B.BNRI_CYU_CD,";
				$sql .= "   B.BNRI_SAI_CD,";
				$sql .= "   B.BUTURYO,";
				$sql .= "   B.SURYO_LINE";
				$sql .= " FROM";
				$sql .= "   M_NINUSI_KEIYAKU A";
				$sql .= "     INNER JOIN T_ABC_DB_DAY B ON";
				$sql .= "       A.NINUSI_CD=B.NINUSI_CD AND";
				$sql .= "       A.SOSIKI_CD=B.SOSIKI_CD AND";
				$sql .= "       (";
				$sql .= "         (";
				$sql .= "           A.BNRI_DAI_CD=B.BNRI_DAI_CD AND";
				$sql .= "           A.BNRI_CYU_CD='000'";
				$sql .= "         )";
				$sql .= "         OR";
				$sql .= "         (";
				$sql .= "           A.BNRI_DAI_CD=B.BNRI_DAI_CD AND";
				$sql .= "           A.BNRI_CYU_CD=B.BNRI_CYU_CD AND";
				$sql .= "           A.BNRI_CYU_CD<>'000'";
				$sql .= "         )";
				$sql .= "       )";
				$sql .= "       AND";
				$sql .= "       B.YMD>='" . $ymd_from . "' AND";
				$sql .= "       B.YMD<='" . $ymd_to . "'";
				$sql .= "     LEFT JOIN M_BNRI_DAI C ON";
				$sql .= "       A.NINUSI_CD=C.NINUSI_CD AND";
				$sql .= "       A.SOSIKI_CD=C.SOSIKI_CD AND";
				$sql .= "       A.BNRI_DAI_CD=C.BNRI_DAI_CD";
				$sql .= "     LEFT JOIN M_BNRI_CYU D ON";
				$sql .= "       A.NINUSI_CD=D.NINUSI_CD AND";
				$sql .= "       A.SOSIKI_CD=D.SOSIKI_CD AND";
				$sql .= "       A.BNRI_DAI_CD=D.BNRI_DAI_CD AND";
				$sql .= "       A.BNRI_CYU_CD=D.BNRI_CYU_CD";
				$sql .= " WHERE";
				$sql .= "   A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
				$sql .= "   A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
				$sql .= "   A.HIYOU IS NOT NULL AND";
				$sql .= "   A.SURYO IS NOT NULL";
				$sql .= " ORDER BY";
				$sql .= "   A.NINUSI_CD,";
				$sql .= "   A.SOSIKI_CD,";
				$sql .= "   B.YMD,";
				$sql .= "   A.BNRI_DAI_CD,";
				$sql .= "   A.BNRI_CYU_CD,";
				$sql .= "   B.BNRI_DAI_CD,";
				$sql .= "   B.BNRI_CYU_CD,";
				$sql .= "   B.BNRI_SAI_CD";

			} else if ($mode == "1") {

				$sql  = "SELECT ";
				$sql .= "   A.NINUSI_CD,";
				$sql .= "   A.SOSIKI_CD,";
				$sql .= "   B.YMD,";
				$sql .= "   A.BNRI_DAI_CD AS KEIYAKU_BNRI_DAI_CD,";
				$sql .= "   A.BNRI_CYU_CD AS KEIYAKU_BNRI_CYU_CD,";
				$sql .= "   C.BNRI_DAI_RYAKU,";
				$sql .= "   D.BNRI_CYU_RYAKU,";
				$sql .= "   B.BNRI_DAI_CD,";
				$sql .= "   B.BNRI_CYU_CD,";
				$sql .= "   B.BNRI_SAI_CD,";
				$sql .= "   B.BUTURYO,";
				$sql .= "   B.SURYO_LINE";
				$sql .= " FROM";
				$sql .= "   M_NINUSI_KEIYAKU A";
				$sql .= "     INNER JOIN T_LSP_YOSOKU_D B ON";
				$sql .= "       A.NINUSI_CD=B.NINUSI_CD AND";
				$sql .= "       A.SOSIKI_CD=B.SOSIKI_CD AND";
				$sql .= "       (";
				$sql .= "         (";
				$sql .= "           A.BNRI_DAI_CD=B.BNRI_DAI_CD AND";
				$sql .= "           A.BNRI_CYU_CD='000'";
				$sql .= "         )";
				$sql .= "         OR";
				$sql .= "         (";
				$sql .= "           A.BNRI_DAI_CD=B.BNRI_DAI_CD AND";
				$sql .= "           A.BNRI_CYU_CD=B.BNRI_CYU_CD AND";
				$sql .= "           A.BNRI_CYU_CD<>'000'";
				$sql .= "         )";
				$sql .= "       )";
				$sql .= "       AND";
				$sql .= "       B.YMD>='" . $ymd_from . "' AND";
				$sql .= "       B.YMD<='" . $ymd_to . "'";
				$sql .= "     LEFT JOIN M_BNRI_DAI C ON";
				$sql .= "       A.NINUSI_CD=C.NINUSI_CD AND";
				$sql .= "       A.SOSIKI_CD=C.SOSIKI_CD AND";
				$sql .= "       A.BNRI_DAI_CD=C.BNRI_DAI_CD";
				$sql .= "     LEFT JOIN M_BNRI_CYU D ON";
				$sql .= "       A.NINUSI_CD=D.NINUSI_CD AND";
				$sql .= "       A.SOSIKI_CD=D.SOSIKI_CD AND";
				$sql .= "       A.BNRI_DAI_CD=D.BNRI_DAI_CD AND";
				$sql .= "       A.BNRI_CYU_CD=D.BNRI_CYU_CD";
				$sql .= " WHERE";
				$sql .= "   A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
				$sql .= "   A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
				$sql .= "   A.HIYOU IS NOT NULL AND";
				$sql .= "   A.SURYO IS NOT NULL";
				$sql .= " ORDER BY";
				$sql .= "   A.NINUSI_CD,";
				$sql .= "   A.SOSIKI_CD,";
				$sql .= "   B.YMD,";
				$sql .= "   A.BNRI_DAI_CD,";
				$sql .= "   A.BNRI_CYU_CD,";
				$sql .= "   B.BNRI_DAI_CD,";
				$sql .= "   B.BNRI_CYU_CD,";
				$sql .= "   B.BNRI_SAI_CD";

			}

			$this->queryWithPDOLog($stmt,$pdo,$sql,"荷主契約情報　取得");

			$MNinusiKeiyakuArray = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {

				$array = array(
						"NINUSI_CD" => $result["NINUSI_CD"],
						"SOSIKI_CD" => $result["SOSIKI_CD"],
						"YMD" => $result["YMD"],
						"KEIYAKU_BNRI_DAI_CD" => $result["KEIYAKU_BNRI_DAI_CD"],
						"KEIYAKU_BNRI_CYU_CD" => $result["KEIYAKU_BNRI_CYU_CD"],
						"BNRI_DAI_RYAKU" => $result["BNRI_DAI_RYAKU"],
						"BNRI_CYU_RYAKU" => $result["BNRI_CYU_RYAKU"],
						"BNRI_DAI_CD" => $result["BNRI_DAI_CD"],
						"BNRI_CYU_CD" => $result["BNRI_CYU_CD"],
						"BNRI_SAI_CD" => $result["BNRI_SAI_CD"],
						"BUTURYO" => $result["BUTURYO"],
						"SURYO_LINE" => $result["SURYO_LINE"],
				);

				$MNinusiKeiyakuArray[] = $array;
			}

			//物量基本情報最新化
			foreach ($MNinusiKeiyakuArray as $MNinusiKeiyaku) {

				$BASE_NINUSI_CD = $MNinusiKeiyaku["NINUSI_CD"];
				$BASE_SOSIKI_CD = $MNinusiKeiyaku["SOSIKI_CD"];
				$BASE_YMD = $MNinusiKeiyaku["YMD"];
				$BASE_BNRI_DAI_CD = $MNinusiKeiyaku["BNRI_DAI_CD"];
				$BASE_BNRI_CYU_CD = $MNinusiKeiyaku["BNRI_CYU_CD"];
				$BASE_BNRI_SAI_CD = $MNinusiKeiyaku["BNRI_SAI_CD"];

				foreach ($buturyoArray as $buturyo) {

					if ($BASE_NINUSI_CD = $buturyo['NINUSI_CD'] &&
					$BASE_SOSIKI_CD = $buturyo['SOSIKI_CD'] &&
					$BASE_YMD = $buturyo['YMD'] &&
					$BASE_BNRI_DAI_CD = $buturyo['BNRI_DAI_CD'] &&
					$BASE_BNRI_CYU_CD = $buturyo['BNRI_CYU_CD'] &&
					$BASE_BNRI_SAI_CD = $buturyo['BNRI_SAI_CD']) {


						$MNinusiKeiyaku["BUTURYO"] =  $buturyo['BUTURYO'];
						$MNinusiKeiyaku["SURYO_LINE"] =  $buturyo['SURYO_LINE'];

					}

				}
			}

			//物量情報判定

			$primaryArray = array();

			$TMP_NINUSI_CD = "";
			$TMP_SOSIKI_CD = "";
			$TMP_YMD = "";
			$TMP_KEIYAKU_BNRI_DAI_CD = "";
			$TMP_KEIYAKU_BNRI_CYU_CD = "";

			foreach ($MNinusiKeiyakuArray as $MNinusiKeiyaku) {

				$NINUSI_CD = $MNinusiKeiyaku["NINUSI_CD"];
				$SOSIKI_CD = $MNinusiKeiyaku["SOSIKI_CD"];
				$YMD = $MNinusiKeiyaku["YMD"];
				$KEIYAKU_BNRI_DAI_CD = $MNinusiKeiyaku["KEIYAKU_BNRI_DAI_CD"];
				$KEIYAKU_BNRI_CYU_CD = $MNinusiKeiyaku["KEIYAKU_BNRI_CYU_CD"];
				$BNRI_DAI_RYAKU = $MNinusiKeiyaku["BNRI_DAI_RYAKU"];
				$BNRI_CYU_RYAKU = $MNinusiKeiyaku["BNRI_CYU_RYAKU"];
				$BUTURYO = $MNinusiKeiyaku["BUTURYO"];
				$SURYO_LINE= $MNinusiKeiyaku["SURYO_LINE"];

				if ($TMP_NINUSI_CD != $NINUSI_CD ||
				$TMP_SOSIKI_CD != $SOSIKI_CD ||
				$TMP_YMD != $YMD ||
				$TMP_KEIYAKU_BNRI_DAI_CD != $KEIYAKU_BNRI_DAI_CD ||
				$TMP_KEIYAKU_BNRI_CYU_CD != $KEIYAKU_BNRI_CYU_CD) {

					$primary = array(

							"NINUSI_CD" => $NINUSI_CD,
							"SOSIKI_CD" => $SOSIKI_CD,
							"YMD" => $YMD,
							"KEIYAKU_BNRI_DAI_CD" => $KEIYAKU_BNRI_DAI_CD,
							"KEIYAKU_BNRI_CYU_CD" => $KEIYAKU_BNRI_CYU_CD,
							"BUTURYO" => $BUTURYO,
							"SURYO_LINE" => $SURYO_LINE,
							"BNRI_DAI_RYAKU" => $BNRI_DAI_RYAKU,
							"BNRI_CYU_RYAKU" => $BNRI_CYU_RYAKU

					);

					$primaryArray[] = $primary;

					$TMP_NINUSI_CD = $NINUSI_CD;
					$TMP_SOSIKI_CD = $SOSIKI_CD;
					$TMP_YMD = $YMD;
					$TMP_KEIYAKU_BNRI_DAI_CD = $KEIYAKU_BNRI_DAI_CD;
					$TMP_KEIYAKU_BNRI_CYU_CD = $KEIYAKU_BNRI_CYU_CD;

				}


			}

			foreach($primaryArray as $primary) {

				$NINUSI_CD = $primary["NINUSI_CD"];
				$SOSIKI_CD = $primary["SOSIKI_CD"];
				$YMD = $primary["YMD"];
				$KEIYAKU_BNRI_DAI_CD = $primary["KEIYAKU_BNRI_DAI_CD"];
				$KEIYAKU_BNRI_CYU_CD = $primary["KEIYAKU_BNRI_CYU_CD"];
				$BUTURYO = $primary["BUTURYO"];
				$SURYO_LINE= $primary["SURYO_LINE"];
				$BNRI_DAI_RYAKU = $primary["BNRI_DAI_RYAKU"];
				$BNRI_CYU_RYAKU = $primary["BNRI_CYU_RYAKU"];

				$errorFlg = 0;

				foreach ($MNinusiKeiyakuArray as $MNinusiKeiyaku) {

					if ($NINUSI_CD == $MNinusiKeiyaku["NINUSI_CD"] &&
					$SOSIKI_CD == $MNinusiKeiyaku["SOSIKI_CD"] &&
					$YMD == $MNinusiKeiyaku["YMD"] &&
					$KEIYAKU_BNRI_DAI_CD == $MNinusiKeiyaku["KEIYAKU_BNRI_DAI_CD"] &&
					$KEIYAKU_BNRI_CYU_CD == $MNinusiKeiyaku["KEIYAKU_BNRI_CYU_CD"]) {

						if ($BUTURYO != $MNinusiKeiyaku["BUTURYO"] ||
						$SURYO_LINE != $MNinusiKeiyaku["SURYO_LINE"]) {

							$errorFlg = 1;
							break;
						}

					}
				}

				if ($errorFlg == 1) {

					$array = array(
							"NINUSI_CD" => $NINUSI_CD,
							"SOSIKI_CD" => $SOSIKI_CD,
							"YMD" => $YMD,
							"KEIYAKU_BNRI_DAI_CD" => $KEIYAKU_BNRI_DAI_CD,
							"KEIYAKU_BNRI_CYU_CD" => $KEIYAKU_BNRI_CYU_CD,
							"BNRI_DAI_RYAKU" => $BNRI_DAI_RYAKU,
							"BNRI_CYU_RYAKU" => $BNRI_CYU_RYAKU
					);

					$resultArray[] = $array;
				}
			}

			$pdo = null;

			return $resultArray;

		} catch (Exception $e) {

			$pdo = null;
			throw $e;
		}

		return array();
	}
	
	/**
	 * バッチの指定処理日の当日生産性算出 (スタッフ指定の場合は、処理日、スタッフCD以外をNULLにする）
	 * @param バッチ処理日
	 * @param バッチコード
	 * @param 集計管理No
	 * @param バッチコード
	 * @param 処理モード 0:物量 1:ライン数量
	 * @return 結果配列
	 */
	public function getSeisansei($ymd_syori,$s_batch_cd,$s_kanri_no = null,$staff_cd = null,$mode = 0,$max_kado_time = null) {

		$pdo = null;
		$resultArray = array();

		try {

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			//就業開始基準時間取得
			$sql  = "SELECT ";
			$sql .= "   DT_SYUGYO_ST_BASE,";
			$sql .= "   CURRENT_DATE,";
			$sql .= "   NOW() AS NOW";
			$sql .= " FROM";
			$sql .= "   M_SYSTEM";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "';";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業開始基準時間　取得");
	
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$dt_syugyo_st_base = $result['DT_SYUGYO_ST_BASE'];
			$ymd_today = $result['CURRENT_DATE'];
			$now = $result['NOW'];
			
			//日付取得			
			$tmpDate = $ymd_today . " " . $dt_syugyo_st_base;
			
			if (strtotime($now) < strtotime($tmpDate)) {
			
				$dt = new DateTime($tmpDate);
				$dt->sub(new DateInterval("P1D"));
				$ymd_today = $dt->format('Y-m-d');
				$tmpDate = $ymd_today . " " . $dt_syugyo_st_base;
			}
			
			//日付取得			
			$tmpDate = $ymd_today . " " . $dt_syugyo_st_base;
			$dt = new DateTime($tmpDate);
			$date1 = $dt->format('Y-m-d H:i:s');
			$dt->add(new DateInterval("P1D"));
			$date2 = $dt->format('Y-m-d H:i:s');
			
			//稼働スタッフを取得
			$STAFF_CDArray = array();
			
			if ($staff_cd == null) {
			
				$sql  = "SELECT";
				$sql .= "    A.STAFF_CD";
				$sql .= "  FROM";
				$sql .= "    T_PICK_S_KANRI A";
				$sql .= "  WHERE";
				$sql .= "    A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
				$sql .= "    A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
				$sql .= "    A.YMD_SYORI='" . $ymd_syori . "' AND";
				$sql .= "    A.DT_SYORI_SUMI IS NULL AND";
				$sql .= "    A.DT_SYORI_CYU IS NOT NULL AND";
				$sql .= "    A.S_BATCH_NO_CD='" . $s_batch_cd . "'";
				
				if ($s_kanri_no != null ) {
					
					$sql .= "  AND";
					$sql .= "    A.S_KANRI_NO='" . $s_kanri_no . "'";
				}
				
				$sql .= "  GROUP BY";
				$sql .= "    A.STAFF_CD";
				
				$this->queryWithPDOLog($stmt,$pdo,$sql,"稼働スタッフ算出　取得");
				
				while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
					
					$STAFF_CDArray[] = $result["STAFF_CD"];
				}
			
			} else {
				
				$STAFF_CDArray[] = $staff_cd;
			}
			
			if (count($STAFF_CDArray) == 0) {
			
				$array = array(
					
					"KADO_STAFF" => 0,
					"SEISANSEI" => 0,
					"BUTURYO" => 0,
					"KADO_TIME" => 0
				);
				
				return $array;
			}
			
			//1. 就業実績明細情報取得
			
			//1_1. 時間範囲内
			$sql  = "SELECT ";
			$sql .= "   A.NINUSI_CD,";
			$sql .= "   A.SOSIKI_CD,";
			$sql .= "   A.YMD_SYUGYO,";
			$sql .= "   A.STAFF_CD,";
			$sql .= "   A.BNRI_DAI_CD,";
			$sql .= "   A.BNRI_CYU_CD,";
			$sql .= "   A.BNRI_SAI_CD,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(A.DT_KOTEI_ST), A.YMD_SYUGYO) <> 0 THEN";		
			$sql .= "         ADDTIME(TIME(A.DT_KOTEI_ST), concat(24 * DATEDIFF(DATE(A.DT_KOTEI_ST), A.YMD_SYUGYO),':00:00'))";	
			$sql .= "       ELSE";	
			$sql .= "         TIME(A.DT_KOTEI_ST)";
			$sql .= "     END AS DT_KOTEI_ST_H,";	
			$sql .= "   CASE";		
			$sql .= "     WHEN DATEDIFF(DATE(A.DT_KOTEI_ED), A.YMD_SYUGYO) <> 0 THEN";	
			$sql .= "         ADDTIME(TIME(A.DT_KOTEI_ED), concat(24 * DATEDIFF(DATE(A.DT_KOTEI_ED), A.YMD_SYUGYO),':00:00'))";
			$sql .= "       ELSE TIME(A.DT_KOTEI_ED)";		
			$sql .= "     END AS DT_KOTEI_ED_H,";	
			$sql .= "   CASE";		
			$sql .= "     WHEN DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO) <> 0 THEN";	
			$sql .= "         ADDTIME(TIME(B.DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO),':00:00'))";
			$sql .= "       ELSE TIME(B.DT_SYUGYO_ST)";		
			$sql .= "     END AS DT_SYUGYO_ST";
			$sql .= " FROM";
			$sql .= "   T_KOTEI_D A";
			$sql .= "     INNER JOIN T_KOTEI_H B ON";
			$sql .= "       A.NINUSI_CD=B.NINUSI_CD AND";
			$sql .= "       A.SOSIKI_CD=B.SOSIKI_CD AND";
			$sql .= "       A.YMD_SYUGYO=B.YMD_SYUGYO AND";
			$sql .= "       A.STAFF_CD=B.STAFF_CD";
			$sql .= "     INNER JOIN M_CALC_TGT_KOTEI C ON";
			$sql .= "       C.KOTEI_KBN = 1 AND";
            $sql .= "       A.NINUSI_CD = C.NINUSI_CD AND";
            $sql .= "       A.SOSIKI_CD = C.SOSIKI_CD AND";
            $sql .= "       A.BNRI_DAI_CD = C.BNRI_DAI_CD AND";
            $sql .= "       A.BNRI_CYU_CD = C.BNRI_CYU_CD AND";
            $sql .= "       A.BNRI_SAI_CD = C.BNRI_SAI_CD";
			$sql .= " WHERE";
			$sql .= "   A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   A.DT_KOTEI_ST>='" . $date1 . "' AND";
			$sql .= "   A.DT_KOTEI_ED<='" .  $date2 . "'";
			$sql .= "   AND (";
			
			$firstFlg = 0;
			
			foreach($STAFF_CDArray as $STAFF_CD_H) {
			
				if ($firstFlg == 1) {
					$sql .= "   OR";
				}
				
				$sql .= "   A.STAFF_CD='" . $STAFF_CD_H . "'";
				
				$firstFlg = 1;
			}
			
			$sql .= "   )";

			
			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業実績明細情報・時間範囲内　取得");
			
			$arrayList1 = array();
			
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
	
				$array = array(
				    "NINUSI_CD" => $result['NINUSI_CD'],
					"SOSIKI_CD" => $result['SOSIKI_CD'],
					"YMD_SYUGYO" => $result['YMD_SYUGYO'],
					"STAFF_CD" => $result['STAFF_CD'],
					"BNRI_DAI_CD" => $result['BNRI_DAI_CD'],
					"BNRI_CYU_CD" => $result['BNRI_CYU_CD'],
					"BNRI_SAI_CD" => $result['BNRI_SAI_CD'],
					"DT_KOTEI_ST_H" => $result['DT_KOTEI_ST_H'],
					"DT_KOTEI_ED_H" => $result['DT_KOTEI_ED_H'],
					"DT_SYUGYO_ST" => $result['DT_SYUGYO_ST']
				);
				
				$arrayList1[] = $array;
			}
			
			//1_2. 開始時間またがり
			$sql  = "SELECT ";
			$sql .= "   A.NINUSI_CD,";
			$sql .= "   A.SOSIKI_CD,";
			$sql .= "   A.YMD_SYUGYO,";
			$sql .= "   A.STAFF_CD,";
			$sql .= "   A.BNRI_DAI_CD,";
			$sql .= "   A.BNRI_CYU_CD,";
			$sql .= "   A.BNRI_SAI_CD,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE('" . $date1 ."'), A.YMD_SYUGYO) <> 0 THEN";		
			$sql .= "         ADDTIME(TIME('" . $date1 ."'), concat(24 * DATEDIFF(DATE('" . $date1 ."'), A.YMD_SYUGYO),':00:00'))";	
			$sql .= "       ELSE";	
			$sql .= "         TIME('" . $date1 ."')";
			$sql .= "     END AS DT_KOTEI_ST_H,";
			$sql .= "   CASE";		
			$sql .= "     WHEN DATEDIFF(DATE(A.DT_KOTEI_ED), A.YMD_SYUGYO) <> 0 THEN";	
			$sql .= "         ADDTIME(TIME(A.DT_KOTEI_ED), concat(24 * DATEDIFF(DATE(A.DT_KOTEI_ED), A.YMD_SYUGYO),':00:00'))";
			$sql .= "       ELSE TIME(A.DT_KOTEI_ED)";		
			$sql .= "     END AS DT_KOTEI_ED_H,";	
			$sql .= "   CASE";		
			$sql .= "     WHEN DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO) <> 0 THEN";	
			$sql .= "         ADDTIME(TIME(B.DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO),':00:00'))";
			$sql .= "       ELSE TIME(B.DT_SYUGYO_ST)";		
			$sql .= "     END AS DT_SYUGYO_ST";
			$sql .= " FROM";
			$sql .= "   T_KOTEI_D A";
			$sql .= "     INNER JOIN T_KOTEI_H B ON";
			$sql .= "       A.NINUSI_CD=B.NINUSI_CD AND";
			$sql .= "       A.SOSIKI_CD=B.SOSIKI_CD AND";
			$sql .= "       A.YMD_SYUGYO=B.YMD_SYUGYO AND";
			$sql .= "       A.STAFF_CD=B.STAFF_CD";
			$sql .= "     INNER JOIN M_CALC_TGT_KOTEI C ON";
			$sql .= "       C.KOTEI_KBN = 1 AND";
            $sql .= "       A.NINUSI_CD = C.NINUSI_CD AND";
            $sql .= "       A.SOSIKI_CD = C.SOSIKI_CD AND";
            $sql .= "       A.BNRI_DAI_CD = C.BNRI_DAI_CD AND";
            $sql .= "       A.BNRI_CYU_CD = C.BNRI_CYU_CD AND";
            $sql .= "       A.BNRI_SAI_CD = C.BNRI_SAI_CD";
			$sql .= " WHERE";
			$sql .= "   A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   A.DT_KOTEI_ST<'" . $date1 . "' AND";
			$sql .= "   (";
			$sql .= "     A.DT_KOTEI_ED>='" . $date1 . "' AND";
			$sql .= "     A.DT_KOTEI_ED<='" .  $date2 . "'";
			$sql .= "   )";
			$sql .= "   AND (";
			
			$firstFlg = 0;
			
			foreach($STAFF_CDArray as $STAFF_CD_H) {
			
				if ($firstFlg == 1) {
					$sql .= "   OR";
				}
				
				$sql .= "   A.STAFF_CD='" . $STAFF_CD_H . "'";
				
				$firstFlg = 1;
			}
			
			$sql .= "   )";

			
			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業実績明細情報・開始時間またがり　取得");
			
			$arrayList2 = array();
			
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
	
				$array = array(
				    "NINUSI_CD" => $result['NINUSI_CD'],
					"SOSIKI_CD" => $result['SOSIKI_CD'],
					"YMD_SYUGYO" => $result['YMD_SYUGYO'],
					"STAFF_CD" => $result['STAFF_CD'],
					"BNRI_DAI_CD" => $result['BNRI_DAI_CD'],
					"BNRI_CYU_CD" => $result['BNRI_CYU_CD'],
					"BNRI_SAI_CD" => $result['BNRI_SAI_CD'],
					"DT_KOTEI_ST_H" => $result['DT_KOTEI_ST_H'],
					"DT_KOTEI_ED_H" => $result['DT_KOTEI_ED_H'],
					"DT_SYUGYO_ST" => $result['DT_SYUGYO_ST']
				);
				
				$arrayList2[] = $array;
			}
			
			//1_3. 終了時間またがり
			$sql  = "SELECT ";
			$sql .= "   A.NINUSI_CD,";
			$sql .= "   A.SOSIKI_CD,";
			$sql .= "   A.YMD_SYUGYO,";
			$sql .= "   A.STAFF_CD,";
			$sql .= "   A.BNRI_DAI_CD,";
			$sql .= "   A.BNRI_CYU_CD,";
			$sql .= "   A.BNRI_SAI_CD,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(A.DT_KOTEI_ST), A.YMD_SYUGYO) <> 0 THEN";		
			$sql .= "         ADDTIME(TIME(A.DT_KOTEI_ST), concat(24 * DATEDIFF(DATE(A.DT_KOTEI_ST), A.YMD_SYUGYO),':00:00'))";	
			$sql .= "       ELSE";	
			$sql .= "         TIME(A.DT_KOTEI_ST)";
			$sql .= "     END AS DT_KOTEI_ST_H,";
			$sql .= "   CASE";		
			$sql .= "     WHEN DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO) <> 0 THEN";	
			$sql .= "         ADDTIME(TIME('" . $date2 . "'), concat(24 * DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO),':00:00'))";
			$sql .= "       ELSE TIME('" . $date2 . "')";		
			$sql .= "     END AS DT_KOTEI_ED_H,";
			$sql .= "   CASE";		
			$sql .= "     WHEN DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO) <> 0 THEN";	
			$sql .= "         ADDTIME(TIME(B.DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO),':00:00'))";
			$sql .= "       ELSE TIME(B.DT_SYUGYO_ST)";		
			$sql .= "     END AS DT_SYUGYO_ST";
			$sql .= " FROM";
			$sql .= "   T_KOTEI_D A";
			$sql .= "     INNER JOIN T_KOTEI_H B ON";
			$sql .= "       A.NINUSI_CD=B.NINUSI_CD AND";
			$sql .= "       A.SOSIKI_CD=B.SOSIKI_CD AND";
			$sql .= "       A.YMD_SYUGYO=B.YMD_SYUGYO AND";
			$sql .= "       A.STAFF_CD=B.STAFF_CD";
			$sql .= "     INNER JOIN M_CALC_TGT_KOTEI C ON";
			$sql .= "       C.KOTEI_KBN = 1 AND";
            $sql .= "       A.NINUSI_CD = C.NINUSI_CD AND";
            $sql .= "       A.SOSIKI_CD = C.SOSIKI_CD AND";
            $sql .= "       A.BNRI_DAI_CD = C.BNRI_DAI_CD AND";
            $sql .= "       A.BNRI_CYU_CD = C.BNRI_CYU_CD AND";
            $sql .= "       A.BNRI_SAI_CD = C.BNRI_SAI_CD";
			$sql .= " WHERE";
			$sql .= "   A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   A.DT_KOTEI_ED>'" . $date2 . "' AND";
			$sql .= "   (";
			$sql .= "     A.DT_KOTEI_ST>='" . $date1 . "' AND";
			$sql .= "     A.DT_KOTEI_ST<='" .  $date2 . "'";
			$sql .= "   )";
			$sql .= "   AND (";
			
			$firstFlg = 0;
			
			foreach($STAFF_CDArray as $STAFF_CD_H) {
			
				if ($firstFlg == 1) {
					$sql .= "   OR";
				}
				
				$sql .= "   A.STAFF_CD='" . $STAFF_CD_H . "'";
				
				$firstFlg = 1;
			}
			
			$sql .= "   )";

			
			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業実績明細情報・終了時間またがり　取得");
			
			$arrayList3 = array();
			
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
	
				$array = array(
				    "NINUSI_CD" => $result['NINUSI_CD'],
					"SOSIKI_CD" => $result['SOSIKI_CD'],
					"YMD_SYUGYO" => $result['YMD_SYUGYO'],
					"STAFF_CD" => $result['STAFF_CD'],
					"BNRI_DAI_CD" => $result['BNRI_DAI_CD'],
					"BNRI_CYU_CD" => $result['BNRI_CYU_CD'],
					"BNRI_SAI_CD" => $result['BNRI_SAI_CD'],
					"DT_KOTEI_ST_H" => $result['DT_KOTEI_ST_H'],
					"DT_KOTEI_ED_H" => $result['DT_KOTEI_ED_H'],
					"DT_SYUGYO_ST" => $result['DT_SYUGYO_ST']
				);
				
				$arrayList3[] = $array;
			}
			
			//1_3_2 終了またがりと判断されるが、終了がNULLの場合
			$sql  = "SELECT ";
			$sql .= "   A.NINUSI_CD,";
			$sql .= "   A.SOSIKI_CD,";
			$sql .= "   A.YMD_SYUGYO,";
			$sql .= "   A.STAFF_CD,";
			$sql .= "   A.BNRI_DAI_CD,";
			$sql .= "   A.BNRI_CYU_CD,";
			$sql .= "   A.BNRI_SAI_CD,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(A.DT_KOTEI_ST), A.YMD_SYUGYO) <> 0 THEN";		
			$sql .= "         ADDTIME(TIME(A.DT_KOTEI_ST), concat(24 * DATEDIFF(DATE(A.DT_KOTEI_ST), A.YMD_SYUGYO),':00:00'))";	
			$sql .= "       ELSE";	
			$sql .= "         TIME(A.DT_KOTEI_ST)";
			$sql .= "     END AS DT_KOTEI_ST_H,";
			
			if ($max_kado_time == null) {
			
				$sql .= "   CASE";		
				$sql .= "     WHEN DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO) < DATEDIFF(DATE(NOW()), A.YMD_SYUGYO) THEN";	
				$sql .= "         ADDTIME(TIME('" . $date2 . "'), concat(24 * DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO),':00:00'))";
				$sql .= "       ELSE TIME(NOW())";		
				$sql .= "     END AS DT_KOTEI_ED_H,";
			
			} else {
				
				$sql .= "   CASE";		
				$sql .= "     WHEN DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO) < DATEDIFF(DATE('" . $max_kado_time . "'), A.YMD_SYUGYO) THEN";	
				$sql .= "         ADDTIME(TIME('" . $date2 . "'), concat(24 * DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO),':00:00'))";
				$sql .= "       ELSE TIME('" . $max_kado_time . "')";		
				$sql .= "     END AS DT_KOTEI_ED_H,";
				
			}
			$sql .= "   CASE";		
			$sql .= "     WHEN DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO) <> 0 THEN";	
			$sql .= "         ADDTIME(TIME(B.DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO),':00:00'))";
			$sql .= "       ELSE TIME(B.DT_SYUGYO_ST)";		
			$sql .= "     END AS DT_SYUGYO_ST";
			$sql .= " FROM";
			$sql .= "   T_KOTEI_D A";
			$sql .= "     INNER JOIN T_KOTEI_H B ON";
			$sql .= "       A.NINUSI_CD=B.NINUSI_CD AND";
			$sql .= "       A.SOSIKI_CD=B.SOSIKI_CD AND";
			$sql .= "       A.YMD_SYUGYO=B.YMD_SYUGYO AND";
			$sql .= "       A.STAFF_CD=B.STAFF_CD";
			$sql .= " WHERE";
			$sql .= "   A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   A.DT_KOTEI_ED IS NULL AND";
			$sql .= "   (";
			$sql .= "     A.DT_KOTEI_ST>='" . $date1 . "' AND";
			$sql .= "     A.DT_KOTEI_ST<='" .  $date2 . "'";
			$sql .= "   )";
			$sql .= "   AND (";
			
			$firstFlg = 0;
			
			foreach($STAFF_CDArray as $STAFF_CD_H) {
			
				if ($firstFlg == 1) {
					$sql .= "   OR";
				}
				
				$sql .= "   A.STAFF_CD='" . $STAFF_CD_H . "'";
				
				$firstFlg = 1;
			}
			
			$sql .= "   )";

			
			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業実績明細情報・終了時間またがりNULL　取得");
			
			$arrayList4 = array();
			
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
	
				$array = array(
				    "NINUSI_CD" => $result['NINUSI_CD'],
					"SOSIKI_CD" => $result['SOSIKI_CD'],
					"YMD_SYUGYO" => $result['YMD_SYUGYO'],
					"STAFF_CD" => $result['STAFF_CD'],
					"BNRI_DAI_CD" => $result['BNRI_DAI_CD'],
					"BNRI_CYU_CD" => $result['BNRI_CYU_CD'],
					"BNRI_SAI_CD" => $result['BNRI_SAI_CD'],
					"DT_KOTEI_ST_H" => $result['DT_KOTEI_ST_H'],
					"DT_KOTEI_ED_H" => $result['DT_KOTEI_ED_H'],
					"DT_SYUGYO_ST" => $result['DT_SYUGYO_ST']
				);
				
				$arrayList4[] = $array;
			}
			
			//1_4. 日またがり
			$sql  = "SELECT ";
			$sql .= "   A.NINUSI_CD,";
			$sql .= "   A.SOSIKI_CD,";
			$sql .= "   A.YMD_SYUGYO,";
			$sql .= "   A.STAFF_CD,";
			$sql .= "   A.BNRI_DAI_CD,";
			$sql .= "   A.BNRI_CYU_CD,";
			$sql .= "   A.BNRI_SAI_CD,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE('" . $date1 ."'), A.YMD_SYUGYO) <> 0 THEN";		
			$sql .= "         ADDTIME(TIME('" . $date1 ."'), concat(24 * DATEDIFF(DATE('" . $date1 ."'), A.YMD_SYUGYO),':00:00'))";	
			$sql .= "       ELSE";	
			$sql .= "         TIME('" . $date1 ."')";
			$sql .= "     END AS DT_KOTEI_ST_H,";
			$sql .= "   CASE";		
			$sql .= "     WHEN DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO) <> 0 THEN";	
			$sql .= "         ADDTIME(TIME('" . $date2 . "'), concat(24 * DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO),':00:00'))";
			$sql .= "       ELSE TIME('" . $date2 . "')";		
			$sql .= "     END AS DT_KOTEI_ED_H,";
			$sql .= "   CASE";		
			$sql .= "     WHEN DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO) <> 0 THEN";	
			$sql .= "         ADDTIME(TIME(B.DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO),':00:00'))";
			$sql .= "       ELSE TIME(B.DT_SYUGYO_ST)";		
			$sql .= "     END AS DT_SYUGYO_ST";
			$sql .= " FROM";
			$sql .= "   T_KOTEI_D A";
			$sql .= "     INNER JOIN T_KOTEI_H B ON";
			$sql .= "       A.NINUSI_CD=B.NINUSI_CD AND";
			$sql .= "       A.SOSIKI_CD=B.SOSIKI_CD AND";
			$sql .= "       A.YMD_SYUGYO=B.YMD_SYUGYO AND";
			$sql .= "       A.STAFF_CD=B.STAFF_CD";
			$sql .= "     INNER JOIN M_CALC_TGT_KOTEI C ON";
			$sql .= "       C.KOTEI_KBN = 1 AND";
            $sql .= "       A.NINUSI_CD = C.NINUSI_CD AND";
            $sql .= "       A.SOSIKI_CD = C.SOSIKI_CD AND";
            $sql .= "       A.BNRI_DAI_CD = C.BNRI_DAI_CD AND";
            $sql .= "       A.BNRI_CYU_CD = C.BNRI_CYU_CD AND";
            $sql .= "       A.BNRI_SAI_CD = C.BNRI_SAI_CD";
			$sql .= " WHERE";
			$sql .= "   A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   A.DT_KOTEI_ST<'" . $date1 . "' AND";
			$sql .= "   A.DT_KOTEI_ED>'" . $date2 . "'";

			$sql .= "   AND (";
			
			$firstFlg = 0;
			
			foreach($STAFF_CDArray as $STAFF_CD_H) {
			
				if ($firstFlg == 1) {
					$sql .= "   OR";
				}
				
				$sql .= "   A.STAFF_CD='" . $STAFF_CD_H . "'";
				
				$firstFlg = 1;
			}
			
			$sql .= "   )";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業実績明細情報・日またがり　取得");
			
			$arrayList5 = array();
			
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
	
				$array = array(
				    "NINUSI_CD" => $result['NINUSI_CD'],
					"SOSIKI_CD" => $result['SOSIKI_CD'],
					"YMD_SYUGYO" => $result['YMD_SYUGYO'],
					"STAFF_CD" => $result['STAFF_CD'],
					"BNRI_DAI_CD" => $result['BNRI_DAI_CD'],
					"BNRI_CYU_CD" => $result['BNRI_CYU_CD'],
					"BNRI_SAI_CD" => $result['BNRI_SAI_CD'],
					"DT_KOTEI_ST_H" => $result['DT_KOTEI_ST_H'],
					"DT_KOTEI_ED_H" => $result['DT_KOTEI_ED_H'],
					"DT_SYUGYO_ST" => $result['DT_SYUGYO_ST']
				);
				
				$arrayList5[] = $array;
			}
			
			//1_4_2. 日またがり・終了がNULLの場合
			$sql  = "SELECT ";
			$sql .= "   A.NINUSI_CD,";
			$sql .= "   A.SOSIKI_CD,";
			$sql .= "   A.YMD_SYUGYO,";
			$sql .= "   A.STAFF_CD,";
			$sql .= "   A.BNRI_DAI_CD,";
			$sql .= "   A.BNRI_CYU_CD,";
			$sql .= "   A.BNRI_SAI_CD,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE('" . $date1 ."'), A.YMD_SYUGYO) <> 0 THEN";		
			$sql .= "         ADDTIME(TIME('" . $date1 ."'), concat(24 * DATEDIFF(DATE('" . $date1 ."'), A.YMD_SYUGYO),':00:00'))";	
			$sql .= "       ELSE";	
			$sql .= "         TIME('" . $date1 ."')";
			$sql .= "     END AS DT_KOTEI_ST_H,";
			
			if ($max_kado_time == null) {
			
				$sql .= "   CASE";		
				$sql .= "     WHEN DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO) < DATEDIFF(DATE(NOW()), A.YMD_SYUGYO) THEN";	
				$sql .= "         ADDTIME(TIME('" . $date2 . "'), concat(24 * DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO),':00:00'))";
				$sql .= "       ELSE TIME(NOW())";		
				$sql .= "     END AS DT_KOTEI_ED_H,";
			
			} else {
				
				$sql .= "   CASE";		
				$sql .= "     WHEN DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO) < DATEDIFF(DATE('" . $max_kado_time . "'), A.YMD_SYUGYO) THEN";	
				$sql .= "         ADDTIME(TIME('" . $date2 . "'), concat(24 * DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO),':00:00'))";
				$sql .= "       ELSE TIME('" . $max_kado_time . "')";		
				$sql .= "     END AS DT_KOTEI_ED_H,";
				
			}
			
			$sql .= "   CASE";		
			$sql .= "     WHEN DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO) <> 0 THEN";	
			$sql .= "         ADDTIME(TIME(B.DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO),':00:00'))";
			$sql .= "       ELSE TIME(B.DT_SYUGYO_ST)";		
			$sql .= "     END AS DT_SYUGYO_ST";
			$sql .= " FROM";
			$sql .= "   T_KOTEI_D A";
			$sql .= "     INNER JOIN T_KOTEI_H B ON";
			$sql .= "       A.NINUSI_CD=B.NINUSI_CD AND";
			$sql .= "       A.SOSIKI_CD=B.SOSIKI_CD AND";
			$sql .= "       A.YMD_SYUGYO=B.YMD_SYUGYO AND";
			$sql .= "       A.STAFF_CD=B.STAFF_CD";
			$sql .= "     INNER JOIN M_CALC_TGT_KOTEI C ON";
			$sql .= "       C.KOTEI_KBN = 1 AND";
            $sql .= "       A.NINUSI_CD = C.NINUSI_CD AND";
            $sql .= "       A.SOSIKI_CD = C.SOSIKI_CD AND";
            $sql .= "       A.BNRI_DAI_CD = C.BNRI_DAI_CD AND";
            $sql .= "       A.BNRI_CYU_CD = C.BNRI_CYU_CD AND";
            $sql .= "       A.BNRI_SAI_CD = C.BNRI_SAI_CD";
			$sql .= " WHERE";
			$sql .= "   A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   A.DT_KOTEI_ST<'" . $date1 . "' AND";
			$sql .= "   A.DT_KOTEI_ED IS NULL";
			$sql .= "   AND (";
			
			$firstFlg = 0;
			
			foreach($STAFF_CDArray as $STAFF_CD_H) {
			
				if ($firstFlg == 1) {
					$sql .= "   OR";
				}
				
				$sql .= "   A.STAFF_CD='" . $STAFF_CD_H . "'";
				
				$firstFlg = 1;
			}
			
			$sql .= "   )";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業実績明細情報・日またがりNULL　取得");
			
			$arrayList6 = array();
			

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
	
				$array = array(
				    "NINUSI_CD" => $result['NINUSI_CD'],
					"SOSIKI_CD" => $result['SOSIKI_CD'],
					"YMD_SYUGYO" => $result['YMD_SYUGYO'],
					"STAFF_CD" => $result['STAFF_CD'],
					"BNRI_DAI_CD" => $result['BNRI_DAI_CD'],
					"BNRI_CYU_CD" => $result['BNRI_CYU_CD'],
					"BNRI_SAI_CD" => $result['BNRI_SAI_CD'],
					"DT_KOTEI_ST_H" => $result['DT_KOTEI_ST_H'],
					"DT_KOTEI_ED_H" => $result['DT_KOTEI_ED_H'],
					"DT_SYUGYO_ST" => $result['DT_SYUGYO_ST']
				);
				
				$arrayList6[] = $array;
			}
			
			$jissekiMArray = array_merge($arrayList1,$arrayList2,$arrayList3,$arrayList4,$arrayList5,$arrayList6);
			$NINUSI_CD = array();
			$SOSIKI_CD =  array();
			$YMD_SYUGYO =  array();
			$STAFF_CD =  array();
			$DT_SYUGYO_ST =  array();
			$DT_KOTEI_ST_H =  array();
			
			foreach ($jissekiMArray as $key => $row) {
			    $NINUSI_CD[$key]  = $row['NINUSI_CD'];
			    $SOSIKI_CD[$key]  = $row['SOSIKI_CD'];
			    $YMD_SYUGYO[$key]  = $row['YMD_SYUGYO'];
			    $STAFF_CD[$key]  = $row['STAFF_CD'];
			    $DT_SYUGYO_ST[$key]  = $row['DT_SYUGYO_ST'];
			    $DT_KOTEI_ST_H[$key]  = $row['DT_KOTEI_ST_H'];
			}
	
			array_multisort($NINUSI_CD,SORT_ASC,
							$SOSIKI_CD,SORT_ASC,
							$YMD_SYUGYO,SORT_ASC,
							$STAFF_CD,SORT_ASC,
							$DT_SYUGYO_ST,SORT_ASC,
							$DT_KOTEI_ST_H,SORT_ASC,
							$jissekiMArray);
			
	
			//稼働時間の算出
			$seisansei = 0;
			$buturyo = 0;
			$KadoTime = 0;
			$kado_staff = count($STAFF_CDArray);
			
			foreach($STAFF_CDArray as $STAFF_CD_H) {
				
				$tmp_KadoTime = 0;
				
				//稼働時間の算出
				foreach($jissekiMArray as $jissekiM) {
				
					if ($jissekiM["STAFF_CD"] == $STAFF_CD_H) {
						
						$BASE_YMD_SYUGYO  = $jissekiM['YMD_SYUGYO'];
						
						//工程就業時間算出
						$dt1 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ST_H']),0,17) . "00");
						$dt2 = new DateTime(substr($this->rechangeDateTime($BASE_YMD_SYUGYO,$jissekiM['DT_KOTEI_ED_H']),0,17) . "00");
		
						$interval = $dt1->diff($dt2);
						$day = $interval->format('%d');
						$hour = $interval->format('%h');
						$minutes = $interval->format('%i');
						
						$tmp_tmp_KadoTime = $hour * 60 + $day * 24 * 60 + $minutes;
						
						if ($tmp_tmp_KadoTime > 0) {
							
							$tmp_KadoTime += $tmp_tmp_KadoTime;
						}
						
					}
						
				}
	
				if ($tmp_KadoTime == 0) {
					continue;
				}
	
				//物量の算出
				$sql  = "SELECT";
				$sql .= "    SUM(A.SURYO_PIECE) AS PIECE_NUM,";
				$sql .= "    SUM(A.SURYO_ITEM) AS SURYO_ITEM";
				$sql .= "  FROM";
				$sql .= "    T_PICK_S_KANRI A";
				$sql .= "  WHERE";
				$sql .= "    A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
				$sql .= "    A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
				$sql .= "    A.DT_SYORI_SUMI >='" . $date1 . "' AND";
				$sql .= "    A.DT_SYORI_SUMI <='" . $date2 . "' AND ";
				$sql .= "    A.STAFF_CD ='" . $STAFF_CD_H . "' ";

	
				$this->queryWithPDOLog($stmt,$pdo,$sql, '物量　算出');
				
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$tmp_buturyo = 0;
				
				if ($mode == 1) {
					
					$tmp_buturyo = $result["SURYO_ITEM"];
					
				} else if ($mode == 0) {
					
					$tmp_buturyo = $result["PIECE_NUM"];
				}
				
				$seisansei += $tmp_buturyo / $tmp_KadoTime * 60;
				$buturyo += $tmp_buturyo;
				$KadoTime += $tmp_KadoTime;
			
			}
			
			//稼働スタッフ
			$pdo = null;
			
			$array = array(
				
				"KADO_STAFF" => $kado_staff,
				"SEISANSEI" => $seisansei,
				"BUTURYO" => $buturyo,
				"KADO_TIME" => $KadoTime,
				"YMD" => $ymd_syori,
				"YMD_FROM" => $date1,
				"YMD_TO" => $date2
			);

			return $array;


		} catch (Exception $e) {

			$pdo = null;
			throw $e;
		}

		return 0;
	}
}