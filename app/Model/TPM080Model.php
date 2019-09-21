<?php
/**
 * TPM080Model
 *
 * 種蒔き進捗画面
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class TPM080Model extends DCMSAppModel{

	public $name = 'TPM080Model';
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
	 * 種蒔き進捗データ取得
	 * @access   public
	 * @return   種蒔き進捗データ
	 */
	public function getList($ymd_from = '',$ymd_to = '',$BatchNo_from = '',$BatchNo_to= '',$Tokuisaki= '',$homen = '',$tenpo = '',$Sinchoku = '',$Shohin = '') {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			//種蒔き進捗データの取得
			$sql  = "SELECT M1.MEI_1 AS A1";
			$sql .= ",TN.SHUKKA_BI AS A2";
			$sql .= ",TN.HOMEN_MEI AS A3";
			$sql .= ",TN.BATCH_NUMBER AS A4";
			$sql .= ",TN.SHOHIN_CODE AS A5";
			$sql .= ",TN.TENPO_MEI AS A6";
			$sql .= ",COALESCE(TN.KAISI_JIKAN,'') AS A7";
			$sql .= ",COALESCE(TN.SYURYO_JIKAN,'') AS A8";
			$sql .= ",TN.NISUGATA_1_SHIJISU AS A9";
			$sql .= ",SH.NISUGATA_1_TANI AS A10";
			$sql .= ",TN.NISUGATA_2_SHIJISU AS A11";
			$sql .= ",SH.NISUGATA_2_TANI AS A12";
			$sql .= ",TN.NISUGATA_3_SHIJISU AS A13";
			$sql .= ",SH.NISUGATA_3_TANI AS A14";
			$sql .= ",TN.SO_BARA_SU AS A15";
			$sql .= ",SH.NISUGATA_3_TANI AS A16";
			$sql .= ",TN.NISUGATA_1_SUMISU AS A17";
			$sql .= ",SH.NISUGATA_1_TANI AS A18";
			$sql .= ",TN.NISUGATA_2_SUMISU AS A19";
			$sql .= ",SH.NISUGATA_2_TANI AS A20";
			$sql .= ",TN.NISUGATA_3_SUMISU AS A21";
			$sql .= ",SH.NISUGATA_3_TANI AS A22";
			$sql .= ",TN.SO_BARA_SUMISU AS A23";
			$sql .= ",SH.NISUGATA_3_TANI AS A24";
			$sql .= ",TN.NISUGATA_1_SHIJISU-COALESCE(TN.NISUGATA_1_SUMISU,0) AS A25";
			$sql .= ",SH.NISUGATA_1_TANI AS A26";
			$sql .= ",TN.NISUGATA_2_SHIJISU-COALESCE(TN.NISUGATA_2_SUMISU,0) AS A27";
			$sql .= ",SH.NISUGATA_2_TANI AS A28";
			$sql .= ",TN.NISUGATA_3_SHIJISU-COALESCE(TN.NISUGATA_3_SUMISU,0) AS A29";
			$sql .= ",SH.NISUGATA_3_TANI AS A30";
			$sql .= ",TN.SO_BARA_SU-COALESCE(TN.SO_BARA_SUMISU,0) AS A31";
			$sql .= ",SH.NISUGATA_3_TANI AS A32";
			$sql .= ",SH.SHOHIN_MEI AS A33";
			$sql .= ",TN.TOTAL_PK_NUMBER AS A34";
			$sql .= ",M2.MEI_1 AS A35";
			$sql .= ",TN.TOKUISAKI_MEI 	AS A36";
			$sql .= ",TN.TENPO_CODE AS A37";
			$sql .= ",S1.STAFF_NM AS A38";
			$sql .= " FROM T_OZAX_TANEMAKI TN INNER JOIN M_OZAX_SHOHIN SH ON (TN.NINUSI_CD=SH.NINUSI_CD AND TN.SOSIKI_CD=SH.SOSIKI_CD  AND TN.SHOHIN_CODE=SH.SHOHIN_CODE)";
			$sql .= " LEFT JOIN M_MEISYO M1 ON (TN.NINUSI_CD=M1.NINUSI_CD AND TN.SOSIKI_CD=M1.SOSIKI_CD  AND TN.TANEMAKI_KUBUN=M1.MEI_CD AND M1.MEI_KBN='38')";
			$sql .= " LEFT JOIN M_MEISYO M2 ON (TN.NINUSI_CD=M2.NINUSI_CD AND TN.SOSIKI_CD=M2.SOSIKI_CD  AND SH.SHOMI_KIGEN_KUBUN=M2.MEI_CD AND M2.MEI_KBN='40')";
			$sql .= " LEFT JOIN M_STAFF_KIHON S1 ON (TN.KOSIN_STAFF=S1.STAFF_CD)";

			$sql .= ' WHERE ';
			$sql .= "  TN.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  TN.SOSIKI_CD='" . $this->_sosiki_cd . "'";

			//出荷日
			if ($ymd_from != ''){
				$sql .= " AND TN.SHUKKA_BI >= '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}

			if ($ymd_to != ''){
				$sql .= " AND TN.SHUKKA_BI <= '" . $this->MCommon->strToDateString($ymd_to) . "'" ;
			}

			//バッチ№
			if ($BatchNo_from != ''){
				$sql .= " AND TN.BATCH_NUMBER >= '" . $BatchNo_from . "'" ;
			}

			if ($BatchNo_to != ''){
				$sql .= " AND TN.BATCH_NUMBER <= '" . $BatchNo_to . "'" ;
			}

			//得意先
			if ($Tokuisaki != ''){
				$sql .= " AND TN.TOKUISAKI_MEI LIKE '%" . $Tokuisaki . "%'" ;
			}

			//方面
			if ($homen != ''){
				$sql .= " AND TN.HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}

			//店舗名
			if ($tenpo != ''){
				$sql .= " AND TN.TENPO_NAME LIKE '%" . $tenpo . "%'" ;
			}

			//進捗区分
			if ($Sinchoku != ''){
				$sql .= " AND TRIM(TN.TANEMAKI_KUBUN) LIKE '%" . $Sinchoku . "%'" ;
			}

			//商品
			if ($Shohin != ''){
				$sql .= " AND (TRIM(TN.SHOHIN_CODE) LIKE '%" . $Shohin . "%' OR TRIM(SH.SHOHIN_MEI) LIKE '%" . $Shohin . "%')" ;
			}

			$sql .= "    ORDER BY";
			$sql .= "      TN.TANEMAKI_KUBUN,";
			$sql .= "      TN.SHUKKA_BI,";
			$sql .= "      TN.HOMEN_MEI,";
			$sql .= "      TN.BATCH_NUMBER,";
			$sql .= "      TN.TENPO_CODE,";
			$sql .= "      TN.SHOHIN_CODE";
			$sql .= ";";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"種蒔進捗データ 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
				    "TANEMAKI_KUBUN_MEI" => $result['A1'],
					"SHUKKA_BI" => $result['A2'],
					"HOMEN_MEI" => $result['A3'],
					"BATCH_NUMBER" => $result['A4'],
					"SHOHIN_CODE" => $result['A5'],
					"TENPO_MEI" => $result['A6'],
					"KAISI_JIKAN" => $result['A7'],
					"SYURYO_JIKAN" => $result['A8'],
					"NISUGATA_1_SHIJISU" => $result['A9'],
					"NISUGATA_1_TANI" => $result['A10'],
					"NISUGATA_2_SHIJISU" => $result['A11'],
					"NISUGATA_2_TANI" => $result['A12'],
					"NISUGATA_3_SHIJISU" => $result['A13'],
					"NISUGATA_3_TANI" => $result['A14'],
					"SO_BARA_SU" => $result['A15'],
					"NISUGATA_3_TANI" => $result['A16'],
					"NISUGATA_1_SUMISU" => $result['A17'],
					"NISUGATA_1_TANI" => $result['A18'],
					"NISUGATA_2_SUMISU" => $result['A19'],
					"NISUGATA_2_TANI" => $result['A20'],
					"NISUGATA_3_SUMISU" => $result['A21'],
					"NISUGATA_3_TANI" => $result['A22'],
					"SO_BARA_SUMISU" => $result['A23'],
					"NISUGATA_3_TANI" => $result['A24'],
					"NISUGATA_1_ZANSU" => $result['A25'],
					"NISUGATA_1_TANI" => $result['A26'],
					"NISUGATA_2_ZANSU" => $result['A27'],
					"NISUGATA_2_TANI" => $result['A28'],
					"NISUGATA_3_ZANSU" => $result['A29'],
					"NISUGATA_3_TANI" => $result['A30'],
					"SO_BARA_ZANSU" => $result['A31'],
					"NISUGATA_3_TANI" => $result['A32'],
					"SHOHIN_MEI" => $result['A33'],
					"TOTAL_PK_NUMBER" => $result['A34'],
					"SHOMI_KIGEN_KUBUN" => $result['A35'],
					"TOKUISAKI_MEI" => $result['A36'],
					"TENPO_CODE" => $result['A37'],
					"STAFF_NM" => $result['A38']
				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM080", $e->getMessage());
			$pdo = null;
		}


		return array();
	}

	/**
	 * 種蒔き進捗サマリーデータ取得
	 * @access   public
	 * @return   種蒔き進捗サマリーデータ
	 */
	public function getSumallyList($ymd_from = '',$ymd_to = '',$BatchNo_from = '',$BatchNo_to= '',$Tokuisaki= '',$homen = '',$tenpo = '',$Sinchoku = '',$Shohin = '') {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$where = ' ';
			$where .= ' WHERE ';
			$where .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$where .= "  SOSIKI_CD='" . $this->_sosiki_cd . "'";
			//出荷日
			if ($ymd_from != ''){
				$where .= " AND SHUKKA_BI >= '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}

			if ($ymd_to != ''){
				$where .= " AND SHUKKA_BI <= '" . $this->MCommon->strToDateString($ymd_to) . "'" ;
			}

			//バッチ№
			if ($BatchNo_from != ''){
				$where .= " AND BATCH_NUMBER >= '" . $BatchNo_from . "'" ;
			}

			if ($BatchNo_to != ''){
				$where .= " AND BATCH_NUMBER <= '" . $BatchNo_to . "'" ;
			}

			//得意先
			if ($Tokuisaki != ''){
				$where .= " AND TOKUISAKI_MEI LIKE '%" . $Tokuisaki . "%'" ;
			}

			//方面
			if ($homen != ''){
				$where .= " AND HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}

			//店舗
			if ($tenpo != ''){
				$where .= " AND TENPO_NAME LIKE '%" . $tenpo . "%'" ;
			}

			//商品
			if ($Shohin != ''){
				$where .= " AND (TRIM(SHOHIN_CODE) LIKE '%" . $Shohin . "%' OR TRIM(SHOHIN_MEI) LIKE '%" . $Shohin . "%')" ;
			}

			//種蒔き進捗データの取得
			$sql  = "SELECT '1' AS A1,COUNT(DISTINCT HOMEN_MEI) AS A2,COUNT(TENPO_CODE) AS A3,COUNT(SHOHIN_CODE) AS A4,COALESCE(SUM(NISUGATA_1_SHIJISU),0) AS A5,COALESCE(SUM(NISUGATA_2_SHIJISU),0) AS A6,COALESCE(SUM(NISUGATA_3_SHIJISU),0) AS A7,COALESCE(SUM(SO_BARA_SU),0) AS A8,COALESCE(SUM(NISUGATA_1_SHIJISU),0)+COALESCE(SUM(NISUGATA_2_SHIJISU),0)+COALESCE(SUM(NISUGATA_3_SHIJISU),0) AS A9";
			$sql .= " FROM T_OZAX_TANEMAKI";
			$sql .= $where;
			$sql .= " UNION";
			$sql .= " SELECT '2' AS A1,COUNT(DISTINCT HOMEN_MEI) AS A2,COUNT(TENPO_CODE) AS A3,COUNT(SHOHIN_CODE) AS A4,COALESCE(SUM(NISUGATA_1_SUMISU),0) AS A5,COALESCE(SUM(NISUGATA_2_SUMISU),0) AS A6,COALESCE(SUM(NISUGATA_3_SUMISU),0) AS A7,COALESCE(SUM(SO_BARA_SUMISU),0) AS A8,COALESCE(SUM(NISUGATA_1_SUMISU),0)+COALESCE(SUM(NISUGATA_2_SUMISU),0)+COALESCE(SUM(NISUGATA_3_SUMISU),0) AS A9";
			$sql .= " FROM T_OZAX_TANEMAKI";
			$sql .= $where;
			$sql .= " AND TANEMAKI_KUBUN = '2'";
			$sql .= " UNION";
			$sql .= " SELECT '3' AS A1,COUNT(DISTINCT HOMEN_MEI) AS A2,COUNT(TENPO_CODE) AS A3,COUNT(SHOHIN_CODE) AS A4,COALESCE(SUM(NISUGATA_1_SHIJISU)-SUM(NISUGATA_1_SUMISU),0) AS A5,COALESCE(SUM(NISUGATA_2_SHIJISU)-SUM(NISUGATA_2_SUMISU),0) AS A6,COALESCE(SUM(NISUGATA_3_SHIJISU)-SUM(NISUGATA_3_SUMISU),0) AS A7,COALESCE(SUM(SO_BARA_SU)-SUM(SO_BARA_SUMISU),0) AS A8,COALESCE(SUM(NISUGATA_1_SHIJISU)-SUM(NISUGATA_1_SUMISU),0)+COALESCE(SUM(NISUGATA_2_SHIJISU)-SUM(NISUGATA_2_SUMISU),0)+COALESCE(SUM(NISUGATA_3_SHIJISU)-SUM(NISUGATA_3_SUMISU),0) AS A9";
			$sql .= " FROM T_OZAX_TANEMAKI";
			$sql .= $where;
			$sql .= " AND TANEMAKI_KUBUN <> '2'";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"種蒔き進捗サマリーデータ 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
						"BANGO" => $result['A1'],
						"HOMEN_SU" => $result['A2'],
						"TENPO_SU" => $result['A3'],
						"ITEM_SU" => $result['A4'],
						"SO_1_SU" => $result['A5'],
						"SO_2_SU" => $result['A6'],
						"SO_3_SU" => $result['A7'],
						"SO_BARA_SU" => $result['A8'],
						"GOKEI_SU" => $result['A9']
				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM080", $e->getMessage());
			$pdo = null;
		}


		return array();
	}

	/**
	 * 種蒔き進捗ＣＳＶデータ取得
	 * @access   public
	 * @return   種蒔き進捗CSVデータ
	 */
	function getCSV($ymd_from = '',$ymd_to = '',$BatchNo_from = '',$BatchNo_to= '',$Tokuisaki= '',$homen = '',$tenpo = '',$Sinchoku = '',$Shohin = '') {

		$text = array();

		//ヘッダー情報
		$header = "種蒔区分,出荷日,方面,バッチ№,商品コード,店舗名,開始時間,終了時間,";
		$header .= "荷姿１指示数,荷姿１単位,荷姿２指示数,荷姿２単位,荷姿３指示数,荷姿３単位,総バラ指示数,荷姿３単位,";
		$header .= "荷姿１済数,荷姿１単位,荷姿２済数,荷姿２単位,荷姿３済数,荷姿３単位,総バラ済数,荷姿３単位,";
		$header .= "荷姿１残数,荷姿１単位,荷姿２残数,荷姿２単位,荷姿３残数,荷姿３単位,総バラ残数,荷姿３単位,";
		$header .= "商品名,トータルPK№,賞味期限管理区分,得意先名,店舗コード,作業者";

		$text[] = $header;

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			$sql  = "SELECT M1.MEI_1 AS A1";
			$sql .= ",TN.SHUKKA_BI AS A2";
			$sql .= ",TN.HOMEN_MEI AS A3";
			$sql .= ",TN.BATCH_NUMBER AS A4";
			$sql .= ",TN.SHOHIN_CODE AS A5";
			$sql .= ",TN.TENPO_MEI AS A6";
			$sql .= ",COALESCE(TN.KAISI_JIKAN,'') AS A7";
			$sql .= ",COALESCE(TN.SYURYO_JIKAN,'') AS A8";
			$sql .= ",TN.NISUGATA_1_SHIJISU AS A9";
			$sql .= ",SH.NISUGATA_1_TANI AS A10";
			$sql .= ",TN.NISUGATA_2_SHIJISU AS A11";
			$sql .= ",SH.NISUGATA_2_TANI AS A12";
			$sql .= ",TN.NISUGATA_3_SHIJISU AS A13";
			$sql .= ",SH.NISUGATA_3_TANI AS A14";
			$sql .= ",TN.SO_BARA_SU AS A15";
			$sql .= ",SH.NISUGATA_3_TANI AS A16";
			$sql .= ",TN.NISUGATA_1_SUMISU AS A17";
			$sql .= ",SH.NISUGATA_1_TANI AS A18";
			$sql .= ",TN.NISUGATA_2_SUMISU AS A19";
			$sql .= ",SH.NISUGATA_2_TANI AS A20";
			$sql .= ",TN.NISUGATA_3_SUMISU AS A21";
			$sql .= ",SH.NISUGATA_3_TANI AS A22";
			$sql .= ",TN.SO_BARA_SUMISU AS A23";
			$sql .= ",SH.NISUGATA_3_TANI AS A24";
			$sql .= ",TN.NISUGATA_1_SHIJISU-TN.NISUGATA_1_SUMISU AS A25";
			$sql .= ",SH.NISUGATA_1_TANI AS A26";
			$sql .= ",TN.NISUGATA_2_SHIJISU-TN.NISUGATA_2_SUMISU AS A27";
			$sql .= ",SH.NISUGATA_2_TANI AS A28";
			$sql .= ",TN.NISUGATA_3_SHIJISU-TN.NISUGATA_3_SUMISU AS A29";
			$sql .= ",SH.NISUGATA_3_TANI AS A30";
			$sql .= ",TN.SO_BARA_SU-TN.SO_BARA_SUMISU AS A31";
			$sql .= ",SH.NISUGATA_3_TANI AS A32";
			$sql .= ",SH.SHOHIN_MEI AS A33";
			$sql .= ",TN.TOTAL_PK_NUMBER AS A34";
			$sql .= ",M2.MEI_1 AS A35";
			$sql .= ",TN.TOKUISAKI_MEI 	AS A36";
			$sql .= ",TN.TENPO_CODE AS A37";
			$sql .= ",S1.STAFF_NM AS A38";
			$sql .= " FROM T_OZAX_TANEMAKI TN INNER JOIN M_OZAX_SHOHIN SH ON (TN.NINUSI_CD=SH.NINUSI_CD AND TN.SOSIKI_CD=SH.SOSIKI_CD  AND TN.SHOHIN_CODE=SH.SHOHIN_CODE)";
			$sql .= " LEFT JOIN M_MEISYO M1 ON (TN.NINUSI_CD=M1.NINUSI_CD AND TN.SOSIKI_CD=M1.SOSIKI_CD  AND TN.TANEMAKI_KUBUN=M1.MEI_CD AND M1.MEI_KBN='38')";
			$sql .= " LEFT JOIN M_MEISYO M2 ON (TN.NINUSI_CD=M2.NINUSI_CD AND TN.SOSIKI_CD=M2.SOSIKI_CD  AND SH.SHOMI_KIGEN_KUBUN=M2.MEI_CD AND M2.MEI_KBN='40')";
			$sql .= " LEFT JOIN M_STAFF_KIHON S1 ON (TN.KOSIN_STAFF=S1.STAFF_CD)";

			$sql .= ' WHERE ';
			$sql .= "  TN.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  TN.SOSIKI_CD='" . $this->_sosiki_cd . "'";

			//出荷日
			if ($ymd_from != ''){
				$sql .= " AND TN.SHUKKA_BI >= '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}

			if ($ymd_to != ''){
				$sql .= " AND TN.SHUKKA_BI <= '" . $this->MCommon->strToDateString($ymd_to) . "'" ;
			}

			//バッチ№
			if ($BatchNo_from != ''){
				$sql .= " AND TN.BATCH_NUMBER >= '" . $BatchNo_from . "'" ;
			}

			if ($BatchNo_to != ''){
				$sql .= " AND TN.BATCH_NUMBER <= '" . $BatchNo_to . "'" ;
			}

			//得意先
			if ($Tokuisaki != ''){
				$sql .= " AND TN.TOKUISAKI_MEI LIKE '%" . $Tokuisaki . "%'" ;
			}

			//方面
			if ($homen != ''){
				$sql .= " AND TN.HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}

			//店舗名
			if ($tenpo != ''){
				$sql .= " AND TN.TENPO_NAME LIKE '%" . $tenpo . "%'" ;
			}

			//進捗区分
			if ($Sinchoku != ''){
				$sql .= " AND TRIM(TN.TANEMAKI_KUBUN) LIKE '%" . $Sinchoku . "%'" ;
			}

			//商品
			if ($Shohin != ''){
				$sql .= " AND (TRIM(TN.SHOHIN_CODE) LIKE '%" . $Shohin . "%' OR TRIM(SH.SHOHIN_MEI) LIKE '%" . $Shohin . "%')" ;
			}

			$sql .= "    ORDER BY";
			$sql .= "      TN.TANEMAKI_KUBUN,";
			$sql .= "      TN.SHUKKA_BI,";
			$sql .= "      TN.HOMEN_MEI,";
			$sql .= "      TN.BATCH_NUMBER,";
			$sql .= "      TN.TENPO_CODE,";
			$sql .= "      TN.SHOHIN_CODE";
			$sql .= ";";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"種蒔進捗CSVデータ 取得");

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
				$tmpText .= $result['A13'] . ',';
				$tmpText .= $result['A14'] . ',';
				$tmpText .= $result['A15'] . ',';
				$tmpText .= $result['A16'] . ',';
				$tmpText .= $result['A17'] . ',';
				$tmpText .= $result['A18'] . ',';
				$tmpText .= $result['A19'] . ',';
				$tmpText .= $result['A20'] . ',';
				$tmpText .= $result['A21'] . ',';
				$tmpText .= $result['A22'] . ',';
				$tmpText .= $result['A23'] . ',';
				$tmpText .= $result['A24'] . ',';
				$tmpText .= $result['A25'] . ',';
				$tmpText .= $result['A26'] . ',';
				$tmpText .= $result['A27'] . ',';
				$tmpText .= $result['A28'] . ',';
				$tmpText .= $result['A29'] . ',';
				$tmpText .= $result['A30'] . ',';
				$tmpText .= $result['A31'] . ',';
				$tmpText .= $result['A32'] . ',';
				$tmpText .= $result['A33'] . ',';
				$tmpText .= $result['A34'] . ',';
				$tmpText .= $result['A35'] . ',';
				$tmpText .= $result['A36'] . ',';
				$tmpText .= $result['A37'] . ',';
				$tmpText .= $result['A38'] ;

				$text[] = $tmpText;

			}

			$pdo = null;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM080", $e->getMessage());
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
			$sql = "CALL P_TPM080_GET_TIMESTAMP(";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " @timestamp";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, '種蒔き進捗　タイムスタンプ取得');

			$sql = "";
			$sql .= " SELECT";
			$sql .= "  @timestamp";


			$this->queryWithPDOLog($stmt,$pdo,$sql, '種蒔き進捗　タイムスタンプ取得');
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

			$this->printLog("fatal", "例外発生", "TPM080", $e->getMessage());
			$pdo = null;
		}

		return date("Ymd",strtotime("-1 day"));;

	}

}

?>