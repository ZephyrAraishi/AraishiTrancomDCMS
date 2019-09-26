<?php
/**
 * WPO030Model
 *
 * 作業指示(ピッキング割振)
 *
 * @category      WPO
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class WPO030Model extends DCMSAppModel{

	public $name = 'WPO030Model';
	public $useTable = false;

	public $errors = array();

	/**
	 * コンストラクタ
	 * @access   public
	 * @param 荷主コード
	 * @param 組織コード
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
	 * プルダウン用スタッフランク取得
	 * @access   public
	 * @return   スタッフ基本データ
	 */
	public function getRankOption() {

		try {

			//取得カラム設定
			$fields  =  'R.RANK';

			//検索条件設定
			$conditionVal = array();

			$condition  = "NINUSI_CD = :ninusi_cd AND SOSIKI_CD = :sosiki_cd " ;
			$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
			$conditionVal['sosiki_cd'] = $this->_sosiki_cd;

			//情報取得
			$sql  = 'SELECT ';
			$sql .= "$fields ";
			$sql .= 'FROM M_STAFF_RANK R ';
			$sql .= 'WHERE ';
			$sql .= $condition;
			$sql .= 'ORDER BY SORT_SEQ';

			$data = $this->queryWithLog($sql, $conditionVal, "プルダウン用スタッフランク 取得");
			$data = self::editData($data);

			return $data;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO030", $e->getMessage());
		}

		return array();
	}

	/**
	 * スタッフランクマスタデータ取得
	 * @access   public
	 * @return   スタッフランクマスタデータ
	 */
	public function getRankList() {

		try {

			//取得カラム設定
			$fields = '*';

			//検索条件設定
			$conditionVal = array();

			$condition  = "NINUSI_CD = :ninusi_cd And SOSIKI_CD = :sosiki_cd " ;
			$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
			$conditionVal['sosiki_cd'] = $this->_sosiki_cd;

			//情報取得
			$sql  = 'SELECT ';
			$sql .= "$fields ";
			$sql .= 'FROM V_WPO030_M_STAFF_RANK_LST ';
			$sql .= 'WHERE ';
			$sql .= $condition;
			$sql .= 'ORDER BY SORT_SEQ';

			$data = $this->queryWithLog($sql, $conditionVal, "スタッフランクマスタ 取得");
			$data = self::editData($data);

			return $data;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO030", $e->getMessage());
		}

		return array();
	}

	/**
	 * スタッフ基本データ取得
	 * @access   public
	 * @return   スタッフ基本データ
	 */
	public function getStaffList() {

		$stmt = null;
		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "CALL P_WPO030_GET_STAFF_DATA(";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "'";
			$sql .= ")";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"スタッフ基本情報 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			
				$seisanseiArray = $this->MCommon->getSeisansei(null,null,null,$result['STAFF_CD']);
				$buturyoSeisansei = $seisanseiArray["SEISANSEI"];

				$array = array(
						"STAFF_CD" => $result['STAFF_CD'],
						"STAFF_NM" => $result['STAFF_NM'],
						"TOD_PROD" => $buturyoSeisansei,
						"ACT_PROD" => $result['ACT_PROD'],
						"RANK" => $result['RANK'],
						"RANK_UPD" => $result['RANK_UPD'],
				);

				$arrayList[] = $array;

			}

			$pdo = null;
			return $arrayList;

		}catch (Exception $e){

			$pdo = null;
			$this->printLog("fatal", "例外発生", "WPO030", $e->getMessage());
		}

		return null;

	}

	/**
	 * スタッフランクデータ入力チェック
	 * @access   public
	 * @param    スタッフランクデータ
	 * @return   成否情報(true,false)
	 */
	public function checkRankMasterData($data) {
	
		try {
	
			//更新数
			$updCnt = 0;
			foreach($data as $key => $val) {
				//更新ステータスが0の時は、処理をスキップ
				if( $val->STATUS == 0 ){
					continue;
				}else{
					$updCnt++;
				}
	
				//入力チェック
				if(!Validation::notEmpty($val->SURYO_ITEM_FR)){
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($val->NUM, 'アイテム数自'));
					$this->errors['Check'][] =   $message;
					continue;
				}else{
					if(!Validation::numeric($val->SURYO_ITEM_FR)){
						$message = vsprintf($this->MMessage->getOneMessage('WPOE030002'), array($val->NUM, 'アイテム数自'));
						$this->errors['Check'][] =   $message;
						continue;
					}else if(!Validation::maxLength($val->SURYO_ITEM_FR,4)){
						$message = vsprintf($this->MMessage->getOneMessage('WPOE030003'), array($val->NUM, 'アイテム数自', '4'));
						$this->errors['Check'][] =   $message;
						continue;
					}
				}
				if(!Validation::notEmpty($val->SURYO_ITEM_TO)){
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($val->NUM, 'アイテム数至'));
					$this->errors['Check'][] =   $message;
					continue;
				}else{
					if(!Validation::numeric($val->SURYO_ITEM_TO)){
						$message = vsprintf($this->MMessage->getOneMessage('WPOE030002'), array($val->NUM, 'アイテム数至'));
						$this->errors['Check'][] =   $message;
						continue;
					}else if(!Validation::maxLength($val->SURYO_ITEM_TO,4)){
						$message = vsprintf($this->MMessage->getOneMessage('WPOE030003'), array($val->NUM, 'アイテム数至', '4'));
						$this->errors['Check'][] =   $message;
						continue;
					}
				}
				if((int)$val->SURYO_ITEM_FR > (int)$val->SURYO_ITEM_TO){
					$message = vsprintf($this->MMessage->getOneMessage('WPOE030004'), array($val->NUM, 'アイテム数至', 'アイテム数自'));
					$this->errors['Check'][] =   $message;
					continue;
				}
				//ピース数
				if(!Validation::notEmpty($val->SURYO_PIECE_FR)){
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($val->NUM, 'ピース数自'));
					$this->errors['Check'][] =   $message;
					continue;
				}else{
					if(!Validation::numeric($val->SURYO_PIECE_FR)){
						$message = vsprintf($this->MMessage->getOneMessage('WPOE030002'), array($val->NUM, 'ピース数自'));
						$this->errors['Check'][] =   $message;
						continue;
					}else if(!Validation::maxLength($val->SURYO_PIECE_FR,5)){
						$message = vsprintf($this->MMessage->getOneMessage('WPOE030003'), array($val->NUM, 'ピース数自', '5'));
						$this->errors['Check'][] =   $message;
						continue;
					}
				}
				if(!Validation::notEmpty($val->SURYO_PIECE_TO)){
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($val->NUM, 'ピース数至'));
					$this->errors['Check'][] =   $message;
					continue;
				}else{
					if(!Validation::numeric($val->SURYO_PIECE_TO)){
						$message = vsprintf($this->MMessage->getOneMessage('WPOE030002'), array($val->NUM, 'ピース数至'));
						$this->errors['Check'][] =   $message;
						continue;
					}else if(!Validation::maxLength($val->SURYO_PIECE_TO,5)){
						$message = vsprintf($this->MMessage->getOneMessage('WPOE030003'), array($val->NUM, 'ピース数至', '5'));
						$this->errors['Check'][] =   $message;
						continue;
					}
				}
				if((int)$val->SURYO_PIECE_FR > (int)$val->SURYO_PIECE_TO){
					$message = vsprintf($this->MMessage->getOneMessage('WPOE030004'), array($val->NUM, 'ピース数至', 'ピース数自'));
					$this->errors['Check'][] =   $message;
					continue;
				}
			}
			//更新データなし
			if($updCnt==0){
				$message = $this->MMessage->getOneMessage('CMNE000101');
				$this->errors['Check'][] =   $message;
				return false;
			}
	
			return true;
		} catch (Exception $e) {
		
			$this->printLog("fatal", "例外発生", "WPO030", $e->getMessage());
		}
	}
	
	/**
	 * タイムスタンプから最新情報かチェック
	 * @access   public
	 * @param    タイムスタンプ
	 * @return   成否情報(true,false)
	 */
	public function checkTimestampRank($currentTimestamp) {

		$pdo = null;
	
		try{
			
			//排他制御
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			
			//タイムスタンプ取得
			$sql = "SELECT";
			$sql .= "    MAX(MODIFIED) AS MODIFIED";
			$sql .= "  FROM";
			$sql .= "    M_STAFF_RANK";
			$sql .= "  WHERE";
			$sql .= "    NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "    SOSIKI_CD='" . $this->_sosiki_cd . "'";
		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'スタッフランク　タイムスタンプ取得');
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$pdo = null;
			
			$timestamp = $result['MODIFIED'];
			
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
	
			$this->printLog("fatal", "例外発生", "WPO030", $e->getMessage());
			$pdo = null;
		}
		
		//例外の場合、例外メッセージ
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
	public function checkTimestampKihon($currentTimestamp) {

		$pdo = null;
	
		try{
			
			//排他制御
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			
			//タイムスタンプ取得
			$sql = "SELECT";
			$sql .= "    MAX(MODIFIED) AS MODIFIED";
			$sql .= "  FROM";
			$sql .= "    M_STAFF_KIHON";
		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'スタッフ基本　タイムスタンプ取得');
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$pdo = null;
			
			$timestamp = $result['MODIFIED'];
			
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
	
			$this->printLog("fatal", "例外発生", "WPO030", $e->getMessage());
			$pdo = null;
		}
		
		//例外の場合、例外メッセージ
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}

	/**
	 * スタッフ変更ランクデータ入力チェック
	 * @access   public
	 * @param    スタッフデータ
	 * @return   成否情報(true,false)
	 */
	public function checkStaffUpdRank($data) {
	
		try {

			//更新数
			$updCnt = 0;
			foreach($data as $val) {
				//更新ステータスが0の時は、処理をスキップ
				if( $val->STATUS == 0 ){
					continue;
				}else{
					$updCnt++;
				}
			}
			//更新データなし
			if($updCnt==0){
				$message = $this->MMessage->getOneMessage('CMNE000101');
				$this->errors['Check'][] =   $message;
				return false;
			}

			return true;
		
		} catch(Exception $e) {
			
			$this->printLog("fatal", "例外発生", "WPO030", $e->getMessage());
		}
		
		return false;
	}

	/**
	 * ランク情報設定
	 * @access   public
	 * @param    スタッフCD
	 * @param    スタッフランクマスタデータ
	 * @return   成否情報(true,false)
	 */
	public function setRankMaster($staff_cd,$arrayList) {

		$pdo = null;

		try {

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$pdo->beginTransaction();

			foreach($arrayList as $array) {
				//更新ステータスが0の時は、処理をスキップ
				if( $array->STATUS == 0 ){
					continue;
				}

				$rank = $array->RANK;
				$suryoItemFr = $array->SURYO_ITEM_FR;
				$suryoItemTo = $array->SURYO_ITEM_TO;
				$suryoPieceFr = $array->SURYO_PIECE_FR;
				$suryoPieceTo = $array->SURYO_PIECE_TO;

				$sql = "CALL P_WPO030_UPD_RANK_MASTER(";
				$sql .= "'" . $this->_ninusi_cd . "',";
				$sql .= "'" . $this->_sosiki_cd . "',";
				$sql .= "'" . $staff_cd . "',";
				$sql .= "'" . $rank . "',";
				$sql .= "'" . $suryoItemFr . "',";
				$sql .= "'" . $suryoItemTo . "',";
				$sql .= "'" . $suryoPieceFr . "',";
				$sql .= "'" . $suryoPieceTo . "',";
				$sql .= "@return_cd";
				$sql .= ")";

				$this->execWithPDOLog($pdo,$sql, 'スタッフランク　更新');
				
				$sql = "SELECT";
				$sql .= " @return_cd";
				
				$this->queryWithPDOLog($stmt,$pdo,$sql, 'スタッフランク　更新');
				
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$return_cd = $result["@return_cd"];
				
				if ($return_cd == "1") {
				
					throw new Exception("スタッフランク更新　例外発生");
				}
				
			}
			$pdo->commit();
			$pdo = null;
			return true;

		} catch (Exception $e) {
		
			$this->printLog("fatal", "例外発生", "WPO030", $e->getMessage());
			$pdo->rollBack();
			$pdo = null;
		}

		$message =$this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] = $message;
		return false;
	}


	/**
	 * スタッフ変更ランク設定
	 * @access   public
	 * @param    ログインスタッフCD
	 * @param    スタッフ基本データ
	 * @return   成否情報(true,false)
	 */
	public function setStaffUpdRank($staff_cd,$arrayList) {

		$pdo = null;

		try {

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$pdo->beginTransaction();

			foreach($arrayList as $array) {
				//更新ステータスが0の時は、処理をスキップ
				if( $array->STATUS == 0 ){
					continue;
				}

				$targetStaffCd = $array->STAFF_CD;
				$rankUpd = $array->RANK_UPD;

				$sql = "CALL P_WPO030_UPD_STAFF_RANKUPD(";
				$sql .= "'" . $staff_cd . "',";
				$sql .= "'" . $targetStaffCd . "',";
				$sql .= $rankUpd != "" ? "'" . $rankUpd . "'," : "null,";
				$sql .= "@return_cd";
				$sql .= ")";

				$this->execWithPDOLog($pdo,$sql, 'スタッフ変更ランク　更新');
				
				$sql = "SELECT";
				$sql .= " @return_cd";
				
				$this->queryWithPDOLog($stmt,$pdo,$sql, 'スタッフ変更ランク　更新');
				
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$return_cd = $result["@return_cd"];
				
				if ($return_cd == "1") {
				
					throw new Exception("スタッフ変更ランク更新　例外発生");
				}
				
			}
			$pdo->commit();
			$pdo = null;
			return true;

		} catch (Exception $e) {
		
			$this->printLog("fatal", "例外発生", "WPO030", $e->getMessage());
			$pdo->rollBack();
			$pdo = null;
		}

		$message =$this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] = $message;
		return false;
	}

	/**
	 * データ配列加工処理
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
	 * リクエストオブジェクトから変換配列を取得
	 * @access   public
	 * @param    オブジェクト情報
	 * @return   配列情報
	 */
	public function getConvertArrayToRequestObject($requestObject) {

		// テーブル名のキー情報を削除しコントローラへ返却
		$array = array();
		foreach($requestObject as $idx => $val) {
			$array[$idx] = get_object_vars($val);
		}
		return $array;
	}

	public function getTimestamp1() {

		$pdo = null;

		try{

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//タイムスタンプ取得
			$sql = "SELECT";
			$sql .= "    MAX(MODIFIED) AS MODIFIED";
			$sql .= "  FROM";
			$sql .= "    M_STAFF_RANK";
			$sql .= "  WHERE";
			$sql .= "    NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "    SOSIKI_CD='" . $this->_sosiki_cd . "'";
		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'スタッフ基本　タイムスタンプ取得');
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$pdo = null;
			
			$timestamp = $result['MODIFIED'];
			
			if ($timestamp == null) {
				$timestamp = date('Y-m-d H:i:s');
			}
			
			$pdo = null;
			
			return $timestamp;

		} catch (Exception $e){

			$pdo = null;
		}

		return date('Y-m-d H:i:s');

	}
	
	public function getTimestamp2() {

		$pdo = null;

		try{

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//タイムスタンプ取得
			$sql = "SELECT";
			$sql .= "    MAX(MODIFIED) AS MODIFIED";
			$sql .= "  FROM";
			$sql .= "    M_STAFF_KIHON";
		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'スタッフ基本　タイムスタンプ取得');
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$pdo = null;
			
			$timestamp = $result['MODIFIED'];
			
			if ($timestamp == null) {
				$timestamp = date('Y-m-d H:i:s');
			}
			
			$pdo = null;
			
			return $timestamp;

		} catch (Exception $e){

			$pdo = null;
		}

		return date('Y-m-d H:i:s');

	}

}

?>
