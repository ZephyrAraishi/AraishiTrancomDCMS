<?php
/**
 * TPM110Model
 *
 * 種蒔照会画面
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class TPM110Model extends DCMSAppModel{

	public $name = 'TPM110Model';
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
	 * 種蒔照会データ取得
	 * @access   public
	 * @return   種蒔照会データ
	 */
	public function getList($ymd_from = '', $ymd_to = '', $homen= '', $shohin_code = '',$shohin_mei = '',$tenpo = '',$kanban_barcode = '') {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			//種蒔照会データの取得
			$sql  = "SELECT TN.SHUKKA_BI AS A1";
			$sql .= ",TN.NOHIN_BI AS A2";
			$sql .= ",TN.HOMEN_MEI AS A3";
			$sql .= ",TN.TENPO_CODE AS A4";
			$sql .= ",TN.TENPO_MEI AS A5";
			$sql .= ",TN.SHOHIN_CODE AS A6";
			$sql .= ",TN.SHOHIN_MEI AS A7";
			$sql .= ",TN.KANBAN_BARCODE AS A8";
			$sql .= ",TN.NISUGATA_1_KAKUNOSU AS A9";
			$sql .= ",TN.NISUGATA_2_KAKUNOSU 	AS A10";
			$sql .= ",TN.NISUGATA_3_KAKUNOSU 	AS A11";
			$sql .= ",TN.SO_BARA_KAKUNOSU 	AS A12";
			$sql .= ",TN.NINUSI_CD 	AS A13";
			$sql .= ",TN.SOSIKI_CD 	AS A14";
			$sql .= ",TN.BARA_LABEL_BARCODE AS A15";
			$sql .= ",TN.CASE_LABEL_BARCODE AS A16";
			$sql .= ",M1.NISUGATA_1_TANI AS A17";
			$sql .= ",M1.NISUGATA_2_TANI AS A18";
			$sql .= ",M1.NISUGATA_3_TANI AS A19";
			$sql .= ",TN.KOSIN_STAFF AS A20";
			$sql .= ",M2.STAFF_NM AS A21";
			$sql .= " FROM T_OZAX_SHUKKA_CHOHYO TN ";
			$sql .= " LEFT JOIN M_OZAX_SHOHIN M1 ON (TN.NINUSI_CD=M1.NINUSI_CD AND TN.SOSIKI_CD=M1.SOSIKI_CD  AND TN.SHOHIN_CODE=M1.SHOHIN_CODE)";
			$sql .= " LEFT JOIN M_STAFF_KIHON M2 ON (TN.NINUSI_CD=M2.DAI_NINUSI_CD AND TN.SOSIKI_CD=M2.DAI_SOSIKI_CD  AND TN.KOSIN_STAFF=M2.STAFF_CD)";
			
			$sql .= ' WHERE ';
			$sql .= "  TN.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  TN.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "  TN.CASE_LABEL_BARCODE  = ''";

			//出荷日の予定
			if ($ymd_from != ''){
				$sql .= " AND TN.SHUKKA_BI >= '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}

			if ($ymd_to != ''){
				$sql .= " AND TN.SHUKKA_BI <= '" . $this->MCommon->strToDateString($ymd_to) . "'" ;
			}

			//TC倉庫名（方面名）
			if ($homen != ''){
				$sql .= " AND TN.HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}

			//商品名コード
			if ($shohin_code != ''){
				$sql .= " AND TN.SHOHIN_CODE LIKE '%" . $shohin_code . "%'" ;
			}

			//商品名
			if ($shohin_mei != ''){
				$sql .= " AND TN.SHOHIN_MEI LIKE '%" . $shohin_mei . "%'" ;
			}

			//店舗名
			if ($tenpo != ''){
				$sql .= " AND TN.TENPO_MEI LIKE '%" . $tenpo . "%'" ;
			}

			//看板バーコード
			if ($kanban_barcode != ''){
				$sql .= " AND TN.KANBAN_BARCODE LIKE '%" . $kanban_barcode . "%'" ;
			}

			$sql .= "    ORDER BY";
			$sql .= "      TN.HOMEN_MEI,";
			$sql .= "      TN.TENPO_CODE,";
			$sql .= "      TN.KANBAN_BARCODE";
			$sql .= ";";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql,"種蒔照会データ 取得");
			
			$arrayList = array();
			
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			
				$array = array(
				    "SHUKKA_BI" => $result['A1'],
					"NOHIN_BI" => $result['A2'],
					"HOMEN_MEI" => $result['A3'],
					"TENPO_CODE" => $result['A4'],
					"TENPO_MEI" => $result['A5'],
					"SHOHIN_CODE" => $result['A6'],
					"SHOHIN_MEI" => $result['A7'],
					"KANBAN_BARCODE" => $result['A8'],
					"NISUGATA_1_KAKUNOSU" => $result['A9'],
					"NISUGATA_2_KAKUNOSU" => $result['A10'],
					"NISUGATA_3_KAKUNOSU" => $result['A11'],
					"SO_BARA_KAKUNOSU" => $result['A12'],
					"NISUGATA_1_TANI" => $result['A17'],
					"NISUGATA_2_TANI" => $result['A18'],
					"NISUGATA_3_TANI" => $result['A19'],
					"KOSIN_STAFF" => $result['A20'],
					"STAFF_NM" => $result['A21']
				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM110", $e->getMessage());
			$pdo = null;
		}


		return array();
	}


	/**
	 * 種蒔照会ＣＳＶデータ取得
	 * @access   public
	 * @return   種蒔照会CSVデータ
	 */
	public function getCSV($ymd_from = '', $ymd_to = '', $homen= '', $shohin_code = '',$shohin_mei = '',$tenpo = '',$kanban_barcode = '') {

		$text = array();

		//ヘッダー情報
		$header = "出荷日,納品日,方面名,店舗コード,店舗名,商品コード,商品名,";
		$header .= "看板バーコード,荷姿１格納数,荷姿２格納数,荷姿３格納数,総バラ格納数,作業者コード,作業者名";

		$text[] = $header;

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			//種蒔照会データの取得
			$sql  = "SELECT TN.SHUKKA_BI AS A1";
			$sql .= ",TN.NOHIN_BI AS A2";
			$sql .= ",TN.HOMEN_MEI AS A3";
			$sql .= ",TN.TENPO_CODE AS A4";
			$sql .= ",TN.TENPO_MEI AS A5";
			$sql .= ",TN.SHOHIN_CODE AS A6";
			$sql .= ",TN.SHOHIN_MEI AS A7";
			$sql .= ",TN.KANBAN_BARCODE AS A8";
			$sql .= ",TN.NISUGATA_1_KAKUNOSU AS A9";
			$sql .= ",TN.NISUGATA_2_KAKUNOSU 	AS A10";
			$sql .= ",TN.NISUGATA_3_KAKUNOSU 	AS A11";
			$sql .= ",TN.SO_BARA_KAKUNOSU 	AS A12";
			$sql .= ",TN.NINUSI_CD 	AS A13";
			$sql .= ",TN.SOSIKI_CD 	AS A14";
			$sql .= ",TN.BARA_LABEL_BARCODE AS A15";
			$sql .= ",TN.CASE_LABEL_BARCODE AS A16";
			$sql .= ",M1.NISUGATA_1_TANI AS A17";
			$sql .= ",M1.NISUGATA_1_TANI AS A18";
			$sql .= ",M1.NISUGATA_3_TANI AS A19";
			$sql .= ",TN.KOSIN_STAFF AS A20";
			$sql .= ",M2.STAFF_NM AS A21";
			$sql .= " FROM T_OZAX_SHUKKA_CHOHYO TN ";
			$sql .= " LEFT JOIN M_OZAX_SHOHIN M1 ON (TN.NINUSI_CD=M1.NINUSI_CD AND TN.SOSIKI_CD=M1.SOSIKI_CD  AND TN.SHOHIN_CODE=M1.SHOHIN_CODE)";
			$sql .= " LEFT JOIN M_STAFF_KIHON M2 ON (TN.NINUSI_CD=M2.DAI_NINUSI_CD AND TN.SOSIKI_CD=M2.DAI_SOSIKI_CD  AND TN.KOSIN_STAFF=M2.STAFF_CD)";

			
			$sql .= ' WHERE ';
			$sql .= "  TN.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  TN.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "  TN.CASE_LABEL_BARCODE  = ''";

			//出荷日の予定
			if ($ymd_from != ''){
				$sql .= "  AND TN.SHUKKA_BI >= '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}

			if ($ymd_to != ''){
				$sql .= " AND TN.SHUKKA_BI <= '" . $this->MCommon->strToDateString($ymd_to) . "'" ;
			}

			//TC倉庫名（方面名）
			if ($homen != ''){
				$sql .= " AND TN.HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}

			//商品名コード
			if ($shohin_code != ''){
				$sql .= " AND TN.SHOHIN_CODE LIKE '%" . $shohin_code . "%'" ;
			}

			//商品名
			if ($shohin_mei != ''){
				$sql .= " AND TN.SHOHIN_MEI LIKE '%" . $shohin_mei . "%'" ;
			}

			//店舗名
			if ($tenpo != ''){
				$sql .= " AND TN.TENPO_MEI LIKE '%" . $tenpo . "%'" ;
			}

			//看板バーコード
			if ($kanban_barcode != ''){
				$sql .= " AND TN.KANBAN_BARCODE LIKE '%" . $kanban_barcode . "%'" ;
			}

			$sql .= "    ORDER BY";
			$sql .= "      TN.HOMEN_MEI,";
			$sql .= "      TN.TENPO_CODE,";
			$sql .= "      TN.KANBAN_BARCODE";
			$sql .= ";";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"種蒔照会CSVデータ 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$tmpText = $result['A1'] . ',';
				$tmpText .= $result['A2'] . ',';
				$tmpText .= $result['A3'] . ',';
				$tmpText .= $result['A4'] . ',';
				$tmpText .= $result['A5'] . ',';
				$tmpText .= $result['A6'] . ',';
				$tmpText .= $result['A7'] . ',';
				$tmpText .= $result['A8'] . ',';
				$tmpText .= $result['A9'] . ',';
				$tmpText .= $result['A10'] . ',';
				$tmpText .= $result['A11'] . ',';
				$tmpText .= $result['A12'] . ',';
				$tmpText .= $result['A20'] . ',';
				$tmpText .= $result['A21'] ;

				$text[] = $tmpText;

			}

			$pdo = null;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM110", $e->getMessage());
			$pdo = null;
		}


		return $text;
	}





	/**
	 * タイムスタンプ取得
	 * @access   public
	 * @param    タイムスタンプ
	 * @return   成否情報(true,false)
	 */
	public function getTimestamp() {

		$pdo = null;

		try{

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//タイムスタンプ取得
			$sql = "CALL P_TPM110_GET_TIMESTAMP(";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " @timestamp";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, '種蒔照会　タイムスタンプ取得');

			$sql = "";
			$sql .= " SELECT";
			$sql .= "  @timestamp";


			$this->queryWithPDOLog($stmt,$pdo,$sql, '種蒔照会　タイムスタンプ取得');
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			$timestamp = $result['@timestamp'];

			if ($timestamp == null) {
				$timestamp = date('Y-m-d H:i:s');
			}

			return $timestamp;

		} catch (Exception $e){

			$pdo = null;
		}

		return date('Y-m-d H:i:s');

	}
	/**
	 * 締日初期値取得
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

			$this->printLog("fatal", "例外発生", "TPM090", $e->getMessage());
			$pdo = null;
		}

		return date("Ymd",strtotime("-1 day"));;

	}
}

?>