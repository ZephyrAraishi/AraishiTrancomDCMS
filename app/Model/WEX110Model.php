<?php
/**
 * WEX110Model
 *
 * 名称マスタ設定
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');

class WEX110Model extends DCMSAppModel{

	public $name = 'WEX110Model';
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
	 * 大分類マスタ取得
	 * @access   public
	 * @return   大分類マスタ
	 */
	public function getMeisyo($meisyo,$kbn) {
		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			//工程データの取得
			$sql  = "";
			$sql .= "SELECT";
			$sql .= "    MEI_KBN";
			$sql .= "   ,MEI_CD";
			$sql .= "   ,MEI_1";
			$sql .= "   ,MEI_2";
			$sql .= "   ,MEI_3";
			$sql .= "   ,VAL_FREE_STR_1";
			$sql .= "   ,VAL_FREE_STR_2";
			$sql .= "   ,VAL_FREE_STR_3";
			$sql .= "   ,VAL_FREE_NUM_1";
			$sql .= "   ,VAL_FREE_NUM_2";
			$sql .= "   ,VAL_FREE_NUM_3";
			$sql .= " FROM";
			$sql .= "   M_MEISYO";
			$sql .= " WHERE ";
			$sql .= "      NINUSI_CD='" . $this->_ninusi_cd . "'";
			$sql .= "  AND SOSIKI_CD='" . $this->_sosiki_cd . "'";
			
			if ($meisyo != ''){
				$sql .= " AND (" ;
				$sql .= "           MEI_1 LIKE '%" . $meisyo . "%'" ;
				$sql .= "       OR  MEI_2 LIKE '%" . $meisyo . "%'" ;
				$sql .= "       OR  MEI_3 LIKE '%" . $meisyo . "%'" ;
				$sql .= "     )" ;
			}
			
			if ($kbn != ''){
				$sql .= " AND MEI_KBN='" . $kbn . "'" ;
			}

			$sql .= " ORDER BY";
			$sql .= "    MEI_KBN";
			$sql .= "   ,MEI_CD";
			$sql .= ";";


			$this->queryWithPDOLog($stmt,$pdo,$sql,"工程データ 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){


				$array = array(
						"MEI_KBN" => $result['MEI_KBN'],
						"MEI_CD" => $result['MEI_CD'],
						"MEI_1" => $result['MEI_1'],
						"MEI_2" => $result['MEI_2'],
						"MEI_3" => $result['MEI_3'],
						"VAL_FREE_STR_1" => $result['VAL_FREE_STR_1'],
						"VAL_FREE_STR_2" => $result['VAL_FREE_STR_2'],
						"VAL_FREE_STR_3" => $result['VAL_FREE_STR_3'],
						"VAL_FREE_NUM_1" => $result['VAL_FREE_NUM_1'],
						"VAL_FREE_NUM_2" => $result['VAL_FREE_NUM_2'],
						"VAL_FREE_NUM_3" => $result['VAL_FREE_NUM_3'],
				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WEX100", $e->getMessage());
			$pdo = null;
		}


		return array();
	}


	public function checkData($data) {

		$message = "";
		$count_row = 0;	
		$count_process = 0;
		
		foreach($data as $obj) {

			$count_row++;

			$cellsArray = $obj->cells;
			$dataArray = $obj->data;

			$MEI_KBN = $cellsArray[1];
			$MEI_CD = $cellsArray[2];
			$CHANGED = $dataArray[1];

			//更新対象件数取得
			if ($CHANGED != '0'
			and $CHANGED != '5') {
				$count_process++;
			}

			//追加のみ
			if ($CHANGED == '2') {

				$pdo = null;
		
				try{
		
					//DBオブジェクト取得
					$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
					$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		
					//工程データの取得
					$sql  = "";
					$sql .= "SELECT";
					$sql .= "    *";
					$sql .= " FROM";
					$sql .= "   M_MEISYO";
					$sql .= " WHERE ";
					$sql .= "      NINUSI_CD='" . $this->_ninusi_cd . "'";
					$sql .= "  AND SOSIKI_CD='" . $this->_sosiki_cd . "'";
					$sql .= "  AND MEI_KBN='" . $MEI_KBN . "'";
					$sql .= "  AND MEI_CD='" . $MEI_CD . "'";
		
		
					$this->queryWithPDOLog($stmt,$pdo,$sql,"工程データ 取得");
	
					$hasData = false;
		
					while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
		
						$hasData = true;
						$message = "名称区分: " . $result['MEI_KBN'] . " 名称コード: " . $result['MEI_CD'] . " は重複しています。";
						$this->errors['Check'][] = $message;
					}
		
					$pdo = null;
		
				} catch (Exception $e) {
		
					$this->printLog("fatal", "例外発生", "WEX100", $e->getMessage());
					$pdo = null;
				}
			}
		}

		if ($count_process == 0) {
			$message = "更新データがありません。";
			$this->errors['Check'][] = $message;
			return false;
		}
		
		if (count($this->errors) != 0) {
			return false;
		}

		return true;
	}



	public function setData($arrayList, $staff_cd) {

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
			$sql .= "'110',";
			$sql .= "1";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'WEX110 排他制御オン');

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

					$MEI_KBN = $cellsArray[1];
					$MEI_CD = $cellsArray[2];
					$MEI_1 = Sanitize::clean($cellsArray[3]);
					$MEI_2 = Sanitize::clean($cellsArray[4]);
					$MEI_3 = Sanitize::clean($cellsArray[5]);
					$VAL_FREE_STR_1 = Sanitize::clean($cellsArray[6]);
					$VAL_FREE_STR_2 = Sanitize::clean($cellsArray[7]);
					$VAL_FREE_STR_3 = Sanitize::clean($cellsArray[8]);
					$VAL_FREE_NUM_1 = $cellsArray[9];
					$VAL_FREE_NUM_2 = $cellsArray[10];
					$VAL_FREE_NUM_3 = $cellsArray[11];
					$CHANGED = $dataArray[1];

					//　０：なし　　　　　　何もしない
					//　１：　修正　　　　　UPDATE
					//　２：　追加　　　　　INSERT
					//　３：　削除　　　　　DELETE
					//　４：　修正→削除　　DELETE
					//　５：　追加→削除　　何もしない


					//更新の場合
					if ($CHANGED == 1) {

						$sql = "";
						$sql .= "UPDATE M_MEISYO";
						$sql .= "  SET";
						
						if ($MEI_1 != "") {
							$sql .= "    MEI_1='" . $MEI_1 . "'";
						} else {
							$sql .= "    MEI_1=NULL";
						}
						
						if ($MEI_2 != "") {
							$sql .= "   ,MEI_2='" . $MEI_2 . "'";
						} else {
							$sql .= "   ,MEI_2=NULL";
						}
						
						if ($MEI_3 != "") {
							$sql .= "   ,MEI_3='" . $MEI_3 . "'";
						} else {
							$sql .= "   ,MEI_3=NULL";
						}
						
						if ($VAL_FREE_STR_1 != "") {
							$sql .= "   ,VAL_FREE_STR_1='" . $VAL_FREE_STR_1 . "'";
						} else {
							$sql .= "   ,VAL_FREE_STR_1=NULL";
						}
						
						if ($VAL_FREE_STR_2 != "") {
							$sql .= "   ,VAL_FREE_STR_2='" . $VAL_FREE_STR_2 . "'";
						} else {
							$sql .= "   ,VAL_FREE_STR_2=NULL";
						}
						
						if ($VAL_FREE_STR_3 != "") {
							$sql .= "   ,VAL_FREE_STR_3='" . $VAL_FREE_STR_3 . "'";
						} else {
							$sql .= "   ,VAL_FREE_STR_3=NULL";
						}
						
						if ($VAL_FREE_NUM_1 != "") {
							$sql .= "   ,VAL_FREE_NUM_1='" . $VAL_FREE_NUM_1 . "'";
						} else {
							$sql .= "   ,VAL_FREE_NUM_1=NULL";
						}
						
						if ($VAL_FREE_NUM_2 != "") {
							$sql .= "   ,VAL_FREE_NUM_2='" . $VAL_FREE_NUM_2 . "'";
						} else {
							$sql .= "   ,VAL_FREE_NUM_2=NULL";
						}
						
						if ($VAL_FREE_NUM_3 != "") {
							$sql .= "   ,VAL_FREE_NUM_3='" . $VAL_FREE_NUM_3 . "'";
						} else {
							$sql .= "   ,VAL_FREE_NUM_3=NULL";
						}
						
						$sql .= "   ,MODIFIED=NOW()";
						$sql .= "   ,MODIFIED_STAFF='". $staff_cd . "'";;
						$sql .= " WHERE";
						$sql .= "       NINUSI_CD='" . $this->_ninusi_cd . "'";
						$sql .= "   AND SOSIKI_CD='" . $this->_sosiki_cd . "'";
						$sql .= "   AND MEI_KBN='" . $MEI_KBN . "'";
						$sql .= "   AND MEI_CD='" . $MEI_CD . "'";

						$this->execWithPDOLog($pdo2,$sql, '名称マスタ　更新');



					//新規の場合
					} else if ($CHANGED == 2) {

						$sql = "";
						$sql .= "INSERT INTO M_MEISYO";
						$sql .= " (";
						$sql .= "    NINUSI_CD";
						$sql .= "   ,SOSIKI_CD";
						$sql .= "   ,MEI_KBN";
						$sql .= "   ,MEI_CD";
						$sql .= "   ,MEI_1";
						$sql .= "   ,MEI_2";
						$sql .= "   ,MEI_3";
						$sql .= "   ,VAL_FREE_STR_1";
						$sql .= "   ,VAL_FREE_STR_2";
						$sql .= "   ,VAL_FREE_STR_3";
						$sql .= "   ,VAL_FREE_NUM_1";
						$sql .= "   ,VAL_FREE_NUM_2";
						$sql .= "   ,VAL_FREE_NUM_3";
						$sql .= "   ,CREATED";
						$sql .= "   ,CREATED_STAFF";
						$sql .= "   ,MODIFIED";
						$sql .= "   ,MODIFIED_STAFF";
						$sql .= " ) VALUES (";
						$sql .= "    '" . $this->_ninusi_cd . "'";
						$sql .= "   ,'" . $this->_sosiki_cd . "'";
						$sql .= "   ,'" . $MEI_KBN . "'";
						$sql .= "   ,'" . $MEI_CD . "'";
						
						if ($MEI_1 != "") {
							$sql .= "   ,'" . $MEI_1 . "'";
						} else {
							$sql .= "   ,NULL";
						}
						
						if ($MEI_2 != "") {
							$sql .= "   ,'" . $MEI_2 . "'";
						} else {
							$sql .= "   ,NULL";
						}
						
						if ($MEI_3 != "") {
							$sql .= "   ,'" . $MEI_3 . "'";
						} else {
							$sql .= "   ,NULL";
						}
						
						if ($VAL_FREE_STR_1 != "") {
							$sql .= "   ,'" . $VAL_FREE_STR_1 . "'";
						} else {
							$sql .= "   ,NULL";
						}
						
						if ($VAL_FREE_STR_2 != "") {
							$sql .= "   ,'" . $VAL_FREE_STR_2 . "'";
						} else {
							$sql .= "   ,NULL";
						}
						
						if ($VAL_FREE_STR_3 != "") {
							$sql .= "   ,'" . $VAL_FREE_STR_3 . "'";
						} else {
							$sql .= "   ,NULL";
						}
												
						if ($VAL_FREE_NUM_1 != "") {
							$sql .= "   ," . $VAL_FREE_NUM_1 . "";
						} else {
							$sql .= "   ,NULL";
						}
						
						if ($VAL_FREE_NUM_2 != "") {
							$sql .= "   ," . $VAL_FREE_NUM_2 . "";
						} else {
							$sql .= "   ,NULL";
						}
						
						if ($VAL_FREE_NUM_3 != "") {
							$sql .= "   ," . $VAL_FREE_NUM_3 . "";
						} else {
							$sql .= "   ,NULL";
						}
						
						$sql .= "   ,NOW()";				
						$sql .= "   ,'" . $staff_cd . "'";
						$sql .= "   ,NOW()";					
						$sql .= "   ,'" . $staff_cd . "'";
						$sql .= ")";

						$this->execWithPDOLog($pdo2,$sql, '名称マスタ　追加');


					//削除の場合
					} else if ($CHANGED == 3 || $CHANGED == 4) {

						$sql = "";
						$sql .= "DELETE FROM M_MEISYO";
						$sql .= " WHERE";
						$sql .= "       NINUSI_CD='" . $this->_ninusi_cd . "'";
						$sql .= "   AND SOSIKI_CD='" . $this->_sosiki_cd . "'";
						$sql .= "   AND MEI_KBN='" . $MEI_KBN . "'";
						$sql .= "   AND MEI_CD='" . $MEI_CD . "'";

						$this->execWithPDOLog($pdo2,$sql, '名称マスタ　更新');

					}

				}

				$pdo2->commit();

			} catch (Exception $e) {

				$this->printLog("fatal", "例外発生", "WEX110", $e->getMessage());
				$pdo2->rollBack();
			}

			$pdo2 = null;

			//排他制御オフ
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WEX',";
			$sql .= "'110',";
			$sql .= "0";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'WEX110 排他制御オフ');

			$pdo = null;

			if ($return_cd == "1") {

				//例外の場合、例外メッセージ
				$message = $this->MMessage->getOneMessage('CMNE000107');
				$this->errors['Check'][] =  $message;
				return false;
			}

			return true;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WEX110", $e->getMessage());
			$pdo = null;

		}

		//例外発生の場合、エラー
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;

	}


}

?>
