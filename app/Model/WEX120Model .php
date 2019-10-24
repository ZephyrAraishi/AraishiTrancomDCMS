<?php
/**
 * WPO040Model
 *
 * 工程管理
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class WPO040Model extends DCMSAppModel{

	public $name = 'WPO040Model';
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
       parent::__construct($ninusi_cd, $sosiki_cd,$kino_id, $id, $table, $ds);

		//メッセージマスタ呼び出し
		$this->MMessage = new MMessage($ninusi_cd,$sosiki_cd,$kino_id);

		//共通関数モデル
		$this->MCommon = new MCommon($ninusi_cd,$sosiki_cd,$kino_id);

	}

	/**
	 * 工程管理データ取得
	 * @access   public
	 * @return   工程管理データ
	 */
	public function getKoteiData($ymd_syugyo = '',$kotei = '',$staff_nm = '',$error_flg = '') {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$allData = array();//全データ
			$uniqueData = array();//プライマリキー

			if ($error_flg == 0 || $error_flg == null) {

				//検索条件に工程がある場合、工程のキーを取得
				$conditionKey = '';

				if ($kotei != ''){

					$sql  = "SELECT DISTINCT";
					$sql .= "  KOTEI_KEY ";
					$sql .= " FROM V_WPO040_KOTEI_KEY ";
					$sql .= " WHERE ";
					$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
					$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
					$sql .= "  BNRI_KOTEI='" . $kotei . "'";

					$this->queryWithPDOLog($stmt,$pdo,$sql,"工程キー取得");

					while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

						$conditionKey =  $conditionKey . "'" . $result['KOTEI_KEY'] . "',";
					}

					if ($conditionKey != '') {

						$conditionKey = substr($conditionKey, 0, strlen($conditionKey)-1);

					} else {

						return;
					}
				}

				//工程データの取得
				$sql  = 'SELECT ';
				$sql .= '  *';
				$sql .= ' FROM V_WPO040_KOTEI ';
				$sql .= ' WHERE ';
				$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
				$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "'";

				//就業日付
				if ($ymd_syugyo != ''){
					$sql .= " AND YMD_SYUGYO = '" . $ymd_syugyo . "'" ;
				}

				//スタッフ氏名⇒2019/09/20　コードでも検索可能とする
				if ($staff_nm != ''){
					if (is_numeric($staff_nm)){
						$sql .= " AND STAFF_CD LIKE '%" . $staff_nm . "%'";
					} else {
						$sql .= " AND STAFF_NM LIKE '%" . $staff_nm . "%'";
					}
				}

				//工程
				if ($conditionKey != "") {
					$sql .= ' AND KOTEI_KEY IN ('.$conditionKey.')';
				}

				$this->queryWithPDOLog($stmt,$pdo,$sql,"工程ヘッダ&工程 取得");

				while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

					$array = array(
					    "NINUSI_CD" => $result['NINUSI_CD'],
						"SOSIKI_CD" => $result['SOSIKI_CD'],
						"STAFF_CD" => $result['STAFF_CD'],
						"YMD_SYUGYO" => $result['YMD_SYUGYO'],
						"STAFF_NM" => $result['STAFF_NM'],
						"KOTEI_KEY" => $result['KOTEI_KEY'],
						"DT_SYUGYO_ST" => $result['DT_SYUGYO_ST'],
						"DT_SYUGYO_ED" => $result['DT_SYUGYO_ED'],
						"KOTEI_NO" => $result['KOTEI_NO'],
						"BNRI_KOTEI" => $result['BNRI_KOTEI'],
						"BNRI_SAI_RYAKU" => $result['BNRI_SAI_RYAKU'],
						"DT_KOTEI_ST" => $result['DT_KOTEI_ST'],
						"BIKO1" => $result['BIKO1'],
						"BIKO2" => $result['BIKO2'],
						"BIKO3" => $result['BIKO3'],
						"KAISI_KYORI" => $result['KAISI_KYORI'],
						"SYURYO_KYORI" => $result['SYURYO_KYORI'],
						"SYARYO_BANGO" => $result['SYARYO_BANGO'],
						"CHIKU_AREA" => $result['CHIKU_AREA'],
						"DT_KOTEI_ED" => $result['DT_KOTEI_ED']

					);

					$allData[] = $array;

					$array = array(
					    "NINUSI_CD" => $result['NINUSI_CD'],
						"SOSIKI_CD" => $result['SOSIKI_CD'],
						"STAFF_CD" => $result['STAFF_CD'],
						"YMD_SYUGYO" => $result['YMD_SYUGYO']

					);

					$uniqueData[] = $array;
				}

				//プライマリデータの重複を削除
				$tmp = array();
				foreach($uniqueData as $key => $val){

					if(!in_array($val,$tmp)){
						$tmp[] = $val;
					}
				}

				$uniqueData = $tmp;

				//工程データをINDEXで処理しやすい形に変換
				$arrayList = array();
				foreach($uniqueData as $array1){

					$tempArray = array();

					foreach($allData as $array2) {

						if ($array1['NINUSI_CD'] == $array2['NINUSI_CD'] &&
								$array1['SOSIKI_CD'] == $array2['SOSIKI_CD'] &&
								$array1['STAFF_CD'] == $array2['STAFF_CD'] &&
								$array1['YMD_SYUGYO'] == $array2['YMD_SYUGYO']) {

									$tempArray2 = array();

									$tempArray2['NINUSI_CD'] = $array2['NINUSI_CD'];
									$tempArray2['SOSIKI_CD'] = $array2['SOSIKI_CD'];
									$tempArray2['STAFF_CD'] = $array2['STAFF_CD'];
									$tempArray2['YMD_SYUGYO'] = $array2['YMD_SYUGYO'];
									$tempArray2['STAFF_NM'] = $array2['STAFF_NM'];
									$tempArray2['KOTEI_KEY'] = $array2['KOTEI_KEY'];
									$tempArray2['DT_SYUGYO_ST'] = $array2['DT_SYUGYO_ST'];
									$tempArray2['DT_SYUGYO_ED'] = $array2['DT_SYUGYO_ED'];
									$tempArray2['DT_SYUGYO_ST2'] = $this->MCommon->changeDateTime($array2['YMD_SYUGYO'],$array2['DT_SYUGYO_ST']);
									$tempArray2['DT_SYUGYO_ED2'] = $this->MCommon->changeDateTime($array2['YMD_SYUGYO'],$array2['DT_SYUGYO_ED']);
									$tempArray2['KOTEI_NO'] = $array2['KOTEI_NO'];
									$tempArray2['BNRI_KOTEI'] = $array2['BNRI_KOTEI'];
									$tempArray2['BNRI_SAI_RYAKU'] = $array2['BNRI_SAI_RYAKU'];
									$tempArray2['DT_KOTEI_ST'] = $array2['DT_KOTEI_ST'];
									$tempArray2['DT_KOTEI_ED'] = $array2['DT_KOTEI_ED'];
									$tempArray2['BIKO1'] = $array2['BIKO1'];
									$tempArray2['BIKO2'] = $array2['BIKO2'];
									$tempArray2['BIKO3'] = $array2['BIKO3'];
									$tempArray2["KAISI_KYORI"] = $array2['KAISI_KYORI'];
									$tempArray2["SYURYO_KYORI"] = $array2['SYURYO_KYORI'];
									$tempArray2["SYARYO_BANGO"] = $array2['SYARYO_BANGO'];
									$tempArray2["CHIKU_AREA"] = $array2['CHIKU_AREA'];
									$tempArray2['DT_KOTEI_ST2'] = $this->MCommon->changeDateTime($array2['YMD_SYUGYO'],$array2['DT_KOTEI_ST']);
									$tempArray2['DT_KOTEI_ED2'] = $this->MCommon->changeDateTime($array2['YMD_SYUGYO'],$array2['DT_KOTEI_ED']);

									$tempArray[] = $tempArray2;
								}

					}

					$arrayList[] = $tempArray;
				}

			} else if($error_flg == 1) {

				//検索条件に工程がある場合、工程のキーを取得
				$conditionKey = '';

				if ($kotei != ''){

					$sql  = "SELECT DISTINCT";
					$sql .= "  KOTEI_KEY ";
					$sql .= " FROM V_WPO040_KOTEI_KEY ";
					$sql .= " WHERE ";
					$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
					$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
					$sql .= "  BNRI_KOTEI='" . $kotei . "'";

					$this->queryWithPDOLog($stmt,$pdo,$sql,"工程キー取得");

					while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

						$conditionKey =  $conditionKey . "'" . $result['KOTEI_KEY'] . "',";
					}

					if ($conditionKey != '') {

						$conditionKey = substr($conditionKey, 0, strlen($conditionKey)-1);

					} else {

						return;
					}
				}

				//工程データの取得
				$sql  = 'SELECT ';
				$sql .= '  *';
				$sql .= ' FROM V_WPO040_KOTEI ';
				$sql .= ' WHERE ';
				$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
				$sql .= "  KEIKA=1 AND";
				$sql .= "  (DT_SYUGYO_ED IS NULL OR";
				$sql .= "  DT_SYUGYO_ED = '' ) AND";
				$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "'";

				//就業日付
				if ($ymd_syugyo != ''){
					$sql .= " AND YMD_SYUGYO = '" . $ymd_syugyo . "'" ;
				}

				//スタッフ氏名⇒2019/09/20 コードでも検索可能とする
				if ($staff_nm != ''){
					if (is_numeric($staff_nm)){
						$sql .= " AND STAFF_CD LIKE '%" . $staff_nm . "%'";
					} else {
						$sql .= " AND STAFF_NM LIKE '%" . $staff_nm . "%'";
					}
				}

				//工程
				if ($conditionKey != "") {
					$sql .= ' AND KOTEI_KEY IN ('.$conditionKey.')';
				}

				$this->queryWithPDOLog($stmt,$pdo,$sql,"工程ヘッダ&工程 取得");

				while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

					$array = array(
							"NINUSI_CD" => $result['NINUSI_CD'],
							"SOSIKI_CD" => $result['SOSIKI_CD'],
							"STAFF_CD" => $result['STAFF_CD'],
							"YMD_SYUGYO" => $result['YMD_SYUGYO'],
							"STAFF_NM" => $result['STAFF_NM'],
							"KOTEI_KEY" => $result['KOTEI_KEY'],
							"DT_SYUGYO_ST" => $result['DT_SYUGYO_ST'],
							"DT_SYUGYO_ED" => $result['DT_SYUGYO_ED'],
							"KOTEI_NO" => $result['KOTEI_NO'],
							"BNRI_KOTEI" => $result['BNRI_KOTEI'],
							"BNRI_SAI_RYAKU" => $result['BNRI_SAI_RYAKU'],
							"DT_KOTEI_ST" => $result['DT_KOTEI_ST'],
							"BIKO1" => $result['BIKO1'],
							"BIKO2" => $result['BIKO2'],
							"BIKO3" => $result['BIKO3'],
							"KAISI_KYORI" => $result['KAISI_KYORI'],
							"SYURYO_KYORI" => $result['SYURYO_KYORI'],
							"SYARYO_BANGO" => $result['SYARYO_BANGO'],
							"CHIKU_AREA" => $result['CHIKU_AREA'],
							"DT_KOTEI_ED" => $result['DT_KOTEI_ED']
					);

					$allData[] = $array;

					$array = array(
							"NINUSI_CD" => $result['NINUSI_CD'],
							"SOSIKI_CD" => $result['SOSIKI_CD'],
							"STAFF_CD" => $result['STAFF_CD'],
							"YMD_SYUGYO" => $result['YMD_SYUGYO']

					);

					$uniqueData[] = $array;
				}

				//プライマリデータの重複を削除
				$tmp = array();
				foreach($uniqueData as $key => $val){

					if(!in_array($val,$tmp)){
						$tmp[] = $val;
					}
				}

				$uniqueData = $tmp;

				//工程データをINDEXで処理しやすい形に変換
				$arrayList = array();
				foreach($uniqueData as $array1){

					$tempArray = array();

					foreach($allData as $array2) {

						if ($array1['NINUSI_CD'] == $array2['NINUSI_CD'] &&
								$array1['SOSIKI_CD'] == $array2['SOSIKI_CD'] &&
								$array1['STAFF_CD'] == $array2['STAFF_CD'] &&
								$array1['YMD_SYUGYO'] == $array2['YMD_SYUGYO']) {

									$tempArray2 = array();

									$tempArray2['NINUSI_CD'] = $array2['NINUSI_CD'];
									$tempArray2['SOSIKI_CD'] = $array2['SOSIKI_CD'];
									$tempArray2['STAFF_CD'] = $array2['STAFF_CD'];
									$tempArray2['YMD_SYUGYO'] = $array2['YMD_SYUGYO'];
									$tempArray2['STAFF_NM'] = $array2['STAFF_NM'];
									$tempArray2['KOTEI_KEY'] = $array2['KOTEI_KEY'];
									$tempArray2['DT_SYUGYO_ST'] = $array2['DT_SYUGYO_ST'];
									$tempArray2['DT_SYUGYO_ED'] = $array2['DT_SYUGYO_ED'];
									$tempArray2['DT_SYUGYO_ST2'] = $this->MCommon->changeDateTime($array2['YMD_SYUGYO'],$array2['DT_SYUGYO_ST']);
									$tempArray2['DT_SYUGYO_ED2'] = $this->MCommon->changeDateTime($array2['YMD_SYUGYO'],$array2['DT_SYUGYO_ED']);
									$tempArray2['KOTEI_NO'] = $array2['KOTEI_NO'];
									$tempArray2['BNRI_KOTEI'] = $array2['BNRI_KOTEI'];
									$tempArray2['BNRI_SAI_RYAKU'] = $array2['BNRI_SAI_RYAKU'];
									$tempArray2['DT_KOTEI_ST'] = $array2['DT_KOTEI_ST'];
									$tempArray2['DT_KOTEI_ED'] = $array2['DT_KOTEI_ED'];
									$tempArray2['BIKO1'] = $array2['BIKO1'];
									$tempArray2['BIKO2'] = $array2['BIKO2'];
									$tempArray2['BIKO3'] = $array2['BIKO3'];
									$tempArray2["KAISI_KYORI"] = $array2['KAISI_KYORI'];
									$tempArray2["SYURYO_KYORI"] = $array2['SYURYO_KYORI'];
									$tempArray2["SYARYO_BANGO"] = $array2['SYARYO_BANGO'];
									$tempArray2["CHIKU_AREA"] = $array2['CHIKU_AREA'];
									$tempArray2['DT_KOTEI_ST2'] = $this->MCommon->changeDateTime($array2['YMD_SYUGYO'],$array2['DT_KOTEI_ST']);
									$tempArray2['DT_KOTEI_ED2'] = $this->MCommon->changeDateTime($array2['YMD_SYUGYO'],$array2['DT_KOTEI_ED']);

									$tempArray[] = $tempArray2;
								}

					}

					$arrayList[] = $tempArray;
				}

			} else if($error_flg == 2) {
				//検索条件に工程がある場合、工程のキーを取得
				$conditionKey = '';

				if ($kotei != ''){

					$sql  = "SELECT DISTINCT";
					$sql .= "  KOTEI_KEY ";
					$sql .= " FROM V_WPO040_KOTEI_KEY ";
					$sql .= " WHERE ";
					$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
					$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
					$sql .= "  BNRI_KOTEI='" . $kotei . "'";

					$this->queryWithPDOLog($stmt,$pdo,$sql,"工程キー取得");

					while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

						$conditionKey =  $conditionKey . "'" . $result['KOTEI_KEY'] . "',";
					}

					if ($conditionKey != '') {

						$conditionKey = substr($conditionKey, 0, strlen($conditionKey)-1);

					} else {

						return;
					}
				}

				//工程データの取得
				$sql  = 'SELECT ';
				$sql .= '  *';
				$sql .= ' FROM V_WPO040_KOTEI ';
				$sql .= ' WHERE ';
				$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
				$sql .= "  KEIKA=1 AND";
				$sql .= "  (DT_SYUGYO_ED IS NULL OR";
				$sql .= "  DT_SYUGYO_ED = '' ) AND";
				$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "'";

				//就業日付
				if ($ymd_syugyo != ''){
					$sql .= " AND YMD_SYUGYO <= '" . $ymd_syugyo . "'" ;
				}

				//スタッフ氏名
				if ($staff_nm != ''){
					$sql .= " AND STAFF_NM LIKE '%" . $staff_nm . "%'";
				}

				//工程
				if ($conditionKey != "") {
					$sql .= ' AND KOTEI_KEY IN ('.$conditionKey.')';
				}

				$this->queryWithPDOLog($stmt,$pdo,$sql,"工程ヘッダ&工程 取得");

				while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

					$array = array(
							"NINUSI_CD" => $result['NINUSI_CD'],
							"SOSIKI_CD" => $result['SOSIKI_CD'],
							"STAFF_CD" => $result['STAFF_CD'],
							"YMD_SYUGYO" => $result['YMD_SYUGYO'],
							"STAFF_NM" => $result['STAFF_NM'],
							"KOTEI_KEY" => $result['KOTEI_KEY'],
							"DT_SYUGYO_ST" => $result['DT_SYUGYO_ST'],
							"DT_SYUGYO_ED" => $result['DT_SYUGYO_ED'],
							"KOTEI_NO" => $result['KOTEI_NO'],
							"BNRI_KOTEI" => $result['BNRI_KOTEI'],
							"BNRI_SAI_RYAKU" => $result['BNRI_SAI_RYAKU'],
							"DT_KOTEI_ST" => $result['DT_KOTEI_ST'],
							"BIKO1" => $result['BIKO1'],
							"BIKO2" => $result['BIKO2'],
							"BIKO3" => $result['BIKO3'],
							"KAISI_KYORI" => $result['KAISI_KYORI'],
							"SYURYO_KYORI" => $result['SYURYO_KYORI'],
							"SYARYO_BANGO" => $result['SYARYO_BANGO'],
							"CHIKU_AREA" => $result['CHIKU_AREA'],
							"DT_KOTEI_ED" => $result['DT_KOTEI_ED']
					);

					$allData[] = $array;

					$array = array(
							"NINUSI_CD" => $result['NINUSI_CD'],
							"SOSIKI_CD" => $result['SOSIKI_CD'],
							"STAFF_CD" => $result['STAFF_CD'],
							"YMD_SYUGYO" => $result['YMD_SYUGYO']

					);

					$uniqueData[] = $array;
				}

				//プライマリデータの重複を削除
				$tmp = array();
				foreach($uniqueData as $key => $val){

					if(!in_array($val,$tmp)){
						$tmp[] = $val;
					}
				}

				$uniqueData = $tmp;

				//工程データをINDEXで処理しやすい形に変換
				$arrayList = array();
				foreach($uniqueData as $array1){

					$tempArray = array();

					foreach($allData as $array2) {

						if ($array1['NINUSI_CD'] == $array2['NINUSI_CD'] &&
								$array1['SOSIKI_CD'] == $array2['SOSIKI_CD'] &&
								$array1['STAFF_CD'] == $array2['STAFF_CD'] &&
								$array1['YMD_SYUGYO'] == $array2['YMD_SYUGYO']) {

									$tempArray2 = array();

									$tempArray2['NINUSI_CD'] = $array2['NINUSI_CD'];
									$tempArray2['SOSIKI_CD'] = $array2['SOSIKI_CD'];
									$tempArray2['STAFF_CD'] = $array2['STAFF_CD'];
									$tempArray2['YMD_SYUGYO'] = $array2['YMD_SYUGYO'];
									$tempArray2['STAFF_NM'] = $array2['STAFF_NM'];
									$tempArray2['KOTEI_KEY'] = $array2['KOTEI_KEY'];
									$tempArray2['DT_SYUGYO_ST'] = $array2['DT_SYUGYO_ST'];
									$tempArray2['DT_SYUGYO_ED'] = $array2['DT_SYUGYO_ED'];
									$tempArray2['DT_SYUGYO_ST2'] = $this->MCommon->changeDateTime($array2['YMD_SYUGYO'],$array2['DT_SYUGYO_ST']);
									$tempArray2['DT_SYUGYO_ED2'] = $this->MCommon->changeDateTime($array2['YMD_SYUGYO'],$array2['DT_SYUGYO_ED']);
									$tempArray2['KOTEI_NO'] = $array2['KOTEI_NO'];
									$tempArray2['BNRI_KOTEI'] = $array2['BNRI_KOTEI'];
									$tempArray2['BNRI_SAI_RYAKU'] = $array2['BNRI_SAI_RYAKU'];
									$tempArray2['DT_KOTEI_ST'] = $array2['DT_KOTEI_ST'];
									$tempArray2['DT_KOTEI_ED'] = $array2['DT_KOTEI_ED'];
									$tempArray2['BIKO1'] = $array2['BIKO1'];
									$tempArray2['BIKO2'] = $array2['BIKO2'];
									$tempArray2['BIKO3'] = $array2['BIKO3'];
									$tempArray2["KAISI_KYORI"] = $array2['KAISI_KYORI'];
									$tempArray2["SYURYO_KYORI"] = $array2['SYURYO_KYORI'];
									$tempArray2["SYARYO_BANGO"] = $array2['SYARYO_BANGO'];
									$tempArray2["CHIKU_AREA"] = $array2['CHIKU_AREA'];
									$tempArray2['DT_KOTEI_ST2'] = $this->MCommon->changeDateTime($array2['YMD_SYUGYO'],$array2['DT_KOTEI_ST']);
									$tempArray2['DT_KOTEI_ED2'] = $this->MCommon->changeDateTime($array2['YMD_SYUGYO'],$array2['DT_KOTEI_ED']);

									$tempArray[] = $tempArray2;
								}

					}

					$arrayList[] = $tempArray;
				}
			}



			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO040", $e->getMessage());
			$pdo = null;
		}


		return array();
	}

	/**
	 * 工程データ入力チェック
	 * @access   public
	 * @param    工程データ
	 * @return   成否情報(true,false)
	 */
	function checkKoteiData($data) {


	 	try {

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$timestamp = $this->MCommon->getTimestamp();//現在時間を保持


			/********** 必須項目チェックFASE *************/
			$gyoCount = 0;
			$errorCount = 0;
			$komokuCount = 0;

			foreach($data as $obj){

				$gyoCount++;

				//データを抜き出す
				$cellsArray = $obj->cells;
				$dataArray = $obj->data;
				$koteisArray = $obj->koteis;


				//行が更新データの場合
				if ($dataArray[4] == 1 ) {

					$komokuCount++;

					//工程ヘッダのチェック

					//データがない
					if ($dataArray[0] == "") {

						$message = vsprintf($this->MMessage->getOneMessage('WPOE040002'), array($gyoCount ));
						$this->errors['Check'][] =  $message;
						$errorCount++;
						continue;
					}

					//就業日付のデータフォーマットが不正
					if (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}/",$dataArray[1]) == false) {

						$message = vsprintf($this->MMessage->getOneMessage('WPOE040003'), array($gyoCount ));
						$this->errors['Check'][] =  $message;
						$errorCount++;
						continue;
					}

					$year = substr($dataArray[1], 0,4);
					$month = substr($dataArray[1], 5,2);
					$day = substr($dataArray[1], 8,2);

					//就業日付が不正
					if (checkdate($month, $day, $year) == false) {

						$message = vsprintf($this->MMessage->getOneMessage('WPOE040004'), array($gyoCount ));
						$this->errors['Check'][] =  $message;
						$errorCount++;
						continue;
					}

					//就業開始時刻のデータフォーマットが不正
					if (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/",$dataArray[2]) == false) {

						$message = vsprintf($this->MMessage->getOneMessage('WPOE040005'), array($gyoCount ));
						$this->errors['Check'][] =  $message;
						$errorCount++;
						continue;
					}

					//就業開始時刻が不正
					if (date_parse_from_format ("Y-m-d H:i:s" ,$dataArray[2])['error_count'] > 0) {
					//if (strptime($dataArray[2],'%Y-%m-%d %T' ) == false) {

						$message = vsprintf($this->MMessage->getOneMessage('WPOE040006'), array($gyoCount ));
						$this->errors['Check'][] =  $message;
						$errorCount++;
						continue;
					}

					//時間チェック
					if (strtotime($timestamp) < strtotime($dataArray[2])) {

						$message = vsprintf($this->MMessage->getOneMessage('WPOE040031'), array($gyoCount,"就業開始時間"));
						$this->errors['Check'][] = $message;
						$errorCount++;
						continue;
					}

					//就業終了時刻が入力されている場合
					if ($dataArray[3] != "") {

						//就業終了時刻のフォーマットが不正
						if (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/",$dataArray[3]) == false) {

							$message = vsprintf($this->MMessage->getOneMessage('WPOE040007'), array($gyoCount ));
							$this->errors['Check'][] =  $message;
							$errorCount++;
							continue;
						}

						//就業終了時刻が不正
						if (date_parse_from_format ("Y-m-d H:i:s" ,$dataArray[3])['error_count'] > 0) {
						//if (strptime($dataArray[3],'%Y-%m-%d %T' ) == false) {

							$message = vsprintf($this->MMessage->getOneMessage('WPOE040008'), array($gyoCount ));
							$this->errors['Check'][] =  $message;
							$errorCount++;
							continue;
						}

						//時間チェック
						if (strtotime($timestamp) < strtotime($dataArray[3])) {

							$message = vsprintf($this->MMessage->getOneMessage('WPOE040031'), array($gyoCount ,"就業終了時間"));
							$this->errors['Check'][] = $message;
							$errorCount++;
							continue;
						}

						//就業終了時刻が就業開始時刻より遅い
						if (strtotime($dataArray[3]) < strtotime($dataArray[2])) {

							$message = vsprintf($this->MMessage->getOneMessage('WPOE040009'), array($gyoCount ));
							$this->errors['Check'][] =  $message;
							$errorCount++;
							continue;
						}

					}

					//工程データのチェック
					$koteiCount = 0;//工程の数
					$koteiCyuCount = 0;//工程が未完了の数
					$koteiErrorFlg = 0;//工程でエラーがあった場合

					foreach($koteisArray as $obj2){

						//データの取り出し
						$cellsKoteiArray = $obj2->cells;
						$dataKoteiArray = $obj2->data;
						$koteiCount++;

						//工程データがない場合はスキップ
						if ($cellsKoteiArray[0] == "" &&
							$cellsKoteiArray[1] == "" &&
							$cellsKoteiArray[2] == "" ) {

							continue;
						}

						//工程データの工程と工程開始時刻は必須
						if (($cellsKoteiArray[0] == "" &&
							 $cellsKoteiArray[1] != "") ||
							($cellsKoteiArray[0] != "" &&
							 $cellsKoteiArray[1] == "")) {

							$message = vsprintf($this->MMessage->getOneMessage('WPOE040010'), array($gyoCount ,$koteiCount));
							$this->errors['Check'][] =  $message;
							$errorCount++;
							$koteiErrorFlg = 1;
							break;
						}

						//工程開始時刻のフォーマットが不正
						if (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/",$dataKoteiArray[1]) == false) {

							$message = vsprintf($this->MMessage->getOneMessage('WPOE040011'), array($gyoCount ,$koteiCount));
							$this->errors['Check'][] =  $message;
							$errorCount++;
							$koteiErrorFlg = 1;
							break;
						}

						//工程開始時刻が不正
						if (date_parse_from_format ("Y-m-d H:i:s" ,$dataKoteiArray[1])['error_count'] > 0) {
						//if (strptime($dataKoteiArray[1],'%Y-%m-%d %T' ) == false) {

							$message = vsprintf($this->MMessage->getOneMessage('WPOE040012'), array($gyoCount ,$koteiCount));
							$this->errors['Check'][] =  $message;
							$errorCount++;
							$koteiErrorFlg = 1;
							break;
						}

						//時間チェック
						if (strtotime($timestamp) < strtotime($dataKoteiArray[1])) {

							$message = vsprintf($this->MMessage->getOneMessage('WPOE040032'), array($gyoCount ,$koteiCount,"工程開始時間"));
							$this->errors['Check'][] = $message;
							$errorCount++;
							$koteiErrorFlg = 1;
							break;
						}

						//工程開始時刻は、就業開始時刻より後でなければならない
						if (strtotime($dataArray[2]) > strtotime($dataKoteiArray[1])) {

							$message = vsprintf($this->MMessage->getOneMessage('WPOE040021'), array($gyoCount ,$koteiCount));
							$this->errors['Check'][] =  $message;
							$errorCount++;
							$koteiErrorFlg = 1;
							break;
						}

						//就業終了時刻がある場合
						if ($dataArray[3] != "") {

							//工程開始時刻は、就業終了時刻より前でなければならない
							if (strtotime($dataArray[3]) < strtotime($dataKoteiArray[1])) {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE040023'), array($gyoCount ,$koteiCount));
								$this->errors['Check'][] =  $message;
								$errorCount++;
								$koteiErrorFlg = 1;
								break;
							}

						}


						//工程終了時刻がある場合
						if ($dataKoteiArray[2] != "") {

							//工程終了時刻が入力されていない
							if ($dataKoteiArray[2] == "") {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE040025'), array($gyoCount ,$koteiCount));
								$this->errors['Check'][] =  $message;
								$errorCount++;
								$koteiErrorFlg = 1;
								break;
							}

							//工程終了時刻のフォーマットが不正
							if (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/",$dataKoteiArray[2]) == false) {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE040013'), array($gyoCount ,$koteiCount));
								$this->errors['Check'][] =  $message;
								$errorCount++;
								$koteiErrorFlg = 1;
								break;
							}

							//工程終了時刻が不正
							if (date_parse_from_format ("Y-m-d H:i:s" ,$dataKoteiArray[2])['error_count'] > 0) {
							//if (strptime($dataKoteiArray[2],'%Y-%m-%d %T' ) == false) {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE040014'), array($gyoCount ,$koteiCount));
								$this->errors['Check'][] =  $message;
								$errorCount++;
								$koteiErrorFlg = 1;
								break;
							}

							//時間チェック
							if (strtotime($timestamp) < strtotime($dataKoteiArray[2])) {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE040032'), array($gyoCount ,$koteiCount,"工程終了時間"));
								$this->errors['Check'][] = $message;
								$errorCount++;
								$koteiErrorFlg = 1;
								break;
							}

							//工程終了時刻は工程開始時刻の後でなければならない
							if (strtotime($dataKoteiArray[2]) < strtotime($dataKoteiArray[1])) {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE040015'), array($gyoCount ,$koteiCount));
								$this->errors['Check'][] =  $message;
								$errorCount++;
								$koteiErrorFlg = 1;
								break;
							}

							//工程終了時刻は、工程開始時刻の後でなければならない
							if (strtotime($dataArray[2]) > strtotime($dataKoteiArray[2])) {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE040022'), array($gyoCount ,$koteiCount));
								$this->errors['Check'][] =  $message;
								$errorCount++;
								$koteiErrorFlg = 1;
								break;
							}

							//就業開始時刻がある場合
							if ($dataArray[3] != "") {

								//工程終了時刻は、就業終了時刻より前でなければならいない
								if (strtotime($dataArray[3]) < strtotime($dataKoteiArray[2])) {

									$message = vsprintf($this->MMessage->getOneMessage('WPOE040024'), array($gyoCount ,$koteiCount));
									$this->errors['Check'][] =  $message;
									$errorCount++;
									$koteiErrorFlg = 1;
									break;
								}
							}

						//工程終了時刻がない場合
						} else {
							$koteiCyuCount++;

							//工程未完了は一個のみでなければならない
							if ($koteiCyuCount > 1) {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE040016'), array($gyoCount ,$koteiCyuCount));
								$this->errors['Check'][] =  $message;
								$errorCount++;
								$koteiErrorFlg = 1;
								break;
							}
						}


					}

					//工程でエラーが起こった場合、CONTINUE
					if ($koteiErrorFlg == 1) {
						continue;
					}


					//工程時間の時間範囲がかぶってないかチェック
					$koteiCount = 0;

					//一個ずつ比較していく（総当たり）
					foreach($koteisArray as $obj2){

						//データを取り出す
						$cellsKoteiArrayBase = $obj2->cells;
						$dataKoteiArrayBase = $obj2->data;
						$koteiCount++;

						$dt_kotei_st_base = $dataKoteiArrayBase[1];
						$dt_kotei_ed_base = $dataKoteiArrayBase[2];

						//工程データがない場合スキップ
						if ($cellsKoteiArrayBase[0] == "" &&
							$cellsKoteiArrayBase[1] == "" &&
							$cellsKoteiArrayBase[2] == "" ) {

							continue;
						}

						//工程が完了している場合
						if ($dt_kotei_ed_base != "") {

							//baseとの総当たり比較
							$koteiCount2 = 0;
							foreach($koteisArray as $obj3){

								//データを取り出す
								$cellsKoteiArray = $obj3->cells;
								$dataKoteiArray = $obj3->data;
								$koteiCount2++;

								$dt_kotei_st = $dataKoteiArray[1];
								$dt_kotei_ed = $dataKoteiArray[2];

								//データがない場合スキップ
								if ($cellsKoteiArray[0] == "" &&
									$cellsKoteiArray[1] == "" &&
									$cellsKoteiArray[2] == "" ) {

									continue;
								}

								//オブジェクトが一緒の場合スキップ
								if ($obj2 === $obj3) {

									continue;
								}

								//未完了の工程の場合はスキップ
								if ($dt_kotei_ed == "") {
									continue;
								}

								//工程データが被っているか確認
								if ((strtotime($dt_kotei_st) < strtotime($dt_kotei_st_base) &&
									 strtotime($dt_kotei_st_base) < strtotime($dt_kotei_ed)) ||
									(strtotime($dt_kotei_st) < strtotime($dt_kotei_ed_base) &&
									 strtotime($dt_kotei_ed_base) < strtotime($dt_kotei_ed)) ||
									(strtotime($dt_kotei_st) > strtotime($dt_kotei_st_base) &&
									 strtotime($dt_kotei_ed_base) > strtotime($dt_kotei_ed))
									 ) {

									$message = vsprintf($this->MMessage->getOneMessage('WPOE040019'), array($gyoCount ,$koteiCount,$koteiCount2));
									$this->errors['Check'][] = $message;
									$errorCount++;
									$koteiErrorFlg = 1;
									break;
								}

							}

							if ($koteiErrorFlg == 1) {
								break;
							}

						//工程が未完了の場合（この時点で一つのみのはず）
						} else {

							//baseとの総当たり比較
							$koteiCount2 = 0;
							foreach($koteisArray as $obj3){

								//データの取り出し
								$cellsKoteiArray = $obj3->cells;
								$dataKoteiArray = $obj3->data;
								$koteiCount2++;

								$dt_kotei_st = $dataKoteiArray[1];
								$dt_kotei_ed = $dataKoteiArray[2];

								//データがない場合はスキップ
								if ($cellsKoteiArray[0] == "" &&
									$cellsKoteiArray[1] == "" &&
									$cellsKoteiArray[2] == "" ) {

									continue;
								}

								//オブジェクトが被る場合
								if ($obj2 === $obj3) {

									continue;
								}

								//工程が未完了な場合、上条件で満たされるはず
								if ($dt_kotei_ed == "") {
									continue;
								}

								//工程開始時間は最新でなければならない
								if (strtotime($dt_kotei_st_base) < strtotime($dt_kotei_ed)) {

									$message = vsprintf($this->MMessage->getOneMessage('WPOE040019'), array($gyoCount ,$koteiCount));
									$this->errors['Check'][] = $message;
									$errorCount++;
									$koteiErrorFlg = 1;
									break;
								}

							}

							if ($koteiErrorFlg == 1) {
								break;
							}
						}
					}

					//工程でエラーが起こった場合、CONTINUE
					if ($koteiErrorFlg == 1) {
						continue;
					}


				//新規の場合
				} else if($dataArray[4] == 2) {

					$komokuCount++;

					//工程ヘッダのチェック

					//データがない
					if ($dataArray[0] == "") {

						$message = vsprintf($this->MMessage->getOneMessage('WPOE040002'), array($gyoCount ));
						$this->errors['Check'][] =  $message;
						$errorCount++;
						continue;
					}

					//就業日付のフォーマットが不正
					if (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}/",$dataArray[1]) == false) {

						$message = vsprintf($this->MMessage->getOneMessage('WPOE040003'), array($gyoCount ));
						$this->errors['Check'][] =  $message;
						$errorCount++;
						continue;
					}

					$year = substr($dataArray[1], 0,4);
					$month = substr($dataArray[1], 5,2);
					$day = substr($dataArray[1], 8,2);

					//就業日付が不正
					if (checkdate($month, $day, $year) == false) {

						$message = vsprintf($this->MMessage->getOneMessage('WPOE040004'), array($gyoCount ));
						$this->errors['Check'][] =  $message;
						$errorCount++;
						continue;
					}

					//就業開始時刻のフォーマットが不正
					if (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/",$dataArray[2]) == false) {

						$message = vsprintf($this->MMessage->getOneMessage('WPOE040005'), array($gyoCount ));
						$this->errors['Check'][] =  $message;
						$errorCount++;
						continue;
					}

					//就業開始時刻が不正
					if (date_parse_from_format ("Y-m-d H:i:s" ,$dataArray[2])['error_count'] > 0) {
					//if (strptime($dataArray[2],'%Y-%m-%d %T' ) == false) {

						$message = vsprintf($this->MMessage->getOneMessage('WPOE040006'), array($gyoCount ));
						$this->errors['Check'][] =  $message;
						$errorCount++;
						continue;
					}

					//時間チェック
					if (strtotime($timestamp) < strtotime($dataArray[2])) {

						$message = vsprintf($this->MMessage->getOneMessage('WPOE040031'), array($gyoCount ,"就業開始時間"));
						$this->errors['Check'][] = $message;
						$errorCount++;
						continue;
					}

					//就業終了時刻がある場合
					if ($dataArray[3] != "") {

						//就業終了時刻のフォーマットが不正
						if (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/",$dataArray[3]) == false) {

							$message = vsprintf($this->MMessage->getOneMessage('WPOE040007'), array($gyoCount ));
							$this->errors['Check'][] =  $message;
							$errorCount++;
							continue;
						}

						//就業終了時刻が不正
						if (date_parse_from_format ("Y-m-d H:i:s" ,$dataArray[3])['error_count'] > 0) {
						//if (strptime($dataArray[3],'%Y-%m-%d %T' ) == false) {

							$message = vsprintf($this->MMessage->getOneMessage('WPOE040008'), array($gyoCount ));
							$this->errors['Check'][] =  $message;
							$errorCount++;
							continue;
						}

						//時間チェック
						if (strtotime($timestamp) < strtotime($dataArray[3])) {

							$message = vsprintf($this->MMessage->getOneMessage('WPOE040031'), array($gyoCount,"就業終了時間"));
							$this->errors['Check'][] = $message;
							$errorCount++;
							continue;
						}

						//就業終了時刻は、就業開始時刻の後でなければならない
						if (strtotime($dataArray[3]) < strtotime($dataArray[2])) {

							$message = vsprintf($this->MMessage->getOneMessage('WPOE040009'), array($gyoCount ));
							$this->errors['Check'][] =  $message;
							$errorCount++;
							continue;
						}

					}

					//工程データのチェック
					$koteiCount = 0;
					$koteiCyuCount = 0;
					$koteiErrorFlg = 0;

					foreach($koteisArray as $obj2){

						//データの取り出し
						$cellsKoteiArray = $obj2->cells;
						$dataKoteiArray = $obj2->data;
						$koteiCount++;

						//データなしの場合
						if ($cellsKoteiArray[0] == "" &&
							$cellsKoteiArray[1] == "" &&
							$cellsKoteiArray[2] == "" ) {

							continue;
						}

						//工程と工程開始時間は必須
						if (($cellsKoteiArray[0] == "" &&
							 $cellsKoteiArray[1] != "") ||
							($cellsKoteiArray[0] != "" &&
							 $cellsKoteiArray[1] == "")) {

							$message = vsprintf($this->MMessage->getOneMessage('WPOE040010'), array($gyoCount ,$koteiCount));
							$this->errors['Check'][] =  $message;
							$errorCount++;
							$koteiErrorFlg = 1;
							break;
						}

						//工程開始時刻のフォーマットが不正
						if (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/",$dataKoteiArray[1]) == false) {

							$message = vsprintf($this->MMessage->getOneMessage('WPOE040011'), array($gyoCount ,$koteiCount));
							$this->errors['Check'][] =  $message;
							$errorCount++;
							$koteiErrorFlg = 1;
							break;
						}

						//工程開始時刻が不正
						if (date_parse_from_format ("Y-m-d H:i:s" ,$dataKoteiArray[1])['error_count'] > 0) {
						//if (strptime($dataKoteiArray[1],'%Y-%m-%d %T' ) == false) {

							$message = vsprintf($this->MMessage->getOneMessage('WPOE040012'), array($gyoCount ,$koteiCount));
							$this->errors['Check'][] =  $message;
							$errorCount++;
							$koteiErrorFlg = 1;
							break;
						}

						//時間チェック
						if (strtotime($timestamp) < strtotime($dataKoteiArray[1])) {

							$message = vsprintf($this->MMessage->getOneMessage('WPOE040032'), array($gyoCount ,$koteiCount,"工程開始時間"));
							$this->errors['Check'][] = $message;
							$errorCount++;
							$koteiErrorFlg = 1;
							break;
						}

						//工程開始時刻は、就業開始時刻より後でなければならない
						if (strtotime($dataArray[2]) > strtotime($dataKoteiArray[1])) {

							$message = vsprintf($this->MMessage->getOneMessage('WPOE040021'), array($gyoCount ,$koteiCount));
							$this->errors['Check'][] =  $message;
							$errorCount++;
							$koteiErrorFlg = 1;
							break;
						}

						//就業終了時刻がある場合
						if ($dataArray[3] != "") {

							//工程開始時刻は、工程終了時刻より前でなければならない
							if (strtotime($dataArray[3]) < strtotime($dataKoteiArray[1])) {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE040023'), array($gyoCount ,$koteiCount));
								$this->errors['Check'][] =  $message;
								$errorCount++;
								$koteiErrorFlg = 1;
								break;
							}

						}

						//工程終了時刻がある場合
						if ($dataKoteiArray[2] != "") {

							//工程終了時刻が入力されていない
							if ($dataKoteiArray[2] == "") {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE040025'), array($gyoCount ,$koteiCount));
								$this->errors['Check'][] =  $message;
								$errorCount++;
								$koteiErrorFlg = 1;
								break;
							}

							//工程終了時刻のフォーマットが不正
							if (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/",$dataKoteiArray[2]) == false) {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE040013'), array($gyoCount ,$koteiCount));
								$this->errors['Check'][] =  $message;
								$errorCount++;
								$koteiErrorFlg = 1;
								break;
							}

							//工程終了時刻が不正
							if (date_parse_from_format ("Y-m-d H:i:s" ,$dataKoteiArray[2])['error_count'] > 0) {
							//if (strptime($dataKoteiArray[2],'%Y-%m-%d %T' ) == false) {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE040014'), array($gyoCount ,$koteiCount));
								$this->errors['Check'][] =  $message;
								$errorCount++;
								$koteiErrorFlg = 1;
								break;
							}

							//時間チェック
							if (strtotime($timestamp) < strtotime($dataKoteiArray[2])) {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE040032'), array($gyoCount ,$koteiCount,"工程終了時間"));
								$this->errors['Check'][] = $message;
								$errorCount++;
								$koteiErrorFlg = 1;
								break;
							}

							//工程終了時刻は、工程開始時刻より後でなければならない
							if (strtotime($dataKoteiArray[2]) < strtotime($dataKoteiArray[1])) {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE040015'), array($gyoCount ,$koteiCount));
								$this->errors['Check'][] =  $message;
								$errorCount++;
								$koteiErrorFlg = 1;
								break;
							}

							//工程終了時刻は、就業開始時刻より後でなければならない
							if (strtotime($dataArray[2]) > strtotime($dataKoteiArray[2])) {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE040022'), array($gyoCount ,$koteiCount));
								$this->errors['Check'][] =  $message;
								$errorCount++;
								$koteiErrorFlg = 1;
								break;
							}

							//就業終了時刻がある場合
							if ($dataArray[3] != "") {

								//工程終了時刻は、工程狩猟時刻より前でなければならない
								if (strtotime($dataArray[3]) < strtotime($dataKoteiArray[2])) {

									$message = vsprintf($this->MMessage->getOneMessage('WPOE040024'), array($gyoCount ,$koteiCount));
									$this->errors['Check'][] =  $message;
									$errorCount++;
									$koteiErrorFlg = 1;
									break;
								}
							}

						//工程が未完了の場合
						} else {
							$koteiCyuCount++;

							//工程未完了は一個でなければならない
							if ($koteiCyuCount > 1) {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE040016'), array($gyoCount ,$koteiCyuCount));
								$this->errors['Check'][] =  $message;
								$errorCount++;
								$koteiErrorFlg = 1;
								break;
							}
						}

					}

					//工程でエラーが起こった場合、CONTINUE
					if ($koteiErrorFlg == 1) {
						continue;
					}

					//工程時間の時間範囲がかぶってないかチェック
					$koteiCount = 0;

					//一個ずつ比較していく（総当たり）
					foreach($koteisArray as $obj2){

						//データを取り出す
						$cellsKoteiArrayBase = $obj2->cells;
						$dataKoteiArrayBase = $obj2->data;
						$koteiCount++;

						$dt_kotei_st_base = $dataKoteiArrayBase[1];
						$dt_kotei_ed_base = $dataKoteiArrayBase[2];

						//工程データがない場合
						if ($cellsKoteiArrayBase[0] == "" &&
							$cellsKoteiArrayBase[1] == "" &&
							$cellsKoteiArrayBase[2] == "" ) {

							continue;
						}

						//工程データが完了している場合
						if ($dt_kotei_ed_base != "") {

							//baseとの総当たり比較
							$koteiCount2 = 0;
							foreach($koteisArray as $obj3){

								//データを取り出す
								$cellsKoteiArray = $obj3->cells;
								$dataKoteiArray = $obj3->data;
								$koteiCount2++;

								$dt_kotei_st = $dataKoteiArray[1];
								$dt_kotei_ed = $dataKoteiArray[2];

								//データがない場合
								if ($cellsKoteiArray[0] == "" &&
									$cellsKoteiArray[1] == "" &&
									$cellsKoteiArray[2] == "" ) {

									continue;
								}

								//同じオブジェクトの場合
								if ($obj2 === $obj3) {

									continue;
								}

								//工程が未完の場合
								if ($dt_kotei_ed == "") {
									continue;
								}

								//工程が被っているかチェック
								if ((strtotime($dt_kotei_st) < strtotime($dt_kotei_st_base) &&
									 strtotime($dt_kotei_st_base) < strtotime($dt_kotei_ed)) ||
									(strtotime($dt_kotei_st) < strtotime($dt_kotei_ed_base) &&
									 strtotime($dt_kotei_ed_base) < strtotime($dt_kotei_ed)) ||
									(strtotime($dt_kotei_st) > strtotime($dt_kotei_st_base) &&
									 strtotime($dt_kotei_ed_base) > strtotime($dt_kotei_ed))
									 ) {

									$message = vsprintf($this->MMessage->getOneMessage('WPOE040019'), array($gyoCount ,$koteiCount,$koteiCount2));
									$this->errors['Check'][] = $message;
									$errorCount++;
									$koteiErrorFlg = 1;
									break;
								}

							}

							if ($koteiErrorFlg == 1) {
								break;
							}

						//工程が未完了の場合（この時点で一つのみのはず）
						} else {

							//baseとの総当たり比較
							$koteiCount2 = 0;
							foreach($koteisArray as $obj3){

								//データの取り出し
								$cellsKoteiArray = $obj3->cells;
								$dataKoteiArray = $obj3->data;
								$koteiCount2++;

								$dt_kotei_st = $dataKoteiArray[1];
								$dt_kotei_ed = $dataKoteiArray[2];

								//データがない場合
								if ($cellsKoteiArray[0] == "" &&
									$cellsKoteiArray[1] == "" &&
									$cellsKoteiArray[2] == "" ) {

									continue;
								}

								//オブジェクトが重なった場合
								if ($obj2 === $obj3) {

									continue;
								}

								//工程が未完の場合
								if ($dt_kotei_ed == "") {
									continue;
								}

								//工程開始時間は最新でなければならない
								if (strtotime($dt_kotei_st_base) < strtotime($dt_kotei_ed)) {

									$message = vsprintf($this->MMessage->getOneMessage('WPOE040019'), array($gyoCount ,$koteiCount));
									$this->errors['Check'][] = $message;
									$errorCount++;
									$koteiErrorFlg = 1;
									break;
								}

							}

							if ($koteiErrorFlg == 1) {
								break;
							}
						}
					}

					//工程でエラーが起こった場合、CONTINUE
					if ($koteiErrorFlg == 1) {
						continue;
					}

				//削除の場合
				}  else if($dataArray[4] != 0) {

					$komokuCount++;
				}

			}

			if ($errorCount > 0) {

				return false;
			}

			//データがない場合
			if ($komokuCount == 0) {

				$message = $this->MMessage->getOneMessage('CMNE000101');
				$this->errors['Check'][] = $message;
				return false;
			}


			/********** 配列相関チェックFASE *************/
			$gyoCount = 0;

			foreach($data as $obj){

				$gyoCount++;

				//データを抜き出す
				$cellsArray = $obj->cells;
				$dataArray = $obj->data;
				$koteisArray = $obj->koteis;

				$staff_cd = $dataArray[0];
				$ymd_syugyo = $dataArray[1];
				$dt_syugyo_st =  $dataArray[2];
				$dt_syugyo_ed =  $dataArray[3];

				//更新・新規のみ
				if ($dataArray[4] != 2 && $dataArray[4] != 1 ) {

					continue;
				}

				//就業終了時刻が入ってないレコードのカウント
				$shugyoEdNoCount = 0;

				//ベース対象がもし入ってない場合、カウントアップ
				if ($dt_syugyo_ed == "") {

					$shugyoEdNoCount++;
				}

				$koteiErrorFlg = 0;

				//総当たりチェックをかける
				foreach($data as $obj2){

					//同じオブジェクトはスキップ
					if ($obj2 === $obj) {
						continue;
					}

					$cellsArray2 = $obj2->cells;
					$dataArray2 = $obj2->data;
					$koteisArray2 = $obj2->koteis;

					$staff_cd2 = $dataArray2[0];
					$ymd_syugyo2 = $dataArray2[1];
					$dt_syugyo_st2 =  $dataArray2[2];
					$dt_syugyo_ed2 =  $dataArray2[3];

					//新規・更新のみ
					if ($dataArray2[4] != 2 && $dataArray2[4] != 1 ) {

						continue;
					}

					//配列内で、プライマリが被っていないかチェック
					if ($staff_cd2 == $staff_cd &&
						$ymd_syugyo2 == $ymd_syugyo) {

						$message = vsprintf($this->MMessage->getOneMessage('WPOE040028'), array($gyoCount ));
						$this->errors['Check'][] =  $message;
						$errorCount++;
						$koteiErrorFlg = 1;
						break;

					}

					//同じスタッフCDで、就業終了日付がない場合、カウントアップ
					if ($staff_cd2 == $staff_cd &&
						 $dt_syugyo_ed2 == "") {

						$shugyoEdNoCount++;
					}

					//同じスタッフCDで、就業終了日付がないのが二つ以上ないかチェック
					if ($shugyoEdNoCount > 1) {

						$message = vsprintf($this->MMessage->getOneMessage('WPOE040029'), array($gyoCount ));
						$this->errors['Check'][] =  $message;
						$errorCount++;
						$koteiErrorFlg = 1;
						break;

					}

					//同じスタッフCDで、日付が被っていないかチェック
					//ベースの就業終了日付がない場合
					if ($dt_syugyo_ed == "" && $staff_cd == $staff_cd2) {

						//ベースの就業日付がない場合、他のは就業終了日付がある（ない場合は上でカウントアップされてエラー)
						if ($dt_syugyo_ed2  != "") {

							//ベースの就業日付がない場合、一番遅い時間でなくてはならない
							if ((strtotime($dt_syugyo_st) < strtotime($dt_syugyo_ed2)) ||
							    (strtotime($dt_syugyo_st) < strtotime($dt_syugyo_st2))) {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE040027'), array($gyoCount ));
								$this->errors['Check'][] =  $message;
								$errorCount++;
								$koteiErrorFlg = 1;
								break;

							}

						}
					//ベースの就業終了日付がある場合
					}  else if ($dt_syugyo_ed != "" && $staff_cd == $staff_cd2) {

						//対象が就業終了がある場合
						if ($dt_syugyo_ed2  != "") {

							if (((strtotime($dt_syugyo_st) < strtotime($dt_syugyo_ed2)) &&
							    (strtotime($dt_syugyo_st) > strtotime($dt_syugyo_st2))) ||
							    ((strtotime($dt_syugyo_ed) < strtotime($dt_syugyo_ed2)) &&
							    (strtotime($dt_syugyo_ed) > strtotime($dt_syugyo_st2)))) {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE040027'), array($gyoCount ));
								$this->errors['Check'][] =  $message;
								$errorCount++;
								$koteiErrorFlg = 1;
								break;

							}

						//対象に就業終了がない場合
						} else {

							if ((strtotime($dt_syugyo_st) > strtotime($dt_syugyo_st2)) ||
							    (strtotime($dt_syugyo_ed) > strtotime($dt_syugyo_st2))) {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE040027'), array($gyoCount ));
								$this->errors['Check'][] =  $message;
								$errorCount++;
								$koteiErrorFlg = 1;
								break;

							}

						}

					}
				}

				if ($koteiErrorFlg == 1) {
					continue;
				}

			}

			if ($errorCount > 0) {

				return false;
			}

			/********** DB相関チェックFASE *************/
			$gyoCount = 0;

			foreach($data as $obj){

				$gyoCount++;

				//データを抜き出す
				$cellsArray = $obj->cells;
				$dataArray = $obj->data;
				$koteisArray = $obj->koteis;


				//行が更新データの場合
				if ($dataArray[4] == 1 ) {

					//存在チェック
					//T_KOTEI_Hが存在していなければならない。
					$sql  = 'SELECT ';
					$sql .= '  COUNT(*) AS COUNT';
					$sql .= ' FROM V_WPO040_KOTEI ';
					$sql .= ' WHERE ';
					$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
					$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
					$sql .= "  YMD_SYUGYO = '" . $dataArray[1] . "' AND" ;
					$sql .= "  STAFF_CD = '" . $dataArray[0] . "'";

					$this->queryWithPDOLog($stmt,$pdo,$sql,"工程ヘッダ　存在チェック");

					$result = $stmt->fetch(PDO::FETCH_ASSOC);
					$countKOTEI = $result['COUNT'];

					//存在してない場合エラー
					if ($countKOTEI == 0) {

						$message = vsprintf($this->MMessage->getOneMessage('WPOE040017'), array($gyoCount ));
						$this->errors['Check'][] =  $message;
						$errorCount++;
						continue;
					}

					//就業終了時刻が入力されている場合
					if ($dataArray[3] != "") {

						//別のプライマリと被っていない
						$sql  = 'SELECT ';
						$sql .= '  YMD_SYUGYO';
						$sql .= ' FROM';
						$sql .= '  T_KOTEI_H';
						$sql .= ' WHERE ';
						$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
						$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
						$sql .= "  STAFF_CD = '" . $dataArray[0] . "' AND";
						$sql .= "  YMD_SYUGYO <> '" . $dataArray[1] . "' AND";
						$sql .= "  ((DT_SYUGYO_ST<'" . $dataArray[2] . "' AND";
						$sql .= "  DT_SYUGYO_ED>'" . $dataArray[2] . "' ) OR";
						$sql .= "  (DT_SYUGYO_ST<'" . $dataArray[3] . "' AND";
						$sql .= "  DT_SYUGYO_ED>'" . $dataArray[3] . "'))";

						$this->queryWithPDOLog($stmt,$pdo,$sql,"工程ヘッダ　重複チェック");

						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$ymd_syugyo = $result['YMD_SYUGYO'];


						//工程ヘッダが存在している場合
						if ($ymd_syugyo != "") {

							$year = substr($ymd_syugyo, 0,4);
							$month = substr($ymd_syugyo, 5,2);
							$day = substr($ymd_syugyo, 8,2);

							$ymd_syugyo_str = $year . "年" . $month . "月" . $day . "日";
							$message = vsprintf($this->MMessage->getOneMessage('WPOE040030'), array($gyoCount ,$ymd_syugyo_str));
							$this->errors['Check'][] = $message;
							$errorCount++;
							continue;
						}

						//別のプライマリと被っていない
						$sql  = 'SELECT ';
						$sql .= '  YMD_SYUGYO';
						$sql .= ' FROM';
						$sql .= '  T_KOTEI_H';
						$sql .= ' WHERE ';
						$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
						$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
						$sql .= "  YMD_SYUGYO <> '" . $dataArray[1] . "' AND";
						$sql .= "  STAFF_CD = '" . $dataArray[0] . "' AND";
						$sql .= "  (DT_SYUGYO_ST<'" . $dataArray[2] . "' OR";
						$sql .= "  DT_SYUGYO_ST<'" . $dataArray[3] . "' ) AND";
						$sql .= "  DT_SYUGYO_ED IS NULL";

						$this->queryWithPDOLog($stmt,$pdo,$sql,"工程ヘッダ　重複チェック");

						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$ymd_syugyo = $result['YMD_SYUGYO'];


						//工程ヘッダが存在している場合
						if ($ymd_syugyo != "") {

							$year = substr($ymd_syugyo, 0,4);
							$month = substr($ymd_syugyo, 5,2);
							$day = substr($ymd_syugyo, 8,2);

							$ymd_syugyo_str = $year . "年" . $month . "月" . $day . "日";
							$message = vsprintf($this->MMessage->getOneMessage('WPOE040030'), array($gyoCount ,$ymd_syugyo_str));
							$this->errors['Check'][] = $message;
							$errorCount++;
							continue;
						}


					//就業終了がない場合
					} else {

						//就業終了してないのが他にもある場合
						$sql  = 'SELECT ';
						$sql .= '  YMD_SYUGYO';
						$sql .= ' FROM';
						$sql .= '  T_KOTEI_H';
						$sql .= ' WHERE ';
						$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
						$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
						$sql .= "  STAFF_CD = '" . $dataArray[0] . "' AND";
						$sql .= "  YMD_SYUGYO <> '" . $dataArray[1] . "' AND";
						$sql .= "  DT_SYUGYO_ED IS NULL";

						$this->queryWithPDOLog($stmt,$pdo,$sql,"工程ヘッダ　未完了チェック");

						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$ymd_syugyo = $result['YMD_SYUGYO'];


						//工程ヘッダが存在している場合
						if ($ymd_syugyo != "") {

							$year = substr($ymd_syugyo, 0,4);
							$month = substr($ymd_syugyo, 5,2);
							$day = substr($ymd_syugyo, 8,2);

							$ymd_syugyo_str = $year . "年" . $month . "月" . $day . "日";
							$message = vsprintf($this->MMessage->getOneMessage('WPOE040026'), array($gyoCount ,$ymd_syugyo_str));
							$this->errors['Check'][] = $message;
							$errorCount++;
							continue;
						}

						//別のプライマリと被っていない
						$sql  = 'SELECT ';
						$sql .= '  YMD_SYUGYO';
						$sql .= ' FROM';
						$sql .= '  T_KOTEI_H';
						$sql .= ' WHERE ';
						$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
						$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
						$sql .= "  YMD_SYUGYO <> '" . $dataArray[1] . "' AND";
						$sql .= "  STAFF_CD = '" . $dataArray[0] . "' AND";
						$sql .= "  DT_SYUGYO_ED>'" . $dataArray[2] . "'";

						$this->queryWithPDOLog($stmt,$pdo,$sql,"工程ヘッダ　重複チェック");

						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$ymd_syugyo = $result['YMD_SYUGYO'];


						//工程ヘッダが存在している場合
						if ($ymd_syugyo != "") {

							$year = substr($ymd_syugyo, 0,4);
							$month = substr($ymd_syugyo, 5,2);
							$day = substr($ymd_syugyo, 8,2);

							$ymd_syugyo_str = $year . "年" . $month . "月" . $day . "日";
							$message = vsprintf($this->MMessage->getOneMessage('WPOE040030'), array($gyoCount ,$ymd_syugyo_str));
							$this->errors['Check'][] = $message;
							$errorCount++;
							continue;
						}

					}


				//新規の場合
				} else if($dataArray[4] == 2) {


					//存在チェック
					//T_KOTEI_Hが存在してはならない
					$sql  = 'SELECT ';
					$sql .= '  COUNT(*) AS COUNT';
					$sql .= ' FROM V_WPO040_KOTEI ';
					$sql .= ' WHERE ';
					$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
					$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
					$sql .= "  YMD_SYUGYO = '" . $dataArray[1] . "' AND" ;
					$sql .= "  STAFF_CD = '" . $dataArray[0] . "'";

					$this->queryWithPDOLog($stmt,$pdo,$sql,"工程ヘッダ　存在チェック");

					$result = $stmt->fetch(PDO::FETCH_ASSOC);
					$countKOTEI = $result['COUNT'];

					//工程ヘッダが存在している場合
					if ($countKOTEI != 0) {

						$message = vsprintf($this->MMessage->getOneMessage('WPOE040018'), array($gyoCount ));
						$this->errors['Check'][] = $message;
						$errorCount++;
						continue;
					}

					//就業終了時刻がある場合
					if ($dataArray[3] != "") {


						//別のプライマリと被っていない
						$sql  = 'SELECT ';
						$sql .= '  YMD_SYUGYO';
						$sql .= ' FROM';
						$sql .= '  T_KOTEI_H';
						$sql .= ' WHERE ';
						$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
						$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
						$sql .= "  STAFF_CD = '" . $dataArray[0] . "' AND";
						$sql .= "  ((DT_SYUGYO_ST<'" . $dataArray[2] . "' AND";
						$sql .= "  DT_SYUGYO_ED>'" . $dataArray[2] . "' ) OR";
						$sql .= "  (DT_SYUGYO_ST<'" . $dataArray[3] . "' AND";
						$sql .= "  DT_SYUGYO_ED>'" . $dataArray[3] . "'))";

						$this->queryWithPDOLog($stmt,$pdo,$sql,"工程ヘッダ　重複チェック");

						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$ymd_syugyo = $result['YMD_SYUGYO'];


						//工程ヘッダが存在している場合
						if ($ymd_syugyo != "") {

							$year = substr($ymd_syugyo, 0,4);
							$month = substr($ymd_syugyo, 5,2);
							$day = substr($ymd_syugyo, 8,2);

							$ymd_syugyo_str = $year . "年" . $month . "月" . $day . "日";
							$message = vsprintf($this->MMessage->getOneMessage('WPOE040030'), array($gyoCount ,$ymd_syugyo_str));
							$this->errors['Check'][] = $message;
							$errorCount++;
							continue;
						}

						//別のプライマリと被っていない(就業終了していない場合)
						$sql  = 'SELECT ';
						$sql .= '  YMD_SYUGYO';
						$sql .= ' FROM';
						$sql .= '  T_KOTEI_H';
						$sql .= ' WHERE ';
						$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
						$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
						$sql .= "  STAFF_CD = '" . $dataArray[0] . "' AND";
						$sql .= "  (DT_SYUGYO_ST<'" . $dataArray[2] . "' OR";
						$sql .= "  DT_SYUGYO_ST<'" . $dataArray[3] . "' ) AND";
						$sql .= "  DT_SYUGYO_ED IS NULL";

						$this->queryWithPDOLog($stmt,$pdo,$sql,"工程ヘッダ　重複チェック");

						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$ymd_syugyo = $result['YMD_SYUGYO'];


						//工程ヘッダが存在している場合
						if ($ymd_syugyo != "") {

							$year = substr($ymd_syugyo, 0,4);
							$month = substr($ymd_syugyo, 5,2);
							$day = substr($ymd_syugyo, 8,2);

							$ymd_syugyo_str = $year . "年" . $month . "月" . $day . "日";
							$message = vsprintf($this->MMessage->getOneMessage('WPOE040030'), array($gyoCount ,$ymd_syugyo_str));
							$this->errors['Check'][] = $message;
							$errorCount++;
							continue;
						}


					//就業終了がない場合
					} else {

						//就業終了してないのが他にもある場合
						$sql  = 'SELECT ';
						$sql .= '  YMD_SYUGYO';
						$sql .= ' FROM';
						$sql .= '  T_KOTEI_H';
						$sql .= ' WHERE ';
						$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
						$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
						$sql .= "  STAFF_CD = '" . $dataArray[0] . "' AND";
						$sql .= "  DT_SYUGYO_ED IS NULL";

						$this->queryWithPDOLog($stmt,$pdo,$sql,"工程ヘッダ　未完了チェック");

						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$ymd_syugyo = $result['YMD_SYUGYO'];


						//工程ヘッダが存在している場合
						if ($ymd_syugyo != "") {

							$year = substr($ymd_syugyo, 0,4);
							$month = substr($ymd_syugyo, 5,2);
							$day = substr($ymd_syugyo, 8,2);

							$ymd_syugyo_str = $year . "年" . $month . "月" . $day . "日";
							$message = vsprintf($this->MMessage->getOneMessage('WPOE040026'), array($gyoCount ,$ymd_syugyo_str));
							$this->errors['Check'][] = $message;
							return;
						}

						//別のプライマリと被っていない（就業は最新のデータである）
						$sql  = 'SELECT ';
						$sql .= '  YMD_SYUGYO';
						$sql .= ' FROM';
						$sql .= '  T_KOTEI_H';
						$sql .= ' WHERE ';
						$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
						$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
						$sql .= "  STAFF_CD = '" . $dataArray[0] . "' AND";
						$sql .= "  DT_SYUGYO_ED>'" . $dataArray[2] . "'";

						$this->queryWithPDOLog($stmt,$pdo,$sql,"工程ヘッダ　重複チェック");

						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$ymd_syugyo = $result['YMD_SYUGYO'];


						//工程ヘッダが存在している場合
						if ($ymd_syugyo != "") {

							$year = substr($ymd_syugyo, 0,4);
							$month = substr($ymd_syugyo, 5,2);
							$day = substr($ymd_syugyo, 8,2);

							$ymd_syugyo_str = $year . "年" . $month . "月" . $day . "日";
							$message = vsprintf($this->MMessage->getOneMessage('WPOE040030'), array($gyoCount ,$ymd_syugyo_str));
							$this->errors['Check'][] = $message;
							$errorCount++;
							continue;
						}

					}

				}
			}

			if ($errorCount > 0) {

				return false;
			}

			$pdo = null;


			return true;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO040", $e->getMessage());
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