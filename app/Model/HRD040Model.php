<?php
/**
 * HRD040Model
 *
 * スタッフ許可マスタ設定
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');

class HRD040Model extends DCMSAppModel{

	public $name = 'HRD040Model';
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
	 * スタッフ許可マスタ取得
	 * @access   public
	 * @return   スタッフ許可マスタ
	 */
	public function getMStaffKyokaLst($staff_cd, $subsystem_id, $kino_id) {
		/* ▼取得カラム設定 */
		$fields = 'KN.STAFF_CD,KN_STAFF_NM,KY.SUB_SYSTEM_ID,KY.KINO_ID';

		/* ▼検索条件設定 */

		$conditionVal = array();
		$conditionKey = '';

		/* ▼検索条件設定 */
		$condition  = "NINUSI_CD       = :ninusi_cd";
		$condition .= " AND SOSIKI_CD  = :sosiki_cd";

		// 条件値指定
		$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
		$conditionVal['sosiki_cd'] = $this->_sosiki_cd;


		//スタッフコード
		if ($staff_cd != null){
			$condition .= " AND KN.STAFF_CD LIKE :staff_cd " ;
			$conditionVal['staff_cd'] = $staff_cd . "%";
		}
		//サブシステムID
		if ($subsystem_id != null){
			$condition .= " AND KY.SUB_SYSTEM_ID LIKE :subsystem_id " ;
			$conditionVal['subsystem_id'] = "%" . $subsystem_id . "%";
		}
		//機能ID
		if ($kino_id != null){
			$condition .= " AND KY.KINO_ID LIKE :kino_id " ;
			$conditionVal['kino_id'] = "%" . $kino_id . "%";
		}

		/* ▼拠点情報取得 */
		$sql  = 'SELECT ';
		$sql .= "$fields ";
		$sql .= 'FROM M_STAFF_KYOKA KY INNER JOIN M_STAFF_KIHON KN ON (KY.STAFF_CD=KN.STAFF_CD) ';
		$sql .= 'WHERE ';
		$sql .= $condition;
		$sql .= $conditionKey;
		$sql .= " ORDER BY";
		$sql .= "   KN.STAFF_CD";

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


	public function checkStaffKyokaMst($data) {

		//　入力チェック

		$message = "";
		$count = 0;			//行
		$count4 = 0;		//更新対象件数取得
		foreach($data as $obj) {

			$count++;

			$cellsArray = $obj->cells;
			$dataArray = $obj->data;

			$STAFF_CD = $cellsArray[0];
			$STAFF_NM = $cellsArray[1];
			$SUB_SYSTEM_ID = $cellsArray[2];
			$KINO_ID = $cellsArray[3];
			$LINE_COUNT = $dataArray[0];
			$CHANGED = $dataArray[1];

			//更新対象件数取得
			if ($CHANGED != '0'
			and $CHANGED != '5') {
				$count4++;
			}

			//追加と修正のみ
			if ($CHANGED == '1'
			or  $CHANGED == '2') {

				//必須チェック
				if (empty($STAFF_CD)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, 'スタッフコード'));
					$this->errors['Check'][] =   $message;
					continue;
				}
				if (empty($SUB_SYSTEM_ID)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, 'サブシステムID'));
					$this->errors['Check'][] =   $message;
					continue;
				}
				if (empty($KINO_ID)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '機能ID'));
					$this->errors['Check'][] =   $message;
					continue;
				}

			}
		}

		if ($count4 == 0) {
			$message = "更新データがありません。";
			$this->errors['Check'][] = $message;
			return false;
		}
		if (count($this->errors) != 0) {
			return false;
		}

		$count = 0;
		$count2 = 0;		//キー重複チェック用
		$count3 = 0;		//キー重複チェック用

		foreach($data as $obj) {

			$count++;

			$cellsArray = $obj->cells;
			$dataArray = $obj->data;

			$CHANGED = $dataArray[1];
			$BNRI_DAI_CD = $cellsArray[0];

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
					if ($cellsArray2[0] == $BNRI_DAI_CD
							and $dataArray2[1]  == '2') {
						$count3++;
					}
				}

				if ($count3 > 1) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010007'), array($count));
					$this->errors['Check'][] =   $message;
					continue;
				}
			}
		}

		if (count($this->errors) != 0) {
			return false;
		}

		$count = 0;

		foreach($data as $obj) {

			$count++;

			$cellsArray = $obj->cells;
			$dataArray = $obj->data;

			$CHANGED = $dataArray[1];
			$BNRI_DAI_CD = $cellsArray[0];

			// =================================================================
			// キー重複チェック
			// =================================================================

			//追加の場合
			if ($CHANGED == '2') {

				//データベースにチェック
				$dataForCheck = $this->getMStaffKyokaForCheck($STAFF_CD,$SUB_SYSTEM_ID,$KINO_ID);
				if (!empty($dataForCheck)) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE030005'), array($count, 'スタッフ許可マスタ'));
					$this->errors['Check'][] =   $message;
					continue;
				}
			}

			//更新の場合
			if ($CHANGED == 1) {

				//データベースにチェック
				$dataForCheck = $this->getMStaffKyokaForCheck($STAFF_CD,$SUB_SYSTEM_ID,$KINO_ID);
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



	public function setStaffKyokaMst($arrayList, $staff_cd) {

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
			$sql .= "'030',";
			$sql .= "1";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'HRD040 排他制御オン');

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

					$STAFF_CD     = $cellsArray[0];
					$STAFF_NM     = Sanitize::clean($cellsArray[1]);
					$SUB_SYSTEM_ID  = Sanitize::clean($cellsArray[2]);
					$KINO_ID    = Sanitize::clean($cellsArray[3]);
					$LINE_COUNT      = $dataArray[0];
					$CHANGED         = $dataArray[1];

					//　０：なし　　　　　　何もしない
					//　１：　修正　　　　　UPDATE
					//　２：　追加　　　　　INSERT
					//　３：　削除　　　　　DELETE
					//　４：　修正→削除　　DELETE
					//　５：　追加→削除　　何もしない


					//更新の場合
					if ($CHANGED == 1) {

						//スタッフ許可マスタを更新する
						$sql = "UPDATE M_STAFF_KYOKA SET";
						$sql .= " SUB_SYSTEM_ID = '" . $SUB_SYSTEM_ID . "',";
						$sql .= ",KINO_ID = '" . $KINO_ID . "',";
						$sql .= " WHERE NINUSI_CD = '" . $this->_ninusi_cd . "',";
						$sql .= "  AND  SOSIKI_CD = '" . $this->_sosiki_cd . "',";
						$sql .= "'" . $staff_cd . "',";
						$sql .= "'" . $BNRI_DAI_CD . "',";
						$sql .= "'" . $BNRI_DAI_NM . "',";
						$sql .= "'" . $BNRI_DAI_RYAKU . "',";
						$sql .= "'" . $BNRI_DAI_EXP . "',";
						$sql .= "'" . $KBN_TANI_CD . "',";
						$sql .= "@return_cd";
						$sql .= ")";

						$this->execWithPDOLog($pdo2,$sql, '大分類マスタ　更新');

					//新規の場合
					} else if ($CHANGED == 2) {

						//大分類マスタを追加する
						$sql = "CALL P_HRD040_INS_DAI_BNRI(";
						$sql .= "'" . $this->_ninusi_cd . "',";
						$sql .= "'" . $this->_sosiki_cd . "',";
						$sql .= "'" . $staff_cd . "',";
						$sql .= "'" . $BNRI_DAI_CD . "',";
						$sql .= "'" . $BNRI_DAI_NM . "',";
						$sql .= "'" . $BNRI_DAI_RYAKU . "',";
						$sql .= "'" . $BNRI_DAI_EXP . "',";
						$sql .= "'" . $KBN_TANI_CD . "',";
						$sql .= "@return_cd";
						$sql .= ")";

						$this->execWithPDOLog($pdo2,$sql, '大分類マスタ　追加');

						$sql = "SELECT";
						$sql .= " @return_cd";

						$this->queryWithPDOLog($stmt,$pdo2,$sql, '大分類マスタ　追加');

						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$return_cd = $result["@return_cd"];

						if ($return_cd == "1") {

							throw new Exception('大分類マスタ　追加');
						}

					//削除の場合
					} else if ($CHANGED == 3 || $CHANGED == 4) {

							if (!empty($BNRI_DAI_CD)) {

								//大分類マスタを削除する
								$sql = "CALL P_HRD040_DEL_DAI_BNRI(";
								$sql .= "'" . $this->_ninusi_cd . "',";
								$sql .= "'" . $this->_sosiki_cd . "',";
								$sql .= "'" . $BNRI_DAI_CD . "',";
								$sql .= "@return_cd";
								$sql .= ")";

								$this->execWithPDOLog($pdo2,$sql, '大分類マスタ　削除');

								$sql = "SELECT";
								$sql .= " @return_cd";

								$this->queryWithPDOLog($stmt,$pdo2,$sql, '大分類マスタ　削除');

								$result = $stmt->fetch(PDO::FETCH_ASSOC);
								$return_cd = $result["@return_cd"];

								if ($return_cd == "1") {

									throw new Exception('大分類マスタ　削除');
								}
							}

					}

				}

				$pdo2->commit();

			} catch (Exception $e) {

				$this->printLog("fatal", "例外発生", "HRD040", $e->getMessage());
				$pdo2->rollBack();
			}

			$pdo2 = null;

			//排他制御オフ
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WEX',";
			$sql .= "'030',";
			$sql .= "0";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'HRD040 排他制御オフ');

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

			$this->printLog("fatal", "例外発生", "HRD040", $e->getMessage());
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
		$sql .= 'FROM V_HRD040_M_BNRI_DAI_CHK ';
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

			$sql = "CALL P_HRD040_GET_TIMESTAMP(";
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

			$this->printLog("fatal", "例外発生", "HRD040", $e->getMessage());
			$pdo = null;
		    throw $e;
		}

		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}


}

?>
