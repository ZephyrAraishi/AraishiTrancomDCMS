<?php
/**
 * TPM120Model
 *
 * 物量比較画面
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class TPM120Model extends DCMSAppModel{

	public $name = 'TPM120Model';
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
	 * 物量比較データ取得
	 * @access   public
	 * @return   物量比較データ
	 */
	public function getList($ymd_from = '',$ymd_to = '') {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//物量比較データの取得
			$sql  = "SELECT DISTINCT TN.TOKUISAKI_MEI ";										//得意先名
			$sql .= ",COUNT(TN.TOKUISAKI_CODE) AS GYOSU";										//行数
			$sql .= ",SUM(TN.NISUGATA_1_SHIJISU ) AS SHIJISU1";									//荷姿1指示数
			$sql .= ",SUM(TN.NISUGATA_2_SHIJISU ) AS SHIJISU2";									//荷姿2指示数
			$sql .= ",SUM(TN.NISUGATA_3_SHIJISU ) AS SHIJISU3";									//荷姿3指示数
			$sql .= ",TN.TOKUISAKI_CODE ";														//得意先コード
			$sql .= " FROM T_OZAX_SHUKKA TN ";
			
			$sql .= ' WHERE ';
			$sql .= "  TN.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  TN.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";

			//出荷日の予定
			if ($ymd_from != ''){
				$sql .= "  TN.SHUKKA_BI >= '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}

			if ($ymd_to != ''){
				$sql .= " AND TN.SHUKKA_BI <= '" . $this->MCommon->strToDateString($ymd_to) . "'" ;
			}
			
			$sql .= " GROUP BY";
			$sql .= "  TN.TOKUISAKI_CODE";
			$sql .= ";";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"積込検品進捗データ 取得");
			
			$arrayList = array();
			
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			
				$array = array(
				    "TOKUISAKI_CODE" => $result['TOKUISAKI_CODE'],				//得意コード
					"TOKUISAKI_MEI" => $result['TOKUISAKI_MEI'],				//得意先名
					"GYOSU" => $result['GYOSU'],								//行数
					"SHIJISU1" => $result['SHIJISU1'],							//荷姿1指示数
					"SHIJISU2" => $result['SHIJISU2'],							//荷姿2指示数
					"SHIJISU3" => $result['SHIJISU3'],							//荷姿3指示数
				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM120", $e->getMessage());
			$pdo = null;
		}

		return array();
	}

	/**
	 * 物量比較データ取得2
	 * @access   public
	 * @return   物量比較データ2
	 */
	public function getList2($ymd_from = '',$ymd_to = '') {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//積込検品進捗データの取得
			$sql  = "SELECT COUNT(TN.CASE_LABEL_BARCODE) AS KENSU";						//ケースラベル数
			$sql .= ",M1.TOKUISAKI_CODE ";

			$sql .= " FROM T_OZAX_SHUKKA_CHOHYO TN ";
			$sql .= "  INNER JOIN T_OZAX_SHUKKA M1 ON (TN.NINUSI_CD =M1.NINUSI_CD AND TN.SOSIKI_CD =M1.SOSIKI_CD AND TN.GYOTAI_SOKO_CODE =M1.GYOTAI_SOKO_CODE AND TN.HOMEN_MEI =M1.HOMEN_MEI AND 
							TN.SHUKKA_BI =M1.SHUKKA_BI AND TN.TENPO_CODE =M1.TENPO_CODE AND TN.BATCH_NUMBER =M1.BATCH_NUMBER AND TN.SHOHIN_CODE =M1.SHOHIN_CODE AND
							TN.SYOMI_KIGEN =M1.SYOMI_KIGEN )";
			
			$sql .= ' WHERE ';
			$sql .= "  TN.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  TN.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "  TN.CASE_LABEL_BARCODE != ''";

			//出荷日の予定
			if ($ymd_from != ''){
				$sql .= "  AND TN.SHUKKA_BI >= '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}

			if ($ymd_to != ''){
				$sql .= " AND TN.SHUKKA_BI <= '" . $this->MCommon->strToDateString($ymd_to) . "'" ;
			}
			
			$sql .= " GROUP BY";
			$sql .= "  TOKUISAKI_CODE";
			$sql .= ";";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"積込検品進捗データ 取得");
			
			$arrayList = array();
			
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			
				$array = array(
				    "K_KENSU" => $result['KENSU'],
					"TOKUISAKI_CODE" => $result['TOKUISAKI_CODE'],

				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM120", $e->getMessage());
			$pdo = null;
		}

		return array();
	}

	/**
	 * 物量比較データ取得3
	 * @access   public
	 * @return   物量比較データ3
	 */
	public function getList3($ymd_from = '',$ymd_to = '') {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//物量比較データの取得
			$sql  = "SELECT COUNT(DISTINCT TN.KANBAN_BARCODE) AS B_KENSU";										//看板数
			$sql .= ",M1.TOKUISAKI_CODE ";

			$sql .= " FROM T_OZAX_SHUKKA_CHOHYO TN ";
			$sql .= "  INNER JOIN T_OZAX_SHUKKA M1 ON
			(TN.NINUSI_CD =M1.NINUSI_CD AND TN.SOSIKI_CD =M1.SOSIKI_CD AND TN.GYOTAI_SOKO_CODE =M1.GYOTAI_SOKO_CODE AND TN.HOMEN_MEI =M1.HOMEN_MEI AND 
			TN.SHUKKA_BI =M1.SHUKKA_BI AND TN.TENPO_CODE =M1.TENPO_CODE AND TN.BATCH_NUMBER =M1.BATCH_NUMBER AND TN.SHOHIN_CODE =M1.SHOHIN_CODE AND TN.SYOMI_KIGEN =M1.SYOMI_KIGEN )";
			
			$sql .= ' WHERE ';
			$sql .= "  TN.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  TN.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "  TN.KANBAN_BARCODE != ''";

			//出荷日の予定
			if ($ymd_from != ''){
				$sql .= "  AND TN.SHUKKA_BI >= '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}

			if ($ymd_to != ''){
				$sql .= " AND TN.SHUKKA_BI <= '" . $this->MCommon->strToDateString($ymd_to) . "'" ;
			}
			
			$sql .= " GROUP BY";
			$sql .= "  TOKUISAKI_CODE";
			$sql .= ";";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql,"物量比較データ 取得");
			
			$arrayList = array();
			
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			
				$array = array(
				    "B_KENSU" => $result['B_KENSU'],
					"TOKUISAKI_CODE" => $result['TOKUISAKI_CODE'],

				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM120", $e->getMessage());
			$pdo = null;
		}

		return array();
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
			$sql = "CALL P_TPM120_GET_TIMESTAMP(";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " @timestamp";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, '物量比較　タイムスタンプ取得');

			$sql = "";
			$sql .= " SELECT";
			$sql .= "  @timestamp";


			$this->queryWithPDOLog($stmt,$pdo,$sql, '物量比較　タイムスタンプ取得');
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
		/**
	 * 検索日取得
	 * @access   public
	 * @return   検索日付
	 */
	public function getTimesearch($ymd_from,$ymd_to) {
		$w = date("w",strtotime($ymd));
			if ($w == 1){
				$ymd_m = date('Ymd',strtotime($ymd));
			}else{
				$ymd_m = date('Ymd', strtotime("last Monday", strtotime($ymd)));
			}
				$tue = date("Ymd", strtotime("$ymd_m +1 day"  ));
				$sun = date("Ymd", strtotime("$ymd_m +6 day"  ));
				$lmon = date("Ymd", strtotime("$ymd_m -7 day"  ));
				$ltue = date("Ymd", strtotime("$ymd_m -6 day"  ));
				$lsun = date("Ymd", strtotime("$ymd_m -1 day"  ));
			return array();
	}
}

?>