<?php
/**
 * TPM040
 *
 * 作業進捗　優先変更
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */
 

App::uses('MMessage', 'Model');

class TPM040Model extends DCMSAppModel {
	
	public $name = 'TPM040Model';
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
	 * 全体の進捗情報取得
	 * @access   public
	 * @param    スタッフCD
	 * @return   全体の進捗情報
	 */
	public function setBATCH_STATUS($staff_cd) {
	
		$pdo = null;
	
		try{
		
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "CALL P_TPM040_SET_STATUS(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "'";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, 'バッチステータス　更新');
				
			$pdo = null;

			return true;

	    	
		}catch (Exception $e){

			$this->printLog("fatal", "例外発生", "TPM040", $e->getMessage(). $sql);
			$pdo = null;
		}
		
		return false;
	}
	
	/**
	 * 指定ソーターの進捗情報取得
	 * @access   public
	 * @param    ソーターCD
	 * @param    未処理、処理中の基準日
	 * @param    処理済みの基準日
	 * @return   指定ソーターの進捗情報
	 */
	public function getSorter_Info($sorter) {
	
	
		$pdo = null;
	
		try{
		
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "CALL P_TPM040_GET_DATA_SORTER(";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'". $sorter . "',";
			$sql .= "@sorter_nm";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, 'ソーター' .$sorter . 'ソーター情報　取得');
			
			$sql = "";
			$sql .= "SELECT";
			$sql .= "@sorter_nm";
		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ソーター' .$sorter . 'ソーター情報　取得');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			$array = array(
				"sorter_nm" => $result['@sorter_nm']
				);
				
			$pdo = null;
				
