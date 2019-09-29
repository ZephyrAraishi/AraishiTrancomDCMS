<?php
/**
 * WEX012Model
 *
 * 組織マスタ設定
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');

class WEX012Model extends DCMSAppModel{

	public $name = 'WEX012Model';
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
	 * 組織マスタ取得
	 * @access   public
	 * @return   組織マスタ
	 */
	public function getMSosikiLst($ninusi_cd, $sosiki_cd,  $sosiki_nm) {
		/* ▼取得カラム設定 */
		$fields = 'A.NINUSI_CD,B.NINUSI_NM,A.SOSIKI_CD,A.SOSIKI_NM,A.SOSIKI_RYAKU';

		/* ▼検索条件設定 */

		$conditionVal = array();
		$conditionKey = '';

		/* ▼検索条件設定 */
		$condition  = "";

		//荷主コード
		if ($ninusi_cd != null){
			$condition .= " AND A.NINUSI_CD = :ninusi_cd " ;
			$conditionVal['ninusi_cd'] = $ninusi_cd;
		}
		//組織コード
		if ($sosiki_cd != null){
			$condition .= " AND A.SOSIKI_CD = :sosiki_cd " ;
			$conditionVal['sosiki_cd'] = $sosiki_cd;
		}
		//組織名称
		if ($sosiki_nm != null){
			$condition .= " AND A.SOSIKI_NM LIKE :sosiki_nm " ;
			$conditionVal['sosiki_nm'] = $sosiki_nm . "%";
		}
		//組織略称
		/* ▼組織情報取得 */
		$sql  = 'SELECT ';
		$sql .= "$fields ";
		$sql .= 'FROM M_NINUSI B LEFT JOIN M_SOSIKI A ON A.NINUSI_CD=B.NINUSI_CD ';
		$sql .= 'WHERE 0=0';
		$sql .= $condition;
		$sql .= $conditionKey;
		$sql .= " ORDER BY";
		$sql .= "   A.NINUSI_CD,A.SOSIKI_CD";

		$data = $this->query($sql, $conditionVal, false);
		$data = self::editData($data);

		return $data;
	}

	/**
	 * 組織情報編集処理
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


	public function checkSosikiMst($data) {

		//　入力チェック

		$message = "";
		$count = 0;			//行
		$count4 = 0;		//更新対象件数取得
		foreach($data as $obj) {

			$count++;

			$cellsArray = $obj->cells;
			$dataArray = $obj->data;

			$NINUSI_CD   = $cellsArray[0];
			$NINUSI_NM      = $cellsArray[1];
			$SOSIKI_CD   = $cellsArray[2];
			$SOSIKI_NM      = $cellsArray[3];
			$SOSIKI_RYAKU   = $cellsArray[4];

			$LINE_COUNT       = $dataArray[0];
			$CHANGED          = $dataArray[1];

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
				}
				if (empty($SOSIKI_CD)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '組織コード'));
					$this->errors['Check'][] =   $message;
				}
				if (empty($SOSIKI_NM)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '組織名称'));
					$this->errors['Check'][] =   $message;
				}
				if (empty($SOSIKI_RYAKU)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '組織略称'));
					$this->errors['Check'][] =   $message;
				}

				//桁数チェック
				if (strlen($NINUSI_CD) != 10) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE050002'), array($count, '荷主コード', '10'));
					$this->errors['Check'][] =   $message;
				}
				if (strlen($SOSIKI_CD) != 10) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE050002'), array($count, '組織コード', '10'));
					$this->errors['Check'][] =   $message;
				}
				if (mb_strlen($SOSIKI_NM) > 20) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE050004'), array($count, '組織名称', '20'));
					$this->errors['Check'][] =   $message;
				}
				if (mb_strlen($SOSIKI_RYAKU) > 6) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE050004'), array($count, '組織略称', '6'));
					$this->errors['Check'][] =   $message;
				}

				//数字チェック
				if (!preg_match("/^[0-9]+$/",$SOSIKI_CD)) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE050003'), array($count, '組織コード'));
					$this->errors['Check'][] =   $message;
				}

				//"000"は入力不可
				if ($SOSIKI_CD == '0000000000' ) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE050006'), array($count, '組織コード', '001'));
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

			$NINUSI_CD   = $cellsArray[0];
			$NINUSI_NM      = $cellsArray[1];
			$SOSIKI_CD   = $cellsArray[2];
			$SOSIKI_NM      = $cellsArray[3];
			$SOSIKI_RYAKU   = $cellsArray[4];
			$CHANGED       = $dataArray[1];

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
					if ($cellsArray2[0] == $NINUSI_CD
							and $cellsArray2[2] == $SOSIKI_CD
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

			$NINUSI_CD   = $cellsArray[0];
			$NINUSI_NM      = $cellsArray[1];
			$SOSIKI_CD   = $cellsArray[2];
			$SOSIKI_NM      = $cellsArray[3];
			$SOSIKI_RYAKU   = $cellsArray[4];
			$CHANGED       = $dataArray[1];

			// =================================================================
			// キー重複チェック
			// =================================================================

			//追加のみ
			if ($CHANGED == '2') {

				//親が存在するかチェック
				$dataForCheck = $this->getMNinusiForCheck($NINUSI_CD);
				if (empty($dataForCheck)) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE050009'), array($count));
					$this->errors['Check'][] =   $message;
					continue;
				}

				//データベースにチェック
				$dataForCheck = $this->getMSosikiForCheck($NINUSI_CD, $SOSIKI_CD);
				if (!empty($dataForCheck)) {
					$message = vsprintf($this->MMessage->getOneMessage('WEXE050005'), array($count, '細分類コード'));
					$this->errors['Check'][] =   $message;
					continue;
				}
			}

			//更新の場合
			if ($CHANGED == 1) {

				//データベースにチェック
				$dataForCheck = $this->getMSosikiForCheck($NINUSI_CD, $SOSIKI_CD);
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



	public function setSosikiMst($arrayList, $staff_cd) {

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
			$sql .= "'012',";
			$sql .= "1";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'WEX012 排他制御オン');

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

					$NINUSI_CD      = $cellsArray[0];
					$SOSIKI_CD      = $cellsArray[2];
					$SOSIKI_NM      = Sanitize::clean($cellsArray[3]);
					$SOSIKI_RYAKU   = Sanitize::clean($cellsArray[4]);

					$LINE_COUNT       = $dataArray[0];
					$CHANGED          = $dataArray[1];

					//　０：なし　　　　　　何もしない
					//　１：　修正　　　　　UPDATE
					//　２：　追加　　　　　INSERT
					//　３：　削除　　　　　DELETE
					//　４：　修正→削除　　DELETE
					//　５：　追加→削除　　何もしない


					//更新の場合
					if ($CHANGED == 1) {

						//組織マスタを更新する
						$sql = "CALL UPDATE M_SOSIKI SET";
						$sql .= "SOSIKI_NM='" . $SOSIKI_NM . "',";
						$sql .= ",SOSIKI_RYAKU='" . $SOSIKI_RYAKU . "'";
						$sql .= ",MODIFIED=now()";
						$sql .= ",MODIFIED_STAFF='" . $staff_cd . "'";
						$sql .= " WHERE NINUSI_CD='" . $NINUSI_CD . "'";
						$sql .= "   AND SOSIKI_CD='" . $SOSIKI_CD . "';";

						$this->execWithPDOLog($pdo2,$sql, '組織マスタ　更新');

					//新規の場合
					} else if ($CHANGED == 2) {

						//組織マスタを追加する
						$sql = "CALL INSERT INTO M_SOSIKI(NINUSI_CD,SOSIKI_CD,SOSIKI_NM,SOSIKI_RYAKU,CREATED,CREATED_STAFF,MODIFIED,MODIFIED_STAFF) VALUES(";
						$sql .= "'" . $NINUSI_CD . "',";
						$sql .= "'" . $SOSIKI_CD . "',";
						$sql .= "'" . $SOSIKI_NM . "',";
						$sql .= "'" . $SOSIKI_RYAKU . "',";
						$sql .= "now(),";
						$sql .= "'" . $staff_cd . "',";
						$sql .= "now(),";
						$sql .= "'" . $staff_cd . "'";
						$sql .= ")";

						$this->execWithPDOLog($pdo2,$sql, '組織マスタ　追加');

					//削除の場合
					} else if ($CHANGED == 3 || $CHANGED == 4) {

							if (!empty($NINUSI_CD)
							and !empty($SOSIKI_CD)) {

								//組織マスタを削除する
								$sql = "DELETE FROM M_SOSIKI ";
								$sql .= " WHERE NINUSI_CD='" . $NINUNSI_CD . "',";
								$sql .= "   AND SOSIKI_CD='" . $SOSIKI_CD . "',";

								$this->execWithPDOLog($pdo2,$sql, '組織マスタ　削除');

							}

					}

				}

				$pdo2->commit();

			} catch (Exception $e) {
				$return_cd = "1";
				$this->printLog("fatal", "例外発生", "WEX012", $e->getMessage());
				$pdo2->rollBack();
			}

			$pdo2 = null;

			//排他制御オフ
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WEX',";
			$sql .= "'012',";
			$sql .= "0";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'WEX012 排他制御オフ');

			$pdo = null;

			if ($return_cd == "1") {

				//例外の場合、例外メッセージ
				$message = $this->MMessage->getOneMessage('CMNE000107');
				$this->errors['Check'][] =  $message;
				return false;
			}

			return true;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WEX012", $e->getMessage());
			$pdo = null;

		}

		//例外発生の場合、エラー
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;

	}

	/**
	 * 荷主マスタ取得（チェック用）
	 */
	public function getMNinusiForCheck($ninusi_cd) {

		/* ▼取得カラム設定 */
		$fields = 'NINUSI_CD,NINUSI_NM';

		/* ▼検索条件設定 */

		$conditionVal = array();
		$conditionKey = '';

		/* ▼検索条件設定 */
		$condition  = "";
		$conditionVal['ninusi_cd'] = "";

		//荷主コード
		$condition .= " AND NINUSI_CD = :ninusi_cd " ;
		$conditionVal['ninusi_cd'] = $ninusi_cd;

		/* ▼荷主情報取得 */
		$sql  = 'SELECT ';
		$sql .= "$fields ";
		$sql .= 'FROM M_NINUSI ';
		$sql .= 'WHERE ';
		$sql .= $condition;
		$sql .= $conditionKey;

		$data = $this->queryWithLog($sql, $conditionVal, false);
		$data = self::editData($data);

		return $data;

	}

	/**
	 * 組織マスタ取得（チェック用）
	 */
	public function getMSosikiForCheck($ninusi_cd, $sosiki_cd) {

		/* ▼取得カラム設定 */
		$fields = '*';

		/* ▼検索条件設定 */

		$conditionVal = array();
		$conditionKey = '';

		/* ▼検索条件設定 */
		$condition  = "";
		$conditionVal['ninusi_cd'] = "";
		$conditionVal['sosiki_cd'] = "";

		//荷主コード
		$condition .= " AND NINUSI_CD = :ninusi_cd " ;
		$conditionVal['ninusi_cd'] = $ninusi_cd;

		//組織コード
		$condition .= " AND SOSIKI_CD = :sosiki_cd " ;
		$conditionVal['sosiki_cd'] = $sosiki_cd;

		/* ▼拠点情報取得 */
		$sql  = 'SELECT ';
		$sql .= "$fields ";
		$sql .= 'FROM M_SOSIKI ';
		$sql .= 'WHERE ';
		$sql .= $condition;
		$sql .= $conditionKey;

		$data = $this->queryWithLog($sql, $conditionVal, false);
		$data = self::editData($data);

		return $data;

	}


	public function checkTimestamp($currentTimestamp) {

		$pdo = null;

		try{

			//排他制御
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "CALL P_WEX012_GET_TIMESTAMP(";
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

			$this->printLog("fatal", "例外発生", "WEX012", $e->getMessage());
			$pdo = null;
		    throw $e;
		}

		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}

	/**
	 * 荷主マスタ取得（コンボボックス用）
	 */
	public function getMNinusi() {

		/* ▼取得カラム設定 */
		$fields = 'NINUSI_CD,NINUSI_NM';

		/* ▼荷主情報取得 */
		$sql  = 'SELECT ';
		$sql .= "$fields ";
		$sql .= 'FROM M_NINUSI ';

		$data = $this->queryWithLog($sql, "", false);
		$data = self::editData($data);

		return $data;

	}


}

?>
