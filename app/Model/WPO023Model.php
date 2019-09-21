<?php
/**
 * WPO023Model
 *
 * ステータス変更画面
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class WPO023Model extends DCMSAppModel{
	
	public $name = 'WPO023Model';
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
	 * 出荷管理_集計管理№データ取得
	 * @access   public
	 * @param    バッチ名CD
	 * @param    集計管理No
	 * @param    ゾーン
	 * @param    スタッフ名
	 * @param    ステータス区分
	 * @return   ピッキング集計管理№データ
	 */
	public function getSKanriLst($batch_nm_cd, $s_batch_no, $s_kanri_no, $zone, $staff_nm, $kbn_status) {
	
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
			
			$pdo = null;
	
			//取得カラム設定
			$fields = '*';
	
			//検索条件設定
			$conditionVal = array();
			$conditionKey = '';
			
			//検索条件設定
			$condition  = "NINUSI_CD = :ninusi_cd And SOSIKI_CD = :sosiki_cd " ;
			$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
			$conditionVal['sosiki_cd'] = $this->_sosiki_cd;
	
			//バッチ名
			if ($batch_nm_cd != null){
				$condition .= " AND BATCH_NM_CD = :batch_nm_cd " ;
				$conditionVal['batch_nm_cd'] = $batch_nm_cd;
			}
			//Sバッチ№
			if ($s_batch_no != null){
				$condition .= " AND S_BATCH_NO LIKE :s_batch_no " ;
				$conditionVal['s_batch_no'] = $s_batch_no . "%";
			}
			//集計管理№
			if ($s_kanri_no != null){
				$condition .= " AND S_KANRI_NO LIKE :s_kanri_no " ;
				$conditionVal['s_kanri_no'] = $s_kanri_no . "%";
			}
			//ゾーン
			if ($zone != null){
				$condition .= " AND ZONE = :zone " ;
				$conditionVal['zone'] = $zone;
			}
			//スタッフ
			if ($staff_nm != null){
				$condition .= " AND STAFF_NM LIKE :staff_nm " ;
				$conditionVal['staff_nm'] = "%" . $staff_nm . "%";
			}
			//ステータス
			if ($kbn_status != null){
				$condition .= " AND KBN_STATUS = :kbn_status " ;
				$conditionVal['kbn_status'] = $kbn_status;
			}
			
			$condition  .= " AND (YMD_SYORI >= :ymd_shori OR SUBSTR(KBN_STATUS,2,1) <> '9')" ;
			$conditionVal['ymd_shori'] = $ymd_syori;
			
			//拠点情報取得
			$sql  = 'SELECT ';
			$sql .= "$fields ";
			$sql .= 'FROM V_WPO020_S_KANRI_LST ';
			$sql .= 'WHERE ';
			$sql .= $condition;
			$sql .= $conditionKey;
			
			$data = $this->queryWithLog($sql, $conditionVal, "集計管理 取得");
			$data = self::editData($data);
		
			return $data;
		
		} catch (Exception $e) {
			
			$this->printLog("fatal", "例外発生", "WPO023", $e->getMessage());
		}
		
		return array();
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
	
	/**
	 * TPM040の排他制御
	 * @access   public
	 * @param    スタッフCD
	 * @return   成否情報(true,false)
	 */
	public function checkTPM040($staff_cd) {
	
		$pdo = null;
	
		try{
			
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			//排他チェック
			$sql = "CALL P_GET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'TPM',";
			$sql .= "'040',";
			$sql .= "@return";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql,'TPM040 排他確認');
			
			$sql = "";
			$sql .= "SELECT";
			$sql .= "@return";

		
			$this->queryWithPDOLog($stmt,$pdo,$sql,'TPM040 排他確認');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			$pdo = null;
			
			if ($result['@return'] == "0") {
			
				return true;	
			} else {
			
				$message =$this->MMessage->getOneMessage('CMNE000105');
				$this->errors['Check'][] = $message;
				return false;
			}
			
		} catch (Exception $e){
	
			$this->printLog("fatal", "例外発生", "WPO023", $e->getMessage());
			$pdo = null;
		}
		
		$message =$this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] = $message;
		return false;
	}
	
	/**
	 * ステータス情報設定
	 * @access   public
	 * @param    スタッフCD
	 * @param    集計管理Noデータ
	 * @return   成否情報(true,false)
	 */
	public function setSTATUS($staff_cd,$arrayList) {

		$pdo = null;
		$pdo2 = null;
		$return_cd = "0";
	
		try{
			
			//排他制御
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WPO',";
			$sql .= "'023',";
			$sql .= "1";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql,'WPO023 排他制御オン');
			$pdo = null;
			

			try {

				$pdo2 = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
				$pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
				$pdo2->beginTransaction();
				$count = 1;
				
				foreach($arrayList as $array) {
					
					
					$S_BATCH_NO_CD = $array->S_BATCH_NO_CD;
					$S_KANRI_NO = $array->S_KANRI_NO;
					$KBN_STATUS = $array->KBN_STATUS;
					$YMD_SYORI = $array->YMD_SYORI;
					
					$sql = "CALL P_WPO023_SET_STATUS(";
					$sql .= "'" . $staff_cd . "',";
					$sql .= "'" . $this->_ninusi_cd . "',";
					$sql .= "'" . $this->_sosiki_cd . "',";
					$sql .= "'" . $S_BATCH_NO_CD . "',";
					$sql .= "'" . $S_KANRI_NO . "',";
					$sql .= "'" . $KBN_STATUS . "',";
					$sql .= "'" . $YMD_SYORI . "',";
					$sql .= "@return_cd";
					$sql .= ")";
					
					$this->execWithPDOLog($pdo2,$sql, '集計管理 ステータス更新');
					
					$sql = "SELECT";
					$sql .= " @return_cd";
					
					$this->queryWithPDOLog($stmt,$pdo2,$sql, '集計管理 ステータス更新');
					
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
					$return_cd = $result["@return_cd"];
					
					if ($return_cd == "1") {
					
						throw new Exception('集計管理ステータス更新　例外発生');
					}
					
								
					$count++;
				}
				
				$pdo2->commit();
			
			} catch (Exception $e) {
			
				$this->printLog("fatal", "例外発生", "WPO023", $e->getMessage());
				$pdo2->rollBack();
			}
			
			$pdo2 = null;
			
			//排他解除
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WPO',";
			$sql .= "'023',";
			$sql .= "0";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql,'WPO023 排他制御オフ');
			$pdo = null;
			
			if ($return_cd == "1") {
				
				//例外の場合、例外メッセージ
				$message = $this->MMessage->getOneMessage('CMNE000107');
				$this->errors['Check'][] =  $message;
				return false;
			}
			
			return true;
			
		} catch (Exception $e){
			
			$this->printLog("fatal", "例外発生", "WPO023", $e->getMessage());
			$pdo = null;
		}
		
		$message =$this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] = $message;
		return false;
	}
	
}

?>
 