			return $array;

	    	
		}catch (Exception $e){

			$this->printLog("fatal", "例外発生", "TPM040", $e->getMessage());
			$pdo = null;
		}
		
		return array();
	}

	
	/**
	 * 仮置き分バッチリスト取得
	 * @access   public
	 * @param    ソーターCD
	 * @param    未処理、処理中の基準日
	 * @param    処理済みの基準日
	 * @return   指定ソーターの未処理分バッチリスト
	 */
	public function getSorter_List_Kari() {
	
	
		$pdo = null;
	
		try{
		
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "CALL P_TPM040_GET_DATA_SORTER_LIST(";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "NULL";
			$sql .= ")";
		
			$this->queryWithPDOLog($stmt,$pdo,$sql, '仮置き場バッチ未処理リスト　取得');
			
			$count = 0;
			
			$arrayList = array();
				
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
				   "PRIORITY" => $result['PRIORITY'],
				   "TOP_PRIORITY_FLG" => $result['TOP_PRIORITY_FLG'],
				   "BATCH_NM" => $result['BATCH_NM'],
				   "S_BATCH_NO" => $result['S_BATCH_NO'],
				   "S_BATCH_NO_CD" => $result['S_BATCH_NO_CD'],
				   "STATUS_NM" => $result['STATUS_NM'],
				   "S_SOSU" => $result['S_SOSU'],
				   "S_MISYORI" => $result['S_MISYORI'],
				   "S_SYORICHU" => $result['S_SYORICHU'],
				   "S_TANAKETSU" => $result['S_TANAKETSU'],
				   "S_SYORIZUMI" => $result['S_SYORIZUMI'],
				   "S_TANAKETSUZUMI" => $result['S_TANAKETSUZUMI'],
				   "PIECE_SOSU" => $result['PIECE_SOSU'],
				   "PIECE_SUMI" => $result['PIECE_SUMI'],
				   "PIECE_ZAN" => $result['PIECE_ZAN'],
				   "KBN_BATCH_STATUS" => $result['KBN_BATCH_STATUS'],
				   "YMD_SYORI" => $result['YMD_SYORI'],
				   "SORTER_SOSU" => $result['SORTER_SOSU'],
				   "SORTER_SUMI" => $result['SORTER_SUMI'],
				   "SORTER_ZAN" => $result['SORTER_ZAN'],
				   "SURYO_SHOOT" => $result['SURYO_SHOOT'],
				   "WMS_FLG" => $result['WMS_FLG'],
				   "KADO_STAFF" => '0',
				   "SOKETSU" => '0',
				   "TIME_KAISHI" => '',
				   "TIME_SYURYO" => '',
				   "TIME_YOSO" => ''

				);
				
				$arrayList[$count] = $array;

				$count++;
			}
			
				
			$pdo = null;
				
			return $arrayList;

	    	
		}catch (Exception $e){

			$this->printLog("fatal", "例外発生", "TPM040", $e->getMessage());
			$pdo = null;
		}
		
		return array();
	}
	
	
	/**
	 * バッチリスト取得
	 * @access   public
	 * @param    ソーターCD
	 * @param    未処理、処理中の基準日
	 * @param    処理済みの基準日
	 * @return   指定ソーターの処理中分バッチリスト
	 */
	public function getSorterList($sorter) {
	
	
		$pdo = null;
		$pdo2 = null;
	
		try{
		
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "CALL P_TPM040_GET_DATA_SORTER_LIST(";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $sorter . "'";
			$sql .= ")";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ソーター' . $sorter .'バッチリスト　取得');
	
			$count = 0;
			
			$arrayList = array();

		    $pdo2 = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
		    $pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			
				$kado_staff = 0;
				$yoso_time = '';
			
				if ($result['KBN_BATCH_STATUS'] == "01") {
				
					$sql = "";
					$sql .= "SELECT ";
					$sql .= "   COUNT(DISTINCT STAFF_CD) AS STAFF_COUNT";
					$sql .= " FROM";
					$sql .= "   T_PICK_S_KANRI";
					$sql .= " WHERE";
					$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
					$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
					$sql .= "   YMD_SYORI='" . $result['YMD_SYORI'] . "' AND";
					$sql .= "   S_BATCH_NO_CD='" . $result['S_BATCH_NO_CD'] . "'";
				
					$this->queryWithPDOLog($stmt2,$pdo2,$sql, '生産性データ　取得');
					$result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
					
					$kado_staff = $result2["STAFF_COUNT"];
					
					$sql = "";
					$sql .= "SELECT ";
					$sql .= "   MAX(DT_SYORI_CYU) AS DT_SYORI_CYU";
					$sql .= " FROM";
					$sql .= "   T_PICK_S_KANRI";
					$sql .= " WHERE";
					$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
					$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
					$sql .= "   YMD_SYORI='" . $result['YMD_SYORI'] . "' AND";
					$sql .= "   S_BATCH_NO_CD='" . $result['S_BATCH_NO_CD'] . "' AND";
					$sql .= "   DT_SYORI_SUMI IS NULL AND";
					$sql .= "   DT_SYORI_CYU IS NOT NULL";
				
					$this->queryWithPDOLog($stmt2,$pdo2,$sql, '生産性データ　取得');
					$result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
					$current_time = $result2["DT_SYORI_CYU"];
					
					if ($current_time == "") {
						
						$sql = "";
						$sql .= "SELECT ";
						$sql .= "   MAX(DT_SYORI_SUMI) AS DT_SYORI_SUMI";
						$sql .= " FROM";
						$sql .= "   T_PICK_S_KANRI";
						$sql .= " WHERE";
						$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
						$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
						$sql .= "   YMD_SYORI='" . $result['YMD_SYORI'] . "' AND";
						$sql .= "   S_BATCH_NO_CD='" . $result['S_BATCH_NO_CD'] . "' AND";
						$sql .= "   DT_SYORI_SUMI IS NOT NULL";
					
						$this->queryWithPDOLog($stmt2,$pdo2,$sql, '生産性データ　取得');
						$result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
						$current_time = $result2["DT_SYORI_SUMI"];
					}
					
					$sql = "";
					$sql .= "SELECT ";
					$sql .= "   SUM(SURYO_PIECE) AS SYURYO_PIECE";
					$sql .= " FROM";
					$sql .= "   T_PICK_S_KANRI";
					$sql .= " WHERE";
					$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
					$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
					$sql .= "   DT_SYORI_SUMI IS NULL AND";
					$sql .= "   YMD_SYORI='" . $result['YMD_SYORI'] . "' AND";
					$sql .= "   S_BATCH_NO_CD='" . $result['S_BATCH_NO_CD'] . "'";
				
					$this->queryWithPDOLog($stmt2,$pdo2,$sql, '生産性データ　取得');
					$result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
					
					$sum_piece = $result2["SYURYO_PIECE"];
				
					//予想時間を計算
					$yoso_time = '';
					
					$seisanseiArray = $this->MCommon->getSeisansei($result['YMD_SYORI'],$result['S_BATCH_NO_CD'],null,null,0,$current_time);
					$seisansei = $seisanseiArray["SEISANSEI"];
					
	
					if ($seisansei != 0) {
						
						$yosoMinutes = $sum_piece / ($seisansei / 60);
						
						
						$dt = new DateTime($current_time);
						$dt->add(new DateInterval('PT' . round($yosoMinutes) . 'M'));
						
						$yoso_time = $dt->format('H:i');
						
					}
				
					
				}
				
				$time_kaishi = $result['TIME_KAISHI'];
			
				if ($time_kaishi != null || $time_kaishi != '') {
				
					$time_kaishi = date('H:i',strtotime($time_kaishi));
				}
				
				$time_syuryo = $result['TIME_SYURYO'];
			
				if ($time_syuryo != null || $time_syuryo != '') {
				
					$time_syuryo = date('H:i',strtotime($time_syuryo));
				}

				$array = array(
				   "PRIORITY" => $result['PRIORITY'],
				   "TOP_PRIORITY_FLG" => $result['TOP_PRIORITY_FLG'],
				   "BATCH_NM" => $result['BATCH_NM'],
				   "S_BATCH_NO" => $result['S_BATCH_NO'],
				   "S_BATCH_NO_CD" => $result['S_BATCH_NO_CD'],
				   "STATUS_NM" => $result['STATUS_NM'],
				   "S_SOSU" => $result['S_SOSU'],
				   "S_MISYORI" => $result['S_MISYORI'],
				   "S_SYORICHU" => $result['S_SYORICHU'],
				   "S_TANAKETSU" => $result['S_TANAKETSU'],
				   "S_SYORIZUMI" => $result['S_SYORIZUMI'],
				   "S_TANAKETSUZUMI" => $result['S_TANAKETSUZUMI'],
				   "PIECE_SOSU" => $result['PIECE_SOSU'],
				   "PIECE_SUMI" => $result['PIECE_SUMI'],
				   "PIECE_ZAN" => $result['PIECE_ZAN'],
				   "KBN_BATCH_STATUS" => $result['KBN_BATCH_STATUS'],
				   "YMD_SYORI" => $result['YMD_SYORI'],
				   "SORTER_SOSU" => $result['SORTER_SOSU'],
				   "SORTER_SUMI" => $result['SORTER_SUMI'],
				   "SORTER_ZAN" => $result['SORTER_ZAN'],
				   "SURYO_SHOOT" => $result['SURYO_SHOOT'],
				   "WMS_FLG" => $result['WMS_FLG'],
				   "SORTER_SEISANSEI" => $result['SORTER_SEISANSEI'],
				   "KADO_STAFF" => $kado_staff,
				   "SOKETSU" => '0',
				   "TIME_KAISHI" => $time_kaishi,
				   "TIME_SYURYO" => $time_syuryo,
				   "TIME_YOSO" => $yoso_time
				);
				
				$arrayList[$count] = $array;
				
				

				$count++;
			}
			
			$pdo2 = null;
			$pdo = null;
				
			return $arrayList;

	    	
		}catch (Exception $e){
	
			$this->printLog("fatal", "例外発生", "TPM040", $e->getMessage());
			$pdo = null;
			$pdo2 = null;
		}
		
		return null;
	}
	
	/**
	 * WPO010の排他確認
	 * @access   public
	 * @param    スタッフCD
	 * @return   成否情報(true,false)
	 */
	public function checkWPO010($staff_cd) {
	
		$pdo = null;
	
		try{
			
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "CALL P_GET_LOCK(";
			$sql .= " '" . $staff_cd . "',";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " 'WPO',";
			$sql .= " '010',";
			$sql .= " @return";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'WPO010　排他確認');
			
			$sql = "";
			$sql .= "SELECT";
			$sql .= " @return";

		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'WPO010　排他確認');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			$pdo = null;
			
			if ($result['@return'] == "0") {
				
				return true;
			} else {
			
				$message = $this->MMessage->getOneMessage('CMNE000105');
				$this->errors['Check'][] =  $message;
				return false;
			}
			
		} catch (Exception $e){
	
			$this->printLog("fatal", "例外発生", "TPM040", $e->getMessage());
			$pdo = null;
		}
		
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}
	
	/**
	 * WPO020の排他確認
	 * @access   public
	 * @param    スタッフCD
	 * @return   成否情報(true,false)
	 */
	public function checkWPO020($staff_cd) {
	
		$pdo = null;
	
		try{
			
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "CALL P_GET_LOCK(";
			$sql .= " '" . $staff_cd . "',";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " 'WPO',";
			$sql .= " '020',";
			$sql .= " @return";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'WPO020　排他確認');
			
			$sql = "";
			$sql .= "SELECT";
			$sql .= " @return";

			$this->queryWithPDOLog($stmt,$pdo,$sql, 'WPO020　排他確認');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			$pdo = null;
			
			if ($result['@return'] == "0") {
				
				return true;
			} else {
			
				$message = $this->MMessage->getOneMessage('CMNE000105');
				$this->errors['Check'][] =  $message;
				return false;
			}
			
		} catch (Exception $e){
	
			$this->printLog("fatal", "例外発生", "TPM040", $e->getMessage());
			$pdo = null;
		}
		
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
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
			
			//排他制御
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "CALL P_TPM040_GET_TIMESTAMP(";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " @timestamp";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'バッチ　タイムスタンプ取得');
			
			$sql = "";
			$sql .= "SELECT";
			$sql .= " @timestamp";
		
		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'バッチ　タイムスタンプ取得');
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$pdo = null;
			
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
	
			$this->printLog("fatal", "例外発生", "TPM040", $e->getMessage());
			$pdo = null;
		}
		
		//例外の場合、例外メッセージ
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}
	
	public function getTimestamp() {

		$pdo = null;

		try{

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//タイムスタンプ取得
			$sql = "CALL P_TPM040_GET_TIMESTAMP(";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " @timestamp";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'バッチ　タイムスタンプ取得');
			
			$sql = "";
			$sql .= "SELECT";
			$sql .= " @timestamp";
		
		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'バッチ　タイムスタンプ取得');
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$pdo = null;
			
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
	 * バッチデータの優先番号を更新
	 * @access   public
	 * @param    スタッフCD
	 * @param    バッチデータ
	 * @return   成否情報(true,false)
	 */
	public function setPRIORITY($staff_cd,$arrayList) {

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
			$sql .= "'TPM',";
			$sql .= "'040',";
			$sql .= "1";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql,'TPM040　排他制御オン');			

			try {

				$pdo2 = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
				$pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
				$sql = "CALL P_TPM040_SET_STATUS(";
				$sql .= "'" . $staff_cd . "',";
				$sql .= "'" . $this->_ninusi_cd . "',";
				$sql .= "'" . $this->_sosiki_cd . "'";
				$sql .= ")";

				$this->execWithPDOLog($pdo2,$sql, 'バッチステータス　更新');
				
				
				//$sql = "LOCK TABLE T_PICK_BATCH WRITE";
				//$this->execWithPDOLog($pdo2,$sql,'T_PICK_BATCH　テーブルロックオン');
				
				//排他チェック
				foreach($arrayList as $array) {
					
					$S_BATCH_NO_CD = $array->S_BATCH_NO_CD;
					$TOP_PRIORITY_FLG =  $array->TOP_PRIORITY_FLG;
					$OLD_SORTER = $array->OLD_SORTER;
					$CHANGE_SORTER_CD = $array->SORTER;
					$YMD_SYORI = $array->YMD_SYORI;
					
					$sql = "SELECT";
					$sql .= "  SORTER_CD";
					$sql .= " FROM";
					$sql .= "  T_PICK_BATCH";
					$sql .= " WHERE";
					$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
					$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
					$sql .= "  S_BATCH_NO_CD='" . $S_BATCH_NO_CD . "' AND";
					$sql .= "  YMD_SYORI='" . $YMD_SYORI . "'";
					
					$this->queryWithPDOLog($stmt,$pdo2,$sql, 'バッチ　変更前ソーター取得');
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
					
					$SORTER_CD = $result["SORTER_CD"];
					
					if ($SORTER_CD != $OLD_SORTER) {
					
						//$sql = "UNLOCK TABLES";
						//$this->execWithPDOLog($pdo2,$sql,'T_PICK_BATCH　テーブルロックオフ');
						
						$sql = "CALL P_SET_LOCK(";
						$sql .= "'" . $staff_cd . "',";
						$sql .= "'" . $this->_ninusi_cd . "',";
						$sql .= "'" . $this->_sosiki_cd . "',";
						$sql .= "'TPM',";
						$sql .= "'040',";
						$sql .= "0";
						$sql .= ")";
					
						$this->execWithPDOLog($pdo2,$sql,'TPM040　排他制御オフ');
					
						$message =$this->MMessage->getOneMessage('CMNE000106');
						$this->errors['Check'][] = $message;
						return false;
					}
					
					if ($CHANGE_SORTER_CD != $SORTER_CD) {
					
						
						$sql = "SELECT";
						$sql .= "  KBN_BATCH_STATUS";
						$sql .= " FROM";
						$sql .= "  T_PICK_BATCH";
						$sql .= " WHERE";
						$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
						$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
						$sql .= "  S_BATCH_NO_CD='" . $S_BATCH_NO_CD . "' AND";
						$sql .= "  YMD_SYORI='" . $YMD_SYORI . "'";
					
						$this->queryWithPDOLog($stmt,$pdo2,$sql, 'バッチ　変更前ソーター取得');
						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						
						$KBN_STATUS = $result["KBN_BATCH_STATUS"];
						
						if ($KBN_STATUS != "00") {
							
							//$sql = "UNLOCK TABLES";
							//$this->execWithPDOLog($pdo2,$sql,'T_PICK_BATCH　テーブルロックオフ');
							
							$sql = "CALL P_SET_LOCK(";
							$sql .= "'" . $staff_cd . "',";
							$sql .= "'" . $this->_ninusi_cd . "',";
							$sql .= "'" . $this->_sosiki_cd . "',";
							$sql .= "'TPM',";
							$sql .= "'040',";
							$sql .= "0";
							$sql .= ")";
						
							$this->execWithPDOLog($pdo2,$sql,'TPM040　排他制御オフ');
						
							$message =$this->MMessage->getOneMessage('TPME040001');
							$this->errors['Check'][] = $message;
							return false;
						}
						
					}
					
				}
				
				$count = 1;
				$pdo2->beginTransaction();
				
				//更新処理
				foreach($arrayList as $array) {
					
					$S_BATCH_NO_CD = $array->S_BATCH_NO_CD;
					$TOP_PRIORITY_FLG =  $array->TOP_PRIORITY_FLG;
					$SORTER = $array->SORTER;
					$YMD_SYORI = $array->YMD_SYORI;
					$WMS_FLG = $array->WMS_FLG;
					
					$PRIORITY = $count;
					
					$sql = "CALL P_TPM040_SET_PRIORITY(";
					$sql .= "'" . $staff_cd . "',";
					$sql .= "'" . $this->_ninusi_cd . "',";
					$sql .= "'" . $this->_sosiki_cd . "',";
					$sql .= "'" . $S_BATCH_NO_CD . "',";
					$sql .= "'" . $YMD_SYORI . "',";
					$sql .= "" . $PRIORITY . ",";
					
					if ($SORTER == "") {
					
						$sql .= "NULL,";
					} else {
						
						$sql .= "'" . $SORTER . "',";
					}
					
					$sql .= "" . $WMS_FLG . ",";
					$sql .= "" . $TOP_PRIORITY_FLG . ",";
					$sql .= "@return_cd";
					$sql .= ")";
					
					$this->execWithPDOLog($pdo2,$sql, 'バッチ　更新');	
					
					$sql = "SELECT";
					$sql .= " @return_cd";
					
					$this->queryWithPDOLog($stmt,$pdo2,$sql, 'バッチ　更新');
					
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
					$return_cd = $result["@return_cd"];
					
					if ($return_cd == "1") {
					
						throw new Exception("バッチ更新　例外発生");
					}
					
					$count++;
				}
				
				$pdo2->commit();
				
				$sql = "UNLOCK TABLES";
				$this->execWithPDOLog($pdo2,$sql,'T_PICK_BATCH　テーブルロックオフ');
			
			} catch (Exception $e) {
			
				$this->printLog("fatal", "例外発生", "TPM040", $e->getMessage() . $sql);
				$pdo2->rollBack();
			
				try {
					
					$sql = "UNLOCK TABLES";
					$this->execWithPDOLog($pdo2,$sql,'T_PICK_BATCH　テーブルロックオフ');

				} catch (Exception $e) {
					
					$this->printLog("fatal", "例外発生", "TPM040", $e->getMessage());
				}

			}

			$pdo2 = null;
		
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'TPM',";
			$sql .= "'040',";
			$sql .= "0";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql,'TPM040　排他制御オフ');
			$pdo = null;
			
			if ($return_cd == "1") {
				
				//例外の場合、例外メッセージ
				$message = $this->MMessage->getOneMessage('CMNE000107');
				$this->errors['Check'][] =  $message;
				return false;
			}
			
			return true;
			
		} catch (Exception $e){
	
			$this->printLog("fatal", "例外発生", "TPM040", $e->getMessage());
			$pdo = null;
		}
		
		//例外の場合、例外メッセージ
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}
	
	/**
	 * TIME_STを更新
	 * @access   public
	 * @param    スタッフCD
	 * @param    バッチデータ
	 * @return   成否情報(true,false)
	 */
	public function setDateTime($staff_cd,$sorter_cd,$dateTime,$seisansei) {

		$pdo = null;
		
		if ($dateTime == "") {
			
			return true;
		}
	
		try{
			
			//排他制御
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "CALL P_TPM040_SET_TIME_ST(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $sorter_cd . "',";
			$sql .= "'" .$dateTime . "',";
			$sql .= "'" .$seisansei . "',";
			$sql .= "@return_cd";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'ソーター' . $sorter_cd . 'の開始時間　更新');	
			
			$sql = "SELECT";
			$sql .= " @return_cd";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ソーター' . $sorter_cd . 'の開始時間　更新');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];
			
			if ($return_cd == "1") {
			
				throw new Exception('ソーター' . $sorter_cd . 'の開始時間更新　例外発生');
			}
			
			return true;
			
		}catch(Exception $e) {
			
			$this->printLog("fatal", "例外発生", "TPM040", $e->getMessage());
			$pdo = null;
		}
		
		//例外の場合、例外メッセージ
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}
	
			/**
	 * バッチデータの優先番号を更新
	 * @access   public
	 * @param    スタッフCD
	 * @param    バッチデータ
	 * @return   成否情報(true,false)
	 */
	public function getSORTER_KAISHI($sorter_cd) {

		$pdo = null;
	
		try{
			
			//排他制御
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "CALL P_TPM040_GET_SORTER_KAISHI(";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $sorter_cd . "',";
			$sql .= "@dateTime,";
			$sql .= "@seisansei";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'ソーター' . $sorter_cd . 'の開始時間　取得');	
			
			$sql = "SELECT";
			$sql .= " @dateTime,";
			$sql .= " @seisansei";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ソーター' . $sorter_cd . 'の開始時間　取得');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			
			$array = array(
				"STARTTIME" => $result["@dateTime"],
				"SEISANSEI" => $result["@seisansei"]
			);
			
			return $array;
			
		}catch(Exception $e) {
			
			$this->printLog("fatal", "例外発生", "TPM040", $e->getMessage());
			$pdo = null;
		}
		
		return "";
	}
}

?>
 