<?php
/**
 * WEX010
 *
 * 荷主基本情報登録画面
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');
App::uses('Validation', 'Utility');

class WEX010Model extends DCMSAppModel {

	public $name = 'WEX010Model';
	public $useTable = false;
	public $validate;

	// 正規表現定数
	const postal_regex = '/^\d{3}\-\d{4}$/';
	const phone_regex  = '/^[0-9\-()+]{1,20}$/';

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
       
		//メッセージマスタ呼び出し
		$this->MMessage = new MMessage($ninusi_cd,$sosiki_cd,$kino_id);

		//共通関数モデル
		$this->MCommon = new MCommon($ninusi_cd,$sosiki_cd,$kino_id);
		
    }

	/**
	 * 荷主基本情報データ取得
	 * @access   public
	 * @return   荷主契約情報データ
	 */
	public function getNinusiInfo() {
	
		try {

			/* 取得カラム設定 */
			$fields = '*';
	
			// 検索条件設定
	 		$condition  = "NINUSI_CD = '". $this->_ninusi_cd. "'";
	 		$condition .=" AND SOSIKI_CD = '". $this->_sosiki_cd. "'" ;
	
			$sql  = 'SELECT ';
			$sql .= "$fields ";
			$sql .= 'FROM V_WEX010_M_NINUSI_SOSIKI M_NINUSI_SOSIKI ';
			$sql .= 'WHERE ';
			$sql .= $condition;
	
	  		$data = $this->queryWithLog($sql, null, "荷主組織情報データ取得");
	
			return $data['0'];
		
		} catch (Exception $e) {
			
			$this->printLog("fatal", "例外発生", "WEX010", $e->getMessage());
		}
		
		return null;
	}

	/**
	 * 更新前チェック組織情報
	 *
	 * @param unknown $data
	 * @return boolean
	 */
	public function checkBeforeUpdate($data){
	
		try {

			// 更新前チェック荷主基本情報
			if($this->checkBeforeUpdateForNinusiKihon($data) == false) {
				
				return false;
			}
	
			// 更新前チェック組織情報
			if($this->checkBeforeUpdateForSosiki($data) == false) {
				
				return false;
			}

			return true;
		
		} catch (Exception $e) {
		
			$this->printLog("fatal", "例外発生", "WEX010", $e->getMessage());
		}
		
		return false;

	}

	/**
	 * 更新前チェック荷主基本情報
	 *
	 * @param unknown $data
	 * @return boolean
	 */
	private function checkBeforeUpdateForNinusiKihon($data){
		// 入力チェック
		// 荷主名
		if(!Validation::notEmpty($data['M_NINUSI_SOSIKI']['NINUSI_NM'])){
			$msg = vsprintf($this->MMessage->getOneMessage('CMNE000001'), '会社名');
			$this->errors['Check'][] =  $msg;
			
		} else if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['NINUSI_NM'], 20)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'),  array('会社名', '20'));
			$this->errors['Check'][] =  $msg;
		}
		// 会社名略
		if(!Validation::notEmpty($data['M_NINUSI_SOSIKI']['NINUSI_RYAKU'])){
			$msg = vsprintf($this->MMessage->getOneMessage('CMNE000001'), '会社名略');
			$this->errors['Check'][] =  $msg;
		} else if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['NINUSI_RYAKU'], 6)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'),  array('会社名略', '6'));
			$this->errors['Check'][] =  $msg;
		}
		// 荷主郵便番号
		if(Validation::notEmpty($data['M_NINUSI_SOSIKI']['NINUSI_POST_NO'])
		&& !Validation::postal($data['M_NINUSI_SOSIKI']['NINUSI_POST_NO'], self::postal_regex, 'ja')){
		
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010003'), '本社郵便番号');
			$this->errors['Check'][] =  $msg;
		}
		// 住所１
		if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['NINUSI_ADDR_1'], 40)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'),  array('本社住所１', '40'));
			$this->errors['Check'][] =  $msg;
		}
		// 住所２
		if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['NINUSI_ADDR_2'], 40)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'),  array('本社住所２', '40'));
			$this->errors['Check'][] =  $msg;
		}
		// 住所３
		if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['NINUSI_ADDR_3'], 40)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'),  array('本社住所３', '40'));
			$this->errors['Check'][] =  $msg;
		}
		// TEL
		if(Validation::notEmpty($data['M_NINUSI_SOSIKI']['NINUSI_TEL'])
		&& !Validation::phone($data['M_NINUSI_SOSIKI']['NINUSI_TEL'], self::phone_regex, 'ja')){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010003'), '本社TEL');
			$this->errors['Check'][] =  $msg;
		}
		// FAX
		if(Validation::notEmpty($data['M_NINUSI_SOSIKI']['NINUSI_FAX'])
		&& !Validation::phone($data['M_NINUSI_SOSIKI']['NINUSI_FAX'], self::phone_regex, 'ja')){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010003'), '本社FAX');
			$this->errors['Check'][] =  $msg;
		}
		// 代表者
		if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['NINUSI_DAI_NM'], 20)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'),  array('代表者', '20'));
			$this->errors['Check'][] =  $msg;
		}
		// 設立
		if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['NINUSI_EST_YM'], 10)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'),  array('設立', '10'));
			$this->errors['Check'][] =  $msg;
		}
		// 資本金
		if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['NINUSI_SIHON'], 15)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'),  array('資本金', '15'));
			$this->errors['Check'][] =  $msg;
		} else if(Validation::notEmpty($data['M_NINUSI_SOSIKI']['NINUSI_SIHON'])
				&& !Validation::numeric($data['M_NINUSI_SOSIKI']['NINUSI_SIHON'])){
			$msg = vsprintf($this->MMessage->getOneMessage('CMNE000002'),  '資本金');
			$this->errors['Check'][] =  $msg;
		}
		// 事業内容
		if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['NINUSI_JIGYO'], 100)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'), array('事業内容', '100'));
			$this->errors['Check'][] =  $msg;
		}
		
		if (!empty($this->errors)) {
			return false;
		}
		
		return true;
	}

	/**
	 * 更新前チェック組織情報
	 *
	 * @param unknown $data
	 * @return boolean
	 */
	private function checkBeforeUpdateForSosiki($data){

		// 倉庫部署名
		if(!Validation::notEmpty($data['M_NINUSI_SOSIKI']['SOSIKI_NM'])){
			$msg = vsprintf($this->MMessage->getOneMessage('CMNE000001'), '倉庫部署名');
			$this->errors['Check'][] =  $msg;
		} else if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['SOSIKI_NM'], 20)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'),  array('倉庫部署名', '20'));
			$this->errors['Check'][] =  $msg;
		}
		// 倉庫部署名略
		if(!Validation::notEmpty($data['M_NINUSI_SOSIKI']['SOSIKI_RYAKU'])){
			$msg = vsprintf($this->MMessage->getOneMessage('CMNE000001'), '倉庫部署名略');
			$this->errors['Check'][] =  $msg;
		} else if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['SOSIKI_RYAKU'], 6)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'),  array('倉庫部署名略', '6'));
			$this->errors['Check'][] =  $msg;
		}
		// 組織郵便番号
		if(Validation::notEmpty($data['M_NINUSI_SOSIKI']['SOSIKI_POST_NO'])
		&& !Validation::postal($data['M_NINUSI_SOSIKI']['SOSIKI_POST_NO'], self::postal_regex, 'ja')){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010003'), '担当部署郵便番号');
			$this->errors['Check'][] =  $msg;
		}
		// 住所１
		if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['SOSIKI_ADDR_1'], 40)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'),  array('担当部署住所１', '40'));
			$this->errors['Check'][] =  $msg;
		}
		// 住所２
		if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['SOSIKI_ADDR_2'], 40)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'),  array('担当部署住所２', '40'));
			$this->errors['Check'][] =  $msg;
		}
		// 住所３
		if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['SOSIKI_ADDR_3'], 40)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'),  array('担当部署住所３', '40'));
			$this->errors['Check'][] =  $msg;
		}
		// TEL
		if(Validation::notEmpty($data['M_NINUSI_SOSIKI']['SOSIKI_TEL'])
		&& !Validation::phone($data['M_NINUSI_SOSIKI']['SOSIKI_TEL'], self::phone_regex, 'ja')){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010003'), '担当部署TEL');
			$this->errors['Check'][] =  $msg;
		}
		// FAX
		if(Validation::notEmpty($data['M_NINUSI_SOSIKI']['SOSIKI_FAX'])
		&& !Validation::phone($data['M_NINUSI_SOSIKI']['SOSIKI_FAX'], self::phone_regex, 'ja')){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010003'), '担当部署FAX');
			$this->errors['Check'][] =  $msg;
		}
		// 倉庫責任者
		if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['SOSIKI_DAI_NM'], 20)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'),  array('倉庫責任者', '20'));
			$this->errors['Check'][] =  $msg;
		}
		// 請求部署名
		if(!Validation::notEmpty($data['M_NINUSI_SOSIKI']['SEIKYU_BUSYO_NM'])){
			$msg = vsprintf($this->MMessage->getOneMessage('CMNE000001'), '請求部署名');
			$this->errors['Check'][] =  $msg;
		} else if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['SEIKYU_BUSYO_NM'], 20)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'),  array('請求部署名', '20'));
			$this->errors['Check'][] =  $msg;
		}
		// 請求部署郵便番号
		if(Validation::notEmpty($data['M_NINUSI_SOSIKI']['SEIKYU_POST_NO'])
		&& !Validation::postal($data['M_NINUSI_SOSIKI']['SEIKYU_POST_NO'], self::postal_regex, 'ja')){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010003'), '請求部署郵便番号');
			$this->errors['Check'][] =  $msg;
		}
		// 請求部署住所１
		if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['SEIKYU_ADDR_1'], 40)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'),  array('請求部署住所１', '40'));
			$this->errors['Check'][] =  $msg;
		}
		// 請求部署住所２
		if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['SEIKYU_ADDR_2'], 40)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'),  array('請求部署住所２', '40'));
			$this->errors['Check'][] =  $msg;
		}
		// 請求部署住所３
		if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['SEIKYU_ADDR_3'], 40)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'),  array('請求部署住所３', '40'));
			$this->errors['Check'][] =  $msg;
		}
		// 請求部署TEL
		if(Validation::notEmpty($data['M_NINUSI_SOSIKI']['SEIKYU_TEL'])
		&& !Validation::phone($data['M_NINUSI_SOSIKI']['SEIKYU_TEL'], self::phone_regex, 'ja')){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010003'),  array('請求部署TEL'));
			$this->errors['Check'][] =  $msg;
		}
		// 請求部署FAX
		if(Validation::notEmpty($data['M_NINUSI_SOSIKI']['SEIKYU_FAX'])
		&& !Validation::phone($data['M_NINUSI_SOSIKI']['SEIKYU_FAX'], self::phone_regex, 'ja')){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010003'), '請求部署FAX');
			$this->errors['Check'][] =  $msg;
		}
		// 請求担当者
		if(!Validation::maxLength($data['M_NINUSI_SOSIKI']['SEIKYU_DAI_NM'], 20)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE010002'),  array('請求担当者', '20'));
			$this->errors['Check'][] =  $msg;
		}
		
		if (!empty($this->errors)) {
			return false;
		}
		
		return true;
	}

	/**
	 * 更新処理
	 *
	 * @param unknown $data
	 */
	public function update($data){

		try {
			$common = $this->MCommon;

			$dataSource = $this->getDataSource();

			$dataSource->begin();

			$params = "";
 	 		$params .=  " '".$this->_ninusi_cd."'";
  			$params .=  ",'".$this->_sosiki_cd."'," ;

			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['NINUSI_NM']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['NINUSI_NM'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['NINUSI_RYAKU']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['NINUSI_RYAKU'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['NINUSI_POST_NO']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['NINUSI_POST_NO'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['NINUSI_ADDR_1']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['NINUSI_ADDR_1'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['NINUSI_ADDR_2']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['NINUSI_ADDR_2'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['NINUSI_ADDR_3']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['NINUSI_ADDR_3'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['NINUSI_TEL']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['NINUSI_TEL'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['NINUSI_FAX']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['NINUSI_FAX'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['NINUSI_DAI_NM']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['NINUSI_DAI_NM'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['NINUSI_EST_YM']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['NINUSI_EST_YM'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['NINUSI_SIHON']) ? $data['M_NINUSI_SOSIKI']['NINUSI_SIHON'] . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['NINUSI_JIGYO']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['NINUSI_JIGYO'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['SOSIKI_NM']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['SOSIKI_NM'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['SOSIKI_RYAKU']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['SOSIKI_RYAKU'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['SOSIKI_POST_NO']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['SOSIKI_POST_NO'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['SOSIKI_ADDR_1']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['SOSIKI_ADDR_1'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['SOSIKI_ADDR_2']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['SOSIKI_ADDR_2'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['SOSIKI_ADDR_3']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['SOSIKI_ADDR_3'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['SOSIKI_TEL']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['SOSIKI_TEL'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['SOSIKI_FAX']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['SOSIKI_FAX'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['SOSIKI_DAI_NM']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['SOSIKI_DAI_NM'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['SEIKYU_BUSYO_NM']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['SEIKYU_BUSYO_NM'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['SEIKYU_POST_NO']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['SEIKYU_POST_NO'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['SEIKYU_ADDR_1']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['SEIKYU_ADDR_1'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['SEIKYU_ADDR_2']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['SEIKYU_ADDR_2'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['SEIKYU_ADDR_3']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['SEIKYU_ADDR_3'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['SEIKYU_TEL']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['SEIKYU_TEL'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['SEIKYU_FAX']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['SEIKYU_FAX'])) . "," : "NULL,";
			$params .= Validation::notEmpty($data['M_NINUSI_SOSIKI']['SEIKYU_DAI_NM']) ? $common->encloseString(Sanitize::clean($data['M_NINUSI_SOSIKI']['SEIKYU_DAI_NM'])) . "," : "NULL,";
			$params .=  "'".$_SESSION['staff_cd']."'";

//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['NINUSI_NM']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['NINUSI_RYAKU']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['NINUSI_POST_NO']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['NINUSI_ADDR_1']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['NINUSI_ADDR_2']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['NINUSI_ADDR_3']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['NINUSI_TEL']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['NINUSI_FAX']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['NINUSI_DAI_NM']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['NINUSI_EST_YM']."'";
//			if(Validation::notEmpty($data['M_NINUSI_SOSIKI']['NINUSI_SIHON'])){
//				$params .=  ", ".$data['M_NINUSI_SOSIKI']['NINUSI_SIHON'];
//			} else {
//				$params .=  ", null";
//			}
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['NINUSI_JIGYO']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['SOSIKI_NM']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['SOSIKI_RYAKU']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['SOSIKI_POST_NO']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['SOSIKI_ADDR_1']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['SOSIKI_ADDR_2']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['SOSIKI_ADDR_3']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['SOSIKI_TEL']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['SOSIKI_FAX']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['SOSIKI_DAI_NM']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['SEIKYU_BUSYO_NM']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['SEIKYU_POST_NO']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['SEIKYU_ADDR_1']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['SEIKYU_ADDR_2']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['SEIKYU_ADDR_3']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['SEIKYU_TEL']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['SEIKYU_FAX']."'";
//			$params .=  ",'".$data['M_NINUSI_SOSIKI']['SEIKYU_DAI_NM']."'";
//			$params .=  ",'".$_SESSION['staff_cd']."'";
			$params .=  ',@r';

			$sql = "CALL P_WEX010_UPD_NINUSI_SOSIKI($params)";

			$this->queryWithLog($sql, $params, "荷主組織情報更新");

			$data = $this->query("SELECT @r",false);
			if ($data[0][0]['@r'] != 0) {
 				$dataSource->rollback();
 				$message = $this->MMessage->getOneMessage('CMNE000107');
 				$this->errors['Check'][] =  $message;
				return false;
			}
 			$this->printLog('info', '荷主基本情報更新', "WEXO010", '正常終了');
 			$dataSource->commit();
 			
 			return true;
		} catch (Exception $e) {
		
			$this->printLog("fatal", "例外発生", "WEX010", $e->getMessage());
			$dataSource->rollback();
			throw $e;
		}
		
		//例外発生の場合、エラー
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}
}

?>
