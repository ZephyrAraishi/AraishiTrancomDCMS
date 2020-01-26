<?php
/**
 * WEX120Model
 *
 * 品質情報データ管理画面
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class WEX120Model extends DCMSAppModel{

	public $name = 'WEX120Model';
	public $useTable = false;

	public $errors = array();

	/**
	 * コンストラクタ
	 * @access   public
	 * @param 機能ID
	 * @param モデルのID.
	 * @param モデルのテーブル名.
	 * @param モデルのデータソース.
	 */
    function __construct($ninusi_cd = '0000000000', $sosiki_cd = '0000000000', $kino_id = '', $id = false, $table = null, $ds = null) {

    	//コンストラクタ
    	parent::__construct($ninusi_cd, $sosiki_cd,$kino_id, $id, $table, $ds);

		//メッセージマスタ呼び出し
		$this->MMessage = new MMessage($ninusi_cd,$sosiki_cd,$kino_id);

		//共通関数モデル
		$this->MCommon = new MCommon($ninusi_cd,$sosiki_cd,$kino_id);

	}

	/**
	 * 品質管理データ取得
	 * @access   public
	 * @return   品質管理データ
	 */
	public function getHinsitsuData($ymd_hassei = '',$kotei = '',$hassei_staff_nm = '',$taiou_staff_nm = '',$kbn_hinsitu_kanri = '',$kbn_hinsitu_naiyo = '') {

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
			$condition .= " AND YMD_HASSEI >= ". $common->encloseString($ymd_hassei);

			//分類コード展開
			$bnri_work = explode('_', $kotei);
			//大分類コード
			if ($bnri_dai_cd != null){
				$condition .= " AND BNRI_DAI_CD = ". $common->encloseString($bnri_work[0]);
			}
			//中分類コード
			if ($bnri_cyu_cd != null){
				$condition .= " AND BNRI_CYU_CD = ". $common->encloseString($bnri_work[1]);
			}
			//細分類コード
			if ($bnri_sai_cd != null){
				$condition .= " AND BNRI_SAI_CD = ". $common->encloseString($bnri_work[2]);
			}

			//発生スタッフ
			if ($hassei_staff_nm != null){
				$condition .= " AND HASSEI_STAFF_CD = ". $common->encloseString($hassei_staff_nm);
			}
			//対応スタッフ
			if ($taiou_staff_nm != null){
				$condition .= " AND TAIOU_STAFF_CD = ". $common->encloseString($taiou_staff_nm);
			}
			//品質管理区分
			if ($kbn_hinsitu_kanri != null){
				$condition .= " AND HINSITSU_KANRO_CD = ". $common->encloseString($kbn_hinsitu_kanri);
			}
			//品質内容区分
			if ($kbn_hinsitu_naiyo != null){
				$condition .= " AND HINSITSU_NAIYO_CD = ". $common->encloseString($kbn_hinsitu_naiyo);
			}
			/* ▼品質管理情報取得 */
			$sql  = 'SELECT ';
			$sql .= "$fields ";
			$sql .= 'FROM V_WEX120_HINSITSU_DATA ';
			$sql .= 'WHERE ';
			$sql .= $condition;

			$this->queryWithPDOLog($stmt, $pdo, $sql,"品質管理情報　取得");

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

			$this->printLog("fatal", "例外発生", "WEX120", $e->getMessage());
			$message = $this->MMessage->getOneMessage('CMNE000107');
			$this->errors['Check'][] =   $message;

			$pdo = null;

		}

		return array();

		}

	/**
	 * 品質管理データ入力チェック
	 * @access   public
	 * @param    品質管理データ
	 * @return   成否情報(true,false)
	 */
	function checkHinsitsuData($data) {

	 	try {


		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WEX120", $e->getMessage());
			$pdo = null;
		}

		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;

	}

	/**
	 * タイムスタンプから最新情報かチェック
	 * @access   public
	 * @param    タイムスタンプ
	 * @return   成否情報(true,false)
	 */
	public function checkTimestamp($currentTimestamp) {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//タイムスタンプ取得
			$sql = "CALL P_WPO040_GET_TIMESTAMP(";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " @timestamp";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, '工程　タイムスタンプ取得');

			$sql = "";
			$sql .= " SELECT";
			$sql .= "  @timestamp";


			$this->queryWithPDOLog($stmt,$pdo,$sql, '工程　タイムスタンプ取得');
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			$timestamp = $result['@timestamp'];

			if ($timestamp == null) {
				$timestamp = "1900-01-01 00:00:00";
			}

			$pdo = null;

			//画面取得時タイムスタンプと、DB最新タイムスタンプを比較
			if (strtotime($currentTimestamp) >= strtotime($timestamp)) {

				return true;
			} else {

				$message =$this->MMessage->getOneMessage('CMNE000106');
				$this->errors['Check'][] = $message;
				return false;
			}


		} catch (Exception $e){

			$this->printLog("fatal", "例外発生", "WPO040", $e->getMessage());
			$pdo = null;
		}

		//例外の場合、例外メッセージ
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}

	/**
	 * 工程データソートメソッド
	 * @access   public
	 * @param    対象オブジェクト１
	 * @param    対象オブジェクト２
	 * @return   リターンCD(0:同じ -1:１が小さい 1:1が大きい)
	 */
	function cmp($obj1, $obj2) {

		$dt_kotei_st1 = $obj1->data[1];
		$dt_kotei_st2 = $obj2->data[1];
		$dt_kotei_ed1 = $obj1->data[2];
		$dt_kotei_ed2 = $obj2->data[2];

		if ($dt_kotei_ed1 == "") {
			return 1;
		}

		if ($dt_kotei_ed2 == "") {
			return -1;
		}

	    if ($dt_kotei_st1 == $dt_kotei_st2) {
	        return 0;
	    }

		if (strtotime($dt_kotei_st1) < strtotime($dt_kotei_st2)) {

			return -1;
		}

	    return 1;
	}


	/**
	 * 工程データ更新処理
	 * @access   public
	 * @param    工程データ
	 * @return   成否情報(true,false)
	 */
	function setKoteiData($data,$staff_cd) {

		$pdo = null;
		$pdo2 = null;
		$return_cd = "0";

		try {

			//排他制御オン
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WPO',";
			$sql .= "'040',";
			$sql .= "1";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'WPO040 排他制御オン');

			try {

				$pdo2 = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
				$pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$pdo2->beginTransaction();

				//行ずつ処理
				foreach($data as $obj){

					//データの取り出し
					$cellsArray = $obj->cells;
					$dataArray = $obj->data;
					$koteisArray = $obj->koteis;

					//更新、新規の場合
					if ($dataArray[4] == 1 || $dataArray[4] == 2 ) {

						//工程データを時間順にソート
						usort($koteisArray, array("WPO040Model", "cmp"));

						//工程ナンバー
						$koteiNo = 1;

						//最初に全データを削除
						$sql = "CALL P_WPO040_DELETE_DATA(";
						$sql .= "'" . $this->_ninusi_cd . "',";
						$sql .= "'" . $this->_sosiki_cd . "',";
						$sql .= "'" . $dataArray[0] . "',";
						$sql .= "'" . $dataArray[1] . "',";
						$sql .= "@return_cd";
						$sql .= ")";

						$this->execWithPDOLog($pdo2,$sql, '工程ヘッダ&工程　削除');

						$sql = "SELECT";
						$sql .= " @return_cd";

						$this->queryWithPDOLog($stmt,$pdo2,$sql, '工程ヘッダ&工程　削除');

						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$return_cd = $result["@return_cd"];

						if ($return_cd == "1") {

							throw new Exception("工程ヘッダ&工程削除　例外発生");
						}

						//工程ヘッダをセットする
						$sql = "CALL P_WPO040_SET_KOTEI_H(";
						$sql .= "'" . $this->_ninusi_cd . "',";
						$sql .= "'" . $this->_sosiki_cd . "',";
						$sql .= "'" . $dataArray[0] . "',";
						$sql .= "'" . $staff_cd . "',";
						$sql .= "'" . $dataArray[1] . "',";
						$sql .= "'" . $dataArray[2] . "',";

						if ($dataArray[3] == "") {
							$sql .= "NULL,";
						} else {
							$sql .= "'" . $dataArray[3] . "',";
						}

						$sql .= "@return_cd";
						$sql .= ")";

						$this->execWithPDOLog($pdo2,$sql, '工程ヘッダ　追加');

						$sql = "SELECT";
						$sql .= " @return_cd";

						$this->queryWithPDOLog($stmt,$pdo2,$sql, '工程ヘッダ　追加');

						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$return_cd = $result["@return_cd"];

						if ($return_cd == "1") {

							throw new Exception("工程ヘッダ　例外発生");
						}

						//行ごとに工程データをセットする
						foreach ($koteisArray as $obj2) {

							$cellsKoteiArray = $obj2->cells;
							$dataKoteiArray = $obj2->data;

							if ($cellsKoteiArray[0] == "" ||
							    $cellsKoteiArray[1] == "") {

								    continue;
							}


							$sql = "CALL P_WPO040_SET_KOTEI_D(";
							$sql .= "'" . $this->_ninusi_cd . "',";
							$sql .= "'" . $this->_sosiki_cd . "',";
							$sql .= "'" . $dataArray[0] . "',";
							$sql .= "'" . $staff_cd . "',";
							$sql .= "'" . $dataArray[1] . "',";
							$sql .= "" . $koteiNo++ . ",";
							$sql .= "'" . $dataKoteiArray[1] . "',";

							if ($dataKoteiArray[2] == '') {

								$sql .= "NULL,";
							} else {

								$sql .= "'" . $dataKoteiArray[2] . "',";
							}

							$sql .= "'" . substr($dataKoteiArray[0],0,3) . "',";
							$sql .= "'" . substr($dataKoteiArray[0],4,3) . "',";
							$sql .= "'" . substr($dataKoteiArray[0],8,3) . "',";
							$sql .= "" . $pdo2->quote(htmlspecialchars_decode($dataKoteiArray[3])) . ",";
							$sql .= "" . $pdo2->quote(htmlspecialchars_decode($dataKoteiArray[4])) . ",";
							$sql .= "" . $pdo2->quote(htmlspecialchars_decode($dataKoteiArray[5])) . ",";

							if ($dataKoteiArray[6] == '') {

								$sql .= "NULL,";
							} else {

								$sql .= "'" . $dataKoteiArray[6] . "',";
							}

							if ($dataKoteiArray[7] == '') {

								$sql .= "NULL,";
							} else {

								$sql .= "'" . $dataKoteiArray[7] . "',";
							}
							$sql .= "" . $pdo2->quote(htmlspecialchars_decode($dataKoteiArray[8])) . ",";
							$sql .= "" . $pdo2->quote(htmlspecialchars_decode($dataKoteiArray[9])) . ",";
							$sql .= "@return_cd";
							$sql .= ")";

							$this->execWithPDOLog($pdo2,$sql, '工程　追加');

							$sql = "SELECT";
							$sql .= " @return_cd";

							$this->queryWithPDOLog($stmt,$pdo2,$sql, '工程　追加');

							$result = $stmt->fetch(PDO::FETCH_ASSOC);
							$return_cd = $result["@return_cd"];

							if ($return_cd == "1") {

								throw new Exception("工程追加　例外発生");
							}

						}


					//削除の場合
					} else if ($dataArray[4] == 3 || $dataArray[4] == 4) {

						//データを削除する
						$sql = "CALL P_WPO040_DELETE_DATA(";
						$sql .= "'" . $this->_ninusi_cd . "',";
						$sql .= "'" . $this->_sosiki_cd . "',";
						$sql .= "'" . $dataArray[0] . "',";
						$sql .= "'" . $dataArray[1] . "',";
						$sql .= "@return_cd";
						$sql .= ")";

						$this->execWithPDOLog($pdo2,$sql, '工程　削除');

						$sql = "SELECT";
						$sql .= " @return_cd";

						$this->queryWithPDOLog($stmt,$pdo2,$sql, '工程　削除');

						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$return_cd = $result["@return_cd"];

						if ($return_cd == "1") {

							throw new Exception("工程削除　例外発生");
						}

					}

				}

				$pdo2->commit();

			} catch (Exception $e) {

				$this->printLog("fatal", "例外発生", "WPO040", $e->getMessage());
				$pdo2->rollBack();
			}

			$pdo2 = null;

			//排他制御オフ
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WPO',";
			$sql .= "'040',";
			$sql .= "0";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'WPO040 排他制御オフ');

			$pdo = null;

			if ($return_cd == "1") {

				//例外の場合、例外メッセージ
				$message = $this->MMessage->getOneMessage('CMNE000107');
				$this->errors['Check'][] =  $message;
				return false;
			}

			return true;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO040", $e->getMessage());
			$pdo = null;

		}

		//例外発生の場合、エラー
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}

	public function getTimestamp() {

		$pdo = null;

		try{

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//タイムスタンプ取得
			$sql = "CALL P_WPO040_GET_TIMESTAMP(";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " @timestamp";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, '工程　タイムスタンプ取得');

			$sql = "";
			$sql .= " SELECT";
			$sql .= "  @timestamp";


			$this->queryWithPDOLog($stmt,$pdo,$sql, '工程　タイムスタンプ取得');
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			$timestamp = $result['@timestamp'];

			if ($timestamp == null) {
				$timestamp = date('Y-m-d H:i:s');
			}

			return $timestamp;

		} catch (Exception $e){

			$pdo = null;
		}

		return date('Y-m-d H:i:s');

	}

}

?>