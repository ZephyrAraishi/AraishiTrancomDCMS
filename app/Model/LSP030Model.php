<?php
/**
 * LSP030Model
 *
 * 作業実績照会
 *
 * @category      LSP
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('MMessage', 'Model');

class LSP030Model extends DCMSAppModel {
	
	public $name = 'LSP030Model';
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
       
		/* ▼メッセージマスタ呼び出し */
		$this->MMessage = new MMessage($ninusi_cd, $sosiki_cd, $kino_id);
    }
    
    /**
     * 月別作業情報取得
     * @access   public
	 * @param    $start_ymd 開始日
	 * @param    $end_ymd 終了日
     * @return   勤務情報
     */
    public function getMonthlyWorkInfo($start_ymd, $end_ymd) {
    	
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
		$sql .= "    V_LSP030_LSP_MONTH_LST ";
		$sql .= "where ";
		$sql .= "    YMD between STR_TO_DATE( :start_ymd ,'%Y%m%d') and STR_TO_DATE( :end_ymd ,'%Y%m%d') ";
		$sql .= "and NINUSI_CD = :ninusi_cd ";
		$sql .= "and SOSIKI_CD = :sosiki_cd ";
		
        // 月別作業予測・実績情報を取得
		$data = $this->queryWithLog($sql, $conditionVal, "月別作業情報取得");
		$data = self::editData($data);
		
		return $data;
    }
    
    /**
     * 実績CSV情報取得
     * @access   public
	 * @param    $ymd 対象日付
     * @return   勤務情報
     */
    public function getJissekiCsvInfo($ymd) {
    	
    	$fields = '*';
    	
    	// 検索条件を設定
    	$conditionVal = array();
    	$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
    	$conditionVal['sosiki_cd'] = $this->_sosiki_cd;
    	$conditionVal['viewYmd'] = $ymd;

    	// 発行するSQLを設定
    	$sql = "select ";
    	$sql .= "    BND.BNRI_DAI_RYAKU as BNRI_DAI_NM, ";
    	$sql .= "    BNC.BNRI_CYU_RYAKU as BNRI_CYU_NM, ";
    	$sql .= "    LJD.BUTURYO as BUTURYO, ";
    	$sql .= "    LJD.URIAGE AS URIAGE, ";
    	$sql .= "    LJD.GENKA AS GENKA, ";
    	$sql .= "    LJD.URIAGE - LJD.GENKA AS ARARI ";
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
    	$sql .= "    left outer join T_LSP_JISSEKI_D LJD ";
    	$sql .= "        on BNC.NINUSI_CD = LJD.NINUSI_CD ";
    	$sql .= "        and BNC.SOSIKI_CD = LJD.SOSIKI_CD ";
    	$sql .= "        and BNC.BNRI_DAI_CD = LJD.BNRI_DAI_CD ";
    	$sql .= "        and BNC.BNRI_CYU_CD = LJD.BNRI_CYU_CD ";
    	$sql .= "        and LJD.YMD = STR_TO_DATE( :viewYmd ,'%Y%m%d') ";
    	$sql .= "where ";
    	$sql .= "    BND.NINUSI_CD = :ninusi_cd ";
    	$sql .= "and BND.SOSIKI_CD = :sosiki_cd ";
    	$sql .= "and (CTK.KOTEI_KBN is null or CTK.KOTEI_KBN <> '2') ";
    	$sql .= "order by ";
    	$sql .= "    BND.BNRI_DAI_CD asc, ";
    	$sql .= "    BNC.BNRI_CYU_CD asc ";
    	
    	// 実績CSV情報を取得
    	$data = $this->queryWithLog($sql, $conditionVal, "実績CSV情報取得");
    	$data = self::editData($data);
    	
    	return $data;
    }
    
    /**
     * 日別作業情報取得
     * @access   public
	 * @param    $ymd 対象日付
     * @return   勤務情報
     */
    public function getDailyWorkInfo($ymd) {

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
    	$sql .= "    BNC.BNRI_CYU_RYAKU as BNRI_CYU_NM, ";
    	$sql .= "    BNC.KBN_URIAGE, ";
    	$sql .= "    LYD.NINZU as LYD_NINZU, ";
    	$sql .= "    LYD.BUTURYO as LYD_BUTURYO, ";
    	$sql .= "    time_format(LYD.TIME_YOSOKU_START, '%H%i') as LYD_TIME_START, ";
    	$sql .= "    time_format(LYD.TIME_YOSOKU_END, '%H%i') as LYD_TIME_END, ";
    	$sql .= "    LJD.NINZU as LJD_NINZU, ";
    	$sql .= "    LJD.BUTURYO as LJD_BUTURYO, ";
    	$sql .= "    time_format(LJD.TIME_START, '%H%i') as LJD_TIME_START, ";
    	$sql .= "    time_format(LJD.TIME_END, '%H%i') as LJD_TIME_END ";
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
    	$sql .= "    left outer join T_LSP_JISSEKI_D LJD ";
    	$sql .= "        on BNC.NINUSI_CD = LJD.NINUSI_CD ";
    	$sql .= "        and BNC.SOSIKI_CD = LJD.SOSIKI_CD ";
    	$sql .= "        and BNC.BNRI_DAI_CD = LJD.BNRI_DAI_CD ";
    	$sql .= "        and BNC.BNRI_CYU_CD = LJD.BNRI_CYU_CD ";
    	$sql .= "        and LJD.YMD = STR_TO_DATE( :viewYmd ,'%Y%m%d') ";
    	$sql .= "where ";
    	$sql .= "    BND.NINUSI_CD = :ninusi_cd ";
    	$sql .= "and BND.SOSIKI_CD = :sosiki_cd ";
    	$sql .= "and (CTK.KOTEI_KBN is null or CTK.KOTEI_KBN <> '2') ";
    	$sql .= "order by ";
    	$sql .= "    BND.BNRI_DAI_CD asc, ";
    	$sql .= "    BNC.BNRI_CYU_CD asc ";
    	
    	// 日別作業予測・実績情報を取得
    	$data = $this->queryWithLog($sql, $conditionVal, "日別作業情報取得");
    	$data = self::editData($data);
    	
    	return $data;
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
 