<?php
/**
 * WEX093Model
 *
 * 日別作業実績参照
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class WEX093Model extends DCMSAppModel{
	
	public $name = 'WEX093Model';
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
		
		//共通関数モデル
		$this->MCommon = new MCommon($ninusi_cd,$sosiki_cd,$kino_id);
    }


	/**
	 * 検索時　入力チェック
	 * @access   public
	 * @return   
	 */
	public function checkInputSearch($date_from, $date_to) {

		try{

			//入力チェック
			$inputCheckFlg = '0';

			//対象期間（自）チェック
			if ($date_from == '') {
				//ないエラー
				$message = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array('対象期間（自）'));
				$this->errors['Check'][] =   $message;
				$inputCheckFlg = '1';
			} else {
				//データフォーマットが不正
				if (preg_match("/^[0-9]{4}[0-9]{2}[0-9]{2}/",$date_from) == false) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array('対象期間（自）'));
					$this->errors['Check'][] =   $message;
					$inputCheckFlg = '1';
				} else {
				
					$year = substr($date_from, 0,4);
					$month = substr($date_from, 4,2);
					$day = substr($date_from, 6,2);
					
					//不正
					if (checkdate($month, $day, $year) == false) {
						$message = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array('対象期間（自）'));
						$this->errors['Check'][] =   $message;
						$inputCheckFlg = '1';
					}
				}
			}

			//対象期間（至）チェック
			if ($date_to == '') {
				//ないエラー
				$message = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array('対象期間（至）'));
				$this->errors['Check'][] =   $message;
				$inputCheckFlg = '1';
			} else {
				//データフォーマットが不正
				if (preg_match("/^[0-9]{4}[0-9]{2}[0-9]{2}/",$date_to) == false) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array('対象期間（至）'));
					$this->errors['Check'][] =   $message;
					$inputCheckFlg = '1';
				} else {
				
					$year = substr($date_to, 0,4);
					$month = substr($date_to, 4,2);
					$day = substr($date_to, 6,2);
					
					//不正
					if (checkdate($month, $day, $year) == false) {
						$message = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array('対象期間（至）'));
						$this->errors['Check'][] =   $message;
						$inputCheckFlg = '1';
					}
				}
			}

			//範囲チェック
			if ($inputCheckFlg == '0') {
				//範囲チェックエラー
				if ($date_from > $date_to ) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE090002'), array('対象期間（至）', '対象期間（自）'));
					$this->errors['Check'][] =   $message;
					$inputCheckFlg = '1';
				}
			}

			if ($inputCheckFlg != '0') {
				return true;
			}

			return false;


		} catch (Exception $e){
	
			$this->printLog("fatal", "例外発生", "WEX093", $e->getMessage());

			$message = $this->MMessage->getOneMessage('CMNE000107');
			$this->errors['Check'][] =   $message;

			return true;
		}

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
			
			$this->queryWithPDOLog($stmt,$pdo,$sql,"検索最大件数取得");
			
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
				$SearchMaxCnt =  $result['SEARCH_MAX_CNT'];
			}
			
			$pdo = null;
			
			return $SearchMaxCnt;

		} catch (Exception $e) {
			
			$this->printLog("fatal", "例外発生", "WEX093", $e->getMessage());
			$message = $this->MMessage->getOneMessage('CMNE000107');
			$this->errors['Check'][] =   $message;

			$pdo = null;
		}

	}


	/**
	 * 作業実績日別データ取得
	 * @access   public
	 * @return   作業実績日別データ
	 */
	public function getAbcDbDayList($bnri_dai_cd, $bnri_cyu_cd,  $bnri_sai_cd, $date_from, $date_to,
	                               $kbn_ktd_mktk, $kbn_hissu_wk, $kbn_gyomu,
	                               $kbn_fukakachi, $kbn_senmon , $kbn_ktd_type, $SearchMaxCnt) {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$common = $this->MCommon;
				
			/* ▼取得カラム設定 */
			$fields = '*';
			
			/* ▼検索条件設定 */
			$condition  = "NINUSI_CD       = ". $common->encloseString($this->_ninusi_cd);
			$condition .= " AND SOSIKI_CD  = ". $common->encloseString($this->_sosiki_cd);

			//対象期間
			$condition .= " AND YMD >= ". $common->encloseString($date_from);
			$condition .= " AND YMD <= ". $common->encloseString($date_to);

			//大分類コード
			if ($bnri_dai_cd != null){
				$condition .= " AND BNRI_DAI_CD = ". $common->encloseString($bnri_dai_cd);
			}
			//中分類コード
			if ($bnri_cyu_cd != null){
				$condition .= " AND BNRI_CYU_CD = ". $common->encloseString($bnri_cyu_cd);
			}
			//細分類コード
			if ($bnri_sai_cd != null){
				$condition .= " AND BNRI_SAI_CD = ". $common->encloseString($bnri_sai_cd);
			}

			//活動目的
			if ($kbn_ktd_mktk != null){
				$condition .= " AND KBN_KTD_MKTK = ". $common->encloseString($kbn_ktd_mktk);
			}
			//必須作業
			if ($kbn_hissu_wk != null){
				$condition .= " AND KBN_HISSU_WK = ". $common->encloseString($kbn_hissu_wk);
			}
			//業務区分
			if ($kbn_gyomu != null){
					$condition .= " AND KBN_GYOMU = ". $common->encloseString($kbn_gyomu);
			}
			//付加価値性
			if ($kbn_fukakachi != null){
				$condition .= " AND KBN_FUKAKACHI = ". $common->encloseString($kbn_fukakachi);
			}
			//専門区分
			if ($kbn_senmon != null){
				$condition .= " AND KBN_SENMON = ". $common->encloseString($kbn_senmon);
			}
			//活動タイプ
			if ($kbn_ktd_type != null){
				$condition .= " AND KBN_KTD_TYPE = ". $common->encloseString($kbn_ktd_type);
			}
			
			/* ▼拠点情報取得 */
			$sql  = 'SELECT ';
			$sql .= "$fields ";
			$sql .= 'FROM V_WEX093_T_ABC_DB_DAY_LST ';
			$sql .= 'WHERE ';
			$sql .= $condition;
			
			$this->queryWithPDOLog($stmt, $pdo, $sql,"日別作業実績情報　取得");
			
			$data = array();
			
			while ($entity = $stmt->fetch(PDO::FETCH_ASSOC)) {
				array_push($data, $entity);
			}
			
			if ( count($data) > $SearchMaxCnt ) {
				$message = vsprintf($this->MMessage->getOneMessage('CMNE000102'), array($SearchMaxCnt, count($data)));
				$this->errors['Check'][] =   $message;
			}
			
			$pdo = null;

			return $data;

		} catch (Exception $e){
	
			$this->printLog("fatal", "例外発生", "WEX093", $e->getMessage());
			$message = $this->MMessage->getOneMessage('CMNE000107');
			$this->errors['Check'][] =   $message;
			
			$pdo = null;

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


}

?>
 