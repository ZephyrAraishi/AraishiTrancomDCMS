<?php
/**
 * TPM020
 *
 * 作業進捗　全体進捗②
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('DCMSAppModel', 'Model');

class TPM020Model extends DCMSAppModel {
	
	public $name = 'TPM020Model';
	public $useTable = false;
	// バリデート
	private $errMsg;
		
	/**
	 * ゾーン別_ピッキング集計管理№データ取得
	 * @access   public
	 * @return   ピッキング集計管理№データ
	 */
	public function getTPickSKanri_Zone($staff_cd) {
	
		try {
		
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "CALL P_TPM010_GET_DATA_ZENTAI(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "@ymd_syori,";
			$sql .= "@misyori,";
			$sql .= "@syorichu,";
			$sql .= "@syorizumi,";
			$sql .= "@piece_misyori,";
			$sql .= "@piece_syorichu,";
			$sql .= "@piece_syorizumi";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, '基準日　取得');
			
			$sql = "";
			$sql .= "SELECT";
			$sql .= "@ymd_syori,";
			$sql .= "@misyori,";
			$sql .= "@syorichu,";
			$sql .= "@syorizumi,";
			$sql .= "@piece_misyori,";
			$sql .= "@piece_syorichu,";
			$sql .= "@piece_syorizumi";
		
			$this->queryWithPDOLog($stmt,$pdo,$sql, '基準日　取得');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			$ymd_syori = $result['@ymd_syori'];

			$pdo = null;
	
			$sql = "";			
			$sql  .= "SELECT ";
			$sql  .= "	     Z.NINUSI_CD";
			$sql  .= "      ,Z.SOSIKI_CD";
			$sql  .= "      ,Z.ZONE_NM ZONE_MEI";
			$sql  .= "      ,COUNT(S.KBN_STATUS) ALL_CNT";
			$sql  .= "      ,SUM(CASE S.KBN_STATUS WHEN '01' THEN 1 ELSE 0 END) SYORI_MI_CNT";
			$sql  .= "      ,SUM(CASE S.KBN_STATUS WHEN '02' THEN 1 ELSE 0 END) SYORI_CYU_CNT";
			$sql  .= "      ,SUM(CASE S.KBN_STATUS WHEN '11' THEN 1 ELSE 0 END) T_KETSU_CNT";
			$sql  .= "      ,SUM(CASE S.KBN_STATUS WHEN '09' THEN 1 ELSE 0 END) SYORI_SUMI_CNT";
			$sql  .= "      ,SUM(CASE S.KBN_STATUS WHEN '19' THEN 1 ELSE 0 END) T_KETSU_SUMI_CNT";
			$sql  .= "  FROM ";
			$sql  .= "       (";
			$sql  .= "          SELECT";
			$sql  .= "                 NINUSI_CD AS NINUSI_CD, ";
			$sql  .= "                 SOSIKI_CD AS SOSIKI_CD, ";
			$sql  .= "                 MEI_CD AS ZONE, ";
			$sql  .= "                 MEI_1 AS ZONE_NM, ";
			$sql  .= "                 VAL_FREE_NUM_1 AS ZONE_ORDER ";
			$sql  .= "            FROM ";
			$sql  .= "                 M_MEISYO ";
			$sql  .= "           WHERE ";
			$sql  .= "	               NINUSI_CD = '" . $this->_ninusi_cd . "' AND";
			$sql  .= "                 SOSIKI_CD = '" . $this->_sosiki_cd . "' AND";
			$sql  .= "                 MEI_KBN = '05'";
			$sql  .= "       ) Z";
			$sql  .= " LEFT JOIN";
			$sql  .= "       T_PICK_S_KANRI S";
			$sql  .= "    ON ";
			$sql  .= "	       Z.NINUSI_CD = S.NINUSI_CD";
			$sql  .= "     AND Z.SOSIKI_CD = S.SOSIKI_CD";
			$sql  .= "     AND Z.ZONE = S.ZONE";
			$sql  .= "     AND S.YMD_SYORI >= '" . $ymd_syori . "'";
			$sql  .= " GROUP BY";
			$sql  .= "	     Z.NINUSI_CD";
			$sql  .= "      ,Z.SOSIKI_CD";
			$sql  .= "      ,Z.ZONE_NM";
			$sql  .= " ORDER BY";
			$sql  .= "	     Z.NINUSI_CD";
			$sql  .= "      ,Z.SOSIKI_CD";
			$sql  .= "      ,Z.ZONE_ORDER";

			$data = $this->queryWithLog($sql, null, "ゾーン別_ピッキング集計管理№データ　取得");
			$data = self::editData($data);		
	
			return $data;
		
		} catch(Exception $e) {
			
			$this->printLog("fatal", "例外発生", "TPM020", $e->getMessage());
		}
		
		return null;
	}
	
	/**
	 * 時間別_ピッキング集計管理№データ取得
	 * @access   public
	 * @return   ピッキング集計管理№データ
	 */
	public function getTPickSKanri_time_tp() {
	
		try {
		
			//DBオブジェクト取得
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
			$ymd_syori = $result['CURRENT_DATE'];
			$now = $result['NOW'];
			
			//日付取得			
			$tmpDate = $ymd_syori . " " . $dt_syugyo_st_base;
			
			if (strtotime($now) < strtotime($tmpDate)) {
			
				$dt = new DateTime($tmpDate);
				$dt->sub(new DateInterval("P1D"));
				$ymd_syori = $dt->format('Y-m-d');
				$tmpDate = $ymd_syori . " " . $dt_syugyo_st_base;
			}
			
			$dt = new DateTime($tmpDate);
			$date1 = $dt->format('Y-m-d H:i:s');
			$dt->add(new DateInterval("P1D"));
			$date2 = $dt->format('Y-m-d H:i:s');
			
			$pdo = null;
		
			$sql = "";
			$sql  .= "SELECT ";
			$sql  .= "       S.NINUSI_CD";
			$sql  .= "      ,S.SOSIKI_CD";
			$sql  .= "      ,CASE ";
			$sql  .= "	     WHEN";
			$sql  .= "	       HOUR(S.DT_SYORI_SUMI) >= 7 THEN";
			$sql  .= "	         HOUR(S.DT_SYORI_SUMI)";
			$sql  .= "	     WHEN";
			$sql  .= "	       HOUR(S.DT_SYORI_SUMI) < 7 THEN";
			$sql  .= "	         HOUR(S.DT_SYORI_SUMI) + 24";
			$sql  .= "	   END AS END_H";
			$sql  .= "      ,SUM(S.SURYO_PIECE) AS SUM_PIECE";
			$sql  .= "      ,COUNT(DISTINCT S.STAFF_CD) CNT_STAFF";
			$sql  .= "  FROM ";
			$sql  .= "       T_PICK_S_KANRI S";
			$sql  .= " WHERE ";
			$sql  .= "       S.DT_SYORI_SUMI Is Not Null";
			$sql  .= "   AND S.NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql  .= "   AND S.SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$sql  .= "   AND S.DT_SYORI_SUMI >= '" . $date1 . "'";
			$sql  .= "   AND S.DT_SYORI_SUMI < '" . $date2 . "'";
			$sql  .= " GROUP BY";
			$sql  .= "       S.NINUSI_CD";
			$sql  .= "      ,S.SOSIKI_CD";
			$sql  .= "      ,HOUR(S.DT_SYORI_SUMI)";
			$sql  .= " ORDER BY";
			$sql  .= "       S.NINUSI_CD";
			$sql  .= "      ,S.SOSIKI_CD";
			$sql  .= "      ,END_H";
			

			$data = $this->queryWithLog($sql, null, "時間別_ピッキング集計管理№データ　取得");
			$data = self::editData($data);		
			
			return $data;
		
		} catch(Exception $e) {
		
			$this->printLog("fatal", "例外発生", "TPM020", $e->getMessage());
		}
		
		return null;
	}
	
	/**
	 * ピッキングアイテムデータ_棚欠_取得
	 * @access   public
	 * @return   ピッキングアイテムデータ
	 */
	public function getTPickItem_TKetu() {
	
		try {
	
			/* ▼取得カラム設定 */
			$fields = '*';
			
			/* ▼検索条件設定 */
			$condition  = "NINUSI_CD = '". $this->_ninusi_cd. "' And SOSIKI_CD = '". $this->_sosiki_cd. "'" ;
			
			/* ▼拠点情報取得 */
			$sql  = 'SELECT ';
			$sql .= "$fields ";
			$sql .= 'FROM V_TPM020_T_KETU ';
			$sql .= 'WHERE ';
			$sql .= $condition;
			
			$data = $this->queryWithLog($sql, $condition, "ピッキングアイテムデータ_棚欠　取得");
			$data = self::editData($data);		
	
			return $data;
		
		} catch (Exception $e) {
		
			$this->printLog("fatal", "例外発生", "TPM020", $e->getMessage());
		}
		return null;
	}
	
	/**
	 * 拠点情報編集処理
	 * @access   public
	 * @param    編集前情報
	 * @return   編集後情報
	 */
	public function editData($results) {
	
		// テーブル名のキー情報を削除しコントローラへ返却
		$data = array();
		foreach($results as $key => $value) {
			foreach($value as $key2 => $value2) {
				foreach($value2 as $key3 => $value3) {
					$data[$key][$key3] = $value3;
				}
			}
		}
		return $data;
	}
	
}

?>
 