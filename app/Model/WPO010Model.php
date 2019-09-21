<?php
/**
 * WPO010Model
 *
 * バッチ編成取込
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class WPO010Model extends DCMSAppModel{
	
	public $name = 'WPO010Model';
	public $useTable = false;
	

    public $errors = array();
    public $infos = array();
    
    //取込処理カウント数
    public $batchNmLst = null;
    public $allData = null;
    
    
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
	 * Oracleより集計ピッキングデータ読み込み
	 * @access   public
	 * @param    なし
	 * @return   void
	 */
	function getOraclePickingData() {
	

		$batchLst = array();
		$lineCount = 0;	

		set_time_limit(0);
		
		try {
			
			// 抽出除外バッチコード取得
			$aryBatchCD = array();
			$aryBatchCD = $this->getExistBatchCD(); 
				
			// Oracle(WMS)接続
			$oraConn = null;
			$Oracle_String = "";
			$Oracle_String = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)" . 
										           "(HOST=" . $this->oracle_host .  ")" .
										           "(PORT=" . $this->oracle_port . ")" . 
										  ")" . 
										  "(CONNECT_DATA=(SERVICE_NAME=" . $this->oracle_sid . ")" . 
							 			  ")" . 
							 ")";
			putenv($this->oracle_home);
			
			if (!($oraConn = oci_connect($this->oracle_user_name, $this->oracle_password, $Oracle_String,"UTF8"))) {
				$e = oci_error();
				throw new Exception('Cannot connect to oracle database: ' . $e['message']);
			}
			
			//集計ピッキングデータをOracle(WMS)より抽出
			$sql = "SELECT * ";
			$sql .= "  FROM elecom.vwPickingInfo"; 
			$sql .= "  WHERE BATCH_CD NOT IN (";
			foreach($aryBatchCD as $key => $value) {
				$sql .= "'" . $value['BATCH_CD'] . "' ,";
			}
			$sql .= "'99')";
			
			$oraStmt = oci_parse($oraConn, $sql);
			oci_execute($oraStmt);
			$binRet = oci_execute($oraStmt);
			
			if (!$binRet) {
				$e = oci_error($oraStmt);
				throw new Exception('Cannot read to oracle database: ' . $e['message']);
			}
			$lineCount = 0;
			
			$this->printLog("info", "ORACLE連携", "WPO010", $sql);
			
			
			while ($rowData = oci_fetch_array($oraStmt, OCI_ASSOC+OCI_RETURN_NULLS)) {
		
				$rowErrFlg = false;
				
				$OUTBOX_MASTER_UNIT = $rowData['OUTBOX_MASTER_UNIT'];
				$INBOX_MASTER_UNIT = $rowData['INBOX_MASTER_UNIT'];
				
				$SURYO_TOTAL = $rowData['TOTAL_NUM'];
				$ITEM_SURYO_HAKO_SOTO = 0;
				
				if ($OUTBOX_MASTER_UNIT != 0) {
				
					$ITEM_SURYO_HAKO_SOTO = floor($SURYO_TOTAL / $OUTBOX_MASTER_UNIT);
				}
				
				$ITEM_SURYO_HAKO_UCHI = 0;
				
				if ($INBOX_MASTER_UNIT != 0) {
				
					$ITEM_SURYO_HAKO_UCHI = floor(($SURYO_TOTAL - $ITEM_SURYO_HAKO_SOTO * $OUTBOX_MASTER_UNIT) / $INBOX_MASTER_UNIT);
				}
				
				
				$SURYO_BARA = $SURYO_TOTAL - $ITEM_SURYO_HAKO_SOTO * $OUTBOX_MASTER_UNIT - $ITEM_SURYO_HAKO_UCHI * $INBOX_MASTER_UNIT;
				
				
				$array = array(
					"YMD_SYORI" => $rowData['YMD_SYUKKA'],
					"YMD_UNYO" => $rowData['YMD_UNYO'],
					"BATCH_CD" => $rowData['BATCH_CD'],
					"S_BATCH_NO" => $rowData['S_BATCH_NO'],
					"S_BATCH_NO_CD" => $rowData['S_BATCH_NO_CD'],
					"UPD_CNT_CD" => $rowData['UPDATE_CD'],
					"SORTER_CD" => $rowData['SORTER_CD'],
					"ZONE" => $rowData['ZONE_CD'],
					"S_KANRI_NO" => $rowData['PICKING_NO'],
					"SURYO_HAKO_SOTO" => $rowData['OUTBOX_UNIT'],
					"SURYO_HAKO_UCHI" => $rowData['INBOX_UNIT'],
					"SURYO_ITEM" => $rowData['ITEM_NUM'],
					"SURYO_PIECE" => $rowData['PIECE_NUM'],
					"LOCATION" => $rowData['LOCATION'],
					"ITEM_CD" => $rowData['SKU_CD'],
					"ITEM_NM" => mb_convert_encoding($rowData['ITEM_NAME'],"UTF-8"),
					"ITEM_HAKO_SOTO_CD" => $rowData['OUTBOX_ITF'],
					"ITEM_HAKO_UCHI_CD" => $rowData['INBOX_ITF'],
					"ITEM_BARA_CD" => $rowData['JAN_CD'],
					"ITEM_SURYO_HAKO_SOTO" => $ITEM_SURYO_HAKO_SOTO,
					"ITEM_SURYO_HAKO_UCHI" => $ITEM_SURYO_HAKO_UCHI,
					"SURYO_BARA" => $SURYO_BARA,
					"SURYO_TOTAL" => $rowData['TOTAL_NUM'],
					"IRI_HAKO_SOTO_CD" => $OUTBOX_MASTER_UNIT,
					"IRI_HAKO_UCHI_CD" => $INBOX_MASTER_UNIT,
					"OUTBOX_MASTER_UNIT" => $OUTBOX_MASTER_UNIT,
					"INBOX_MASTER_UNIT" => $INBOX_MASTER_UNIT,
				
				);
				
				$batchLst[] = $array;
			}

			oci_free_statement($oraStmt);
			oci_close($oraConn);
			//error_log(print_r($batchLst,true),3,"log.txt");
			$resultArray = array();
			$unyoArray = array();
			
			foreach ($batchLst as $batchData) {
			
				$array = array (
				
					"YMD_UNYO" => $batchData["YMD_UNYO"],
					"UPD_CNT_CD" => $batchData["UPD_CNT_CD"]
				);
				
				$unyoArray[] = $array;
				
			}
			
			$tmp = array();
			
			foreach($unyoArray as $val) {
			
				if (!in_array($val, $tmp)) {
				
					$tmp[] = $val;
				}
			}
			
			//データからバッチNo
			$unyoArray = $tmp;

			foreach($unyoArray as $unyoLst) {
				
				$YMD_UNYO = $unyoLst["YMD_UNYO"];
				$UPD_CNT_CD = $unyoLst["UPD_CNT_CD"];
			
				//データからバッチNo
				$batchArray = array();
				
				foreach ($batchLst as $batchData) {
				
					if ($YMD_UNYO == $batchData["YMD_UNYO"] &&
					    $UPD_CNT_CD == $batchData["UPD_CNT_CD"]) {
				
						$array = array (
						
							"S_BATCH_NO_CD" => $batchData["S_BATCH_NO_CD"],
							"YMD_SYORI" => $batchData["YMD_SYORI"],
						);
						
						$batchArray[] = $array;
					
					}
					
				}
				
				$tmp = array();
				
				foreach($batchArray as $val) {
				
					if (!in_array($val, $tmp)) {
					
						$tmp[] = $val;
					}
				}
				
				$batchArray = $tmp;
			
				foreach($batchArray as $primaryArray) {
				
					$S_BATCH_NO_CD = $primaryArray["S_BATCH_NO_CD"];
					$YMD_SYORI = $primaryArray["YMD_SYORI"];
				
						
					//ピッキングリスト数を数える
					$skanriArray = array();
					
					foreach($batchLst as $batchData) {
					
						if ($YMD_UNYO == $batchData["YMD_UNYO"] &&
					    	$UPD_CNT_CD == $batchData["UPD_CNT_CD"] &&
					    	$S_BATCH_NO_CD == $batchData["S_BATCH_NO_CD"] &&
							$YMD_SYORI == $batchData["YMD_SYORI"]) {
							
							$skanriArray[] = $batchData["S_KANRI_NO"];
						}
					
					}
					
					$skanriArray = array_unique($skanriArray);
					
					$PICKING_NUM = count($skanriArray);
					
					//ピース数
					$TOTAL_PIECE_NUM = 0;
					

					foreach($batchLst as $batchData) {
				
						if ($YMD_UNYO == $batchData["YMD_UNYO"] &&
				    		$UPD_CNT_CD == $batchData["UPD_CNT_CD"] &&
				    		$S_BATCH_NO_CD == $batchData["S_BATCH_NO_CD"] &&
				    		$YMD_SYORI == $batchData["YMD_SYORI"]) {
							
							$TOTAL_PIECE_NUM += $batchData["SURYO_TOTAL"];
						}
					
					}

					
					//$S_BATCH_NO
					$S_BATCH_NO = "";
				
					foreach($batchLst as $batchData) {
						
						if ($YMD_UNYO == $batchData["YMD_UNYO"] &&
					    	$UPD_CNT_CD == $batchData["UPD_CNT_CD"] &&
					    	$S_BATCH_NO_CD == $batchData["S_BATCH_NO_CD"] &&
							$YMD_SYORI == $batchData["YMD_SYORI"]) {
						
							$S_BATCH_NO = $batchData["S_BATCH_NO"];
							
							break;
						}
						
					}
					
					$array = array(
					
						"YMD_SYORI" => $YMD_SYORI,
						"YMD_UNYO" => $YMD_UNYO,
						"S_BATCH_NO" => $S_BATCH_NO,
						"S_BATCH_NO_CD" => $S_BATCH_NO_CD,
						"UPD_CNT_CD" => $UPD_CNT_CD,
						"PICKING_NUM" => $PICKING_NUM,
						"TOTAL_PIECE_NUM" => $TOTAL_PIECE_NUM,
						"BATCH_NM" => "",
						"BATCH_NM_CD" => "",
						"CHANGE" => "0",
						"UPD_CNT_CD" => $UPD_CNT_CD
					);
					
					$resultArray[] = $array;
				}
				
			}
		
			$this->batchNmLst = $resultArray;
			$this->allData = $batchLst;
		
			
			return true;
		
		} catch (Exception $e) {
		
			$this->printLog("fatal", "例外発生", "WPO010", $e->getMessage());
		}
		
		// Oracle(WMS)クローズ
		oci_free_statement($oraStmt);
		oci_close($oraConn);
		
		
		//例外発生の場合、エラー
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
			
		
	}
	
	/**
	 * MySQLより集計ピッキングデータ読み込み(テスト用モジュール)
	 * @access   public
	 * @param    なし
	 * @return   void
	 */
	function getMySQLPickingData() {
	
	
		$batchLst = array();
		$lineCount = 0;
	
		set_time_limit(0);
	
		try {
				
			// 抽出除外バッチコード取得
			$aryBatchCD = array();
			$aryBatchCD = $this->getExistBatchCD();
	
			// MySQLテストデータ接続
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

							
			//集計ピッキングデータをOracle(WMS)より抽出
			$sql = "SELECT * ";
			$sql .= "  FROM vwPickingInfo";
			$sql .= "  WHERE BATCH_CD NOT IN (";
			foreach($aryBatchCD as $key => $value) {
				$sql .= "'" . $value['BATCH_CD'] . "' ,";
			}
			$sql .= "'99')";
				
			$this->queryWithPDOLog($stmt,$pdo,$sql,"存在バッチコード");

			$arrayList = array();

			while($rowData = $stmt->fetch(PDO::FETCH_ASSOC)){
				
				$rowErrFlg = false;
	
				$OUTBOX_MASTER_UNIT = $rowData['OUTBOX_MASTER_UNIT'];
				$INBOX_MASTER_UNIT = $rowData['INBOX_MASTER_UNIT'];
	
				$SURYO_TOTAL = $rowData['TOTAL_NUM'];
				$ITEM_SURYO_HAKO_SOTO = 0;
	
				if ($OUTBOX_MASTER_UNIT != 0) {
	
					$ITEM_SURYO_HAKO_SOTO = floor($SURYO_TOTAL / $OUTBOX_MASTER_UNIT);
				}
	
				$ITEM_SURYO_HAKO_UCHI = 0;
	
				if ($INBOX_MASTER_UNIT != 0) {
	
					$ITEM_SURYO_HAKO_UCHI = floor(($SURYO_TOTAL - $ITEM_SURYO_HAKO_SOTO * $OUTBOX_MASTER_UNIT) / $INBOX_MASTER_UNIT);
				}
	
	
				$SURYO_BARA = $SURYO_TOTAL - $ITEM_SURYO_HAKO_SOTO * $OUTBOX_MASTER_UNIT - $ITEM_SURYO_HAKO_UCHI * $INBOX_MASTER_UNIT;
	
	
				$array = array(
						"YMD_SYORI" => $rowData['YMD_SYUKKA'],
						"YMD_UNYO" => $rowData['YMD_UNYO'],
						"BATCH_CD" => $rowData['BATCH_CD'],
						"S_BATCH_NO" => $rowData['S_BATCH_NO'],
						"S_BATCH_NO_CD" => $rowData['S_BATCH_NO_CD'],
						"UPD_CNT_CD" => $rowData['UPDATE_CD'],
						"SORTER_CD" => $rowData['SORTER_CD'],
						"ZONE" => $rowData['ZONE_CD'],
						"S_KANRI_NO" => $rowData['PICKING_NO'],
						"SURYO_HAKO_SOTO" => $rowData['OUTBOX_UNIT'],
						"SURYO_HAKO_UCHI" => $rowData['INBOX_UNIT'],
						"SURYO_ITEM" => $rowData['ITEM_NUM'],
						"SURYO_PIECE" => $rowData['PIECE_NUM'],
						"LOCATION" => $rowData['LOCATION'],
						"ITEM_CD" => $rowData['SKU_CD'],
						"ITEM_NM" => mb_convert_encoding($rowData['ITEM_NAME'],"UTF-8"),
						"ITEM_HAKO_SOTO_CD" => $rowData['OUTBOX_ITF'],
						"ITEM_HAKO_UCHI_CD" => $rowData['INBOX_ITF'],
						"ITEM_BARA_CD" => $rowData['JAN_CD'],
						"ITEM_SURYO_HAKO_SOTO" => $ITEM_SURYO_HAKO_SOTO,
						"ITEM_SURYO_HAKO_UCHI" => $ITEM_SURYO_HAKO_UCHI,
						"SURYO_BARA" => $SURYO_BARA,
						"SURYO_TOTAL" => $rowData['TOTAL_NUM'],
						"IRI_HAKO_SOTO_CD" => $OUTBOX_MASTER_UNIT,
						"IRI_HAKO_UCHI_CD" => $INBOX_MASTER_UNIT,
						"OUTBOX_MASTER_UNIT" => $OUTBOX_MASTER_UNIT,
						"INBOX_MASTER_UNIT" => $INBOX_MASTER_UNIT,
	
				);
	
				$batchLst[] = $array;
			}
	
			$pdo = null;
			$resultArray = array();
			$unyoArray = array();
				
			foreach ($batchLst as $batchData) {
					
				$array = array (
	
						"YMD_UNYO" => $batchData["YMD_UNYO"],
						"UPD_CNT_CD" => $batchData["UPD_CNT_CD"]
				);
	
				$unyoArray[] = $array;
	
			}
				
			$tmp = array();
				
			foreach($unyoArray as $val) {
					
				if (!in_array($val, $tmp)) {
	
					$tmp[] = $val;
				}
			}
				
			//データからバッチNo
			$unyoArray = $tmp;
	
			foreach($unyoArray as $unyoLst) {
	
				$YMD_UNYO = $unyoLst["YMD_UNYO"];
				$UPD_CNT_CD = $unyoLst["UPD_CNT_CD"];
					
				//データからバッチNo
				$batchArray = array();
	
				foreach ($batchLst as $batchData) {
	
					if ($YMD_UNYO == $batchData["YMD_UNYO"] &&
							$UPD_CNT_CD == $batchData["UPD_CNT_CD"]) {
	
						$array = array (
	
								"S_BATCH_NO_CD" => $batchData["S_BATCH_NO_CD"],
								"YMD_SYORI" => $batchData["YMD_SYORI"],
						);
	
						$batchArray[] = $array;
							
					}
						
				}
	
				$tmp = array();
	
				foreach($batchArray as $val) {
	
					if (!in_array($val, $tmp)) {
							
						$tmp[] = $val;
					}
				}
	
				$batchArray = $tmp;
					
				foreach($batchArray as $primaryArray) {
	
					$S_BATCH_NO_CD = $primaryArray["S_BATCH_NO_CD"];
					$YMD_SYORI = $primaryArray["YMD_SYORI"];
	
	
					//ピッキングリスト数を数える
					$skanriArray = array();
						
					foreach($batchLst as $batchData) {
							
						if ($YMD_UNYO == $batchData["YMD_UNYO"] &&
								$UPD_CNT_CD == $batchData["UPD_CNT_CD"] &&
								$S_BATCH_NO_CD == $batchData["S_BATCH_NO_CD"] &&
								$YMD_SYORI == $batchData["YMD_SYORI"]) {
								
							$skanriArray[] = $batchData["S_KANRI_NO"];
						}
							
					}
						
					$skanriArray = array_unique($skanriArray);
						
					$PICKING_NUM = count($skanriArray);
						
					//ピース数
					$TOTAL_PIECE_NUM = 0;
						
	
					foreach($batchLst as $batchData) {
	
						if ($YMD_UNYO == $batchData["YMD_UNYO"] &&
								$UPD_CNT_CD == $batchData["UPD_CNT_CD"] &&
								$S_BATCH_NO_CD == $batchData["S_BATCH_NO_CD"] &&
								$YMD_SYORI == $batchData["YMD_SYORI"]) {
								
							$TOTAL_PIECE_NUM += $batchData["SURYO_TOTAL"];
						}
							
					}
	
						
					//$S_BATCH_NO
					$S_BATCH_NO = "";
	
					foreach($batchLst as $batchData) {
	
						if ($YMD_UNYO == $batchData["YMD_UNYO"] &&
								$UPD_CNT_CD == $batchData["UPD_CNT_CD"] &&
								$S_BATCH_NO_CD == $batchData["S_BATCH_NO_CD"] &&
								$YMD_SYORI == $batchData["YMD_SYORI"]) {
	
							$S_BATCH_NO = $batchData["S_BATCH_NO"];
								
							break;
						}
	
					}
						
					$array = array(
								
							"YMD_SYORI" => $YMD_SYORI,
							"YMD_UNYO" => $YMD_UNYO,
							"S_BATCH_NO" => $S_BATCH_NO,
							"S_BATCH_NO_CD" => $S_BATCH_NO_CD,
							"UPD_CNT_CD" => $UPD_CNT_CD,
							"PICKING_NUM" => $PICKING_NUM,
							"TOTAL_PIECE_NUM" => $TOTAL_PIECE_NUM,
							"BATCH_NM" => "",
							"BATCH_NM_CD" => "",
							"CHANGE" => "0",
							"UPD_CNT_CD" => $UPD_CNT_CD
					);
						
					$resultArray[] = $array;
				}
	
			}
	
			$this->batchNmLst = $resultArray;
			$this->allData = $batchLst;
	
				
			return true;
	
		} catch (Exception $e) {
	
			$this->printLog("fatal", "例外発生", "WPO010", $e->getMessage());
		}
	
		// Oracle(WMS)クローズ
		oci_free_statement($oraStmt);
		oci_close($oraConn);
	
	
		//例外発生の場合、エラー
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
			
	
	}
	
	
	/**
	 * ピッキングデータ設定
	 * @access   public
	 * @param    アップロードされたcsvファイル
	 * @return   void
	 */
	function setPickData($staff_cd,$batchLst,$batchNmLst) {
		
		set_time_limit(0);
		
		//重複チェック
		foreach($batchNmLst as $array) {
			
			$LINE_COUNT = $array->LINE_COUNT;
			$BATCH_NM_CD = $array->BATCH_NM_CD;

			
			if (empty($BATCH_NM_CD)) {
			
				$msgStr = vsprintf($this->MMessage->getOneMessage('WPOE010010'),	array($LINE_COUNT));
				$this->errors['batch_nm'][] = $msgStr;
			}			

		}
					
		if (!empty($this->errors['batch_nm'])) {
			return false;
		}
		
		//TPM040の排他確認
		$lock_flg = $this->checkTPM040($staff_cd);
				
		if ($lock_flg == 1) {
			$msgStr = $this->MMessage->getOneMessage('WPOE010012');
			$this->printLog('error', Configure::read("LogAccessMenu.csv"), "WPO010", $msgStr);
			$this->errors['batch_nm'][] = $msgStr;
			return false;
		}
		
		
		$result_return_cd = false;
		$pdo = null;

		try {
		
			$sem_id = sem_get(5123412);
			sem_acquire($sem_id);
			
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$pdo->beginTransaction();
			
			// 排他フラグ更新【START】
			// 全体進捗優先変更側で更新ができないようにする
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WPO',";
			$sql .= "'010',";
			$sql .= "1";
			$sql .= ")";
			
			$this->execWithPDOLog($pdo,$sql,"WPO010 排他制御オン");
			
			$lineCount = 1;
			$errorCount = 0;
			
			//データからカウント
			$unyoArray = array();
			
			foreach ($batchLst as $batchData) {
			
				$array = array (
				
					"YMD_UNYO" => $batchData["YMD_UNYO"],
					"UPD_CNT_CD" => $batchData["UPD_CNT_CD"]
				);
				
				$unyoArray[] = $array;
				
			}
			
			$tmp = array();
			
			foreach($unyoArray as $val) {
			
				if (!in_array($val, $tmp)) {
				
					$tmp[] = $val;
				}
			}
			
			//データからバッチNo
			$unyoArray = $tmp;
			$batchCount = 1;
			
			foreach($unyoArray as $unyoLst) {
				
				$YMD_UNYO = $unyoLst["YMD_UNYO"];
				$UPD_CNT_CD = $unyoLst["UPD_CNT_CD"];
				
				
				//更新コード重複チェック
				$sql = "";
				$sql .= "SELECT";
				$sql .= "    COUNT(*) AS COUNT";
				$sql .= "  FROM";
				$sql .= "    T_PICK_BATCH";
				$sql .= "  WHERE";
				$sql .= "    NINUSI_CD='" . $this->_ninusi_cd . "' AND";
				$sql .= "    SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
				$sql .= "    YMD_UNYO='" . $YMD_UNYO . "' AND";
				$sql .= "    UPD_CNT_CD='" . $UPD_CNT_CD . "'";
		
		
				$this->queryWithPDOLog($stmt,$pdo,$sql, 'バッチ存在　確認');
					
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				
				$countBatch = $result['COUNT'];
				$updateCntErrorFlg = 0;
				
				if ($countBatch != "0") {
				
					$updateCntErrorFlg = 1;
				}				
				
				$batchArray = array();
				
				//バッチリストを作る
				foreach ($batchLst as $batchData) {
				
					if ($YMD_UNYO == $batchData["YMD_UNYO"] &&
					    $UPD_CNT_CD == $batchData["UPD_CNT_CD"]) {
				
						$array = array (
						
							"S_BATCH_NO_CD" => $batchData["S_BATCH_NO_CD"],
							"YMD_SYORI" => $batchData["YMD_SYORI"],
						);
						
						$batchArray[] = $array;
					
					}
				}
				
				$tmp = array();
				
				foreach($batchArray as $val) {
				
					if (!in_array($val, $tmp)) {
					
						$tmp[] = $val;
					}
				}
				
				$batchArray = $tmp;
				
				//バッチ毎
				foreach($batchArray as $primaryArray) {
				
					$S_BATCH_NO_CD = $primaryArray["S_BATCH_NO_CD"];
					$YMD_SYORI = $primaryArray["YMD_SYORI"];
					
					if ($updateCntErrorFlg == 1) {
					
						// 重複エラー時
						$this->printLog('info', Configure::read("LogAccessMenu.pick"), "WPO010", $sql);
						$msgStr = vsprintf($this->MMessage->getOneMessage('WPOI010002'),array($batchCount,
																							  $YMD_SYORI,
																							  $S_BATCH_NO_CD));
				
						$this->infos['check'][] = $msgStr;
						
						$batchCount++;
						continue;
					}
					
					//バッチ重複チェック
					$sql = "";
					$sql .= "SELECT";
					$sql .= "    COUNT(*) AS COUNT";
					$sql .= "  FROM";
					$sql .= "    T_PICK_BATCH";
					$sql .= "  WHERE";
					$sql .= "    NINUSI_CD='" . $this->_ninusi_cd . "' AND";
					$sql .= "    SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
					$sql .= "    YMD_SYORI='" . $YMD_SYORI . "' AND";
					$sql .= "    S_BATCH_NO_CD='" . $S_BATCH_NO_CD . "'";
			
			
					$this->queryWithPDOLog($stmt,$pdo,$sql, 'バッチ存在　確認');
						
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
					
					$countBatch = $result['COUNT'];
				
					if ($countBatch != "0") {
					
						// 重複エラー時
						$this->printLog('fatal', Configure::read("LogAccessMenu.pick"), "WPO010", $sql);
						$msgStr = vsprintf($this->MMessage->getOneMessage('WPOE010011'),
								array($batchCount,
									  $YMD_SYORI,
									  $S_BATCH_NO_CD));
				
						$this->errors['check'][] = $msgStr;
						$errorCount += 1;
						
						$batchCount++;
						continue;	
					}
					
					//バッチネームCD取得
					$BATCH_NM_CD = "";
						
					foreach($batchNmLst as $batchNm ) {
						
						if ($batchNm->YMD_UNYO == $YMD_UNYO &&
							$batchNm->UPD_CNT_CD == $UPD_CNT_CD &&
							$batchNm->YMD_SYORI == $YMD_SYORI &&
						    $batchNm->S_BATCH_NO_CD == $S_BATCH_NO_CD) {
							
							$BATCH_NM_CD = $batchNm->BATCH_NM_CD;
						}
					}
						
					//集権管理Noリスト
					$skanriArray = array();
					
					foreach($batchLst as $batchData) {
					
						if ($YMD_UNYO == $batchData["YMD_UNYO"] &&
					    	$UPD_CNT_CD == $batchData["UPD_CNT_CD"] &&
					    	$S_BATCH_NO_CD == $batchData["S_BATCH_NO_CD"] &&
							$YMD_SYORI == $batchData["YMD_SYORI"]) {
							
							$skanriArray[] = $batchData["S_KANRI_NO"];
						}
					
					}
					
					$skanriArray = array_unique($skanriArray);
				
					sort($skanriArray);

					//集計管理NO毎
					foreach($skanriArray as $S_KANRI_NO) {
					
						//数を集計
						$SURYO_PICE = 0;
						$SURYO_ITEM = 0;
						$SURYO_HAKO_SOTO = 0;
						$SURYO_HAKO_UCHI = 0;
						
						foreach($batchLst as $batchData) {
					
							if ($YMD_UNYO == $batchData["YMD_UNYO"] &&
					    		$UPD_CNT_CD == $batchData["UPD_CNT_CD"] &&
					    		$S_BATCH_NO_CD == $batchData["S_BATCH_NO_CD"] &&
					    		$S_KANRI_NO == $batchData["S_KANRI_NO"] &&
					    		$YMD_SYORI == $batchData["YMD_SYORI"]) {
								
								$SURYO_PICE += $batchData["SURYO_TOTAL"];
								$SURYO_ITEM++;
								$SURYO_HAKO_SOTO += $batchData["ITEM_SURYO_HAKO_SOTO"];
								$SURYO_HAKO_UCHI += $batchData["ITEM_SURYO_HAKO_UCHI"];
							}
						
						}
					
						
						foreach($batchLst as $batch) {
						
							if ($YMD_UNYO == $batch["YMD_UNYO"] &&
					    		$UPD_CNT_CD == $batch["UPD_CNT_CD"] &&
					    		$S_BATCH_NO_CD == $batch["S_BATCH_NO_CD"] &&
							 	$batch["S_KANRI_NO"] == $S_KANRI_NO &&
							 	$batch["YMD_SYORI"] == $YMD_SYORI) {

								
								$sql = "CALL P_WPO010_SET_PICK_DATA(";
								$sql .= "'" . $staff_cd . "',";
								$sql .= "'" . $this->_ninusi_cd . "',";
								$sql .= "'" . $this->_sosiki_cd . "',";
								$sql .= "'" . $batch['YMD_SYORI'] . "',";
								$sql .= "'" . $batch['YMD_UNYO'] . "',";
								$sql .= "'" . $batch['BATCH_CD'] . "',";
								$sql .= "'" . $batch['S_BATCH_NO_CD'] . "',";
								$sql .= "'" . $batch['S_BATCH_NO'] . "',";
								$sql .= "'" . $batch['UPD_CNT_CD'] . "',";
								$sql .= "'" . $BATCH_NM_CD . "',";
								$sql .= "NULL,";
								$sql .= "'" . $batch['ZONE'] . "',";
								$sql .= "'" . $batch['S_KANRI_NO'] . "',";
								$sql .= $SURYO_HAKO_SOTO . ",";
								$sql .= $SURYO_HAKO_UCHI . ",";
								$sql .= $SURYO_ITEM . ",";
								$sql .= $SURYO_PICE . ",";
								$sql .= ($batch['LOCATION'] != "" ? "'" . $batch['LOCATION'] . "'," : "NULL,");
								$sql .= ($batch['ITEM_CD'] != "" ? "'" . $batch['ITEM_CD'] . "'," : "NULL,");
								$sql .= ($batch['ITEM_NM'] != "" ? "'" . Sanitize::clean(TRIM($batch['ITEM_NM'])) . "'," : "NULL,");
								$sql .= ($batch['ITEM_HAKO_SOTO_CD'] != "" ? "'" . $batch['ITEM_HAKO_SOTO_CD'] . "'," : "NULL,");
								$sql .= ($batch['ITEM_HAKO_UCHI_CD'] != "" ? "'" . $batch['ITEM_HAKO_UCHI_CD'] . "'," : "NULL,");
								if ('00' == substr($batch['ITEM_BARA_CD'], 0, 2)) {
									$sql .= ($batch['ITEM_BARA_CD'] != "" ? "'" . substr($batch['ITEM_BARA_CD'], 1, 12) . "'," : "NULL,");
								} else {
									$sql .= ($batch['ITEM_BARA_CD'] != "" ? "'" . $batch['ITEM_BARA_CD'] . "'," : "NULL,");
								}
								$sql .= ($batch['ITEM_SURYO_HAKO_SOTO'] != "" || $batch['ITEM_SURYO_HAKO_SOTO'] == null ? $batch['ITEM_SURYO_HAKO_SOTO'] . "," : "0,");
								$sql .= ($batch['ITEM_SURYO_HAKO_UCHI'] != "" || $batch['ITEM_SURYO_HAKO_UCHI'] == null ? $batch['ITEM_SURYO_HAKO_UCHI'] . "," : "0,");
								$sql .= ($batch['IRI_HAKO_SOTO_CD'] != "" || $batch['IRI_HAKO_SOTO_CD'] == null ? $batch['IRI_HAKO_SOTO_CD'] . "," : "0,");
								$sql .= ($batch['IRI_HAKO_UCHI_CD'] != "" || $batch['IRI_HAKO_UCHI_CD'] == null ? $batch['IRI_HAKO_UCHI_CD'] . "," : "0,");
								$sql .= ($batch['SURYO_BARA'] != "" || $batch['SURYO_BARA'] == null ? $batch['SURYO_BARA'] . "," : "0,");
								$sql .= ($batch['SURYO_TOTAL'] != "" ? $batch['SURYO_TOTAL'] . "," : "NULL,");
								$sql .= "@return_cd";
								$sql .= ")";
								
								$this->execWithPDOLog($pdo,$sql, Configure::read("LogAccessMenu.pick"));
								
								$sql = "SELECT";
								$sql .= " @return_cd";
								
								$this->queryWithPDOLog($stmt,$pdo,$sql, Configure::read("LogAccessMenu.pick"));
								
								$result = $stmt->fetch(PDO::FETCH_ASSOC);
								$return_cd = $result["@return_cd"];
								
								if ($return_cd == 1) {
									//　その他エラー時
									$this->errors['error'][] = $this->MMessage->getOneMessage('CMNE000107');
									$errorCount += 1;
								}
								
								$lineCount += 1;
							}	
						}
					}
					
					$batchCount++;

					// ★★★３ロケ集約機能追加★★★
					$updatestaff = "3locsum";
					// 名称マスタより変換テーブルを配列に保存
					$sql = "";
					$sql .= "SELECT";
					$sql .= "   MEI_CD,MEI_1";
					$sql .= "  FROM";
					$sql .= "    M_MEISYO";
					$sql .= " WHERE ";
					$sql .= "    NINUSI_CD='" . $this->_ninusi_cd . "' AND";
					$sql .= "    SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
					$sql .= "    MEI_KBN='40'";
					$this->queryWithPDOLog($stmt,$pdo,$sql,"３ロケ集約");
					$trantable = array();
					while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
							$array = array(
								"THIRDLOC" => $result["MEI_CD"],
								"SECONDLOC" => $result["MEI_1"]
									);
							$trantable[] = $array;
					}
					// 取込対象Sバッチ№より３ロケ集計管理番号を取得し、１集計管理番号ずつ処理を行う
					$sql = "";
					$sql .= "SELECT";
					$sql .= "   DISTINCT S_KANRI_NO";
					$sql .= "  FROM";
					$sql .= "    T_PICK_S_KANRI";
					$sql .= "  WHERE";
					$sql .= "    NINUSI_CD='" . $this->_ninusi_cd . "' AND";
					$sql .= "    SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
					$sql .= "    YMD_SYORI='" . $YMD_SYORI . "' AND";
					$sql .= "    S_BATCH_NO_CD='" . $S_BATCH_NO_CD . "' AND";
					$sql .= "    ZONE LIKE '3%'";
						
					$this->queryWithPDOLog($stmt,$pdo,$sql,"３ロケ集約");
					$arrayList = array();
					while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
		
						// 更新前３ロケ「T_PICK_S_KANRI」のバックアップ
						$sql = "";
						$sql = "INSERT INTO T_PICK_S_KANRI_BEFORE_UPDATE ";
						$sql .= "SELECT";
						$sql .= "   *";
						$sql .= "  FROM";
						$sql .= "    T_PICK_S_KANRI";
						$sql .= "  WHERE";
						$sql .= "    NINUSI_CD='" . $this->_ninusi_cd . "' AND";
						$sql .= "    SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
						$sql .= "    YMD_SYORI='" . $YMD_SYORI . "' AND";
						$sql .= "    S_BATCH_NO_CD='" . $S_BATCH_NO_CD . "' AND";
						$sql .= "    S_KANRI_NO='" . $result['S_KANRI_NO'] . "'";
						$this->execWithPDOLog($pdo,$sql, 'WPO010　更新前３ロケ：T_PICK_S_KANRI' );
						
						// ３ロケ「T_PICK_ITEM」を１行ずつ処理
						$sql = "";
						$sql .= "SELECT *";
						$sql .= "  FROM";
						$sql .= "    T_PICK_ITEM";
						$sql .= "  WHERE";
						$sql .= "    NINUSI_CD='" . $this->_ninusi_cd . "' AND";
						$sql .= "    SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
						$sql .= "    YMD_SYORI='" . $YMD_SYORI . "' AND";
						$sql .= "    S_BATCH_NO_CD='" . $S_BATCH_NO_CD . "' AND";
						$sql .= "    S_KANRI_NO='" . $result['S_KANRI_NO'] . "'";
						$this->queryWithPDOLog($stmt1,$pdo,$sql,"３ロケ集約");
						while($rowdata3loc = $stmt1->fetch(PDO::FETCH_ASSOC)){

							// 変換する２ロケゾーンを探索
							$retStatus = $this->search2LocZone($rowdata3loc["LOCATION"], $trantable);
							// 変換対象となる２ロケゾーンが名称マスタに存在する
							if ($retStatus!=false) {
								
								// ２ロケ「T_PICK_ITEM」のデータ存在チェック
								$sql = "";
								$sql .= "SELECT S_KANRI_NO,PICK_NO,ITEM_CD";
								$sql .= "  FROM";
								$sql .= "    T_PICK_ITEM";
								$sql .= "  WHERE";
								$sql .= "    NINUSI_CD='" . $this->_ninusi_cd . "' AND";
								$sql .= "    SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
								$sql .= "    YMD_SYORI='" . $YMD_SYORI . "' AND";
								$sql .= "    S_BATCH_NO_CD='" . $S_BATCH_NO_CD . "' AND";
								$sql .= "    LOCATION LIKE '" . $retStatus . "%' AND";
								$sql .= "    ITEM_CD = '" . $rowdata3loc["ITEM_CD"] . "'";
								$this->queryWithPDOLog($stmt2,$pdo,$sql,"３ロケ集約");
								$rowdata2loc = $stmt2->fetch(PDO::FETCH_ASSOC);
								
								if ($rowdata2loc <> false) {
									// 変換対象となる２ロケ「T_PICK_ITEM」にアイテムが存在するので更新処理を行う
									$sql = "";
									$sql .= "INSERT INTO T_PICK_ITEM_2LOC_BEFORE ";
									$sql .= "  SELECT * ";
									$sql .= "  FROM";
									$sql .= "    T_PICK_ITEM";
									$sql .= "  WHERE";
									$sql .= "    NINUSI_CD='" . $this->_ninusi_cd . "' AND";
									$sql .= "    SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
									$sql .= "    YMD_SYORI='" . $YMD_SYORI . "' AND";
									$sql .= "    S_BATCH_NO_CD='" . $S_BATCH_NO_CD . "' AND";
									$sql .= "    S_KANRI_NO = '" . $rowdata2loc['S_KANRI_NO'] . "' AND";
									$sql .= "    PICK_NO = '" . $rowdata2loc['PICK_NO'] . "' AND";
									$sql .= "    ITEM_CD = '" . $rowdata2loc['ITEM_CD'] . "'";
									$this->execWithPDOLog($pdo,$sql, 'WPO010　更新後２ロケ：T_PICK_ITEM' );
									// 変換対象となる２ロケ「T_PICK_ITEM」にアイテムが存在するので更新処理を行う
									$sql = "";
									$sql .= "UPDATE T_PICK_ITEM SET ";
									$sql .= "  SURYO_HAKO_SOTO=SURYO_HAKO_SOTO+" . $rowdata3loc["SURYO_HAKO_SOTO"];
									$sql .= "  ,SURYO_HAKO_UCHI=SURYO_HAKO_UCHI+" . $rowdata3loc["SURYO_HAKO_UCHI"];
									$sql .= "  ,SURYO_BARA=SURYO_BARA+" . $rowdata3loc["SURYO_BARA"];
									$sql .= "  ,SURYO_TOTAL=SURYO_TOTAL+" . $rowdata3loc["SURYO_TOTAL"];
									$sql .= "  ,MODIFIED_STAFF='U" . $updatestaff . "'";
									$sql .= "  WHERE";
									$sql .= "    NINUSI_CD='" . $this->_ninusi_cd . "' AND";
									$sql .= "    SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
									$sql .= "    YMD_SYORI='" . $YMD_SYORI . "' AND";
									$sql .= "    S_BATCH_NO_CD='" . $S_BATCH_NO_CD . "' AND";
									$sql .= "    S_KANRI_NO = '" . $rowdata2loc['S_KANRI_NO'] . "' AND";
									$sql .= "    PICK_NO = '" . $rowdata2loc['PICK_NO'] . "' AND";
									$sql .= "    ITEM_CD = '" . $rowdata2loc['ITEM_CD'] . "'";
									$this->execWithPDOLog($pdo,$sql, 'WPO010　更新後２ロケ：T_PICK_ITEM' );
									
									$sql = "";
									$sql .= " INSERT INTO T_PICK_ITEM_3LOC_UPDATE ";
									$sql .= " VALUES('" . $rowdata3loc["NINUSI_CD"] . "'";
									$sql .= " ,'" . $rowdata3loc["SOSIKI_CD"] . "'";
									$sql .= " ,'" . $rowdata3loc["YMD_SYORI"] . "'";
									$sql .= " ,'" . $rowdata3loc["S_BATCH_NO_CD"] . "'";
									$sql .= " ,'" . $rowdata3loc["S_KANRI_NO"] . "'";
									$sql .= " ," . $rowdata3loc["PICK_NO"];
									$sql .= " ,'" . $rowdata3loc["ITEM_CD"] . "'";
									$sql .= " ,'" . $rowdata3loc["ITEM_NM"] . "'";
									$sql .= " ,'" . $rowdata3loc["LOCATION"] . "'";
									$sql .= " ,'" . $rowdata3loc["ITEM_HAKO_SOTO_CD"] . "'";
									$sql .= " ,'" . $rowdata3loc["ITEM_HAKO_UCHI_CD"] . "'";
									$sql .= " ,'" . $rowdata3loc["ITEM_BARA_CD"] . "'";
									$sql .= " ," . $rowdata3loc["IRI_HAKO_SOTO"];
									$sql .= " ," . $rowdata3loc["IRI_HAKO_UCHI"];
									$sql .= " ," . $rowdata3loc["SURYO_HAKO_SOTO"];
									$sql .= " ," . $rowdata3loc["SURYO_HAKO_UCHI"];
									$sql .= " ," . $rowdata3loc["SURYO_BARA"];
									$sql .= " ," . $rowdata3loc["SURYO_TOTAL"];
									$sql .= " ," . $rowdata3loc["SURYO_T_KETU"];
									$sql .= " ," . $rowdata3loc["SURYO_S_KETU"];
									$sql .= " ,'" . $rowdata3loc["CREATED"] . "'";
									$sql .= " ,'" . $rowdata3loc["CREATED_STAFF"] . "'";
									$sql .= " ,'" . $rowdata3loc["MODIFIED"] . "'";
									$sql .= " ,'" . $rowdata3loc["MODIFIED_STAFF"] . "'";
									$sql .= ")";
									$this->execWithPDOLog($pdo,$sql, 'WPO010　変換対象更新バックアップ：T_PICK_ITEM' );
										
								} else {
									// 変換対象となる２ロケ「T_PICK_ITEM」にアイテムが存在しないので追加処理を行う
									$sql = "";
									$sql .= "SELECT S_KANRI_NO,SURYO_ITEM";
									$sql .= "  FROM";
									$sql .= "    T_PICK_S_KANRI";
									$sql .= "  WHERE";
									$sql .= "    NINUSI_CD='" . $this->_ninusi_cd . "' AND";
									$sql .= "    SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
									$sql .= "    YMD_SYORI='" . $YMD_SYORI . "' AND";
									$sql .= "    S_BATCH_NO_CD='" . $S_BATCH_NO_CD . "' AND";
									$sql .= "    ZONE = '" . substr($retStatus,0,2) . "'";
									$sql .= " ORDER BY SURYO_ITEM ASC";
									$sql .= " LIMIT 1";
									$this->queryWithPDOLog($stmt3,$pdo,$sql,"３ロケ集約");
									$rowdata2locMin = $stmt3->fetch(PDO::FETCH_ASSOC);
									$Pick_No = $rowdata2locMin["SURYO_ITEM"] + 1;
									$changeLocation = $this->change2LocZone($rowdata3loc["LOCATION"],$retStatus); 
									$aryinsert = array();
									$array = array(
										"S_KANRI_NO" => $rowdata2locMin["S_KANRI_NO"]
									);
									$aryinsert[] = $array;
									$sql = "";
									$sql .= " INSERT INTO T_PICK_ITEM ";
									$sql .= " VALUES('" . $rowdata3loc["NINUSI_CD"] . "'";
									$sql .= " ,'" . $rowdata3loc["SOSIKI_CD"] . "'";
									$sql .= " ,'" . $rowdata3loc["YMD_SYORI"] . "'";
									$sql .= " ,'" . $rowdata3loc["S_BATCH_NO_CD"] . "'";
									$sql .= " ,'" . $rowdata2locMin["S_KANRI_NO"] . "'";
									$sql .= " ," . $Pick_No;
									$sql .= " ,'" . $rowdata3loc["ITEM_CD"] . "'";
									$sql .= " ,'" . $rowdata3loc["ITEM_NM"] . "'";
									$sql .= " ,'" . $changeLocation . "'";
									$sql .= " ,'" . $rowdata3loc["ITEM_HAKO_SOTO_CD"] . "'";
									$sql .= " ,'" . $rowdata3loc["ITEM_HAKO_UCHI_CD"] . "'";
									$sql .= " ,'" . $rowdata3loc["ITEM_BARA_CD"] . "'";
									$sql .= " ," . $rowdata3loc["IRI_HAKO_SOTO"];
									$sql .= " ," . $rowdata3loc["IRI_HAKO_UCHI"];
									$sql .= " ," . $rowdata3loc["SURYO_HAKO_SOTO"];
									$sql .= " ," . $rowdata3loc["SURYO_HAKO_UCHI"];
									$sql .= " ," . $rowdata3loc["SURYO_BARA"];
									$sql .= " ," . $rowdata3loc["SURYO_TOTAL"];
									$sql .= " ," . $rowdata3loc["SURYO_T_KETU"];
									$sql .= " ," . $rowdata3loc["SURYO_S_KETU"];
									$sql .= " ,'" . $rowdata3loc["CREATED"] . "'";
									$sql .= " ,'" . $rowdata3loc["CREATED_STAFF"] . "'";
									$sql .= " ,'" . $rowdata3loc["MODIFIED"] . "'";
									$sql .= " ,'I" . $updatestaff . "'";
									$sql .= ")";
									$this->execWithPDOLog($pdo,$sql, 'WPO010　追加変換：T_PICK_ITEM' );
										
									$sql = "";
									$sql .= " INSERT INTO T_PICK_ITEM_3LOC_INSERT ";
									$sql .= " VALUES('" . $rowdata3loc["NINUSI_CD"] . "'";
									$sql .= " ,'" . $rowdata3loc["SOSIKI_CD"] . "'";
									$sql .= " ,'" . $rowdata3loc["YMD_SYORI"] . "'";
									$sql .= " ,'" . $rowdata3loc["S_BATCH_NO_CD"] . "'";
									$sql .= " ,'" . $rowdata3loc["S_KANRI_NO"] . "'";
									$sql .= " ," . $rowdata3loc["PICK_NO"];
									$sql .= " ,'" . $rowdata3loc["ITEM_CD"] . "'";
									$sql .= " ,'" . $rowdata3loc["ITEM_NM"] . "'";
									$sql .= " ,'" . $rowdata3loc["LOCATION"] . "'";
									$sql .= " ,'" . $rowdata3loc["ITEM_HAKO_SOTO_CD"] . "'";
									$sql .= " ,'" . $rowdata3loc["ITEM_HAKO_UCHI_CD"] . "'";
									$sql .= " ,'" . $rowdata3loc["ITEM_BARA_CD"] . "'";
									$sql .= " ," . $rowdata3loc["IRI_HAKO_SOTO"];
									$sql .= " ," . $rowdata3loc["IRI_HAKO_UCHI"];
									$sql .= " ," . $rowdata3loc["SURYO_HAKO_SOTO"];
									$sql .= " ," . $rowdata3loc["SURYO_HAKO_UCHI"];
									$sql .= " ," . $rowdata3loc["SURYO_BARA"];
									$sql .= " ," . $rowdata3loc["SURYO_TOTAL"];
									$sql .= " ," . $rowdata3loc["SURYO_T_KETU"];
									$sql .= " ," . $rowdata3loc["SURYO_S_KETU"];
									$sql .= " ,'" . $rowdata3loc["CREATED"] . "'";
									$sql .= " ,'" . $rowdata3loc["CREATED_STAFF"] . "'";
									$sql .= " ,'" . $rowdata3loc["MODIFIED"] . "'";
									$sql .= " ,'" . $rowdata3loc["MODIFIED_STAFF"] . "'";
									$sql .= ")";
									$this->execWithPDOLog($pdo,$sql, 'WPO010　変換対象追加バックアップ：T_PICK_ITEM' );
										
								}
								
								// 「T_PICK_S_KANRI」のバックアップ
								$sql = "";
								$sql = "INSERT INTO T_PICK_S_KANRI_BEFORE_UPDATE ";
								$sql .= "SELECT";
								$sql .= "   *";
								$sql .= "  FROM";
								$sql .= "    T_PICK_S_KANRI";
								$sql .= "  WHERE";
								$sql .= "    NINUSI_CD='" . $this->_ninusi_cd . "' AND";
								$sql .= "    SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
								$sql .= "    YMD_SYORI='" . $YMD_SYORI . "' AND";
								$sql .= "    S_BATCH_NO_CD='" . $S_BATCH_NO_CD . "' AND";
								if ($rowdata2loc==false) {
									$sql .= "    S_KANRI_NO = '" . $rowdata2locMin['S_KANRI_NO'] . "' AND";
								} else {
									$sql .= "    S_KANRI_NO = '" . $rowdata2loc['S_KANRI_NO'] . "' AND";
								}
								$sql .= "    MODIFIED_STAFF<>'" . $updatestaff . "'";
								$this->execWithPDOLog($pdo,$sql, 'WPO010　更新前２ロケ：T_PICK_S_KANRI' );
								
								// 「T_PICK_S_KANRI」の更新クエリー実行
								$sql = "";
								$sql .= "UPDATE T_PICK_S_KANRI SET ";
								$sql .= "  SURYO_HAKO_SOTO=SURYO_HAKO_SOTO+" . $rowdata3loc["SURYO_HAKO_SOTO"];
								$sql .= "  ,SURYO_HAKO_UCHI=SURYO_HAKO_UCHI+" . $rowdata3loc["SURYO_HAKO_UCHI"];
								$sql .= "  ,SURYO_PIECE=SURYO_PIECE+" . $rowdata3loc["SURYO_TOTAL"];
								if ($rowdata2loc==false) {
									$sql .= "  ,SURYO_ITEM=SURYO_ITEM+1";
								} 
								$sql .= "  ,MODIFIED_STAFF='" . $updatestaff . "'";
								$sql .= "  WHERE";
								$sql .= "    NINUSI_CD='" . $this->_ninusi_cd . "' AND";
								$sql .= "    SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
								$sql .= "    YMD_SYORI='" . $YMD_SYORI . "' AND";
								$sql .= "    S_BATCH_NO_CD='" . $S_BATCH_NO_CD . "' AND";
								if ($rowdata2loc==false) {
									$sql .= "    S_KANRI_NO = '" . $rowdata2locMin['S_KANRI_NO'] . "'";
								} else {
									$sql .= "    S_KANRI_NO = '" . $rowdata2loc['S_KANRI_NO'] . "'";
								}
								$this->execWithPDOLog($pdo,$sql, 'WPO010　更新後２ロケ：T_PICK_S_KANRI' );
							
								// ３ロケ「T_PICK_ITEM」を削除
								$sql = "";
								$sql .= "DELETE ";
								$sql .= "  FROM";
								$sql .= "    T_PICK_ITEM";
								$sql .= "  WHERE";
								$sql .= "    NINUSI_CD='" . $this->_ninusi_cd . "' AND";
								$sql .= "    SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
								$sql .= "    YMD_SYORI='" . $YMD_SYORI . "' AND";
								$sql .= "    S_BATCH_NO_CD='" . $S_BATCH_NO_CD . "' AND";
								$sql .= "    S_KANRI_NO='" . $rowdata3loc['S_KANRI_NO'] . "' AND";
								$sql .= "    PICK_NO='" . $rowdata3loc["PICK_NO"] . "' AND";
								$sql .= "    ITEM_CD='" . $rowdata3loc["ITEM_CD"] . "'";
								$this->execWithPDOLog($pdo,$sql, 'WPO010　３ロケ削除：T_PICK_ITEM' );
								
							}
							
						}
						
						// ２ロケデータに追加があった場合、「T_PICK_ITEM」の「PICK_NO」を昇順に並べ直す
						$tmp = array();
						
						foreach($aryinsert as $val) {
						
							if (!in_array($val, $tmp)) {
									
								$tmp[] = $val;
							}
						}
						
						$aryinsert = $tmp;
						
						foreach($aryinsert as $array) {
							$numLineCount=0;
							$sql = "";
							$sql .= "SELECT *";
							$sql .= "  FROM";
							$sql .= "    T_PICK_ITEM";
							$sql .= "  WHERE";
							$sql .= "    NINUSI_CD='" . $this->_ninusi_cd . "' AND";
							$sql .= "    SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
							$sql .= "    YMD_SYORI='" . $YMD_SYORI . "' AND";
							$sql .= "    S_BATCH_NO_CD='" . $S_BATCH_NO_CD . "' AND";
							$sql .= "    S_KANRI_NO='" . $array['S_KANRI_NO'] . "'";
							$sql .= " ORDER BY LOCATION";
							$this->queryWithPDOLog($stmt4,$pdo,$sql,"３ロケ集約");
							while($rowdataSort = $stmt4->fetch(PDO::FETCH_ASSOC)){
								$numLineCount += 1;
								$sql = "";
								$sql .= "UPDATE T_PICK_ITEM SET";
								$sql .= " PICK_NO=" . $numLineCount;
								$sql .= "  WHERE";
								$sql .= "    NINUSI_CD='" . $this->_ninusi_cd . "' AND";
								$sql .= "    SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
								$sql .= "    YMD_SYORI='" . $YMD_SYORI . "' AND";
								$sql .= "    S_BATCH_NO_CD='" . $S_BATCH_NO_CD . "' AND";
								$sql .= "    S_KANRI_NO='" . $array['S_KANRI_NO'] . "' AND";
								$sql .= "    LOCATION='" . $rowdataSort['LOCATION'] . "'";
								$this->execWithPDOLog($pdo,$sql, 'WPO010　PICK_NO_SORT：T_PICK_ITEM' );
							}
						}						
						if ($retStatus!=false) {
							// ３ロケ「T_PICK_S_KANRI」を削除
							$sql = "";
							$sql .= "DELETE ";
							$sql .= "  FROM";
							$sql .= "    T_PICK_S_KANRI";
							$sql .= "  WHERE";
							$sql .= "    NINUSI_CD='" . $this->_ninusi_cd . "' AND";
							$sql .= "    SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
							$sql .= "    YMD_SYORI='" . $YMD_SYORI . "' AND";
							$sql .= "    S_BATCH_NO_CD='" . $S_BATCH_NO_CD . "' AND";
							$sql .= "    S_KANRI_NO='" . $result['S_KANRI_NO'] . "'";
							$this->execWithPDOLog($pdo,$sql, 'WPO010　３ロケ削除：T_PICK_S_KANRI' );
						}						
					}
					// ★★★３ロケ集約機能ここまで★★★

				}
			}
			
			
			
			if ($errorCount > 0) {
				$pdo->rollback();
				
				// エラー終了
				$msgStrCnt = vsprintf($this->MMessage->getOneMessage('WPOE010008'), array($errorCount));
				$this->printLog('error', Configure::read("LogAccessMenu.pick"), "WPO010", $msgStrCnt);
				$this->errors['check'] = array();
				$this->errors['check'][] = $msgStrCnt;
				$this->errors['check'][] = $msgStr;
				
			} else {
				
				// 正常終了
				$pdo->commit();
				
				$msgStr = vsprintf($this->MMessage->getOneMessage('WPOI010001'), array($lineCount-1));
				$this->infos['check'][] = $msgStr;
				$this->printLog('info', "バッチ編成取り込み", "WPO010", $msgStr);
				$result_return_cd = true;
			}
			
		    // 排他フラグ更新【END】
		    // 全体進捗優先変更側で更新ができないようにする
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WPO',";
			$sql .= "'010',";
			$sql .= "0";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,"WPO010 排他制御オフ");
		
			$pdo = null;
			
			
		} catch (Exception $e) {
			// ３ロケ集約ロールバック追加
			$pdo->rollback();
			$this->printLog("fatal", "例外発生", "WPO010", $e->getMessage());
			$this->errors['check'] = array();
			$this->errors['check'][] = $this->MMessage->getOneMessage('CMNE000107');
			
			$pdo = null;
		}
		
		sem_release($sem_id);

	    return $result_return_cd;
	    		
	}
	
	/**
	 * TPM040排他チェック
	 * @access   public
	 * @param    スタッフコード
	 * @return   void
	 */
	public function checkTPM040($staff_cd) {
	
		$pdo = null;
	
		try{
				
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
	
			$sql = "CALL P_GET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'TPM'". ",";
			$sql .= "'040'". ",";
			$sql .= "@return";
			$sql .= ")";
	
			$this->execWithPDOLog($pdo,$sql, 'TPM040 排他確認');
				
			$sql = "";
			$sql .= "SELECT";
			$sql .= "@return";
	
	
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'TPM040 排他確認');
				
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			$pdo = null;
			return $result['@return'];
				
		} catch (Exception $e){
			
			$this->printLog("fatal", "例外発生", "WPO010", $e->getMessage());
			$pdo = null;
			throw $e;
		}
	
		return 1;
	}

	public function getExistBatchCD() {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql  = "SELECT DISTINCT ";
			$sql .= "  BATCH_CD";
			$sql .= " FROM";
			$sql .= "  T_PICK_BATCH";
			$sql .= " WHERE ";
			$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "  (YMD_UNYO = DATE(NOW()) OR YMD_UNYO = DATE(DATE_ADD(NOW(),INTERVAL 1 DAY)))";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"存在バッチコード");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
						"BATCH_CD" => $result['BATCH_CD']
				);

				$arrayList[] = $array;
				
			}
			
			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO010", $e->getMessage());
			$pdo = null;
		}


		return $arrayList;
	}

	public function search2LocZone($str3Loc_Zone,$trantable) {
		$serchString = substr($str3Loc_Zone,0,3);
		foreach ($trantable as $array) {
			if ($serchString==$array["THIRDLOC"]) {
				return $array["SECONDLOC"];				
			}
		}
		$serchString = substr($str3Loc_Zone,0,2);
		foreach ($trantable as $array) {
			if ($serchString==$array["THIRDLOC"]) {
				return $array["SECONDLOC"];				
			}
		}
		return false;
	}
	
	public function change2LocZone($str3Loc_Zone,$strZone) {
		return substr($strZone,0,2) . substr($str3Loc_Zone,2,6);
	}
	
	
	
}


?>
 
