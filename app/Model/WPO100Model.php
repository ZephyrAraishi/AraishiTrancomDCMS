<?php
/**
 * WPO100Model
 *
 * 出荷指示データ削除画面
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 * @memo          2017/09/29 K.Araishi TPM070を元に新規作成
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class WPO100Model extends DCMSAppModel{

	public $name = 'WPO100Model';
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
	 * 出荷指示削除データ取得
	 * @access   public
	 * @return   出荷指示データ
	 */
	public function getList($ymd_from = '',$BatchNo_from = '',$homen = '') {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//出荷指示データの取得
			$sql  = "SELECT CONCAT('(',GYOTAI_SOKO_CODE,')',GYOTAI_SOKO_MEI) AS A1";
			$sql .= ",SHUKKA_BI AS A2";
			$sql .= ",NOHIN_BI AS A3";
			$sql .= ",BATCH_NUMBER AS A4";
			$sql .= ",CONCAT('(',HOMEN_CODE ,')',HOMEN_MEI) AS A5";
			$sql .= ",CONCAT('(',TENPO_CODE ,')',TENPO_MEI) AS A6";
			$sql .= ",CONCAT('(',TOKUISAKI_CODE ,')', TOKUISAKI_MEI) AS A7";
			$sql .= ",LOCATION AS A8";
			$sql .= ",CONCAT('(',SHOHIN_CODE ,')', SHOHIN_MEI) AS A9";
			$sql .= ",COALESCE(SYOMI_KIGEN,'') AS A10";
			$sql .= ",NISUGATA_1_SHIJISU AS A11";
			$sql .= ",NISUGATA_2_SHIJISU AS A12";
			$sql .= ",NISUGATA_3_SHIJISU AS A13";
			$sql .= ",SO_BARA_SU AS A14";
			$sql .= ",CONCAT('(',DAIHYO_SOKO_CODE ,')', DAIHYO_SOKO_MEI) AS A15";
			$sql .= " FROM T_OZAX_SHUKKA";

			$sql .= ' WHERE ';
			$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "'";

			//出荷日
			if ($ymd_from != ''){
				$sql .= " AND SHUKKA_BI = '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}

			//バッチ№
			if ($BatchNo_from != ''){
				$sql .= " AND BATCH_NUMBER = '" . $BatchNo_from . "'" ;
			}

			//方面
			if ($homen != ''){
				$sql .= " AND HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}

			$sql .= "    ORDER BY";
			$sql .= "      SHUKKA_BI,";
			$sql .= "      NOHIN_BI,";
			$sql .= "      GYOTAI_SOKO_CODE,";
			$sql .= "      BATCH_NUMBER,";
			$sql .= "      HOMEN_MEI,";
			$sql .= "      TENPO_MEI,";
			$sql .= "      LOCATION,";
			$sql .= "      SHOHIN_CODE";
			$sql .= ";";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"出荷指示削除データ 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
				    "GYOTAI_SOKO" => $result['A1'],
					"SHUKKA_BI" => $result['A2'],
					"NOHIN_BI" => $result['A3'],
					"BATCH_NUMBER" => $result['A4'],
					"HOMEN" => $result['A5'],
					"TENPO" => $result['A6'],
					"TOKUISAKI" => $result['A7'],
					"LOCATION" => $result['A8'],
					"SHOHIN" => $result['A9'],
					"SYOMI_KIGEN" => $result['A10'],
					"NISUGATA_1_SHIJISU" => $result['A11'],
					"NISUGATA_2_SHIJISU" => $result['A12'],
					"NISUGATA_3_SHIJISU" => $result['A13'],
					"SO_BARA_SU" => $result['A14'],
					"DAIHYO_SOKO" => $result['A15']
				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO100", $e->getMessage());
			$pdo = null;
		}


		return array();
	}

	/**
	 * 出荷指示関連データ削除処理
	 * @access   public
	 * @return   処理メッセージ
	 */
	function deleteShukkaData($ymd_from = '',$BatchNo_from = '',$homen = '') {

		$pdo = null;
		$retText = "";
		$sql = "";
		$where = "";

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// トータルピッキング開始ちぇっく
			$sql  = "SELECT COUNT(*) AS A1";
			$sql .= " FROM T_OZAX_TOTAL_PK";

			$where = ' WHERE ';
			$where .= "  NINUSI_CD='" . $this->_ninusi_cd . "'";
			$where .= "  AND SOSIKI_CD='" . $this->_sosiki_cd . "'";
			$where .= "  AND TOTAL_PK_KUBUN != '0'";
			//出荷日
			if ($ymd_from != ''){
				$where .= " AND SHUKKA_BI = '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}
			//バッチ№
			if ($BatchNo_from != ''){
				$where .= " AND BATCH_NUMBER = '" . $BatchNo_from . "'" ;
			}
			//方面
			if ($homen != ''){
				$where .= " AND HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}
			$where .= ";";

			$this->printLog("check", "テスト用", "WPO100", $sql . $where);
			$this->queryWithPDOLog($stmt_check,$pdo,$sql . $where,"トータルピッキング開始チェック");
			$result_check = $stmt_check->fetch(PDO::FETCH_ASSOC);
			$datacount = $result_check["A1"];
			if ($datacount != 0) {
				$retText = "トータルピッキングが既に開始されています。削除処理を継続出来ません。";
				return $retText;
			}

			//クエリーの条件節文字列を作成
			$where = ' WHERE ';
			$where .= "  NINUSI_CD='" . $this->_ninusi_cd . "'";
			$where .= "  AND SOSIKI_CD='" . $this->_sosiki_cd . "'";
			//出荷日
			if ($ymd_from != ''){
				$where .= " AND SHUKKA_BI = '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}
			//バッチ№
			if ($BatchNo_from != ''){
				$where .= " AND BATCH_NUMBER = '" . $BatchNo_from . "'" ;
			}
			//方面
			if ($homen != ''){
				$where .= " AND HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}
			$where .= ";";

			//トランザクション開始
			$pdo->beginTransaction();

			// 出荷指示データの削除
			$sql = "DELETE FROM T_OZAX_SHUKKA ";
			$this->execWithPDOLog($pdo, $sql . $where, 'WPO100  出荷指示データ削除');

			// トータルピッキングデータの削除
			$sql = "DELETE FROM T_OZAX_TOTAL_PK ";
			$this->execWithPDOLog($pdo, $sql . $where, 'WPO100  トータルピッキングデータデータ削除');

			$sql = "DELETE FROM T_OZAX_IPOD_TOTAL_PK ";
			$this->execWithPDOLog($pdo, $sql . $where, 'WPO100  トータルピッキングデータiPodデータ削除');

			// 種蒔データの削除
			$sql = "DELETE FROM T_OZAX_TANEMAKI ";
			$this->execWithPDOLog($pdo, $sql . $where, 'WPO100  種蒔データ削除');

			$sql = "DELETE FROM T_OZAX_IPOD_TANEMAKI ";
			$this->execWithPDOLog($pdo, $sql . $where, 'WPO080  種蒔iPodデータ削除');

			// ケースラベルデータの削除
			$sql = "DELETE FROM T_OZAX_CASE_LABEL ";
			$this->execWithPDOLog($pdo, $sql . $where, 'WPO100  ケースラベルデータ削除');

			// バララベルデータの削除
			$sql = "DELETE FROM T_OZAX_BARA_LABEL ";
			$this->execWithPDOLog($pdo, $sql . $where, 'WPO100  バララベルデータ削除');

			// 看板ラベルデータの削除
			$sql = "DELETE FROM T_OZAX_KANBAN ";
			$this->execWithPDOLog($pdo, $sql . $where, 'WPO100  看板ラベルデータ削除');

			// 出荷帳票データの削除
			$sql = "DELETE FROM T_OZAX_SHUKKA_CHOHYO ";
			//クエリーの条件節文字列を作成
			$where = ' WHERE ';
			$where .= "  NINUSI_CD='" . $this->_ninusi_cd . "'";
			$where .= "  AND SOSIKI_CD='" . $this->_sosiki_cd . "'";
			//出荷日
			if ($ymd_from != ''){
				$where .= " AND SHUKKA_BI = '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}
			//バッチ№
			if ($BatchNo_from != ''){
				$where .= " AND BATCH_NUMBER = '" . $BatchNo_from . "'" ;
			}
			//方面
			if ($homen != ''){
				$where .= " AND HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}
			$where .= ";";

			$this->execWithPDOLog($pdo, $sql . $where, 'WPO100  出荷帳票データ削除');

			// 店舗仕分けデータの削除
			$sql = "DELETE FROM T_OZAX_TENPO_SIWAKE ";
			$where = " WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$where .= "   AND SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			//納品日
			if ($ymd_from != ''){
				$where .= "  AND NOHIN_BI >= '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}
			//バッチ№
			if ($BatchNo_from != ''){
				$where .= " AND BATCH_NUMBER = '" . $BatchNo_from . "'" ;
			}
			//方面
			if ($homen != ''){
				$where .= " AND HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}
			$where .= ";";
			$this->execWithPDOLog($pdo, $sql . $where, 'WPO100  店舗仕分データ削除');

			$sql = "DELETE FROM T_OZAX_ANDROID_TENPO_SIWAKE ";
			$this->execWithPDOLog($pdo, $sql . $where , 'WPO100  店舗仕分Androidデータ削除');

			$pdo->commit();
			$retText = "処理が正常に完了しました。";
			$pdo = null;

		} catch (Exception $e) {
			$pdo->rollBack();
			$this->printLog("fatal", "例外発生", "WPO100", $e->getMessage());
			$retText = $e->getMessage();
			$pdo = null;
		}


		return $retText;
	}

	/**
	 * 出荷指示関連データ更新処理⇒日付項目を５０年後
	 * @access   public
	 * @return   処理メッセージ
	 */
	function updateShukkaData($ymd_from = '',$BatchNo_from = '',$homen = '') {

		$pdo = null;
		$retText = "";
		$sql = "";
		$where = "";

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// トータルピッキング開始ちぇっく
			$sql  = "SELECT COUNT(*) AS A1";
			$sql .= " FROM T_OZAX_TOTAL_PK";

			$where = ' WHERE ';
			$where .= "  NINUSI_CD='" . $this->_ninusi_cd . "'";
			$where .= "  AND SOSIKI_CD='" . $this->_sosiki_cd . "'";
			$where .= "  AND TOTAL_PK_KUBUN != '0'";
			//出荷日
			if ($ymd_from != ''){
				$where .= " AND SHUKKA_BI = '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}
			//バッチ№
			if ($BatchNo_from != ''){
				$where .= " AND BATCH_NUMBER = '" . $BatchNo_from . "'" ;
			}
			//方面
			if ($homen != ''){
				$where .= " AND HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}
			$where .= ";";

			$this->printLog("check", "テスト用", "WPO100", $sql . $where);
			$this->queryWithPDOLog($stmt_check,$pdo,$sql . $where,"トータルピッキング開始チェック");
			$result_check = $stmt_check->fetch(PDO::FETCH_ASSOC);
			$datacount = $result_check["A1"];
			if ($datacount != 0) {
				$retText = "トータルピッキングが既に開始されています。削除処理を継続出来ません。";
				return $retText;
			}

			//クエリーの条件節文字列を作成
			$where = ' WHERE ';
			$where .= "  NINUSI_CD='" . $this->_ninusi_cd . "'";
			$where .= "  AND SOSIKI_CD='" . $this->_sosiki_cd . "'";
			//出荷日
			if ($ymd_from != ''){
				$where .= " AND SHUKKA_BI = '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}
			//バッチ№
			if ($BatchNo_from != ''){
				$where .= " AND BATCH_NUMBER = '" . $BatchNo_from . "'" ;
			}
			//方面
			if ($homen != ''){
				$where .= " AND HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}
			$where .= ";";

			//トランザクション開始
			$pdo->beginTransaction();

			// 出荷指示データの更新
			$sql = "UPDATE T_OZAX_SHUKKA SET SHUKKA_BI = DATE_ADD(SHUKKA_BI, INTERVAL 50 YEAR),NOHIN_BI = DATE_ADD(NOHIN_BI, INTERVAL 50 YEAR),NYUKA_BI=CONCAT('9',IFNULL(NYUKA_BI,''))";
			$this->execWithPDOLog($pdo, $sql . $where, 'WPO100  出荷指示データ削除');

			// トータルピッキングデータの更新
			$sql = "UPDATE T_OZAX_TOTAL_PK SET SHUKKA_BI = DATE_ADD(SHUKKA_BI, INTERVAL 50 YEAR),NOHIN_BI = DATE_ADD(NOHIN_BI, INTERVAL 50 YEAR)";
			$this->execWithPDOLog($pdo, $sql . $where, 'WPO100  トータルピッキングデータデータ削除');

			$sql = "UPDATE T_OZAX_IPOD_TOTAL_PK SET SHUKKA_BI = DATE_ADD(SHUKKA_BI, INTERVAL 50 YEAR),NOHIN_BI = DATE_ADD(NOHIN_BI, INTERVAL 50 YEAR)";
			$this->execWithPDOLog($pdo, $sql . $where, 'WPO100  トータルピッキングデータiPodデータ削除');

			// 種蒔データの削除
			$sql = "UPDATE T_OZAX_TANEMAKI SET SHUKKA_BI = DATE_ADD(SHUKKA_BI, INTERVAL 50 YEAR),NOHIN_BI = DATE_ADD(NOHIN_BI, INTERVAL 50 YEAR)";
			$this->execWithPDOLog($pdo, $sql . $where, 'WPO100  種蒔データ削除');

			$sql = "UPDATE T_OZAX_IPOD_TANEMAKI SET SHUKKA_BI = DATE_ADD(SHUKKA_BI, INTERVAL 50 YEAR),NOHIN_BI = DATE_ADD(NOHIN_BI, INTERVAL 50 YEAR)";
			$this->execWithPDOLog($pdo, $sql . $where, 'WPO080  種蒔iPodデータ削除');

			// ケースラベルデータの削除
			$sql = "UPDATE T_OZAX_CASE_LABEL SET SHUKKA_BI = DATE_ADD(SHUKKA_BI, INTERVAL 50 YEAR)";
			$this->execWithPDOLog($pdo, $sql . $where, 'WPO100  ケースラベルデータ削除');

			// バララベルデータの削除
			$sql = "UPDATE T_OZAX_BARA_LABEL SET SHUKKA_BI = DATE_ADD(SHUKKA_BI, INTERVAL 50 YEAR)";
			$this->execWithPDOLog($pdo, $sql . $where, 'WPO100  バララベルデータ削除');

			// 看板ラベルデータの削除
			$sql = "UPDATE T_OZAX_KANBAN SET SHUKKA_BI = DATE_ADD(SHUKKA_BI, INTERVAL 50 YEAR)";
			$this->execWithPDOLog($pdo, $sql . $where, 'WPO100  看板ラベルデータ削除');

			// 出荷帳票データの削除
			$sql = "UPDATE T_OZAX_SHUKKA_CHOHYO SET SHUKKA_BI = DATE_ADD(SHUKKA_BI, INTERVAL 50 YEAR),NOHIN_BI = DATE_ADD(NOHIN_BI, INTERVAL 50 YEAR)";
			//クエリーの条件節文字列を作成
			$where = ' WHERE ';
			$where .= "  NINUSI_CD='" . $this->_ninusi_cd . "'";
			$where .= "  AND SOSIKI_CD='" . $this->_sosiki_cd . "'";
			//出荷日
			if ($ymd_from != ''){
				$where .= " AND SHUKKA_BI = '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}
			//バッチ№
			if ($BatchNo_from != ''){
				$where .= " AND BATCH_NUMBER = '" . $BatchNo_from . "'" ;
			}
			//方面
			if ($homen != ''){
				$where .= " AND HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}
			$where .= ";";

			$this->execWithPDOLog($pdo, $sql . $where, 'WPO100  出荷帳票データ削除');

			// 店舗仕分けデータの削除
			$sql = "UPDATE T_OZAX_TENPO_SIWAKE SET NOHIN_BI = DATE_ADD(NOHIN_BI, INTERVAL 50 YEAR)";
			$where = " WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$where .= "   AND SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			//納品日
			if ($ymd_from != ''){
				$where .= "  AND NOHIN_BI >= '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}
			//バッチ№
			if ($BatchNo_from != ''){
				$where .= " AND BATCH_NUMBER = '" . $BatchNo_from . "'" ;
			}
			//方面
			if ($homen != ''){
				$where .= " AND HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}
			$where .= ";";
			$this->execWithPDOLog($pdo, $sql . $where, 'WPO100  店舗仕分データ削除');

			$sql = "UPDATE T_OZAX_ANDROID_TENPO_SIWAKE SET NOHIN_BI = DATE_ADD(NOHIN_BI, INTERVAL 50 YEAR)";
			$this->execWithPDOLog($pdo, $sql . $where , 'WPO100  店舗仕分Androidデータ削除');

			$pdo->commit();
			$retText = "処理が正常に完了しました。";
			$pdo = null;

		} catch (Exception $e) {
			$pdo->rollBack();
			$this->printLog("fatal", "例外発生", "WPO100", $e->getMessage());
			$retText = $e->getMessage();
			$pdo = null;
		}


		return $retText;
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
			$sql = "CALL P_TPM070_GET_TIMESTAMP(";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " @timestamp";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, 'トータルピッキング進捗　タイムスタンプ取得');

			$sql = "";
			$sql .= " SELECT";
			$sql .= "  @timestamp";


			$this->queryWithPDOLog($stmt,$pdo,$sql, 'トータルピッキング進捗　タイムスタンプ取得');
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

			$this->printLog("fatal", "例外発生", "WPO100", $e->getMessage());
			$pdo = null;
		}

		return date("Ymd",strtotime("-1 day"));;

	}

}

?>