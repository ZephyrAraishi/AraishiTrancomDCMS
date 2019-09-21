<?php
/**
 * TPM090Model
 *
 * 積込検品進捗画面
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class TPM090Model extends DCMSAppModel{

	public $name = 'TPM090Model';
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
	 * 積込検品進捗データ取得
	 * @access   public
	 * @return   積込検品進捗データ
	 */
	public function getList($ymd_from = '', $ymd_to = '', $homen= '', $tenpo = '',$Sinchoku = '',$tokuisaki = '') {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			//積込検品進捗データの取得
			$sql  = "SELECT M1.MEI_1 AS A1";
			$sql .= ",TN.NOHIN_BI AS A2";
			$sql .= ",TN.TENPO_CODE AS A3";
			$sql .= ",TN.TENPO_MEI AS A4";
			$sql .= ",COALESCE(TN.KAISI_JIKAN,'') AS A5";
			$sql .= ",COALESCE(TN.SYURYO_JIKAN,'') AS A6";
			$sql .= ",TN.KONPO_SU AS A7";
			$sql .= ",TN.SUMI_KONPO_SU AS A8";
			$sql .= ",TN.KONPO_SU-TN.SUMI_KONPO_SU AS A9";
			$sql .= ",TN.TOKUISAKI_MEI 	AS A10";
			$sql .= ",TN.HOMEN_MEI 	AS A11";
			$sql .= ",TN.TOKUISAKI_CODE 	AS A12";
			$sql .= " FROM T_OZAX_TENPO_SIWAKE TN ";
			$sql .= " LEFT JOIN M_MEISYO M1 ON (TN.NINUSI_CD=M1.NINUSI_CD AND TN.SOSIKI_CD=M1.SOSIKI_CD  AND TN.TENPO_SIWAKE_KUBUN=M1.MEI_CD AND M1.MEI_KBN='39')";

			$sql .= ' WHERE ';
			$sql .= "  TN.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  TN.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "  TN.KONPO_SU<>0";

			//納品日
			if ($ymd_from != ''){
				$sql .= " AND TN.NOHIN_BI >= '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}

			if ($ymd_to != ''){
				$sql .= " AND TN.NOHIN_BI <= '" . $this->MCommon->strToDateString($ymd_to) . "'" ;
			}

			//得意先名
			if ($tokuisaki != ''){
				$sql .= " AND TN.TOKUISAKI_MEI LIKE '%" . $tokuisaki . "%'" ;
			}

			//TC倉庫名
			if ($homen != ''){
				$sql .= " AND TN.HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}

			//店舗名
			if ($tenpo != ''){
				$sql .= " AND TN.TENPO_MEI LIKE '%" . $tenpo . "%'" ;
			}

			//進捗区分
			if ($Sinchoku != ''){
				$sql .= " AND TRIM(TN.TENPO_SIWAKE_KUBUN) LIKE '%" . $Sinchoku . "%'" ;
			}

			$sql .= "    ORDER BY";
			$sql .= "      TN.NOHIN_BI,";
			$sql .= "      TN.HOMEN_MEI,";
			$sql .= "      TN.TENPO_SIWAKE_KUBUN,";
			$sql .= "      TN.TENPO_MEI";
			$sql .= ";";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"積込検品進捗データ 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
				    "TENPO_SIWAKE_KUBUN_MEI" => $result['A1'],
					"NOHIN_BI" => $result['A2'],
					"TENPO_CODE" => $result['A3'],
					"TENPO_MEI" => $result['A4'],
					"KAISI_JIKAN" => $result['A5'],
					"SYURYO_JIKAN" => $result['A6'],
					"KONPO_SU" => $result['A7'],
					"SUMI_KONPO_SU" => $result['A8'],
					"ZAN_KONPO_SU" => $result['A9'],
					"TOKUISAKI_MEI" => $result['A10'],
					"HOMEN_MEI" => $result['A11'],
					"TOKUISAKI_CODE" => $result['A12']
				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM090", $e->getMessage());
			$pdo = null;
		}


		return array();
	}

	/**
	 * 積込検品進捗サマリーデータ取得
	 * @access   public
	 * @return   積込検品進捗サマリーデータ
	 */
	public function getSumallyList($ymd_from = '',$ymd_to = '',$homen= '',$tenpo = '',$Sinchoku = '',$tokuisaki = '') {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$where = ' ';
			$where .= ' WHERE ';
			$where .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$where .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$where .= "  KONPO_SU<>0";
			//納品日
			if ($ymd_from != ''){
				$where .= " AND NOHIN_BI >= '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}

			if ($ymd_to != ''){
				$where .= " AND NOHIN_BI <= '" . $this->MCommon->strToDateString($ymd_to) . "'" ;
			}

			//得意先名
			if ($tokuisaki != ''){
				$where .= " AND TOKUISAKI_MEI LIKE '%" . $tokuisaki . "%'" ;
			}

			//TC倉庫名
			if ($homen != ''){
				$where .= " AND HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}

			//店舗
			if ($tenpo != ''){
				$where .= " AND TENPO_MEI LIKE '%" . $tenpo . "%'" ;
			}

				//積込検品進捗データの取得
			$sql  = "SELECT '1' AS A1,COUNT(DISTINCT HOMEN_MEI) AS A2,COUNT(DISTINCT TENPO_MEI) AS A3,COALESCE(SUM(KONPO_SU),0) AS A4";
			$sql .= " FROM T_OZAX_TENPO_SIWAKE";
			$sql .= $where;
			$sql .= " UNION";
			$sql .= " SELECT '2' AS A1,COUNT(DISTINCT HOMEN_MEI) AS A2,COUNT(DISTINCT TENPO_MEI) AS A3,COALESCE(SUM(SUMI_KONPO_SU),0) AS A4";
			$sql .= " FROM T_OZAX_TENPO_SIWAKE";
			$sql .= $where;
			$sql .= " AND TENPO_SIWAKE_KUBUN = '2'";
			$sql .= " UNION";
			$sql .= " SELECT '3' AS A1,COUNT(DISTINCT HOMEN_MEI) AS A2,COUNT(DISTINCT TENPO_MEI) AS A3,COALESCE(SUM(KONPO_SU-SUMI_KONPO_SU),0) AS A4";
			$sql .= " FROM T_OZAX_TENPO_SIWAKE";
			$sql .= $where;
			$sql .= " AND TENPO_SIWAKE_KUBUN <> '2'";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"積込検品進捗サマリーデータ 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
						"BANGO" => $result['A1'],
						"HOMEN_SU" => $result['A2'],
						"TENPO_SU" => $result['A3'],
						"KONPO_SU" => $result['A4']
				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM090", $e->getMessage());
			$pdo = null;
		}


		return array();
	}

	/**
	 * 積込検品ラベル残データ取得
	 * @access   public
	 * @return   積込検品ラベル残データ
	 */
	public function getZanList($ymd_from = '', $ymd_to = '', $homen= '', $tenpo = '',$Sinchoku = '',$tokuisaki = '') {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			//積込検品進捗データの取得
			$sql  = "SELECT ats.NOHIN_BI AS A1";
			$sql .= ",ats.HOMEN_MEI A2";
			$sql .= ",ats.TENPO_MEI AS A3";
			$sql .= ",COALESCE(ats.CASE_BARCODE,'') AS A4";
			$sql .= ",COALESCE(ats.BARA_BARCODE,'') AS A5";
			$sql .= ",case when cl.SHOHIN_MEI is null then 'バラ梱包' else cl.SHOHIN_MEI end AS A6";
			$sql .= "  from t_ozax_android_tenpo_siwake ats ";
			$sql .= "  left join t_ozax_case_label cl on ats.NINUSI_CD=cl.NINUSI_CD and ats.SOSIKI_CD=cl.SOSIKI_CD and ats.HOMEN_MEI=cl.HOMEN_MEI and ats.TENPO_CODE=cl.TENPO_CODE  and ats.CASE_BARCODE=cl.CASE_LABEL_BARCODE ";
			$sql .= ' WHERE ';
			$sql .= "  ats.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  ats.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "  (ats.SUMI_CASE_BARCODE = '' or ats.SUMI_CASE_BARCODE is null)";
			$sql .= "  and (ats.SUMI_BARA_BARCODE = '' or ats.SUMI_BARA_BARCODE is null)";

			//納品日
			if ($ymd_from != ''){
				$sql .= " AND ats.NOHIN_BI >= '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}

			if ($ymd_to != ''){
				$sql .= " AND ats.NOHIN_BI <= '" . $this->MCommon->strToDateString($ymd_to) . "'" ;
			}

			//得意先名
			if ($tokuisaki != ''){
				$sql .= " AND ats.TOKUISAKI_MEI LIKE '%" . $tokuisaki . "%'" ;
			}

			//TC倉庫名
			if ($homen != ''){
				$sql .= " AND ats.HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}

			//店舗名
			if ($tenpo != ''){
				$sql .= " AND ats.TENPO_MEI LIKE '%" . $tenpo . "%'" ;
			}

			$sql .= "    ORDER BY";
			$sql .= "      ats.NOHIN_BI,";
			$sql .= "      ats.HOMEN_MEI,";
			$sql .= "      ats.TENPO_MEI";
			$sql .= ";";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"積込検品残ラベルデータ 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
					"NOHIN_BI" => $result['A1'],
					"HOMEN_MEI" => $result['A2'],
					"TENPO_MEI" => $result['A3'],
					"CASE_BARCODE" => $result['A4'],
					"BARA_BARCODE" => $result['A5'],
					"SHOHIN" => $result['A6']
				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM090", $e->getMessage());
			$pdo = null;
		}


		return array();
	}

	/**
	 * 積込検品進捗ＣＳＶデータ取得
	 * @access   public
	 * @return   積込検品進捗CSVデータ
	 */
	function getCSV($ymd_from = '',$ymd_to = '',$homen= '',$tenpo = '',$Sinchoku = '',$tokuisaki = '') {

		$text = array();

		//ヘッダー情報
		$header = "積込検品区分,納品日,方面名,得意先コード,店舗コード,店舗名,開始時間,終了時間,";
		$header .= "指示梱包数,積込梱包数,残梱包数,得意先名";

		$text[] = $header;

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			//積込検品進捗データの取得
			$sql  = "SELECT M1.MEI_1 AS A1";
			$sql .= ",TN.NOHIN_BI AS A2";
			$sql .= ",TN.TOKUISAKI_CODE 	AS A3";
			$sql .= ",TN.TENPO_CODE AS A4";
			$sql .= ",TN.TENPO_MEI AS A5";
			$sql .= ",COALESCE(TN.KAISI_JIKAN,'') AS A6";
			$sql .= ",COALESCE(TN.SYURYO_JIKAN,'') AS A7";
			$sql .= ",TN.KONPO_SU AS A8";
			$sql .= ",TN.SUMI_KONPO_SU AS A9";
			$sql .= ",TN.KONPO_SU-TN.SUMI_KONPO_SU AS A10";
			$sql .= ",TN.TOKUISAKI_MEI 	AS A11";
			$sql .= ",TN.HOMEN_MEI 	AS A12";
			$sql .= " FROM T_OZAX_TENPO_SIWAKE TN ";
			$sql .= " LEFT JOIN M_MEISYO M1 ON (TN.NINUSI_CD=M1.NINUSI_CD AND TN.SOSIKI_CD=M1.SOSIKI_CD  AND TN.TENPO_SIWAKE_KUBUN=M1.MEI_CD AND M1.MEI_KBN='39')";

			$sql .= ' WHERE ';
			$sql .= "  TN.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  TN.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "  TN.KONPO_SU<>0";

			//納品日
			if ($ymd_from != ''){
				$sql .= " AND TN.NOHIN_BI >= '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}

			if ($ymd_to != ''){
				$sql .= " AND TN.NOHIN_BI <= '" . $this->MCommon->strToDateString($ymd_to) . "'" ;
			}

			//得意先名
			if ($tokuisaki != ''){
				$sql .= " AND TN.TOKUISAKI_MEI LIKE '%" . $tokuisaki . "%'" ;
			}

			//TC倉庫名
			if ($homen != ''){
				$sql .= " AND TN.HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}

			//店舗名
			if ($tenpo != ''){
				$sql .= " AND TN.TENPO_MEI LIKE '%" . $tenpo . "%'" ;
			}

			//進捗区分
			if ($Sinchoku != ''){
				$sql .= " AND TRIM(TN.TENPO_SIWAKE_KUBUN) LIKE '%" . $Sinchoku . "%'" ;
			}

			$sql .= "    ORDER BY";
			$sql .= "      TN.NOHIN_BI,";
			$sql .= "      TN.HOMEN_MEI,";
			$sql .= "      TN.TENPO_SIWAKE_KUBUN,";
			$sql .= "      TN.TENPO_MEI";
			$sql .= ";";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"種蒔進捗CSVデータ 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$tmpText = $result['A1'] . ',';
				$tmpText .= $result['A2'] . ',';
				$tmpText .= $result['A12'] . ',';
				$tmpText .= $result['A3'] . ',';
				$tmpText .= $result['A4'] . ',';
				$tmpText .= $result['A5'] . ',';
				$tmpText .= $result['A6'] . ',';
				$tmpText .= $result['A7'] . ',';
				$tmpText .= $result['A8'] . ',';
				$tmpText .= $result['A9'] . ',';
				$tmpText .= $result['A10'] . ',';
				$tmpText .= $result['A11'] ;

				$text[] = $tmpText;

			}

			$pdo = null;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM090", $e->getMessage());
			$pdo = null;
		}


		return $text;
	}

	/**
	 * 積込検品残ラベル情報ＣＳＶデータ取得
	 * @access   public
	 * @return   積込検品残ラベルCSVデータ
	 */
	function getZanCSV($ymd_from = '',$ymd_to = '',$homen= '',$tenpo = '',$Sinchoku = '',$tokuisaki = '') {

		$text = array();

		//ヘッダー情報
		$header = "納品日,方面名,店舗名,ケースバーコード,看板バーコード,商品名";

		$text[] = $header;

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			//積込検品進捗データの取得
			$sql  = "SELECT ts.NOHIN_BI AS A1";
			$sql .= ",ts.HOMEN_MEI A2";
			$sql .= ",ts.TENPO_MEI AS A3";
			$sql .= ",COALESCE(ats.CASE_BARCODE,'') AS A4";
			$sql .= ",COALESCE(ats.BARA_BARCODE,'') AS A5";
			$sql .= ",case when cl.SHOHIN_MEI is null then 'バラ梱包' else cl.SHOHIN_MEI end AS A6";
			$sql .= "  from t_ozax_tenpo_siwake ts inner join t_ozax_android_tenpo_siwake ats ";
			$sql .= "  on ts.NINUSI_CD=ats.NINUSI_CD and ts.SOSIKI_CD=ats.SOSIKI_CD and ts.HOMEN_MEI=ats.HOMEN_MEI and ts.NOHIN_BI=ats.NOHIN_BI and ts.TOKUISAKI_CODE=ats.TOKUISAKI_CODE and ts.TENPO_CODE=ats.TENPO_CODE";
			$sql .= "  left join t_ozax_case_label cl on ats.NINUSI_CD=cl.NINUSI_CD and ats.SOSIKI_CD=cl.SOSIKI_CD and ats.HOMEN_MEI=cl.HOMEN_MEI and ats.TENPO_CODE=cl.TENPO_CODE  and ats.CASE_BARCODE=cl.CASE_LABEL_BARCODE ";

			$sql .= ' WHERE ';
			$sql .= "  ts.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  ts.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "  ts.TENPO_SIWAKE_KUBUN <> '2'";
			$sql .= "  and (ats.SUMI_CASE_BARCODE = '' or ats.SUMI_CASE_BARCODE is null)";
			$sql .= "   and (ats.SUMI_BARA_BARCODE = '' or ats.SUMI_BARA_BARCODE is null)";

			//納品日
			if ($ymd_from != ''){
				$sql .= " AND ts.NOHIN_BI >= '" . $this->MCommon->strToDateString($ymd_from) . "'" ;
			}

			if ($ymd_to != ''){
				$sql .= " AND ts.NOHIN_BI <= '" . $this->MCommon->strToDateString($ymd_to) . "'" ;
			}

			//得意先名
			if ($tokuisaki != ''){
				$sql .= " AND ts.TOKUISAKI_MEI LIKE '%" . $tokuisaki . "%'" ;
			}

			//TC倉庫名
			if ($homen != ''){
				$sql .= " AND ts.HOMEN_MEI LIKE '%" . $homen . "%'" ;
			}

			//店舗名
			if ($tenpo != ''){
				$sql .= " AND ts.TENPO_MEI LIKE '%" . $tenpo . "%'" ;
			}


			$sql .= "    ORDER BY";
			$sql .= "      ts.NOHIN_BI,";
			$sql .= "      ts.HOMEN_MEI,";
			$sql .= "      ts.TENPO_MEI";
			$sql .= ";";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"種蒔進捗残ラベルCSVデータ 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$tmpText = $result['A1'] . ',';
				$tmpText .= $result['A2'] . ',';
				$tmpText .= $result['A3'] . ',';
				$tmpText .= $result['A4'] . ',';
				$tmpText .= $result['A5'] . ',';
				$tmpText .= $result['A6'] ;

				$text[] = $tmpText;

			}

			$pdo = null;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM090", $e->getMessage());
			$pdo = null;
		}


		return $text;
	}

	/**
	 * 積込検品ＴＣ名のデータ取得
	 * @access   public
	 * @return   積込検品ＴＣ名データ
	 */
	public function getTCName($ymd_from = '',$ymd_to = '') {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$where = ' ';
			$where .= ' WHERE ';
			$where .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$where .= "  SOSIKI_CD='" . $this->_sosiki_cd . "'";
			//納品日
			if ($ymd_from != ''){
				$where .= " AND NOHIN_BI >= '" . $this->MCommon->strToDateMath($ymd_from,-10) . "'" ;
			}

			if ($ymd_to != ''){
				$where .= " AND NOHIN_BI <= '" . $this->MCommon->strToDateMath($ymd_to,3) . "'" ;
			}

				//積込検品ＴＣ名データの取得
			$sql  = "SELECT HOMEN_MEI AS A1";
			$sql .= " FROM T_OZAX_TENPO_SIWAKE";
			$sql .= $where;
			$sql .= " GROUP BY HOMEN_MEI;";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"積込検品進捗サマリーデータ 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
						"HOMEN_MEI" => $result['A1']
				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM090", $e->getMessage());
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
			$sql = "CALL P_TPM090_GET_TIMESTAMP(";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " @timestamp";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, '積込検品進捗　タイムスタンプ取得');

			$sql = "";
			$sql .= " SELECT";
			$sql .= "  @timestamp";


			$this->queryWithPDOLog($stmt,$pdo,$sql, '積込検品進捗　タイムスタンプ取得');
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