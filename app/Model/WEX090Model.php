<?php
/**
 * WEX090Model
 *
 * 改善情報設定
 *
 * @category      LSP
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('MMeisyo', 'Model');
App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class WEX090Model extends DCMSAppModel {
	
	public $name = 'WEX090Model';
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
		$this->MCommon = new MCommon($ninusi_cd, $sosiki_cd, $kino_id);
    }
    
    /**
     * 検索可能最大件数取得
     * @access   public
     * @return   検索可能最大件数
     */
    public function getSearchMaxCnt() {
    
    	// 検索条件を設定
    	$conditionVal = array();
    	$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
    	$conditionVal['sosiki_cd'] = $this->_sosiki_cd;
    
    	// 発行するSQLを設定
    	$sql  = "select ";
    	$sql .= "    SEARCH_MAX_CNT ";
    	$sql .= "from ";
    	$sql .= "    M_SYSTEM ";
    	$sql .= "where ";
    	$sql .= "    NINUSI_CD = :ninusi_cd ";
    	$sql .= "and SOSIKI_CD = :sosiki_cd ";

    	// システムマスタを取得
    	$results = $this->queryWithLog($sql, $conditionVal, "最大可能件数取得");
    	$results = self::editData($results);
    
    	return $results[0]["SEARCH_MAX_CNT"];
    }
    
    /**
     * 改善データベース件数取得
     *
     * @access   public
     * @param $from 対象期間(自)
     * @param $to 対象期間(至)
     * @param $kikan_kbn 対象期間区分
     * @param $bnri_dai_cd 大分類コード
     * @param $bnri_cyu_cd 中分類コード
     * @param $bnri_sai_cd 細分類コード
     * @param $title 改善表題
     * @param $kzn_kmk 改善項目
     * @return ABCデータベーススタッフ別情報
     */
    public function getKaizenDataCount($from, $to, $kikan_kbn, $bnri_dai_cd, $bnri_cyu_cd, $bnri_sai_cd, $title, $kzn_kmk) {
    	
    	$fields = '*';
    	 
    	// 検索条件を設定
    	$conditionVal = array();
    	$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
    	$conditionVal['sosiki_cd'] = $this->_sosiki_cd;
    	$conditionVal['kikan_kbn'] = $kikan_kbn;
    	$conditionVal['from'] = $from;
    	$conditionVal['to'] = $to;
    	if ($bnri_dai_cd != "") {
    		$conditionVal['bnri_dai_cd'] = $bnri_dai_cd;
    	}
    	if ($bnri_cyu_cd != "") {
    		$conditionVal['bnri_cyu_cd'] = $bnri_cyu_cd;
    	}
    	if ($bnri_sai_cd != "") {
    		$conditionVal['bnri_sai_cd'] = $bnri_sai_cd;
    	}
    	if ($title != "") {
    		$conditionVal['title'] = "%" . $title . "%";
    	}
    	if ($kzn_kmk != "") {
    		$conditionVal['kzn_kmk'] = $kzn_kmk;
    	}
    	
    	// 発行するSQLを設定
    	$sql  = "select ";
    	$sql .= "    count(KZN_DATA_NO) as CNT ";
    	$sql .= "from ";
    	$sql .= "    T_KAIZEN_DB ";
    	$sql .= "where ";
    	$sql .= "    NINUSI_CD = :ninusi_cd ";
    	$sql .= "and SOSIKI_CD = :sosiki_cd ";
    	$sql .= "and TGT_KIKAN_KBN = :kikan_kbn ";
    	$sql .= "and TGT_KIKAN_ST <= :to ";
    	$sql .= "and TGT_KIKAN_ED >= :from ";
    	if ($bnri_dai_cd != "") {
    		$sql .= "and BNRI_DAI_CD = :bnri_dai_cd ";
    	}
    	if ($bnri_cyu_cd != "") {
    		$sql .= "and BNRI_CYU_CD = :bnri_cyu_cd ";
    	}
    	if ($bnri_sai_cd != "") {
    		$sql .= "and BNRI_SAI_CD = :bnri_sai_cd ";
    	}
    	if ($title != "") {
    		$sql .= "and KZN_TITLE like :title ";
    	}
    	if ($kzn_kmk != "") {
    		$sql .= "and KBN_KZN_KMK = :kzn_kmk ";
    	}
    	 
    	// 改善データベース情報を取得
    	$results = $this->queryWithLog($sql, $conditionVal, "改善データベース件数取得");
    	$results = self::editData($results);
    	 
    	return $results[0]["CNT"];
    }
    
    /**
     * 改善データベース情報取得
     * 
     * @access   public
     * @param $from 対象期間(自)
	 * @param $to 対象期間(至)
	 * @param $kikan_kbn 対象期間区分
	 * @param $bnri_dai_cd 大分類コード
	 * @param $bnri_cyu_cd 中分類コード
	 * @param $bnri_sai_cd 細分類コード
	 * @param $title 改善表題
	 * @param $kzn_kmk 改善項目
	 * @return ABCデータベーススタッフ別情報
     */
    public function getKaizenDataList($from, $to, $kikan_kbn, $bnri_dai_cd, $bnri_cyu_cd, $bnri_sai_cd, $title, $kzn_kmk) {
    	
    	$fields = '*';
    	
    	// 検索条件を設定
    	$conditionVal = array();
    	$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
    	$conditionVal['sosiki_cd'] = $this->_sosiki_cd;
    	$conditionVal['kikan_kbn'] = $kikan_kbn;
    	$conditionVal['from'] = $from;
    	$conditionVal['to'] = $to;
    	if ($bnri_dai_cd != "") {
    		$conditionVal['bnri_dai_cd'] = $bnri_dai_cd;
    	}
    	if ($bnri_cyu_cd != "") {
    		$conditionVal['bnri_cyu_cd'] = $bnri_cyu_cd;
    	}
    	if ($bnri_sai_cd != "") {
    		$conditionVal['bnri_sai_cd'] = $bnri_sai_cd;
    	}
    	if ($title != "") {
    		$conditionVal['title'] = "%" . $title . "%";
    	}
    	if ($kzn_kmk != "") {
    		$conditionVal['kzn_kmk'] = $kzn_kmk;
    	}
    		
    	// 発行するSQLを設定
    	$sql  = "select ";
    	$sql .= "$fields ";
    	$sql .= "from ";
    	$sql .= "    V_WEX090_T_KAIZEN_DB_LST ";
    	$sql .= "where ";
    	$sql .= "    NINUSI_CD = :ninusi_cd ";
    	$sql .= "and SOSIKI_CD = :sosiki_cd ";
    	$sql .= "and TGT_KIKAN_KBN = :kikan_kbn ";
    	$sql .= "and TGT_KIKAN_ST <= :to ";
    	$sql .= "and TGT_KIKAN_ED >= :from ";
    	if ($bnri_dai_cd != "") {
    		$sql .= "and BNRI_DAI_CD = :bnri_dai_cd ";
    	}
    	if ($bnri_cyu_cd != "") {
    		$sql .= "and BNRI_CYU_CD = :bnri_cyu_cd ";
    	}
    	if ($bnri_sai_cd != "") {
    		$sql .= "and BNRI_SAI_CD = :bnri_sai_cd ";
    	}
    	if ($title != "") {
    		$sql .= "and KZN_TITLE like :title ";
    	}
    	if ($kzn_kmk != "") {
    		$sql .= "and KBN_KZN_KMK = :kzn_kmk ";
    	}
    	$sql .= "order by ";
    	$sql .= "    TGT_KIKAN_ED desc, ";
    	$sql .= "    BNRI_DAI_CD asc, ";
    	$sql .= "    BNRI_CYU_CD asc, ";
    	$sql .= "    BNRI_SAI_CD asc, ";
    	$sql .= "    KBN_KZN_KMK asc, ";
    	$sql .= "    KZN_DATA_NO asc ";
    	
    	// 改善データベース情報を取得
    	$results = $this->queryWithLog($sql, $conditionVal, "改善データベース情報取得");
    	$results = self::editData($results);
    	
    	return $results;
    }
    
    /**
     * タイムスタンプチェック
     * 
     * @access   public
	 * @param    $currentTimestamp 現在日時
     * @return 正常な場合true、そうでない場合false
     */
    public function checkTimestamp($currentTimestamp) {
    
    	$pdo = null;
    
    	try{
    			
    		$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
    		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    		$sql = "CALL P_WEX090_GET_TIMESTAMP(";
    		$sql .= "'" . $this->_ninusi_cd . "',";
    		$sql .= "'" . $this->_sosiki_cd . "',";
    		$sql .= "@timestamp";
    		$sql .= ")";
    
    		$this->execWithPDOLog($pdo,$sql, 'データ整合性チェックの為にタイムスタンプ取得');
    			
    		$sql = "";
    		$sql .= "SELECT ";
    		$sql .= "@timestamp";
    
    
    		$this->queryWithPDOLog($stmt,$pdo,$sql, 'データ整合性チェックの為にタイムスタンプ取得');
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
    
    		$this->printLog("fatal", "例外発生", "WEX090", $e->getMessage());
    		$pdo = null;
    		throw $e;
    	}
    
    	$message = $this->MMessage->getOneMessage('CMNE000107');
    	$this->errors['Check'][] =  $message;
    	return false;
    }
    
    /**
     * 改善データベース情報更新
     *
     * @param $kikanKbn 改善期間区分
     * @param $kaizenData 改善データベース情報
     * @return 処理結果
     */
    function updateKaizenData($kikanKbn, $kaizenData) {
    	
		$pdo = null;
		$pdo2 = null;
		$return_cd = "0";
		
		try {
			
			$staffCd = $_SESSION['staff_cd'];

			// 排他制御
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staffCd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WEX',";
			$sql .= "'090',";
			$sql .= "1";
			$sql .= ")";
			$this->execWithPDOLog($pdo,$sql,'WEX090 排他制御オン');
			
			try {
			
				$kznDataNo = -1;
			
				$pdo2 = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
				$pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$pdo2->beginTransaction();
			
				foreach ($kaizenData as $data) {
					 
					// 新規登録の場合
					if ($data->updType == '2') {
			
						// 改善データ番号を設定
						if ($kznDataNo == -1) {
							$kznDataNo = $this->getKznDataMaxNo() + 1;
						} else {
							$kznDataNo++;
						}
			
						$params = "";
						$params .=  " '".$this->_ninusi_cd."'";
						$params .=  ",'".$this->_sosiki_cd."'";
						$params .=  ",'".$kznDataNo."'";
						$params .=  ",'".$kikanKbn."'";
						$params .=  ",'".$data->tgtKikanSt."'";
						$params .=  ",'".$data->tgtKikanEd."'";
						$params .=  ",'".$data->bnriDaiCd."'";
						$params .=  ",'".$data->bnriCyuCd."'";
						$params .=  ",'".$data->bnriSaiCd."'";
						$params .=  ",'".$data->kbnKznKmk."'";
						$params .=  ",'".Sanitize::clean($data->kznTitle)."'";
						$params .=  ",'".Sanitize::clean($data->kznText)."'";
						$params .=  ",'".$data->kznValue."'";
						$params .=  ",'".$data->kznCost."'";
						$params .=  ",'".$staffCd."'";
						$params .=  ',@return_cd';
						$sql = "CALL P_WEX090_INS_T_KAIZEN_DB($params)";
						$this->execWithPDOLog($pdo2, $sql, '改善データベース情報　登録');
			
						$sql = "SELECT";
						$sql .= " @return_cd";
						$this->queryWithPDOLog($stmt,$pdo2,$sql, '改善データベース情報　登録');
						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$return_cd = $result["@return_cd"];
						if ($return_cd == "1") {
							throw new Exception('改善データベース情報　登録');
						}
			
						// 更新の場合
					} else if ($data->updType == '1') {
			
						$params = "";
						$params .=  " '".$this->_ninusi_cd."'";
						$params .=  ",'".$this->_sosiki_cd."'";
						$params .=  ",'".$data->kznDataNo."'";
						$params .=  ",'".$kikanKbn."'";
						$params .=  ",'".$data->tgtKikanSt."'";
						$params .=  ",'".$data->tgtKikanEd."'";
						$params .=  ",'".$data->bnriDaiCd."'";
						$params .=  ",'".$data->bnriCyuCd."'";
						$params .=  ",'".$data->bnriSaiCd."'";
						$params .=  ",'".$data->kbnKznKmk."'";
						$params .=  ",'".Sanitize::clean($data->kznTitle)."'";
						$params .=  ",'".Sanitize::clean($data->kznText)."'";
						$params .=  ",'".$data->kznValue."'";
						$params .=  ",'".$data->kznCost."'";
						$params .=  ",'".$staffCd."'";
						$params .=  ',@return_cd';
						$sql = "CALL P_WEX090_UPD_T_KAIZEN_DB($params)";
						$this->execWithPDOLog($pdo2, $sql, '改善データベース情報　更新');
			
						$sql = "SELECT";
						$sql .= " @return_cd";
						$this->queryWithPDOLog($stmt,$pdo2,$sql, '改善データベース情報　更新');
						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$return_cd = $result["@return_cd"];
						if ($return_cd == "1") {
							throw new Exception('改善データベース情報　更新');
						}
			
						// 削除の場合
					} else if ($data->updType == '3' || $data->updType == '4') {
			
						$params = "";
						$params .=  " '".$this->_ninusi_cd."'";
						$params .=  ",'".$this->_sosiki_cd."'";
						$params .=  ",'".$data->kznDataNo."'";
						$params .=  ',@return_cd';
						$sql = "CALL P_WEX090_DEL_T_KAIZEN_DB($params)";
						$this->execWithPDOLog($pdo2, $sql, '改善データベース情報　削除');
			
						$sql = "SELECT";
						$sql .= " @return_cd";
						$this->queryWithPDOLog($stmt,$pdo2,$sql, '改善データベース情報　削除');
						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$return_cd = $result["@return_cd"];
						if ($return_cd == "1") {
							throw new Exception('改善データベース情報　削除');
						}
					}
				}
			
				$pdo2->commit();
			
			} catch (Exception $e) {
				$return_cd == "1";
				$this->printLog("fatal", "例外発生", "WEX090", $e->getMessage());
				$pdo2->rollBack();
			}
			
			$pdo2 = null;
				
			//排他制御オフ
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staffCd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WEX',";
			$sql .= "'090',";
			$sql .= "0";
			$sql .= ")";
			
			$this->execWithPDOLog($pdo,$sql,'WEX090 排他制御オフ');
				
			$pdo = null;
			
			if ($return_cd == "1") {
				$message = $this->MMessage->getOneMessage('CMNE000107');
				$this->errors['Check'][] =  $message;
				return false;
			}
			
			return true;
			
		} catch (Exception $e) {
			$this->printLog("fatal", "例外発生", "WEX090", $e->getMessage());
			$pdo = null;
		}
		
		//例外発生の場合、エラー
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
    }
    
    /**
     * 改善データ番号最大値取得
     * 
     */
    function getKznDataMaxNo() {

    	// 検索条件を設定
    	$conditionVal = array();
    	$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
    	$conditionVal['sosiki_cd'] = $this->_sosiki_cd;
    	 
    	// 発行するSQLを設定
    	$sql  = "select ";
    	$sql .= "    max(KZN_DATA_NO) as KZN_DATA_NO ";
    	$sql .= "from ";
    	$sql .= "    T_KAIZEN_DB ";
    	$sql .= "where ";
    	$sql .= "    NINUSI_CD = :ninusi_cd ";
    	$sql .= "and SOSIKI_CD = :sosiki_cd ";
    	
    	// 改善データベース情報を取得
    	$results = $this->queryWithLog($sql, $conditionVal, "改善データベース件数取得");
    	$results = self::editData($results);
    	
    	return $results[0]["KZN_DATA_NO"];
    }
    
    /**
     * SQL取得結果編集処理
     * @access   public
     * @param    $results 編集前情報
     * @return   編集後情報
     */
    private function editData($results) {
    
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
 