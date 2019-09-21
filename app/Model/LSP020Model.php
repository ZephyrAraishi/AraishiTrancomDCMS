<?php
/**
 * LSP020Model
 *
 * 予測シュミレーション
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

class LSP020Model extends DCMSAppModel {
	
	public $name = 'LSP020Model';
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
     * 月別予測情報取得
     * @access   public
	 * @param    $start_ymd 開始日
	 * @param    $end_ymd 終了日
     * @return   月別予測情報
     */
    public function getMonthlyYosokuInfo($start_ymd, $end_ymd) {
    	
    	$fields = '*';
    	
    	// 検索条件を設定
    	$conditionVal = array();
    	$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
    	$conditionVal['sosiki_cd'] = $this->_sosiki_cd;
    	$conditionVal['start_ymd'] = $start_ymd;
    	$conditionVal['end_ymd'] = $end_ymd;
    	
    	// 発行するSQLを設定
		$sql  = "select ";
		$sql .= "$fields ";
		$sql .= "from ";
		$sql .= "	V_LSP020_LSP_MONTH_LST ";
		$sql .= "where ";
		$sql .= "    NINUSI_CD = :ninusi_cd ";
		$sql .= "and SOSIKI_CD = :sosiki_cd ";
		$sql .= "and YMD between :start_ymd and :end_ymd ";
		$sql .= "order by ";
		$sql .= "	YMD asc ";
		
        // 月別作業予測・実績情報を取得
		$data = $this->queryWithLog($sql, $conditionVal, "月別予測情報取得");
		$data = self::editData($data);
		
		return $data;
    }
    
    /**
     * LSP按分率情報取得
     * @access   public
     * @param    $ymd 対象日付
     * @return   LSP按分率情報
     */
    public function getAnbunRate($ymd) {
    	 
    	// 検索条件を設定
    	$conditionVal = array();
    	$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
    	$conditionVal['sosiki_cd'] = $this->_sosiki_cd;
    	$conditionVal['ymd'] = $ymd;
    	 
    	$sql  = "select ";
    	$sql .= "	time_format(YOSOKU_TIME, '%H') as YOSOKU_TIME, ";
    	$sql .= "	ANBUN_RATE ";
    	$sql .= "from ";
    	$sql .= "	T_LSP_YOSOKU_R ";
    	$sql .= "where ";
    	$sql .= "    NINUSI_CD = :ninusi_cd ";
    	$sql .= "and SOSIKI_CD = :sosiki_cd ";
    	$sql .= "and YMD = STR_TO_DATE( :ymd ,'%Y%m%d')";
    	$sql .= "order by ";
    	$sql .= "	YOSOKU_TIME ";
    	 
    	// LSP按分率情報を取得
    	$data = $this->queryWithLog($sql, $conditionVal, "LSP按分率情報取得");
    	$data = self::editData($data);
    
    	return $data;
    }
    
    /**
     * 登録済みLSP予測情報取得
     * @access   public
	 * @param    $ymd 対象日付
     * @return   登録済みLSP予測情報
     */
    public function getDailyYosokuInfo($ymd) {

    	// 検索条件を設定
    	$conditionVal = array();
    	$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
    	$conditionVal['sosiki_cd'] = $this->_sosiki_cd;
    	$conditionVal['viewYmd'] = $ymd;

    	// 発行するSQLを設定
    	$sql = "select ";
    	$sql .= "    BND.BNRI_DAI_CD, ";
    	$sql .= "    BND.BNRI_DAI_RYAKU as BNRI_DAI_NM, ";
    	$sql .= "    BNC.BNRI_CYU_CD, ";
    	$sql .= "    BNC.BNRI_CYU_RYAKU as BNRI_CYU_NM, ";
    	$sql .= "    BNC.KBN_URIAGE, ";
    	$sql .= "    LYD.BNRI_DAI_CD as LYD_BNRI_DAI_CD, ";
    	$sql .= "    LYD.NINZU as NINZU, ";
    	$sql .= "    LYD.BUTURYO as BUTURYO, ";
    	$sql .= "    LYD.URIAGE AS URIAGE, ";
    	$sql .= "    LYD.GENKA AS GENKA, ";
    	$sql .= "    LYD.URIAGE - LYD.GENKA AS ARARI, ";
    	$sql .= "    LYD.JIKAN AS JIKAN, ";
    	$sql .= "    time_format(LYD.TIME_START, '%H%i') as TIME_START, ";
    	$sql .= "    time_format(LYD.TIME_END, '%H%i') as TIME_END ";
    	$sql .= "from ";
    	$sql .= "    M_BNRI_DAI BND ";
    	$sql .= "    inner join M_BNRI_CYU BNC ";
    	$sql .= "        on BND.NINUSI_CD = BNC.NINUSI_CD ";
    	$sql .= "        and BND.SOSIKI_CD = BNC.SOSIKI_CD ";
    	$sql .= "        and BND.BNRI_DAI_CD = BNC.BNRI_DAI_CD ";
    	$sql .= "    left outer join ( ";
    	$sql .= "            select ";
    	$sql .= "                NINUSI_CD, ";
    	$sql .= "                SOSIKI_CD, ";
    	$sql .= "                BNRI_DAI_CD, ";
    	$sql .= "                BNRI_CYU_CD, ";
    	$sql .= "                min(KOTEI_KBN) as KOTEI_KBN ";
    	$sql .= "            from ";
    	$sql .= "                M_CALC_TGT_KOTEI ";
    	$sql .= "            group by ";
    	$sql .= "                NINUSI_CD, ";
    	$sql .= "                SOSIKI_CD, ";
    	$sql .= "                BNRI_DAI_CD, ";
    	$sql .= "                BNRI_CYU_CD ";
    	$sql .= "            ) CTK ";
    	$sql .= "        on CTK.NINUSI_CD = BNC.NINUSI_CD ";
    	$sql .= "        and CTK.SOSIKI_CD = BNC.SOSIKI_CD ";
    	$sql .= "        and CTK.BNRI_DAI_CD = BNC.BNRI_DAI_CD ";
    	$sql .= "        and CTK.BNRI_CYU_CD = BNC.BNRI_CYU_CD ";
    	$sql .= "    left outer join T_LSP_YOSOKU_D LYD ";
    	$sql .= "        on BNC.NINUSI_CD = LYD.NINUSI_CD ";
    	$sql .= "        and BNC.SOSIKI_CD = LYD.SOSIKI_CD ";
    	$sql .= "        and BNC.BNRI_DAI_CD = LYD.BNRI_DAI_CD ";
    	$sql .= "        and BNC.BNRI_CYU_CD = LYD.BNRI_CYU_CD ";
    	$sql .= "        and LYD.YMD = STR_TO_DATE( :viewYmd ,'%Y%m%d') ";
    	$sql .= "where ";
    	$sql .= "    BND.NINUSI_CD = :ninusi_cd ";
    	$sql .= "and BND.SOSIKI_CD = :sosiki_cd ";
    	$sql .= "and (CTK.KOTEI_KBN is null or CTK.KOTEI_KBN <> '2') ";
    	$sql .= "order by ";
    	$sql .= "    BND.BNRI_DAI_CD asc, ";
    	$sql .= "    BNC.BNRI_CYU_CD asc ";
    	
    	// LSP予測情報を取得
    	$data = $this->queryWithLog($sql, $conditionVal, "登録済みLSP予測情報取得");
    	$data = self::editData($data);
    	
    	return $data;
    }
    
    /**
     * スタッフ勤務設定情報取得
     * 
     * @access   public
	 * @param    $bnriDaiCd 大分類コード
	 * @param    $bnriCyuCd 中分類コード
	 * @param    $ymd 対象日付
     * @return   勤務情報
     */
    public function getStaffKinmuData($bnriDaiCd, $bnriCyuCd, $ymd) {
    	
    	$fields = '*';
    	
    	// 検索条件を設定
    	$conditionVal = array();
    	$conditionVal['bnri_dai_cd'] = $bnriDaiCd;
    	$conditionVal['bnri_cyu_cd'] = $bnriCyuCd;
    	$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
    	$conditionVal['sosiki_cd'] = $this->_sosiki_cd;
    	$conditionVal['ymd'] = $ymd;
    	
    	$sql  = "select ";
		$sql .= "$fields ";
    	$sql .= "from ";
    	$sql .= "	V_LSP020_LSP_STAFF_KINMU_LST ";
    	$sql .= "where ";
    	$sql .= "	BNRI_DAI_CD = :bnri_dai_cd ";
    	$sql .= "and BNRI_CYU_CD = :bnri_cyu_cd ";
    	$sql .= "and NINUSI_CD = :ninusi_cd ";
    	$sql .= "and SOSIKI_CD = :sosiki_cd ";
    	$sql .= "and YMD_KINMU = STR_TO_DATE( :ymd ,'%Y%m%d') ";
    
    	// スタッフ勤務設定情報を取得
    	$data = $this->queryWithLog($sql, $conditionVal, "スタッフ勤務設定情報取得");
    	$data = self::editData($data);
    	 
    	return $data;
    }
    
    /**
     * 単位名称取得
     * @access   public
	 * @param    $bnriDaiCd 大分類コード
	 * @param    $bnriCyuCd 中分類コード
     * @return   単位名称
     */
    public function getTaniName($bnriDaiCd, $bnriCyuCd) {
    	 
    	// 検索条件を設定
    	$conditionVal = array();
    	$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
    	$conditionVal['sosiki_cd'] = $this->_sosiki_cd;
    	$conditionVal['bnri_dai_cd'] = $bnriDaiCd;
    	$conditionVal['bnri_cyu_cd'] = $bnriCyuCd;
    	 
    	$sql  = "select ";
    	$sql .= "    KBN_TANI ";
    	$sql .= "from ";
    	$sql .= "    M_BNRI_CYU ";
    	$sql .= "where ";
    	$sql .= "    NINUSI_CD = :ninusi_cd ";
    	$sql .= "and SOSIKI_CD = :sosiki_cd ";
    	$sql .= "and BNRI_DAI_CD = :bnri_dai_cd ";
    	$sql .= "and BNRI_CYU_CD = :bnri_cyu_cd ";
    	
    	// 中分類マスタ情報を取得
    	$data = $this->queryWithLog($sql, $conditionVal, "単位名称取得");
    	$data = self::editData($data);
    	
    	$kbn = $data[0]["KBN_TANI"];
    	
    	$mst = $this->MMeisyo->getMeisyo("13");
    	
    	return $mst[$kbn];
    }
    
    /**
     * シュミレーション処理実行
     * @access   public
	 * @param    $ymd 対象日付
	 * @param    $yosokuInfo 予測情報
	 * @param    $anbunInfo 按分率情報
	 * @param    $calcFlg 売上算出処理を行う場合true、そうでない場合false
     * @return   勤務情報
     */
    public function executeSimulation($ymd, $yosokuInfo, $anbunInfo, $calcFlg) {
    	
    	try {
    		
    		// 対象日付のスタッフ生産性情報を取得
    		$staffSsnInfo = $this->getDailyStaffSeisanseiInfo($ymd);
    		 
    		// 対象日付の勤務設定情報を取得
    		$staffKnmInfo = $this->getDailyStaffKinmuInfo($ymd);
    		
    		// 工程別シュミレーション結果情報を作成
    		$koteiData = $this->createKoteiSimulationData($ymd, $yosokuInfo, $staffSsnInfo, $staffKnmInfo, $anbunInfo, $calcFlg);
    		 
    		// 工程別シュミレーション結果を集計
    		$sumUriage = 0;
    		$sumGenka = 0;
    		$sumArari = 0;
    		$sumJikan = 0;
    		$sumNinzu = 0;
    		foreach ($koteiData as $data) {
    			$sumUriage += $data["K_URIAGE"];
    			$sumGenka += $data["K_GENKA"];
    			$sumArari += $data["K_ARARI"];
    			$sumJikan += $data["K_JIKAN"];
    			$sumNinzu += $data["K_NINZU"];
    		}
    		 
    		// 戻り値のシュミレーション結果情報を作成
    		$result = array();
    		$result["NISUSI_CD"] = $this->_ninusi_cd;
    		$result["SOSIKI_CD"] = $this->_sosiki_cd;
    		$result["YMD"] = $ymd;
    		$result["URIAGE"] = $sumUriage;
    		$result["GENKA"] = $sumGenka;
    		$result["ARARI"] = $sumArari;
    		$result["JIKAN"] = $sumJikan;
    		$result["NINZU"] = $sumNinzu;
    		$result["KOTEI_SIMULATION_RESULT"] = $koteiData;
    		
    		return $result;
    		
    	} catch (Exception $e) {
    		
    		$this->printLog("fatal", "例外発生", "LSP020", $e->getMessage());
    	}
    	
		return null;    	
    }
    
    /**
     * LSP予測情報更新処理
     * @access   public
     * @param    $simData シュミレーション結果情報
     * @param    $anbunData 按分率情報
     * @return   更新結果
     */
    public function updateLSPSimulation($simData, $anbunData) {
		
		$pdo = null;
		$pdo2 = null;
		$return_cd = "0";
		
		try {
			
			$ymd = $this->strToDate($simData["YMD"]);
			$staffCd = $_SESSION['staff_cd'];
		
			// 排他制御
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staffCd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'LSP',";
			$sql .= "'020',";
			$sql .= "1";
			$sql .= ")";
			$this->execWithPDOLog($pdo,$sql,'LSP020 排他制御オン');
				
			try {
				
				$pdo2 = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
				$pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$pdo2->beginTransaction();
				
				// LSP予測ヘッダ情報を削除
				$params = "";
				$params .=  " '".$this->_ninusi_cd."'";
				$params .=  ",'".$this->_sosiki_cd."'";
				$params .=  ",'".$ymd."'";
				$params .=  ',@r';
				$sql = "CALL P_LSP020_DEL_T_LSP_YOSOKU_H($params)";
				$this->execWithPDOLog($pdo2, $sql, 'LSP予測ヘッダ情報　削除');
				$sql = "SELECT";
				$sql .= " @return_cd";
				$this->queryWithPDOLog($stmt,$pdo2,$sql, 'LSP予測ヘッダ情報　削除');
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$return_cd = $result["@return_cd"];
				if ($return_cd == "1") {
					throw new Exception('LSP予測ヘッダ情報　削除');
				}
				
				// LSP予測明細情報を削除
				$params = "";
				$params .=  " '".$this->_ninusi_cd."'";
				$params .=  ",'".$this->_sosiki_cd."'";
				$params .=  ",'".$ymd."'";
				$params .=  ',@r';
				$sql = "CALL P_LSP020_DEL_T_LSP_YOSOKU_D($params)";
				$this->execWithPDOLog($pdo2, $sql, 'LSP予測明細情報　削除');
				$sql = "SELECT";
				$sql .= " @return_cd";
				$this->queryWithPDOLog($stmt,$pdo2,$sql, 'LSP予測明細情報　削除');
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$return_cd = $result["@return_cd"];
				if ($return_cd == "1") {
					throw new Exception('LSP予測明細情報　削除');
				}
				
				// LSP予測按分率情報を削除
				$params = "";
				$params .=  " '".$this->_ninusi_cd."'";
				$params .=  ",'".$this->_sosiki_cd."'";
				$params .=  ",'".$ymd."'";
				$params .=  ',@return_cd';
				$sql = "CALL P_LSP020_DEL_T_LSP_YOSOKU_R($params)";
				$this->execWithPDOLog($pdo2, $sql, 'LSP予測按分率情報　削除');
				$sql = "SELECT";
				$sql .= " @return_cd";
				$this->queryWithPDOLog($stmt,$pdo2,$sql, 'LSP予測按分率情報　削除');
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$return_cd = $result["@return_cd"];
				if ($return_cd == "1") {
					throw new Exception('LSP予測按分率情報　削除');
				}
				
				// LSP予測ヘッダ情報を登録
				$params = "";
				$params .=  " '".$this->_ninusi_cd."'";
				$params .=  ",'".$this->_sosiki_cd."'";
				$params .=  ",'".$ymd."'";
				$params .=  ",'".$simData["URIAGE"]."'";
				$params .=  ",'".$simData["GENKA"]."'";
				$params .=  ",'".$simData["NINZU"]."'";
				$params .=  ",'".$simData["JIKAN"]."'";
				$params .=  ",'".$staffCd."'";
				$params .=  ',@return_cd';
				$sql = "CALL P_LSP020_INS_T_LSP_YOSOKU_H($params)";
				$this->execWithPDOLog($pdo2, $sql, 'LSP予測ヘッダ情報　削除');
				$sql = "SELECT";
				$sql .= " @return_cd";
				$this->queryWithPDOLog($stmt,$pdo2,$sql, 'LSP予測ヘッダ情報　削除');
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$return_cd = $result["@return_cd"];
				if ($return_cd == "1") {
					throw new Exception('LSP予測ヘッダ情報　削除');
				}
				
				// LSP予測明細情報を登録
				$koteiData = $simData["KOTEI_SIMULATION_RESULT"];
				foreach ($koteiData as $data) {
					if ($data["K_KBN_URIAGE"] != "01" && empty($data["K_BUTURYO"]) && empty($data["K_NINZU"])) {
						continue;
					}
					$params = "";
					$params .=  " '".$this->_ninusi_cd."'";
					$params .=  ",'".$this->_sosiki_cd."'";
					$params .=  ",'".$ymd."'";
					$params .=  ",'".$data["K_BNRI_DAI_CD"]."'";
					$params .=  ",'".$data["K_BNRI_CYU_CD"]."'";
					$params .=  ",'".$data["K_URIAGE"]."'";
					$params .=  ",'".$data["K_GENKA"]."'";
					$params .=  ",'".$data["K_NINZU"]."'";
					$params .=  ",'".$data["K_JIKAN"]."'";
					$params .=  ",'".$data["K_BUTURYO"]."'";
					$params .=  ",'".$this->strToTime($data["K_TIME_START"])."'";
					$params .=  ",'".$this->strToTime($data["K_TIME_END"])."'";
					$params .=  ",'".$this->strToTime($data["K_YOSOKU_TIME_START"])."'";
					$params .=  ",'".$this->strToTime($data["K_YOSOKU_TIME_END"])."'";
					$params .=  ",'".$data["K_KEIKOKU_BUTURYO"]."'";
					$params .=  ",'".$staffCd."'";
					$params .=  ',@return_cd';
					$sql = "CALL P_LSP020_INS_T_LSP_YOSOKU_D($params)";
					$this->execWithPDOLog($pdo2, $sql, 'LSP予測明細情報　登録');
					$sql = "SELECT";
					$sql .= " @return_cd";
					$this->queryWithPDOLog($stmt,$pdo2,$sql, 'LSP予測明細情報　登録');
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
					$return_cd = $result["@return_cd"];
					if ($return_cd == "1") {
						throw new Exception('LSP予測明細情報　登録');
					}
				}
				
				// LSP予測按分率情報を登録
				foreach ($anbunData as $data) {
					$params = "";
					$params .=  " '".$this->_ninusi_cd."'";
					$params .=  ",'".$this->_sosiki_cd."'";
					$params .=  ",'".$ymd."'";
					$params .=  ",'".$data["YOSOKU_TIME"].":00:00"."'";
					$params .=  ",'".$data["ANBUN_RATE"]."'";
					$params .=  ",'".$staffCd."'";
					$params .=  ',@r';
					$sql = "CALL P_LSP020_INS_T_LSP_YOSOKU_R($params)";
					$this->execWithPDOLog($pdo2, $sql, 'LSP予測按分率情報登録　登録');
					$sql = "SELECT";
					$sql .= " @return_cd";
					$this->queryWithPDOLog($stmt,$pdo2,$sql, 'LSP予測按分率情報登録　登録');
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
					$return_cd = $result["@return_cd"];
					if ($return_cd == "1") {
						throw new Exception('LSP予測按分率情報登録　登録');
					}
				}
					
				$pdo2->commit();
					
			} catch (Exception $e) {
				$return_cd = '1';
				$this->printLog("fatal", "例外発生", "LSP020", $e->getMessage());
				$pdo2->rollBack();
			}
				
			$pdo2 = null;
		
			//排他制御オフ
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staffCd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'LSP',";
			$sql .= "'020',";
			$sql .= "0";
			$sql .= ")";
				
			$this->execWithPDOLog($pdo,$sql,'LSP020 排他制御オフ');
		
			$pdo = null;
				
			if ($return_cd == "1") {
				$message = $this->MMessage->getOneMessage('CMNE000107');
				$this->errors['Check'][] =  $message;
				return false;
			}
				
			return true;
				
		} catch (Exception $e) {
			$this->printLog("fatal", "例外発生", "LSP020", $e->getMessage());
			$pdo = null;
		}
		
		//例外発生の場合、エラー
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
    }
    
    /**
     * 契約単位代表物量フラグ設定件数情報取得
     *
     * @access   public
     * @return 契約単位代表物量フラグ設定件数情報
     */
    public function getKeiyakuTaniDaihyoButuryoCntInfo() {
    	 
    	// 検索条件を設定
    	$conditionVal = array();
    	$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
    	$conditionVal['sosiki_cd'] = $this->_sosiki_cd;
    	 
    	$sql  = "select ";
    	$sql .= "    BND.BNRI_DAI_CD, ";
    	$sql .= "    BND.BNRI_DAI_RYAKU, ";
    	$sql .= "    BNC.BNRI_CYU_CD, ";
    	$sql .= "    BNC.BNRI_CYU_RYAKU, ";
    	$sql .= "    ifnull(NKY.BNRI_DAI_CD, '000') as KY_BNRI_DAI_CD, ";
    	$sql .= "    ifnull(NKY.BNRI_CYU_CD, '000') as KY_BNRI_CYU_CD, ";
    	$sql .= "    ifnull(NKYD.KENSU_BNRI_DAI, 0) as KY_DAIHYO_CNT_DAI, ";
    	$sql .= "    ifnull(NKYC.KENSU_BNRI_CYU, 0) as KY_DAIHYO_CNT_CYU ";
    	$sql .= "from ";
    	$sql .= "    M_BNRI_DAI BND ";
    	$sql .= "    left outer join M_BNRI_CYU BNC ";
    	$sql .= "        on BND.NINUSI_CD = BNC.NINUSI_CD ";
    	$sql .= "        and BND.SOSIKI_CD = BNC.SOSIKI_CD ";
    	$sql .= "        and BND.BNRI_DAI_CD = BNC.BNRI_DAI_CD ";
    	$sql .= "    left outer join M_NINUSI_KEIYAKU NKY ";
    	$sql .= "        on BNC.NINUSI_CD = NKY.NINUSI_CD ";
    	$sql .= "        and BNC.SOSIKI_CD = NKY.SOSIKI_CD ";
    	$sql .= "        and ( ";
    	$sql .= "            ( ";
    	$sql .= "                BNC.BNRI_DAI_CD = NKY.BNRI_DAI_CD ";
    	$sql .= "                and NKY.BNRI_CYU_CD = '000' ";
    	$sql .= "            ) ";
    	$sql .= "            or ";
    	$sql .= "            ( ";
    	$sql .= "                BNC.BNRI_DAI_CD = NKY.BNRI_DAI_CD ";
    	$sql .= "                and BNC.BNRI_CYU_CD = NKY.BNRI_CYU_CD ";
    	$sql .= "                and NKY.BNRI_CYU_CD <> '000' ";
    	$sql .= "            ) ";
    	$sql .= "        ) ";
    	$sql .= "    left outer join ( ";
    	$sql .= "            select ";
    	$sql .= "                BNS.NINUSI_CD as NINUSI_CD, ";
    	$sql .= "                BNS.SOSIKI_CD as SOSIKI_CD, ";
    	$sql .= "                BNS.BNRI_DAI_CD as BNRI_DAI_CD, ";
    	$sql .= "                COUNT(BNS.KEIYAKU_BUTURYO_FLG) as KENSU_BNRI_DAI ";
    	$sql .= "            from ";
    	$sql .= "                M_BNRI_SAI BNS ";
    	$sql .= "            where ";
    	$sql .= "                BNS.NINUSI_CD = :ninusi_cd ";
    	$sql .= "            and BNS.SOSIKI_CD = :sosiki_cd ";
    	$sql .= "            and BNS.KEIYAKU_BUTURYO_FLG = 1 ";
    	$sql .= "            group by ";
    	$sql .= "                BNS.NINUSI_CD, ";
    	$sql .= "                BNS.SOSIKI_CD, ";
    	$sql .= "                BNS.BNRI_DAI_CD ";
    	$sql .= "            ) NKYD  ";
    	$sql .= "        on BNC.NINUSI_CD = NKYD.NINUSI_CD ";
    	$sql .= "        and BNC.SOSIKI_CD = NKYD.SOSIKI_CD ";
    	$sql .= "        and BNC.BNRI_DAI_CD = NKYD.BNRI_DAI_CD ";
    	$sql .= "    left outer join ( ";
    	$sql .= "            select ";
    	$sql .= "                BNS.NINUSI_CD as NINUSI_CD, ";
    	$sql .= "                BNS.SOSIKI_CD as SOSIKI_CD, ";
    	$sql .= "                BNS.BNRI_DAI_CD as BNRI_DAI_CD, ";
    	$sql .= "                BNS.BNRI_CYU_CD as BNRI_CYU_CD, ";
    	$sql .= "                COUNT(BNS.KEIYAKU_BUTURYO_FLG) as KENSU_BNRI_CYU ";
    	$sql .= "            from ";
    	$sql .= "                M_BNRI_SAI BNS ";
    	$sql .= "            where ";
    	$sql .= "                BNS.NINUSI_CD = :ninusi_cd ";
    	$sql .= "            and BNS.SOSIKI_CD = :sosiki_cd ";
    	$sql .= "            and BNS.KEIYAKU_BUTURYO_FLG = 1 ";
    	$sql .= "            group by ";
    	$sql .= "                BNS.NINUSI_CD, ";
    	$sql .= "                BNS.SOSIKI_CD, ";
    	$sql .= "                BNS.BNRI_DAI_CD, ";
    	$sql .= "                BNS.BNRI_CYU_CD ";
    	$sql .= "        ) NKYC ";
    	$sql .= "        on BNC.NINUSI_CD = NKYC.NINUSI_CD ";
    	$sql .= "        and BNC.SOSIKI_CD = NKYC.SOSIKI_CD ";
    	$sql .= "        and BNC.BNRI_DAI_CD = NKYC.BNRI_DAI_CD ";
    	$sql .= "        and BNC.BNRI_CYU_CD = NKYC.BNRI_CYU_CD ";
    	$sql .= "where ";
    	$sql .= "  BND.NINUSI_CD = :ninusi_cd ";
    	$sql .= "and BND.SOSIKI_CD = :sosiki_cd ";
    	$sql .= "order by ";
    	$sql .= "  BND.BNRI_DAI_CD asc, ";
    	$sql .= "  BNC.BNRI_CYU_CD asc ";
    
    	// 契約単位代表物量情報を取得
    	$data = $this->query($sql, $conditionVal, false);
    	$data = self::editData($data);
    	 
    	return $data;
    }
    
    /**
     * 契約単位代表物量フラグ設定情報取得
     *
     * @access   public
     * @return 契約単位代表物量フラグ設定情報
     */
    public function getKeiyakuTaniDaihyoButuryoFlgInfo() {
    
    	// 検索条件を設定
    	$conditionVal = array();
    	$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
    	$conditionVal['sosiki_cd'] = $this->_sosiki_cd;
    
		$sql  = "select ";
		$sql .= "    BND.BNRI_DAI_CD, ";
		$sql .= "    BNC.BNRI_CYU_CD, ";
		$sql .= "    ifnull(BNS.KEIYAKU_BUTURYO_FLG, 0) as KEIYAKU_BUTURYO_FLG ";
		$sql .= "from ";
		$sql .= "    M_BNRI_DAI BND  ";
		$sql .= "    left outer join M_BNRI_CYU BNC ";
		$sql .= "        on BND.NINUSI_CD = BNC.NINUSI_CD ";
		$sql .= "        and BND.SOSIKI_CD = BNC.SOSIKI_CD ";
		$sql .= "        and BND.BNRI_DAI_CD = BNC.BNRI_DAI_CD ";
		$sql .= "    left outer join ( ";
		$sql .= "            select ";
		$sql .= "                NINUSI_CD, ";
		$sql .= "                SOSIKI_CD, ";
		$sql .= "                BNRI_DAI_CD, ";
		$sql .= "                BNRI_CYU_CD, ";
		$sql .= "                max(KEIYAKU_BUTURYO_FLG) as KEIYAKU_BUTURYO_FLG ";
		$sql .= "            from ";
		$sql .= "                M_BNRI_SAI ";
		$sql .= "            where ";
		$sql .= "                NINUSI_CD = :ninusi_cd ";
		$sql .= "            and SOSIKI_CD = :sosiki_cd ";
		$sql .= "            group by ";
		$sql .= "                NINUSI_CD, ";
		$sql .= "                SOSIKI_CD, ";
		$sql .= "                BNRI_DAI_CD, ";
		$sql .= "                BNRI_CYU_CD ";
		$sql .= "        ) BNS ";
		$sql .= "        on BNC.NINUSI_CD = BNS.NINUSI_CD ";
		$sql .= "        and BNC.SOSIKI_CD = BNS.SOSIKI_CD ";
		$sql .= "        and BNC.BNRI_DAI_CD = BNS.BNRI_DAI_CD ";
		$sql .= "        and BNC.BNRI_CYU_CD = BNS.BNRI_CYU_CD ";
		$sql .= "where ";
		$sql .= "  BND.NINUSI_CD = :ninusi_cd ";
		$sql .= "and BND.SOSIKI_CD = :sosiki_cd ";
		$sql .= "order by ";
		$sql .= "  BND.BNRI_DAI_CD asc, ";
		$sql .= "  BNC.BNRI_CYU_CD asc ";
    
    	// 契約単位代表物量情報を取得
    	$data = $this->query($sql, $conditionVal, false);
    	$data = self::editData($data);
    
    	return $data;
    }

    /**
     * 日別スタッフ生産性情報取得
     * @access   public
     * @param    $ymd 対象日付
     * @return   勤務情報
     */
    private function getDailyStaffSeisanseiInfo($ymd) {

    	$fields = '*';

    	// 検索条件を設定
    	$conditionVal = array();
    	$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
    	$conditionVal['sosiki_cd'] = $this->_sosiki_cd;
    	$conditionVal['ymd'] = $ymd;
    	 
    	$sql  = "select ";
    	$sql .= "$fields ";
        $sql .= "from ";
        $sql .= "    V_LSP020_LSP_STAFF_SEISANSEI_LST ";
        $sql .= "where ";
        $sql .= "    YMD_KINMU = STR_TO_DATE( :ymd ,'%Y%m%d') ";
        $sql .= "and NINUSI_CD = :ninusi_cd ";
        $sql .= "and SOSIKI_CD = :sosiki_cd ";
        $sql .= "order by ";
        $sql .= "    BNRI_DAI_CD asc, ";
        $sql .= "    BNRI_CYU_CD asc, ";
        $sql .= "    STAFF_CD asc, ";
        $sql .= "    TIME_SYUGYO_ST asc ";
    
    	// 勤務設定情報を取得
    	$data = $this->query($sql, $conditionVal, false);
    	$data = self::editData($data);
    	 
    	return $data;
    }
    
    /**
     * 日別スタッフ勤務人数情報取得
     * @access   public
     * @param    $ymd 対象日付
     * @return   勤務情報
     */
    private function getDailyStaffKinmuInfo($ymd) {
    	
    	$fields = '*';
    	
    	// 検索条件を設定
    	$conditionVal = array();
    	$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
    	$conditionVal['sosiki_cd'] = $this->_sosiki_cd;
    	$conditionVal['ymd'] = $ymd;
    	
    	$sql  = "select ";
    	$sql .= "$fields ";
    	$sql .= "from ";
    	$sql .= "    V_LSP020_LSP_STAFF_KINMU_CNT ";
    	$sql .= "where ";
    	$sql .= "    YMD_KINMU = STR_TO_DATE( :ymd ,'%Y%m%d') ";
    	$sql .= "and NINUSI_CD = :ninusi_cd ";
    	$sql .= "and SOSIKI_CD = :sosiki_cd ";
    	$sql .= "order by ";
    	$sql .= "    BNRI_DAI_CD asc, ";
    	$sql .= "    BNRI_CYU_CD asc ";
    	
    	// 勤務設定情報を取得
    	$data = $this->query($sql, $conditionVal, false);
    	$data = self::editData($data);
    	
    	return $data;
    }
    
    /**
     * 工程別シュミレーション結果情報作成
     * @access   public
     * @param    $ymd 対象日付
     * @param    $yosokuInfo 予測情報
     * @param    $staffSsnInfo スタッフ生産性情報
     * @param    $staffKnmInfo 勤務設定情報
     * @param    $anbunInfo 按分率情報
	 * @param    $calcFlg 売上算出処理を行う場合true、そうでない場合false
     * @return   工程別シュミレーション結果情報
     */
    private function createKoteiSimulationData($ymd, $yosokuInfo, $staffSsnInfo, $staffKnmInfo, $anbunInfo, $calcFlg) {

		$result = array();
		
		$flgInfos = null;
		
		// 共通部品にて売上算出処理を行う場合、契約単位代表物量フラグ情報を取得
		if ($calcFlg) {
			$flgInfos = $this->getKeiyakuTaniDaihyoButuryoFlgInfo();
		}
		
			
		for ($i = 0; $i < count($yosokuInfo); $i++) {
	
			$row = array();
				
			$bnriDaiCd = $yosokuInfo[$i]["BNRI_DAI_CD"];
			$bnriDaiNm = $yosokuInfo[$i]["BNRI_DAI_NM"];
			$bnriCyuCd = $yosokuInfo[$i]["BNRI_CYU_CD"];
			$bnriCyuNm = $yosokuInfo[$i]["BNRI_CYU_NM"];
			$uriageKbn = $yosokuInfo[$i]["KBN_URIAGE"];
			$buturyo = $yosokuInfo[$i]["BUTURYO"];
			$startTime = $yosokuInfo[$i]["TIME_START"];
			$endTime = $yosokuInfo[$i]["TIME_END"];
			$keikokuButuryo = 0;
			$uriage = 0;
			$genka = 0;
			$jikan = 0;
			$ninzu = 0;
			$yosokuTimeStart = 0;
			$yosokuTimeEnd = 0;
			$timeSimulationData = array();
			
			// 工程が合致するスタッフ情報を抜き出す
			// ※生産性計算用に、「開始時刻(分)」「終了時刻(分)」「スタッフ単位での最も遅い工程開始時刻(分)」を設定しておく
			$staff = array();
			$temp = array();
			foreach ($staffSsnInfo as $array) {
				if ($array["BNRI_DAI_CD"] == $bnriDaiCd
						&& $array["BNRI_CYU_CD"] == $bnriCyuCd) {
					$staffCd = $array["STAFF_CD"];
					$array["TIME_SYUGYO_ST_M"] = (substr($array["TIME_SYUGYO_ST"], 0, 2) * 60) + (substr($array["TIME_SYUGYO_ST"], 2, 2));
					$array["TIME_SYUGYO_ED_M"] = (substr($array["TIME_SYUGYO_ED"], 0, 2) * 60) + (substr($array["TIME_SYUGYO_ED"], 2, 2));
					$array["LATE_TIME_SYUGYO_ST_M"] = (substr($array["LATE_TIME_SYUGYO_ST"], 0, 2) * 60) + (substr($array["LATE_TIME_SYUGYO_ST"], 3, 2));
					$staff[] = $array;
					if (!array_key_exists($staffCd, $temp)) {
						$temp[$staffCd] = $staffCd;
					}
				}
			}
			
			// 工程が合致するスタッフ数を抜き出す
			foreach ($staffKnmInfo as $array) {
				if ($array["BNRI_DAI_CD"] == $bnriDaiCd
						&& $array["BNRI_CYU_CD"] == $bnriCyuCd) {
					$ninzu = $array["STAFF_CNT"];
					break;
				}
			}
			
			// 工程別予測時刻シュミレーション処理を実行
			if ($uriageKbn != "01" && !empty($buturyo)) {
				$timeSimRes = $this->executeYosokuTimeSimulation($anbunInfo, $staff, $buturyo, $startTime, $endTime);
				$yosokuTimeStart = $timeSimRes["YOSOKU_TIME_START"];
				$yosokuTimeEnd = $timeSimRes["YOSOKU_TIME_END"];
				$keikokuButuryo = $timeSimRes["KEIKOKU_BUTURYO"];
				$timeSimulationData = $timeSimRes["SIMULATION_RESULT"];
			}
			

			// 「【共通処理】稼働コスト算出」を使用し、『原価』『時間』を取得
			if ($calcFlg && (!empty($ninzu) && ($uriageKbn == "01" || !empty($buturyo)))) {
				$jissekiMArray = array();
				foreach ($staff as $data) {
					$array = array();
					
					// 工程終了時刻
					// 　・当日の最終工程　　　　→　予測終了時刻（終了時刻を超えている場合のみ）
					// 　・当日の最終工程でない　→　勤務設定情報の就業終了
					$koteiSt = $data["TIME_SYUGYO_ST"];
					$koteiEd = $data["TIME_SYUGYO_ED"];
					if ($data["TIME_SYUGYO_ST_M"] == $data["LATE_TIME_SYUGYO_ST_M"]
							&& $koteiEd < $yosokuTimeEnd) {
						$koteiEd = $yosokuTimeEnd;
					}
					
			    	$array["NINUSI_CD"] = $this->_ninusi_cd;                   // 荷主コード
			    	$array["SOSIKI_CD"] = $this->_sosiki_cd;                   // 組織コード
			    	$array["YMD_SYUGYO"] = $ymd;                               // 就業日付
			    	$array["BNRI_DAI_CD"] = $bnriDaiCd;                        // 大分類コード
			    	$array["BNRI_CYU_CD"] = $bnriCyuCd;                        // 中分類コード
			    	$array["BNRI_SAI_CD"] = "";                                // 細分類コード
			    	$array["STAFF_CD"] = $data["STAFF_CD"];                    // スタッフコード
			    	$array["DT_KOTEI_ST_H"] = $this->strToTime($koteiSt);      // 工程開始時刻
			    	$array["DT_KOTEI_ED_H"] = $this->strToTime($koteiEd);      // 工程終了時刻
			    	$array["DT_SYUGYO_ST"] = $data["EARLY_TIME_SYUGYO_ST"];    // 就業開始時刻
					$jissekiMArray[] = $array;
				}
				
				$cmnRes = $this->MCommon->getKadoCost($ymd, $jissekiMArray);
				foreach ($cmnRes as $res) {
					$genka += $res["COST"];
					$jikan += $res["TIME_KOTEI_SYUGYO"];
				}
			}
			
			// 「【共通処理】売上算出」を使用し、『売上金額』を取得
			if ($calcFlg && ($uriageKbn == "01" || !empty($buturyo))) {
				
				// 工程に対応する契約単位代表物量フラグを取り出す
				$flg = "0";
				foreach ($flgInfos as $fData) {
					if ($bnriDaiCd == $fData["BNRI_DAI_CD"]
							&& $bnriCyuCd == $fData["BNRI_CYU_CD"]) {
						$flg = $fData["KEIYAKU_BUTURYO_FLG"];
						break;
					}
				}
				
				$hibetuArray = array();
				$array = array();
		    	$array["NINUSI_CD"] = $this->_ninusi_cd;    // 荷主コード
		    	$array["SOSIKI_CD"] = $this->_sosiki_cd;    // 組織コード
		    	$array["BNRI_DAI_CD"] = $bnriDaiCd;         // 大分類コード
		    	$array["BNRI_CYU_CD"] = $bnriCyuCd;         // 中分類コード
			    $array["BNRI_SAI_CD"] = "";                 // 細分類コード
		    	$array["ABC_COST"] = $genka;                // コスト
		    	$array["BUTURYO"] = $buturyo;               // 物量
		    	$array["KEIYAKU_BUTURYO_FLG"] = $flg;       // 契約単位代表物量フラグ
				$hibetuArray[] = $array;
				$cmnRes = $this->MCommon->getUriage($hibetuArray);
				foreach ($cmnRes as $res) {
					$uriage += $res["URIAGE"];
				}
			}
			
			// 各工程の情報を格納
			$rowData = array (
					"K_BNRI_DAI_CD" => $bnriDaiCd,                // 大分類コード
					"K_BNRI_DAI_NM" => $bnriDaiNm,                // 大分類名
					"K_BNRI_CYU_CD" => $bnriCyuCd,                // 中分類コード
					"K_BNRI_CYU_NM" => $bnriCyuNm,                // 中分類名
					"K_KBN_URIAGE" => $uriageKbn,                 // 売上区分
					"K_BUTURYO" => $buturyo,                      // 物量
					"K_TIME_START" => $startTime,                 // 開始時刻
					"K_TIME_END" => $endTime,                     // 終了時刻
					"K_KEIKOKU_BUTURYO" => $keikokuButuryo,       // 警告物量
					"K_URIAGE" => $uriage,                        // 売上金額
					"K_GENKA" => $genka,                          // 原価
					"K_ARARI" => ($uriage - $genka),              // 粗利
					"K_JIKAN" => $jikan,                          // 時間
					"K_NINZU" => $ninzu,                          // 人数
					"K_YOSOKU_TIME_START" => $yosokuTimeStart,    // 予測開始時刻
					"K_YOSOKU_TIME_END" => $yosokuTimeEnd,        // 予測終了時刻
					"K_TIME_SIMULATION" => $timeSimulationData    // 時刻別予測シュミレーション結果
			);
			$result[] = $rowData;
		}
			
		// 大分類が持つ中分類の件数を設定
		$daiCd = "";
		$cnt = 0;
		for ($i = 0; $i < count($result); $i++) {
			if ($result[$i]["K_BNRI_DAI_CD"] != $daiCd) {
				$daiCd = $result[$i]["K_BNRI_DAI_CD"];
				if ($i == 0) {
					$cnt += 1;
					continue;
				}
				$result[$i - $cnt]["K_BNRI_CYU_COUNT"] = $cnt;
				$cnt = 0;
			}
			$cnt += 1;
		}
		$result[count($result) - $cnt]["K_BNRI_CYU_COUNT"] = $cnt;
			
		return $result;
    }
    
    /**
     * 工程別予測時刻シュミレーション処理実行
     * @access   private
     * @param    $anbunInfo 按分率情報
     * @param    $staff スタッフ勤務設定情報
     * @param    $buturyo 物量
     * @param    $startTime 開始時刻（HHmm形式）
     * @param    $endTime 終了時刻（HHmm形式）
     * @return   予測時刻計測シュミレーション結果
     */
    private function executeYosokuTimeSimulation($anbunInfo, $staff, $buturyo, $startTime, $endTime) {
    
		$timeInfos = array();
		
		// 警告の基準となる開始・終了時刻を分に変換
		$startTm = (substr($startTime, 0, 2) * 60) + (substr($startTime, 2, 2));
		$endTm = (substr($endTime, 0, 2) * 60) + (substr($endTime, 2, 2));
		
		$yosokuSt = 0;
		$yosokuEd = 0;
		
		$sumButuryo = 0;
		$sumAnbunRate = 0;
		$zanButuryo = 0;
		$keikokuButuryo = 0;
		$zanUmuFlg = false;
			
		foreach ($anbunInfo as $anbun) {
	
			$sagyoButuryo = 0;
			$seisansei = 0;
			$shijiButuryo = 0;
			$prevZanButuryo = $zanButuryo;
	
			// 現時刻の開始～終了を設定
			$from = $anbun["YOSOKU_TIME"] * 60;
			$to = $from + 60;
	
			// 按分率に対する物量を算出（出荷指示物量）
			if ($anbun["ANBUN_RATE"] != 0 && $zanUmuFlg == false) {
				$shijiButuryo = floor($buturyo * ($anbun["ANBUN_RATE"] / 100));
			}
			$sumAnbunRate += $anbun["ANBUN_RATE"];
			$sumButuryo += $shijiButuryo;
			if ($zanUmuFlg == false && $sumAnbunRate == 100) {
				$shijiButuryo -= ($sumButuryo - $buturyo);
			}
	
			// 作業対象の物量を設定（開始時刻を経過している場合のみ）
			if ($startTm <= $from) {
				$sagyoButuryo = $prevZanButuryo + $shijiButuryo;
			}
	
			/*
			 * 時間あたりのスタッフ生産性合計を算出
			*/
			foreach ($staff as $data) {
				$stb = $data["SEISANSEI_TIME_BUTURYO"];
				$st = $data["TIME_SYUGYO_ST_M"];
				$ed = $data["TIME_SYUGYO_ED_M"];
	
				// 現ループ時刻が、就業時間内の場合
				if ($st < $to && $from < $ed) {
	
					// 生産性を加算
					$t = 0;
					$t += $from < $st ? ($st - $from) : 0;
					$t += $ed < $to ? ($to - $ed) : 0;
					if ($t != 0) {
						$stb2 = floor($stb * ($t / 60));
						$stb -= $stb2;
					}
					$seisansei += $stb;
					if ($yosokuSt == 0 || $st < $yosokuSt) {
						$yosokuSt = $st;
					}
	
					// 就業時間外の場合
				} else {
	
					// 就業時間内ではないが、
					// スタッフ単位での最終工程の場合は、残業として作業を行うため、物量を保持
					if ($st == $data["LATE_TIME_SYUGYO_ST_M"]
							&& $data["LATE_TIME_SYUGYO_ST_M"] <= $from
							&& $sagyoButuryo != 0) {
						$seisansei += $stb;
					}
				}
			}
			$seisansei = floor($seisansei); // 少数部分を切り捨てる
	
			// 残物量を算出
			$zanButuryo = $sagyoButuryo - $seisansei;
			if ($zanButuryo < 0) {
				$zanButuryo = 0;
			}
			if ($from < $startTm) { // 開始時刻を経過していない場合、現時間の物量を翌時間へ持ち越す
				$zanButuryo = $prevZanButuryo + $shijiButuryo;
			}
	
			// 残物量がなくなった場合、作業終了とする
			if ($seisansei != 0 && $sumAnbunRate == 100 && $zanButuryo == 0) {
					
				// 予測終了時刻を設定
				$val = 0;
				if ($yosokuEd == 0) {
					$yosokuEd = $from + round(($sagyoButuryo / $seisansei) * 60);
					if ($sagyoButuryo != $seisansei) {
						$val = $seisansei - $sagyoButuryo;
					}
				} else {
					$val = $seisansei;
				}
	
				// 終了時刻に到達していない場合、警告数量に加算
				if ($from < $endTm) {
					$keikokuButuryo += $val;
				}
			}
	
			// 終了時刻を過ぎた場合、警告数量に設定
			if ($endTm <= $to && $keikokuButuryo == 0) {
				$keikokuButuryo = $zanButuryo * -1;
			}
	
			$timeInfos[] = array(
					"T_TIME" => $anbun["YOSOKU_TIME"],   // 時刻
					"T_SHIJI_BUTURYO" => $shijiButuryo,  // 出荷指示物量
					"T_SAGYO_BUTURYO" => $sagyoButuryo,  // 作業対象物量
					"T_SEISANSEI" => $seisansei,         // 生産性
			);
	
			$zanUmuFlg = $sumAnbunRate == 100;
		}
		
		$result = array(
				"KEIKOKU_BUTURYO" => $keikokuButuryo,                   // 警告物量
				"YOSOKU_TIME_START" => $this->minuteToTime($yosokuSt),  // 予測開始時刻
				"YOSOKU_TIME_END" => $this->minuteToTime($yosokuEd),    // 予測終了時刻
				"SIMULATION_RESULT" => $timeInfos                       // 時刻別シュミレーション結果
		);
		
		return $result;
    }
    
    /**
     * 日付文字列（yyyyMMdd形式）をＤＢ登録用の日付文字列に変換します。
     * @access private
     * @param $text      日付文字列（yyyyMMdd形式）
     * @return ＤＢ登録用の日付文字列
     */
    private function strToDate($text) {
    	
    	if (empty($text)) {
    		return "";
    	}
    	 
    	return substr($text, 0, 4)."/".substr($text, 4, 2)."/".substr($text, 6, 2);
    }
    
    /**
     * 時刻文字列（HHmm形式）をＤＢ登録用の時刻文字列に変換します。
     * @access private
     * @param $minute      時刻文字列（HHmm形式）
     * @return ＤＢ登録用の時刻文字列
     */
    private function strToTime($text) {
    	 
    	if (empty($text)) {
    		return "";
    	}
    	
    	return substr($text, 0, 2).":".substr($text, 2, 2).":"."00";
    }
    
    /**
     * 分を時刻文字列（HHmm形式）に変換します。
     *
     * @access private
     * @param $minute      分
     * @return 時刻文字列（HHmm形式）
     */
    private function minuteToTime($minute) {
    
    	return 0 < $minute ? sprintf("%02d", $minute / 60).sprintf("%02d", $minute % 60) : "0000";
    }
    
    /**
     * SQL取得結果編集処理
     * 
     * @access   private
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
 