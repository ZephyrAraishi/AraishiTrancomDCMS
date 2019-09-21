<?php
/**
 * WEX095Model
 *
 * ABCデータベース参照
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MMeisyo', 'Model');
App::uses('MCommon', 'Model');

class WEX095Model extends DCMSAppModel{
	
	public $name = 'WEX095Model';
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
		//共通関数モデル
		$this->MCommon = new MCommon($ninusi_cd,$sosiki_cd,$kino_id);
		
	}
	
	/**
	 * 検索可能最大件数取得
	 * @access   public
	 * @return   検索可能最大件数
	 */
	public function getSearchMaxCnt() {
	
		$pdo = null;
		
		try{
				
			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
			$SearchMaxCnt = '';
			
			// 発行するSQLを設定
			$sql  = "select ";
			$sql .= "    SEARCH_MAX_CNT ";
			$sql .= "from ";
			$sql .= "    M_SYSTEM ";
			$sql .= "where ";
			$sql .= "    NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "and SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql,"検索最大件数取得");
				
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
				$SearchMaxCnt =  $result['SEARCH_MAX_CNT'];
			}
				
			$pdo = null;
				
			return $SearchMaxCnt;
							
			
		}catch (Exception $e) {
			
			$this->printLog("fatal", "例外発生", "WEX095", $e->getMessage());
			$message = $this->MMessage->getOneMessage('CMNE000107');
			$this->errors['Check'][] =   $message;

			$pdo = null;
		}
		
		return '';
		
		
	}
	
	/**
	 * ABCデータベーススタッフ別情報件数取得
	 *
	 * @param $start_ymd 年月日(自)
	 * @param $end_ymd 年月日(至)
	 * @param $bnri_dai_cd 大分類コード
	 * @param $bnri_cyu_cd 中分類コード
	 * @param $bnri_sai_cd 細分類コード
	 * @param $staff_nm スタッフ名称
	 * @return ABCデータベーススタッフ別情報
	 */
	public function getABCStaffCount($start_ymd, $end_ymd, $bnri_dai_cd, $bnri_cyu_cd, $bnri_sai_cd, $staff_nm) {
		
		$pdo = null;
		
		try{
		
			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
			$common = $this->MCommon;
			
			// 発行するSQLを設定
			$sql  = "select ";
			$sql .= "    count(STAFF_CD) as CNT ";
			$sql .= "from ";
			$sql .= "    V_WEX095_T_ABC_DB_STAFF_LST ";
			$sql .= "where ";
			$sql .= "    NINUSI_CD = " . $common->encloseString($this->_ninusi_cd);
			$sql .= " and SOSIKI_CD = " . $common->encloseString($this->_sosiki_cd);
			$sql .= " and YMD between ". $common->encloseString($start_ymd) . " and ". $common->encloseString($end_ymd);
			if ($bnri_dai_cd != "") {
				$sql .= " and BNRI_DAI_CD = ". $common->encloseString($bnri_dai_cd);
			}
			if ($bnri_cyu_cd != "") {
				$sql .= " and BNRI_CYU_CD = ". $common->encloseString($bnri_cyu_cd);
			}
			if ($bnri_sai_cd != "") {
				$sql .= " and BNRI_SAI_CD = ". $common->encloseString($bnri_sai_cd);
			}
			if ($staff_nm != "") {
				$sql .= " and STAFF_NM like ". $common->encloseString('%' . $staff_nm . '%');
			}
			
			// ABCデータベーススタッフ別情報を取得
			$this->queryWithPDOLog($stmt, $pdo, $sql,"ABCデータベーススタッフ別情報　取得");

			$results = array();
			while ($entity = $stmt->fetch(PDO::FETCH_ASSOC)) {
				array_push($results, $entity);
			}
			
			$pdo = null;
			
			return $results[0]["CNT"];
		
		}catch (Exception $e) {
				
			$this->printLog("fatal", "例外発生", "WEX095", $e->getMessage());
			$message = $this->MMessage->getOneMessage('CMNE000107');
			$this->errors['Check'][] =   $message;
		
			$pdo = null;
		}
		
		return 0;
		
	}
	
	/**
	 * ABCデータベーススタッフ別情報取得
	 * 
	 * @param $start_ymd 年月日(自)
	 * @param $end_ymd 年月日(至)
	 * @param $bnri_dai_cd 大分類コード
	 * @param $bnri_cyu_cd 中分類コード
	 * @param $bnri_sai_cd 細分類コード
	 * @param $staff_nm スタッフ名称
	 * @return ABCデータベーススタッフ別情報
	 */
	public function getABCStaffData($start_ymd, $end_ymd, $bnri_dai_cd, $bnri_cyu_cd, $bnri_sai_cd, $staff_nm) {
		
		$pdo = null;
		
		try{
		
			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$common = $this->MCommon;
			$fields = '*';
			 
			// 発行するSQLを設定
			$sql  = "select ";
			$sql .= "$fields ";
			$sql .= "from ";
			$sql .= "    V_WEX095_T_ABC_DB_STAFF_LST ";
			$sql .= "where ";
		    $sql .= "    NINUSI_CD = " . $common->encloseString($this->_ninusi_cd);
			$sql .= " and SOSIKI_CD = " . $common->encloseString($this->_sosiki_cd);
			$sql .= " and YMD between ". $common->encloseString($start_ymd) . " and ". $common->encloseString($end_ymd);
			if ($bnri_dai_cd != "") {
				$sql .= " and BNRI_DAI_CD = ". $common->encloseString($bnri_dai_cd);
			}
			if ($bnri_cyu_cd != "") {
				$sql .= " and BNRI_CYU_CD = ". $common->encloseString($bnri_cyu_cd);
			}
			if ($bnri_sai_cd != "") {
				$sql .= " and BNRI_SAI_CD = ". $common->encloseString($bnri_sai_cd);
			}
			if ($staff_nm != "") {
				$sql .= " and STAFF_NM like ". $common->encloseString('%' . $staff_nm . '%');
			}
			$sql .= " order by ";
			$sql .= "    YMD desc, ";
			$sql .= "    STAFF_CD asc, ";
			$sql .= "    BNRI_DAI_CD asc, ";
			$sql .= "    BNRI_CYU_CD asc, ";
			$sql .= "    BNRI_SAI_CD asc ";
			
				// ABCデータベーススタッフ別情報を取得
			$this->queryWithPDOLog($stmt, $pdo, $sql,"ABCデータベーススタッフ別情報　取得");
							
			$results = array();
			while ($entity = $stmt->fetch(PDO::FETCH_ASSOC)) {
				array_push($results, $entity);
			}
				
			$pdo = null;

			return $results;
			
		}catch (Exception $e) {
		
			$this->printLog("fatal", "例外発生", "WEX095", $e->getMessage());
			$message = $this->MMessage->getOneMessage('CMNE000107');
			$this->errors['Check'][] =   $message;
		
			$pdo = null;
		}
		
		return array();
	}
	
}