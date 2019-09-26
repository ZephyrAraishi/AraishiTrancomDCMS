<?php
/**
 * TPM010
 *
 * 作業進捗　全体進捗①
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('DCMSAppModel', 'Model');
App::uses('MCommon', 'Model');

class TPM010Model extends DCMSAppModel {
	
	public $name = 'TPM010Model';
	public $useTable = false;
	
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
		
		//共通関数モデル
		$this->MCommon = new MCommon($ninusi_cd,$sosiki_cd,$kino_id);
		
	}
	
	/**
	 * 全体の進捗情報取得
	 * @access   public
	 * @param    スタッフCD
	 * @return   全体の進捗情報
	 */
	public function getZENTAI_INFO($staff_cd) {
	
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

			$this->execWithPDOLog($pdo,$sql, '全体進捗情報　取得');
			
			$sql = "";
			$sql .= "SELECT";
			$sql .= "@ymd_syori,";
			$sql .= "@misyori,";
			$sql .= "@syorichu,";
			$sql .= "@syorizumi,";
			$sql .= "@piece_misyori,";
			$sql .= "@piece_syorichu,";
			$sql .= "@piece_syorizumi";
		
			$this->queryWithPDOLog($stmt,$pdo,$sql, '全体進捗情報　取得');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			$array = array(		
				"ymd_syori" => $result['@ymd_syori'],
				"misyori" => $result['@misyori'],
				"syorichu" => $result['@syorichu'],
				"syorizumi" => $result['@syorizumi'],
				"piece_misyori" => $result['@piece_misyori'],
				"piece_syorichu" => $result['@piece_syorichu'],
				"piece_syorizumi" => $result['@piece_syorizumi'],
				);
				
			$pdo = null;

			return $array;

	    	
		}catch (Exception $e){

			$this->printLog("fatal", "例外発生", "TPM010", $e->getMessage());
			$pdo = null;
		}
		
		return array();
	}
	
	/**
	 * 指定ソーターの進捗情報取得
	 * @access   public
	 * @param    ソーターCD
	 * @param    未処理、処理中の基準日
	 * @param    処理済みの基準日
	 * @return   指定ソーターの進捗情報
	 */
	public function getSorter_Suryo($sorter,$ymd_syori) {
	
	
		$pdo = null;
	
		try{
		
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "CALL P_TPM010_GET_DATA_SORTER_SURYO(";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'". $sorter . "',";
			$sql .= "'" . $ymd_syori . "',";
			$sql .= "@misyori,";
			$sql .= "@syorichu,";
			$sql .= "@syorizumi,";
			$sql .= "@piece_misyori,";
			$sql .= "@piece_syorichu,";
			$sql .= "@piece_syorizumi,";
			$sql .= "@sorter_nm";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, 'ソーター' .$sorter . '全体進捗　取得');
			
			$sql = "";
			$sql .= "SELECT";
			$sql .= "@misyori,";
			$sql .= "@syorichu,";
			$sql .= "@syorizumi,";
			$sql .= "@piece_misyori,";
			$sql .= "@piece_syorichu,";
			$sql .= "@piece_syorizumi,";
			$sql .= "@sorter_nm";
		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ソーター' .$sorter . '全体進捗　取得');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			$array = array(
				"misyori" => $result['@misyori'],
				"syorichu" => $result['@syorichu'],
				"syorizumi" => $result['@syorizumi'],
				"piece_misyori" => $result['@piece_misyori'],
				"piece_syorichu" => $result['@piece_syorichu'],
				"piece_syorizumi" => $result['@piece_syorizumi'],
				"sorter_nm" => $result['@sorter_nm']
				);
				
			$pdo = null;
				
			return $array;

	    	
		}catch (Exception $e){

			$this->printLog("fatal", "例外発生", "TPM010", $e->getMessage());
			$pdo = null;
		}
		
		return array();
	}
	
	/**
	 * 指定ソーターの未処理分バッチリスト取得
	 * @access   public
	 * @param    ソーターCD
	 * @param    未処理、処理中の基準日
	 * @param    処理済みの基準日
	 * @return   指定ソーターの未処理分バッチリスト
	 */
	public function getSorter_List_Mishori($sorter,$ymd_syori) {
	
	
		$pdo = null;
	
		try{
		
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "CALL P_TPM010_GET_DATA_SORTER_LIST(";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $sorter . "',";
			$sql .= "'" . $ymd_syori . "',";
			$sql .= "'00'";
			$sql .= ")";
		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ソーター' . $sorter .'バッチ未処理リスト　取得');
			
			$count = 0;
			
			$arrayList = array();
				
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
				   "PRIORITY" => $result['PRIORITY'],
				   "TOP_PRIORITY_FLG" => $result['TOP_PRIORITY_FLG'],
				   "BATCH_NM" => $result['BATCH_NM'],
				   "S_BATCH_NO" => $result['S_BATCH_NO'],
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
				   "SORTER_SOSU" => $result['SORTER_SOSU'],
				   "SORTER_SUMI" => $result['SORTER_SUMI'],
				   "SORTER_ZAN" => $result['SORTER_ZAN'],
				   "KADO_STAFF" => '0',
				   "SOKETSU" => '0',
				   "TIME_KAISHI" => '',
				   "TIME_SYURYO" => '',
				   "TIME_YOSO" => '',
				   "SEISANSEI" => ''
				);
				
				$arrayList[$count] = $array;

				$count++;
			}
			
				
			$pdo = null;
				
			return $arrayList;

	    	
		}catch (Exception $e){

			$this->printLog("fatal", "例外発生", "TPM010", $e->getMessage());
			$pdo = null;
		    throw $e;
		}
		
		return null;
	}
	
	/**
	 * 指定ソーターの処理済み分バッチリスト取得
	 * @access   public
	 * @param    ソーターCD
	 * @param    未処理、処理中の基準日
	 * @param    処理済みの基準日
	 * @return   指定ソーターの処理済み分バッチリスト
	 */
	public function getSorter_List_Sumi($sorter,$ymd_syori) {
	
	
		$pdo = null;
	
		try{
		
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "CALL P_TPM010_GET_DATA_SORTER_LIST(";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $sorter . "',";
			$sql .= "'" . $ymd_syori . "',";
			$sql .= "'89'";
			$sql .= ")";
		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ソーター' . $sorter .'バッチ処理済みリスト　取得');
			
			$count = 0;
			
			$arrayList1 = array();
			
			$pdo2 = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			
				$time_kaishi = $result['TIME_KAISHI'];
			
				if ($time_kaishi != null || $time_kaishi != '') {
				
					$time_kaishi = date('H:i',strtotime($time_kaishi));
				}
				
				$time_syuryo = $result['TIME_SYURYO'];
			
				if ($time_syuryo != null || $time_syuryo != '') {
				
					$time_syuryo = date('H:i',strtotime($time_syuryo));
				}
				
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

				$array = array(
				   "PRIORITY" => $result['PRIORITY'],
				   "TOP_PRIORITY_FLG" => $result['TOP_PRIORITY_FLG'],
				   "BATCH_NM" => $result['BATCH_NM'],
				   "S_BATCH_NO" => $result['S_BATCH_NO'],
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
				   "SORTER_SOSU" => $result['SORTER_SOSU'],
				   "SORTER_SUMI" => $result['SORTER_SUMI'],
				   "SORTER_ZAN" => $result['SORTER_ZAN'],
				   "KADO_STAFF" => $kado_staff,
				   "SOKETSU" => '0',
				   "TIME_KAISHI" => $time_kaishi,
				   "TIME_SYURYO" => $time_syuryo,
				   "TIME_YOSO" => '',
				   "SEISANSEI" => ''
				);
				
				$arrayList1[$count] = $array;

				$count++;
			}
			
			$stmt->closeCursor();
			
			$sql = "CALL P_TPM010_GET_DATA_SORTER_LIST(";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $sorter . "',";
			$sql .= "'" . $ymd_syori . "',";
			$sql .= "'99'";
			$sql .= ")";
		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ソーター' . $sorter .'バッチ処理済みリスト　取得');
			
			$count = 0;
			
			$arrayList2 = array();
			
				
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			
				$time_kaishi = $result['TIME_KAISHI'];
			
				if ($time_kaishi != null || $time_kaishi != '') {
				
					$time_kaishi = date('H:i',strtotime($time_kaishi));
				}
				
				$time_syuryo = $result['TIME_SYURYO'];
			
				if ($time_syuryo != null || $time_syuryo != '') {
				
					$time_syuryo = date('H:i',strtotime($time_syuryo));
				}
				
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

				$array = array(
				   "PRIORITY" => $result['PRIORITY'],
				   "TOP_PRIORITY_FLG" => $result['TOP_PRIORITY_FLG'],
				   "BATCH_NM" => $result['BATCH_NM'],
				   "S_BATCH_NO" => $result['S_BATCH_NO'],
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
				   "SORTER_SOSU" => $result['SORTER_SOSU'],
				   "SORTER_SUMI" => $result['SORTER_SUMI'],
				   "SORTER_ZAN" => $result['SORTER_ZAN'],
				   "KADO_STAFF" => $kado_staff,
				   "SOKETSU" => '0',
				   "TIME_KAISHI" => $time_kaishi,
				   "TIME_SYURYO" => $time_syuryo,
				   "TIME_YOSO" => '',
				   "SEISANSEI" => ''
				);
				
				$arrayList2[$count] = $array;

				$count++;
			}
			
			$pdo2 = null;
			$pdo = null;
			
			$arrayList = array_merge($arrayList1,$arrayList2);
				
			return $arrayList;

	    	
		}catch (Exception $e){

			$this->printLog("fatal", "例外発生", "TPM010", $e->getMessage());
			$pdo = null;
		    throw $e;
		}
		
		return null;
	}
	
	/**
	 * 指定ソーターの処理中分バッチリスト取得
	 * @access   public
	 * @param    ソーターCD
	 * @param    未処理、処理中の基準日
	 * @param    処理済みの基準日
	 * @return   指定ソーターの処理中分バッチリスト
	 */
	public function getSorter_List_Syorichu($sorter,$ymd_syori) {
	
	
		$pdo = null;
		$pdo2 = null;
	
		try{
		
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "CALL P_TPM010_GET_DATA_SORTER_LIST(";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $sorter . "',";
			$sql .= "'" . $ymd_syori . "',";
			$sql .= "'01'";
			$sql .= ")";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ソーター' . $sorter .'バッチ処理中リスト　取得');
	
			$count = 0;
			
			$arrayList = array();
			
			$pdo2 = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

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

				$time_kaishi = $result['TIME_KAISHI'];
				
				if ($time_kaishi != null || $time_kaishi != '') {
				
					$time_kaishi = date('H:i',strtotime($time_kaishi));
				}
				
				if ($seisansei != 0) {
				
					$seisansei=$seisansei*10;
					$seisansei=floor($seisansei);
					$seisansei=$seisansei/10;
					$seisansei=number_format($seisansei, 1, '.', ',');
				
				} else {
					$seisansei = "";
				}

				$array = array(
				   "PRIORITY" => $result['PRIORITY'],
				   "TOP_PRIORITY_FLG" => $result['TOP_PRIORITY_FLG'],
				   "BATCH_NM" => $result['BATCH_NM'],
				   "S_BATCH_NO" => $result['S_BATCH_NO'],
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
				   "SORTER_SOSU" => $result['SORTER_SOSU'],
				   "SORTER_SUMI" => $result['SORTER_SUMI'],
				   "SORTER_ZAN" => $result['SORTER_ZAN'],
				   "KADO_STAFF" => $kado_staff,
				   "SOKETSU" => '0',
				   "TIME_KAISHI" => $time_kaishi,
				   "TIME_SYURYO" => '',
				   "TIME_YOSO" => $yoso_time,
				   "SEISANSEI" => $seisansei
				);
				
				$arrayList[$count] = $array;
				
				$count++;
			}
			
			$pdo2 = null;
			$pdo = null;
				
			return $arrayList;

	    	
		}catch (Exception $e){
	
			$this->printLog("fatal", "例外発生", "TPM010", $e->getMessage());
			$pdo = null;
			$pdo2 = null;
		    throw $e;
		}
		
		return null;
	}		
}

?>
 