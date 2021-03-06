<?php
/**
 * HRD030Model
 *
 * ABCデータベース参照
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MMeisyo', 'Model');
App::uses('MCommon', 'Model');

class HRD030Model extends DCMSAppModel{
	
	public $name = 'HRD030Model';
	public $useTable = false;
	
	public $errors = array();
	
	/**
	 * コンストラクタ
	 * @access   public
	 * @param    荷主コード
	 * @param    組織コード
	 * @param integer|string|array $id Set this ID for this model on startup, can also be an array of options, see above.
	 * @param string $table Name of database table to use.
	 * @param string $ds DataSource connection name.
	*/
	function __construct($ninusi_cd = '0000000000', $sosiki_cd = '0000000000', $kino_id = '', $id = false, $table = null, $ds = null) {
		parent::__construct($ninusi_cd, $sosiki_cd,$kino_id, $id, $table, $ds);
		 
		$this->MMessage = new MMessage($ninusi_cd, $sosiki_cd, $kino_id);
		$this->MMeisyo = new MMeisyo($ninusi_cd, $sosiki_cd, $kino_id);
		//共通関数モデル
		$this->MCommon = new MCommon($ninusi_cd,$sosiki_cd,$kino_id);
		
	}
	
	public function getJinzaiCsvData($staff_cd, $haken_kaisya_cd, $staff_nm, $del_flg) {
		
		$pdo = null;
		
		try{
		
			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$common = $this->MCommon;
			$fields = '*';
			 
			// 発行するSQLを設定
			$sql  = "SELECT M1.STAFF_CD";
			$sql .= "     ,M1.STAFF_NM";
			$sql .= "	  ,M3.KAISYA_NM ";
			$sql .= " 	  ,M2.MEI_1 AS YAKUSYOKU";
			$sql .= " 	  ,M5.MEI_1 AS PICK";
			$sql .= " 	  ,M4.MEI_1 AS KEIYAKU";
			$sql .= " 	  ,M1.RANK";
			$sql .= " 	  ,M1.KIN_SYOTEI";
			$sql .= " 	  ,M1.KIN_JIKANGAI";
			$sql .= " 	  ,M1.KIN_SNY";
			$sql .= " 	  ,M1.KIN_KST";
			$sql .= " 	  ,M1.KIN_H_KST";
			$sql .= " 	  ,M1.KIN_H_KST_SNY";
			$sql .= " 	  ,M1.YMD_SYUGYO_ST";
			$sql .= " 	  ,M1.YMD_SYUGYO_ED";
			$sql .= " 	  ,M1.SYUKIN_NISU_MAX";
			$sql .= " 	  ,M1.SYUKIN_TIME_MAX";
			$sql .= " 	  ,M1.CHINGIN_MAX";
			$sql .= "    FROM M_STAFF_KIHON M1 LEFT JOIN M_MEISYO M2 ON (M1.KBN_PST=M2.MEI_CD AND M2.MEI_KBN='17') ";
			$sql .= "    LEFT JOIN M_HAKEN_KAISYA M3 ON (M1.HAKEN_KAISYA_CD=M3.KAISYA_CD) ";
			$sql .= "	 LEFT JOIN M_MEISYO M4 ON (M1.KBN_KEIYAKU=M4.MEI_CD AND M4.MEI_KBN='30')     ";
			$sql .= " 	 LEFT JOIN M_MEISYO M5 ON (M1.KBN_PICK=M5.MEI_CD AND M5.MEI_KBN='19')    ";
			$sql .= " WHERE ";
		    $sql .= "     1 = 1";
			if ($staff_cd != "") {
				$sql .= " AND M1.STAFF_CD LIKE ". $common->encloseString($staff_cd);
			}
			if ($haken_kaisya_cd != "") {
				$sql .= " AND M1.HAKEN_KAISYA_CD = ". $common->encloseString($haken_kaisya_cd);
			}
			if ($staff_nm != "") {
				$sql .= " AND M1.STAFF_NM LIKE ". $common->encloseString('%' . $staff_nm . '%');
			}
			if ($del_flg != "") {
				$sql .= " AND M1.DEL_FLG = ". $common->encloseString($del_flg);
			}
			$sql .= " ORDER BY ";
			$sql .= "    STAFF_CD ASC ";
			
			$this->queryWithPDOLog($stmt, $pdo, $sql,"人材基本情報CSV　取得");
							
			$results = array();
			while ($entity = $stmt->fetch(PDO::FETCH_ASSOC)) {
				array_push($results, $entity);
			}
				
			$pdo = null;

			return $results;
			
		}catch (Exception $e) {
		
			$this->printLog("fatal", "例外発生", "HRD030", $e->getMessage());
			$message = $this->MMessage->getOneMessage('CMNE000107');
			$this->errors['Check'][] =   $message;
		
			$pdo = null;
		}
		
		return array();
	}
	
	public function getSyugyoCsvData($startYmd, $endYmd, $staff_cd, $haken_kaisya_cd, $staff_nm,$del_flg) {
		
		$pdo = null;
		
		try{
		
			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$common = $this->MCommon;
			$fields = '*';
	
			//休憩用コード取得
			$array = $this->MMeisyo->getMeisyo("35");
			foreach ( $array as $key=>$val ) {
				// 1つしか取れない前提
				$restCD = $key;
			}
			 
			// 発行するSQLを設定
			$sql  = "			SELECT T1.YMD_SYUGYO  AS A1";
			$sql .= "			,DATE_FORMAT(NOW(),'%Y%m%d')  AS A2";
			$sql .= "			,M1.STAFF_CD  AS A3";
			$sql .= "			,M1.STAFF_NM  AS A4";
			$sql .= "			,M3.KAISYA_NM  AS A5";
			$sql .= "			,M1.KIN_SYOTEI  AS A6";
			$sql .= "			,M1.KIN_JIKANGAI  AS A7";
			$sql .= "			,M1.KIN_SNY  AS A8";
			$sql .= "			,M1.KIN_KST  AS A9";
			$sql .= "			,M1.KIN_H_KST  AS A10";
			$sql .= "			,M1.KIN_H_KST_SNY  AS A11";
			$sql .= "			,T1.DT_SYUGYO_ST  AS A12";
			$sql .= "			,T1.DT_SYUGYO_ED  AS A13";
			$sql .= "			,HOUR(TIMEDIFF(T1.DT_SYUGYO_ED,T1.DT_SYUGYO_ST)) + TRUNCATE(MINUTE(TIMEDIFF(T1.DT_SYUGYO_ED,T1.DT_SYUGYO_ST))/60,2) AS A14";
			$sql .= "			,HOUR(T1.DT_SYUGYO_ST) +";
			$sql .= "			CASE WHEN MINUTE(T1.DT_SYUGYO_ST) >= 1 AND MINUTE(T1.DT_SYUGYO_ST) <= 15 THEN 0.25";
			$sql .= "			WHEN MINUTE(T1.DT_SYUGYO_ST) >= 16 AND MINUTE(T1.DT_SYUGYO_ST) <= 30 THEN 0.5";
			$sql .= "			WHEN MINUTE(T1.DT_SYUGYO_ST) >= 31 AND MINUTE(T1.DT_SYUGYO_ST) <= 45 THEN 0.75";
			$sql .= "			WHEN MINUTE(T1.DT_SYUGYO_ST) >= 46 AND MINUTE(T1.DT_SYUGYO_ST) <= 59 THEN 1";
			$sql .= "			ELSE 0";
			$sql .= "			END AS A15";
			$sql .= "			,DATEDIFF(T1.DT_SYUGYO_ED,T1.DT_SYUGYO_ST) * 24 + HOUR(T1.DT_SYUGYO_ED) +";
			$sql .= "			CASE WHEN MINUTE(T1.DT_SYUGYO_ED) >= 0 AND MINUTE(T1.DT_SYUGYO_ED) <= 14 THEN 0";
			$sql .= "			WHEN MINUTE(T1.DT_SYUGYO_ED) >= 15 AND MINUTE(T1.DT_SYUGYO_ED) <= 29 THEN 0.25";
			$sql .= "			WHEN MINUTE(T1.DT_SYUGYO_ED) >= 30 AND MINUTE(T1.DT_SYUGYO_ED) <= 44 THEN 0.5";
			$sql .= "			WHEN MINUTE(T1.DT_SYUGYO_ED) >= 45 AND MINUTE(T1.DT_SYUGYO_ED) <= 59 THEN 0.75";
			$sql .= "			END AS A16";
			$sql .= "			,(YEAR(T1.DT_SYUGYO_ED) * 365 * 24 + MONTH(T1.DT_SYUGYO_ED) * 31 * 24 + DAY(T1.DT_SYUGYO_ED) * 24 + HOUR(T1.DT_SYUGYO_ED) +";
			$sql .= "			CASE WHEN MINUTE(T1.DT_SYUGYO_ED) >= 0 AND MINUTE(T1.DT_SYUGYO_ED) <= 14 THEN 0";
			$sql .= "			WHEN MINUTE(T1.DT_SYUGYO_ED) >= 15 AND MINUTE(T1.DT_SYUGYO_ED) <= 29 THEN 0.25";
			$sql .= "			WHEN MINUTE(T1.DT_SYUGYO_ED) >= 30 AND MINUTE(T1.DT_SYUGYO_ED) <= 44 THEN 0.5";
			$sql .= "			WHEN MINUTE(T1.DT_SYUGYO_ED) >= 45 AND MINUTE(T1.DT_SYUGYO_ED) <= 59 THEN 0.75";
			$sql .= "			END)";
			$sql .= "			-(YEAR(T1.DT_SYUGYO_ST) * 365 * 24 + MONTH(T1.DT_SYUGYO_ST) * 31 * 24 + DAY(T1.DT_SYUGYO_ST) * 24 + HOUR(T1.DT_SYUGYO_ST) +";
			$sql .= "			CASE WHEN MINUTE(T1.DT_SYUGYO_ST) >= 1 AND MINUTE(T1.DT_SYUGYO_ST) <= 15 THEN 0.25";
			$sql .= "			WHEN MINUTE(T1.DT_SYUGYO_ST) >= 16 AND MINUTE(T1.DT_SYUGYO_ST) <= 30 THEN 0.5";
			$sql .= "			WHEN MINUTE(T1.DT_SYUGYO_ST) >= 31 AND MINUTE(T1.DT_SYUGYO_ST) <= 45 THEN 0.75";
			$sql .= "			WHEN MINUTE(T1.DT_SYUGYO_ST) >= 46 AND MINUTE(T1.DT_SYUGYO_ST) <= 59 THEN 1";
			$sql .= "			ELSE 0";
			$sql .= "			END) AS A17";
			$sql .= "			,T2.DT_KOTEI_ST  AS A18";
			$sql .= "			,T2.DT_KOTEI_ED  AS A19";
			$sql .= "			,IFNULL(HOUR(TIMEDIFF(T2.DT_KOTEI_ED,T2.DT_KOTEI_ST)),0) +";
			$sql .= "			IFNULL(TRUNCATE(MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,T2.DT_KOTEI_ST))/60,2),0) AS A20";
			$sql .= "			,IFNULL(HOUR(TIMEDIFF(T2.DT_KOTEI_ED,T2.DT_KOTEI_ST)),0) +";
			$sql .= "			CASE WHEN MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,T2.DT_KOTEI_ST)) >= 1 AND MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,T2.DT_KOTEI_ST)) <= 15 THEN 0.25";
			$sql .= "			WHEN MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,T2.DT_KOTEI_ST)) >= 16 AND MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,T2.DT_KOTEI_ST)) <= 30 THEN 0.5";
			$sql .= "			WHEN MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,T2.DT_KOTEI_ST)) >= 31 AND MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,T2.DT_KOTEI_ST)) <= 45 THEN 0.75";
			$sql .= "			WHEN MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,T2.DT_KOTEI_ST)) >= 46 AND MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,T2.DT_KOTEI_ST)) <= 59 THEN 1";
			$sql .= "			ELSE 0";
			$sql .= "			END AS A21";
			$sql .= "			,IFNULL(HOUR(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)),0) +";
			$sql .= "			TRUNCATE(MINUTE(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST))/60,2) AS A22";
			$sql .= "			,IFNULL(HOUR(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME))),0) +";
			$sql .= "			TRUNCATE(MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME)))/60,2) AS A23";
			$sql .= "			,IFNULL(HOUR(TIMEDIFF(T1.DT_SYUGYO_ED,CAST(CONCAT(YEAR(T1.DT_SYUGYO_ST),'-',MONTH(T1.DT_SYUGYO_ST),'-',DAY(T1.DT_SYUGYO_ST),' ',22,':',0,':',0) AS DATETIME))),0) +";
			$sql .= "			TRUNCATE(MINUTE(TIMEDIFF(T1.DT_SYUGYO_ED,CAST(CONCAT(YEAR(T1.DT_SYUGYO_ST),'-',MONTH(T1.DT_SYUGYO_ST),'-',DAY(T1.DT_SYUGYO_ST),' ',22,':',0,':',0) AS DATETIME)))/60,2) AS A24";
			$sql .= "			,DATEDIFF(T2.DT_KOTEI_ST,T2.DT_KOTEI_ED) * 24 + HOUR(T2.DT_KOTEI_ST)  AS A25";
			$sql .= "			,DATEDIFF(T2.DT_KOTEI_ST,T2.DT_KOTEI_ED) * 24 + HOUR(T2.DT_KOTEI_ED)  AS A26";
			$sql .= "			,IFNULL(HOUR(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)),0) +";
			$sql .= "			CASE WHEN MINUTE(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)) >= 1 AND MINUTE(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)) <= 15 THEN 0.25";
			$sql .= "			WHEN MINUTE(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)) >= 16 AND MINUTE(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)) <= 30 THEN 0.5";
			$sql .= "			WHEN MINUTE(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)) >= 31 AND MINUTE(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)) <= 45 THEN 0.75";
			$sql .= "			WHEN MINUTE(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)) >= 46 AND MINUTE(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)) <= 59 THEN 1";
			$sql .= "			ELSE 0";
			$sql .= "			END AS A27";
			$sql .= "			,IFNULL(HOUR(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME))),0) +";
			$sql .= "			CASE WHEN MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME))) >= 1 AND MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME))) <= 15 THEN 0.25";
			$sql .= "			WHEN MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME))) >= 16 AND MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME))) <= 30 THEN 0.5";
			$sql .= "			WHEN MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME))) >= 31 AND MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME))) <= 45 THEN 0.75";
			$sql .= "			WHEN MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME))) >= 46 AND MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ST),'-',MONTH(T2.DT_KOTEI_ST),'-',DAY(T2.DT_KOTEI_ST),' ',22,':',0,':',0) AS DATETIME))) <= 59 THEN 1";
			$sql .= "			ELSE 0";
			$sql .= "			END AS A28";
			$sql .= "			,DATEDIFF(T1.DT_SYUGYO_ED,T1.DT_SYUGYO_ST) * 24 + HOUR(T1.DT_SYUGYO_ED) AS A29";
			$sql .= "			,IFNULL(HOUR(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)),0) +";
			$sql .= "			TRUNCATE(MINUTE(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST))/60,2) AS A30";
			$sql .= "			,IFNULL(HOUR(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME))),0) +";
			$sql .= "			TRUNCATE(MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME)))/60,2) AS A31";
			$sql .= "			,IFNULL(HOUR(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)),0) +";
			$sql .= "			CASE WHEN MINUTE(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)) >= 1 AND MINUTE(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)) <= 15 THEN 0.25";
			$sql .= "			WHEN MINUTE(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)) >= 16 AND MINUTE(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)) <= 30 THEN 0.5";
			$sql .= "			WHEN MINUTE(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)) >= 31 AND MINUTE(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)) <= 45 THEN 0.75";
			$sql .= "			WHEN MINUTE(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)) >= 46 AND MINUTE(TIMEDIFF(CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME),T2.DT_KOTEI_ST)) <= 59 THEN 1";
			$sql .= "			ELSE 0";
			$sql .= "			END AS A32";
			$sql .= "			,IFNULL(HOUR(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME))),0) +";
			$sql .= "			CASE WHEN MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME))) >= 1 AND MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME))) <= 15 THEN 0.25";
			$sql .= "			WHEN MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME))) >= 16 AND MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME))) <= 30 THEN 0.5";
			$sql .= "			WHEN MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME))) >= 31 AND MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME))) <= 45 THEN 0.75";
			$sql .= "			WHEN MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME))) >= 46 AND MINUTE(TIMEDIFF(T2.DT_KOTEI_ED,CAST(CONCAT(YEAR(T2.DT_KOTEI_ED),'-',MONTH(T2.DT_KOTEI_ED),'-',DAY(T2.DT_KOTEI_ED),' ',5,':',0,':',0) AS DATETIME))) <= 59 THEN 1";
			$sql .= "			ELSE 0";
			$sql .= "			END AS A33";
			$sql .= "			FROM (T_KOTEI_H T1 LEFT JOIN (";
			$sql .= "			SELECT DD.NINUSI_CD,DD.SOSIKI_CD,DD.STAFF_CD,DD.YMD_SYUGYO,DD.DT_KOTEI_ST,DD.DT_KOTEI_ED";
			$sql .= "			FROM T_KOTEI_D DD";
			$sql .= "			WHERE DD.BNRI_DAI_CD='" . $restCD  . "'";
			$sql .= "					) T2 ON (T1.NINUSI_CD=T2.NINUSI_CD AND T1.SOSIKI_CD=T2.SOSIKI_CD AND T1.STAFF_CD=T2.STAFF_CD AND T1.YMD_SYUGYO=T2.YMD_SYUGYO))";
			$sql .= "					INNER JOIN M_STAFF_KIHON M1 ON (T1.STAFF_CD=M1.STAFF_CD)";
			$sql .= "					INNER JOIN M_HAKEN_KAISYA M3 ON (M1.HAKEN_KAISYA_CD=M3.KAISYA_CD)";
			$sql .= " WHERE ";
		    $sql .= "     1 = 1";
			if ($startYmd != "") {
				$sql .= " AND T1.YMD_SYUGYO >= DATE(". $common->encloseString($startYmd) . ")";
			}
			if ($endYmd != "") {
				$sql .= " AND T1.YMD_SYUGYO <= DATE(". $common->encloseString($endYmd) . ")";
							}
			if ($staff_cd != "") {
				$sql .= " AND T1.STAFF_CD = ". $common->encloseString($staff_cd);;
			}
			if ($haken_kaisya_cd != "") {
				$sql .= " AND M1.HAKEN_KAISYA_CD = ". $common->encloseString($haken_kaisya_cd);
			}
			if ($staff_nm != "") {
				$sql .= " AND M1.STAFF_CD LIKE ". $common->encloseString('%' . $staff_nm . '%');
			}
			if ($del_flg != "") {
				$sql .= " AND M1.DEL_FLG = ". $common->encloseString($del_flg);
			}
			$sql .= " ORDER BY ";
			$sql .= "    T1.YMD_SYUGYO,M1.STAFF_CD,T2.DT_KOTEI_ST ";
			
			$this->printLog("fatal", "例外発生", "WEX110", $sql);
			$this->queryWithPDOLog($stmt, $pdo, $sql,"就業実績情報CSV　取得");

			$results = array();
			$col1 = "";	$col2 = "";	$col3 = "";	$col4 = "";	$col5 = "";
			$col6 = "";	$col7 = "";	$col8 = "";	$col9 = "";	$col10= "";
			$col11= "";	$col12= "";	$col13= "";	$col14= "";	$col15= "";
			$col16= "";	$col17= "";	$col18= "";	$col19= "";	$col20= "";
			$col21= "";	$col22= "";	$col23= "";	$col24= "";	$col25= "";
			$col26= "";	$col27= "";	$col28= "";	$col29= "";	$col30= "";
			$col31= "";	$col32= "";	$col33= "";	$col34= "";	$col35= "";
			$col36= "";	$col37= "";	$col38= "";	$col39= "";	$col40= "";
			$col41= "";	$col42= "";	$col43= "";	$col44= "";	$col45= "";
			$col46= "";	$col47= "";	$col48= "";	$col49= "";	$col50= "";
			$col51= "";	$col52= "";	$col53= "";	$col54= "";	$col55= "";
			$col56= "";	$col57= "";	$col58= "";	$col59= "";	$col60= "";
			$col61= "";	$col62= "";	$col63= "";	$col64= "";	$col65= "";
			$col66= "";	$col67= "";	$col68= "";	$col69= "";	$col70= "";
			$col71= "";	$col72= "";
			$shinya_time = 0;
			$shinya_syugyo = 0;
			$columnCount = 1;
			$linecnt = 0;
			while ($entity = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$linecnt++;
				if ($linecnt == 1) {
					$col1 = $entity["A1"];
					$col3 = $entity["A3"];
				}

				if ($col1 == $entity["A1"] && $col3 == $entity["A3"]) {
					if ($columnCount==1){
						$col1 = $entity["A1"];
						$col2 = $entity["A2"];
						$col3 = $entity["A3"];
						$col4 = $entity["A4"];
						$col5 = $entity["A5"];
						$col6 = $entity["A6"];
						$col7 = $entity["A7"];
						$col8 = $entity["A8"];
						$col9 = $entity["A9"];
						$col10= $entity["A10"];
						$col11 = $entity["A11"];
						$col12 = $entity["A12"];
						$col13 = $entity["A13"];
						$col14 = $entity["A14"];
						$col15 = $entity["A15"];
						$col16 = $entity["A16"];
						$col17 = $entity["A17"];
						
						$col18 = $entity["A18"];
						$col19 = $entity["A19"];
						if (is_numeric($entity["A20"])) {
							$col20 = $entity["A20"];
						} else {
							$col20 = 0;
						}
						$rest_st =  $entity["A25"];
						$rest_ed =  $entity["A26"];
						if (($rest_st < 22 and $rest_st >= 5) and  ($rest_ed < 22 and $rest_ed >= 5)) {
							if (is_numeric($entity["A20"])) {
								$col21 = $entity["A20"];
								$col23 = $entity["A21"];
							} else {
								$col21 = 0;
								$col23 = 0;
							}
							$col22 = 0;
							$col24 = 0;
						} elseif (($rest_st >= 22 or $rest_st < 5) and ($rest_ed >= 22 or $rest_ed < 5)) {
							if (is_numeric($entity["A20"])) {
								$col22 = $entity["A20"];
								$col24 = $entity["A21"];
							} else {
								$col22 = 0;
								$col24 = 0;
							}
							$col21 = 0;
							$col23 = 0;
						} elseif (($rest_st < 22 && $rest_st >= 5) and ($rest_ed >= 22 or $rest_ed < 5)) {
							if (is_numeric($entity["A30"])) {
								$col21 = $entity["A30"];
								$col23 = $entity["A32"];
								$col22 = $entity["A31"];
								$col24 = $entity["A33"];
							} else {
								$col21 = 0;
								$col22 = 0;
								$col23 = 0;
								$col24 = 0;
							}
						}
						if ($col21 < 0) {
							$col21 = 0;
						}						
						if ($col22 < 0) {
							$col22 = 0;
						}						
						if ($col23 < 0) {
							$col23 = 0;
						}						
						if ($col24 < 0) {
							$col24 = 0;
						}						
						$columnCount++;
						
					} elseif ($columnCount == 2) {
						$col25 = $entity["A18"];
						$col26 = $entity["A19"];
						if (is_numeric($entity["A20"])) {
							$col27 = $entity["A20"];
						} else {
							$col27 = 0;
						}
						$rest_st =  $entity["A25"];
						$rest_ed =  $entity["A26"];
						if (($rest_st < 22 and $rest_st >= 5) and  ($rest_ed < 22 and $rest_ed >= 5)) {
							if (is_numeric($entity["A20"])) {
								$col28 = $entity["A20"];
								$col30 = $entity["A21"];
							} else {
								$col28 = 0;
								$col30 = 0;
							}
							$col29 = 0;
							$col31 = 0;
						} elseif (($rest_st >= 22 or $rest_st < 5) and ($rest_ed >= 22 or $rest_ed < 5)) {
							if (is_numeric($entity["A20"])) {
								$col29 = $entity["A20"];
								$col31 = $entity["A21"];
							} else {
								$col29 = 0;
								$col31 = 0;
							}
							$col28 = 0;
							$col30 = 0;
						} elseif (($rest_st < 22 && $rest_st >= 5) and ($rest_ed >= 22 or $rest_ed < 5)) {
							if (is_numeric($entity["A22"])) {
								$col28 = $entity["A22"];
								$col30 = $entity["A27"];
								$col29 = $entity["A23"];
								$col31 = $entity["A28"];
							} else {
								$col28 = 0;
								$col30 = 0;
								$col29 = 0;
								$col31 = 0;
							}
						} elseif (($rest_st >= 22 or $rest_st < 5) and ($rest_ed < 22 and $rest_ed >= 5)) {
							if (is_numeric($entity["A30"])) {
								$col28 = $entity["A30"];
								$col30 = $entity["A32"];
								$col29 = $entity["A31"];
								$col31 = $entity["A33"];
							} else {
								$col28 = 0;
								$col30 = 0;
								$col29 = 0;
								$col31 = 0;
							}
						}
						if ($col28 < 0) {
							$col28 = 0;
						}						
						if ($col29 < 0) {
							$col29 = 0;
						}						
						if ($col30 < 0) {
							$col30 = 0;
						}						
						if ($col31 < 0) {
							$col31 = 0;
						}						
						$columnCount++;
					} elseif ($columnCount == 3) {
						$col32 = $entity["A18"];
						$col33 = $entity["A19"];
						if (is_numeric($entity["A20"])) {
							$col34 = $entity["A20"];
						} else {
							$col34 = 0;
						}
						$rest_st =  $entity["A25"];
						$rest_ed =  $entity["A26"];
						if (($rest_st < 22 and $rest_st >= 5) and  ($rest_ed < 22 and $rest_ed >= 5)) {
							if (is_numeric($entity["A20"])) {
								$col35 = $entity["A20"];
								$col37 = $entity["A21"];
							} else {
								$col35 = 0;
								$col37 = 0;
							}
							$col36 = 0;
							$col38 = 0;
						} elseif (($rest_st >= 22 or $rest_st < 5) and ($rest_ed >= 22 or $rest_ed < 5)) {
							if (is_numeric($entity["A20"])) {
								$col36 = $entity["A20"];
								$col38 = $entity["A21"];
							} else {
								$col36 = 0;
								$col38 = 0;
							}
							$col35 = 0;
							$col37 = 0;
						} elseif (($rest_st < 22 && $rest_st >= 5) and ($rest_ed >= 22 or $rest_ed < 5)) {
							if (is_numeric($entity["A21"])) {
								$col35 = $entity["A22"];
								$col37 = $entity["A27"];
								$col36 = $entity["A23"];
								$col38 = $entity["A28"];
							} else {
								$col35 = 0;
								$col36 = 0;
								$col37 = 0;
								$col38 = 0;
							}
						} elseif (($rest_st >= 22 or $rest_st < 5) and ($rest_ed < 22 and $rest_ed >= 5)) {
							if (is_numeric($entity["A30"])) {
								$col35 = $entity["A30"];
								$col37 = $entity["A32"];
								$col36 = $entity["A31"];
								$col38 = $entity["A33"];
							} else {
								$col35 = 0;
								$col36 = 0;
								$col37 = 0;
								$col38 = 0;
							}
						}
						if ($col35 < 0) {
							$col35 = 0;
						}						
						if ($col36 < 0) {
							$col36 = 0;
						}						
						if ($col37 < 0) {
							$col37 = 0;
						}						
						if ($col38 < 0) {
							$col38 = 0;
						}						
						$columnCount++;
					} elseif ($columnCount == 4) {
						$col39 = $entity["A18"];
						$col40 = $entity["A19"];
						if (is_numeric($entity["A20"])) {
							$col41 = $entity["A20"];
						} else {
							$col41 = 0;
						}
						$rest_st =  $entity["A25"];
						$rest_ed =  $entity["A26"];
						if (($rest_st < 22 and $rest_st >= 5) and  ($rest_ed < 22 and $rest_ed >= 5)) {
							if (is_numeric($entity["A20"])) {
								$col42 = $entity["A20"];
								$col44 = $entity["A21"];
							} else {
								$col42 = 0;
								$col44 = 0;
							}
							$col43 = 0;
							$col45 = 0;
						} elseif (($rest_st >= 22 or $rest_st < 5) and ($rest_ed >= 22 or $rest_ed < 5)) {
							if (is_numeric($entity["A20"])) {
								$col43 = $entity["A20"];
								$col45 = $entity["A21"];
							} else {
								$col43 = 0;
								$col45 = 0;
							}
							$col42 = 0;
							$col44 = 0;
						} elseif (($rest_st < 22 && $rest_st >= 5) and ($rest_ed >= 22 or $rest_ed < 5)) {
							if (is_numeric($entity["A21"])) {
								$col42 = $entity["A22"];
								$col44 = $entity["A27"];
								$col43 = $entity["A23"];
								$col45 = $entity["A28"];
							} else {
								$col42 = 0;
								$col43 = 0;
								$col44 = 0;
								$col45 = 0;
							}
						} elseif (($rest_st >= 22 or $rest_st < 5) and ($rest_ed < 22 and $rest_ed >= 5)) {
							if (is_numeric($entity["A30"])) {
								$col42 = $entity["A30"];
								$col44 = $entity["A32"];
								$col43 = $entity["A31"];
								$col45 = $entity["A33"];
							} else {
								$col42 = 0;
								$col43 = 0;
								$col44 = 0;
								$col45 = 0;
							}
						}
						if ($col42 < 0) {
							$col42 = 0;
						}						
						if ($col43 < 0) {
							$col43 = 0;
						}						
						if ($col44 < 0) {
							$col44 = 0;
						}						
						if ($col45 < 0) {
							$col45 = 0;
						}						
						$columnCount++;
					} elseif ($columnCount == 5) {
						$col46 = $entity["A18"];
						$col47 = $entity["A19"];
						if (is_numeric($entity["A20"])) {
							$col48 = $entity["A20"];
						} else {
							$col48 = 0;
						}
						$rest_st =  $entity["A25"];
						$rest_ed =  $entity["A26"];
						if (($rest_st < 22 and $rest_st >= 5) and  ($rest_ed < 22 and $rest_ed >= 5)) {
							if (is_numeric($entity["A20"])) {
								$col49 = $entity["A20"];
								$col51 = $entity["A21"];
							} else {
								$col49 = 0;
								$col51 = 0;
							}
							$col50 = 0;
							$col52 = 0;
						} elseif (($rest_st >= 22 or $rest_st < 5) and ($rest_ed >= 22 or $rest_ed < 5)) {
							if (is_numeric($entity["A20"])) {
								$col50 = $entity["A20"];
								$col52 = $entity["A21"];
							} else {
								$col50 = 0;
								$col52 = 0;
							}
							$col49 = 0;
							$col51 = 0;
						} elseif (($rest_st < 22 && $rest_st >= 5) and ($rest_ed >= 22 or $rest_ed < 5)) {
							if (is_numeric($entity["A21"])) {
								$col49 = $entity["A22"];
								$col51 = $entity["A27"];
								$col50 = $entity["A23"];
								$col52 = $entity["A28"];
							} else {
								$col49 = 0;
								$col50 = 0;
								$col51 = 0;
								$col52 = 0;
							}
						} elseif (($rest_st >= 22 or $rest_st < 5) and ($rest_ed < 22 and $rest_ed >= 5)) {
							if (is_numeric($entity["A30"])) {
								$col49 = $entity["A30"];
								$col51 = $entity["A32"];
								$col50 = $entity["A31"];
								$col52 = $entity["A33"];
							} else {
								$col49 = 0;
								$col51 = 0;
								$col50 = 0;
								$col52 = 0;
							}
						}
						if ($col49 < 0) {
							$col49 = 0;
						}						
						if ($col50 < 0) {
							$col50 = 0;
						}						
						if ($col51 < 0) {
							$col51 = 0;
						}						
						if ($col52 < 0) {
							$col52 = 0;
						}						
						$columnCount++;
					}
					$shinya_time = $entity["A29"];
					$shinya_syugyo = $entity["A24"];
				} else {

					for ($i=$columnCount;$i<=5;$i++) {
						if ($i == 2) {
							$col25 = '';
							$col26 = '';
							$col27 = 0;
							$col28 = 0;
							$col29 = 0;
							$col30 = 0;
							$col31 = 0;
								
							$col32 = '';
							$col33 = '';
							$col34 = 0;
							$col35 = 0;
							$col36 = 0;
							$col37 = 0;
							$col38 = 0;
							
							$col39 = '';
							$col40 = '';
							$col41 = 0;
							$col42 = 0;
							$col43 = 0;
							$col44 = 0;
							$col45 = 0;
							
							$col46 = '';
							$col47 = '';
							$col48 = 0;
							$col49 = 0;
							$col50 = 0;
							$col51 = 0;
							$col52 = 0;
							break;
						}
						elseif ($i == 3) {
							$col32 = '';
							$col33 = '';
							$col34 = 0;
							$col35 = 0;
							$col36 = 0;
							$col37 = 0;
							$col38 = 0;
							
							$col39 = '';
							$col40 = '';
							$col41 = 0;
							$col42 = 0;
							$col43 = 0;
							$col44 = 0;
							$col45 = 0;
							
							$col46 = '';
							$col47 = '';
							$col48 = 0;
							$col49 = 0;
							$col50 = 0;
							$col51 = 0;
							$col52 = 0;
							break;
						}
						elseif ($i == 4) {
							$col39 = '';
							$col40 = '';
							$col41 = 0;
							$col42 = 0;
							$col43 = 0;
							$col44 = 0;
							$col45 = 0;
							
							$col46 = '';
							$col47 = '';
							$col48 = 0;
							$col49 = 0;
							$col50 = 0;
							$col51 = 0;
							$col52 = 0;
							break;
						} else {
							$col46 = '';
							$col47 = '';
							$col48 = 0;
							$col49 = 0;
							$col50 = 0;
							$col51 = 0;
							$col52 = 0;
							break;
						}
					}						
					
					$col53 = $col21+$col28+$col35+$col42+$col49;
					$col54 = $col22+$col29+$col36+$col43+$col50;
					$col55 = $col53+$col54;
					$work = 0;
					$answer = 0;
					$work = floatval($col53) - intval($col53);
					switch (true) {
						case $work > 0 && $work <= 0.25:
							$answer = 0.25;
							break;
						case $work > 0.25 && $work <= 0.5:
							$answer = 0.5;
							break;
						case $work > 0.5 && $work <= 0.75:
							$answer = 0.75;
							break;
						case $work > 0.75 && $work <= 0.99:
							$answer = 1;
							break;
						default:
							$answer = 0;
							break;
					}
					$col56 = intval($col53) + $answer;
					
					$work = 0;
					$answer = 0;
					$work = floatval($col54) - intval($col54);
					switch (true) {
						case $work > 0 && $work <= 0.25:
							$answer = 0.25;
							break;
						case $work > 0.25 && $work <= 0.5:
							$answer = 0.5;
							break;
						case $work > 0.5 && $work <= 0.75:
							$answer = 0.75;
							break;
						case $work > 0.75 && $work <= 0.99:
							$answer = 1;
							break;
						default:
							$answer = 0;
							break;
					}
					$col57 = intval($col54) + $answer;
					$col58 = $col56+$col57;
					
					$normal = $col17 - $col55;
					if ($normal < 0) {
						$col17 = 0;
						$col59 = 0;
						$col60 = 0;
						$col61 = 0;
					} elseif ($normal >= 8) {
						$col59 = $normal;
						$col60 = $normal-8;
						if ($shinya_time >= 22) {
							$col61 = $shinya_syugyo-$col54;
						} else {
							$col61 = 0;
						}
					} else {
						$col59 = $normal;
						$col60 = 0;
						if ($shinya_time >= 22) {
							$col61 = $shinya_syugyo-$col54;
						} else {
							$col61 = 0;
						}
					}
					if ($col61<0) {
						$col61 = 0;
					}
					
					$marume = $col17 - $col58;
					if ($marume < 0) {
						$col17 = 0;
						$col62 = 0;
						$col63 = 0;
						$col64 = 0;
					} elseif ($marume >= 8) {
						$col62 = $marume;
						$col63 = $marume-8;
						if ($shinya_time >= 22) {
							$col64 = $shinya_syugyo-$col57;
							$work = 0;
							$answer = 0;
							$work = floatval($col64) - intval($col64);
							switch (true) {
								case $work >= 0 && $work < 0.25:
									$answer = 0;
									break;
								case $work >= 0.25 && $work < 0.5:
									$answer = 0.25;
									break;
								case $work >= 0.5 && $work < 0.75:
									$answer = 0.5;
									break;
								case $work >= 0.75 && $work < 0.99:
									$answer = 0.75;
									break;
								default:
									$answer = 0;
									break;
							}
							$col64 = intval($col64) + $answer;
								
						} else {
							$col64 = 0;
						}
					} else {
						$col62 = $marume;
						$col63 = 0;
						if ($shinya_time >= 22) {
							$col64 = $shinya_syugyo-$col57;
						} else {
							$col64 = 0;
						}
					}
					if ($col64<0) {
						$col64 = 0;
					}
					
					$col65 = $col59*$col6;
					$col66 = $col60*$col7;
					$col67 = $col61*$col8;
					$col68 = $col65+$col66+$col67;
					$col69 = $col62*$col6;
					$col70 = $col63*$col7;
					$col71 = $col64*$col8;
					$col72 = $col69+$col70+$col71;

					$outarray = array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col10,$col11,$col12,$col13,$col14,$col15,$col16,$col17,$col18,$col19,$col20,$col21,$col22,$col23,$col24,$col25,$col26,$col27,$col28,$col29,$col30,$col31,$col32,$col33,$col34,$col35,$col36,$col37,$col38,$col39,$col40,$col41,$col42,$col43,$col44,$col45,$col46,$col47,$col48,$col49,$col50,$col51,$col52,$col53,$col54,$col55,$col56,$col57,$col58,$col59,$col60,$col61,$col62,$col63,$col64,$col65,$col66,$col67,$col68,$col69,$col70,$col71,$col72);

					$col1 = $entity["A1"];
					$col2 = $entity["A2"];
					$col3 = $entity["A3"];
					$col4 = $entity["A4"];
					$col5 = $entity["A5"];
					$col6 = $entity["A6"];
					$col7 = $entity["A7"];
					$col8 = $entity["A8"];
					$col9 = $entity["A9"];
					$col10= $entity["A10"];
					$col11 = $entity["A11"];
					$col12 = $entity["A12"];
					$col13 = $entity["A13"];
					$col14 = $entity["A14"];
					$col15 = $entity["A15"];
					$col16 = $entity["A16"];
					$col17 = $entity["A17"];
					
					$col18 = $entity["A18"];
					$col19 = $entity["A19"];
					if (is_numeric($entity["A20"])) {
						$col20 = $entity["A20"];
					} else {
						$col20 = 0;
					}
					$rest_st =  $entity["A25"];
					$rest_ed =  $entity["A26"];
					if (($rest_st < 22 and $rest_st >= 5) and  ($rest_ed < 22 and $rest_ed >= 5)) {
						if (is_numeric($entity["A20"])) {
							$col21 = $entity["A20"];
							$col23 = $entity["A21"];
						} else {
							$col21 = 0;
							$col23 = 0;
						}
						$col22 = 0;
						$col24 = 0;
					} elseif (($rest_st >= 22 or $rest_st < 5) and ($rest_ed >= 22 or $rest_ed < 5)) {
						if (is_numeric($entity["A20"])) {
							$col22 = $entity["A20"];
							$col24 = $entity["A21"];
						} else {
							$col22 = 0;
							$col24 = 0;
						}
						$col21 = 0;
						$col23 = 0;
					} elseif (($rest_st < 22 && $rest_st >= 5) and ($rest_ed >= 22 or $rest_ed < 5)) {
						if (is_numeric($entity["A21"])) {
							$col21 = $entity["A22"];
							$col23 = $entity["A27"];
							$col22 = $entity["A23"];
							$col24 = $entity["A28"];
						} else {
							$col21 = 0;
							$col22 = 0;
							$col23 = 0;
							$col24 = 0;
						}
					} elseif (($rest_st >= 22 or $rest_st < 5) and ($rest_ed < 22 and $rest_ed >= 5)) {
						if (is_numeric($entity["A30"])) {
							$col21 = $entity["A30"];
							$col23 = $entity["A32"];
							$col22 = $entity["A31"];
							$col24 = $entity["A33"];
						} else {
							$col21 = 0;
							$col22 = 0;
							$col23 = 0;
							$col24 = 0;
						}
					}
					if ($col21 < 0) {
						$col21 = 0;
					}						
					if ($col22 < 0) {
						$col22 = 0;
					}						
					if ($col23 < 0) {
						$col23 = 0;
					}						
					if ($col24 < 0) {
						$col24 = 0;
					}						
					$shinya_time = $entity["A29"];
					$shinya_syugyo = $entity["A24"];
						
					$columnCount=2;
					
					array_push($results, $outarray);
				}
							
			}
			
			if ($linecnt != 0) {
				//最終行は必ず入れる対応
					for ($i=$columnCount;$i<=5;$i++) {
						if ($i == 2) {
							$col25 = '';
							$col26 = '';
							$col27 = 0;
							$col28 = 0;
							$col29 = 0;
							$col30 = 0;
							$col31 = 0;
								
							$col32 = '';
							$col33 = '';
							$col34 = 0;
							$col35 = 0;
							$col36 = 0;
							$col37 = 0;
							$col38 = 0;
							
							$col39 = '';
							$col40 = '';
							$col41 = 0;
							$col42 = 0;
							$col43 = 0;
							$col44 = 0;
							$col45 = 0;
							
							$col46 = '';
							$col47 = '';
							$col48 = 0;
							$col49 = 0;
							$col50 = 0;
							$col51 = 0;
							$col52 = 0;
							break;
						}
						elseif ($i == 3) {
							$col32 = '';
							$col33 = '';
							$col34 = 0;
							$col35 = 0;
							$col36 = 0;
							$col37 = 0;
							$col38 = 0;
							
							$col39 = '';
							$col40 = '';
							$col41 = 0;
							$col42 = 0;
							$col43 = 0;
							$col44 = 0;
							$col45 = 0;
							
							$col46 = '';
							$col47 = '';
							$col48 = 0;
							$col49 = 0;
							$col50 = 0;
							$col51 = 0;
							$col52 = 0;
							break;
						}
						elseif ($i == 4) {
							$col39 = '';
							$col40 = '';
							$col41 = 0;
							$col42 = 0;
							$col43 = 0;
							$col44 = 0;
							$col45 = 0;
							
							$col46 = '';
							$col47 = '';
							$col48 = 0;
							$col49 = 0;
							$col50 = 0;
							$col51 = 0;
							$col52 = 0;
							break;
						} else {
							$col46 = '';
							$col47 = '';
							$col48 = 0;
							$col49 = 0;
							$col50 = 0;
							$col51 = 0;
							$col52 = 0;
							break;
						}
					}						
					
					$col53 = $col21+$col28+$col35+$col42+$col49;
					$col54 = $col22+$col29+$col36+$col43+$col50;
					$col55 = $col53+$col54;
					$work = 0;
					$answer = 0;
					$work = floatval($col53) - intval($col53);
					switch (true) {
						case $work > 0 && $work <= 0.25:
							$answer = 0.25;
							break;
						case $work > 0.25 && $work <= 0.5:
							$answer = 0.5;
							break;
						case $work > 0.5 && $work <= 0.75:
							$answer = 0.75;
							break;
						case $work > 0.75 && $work <= 0.99:
							$answer = 1;
							break;
						default:
							$answer = 0;
							break;
					}
					$col56 = intval($col53) + $answer;
					
					$work = 0;
					$answer = 0;
					$work = floatval($col54) - intval($col54);
					switch (true) {
						case $work > 0 && $work <= 0.25:
							$answer = 0.25;
							break;
						case $work > 0.25 && $work <= 0.5:
							$answer = 0.5;
							break;
						case $work > 0.5 && $work <= 0.75:
							$answer = 0.75;
							break;
						case $work > 0.75 && $work <= 0.99:
							$answer = 1;
							break;
						default:
							$answer = 0;
							break;
					}
					$col57 = intval($col54) + $answer;
					$col58 = $col56+$col57;
					
					$normal = $col17 - $col55;
					if ($normal < 0) {
						$col17 = 0;
						$col59 = 0;
						$col60 = 0;
						$col61 = 0;
					} elseif ($normal >= 8) {
						$col59 = $normal;
						$col60 = $normal-8;
						if ($shinya_time >= 22) {
							$col61 = $shinya_syugyo-$col54;
						} else {
							$col61 = 0;
						}
					} else {
						$col59 = $normal;
						$col60 = 0;
						if ($shinya_time >= 22) {
							$col61 = $shinya_syugyo-$col54;
						} else {
							$col61 = 0;
						}
					}
					if ($col61<0) {
						$col61 = 0;
					}
					
					$marume = $col17 - $col58;
					if ($marume < 0) {
						$col17 = 0;
						$col62 = 0;
						$col63 = 0;
						$col64 = 0;
					} elseif ($marume >= 8) {
						$col62 = $marume;
						$col63 = $marume-8;
						if ($shinya_time >= 22) {
							$col64 = $shinya_syugyo-$col57;
							$work = 0;
							$answer = 0;
							$work = floatval($col64) - intval($col64);
							switch (true) {
								case $work >= 0 && $work < 0.25:
									$answer = 0;
									break;
								case $work >= 0.25 && $work < 0.5:
									$answer = 0.25;
									break;
								case $work >= 0.5 && $work < 0.75:
									$answer = 0.5;
									break;
								case $work >= 0.75 && $work < 0.99:
									$answer = 0.75;
									break;
								default:
									$answer = 0;
									break;
							}
							$col64 = intval($col64) + $answer;
						} else {
							$col64 = 0;
						}
					} else {
						$col62 = $marume;
						$col63 = 0;
						if ($shinya_time >= 22) {
							$col64 = $shinya_syugyo-$col57;
						} else {
							$col64 = 0;
						}
					}
					if ($col64<0) {
						$col64 = 0;
					}
					
					$col65 = $col59*$col6;
					$col66 = $col60*$col7;
					$col67 = $col61*$col8;
					$col68 = $col65+$col66+$col67;
					$col69 = $col62*$col6;
					$col70 = $col63*$col7;
					$col71 = $col64*$col8;
					$col72 = $col69+$col70+$col71;
																							
					$outarray = array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col10,$col11,$col12,$col13,$col14,$col15,$col16,$col17,$col18,$col19,$col20,$col21,$col22,$col23,$col24,$col25,$col26,$col27,$col28,$col29,$col30,$col31,$col32,$col33,$col34,$col35,$col36,$col37,$col38,$col39,$col40,$col41,$col42,$col43,$col44,$col45,$col46,$col47,$col48,$col49,$col50,$col51,$col52,$col53,$col54,$col55,$col56,$col57,$col58,$col59,$col60,$col61,$col62,$col63,$col64,$col65,$col66,$col67,$col68,$col69,$col70,$col71,$col72);
					array_push($results, $outarray);
			}
			$pdo = null;

			return $results;
			
		}catch (Exception $e) {
		
			$this->printLog("fatal", "例外発生", "HRD030", $e->getMessage());
			$message = $this->MMessage->getOneMessage('CMNE000107');
			$this->errors['Check'][] =   $message;
		
			$pdo = null;
		}
		
		return array();
	}
	
	public function getKoteiCsvData($startYmd, $endYmd, $daibunruicd, $cyubunruicd, $saibunruicd) {
		
		$pdo = null;
		
		try{
		
			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$common = $this->MCommon;
			$fields = '*';
			 
			// 発行するSQLを設定
			$sql  = "SELECT BAT.YMD_SYORI";
			$sql .= "      ,BAT.S_BATCH_NO";
			$sql .= "      ,BAT.BATCH_NM_CD";
			$sql .= "      ,KAN.ZONE";
			$sql .= "      ,KAN.SURYO_HAKO_SOTO";
			$sql .= "      ,KAN.SURYO_HAKO_UCHI";
			$sql .= "      ,KAN.SURYO_ITEM";
			$sql .= "      ,KAN.SURYO_PIECE";
			$sql .= "      ,KAN.STAFF_CD";
			$sql .= "      ,KAN.DT_SYORI_CYU";
			$sql .= "      ,KAN.DT_SYORI_SUMI";
			$sql .= "      ,TIMEDIFF(KAN.DT_SYORI_SUMI,KAN.DT_SYORI_CYU)";
			$sql .= "      ,ITM.ITEM_CD";
			$sql .= "      ,ITM.LOCATION";
			$sql .= "      ,ITM.SURYO_HAKO_SOTO";
			$sql .= "      ,ITM.SURYO_HAKO_UCHI";
			$sql .= "      ,ITM.SURYO_BARA";
			$sql .= "      ,ITM.SURYO_TOTAL";
			$sql .= "  FROM (T_PICK_BATCH BAT INNER JOIN T_PICK_S_KANRI KAN";
			$sql .= "    ON   BAT.NINUSI_CD=KAN.NINUSI_CD";
			$sql .= "     AND BAT.SOSIKI_CD=KAN.SOSIKI_CD";
			$sql .= "     AND BAT.YMD_SYORI=KAN.YMD_SYORI";
			$sql .= "     AND BAT.S_BATCH_NO_CD=KAN.S_BATCH_NO_CD)";
			$sql .= "     INNER JOIN T_PICK_ITEM ITM";
			$sql .= "    ON   KAN.NINUSI_CD=ITM.NINUSI_CD";
			$sql .= "     AND KAN.SOSIKI_CD=ITM.SOSIKI_CD";
			$sql .= "     AND KAN.YMD_SYORI=ITM.YMD_SYORI";
			$sql .= "     AND KAN.S_BATCH_NO_CD=ITM.S_BATCH_NO_CD";
			$sql .= "     AND KAN.S_KANRI_NO=ITM.S_KANRI_NO";
			$sql .= " WHERE ";
		    $sql .= "     1 = 1";
			if ($startYmd != "") {
				$sql .= " AND BAT.YMD_SYORI >= DATE(". $common->encloseString($startYmd) . ")";
			}
			if ($endYmd != "") {
				$sql .= " AND BAT.YMD_SYORI <= DATE(". $common->encloseString($endYmd) . ")";
							}
			$sql .= " ORDER BY ";
			$sql .= "    BAT.YMD_SYORI,BAT.S_BATCH_NO,KAN.ZONE,KAN.DT_SYORI_CYU ";
			
			$this->queryWithPDOLog($stmt, $pdo, $sql,"工程実績情報CSV　取得");
							
			$results = array();
			while ($entity = $stmt->fetch(PDO::FETCH_ASSOC)) {
				array_push($results, $entity);
			}
				
			$pdo = null;

			return $results;
			
		}catch (Exception $e) {
		
			$this->printLog("fatal", "例外発生", "HRD030", $e->getMessage());
			$message = $this->MMessage->getOneMessage('CMNE000107');
			$this->errors['Check'][] =   $message;
		
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



}