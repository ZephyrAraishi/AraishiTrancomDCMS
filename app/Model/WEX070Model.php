<?php
/**
 * WEX070Model
 *
 * ABCデータベース登録
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');

class WEX070Model extends DCMSAppModel{
	
	public $name = 'WEX070Model';
	public $useTable = false;

	public $errors = array();
    public $infos = array();

	
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
       
		/* ▼メッセージマスタ呼び出し */
		$this->MMessage = new MMessage($ninusi_cd, $sosiki_cd, $kino_id);
    }

	/**
	 * ABCデータベース取得
	 * @access   public
	 * @return   ABCデータベース
	 */
	public function getTAbcDbLstMaxYMD() {
	
		$pdo = null;

		try{

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//タイムスタンプ取得
			$sql = "SELECT";
			$sql .= "   MAX(YMD) AS YMD";
			$sql .= "  FROM T_ABC_DB_DAY";
		
		
			$this->queryWithPDOLog($stmt,$pdo,$sql, '検索条件初期値　取得');
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			$timestamp = $result['YMD'];
			
			if ($timestamp == null) {
				$timestamp = date('Y-m-d');
			}
			
			return $timestamp;

		} catch (Exception $e){

			$pdo = null;
		}

		return date('Y-m-d');
	}

	/**
	 * ABCデータベース取得
	 * @access   public
	 * @return   ABCデータベース
	 */
	public function getTAbcDbLst($bnri_dai_cd, $bnri_cyu_cd,  $bnri_sai_cd, 
                                 $start_ymd_ins , $end_ymd_ins, $count = false) {
		/* ▼取得カラム設定 */
		$fields = $count ? " COUNT(*) AS COUNT" : " *";

		/* ▼検索条件設定 */	
		$conditionVal = array();
		$conditionKey = '';
		
		/* ▼検索条件設定 */
		$condition  = "NINUSI_CD       = :ninusi_cd";
		$condition .= " AND SOSIKI_CD  = :sosiki_cd";

		// 条件値指定
		$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
		$conditionVal['sosiki_cd'] = $this->_sosiki_cd;


		//大分類コード
		if ($bnri_dai_cd != null){
			$condition .= " AND BNRI_DAI_CD = :bnri_dai_cd " ;
			$conditionVal['bnri_dai_cd'] = $bnri_dai_cd;
		}
		//中分類コード
		if ($bnri_cyu_cd != null){
			$condition .= " AND BNRI_CYU_CD = :bnri_cyu_cd " ;
			$conditionVal['bnri_cyu_cd'] = $bnri_cyu_cd;
		}
		//細分類コード
		if ($bnri_sai_cd != null){
			$condition .= " AND BNRI_SAI_CD = :bnri_sai_cd " ;
			$conditionVal['bnri_sai_cd'] = $bnri_sai_cd;
		}
		//開始年月日
		if ($start_ymd_ins != null){
			$condition .= " AND YMD >= :start_ymd_ins " ;
			$conditionVal['start_ymd_ins'] = $start_ymd_ins;
		}
		//終了年月日
		if ($end_ymd_ins != null){
			$condition .= " AND YMD <= :end_ymd_ins " ;
			$conditionVal['end_ymd_ins'] = $end_ymd_ins;
		}

		/* ▼拠点情報取得 */
		$sql  = 'SELECT ';
		$sql .= "$fields ";
		$sql .= 'FROM V_WEX070_T_ABC_DB_LST ';
		$sql .= 'WHERE ';
		$sql .= $condition;
		$sql .= $conditionKey;
		
		$data = $this->queryWithLog($sql, $conditionVal, "ABCデータベース日別情報　取得");
		
		$data = self::editData($data);
	
		return $data;
	}
	
	/**
	 * カウントのチェックを行う
	 * @param unknown $conditions
	 * @return boolean true..OK
	 */
	public function checMaxCount($bnri_dai_cd, $bnri_cyu_cd,  $bnri_sai_cd, 
                                 $start_ymd_ins , $end_ymd_ins) {
                                 
		$SearchMaxCnt = $this->getMSystem();

		$ThisCount = $this->getTAbcDbLst($bnri_dai_cd, $bnri_cyu_cd,  $bnri_sai_cd, 
                                 $start_ymd_ins , $end_ymd_ins, true)[0]['COUNT'];
		
		if($ThisCount > $SearchMaxCnt) {
 			$message = vsprintf($this->MMessage->getOneMessage('CMNE000102'), array($SearchMaxCnt, $ThisCount));
			$this->errors['Check'][] = $message;
			return false;
		}

		return true;
	}
	
	/**
	 * ABCデータベース情報編集処理
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

	
	public function checkAbcDb($data,$staff_cd) {

		//　入力チェック
		try {
	
			$message = "";
			$count = 1;			//行
			$count2 = 0;		//キー重複チェック用
			$count3 = 0;		//キー重複チェック用
			$count4 = 0;		//更新対象件数取得
			
			$errorCount = 0;
			
			foreach($data as $obj) {
				
				$cellsArray = $obj->cells;
				$dataArray = $obj->data;
				 
	
				$LINE_COUNT       = $dataArray[0];
				$CHANGED          = $dataArray[1];
				$BNRI_DAI_CD      = $dataArray[2];
				$BNRI_CYU_CD      = $dataArray[3];
				$BNRI_SAI_CD      = $dataArray[4];
				$BUTURYO          = $dataArray[6];
				$SURYO_LINE       = $dataArray[7];
	
	
				//更新対象件数取得
				if ($CHANGED != '0' 
				and $CHANGED != '5') {
					$count4++;
				}
	
				//追加と修正のみ
				if ($CHANGED == '1' 
				or  $CHANGED == '2') {
	
					//数字チェック
					if ($BUTURYO == "" || $BUTURYO = null) {
						$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '物量'));
						$this->errors['Check'][] =   $message;
						
						$errorCount++;
						continue;
					}
					
					if ($SURYO_LINE == "" || $SURYO_LINE = null) {
						$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, 'ライン数量'));
						$this->errors['Check'][] =   $message;
						
						$errorCount++;
						continue;
					}
	
					//数字チェック
					if (preg_match("/^[0-9]+$/",$BUTURYO) != 0) {
						$message = vsprintf($this->MMessage->getOneMessage('WEXE070006'), array($count, '物量'));
						$this->errors['Check'][] =   $message;
						
						$errorCount++;
						continue;
					}
					
					if (preg_match("/^[0-9]+$/",$SURYO_LINE) != 0) {
						$message = vsprintf($this->MMessage->getOneMessage('WEXE070006'), array($count, 'ライン数量'));
						$this->errors['Check'][] =   $message;
						
						$errorCount++;
						continue;
					}
					
					//桁数チェック
					if (mb_strlen($BUTURYO) > 8) {
						$message = vsprintf($this->MMessage->getOneMessage('WEXE070005'), array($count, '物量', '8'));
						$this->errors['Check'][] =   $message;
						
						$errorCount++;
						continue;
					}
					
					if (mb_strlen($SURYO_LINE) > 8) {
						$message = vsprintf($this->MMessage->getOneMessage('WEXE070005'), array($count, 'ライン数量', '8'));
						$this->errors['Check'][] =   $message;
						
						$errorCount++;
						continue;
					}
	
				}
	
				$count++;
	
			}
	
			if (count($this->errors) != 0) {
				return false;
			}
	
			if ($count4 == 0) {
				$message = "更新データがありません。";
				$this->errors['Check'][] = $message;
				return false;
			}
			
			//排他チェック
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "CALL P_GET_LOCK(";
			$sql .= " '" . $staff_cd . "',";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " 'WPO',";
			$sql .= " '050',";
			$sql .= " @return";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'WPO050　排他確認');
			
			$sql = "";
			$sql .= "SELECT";
			$sql .= " @return";

		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'WPO050　排他確認');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			if ($result['@return'] != "0") {
			
				$message = $this->MMessage->getOneMessage('CMNE000105');
				$this->errors['Check'][] =  $message;
				return false;
			}
			
			$sql = "CALL P_GET_LOCK(";
			$sql .= " '" . $staff_cd . "',";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " 'WEX',";
			$sql .= " '070',";
			$sql .= " @return";
			$sql .= ")";
			
			$this->execWithPDOLog($pdo,$sql, 'WEX070　排他確認');
				
			$sql = "";
			$sql .= "SELECT";
			$sql .= " @return";
			
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'WEX070　排他確認');
				
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
				
			if ($result['@return'] != "0") {
					
				$message = $this->MMessage->getOneMessage('CMNE000105');
				$this->errors['Check'][] =  $message;
				return false;
			}
				
			$sql = "CALL P_GET_LOCK(";
			$sql .= " '" . $staff_cd . "',";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " 'WEX',";
			$sql .= " '080',";
			$sql .= " @return";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'WEX080　排他確認');
			
			$sql = "";
			$sql .= "SELECT";
			$sql .= " @return";

		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'WEX080　排他確認');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			if ($result['@return'] != "0") {
			
				$message = $this->MMessage->getOneMessage('CMNE000105');
				$this->errors['Check'][] =  $message;
				return false;
			}
			
			return true;
			
		}catch(Exception $e) {
		
			$this->printLog("fatal", "例外発生", "WEX070", $e->getMessage());
			$pdo = null;
			
		}
		
		//例外発生の場合、エラー
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;

	}


	
	public function setAbcDb($arrayList, $staff_cd) {

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
			$sql .= "'WEX',";
			$sql .= "'070',";
			$sql .= "1";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql,'WEX070 排他制御オン');

			try {

				$pdo2 = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
				$pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$pdo2->beginTransaction();
				$count = 0;

				
				//行ずつ処理
				foreach($arrayList as $obj) {

					$count++;

					$cellsArray = $obj->cells;
					$dataArray = $obj->data;


					$LINE_COUNT       = $dataArray[0];
					$CHANGED          = $dataArray[1];
					$BNRI_DAI_CD      = $dataArray[2];
					$BNRI_CYU_CD      = $dataArray[3];
					$BNRI_SAI_CD      = $dataArray[4];
					$YMD_SYORI        = $dataArray[5];
					$BUTURYO          = $dataArray[6];
					$SURYO_LINE       = $dataArray[7];
			
					//　０：なし　　　　　　何もしない
					//　１：　修正　　　　　UPDATE
					//この処理では、以下はなし
					//　２：　追加　　　　　INSERT
					//　３：　削除　　　　　DELETE
					//　４：　修正→削除　　DELETE
					//　５：　追加→削除　　何もしない

					
					//更新の場合
					if ($CHANGED == 1) {
					
						//ABCデータベースを更新する
						$sql = "CALL P_WEX070_UPD_ABC_DB(";
						$sql .= "'" . $this->_ninusi_cd . "',";
						$sql .= "'" . $this->_sosiki_cd . "',";
						$sql .= "'" . $staff_cd . "',";
						$sql .= "'" . $YMD_SYORI . "',";
						$sql .= "'" . $BNRI_DAI_CD . "',";
						$sql .= "'" . $BNRI_CYU_CD . "',";
						$sql .= "'" . $BNRI_SAI_CD . "',";
						$sql .= "'" . $BUTURYO . "',";
						$sql .= "'" . $SURYO_LINE . "',";
						$sql .= "@return_cd";
						$sql .= ")";
					
						$this->execWithPDOLog($pdo2,$sql, 'ABCデータベース　更新');
						
						$sql = "SELECT";
						$sql .= " @return_cd";
						
						$this->queryWithPDOLog($stmt,$pdo2,$sql, 'ABCデータベース　更新');
						
						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$return_cd = $result["@return_cd"];
						
						if ($return_cd == "1") {
						
							throw new Exception('ABCデータベース　更新');
						}
					
					}
							
				}
				
				$pdo2->commit();
			
			} catch (Exception $e) {
			
				$this->printLog("fatal", "例外発生", "WEX070", $e->getMessage());
				$pdo2->rollBack();
			}
			
			$pdo2 = null;
			
			//排他制御オフ
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WEX',";
			$sql .= "'070',";
			$sql .= "0";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql,'WEX070 排他制御オフ');
			
			$pdo = null;

			if ($return_cd == "1") {
				
				//例外の場合、例外メッセージ
				$message = $this->MMessage->getOneMessage('CMNE000107');
				$this->errors['Check'][] =  $message;
				return false;
			}

			return true;
		
		} catch (Exception $e) {
			
			$this->printLog("fatal", "例外発生", "WEX070", $e->getMessage());
			$pdo = null;
			
		}
		
		//例外発生の場合、エラー
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;

	}


	public function checkTimestamp($currentTimestamp) {

		$pdo = null;
	
		try{
			
			//排他制御
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "CALL P_WEX070_GET_TIMESTAMP(";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "@timestamp";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'ABCデータ　タイムスタンプ取得');
			
			$sql = "";
			$sql .= "SELECT ";
			$sql .= "@timestamp";
		
		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ABCデータ　タイムスタンプ取得');
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			$timestamp = $result['@timestamp'];
			
			if ($timestamp == null) {
				$timestamp = "1900-01-01 00:00:00";
			}
				
			$pdo = null;
			
			if (strtotime($currentTimestamp) >= strtotime($timestamp)) {
				return true;
			} else {
				
				$message =$this->MMessage->getOneMessage('CMNE000106');
				$this->errors['Check'][] = $message;
				return false;
			}

			
		} catch (Exception $e){
	
			$this->printLog("fatal", "例外発生", "WEX070", $e->getMessage());
			$pdo = null;
		    throw $e;
		}
		
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
			$sql = "CALL P_WEX070_GET_TIMESTAMP(";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "@timestamp";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'ABCデータ　タイムスタンプ取得');
			
			$sql = "";
			$sql .= "SELECT ";
			$sql .= "@timestamp";
		
		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ABCデータ　タイムスタンプ取得');
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
	 * csv読み込み
	 * @access   public
	 * @param    アップロードされたcsvファイル
	 * @return   void
	 */
	public function setAbcDbCsv($up_file,$staff_cd) {
	
		//バッチ設定一覧情報作成用ワーク
		
		$lineCount = 0;		// 行カウント
		$errorCount = 0;    // エラーカウント
		$processName = "作業工程CSV取込";
		
		/* ▼必須チェック */
		if (!is_uploaded_file($up_file['tmp_name'])){
			$this->errors['check'][] = vsprintf($this->MMessage->getOneMessage('CMNE000001'), "作業工程ファイルパス");
			return $result;
		}
	
		/* ▼拡張子チェック */
		if (!preg_match('/\.csv$/i', $up_file['name'])) {
			$this->errors['check'][] = vsprintf($this->MMessage->getOneMessage('WEXE070001'), $up_file['name']);
			return $result;
		}
	
		/* ▼ファイルサイズチェック */
		if (filesize($up_file['tmp_name']) == 0) {
			$this->errors['check'][] = vsprintf($this->MMessage->getOneMessage('WEXE070002'), $up_file['name']);
			return $result;
		}
		

		try{
		
			$file = $up_file['tmp_name'];
			$tmp = fopen($file, "r");
			
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			
			$sql = "";
			$sql .= "SELECT";
			$sql .= "   A.BNRI_DAI_CD,";
			$sql .= "   A.BNRI_CYU_CD,";
			$sql .= "   A.BNRI_SAI_CD,";
			$sql .= "   A.ROW_NO,";
			$sql .= "   A.COL_NO_LINE,";
			$sql .= "   A.COL_NO_BUTURYO,";
			$sql .= "   B.KOTEI_KBN,";
			$sql .= "   D.KBN_URIAGE,";
			$sql .= "   C.BNRI_DAI_RYAKU,";
			$sql .= "   D.BNRI_CYU_RYAKU,";
			$sql .= "   E.BNRI_SAI_RYAKU,";
			$sql .= "   E.KBN_GET_DATA";
			$sql .= " FROM";
			$sql .= "   M_KOTEI_CSV A";
			$sql .= "     LEFT JOIN M_CALC_TGT_KOTEI B ON";
			$sql .= "       A.NINUSI_CD=B.NINUSI_CD AND";
			$sql .= "       A.SOSIKI_CD=B.SOSIKI_CD AND";
			$sql .= "       A.BNRI_DAI_CD=B.BNRI_DAI_CD AND";
			$sql .= "       A.BNRI_CYU_CD=B.BNRI_CYU_CD AND";
			$sql .= "       A.BNRI_SAI_CD=B.BNRI_SAI_CD AND";
			$sql .= "       B.KOTEI_KBN=1";
			$sql .= "     LEFT JOIN M_BNRI_DAI C ON";
			$sql .= "       A.NINUSI_CD=C.NINUSI_CD AND";
			$sql .= "       A.SOSIKI_CD=C.SOSIKI_CD AND";
			$sql .= "       A.BNRI_DAI_CD=C.BNRI_DAI_CD";
			$sql .= "     LEFT JOIN M_BNRI_CYU D ON";
			$sql .= "       A.NINUSI_CD=D.NINUSI_CD AND";
			$sql .= "       A.SOSIKI_CD=D.SOSIKI_CD AND";
			$sql .= "       A.BNRI_DAI_CD=D.BNRI_DAI_CD AND";
			$sql .= "       A.BNRI_CYU_CD=D.BNRI_CYU_CD";
			$sql .= "     LEFT JOIN M_BNRI_SAI E ON";
			$sql .= "       A.NINUSI_CD=E.NINUSI_CD AND";
			$sql .= "       A.SOSIKI_CD=E.SOSIKI_CD AND";
			$sql .= "       A.BNRI_DAI_CD=E.BNRI_DAI_CD AND";
			$sql .= "       A.BNRI_CYU_CD=E.BNRI_CYU_CD AND";
			$sql .= "       A.BNRI_SAI_CD=E.BNRI_SAI_CD";
			$sql .= " WHERE";
			$sql .= "   A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   A.SOSIKI_CD='" . $this->_sosiki_cd . "'";
			$sql .= " ORDER BY";
			$sql .= "   A.ROW_NO";
			$sql .= "  ,E.KBN_GET_DATA";			
			$sql .= "  ,A.BNRI_DAI_CD";
			$sql .= "  ,A.BNRI_CYU_CD";
			$sql .= "  ,A.BNRI_SAI_CD";

			
			
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, '作業工程CSV取込設定マスタ　取得');
			
			$MKouteiCsv = array();
			
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
				
				$array = array(
				
					"BNRI_DAI_CD" => $result["BNRI_DAI_CD"],
					"BNRI_CYU_CD" => $result["BNRI_CYU_CD"],
					"BNRI_SAI_CD" => $result["BNRI_SAI_CD"],
					"ROW_NO" => $result["ROW_NO"],
					"COL_NO_LINE" => $result["COL_NO_LINE"],
					"COL_NO_BUTURYO" => $result["COL_NO_BUTURYO"],
					"KOTEI_KBN" => $result["KOTEI_KBN"],
					"KBN_URIAGE" => $result["KBN_URIAGE"],
					"BNRI_DAI_RYAKU" => $result["BNRI_DAI_RYAKU"],
					"BNRI_CYU_RYAKU" => $result["BNRI_CYU_RYAKU"],
					"BNRI_SAI_RYAKU" => $result["BNRI_SAI_RYAKU"],
					"KBN_GET_DATA" => $result["KBN_GET_DATA"]
				);
				
				$MKouteiCsv[] = $array;
			}
			
			$DbCount = 0;
			$csvCount = 0;
			$errorCount = 0;
			$keikokuCount = 0;
			
			$titleFlg = 0;
			
			$resultArray = array();
			
			while ($row = fgetcsv($tmp)) {
				
				$errorFlg = 0;
				
				//タイトル行はスキップ
				if($titleFlg == 0) {
					$titleFlg = 1;
					continue;
				}
				
				//エラー件数が100件以上の場合処理を中断
				if ($errorCount >= 100) {
					break;
				}
			
				//現在のCSVのデータ行
				$csvCount++;
			
			
				//1.作業工程CSVファイルから１行読み込み
				if (count($row) != 23) {
					$message = vsprintf( $this->MMessage->getOneMessage('WEXE070003'), array($csvCount,$up_file['name']));
					$this->errors['check'][] = $message;
					$errorCount++;
					$errorFlg = 1;
				}
				
				//2.作業工程CSV取り込み設定マスタ情報取得
				$MKoutei = array();
				foreach($MKouteiCsv as $array) {
				
					if($array["ROW_NO"] == $csvCount) {
						$MKoutei[] = $array;
					}
					
				}
				
				if (count($MKoutei) == 0) {
					continue;
				}
				
				foreach($MKoutei as $MKouteiWk) {
					
					$warnFlg = 0;
					
					//3.年月日取得
					$ymd = trim(mb_convert_encoding($row[0],"UTF-8", "SJIS-win"));
					
					//日付フォーマットチェック
					if (preg_match("/^[0-9]{8}$/",$ymd) == false) {
					
						$message = vsprintf($this->MMessage->getOneMessage('WEXE070004'), array($csvCount,"年月日"));
						$this->errors['check'][] =  $message;
						$errorCount++;
						$errorFlg = 1;
					} else {
							
						$year = substr($ymd, 0,4);
						$month = substr($ymd, 4,2);
						$day = substr($ymd, 6,2);
							
						$hizuke = (int)$year . "年" . (int)$month . "月" . (int)$day . "日";
							
						//日付チェック
						if (checkdate($month, $day, $year) == false) {
							$message = vsprintf($this->MMessage->getOneMessage('WEXE070004'), array($csvCount,"年月日"));
							$this->errors['check'][] =  $message;
							$errorCount++;
							$errorFlg = 1;
						}
					
					}
					
					//4.物量情報の取得
					$BUTURYO = str_replace(",", "", trim(mb_convert_encoding($row[$MKouteiWk["COL_NO_BUTURYO"] - 1],"UTF-8", "SJIS-win")));
					$LINE_SURYO = str_replace(",", "", trim(mb_convert_encoding($row[$MKouteiWk["COL_NO_LINE"] - 1],"UTF-8", "SJIS-win")));
					
					//数値チェック
					if (!is_numeric($BUTURYO)) {
					
						$message = vsprintf($this->MMessage->getOneMessage('WEXE070006'), array($csvCount,"物量"));
						$this->errors['check'][] = $message;
						$errorCount++;
						$errorFlg = 1;
					}
					
					//桁数チェック
					if (mb_strlen($BUTURYO) > 8) {
					
						$message = vsprintf($this->MMessage->getOneMessage('WEXE070005'), array($csvCount,"物量",8));
						$this->errors['check'][] = $message;
						$errorCount++;
						$errorFlg = 1;
					}
					
					//数値チェック
					if (!is_numeric($LINE_SURYO)) {
					
						$message = vsprintf($this->MMessage->getOneMessage('WEXE070006'), array($csvCount,"ライン数量"));
						$this->errors['check'][] = $message;
						$errorCount++;
						$errorFlg = 1;
					}
					
					//桁数チェック
					if (mb_strlen($LINE_SURYO) > 8) {
					
						$message = vsprintf($this->MMessage->getOneMessage('WEXE070005'), array($csvCount,"ライン数量",8));
						$this->errors['check'][] = $message;
						$errorCount++;
						$errorFlg = 1;
					}
					
					//5.エラー判定
					if ($errorFlg == 1) {
						break;
					}
					
					//6.工程情報取得
					if ($MKouteiWk["KOTEI_KBN"] == "1") {
						$message = vsprintf($this->MMessage->getOneMessage('WEXW070001'), array($csvCount,
								$hizuke,
								$MKouteiWk["BNRI_DAI_RYAKU"],
								$MKouteiWk["BNRI_CYU_RYAKU"],
								$MKouteiWk["BNRI_SAI_RYAKU"]));
						$this->infos['check'][] = $message;
						$keikokuCount++;
						$warnFlg = 1;
					}
					
					if ($MKouteiWk["KBN_URIAGE"] == "01") {
						$message = vsprintf($this->MMessage->getOneMessage('WEXW070002'), array($csvCount,
								$hizuke,
								$MKouteiWk["BNRI_DAI_RYAKU"],
								$MKouteiWk["BNRI_CYU_RYAKU"],
								$MKouteiWk["BNRI_SAI_RYAKU"]));
						$this->infos['check'][] = $message;
						$keikokuCount++;
						$warnFlg = 1;
					}
					
					if (($BUTURYO != 0 or $LINE_SURYO != 0) and $MKouteiWk["KBN_GET_DATA"] == "02") {
						$message = vsprintf($this->MMessage->getOneMessage('WEXW070003'), array($csvCount,
								$hizuke,
								$MKouteiWk["BNRI_DAI_RYAKU"],
								$MKouteiWk["BNRI_CYU_RYAKU"],
								$MKouteiWk["BNRI_SAI_RYAKU"]));
						$this->infos['check'][] = $message;
						$keikokuCount++;
					}						
					
					//7.警告判定
					if($warnFlg == 1) {
						continue;
					}
					
					if ($BUTURYO != 0 or $LINE_SURYO != 0) {
							
						$array = array (
								"YMD" => $ymd,
								"BNRI_DAI_CD" => $MKouteiWk["BNRI_DAI_CD"],
								"BNRI_CYU_CD" => $MKouteiWk["BNRI_CYU_CD"],
								"BNRI_SAI_CD" => $MKouteiWk["BNRI_SAI_CD"],
								"BNRI_DAI_RYAKU" => $MKouteiWk["BNRI_DAI_RYAKU"],
								"BNRI_CYU_RYAKU" => $MKouteiWk["BNRI_CYU_RYAKU"],
								"BNRI_SAI_RYAKU" => $MKouteiWk["BNRI_SAI_RYAKU"],
								"BUTURYO" => $BUTURYO,
								"LINE_SURYO" => $LINE_SURYO,
								"CSV_GYO" => $csvCount
						);
							
						$resultArray[] = $array;
					
					}
				}

			}
			
			//エラー判定
			if ($errorCount > 0) {
				
				$message1 = '';
				
				foreach($this->errors['check'] as $msg) {
					$this->printLog('error', $processName, $this->_kino_id,$msg);
				}
					
				if ($errorCount >= 100) {
				
					$message1 = vsprintf($this->MMessage->getOneMessage('WEXE070009'), array("100"));
					$this->printLog('error', $processName, $this->_kino_id,$message1);
				} else {
					$message1 = vsprintf($this->MMessage->getOneMessage('WEXE070007'), array($errorCount));
					$this->printLog('error', $processName, $this->_kino_id,$message1);
				}
				
				$daihyo = $this->errors['check'][0];
				
				$message = vsprintf($this->MMessage->getOneMessage('WEXE070008'), array($daihyo));
			
				$this->errors['check'] = array();
				$this->errors['check'][] = $message1;
				$this->errors['check'][] = $message;
				
			
				return false;
			}
	
			//更新前チェック
			
			$sql = "CALL P_GET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WPO',";
			$sql .= "'050',";
			$sql .= "@return";
			$sql .= ")";
	
			$this->execWithPDOLog($pdo,$sql, 'WPO050 排他確認');
				
			$sql = "";
			$sql .= "SELECT";
			$sql .= "@return";
	
	
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'WPO050 排他確認');
				
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
				
			if($result['@return'] != "0") {
			
				$message = $this->MMessage->getOneMessage('CMNE000105');
				$this->errors['Check'][] =  $message;
				return false;	
			}
			
			$sql = "CALL P_GET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WEX',";
			$sql .= "'070',";
			$sql .= "@return";
			$sql .= ")";
			
			$this->execWithPDOLog($pdo,$sql, 'WEX070 排他確認');
			
			$sql = "";
			$sql .= "SELECT";
			$sql .= "@return";
			
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'WEX070 排他確認');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			if($result['@return'] != "0") {
					
				$message = $this->MMessage->getOneMessage('CMNE000105');
				$this->errors['Check'][] =  $message;
				return false;
			}
			
			$sql = "CALL P_GET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WEX',";
			$sql .= "'080',";
			$sql .= "@return";
			$sql .= ")";
	
			$this->execWithPDOLog($pdo,$sql, 'WEX080 排他確認');
				
			$sql = "";
			$sql .= "SELECT";
			$sql .= "@return";
	
	
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'WEX080 排他確認');
				
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
				
			if($result['@return'] != "0") {
			
				$message = $this->MMessage->getOneMessage('CMNE000105');
				$this->errors['Check'][] =  $message;
				return false;	
			}
			
			//更新処理
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WEX',";
			$sql .= "'070',";
			$sql .= "1";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql,'WEX070 排他制御オン');
			
			$errorCount = 0;
			$kosinCount = 0;
			$updateCount = 0;
			


			try {

				$pdo2 = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
				$pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$pdo2->beginTransaction();
				
				$lineCount = 1;
				
				foreach($resultArray as $array) {

					//更新する
					$sql = "CALL P_WEX070_SET_ABC_DATA_FROM_CSV(";
					$sql .= "'" . $staff_cd           . "',";
					$sql .= "'" . $this->_ninusi_cd   . "',";
					$sql .= "'" . $this->_sosiki_cd   . "',";
					$sql .= "'" . $array['YMD']     . "',";
					$sql .= "'" . $array['BNRI_DAI_CD'] . "',";
					$sql .= "'" . $array['BNRI_CYU_CD'] . "',";
					$sql .= "'" . $array['BNRI_SAI_CD'] . "',";
					$sql .= "" . $array['BUTURYO'] . ",";
					$sql .= "" . $array['LINE_SURYO'] . ",";
					$sql .= "@return";
					$sql .= ")";
					
					$this->execWithPDOLog($pdo2,$sql, 'ABCデータベース　更新');
					
					$sql = "";
					$sql .= "SELECT";
					$sql .= "@return";
			
			
					$this->queryWithPDOLog($stmt,$pdo2,$sql, 'ABCデータベース 更新');
						
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
						
					if($result['@return'] == "2") {
					
						$year = substr($array["YMD"], 0,4);
						$month = substr($array["YMD"], 4,2);
						$day = substr($array["YMD"], 6,2);
						
						$hizuke = (int)$year . "年" . (int)$month . "月" . (int)$day . "日";
					
						$message = vsprintf($this->MMessage->getOneMessage('WEXW070004'), array($array["CSV_GYO"],
																								$hizuke,
																								$array["BNRI_DAI_RYAKU"],
																								$array["BNRI_CYU_RYAKU"],
																								$array["BNRI_SAI_RYAKU"]));
						$this->infos['check'][] = $message;
						$keikokuCount++;

					} else if ($result['@return'] == "1"){
						
						throw(new Exception("ABCデータベース更新 例外発生"));
						$errorCount++;
						
					}  else if ($result['@return'] == "0"){
						$updateCount++;
					}

				}

				// 正常終了
				$pdo2->commit();
			
			} catch (Exception $e) {
			
				$this->printLog("fatal", "例外発生", "WEX070", $e->getMessage());
				$pdo2->rollBack();
			}
			
			$pdo2 = null;
			
			//排他制御オフ
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WEX',";
			$sql .= "'070',";
			$sql .= "0";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql,'WEX070 排他制御オフ');
			
			$pdo = null;
			
			if ($errorCount > 0) {
				throw(new Exception("ABCデータベース更新 例外発生"));
			}

			
			if ($keikokuCount > 0) {			
			
				$message = vsprintf($this->MMessage->getOneMessage('WEXI070001'),$updateCount);
				array_splice($this->infos['check'], 0, 0, array($message));
				
				foreach($this->infos['check'] as $msg) {
					$this->printLog('warn', $processName, $this->_kino_id,$msg);
				}
				
				$message = vsprintf($this->MMessage->getOneMessage('WEXW070005'),$keikokuCount);
				$this->printLog('warn', $processName, $this->_kino_id,$message);
				array_splice($this->infos['check'], 1, 0, array($message));
			} else {
				
				$message = vsprintf($this->MMessage->getOneMessage('WEXI070001'),$updateCount);
				$this->infos['check'][] = $message;
			}
			
			return true;

	
		} catch(Exception $e) {
	
			$this->printLog("fatal", "例外発生", "WEX070", $e->getMessage());
			$message = $this->MMessage->getOneMessage('CMNE000107');
			$this->errors['Check'][] =  $message;
			return false;
		}
	
	
		return $result;
	}

	/**
	 * 検索可能最大件数取得
	 * @access   public
	 * @return   システムマスタ
	 */
	public function getMSystem() {
		
		$pdo = null;
		
		try{
		
			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$SearchMaxCnt = '';
			
			$sql  = "SELECT ";
			$sql .= "  SEARCH_MAX_CNT ";
			$sql .= " FROM M_SYSTEM ";
			$sql .= " WHERE ";
			$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "'";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql,"工程キー取得");
			
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
				$SearchMaxCnt =  $result['SEARCH_MAX_CNT'];
			}
			
			$pdo = null;
			
			return $SearchMaxCnt;

		} catch (Exception $e) {
			
			$message = $this->MMessage->getOneMessage('CMNE000107');
			$this->errors['Check'][] =   $message;

			$pdo = null;
		}

	}
}



?>
 