<?php
/**
 * TPM070Model
 *
 * トータルピッキング進捗画面
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class TPM070Model extends DCMSAppModel{

	public $name = 'TPM070Model';
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
	 * トータルピッキング進捗データ取得
	 * @access   public
	 * @return   トータルピッキング進捗データ
	 */
	public function getList($ymd_from = '',$ymd_to = '',$BatchNo_from = '',$BatchNo_to= '',$Tokuisaki= '',$homen = '',$shomikigen_henko = '',$Sinchoku = '',$Shohin = '') {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			//トータルピッキング進捗データの取得
			$sql  = "SELECT M1.MEI_1 AS A1";
			$sql .= ",PK.SHUKKA_BI AS A2";
			$sql .= ",PK.HOMEN_MEI AS A3";
			$sql .= ",PK.BATCH_NUMBER AS A4";
			$sql .= ",PK.SHOHIN_CODE AS A5";
			$sql .= ",COALESCE(PK.SYOMI_KIGEN,'') AS A6";
			$sql .= ",COALESCE(IP.HENKO_SYOMI_KIGEN,'') AS A7";
			$sql .= ",COALESCE(PK.LOCATION_CODE,'')  AS A8";
			$sql .= ",COALESCE(PK.KAISI_JIKAN,'') AS A9";
			$sql .= ",COALESCE(PK.SYURYO_JIKAN,'') AS A10";
			$sql .= ",SUM(IP.HENKO_NISUGATA_1_SHIJISU) AS A11";
			$sql .= ",SH.NISUGATA_1_TANI AS A12";
			$sql .= ",SUM(IP.HENKO_NISUGATA_2_SHIJISU) AS A13";
			$sql .= ",SH.NISUGATA_2_TANI AS A14";
			$sql .= ",SUM(IP.HENKO_NISUGATA_3_SHIJISU) AS A15";
			$sql .= ",SH.NISUGATA_3_TANI AS A16";
			$sql .= ",SUM(IP.HENKO_SO_BARA_SU) AS A17";
			$sql .= ",SH.NISUGATA_3_TANI 	AS A18";
			$sql .= ",SUM(IP.NISUGATA_1_SUMISU) 	AS A19";
			$sql .= ",SH.NISUGATA_1_TANI 	AS A20";
			$sql .= ",SUM(IP.NISUGATA_2_SUMISU) 	AS A21";
			$sql .= ",SH.NISUGATA_2_TANI 	AS A22";
			$sql .= ",SUM(IP.NISUGATA_3_SUMISU) 	AS A23";
			$sql .= ",SH.NISUGATA_3_TANI 	AS A24";
			$sql .= ",SUM(IP.SO_BARA_SUMISU) AS A25";
			$sql .= ",SH.NISUGATA_3_TANI AS A26";
			$sql .= ",SUM(IP.HENKO_NISUGATA_1_SHIJISU-IP.NISUGATA_1_SUMISU) AS A27";
			$sql .= ",SH.NISUGATA_1_TANI AS A28";
			$sql .= ",SUM(IP.HENKO_NISUGATA_2_SHIJISU-IP.NISUGATA_2_SUMISU) AS A29";
			$sql .= ",SH.NISUGATA_2_TANI AS A30";
			$sql .= ",SUM(IP.HENKO_NISUGATA_3_SHIJISU-IP.NISUGATA_3_SUMISU) AS A31";
			$sql .= ",SH.NISUGATA_3_TANI AS A32";
			$sql .= ",SUM(IP.HENKO_SO_BARA_SU-IP.SO_BARA_SUMISU) AS A33";
			$sql .= ",SH.NISUGATA_3_TANI AS A34";
			$sql .= ",PK.SHOHIN_MEI AS A35";
			$sql .= ",PK.TOTAL_PK_NUMBER AS A36";
			$sql .= ",M2.MEI_1 AS A37";
			$sql .= ",PK.TOKUISAKI_MEI 	AS A38";
			$sql .= ",S1.STAFF_NM 	AS A39";
			$sql .= " FROM T_OZAX_TOTAL_PK PK INNER JOIN M_OZAX_SHOHIN SH ON (PK.NINUSI_CD=SH.NINUSI_CD AND PK.SOSIKI_CD=SH.SOSIKI_CD  AND PK.SHOHIN_CODE=SH.SHOHIN_CODE)";
			$sql .= " INNER JOIN T_OZAX_IPOD_TOTAL_PK IP ON (PK.NINUSI_CD=IP.NINUSI_CD AND PK.SOSIKI_CD=IP.SOSIKI_CD  AND PK.GYOTAI_SOKO_CODE=IP.GYOTAI_SOKO_CODE  AND PK.SHUKKA_BI=IP.SHUKKA_BI  AND PK.BATCH_NUMBER=IP.BATCH_NUMBER  AND PK.HOMEN_MEI=IP.HOMEN_MEI  AND PK.SHOHIN_CODE=IP.SHOHIN_CODE  AND PK.TOTAL_PK_NUMBER=IP.TOTAL_PK_NUMBER)";
			$sql .= " LEFT JOIN M_MEISYO M1 ON (PK.NINUSI_CD=M1.NINUSI_CD AND PK.SOSIKI_CD=M1.SOSIKI_CD  AND PK.TOTAL_PK_KUBUN=M1.MEI_CD AND M1.MEI_KBN='37')";
			$sql .= " LEFT JOIN M_MEISYO M2 ON (PK.NINUSI_CD=M2.NINUSI_CD AND PK.SOSIKI_CD=M2.SOSIKI_CD  AND SH.SHOMI_KIGEN_KUBUN=M2.MEI_CD AND M2.MEI_KBN='41')";
			$sql .= " LEFT JOIN M_STAFF_KIHON S1 ON (PK.KOSIN_STAFF=S1.STAFF_CD)";

			$sql .= ' WHERE ';
			$sql .= "  PK.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  PK.SOSIKI_CD='" . $this->_sosiki_cd . "'";

			//出荷日
			if ($ymd_from != ''){
				$sql .= " AND PK.SHUKKA_BI >= '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}

			if ($ymd_to != ''){
				$sql .= " AND PK.SHUKKA_BI <= '" . $this->MCommon->strToDateString($ymd_to) . "'" ;
			}

			//バッチ№
			if ($BatchNo_from != ''){
				$sql .= " AND PK.BATCH_NUMBER >= '" . $BatchNo_from . "'" ;
			}

			if ($BatchNo_to != ''){
				$sql .= " AND PK.BATCH_NUMBER <= '" . $BatchNo_to . "'" ;
			}

			//得意先
			if ($Tokuisaki != ''){
				$sql .= " AND PK.TOKUISAKI_MEI LIKE '%" . $Tokuisaki . "%'" ;
			}

			//方面
			if ($homen != ''){
				$sql .= " AND PK.HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}

			//賞味期限日変更分のみ
			if ($shomikigen_henko == 'on'){
				$sql .= " AND TRIM(IP.HENKO_SYOMI_KIGEN) != ''" ;
			}

			//進捗区分
			if ($Sinchoku != ''){
				$sql .= " AND TRIM(PK.TOTAL_PK_KUBUN) LIKE '%" . $Sinchoku . "%'" ;
			}

			//商品
			if ($Shohin != ''){
				$sql .= " AND (TRIM(PK.SHOHIN_CODE) LIKE '%" . $Shohin . "%' OR TRIM(PK.SHOHIN_MEI) LIKE '%" . $Shohin . "%')" ;
			}

			$sql .= "    GROUP BY";
			$sql .= "     M1.MEI_1,PK.SHUKKA_BI";
			$sql .= "     ,PK.HOMEN_MEI";
			$sql .= "     ,PK.BATCH_NUMBER";
			$sql .= "     ,PK.SHOHIN_CODE";
			$sql .= "     ,PK.SYOMI_KIGEN";
			$sql .= "     ,IP.HENKO_SYOMI_KIGEN";
			$sql .= "     ,PK.LOCATION_CODE";
			$sql .= "     ,PK.KAISI_JIKAN";
			$sql .= "     ,PK.SYURYO_JIKAN";
			$sql .= "     ,PK.TOTAL_PK_NUMBER";
			$sql .= "     ,PK.SHOHIN_MEI";
			$sql .= "     ,PK.TOTAL_PK_NUMBER";
			$sql .= "     ,M2.MEI_1";
			$sql .= "     ,PK.TOKUISAKI_MEI";
			$sql .= "     ,S1.STAFF_NM";
			$sql .= "    ORDER BY";
			$sql .= "      PK.TOTAL_PK_KUBUN,";
			$sql .= "      PK.SHUKKA_BI,";
			$sql .= "      PK.HOMEN_MEI,";
			$sql .= "      PK.BATCH_NUMBER,";
			$sql .= "      PK.LOCATION_CODE";
			$sql .= ";";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"トータルピッキング進捗データ 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
				    "TOTAL_PK_KUBUN_MEI" => $result['A1'],
					"SHUKKA_BI" => $result['A2'],
					"HOMEN_MEI" => $result['A3'],
					"BATCH_NUMBER" => $result['A4'],
					"SHOHIN_CODE" => $result['A5'],
					"SYOMI_KIGEN" => $result['A6'],
					"HENKO_SYOMI_KIGEN" => $result['A7'],
					"LOCATION_CODE" => $result['A8'],
					"KAISI_JIKAN" => $result['A9'],
					"SYURYO_JIKAN" => $result['A10'],
					"NISUGATA_1_SHIJISU" => $result['A11'],
					"NISUGATA_1_TANI" => $result['A12'],
					"NISUGATA_2_SHIJISU" => $result['A13'],
					"NISUGATA_2_TANI" => $result['A14'],
					"NISUGATA_3_SHIJISU" => $result['A15'],
					"NISUGATA_3_TANI" => $result['A16'],
					"SO_BARA_SU" => $result['A17'],
					"NISUGATA_3_TANI" => $result['A18'],
					"NISUGATA_1_SUMISU" => $result['A19'],
					"NISUGATA_1_TANI" => $result['A20'],
					"NISUGATA_2_SUMISU" => $result['A21'],
					"NISUGATA_2_TANI" => $result['A22'],
					"NISUGATA_3_SUMISU" => $result['A23'],
					"NISUGATA_3_TANI" => $result['A24'],
					"SO_BARA_SUMISU" => $result['A25'],
					"NISUGATA_3_TANI" => $result['A26'],
					"NISUGATA_1_ZANSU" => $result['A27'],
					"NISUGATA_1_TANI" => $result['A28'],
					"NISUGATA_2_ZANSU" => $result['A29'],
					"NISUGATA_2_TANI" => $result['A30'],
					"NISUGATA_3_ZANSU" => $result['A31'],
					"NISUGATA_3_TANI" => $result['A32'],
					"SO_BARA_ZANSU" => $result['A33'],
					"NISUGATA_3_TANI" => $result['A34'],
					"SHOHIN_MEI" => $result['A35'],
					"TOTAL_PK_NUMBER" => $result['A36'],
					"SHOMI_KIGEN_KUBUN" => $result['A37'],
					"TOKUISAKI_MEI" => $result['A38'],
					"STAFF_NM" => $result['A39']
				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM070", $e->getMessage());
			$pdo = null;
		}


		return array();
	}

	/**
	 * トータルピッキング進捗ケースサマリーデータ取得
	 * @access   public
	 * @return   トータルピッキング進捗ケースサマリーデータ
	 */
	public function getCaseSumallyList($ymd_from = '',$ymd_to = '',$BatchNo_from = '',$BatchNo_to= '',$Tokuisaki= '',$homen = '',$shomikigen_henko = '',$Sinchoku = '',$Shohin = '') {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$where = ' ';
			$where .= ' WHERE ';
			$where .= "  TP.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$where .= "  TP.SOSIKI_CD='" . $this->_sosiki_cd . "'";
				//出荷日
			if ($ymd_from != ''){
				$where .= " AND TP.SHUKKA_BI >= '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}

			if ($ymd_to != ''){
				$where .= " AND TP.SHUKKA_BI <= '" . $this->MCommon->strToDateString($ymd_to) . "'" ;
			}

			//バッチ№
			if ($BatchNo_from != ''){
				$where .= " AND TP.BATCH_NUMBER >= '" . $BatchNo_from . "'" ;
			}

			if ($BatchNo_to != ''){
				$where .= " AND TP.BATCH_NUMBER <= '" . $BatchNo_to . "'" ;
			}

			//得意先
			if ($Tokuisaki != ''){
				$where .= " AND TP.TOKUISAKI_MEI LIKE '%" . $Tokuisaki . "%'" ;
			}

			//方面
			if ($homen != ''){
				$where .= " AND TP.HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}

			//商品
			if ($Shohin != ''){
				$where .= " AND (TRIM(TP.SHOHIN_CODE) LIKE '%" . $Shohin . "%' OR TRIM(TP.SHOHIN_MEI) LIKE '%" . $Shohin . "%')" ;
			}

			//ケーストータルピッキング進捗データの取得
			$sql  = "SELECT '1' AS A1,COUNT(DISTINCT TP.HOMEN_MEI) AS A2,COUNT(TP.SHOHIN_CODE) AS A3,COALESCE(SUM(TP.NISUGATA_1_SHIJISU),0) AS A4,COALESCE(SUM(TP.NISUGATA_2_SHIJISU),0) AS A5,COALESCE(SUM(TP.NISUGATA_3_SHIJISU),0) AS A6,COALESCE(SUM(TP.SO_BARA_SU),0) AS A7,COALESCE(SUM(TP.NISUGATA_1_SHIJISU),0)+COALESCE(SUM(TP.NISUGATA_2_SHIJISU),0)+COALESCE(SUM(TP.NISUGATA_3_SHIJISU),0) AS A8";
			$sql .= " FROM T_OZAX_TOTAL_PK TP INNER JOIN M_OZAX_LOCATION LO ON TP.NINUSI_CD=LO.NINUSI_CD AND TP.SOSIKI_CD=LO.SOSIKI_CD AND LEFT(TP.LOCATION_CODE,1)=LO.LOCATION_CODE AND LO.LOCATION_KUBUN='1'";
			$sql .= $where;
			$sql .= " UNION";
			$sql .= " SELECT '2' AS A1,COUNT(DISTINCT TP.HOMEN_MEI) AS A2,COUNT(TP.SHOHIN_CODE) AS A3,COALESCE(SUM(TP.NISUGATA_1_SUMISU),0) AS A4,COALESCE(SUM(TP.NISUGATA_2_SUMISU),0) AS A5,COALESCE(SUM(TP.NISUGATA_3_SUMISU),0) AS A6,COALESCE(SUM(TP.SO_BARA_SUMISU),0) AS A7,COALESCE(SUM(TP.NISUGATA_1_SUMISU),0)+COALESCE(SUM(TP.NISUGATA_2_SUMISU),0)+COALESCE(SUM(TP.NISUGATA_3_SUMISU),0) AS A8";
			$sql .= " FROM T_OZAX_TOTAL_PK TP INNER JOIN M_OZAX_LOCATION LO ON TP.NINUSI_CD=LO.NINUSI_CD AND TP.SOSIKI_CD=LO.SOSIKI_CD AND LEFT(TP.LOCATION_CODE,1)=LO.LOCATION_CODE AND LO.LOCATION_KUBUN='1'";
			$sql .= $where;
			$sql .= " AND TP.TOTAL_PK_KUBUN = '2'";
			$sql .= " UNION";
			$sql .= " SELECT '3' AS A1,COUNT(DISTINCT TP.HOMEN_MEI) AS A2,COUNT(TP.SHOHIN_CODE) AS A3,COALESCE(SUM(TP.NISUGATA_1_SHIJISU)-SUM(TP.NISUGATA_1_SUMISU),0) AS A4,COALESCE(SUM(TP.NISUGATA_2_SHIJISU)-SUM(TP.NISUGATA_2_SUMISU),0) AS A5,COALESCE(SUM(TP.NISUGATA_3_SHIJISU)-SUM(TP.NISUGATA_3_SUMISU),0) AS A6,COALESCE(SUM(TP.SO_BARA_SU)-SUM(TP.SO_BARA_SUMISU),0) AS A7,COALESCE(SUM(TP.NISUGATA_1_SHIJISU)-SUM(TP.NISUGATA_1_SUMISU),0)+COALESCE(SUM(TP.NISUGATA_2_SHIJISU)-SUM(TP.NISUGATA_2_SUMISU),0)+COALESCE(SUM(TP.NISUGATA_3_SHIJISU)-SUM(TP.NISUGATA_3_SUMISU),0) AS A8";
			$sql .= " FROM T_OZAX_TOTAL_PK TP INNER JOIN M_OZAX_LOCATION LO ON TP.NINUSI_CD=LO.NINUSI_CD AND TP.SOSIKI_CD=LO.SOSIKI_CD AND LEFT(TP.LOCATION_CODE,1)=LO.LOCATION_CODE AND LO.LOCATION_KUBUN='1'";
			$sql .= $where;
			$sql .= " AND TP.TOTAL_PK_KUBUN <> '2'";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"トータルピッキング進捗ケースサマリーデータ 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
						"BANGO" => $result['A1'],
						"HOMEN_SU" => $result['A2'],
						"ITEM_SU" => $result['A3'],
						"SO_1_SU" => $result['A4'],
						"SO_2_SU" => $result['A5'],
						"SO_3_SU" => $result['A6'],
						"SO_BARA_SU" => $result['A7'],
						"GOKEI_SU" => $result['A8']
				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM070", $e->getMessage());
			$pdo = null;
		}


		return array();
	}

	/**
	 * トータルピッキング進捗バラサマリーデータ取得
	 * @access   public
	 * @return   トータルピッキング進捗バラサマリーデータ
	 */
	public function getBaraSumallyList($ymd_from = '',$ymd_to = '',$BatchNo_from = '',$BatchNo_to= '',$Tokuisaki= '',$homen = '',$shomikigen_henko = '',$Sinchoku = '',$Shohin = '') {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$where = ' ';
			$where .= ' WHERE ';
			$where .= "  TP.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$where .= "  TP.SOSIKI_CD='" . $this->_sosiki_cd . "'";
				//出荷日
			if ($ymd_from != ''){
				$where .= " AND TP.SHUKKA_BI >= '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}

			if ($ymd_to != ''){
				$where .= " AND TP.SHUKKA_BI <= '" . $this->MCommon->strToDateString($ymd_to) . "'" ;
			}

			//バッチ№
			if ($BatchNo_from != ''){
				$where .= " AND TP.BATCH_NUMBER >= '" . $BatchNo_from . "'" ;
			}

			if ($BatchNo_to != ''){
				$where .= " AND TP.BATCH_NUMBER <= '" . $BatchNo_to . "'" ;
			}

			//得意先
			if ($Tokuisaki != ''){
				$where .= " AND TP.TOKUISAKI_MEI LIKE '%" . $Tokuisaki . "%'" ;
			}

			//方面
			if ($homen != ''){
				$where .= " AND TP.HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}

			//商品
			if ($Shohin != ''){
				$where .= " AND (TRIM(TP.SHOHIN_CODE) LIKE '%" . $Shohin . "%' OR TRIM(TP.SHOHIN_MEI) LIKE '%" . $Shohin . "%')" ;
			}

			//バラトータルピッキング進捗データの取得
			$sql  = "SELECT '1' AS A1,COUNT(DISTINCT TP.HOMEN_MEI) AS A2,COUNT(TP.SHOHIN_CODE) AS A3,COALESCE(SUM(TP.NISUGATA_1_SHIJISU),0) AS A4,COALESCE(SUM(TP.NISUGATA_2_SHIJISU),0) AS A5,COALESCE(SUM(TP.NISUGATA_3_SHIJISU),0) AS A6,COALESCE(SUM(TP.SO_BARA_SU),0) AS A7,COALESCE(SUM(TP.NISUGATA_1_SHIJISU),0)+COALESCE(SUM(TP.NISUGATA_2_SHIJISU),0)+COALESCE(SUM(TP.NISUGATA_3_SHIJISU),0) AS A8";
			$sql .= " FROM T_OZAX_TOTAL_PK TP INNER JOIN M_OZAX_LOCATION LO ON TP.NINUSI_CD=LO.NINUSI_CD AND TP.SOSIKI_CD=LO.SOSIKI_CD AND LEFT(TP.LOCATION_CODE,1)=LO.LOCATION_CODE AND LO.LOCATION_KUBUN='2'";
			$sql .= $where;
			$sql .= " UNION";
			$sql .= " SELECT '2' AS A1,COUNT(DISTINCT TP.HOMEN_MEI) AS A2,COUNT(TP.SHOHIN_CODE) AS A3,COALESCE(SUM(TP.NISUGATA_1_SUMISU),0) AS A4,COALESCE(SUM(TP.NISUGATA_2_SUMISU),0) AS A5,COALESCE(SUM(TP.NISUGATA_3_SUMISU),0) AS A6,COALESCE(SUM(TP.SO_BARA_SUMISU),0) AS A7,COALESCE(SUM(TP.NISUGATA_1_SUMISU),0)+COALESCE(SUM(TP.NISUGATA_2_SUMISU),0)+COALESCE(SUM(TP.NISUGATA_3_SUMISU),0) AS A8";
			$sql .= " FROM T_OZAX_TOTAL_PK TP INNER JOIN M_OZAX_LOCATION LO ON TP.NINUSI_CD=LO.NINUSI_CD AND TP.SOSIKI_CD=LO.SOSIKI_CD AND LEFT(TP.LOCATION_CODE,1)=LO.LOCATION_CODE AND LO.LOCATION_KUBUN='2'";
			$sql .= $where;
			$sql .= " AND TP.TOTAL_PK_KUBUN = '2'";
			$sql .= " UNION";
			$sql .= " SELECT '3' AS A1,COUNT(DISTINCT TP.HOMEN_MEI) AS A2,COUNT(TP.SHOHIN_CODE) AS A3,COALESCE(SUM(TP.NISUGATA_1_SHIJISU)-SUM(TP.NISUGATA_1_SUMISU),0) AS A4,COALESCE(SUM(TP.NISUGATA_2_SHIJISU)-SUM(TP.NISUGATA_2_SUMISU),0) AS A5,COALESCE(SUM(TP.NISUGATA_3_SHIJISU)-SUM(TP.NISUGATA_3_SUMISU),0) AS A6,COALESCE(SUM(TP.SO_BARA_SU)-SUM(TP.SO_BARA_SUMISU),0) AS A7,COALESCE(SUM(TP.NISUGATA_1_SHIJISU)-SUM(TP.NISUGATA_1_SUMISU),0)+COALESCE(SUM(TP.NISUGATA_2_SHIJISU)-SUM(TP.NISUGATA_2_SUMISU),0)+COALESCE(SUM(TP.NISUGATA_3_SHIJISU)-SUM(TP.NISUGATA_3_SUMISU),0) AS A8";
			$sql .= " FROM T_OZAX_TOTAL_PK TP INNER JOIN M_OZAX_LOCATION LO ON TP.NINUSI_CD=LO.NINUSI_CD AND TP.SOSIKI_CD=LO.SOSIKI_CD AND LEFT(TP.LOCATION_CODE,1)=LO.LOCATION_CODE AND LO.LOCATION_KUBUN='2'";
			$sql .= $where;
			$sql .= " AND TP.TOTAL_PK_KUBUN <> '2'";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"トータルピッキング進捗バラサマリーデータ 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
						"BANGO" => $result['A1'],
						"HOMEN_SU" => $result['A2'],
						"ITEM_SU" => $result['A3'],
						"SO_1_SU" => $result['A4'],
						"SO_2_SU" => $result['A5'],
						"SO_3_SU" => $result['A6'],
						"SO_BARA_SU" => $result['A7'],
						"GOKEI_SU" => $result['A8']
				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM070", $e->getMessage());
			$pdo = null;
		}


		return array();
	}

	/**
	 * トータルピッキング進捗ＣＳＶデータ取得
	 * @access   public
	 * @return   トータルピッキング進捗ＣＳＶデータ
	 */
	function getCSV($ymd_from = '',$ymd_to = '',$BatchNo_from = '',$BatchNo_to= '',$Tokuisaki= '',$homen = '',$shomikigen_henko = '',$Sinchoku = '',$Shohin = '') {

		$text = array();

		//ヘッダー情報
		$header = "PK区分,出荷日,方面,バッチ№,商品コード,賞味期限日,変更賞味期限日,ロケーション,開始時間,終了時間,";
		$header .= "荷姿１指示数,荷姿１単位,荷姿２指示数,荷姿２単位,荷姿３指示数,荷姿３単位,総バラ指示数,荷姿３単位,";
		$header .= "荷姿１済数,荷姿１単位,荷姿２済数,荷姿２単位,荷姿３済数,荷姿３単位,総バラ済数,荷姿３単位,";
		$header .= "荷姿１残数,荷姿１単位,荷姿２残数,荷姿２単位,荷姿３残数,荷姿３単位,総バラ残数,荷姿３単位,";
		$header .= "商品名,トータルPK№,賞味期限管理区分,得意先名,作業者";

		$text[] = $header;

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			$sql  = "SELECT M1.MEI_1 AS A1";
			$sql .= ",PK.SHUKKA_BI AS A2";
			$sql .= ",PK.HOMEN_MEI AS A3";
			$sql .= ",PK.BATCH_NUMBER AS A4";
			$sql .= ",PK.SHOHIN_CODE AS A5";
			$sql .= ",COALESCE(PK.SYOMI_KIGEN,'') AS A6";
			$sql .= ",COALESCE(IP.HENKO_SYOMI_KIGEN,'') AS A7";
			$sql .= ",COALESCE(PK.LOCATION_CODE,'')  AS A8";
			$sql .= ",COALESCE(PK.KAISI_JIKAN,'') AS A9";
			$sql .= ",COALESCE(PK.SYURYO_JIKAN,'') AS A10";
			$sql .= ",SUM(IP.HENKO_NISUGATA_1_SHIJISU) AS A11";
			$sql .= ",SH.NISUGATA_1_TANI AS A12";
			$sql .= ",SUM(IP.HENKO_NISUGATA_2_SHIJISU) AS A13";
			$sql .= ",SH.NISUGATA_2_TANI AS A14";
			$sql .= ",SUM(IP.HENKO_NISUGATA_3_SHIJISU) AS A15";
			$sql .= ",SH.NISUGATA_3_TANI AS A16";
			$sql .= ",SUM(IP.HENKO_SO_BARA_SU) AS A17";
			$sql .= ",SH.NISUGATA_3_TANI 	AS A18";
			$sql .= ",SUM(IP.NISUGATA_1_SUMISU) 	AS A19";
			$sql .= ",SH.NISUGATA_1_TANI 	AS A20";
			$sql .= ",SUM(IP.NISUGATA_2_SUMISU) 	AS A21";
			$sql .= ",SH.NISUGATA_2_TANI 	AS A22";
			$sql .= ",SUM(IP.NISUGATA_3_SUMISU) 	AS A23";
			$sql .= ",SH.NISUGATA_3_TANI 	AS A24";
			$sql .= ",SUM(IP.SO_BARA_SUMISU) AS A25";
			$sql .= ",SH.NISUGATA_3_TANI AS A26";
			$sql .= ",SUM(IP.HENKO_NISUGATA_1_SHIJISU-IP.NISUGATA_1_SUMISU) AS A27";
			$sql .= ",SH.NISUGATA_1_TANI AS A28";
			$sql .= ",SUM(IP.HENKO_NISUGATA_2_SHIJISU-IP.NISUGATA_2_SUMISU) AS A29";
			$sql .= ",SH.NISUGATA_2_TANI AS A30";
			$sql .= ",SUM(IP.HENKO_NISUGATA_3_SHIJISU-IP.NISUGATA_3_SUMISU) AS A31";
			$sql .= ",SH.NISUGATA_3_TANI AS A32";
			$sql .= ",SUM(IP.HENKO_SO_BARA_SU-IP.SO_BARA_SUMISU) AS A33";
			$sql .= ",SH.NISUGATA_3_TANI AS A34";
			$sql .= ",PK.SHOHIN_MEI AS A35";
			$sql .= ",PK.TOTAL_PK_NUMBER AS A36";
			$sql .= ",M2.MEI_1 AS A37";
			$sql .= ",PK.TOKUISAKI_MEI 	AS A38";
			$sql .= ",S1.STAFF_NM 	AS A39";
			$sql .= " FROM T_OZAX_TOTAL_PK PK INNER JOIN M_OZAX_SHOHIN SH ON (PK.NINUSI_CD=SH.NINUSI_CD AND PK.SOSIKI_CD=SH.SOSIKI_CD  AND PK.SHOHIN_CODE=SH.SHOHIN_CODE)";
			$sql .= " INNER JOIN T_OZAX_IPOD_TOTAL_PK IP ON (PK.NINUSI_CD=IP.NINUSI_CD AND PK.SOSIKI_CD=IP.SOSIKI_CD  AND PK.GYOTAI_SOKO_CODE=IP.GYOTAI_SOKO_CODE  AND PK.SHUKKA_BI=IP.SHUKKA_BI  AND PK.BATCH_NUMBER=IP.BATCH_NUMBER  AND PK.HOMEN_MEI=IP.HOMEN_MEI  AND PK.SHOHIN_CODE=IP.SHOHIN_CODE  AND PK.TOTAL_PK_NUMBER=IP.TOTAL_PK_NUMBER)";
			$sql .= " LEFT JOIN M_MEISYO M1 ON (PK.NINUSI_CD=M1.NINUSI_CD AND PK.SOSIKI_CD=M1.SOSIKI_CD  AND PK.TOTAL_PK_KUBUN=M1.MEI_CD AND M1.MEI_KBN='37')";
			$sql .= " LEFT JOIN M_MEISYO M2 ON (PK.NINUSI_CD=M2.NINUSI_CD AND PK.SOSIKI_CD=M2.SOSIKI_CD  AND SH.SHOMI_KIGEN_KUBUN=M2.MEI_CD AND M2.MEI_KBN='41')";
			$sql .= " LEFT JOIN M_STAFF_KIHON S1 ON (PK.KOSIN_STAFF=S1.STAFF_CD)";

			$sql .= ' WHERE ';
			$sql .= "  PK.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  PK.SOSIKI_CD='" . $this->_sosiki_cd . "'";

			//出荷日
			if ($ymd_from != ''){
				$sql .= " AND PK.SHUKKA_BI >= '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}

			if ($ymd_to != ''){
				$sql .= " AND PK.SHUKKA_BI <= '" . $this->MCommon->strToDateString($ymd_to) . "'" ;
			}

			//バッチ№
			if ($BatchNo_from != ''){
				$sql .= " AND PK.BATCH_NUMBER >= '" . $BatchNo_from . "'" ;
			}

			if ($BatchNo_to != ''){
				$sql .= " AND PK.BATCH_NUMBER <= '" . $BatchNo_to . "'" ;
			}

			//得意先
			if ($Tokuisaki != ''){
				$sql .= " AND PK.TOKUISAKI_MEI LIKE '%" . $Tokuisaki . "%'" ;
			}

			//方面
			if ($homen != ''){
				$sql .= " AND PK.HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}

			//賞味期限日変更分のみ
			if ($shomikigen_henko == 'on'){
				$sql .= " AND TRIM(IP.HENKO_SYOMI_KIGEN) != ''" ;
			}

			//進捗区分
			if ($Sinchoku != ''){
				$sql .= " AND TRIM(PK.TOTAL_PK_KUBUN) LIKE '%" . $Sinchoku . "%'" ;
			}

			//商品
			if ($Sinchoku != ''){
				$sql .= " AND (TRIM(PK.SHOHIN_CODE) LIKE '%" . $Shohin . "%' OR TRIM(PK.SHOHIN_MEI) LIKE '%" . $Shohin . "%')" ;
			}

			$sql .= "    GROUP BY";
			$sql .= "     M1.MEI_1,PK.SHUKKA_BI";
			$sql .= "     ,PK.HOMEN_MEI";
			$sql .= "     ,PK.BATCH_NUMBER";
			$sql .= "     ,PK.SHOHIN_CODE";
			$sql .= "     ,PK.SYOMI_KIGEN";
			$sql .= "     ,IP.HENKO_SYOMI_KIGEN";
			$sql .= "     ,PK.LOCATION_CODE";
			$sql .= "     ,PK.KAISI_JIKAN";
			$sql .= "     ,PK.SYURYO_JIKAN";
			$sql .= "     ,PK.TOTAL_PK_NUMBER";
			$sql .= "     ,PK.SHOHIN_MEI";
			$sql .= "     ,PK.TOTAL_PK_NUMBER";
			$sql .= "     ,M2.MEI_1";
			$sql .= "     ,PK.TOKUISAKI_MEI";
			$sql .= "     ,S1.STAFF_NM";
			$sql .= "    ORDER BY";
			$sql .= "      PK.TOTAL_PK_KUBUN,";
			$sql .= "      PK.SHUKKA_BI,";
			$sql .= "      PK.HOMEN_MEI,";
			$sql .= "      PK.BATCH_NUMBER,";
			$sql .= "      PK.LOCATION_CODE";
			$sql .= ";";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"トータルピッキング進捗CSVデータ 取得");

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
				$tmpText .= $result['A38'] . ',';
				$tmpText .= $result['A39'] ;

				$text[] = $tmpText;

			}

			$pdo = null;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM070", $e->getMessage());
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

			$this->printLog("fatal", "例外発生", "TPM070", $e->getMessage());
			$pdo = null;
		}

		return date("Ymd",strtotime("-1 day"));;

	}

}

?>