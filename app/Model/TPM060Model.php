<?php
/**
 * TPM060Model
 *
 * 工程配置進捗2
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class TPM060Model extends DCMSAppModel{

	public $name = 'TPM060Model';
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
	public function getKoteiList($YMD_FROM = '',$YMD_TO = '',$KOTEI = '',$timeArray= array(),$ninzu = '',$jikan = '',$kotei2 = '') {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			//工程データの取得
			$sql  = "SELECT ";
			$sql .= "     A.NINUSI_CD AS NINUSI_CD";
			$sql .= "   , I.NINUSI_NM AS NINUSI_NM";
			$sql .= "   , A.SOSIKI_CD AS SOSIKI_CD";
			$sql .= "   , J.SOSIKI_NM AS SOSIKI_NM";
			$sql .= "   , A.YMD_SYUGYO AS YMD_SYUGYO";
			$sql .= "   , C.BNRI_DAI_CD AS BNRI_DAI_CD";
			$sql .= "   , C.BNRI_DAI_RYAKU AS BNRI_DAI_RYAKU";
			$sql .= "   , D.BNRI_CYU_CD AS BNRI_CYU_CD";
			$sql .= "   , D.BNRI_CYU_RYAKU AS BNRI_CYU_RYAKU";
			$sql .= "   , E.BNRI_SAI_CD AS BNRI_SAI_CD";
			$sql .= "   , E.BNRI_SAI_RYAKU AS BNRI_SAI_RYAKU";

			if ($ninzu == 'on') {

				foreach ($timeArray as $array) {

					$sql .= "   , SUM(CASE";
					$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NOT NULL THEN";//工程が閉じている
					$sql .= "         CASE";
					$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり
					$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ED AND";
					$sql .= "                 DT_KOTEI_ED <= ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
					$sql .= "              1";
					$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり
					$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
					$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') < DT_KOTEI_ED THEN";
					$sql .= "              1";
					$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//時間内またがり
					$sql .= "                 DT_KOTEI_ED <= ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
					$sql .= "              1";
					$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//時間外またがり
					$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') < DT_KOTEI_ED THEN";
					$sql .= "              1";
					$sql .= "            ELSE";
					$sql .= "              0";
					$sql .= "          END";
					$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NULL THEN";
					$sql .= "         CASE";
					$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり・範囲が現時間より過去
					$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
					$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') <= NOW() THEN";
					$sql .= "              1 ";
					$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり・範囲が現時間内
					$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
					$sql .= "                 NOW() < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
					$sql .= "              1 ";
					$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり・範囲が現時間より過去
					$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
					$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
					$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') <= NOW() THEN";
					$sql .= "              1 ";
					$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり・範囲が現時内
					$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
					$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
					$sql .= "                 NOW() < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
					$sql .= "              1 ";
					$sql .= "            ELSE";
					$sql .= "              0";
					$sql .= "          END";
					$sql .= "       ELSE";
					$sql .= "         0";
					$sql .= "     END) AS NINZU" . str_replace(":", "", $array);
				}
			}

			if ($jikan == 'on') {

				foreach ($timeArray as $array) {

					$sql .= "   , SUM(CASE";
					$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NOT NULL THEN";//工程が閉じている
					$sql .= "         CASE";
					$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり
					$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ED AND";
					$sql .= "                 DT_KOTEI_ED <= ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
					$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ED, '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ), '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
					$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり
					$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
					$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') < DT_KOTEI_ED THEN";
					$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00'), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
					$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//時間内またがり
					$sql .= "                 DT_KOTEI_ED <= ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
					$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ED, '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
					$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//時間外またがり
					$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') < DT_KOTEI_ED THEN";
					$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00'), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ), '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
					$sql .= "            ELSE";
					$sql .= "              0";
					$sql .= "          END";
					$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NULL THEN";
					$sql .= "         CASE";
					$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり・範囲が現時間より過去
					$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
					$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') <= NOW() THEN";
					$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00'), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ), '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
					$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり・範囲が現時間内
					$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
					$sql .= "                 NOW() < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
					$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( NOW(), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ), '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
					$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり・範囲が現時間より過去
					$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
					$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
					$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') <= NOW() THEN";
					$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00'), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
					$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり・範囲が現時内
					$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
					$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
					$sql .= "                 NOW() < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
					$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( NOW(), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
					$sql .= "            ELSE";
					$sql .= "              0";
					$sql .= "          END";
					$sql .= "       ELSE";
					$sql .= "         0";
					$sql .= "     END) AS JIKAN" . str_replace(":", "", $array);
				}

			}

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
			$sql .= "  LEFT JOIN M_NINUSI I";
			$sql .= "    ON A.NINUSI_CD   = I.NINUSI_CD ";
			$sql .= "  LEFT JOIN M_SOSIKI J";
			$sql .= "    ON A.SOSIKI_CD   = J.SOSIKI_CD ";
			$sql .= "  LEFT JOIN M_STAFF_KIHON K";
			$sql .= "    ON A.STAFF_CD = K.STAFF_CD";


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

			if ($kotei2 != "") {


				$sql .= " AND (" ;
				$sql .= "       C.BNRI_DAI_NM LIKE " . $pdo->quote("%" .$kotei2 . "%") . " OR" ;
				$sql .= "       D.BNRI_CYU_NM LIKE " . $pdo->quote("%" .$kotei2 . "%") . " OR" ;
				$sql .= "       E.BNRI_SAI_NM LIKE " . $pdo->quote("%" .$kotei2 . "%");
				$sql .= "     )" ;


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

				$ninzuArray = array();

				foreach ($timeArray as $time) {

					if ($ninzu == 'on') {

						$ninzuArray[] = $result["NINZU" . str_replace(":", "", $time)];
					}else {
						$ninzuArray[] = '';
					}

				}

				$jikanArray = array();

				foreach ($timeArray as $time) {

					if ($jikan == 'on') {

						$jikanArray[] = $result["JIKAN" . str_replace(":", "", $time)];
					}else {
						$jikanArray[] = '';
					}

				}

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
					"NINZU" => $ninzuArray,
					"JIKAN" => $jikanArray

				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM060", $e->getMessage());
			$pdo = null;
		}


		return array();
	}


	function getCSV($YMD_FROM, $YMD_TO, $KOTEI,$timeArray= array(),$ninzu = '',$jikan = '',$kotei2 = '') {

		$text = array();

		//ヘッダー情報
		$header = "荷主CD,荷主名,組織CD,組織名,年月日,大分類CD,大分類名称,中分類CD,中分類名称,細分類CD,細分類名称,";
		$header .= "工程開始時間,工程終了時間,作業総時間(時),作業総時間(分),";

		foreach ($timeArray as $array) {
			$header .= $array . "(人員),";
			$header .= $array . "(時間),";
		}

		$text[] = $header;

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


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
			$sql .= "   , NOW() AS NOW_TIME";

			$sql .= "   , MIN(CASE";
			$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NOT NULL THEN";//工程が閉じている
			$sql .= "         CASE";

			foreach ($timeArray as $array) {

				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ED AND";
				$sql .= "                 DT_KOTEI_ED <= ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' )";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり
				$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') < DT_KOTEI_ED THEN";
				$sql .= "              DT_KOTEI_ST";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//時間内またがり
				$sql .= "                 DT_KOTEI_ED <= ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              DT_KOTEI_ST";
				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//時間外またがり
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') < DT_KOTEI_ED THEN";
				$sql .= "              ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' )";

			}

			$sql .= "            ELSE";
			$sql .= "              NULL";
			$sql .= "            END";
			$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NULL THEN";
			$sql .= "         CASE";

			foreach ($timeArray as $array) {

				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり・範囲が現時間より過去
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') <= NOW() THEN";
				$sql .= "              ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) ";
				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり・範囲が現時間内
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 NOW() < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) ";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり・範囲が現時間より過去
				$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') <= NOW() THEN";
				$sql .= "              DT_KOTEI_ST ";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり・範囲が現時内
				$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 NOW() < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              DT_KOTEI_ST ";

			}

			$sql .= "            ELSE";
			$sql .= "              NULL";
			$sql .= "            END";
			$sql .= "       ELSE";
			$sql .= "         NULL";
			$sql .= "     END) AS MIN_KOTEI_ST";

			$sql .= "   , MAX(CASE";
			$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NOT NULL THEN";//工程が閉じている
			$sql .= "         CASE";

			$reverseTimeArray = array_reverse($timeArray);

			foreach ($reverseTimeArray as $array) {

				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ED AND";
				$sql .= "                 DT_KOTEI_ED <= ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              DT_KOTEI_ED";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり
				$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') < DT_KOTEI_ED THEN";
				$sql .= "              ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00')";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//時間内またがり
				$sql .= "                 DT_KOTEI_ED <= ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              DT_KOTEI_ED";
				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//時間外またがり
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') < DT_KOTEI_ED THEN";
				$sql .= "              ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00')";

			}

			$sql .= "            ELSE";
			$sql .= "              NULL";
			$sql .= "            END";
			$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NULL THEN";
			$sql .= "         CASE";

			foreach ($reverseTimeArray as $array) {

				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり・範囲が現時間より過去
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') <= NOW() THEN";
				$sql .= "              ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') ";
				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり・範囲が現時間内
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 NOW() < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              NOW() ";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり・範囲が現時間より過去
				$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') <= NOW() THEN";
				$sql .= "              ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') ";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり・範囲が現時内
				$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 NOW() < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              NOW() ";

			}

			$sql .= "            ELSE";
			$sql .= "              NULL";
			$sql .= "            END";
			$sql .= "       ELSE";
			$sql .= "         NULL";
			$sql .= "     END) AS MAX_KOTEI_ED";

			$sql .= "   , TRUNCATE(SUM(CASE";
			$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NOT NULL THEN";//工程が閉じている

			foreach ($timeArray as $array) {

				$sql .= "         (CASE";
				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ED AND";
				$sql .= "                 DT_KOTEI_ED <= ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ED, '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ), '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり
				$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') < DT_KOTEI_ED THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00'), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//時間内またがり
				$sql .= "                 DT_KOTEI_ED <= ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ED, '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//時間外またがり
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') < DT_KOTEI_ED THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00'), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ), '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "          ELSE";
				$sql .= "            0";
				$sql .= "          END)";

				if ($timeArray[count($timeArray) -1] != $array) {
					$sql .= "      +";
				}

			}

			$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NULL THEN";

			foreach ($timeArray as $array) {

				$sql .= "         (CASE";
				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり・範囲が現時間より過去
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') <= NOW() THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00'), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ), '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり・範囲が現時間内
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 NOW() < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( NOW(), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ), '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり・範囲が現時間より過去
				$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') <= NOW() THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00'), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり・範囲が現時内
				$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 NOW() < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( NOW(), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "          ELSE";
				$sql .= "            0";
				$sql .= "          END)";

				if ($timeArray[count($timeArray) -1] != $array) {
					$sql .= "      +";
				}

			}

			$sql .= "       ELSE";
			$sql .= "         0";
			$sql .= "     END)/60 + .005, 2) AS JIKAN_H";

			$sql .= "   , SUM(CASE";
			$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NOT NULL THEN";//工程が閉じている

			foreach ($timeArray as $array) {

				$sql .= "         (CASE";
				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ED AND";
				$sql .= "                 DT_KOTEI_ED <= ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ED, '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ), '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり
				$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') < DT_KOTEI_ED THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00'), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//時間内またがり
				$sql .= "                 DT_KOTEI_ED <= ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ED, '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//時間外またがり
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') < DT_KOTEI_ED THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00'), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ), '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "          ELSE";
				$sql .= "            0";
				$sql .= "          END)";

				if ($timeArray[count($timeArray) -1] != $array) {
					$sql .= "      +";
				}

			}

			$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NULL THEN";

			foreach ($timeArray as $array) {

				$sql .= "         (CASE";
				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり・範囲が現時間より過去
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') <= NOW() THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00'), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ), '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり・範囲が現時間内
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 NOW() < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( NOW(), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ), '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり・範囲が現時間より過去
				$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') <= NOW() THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00'), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり・範囲が現時内
				$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 NOW() < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( NOW(), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "          ELSE";
				$sql .= "            0";
				$sql .= "          END)";

				if ($timeArray[count($timeArray) -1] != $array) {
					$sql .= "      +";
				}

			}

			$sql .= "       ELSE";
			$sql .= "         0";
			$sql .= "     END) AS JIKAN_M";


			if ($ninzu == 'on') {

				foreach ($timeArray as $array) {

					$sql .= "   , SUM(CASE";
					$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NOT NULL THEN";//工程が閉じている
					$sql .= "         CASE";
					$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり
					$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ED AND";
					$sql .= "                 DT_KOTEI_ED <= ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
					$sql .= "              1";
					$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり
					$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
					$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') < DT_KOTEI_ED THEN";
					$sql .= "              1";
					$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//時間内またがり
					$sql .= "                 DT_KOTEI_ED <= ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
					$sql .= "              1";
					$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//時間外またがり
					$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') < DT_KOTEI_ED THEN";
					$sql .= "              1";
					$sql .= "            ELSE";
					$sql .= "              0";
					$sql .= "          END";
					$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NULL THEN";
					$sql .= "         CASE";
					$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり・範囲が現時間より過去
					$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
					$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') <= NOW() THEN";
					$sql .= "              1 ";
					$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり・範囲が現時間内
					$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
					$sql .= "                 NOW() < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
					$sql .= "              1 ";
					$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり・範囲が現時間より過去
					$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
					$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
					$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') <= NOW() THEN";
					$sql .= "              1 ";
					$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり・範囲が現時内
					$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
					$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
					$sql .= "                 NOW() < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
					$sql .= "              1 ";
					$sql .= "            ELSE";
					$sql .= "              0";
					$sql .= "          END";
					$sql .= "       ELSE";
					$sql .= "         0";
					$sql .= "     END) AS NINZU" . str_replace(":", "", $array);
				}

			}

			foreach ($timeArray as $array) {

				$sql .= "   , SUM(CASE";
				$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NOT NULL THEN";//工程が閉じている
				$sql .= "         CASE";
				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ED AND";
				$sql .= "                 DT_KOTEI_ED <= ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ED, '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ), '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり
				$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') < DT_KOTEI_ED THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00'), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//時間内またがり
				$sql .= "                 DT_KOTEI_ED <= ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ED, '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//時間外またがり
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') < DT_KOTEI_ED THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00'), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ), '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            ELSE";
				$sql .= "              0";
				$sql .= "          END";
				$sql .= "       WHEN DT_KOTEI_ST IS NOT NULL AND DT_KOTEI_ED IS NULL THEN";
				$sql .= "         CASE";
				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり・範囲が現時間より過去
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') <= NOW() THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00'), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ), '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            WHEN DT_KOTEI_ST < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) AND";//開始またがり・範囲が現時間内
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 NOW() < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( NOW(), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ), '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり・範囲が現時間より過去
				$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') <= NOW() THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00'), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            WHEN ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= DT_KOTEI_ST AND";//終了またがり・範囲が現時内
				$sql .= "                 DT_KOTEI_ST < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') AND";
				$sql .= "                 ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ) <= NOW() AND";
				$sql .= "                 NOW() < ADDTIME(ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$array.":00' ),'00:30:00') THEN";
				$sql .= "              TRUNCATE( (UNIX_TIMESTAMP( DATE_FORMAT( NOW(), '%Y-%m-%d %H:%i' ) ) - UNIX_TIMESTAMP( DATE_FORMAT( DT_KOTEI_ST, '%Y-%m-%d %H:%i' ) )) / 60 + .9, 0) ";
				$sql .= "            ELSE";
				$sql .= "              0";
				$sql .= "          END";
				$sql .= "       ELSE";
				$sql .= "         0";
				$sql .= "     END) AS JIKAN" . str_replace(":", "", $array);
			}

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
			$sql .= "  LEFT JOIN M_NINUSI I";
			$sql .= "    ON A.NINUSI_CD   = I.NINUSI_CD ";
			$sql .= "  LEFT JOIN M_SOSIKI J";
			$sql .= "    ON A.SOSIKI_CD   = J.SOSIKI_CD ";
			$sql .= "  LEFT JOIN M_STAFF_KIHON K";
			$sql .= "    ON A.STAFF_CD = K.STAFF_CD";


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

			if ($kotei2 != "") {


				$sql .= " AND (" ;
				$sql .= "       C.BNRI_DAI_NM LIKE " . $pdo->quote("%" .$kotei2 . "%") . " OR" ;
				$sql .= "       D.BNRI_CYU_NM LIKE " . $pdo->quote("%" .$kotei2 . "%") . " OR" ;
				$sql .= "       E.BNRI_SAI_NM LIKE " . $pdo->quote("%" .$kotei2 . "%");
				$sql .= "     )" ;


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

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$tmpText = $result['NINUSI_CD'] . ',';
				$tmpText .= $result['NINUSI_NM'] . ',';
				$tmpText .= $result['SOSIKI_CD'] . ',';
				$tmpText .= $result['SOSIKI_NM'] . ',';
				$tmpText .= $result['YMD_SYUGYO'] . ',';
				$tmpText .= $result['BNRI_DAI_CD'] . ',';
				$tmpText .= $result['BNRI_DAI_NM'] . ',';
				$tmpText .= $result['BNRI_CYU_CD'] . ',';
				$tmpText .= $result['BNRI_CYU_NM'] . ',';
				$tmpText .= $result['BNRI_SAI_CD'] . ',';
				$tmpText .= $result['BNRI_SAI_NM'] . ',';
				$tmpText .= $result['MIN_KOTEI_ST'] . ',';

				if ($result['MAX_KOTEI_ED'] == $result['NOW_TIME']) {

					$tmpText .= ',';
				} else {
					$tmpText .= $result['MAX_KOTEI_ED'] . ',';
				}


				$tmpText .= $result["JIKAN_H"] . ',';
				$tmpText .= $result["JIKAN_M"] . ',';


				if ($ninzu == 'on' && $jikan =='on') {

					foreach ($timeArray as $time) {

						$tmpText .= $result["NINZU" . str_replace(":", "", $time)] . ',';
						$tmpText .= $result["JIKAN" . str_replace(":", "", $time)] . ',';
					}

				} else if ($ninzu == 'on' && $jikan !='on') {

					foreach ($timeArray as $time) {

						$tmpText .= $result["NINZU" . str_replace(":", "", $time)] . ',';
						$tmpText .= ',';
					}

				} else if ($ninzu != 'on' && $jikan =='on') {

					foreach ($timeArray as $time) {

						$tmpText .= ',';
						$tmpText .= $result["JIKAN" . str_replace(":", "", $time)] . ',';

					}

				} else {

					foreach ($timeArray as $time) {

						$tmpText .= ',';
						$tmpText .= ',';
					}

				}

				$text[] = $tmpText;

			}

			$pdo = null;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM060", $e->getMessage());
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
	 * 時間一覧取得
	 * @access   public
	 * @return   日付
	 */
	function getTimes() {

		$timeArray = array();


		try {



			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


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
			$times = explode(":", $dt_syugyo_st_base);

			if (count($times) < 1) {
				return $timeArray;
			}

			$hour = (int)$times[0];
			$min = (int)$times[1];

			$timeArray[] = sprintf('%02d', $hour) . ":" . sprintf('%02d', $min);

			for ($i = 0; $i < 71; $i++) {

				if ($min == 30) {

					$hour += 1;
					$min = 0;

				} else {

					$min = 30;

				}

				$timeArray[] = sprintf('%02d', $hour) . ":" . sprintf('%02d', $min);

			}

			$pdo = null;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM060", $e->getMessage());
			$pdo = null;
		}

		return $timeArray;

	}

}

?>