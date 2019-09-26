<?php
/**
 * WPO050Model
 *
 * 日時締更新
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class WPO050Model extends DCMSAppModel{

	public $name = 'WPO050Model';
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
	 * データ取得
	 * @access   public
	 * @return   工程管理データ
	 */
	public function getData() {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql  = "SELECT ";
			$sql .= "   A.DT_EXEC,";
			$sql .= "   A.EXEC_STAFF_CD,";
			$sql .= "   B.STAFF_NM,";
			$sql .= "   A.YMD_SIME,";
			$sql .= "   C.MEI_1 AS RESULT_NM,";
			$sql .= "   A.RESULT,";
			$sql .= "   A.STAFF_CNT";
			$sql .= " FROM";
			$sql .= "   T_SIME_PROC_RIREKI A";
			$sql .= "     LEFT JOIN M_STAFF_KIHON B ON";
			$sql .= "       A.EXEC_STAFF_CD = B.STAFF_CD";
			$sql .= "     LEFT JOIN M_MEISYO C ON";
			$sql .= "       A.NINUSI_CD = C.NINUSI_CD AND";
			$sql .= "       A.SOSIKI_CD = C.SOSIKI_CD AND";
			$sql .= "       A.RESULT = C.MEI_CD AND";
			$sql .= "       C.MEI_KBN = '26'";
			$sql .= " WHERE";
			$sql .= "   A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   A.SUB_SYSTEM_ID='WPO'";
			$sql .= " ORDER BY";
			$sql .= "  A.DT_EXEC DESC";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"締処理履歴情報　取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
				    "DT_EXEC" => $result['DT_EXEC'],
					"EXEC_STAFF_CD" => $result['EXEC_STAFF_CD'],
					"STAFF_NM" => $result['STAFF_NM'],
					"YMD_SIME" => $result['YMD_SIME'],
					"RESULT_NM" => $result['RESULT_NM'],
					"RESULT" => $result['RESULT'],
					"STAFF_CNT" => $result['STAFF_CNT'],
				);

				$arrayList[] = $array;
			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO050", $e->getMessage());
			$pdo = null;
		}


		return array();
	}

	/**
	 * データ更新
	 * @access   public
	 * @return   工程管理データ
	 */
	public function setData($date,$staff_cd,$updateFlg) {

		$pdo = null;
		$pdo2 = null;

		try{

			$tableData = null;

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WPO',";
			$sql .= "'050',";
			$sql .= "1";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'WPO050　排他制御オン');

			$pdo->beginTransaction();

			//就業開始基準時間取得
			$sql  = "SELECT ";
			$sql .= "   DT_SYUGYO_ST_BASE";
			$sql .= " FROM";
			$sql .= "   M_SYSTEM";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "';";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業開始基準時間　取得");

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$dt_syugyo_st_base = $result['DT_SYUGYO_ST_BASE'];

			//日付取得
			$year = substr($date, 0,4);
			$month = substr($date, 4,2);
			$day = substr($date, 6,2);

			$ymd_sime = $year . "-" . $month . "-" . $day;

			$tmpDate = $year . "-" . $month . "-" . $day . " " . $dt_syugyo_st_base;
			$dt = new DateTime($tmpDate);
			$date1 = $dt->format('Y-m-d H:i:s');
			$dt->add(new DateInterval("P1D"));
			$date2 = $dt->format('Y-m-d H:i:s');

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
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(A.DT_KOTEI_ST), concat(24 * DATEDIFF(DATE(A.DT_KOTEI_ST), A.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(A.DT_KOTEI_ST)";
			$sql .= "     END AS DT_KOTEI_ST_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(A.DT_KOTEI_ED), A.YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(A.DT_KOTEI_ED), concat(24 * DATEDIFF(DATE(A.DT_KOTEI_ED), A.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE TIME(A.DT_KOTEI_ED)";
			$sql .= "     END AS DT_KOTEI_ED_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(B.DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
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
			$sql .= "   A.DT_KOTEI_ST>='" . $date1 . "' AND";
			$sql .= "   A.DT_KOTEI_ED<='" .  $date2 . "'";

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
			$sql .= "         TIME_FORMAT(ADDTIME(TIME('" . $date1 ."'), concat(24 * DATEDIFF(DATE('" . $date1 ."'), A.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME('" . $date1 ."')";
			$sql .= "     END AS DT_KOTEI_ST_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(A.DT_KOTEI_ED), A.YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(A.DT_KOTEI_ED), concat(24 * DATEDIFF(DATE(A.DT_KOTEI_ED), A.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE TIME(A.DT_KOTEI_ED)";
			$sql .= "     END AS DT_KOTEI_ED_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(B.DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
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
			$sql .= "   A.DT_KOTEI_ST<'" . $date1 . "' AND";
			$sql .= "   (";
			$sql .= "     A.DT_KOTEI_ED>='" . $date1 . "' AND";
			$sql .= "     A.DT_KOTEI_ED<='" .  $date2 . "'";
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
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(A.DT_KOTEI_ST), concat(24 * DATEDIFF(DATE(A.DT_KOTEI_ST), A.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(A.DT_KOTEI_ST)";
			$sql .= "     END AS DT_KOTEI_ST_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME('" . $date2 . "'), concat(24 * DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE TIME('" . $date2 . "')";
			$sql .= "     END AS DT_KOTEI_ED_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(B.DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
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
			$sql .= "   (";
			$sql .= "     A.DT_KOTEI_ED>'" . $date2 . "' OR";
			$sql .= "     A.DT_KOTEI_ED IS NULL";
			$sql .= "   )";
			$sql .= "   AND";
			$sql .= "   (";
			$sql .= "     A.DT_KOTEI_ST>='" . $date1 . "' AND";
			$sql .= "     A.DT_KOTEI_ST<='" .  $date2 . "'";
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
			$sql .= "         TIME_FORMAT(ADDTIME(TIME('" . $date1 ."'), concat(24 * DATEDIFF(DATE('" . $date1 ."'), A.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME('" . $date1 ."')";
			$sql .= "     END AS DT_KOTEI_ST_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME('" . $date2 . "'), concat(24 * DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE TIME('" . $date2 . "')";
			$sql .= "     END AS DT_KOTEI_ED_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(B.DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
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
			$sql .= "   A.DT_KOTEI_ST<'" . $date1 . "' AND";
			$sql .= "   (";
			$sql .= "     A.DT_KOTEI_ED>'" . $date2 . "' OR";
			$sql .= "     A.DT_KOTEI_ED IS NULL";
			$sql .= "   )";


			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業実績明細情報・日またがり　取得");

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

			$jissekiMArray = array_merge($arrayList1,$arrayList2,$arrayList3,$arrayList4);
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

			//2. 就業実績ヘッダ情報取得

			//2_1. 時間範囲内
			$sql  = "SELECT ";
			$sql .= "   NINUSI_CD,";
			$sql .= "   SOSIKI_CD,";
			$sql .= "   YMD_SYUGYO,";
			$sql .= "   STAFF_CD,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(DT_SYUGYO_ST)";
			$sql .= "     END AS DT_SYUGYO_ST_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(DT_SYUGYO_ED), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(DT_SYUGYO_ED), concat(24 * DATEDIFF(DATE(DT_SYUGYO_ED), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(DT_SYUGYO_ED)";
			$sql .= "     END AS DT_SYUGYO_ED_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(DT_SYUGYO_ST)";
			$sql .= "     END AS DT_SYUGYO_ST";
			$sql .= " FROM";
			$sql .= "   T_KOTEI_H";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   DT_SYUGYO_ST>='" . $date1 . "' AND";
			$sql .= "   DT_SYUGYO_ED<='" .  $date2 . "'";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業実績ヘッダ情報・時間範囲内　取得");

			$arrayList1 = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
				    "NINUSI_CD" => $result['NINUSI_CD'],
					"SOSIKI_CD" => $result['SOSIKI_CD'],
					"YMD_SYUGYO" => $result['YMD_SYUGYO'],
					"STAFF_CD" => $result['STAFF_CD'],
					"DT_SYUGYO_ST_H" => $result['DT_SYUGYO_ST_H'],
					"DT_SYUGYO_ED_H" => $result['DT_SYUGYO_ED_H'],
					"DT_SYUGYO_ST" => $result['DT_SYUGYO_ST']
				);

				$arrayList1[] = $array;
			}

			//2_2. 開始時間またがり
			$sql  = "SELECT ";
			$sql .= "   NINUSI_CD,";
			$sql .= "   SOSIKI_CD,";
			$sql .= "   YMD_SYUGYO,";
			$sql .= "   STAFF_CD,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE('" . $date1 ."'), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME('" . $date1 ."'), concat(24 * DATEDIFF(DATE('" . $date1 ."'), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME('" . $date1 ."')";
			$sql .= "     END AS DT_SYUGYO_ST_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(DT_SYUGYO_ED), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(DT_SYUGYO_ED), concat(24 * DATEDIFF(DATE(DT_SYUGYO_ED), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(DT_SYUGYO_ED)";
			$sql .= "     END AS DT_SYUGYO_ED_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(DT_SYUGYO_ST)";
			$sql .= "     END AS DT_SYUGYO_ST";
			$sql .= " FROM";
			$sql .= "   T_KOTEI_H";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   DT_SYUGYO_ST<'" . $date1 . "' AND";
			$sql .= "   (";
			$sql .= "     DT_SYUGYO_ED>='" . $date1 . "' AND";
			$sql .= "     DT_SYUGYO_ED<='" .  $date2 . "'";
			$sql .= "   )";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業実績ヘッダ情報・開始時間またがり　取得");

			$arrayList2 = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
				    "NINUSI_CD" => $result['NINUSI_CD'],
					"SOSIKI_CD" => $result['SOSIKI_CD'],
					"YMD_SYUGYO" => $result['YMD_SYUGYO'],
					"STAFF_CD" => $result['STAFF_CD'],
					"DT_SYUGYO_ST_H" => $result['DT_SYUGYO_ST_H'],
					"DT_SYUGYO_ED_H" => $result['DT_SYUGYO_ED_H'],
					"DT_SYUGYO_ST" => $result['DT_SYUGYO_ST']
				);

				$arrayList2[] = $array;
			}

			//2_3. 終了時間またがり
			$sql  = "SELECT ";
			$sql .= "   NINUSI_CD,";
			$sql .= "   SOSIKI_CD,";
			$sql .= "   YMD_SYUGYO,";
			$sql .= "   STAFF_CD,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(DT_SYUGYO_ST)";
			$sql .= "   END AS DT_SYUGYO_ST_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE('" . $date2 . "'), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME('" . $date2 . "'), concat(24 * DATEDIFF(DATE('" . $date2 . "'), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE TIME('" . $date2 . "')";
			$sql .= "     END AS DT_SYUGYO_ED_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(DT_SYUGYO_ST)";
			$sql .= "   END AS DT_SYUGYO_ST";
			$sql .= " FROM";
			$sql .= "   T_KOTEI_H";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   (";
			$sql .= "     DT_SYUGYO_ED>'" . $date2 . "' OR";
			$sql .= "     DT_SYUGYO_ED IS NULL";
			$sql .= "   )";
			$sql .= "   AND";
			$sql .= "   (";
			$sql .= "     DT_SYUGYO_ST>='" . $date1 . "' AND";
			$sql .= "     DT_SYUGYO_ST<='" .  $date2 . "'";
			$sql .= "   )";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業実績ヘッダ情報・終了時間またがり　取得");

			$arrayList3 = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
				    "NINUSI_CD" => $result['NINUSI_CD'],
					"SOSIKI_CD" => $result['SOSIKI_CD'],
					"YMD_SYUGYO" => $result['YMD_SYUGYO'],
					"STAFF_CD" => $result['STAFF_CD'],
					"DT_SYUGYO_ST_H" => $result['DT_SYUGYO_ST_H'],
					"DT_SYUGYO_ED_H" => $result['DT_SYUGYO_ED_H'],
					"DT_SYUGYO_ST" => $result['DT_SYUGYO_ST']
				);

				$arrayList3[] = $array;
			}

			//2_4. 日またがり
			$sql  = "SELECT ";
			$sql .= "   NINUSI_CD,";
			$sql .= "   SOSIKI_CD,";
			$sql .= "   YMD_SYUGYO,";
			$sql .= "   STAFF_CD,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE('" . $date1 ."'), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME('" . $date1 ."'), concat(24 * DATEDIFF(DATE('" . $date1 ."'), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME('" . $date1 ."')";
			$sql .= "     END AS DT_SYUGYO_ST_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE('" . $date2 . "'), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME('" . $date2 . "'), concat(24 * DATEDIFF(DATE('" . $date2 . "'), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE TIME('" . $date2 . "')";
			$sql .= "     END AS DT_SYUGYO_ED_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(DT_SYUGYO_ST)";
			$sql .= "   END AS DT_SYUGYO_ST";
			$sql .= " FROM";
			$sql .= "   T_KOTEI_H";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   DT_SYUGYO_ST<'" . $date1 . "' AND";
			$sql .= "   (";
			$sql .= "     DT_SYUGYO_ED>'" . $date2 . "' OR";
			$sql .= "     DT_SYUGYO_ED IS NULL";
			$sql .= "   )";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業実績ヘッダ情報・日またがり　取得");

			$arrayList4 = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
				    "NINUSI_CD" => $result['NINUSI_CD'],
					"SOSIKI_CD" => $result['SOSIKI_CD'],
					"YMD_SYUGYO" => $result['YMD_SYUGYO'],
					"STAFF_CD" => $result['STAFF_CD'],
					"DT_SYUGYO_ST_H" => $result['DT_SYUGYO_ST_H'],
					"DT_SYUGYO_ED_H" => $result['DT_SYUGYO_ED_H'],
					"DT_SYUGYO_ST" => $result['DT_SYUGYO_ST']
				);

				$arrayList4[] = $array;
			}

			$jissekiHArray = array_merge($arrayList1,$arrayList2,$arrayList3,$arrayList4);
			$NINUSI_CD = array();
			$SOSIKI_CD =  array();
			$YMD_SYUGYO =  array();
			$STAFF_CD =  array();
			$DT_SYUGYO_ST =  array();
			$DT_SYUGYO_ST_H =  array();

			foreach ($jissekiHArray as $key => $row) {
			    $NINUSI_CD[$key]  = $row['NINUSI_CD'];
			    $SOSIKI_CD[$key]  = $row['SOSIKI_CD'];
			    $YMD_SYUGYO[$key]  = $row['YMD_SYUGYO'];
			    $STAFF_CD[$key]  = $row['STAFF_CD'];
			    $DT_SYUGYO_ST[$key]  = $row['DT_SYUGYO_ST'];
			    $DT_SYUGYO_ST_H[$key]  = $row['DT_SYUGYO_ST_H'];
			}

			array_multisort($NINUSI_CD,SORT_ASC,
							$SOSIKI_CD,SORT_ASC,
							$YMD_SYUGYO,SORT_ASC,
							$STAFF_CD,SORT_ASC,
							$DT_SYUGYO_ST,SORT_ASC,
							$DT_SYUGYO_ST_H,SORT_ASC,
							$jissekiHArray);

			$kadoCostArray = $this->MCommon->getKadoCost($ymd_sime,$jissekiMArray,$jissekiHArray);

			//コスト算出データ更新処理

			//コスト算出データ削除
			$sql = "CALL P_WPO050_DELETE_CALC_DATA(";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "@return_cd";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, 'コスト算出データ　削除');

			$sql = "SELECT";
			$sql .= " @return_cd";

			$this->queryWithPDOLog($stmt,$pdo,$sql, 'コスト算出データ　削除');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];

			if ($return_cd == "1") {

				throw new Exception('コスト算出データ削除　例外発生');
			}

			//コスト算出データ登録
			foreach($kadoCostArray as $array) {

				$sql = "CALL P_WPO050_SET_CALC_DATA(";
				$sql .= "'" . $staff_cd . "',";
				$sql .= "'" . $array['NINUSI_CD'] . "',";
				$sql .= "'" . $array['SOSIKI_CD'] . "',";
				$sql .= "'" . $array['YMD_SIME'] . "',";
				$sql .= "'" . $array['STAFF_CD'] . "',";
				$sql .= "'" . $array['YMD_SYUGYO'] . "',";
				$sql .= "'" . $array['BNRI_DAI_CD'] . "',";
				$sql .= "'" . $array['BNRI_CYU_CD'] . "',";
				$sql .= "'" . $array['BNRI_SAI_CD'] . "',";
				$sql .= "'" . $array['TIME_KOTEI_ST'] . "',";
				$sql .= ($array['TIME_KOTEI_ED'] != "" ? "'" . $array['TIME_KOTEI_ED'] . "'," : "NULL,");
				$sql .= ($array['TIME_KOTEI_SYUGYO'] != "" ?  $array['TIME_KOTEI_SYUGYO'] . "," : "0,");
				$sql .= ($array['TIME_SYUGYO_ST'] != "" ? "'" . $array['TIME_SYUGYO_ST'] . "'," : "NULL,");
				$sql .= ($array['TIME_SYUGYO_ED'] != "" ? "'" . $array['TIME_SYUGYO_ED'] . "'," : "NULL,");
				$sql .= ($array['TIME_SNY_TEKIYO_ST'] != "" ? "'" . $array['TIME_SNY_TEKIYO_ST'] . "'," : "NULL,");
				$sql .= ($array['TIME_SNY_TEKIYO_ED'] != "" ? "'" . $array['TIME_SNY_TEKIYO_ED'] . "'," : "NULL,");
				$sql .= ($array['KIN_SYOTEI'] != "" ?  $array['KIN_SYOTEI'] . "," : "0,");
				$sql .= ($array['KIN_JIKANGAI'] != "" ?  $array['KIN_JIKANGAI'] . "," : "0,");
				$sql .= ($array['KIN_SNY'] != "" ?  $array['KIN_SNY'] . "," : "0,");
				$sql .= ($array['KIN_KST'] != "" ?  $array['KIN_KST'] . "," : "0,");
				$sql .= ($array['KIN_H_KST'] != "" ?  $array['KIN_H_KST'] . "," : "0,");
				$sql .= ($array['KIN_H_KST_SNY'] != "" ?  $array['KIN_H_KST_SNY'] . "," : "0,");
				$sql .= ($array['TIME_SYOTEI_ST'] != "" ? "'" . $array['TIME_SYOTEI_ST'] . "'," : "NULL,");
				$sql .= ($array['TIME_SYOTEI_ED'] != "" ? "'" . $array['TIME_SYOTEI_ED'] . "'," : "NULL,");
				$sql .= ($array['TIME_JIKANGAI_ST'] != "" ? "'" . $array['TIME_JIKANGAI_ST'] . "'," : "NULL,");
				$sql .= ($array['TIME_JIKANGAI_ED'] != "" ? "'" . $array['TIME_JIKANGAI_ED'] . "'," : "NULL,");
				$sql .= ($array['TIME_KST_ST'] != "" ? "'" . $array['TIME_KST_ST'] . "'," : "NULL,");
				$sql .= ($array['TIME_KST_ED'] != "" ? "'" . $array['TIME_KST_ED'] . "'," : "NULL,");
				$sql .= ($array['TIME_H_KST_ST'] != "" ? "'" . $array['TIME_H_KST_ST'] . "'," : "NULL,");
				$sql .= ($array['TIME_H_KST_ED'] != "" ? "'" . $array['TIME_H_KST_ED'] . "'," : "NULL,");
				$sql .= ($array['TIME_CNT_SYOTEI'] != "" ?  $array['TIME_CNT_SYOTEI'] . "," : "0,");
				$sql .= ($array['TIME_CNT_JIKANGAI'] != "" ?  $array['TIME_CNT_JIKANGAI'] . "," : "0,");
				$sql .= ($array['TIME_CNT_SNY'] != "" ?  $array['TIME_CNT_SNY'] . "," : "0,");
				$sql .= ($array['TIME_CNT_KST'] != "" ?  $array['TIME_CNT_KST'] . "," : "0,");
				$sql .= ($array['TIME_CNT_H_KST'] != "" ?  $array['TIME_CNT_H_KST'] . "," : "0,");
				$sql .= ($array['TIME_CNT_H_KST_SNY'] != "" ?  $array['TIME_CNT_H_KST_SNY'] . "," : "0,");
				$sql .= ($array['COST_SYOTEI'] != "" ?  $array['COST_SYOTEI'] . "," : "0,");
				$sql .= ($array['COST_JIKANGAI'] != "" ?  $array['COST_JIKANGAI'] . "," : "0,");
				$sql .= ($array['COST_SNY'] != "" ?  $array['COST_SNY'] . "," : "0,");
				$sql .= ($array['COST_KST'] != "" ?  $array['COST_KST'] . "," : "0,");
				$sql .= ($array['COST_H_KST'] != "" ?  $array['COST_H_KST'] . "," : "0,");
				$sql .= ($array['COST_H_KST_SNY'] != "" ?  $array['COST_H_KST_SNY'] . "," : "0,");
				$sql .= ($array['COST'] != "" ?  $array['COST'] . "," : "0,");
				$sql .= ($array['TNK'] != "" ?  $array['TNK'] . "," : "0,");
				$sql .= "@return_cd";
				$sql .= ")";

				$this->execWithPDOLog($pdo,$sql, 'コスト算出データ　追加');

				$sql = "SELECT";
				$sql .= " @return_cd";

				$this->queryWithPDOLog($stmt,$pdo,$sql, 'コスト算出データ　追加');

				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$return_cd = $result["@return_cd"];

				if ($return_cd == "1") {

					throw new Exception('コスト算出データ追加　例外発生');
				}
			}



			//ABCデータベーススタッフ別情報作成
			$sql = "CALL P_WPO050_SET_ABC_DB_STAFF(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "'" . $date1 . "',";
			$sql .= "'" . $date2 . "',";
			$sql .= "@return_cd";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, 'ABCデータベーススタッフ別　登録');

			$sql = "SELECT";
			$sql .= " @return_cd";

			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ABCデータベーススタッフ別　登録');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];

			if ($return_cd == "1") {

				throw new Exception('ABCデータベーススタッフ別登録　例外発生');
			}

			//ABCデーターベース日別情報作成
			$sql = "CALL P_WPO050_SET_ABC_DB_HIBETU(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "@return_cd";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, 'ABCデータベース日別　登録');

			$sql = "SELECT";
			$sql .= " @return_cd";

			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ABCデータベース日別　登録');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];

			if ($return_cd == "1") {

				throw new Exception('ABCデータベース日別登録　例外発生');
			}

			//就業実績日別情報
			$sql = "CALL P_WPO050_SET_STAFF_SYUGYO(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "'" . $date1 . "',";
			$sql .= "'" . $date2 . "',";
			$sql .= "@return_cd";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, '就業実績情報　登録');

			$sql = "SELECT";
			$sql .= " @return_cd";

			$this->queryWithPDOLog($stmt,$pdo,$sql, '就業実績情報　登録');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];

			if ($return_cd == "1") {

				throw new Exception('就業実績情報登録　例外発生');
			}

			//就業実績情報展開処理呼び出し
			$sql = "CALL P_WPO050_SET_TENKAI(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "@return_cd";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, '就業実績情報展開処理　登録');

			$sql = "SELECT";
			$sql .= " @return_cd";

			$this->queryWithPDOLog($stmt,$pdo,$sql, '就業実績情報展開処理　登録');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];

			if ($return_cd == "1") {

				throw new Exception('就業実績情報展開処理登録　例外発生');
			}

			//スタッフ就業実績情報順位設定呼び出し
			$sql = "CALL P_WPO050_SET_RANK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "@return_cd";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, '就業実績情報順位設定　登録');

			$sql = "SELECT";
			$sql .= " @return_cd";

			$this->queryWithPDOLog($stmt,$pdo,$sql, '就業実績情報順位設定　登録');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];

			if ($return_cd == "1") {

				throw new Exception('就業情報順位設定登録　例外発生');
			}

			//スタッフランク設定
			$sql = "CALL P_WPO050_SET_STAFF_RANK(";
			$sql .= " '" . $staff_cd . "',";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " @return";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, 'スタッフランク設定　更新');

			$sql = "SELECT";
			$sql .= " @return_cd";

			$this->queryWithPDOLog($stmt,$pdo,$sql, 'スタッフランク設定　更新');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];

			if ($return_cd == "1") {

				throw new Exception('スタッフランク設定　例外発生');
			}

			$pdo->commit();

			//排他処理
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WPO',";
			$sql .= "'050',";
			$sql .= "0";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'WPO050　排他制御オフ');

			$pdo = null;


		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO050", $e->getMessage());
			$updateFlg = "2";
			$pdo->rollBack();

			//排他処理
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WPO',";
			$sql .= "'050',";
			$sql .= "0";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'WPO050　排他制御オフ');

			$pdo = null;
		}

		try {

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$pdo->beginTransaction();

			//締処理履歴情報登録
			$RESULT = "01";

			if ($updateFlg == "1") {

				$RESULT = "02";
			} else if ($updateFlg == "2") {

				$RESULT = "03";
			}

			$sql = "CALL P_WPO050_SET_RIREKI(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "'" . $RESULT . "',";
			$sql .= "@return_cd";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, '締処理履歴　登録');

			$sql = "SELECT";
			$sql .= " @return_cd";

			$this->queryWithPDOLog($stmt,$pdo,$sql, '締処理履歴　登録');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];

			if ($return_cd == "1") {

				throw new Exception('締処理履歴登録　例外発生');
			}

			$pdo->commit();
			$pdo = null;

			return true;
		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO050", $e->getMessage());

			$pdo->rollBack();
			$pdo = null;
		}

		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}

	/**
	 * 入力チェック・存在チェック
	 * @access   public
	 * @param    工程データ
	 * @return   成否情報(true,false)
	 */
	function checkData($date,$staff_cd) {


	 	try {


			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



			//データがない
			if ($date == "") {

				$message = $this->MMessage->getOneMessage('WPOE050001');
				$this->errors['Check'][] =  $message;
				return false;
			}

			//締日付のデータフォーマットが不正
			if (preg_match("/^[0-9]{8}$/",$date) == false) {

				$message = $this->MMessage->getOneMessage('WPOE050002');
				$this->errors['Check'][] =  $message;
				return false;
			}

			$year = substr($date, 0,4);
			$month = substr($date, 4,2);
			$day = substr($date, 6,2);

			//締日付が不正
			if (checkdate($month, $day, $year) == false) {

				$message = $this->MMessage->getOneMessage('WPOE050002');
				$this->errors['Check'][] =  $message;
				return false;
			}

			//当日以降過チェック

			$tmpDate = date("Y-m-d 00:00:00");
			$dt = new DateTime($tmpDate);
			$dt->add(new DateInterval("P1D"));
			$date = $dt->format('Y-m-d H:i:s');//明日

			if ((strtotime($year . "-" . $month . "-" . $day . " 00:00:00") >= strtotime($date))) {

				$message = $this->MMessage->getOneMessage('WPOE050003');
				$this->errors['Check'][] =  $message;
				return false;

			}


			$sql  = "SELECT ";
			$sql .= "   DT_SYUGYO_ST_BASE";
			$sql .= " FROM";
			$sql .= "   M_SYSTEM";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "';";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業開始基準時間　取得");

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$dt_syugyo_st_base = $result['DT_SYUGYO_ST_BASE'];

			$tmpDate = $year . "-" . $month . "-" . $day . " " . $dt_syugyo_st_base;

			$dt = new DateTime($tmpDate);
			$date1 = $dt->format('Y-m-d H:i:s');
			$dt->add(new DateInterval("P1D"));
			$date2 = $dt->format('Y-m-d H:i:s');

			//存在チェック
			$sql  = "SELECT ";
			$sql .= "   COUNT(*) AS COUNT";
			$sql .= " FROM";
			$sql .= "   T_KOTEI_D";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   (";
			$sql .= "     (";
			$sql .= "       DT_KOTEI_ST>='" . $date1 . "' AND";
			$sql .= "       DT_KOTEI_ED<='" .  $date2 . "'";
			$sql .= "     )";
			$sql .= "     OR";
			$sql .= "     (";
			$sql .= "       DT_KOTEI_ST<'" . $date1 . "' AND";
			$sql .= "       (";
			$sql .= "         DT_KOTEI_ED>='" . $date1 . "' AND";
			$sql .= "         DT_KOTEI_ED<='" .  $date2 . "'";
			$sql .= "       )";
			$sql .= "     )";
			$sql .= "     OR";
			$sql .= "     (";
			$sql .= "       (";
			$sql .= "         DT_KOTEI_ED>'" . $date2 . "' OR";
			$sql .= "         DT_KOTEI_ED IS NULL";
			$sql .= "       )";
			$sql .= "       AND";
			$sql .= "       (";
			$sql .= "         DT_KOTEI_ST>='" . $date1 . "' AND";
			$sql .= "         DT_KOTEI_ST<='" .  $date2 . "'";
			$sql .= "       )";
			$sql .= "     )";
			$sql .= "     OR";
			$sql .= "     (";
			$sql .= "       DT_KOTEI_ST<'" . $date1 . "' AND";
			$sql .= "       (";
			$sql .= "         DT_KOTEI_ED>'" . $date2 . "' OR";
			$sql .= "         DT_KOTEI_ED IS NULL";
			$sql .= "       )";
			$sql .= "     )";
			$sql .= "   )";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"工程明細　存在チェック");

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$countKOTEI = $result['COUNT'];

			//存在してない場合エラー
			if ($countKOTEI == 0) {

				$message = $this->MMessage->getOneMessage('WPOE050004');
				$this->errors['Check'][] =  $message;
				return false;
			}

			$pdo = null;


			return true;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO050", $e->getMessage());
			$pdo = null;
		}

		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;

	}

	/**
	 * 警告チェック
	 * @access   public
	 * @param    データ
	 * @param    スタッフCD
	 * @return   成否情報(true,false)
	 */
	function checkData2($date,$staff_cd) {


	 	try {


			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//就業ヘッダ
			$sql  = "SELECT ";
			$sql .= "   A.STAFF_CD AS STAFF_CD,";
			$sql .= "   B.STAFF_NM AS STAFF_NM,";
			$sql .= "   A.YMD_SYUGYO AS YMD_SYUGYO";
			$sql .= " FROM";
			$sql .= "   T_KOTEI_H A";
			$sql .= "     LEFT JOIN M_STAFF_KIHON B ON";
			$sql .= "       A.STAFF_CD=B.STAFF_CD";
			$sql .= " WHERE";
			$sql .= "   A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   A.YMD_SYUGYO='" . $date . "' AND";
			$sql .= "   A.DT_SYUGYO_ED IS NULL";
			$sql .= " ORDER BY";
			$sql .= "   A.YMD_SYUGYO,";
			$sql .= "   A.STAFF_CD";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"工程管理情報　取得");

			//就業ヘッダーエラー
			$WARN_ARRAY1 = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				//警告
				//就業終了していない
				$ymd_syugyo = $result["YMD_SYUGYO"];

				$year = substr($ymd_syugyo, 0,4);
				$month = substr($ymd_syugyo, 5,2);
				$day = substr($ymd_syugyo, 8,2);

				$array = array(
					"YMD_SYUGYO" => $year . "年" . (int)$month . "月" . (int)$day . "日",
					"STAFF_NM" => $result["STAFF_NM"]
				);

				$WARN_ARRAY1[] = $array;

			}

			//就業ヘッダ
			$sql  = "SELECT ";
			$sql .= "   B.STAFF_CD AS STAFF_CD,";
			$sql .= "   C.STAFF_NM AS STAFF_NM,";
			$sql .= "   B.YMD_SYUGYO AS YMD_SYUGYO,";
			$sql .= "   B.KOTEI_NO AS KOTEI_NO,";
			$sql .= "   B.BNRI_DAI_CD AS BNRI_DAI_CD,";
			$sql .= "   B.BNRI_CYU_CD AS BNRI_CYU_CD,";
			$sql .= "   B.BNRI_SAI_CD AS BNRI_SAI_CD,";
			$sql .= "   D.BNRI_DAI_RYAKU AS BNRI_DAI_RYAKU,";
			$sql .= "   E.BNRI_CYU_RYAKU AS BNRI_CYU_RYAKU,";
			$sql .= "   F.BNRI_SAI_RYAKU AS BNRI_SAI_RYAKU";
			$sql .= " FROM";
			$sql .= "   T_KOTEI_D B";
			$sql .= "     LEFT JOIN M_STAFF_KIHON C ON";
			$sql .= "       B.STAFF_CD=C.STAFF_CD";
			$sql .= "     LEFT JOIN M_BNRI_DAI D ON";
			$sql .= "       B.NINUSI_CD=D.NINUSI_CD AND";
			$sql .= "       B.SOSIKI_CD=D.SOSIKI_CD AND";
			$sql .= "       B.BNRI_DAI_CD=D.BNRI_DAI_CD";
			$sql .= "     LEFT JOIN M_BNRI_CYU E ON";
			$sql .= "       B.NINUSI_CD=E.NINUSI_CD AND";
			$sql .= "       B.SOSIKI_CD=E.SOSIKI_CD AND";
			$sql .= "       B.BNRI_DAI_CD=E.BNRI_DAI_CD AND";
			$sql .= "       B.BNRI_CYU_CD=E.BNRI_CYU_CD";
			$sql .= "     LEFT JOIN M_BNRI_SAI F ON";
			$sql .= "       B.NINUSI_CD=F.NINUSI_CD AND";
			$sql .= "       B.SOSIKI_CD=F.SOSIKI_CD AND";
			$sql .= "       B.BNRI_DAI_CD=F.BNRI_DAI_CD AND";
			$sql .= "       B.BNRI_CYU_CD=F.BNRI_CYU_CD AND";
			$sql .= "       B.BNRI_SAI_CD=F.BNRI_SAI_CD";
			$sql .= " WHERE";
			$sql .= "   B.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   B.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   B.YMD_SYUGYO='" . $date . "' AND";
			$sql .= "   B.DT_KOTEI_ED IS NULL";
			$sql .= " ORDER BY";
			$sql .= "   B.YMD_SYUGYO,";
			$sql .= "   B.STAFF_CD,";
			$sql .= "   B.KOTEI_NO";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"工程管理情報　取得");

			//就業明細
			$WARN_ARRAY2 = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				//警告
				//工程終了していない
				$ymd_syugyo = $result["YMD_SYUGYO"];

				$year = substr($ymd_syugyo, 0,4);
				$month = substr($ymd_syugyo, 5,2);
				$day = substr($ymd_syugyo, 8,2);

				$array = array(
					"YMD_SYUGYO" => $year . "年" . (int)$month . "月" . (int)$day . "日",
					"STAFF_NM" => $result["STAFF_NM"],
			    	"BNRI_DAI_RYAKU" => $result["BNRI_DAI_RYAKU"],
			    	"BNRI_CYU_RYAKU" => $result["BNRI_CYU_RYAKU"],
			    	"BNRI_SAI_RYAKU" => $result["BNRI_SAI_RYAKU"]
			    );

			    $WARN_ARRAY2[] = $array;


			}

			//警告
			$message = "";

			if (count($WARN_ARRAY1) > 0 ||
				count($WARN_ARRAY2) > 0 ) {

				$message1 = "";

				if (count($WARN_ARRAY1) > 0){


					foreach($WARN_ARRAY1 as $array) {

						$message1 .= "    就業日付：[" . $array["YMD_SYUGYO"] . "]" .
							   		 "  スタッフ名：[" . $array["STAFF_NM"] . "]\n";
					}

				} else {
					$message1 .= "    なし\n";
				}

				$message2 = "";


				if (count($WARN_ARRAY2) > 0){

					foreach($WARN_ARRAY2 as $array) {

						$message2 .=  "    就業日付：[" . $array["YMD_SYUGYO"] . "]" .
							   		  "  スタッフ名：[" . $array["STAFF_NM"] . "]" .
							   		  "  大分類：[" . $array["BNRI_DAI_RYAKU"] . "]" .
							   		  "  中分類：[" . $array["BNRI_CYU_RYAKU"] . "]" .
							   		  "  細分類：[" . $array["BNRI_SAI_RYAKU"] . "]\n";
					}

				} else {
					$message2 .= "    なし\n";
				}

				$message = vsprintf($this->MMessage->getOneMessage('WPOQ050002'),array($message1,$message2));
			}


			$pdo = null;


			return $message;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO050", $e->getMessage());
			$pdo = null;
		}

		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;

	}


	/**
	 * 上書きチェックチェック
	 * @access   public
	 * @param    データ
	 * @return   成否情報(true,false)
	 */
	function checkData3($date,$staff_cd) {


	 	try {


			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql  = "SELECT ";
			$sql .= "   COUNT(*) AS COUNT";
			$sql .= " FROM";
			$sql .= "   T_SIME_CALC_DATA_WPO_COST";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   YMD_SIME='" . $date . "'";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"既存上書き確認　取得");

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$count = $result['COUNT'];

			$pdo = null;

			//存在してない場合エラー
			if ($count == 0) {

				return "0_0";
			} else {

				return "0_1";
			}

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO050", $e->getMessage());
			$pdo = null;
		}

		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;

	}

	/**
	 * 排他チェック
	 * @access   public
	 * @param    データ
	 * @param    スタッフコード
	 * @return   成否情報(true,false)
	 */
	function checkData4($date,$staff_cd) {


	 	try {


			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//排他チェック
			$sql = "CALL P_GET_LOCK(";
			$sql .= " '" . $staff_cd . "',";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " 'WPO',";
			$sql .= " '040',";
			$sql .= " @return";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, 'WPO040　排他確認');

			$sql = "";
			$sql .= "SELECT";
			$sql .= " @return";


			$this->queryWithPDOLog($stmt,$pdo,$sql, 'WPO040　排他確認');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			if ($result['@return'] != "0") {

				$message = vsprintf($this->MMessage->getOneMessage('WPOE050005'), array("工程管理"));
				$this->errors['Check'][] =  $message;
				return false;
			}


			//排他チェック
			$sql = "CALL P_GET_LOCK(";
			$sql .= " '" . $staff_cd . "',";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " 'WEX',";
			$sql .= " '070',";
			$sql .= " @return";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, 'WEX070　排他確認');

			$sql = "";
			$sql .= "SELECT";
			$sql .= " @return";


			$this->queryWithPDOLog($stmt,$pdo,$sql, 'WEX070　排他確認');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);


			if ($result['@return'] != "0") {

				$message = vsprintf($this->MMessage->getOneMessage('WPOE050005'), array("ABCデータベース登録"));
				$this->errors['Check'][] =  $message;
				return false;
			}

			//排他チェック
			$sql = "CALL P_GET_LOCK(";
			$sql .= " '" . $staff_cd . "',";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " 'WEX',";
			$sql .= " '080',";
			$sql .= " @return";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, 'WEX080　排他確認');

			$sql = "";
			$sql .= "SELECT";
			$sql .= " @return";


			$this->queryWithPDOLog($stmt,$pdo,$sql, 'WEX080　排他確認');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			if ($result['@return'] != "0") {

				$message = vsprintf($this->MMessage->getOneMessage('WPOE050005'), array("作業実績日次締更新"));
				$this->errors['Check'][] =  $message;
				return false;
			}

			//排他チェック
			$sql = "CALL P_GET_LOCK(";
			$sql .= " '" . $staff_cd . "',";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " 'WPO',";
			$sql .= " '050',";
			$sql .= " @return";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, 'WPO050　排他確認');

			$sql = "";
			$sql .= "SELECT";
			$sql .= " @return";


			$this->queryWithPDOLog($stmt,$pdo,$sql, 'WPO050　排他確認');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			if ($result['@return'] != "0") {

				$message = vsprintf($this->MMessage->getOneMessage('WPOE050005'), array("作業指示日次締更新"));
				$this->errors['Check'][] =  $message;
				return false;
			}

			//排他チェック
			$sql = "CALL P_GET_LOCK(";
			$sql .= " '" . $staff_cd . "',";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " 'HRD',";
			$sql .= " '010',";
			$sql .= " @return";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, 'HRD010　排他確認');

			$sql = "";
			$sql .= "SELECT";
			$sql .= " @return";


			$this->queryWithPDOLog($stmt,$pdo,$sql, 'HRD010　排他確認');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			if ($result['@return'] != "0") {

				$message = vsprintf($this->MMessage->getOneMessage('WPOE050005'), array("人材登録・基本情報"));
				$this->errors['Check'][] =  $message;
				return false;
			}

			$pdo = null;


			return true;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO050", $e->getMessage());
			$pdo = null;
		}

		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;

	}

	/**
	 * 締日初期値取得
	 * @access   public
	 * @return   日付
	 */
	function getYesterday() {


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
			$dt->sub(new DateInterval("P1D"));
			$ymd_yesterday = $dt->format('Ymd');

			$pdo = null;

			return  $ymd_yesterday;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO050", $e->getMessage());
			$pdo = null;
		}

		return date("Ymd",strtotime("-1 day"));;

	}


	/**
	 * 締処理
	 * @access   public
	 * @return   true or false
	 */
	function setAutoShime($ninusi_cd = '' ,$sosiki_cd = '', $ymd_syori = '') {

		try {
			
			if ($ninusi_cd != '' && $sosiki_cd != '') {
				$this->_ninusi_cd = $ninusi_cd;
				$this->_sosiki_cd = $sosiki_cd;
				$this->setData2($ninusi_cd,$sosiki_cd,$ymd_syori);
				return true;
			}

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql  = "SELECT ";
			$sql .= "   SOSIKI_CD,";
			$sql .= "   NINUSI_CD";
			$sql .= " FROM";
			$sql .= "   M_AUTO_SHIME";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"日時締キー　取得");

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$sosiki_cd = $result['SOSIKI_CD'];
				$ninusi_cd = $result['NINUSI_CD'];

				$this->_ninusi_cd = $ninusi_cd;
				$this->_sosiki_cd = $sosiki_cd;
				
				if ($ymd_syori != '') {
					$this->setData2($ninusi_cd,$sosiki_cd,$ymd_syori);
				} else {
					$date = $this->getToday();
					$this->setData2($ninusi_cd,$sosiki_cd,$date);
					$date = $this->getYesterday();
					$this->setData2($ninusi_cd,$sosiki_cd,$date);
				}
				
				
			}

			$pdo = null;

			return true;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO050", $e->getMessage());
			$pdo = null;
		}

		return false;

	}

	/**
	 * データ更新
	 * @access   public
	 * @return   工程管理データ
	 */
	public function setData2($ninusi_cd,$sosiki_cd,$ymd_syori = '') {

		$pdo = null;
		$pdo2 = null;
		
		$date = $this->getToday();
		$staff_cd = "";

		if ($ymd_syori != '') {
			$date = $ymd_syori;
		}
		
		try{

			$tableData = null;

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $ninusi_cd . "',";
			$sql .= "'" . $sosiki_cd . "',";
			$sql .= "'WPO',";
			$sql .= "'050',";
			$sql .= "1";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'WPO050　排他制御オン');

			$pdo->beginTransaction();

			//就業開始基準時間取得
			$sql  = "SELECT ";
			$sql .= "   DT_SYUGYO_ST_BASE";
			$sql .= " FROM";
			$sql .= "   M_SYSTEM";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $sosiki_cd . "';";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業開始基準時間　取得");

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$dt_syugyo_st_base = $result['DT_SYUGYO_ST_BASE'];

			//日付取得
			$year = substr($date, 0,4);
			$month = substr($date, 4,2);
			$day = substr($date, 6,2);

			$ymd_sime = $year . "-" . $month . "-" . $day;

			$tmpDate = $year . "-" . $month . "-" . $day . " " . $dt_syugyo_st_base;
			$dt = new DateTime($tmpDate);
			$date1 = $dt->format('Y-m-d H:i:s');
			$dt->add(new DateInterval("P1D"));
			$date2 = $dt->format('Y-m-d H:i:s');

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
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(A.DT_KOTEI_ST), concat(24 * DATEDIFF(DATE(A.DT_KOTEI_ST), A.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(A.DT_KOTEI_ST)";
			$sql .= "     END AS DT_KOTEI_ST_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(A.DT_KOTEI_ED), A.YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(A.DT_KOTEI_ED), concat(24 * DATEDIFF(DATE(A.DT_KOTEI_ED), A.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE TIME(A.DT_KOTEI_ED)";
			$sql .= "     END AS DT_KOTEI_ED_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(B.DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
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
			$sql .= "   A.NINUSI_CD='" . $ninusi_cd . "' AND";
			$sql .= "   A.SOSIKI_CD='" . $sosiki_cd . "' AND";
			$sql .= "   A.DT_KOTEI_ST>='" . $date1 . "' AND";
			$sql .= "   A.DT_KOTEI_ED<='" .  $date2 . "'";

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
			$sql .= "         TIME_FORMAT(ADDTIME(TIME('" . $date1 ."'), concat(24 * DATEDIFF(DATE('" . $date1 ."'), A.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME('" . $date1 ."')";
			$sql .= "     END AS DT_KOTEI_ST_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(A.DT_KOTEI_ED), A.YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(A.DT_KOTEI_ED), concat(24 * DATEDIFF(DATE(A.DT_KOTEI_ED), A.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE TIME(A.DT_KOTEI_ED)";
			$sql .= "     END AS DT_KOTEI_ED_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(B.DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
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
			$sql .= "   A.NINUSI_CD='" . $ninusi_cd . "' AND";
			$sql .= "   A.SOSIKI_CD='" . $sosiki_cd . "' AND";
			$sql .= "   A.DT_KOTEI_ST<'" . $date1 . "' AND";
			$sql .= "   (";
			$sql .= "     A.DT_KOTEI_ED>='" . $date1 . "' AND";
			$sql .= "     A.DT_KOTEI_ED<='" .  $date2 . "'";
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
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(A.DT_KOTEI_ST), concat(24 * DATEDIFF(DATE(A.DT_KOTEI_ST), A.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(A.DT_KOTEI_ST)";
			$sql .= "     END AS DT_KOTEI_ST_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN '" . $date2 . "' <= NOW() THEN";
			$sql .= "        (";
			$sql .= "         CASE";
			$sql .= "           WHEN DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO) <> 0 THEN";
			$sql .= "               TIME_FORMAT(ADDTIME(TIME('" . $date2 . "'), concat(24 * DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "             ELSE TIME('" . $date2 . "')";
			$sql .= "           END";
			$sql .= "        )";
			$sql .= "       ELSE";
			$sql .= "        (";
			$sql .= "         CASE";
			$sql .= "           WHEN DATEDIFF(DATE(NOW()), A.YMD_SYUGYO) <> 0 THEN";
			$sql .= "               TIME_FORMAT(ADDTIME(TIME(NOW()), concat(24 * DATEDIFF(DATE(NOW()), A.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "             ELSE TIME(NOW())";
			$sql .= "           END";
			$sql .= "        )";
			$sql .= "     END AS DT_KOTEI_ED_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(B.DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
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
			$sql .= "   A.NINUSI_CD='" . $ninusi_cd . "' AND";
			$sql .= "   A.SOSIKI_CD='" . $sosiki_cd . "' AND";
			$sql .= "   (";
			$sql .= "     A.DT_KOTEI_ED>'" . $date2 . "' OR";
			$sql .= "     A.DT_KOTEI_ED IS NULL";
			$sql .= "   )";
			$sql .= "   AND";
			$sql .= "   (";
			$sql .= "     A.DT_KOTEI_ST>='" . $date1 . "' AND";
			$sql .= "     A.DT_KOTEI_ST<='" .  $date2 . "'";
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
			$sql .= "         TIME_FORMAT(ADDTIME(TIME('" . $date1 ."'), concat(24 * DATEDIFF(DATE('" . $date1 ."'), A.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME('" . $date1 ."')";
			$sql .= "     END AS DT_KOTEI_ST_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN '" . $date2 . "' <= NOW() THEN";
			$sql .= "        (";
			$sql .= "         CASE";
			$sql .= "           WHEN DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO) <> 0 THEN";
			$sql .= "               TIME_FORMAT(ADDTIME(TIME('" . $date2 . "'), concat(24 * DATEDIFF(DATE('" . $date2 . "'), A.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "             ELSE TIME('" . $date2 . "')";
			$sql .= "           END";
			$sql .= "        )";
			$sql .= "       ELSE";
			$sql .= "        (";
			$sql .= "         CASE";
			$sql .= "           WHEN DATEDIFF(DATE(NOW()), A.YMD_SYUGYO) <> 0 THEN";
			$sql .= "               TIME_FORMAT(ADDTIME(TIME(NOW()), concat(24 * DATEDIFF(DATE(NOW()), A.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "             ELSE TIME(NOW())";
			$sql .= "           END";
			$sql .= "        )";
			$sql .= "     END AS DT_KOTEI_ED_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(B.DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(B.DT_SYUGYO_ST), B.YMD_SYUGYO),':00:00')), '%H:%i:%s')";
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
			$sql .= "   A.NINUSI_CD='" . $ninusi_cd . "' AND";
			$sql .= "   A.SOSIKI_CD='" . $sosiki_cd . "' AND";
			$sql .= "   A.DT_KOTEI_ST<'" . $date1 . "' AND";
			$sql .= "   (";
			$sql .= "     A.DT_KOTEI_ED>'" . $date2 . "' OR";
			$sql .= "     A.DT_KOTEI_ED IS NULL";
			$sql .= "   )";


			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業実績明細情報・日またがり　取得");

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

			$jissekiMArray = array_merge($arrayList1,$arrayList2,$arrayList3,$arrayList4);
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

			//2. 就業実績ヘッダ情報取得

			//2_1. 時間範囲内
			$sql  = "SELECT ";
			$sql .= "   NINUSI_CD,";
			$sql .= "   SOSIKI_CD,";
			$sql .= "   YMD_SYUGYO,";
			$sql .= "   STAFF_CD,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(DT_SYUGYO_ST)";
			$sql .= "     END AS DT_SYUGYO_ST_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(DT_SYUGYO_ED), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(DT_SYUGYO_ED), concat(24 * DATEDIFF(DATE(DT_SYUGYO_ED), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(DT_SYUGYO_ED)";
			$sql .= "     END AS DT_SYUGYO_ED_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(DT_SYUGYO_ST)";
			$sql .= "     END AS DT_SYUGYO_ST";
			$sql .= " FROM";
			$sql .= "   T_KOTEI_H";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $sosiki_cd . "' AND";
			$sql .= "   DT_SYUGYO_ST>='" . $date1 . "' AND";
			$sql .= "   DT_SYUGYO_ED<='" .  $date2 . "'";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業実績ヘッダ情報・時間範囲内　取得");

			$arrayList1 = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
						"NINUSI_CD" => $result['NINUSI_CD'],
						"SOSIKI_CD" => $result['SOSIKI_CD'],
						"YMD_SYUGYO" => $result['YMD_SYUGYO'],
						"STAFF_CD" => $result['STAFF_CD'],
						"DT_SYUGYO_ST_H" => $result['DT_SYUGYO_ST_H'],
						"DT_SYUGYO_ED_H" => $result['DT_SYUGYO_ED_H'],
						"DT_SYUGYO_ST" => $result['DT_SYUGYO_ST']
				);

				$arrayList1[] = $array;
			}

			//2_2. 開始時間またがり
			$sql  = "SELECT ";
			$sql .= "   NINUSI_CD,";
			$sql .= "   SOSIKI_CD,";
			$sql .= "   YMD_SYUGYO,";
			$sql .= "   STAFF_CD,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE('" . $date1 ."'), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME('" . $date1 ."'), concat(24 * DATEDIFF(DATE('" . $date1 ."'), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME('" . $date1 ."')";
			$sql .= "     END AS DT_SYUGYO_ST_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(DT_SYUGYO_ED), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(DT_SYUGYO_ED), concat(24 * DATEDIFF(DATE(DT_SYUGYO_ED), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(DT_SYUGYO_ED)";
			$sql .= "     END AS DT_SYUGYO_ED_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(DT_SYUGYO_ST)";
			$sql .= "     END AS DT_SYUGYO_ST";
			$sql .= " FROM";
			$sql .= "   T_KOTEI_H";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $sosiki_cd . "' AND";
			$sql .= "   DT_SYUGYO_ST<'" . $date1 . "' AND";
			$sql .= "   (";
			$sql .= "     DT_SYUGYO_ED>='" . $date1 . "' AND";
			$sql .= "     DT_SYUGYO_ED<='" .  $date2 . "'";
			$sql .= "   )";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業実績ヘッダ情報・開始時間またがり　取得");

			$arrayList2 = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
						"NINUSI_CD" => $result['NINUSI_CD'],
						"SOSIKI_CD" => $result['SOSIKI_CD'],
						"YMD_SYUGYO" => $result['YMD_SYUGYO'],
						"STAFF_CD" => $result['STAFF_CD'],
						"DT_SYUGYO_ST_H" => $result['DT_SYUGYO_ST_H'],
						"DT_SYUGYO_ED_H" => $result['DT_SYUGYO_ED_H'],
						"DT_SYUGYO_ST" => $result['DT_SYUGYO_ST']
				);

				$arrayList2[] = $array;
			}

			//2_3. 終了時間またがり
			$sql  = "SELECT ";
			$sql .= "   NINUSI_CD,";
			$sql .= "   SOSIKI_CD,";
			$sql .= "   YMD_SYUGYO,";
			$sql .= "   STAFF_CD,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(DT_SYUGYO_ST)";
			$sql .= "   END AS DT_SYUGYO_ST_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN '" . $date2 . "' <= NOW() THEN";
			$sql .= "        (";
			$sql .= "           CASE";
			$sql .= "             WHEN DATEDIFF(DATE('" . $date2 . "'), YMD_SYUGYO) <> 0 THEN";
			$sql .= "                 TIME_FORMAT(ADDTIME(TIME('" . $date2 . "'), concat(24 * DATEDIFF(DATE('" . $date2 . "'), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "               ELSE TIME('" . $date2 . "')";
			$sql .= "             END";
			$sql .= "        )";
			$sql .= "       ELSE";
			$sql .= "        (";
			$sql .= "           CASE";
			$sql .= "             WHEN DATEDIFF(DATE(NOW()), YMD_SYUGYO) <> 0 THEN";
			$sql .= "                 TIME_FORMAT(ADDTIME(TIME(NOW()), concat(24 * DATEDIFF(DATE(NOW()), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "               ELSE TIME(NOW())";
			$sql .= "             END";
			$sql .= "        )";
			$sql .= "     END AS DT_SYUGYO_ED_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(DT_SYUGYO_ST)";
			$sql .= "   END AS DT_SYUGYO_ST";
			$sql .= " FROM";
			$sql .= "   T_KOTEI_H";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $sosiki_cd . "' AND";
			$sql .= "   (";
			$sql .= "     DT_SYUGYO_ED>'" . $date2 . "' OR";
			$sql .= "     DT_SYUGYO_ED IS NULL";
			$sql .= "   )";
			$sql .= "   AND";
			$sql .= "   (";
			$sql .= "     DT_SYUGYO_ST>='" . $date1 . "' AND";
			$sql .= "     DT_SYUGYO_ST<='" .  $date2 . "'";
			$sql .= "   )";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業実績ヘッダ情報・終了時間またがり　取得");

			$arrayList3 = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
						"NINUSI_CD" => $result['NINUSI_CD'],
						"SOSIKI_CD" => $result['SOSIKI_CD'],
						"YMD_SYUGYO" => $result['YMD_SYUGYO'],
						"STAFF_CD" => $result['STAFF_CD'],
						"DT_SYUGYO_ST_H" => $result['DT_SYUGYO_ST_H'],
						"DT_SYUGYO_ED_H" => $result['DT_SYUGYO_ED_H'],
						"DT_SYUGYO_ST" => $result['DT_SYUGYO_ST']
				);

				$arrayList3[] = $array;
			}

			//2_4. 日またがり
			$sql  = "SELECT ";
			$sql .= "   NINUSI_CD,";
			$sql .= "   SOSIKI_CD,";
			$sql .= "   YMD_SYUGYO,";
			$sql .= "   STAFF_CD,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE('" . $date1 ."'), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME('" . $date1 ."'), concat(24 * DATEDIFF(DATE('" . $date1 ."'), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME('" . $date1 ."')";
			$sql .= "     END AS DT_SYUGYO_ST_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN '" . $date2 . "' <= NOW() THEN";
			$sql .= "        (";
			$sql .= "           CASE";
			$sql .= "             WHEN DATEDIFF(DATE('" . $date2 . "'), YMD_SYUGYO) <> 0 THEN";
			$sql .= "                 TIME_FORMAT(ADDTIME(TIME('" . $date2 . "'), concat(24 * DATEDIFF(DATE('" . $date2 . "'), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "               ELSE TIME('" . $date2 . "')";
			$sql .= "             END";
			$sql .= "        )";
			$sql .= "       ELSE";
			$sql .= "        (";
			$sql .= "           CASE";
			$sql .= "             WHEN DATEDIFF(DATE(NOW()), YMD_SYUGYO) <> 0 THEN";
			$sql .= "                 TIME_FORMAT(ADDTIME(TIME(NOW()), concat(24 * DATEDIFF(DATE(NOW()), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "               ELSE TIME(NOW())";
			$sql .= "             END";
			$sql .= "        )";
			$sql .= "     END AS DT_SYUGYO_ED_H,";
			$sql .= "   CASE";
			$sql .= "     WHEN DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO) <> 0 THEN";
			$sql .= "         TIME_FORMAT(ADDTIME(TIME(DT_SYUGYO_ST), concat(24 * DATEDIFF(DATE(DT_SYUGYO_ST), YMD_SYUGYO),':00:00')), '%H:%i:%s')";
			$sql .= "       ELSE";
			$sql .= "         TIME(DT_SYUGYO_ST)";
			$sql .= "   END AS DT_SYUGYO_ST";
			$sql .= " FROM";
			$sql .= "   T_KOTEI_H";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $sosiki_cd . "' AND";
			$sql .= "   DT_SYUGYO_ST<'" . $date1 . "' AND";
			$sql .= "   (";
			$sql .= "     DT_SYUGYO_ED>'" . $date2 . "' OR";
			$sql .= "     DT_SYUGYO_ED IS NULL";
			$sql .= "   )";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業実績ヘッダ情報・日またがり　取得");

			$arrayList4 = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
						"NINUSI_CD" => $result['NINUSI_CD'],
						"SOSIKI_CD" => $result['SOSIKI_CD'],
						"YMD_SYUGYO" => $result['YMD_SYUGYO'],
						"STAFF_CD" => $result['STAFF_CD'],
						"DT_SYUGYO_ST_H" => $result['DT_SYUGYO_ST_H'],
						"DT_SYUGYO_ED_H" => $result['DT_SYUGYO_ED_H'],
						"DT_SYUGYO_ST" => $result['DT_SYUGYO_ST']
				);

				$arrayList4[] = $array;
			}

			$jissekiHArray = array_merge($arrayList1,$arrayList2,$arrayList3,$arrayList4);
			$NINUSI_CD = array();
			$SOSIKI_CD =  array();
			$YMD_SYUGYO =  array();
			$STAFF_CD =  array();
			$DT_SYUGYO_ST =  array();
			$DT_SYUGYO_ST_H =  array();

			foreach ($jissekiHArray as $key => $row) {
				$NINUSI_CD[$key]  = $row['NINUSI_CD'];
				$SOSIKI_CD[$key]  = $row['SOSIKI_CD'];
				$YMD_SYUGYO[$key]  = $row['YMD_SYUGYO'];
				$STAFF_CD[$key]  = $row['STAFF_CD'];
				$DT_SYUGYO_ST[$key]  = $row['DT_SYUGYO_ST'];
				$DT_SYUGYO_ST_H[$key]  = $row['DT_SYUGYO_ST_H'];
			}

			array_multisort($NINUSI_CD,SORT_ASC,
					$SOSIKI_CD,SORT_ASC,
					$YMD_SYUGYO,SORT_ASC,
					$STAFF_CD,SORT_ASC,
					$DT_SYUGYO_ST,SORT_ASC,
					$DT_SYUGYO_ST_H,SORT_ASC,
					$jissekiHArray);

			$kadoCostArray = $this->MCommon->getKadoCost2($ymd_sime,$jissekiMArray,$jissekiHArray,$sosiki_cd,$ninusi_cd);

			//コスト算出データ更新処理

			//コスト算出データ削除
			$sql = "CALL P_WPO050_DELETE_CALC_DATA(";
			$sql .= "'" . $ninusi_cd . "',";
			$sql .= "'" . $sosiki_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "@return_cd";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, 'コスト算出データ　削除');

			$sql = "SELECT";
			$sql .= " @return_cd";

			$this->queryWithPDOLog($stmt,$pdo,$sql, 'コスト算出データ　削除');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];

			if ($return_cd == "1") {

				throw new Exception('コスト算出データ削除　例外発生');
			}

			//コスト算出データ登録
			foreach($kadoCostArray as $array) {

				$sql = "CALL P_WPO050_SET_CALC_DATA(";
				$sql .= "'" . $staff_cd . "',";
				$sql .= "'" . $array['NINUSI_CD'] . "',";
				$sql .= "'" . $array['SOSIKI_CD'] . "',";
				$sql .= "'" . $array['YMD_SIME'] . "',";
				$sql .= "'" . $array['STAFF_CD'] . "',";
				$sql .= "'" . $array['YMD_SYUGYO'] . "',";
				$sql .= "'" . $array['BNRI_DAI_CD'] . "',";
				$sql .= "'" . $array['BNRI_CYU_CD'] . "',";
				$sql .= "'" . $array['BNRI_SAI_CD'] . "',";
				$sql .= "'" . $array['TIME_KOTEI_ST'] . "',";
				$sql .= ($array['TIME_KOTEI_ED'] != "" ? "'" . $array['TIME_KOTEI_ED'] . "'," : "NULL,");
				$sql .= ($array['TIME_KOTEI_SYUGYO'] != "" ?  $array['TIME_KOTEI_SYUGYO'] . "," : "0,");
				$sql .= ($array['TIME_SYUGYO_ST'] != "" ? "'" . $array['TIME_SYUGYO_ST'] . "'," : "NULL,");
				$sql .= ($array['TIME_SYUGYO_ED'] != "" ? "'" . $array['TIME_SYUGYO_ED'] . "'," : "NULL,");
				$sql .= ($array['TIME_SNY_TEKIYO_ST'] != "" ? "'" . $array['TIME_SNY_TEKIYO_ST'] . "'," : "NULL,");
				$sql .= ($array['TIME_SNY_TEKIYO_ED'] != "" ? "'" . $array['TIME_SNY_TEKIYO_ED'] . "'," : "NULL,");
				$sql .= ($array['KIN_SYOTEI'] != "" ?  $array['KIN_SYOTEI'] . "," : "0,");
				$sql .= ($array['KIN_JIKANGAI'] != "" ?  $array['KIN_JIKANGAI'] . "," : "0,");
				$sql .= ($array['KIN_SNY'] != "" ?  $array['KIN_SNY'] . "," : "0,");
				$sql .= ($array['KIN_KST'] != "" ?  $array['KIN_KST'] . "," : "0,");
				$sql .= ($array['KIN_H_KST'] != "" ?  $array['KIN_H_KST'] . "," : "0,");
				$sql .= ($array['KIN_H_KST_SNY'] != "" ?  $array['KIN_H_KST_SNY'] . "," : "0,");
				$sql .= ($array['TIME_SYOTEI_ST'] != "" ? "'" . $array['TIME_SYOTEI_ST'] . "'," : "NULL,");
				$sql .= ($array['TIME_SYOTEI_ED'] != "" ? "'" . $array['TIME_SYOTEI_ED'] . "'," : "NULL,");
				$sql .= ($array['TIME_JIKANGAI_ST'] != "" ? "'" . $array['TIME_JIKANGAI_ST'] . "'," : "NULL,");
				$sql .= ($array['TIME_JIKANGAI_ED'] != "" ? "'" . $array['TIME_JIKANGAI_ED'] . "'," : "NULL,");
				$sql .= ($array['TIME_KST_ST'] != "" ? "'" . $array['TIME_KST_ST'] . "'," : "NULL,");
				$sql .= ($array['TIME_KST_ED'] != "" ? "'" . $array['TIME_KST_ED'] . "'," : "NULL,");
				$sql .= ($array['TIME_H_KST_ST'] != "" ? "'" . $array['TIME_H_KST_ST'] . "'," : "NULL,");
				$sql .= ($array['TIME_H_KST_ED'] != "" ? "'" . $array['TIME_H_KST_ED'] . "'," : "NULL,");
				$sql .= ($array['TIME_CNT_SYOTEI'] != "" ?  $array['TIME_CNT_SYOTEI'] . "," : "0,");
				$sql .= ($array['TIME_CNT_JIKANGAI'] != "" ?  $array['TIME_CNT_JIKANGAI'] . "," : "0,");
				$sql .= ($array['TIME_CNT_SNY'] != "" ?  $array['TIME_CNT_SNY'] . "," : "0,");
				$sql .= ($array['TIME_CNT_KST'] != "" ?  $array['TIME_CNT_KST'] . "," : "0,");
				$sql .= ($array['TIME_CNT_H_KST'] != "" ?  $array['TIME_CNT_H_KST'] . "," : "0,");
				$sql .= ($array['TIME_CNT_H_KST_SNY'] != "" ?  $array['TIME_CNT_H_KST_SNY'] . "," : "0,");
				$sql .= ($array['COST_SYOTEI'] != "" ?  $array['COST_SYOTEI'] . "," : "0,");
				$sql .= ($array['COST_JIKANGAI'] != "" ?  $array['COST_JIKANGAI'] . "," : "0,");
				$sql .= ($array['COST_SNY'] != "" ?  $array['COST_SNY'] . "," : "0,");
				$sql .= ($array['COST_KST'] != "" ?  $array['COST_KST'] . "," : "0,");
				$sql .= ($array['COST_H_KST'] != "" ?  $array['COST_H_KST'] . "," : "0,");
				$sql .= ($array['COST_H_KST_SNY'] != "" ?  $array['COST_H_KST_SNY'] . "," : "0,");
				$sql .= ($array['COST'] != "" ?  $array['COST'] . "," : "0,");
				$sql .= ($array['TNK'] != "" ?  $array['TNK'] . "," : "0,");
				$sql .= "@return_cd";
				$sql .= ")";

				$this->execWithPDOLog($pdo,$sql, 'コスト算出データ　追加');

				$sql = "SELECT";
				$sql .= " @return_cd";

				$this->queryWithPDOLog($stmt,$pdo,$sql, 'コスト算出データ　追加');

				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$return_cd = $result["@return_cd"];

				if ($return_cd == "1") {

					throw new Exception('コスト算出データ追加　例外発生');
				}
			}



			//ABCデータベーススタッフ別情報作成
			$sql = "CALL P_WPO050_SET_ABC_DB_STAFF(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $ninusi_cd . "',";
			$sql .= "'" . $sosiki_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "'" . $date1 . "',";
			$sql .= "'" . $date2 . "',";
			$sql .= "@return_cd";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, 'ABCデータベーススタッフ別　登録');

			$sql = "SELECT";
			$sql .= " @return_cd";

			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ABCデータベーススタッフ別　登録');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];

			if ($return_cd == "1") {

				throw new Exception('ABCデータベーススタッフ別登録　例外発生');
			}

			//ABCデーターベース日別情報作成
			$sql = "CALL P_WPO050_SET_ABC_DB_HIBETU(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $ninusi_cd . "',";
			$sql .= "'" . $sosiki_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "@return_cd";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, 'ABCデータベース日別　登録');

			$sql = "SELECT";
			$sql .= " @return_cd";

			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ABCデータベース日別　登録');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];

			if ($return_cd == "1") {

				throw new Exception('ABCデータベース日別登録　例外発生');
			}

			//就業実績日別情報
			$sql = "CALL P_WPO050_SET_STAFF_SYUGYO(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $ninusi_cd . "',";
			$sql .= "'" . $sosiki_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "'" . $date1 . "',";
			$sql .= "'" . $date2 . "',";
			$sql .= "@return_cd";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, '就業実績情報　登録');

			$sql = "SELECT";
			$sql .= " @return_cd";

			$this->queryWithPDOLog($stmt,$pdo,$sql, '就業実績情報　登録');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];

			if ($return_cd == "1") {

				throw new Exception('就業実績情報登録　例外発生');
			}

			//就業実績情報展開処理呼び出し
			$sql = "CALL P_WPO050_SET_TENKAI(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "@return_cd";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, '就業実績情報展開処理　登録');

			$sql = "SELECT";
			$sql .= " @return_cd";

			$this->queryWithPDOLog($stmt,$pdo,$sql, '就業実績情報展開処理　登録');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];

			if ($return_cd == "1") {

				throw new Exception('就業実績情報展開処理登録　例外発生');
			}

			//スタッフ就業実績情報順位設定呼び出し
			$sql = "CALL P_WPO050_SET_RANK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "@return_cd";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, '就業実績情報順位設定　登録');

			$sql = "SELECT";
			$sql .= " @return_cd";

			$this->queryWithPDOLog($stmt,$pdo,$sql, '就業実績情報順位設定　登録');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];

			if ($return_cd == "1") {

				throw new Exception('就業情報順位設定登録　例外発生');
			}

			//スタッフランク設定
			$sql = "CALL P_WPO050_SET_STAFF_RANK(";
			$sql .= " '" . $staff_cd . "',";
			$sql .= " '" . $ninusi_cd . "',";
			$sql .= " '" . $sosiki_cd . "',";
			$sql .= " @return";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, 'スタッフランク設定　更新');

			$sql = "SELECT";
			$sql .= " @return_cd";

			$this->queryWithPDOLog($stmt,$pdo,$sql, 'スタッフランク設定　更新');

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];

			if ($return_cd == "1") {

				throw new Exception('スタッフランク設定　例外発生');
			}

			$pdo->commit();

			//排他処理
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $ninusi_cd . "',";
			$sql .= "'" . $sosiki_cd . "',";
			$sql .= "'WPO',";
			$sql .= "'050',";
			$sql .= "0";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'WPO050　排他制御オフ');

			$pdo = null;


		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO050", $e->getMessage());
			$pdo->rollBack();

			//排他処理
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $ninusi_cd . "',";
			$sql .= "'" . $sosiki_cd . "',";
			$sql .= "'WPO',";
			$sql .= "'050',";
			$sql .= "0";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'WPO050　排他制御オフ');

			$pdo = null;
		}

		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return true;
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

			$this->printLog("fatal", "例外発生", "TPM050", $e->getMessage());
			$pdo = null;
		}

		return date("Ymd",strtotime("-1 day"));;

	}

}

?>