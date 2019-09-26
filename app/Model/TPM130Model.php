<?php
/**
 * TPM130Model
 *
 * 作業進捗画面
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class TPM130Model extends DCMSAppModel{
	const EMPTY_VALUE = '-';
	
	public $name = 'TPM130Model';
	public $useTable = false;

	public $errors = array();

	/**
	 * コンストラクタ
	 * @access   public
	 * @param 機能ID
	 * @param モデルのID.
	 * @param モデルのテーブル名.
	 * @param モデルのデータソース.
	 */
    function __construct($ninusi_cd = '0000000000', $sosiki_cd = '0000000000', $kino_id = '', $id = false, $table = null, $ds = null) {
       parent::__construct($ninusi_cd, $sosiki_cd,$kino_id, $id, $table, $ds);

		//メッセージマスタ呼び出し
		$this->MMessage = new MMessage($ninusi_cd,$sosiki_cd,$kino_id);

		//共通関数モデル
		$this->MCommon = new MCommon($ninusi_cd,$sosiki_cd,$kino_id);

	}

	/**
	 * 進捗データ取得
	 * @access   public
	 * @return   進捗データ
	 */
	public function getProgressList($ymd_from, $ymd_to) {

		//DBオブジェクト取得
		$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// 当該日付の方面名の一覧を取得
		$result = $this->getHomenList($pdo, $ymd_from, $ymd_to);
		
		// トータルピックの進捗率を取得
		$result = $this->getTotalPKProgress($pdo, $ymd_from, $ymd_to, $result);
		
		// 種蒔きの進捗率を取得
		$result = $this->getTanemakiProgress($pdo, $ymd_from, $ymd_to, $result);
		
		// 積込検品の進捗率を取得
		$result = $this->getKenpinProgress($pdo, $ymd_from, $ymd_to, $result);
		
		// すべての処理が完了している場合はクラス名をセット
		foreach ( $result as &$ar ) {
			if ( $ar["TOTAL_PK"] == 100 && ($ar["TANEMAKI"] == 100 || $ar["TANEMAKI"] === self::EMPTY_VALUE) && $ar["KENPIN"]  == 100 ) {
				$ar["CLASS_ALL_FINISH"] = "progFinish";
			}
		}
		
		return $result;
	}

	/**
	 * CSVデータ取得
	 * @access   public
	 * @return   CSVデータ
	 */
	public function getCSV($ymd_from, $ymd_to) {
		$text = array();
		
		//ヘッダー情報
		$header = "方面名,トータルピック(%),種蒔検品(%),積込検品(%)";

		$text[] = $header;
		
		$result = $this->getProgressList($ymd_from, $ymd_to);
		
		foreach ( $result as &$ar ) {
			$tmp = $ar["HOMEN_MEI"] . ",";
			$tmp .= $ar["TOTAL_PK"] . ",";
			$tmp .= $ar["TANEMAKI"] . ",";
			$tmp .= $ar["KENPIN"];
			
			$text[] = $tmp;
		}
		
		return $text;
	}

	/**
	 * 方面一覧データ取得
	 * @access   private
	 * @return   方面一覧データ
	*/
	private function getHomenList($pdo, $ymd_from, $ymd_to) {
		
		// 出荷テーブルから方面名の一覧を取得する
		$sql  = "SELECT DISTINCT HOMEN_MEI";
		$sql .= " FROM T_OZAX_SHUKKA ";
		$sql .= " WHERE ";
		$sql .= " NINUSI_CD='" . $this->_ninusi_cd . "' AND";
		$sql .= " SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
//		$sql .= " SHUKKA_BI  = '" . $this->MCommon->strToDateString($ymd) . "'";
		$sql .= " (SHUKKA_BI  >= '" . $this->MCommon->strToDateString($ymd_from) . "' AND";
		$sql .= " SHUKKA_BI  <= '" . $this->MCommon->strToDateString($ymd_to) . "')";
		$sql .= " ORDER BY";
		$sql .= " HOMEN_MEI";

		$this->queryWithPDOLog($stmt,$pdo,$sql, "作業進捗（方面）データ 取得");
		
		$arrayList = array();
		
		while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
		
			$array = array(
				"HOMEN_MEI" => $result['HOMEN_MEI'],
				"TOTAL_PK" => 0,
				"CLASS_TOTAL_PK" => '',
				"CLASS_TOTAL_PK_FINISH" => '',
				"TANEMAKI" => self::EMPTY_VALUE,
				"CLASS_TANEMAKI" => '',
				"CLASS_TANEMAKI_FINISH" => $this->getFinishCssCalssName(self::EMPTY_VALUE),
				"KENPIN" => 0,
				"CLASS_KENPIN" => '',
				"CLASS_KENPIN_FINISH" => '',
				"CLASS_ALL_FINISH" => '',
			);

			$arrayList[] = $array;
		}
		
		return $arrayList;
	}

	/**
	 * トータルピックデータ取得
	 * @access   private
	 * @return   トータルピックデータ
	*/
	private function getTotalPKProgress($pdo, $ymd_from, $ymd_to, $arrayList) {
		
		$sql  = "SELECT HOMEN_MEI, SUM(SO_BARA_SU) SUM1,  SUM(SO_BARA_SUMISU) SUM2";
		$sql .= " FROM T_OZAX_TOTAL_PK ";
		$sql .= " WHERE ";
		$sql .= " NINUSI_CD='" . $this->_ninusi_cd . "' AND";
		$sql .= " SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
//		$sql .= " SHUKKA_BI  = '" . $this->MCommon->strToDateString($ymd) . "'";
		$sql .= " (SHUKKA_BI  >= '" . $this->MCommon->strToDateString($ymd_from) . "' AND";
		$sql .= " SHUKKA_BI  <= '" . $this->MCommon->strToDateString($ymd_to) . "')";
		$sql .= " GROUP BY";
		$sql .= " HOMEN_MEI";

		$this->queryWithPDOLog($stmt,$pdo,$sql, "作業進捗（トータルピック）データ 取得");
		
		while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			
//	   		$this->printLog('info', "", $result['HOMEN_MEI'] ,$result['SUM2'] . " : " . $result['SUM1']);
			// 作業進捗率を算出
			$progress = $this->calcProgress($result['SUM1'], $result['SUM2']);

			foreach ( $arrayList as &$ar ) {
				if ( $ar["HOMEN_MEI"] == $result['HOMEN_MEI'] ) {
					$ar["TOTAL_PK"] = $progress;
					$ar["CLASS_TOTAL_PK"] = $this->getCssCalssName($progress);
					$ar["CLASS_TOTAL_PK_FINISH"] = $this->getFinishCssCalssName($progress);
					break;
				}
			}
		}
		
		return $arrayList;
	}

	/**
	 * 種蒔きデータ取得
	 * @access   private
	 * @return   種蒔きデータ
	*/
	private function getTanemakiProgress($pdo, $ymd_from, $ymd_to, $arrayList) {
		
		$sql  = "SELECT HOMEN_MEI, SUM(SO_BARA_SU) SUM1,  SUM(SO_BARA_SUMISU) SUM2";
		$sql .= " FROM T_OZAX_TANEMAKI ";
		$sql .= " WHERE ";
		$sql .= " NINUSI_CD='" . $this->_ninusi_cd . "' AND";
		$sql .= " SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
		$sql .= " (SHUKKA_BI  >= '" . $this->MCommon->strToDateString($ymd_from) . "' AND";
		$sql .= " SHUKKA_BI  <= '" . $this->MCommon->strToDateString($ymd_to) . "')";
//		$sql .= " SHUKKA_BI  = '" . $this->MCommon->strToDateString($ymd) . "'";
		$sql .= " GROUP BY";
		$sql .= " HOMEN_MEI";

		$this->queryWithPDOLog($stmt,$pdo,$sql, "作業進捗（種蒔）データ 取得");
		
		while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			
//	   		$this->printLog('info', "", $result['HOMEN_MEI'] ,$result['SUM2'] . " : " . $result['SUM1']);
			// 作業進捗率を算出
			$progress = $this->calcProgress($result['SUM1'], $result['SUM2']);
		
			foreach ( $arrayList as &$ar ) {
				if ( $ar["HOMEN_MEI"] == $result['HOMEN_MEI'] ) {
					$ar["TANEMAKI"] = $progress;
					$ar["CLASS_TANEMAKI"] = $this->getCssCalssName($progress);
					$ar["CLASS_TANEMAKI_FINISH"] = $this->getFinishCssCalssName($progress);
					break;
				}
			}
		}
		
		return $arrayList;
	}

	/**
	 * 出荷検品データ取得
	 * @access   private
	 * @return   出荷検品データ
	*/
	private function getKenpinProgress($pdo, $ymd_from, $ymd_to, $arrayList) {
		
		$sql  = "SELECT SIWAKE.HOMEN_MEI, SUM(SIWAKE.KONPO_SU) SUM1,  SUM(SIWAKE.SUMI_KONPO_SU) SUM2";
		$sql .= " FROM T_OZAX_TENPO_SIWAKE SIWAKE, T_OZAX_SHUKKA SHUKKA";
		$sql .= " WHERE ";
		$sql .= " SIWAKE.NINUSI_CD=SHUKKA.NINUSI_CD AND";
		$sql .= " SIWAKE.SOSIKI_CD=SHUKKA.SOSIKI_CD AND";
		$sql .= " SIWAKE.HOMEN_MEI=SHUKKA.HOMEN_MEI AND";
		$sql .= " SIWAKE.NOHIN_BI=SHUKKA.NOHIN_BI AND";
		$sql .= " SIWAKE.TOKUISAKI_CODE=SHUKKA.TOKUISAKI_CODE AND";
		$sql .= " SIWAKE.TENPO_CODE=SHUKKA.TENPO_CODE AND";
		$sql .= " SIWAKE.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
		$sql .= " SIWAKE.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
		$sql .= " (SHUKKA.SHUKKA_BI  >= '" . $this->MCommon->strToDateString($ymd_from) . "' AND";
		$sql .= " SHUKKA.SHUKKA_BI  <= '" . $this->MCommon->strToDateString($ymd_to) . "')";
		$sql .= " GROUP BY";
		$sql .= " HOMEN_MEI";

		$this->queryWithPDOLog($stmt,$pdo,$sql, "作業進捗（検品）データ 取得");
		
		while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			
//	   		$this->printLog('info', "", $result['HOMEN_MEI'] ,$result['SUM2'] . " : " . $result['SUM1']);
			// 作業進捗率を算出
			$progress = $this->calcProgress($result['SUM1'], $result['SUM2']);
		
			foreach ( $arrayList as &$ar ) {
				if ( $ar["HOMEN_MEI"] == $result['HOMEN_MEI'] ) {
					$ar["KENPIN"] = $progress;
					$ar["CLASS_KENPIN"] = $this->getCssCalssName($progress);
					$ar["CLASS_KENPIN_FINISH"] = $this->getFinishCssCalssName($progress);
					break;
				}
			}
		}
		
		return $arrayList;
	}
	
	/**
	 * 進捗率を算出する
	 * @access   private
	 * @return   進捗率
	*/
	private function calcProgress($val1, $val2) {
		// 小数点第１位以下切り捨て
		if ( $val1 == 0 || $val1 === null || $val2 === null ) {
			$progress = 0;
		} else {
			$progress = floor($val2 / $val1 * 1000) / 10;
			// 少しでも作業を開始していれば0.1にする
			if ( $progress == 0 && $val2 != 0 ) {
				$progress = 0.1;
			}
		}
		return $progress;
	}
	
	/**
	 * 作業終了時のCSSクラス名を取得
	 * @access   private
	 * @return   作業完了の場合はクラス名を返す
	*/
	private function getFinishCssCalssName($value) {
		if ( $value >= 100 || $value === self::EMPTY_VALUE ) {
			return "progFinish";
		} else {
			return "";
		}	
	}

	/**
	 * 作業進捗に応じたCSSクラス名を取得
	 * @access   private
	 * @return   作業進捗に応じたクラス名を返す
	*/
	private function getCssCalssName($value) {
		if ( $value < 50 ) {
			return "prog30per";
		} elseif ( $value < 70 ) {
			return "prog50per";
		} elseif ( $value < 90 ) {
			return "prog70per";
		} elseif ( $value < 100 ) {
			return "prog90per";
		} else {
			return "prog100per";
		}	
	}

	/**
	 * スタッフリストを取得する
	 * @access   publlic
	*/
	public function getStaffList($ymd_from, $ymd_to, $homen, &$totalPk, &$tanemaki, &$kenpin) {
		
		//DBオブジェクト取得
		$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$totalPk = $this->getTotalPkStaffList($pdo, $ymd_from, $ymd_to, $homen);
		$tanemaki = $this->getTanemakiStaffList($pdo, $ymd_from, $ymd_to, $homen);
//		$kenpin = $this->getKenpinStaffList($pdo, $ymd_from, $ymd_to, $homen);
	}
	
	/**
	 * トータルピックスタッフリストを取得する
	 * @access   private
	 * @return   スタッフリスト
	*/
	private function getTotalPkStaffList($pdo, $ymd_from, $ymd_to, $homen) {
		$sql  = "SELECT TOTAL_PK.KOSIN_STAFF,  STAFF.STAFF_NM";
		$sql .= " FROM T_OZAX_TOTAL_PK TOTAL_PK LEFT JOIN M_STAFF_KIHON STAFF ON (";
		$sql .= " TOTAL_PK.KOSIN_STAFF=STAFF.STAFF_CD)";
		$sql .= " WHERE ";
		$sql .= " NINUSI_CD='" . $this->_ninusi_cd . "' AND";
		$sql .= " SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
		$sql .= " TOTAL_PK.HOMEN_MEI='" . $homen . "' AND";
		$sql .= " TOTAL_PK.TOTAL_PK_KUBUN!='0' AND";
		$sql .= " (SHUKKA_BI  >= '" . $this->MCommon->strToDateString($ymd_from) . "' AND";
		$sql .= " SHUKKA_BI  <= '" . $this->MCommon->strToDateString($ymd_to) . "')";
		$sql .= " GROUP BY";
		$sql .= " KOSIN_STAFF";

		$this->queryWithPDOLog($stmt,$pdo,$sql, "作業進捗（トータルピック）スタッフデータ 取得");
		
		$arrayList = array();

		while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			
			$array = array(
				"STAFF_CD" => $result['KOSIN_STAFF'],
				"STAFF_NM" => $result['STAFF_NM'],
			);

			$arrayList[] = $array;
		}
		
		return $arrayList;
	}

	/**
	 * 種蒔スタッフリストを取得する
	 * @access   private
	 * @return   スタッフリスト
	*/
	private function getTanemakiStaffList($pdo, $ymd_from, $ymd_to, $homen) {
		$sql  = "SELECT TANEMAKI.KOSIN_STAFF,  STAFF.STAFF_NM";
		$sql .= " FROM T_OZAX_TANEMAKI TANEMAKI LEFT JOIN M_STAFF_KIHON STAFF ON (";
		$sql .= " TANEMAKI.KOSIN_STAFF=STAFF.STAFF_CD)";
		$sql .= " WHERE ";
		$sql .= " NINUSI_CD='" . $this->_ninusi_cd . "' AND";
		$sql .= " SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
		$sql .= " TANEMAKI.HOMEN_MEI='" . $homen . "' AND";
		$sql .= " TANEMAKI.TANEMAKI_KUBUN!='0' AND";
		$sql .= " (SHUKKA_BI  >= '" . $this->MCommon->strToDateString($ymd_from) . "' AND";
		$sql .= " SHUKKA_BI  <= '" . $this->MCommon->strToDateString($ymd_to) . "')";
		$sql .= " GROUP BY";
		$sql .= " KOSIN_STAFF";

		$this->queryWithPDOLog($stmt,$pdo,$sql, "作業進捗（種蒔）スタッフデータ 取得");
		
		$arrayList = array();

		while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			
			$array = array(
				"STAFF_CD" => $result['KOSIN_STAFF'],
				"STAFF_NM" => $result['STAFF_NM'],
			);

			$arrayList[] = $array;
		}
		
		return $arrayList;
	}

	/**
	 * 検品スタッフリストを取得する
	 * @access   private
	 * @return   スタッフリスト
	*/
	private function getKenpinStaffList($pdo, $ymd_from, $ymd_to, $homen) {

		$sql  = "SELECT SIWAKE.KOSIN_STAFF,  STAFF.STAFF_NM";
		$sql .= " FROM T_OZAX_TENPO_SIWAKE SIWAKE INNER JOIN T_OZAX_SHUKKA SHUKKA ON (";
		$sql .= " SIWAKE.NINUSI_CD=SHUKKA.NINUSI_CD AND";
		$sql .= " SIWAKE.SOSIKI_CD=SHUKKA.SOSIKI_CD AND";
		$sql .= " SIWAKE.HOMEN_MEI=SHUKKA.HOMEN_MEI AND";
		$sql .= " SIWAKE.NOHIN_BI=SHUKKA.NOHIN_BI AND";
		$sql .= " SIWAKE.TOKUISAKI_CODE=SHUKKA.TOKUISAKI_CODE AND";
		$sql .= " SIWAKE.TENPO_CODE=SHUKKA.TENPO_CODE)";
		$sql .= " LEFT JOIN M_STAFF_KIHON STAFF ON (";
		$sql .= " SIWAKE.KOSIN_STAFF=STAFF.STAFF_CD)";
		$sql .= " WHERE ";
		$sql .= " SIWAKE.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
		$sql .= " SIWAKE.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
		$sql .= " SIWAKE.HOMEN_MEI='" . $homen . "' AND";
		$sql .= " SIWAKE.TENPO_SIWAKE_KUBUN!='0' AND";
		$sql .= " (SHUKKA.SHUKKA_BI  >= '" . $this->MCommon->strToDateString($ymd_from) . "' AND";
		$sql .= " SHUKKA.SHUKKA_BI  <= '" . $this->MCommon->strToDateString($ymd_to) . "')";
		$sql .= " GROUP BY";
		$sql .= " KOSIN_STAFF";

		$this->queryWithPDOLog($stmt,$pdo,$sql, "作業進捗（検品）スタッフデータ 取得");
		
		$arrayList = array();

		while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			
			$array = array(
				"STAFF_CD" => $result['KOSIN_STAFF'],
				"STAFF_NM" => $result['STAFF_NM'],
			);

			$arrayList[] = $array;
		}
		
		return $arrayList;
	}

	/**
	 * デフォルト表示出荷日の取得
	 * @access   public
	 * @return   日付
	 */
	function getToday() {


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


			$dt = new DateTime($tmpDate);
			$ymd_today = $dt->format('Ymd');

			$pdo = null;

			return  $ymd_today;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM130", $e->getMessage());
			$pdo = null;
		}

		return date("Ymd",strtotime("-1 day"));;

	}
}

?>