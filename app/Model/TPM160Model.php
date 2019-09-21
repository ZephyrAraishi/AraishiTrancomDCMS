<?php
/**
 * TPM160 Model
 *
 * 工程配置進捗2(大高事業所用）TPM060ベース
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class TPM160Model extends DCMSAppModel{

	public $name = 'TPM160Model';
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


	function getCSV($YMD_FROM, $YMD_TO, $KOTEI, $timeArray= array(), $ninzu = '', $jikan = '', $kotei2 = '') {

		$text = array();

		//ヘッダー情報
		$header = "担当者コード,所属,雇用形態コード,雇用形態,氏名,";
		$header .= "5：00,5：15,5：30,5：45,";
		$header .= "6：00,6：15,6：30,6：45,";
		$header .= "7：00,7：15,7：30,7：45,";
		$header .= "8：00,8：15,8：30,8：45,";
		$header .= "9：00,9：15,9：30,9：45,";
		$header .= "10：00,10：15,10：30,10：45,";
		$header .= "11：00,11：15,11：30,11：45,";
		$header .= "12：00,12：15,12：30,12：45,";
		$header .= "13：00,13：15,13：30,13：45,";
		$header .= "14：00,14：15,14：30,14：45,";
		$header .= "15：00,15：15,15：30,15：45,";
		$header .= "16：00,16：15,16：30,16：45,";
		$header .= "17：00,17：15,17：30,17：45,";
		$header .= "18：00,18：15,18：30,18：45,";
		$header .= "19：00,19：15,19：30,19：45,";
		$header .= "20：00,20：15,20：30,20：45,";
		$header .= "21：00,21：15,21：30,21：45,";
		$header .= "22：00,22：15,22：30,22：45,";
		$header .= "23：00,23：15,23：30,23：45,";
		$header .= "24：00,24：15,24：30,24：45,";
		$header .= "25：00,25：15,25：30,25：45,";
		$header .= "26：00,26：15,26：30,26：45,";
		$header .= "27：00,27：15,27：30,27：45,";
		$header .= "28：00,28：15,28：30,28：45,就業日";

		$text[] = $header;

		//連想配列の準備
		$initBody = array();
		$initBody = [
				'STAFF_CD' => '',
				'SYOZOKU' => '',
				'KOYO_CD' => '',
				'KOYO_NM' => '',
				'STAFF_NM' => '',
				'0500' => '','0515' => '','0530' => '','0545' => '',
				'0600' => '','0615' => '','0630' => '','0645' => '',
				'0700' => '','0715' => '','0730' => '','0745' => '',
				'0800' => '','0815' => '','0830' => '','0845' => '',
				'0900' => '','0915' => '','0930' => '','0945' => '',
				'1000' => '','1015' => '','1030' => '','1045' => '',
				'1100' => '','1115' => '','1130' => '','1145' => '',
				'1200' => '','1215' => '','1230' => '','1245' => '',
				'1300' => '','1315' => '','1330' => '','1345' => '',
				'1400' => '','1415' => '','1430' => '','1445' => '',
				'1500' => '','1515' => '','1530' => '','1545' => '',
				'1600' => '','1615' => '','1630' => '','1645' => '',
				'1700' => '','1715' => '','1730' => '','1745' => '',
				'1800' => '','1815' => '','1830' => '','1845' => '',
				'1900' => '','1915' => '','1930' => '','1945' => '',
				'2000' => '','2015' => '','2030' => '','2045' => '',
				'2100' => '','2115' => '','2130' => '','2145' => '',
				'2200' => '','2215' => '','2230' => '','2245' => '',
				'2300' => '','2315' => '','2330' => '','2345' => '',
				'2400' => '','2415' => '','2430' => '','2445' => '',
				'2500' => '','2515' => '','2530' => '','2545' => '',
				'2600' => '','2615' => '','2630' => '','2645' => '',
				'2700' => '','2715' => '','2730' => '','2745' => '',
				'2800' => '','2815' => '','2830' => '','2845' => '','YMD_SYUGYO' => ''
		];

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			//工程データの取得
			$sql  = "SELECT ";
			$sql .= " M1.STAFF_CD";
			$sql .= ",M2.KAISYA_NM";
			$sql .= ",M1.KBN_KEIYAKU";
			$sql .= ",M3.MEI_1";
			$sql .= ",M1.STAFF_NM";
			$sql .= ",T1.YMD_SYUGYO";
			$sql .= ",T1.DT_SYUGYO_ST";
			$sql .= ",T1.DT_SYUGYO_ED";
			$sql .= ",T2.KOTEI_NO";
			$sql .= ",T2.BNRI_DAI_CD";
			$sql .= ",M4.BNRI_DAI_NM";
			$sql .= ",T2.BNRI_CYU_CD";
			$sql .= ",M5.BNRI_CYU_NM";
			$sql .= ",T2.BNRI_SAI_CD";
			$sql .= ",M6.BNRI_SAI_NM";
			$sql .= ",T2.DT_KOTEI_ST";
			$sql .= ",LEFT(REPLACE(SEC_TO_TIME((TIME_TO_SEC(T2.DT_KOTEI_ST) DIV 900) * 900),':',''),4) AS DT_KOTEI_ST_ROUND";
			$sql .= ",T2.DT_KOTEI_ED";
			$sql .= ",LEFT(REPLACE(SEC_TO_TIME((TIME_TO_SEC(T2.DT_KOTEI_ED) DIV 900) * 900),':',''),4) AS DT_KOTEI_ED_ROUND";
			$sql .= " FROM M_STAFF_KIHON M1 INNER JOIN M_HAKEN_KAISYA M2";
			$sql .= "   ON M1.DAI_NINUSI_CD=M2.NINUSI_CD";
			$sql .= "  AND M1.DAI_SOSIKI_CD=M2.SOSIKI_CD";
			$sql .= "  AND M1.HAKEN_KAISYA_CD=M2.KAISYA_CD";
			$sql .= " INNER JOIN M_MEISYO M3";
			$sql .= "   ON M1.DAI_NINUSI_CD=M3.NINUSI_CD";
			$sql .= "  AND M1.DAI_SOSIKI_CD=M3.SOSIKI_CD";
			$sql .= "   AND M3.MEI_KBN='30'";
			$sql .= "   AND M1.HAKEN_KAISYA_CD=M3.MEI_CD";
			$sql .= " INNER JOIN T_KOTEI_H T1";
			$sql .= "   ON M1.STAFF_CD=T1.STAFF_CD";
			$sql .= " INNER JOIN T_KOTEI_D T2";
			$sql .= "   ON T1.STAFF_CD=T2.STAFF_CD";
			$sql .= "   AND T1.YMD_SYUGYO=T2.YMD_SYUGYO";
			$sql .= " INNER JOIN M_BNRI_DAI M4";
			$sql .= "   ON T2.NINUSI_CD=M4.NINUSI_CD";
			$sql .= "   AND T2.SOSIKI_CD=M4.SOSIKI_CD";
			$sql .= "   AND T2.BNRI_DAI_CD=M4.BNRI_DAI_CD";
			$sql .= " INNER JOIN M_BNRI_CYU M5";
			$sql .= "   ON T2.NINUSI_CD=M5.NINUSI_CD";
			$sql .= "   AND T2.SOSIKI_CD=M5.SOSIKI_CD";
			$sql .= "   AND T2.BNRI_DAI_CD=M5.BNRI_DAI_CD";
			$sql .= "   AND T2.BNRI_CYU_CD=M5.BNRI_CYU_CD";
			$sql .= " INNER JOIN M_BNRI_SAI M6";
			$sql .= "   ON T2.NINUSI_CD=M6.NINUSI_CD";
			$sql .= "   AND T2.SOSIKI_CD=M6.SOSIKI_CD";
			$sql .= "   AND T2.BNRI_DAI_CD=M6.BNRI_DAI_CD";
			$sql .= "   AND T2.BNRI_CYU_CD=M6.BNRI_CYU_CD";
			$sql .= "   AND T2.BNRI_SAI_CD=M6.BNRI_SAI_CD";

			$sql .= ' WHERE ';
			$sql .= "  T1.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  T1.SOSIKI_CD='" . $this->_sosiki_cd . "'";

			//就業日付
			if ($YMD_FROM != ''){
				$sql .= " AND T1.YMD_SYUGYO >= '" . $YMD_FROM . "'" ;
			}

			if ($YMD_TO != ''){
				$sql .= " AND T1.YMD_SYUGYO <= '" . $YMD_TO . "'" ;
			}

			//工程
			if ($KOTEI != "") {

				$koteis = explode("_", $KOTEI);

				if (count($koteis) == 1) {

					$sql .= " AND T2.BNRI_DAI_CD= '" . $koteis[0] . "'" ;

				} elseif (count($koteis) == 2) {

					$sql .= " AND T2.BNRI_DAI_CD= '" . $koteis[0] . "'" ;
					$sql .= " AND T2.BNRI_CYU_CD= '" . $koteis[1] . "'" ;

				} elseif (count($koteis) == 3) {

					$sql .= " AND T2.BNRI_DAI_CD= '" . $koteis[0] . "'" ;
					$sql .= " AND T2.BNRI_CYU_CD= '" . $koteis[1] . "'" ;
					$sql .= " AND T2.BNRI_SAI_CD= '" . $koteis[2] . "'" ;

				}

			}

			if ($kotei2 != "") {


				$sql .= " AND (" ;
				$sql .= "       M4.BNRI_DAI_NM LIKE " . $pdo->quote("%" .$kotei2 . "%") . " OR" ;
				$sql .= "       M5.BNRI_CYU_NM LIKE " . $pdo->quote("%" .$kotei2 . "%") . " OR" ;
				$sql .= "       M6.BNRI_SAI_NM LIKE " . $pdo->quote("%" .$kotei2 . "%");
				$sql .= "     )" ;


			}

			$sql .= "    ORDER BY";
			$sql .= "      T1.YMD_SYUGYO,";
			$sql .= "      T1.STAFF_CD,";
			$sql .= "      T2.KOTEI_NO";
			$sql .= ";";


			$this->queryWithPDOLog($stmt,$pdo,$sql,"工程別データ 取得");

			$Body = $initBody;
			$strSaveYmdSyugyo = '';
			$strSaveStaffCd = '';
			$intLineCounter = 0;
			$strSTKotei = '';
			$strEDKotei = '';

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				if ($strSaveYmdSyugyo == $result['YMD_SYUGYO'] && $strSaveStaffCd == $result['STAFF_CD']) {

					//工程の開始終了の設定
					$intST = $this->getArrayNumber($this->setRoundTime($result['DT_KOTEI_ST_ROUND']),$Body);
					$intED = $this->getArrayNumber($this->setRoundTime($result['DT_KOTEI_ED_ROUND']),$Body);
					for ($i=$intST;$i<=$intED;$i++) {
						$Body[key(array_slice($Body,$i,1,true))] = abs($result['BNRI_CYU_CD'] . $result['BNRI_SAI_CD']);
					}

				} else {
					//就業日かスタッフが変わったのでCSVデータを掃き出し
					if ($intLineCounter != 0) {
						$tmpText = '';
						foreach ($Body as $key => $value) {
							$tmpText .= $value . ',';
						}
						$text[] = $tmpText;
						$Body = $initBody;
					}

					//新しい就業日とスタッフの設定
					$strSaveYmdSyugyo = $result['YMD_SYUGYO'];
					$strSaveStaffCd = $result['STAFF_CD'];
					$Body['STAFF_CD'] = $result['STAFF_CD'];
					$Body['SYOZOKU'] = $result['KAISYA_NM'];
					$Body['KOYO_CD'] = $result['KBN_KEIYAKU'];
					$Body['KOYO_NM'] = $result['MEI_1'];
					$Body['STAFF_NM'] = $result['STAFF_NM'];
					$Body['YMD_SYUGYO'] = $result['YMD_SYUGYO'];

					//工程の開始終了の設定
					$intST = $this->getArrayNumber($this->setRoundTime($result['DT_KOTEI_ST_ROUND']),$Body);
					$intED = $this->getArrayNumber($this->setRoundTime($result['DT_KOTEI_ED_ROUND']),$Body);
					for ($i=$intST;$i<=$intED;$i++) {
						$Body[key(array_slice($Body,$i,1,true))] = abs($result['BNRI_CYU_CD'] . $result['BNRI_SAI_CD']);
					}

				}

				$intLineCounter++;
			}

			$tmpText = '';
			foreach ($Body as $key => $value) {
				$tmpText .= $value . ',';
			}
			$text[] = $tmpText;

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
	 * 丸め時間を０時から４時まで２４時から２８時の文字列に変換する
	 */
	function setRoundTime($strRoundTime) {
		$strWork = '';
		switch (substr($strRoundTime,0,2)) {
			case '00':
				$strWork = '24' . substr($strRoundTime,-2);
				break;
			case '01':
				$strWork = '25' . substr($strRoundTime,-2);
				break;
			case '02':
				$strWork = '26' . substr($strRoundTime,-2);
				break;
			case '03':
				$strWork = '27' . substr($strRoundTime,-2);
				break;
			case '04':
				$strWork = '28' . substr($strRoundTime,-2);
				break;
			default:
				$strWork = $strRoundTime;
				break;
		}
		return $strWork;
	}

	/**
	 * 連想配列のインデックスより何番目かを返す
	 */
	function getArrayNumber($strIndex, $BodyArray = array()) {
		$aryWork = array();
		$aryWork = array_flip(array_keys($BodyArray));
		return $aryWork[$strIndex];
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