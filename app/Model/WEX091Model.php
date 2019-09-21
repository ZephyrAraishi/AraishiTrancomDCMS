<?php
/**
 * WEX091Model
 *
 * 月別作業実績参照
 *
 * @category      WEX
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');
App::uses('Validation', 'Utility');

class WEX091Model extends DCMSAppModel{

	public $name = 'WEX091Model';
	public $useTable = false;

	public $errors = array();

	/** この値より検索結果が多い場合は、再検索をうながす */
	const WEX091_RESULT_LIMIT = 100;

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
	 * 検索前のチェックを行う
	 * @param unknown $conditions
	 * @return boolean true..OK
	 */
	public function checkBeforeSearch($conditions, $SearchMaxCnt) {
		// 入力チェック
		if(!$this->validateConditions($conditions)) {
			return false;
		}

		// その他のチェック
		if(!$this->checkVarious($conditions, $SearchMaxCnt)) {
			return false;
		}

		return true;
	}

	/**
	 * 入力された検索条件の値を検証する
	 * @param unknown $conditions
	 * @return boolean true..OK
	 */
	public function validateConditions($conditions) {
		if (!isset($conditions)) {
			return true;
		}

		$message = "";

		$period_from = $conditions['period_from'];
		$period_to = $conditions['period_to'];

		//必須チェック

		// 期間From
		if (!Validation::notEmpty($period_from)) {
			$message = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array('対象期間（自）'));
			$this->errors['Check'][] = $message;
		}

		// 期間To
		if (!Validation::notEmpty($period_to)) {
			$message = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array('対象期間（至）'));
			$this->errors['Check'][] = $message;
		}

		// 日付チェック

		// 期間From
		if (Validation::notEmpty($period_from)) {
			if (!$this->MCommon->isDateFormatYM($period_from)) {
				// フォーマットチェック
				$message = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array('対象期間（自）'));
				$this->errors['Check'][] = $message;
			} else {
				if (!$this->MCommon->isValidDate($period_from . '01')) {
					// 日付存在チェック
					$message = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array('対象期間（自）'));
					$this->errors['Check'][] = $message;
				}
			}
		}

		// 期間To
		if (Validation::notEmpty($period_to)) {
			if (!$this->MCommon->isDateFormatYM($period_to)) {
				// フォーマットチェック
				$message = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array('対象期間（至）'));
				$this->errors['Check'][] = $message;
			} else {
				if (!$this->MCommon->isValidDate($period_to . '01')) {
					// 日付存在チェック
					$message = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array('対象期間（至）'));
					$this->errors['Check'][] = $message;
				}
			}
		}

		if (count($this->errors) != 0) {
			// 以降のチェックは正しい日付が入力されていることを前提としているため
			// 妥当性エラーの時点でリターンする
			return false;
		}

		// 相関チェック

		// 期間From < 期間To かどうか
		if ($this->MCommon->comparisonDate($period_from, '>', $period_to)) {
			$message = vsprintf($this->MMessage->getOneMessage('WEXE090002'), array('対象期間（至）', '対象期間（自）'));
			$this->errors['Check'][] = $message;
		}

		if (count($this->errors) != 0) {
			return false;
		}

		return true;
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
			
			$this->printLog("fatal", "例外発生", "WEX091", $e->getMessage());
			$message = $this->MMessage->getOneMessage('CMNE000107');
			$this->errors['Check'][] =   $message;

			$pdo = null;
		}

	}


	/**
	 * 必要なチェックを行う
	 * @param unknown $conditions
	 * @return boolean true..OK
	 */
	public function checkVarious($conditions, $SearchMaxCnt) {
		// 検索結果数チェック

		$ThisCount = $this->getAbcDbMonthList($conditions, true)['COUNT'];
		if($ThisCount > $SearchMaxCnt) {
 			$message = vsprintf($this->MMessage->getOneMessage('CMNE000102'), array($SearchMaxCnt, $ThisCount));
			$this->errors['Check'][] = $message;
		}

		if (count($this->errors) != 0) {
			return false;
		}

		return true;
	}

	/**
	 * 月別作業実績を取得
	 *
	 * @access   public
	 * @param $conditions 検索条件
	 * @param $count カウントをとりたい場合はtrueを渡す
	 * @return 月別作業実績情報。$countがtrueの場合はarray('COUNT' => 件数)。
	 */
	public function getAbcDbMonthList($conditions, $count = false) {

		$pdo = null;

		try{
			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$common = $this->MCommon;

			// 検索条件
			// 対象期間From
			$period_from = $conditions['period_from'];
			// 対象期間To
			$period_to = $conditions['period_to'];
			// 工程
			$koteiCd = $conditions['kotei'];
			$koteiDaiCd = '';
			$koteiCyuCd = '';
			$koteiSaiCd = '';
			if (!empty($koteiCd)) {
				$koteiCdArray = explode('_', $koteiCd);
				$koteiDaiCd = $koteiCdArray[0];
				if (mb_strlen($koteiCd) > 3) {
					$koteiCyuCd = $koteiCdArray[1];
				} else {
					$koteiCyuCd = "";
				}
				if (mb_strlen($koteiCd) > 7) {
					$koteiSaiCd = $koteiCdArray[2];
				} else {
					$koteiSaiCd = "";
				}
			}
			// 目的
			$kbn_ktd_mktk = $conditions['kbn_ktd_mktk'];
			// 必須
			$kbn_hissu_wk = $conditions['kbn_hissu_wk'];
			// 業務
			$kbn_gyomu = $conditions['kbn_gyomu'];
			// 価値
			$kbn_fukakachi = $conditions['kbn_fukakachi'];
			// 専門
			$kbn_senmon = $conditions['kbn_senmon'];
			// タイプ
			$kbn_ktd_type = $conditions['kbn_ktd_type'];

			$sql  = 'SELECT';
			$sql .= $count ? " COUNT(*) AS COUNT" : " *";
			$sql .= ' FROM V_WEX091_T_ABC_DB_MONTH_LST ';
			$sql .= ' WHERE ';
			$sql .= ' NINUSI_CD = ' . $common->encloseString($this->_ninusi_cd);
			$sql .= ' AND SOSIKI_CD = ' . $common->encloseString($this->_sosiki_cd);
			$sql .= " AND YM BETWEEN $period_from AND $period_to ";
			if (strlen($koteiDaiCd) > 0) $sql .= ' AND BNRI_DAI_CD = ' . $common->encloseString($koteiDaiCd);
			if (strlen($koteiCyuCd) > 0) $sql .= ' AND BNRI_CYU_CD = ' . $common->encloseString($koteiCyuCd);
			if (strlen($koteiSaiCd) > 0) $sql .= ' AND BNRI_SAI_CD = ' . $common->encloseString($koteiSaiCd);
			if (strlen($kbn_ktd_mktk) > 0) $sql .= ' AND KBN_KTD_MKTK = ' . $common->encloseString($kbn_ktd_mktk);
			if (strlen($kbn_hissu_wk) > 0) $sql .= ' AND KBN_HISSU_WK = ' . $common->encloseString($kbn_hissu_wk);
			if (strlen($kbn_gyomu) > 0) $sql .= ' AND KBN_GYOMU = ' . $common->encloseString($kbn_gyomu);
			if (strlen($kbn_fukakachi) > 0) $sql .= ' AND KBN_FUKAKACHI = ' . $common->encloseString($kbn_fukakachi);
			if (strlen($kbn_senmon) > 0) $sql .= ' AND KBN_SENMON = ' . $common->encloseString($kbn_senmon);
			if (strlen($kbn_ktd_type) > 0) $sql .= ' AND KBN_KTD_TYPE = ' . $common->encloseString($kbn_ktd_type);

			$this->queryWithPDOLog($stmt, $pdo, $sql,"月別作業実績情報　取得");

			if($count) {
				return $stmt->fetch(PDO::FETCH_ASSOC);
			}

			$result = array();
			while ($entity = $stmt->fetch(PDO::FETCH_ASSOC)) {
				array_push($result, $entity);
			}

			$pdo = null;

			return $result;

		} catch (Exception $e) {
			$this->printLog("fatal", "例外発生", "WEX091", $e->getMessage());
			$message = $this->MMessage->getOneMessage('CMNE000107');
			$this->errors['Check'][] =   $message;
			
			$pdo = null;
		}

		return array();
	}

}

?>