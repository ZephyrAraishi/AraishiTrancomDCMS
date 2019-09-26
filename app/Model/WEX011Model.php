<?php
/**
 * WEX011Model
 *
 * 荷主マスタ設定
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');

class WEX011Model extends DCMSAppModel{

	public $name = 'WEX011Model';
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
	 * 荷主マスタ取得
	 * @access   public
	 * @return   荷主マスタ
	 */
	public function getMNinusiLst($ninusi_cd, $ninusi_nm, $ninusi_ryaku) {
		/* ▼取得カラム設定 */
		$fields = 'NINUSI_CD,NINUSI_NM,NINUSI_RYAKU';

		/* ▼検索条件設定 */

		$conditionVal = array();
		$conditionKey = '';
		$condition = '';

		//荷主コード
		if ($ninusi_cd != null){
			$condition .= " AND NINUSI_CD LIKE :ninusi_cd " ;
			$conditionVal['ninusi_cd'] = $ninusi_cd . "%";
		}
		//荷主名称
		if ($ninusi_nm != null){
			$condition .= " AND NINUSI_NM LIKE :ninusi_nm " ;
			$conditionVal['ninusi_nm'] = "%" . $ninusi_nm . "%";
		}
		//荷主名称略称
		if ($ninusi_ryaku != null){
			$condition .= " AND NINUSI_RYAKU LIKE :ninusi_ryaku " ;
			$conditionVal['ninusi_ryaku'] = "%" . $ninusi_ryaku . "%";
		}

		/* ▼荷主マスタ情報取得 */
		$sql  = 'SELECT ';
		$sql .= "$fields ";
		$sql .= 'FROM M_NINUSI ';
		$sql .= 'WHERE 0=0';
		$sql .= $condition;
		$sql .= $conditionKey;
		$sql .= " ORDER BY";
		$sql .= "   NINUSI_CD";

		$data = $this->query($sql, $conditionVal, false);
		$data = self::editData($data);

		return $data;
	}

	/**
	 * 荷主マスタ情報編集処理
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


	public function checkNinusiMst($data) {

		//　入力チェック

		$message = "";
		$count = 0;			//行
		$count4 = 0;		//更新対象件数取得
		foreach($data as $obj) {

			$count++;

			$cellsArray = $obj->cells;
			$dataArray = $obj->data;

			$NINUSI_CD = $cellsArray[0];
			$NINUSI_NM = $cellsArray[1];
			$NINUSI_RYAKU = $cellsArray[2];
			$NINUSI_CD_CNT = $cellsArray[3];
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
				if (empty($NINUSI_CD)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '荷主コード'));
					$this->errors['Check'][] =   $message;
					continue;
				}
				if (empty($NINUSI_NM)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '荷主名称'));
					$this->errors['Check'][] =   $message;
					continue;
				}
				if (empty($NINUSI_RYAKU)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '荷主名称略称'));
					$this->errors['Check'][] =   $message;
					continue;
				}

				//桁数チェック
				if (strlen($NINUSI_CD) > 10) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE030004'), array($count, '荷主コード', '10'));
					$this->errors['Check'][] =   $message;
					continue;
				}
				if (mb_strlen($NINUSI_NM) > 20) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE030004'), array($count, '荷主名称', '20'));
					$this->errors['Check'][] =   $message;
					continue;
				}
				if (mb_strlen($NINUSI_RYAKU) > 6) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE030004'), array($count, '荷主名称略称', '6'));
					$this->errors['Check'][] =   $message;
					continue;
				}

				//数字チェック
				if (!preg_match("/^[0-9]+$/",$NINUSI_CD)) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE030003'), array($count, '荷主コード'));
					$this->errors['Check'][] =   $message;
					continue;
				}

				//"000"は入力不可
				if ($NINUSI_CD == '0000000000' ) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE030006'), array($count, '荷主コード', '001'));
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
			$NINUSI_CD = $cellsArray[0];

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
				$dataForCheck = $this->getMNinusiForCheck($NINUSI_CD);
				if (!empty($dataForCheck)) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE030005'), array($count, '荷主コード'));
					$this->errors['Check'][] =   $message;
					continue;
				}
			}

			//更新の場合
			if ($CHANGED == 1) {

				//データベースにチェック
				$dataForCheck = $this->getMNinusiForCheck($NINUSI_CD);
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



	public function setNinusiMst($arrayList, $staff_cd) {

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
			$sql .= "'011',";
			$sql .= "1";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'WEX011 排他制御オン');

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

					$NINUSI_CD     = $cellsArray[0];
					$NINUSI_NM     = Sanitize::clean($cellsArray[1]);
					$NINUSI_RYAKU  = Sanitize::clean($cellsArray[2]);
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

						//荷主マスタを更新する
						$sql = "UPDATE M_NINUSI SET";
						$sql .= " NINUSI_NM='" . $NINUSI_NM . "',";
						$sql .= " NINUSI_RYAKU='" . $NINUSI_RYAKU . "',";
						$sql .= " MODIFIED=now(),";
						$sql .= " MODIFIED_STAFF='" . $staff_cd . "'";
						$sql .= " WHERE NINUSI_CD='" . $NINUSI_CD . "'";

						$this->execWithPDOLog($pdo2,$sql, '荷主マスタ　更新');

					//新規の場合
					} else if ($CHANGED == 2) {

						//荷主マスタを追加する
						$sql = "INSERT INTO M_NINUSI(NINUSI_CD,NINUSI_NM,NINUSI_RYAKU,CREATED,CREATED_STAFF,MODIFIED,MODIFIED_STAFF) VALUES(";
						$sql .= "'" . $NINUSI_CD . "',";
						$sql .= "'" . $NINUSI_NM . "',";
						$sql .= "'" . $NINUSI_RYAKU . "',";
						$sql .= "now(),";
						$sql .= "'" . $staff_cd . "',";
						$sql .= "now(),";
						$sql .= "'" . $staff_cd . "'";
						$sql .= ")";

						$this->execWithPDOLog($pdo2,$sql, '荷主マスタ　追加');

					//削除の場合
					} else if ($CHANGED == 3 || $CHANGED == 4) {

							if (!empty($NINUSI_CD)) {

								//荷主マスタを削除する
								$sql = "DELETE FROM M_NINUSI";
								$sql .= " WHERE NINUSI_CD='" . $NINUSI_CD . "'";

								$this->execWithPDOLog($pdo2,$sql, '荷主マスタ　削除');

							}

					}

				}

				$pdo2->commit();

			} catch (Exception $e) {

				$this->printLog("fatal", "例外発生", "WEX011", $e->getMessage());
				$pdo2->rollBack();
			}

			$pdo2 = null;

			//排他制御オフ
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WEX',";
			$sql .= "'011',";
			$sql .= "0";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'WEX011 排他制御オフ');

			$pdo = null;

			return true;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WEX011", $e->getMessage());
			$pdo = null;

		}

	}


	/**
	 * 荷主マスタ取得（チェック用）
	 */
	public function getMNinusiForCheck($ninusi_cd) {

		/* ▼取得カラム設定 */
		$fields = '*';

		/* ▼検索条件設定 */

		$conditionVal = array();
		$conditionKey = '';

		//荷主コード
		$condition .= " AND NINUSI_CD = :ninusi_cd " ;
		$conditionVal['ninusi_cd'] = $ninusi_cd;

		/* ▼荷主マスタ情報取得 */
		$sql  = 'SELECT ';
		$sql .= "$fields ";
		$sql .= 'FROM M_NINUSI ';
		$sql .= 'WHERE 0=0';
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

			$sql = "CALL P_WEX011_GET_TIMESTAMP(";
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

			$this->printLog("fatal", "例外発生", "WEX030", $e->getMessage());
			$pdo = null;
		    throw $e;
		}

		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}


}

?>
