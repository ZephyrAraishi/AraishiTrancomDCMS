<?php
/**
 * TPM140Model
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

class TPM140Model extends DCMSAppModel{
	const EMPTY_VALUE = '-';
	
	public $name = 'TPM140Model';
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
	 * 発送データ取得
	 * @access   public
	 * @return   発送データ
	 */
	public function getHassoList($ymd_from, $ymd_to) {

		//DBオブジェクト取得
		$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		// 得意先、店舗の一覧を取得
		$result = $this->getHassoListInternal($pdo, $ymd_from, $ymd_to);
		
		// ケースの発送件数を取得
		$result = $this->getCaseHassoList($pdo, $ymd_from, $ymd_to, $result);
		
		// バラの発送件数を取得
		$result = $this->getBaraHassoList($pdo, $ymd_from, $ymd_to, $result);

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
		$header = "得意先ｺｰﾄﾞ+店舗ｺｰﾄﾞ,納品日,得意先ｺｰﾄﾞ,店舗ｺｰﾄﾞ,得意先名,店舗名,個数";

		$text[] = $header;
		
		$result = $this->getHassoList($ymd_from, $ymd_to);
		
		foreach ( $result as &$ar ) {
			$tmp = $ar["TOKUISAKI_CODE"] . $ar["TENPO_CODE"] . ",";
			$tmp .= str_replace('/', '', $ar["NOHIN_BI"]) . ",";
			$tmp .= $ar["TOKUISAKI_CODE"] . ",";
			$tmp .= $ar["TENPO_CODE"] . ",";
			$tmp .= $ar["TOKUISAKI_MEI"] . ",";
			$tmp .= $ar["TENPO_MEI"] . ",";
			$tmp .= $ar["KOSU"];
			
			$text[] = $tmp;
		}
		
		return $text;
	}

	/**
	 * ケース発送データを取得
	 * @access   public
	 * @return   ケース発送データ
	*/
	private function getHassoListInternal($pdo, $ymd_from, $ymd_to) {
		
		$sql  = "SELECT DISTINCT SHUKKA.TOKUISAKI_CODE, SHUKKA.TOKUISAKI_MEI, SHUKKA.TENPO_CODE, SHUKKA.TENPO_MEI, ";
		$sql  .= " SHUKKA.NOHIN_BI ";
		$sql  .= " FROM T_OZAX_SHUKKA SHUKKA ";
		$sql  .= " WHERE  ";
		$sql  .= " SHUKKA.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
		$sql  .= " SHUKKA.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
		$sql  .= " (SHUKKA.SHUKKA_BI>='" . $this->MCommon->strToDateString($ymd_from) . "'";
		$sql  .= " AND SHUKKA.SHUKKA_BI<='" . $this->MCommon->strToDateString($ymd_to) . "')";
		$sql  .= " ORDER BY TOKUISAKI_CODE, TENPO_CODE, NOHIN_BI";

//			$this->printLog("fatal", "例外発生", "TPM140", $sql);

		$this->queryWithPDOLog($stmt,$pdo,$sql, "ケース発送データ 取得");
		
		$arrayList = array();
		
		while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
		
			$array = array(
				"TOKUISAKI_CODE" => $result['TOKUISAKI_CODE'],
				"TOKUISAKI_MEI" => $result['TOKUISAKI_MEI'],
				"TENPO_CODE" => $result['TENPO_CODE'],
				"TENPO_MEI" => $result['TENPO_MEI'],
				"NOHIN_BI" => $result['NOHIN_BI'],
				"KOSU" => 0,
			);

			$arrayList[] = $array;
		}
		
		return $arrayList;
	}

	/**
	 * ケース発送データを取得
	 * @access   public
	 * @return   ケース発送データ
	*/
	private function getCaseHassoList($pdo, $ymd_from, $ymd_to, $arrayList) {
		
		$sql  = "SELECT SHUKKA.TOKUISAKI_CODE, SHUKKA.TOKUISAKI_MEI, SHUKKA.TENPO_CODE, SHUKKA.TENPO_MEI, ";
		$sql  .= " SHUKKA.NOHIN_BI, count(CHOHYO.CASE_LABEL_BARCODE) KOSU ";
		$sql  .= " FROM T_OZAX_SHUKKA SHUKKA, T_OZAX_SHUKKA_CHOHYO CHOHYO ";
		$sql  .= " WHERE  ";
		$sql  .= " SHUKKA.NINUSI_CD = CHOHYO.NINUSI_CD AND";
		$sql  .= " SHUKKA.SOSIKI_CD = CHOHYO.SOSIKI_CD AND";
		$sql  .= " SHUKKA.GYOTAI_SOKO_CODE = CHOHYO.GYOTAI_SOKO_CODE AND";
		$sql  .= " SHUKKA.HOMEN_MEI = CHOHYO.HOMEN_MEI AND";
		$sql  .= " SHUKKA.SHUKKA_BI = CHOHYO.SHUKKA_BI AND";
		$sql  .= " SHUKKA.TENPO_CODE = CHOHYO.TENPO_CODE AND";
		$sql  .= " SHUKKA.BATCH_NUMBER = CHOHYO.BATCH_NUMBER AND";
		$sql  .= " SHUKKA.SHOHIN_CODE = CHOHYO.SHOHIN_CODE AND";
		$sql  .= " SHUKKA.SYOMI_KIGEN = CHOHYO.SYOMI_KIGEN AND";
		$sql  .= " SHUKKA.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
		$sql  .= " SHUKKA.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
		$sql  .= " (SHUKKA.SHUKKA_BI>='" . $this->MCommon->strToDateString($ymd_from) . "'";
		$sql  .= " AND SHUKKA.SHUKKA_BI<='" . $this->MCommon->strToDateString($ymd_to) . "') AND";
		$sql  .= " CHOHYO.CASE_LABEL_BARCODE != ''";
		$sql  .= " GROUP BY TOKUISAKI_CODE, TENPO_CODE, NOHIN_BI";

		$this->queryWithPDOLog($stmt,$pdo,$sql, "ケース発送データ 取得");
		
		while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
		
			foreach ( $arrayList as &$ar ) {
				if ( $ar["TOKUISAKI_CODE"] == $result['TOKUISAKI_CODE'] && 
				     $ar["TENPO_CODE"] == $result['TENPO_CODE'] && 
				     $ar["NOHIN_BI"] == $result['NOHIN_BI'] ) {
					$ar["KOSU"] = $ar["KOSU"] + $result['KOSU'];
					break;
				}
			}
		}
		
		return $arrayList;
	}

	/**
	 * バラ発送データを取得
	 * @access   public
	 * @return   バラ発送データ
	*/
	private function getBaraHassoList($pdo, $ymd_from, $ymd_to, $arrayList) {
		
		$sql  = "SELECT SHUKKA.TOKUISAKI_CODE, SHUKKA.TOKUISAKI_MEI, SHUKKA.TENPO_CODE, SHUKKA.TENPO_MEI,  ";
		$sql  .= " SHUKKA.NOHIN_BI, count(DISTINCT CHOHYO.KANBAN_BARCODE) KOSU ";
		$sql  .= " FROM T_OZAX_SHUKKA SHUKKA, T_OZAX_SHUKKA_CHOHYO CHOHYO ";
		$sql  .= "WHERE  ";
		$sql  .= " SHUKKA.NINUSI_CD = CHOHYO.NINUSI_CD AND";
		$sql  .= " SHUKKA.SOSIKI_CD = CHOHYO.SOSIKI_CD AND";
		$sql  .= " SHUKKA.GYOTAI_SOKO_CODE = CHOHYO.GYOTAI_SOKO_CODE AND";
		$sql  .= " SHUKKA.HOMEN_MEI = CHOHYO.HOMEN_MEI AND";
		$sql  .= " SHUKKA.SHUKKA_BI = CHOHYO.SHUKKA_BI AND";
		$sql  .= " SHUKKA.TENPO_CODE = CHOHYO.TENPO_CODE AND";
		$sql  .= " SHUKKA.BATCH_NUMBER = CHOHYO.BATCH_NUMBER AND";
		$sql  .= " SHUKKA.SHOHIN_CODE = CHOHYO.SHOHIN_CODE AND";
		$sql  .= " SHUKKA.SYOMI_KIGEN = CHOHYO.SYOMI_KIGEN AND";
		$sql  .= " SHUKKA.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
		$sql  .= " SHUKKA.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
		$sql  .= " (SHUKKA.SHUKKA_BI>='" . $this->MCommon->strToDateString($ymd_from) . "'";
		$sql  .= " AND SHUKKA.SHUKKA_BI<='" . $this->MCommon->strToDateString($ymd_to) . "') AND";
		$sql  .= " CHOHYO.KANBAN_BARCODE != ''";
		$sql  .= " GROUP BY TOKUISAKI_CODE, TENPO_CODE, NOHIN_BI";

		$this->queryWithPDOLog($stmt,$pdo,$sql, "バラ発送データ 取得");

		while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			
			foreach ( $arrayList as &$ar ) {
				if ( $ar["TOKUISAKI_CODE"] == $result['TOKUISAKI_CODE'] && 
				     $ar["TENPO_CODE"] == $result['TENPO_CODE'] && 
				     $ar["NOHIN_BI"] == $result['NOHIN_BI'] ) {
					$ar["KOSU"] = $ar["KOSU"] + $result['KOSU'];
					break;
				}
			}
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

			$this->printLog("fatal", "例外発生", "TPM140", $e->getMessage());
			$pdo = null;
		}

		return date("Ymd",strtotime("-1 day"));;

	}
}

?>