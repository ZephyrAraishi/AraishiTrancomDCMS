<?php
/**
 * WEX040Model
 *
 * 中分類マスタ設定
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');

class WEX040Model extends DCMSAppModel{

	public $name = 'WEX040Model';
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
	 * 中分類マスタ取得
	 * @access   public
	 * @return   中分類マスタ
	 */
	public function getMBnriCyuLst($bnri_dai_cd, $bnri_cyu_cd, $bnri_cyu_nm, $bnri_cyu_exp) {
		/* ▼取得カラム設定 */
		$fields = '*';

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
			$condition .= " AND BNRI_CYU_CD LIKE :bnri_cyu_cd " ;
			$conditionVal['bnri_cyu_cd'] = $bnri_cyu_cd . "%";
		}
		//中分類名称
		if ($bnri_cyu_nm != null){
			$condition .= " AND BNRI_CYU_NM LIKE :bnri_cyu_nm " ;
			$conditionVal['bnri_cyu_nm'] = "%" . $bnri_cyu_nm . "%";
		}
		//中分類説明
		if ($bnri_cyu_exp != null){
			$condition .= " AND BNRI_CYU_EXP LIKE :bnri_cyu_exp " ;
			$conditionVal['bnri_cyu_exp'] = "%" . $bnri_cyu_exp . "%";
		}

		/* ▼拠点情報取得 */
		$sql  = 'SELECT ';
		$sql .= "$fields ";
		$sql .= 'FROM V_WEX040_M_BNRI_CYU_LST ';
		$sql .= 'WHERE ';
		$sql .= $condition;
		$sql .= $conditionKey;
		$sql .= " ORDER BY";
		$sql .= "   BNRI_DAI_CD,";
		$sql .= "   BNRI_CYU_CD";

		$data = $this->query($sql, $conditionVal, false);
		$data = self::editData($data);

		return $data;
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


	public function checkCyuMst($data) {

		//　入力チェック

		$message = "";
		$count = 0;			//行
		$count2 = 0;		//キー重複チェック用
		$count3 = 0;		//キー重複チェック用
		$count4 = 0;		//更新対象件数取得
		foreach($data as $obj) {

			$count++;

			$cellsArray = $obj->cells;
			$dataArray = $obj->data;

			$BNRI_CYU_CD = $cellsArray[1];
			$BNRI_CYU_NM = $cellsArray[2];
			$BNRI_CYU_RYAKU = $cellsArray[3];
			$BNRI_CYU_EXP = $cellsArray[4];
			$KBN_TANI = $cellsArray[5];
			$KBN_URIAGE = $cellsArray[6];
			$BNRI_CYU_CD_CNT = $cellsArray[7];
			$LINE_COUNT = $dataArray[0];
			$CHANGED = $dataArray[1];
			$KBN_TANI_CD = $dataArray[2];
			$KBN_URIAGE_CD = $dataArray[3];
			$BNRI_DAI_CD = $dataArray[4];


			//更新対象件数取得
			if ($CHANGED != '0'
			and $CHANGED != '5') {
				$count4++;
			}

			//追加と修正のみ
			if ($CHANGED == '1'
			or  $CHANGED == '2') {

				//必須チェック
				if (empty($BNRI_DAI_CD)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '大分類コード'));
					$this->errors['Check'][] =   $message;
				}
				if (empty($BNRI_CYU_CD)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '中分類コード'));
					$this->errors['Check'][] =   $message;
				}
				if (empty($BNRI_CYU_NM)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '中分類名称'));
					$this->errors['Check'][] =   $message;
				}
				if (empty($BNRI_CYU_RYAKU)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '中分類略称'));
					$this->errors['Check'][] =   $message;
				}
				if (empty($KBN_TANI_CD)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '単位区分'));
					$this->errors['Check'][] =   $message;
				}
				if (empty($KBN_URIAGE_CD)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '売上区分'));
					$this->errors['Check'][] =   $message;
				}

				//桁数チェック
				if (strlen($BNRI_DAI_CD) != 3) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE040002'), array($count, '大分類コード', '3'));
					$this->errors['Check'][] =   $message;
				}
				if (strlen($BNRI_CYU_CD) != 3) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE040002'), array($count, '中分類コード', '3'));
					$this->errors['Check'][] =   $message;
				}
				if (mb_strlen($BNRI_CYU_NM) > 20) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE040004'), array($count, '中分類名称', '20'));
					$this->errors['Check'][] =   $message;
				}
				if (mb_strlen($BNRI_CYU_RYAKU) > 6) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE040004'), array($count, '中分類略称', '6'));
					$this->errors['Check'][] =   $message;
				}
				if (mb_strlen($BNRI_CYU_EXP) > 40) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE040004'), array($count, '中分類説明', '40'));
					$this->errors['Check'][] =   $message;
				}

				//数字チェック
				if (!preg_match("/^[0-9]+$/",$BNRI_DAI_CD)) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE040003'), array($count, '大分類コード'));
					$this->errors['Check'][] =   $message;
				}
				if (!preg_match("/^[0-9]+$/",$BNRI_CYU_CD)) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE040003'), array($count, '中分類コード'));
					$this->errors['Check'][] =   $message;
				}

				//"000"は入力不可
				if ($BNRI_CYU_CD == '000' ) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE040006'), array($count, '中分類コード', '001'));
					$this->errors['Check'][] =   $message;
				}

			}
		}

		if (count($this->errors) != 0) {
			return false;
		}
		if ($count4 == 0) {
			$message = "更新データがありません。";
			$this->errors['Check'][] = $message;
			return false;
		}

		$count = 0;			//行
		$count2 = 0;		//キー重複チェック用
		$count3 = 0;		//キー重複チェック用

		foreach($data as $obj) {

			$count++;

			$cellsArray = $obj->cells;
			$dataArray = $obj->data;

			$CHANGED = $dataArray[1];
			$BNRI_DAI_CD = $dataArray[4];
			$BNRI_CYU_CD = $cellsArray[1];

			// =================================================================
			// キー重複チェック
			// =================================================================

			//追加のみ
			if ($CHANGED == '2') {

				//追加データのみにチェック
				$count3 = 0;
				$count2 = count($data);
				for ($i = 1; $i < $count2; $i++) {
					$cellsArray2 = $data[$i]->cells;
					$dataArray2 = $data[$i]->data;
					if ($dataArray2[4] == $BNRI_DAI_CD
							and $cellsArray2[1] == $BNRI_CYU_CD
							and $dataArray2[1]  == '2') {
						$count3++;
					}
				}

				if ($count3 > 1) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010007'), array($count));
					$this->errors['Check'][] =   $message;
				}
			}
		}

		if (count($this->errors) != 0) {
			return false;
		}

		$count = 0;			//行

		foreach($data as $obj) {

			$count++;

			$cellsArray = $obj->cells;
			$dataArray = $obj->data;

			$CHANGED = $dataArray[1];
			$BNRI_DAI_CD = $dataArray[4];
			$BNRI_CYU_CD = $cellsArray[1];

			// =================================================================
			// キー重複チェック
			// =================================================================

			//追加の場合
			if ($CHANGED == '2') {

				//親が存在するかチェック
				$dataForCheck = $this->getMBnriDaiForCheck($BNRI_DAI_CD);
				if (empty($dataForCheck)) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE040009'), array($count));
					$this->errors['Check'][] =   $message;
					continue;
				}

				//データベースにチェック
				$dataForCheck = $this->getMBnriCyuForCheck($BNRI_DAI_CD, $BNRI_CYU_CD);
				if (!empty($dataForCheck)) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE040005'), array($count, '中分類コード'));
					$this->errors['Check'][] =   $message;
					continue;
				}
			}

			//更新の場合
			if ($CHANGED == 1) {
				//データベースにチェック
				$dataForCheck = $this->getMBnriCyuForCheck($BNRI_DAI_CD, $BNRI_CYU_CD);
				if (empty($dataForCheck)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010008'), array($count));
					$this->errors['Check'][] =   $message;
					continue;
				}
			}
		}

		if (count($this->errors) != 0) {
			return false;
		}

		return true;
	}



	public function setCyuMst($arrayList, $staff_cd) {

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
			$sql .= "'040',";
			$sql .= "1";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'WEX040 排他制御オン');

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

					$BNRI_CYU_CD     = $cellsArray[1];
					$BNRI_CYU_NM     = Sanitize::clean($cellsArray[2]);
					$BNRI_CYU_RYAKU  = Sanitize::clean($cellsArray[3]);
					$BNRI_CYU_EXP    = Sanitize::clean($cellsArray[4]);
					$KBN_TANI        = Sanitize::clean($cellsArray[5]);
					$KBN_URIAGE      = Sanitize::clean($cellsArray[6]);
					$BNRI_CYU_CD_CNT = $cellsArray[7];
					$LINE_COUNT      = $dataArray[0];
					$CHANGED         = $dataArray[1];
					$KBN_TANI_CD     = $dataArray[2];
					$KBN_URIAGE_CD   = $dataArray[3];
					$BNRI_DAI_CD     = $dataArray[4];

					//　０：なし　　　　　　何もしない
					//　１：　修正　　　　　UPDATE
					//　２：　追加　　　　　INSERT
					//　３：　削除　　　　　DELETE
					//　４：　修正→削除　　DELETE
					//　５：　追加→削除　　何もしない


					//更新の場合
					if ($CHANGED == 1) {

						//中分類マスタを更新する
						$sql = "CALL P_WEX040_UPD_CYU_BNRI(";
						$sql .= "'" . $this->_ninusi_cd . "',";
						$sql .= "'" . $this->_sosiki_cd . "',";
						$sql .= "'" . $staff_cd . "',";
						$sql .= "'" . $BNRI_DAI_CD . "',";
						$sql .= "'" . $BNRI_CYU_CD . "',";
						$sql .= "'" . $BNRI_CYU_NM . "',";
						$sql .= "'" . $BNRI_CYU_RYAKU . "',";
						$sql .= "'" . $BNRI_CYU_EXP . "',";
						$sql .= "'" . $KBN_TANI_CD . "',";
						$sql .= "'" . $KBN_URIAGE_CD . "',";
						$sql .= "@return_cd";
						$sql .= ")";

						$this->execWithPDOLog($pdo2,$sql, '中分類マスタ　更新');

						$sql = "SELECT";
						$sql .= " @return_cd";

						$this->queryWithPDOLog($stmt,$pdo2,$sql, '中分類マスタ　更新');

						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$return_cd = $result["@return_cd"];

						if ($return_cd == "1") {

							throw new Exception('中分類マスタ　更新');
						}

					//新規の場合
					} else if ($CHANGED == 2) {

						//中分類マスタを追加する
						$sql = "CALL P_WEX040_INS_CYU_BNRI(";
						$sql .= "'" . $this->_ninusi_cd . "',";
						$sql .= "'" . $this->_sosiki_cd . "',";
						$sql .= "'" . $staff_cd . "',";
						$sql .= "'" . $BNRI_DAI_CD . "',";
						$sql .= "'" . $BNRI_CYU_CD . "',";
						$sql .= "'" . $BNRI_CYU_NM . "',";
						$sql .= "'" . $BNRI_CYU_RYAKU . "',";
						$sql .= "'" . $BNRI_CYU_EXP . "',";
						$sql .= "'" . $KBN_TANI_CD . "',";
						$sql .= "'" . $KBN_URIAGE_CD . "',";
						$sql .= "@return_cd";
						$sql .= ")";

						$this->execWithPDOLog($pdo2,$sql, '中分類マスタ　追加');

						$sql = "SELECT";
						$sql .= " @return_cd";

						$this->queryWithPDOLog($stmt,$pdo2,$sql, '中分類マスタ　追加');

						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$return_cd = $result["@return_cd"];

						if ($return_cd == "1") {

							throw new Exception('中分類マスタ　追加');
						}


					//削除の場合
					} else if ($CHANGED == 3 || $CHANGED == 4) {

							if (!empty($BNRI_DAI_CD)
							and !empty($BNRI_CYU_CD)) {

								//中分類マスタを削除する
								$sql = "CALL P_WEX040_DEL_CYU_BNRI(";
								$sql .= "'" . $this->_ninusi_cd . "',";
								$sql .= "'" . $this->_sosiki_cd . "',";
								$sql .= "'" . $BNRI_DAI_CD . "',";
								$sql .= "'" . $BNRI_CYU_CD . "',";
								$sql .= "@return_cd";
								$sql .= ")";

								$this->execWithPDOLog($pdo2,$sql, '中分類マスタ　削除');

								$sql = "SELECT";
								$sql .= " @return_cd";

								$this->queryWithPDOLog($stmt,$pdo2,$sql, '中分類マスタ　削除');

								$result = $stmt->fetch(PDO::FETCH_ASSOC);
								$return_cd = $result["@return_cd"];

								if ($return_cd == "1") {

									throw new Exception('中分類マスタ　削除');
								}

							}

					}

				}

				$pdo2->commit();

			} catch (Exception $e) {

				$this->printLog("fatal", "例外発生", "WEX040", $e->getMessage());
				$pdo2->rollBack();
			}

			$pdo2 = null;

			//排他制御オフ
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WEX',";
			$sql .= "'040',";
			$sql .= "0";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'WEX040 排他制御オフ');

			$pdo = null;

			if ($return_cd == "1") {

				//例外の場合、例外メッセージ
				$message = $this->MMessage->getOneMessage('CMNE000107');
				$this->errors['Check'][] =  $message;
				return false;
			}

			//更新後は、以下を必ず入れること
			App::uses('MBunrui', 'Model');

			$this->MBunrui = new MBunrui($this->_ninusi_cd,
			                                     $this->_sosiki_cd,
			                                     $this->name
			                                     );

			// 削除
			$this->MBunrui->deleteMemcache();
			//　ここまで

			return true;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WEX040", $e->getMessage());
			$pdo = null;

		}

		//例外発生の場合、エラー
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;

	}

	/**
	 * 大分類マスタ取得（チェック用）
	 */
	public function getMBnriDaiForCheck($bnri_dai_cd) {

		/* ▼取得カラム設定 */
		$fields = '*';

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
		$condition .= " AND BNRI_DAI_CD = :bnri_dai_cd " ;
		$conditionVal['bnri_dai_cd'] = $bnri_dai_cd;



		/* ▼拠点情報取得 */
		$sql  = 'SELECT ';
		$sql .= "$fields ";
		$sql .= 'FROM M_BNRI_DAI ';
		$sql .= 'WHERE ';
		$sql .= $condition;
		$sql .= $conditionKey;

		$data = $this->query($sql, $conditionVal, false);
		$data = self::editData($data);

		return $data;

	}


	/**
	 * 中分類マスタ取得（チェック用）
	 */
	public function getMBnriCyuForCheck($bnri_dai_cd, $bnri_cyu_cd) {

		/* ▼取得カラム設定 */
		$fields = '*';

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
		$condition .= " AND BNRI_DAI_CD = :bnri_dai_cd " ;
		$conditionVal['bnri_dai_cd'] = $bnri_dai_cd;

		//中分類コード
		$condition .= " AND BNRI_CYU_CD = :bnri_cyu_cd " ;
		$conditionVal['bnri_cyu_cd'] = $bnri_cyu_cd;



		/* ▼拠点情報取得 */
		$sql  = 'SELECT ';
		$sql .= "$fields ";
		$sql .= 'FROM V_WEX040_M_BNRI_CYU_CHK ';
		$sql .= 'WHERE ';
		$sql .= $condition;
		$sql .= $conditionKey;

		$data = $this->query($sql, $conditionVal, false);
		$data = self::editData($data);

		return $data;

	}


	public function checkTimestamp($currentTimestamp) {

		$pdo = null;

		try{

			//排他制御
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "CALL P_WEX040_GET_TIMESTAMP(";
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
//				$this->errors['Check'][] =$currentTimestamp . ' ' . $timestamp;
				return true;
			} else {

				$message =$this->MMessage->getOneMessage('CMNE000106');
				$this->errors['Check'][] = $message;
				return false;
			}


		} catch (Exception $e){

			$this->printLog("fatal", "例外発生", "WEX040", $e->getMessage());
			$pdo = null;
		    throw $e;
		}

		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}


}

?>
