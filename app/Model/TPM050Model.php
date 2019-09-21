<?php
/**
 * TPM050Model
 *
 * 工程配置進捗
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class TPM050Model extends DCMSAppModel{

	public $name = 'TPM050Model';
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
	 * 工程管理データ取得
	 * @access   public
	 * @return   工程管理データ
	 */
	public function getKoteiList($ymd_from = '',$ymd_to = '',$kotei = '') {

		$pdo = null;

		try{

			$today = $this->getToday();

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			//工程データの取得
			$sql  = "SELECT ";
			$sql .= "     A.NINUSI_CD AS NINUSI_CD";
			$sql .= "   , A.SOSIKI_CD AS SOSIKI_CD";
			$sql .= "   , A.YMD_SYUGYO AS YMD_SYUGYO";
			$sql .= "   , C.BNRI_DAI_CD AS BNRI_DAI_CD";
			$sql .= "   , C.BNRI_DAI_RYAKU AS BNRI_DAI_RYAKU";
			$sql .= "   , D.BNRI_CYU_CD AS BNRI_CYU_CD";
			$sql .= "   , D.BNRI_CYU_RYAKU AS BNRI_CYU_RYAKU";
			$sql .= "   , E.BNRI_SAI_CD AS BNRI_SAI_CD";
			$sql .= "   , E.BNRI_SAI_RYAKU AS BNRI_SAI_RYAKU";
			$sql .= "   , COUNT(DISTINCT G.STAFF_CD) AS KADO_NINZU";
			$sql .= "   , F.NINZU AS RUIKEI_NINZU";
			$sql .= "   , TRUNCATE(F.JIKAN / 60 + .005, 2) AS JIKAN_H";
			$sql .= "   , F.JIKAN AS JIKAN_M";
			$sql .= "   , F.BUTURYO AS BUTURYO";
			$sql .= "   , CASE";
			$sql .= " 		WHEN F.JIKAN <> 0 THEN";
			$sql .= " 			TRUNCATE(F.BUTURYO / (F.JIKAN / 60 / 60),1)";
			$sql .= " 		ELSE";
			$sql .= " 			0";
			$sql .= " 	END AS SEISANSEI_JINJI";
			$sql .= "   , H.YOSOKU_BUTURYO AS YOSOKU_BUTURYO";
			$sql .= "   , H.YOSOKU_JIKAN AS YOSOKU_JIKAN";
			$sql .= "   , CONCAT(A.NINUSI_CD, '_', A.SOSIKI_CD, '_',A.STAFF_CD , '_',A.YMD_SYUGYO) AS KOTEI_KEY";
			$sql .= " FROM";
			$sql .= "  T_KOTEI_D A ";
			$sql .= "  INNER JOIN T_KOTEI_H B ";
			$sql .= "    ON A.NINUSI_CD   = B.NINUSI_CD ";
			$sql .= "    AND A.SOSIKI_CD  = B.SOSIKI_CD ";
			$sql .= "    AND A.STAFF_CD   = B.STAFF_CD ";
			$sql .= "    AND A.YMD_SYUGYO = B.YMD_SYUGYO";
			$sql .= "  LEFT JOIN M_BNRI_DAI C";
			$sql .= "    ON  A.NINUSI_CD = C.NINUSI_CD";
			$sql .= "    AND A.SOSIKI_CD = C.SOSIKI_CD";
			$sql .= "    AND A.BNRI_DAI_CD = C.BNRI_DAI_CD";
			$sql .= "  LEFT JOIN M_BNRI_CYU D";
			$sql .= "    ON  A.NINUSI_CD = D.NINUSI_CD";
			$sql .= "    AND A.SOSIKI_CD = D.SOSIKI_CD";
			$sql .= "    AND A.BNRI_DAI_CD = D.BNRI_DAI_CD";
			$sql .= "    AND A.BNRI_CYU_CD = D.BNRI_CYU_CD";
			$sql .= "  LEFT JOIN M_BNRI_SAI E";
			$sql .= "    ON  A.NINUSI_CD = E.NINUSI_CD";
			$sql .= "    AND A.SOSIKI_CD = E.SOSIKI_CD";
			$sql .= "    AND A.BNRI_DAI_CD = E.BNRI_DAI_CD";
			$sql .= "    AND A.BNRI_CYU_CD = E.BNRI_CYU_CD";
			$sql .= "    AND A.BNRI_SAI_CD = E.BNRI_SAI_CD";
			$sql .= "  LEFT JOIN T_ABC_DB_DAY F";
			$sql .= "    ON  A.NINUSI_CD = F.NINUSI_CD ";
			$sql .= "    AND A.SOSIKI_CD  = F.SOSIKI_CD ";
			$sql .= "   AND A.YMD_SYUGYO = F.YMD";
			$sql .= "    AND A.BNRI_DAI_CD = F.BNRI_DAI_CD";
			$sql .= "    AND A.BNRI_CYU_CD = F.BNRI_CYU_CD";
			$sql .= "    AND A.BNRI_SAI_CD = F.BNRI_SAI_CD";
			$sql .= "  LEFT JOIN T_KOTEI_D G ";
			$sql .= "    ON  A.NINUSI_CD = G.NINUSI_CD ";
			$sql .= "    AND A.SOSIKI_CD = G.SOSIKI_CD ";
			$sql .= "    AND A.BNRI_DAI_CD = G.BNRI_DAI_CD";
			$sql .= "    AND A.BNRI_CYU_CD = G.BNRI_CYU_CD";
			$sql .= "    AND A.BNRI_SAI_CD = G.BNRI_SAI_CD";
			$sql .= "    AND G.YMD_SYUGYO = '". $today . "'";
			$sql .= "    AND G.DT_KOTEI_ST < NOW()";
			$sql .= "    AND G.DT_KOTEI_ED IS NULL";
			$sql .= "  LEFT JOIN T_KOTEI_YOSOKU H";
			$sql .= "    ON A.NINUSI_CD   = H.NINUSI_CD ";
			$sql .= "    AND A.SOSIKI_CD  = H.SOSIKI_CD ";
			$sql .= "    AND A.YMD_SYUGYO = H.YMD_SYUGYO";
			$sql .= "    AND A.BNRI_DAI_CD = H.BNRI_DAI_CD";
			$sql .= "    AND A.BNRI_CYU_CD = H.BNRI_CYU_CD";
			$sql .= "    AND A.BNRI_SAI_CD = H.BNRI_SAI_CD";
			$sql .= ' WHERE ';
			$sql .= "  A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  A.SOSIKI_CD='" . $this->_sosiki_cd . "'";

			//就業日付
			if ($ymd_from != ''){
				$sql .= " AND A.YMD_SYUGYO >= '" . $ymd_from . "'" ;
			}

			if ($ymd_to != ''){
				$sql .= " AND A.YMD_SYUGYO <= '" . $ymd_to . "'" ;
			}

			//工程
			if ($kotei != "") {

				$koteis = explode("_", $kotei);

				if (count($koteis) == 1) {

					$sql .= " AND A.BNRI_DAI_CD= '" . $koteis[0] . "'" ;

				} elseif (count($koteis) == 2) {

					$sql .= " AND A.BNRI_DAI_CD= '" . $koteis[0] . "'" ;
					$sql .= " AND A.BNRI_CYU_CD= '" . $koteis[1] . "'" ;

				} elseif (count($koteis) == 3) {

					$sql .= " AND A.BNRI_DAI_CD= '" . $koteis[0] . "'" ;
					$sql .= " AND A.BNRI_CYU_CD= '" . $koteis[1] . "'" ;
					$sql .= " AND A.BNRI_SAI_CD= '" . $koteis[2] . "'" ;

				}

			}

			$sql .= "    GROUP BY";
			$sql .= "      A.NINUSI_CD,";
			$sql .= "      A.SOSIKI_CD,";
			$sql .= "      A.YMD_SYUGYO,";
			$sql .= "      A.BNRI_DAI_CD,";
			$sql .= "      A.BNRI_CYU_CD,";
			$sql .= "      A.BNRI_SAI_CD";
			$sql .= "    ORDER BY";
			$sql .= "      A.NINUSI_CD,";
			$sql .= "      A.SOSIKI_CD,";
			$sql .= "      A.YMD_SYUGYO,";
			$sql .= "      A.BNRI_DAI_CD,";
			$sql .= "      A.BNRI_CYU_CD,";
			$sql .= "      A.BNRI_SAI_CD";
			$sql .= ";";


			$this->queryWithPDOLog($stmt,$pdo,$sql,"工程ヘッダ&工程 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
				    "NINUSI_CD" => $result['NINUSI_CD'],
					"SOSIKI_CD" => $result['SOSIKI_CD'],
					"YMD_SYUGYO" => $result['YMD_SYUGYO'],
					"BNRI_DAI_CD" => $result['BNRI_DAI_CD'],
					"BNRI_DAI_RYAKU" => $result['BNRI_DAI_RYAKU'],
					"BNRI_CYU_CD" => $result['BNRI_CYU_CD'],
					"BNRI_CYU_RYAKU" => $result['BNRI_CYU_RYAKU'],
					"BNRI_SAI_CD" => $result['BNRI_SAI_CD'],
					"BNRI_SAI_RYAKU" => $result['BNRI_SAI_RYAKU'],
					"KADO_NINZU" => $result['KADO_NINZU'],
					"RUIKEI_NINZU" => $result['RUIKEI_NINZU'],
					"JIKAN_H" => $result['JIKAN_H'],
					"JIKAN_M" => $result['JIKAN_M'],
					"BUTURYO" => $result['BUTURYO'],
					"SEISANSEI_JINJI" => $result['SEISANSEI_JINJI'],
					"YOSOKU_BUTURYO" => $result['YOSOKU_BUTURYO'],
					"YOSOKU_JIKAN" => $result['YOSOKU_JIKAN'],
					"KOTEI_KEY" => $result['KOTEI_KEY']

				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM050", $e->getMessage());
			$pdo = null;
		}


		return array();
	}

	/**
	 * 工程予測データ更新処理
	 * @access   public
	 * @param    工程データ
	 * @return   成否情報(true,false)
	 */
	function setKoteiYosokuData($data,$staff_cd) {
		$pdo = null;
		$pdo2 = null;
		$return_cd = "0";

		try {


			$flag = 0;

			//チェック処理
			foreach($data as $obj){

				$cellsArray = $obj->cells;
				$dataArray = $obj->data;

				$CHANGED = $dataArray[3];

				If($CHANGED != 0) {
					$flag = 1;
				}

			}

			if ($flag == 0) {
				$message = "更新データがありません。";
				$this->errors['Check'][] = $message;
				return false;
			}


			//排他制御オン
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'TPM',";
			$sql .= "'050',";
			$sql .= "1";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'TPM050 排他制御オン');

			try {

				$pdo2 = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
				$pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$pdo2->beginTransaction();

				//行ずつ処理
				foreach($data as $obj){

					$cellsArray = $obj->cells;
					$dataArray = $obj->data;

					if($dataArray[3] == 1) {

						$sql = "INSERT";
						$sql .= "    INTO T_KOTEI_YOSOKU";
						$sql .= "  (";
						$sql .= "  NINUSI_CD,";
						$sql .= "  SOSIKI_CD,";
						$sql .= "  YMD_SYUGYO,";
						$sql .= "  BNRI_DAI_CD,";
						$sql .= "  BNRI_CYU_CD,";
						$sql .= "  BNRI_SAI_CD,";
						$sql .= "  YOSOKU_BUTURYO,";
						$sql .= "  YOSOKU_JIKAN,";
						$sql .= "  CREATED,";
						$sql .= "  CREATED_STAFF,";
						$sql .= "  MODIFIED,";
						$sql .= "  MODIFIED_STAFF";
						$sql .= "  ) VALUES (";
						$sql .= "   '" . $this->_ninusi_cd . "',";
						$sql .= "   '" . $this->_sosiki_cd . "',";
						$sql .= "   '" . $cellsArray[0] . "',";
						$sql .= "   '" . $dataArray[0] . "',";
						$sql .= "   '" . $dataArray[1] . "',";
						$sql .= "   '" . $dataArray[2] . "',";
						$sql .= "   " . $cellsArray[10] . ",";
						$sql .= "   " . $cellsArray[11] . ",";
						$sql .= "   NOW(),";
						$sql .= "   '" . $staff_cd . "',";
						$sql .= "   NOW(),";
						$sql .= "   '" . $staff_cd . "'";
						$sql .= "  ) ON DUPLICATE KEY UPDATE";
						$sql .= "  YOSOKU_BUTURYO=" . $cellsArray[10] . ",";
						$sql .= "  YOSOKU_JIKAN=" . $cellsArray[11] . "";

						$this->execWithPDOLog($pdo2,$sql, '工程ヘッダ&工程　削除');
					}

				}

				$pdo2->commit();

			} catch (Exception $e) {

				$this->printLog("fatal", "例外発生", "TPM050", $e->getMessage());
				$pdo2->rollBack();
			}

			$pdo2 = null;


			//排他制御オフ
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'TPM',";
			$sql .= "'050',";
			$sql .= "0";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'TPM050 排他制御オフ');

			$pdo = null;

			if ($return_cd == "1") {

				//例外の場合、例外メッセージ
				$message = $this->MMessage->getOneMessage('CMNE000107');
				$this->errors['Check'][] =  $message;
				return false;
			}

			return true;

			} catch (Exception $e) {

				$this->printLog("fatal", "例外発生", "TPM050", $e->getMessage());
				$pdo = null;

			}

			//例外発生の場合、エラー
			$message = $this->MMessage->getOneMessage('CMNE000107');
			$this->errors['Check'][] =  $message;
			return false;

	}


	/**
	 * 工程管理詳細データ取得
	 * @access   public
	 * @return   工程管理詳細データ
	 */
	public function getKoteiDetailList($YMD_SYUGYO = '',$BNRI_DAI_CD = '',$BNRI_CYU_CD = '',$BNRI_SAI_CD = '',$YMD_FROM = '',$YMD_TO = '') {

		$pdo = null;

		try{

			$today = $this->getToday();

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//現在稼動しているスタッフを取得の取得
			$sql  = "SELECT ";
			$sql .= "     A.NINUSI_CD AS NINUSI_CD";
			$sql .= "   , A.SOSIKI_CD AS SOSIKI_CD";
			$sql .= "   , A.STAFF_CD AS STAFF_CD";
			$sql .= "   , C.STAFF_NM AS STAFF_NM";
			$sql .= "   , TRUNCATE(D.JIKAN / 60 + .005, 2) AS JIKAN_H";
			$sql .= "   , D.JIKAN AS JIKAN_M";
			$sql .= "   , D.BUTURYO AS BUTURYO";
			$sql .= "   , D.SEISANSEI_TIME_BUTURYO AS SEISANSEI_KADO";
			$sql .= "   , E.SEISANSEI_KADO_BUTURYO AS SEISANSEI_JISSEKI";
			$sql .= " FROM";
			$sql .= "  T_KOTEI_D A ";
			$sql .= "  INNER JOIN T_KOTEI_H B ";
			$sql .= "    ON  A.NINUSI_CD = B.NINUSI_CD ";
			$sql .= "    AND A.SOSIKI_CD = B.SOSIKI_CD ";
			$sql .= "    AND A.YMD_SYUGYO = B.YMD_SYUGYO";
			$sql .= "    AND A.STAFF_CD = B.STAFF_CD";
			$sql .= "  LEFT JOIN M_STAFF_KIHON C";
			$sql .= "    ON A.STAFF_CD = C.STAFF_CD";
			$sql .= "  LEFT JOIN T_ABC_DB_STAFF D";
			$sql .= "    ON A.NINUSI_CD   = D.NINUSI_CD ";
			$sql .= "    AND A.SOSIKI_CD  = D.SOSIKI_CD ";
			$sql .= "    AND A.YMD_SYUGYO = D.YMD";
			$sql .= "    AND A.STAFF_CD   = D.STAFF_CD ";
			$sql .= "    AND A.BNRI_DAI_CD = D.BNRI_DAI_CD";
			$sql .= "    AND A.BNRI_CYU_CD = D.BNRI_CYU_CD";
			$sql .= "    AND A.BNRI_SAI_CD = D.BNRI_SAI_CD";
			$sql .= "  LEFT JOIN T_STAFF_SYUGYO_JSK_NEAR E";
			$sql .= "    ON A.STAFF_CD   = E.STAFF_CD ";
			$sql .= "    AND A.BNRI_DAI_CD = E.BNRI_DAI_CD";
			$sql .= "    AND A.BNRI_CYU_CD = E.BNRI_CYU_CD";
			$sql .= "    AND A.BNRI_SAI_CD = E.BNRI_SAI_CD";
			$sql .= ' WHERE ';
			$sql .= "  A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "  A.YMD_SYUGYO = '". $today . "' AND";
			$sql .= "  A.DT_KOTEI_ST < NOW() AND";
			$sql .= "  A.DT_KOTEI_ED IS NULL";

			if ($BNRI_DAI_CD != ''){
				$sql .= " AND A.BNRI_DAI_CD='" . $BNRI_DAI_CD . "'" ;
			}

			if ($BNRI_CYU_CD != ''){
				$sql .= " AND A.BNRI_CYU_CD='" . $BNRI_CYU_CD . "'" ;
			}

			if ($BNRI_SAI_CD != ''){
				$sql .= " AND A.BNRI_SAI_CD='" . $BNRI_SAI_CD . "'" ;
			}


			$sql .= " GROUP BY";
			$sql .= "   A.NINUSI_CD,";
			$sql .= "   A.SOSIKI_CD,";
			$sql .= "   A.STAFF_CD";
			$sql .= " ORDER BY";
			$sql .= "   A.NINUSI_CD,";
			$sql .= "   A.SOSIKI_CD,";
			$sql .= "   A.STAFF_CD";
			$sql .= ";";


			$this->queryWithPDOLog($stmt,$pdo,$sql,"スタッフデータ 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$staff_cd = $result['STAFF_CD'];

				//スタッフの詳細





				$array = array(
						"NINUSI_CD" => $result['NINUSI_CD'],
						"SOSIKI_CD" => $result['SOSIKI_CD'],
						"STAFF_CD" => $result['STAFF_CD'],
						"STAFF_NM" => $result['STAFF_NM'],
						"JIKAN_H" => $result['JIKAN_H'],
						"JIKAN_M" => $result['JIKAN_M'],
						"BUTURYO" => $result['BUTURYO'],
						"SEISANSEI_KADO" => $result['SEISANSEI_KADO'],
						"SEISANSEI_JISSEKI" => $result['SEISANSEI_JISSEKI'],
				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM050", $e->getMessage());
			$pdo = null;
		}


		return array();
	}

	function getCSV($YMD_FROM, $YMD_TO, $KOTEI) {

		$text = array();

		//ヘッダー情報
		$header = "荷主CD,荷主名,組織CD,組織名,年月日,大分類CD,大分類名称,中分類CD,中分類名称,細分類CD,細分類名称,稼動人数,累計人数,累計時間(h),累計期間(m),累計物量,物量生産性,予測物量,予測終了時間,";
		$header .= "スタッフCD,スタッフ名,スタッフ別累計時間(h),スタッフ別累計期間(m),スタッフ別累計物量,稼動生産性,実質生産性";

		$text[] = $header;

		$pdo = null;

		try{

			$today = $this->getToday();

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//検索条件に工程がある場合、工程のキーを取得
			$conditionKey = '';


			//工程データの取得
			$sql  = "SELECT ";
			$sql .= "     A.NINUSI_CD AS NINUSI_CD";
			$sql .= "   , I.NINUSI_NM AS NINUSI_NM";
			$sql .= "   , A.SOSIKI_CD AS SOSIKI_CD";
			$sql .= "   , J.SOSIKI_NM AS SOSIKI_NM";
			$sql .= "   , A.YMD_SYUGYO AS YMD_SYUGYO";
			$sql .= "   , C.BNRI_DAI_CD AS BNRI_DAI_CD";
			$sql .= "   , C.BNRI_DAI_NM AS BNRI_DAI_NM";
			$sql .= "   , D.BNRI_CYU_CD AS BNRI_CYU_CD";
			$sql .= "   , D.BNRI_CYU_NM AS BNRI_CYU_NM";
			$sql .= "   , E.BNRI_SAI_CD AS BNRI_SAI_CD";
			$sql .= "   , E.BNRI_SAI_NM AS BNRI_SAI_NM";
			$sql .= "   , COUNT(DISTINCT G.STAFF_CD) AS KADO_NINZU";
			$sql .= "   , F.NINZU AS RUIKEI_NINZU";
			$sql .= "   , TRUNCATE(F.JIKAN / 60 + .005, 2) AS JIKAN_H";
			$sql .= "   , F.JIKAN AS JIKAN_M";
			$sql .= "   , F.BUTURYO AS BUTURYO";
			$sql .= "   , CASE";
			$sql .= " 		WHEN F.JIKAN <> 0 THEN";
			$sql .= " 			TRUNCATE(F.BUTURYO / F.JIKAN * 60,1)";
			$sql .= " 		ELSE";
			$sql .= " 			0";
			$sql .= " 	END AS SEISANSEI_JINJI";
			$sql .= "   , H.YOSOKU_BUTURYO AS YOSOKU_BUTURYO";
			$sql .= "   , H.YOSOKU_JIKAN AS YOSOKU_JIKAN";
			$sql .= "   , CONCAT(A.NINUSI_CD, '_', A.SOSIKI_CD, '_',A.STAFF_CD , '_',A.YMD_SYUGYO) AS KOTEI_KEY";
			$sql .= " FROM";
			$sql .= "  T_KOTEI_D A ";
			$sql .= "  INNER JOIN T_KOTEI_H B ";
			$sql .= "    ON A.NINUSI_CD   = B.NINUSI_CD ";
			$sql .= "    AND A.SOSIKI_CD  = B.SOSIKI_CD ";
			$sql .= "    AND A.STAFF_CD   = B.STAFF_CD ";
			$sql .= "    AND A.YMD_SYUGYO = B.YMD_SYUGYO";
			$sql .= "  LEFT JOIN M_BNRI_DAI C";
			$sql .= "    ON  A.NINUSI_CD = C.NINUSI_CD";
			$sql .= "    AND A.SOSIKI_CD = C.SOSIKI_CD";
			$sql .= "    AND A.BNRI_DAI_CD = C.BNRI_DAI_CD";
			$sql .= "  LEFT JOIN M_BNRI_CYU D";
			$sql .= "    ON  A.NINUSI_CD = D.NINUSI_CD";
			$sql .= "    AND A.SOSIKI_CD = D.SOSIKI_CD";
			$sql .= "    AND A.BNRI_DAI_CD = D.BNRI_DAI_CD";
			$sql .= "    AND A.BNRI_CYU_CD = D.BNRI_CYU_CD";
			$sql .= "  LEFT JOIN M_BNRI_SAI E";
			$sql .= "    ON  A.NINUSI_CD = E.NINUSI_CD";
			$sql .= "    AND A.SOSIKI_CD = E.SOSIKI_CD";
			$sql .= "    AND A.BNRI_DAI_CD = E.BNRI_DAI_CD";
			$sql .= "    AND A.BNRI_CYU_CD = E.BNRI_CYU_CD";
			$sql .= "    AND A.BNRI_SAI_CD = E.BNRI_SAI_CD";
			$sql .= "  LEFT JOIN T_ABC_DB_DAY F";
			$sql .= "    ON  A.NINUSI_CD = F.NINUSI_CD ";
			$sql .= "    AND A.SOSIKI_CD  = F.SOSIKI_CD ";
			$sql .= "    AND A.YMD_SYUGYO = F.YMD";
			$sql .= "    AND A.BNRI_DAI_CD = F.BNRI_DAI_CD";
			$sql .= "    AND A.BNRI_CYU_CD = F.BNRI_CYU_CD";
			$sql .= "    AND A.BNRI_SAI_CD = F.BNRI_SAI_CD";
			$sql .= "  LEFT JOIN T_KOTEI_D G ";
			$sql .= "    ON  A.NINUSI_CD = G.NINUSI_CD ";
			$sql .= "    AND A.SOSIKI_CD = G.SOSIKI_CD ";
			$sql .= "    AND A.BNRI_DAI_CD = G.BNRI_DAI_CD";
			$sql .= "    AND A.BNRI_CYU_CD = G.BNRI_CYU_CD";
			$sql .= "    AND A.BNRI_SAI_CD = G.BNRI_SAI_CD";
			$sql .= "    AND G.YMD_SYUGYO = '". $today . "'";
			$sql .= "    AND G.DT_KOTEI_ST < NOW()";
			$sql .= "    AND G.DT_KOTEI_ED IS NULL";
			$sql .= "  LEFT JOIN T_KOTEI_YOSOKU H";
			$sql .= "    ON A.NINUSI_CD   = H.NINUSI_CD ";
			$sql .= "    AND A.SOSIKI_CD  = H.SOSIKI_CD ";
			$sql .= "    AND A.YMD_SYUGYO = H.YMD_SYUGYO";
			$sql .= "    AND A.BNRI_DAI_CD = H.BNRI_DAI_CD";
			$sql .= "    AND A.BNRI_CYU_CD = H.BNRI_CYU_CD";
			$sql .= "    AND A.BNRI_SAI_CD = H.BNRI_SAI_CD";
			$sql .= "  LEFT JOIN M_NINUSI I";
			$sql .= "    ON A.NINUSI_CD   = I.NINUSI_CD ";
			$sql .= "  LEFT JOIN M_SOSIKI J";
			$sql .= "    ON A.SOSIKI_CD   = J.SOSIKI_CD ";

			$sql .= ' WHERE ';
			$sql .= "  A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  A.SOSIKI_CD='" . $this->_sosiki_cd . "'";

			//就業日付
			if ($YMD_FROM != ''){
				$sql .= " AND A.YMD_SYUGYO >= '" . $YMD_FROM . "'" ;
			}

			if ($YMD_TO != ''){
				$sql .= " AND A.YMD_SYUGYO <= '" . $YMD_TO . "'" ;
			}

			//工程
			if ($KOTEI != "") {

				$koteis = explode("_", $KOTEI);

				if (count($koteis) == 1) {

					$sql .= " AND A.BNRI_DAI_CD= '" . $koteis[0] . "'" ;

				} elseif (count($koteis) == 2) {

					$sql .= " AND A.BNRI_DAI_CD= '" . $koteis[0] . "'" ;
					$sql .= " AND A.BNRI_CYU_CD= '" . $koteis[1] . "'" ;

				} elseif (count($koteis) == 3) {

					$sql .= " AND A.BNRI_DAI_CD= '" . $koteis[0] . "'" ;
					$sql .= " AND A.BNRI_CYU_CD= '" . $koteis[1] . "'" ;
					$sql .= " AND A.BNRI_SAI_CD= '" . $koteis[2] . "'" ;

				}

			}

			$sql .= "    GROUP BY";
			$sql .= "      A.NINUSI_CD,";
			$sql .= "      A.SOSIKI_CD,";
			$sql .= "      A.YMD_SYUGYO,";
			$sql .= "      A.BNRI_DAI_CD,";
			$sql .= "      A.BNRI_CYU_CD,";
			$sql .= "      A.BNRI_SAI_CD";
			$sql .= "    ORDER BY";
			$sql .= "      A.NINUSI_CD,";
			$sql .= "      A.SOSIKI_CD,";
			$sql .= "      A.YMD_SYUGYO,";
			$sql .= "      A.BNRI_DAI_CD,";
			$sql .= "      A.BNRI_CYU_CD,";
			$sql .= "      A.BNRI_SAI_CD";
			$sql .= ";";


			$this->queryWithPDOLog($stmt,$pdo,$sql,"工程別データ 取得");


			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$NINUSI_CD = $result['NINUSI_CD'];
				$NINUSI_NM = $result['NINUSI_NM'];
				$SOSIKI_CD = $result['SOSIKI_CD'];
				$SOSIKI_NM = $result['SOSIKI_NM'];
				$YMD_SYUGYO = $result['YMD_SYUGYO'];
				$BNRI_DAI_CD = $result['BNRI_DAI_CD'];
				$BNRI_DAI_NM = $result['BNRI_DAI_NM'];
				$BNRI_CYU_CD = $result['BNRI_CYU_CD'];
				$BNRI_CYU_NM = $result['BNRI_CYU_NM'];
				$BNRI_SAI_CD = $result['BNRI_SAI_CD'];
				$BNRI_SAI_NM = $result['BNRI_SAI_NM'];
				$KADO_NINZU = $result['KADO_NINZU'];
				$RUIKEI_NINZU = $result['RUIKEI_NINZU'];
				$JIKAN_H = $result['JIKAN_H'];
				$JIKAN_M = $result['JIKAN_M'];
				$BUTURYO = $result['BUTURYO'];
				$SEISANSEI_JINJI = $result['SEISANSEI_JINJI'];
				$YOSOKU_BUTURYO = $result['YOSOKU_BUTURYO'];
				$YOSOKU_JIKAN = $result['YOSOKU_JIKAN'];
				$KOTEI_KEY = $result['KOTEI_KEY'];



				//現在稼動しているスタッフを取得の取得
				$sql  = "SELECT ";
				$sql .= "     A.NINUSI_CD AS NINUSI_CD";
				$sql .= "   , A.SOSIKI_CD AS SOSIKI_CD";
				$sql .= "   , A.STAFF_CD AS STAFF_CD";
				$sql .= "   , C.STAFF_NM AS STAFF_NM";
				$sql .= "   , TRUNCATE(D.JIKAN / 60 + .005, 2) AS S_JIKAN_H";
				$sql .= "   , D.JIKAN AS S_JIKAN_M";
				$sql .= "   , D.BUTURYO AS S_BUTURYO";
				$sql .= "   , D.SEISANSEI_TIME_BUTURYO AS SEISANSEI_KADO";
				$sql .= "   , E.SEISANSEI_KADO_BUTURYO AS SEISANSEI_JISSEKI";
				$sql .= " FROM";
				$sql .= "  T_KOTEI_D A ";
				$sql .= "  INNER JOIN T_KOTEI_H B ";
				$sql .= "    ON  A.NINUSI_CD = B.NINUSI_CD ";
				$sql .= "    AND A.SOSIKI_CD = B.SOSIKI_CD ";
				$sql .= "    AND A.YMD_SYUGYO = B.YMD_SYUGYO";
				$sql .= "    AND A.STAFF_CD = B.STAFF_CD";
				$sql .= "  LEFT JOIN M_STAFF_KIHON C";
				$sql .= "    ON A.STAFF_CD = C.STAFF_CD";
				$sql .= "  LEFT JOIN T_ABC_DB_STAFF D";
				$sql .= "    ON A.NINUSI_CD   = D.NINUSI_CD ";
				$sql .= "    AND A.SOSIKI_CD  = D.SOSIKI_CD ";
				$sql .= "    AND A.YMD_SYUGYO = D.YMD";
				$sql .= "    AND A.STAFF_CD   = D.STAFF_CD ";
				$sql .= "    AND A.BNRI_DAI_CD = D.BNRI_DAI_CD";
				$sql .= "    AND A.BNRI_CYU_CD = D.BNRI_CYU_CD";
				$sql .= "    AND A.BNRI_SAI_CD = D.BNRI_SAI_CD";
				$sql .= "  LEFT JOIN T_STAFF_SYUGYO_JSK_NEAR E";
				$sql .= "    ON A.STAFF_CD   = E.STAFF_CD ";
				$sql .= "    AND A.BNRI_DAI_CD = E.BNRI_DAI_CD";
				$sql .= "    AND A.BNRI_CYU_CD = E.BNRI_CYU_CD";
				$sql .= "    AND A.BNRI_SAI_CD = E.BNRI_SAI_CD";
				$sql .= ' WHERE ';
				$sql .= "  A.NINUSI_CD='" . $NINUSI_CD . "' AND";
				$sql .= "  A.SOSIKI_CD='" . $SOSIKI_CD . "' AND";
				$sql .= "  A.YMD_SYUGYO = '". $today . "' AND";
				$sql .= "  A.DT_KOTEI_ST < NOW() AND";
				$sql .= "  A.DT_KOTEI_ED IS NULL";

				if ($BNRI_DAI_CD != ''){
					$sql .= " AND A.BNRI_DAI_CD='" . $BNRI_DAI_CD . "'" ;
				}

				if ($BNRI_CYU_CD != ''){
					$sql .= " AND A.BNRI_CYU_CD='" . $BNRI_CYU_CD . "'" ;
				}

				if ($BNRI_SAI_CD != ''){
					$sql .= " AND A.BNRI_SAI_CD='" . $BNRI_SAI_CD . "'" ;
				}


				$sql .= " GROUP BY";
				$sql .= "   A.NINUSI_CD,";
				$sql .= "   A.SOSIKI_CD,";
				$sql .= "   A.STAFF_CD";
				$sql .= " ORDER BY";
				$sql .= "   A.NINUSI_CD,";
				$sql .= "   A.SOSIKI_CD,";
				$sql .= "   A.STAFF_CD";
				$sql .= ";";

				$pdo2 = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
				$pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$this->queryWithPDOLog($stmt2,$pdo2,$sql,"スタッフデータ 取得");

				while($result2 = $stmt2->fetch(PDO::FETCH_ASSOC)){

					$staff_cd = $result2['STAFF_CD'];

					//スタッフの詳細




					$STAFF_CD = $result2['STAFF_CD'];
					$STAFF_NM = $result2['STAFF_NM'];
					$S_JIKAN_H = $result2['S_JIKAN_H'];
					$S_JIKAN_M = $result2['S_JIKAN_M'];
					$S_BUTURYO = $result2['S_BUTURYO'];
					$SEISANSEI_KADO = $result2['SEISANSEI_KADO'];
					$SEISANSEI_JISSEKI = $result2['SEISANSEI_JISSEKI'];


					$text[] = '"' .$NINUSI_CD . '","' . $NINUSI_NM . '","' . $SOSIKI_CD . '","' . $SOSIKI_NM . '",' . $YMD_SYUGYO . ',"' .
							  $BNRI_DAI_CD . '","' . $BNRI_DAI_NM . '","' . $BNRI_CYU_CD . '","' .
							  $BNRI_CYU_NM . '","' . $BNRI_SAI_CD . '","' . $BNRI_SAI_NM . '",' .
							  $KADO_NINZU . ',' . $RUIKEI_NINZU . ',' . $JIKAN_H . ',' . $JIKAN_M . ',' .
							  $BUTURYO . ',' . $SEISANSEI_JINJI . ',' . $YOSOKU_BUTURYO . ',' .
							  $YOSOKU_JIKAN . ',' . $STAFF_CD . ',"' . $STAFF_NM . '",' . $S_JIKAN_H . ',' .
							  $S_JIKAN_M . ',' . $S_BUTURYO . ',' . $SEISANSEI_KADO . ',' . $SEISANSEI_JISSEKI;

				}

				$pdo2 = null;

			}

			$pdo = null;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM050", $e->getMessage());
			$pdo = null;
		}


		return $text;
	}

	function getCSV2($YMD_FROM, $YMD_TO, $KOTEI) {

		$text = array();

		//ヘッダー情報
		$header = "荷主CD,荷主名,組織CD,組織名,年月日,スタッフCD,スタッフ名,工程番号,大分類CD,大分類名称,中分類CD,中分類名称,細分類CD,細分類名称,備考１,備考２,備考３,";
		$header .= "就業開始時間,就業終了時間,工程開始時間,工程終了時間,工程累計時間(h),工程累計時間(m),実績別累計時間(h),実績累計期間(m),実績累計物量,稼動生産性,実質生産性";

		$text[] = $header;

		$pdo = null;

		try{

			$today = $this->getToday();

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//検索条件に工程がある場合、工程のキーを取得
			$conditionKey = '';


			//工程データの取得
			$sql  = "SELECT ";
			$sql .= "     A.NINUSI_CD AS NINUSI_CD";
			$sql .= "   , I.NINUSI_NM AS NINUSI_NM";
			$sql .= "   , A.SOSIKI_CD AS SOSIKI_CD";
			$sql .= "   , J.SOSIKI_NM AS SOSIKI_NM";
			$sql .= "   , A.YMD_SYUGYO AS YMD_SYUGYO";
			$sql .= "   , A.STAFF_CD AS STAFF_CD";
			$sql .= "   , K.STAFF_NM AS STAFF_NM";
			$sql .= "   , C.BNRI_DAI_CD AS BNRI_DAI_CD";
			$sql .= "   , C.BNRI_DAI_NM AS BNRI_DAI_NM";
			$sql .= "   , D.BNRI_CYU_CD AS BNRI_CYU_CD";
			$sql .= "   , D.BNRI_CYU_NM AS BNRI_CYU_NM";
			$sql .= "   , E.BNRI_SAI_CD AS BNRI_SAI_CD";
			$sql .= "   , E.BNRI_SAI_NM AS BNRI_SAI_NM";
			$sql .= "   , A.BIKO1 AS BIKO1";
			$sql .= "   , A.BIKO2 AS BIKO2";
			$sql .= "   , A.BIKO3 AS BIKO3";
			$sql .= "   , B.DT_SYUGYO_ST AS DT_SYUGYO_ST";
			$sql .= "   , B.DT_SYUGYO_ED AS DT_SYUGYO_ED";
			$sql .= "   , A.KOTEI_NO AS KOTEI_NO";
			$sql .= "   , A.DT_KOTEI_ST AS DT_KOTEI_ST";
			$sql .= "   , A.DT_KOTEI_ED AS DT_KOTEI_ED";
			$sql .= "   , CASE";
			$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NOT NULL THEN";
			$sql .= "         TRUNCATE( TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ED, '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) / 60 + .005, 2)";
			$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NULL THEN";
			$sql .= "         TRUNCATE( TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( NOW(), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) / 60 + .005, 2) ";
			$sql .= "       ELSE";
			$sql .= "         NULL";
			$sql .= "     END AS KOTEI_JIKAN_H";
			$sql .= "   , CASE";
			$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NOT NULL THEN";
			$sql .= "         TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ED, '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0)";
			$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NULL THEN";
			$sql .= "         TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( NOW(), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
			$sql .= "       ELSE";
			$sql .= "         NULL";
			$sql .= "     END AS KOTEI_JIKAN_M";
			$sql .= "   , TRUNCATE(L.JIKAN / 60 + .005, 2) AS S_JIKAN_H";
			$sql .= "   , L.JIKAN AS S_JIKAN_M";
			$sql .= "   , L.BUTURYO AS S_BUTURYO";
			$sql .= "   , L.SEISANSEI_TIME_BUTURYO AS SEISANSEI_KADO";
			$sql .= "   , M.SEISANSEI_KADO_BUTURYO AS SEISANSEI_JISSEKI";
			$sql .= " FROM";
			$sql .= "  T_KOTEI_D A ";
			$sql .= "  INNER JOIN T_KOTEI_H B ";
			$sql .= "    ON A.NINUSI_CD   = B.NINUSI_CD ";
			$sql .= "    AND A.SOSIKI_CD  = B.SOSIKI_CD ";
			$sql .= "    AND A.STAFF_CD   = B.STAFF_CD ";
			$sql .= "    AND A.YMD_SYUGYO = B.YMD_SYUGYO";
			$sql .= "  LEFT JOIN M_BNRI_DAI C";
			$sql .= "    ON  A.NINUSI_CD = C.NINUSI_CD";
			$sql .= "    AND A.SOSIKI_CD = C.SOSIKI_CD";
			$sql .= "    AND A.BNRI_DAI_CD = C.BNRI_DAI_CD";
			$sql .= "  LEFT JOIN M_BNRI_CYU D";
			$sql .= "    ON  A.NINUSI_CD = D.NINUSI_CD";
			$sql .= "    AND A.SOSIKI_CD = D.SOSIKI_CD";
			$sql .= "    AND A.BNRI_DAI_CD = D.BNRI_DAI_CD";
			$sql .= "    AND A.BNRI_CYU_CD = D.BNRI_CYU_CD";
			$sql .= "  LEFT JOIN M_BNRI_SAI E";
			$sql .= "    ON  A.NINUSI_CD = E.NINUSI_CD";
			$sql .= "    AND A.SOSIKI_CD = E.SOSIKI_CD";
			$sql .= "    AND A.BNRI_DAI_CD = E.BNRI_DAI_CD";
			$sql .= "    AND A.BNRI_CYU_CD = E.BNRI_CYU_CD";
			$sql .= "    AND A.BNRI_SAI_CD = E.BNRI_SAI_CD";
			$sql .= "  LEFT JOIN T_ABC_DB_DAY F";
			$sql .= "    ON  A.NINUSI_CD = F.NINUSI_CD ";
			$sql .= "    AND A.SOSIKI_CD  = F.SOSIKI_CD ";
			$sql .= "   AND A.YMD_SYUGYO = F.YMD";
			$sql .= "    AND A.BNRI_DAI_CD = F.BNRI_DAI_CD";
			$sql .= "    AND A.BNRI_CYU_CD = F.BNRI_CYU_CD";
			$sql .= "    AND A.BNRI_SAI_CD = F.BNRI_SAI_CD";
			$sql .= "  LEFT JOIN T_KOTEI_YOSOKU H";
			$sql .= "    ON A.NINUSI_CD   = H.NINUSI_CD ";
			$sql .= "    AND A.SOSIKI_CD  = H.SOSIKI_CD ";
			$sql .= "    AND A.YMD_SYUGYO = H.YMD_SYUGYO";
			$sql .= "    AND A.BNRI_DAI_CD = H.BNRI_DAI_CD";
			$sql .= "    AND A.BNRI_CYU_CD = H.BNRI_CYU_CD";
			$sql .= "    AND A.BNRI_SAI_CD = H.BNRI_SAI_CD";
			$sql .= "  LEFT JOIN M_NINUSI I";
			$sql .= "    ON A.NINUSI_CD   = I.NINUSI_CD ";
			$sql .= "  LEFT JOIN M_SOSIKI J";
			$sql .= "    ON A.SOSIKI_CD   = J.SOSIKI_CD ";
			$sql .= "  LEFT JOIN M_STAFF_KIHON K";
			$sql .= "    ON A.STAFF_CD = K.STAFF_CD";
			$sql .= "  LEFT JOIN T_ABC_DB_STAFF L";
			$sql .= "    ON A.NINUSI_CD   = L.NINUSI_CD ";
			$sql .= "    AND A.SOSIKI_CD  = L.SOSIKI_CD ";
			$sql .= "    AND A.YMD_SYUGYO = L.YMD";
			$sql .= "    AND A.STAFF_CD   = L.STAFF_CD ";
			$sql .= "    AND A.BNRI_DAI_CD = L.BNRI_DAI_CD";
			$sql .= "    AND A.BNRI_CYU_CD = L.BNRI_CYU_CD";
			$sql .= "    AND A.BNRI_SAI_CD = L.BNRI_SAI_CD";
			$sql .= "  LEFT JOIN T_STAFF_SYUGYO_JSK_NEAR M";
			$sql .= "    ON A.STAFF_CD   = M.STAFF_CD ";
			$sql .= "    AND A.BNRI_DAI_CD = M.BNRI_DAI_CD";
			$sql .= "    AND A.BNRI_CYU_CD = M.BNRI_CYU_CD";
			$sql .= "    AND A.BNRI_SAI_CD = M.BNRI_SAI_CD";

			$sql .= ' WHERE ';
			$sql .= "  A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  A.SOSIKI_CD='" . $this->_sosiki_cd . "'";

			//就業日付
			if ($YMD_FROM != ''){
				$sql .= " AND A.YMD_SYUGYO >= '" . $YMD_FROM . "'" ;
			}

			if ($YMD_TO != ''){
				$sql .= " AND A.YMD_SYUGYO <= '" . $YMD_TO . "'" ;
			}

			//工程
			if ($KOTEI != "") {

				$koteis = explode("_", $KOTEI);

				if (count($koteis) == 1) {

					$sql .= " AND A.BNRI_DAI_CD= '" . $koteis[0] . "'" ;

				} elseif (count($koteis) == 2) {

					$sql .= " AND A.BNRI_DAI_CD= '" . $koteis[0] . "'" ;
					$sql .= " AND A.BNRI_CYU_CD= '" . $koteis[1] . "'" ;

				} elseif (count($koteis) == 3) {

					$sql .= " AND A.BNRI_DAI_CD= '" . $koteis[0] . "'" ;
					$sql .= " AND A.BNRI_CYU_CD= '" . $koteis[1] . "'" ;
					$sql .= " AND A.BNRI_SAI_CD= '" . $koteis[2] . "'" ;

				}

			}

			$sql .= "    ORDER BY";
			$sql .= "      A.NINUSI_CD,";
			$sql .= "      A.SOSIKI_CD,";
			$sql .= "      A.YMD_SYUGYO,";
			$sql .= "      A.STAFF_CD,";
			$sql .= "      A.KOTEI_NO,";
			$sql .= "      A.BNRI_DAI_CD,";
			$sql .= "      A.BNRI_CYU_CD,";
			$sql .= "      A.BNRI_SAI_CD";
			$sql .= ";";


			$this->queryWithPDOLog($stmt,$pdo,$sql,"工程別データ 取得");


			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$NINUSI_CD = $result['NINUSI_CD'];
				$NINUSI_NM = $result['NINUSI_NM'];
				$SOSIKI_CD = $result['SOSIKI_CD'];
				$SOSIKI_NM = $result['SOSIKI_NM'];
				$YMD_SYUGYO = $result['YMD_SYUGYO'];
				$STAFF_CD = $result['STAFF_CD'];
				$STAFF_NM = $result['STAFF_NM'];
				$BNRI_DAI_CD = $result['BNRI_DAI_CD'];
				$BNRI_DAI_NM = $result['BNRI_DAI_NM'];
				$BNRI_CYU_CD = $result['BNRI_CYU_CD'];
				$BNRI_CYU_NM = $result['BNRI_CYU_NM'];
				$BNRI_SAI_CD = $result['BNRI_SAI_CD'];
				$BNRI_SAI_NM = $result['BNRI_SAI_NM'];
				$BIKO1 = $result['BIKO1'];
				$BIKO2 = $result['BIKO2'];
				$BIKO3 = $result['BIKO3'];
				$DT_SYUGYO_ST = $result['DT_SYUGYO_ST'];
				$DT_SYUGYO_ED = $result['DT_SYUGYO_ED'];
				$KOTEI_NO = $result['KOTEI_NO'];
				$DT_KOTEI_ST = $result['DT_KOTEI_ST'];
				$DT_KOTEI_ED = $result['DT_KOTEI_ED'];
				$KOTEI_JIKAN_H = $result['KOTEI_JIKAN_H'];
				$KOTEI_JIKAN_M = $result['KOTEI_JIKAN_M'];
				$S_JIKAN_H = $result['S_JIKAN_H'];
				$S_JIKAN_M = $result['S_JIKAN_M'];
				$S_BUTURYO = $result['S_BUTURYO'];
				$SEISANSEI_KADO = $result['SEISANSEI_KADO'];
				$SEISANSEI_JISSEKI = $result['SEISANSEI_JISSEKI'];



				$text[] = '"' . $NINUSI_CD . '","' . $NINUSI_NM . '","' . $SOSIKI_CD . '","' . $SOSIKI_NM . '",' . $YMD_SYUGYO . ',"' .
						$STAFF_CD . '","' . $STAFF_NM  . '",' . $KOTEI_NO . ',"' .
						$BNRI_DAI_CD . '","' . $BNRI_DAI_NM . '","' . $BNRI_CYU_CD . '","' .
						$BNRI_CYU_NM . '","' . $BNRI_SAI_CD . '","' . $BNRI_SAI_NM . '","' . $BIKO1 . '","' . $BIKO2 . '","' . $BIKO3 . '",' .
						$DT_SYUGYO_ST . ',' . $DT_SYUGYO_ED . ',' .
						$DT_KOTEI_ST . ',' . $DT_KOTEI_ED . ',' . $KOTEI_JIKAN_H . ',' . $KOTEI_JIKAN_M . ',' .
						$S_JIKAN_H . ',' . $S_JIKAN_M . ',' .
				        $S_BUTURYO . ',' . $SEISANSEI_KADO . ',' . $SEISANSEI_JISSEKI;

			}

			$pdo = null;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM050", $e->getMessage());
			$pdo = null;
		}


		return $text;
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
			$sql = "CALL P_TPM050_GET_TIMESTAMP(";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " @timestamp";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, '工程予測　タイムスタンプ取得');

			$sql = "";
			$sql .= " SELECT";
			$sql .= "  @timestamp";


			$this->queryWithPDOLog($stmt,$pdo,$sql, '工程予測　タイムスタンプ取得');
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
	 * タイムスタンプから最新情報かチェック
	 * @access   public
	 * @param    タイムスタンプ
	 * @return   成否情報(true,false)
	 */
	public function checkTimestamp($currentTimestamp) {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//タイムスタンプ取得
			$sql = "CALL P_TPM050_GET_TIMESTAMP(";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " @timestamp";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, '工程予測　タイムスタンプ取得');

			$sql = "";
			$sql .= " SELECT";
			$sql .= "  @timestamp";


			$this->queryWithPDOLog($stmt,$pdo,$sql, '工程予測　タイムスタンプ取得');
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			$timestamp = $result['@timestamp'];

			if ($timestamp == null) {
				$timestamp = "1900-01-01 00:00:00";
			}

			$pdo = null;

			//画面取得時タイムスタンプと、DB最新タイムスタンプを比較
			if (strtotime($currentTimestamp) >= strtotime($timestamp)) {

				return true;
			} else {

				$message =$this->MMessage->getOneMessage('CMNE000106');
				$this->errors['Check'][] = $message;
				return false;
			}


		} catch (Exception $e){

			$this->printLog("fatal", "例外発生", "TPM050", $e->getMessage());
			$pdo = null;
		}

		//例外の場合、例外メッセージ
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}

}

?>