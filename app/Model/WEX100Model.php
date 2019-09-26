<?php
/**
 * WEX100
 *
 * DS用作業実績
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class WEX100Model extends DCMSAppModel{

	public $name = 'WEX100Model';
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
	public function getList($komokuList,$ymd_from,$ymd_to,$kaisya_cd,$staff_cd_nm,$bnri_cyu_cd,$syaryo_cd,$area_cd,
												 $more,$jikan_from,$jikan_to,$kyori_from,$kyori_to) {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			//工程データの取得
			$sql  = "";
			$sql .= "SELECT";
			$sql .= "    A.NINUSI_CD AS NINUSI_CD";
			$sql .= "   ,C.NINUSI_RYAKU AS NINUSI_NM";
			$sql .= "   ,A.SOSIKI_CD AS SOSIKI_CD";
			$sql .= "   ,D.SOSIKI_RYAKU AS SOSIKI_NM";
			$sql .= "   ,A.YMD_SYUGYO AS YMD_SYUGYO";
			$sql .= "   ,E.MEI_1 AS SYARYO_NM";
			$sql .= "   ,F.BNRI_CYU_RYAKU AS BNRI_CYU_NM";
			$sql .= "   ,G.MEI_1 AS AREA_NM";
			$sql .= "   ,A.STAFF_CD AS STAFF_CD";
			$sql .= "   ,H.STAFF_NM AS STAFF_NM";
			$sql .= "   ,I.MEI_1 AS SEX";
			$sql .= "   ,J.KAISYA_NM AS KAISYA_NM";
			$sql .= "   ,K.MEI_1 AS KEIYAKU_NM";
			$sql .= "   ,MIN(B.DT_SYUGYO_ST) AS DT_SYUGYO_ST";
			$sql .= "   ,MAX(B.DT_SYUGYO_ED) AS DT_SYUGYO_ED";
			
			foreach ($komokuList as $komoku) {
				
				$sql .= " ,MIN(";
				$sql .= "    CASE";
				$sql .= "      WHEN A.BNRI_SAI_CD='" . $komoku['MEI_CD'] . "'";
				$sql .= "        THEN A.DT_KOTEI_ST";
				$sql .= "      ELSE NULL";
				$sql .= "    END";
				$sql .= "  ) AS " . $komoku['MEI_CD'] . "_MIN_KOTEI_ST";
				$sql .= " ,MAX(";
				$sql .= "    CASE";
				$sql .= "      WHEN A.BNRI_SAI_CD='" . $komoku['MEI_CD'] . "'";
				$sql .= "        THEN A.DT_KOTEI_ED";
				$sql .= "      ELSE NULL";
				$sql .= "    END";
				$sql .= "  ) AS " . $komoku['MEI_CD'] . "_MAX_KOTEI_ED";
				$sql .= " ,MIN(";
				$sql .= "    CASE";
				$sql .= "      WHEN A.BNRI_SAI_CD='" . $komoku['MEI_CD'] . "'";
				$sql .= "        THEN A.KAISI_KYORI";
				$sql .= "      ELSE NULL";
				$sql .= "    END";
				$sql .= "  ) AS " . $komoku['MEI_CD'] . "_MIN_KAISI_KYORI";
				$sql .= " ,MAX(";
				$sql .= "    CASE";
				$sql .= "      WHEN A.BNRI_SAI_CD='" . $komoku['MEI_CD'] . "'";
				$sql .= "        THEN A.SYURYO_KYORI";
				$sql .= "      ELSE NULL";
				$sql .= "    END";
				$sql .= "  ) AS " . $komoku['MEI_CD'] . "_MAX_SYURYO_KYORI";
				$sql .= " ,TRUNCATE(";
				$sql .= "    SUM(";
				$sql .= "      CASE";
				$sql .= "        WHEN A.BNRI_SAI_CD='" . $komoku['MEI_CD'] . "'";
				$sql .= "            AND A.DT_KOTEI_ST IS NOT NULL";
				$sql .= "            AND A.DT_KOTEI_ED IS NOT NULL";
				$sql .= "            AND A.DT_KOTEI_ST <> ''";
				$sql .= "            AND A.DT_KOTEI_ED <> ''";
				$sql .= "          THEN ";
				$sql .= "            TIMESTAMPDIFF(SECOND,A.DT_KOTEI_ST,A.DT_KOTEI_ED)";
				$sql .= "        ELSE 0";
				$sql .= "      END";
				$sql .= "    ) / 60";
				$sql .= "  ,0) AS " . $komoku['MEI_CD'] . "_SUM_KOTEI_M";
				$sql .= " ,SUM(";
				$sql .= "    CASE";
				$sql .= "      WHEN A.BNRI_SAI_CD='" . $komoku['MEI_CD'] . "'";
				$sql .= "            AND A.KAISI_KYORI IS NOT NULL";
				$sql .= "            AND A.SYURYO_KYORI IS NOT NULL";
				$sql .= "        THEN A.SYURYO_KYORI - A.KAISI_KYORI";
				$sql .= "      ELSE 0";
				$sql .= "    END";
				$sql .= "  ) AS " . $komoku['MEI_CD'] . "_SUM_KYORI";
			}
			
			
			$sql .= " FROM";
			$sql .= "   T_KOTEI_D A";
			$sql .= "     INNER JOIN T_KOTEI_H B ON";
			$sql .= "           A.NINUSI_CD=B.NINUSI_CD";
			$sql .= "       AND A.SOSIKI_CD=B.SOSIKI_CD";
			$sql .= "       AND A.STAFF_CD=B.STAFF_CD";
			$sql .= "       AND A.YMD_SYUGYO=B.YMD_SYUGYO";
			$sql .= "     LEFT JOIN M_NINUSI C ON";
			$sql .= "           A.NINUSI_CD=C.NINUSI_CD";
			$sql .= "     LEFT JOIN M_SOSIKI D ON";
			$sql .= "           A.NINUSI_CD=D.NINUSI_CD";
			$sql .= "       AND A.SOSIKI_CD=D.SOSIKI_CD";
			$sql .= "     LEFT JOIN M_MEISYO E ON";
			$sql .= "           A.NINUSI_CD=E.NINUSI_CD";
			$sql .= "       AND A.SOSIKI_CD=E.SOSIKI_CD";
			$sql .= "       AND E.MEI_KBN=41";
			$sql .= "       AND A.SYARYO_BANGO=E.MEI_CD";
			$sql .= "     LEFT JOIN M_BNRI_CYU F ON";
			$sql .= "           A.NINUSI_CD=F.NINUSI_CD";
			$sql .= "       AND A.SOSIKI_CD=F.SOSIKI_CD";
			$sql .= "       AND A.BNRI_DAI_CD=F.BNRI_DAI_CD";
			$sql .= "       AND A.BNRI_CYU_CD=F.BNRI_CYU_CD";
			$sql .= "     LEFT JOIN M_MEISYO G ON";
			$sql .= "           A.NINUSI_CD=G.NINUSI_CD";
			$sql .= "       AND A.SOSIKI_CD=G.SOSIKI_CD";
			$sql .= "       AND G.MEI_KBN=42";
			$sql .= "       AND A.CHIKU_AREA=G.MEI_CD";
			$sql .= "     LEFT JOIN M_STAFF_KIHON H ON";
			$sql .= "           A.STAFF_CD=H.STAFF_CD";
			$sql .= "     LEFT JOIN M_MEISYO I ON";
			$sql .= "           A.NINUSI_CD=I.NINUSI_CD";
			$sql .= "       AND A.SOSIKI_CD=I.SOSIKI_CD";
			$sql .= "       AND I.MEI_KBN=16";
			$sql .= "       AND H.KBN_SEX=I.MEI_CD";
			$sql .= "     LEFT JOIN M_HAKEN_KAISYA J ON";
			$sql .= "           A.NINUSI_CD=J.NINUSI_CD";
			$sql .= "       AND A.SOSIKI_CD=J.SOSIKI_CD";
			$sql .= "       AND H.HAKEN_KAISYA_CD=J.KAISYA_CD";
			$sql .= "     LEFT JOIN M_MEISYO K ON";
			$sql .= "           A.NINUSI_CD=K.NINUSI_CD";
			$sql .= "       AND A.SOSIKI_CD=K.SOSIKI_CD";
			$sql .= "       AND K.MEI_KBN=30";
			$sql .= "       AND H.KBN_KEIYAKU=K.MEI_CD";
			$sql .= " WHERE ";
			$sql .= "      A.NINUSI_CD='" . $this->_ninusi_cd . "'";
			$sql .= "  AND A.SOSIKI_CD='" . $this->_sosiki_cd . "'";
			
			if ($ymd_from != ''){
				$sql .= " AND A.YMD_SYUGYO >= '" . $ymd_from . "'" ;
			}

			if ($ymd_to != ''){
				$sql .= " AND A.YMD_SYUGYO <= '" . $ymd_to . "'" ;
			}
			
			if ($kaisya_cd != ''){
				$sql .= " AND H.HAKEN_KAISYA_CD = '" . $kaisya_cd . "'" ;
			}
			
			if ($staff_cd_nm != ''){
				$sql .= " AND (" ;
				$sql .= "      A.STAFF_CD LIKE '%" . $staff_cd_nm . "%'" ;
				$sql .= "   OR H.STAFF_NM LIKE '%" . $staff_cd_nm . "%'" ;
				$sql .= " )" ;
			}
			
			if ($bnri_cyu_cd != ''){
				$sql .= " AND A.BNRI_CYU_CD = '" . $bnri_cyu_cd . "'" ;
			}
			
			if ($syaryo_cd != ''){
				$sql .= " AND A.SYARYO_BANGO = '" . $syaryo_cd . "'" ;
			}
			
			if ($area_cd != ''){
				$sql .= " AND A.CHIKU_AREA = '" . $area_cd . "'" ;
			}
			
			if ($more == 'on'){
				$sql .= " AND B.DT_SYUGYO_ED IS NULL " ;
			} else {
				$sql .= " AND B.DT_SYUGYO_ED IS NOT NULL " ;
			}
			
			if ($jikan_from != '' && $jikan_to != '' ){
				
				$jikan_from = substr($jikan_from, 0, 2) . ":" . substr($jikan_from, 2, 2);
				$jikan_to = substr($jikan_to, 0, 2) . ":" . substr($jikan_to, 2, 2);
				
				$sql .= " AND A.DT_KOTEI_ST >= ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$jikan_from.":00' )" ;
				$sql .= " AND A.DT_KOTEI_ED <= ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$jikan_to.":00' )" ;
			}
			
			if ($kyori_from != '' && $kyori_to != '' ){
				
				$sql .= " AND A.KAISI_KYORI >= ".$kyori_from;
				$sql .= " AND A.SYURYO_KYORI <= ".$kyori_to;
			}
			
			$sql .= " GROUP BY";
			$sql .= "    A.NINUSI_CD";
			$sql .= "   ,A.SOSIKI_CD";
			$sql .= "   ,A.YMD_SYUGYO";
			$sql .= "   ,A.SYARYO_BANGO";
			$sql .= "   ,A.BNRI_DAI_CD";
			$sql .= "   ,A.BNRI_CYU_CD";
			$sql .= "   ,A.CHIKU_AREA";
			$sql .= "   ,A.STAFF_CD";
			$sql .= " ORDER BY";
			$sql .= "    A.NINUSI_CD";
			$sql .= "   ,A.SOSIKI_CD";
			$sql .= "   ,A.YMD_SYUGYO";
			$sql .= "   ,A.SYARYO_BANGO";
			$sql .= "   ,A.BNRI_DAI_CD";
			$sql .= "   ,A.BNRI_CYU_CD";
			$sql .= "   ,A.CHIKU_AREA";
			$sql .= "   ,A.STAFF_CD";
			$sql .= ";";


			$this->queryWithPDOLog($stmt,$pdo,$sql,"工程データ 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){


				$array = array(
						"NINUSI_CD" => $result['NINUSI_CD'],
						"NINUSI_NM" => $result['NINUSI_NM'],
						"SOSIKI_CD" => $result['SOSIKI_CD'],
						"SOSIKI_NM" => $result['SOSIKI_NM'],
						"YMD_SYUGYO" => (new Datetime($result['YMD_SYUGYO']))->format('Y年n月j日'),
						"SYARYO_NM" => $result['SYARYO_NM'],
						"BNRI_CYU_NM" => $result['BNRI_CYU_NM'],
						"AREA_NM" => $result['AREA_NM'],
						"STAFF_CD" => $result['STAFF_CD'],
						"STAFF_NM" => $result['STAFF_NM'],
						"SEX" => $result['SEX'],
						"KAISYA_NM" => $result['KAISYA_NM'],
						"KEIYAKU_NM" => $result['KEIYAKU_NM'],
						"DT_SYUGYO_ST" => $this->MCommon->changeDateTime($result['YMD_SYUGYO'],$result['DT_SYUGYO_ST']),
						"DT_SYUGYO_ED" => $this->MCommon->changeDateTime($result['YMD_SYUGYO'],$result['DT_SYUGYO_ED']),
				);
				
				foreach ($komokuList as $komoku) {
					
					$diff_kotei = '';
					if(!empty($result[$komoku['MEI_CD'] . "_MIN_KOTEI_ST"]) && !empty($result[$komoku['MEI_CD'] . "_MAX_KOTEI_ED"])) {
						$d1 = new Datetime((new Datetime($result[$komoku['MEI_CD'] . "_MIN_KOTEI_ST"]))->format('Y-m-d H:i:00'));
						$d2 = new Datetime((new Datetime($result[$komoku['MEI_CD'] . "_MAX_KOTEI_ED"]))->format('Y-m-d H:i:00'));
						
						$diff = $d2->diff($d1);
						$diff_kotei = $diff->format('%H:%I');
					}
					
					$array[$komoku['MEI_CD'] . "_MIN_KOTEI_ST"] = $this->MCommon->changeDateTime($result['YMD_SYUGYO'],$result[$komoku['MEI_CD'] . "_MIN_KOTEI_ST"]);
					$array[$komoku['MEI_CD'] . "_MAX_KOTEI_ED"] = $this->MCommon->changeDateTime($result['YMD_SYUGYO'],$result[$komoku['MEI_CD'] . "_MAX_KOTEI_ED"]);
					$array[$komoku['MEI_CD'] . "_DIFF_KOTEI"] = $diff_kotei;
					$array[$komoku['MEI_CD'] . "_MIN_KAISI_KYORI"] = $result[$komoku['MEI_CD'] . "_MIN_KAISI_KYORI"];
					$array[$komoku['MEI_CD'] . "_MAX_SYURYO_KYORI"] = $result[$komoku['MEI_CD'] . "_MAX_SYURYO_KYORI"];
					$array[$komoku['MEI_CD'] . "_SUM_KOTEI_M"] = $result[$komoku['MEI_CD'] . "_SUM_KOTEI_M"];
					$array[$komoku['MEI_CD'] . "_SUM_KYORI"] = $result[$komoku['MEI_CD'] . "_SUM_KYORI"];
				}

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WEX100", $e->getMessage());
			$pdo = null;
		}


		return array();
	}

	public function getHaken() {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql  = "SELECT";
			$sql .= "  KAISYA_CD,";
			$sql .= "  KAISYA_NM";
			$sql .= " FROM";
			$sql .= "  M_HAKEN_KAISYA";
			$sql .= " WHERE ";
			$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "'";
			$sql .= " ORDER BY";
			$sql .= "  KAISYA_CD";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"派遣会社　取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
						"KAISYA_CD" => $result['KAISYA_CD'],
						"KAISYA_NM" => $result['KAISYA_NM']
				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "HRD010", $e->getMessage());
			$pdo = null;
		}


		return array();
	}

	//分類マスタ(中
	public function getBnriCyu() {
		
		$pdo = null;
		
		try{
			
			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "";
			$sql .= "SELECT ";
			$sql .= "  M_BNRI_CYU.BNRI_DAI_CD AS BNRI_DAI_CD,";
			$sql .= "  M_BNRI_CYU.BNRI_CYU_CD AS BNRI_CYU_CD,";
			$sql .= "  M_BNRI_CYU.BNRI_CYU_NM AS BNRI_CYU_NM,";
			$sql .= "  M_BNRI_CYU.BNRI_CYU_RYAKU AS BNRI_CYU_RYAKU";
			$sql .= " FROM";
			$sql .= "  M_BNRI_CYU";
			$sql .= "    INNER JOIN M_SYSTEM ON";
			$sql .= "      M_SYSTEM.NINUSI_CD=M_BNRI_CYU.NINUSI_CD AND";
			$sql .= "      M_SYSTEM.SOSIKI_CD=M_BNRI_CYU.SOSIKI_CD AND";
			$sql .= "      M_SYSTEM.DS_M_BNRI_DAI_CD=M_BNRI_CYU.BNRI_DAI_CD";
			$sql .= " WHERE ";
			$sql .= "  M_BNRI_CYU.NINUSI_CD='" . $this->_ninusi_cd ."' AND";
			$sql .= "  M_BNRI_CYU.SOSIKI_CD='" . $this->_sosiki_cd ."'";
			$sql .= " ORDER BY";
			$sql .= "  M_BNRI_CYU.BNRI_DAI_CD,";
			$sql .= "  M_BNRI_CYU.BNRI_CYU_CD";


			$this->queryWithPDOLog($stmt,$pdo,$sql,"コース　取得");
			
			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
						"BNRI_DAI_CD" => $result['BNRI_DAI_CD'],
						"BNRI_CYU_CD" => $result['BNRI_CYU_CD'],
						"BNRI_CYU_NM" => $result['BNRI_CYU_NM'],
						"BNRI_CYU_RYAKU" => $result['BNRI_CYU_RYAKU']
				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WEX100", $e->getMessage());
			$pdo = null;
		}
			

		return array();
	}

	function getCSV($komokuList,$ymd_from,$ymd_to,$kaisya_cd,$staff_cd_nm,$bnri_cyu_cd,$syaryo_cd,$area_cd,
												 $more,$jikan_from,$jikan_to,$kyori_from,$kyori_to) {

		$text = array();

		//ヘッダー情報
		$header = "荷主コード,荷主名,営業所 or センターコード,営業所 or センター名,配達日,車両No.,コース,地区・エリア,社員番号,担当者,性別,所属部署,契約形態,業務開始時間,業務終了時間,";

		$count = 0;
		foreach ($komokuList as $komoku) {

            if(preg_match('/^[0-1]{7}$/',$komoku['VAL_FREE_STR_1']) ) {

            	if (substr($komoku['VAL_FREE_STR_1'],0,1) == "1") {
                    $header .= $komoku['MEI_1'] . " 開始時間,";
                }

                if (substr($komoku['VAL_FREE_STR_1'],1,1) == "1") {
                    $header .= $komoku['MEI_1'] . " 終了時間,";
                }

                if (substr($komoku['VAL_FREE_STR_1'],2,1) == "1") {
                    $header .= $komoku['MEI_1'] . " 作業時間,";
                }

                if (substr($komoku['VAL_FREE_STR_1'],3,1) == "1") {
                    $header .= $komoku['MEI_1'] . " 開始距離,";
                }

                if (substr($komoku['VAL_FREE_STR_1'],4,1) == "1") {
                    $header .= $komoku['MEI_1'] . " 終了距離,";
                }

                if (substr($komoku['VAL_FREE_STR_1'],5,1) == "1") {
                    $header .= $komoku['MEI_1'] . " 累計時間,";
                }

                if (substr($komoku['VAL_FREE_STR_1'],6,1) == "1") {
                    $header .= $komoku['MEI_1'] . " 累計距離,";
                }

			} else {
                $header .= $komoku['MEI_1'] . " 開始時間,";
                $header .= $komoku['MEI_1'] . " 終了時間,";
                $header .= $komoku['MEI_1'] . " 作業時間,";
                $header .= $komoku['MEI_1'] . " 開始距離,";
                $header .= $komoku['MEI_1'] . " 終了距離,";
                $header .= $komoku['MEI_1'] . " 累計時間,";
                $header .= $komoku['MEI_1'] . " 累計距離,";
			}
			
			$count++;
		}

		$text[] = $header;

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			//工程データの取得
			$sql  = "";
			$sql .= "SELECT";
			$sql .= "    A.NINUSI_CD AS NINUSI_CD";
			$sql .= "   ,C.NINUSI_RYAKU AS NINUSI_NM";
			$sql .= "   ,A.SOSIKI_CD AS SOSIKI_CD";
			$sql .= "   ,D.SOSIKI_RYAKU AS SOSIKI_NM";
			$sql .= "   ,A.YMD_SYUGYO AS YMD_SYUGYO";
			$sql .= "   ,E.MEI_1 AS SYARYO_NM";
			$sql .= "   ,F.BNRI_CYU_RYAKU AS BNRI_CYU_NM";
			$sql .= "   ,G.MEI_1 AS AREA_NM";
			$sql .= "   ,A.STAFF_CD AS STAFF_CD";
			$sql .= "   ,H.STAFF_NM AS STAFF_NM";
			$sql .= "   ,I.MEI_1 AS SEX";
			$sql .= "   ,J.KAISYA_NM AS KAISYA_NM";
			$sql .= "   ,K.MEI_1 AS KEIYAKU_NM";
			$sql .= "   ,MIN(B.DT_SYUGYO_ST) AS DT_SYUGYO_ST";
			$sql .= "   ,MAX(B.DT_SYUGYO_ED) AS DT_SYUGYO_ED";
			
			foreach ($komokuList as $komoku) {
				
				$sql .= " ,MIN(";
				$sql .= "    CASE";
				$sql .= "      WHEN A.BNRI_SAI_CD='" . $komoku['MEI_CD'] . "'";
				$sql .= "        THEN A.DT_KOTEI_ST";
				$sql .= "      ELSE NULL";
				$sql .= "    END";
				$sql .= "  ) AS " . $komoku['MEI_CD'] . "_MIN_KOTEI_ST";
				$sql .= " ,MAX(";
				$sql .= "    CASE";
				$sql .= "      WHEN A.BNRI_SAI_CD='" . $komoku['MEI_CD'] . "'";
				$sql .= "        THEN A.DT_KOTEI_ED";
				$sql .= "      ELSE NULL";
				$sql .= "    END";
				$sql .= "  ) AS " . $komoku['MEI_CD'] . "_MAX_KOTEI_ED";
				$sql .= " ,MIN(";
				$sql .= "    CASE";
				$sql .= "      WHEN A.BNRI_SAI_CD='" . $komoku['MEI_CD'] . "'";
				$sql .= "        THEN A.KAISI_KYORI";
				$sql .= "      ELSE NULL";
				$sql .= "    END";
				$sql .= "  ) AS " . $komoku['MEI_CD'] . "_MIN_KAISI_KYORI";
				$sql .= " ,MAX(";
				$sql .= "    CASE";
				$sql .= "      WHEN A.BNRI_SAI_CD='" . $komoku['MEI_CD'] . "'";
				$sql .= "        THEN A.SYURYO_KYORI";
				$sql .= "      ELSE NULL";
				$sql .= "    END";
				$sql .= "  ) AS " . $komoku['MEI_CD'] . "_MAX_SYURYO_KYORI";
				$sql .= " ,TRUNCATE(";
				$sql .= "    SUM(";
				$sql .= "      CASE";
				$sql .= "        WHEN A.BNRI_SAI_CD='" . $komoku['MEI_CD'] . "'";
				$sql .= "            AND A.DT_KOTEI_ST IS NOT NULL";
				$sql .= "            AND A.DT_KOTEI_ED IS NOT NULL";
				$sql .= "            AND A.DT_KOTEI_ST <> ''";
				$sql .= "            AND A.DT_KOTEI_ED <> ''";
				$sql .= "          THEN ";
				$sql .= "            TIMESTAMPDIFF(SECOND,A.DT_KOTEI_ST,A.DT_KOTEI_ED)";
				$sql .= "        ELSE 0";
				$sql .= "      END";
				$sql .= "    ) / 60";
				$sql .= "  ,0) AS " . $komoku['MEI_CD'] . "_SUM_KOTEI_M";
				$sql .= " ,SUM(";
				$sql .= "    CASE";
				$sql .= "      WHEN A.BNRI_SAI_CD='" . $komoku['MEI_CD'] . "'";
				$sql .= "            AND A.KAISI_KYORI IS NOT NULL";
				$sql .= "            AND A.SYURYO_KYORI IS NOT NULL";
				$sql .= "        THEN A.SYURYO_KYORI - A.KAISI_KYORI";
				$sql .= "      ELSE 0";
				$sql .= "    END";
				$sql .= "  ) AS " . $komoku['MEI_CD'] . "_SUM_KYORI";
			}
			
			
			$sql .= " FROM";
			$sql .= "   T_KOTEI_D A";
			$sql .= "     INNER JOIN T_KOTEI_H B ON";
			$sql .= "           A.NINUSI_CD=B.NINUSI_CD";
			$sql .= "       AND A.SOSIKI_CD=B.SOSIKI_CD";
			$sql .= "       AND A.STAFF_CD=B.STAFF_CD";
			$sql .= "       AND A.YMD_SYUGYO=B.YMD_SYUGYO";
			$sql .= "     LEFT JOIN M_NINUSI C ON";
			$sql .= "           A.NINUSI_CD=C.NINUSI_CD";
			$sql .= "     LEFT JOIN M_SOSIKI D ON";
			$sql .= "           A.NINUSI_CD=D.NINUSI_CD";
			$sql .= "       AND A.SOSIKI_CD=D.SOSIKI_CD";
			$sql .= "     LEFT JOIN M_MEISYO E ON";
			$sql .= "           A.NINUSI_CD=E.NINUSI_CD";
			$sql .= "       AND A.SOSIKI_CD=E.SOSIKI_CD";
			$sql .= "       AND E.MEI_KBN=41";
			$sql .= "       AND A.SYARYO_BANGO=E.MEI_CD";
			$sql .= "     LEFT JOIN M_BNRI_CYU F ON";
			$sql .= "           A.NINUSI_CD=F.NINUSI_CD";
			$sql .= "       AND A.SOSIKI_CD=F.SOSIKI_CD";
			$sql .= "       AND A.BNRI_DAI_CD=F.BNRI_DAI_CD";
			$sql .= "       AND A.BNRI_CYU_CD=F.BNRI_CYU_CD";
			$sql .= "     LEFT JOIN M_MEISYO G ON";
			$sql .= "           A.NINUSI_CD=G.NINUSI_CD";
			$sql .= "       AND A.SOSIKI_CD=G.SOSIKI_CD";
			$sql .= "       AND G.MEI_KBN=42";
			$sql .= "       AND A.CHIKU_AREA=G.MEI_CD";
			$sql .= "     LEFT JOIN M_STAFF_KIHON H ON";
			$sql .= "           A.STAFF_CD=H.STAFF_CD";
			$sql .= "     LEFT JOIN M_MEISYO I ON";
			$sql .= "           A.NINUSI_CD=I.NINUSI_CD";
			$sql .= "       AND A.SOSIKI_CD=I.SOSIKI_CD";
			$sql .= "       AND I.MEI_KBN=16";
			$sql .= "       AND H.KBN_SEX=I.MEI_CD";
			$sql .= "     LEFT JOIN M_HAKEN_KAISYA J ON";
			$sql .= "           A.NINUSI_CD=J.NINUSI_CD";
			$sql .= "       AND A.SOSIKI_CD=J.SOSIKI_CD";
			$sql .= "       AND H.HAKEN_KAISYA_CD=J.KAISYA_CD";
			$sql .= "     LEFT JOIN M_MEISYO K ON";
			$sql .= "           A.NINUSI_CD=K.NINUSI_CD";
			$sql .= "       AND A.SOSIKI_CD=K.SOSIKI_CD";
			$sql .= "       AND K.MEI_KBN=30";
			$sql .= "       AND H.KBN_KEIYAKU=K.MEI_CD";
			$sql .= " WHERE ";
			$sql .= "      A.NINUSI_CD='" . $this->_ninusi_cd . "'";
			$sql .= "  AND A.SOSIKI_CD='" . $this->_sosiki_cd . "'";
			
			if ($ymd_from != ''){
				$sql .= " AND A.YMD_SYUGYO >= '" . $ymd_from . "'" ;
			}

			if ($ymd_to != ''){
				$sql .= " AND A.YMD_SYUGYO <= '" . $ymd_to . "'" ;
			}
			
			if ($kaisya_cd != ''){
				$sql .= " AND H.HAKEN_KAISYA_CD = '" . $kaisya_cd . "'" ;
			}
			
			if ($staff_cd_nm != ''){
				$sql .= " AND (" ;
				$sql .= "      A.STAFF_CD LIKE '%" . $staff_cd_nm . "%'" ;
				$sql .= "   OR H.STAFF_NM LIKE '%" . $staff_cd_nm . "%'" ;
				$sql .= " )" ;
			}
			
			if ($bnri_cyu_cd != ''){
				$sql .= " AND A.BNRI_CYU_CD = '" . $bnri_cyu_cd . "'" ;
			}
			
			if ($syaryo_cd != ''){
				$sql .= " AND A.SYARYO_BANGO = '" . $syaryo_cd . "'" ;
			}
			
			if ($area_cd != ''){
				$sql .= " AND A.CHIKU_AREA = '" . $area_cd . "'" ;
			}
			
			if ($more == 'on'){
				$sql .= " AND B.DT_SYUGYO_ED IS NULL " ;
			} else {
				$sql .= " AND B.DT_SYUGYO_ED IS NOT NULL " ;
			}
			
			if ($jikan_from != '' && $jikan_to != '' ){
				
				$jikan_from = substr($jikan_from, 0, 2) . ":" . substr($jikan_from, 2, 2);
				$jikan_to = substr($jikan_to, 0, 2) . ":" . substr($jikan_to, 2, 2);
				
				$sql .= " AND A.DT_KOTEI_ST >= ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$jikan_from.":00' )" ;
				$sql .= " AND A.DT_KOTEI_ED <= ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$jikan_to.":00' )" ;
			}
			
			if ($kyori_from != '' && $kyori_to != '' ){
				
				$sql .= " AND A.KAISI_KYORI >= ".$kyori_from;
				$sql .= " AND A.SYURYO_KYORI <= ".$kyori_to;
			}
			
			$sql .= " GROUP BY";
			$sql .= "    A.NINUSI_CD";
			$sql .= "   ,A.SOSIKI_CD";
			$sql .= "   ,A.YMD_SYUGYO";
			$sql .= "   ,A.SYARYO_BANGO";
			$sql .= "   ,A.BNRI_DAI_CD";
			$sql .= "   ,A.BNRI_CYU_CD";
			$sql .= "   ,A.CHIKU_AREA";
			$sql .= "   ,A.STAFF_CD";
			$sql .= " ORDER BY";
			$sql .= "    A.NINUSI_CD";
			$sql .= "   ,A.SOSIKI_CD";
			$sql .= "   ,A.YMD_SYUGYO";
			$sql .= "   ,A.SYARYO_BANGO";
			$sql .= "   ,A.BNRI_DAI_CD";
			$sql .= "   ,A.BNRI_CYU_CD";
			$sql .= "   ,A.CHIKU_AREA";
			$sql .= "   ,A.STAFF_CD";
			$sql .= ";";


			$this->queryWithPDOLog($stmt,$pdo,$sql,"工程データ 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$tmpText = "";
				$tmpText .= $result['NINUSI_CD'] . ',';
				$tmpText .= $result['NINUSI_NM'] . ',';
				$tmpText .= $result['SOSIKI_CD'] . ',';
				$tmpText .= $result['SOSIKI_NM'] . ',';
				$tmpText .= (new Datetime($result['YMD_SYUGYO']))->format('Y年n月j日') . ',';
				$tmpText .= $result['SYARYO_NM'] . ',';
				$tmpText .= $result['BNRI_CYU_NM'] . ',';
				$tmpText .= $result['AREA_NM'] . ',';
				$tmpText .= $result['STAFF_CD'] . ',';
				$tmpText .= $result['STAFF_NM'] . ',';
				$tmpText .= $result['SEX'] . ',';
				$tmpText .= $result['KAISYA_NM'] . ',';
				$tmpText .= $result['KEIYAKU_NM'] . ',';
				$tmpText .= $this->MCommon->changeDateTime($result['YMD_SYUGYO'],$result['DT_SYUGYO_ST']) . ',';
				$tmpText .= $this->MCommon->changeDateTime($result['YMD_SYUGYO'],$result['DT_SYUGYO_ED']) . ',';
				
				foreach ($komokuList as $komoku) {
					
					$diff_kotei = '';
					if(!empty($result[$komoku['MEI_CD'] . "_MIN_KOTEI_ST"]) && !empty($result[$komoku['MEI_CD'] . "_MAX_KOTEI_ED"])) {
						$d1 = new Datetime((new Datetime($result[$komoku['MEI_CD'] . "_MIN_KOTEI_ST"]))->format('Y-m-d H:i:00'));
						$d2 = new Datetime((new Datetime($result[$komoku['MEI_CD'] . "_MAX_KOTEI_ED"]))->format('Y-m-d H:i:00'));
						
						$diff = $d2->diff($d1);
						$diff_kotei = $diff->format('%H:%I');
					}
					


                    if(preg_match('/^[0-1]{7}$/',$komoku['VAL_FREE_STR_1']) ) {

                        if (substr($komoku['VAL_FREE_STR_1'],0,1) == "1") {
                            $tmpText .= $this->MCommon->changeDateTime($result['YMD_SYUGYO'],$result[$komoku['MEI_CD'] . "_MIN_KOTEI_ST"]) . ',';
                        }

                        if (substr($komoku['VAL_FREE_STR_1'],1,1) == "1") {
                            $tmpText .= $this->MCommon->changeDateTime($result['YMD_SYUGYO'],$result[$komoku['MEI_CD'] . "_MAX_KOTEI_ED"]) . ',';
                        }

                        if (substr($komoku['VAL_FREE_STR_1'],2,1) == "1") {
                            $tmpText .= $diff_kotei . ',';
                        }

                        if (substr($komoku['VAL_FREE_STR_1'],3,1) == "1") {
                            $tmpText .= $result[$komoku['MEI_CD'] . "_MIN_KAISI_KYORI"] . ',';
                        }

                        if (substr($komoku['VAL_FREE_STR_1'],4,1) == "1") {
                            $tmpText .= $result[$komoku['MEI_CD'] . "_MAX_SYURYO_KYORI"] . ',';
                        }

                        if (substr($komoku['VAL_FREE_STR_1'],5,1) == "1") {
                            $tmpText .= $result[$komoku['MEI_CD'] . "_SUM_KOTEI_M"] . ',';
                        }

                        if (substr($komoku['VAL_FREE_STR_1'],6,1) == "1") {
                            $tmpText .= $result[$komoku['MEI_CD'] . "_SUM_KOTEI_M"] . ',';
                        }

                    } else {
                        $tmpText .= $this->MCommon->changeDateTime($result['YMD_SYUGYO'],$result[$komoku['MEI_CD'] . "_MIN_KOTEI_ST"]) . ',';
                        $tmpText .= $this->MCommon->changeDateTime($result['YMD_SYUGYO'],$result[$komoku['MEI_CD'] . "_MAX_KOTEI_ED"]) . ',';
                        $tmpText .= $diff_kotei . ',';
                        $tmpText .= $result[$komoku['MEI_CD'] . "_MIN_KAISI_KYORI"] . ',';
                        $tmpText .= $result[$komoku['MEI_CD'] . "_MAX_SYURYO_KYORI"] . ',';
                        $tmpText .= $result[$komoku['MEI_CD'] . "_SUM_KOTEI_M"] . ',';
                        $tmpText .= $result[$komoku['MEI_CD'] . "_SUM_KYORI"] . ',';
                    }
				}
				
				$text[] = $tmpText;

			}

			$pdo = null;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WEX100", $e->getMessage());
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

}

?>
