<?php
/**
 * WPO060Model
 *
 * 工程一括更新
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class WPO060Model extends DCMSAppModel{

	public $name = 'WPO060Model';
	public $useTable = false;

	public $messages = array();
	public $errors = array();
	public $successes = array();

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
	public function getKoteiList($ymd = '', $timeFrom ='',$timeTo ='', $haken = '', $keiyaku = '', $yakushoku = '', $staff_cd = '', $staff_nm = '') {

		$pdo = null;

		try{

			$today = $this->getToday();

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			//工程データの取得
			$sql  = "SELECT ";
			$sql .= "     A.NINUSI_CD AS NINUSI_CD";
			$sql .= "   , A.SOSIKI_CD AS SOSIKI_CD";
			$sql .= "   , A.YMD_SYUGYO AS YMD_SYUGYO";
			$sql .= "   , A.STAFF_CD AS STAFF_CD";
			$sql .= "   , B.STAFF_NM AS STAFF_NM";
			$sql .= "   , E.KAISYA_NM AS KAISYA_NM";
			$sql .= "   , C.MEI_1 AS YAKUSHOKU";
			$sql .= "   , D.MEI_1 AS KEIYAKU";
			$sql .= "   , A.DT_SYUGYO_ST AS DT_SYUGYO_ST";
			$sql .= "   , A.DT_SYUGYO_ED AS DT_SYUGYO_ED";
			$sql .= " FROM";
			$sql .= "  T_KOTEI_H A ";
			$sql .= "  LEFT JOIN M_STAFF_KIHON B";
			$sql .= "    ON A.STAFF_CD = B.STAFF_CD";
			$sql .= "  LEFT JOIN M_MEISYO C";
			$sql .= "    ON A.NINUSI_CD   = C.NINUSI_CD ";
			$sql .= "    AND A.SOSIKI_CD  = C.SOSIKI_CD ";
			$sql .= "    AND C.MEI_KBN  = '17' ";
			$sql .= "    AND C.MEI_CD  = B.KBN_PST ";
			$sql .= "  LEFT JOIN M_MEISYO D";
			$sql .= "    ON A.NINUSI_CD   = D.NINUSI_CD ";
			$sql .= "    AND A.SOSIKI_CD  = D.SOSIKI_CD ";
			$sql .= "    AND D.MEI_KBN  = '30' ";
			$sql .= "    AND D.MEI_CD  = B.KBN_KEIYAKU ";
			$sql .= "  LEFT JOIN M_HAKEN_KAISYA E";
			$sql .= "    ON A.NINUSI_CD   = E.NINUSI_CD ";
			$sql .= "    AND A.SOSIKI_CD  = E.SOSIKI_CD ";
			$sql .= "    AND E.KAISYA_CD  = B.HAKEN_KAISYA_CD ";
			$sql .= ' WHERE ';
			$sql .= "  A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  A.SOSIKI_CD='" . $this->_sosiki_cd . "'";

			//就業日付
			if ($ymd != ''){
				$sql .= " AND A.YMD_SYUGYO = '" . $ymd . "'" ;
			}

			if ($timeFrom != ''){
				$sql .= " AND A.DT_SYUGYO_ST >= ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$timeFrom.":00' )" ;
			}

			if ($timeTo != ''){
				$sql .= " AND A.DT_SYUGYO_ED <= ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$timeTo.":00' )" ;
			}

			if ($haken != ''){
				$sql .= " AND B.HAKEN_KAISYA_CD = '" . $haken . "'" ;
			}

			if ($keiyaku != ''){
				$sql .= " AND B.KBN_KEIYAKU = '" . $keiyaku . "'" ;
			}

			if ($yakushoku != ''){
				$sql .= " AND B.KBN_PST = '" . $yakushoku . "'" ;
			}

			if ($staff_cd != ''){
				$sql .= " AND A.STAFF_CD LIKE " . $pdo->quote('%'. htmlspecialchars($staff_cd) . '%') . "" ;
			}

			if ($staff_nm != ''){
				$sql .= " AND B.STAFF_NM LIKE " . $pdo->quote('%'. htmlspecialchars($staff_nm) . '%') . "" ;
			}

			$sql .= "    GROUP BY";
			$sql .= "      A.NINUSI_CD,";
			$sql .= "      A.SOSIKI_CD,";
			$sql .= "      A.YMD_SYUGYO,";
			$sql .= "      A.STAFF_CD";
			$sql .= "    ORDER BY";
			$sql .= "      A.NINUSI_CD,";
			$sql .= "      A.SOSIKI_CD,";
			$sql .= "      A.YMD_SYUGYO,";
			$sql .= "      A.STAFF_CD";
			$sql .= ";";


			$this->queryWithPDOLog($stmt,$pdo,$sql,"工程ヘッダ&工程 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
				    "NINUSI_CD" => $result['NINUSI_CD'],
					"SOSIKI_CD" => $result['SOSIKI_CD'],
					"YMD_SYUGYO" => $result['YMD_SYUGYO'],
					"STAFF_CD" => $result['STAFF_CD'],
					"STAFF_NM" => $result['STAFF_NM'],
					"KAISYA_NM" => $result['KAISYA_NM'],
					"YAKUSHOKU" => $result['YAKUSHOKU'],
					"KEIYAKU" => $result['KEIYAKU'],
					"DT_SYUGYO_ST" => $result['DT_SYUGYO_ST'],
					"DT_SYUGYO_ED" => $result['DT_SYUGYO_ED']

				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO060", $e->getMessage());
			$pdo = null;
		}


		return array();
	}

	/**
	 * 工程予測データ更新処理
	 * @access   public
	 * @param    工程データ
	 * @return   成否情報(true,false)
	 */
	function setKotei($data,$kotei,$timeFrom,$timeTo,$staff_cd_session) {
		$pdo = null;
		$pdo2 = null;
		$return_cd = "0";
		$koteis = explode("_", $kotei);
		$bnri_dai_cd = $koteis[0];
		$bnri_cyu_cd = $koteis[1];
		$bnri_sai_cd = $koteis[2];

		$error_code = 0;

		try {


			//排他制御オン
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd_session . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WPO',";
			$sql .= "'060',";
			$sql .= "1";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'WPO060 排他制御オン');

			try {

				$pdo2 = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
				$pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$pdo2->beginTransaction();

				//行ずつ処理
				foreach($data as $obj){

					$cellsArray = $obj->cells;
					$dataArray = $obj->data;

					$ymd_shugyo = $dataArray[0];
					$staff_cd = $dataArray[1];
					$staff_nm = $cellsArray[3];

					if ($cellsArray[0] != 'on') {
						continue;
					}

					//就業時間内か
					$sql  = "SELECT ";
					$sql .= "     COUNT(*) AS COUNT";
					$sql .= " FROM";
					$sql .= "  T_KOTEI_H";
					$sql .= ' WHERE ';
					$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
					$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
					$sql .= "  YMD_SYUGYO='" . $ymd_shugyo . "' AND" ;
					$sql .= "  STAFF_CD='" . $staff_cd . "' AND" ;
					$sql .= "  DATE_FORMAT( DT_SYUGYO_ST, '%Y-%m-%d %H:%i:00' ) <= ADDTIME(CONCAT(YMD_SYUGYO,' 00:00:00'),'".$timeFrom.":00' ) AND" ;
					$sql .= "  ( " ;
					$sql .= "    ADDTIME(CONCAT(YMD_SYUGYO,' 00:00:00'),'".$timeTo.":00' ) <= DATE_FORMAT( DT_SYUGYO_ED, '%Y-%m-%d %H:%i:00' ) " ;
					$sql .= "    OR " ;
					$sql .= "    DT_SYUGYO_ED IS NULL " ;
					$sql .= "  ) " ;

					$this->queryWithPDOLog($stmt2,$pdo2,$sql, '就業確認');

					$result = $stmt2->fetch(PDO::FETCH_ASSOC);

					if ($result['COUNT'] == "0") {

						$message = vsprintf($this->MMessage->getOneMessage('WPOE060001'), array($ymd_shugyo,$staff_cd . ":" . $staff_nm));
						$this->errors[] =  $message;
						continue;
					}


					//工程時間外またがりか
					$sql  = "SELECT ";
					$sql .= "     COUNT(*) AS COUNT";
					$sql .= " FROM";
					$sql .= "  T_KOTEI_D A ";
					$sql .= "  INNER JOIN T_KOTEI_H B ";
					$sql .= "    ON A.NINUSI_CD   = B.NINUSI_CD ";
					$sql .= "    AND A.SOSIKI_CD  = B.SOSIKI_CD ";
					$sql .= "    AND A.STAFF_CD   = B.STAFF_CD ";
					$sql .= "    AND A.YMD_SYUGYO = B.YMD_SYUGYO";
					$sql .= ' WHERE ';
					$sql .= "  A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
					$sql .= "  A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
					$sql .= "  A.YMD_SYUGYO = '" . $ymd_shugyo . "' AND" ;
					$sql .= "  A.STAFF_CD='" . $staff_cd . "' AND" ;
					$sql .= "  ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$timeFrom.":00' ) <= DATE_FORMAT( A.DT_KOTEI_ST, '%Y-%m-%d %H:%i:00' ) ";
					$sql .= "  AND " ;
					$sql .= "  DATE_FORMAT( A.DT_KOTEI_ED, '%Y-%m-%d %H:%i:00' ) <= ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$timeTo.":00' )";
					$sql .= "  AND " ;
					$sql .= "  A.DT_KOTEI_ED IS NOT NULL " ;

					$this->queryWithPDOLog($stmt2,$pdo2,$sql, '工程時間外またがり確認');

					$result = $stmt2->fetch(PDO::FETCH_ASSOC);

					if ($result['COUNT'] != "0") {

						$message = vsprintf($this->MMessage->getOneMessage('WPOE060002'), array($ymd_shugyo,$staff_cd . ":" . $staff_nm));
						$this->errors[] =  $message;
						continue;
					}


					//工程時間内またがりか
					$sql  = "SELECT ";
					$sql .= "      A.SOSIKI_CD AS SOSIKI_CD";
					$sql .= "    , A.NINUSI_CD AS NINUSI_CD";
					$sql .= "    , A.STAFF_CD AS STAFF_CD";
					$sql .= "    , A.YMD_SYUGYO AS YMD_SYUGYO";
					$sql .= "    , A.KOTEI_NO AS KOTEI_NO";
					$sql .= "    , A.BNRI_DAI_CD AS BNRI_DAI_CD";
					$sql .= "    , A.BNRI_CYU_CD AS BNRI_CYU_CD";
					$sql .= "    , A.BNRI_SAI_CD AS BNRI_SAI_CD";
					$sql .= "    , A.DT_KOTEI_ED AS DT_KOTEI_ED";
					$sql .= " FROM";
					$sql .= "  T_KOTEI_D A ";
					$sql .= "  INNER JOIN T_KOTEI_H B ";
					$sql .= "    ON A.NINUSI_CD   = B.NINUSI_CD ";
					$sql .= "    AND A.SOSIKI_CD  = B.SOSIKI_CD ";
					$sql .= "    AND A.STAFF_CD   = B.STAFF_CD ";
					$sql .= "    AND A.YMD_SYUGYO = B.YMD_SYUGYO";
					$sql .= ' WHERE ';
					$sql .= "  A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
					$sql .= "  A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
					$sql .= "  A.YMD_SYUGYO='" . $ymd_shugyo . "' AND" ;
					$sql .= "  A.STAFF_CD='" . $staff_cd . "' AND" ;
					$sql .= "  ( " ;
					$sql .= "    ( " ;
					$sql .= "      DATE_FORMAT( A.DT_KOTEI_ST, '%Y-%m-%d %H:%i:00' ) < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$timeFrom.":00' )";
					$sql .= "      AND " ;
					$sql .= "      ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$timeTo.":00' ) < DATE_FORMAT( A.DT_KOTEI_ED, '%Y-%m-%d %H:%i:00' )";
					$sql .= "      AND " ;
					$sql .= "      A.DT_KOTEI_ED IS NOT NULL";
					$sql .= "    ) " ;
					$sql .= "    OR " ;
					$sql .= "    ( " ;
					$sql .= "      DATE_FORMAT( A.DT_KOTEI_ST, '%Y-%m-%d %H:%i:00' ) < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$timeFrom.":00' )";
					$sql .= "      AND " ;
					$sql .= "      A.DT_KOTEI_ED IS NULL";
					$sql .= "    ) " ;
					$sql .= "  ) " ;

					$this->queryWithPDOLog($stmt2,$pdo2,$sql, '工程時間内またがり確認');

					$sotoKotei = '';

					if ($result = $stmt2->fetch(PDO::FETCH_ASSOC)) {

						$sotoKotei = array(
								"NINUSI_CD" => $result['NINUSI_CD'],
								"SOSIKI_CD" => $result['SOSIKI_CD'],
								"STAFF_CD" => $result['STAFF_CD'],
								"YMD_SYUGYO" => $result['YMD_SYUGYO'],
								"KOTEI_NO" => $result['KOTEI_NO'],
								"BNRI_DAI_CD" => $result['BNRI_DAI_CD'],
								"BNRI_CYU_CD" => $result['BNRI_CYU_CD'],
								"BNRI_SAI_CD" => $result['BNRI_SAI_CD'],
								"DT_KOTEI_ED" => $result['DT_KOTEI_ED']

						);
					}

					//工程内割り込みパターン
					if ($sotoKotei != '') {

						//工程が同じか確認
						if ($bnri_dai_cd == $sotoKotei['BNRI_DAI_CD'] && $bnri_cyu_cd == $sotoKotei['BNRI_CYU_CD'] && $bnri_sai_cd == $sotoKotei['BNRI_SAI_CD'] ) {

							$message = vsprintf($this->MMessage->getOneMessage('WPOE060006'), array($ymd_shugyo,$staff_cd . ":" . $staff_nm));
							$this->errors[] =  $message;
							continue;
						}


						//既存工程を前工程へ更新
						$sql  = "UPDATE ";
						$sql .= "    T_KOTEI_D";
						$sql .= "  SET";
						$sql .= "   DT_KOTEI_ED=ADDTIME(CONCAT(YMD_SYUGYO,' 00:00:00'),'".$timeFrom.":00' )";
						$sql .= "  WHERE";
						$sql .= "    NINUSI_CD='" . $sotoKotei['NINUSI_CD'] . "' AND";
						$sql .= "    SOSIKI_CD='" . $sotoKotei['SOSIKI_CD'] . "' AND";
						$sql .= "    STAFF_CD='" . $sotoKotei['STAFF_CD'] . "' AND" ;
						$sql .= "    YMD_SYUGYO='" . $sotoKotei['YMD_SYUGYO'] . "' AND" ;
						$sql .= "    KOTEI_NO=" . $sotoKotei['KOTEI_NO'] . "" ;

						$this->execWithPDOLog($pdo2,$sql, '前工程　アップデート');

						//差込工程挿入
						$sql  = "INSERT ";
						$sql .= "  INTO";
						$sql .= "    T_KOTEI_D";
						$sql .= "  (";
						$sql .= "    NINUSI_CD,";
						$sql .= "    SOSIKI_CD,";
						$sql .= "    STAFF_CD,";
						$sql .= "    YMD_SYUGYO,";
						$sql .= "    KOTEI_NO,";
						$sql .= "    BNRI_DAI_CD,";
						$sql .= "    BNRI_CYU_CD,";
						$sql .= "    BNRI_SAI_CD,";
						$sql .= "    DT_KOTEI_ST,";
						$sql .= "    DT_KOTEI_ED,";
						$sql .= "    CREATED,";
						$sql .= "    CREATED_STAFF,";
						$sql .= "    MODIFIED,";
						$sql .= "    MODIFIED_STAFF";
						$sql .= "  ) VALUES (";
						$sql .= "   '" . $this->_ninusi_cd . "',";
						$sql .= "   '" . $this->_sosiki_cd . "',";
						$sql .= "   '" . $staff_cd . "',";
						$sql .= "   '" . $ymd_shugyo . "',";
						$sql .= "   500,";
						$sql .= "   '" . $bnri_dai_cd . "',";
						$sql .= "   '" . $bnri_cyu_cd . "',";
						$sql .= "   '" . $bnri_sai_cd . "',";
						$sql .= "   ADDTIME(CONCAT(YMD_SYUGYO,' 00:00:00'),'".$timeFrom.":00' ),";
						$sql .= "   ADDTIME(CONCAT(YMD_SYUGYO,' 00:00:00'),'".$timeTo.":00' ),";
						$sql .= "   NOW(),";
						$sql .= "   '" . $staff_cd_session . "',";
						$sql .= "   NOW(),";
						$sql .= "   '" . $staff_cd_session . "'";
						$sql .= "  )";

						$this->execWithPDOLog($pdo2,$sql, '工程追加');

						//後工程挿入
						$sql  = "INSERT ";
						$sql .= "  INTO";
						$sql .= "    T_KOTEI_D";
						$sql .= "  (";
						$sql .= "    NINUSI_CD,";
						$sql .= "    SOSIKI_CD,";
						$sql .= "    STAFF_CD,";
						$sql .= "    YMD_SYUGYO,";
						$sql .= "    KOTEI_NO,";
						$sql .= "    BNRI_DAI_CD,";
						$sql .= "    BNRI_CYU_CD,";
						$sql .= "    BNRI_SAI_CD,";
						$sql .= "    DT_KOTEI_ST,";
						$sql .= "    DT_KOTEI_ED,";
						$sql .= "    CREATED,";
						$sql .= "    CREATED_STAFF,";
						$sql .= "    MODIFIED,";
						$sql .= "    MODIFIED_STAFF";
						$sql .= "  ) VALUES (";
						$sql .= "   '" . $this->_ninusi_cd . "',";
						$sql .= "   '" . $this->_sosiki_cd . "',";
						$sql .= "   '" . $staff_cd . "',";
						$sql .= "   '" . $ymd_shugyo . "',";
						$sql .= "   501,";
						$sql .= "   '" . $sotoKotei['BNRI_DAI_CD'] . "',";
						$sql .= "   '" . $sotoKotei['BNRI_CYU_CD'] . "',";
						$sql .= "   '" . $sotoKotei['BNRI_SAI_CD'] . "',";
						$sql .= "   ADDTIME(CONCAT(YMD_SYUGYO,' 00:00:00'),'".$timeTo.":00' ),";
						$sql .= "   '" . $sotoKotei['DT_KOTEI_ED'] . "',";
						$sql .= "   NOW(),";
						$sql .= "   '" . $staff_cd_session . "',";
						$sql .= "   NOW(),";
						$sql .= "   '" . $staff_cd_session . "'";
						$sql .= "  )";

						$this->execWithPDOLog($pdo2,$sql, '工程追加');


					//工程外割り込みパターン
					} else {

						$maeFlag = 0; //0:無 1:重なりあり 2:重なり無し
						$maeKotei ='';

						//工程終了またがり
						$sql  = "SELECT ";
						$sql .= "      A.SOSIKI_CD AS SOSIKI_CD";
						$sql .= "    , A.NINUSI_CD AS NINUSI_CD";
						$sql .= "    , A.STAFF_CD AS STAFF_CD";
						$sql .= "    , A.YMD_SYUGYO AS YMD_SYUGYO";
						$sql .= "    , A.KOTEI_NO AS KOTEI_NO";
						$sql .= "    , A.BNRI_DAI_CD AS BNRI_DAI_CD";
						$sql .= "    , A.BNRI_CYU_CD AS BNRI_CYU_CD";
						$sql .= "    , A.BNRI_SAI_CD AS BNRI_SAI_CD";
						$sql .= " FROM";
						$sql .= "  T_KOTEI_D A ";
						$sql .= "  INNER JOIN T_KOTEI_H B ";
						$sql .= "    ON A.NINUSI_CD   = B.NINUSI_CD ";
						$sql .= "    AND A.SOSIKI_CD  = B.SOSIKI_CD ";
						$sql .= "    AND A.STAFF_CD   = B.STAFF_CD ";
						$sql .= "    AND A.YMD_SYUGYO = B.YMD_SYUGYO";
						$sql .= ' WHERE ';
						$sql .= "  A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
						$sql .= "  A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
						$sql .= "  A.YMD_SYUGYO='" . $ymd_shugyo . "' AND" ;
						$sql .= "  A.STAFF_CD='" . $staff_cd . "' AND" ;
						$sql .= "  DATE_FORMAT( A.DT_KOTEI_ST, '%Y-%m-%d %H:%i:00' ) < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$timeFrom.":00' )";
						$sql .= "  AND " ;
						$sql .= "  ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$timeFrom.":00' ) <= DATE_FORMAT( A.DT_KOTEI_ED, '%Y-%m-%d %H:%i:00' )";
						$sql .= "  AND " ;
						$sql .= "  DATE_FORMAT( A.DT_KOTEI_ED, '%Y-%m-%d %H:%i:00' ) <= ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$timeTo.":00' )";
						$sql .= "  AND " ;
						$sql .= "  A.DT_KOTEI_ED IS NOT NULL";


						$this->queryWithPDOLog($stmt2,$pdo2,$sql, '工程開始前またがり確認');

						if ($result = $stmt2->fetch(PDO::FETCH_ASSOC)) {

							$maeKotei = array(
									"NINUSI_CD" => $result['NINUSI_CD'],
									"SOSIKI_CD" => $result['SOSIKI_CD'],
									"STAFF_CD" => $result['STAFF_CD'],
									"YMD_SYUGYO" => $result['YMD_SYUGYO'],
									"KOTEI_NO" => $result['KOTEI_NO'],
									"BNRI_DAI_CD" => $result['BNRI_DAI_CD'],
									"BNRI_CYU_CD" => $result['BNRI_CYU_CD'],
									"BNRI_SAI_CD" => $result['BNRI_SAI_CD']

							);

							$maeFlag = 1;
						}

						//前工程を探す
						if ($maeKotei == '') {

							$sql  = "SELECT ";
							$sql .= "      A.SOSIKI_CD AS SOSIKI_CD";
							$sql .= "    , A.NINUSI_CD AS NINUSI_CD";
							$sql .= "    , A.STAFF_CD AS STAFF_CD";
							$sql .= "    , A.YMD_SYUGYO AS YMD_SYUGYO";
							$sql .= "    , A.KOTEI_NO AS KOTEI_NO";
							$sql .= "    , A.BNRI_DAI_CD AS BNRI_DAI_CD";
							$sql .= "    , A.BNRI_CYU_CD AS BNRI_CYU_CD";
							$sql .= "    , A.BNRI_SAI_CD AS BNRI_SAI_CD";
							$sql .= " FROM";
							$sql .= "  T_KOTEI_D A ";
							$sql .= "  INNER JOIN T_KOTEI_H B ";
							$sql .= "    ON A.NINUSI_CD   = B.NINUSI_CD ";
							$sql .= "    AND A.SOSIKI_CD  = B.SOSIKI_CD ";
							$sql .= "    AND A.STAFF_CD   = B.STAFF_CD ";
							$sql .= "    AND A.YMD_SYUGYO = B.YMD_SYUGYO";
							$sql .= ' WHERE ';
							$sql .= "  A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
							$sql .= "  A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
							$sql .= "  A.YMD_SYUGYO='" . $ymd_shugyo . "' AND" ;
							$sql .= "  A.STAFF_CD='" . $staff_cd . "' AND" ;
							$sql .= "  DATE_FORMAT( A.DT_KOTEI_ED, '%Y-%m-%d %H:%i:00' ) < ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$timeFrom.":00' )";
							$sql .= "  AND " ;
							$sql .= "  A.DT_KOTEI_ED IS NOT NULL";
							$sql .= ' ORDER BY ';
							$sql .= '   KOTEI_NO DESC ';
							$sql .= ' LIMIT 1 ';

							$this->queryWithPDOLog($stmt2,$pdo2,$sql, '前工程探し');

							if ($result = $stmt2->fetch(PDO::FETCH_ASSOC)) {

								$maeKotei = array(
										"NINUSI_CD" => $result['NINUSI_CD'],
										"SOSIKI_CD" => $result['SOSIKI_CD'],
										"STAFF_CD" => $result['STAFF_CD'],
										"YMD_SYUGYO" => $result['YMD_SYUGYO'],
										"KOTEI_NO" => $result['KOTEI_NO'],
										"BNRI_DAI_CD" => $result['BNRI_DAI_CD'],
										"BNRI_CYU_CD" => $result['BNRI_CYU_CD'],
										"BNRI_SAI_CD" => $result['BNRI_SAI_CD']

								);

								$maeFlag = 2;
							}

						}

						//前工程が同じか確認
						if ($maeKotei != '') {

							if ($bnri_dai_cd == $maeKotei['BNRI_DAI_CD'] && $bnri_cyu_cd == $maeKotei['BNRI_CYU_CD'] && $bnri_sai_cd == $maeKotei['BNRI_SAI_CD'] ) {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE060004'), array($ymd_shugyo,$staff_cd . ":" . $staff_nm));
								$this->errors[] =  $message;
								continue;
							}

						}

						$atoFlag = 0; //0:無 1:重なりあり 2:重なり無し
						$atoKotei = '';


						//工程開始前またがり
						$sql  = "SELECT ";
						$sql .= "      A.SOSIKI_CD AS SOSIKI_CD";
						$sql .= "    , A.NINUSI_CD AS NINUSI_CD";
						$sql .= "    , A.STAFF_CD AS STAFF_CD";
						$sql .= "    , A.YMD_SYUGYO AS YMD_SYUGYO";
						$sql .= "    , A.KOTEI_NO AS KOTEI_NO";
						$sql .= "    , A.BNRI_DAI_CD AS BNRI_DAI_CD";
						$sql .= "    , A.BNRI_CYU_CD AS BNRI_CYU_CD";
						$sql .= "    , A.BNRI_SAI_CD AS BNRI_SAI_CD";
						$sql .= " FROM";
						$sql .= "  T_KOTEI_D A ";
						$sql .= "  INNER JOIN T_KOTEI_H B ";
						$sql .= "    ON A.NINUSI_CD   = B.NINUSI_CD ";
						$sql .= "    AND A.SOSIKI_CD  = B.SOSIKI_CD ";
						$sql .= "    AND A.STAFF_CD   = B.STAFF_CD ";
						$sql .= "    AND A.YMD_SYUGYO = B.YMD_SYUGYO";
						$sql .= ' WHERE ';
						$sql .= "  A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
						$sql .= "  A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
						$sql .= "  A.YMD_SYUGYO='" . $ymd_shugyo . "' AND" ;
						$sql .= "  A.STAFF_CD='" . $staff_cd . "' AND" ;
						$sql .= "  ( ";
						$sql .= "    ( ";
						$sql .= "      ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$timeFrom.":00' )  <= DATE_FORMAT( A.DT_KOTEI_ST, '%Y-%m-%d %H:%i:00' )";
						$sql .= "      AND " ;
						$sql .= "      DATE_FORMAT( A.DT_KOTEI_ST, '%Y-%m-%d %H:%i:00' ) <= ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$timeTo.":00' )";
						$sql .= "      AND " ;
						$sql .= "      ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$timeTo.":00' ) < DATE_FORMAT( A.DT_KOTEI_ED, '%Y-%m-%d %H:%i:00' )";
						$sql .= "      AND " ;
						$sql .= "      A.DT_KOTEI_ED IS NOT NULL";
						$sql .= "    )";
						$sql .= "    OR ";
						$sql .= "    ( ";
						$sql .= "      ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$timeFrom.":00' )  <= DATE_FORMAT( A.DT_KOTEI_ST, '%Y-%m-%d %H:%i:00' )";
						$sql .= "      AND " ;
						$sql .= "      DATE_FORMAT( A.DT_KOTEI_ST, '%Y-%m-%d %H:%i:00' ) <= ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$timeTo.":00' )";
						$sql .= "      AND " ;
						$sql .= "      A.DT_KOTEI_ED IS NULL";
						$sql .= "    )";
						$sql .= "  ) ";


						$this->queryWithPDOLog($stmt2,$pdo2,$sql, '工程開始前またがり確認');

						if ($result = $stmt2->fetch(PDO::FETCH_ASSOC)) {

							$atoKotei = array(
									"NINUSI_CD" => $result['NINUSI_CD'],
									"SOSIKI_CD" => $result['SOSIKI_CD'],
									"STAFF_CD" => $result['STAFF_CD'],
									"YMD_SYUGYO" => $result['YMD_SYUGYO'],
									"KOTEI_NO" => $result['KOTEI_NO'],
									"BNRI_DAI_CD" => $result['BNRI_DAI_CD'],
									"BNRI_CYU_CD" => $result['BNRI_CYU_CD'],
									"BNRI_SAI_CD" => $result['BNRI_SAI_CD']

							);

							$atoFlag =1;
						}

						//後工程を探す
						if ($atoKotei == '') {

							$sql  = "SELECT ";
							$sql .= "      A.SOSIKI_CD AS SOSIKI_CD";
							$sql .= "    , A.NINUSI_CD AS NINUSI_CD";
							$sql .= "    , A.STAFF_CD AS STAFF_CD";
							$sql .= "    , A.YMD_SYUGYO AS YMD_SYUGYO";
							$sql .= "    , A.KOTEI_NO AS KOTEI_NO";
							$sql .= "    , A.BNRI_DAI_CD AS BNRI_DAI_CD";
							$sql .= "    , A.BNRI_CYU_CD AS BNRI_CYU_CD";
							$sql .= "    , A.BNRI_SAI_CD AS BNRI_SAI_CD";
							$sql .= " FROM";
							$sql .= "  T_KOTEI_D A ";
							$sql .= "  INNER JOIN T_KOTEI_H B ";
							$sql .= "    ON A.NINUSI_CD   = B.NINUSI_CD ";
							$sql .= "    AND A.SOSIKI_CD  = B.SOSIKI_CD ";
							$sql .= "    AND A.STAFF_CD   = B.STAFF_CD ";
							$sql .= "    AND A.YMD_SYUGYO = B.YMD_SYUGYO";
							$sql .= ' WHERE ';
							$sql .= "  A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
							$sql .= "  A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
							$sql .= "  A.YMD_SYUGYO='" . $ymd_shugyo . "' AND" ;
							$sql .= "  A.STAFF_CD='" . $staff_cd . "' AND" ;
							$sql .= "  ADDTIME(CONCAT(A.YMD_SYUGYO,' 00:00:00'),'".$timeTo.":00' ) < DATE_FORMAT( A.DT_KOTEI_ST, '%Y-%m-%d %H:%i:00' )";
							$sql .= "  AND " ;
							$sql .= "  A.DT_KOTEI_ED IS NOT NULL";
							$sql .= ' ORDER BY ';
							$sql .= '   KOTEI_NO ';
							$sql .= ' LIMIT 1 ';

							$this->queryWithPDOLog($stmt2,$pdo2,$sql, '前工程探し');

							if ($result = $stmt2->fetch(PDO::FETCH_ASSOC)) {

								$atoKotei = array(
										"NINUSI_CD" => $result['NINUSI_CD'],
										"SOSIKI_CD" => $result['SOSIKI_CD'],
										"STAFF_CD" => $result['STAFF_CD'],
										"YMD_SYUGYO" => $result['YMD_SYUGYO'],
										"KOTEI_NO" => $result['KOTEI_NO'],
										"BNRI_DAI_CD" => $result['BNRI_DAI_CD'],
										"BNRI_CYU_CD" => $result['BNRI_CYU_CD'],
										"BNRI_SAI_CD" => $result['BNRI_SAI_CD']

								);

								$atoFlag =2;
							}

						}

						//後工程が同じか確認
						if ($atoKotei != '') {

							if ($bnri_dai_cd == $atoKotei['BNRI_DAI_CD'] && $bnri_cyu_cd == $atoKotei['BNRI_CYU_CD'] && $bnri_sai_cd == $atoKotei['BNRI_SAI_CD'] ) {

								$message = vsprintf($this->MMessage->getOneMessage('WPOE060005'), array($ymd_shugyo,$staff_cd . ":" . $staff_nm));
								$this->errors[] =  $message;
								continue;
							}

						}

						//前工程が重なるのでアップデート
						if($maeFlag == 1) {

							$sql  = "UPDATE ";
							$sql .= "    T_KOTEI_D";
							$sql .= "  SET";
							$sql .= "   DT_KOTEI_ED=ADDTIME(CONCAT(YMD_SYUGYO,' 00:00:00'),'".$timeFrom.":00' )";
							$sql .= "  WHERE";
							$sql .= "    NINUSI_CD='" . $maeKotei['NINUSI_CD'] . "' AND";
							$sql .= "    SOSIKI_CD='" . $maeKotei['SOSIKI_CD'] . "' AND";
							$sql .= "    STAFF_CD='" . $maeKotei['STAFF_CD'] . "' AND" ;
							$sql .= "    YMD_SYUGYO='" . $maeKotei['YMD_SYUGYO'] . "' AND" ;
							$sql .= "    KOTEI_NO=" . $maeKotei['KOTEI_NO'] . "" ;

							$this->execWithPDOLog($pdo2,$sql, '前工程　アップデート');
						}

						//後工程が重なるのでアップデート
						if($atoFlag == 1) {

							$sql  = "UPDATE ";
							$sql .= "    T_KOTEI_D";
							$sql .= "  SET";
							$sql .= "   DT_KOTEI_ST=ADDTIME(CONCAT(YMD_SYUGYO,' 00:00:00'),'".$timeTo.":00' )";
							$sql .= "  WHERE";
							$sql .= "    NINUSI_CD='" . $atoKotei['NINUSI_CD'] . "' AND";
							$sql .= "    SOSIKI_CD='" . $atoKotei['SOSIKI_CD'] . "' AND";
							$sql .= "    STAFF_CD='" . $atoKotei['STAFF_CD'] . "' AND" ;
							$sql .= "    YMD_SYUGYO='" . $atoKotei['YMD_SYUGYO'] . "' AND" ;
							$sql .= "    KOTEI_NO=" . $atoKotei['KOTEI_NO'] . "" ;

							$this->execWithPDOLog($pdo2,$sql, '後工程　アップデート');
						}

						//インサート

						$sql  = "INSERT ";
						$sql .= "  INTO";
						$sql .= "    T_KOTEI_D";
						$sql .= "  (";
						$sql .= "    NINUSI_CD,";
						$sql .= "    SOSIKI_CD,";
						$sql .= "    STAFF_CD,";
						$sql .= "    YMD_SYUGYO,";
						$sql .= "    KOTEI_NO,";
						$sql .= "    BNRI_DAI_CD,";
						$sql .= "    BNRI_CYU_CD,";
						$sql .= "    BNRI_SAI_CD,";
						$sql .= "    DT_KOTEI_ST,";
						$sql .= "    DT_KOTEI_ED,";
						$sql .= "    CREATED,";
						$sql .= "    CREATED_STAFF,";
						$sql .= "    MODIFIED,";
						$sql .= "    MODIFIED_STAFF";
						$sql .= "  ) VALUES (";
						$sql .= "   '" . $this->_ninusi_cd . "',";
						$sql .= "   '" . $this->_sosiki_cd . "',";
						$sql .= "   '" . $staff_cd . "',";
						$sql .= "   '" . $ymd_shugyo . "',";
						$sql .= "   500,";
						$sql .= "   '" . $bnri_dai_cd . "',";
						$sql .= "   '" . $bnri_cyu_cd . "',";
						$sql .= "   '" . $bnri_sai_cd . "',";
						$sql .= "   ADDTIME(CONCAT(YMD_SYUGYO,' 00:00:00'),'".$timeFrom.":00' ),";
						$sql .= "   ADDTIME(CONCAT(YMD_SYUGYO,' 00:00:00'),'".$timeTo.":00' ),";
						$sql .= "   NOW(),";
						$sql .= "   '" . $staff_cd_session . "',";
						$sql .= "   NOW(),";
						$sql .= "   '" . $staff_cd_session . "'";
						$sql .= "  )";

						$this->execWithPDOLog($pdo2,$sql, '工程追加');

					}


					//キー単位全工程取得
					$sql  = "SELECT ";
					$sql .= "    A.NINUSI_CD,";
					$sql .= "    A.SOSIKI_CD,";
					$sql .= "    A.STAFF_CD,";
					$sql .= "    A.YMD_SYUGYO,";
					$sql .= "    A.KOTEI_NO,";
					$sql .= "    A.BNRI_DAI_CD,";
					$sql .= "    A.BNRI_CYU_CD,";
					$sql .= "    A.BNRI_SAI_CD,";
					$sql .= "    A.DT_KOTEI_ST,";
					$sql .= "    A.DT_KOTEI_ED";
					$sql .= " FROM";
					$sql .= "  T_KOTEI_D A ";
					$sql .= "  INNER JOIN T_KOTEI_H B ";
					$sql .= "    ON A.NINUSI_CD   = B.NINUSI_CD ";
					$sql .= "    AND A.SOSIKI_CD  = B.SOSIKI_CD ";
					$sql .= "    AND A.STAFF_CD   = B.STAFF_CD ";
					$sql .= "    AND A.YMD_SYUGYO = B.YMD_SYUGYO";
					$sql .= "  WHERE";
					$sql .= "    A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
					$sql .= "    A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
					$sql .= "    A.STAFF_CD='" . $staff_cd . "' AND" ;
					$sql .= "    A.YMD_SYUGYO='" . $ymd_shugyo . "'" ;
					$sql .= " ORDER BY";
					$sql .= "  A.DT_KOTEI_ST,A.DT_KOTEI_ED ";

					$this->queryWithPDOLog($stmt2,$pdo2,$sql, '工程リスト');

					$arrayList = array();

					$count=1;

					while ($result = $stmt2->fetch(PDO::FETCH_ASSOC)) {

							$array = array(
									"NINUSI_CD" => $result['NINUSI_CD'],
									"SOSIKI_CD" => $result['SOSIKI_CD'],
									"STAFF_CD" => $result['STAFF_CD'],
									"YMD_SYUGYO" => $result['YMD_SYUGYO'],
									"KOTEI_NO" => $count,
									"BNRI_DAI_CD" => $result['BNRI_DAI_CD'],
									"BNRI_CYU_CD" => $result['BNRI_CYU_CD'],
									"BNRI_SAI_CD" => $result['BNRI_SAI_CD'],
									"DT_KOTEI_ST" => $result['DT_KOTEI_ST'],
									"DT_KOTEI_ED" => $result['DT_KOTEI_ED'],
							);

							$arrayList[] = $array;
							$count++;
					}

					//キー単位全工程削除
					$sql  = "DELETE ";
					$sql .= " FROM";
					$sql .= "  T_KOTEI_D";
					$sql .= "  WHERE";
					$sql .= "    NINUSI_CD='" . $this->_ninusi_cd . "' AND";
					$sql .= "    SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
					$sql .= "    STAFF_CD='" . $staff_cd . "' AND" ;
					$sql .= "    YMD_SYUGYO='" . $ymd_shugyo . "'" ;

					$this->queryWithPDOLog($stmt2,$pdo2,$sql, '工程削除');

					//キー単位前工程再配置
					foreach ($arrayList as $array) {

						$sql  = "INSERT ";
						$sql .= "  INTO";
						$sql .= "    T_KOTEI_D";
						$sql .= "  (";
						$sql .= "    NINUSI_CD,";
						$sql .= "    SOSIKI_CD,";
						$sql .= "    STAFF_CD,";
						$sql .= "    YMD_SYUGYO,";
						$sql .= "    KOTEI_NO,";
						$sql .= "    BNRI_DAI_CD,";
						$sql .= "    BNRI_CYU_CD,";
						$sql .= "    BNRI_SAI_CD,";
						$sql .= "    DT_KOTEI_ST,";
						$sql .= "    DT_KOTEI_ED,";
						$sql .= "    CREATED,";
						$sql .= "    CREATED_STAFF,";
						$sql .= "    MODIFIED,";
						$sql .= "    MODIFIED_STAFF";
						$sql .= "  ) VALUES (";
						$sql .= "   '" . $array['NINUSI_CD'] . "',";
						$sql .= "   '" . $array['SOSIKI_CD'] . "',";
						$sql .= "   '" . $array['STAFF_CD'] . "',";
						$sql .= "   '" . $array['YMD_SYUGYO'] . "',";
						$sql .= "   " . $array['KOTEI_NO'] . ",";
						$sql .= "   '" . $array['BNRI_DAI_CD'] . "',";
						$sql .= "   '" . $array['BNRI_CYU_CD'] . "',";
						$sql .= "   '" . $array['BNRI_SAI_CD'] . "',";
						$sql .= "   '" . $array['DT_KOTEI_ST'] . "',";
						$sql .= "   '" . $array['DT_KOTEI_ED'] . "',";
						$sql .= "   NOW(),";
						$sql .= "   '" . $staff_cd_session . "',";
						$sql .= "   NOW(),";
						$sql .= "   '" . $staff_cd_session . "'";
						$sql .= "  )";

						$this->execWithPDOLog($pdo2,$sql, '工程追加');
					}


					$message = vsprintf($this->MMessage->getOneMessage('WPOI060003'), array($ymd_shugyo,$staff_cd . ":" . $staff_nm));
					$this->successes[] =  $message;

				}

				//ログを残す
				$sql  = "DELETE ";
				$sql .= " FROM";
				$sql .= "  T_LOG_KOTEI_IKKATSU";
				$sql .= ' WHERE ';
				$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
				$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "'";

				$this->execWithPDOLog($pdo2,$sql, 'ログ削除');

				$message = vsprintf($this->MMessage->getOneMessage('WPOE060002'), array($ymd_shugyo,$staff_cd . ":" . $staff_nm));

				$sql  = "SELECT ";
				$sql .= "     DAI.BNRI_DAI_NM AS BNRI_DAI_NM,";
				$sql .= "     CYU.BNRI_CYU_NM AS BNRI_CYU_NM,";
				$sql .= "     SAI.BNRI_SAI_NM AS BNRI_SAI_NM";
				$sql .= " FROM";
				$sql .= "  M_BNRI_SAI SAI";
				$sql .= "  LEFT JOIN M_BNRI_DAI DAI";
				$sql .= "    ON  SAI.NINUSI_CD = DAI.NINUSI_CD";
				$sql .= "    AND SAI.SOSIKI_CD = DAI.SOSIKI_CD";
				$sql .= "    AND SAI.BNRI_DAI_CD = DAI.BNRI_DAI_CD";
				$sql .= "  LEFT JOIN M_BNRI_CYU CYU";
				$sql .= "    ON  SAI.NINUSI_CD = CYU.NINUSI_CD";
				$sql .= "    AND SAI.SOSIKI_CD = CYU.SOSIKI_CD";
				$sql .= "    AND SAI.BNRI_DAI_CD = CYU.BNRI_DAI_CD";
				$sql .= "    AND SAI.BNRI_CYU_CD = CYU.BNRI_CYU_CD";
				$sql .= ' WHERE ';
				$sql .= "  SAI.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
				$sql .= "  SAI.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
				$sql .= "  SAI.BNRI_DAI_CD = '" . $bnri_dai_cd . "' AND" ;
				$sql .= "  SAI.BNRI_CYU_CD = '" . $bnri_cyu_cd . "' AND" ;
				$sql .= "  SAI.BNRI_SAI_CD = '" . $bnri_sai_cd . "'" ;


				$this->queryWithPDOLog($stmt2,$pdo2,$sql, '工程確認');
				$result = $stmt2->fetch(PDO::FETCH_ASSOC);

				$titleText = '';
				$titleText .= '差込工程　[' . $bnri_dai_cd. ":" . $result['BNRI_DAI_NM'] . " > " . $bnri_cyu_cd . ":" . $result['BNRI_CYU_NM'] . " > " .$bnri_sai_cd . ":" . $result['BNRI_SAI_NM'] . "]\n";
				$titleText .= "工程開始時刻　[" .$ymd_shugyo . " " . $timeFrom . ":00]　　工程終了時刻　[".$ymd_shugyo . " " . $timeTo . ":00]";

				$logText = '';
				$logText .= "エラー一覧　" . count($this->errors). "件\n";

				foreach ($this->errors as $str) {
					$logText .= $str . "\n";
				}

				$logText .= "\n";
				$logText .= "処理一覧\　" . count($this->successes). "件\n";

				foreach ($this->successes as $str) {
					$logText .= $str . "\n";
				}

				$sql  = "INSERT ";
				$sql .= "  INTO";
				$sql .= "    T_LOG_KOTEI_IKKATSU";
				$sql .= "  (";
				$sql .= "    NINUSI_CD,";
				$sql .= "    SOSIKI_CD,";
				$sql .= "    TITLE,";
				$sql .= "    LOG,";
				$sql .= "    CREATED,";
				$sql .= "    CREATED_STAFF,";
				$sql .= "    MODIFIED,";
				$sql .= "    MODIFIED_STAFF";
				$sql .= "  ) VALUES (";
				$sql .= "   '" . $this->_ninusi_cd . "',";
				$sql .= "   '" . $this->_sosiki_cd . "',";
				$sql .= "   '" . $titleText . "',";
				$sql .= "   '" . $logText . "',";
				$sql .= "   NOW(),";
				$sql .= "   '" . $staff_cd_session . "',";
				$sql .= "   NOW(),";
				$sql .= "   '" . $staff_cd_session . "'";
				$sql .= "  )";

				$this->execWithPDOLog($pdo2,$sql, '工程追加');

				$pdo2->commit();

			} catch (Exception $e) {

				$this->printLog("fatal", "例外発生", "WPO060", $e->getMessage());
				$pdo2->rollBack();
				$error_code = 1;
			}



			$pdo2 = null;


			//排他制御オフ
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd_session . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WPO',";
			$sql .= "'060',";
			$sql .= "0";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'WPO060 排他制御オフ');

			$pdo = null;

			if ($return_cd == "1") {

				//例外の場合、例外メッセージ
				$message = $this->MMessage->getOneMessage('CMNE000107');
				$this->errors = array();
				$this->errors[] =  $message;
				return false;
			}

                        if ($error_code != 0) {
                            $message = "工程終了されていないデータがあります。全て工程終了してから処理してください。";
                            $this->messages[] =  $message;
                        }

			//処理数メッセージを用意
			if ($error_code == 0) {
			    $message = vsprintf($this->MMessage->getOneMessage('WPOI060001'), array(count($this->successes)));
			    $this->messages[] =  $message;
			}

			if (count($this->errors) != 0) {
				$message = vsprintf($this->MMessage->getOneMessage('WPOI060002'), array(count($this->errors)));
				$this->messages[] =  $message;
			}

			return true;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO060", $e->getMessage());
			$pdo = null;

		}

		//例外発生の場合、エラー
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors = array();
		$this->errors[] =  $message;
		return false;

	}



	/**
	 * 締日初期値取得
	 * @access   public
	 * @return   日付
	 */
	function getToday() {


		try {


			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			//就業開始基準時間取得
			$sql  = "SELECT ";
			$sql .= "   DT_SYUGYO_ST_BASE,";
			$sql .= "   CURRENT_DATE,";
			$sql .= "   NOW() AS NOW";
			$sql .= " FROM";
			$sql .= "   M_SYSTEM";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "';";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業開始基準時間　取得");

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$dt_syugyo_st_base = $result['DT_SYUGYO_ST_BASE'];
			$ymd_today = $result['CURRENT_DATE'];
			$now = $result['NOW'];

			//日付取得
			$tmpDate = $ymd_today . " " . $dt_syugyo_st_base;

			if (strtotime($now) < strtotime($tmpDate)) {

				$dt = new DateTime($tmpDate);
				$dt->sub(new DateInterval("P1D"));
				$ymd_today = $dt->format('Y-m-d');
				$tmpDate = $ymd_today . " " . $dt_syugyo_st_base;
			}


			$dt = new DateTime($tmpDate);
			$ymd_today = $dt->format('Ymd');

			$pdo = null;

			return  $ymd_today;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO060", $e->getMessage());
			$pdo = null;
		}

		return date("Ymd",strtotime("-1 day"));;

	}

	/**
	 * タイムスタンプ取得
	 * @access   public
	 * @param    タイムスタンプ
	 * @return   成否情報(true,false)
	 */
	public function getTimestamp() {

		$pdo = null;

		try{

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//タイムスタンプ取得
			$sql = "CALL P_WPO060_GET_TIMESTAMP(";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " @timestamp";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, '工程予測　タイムスタンプ取得');

			$sql = "";
			$sql .= " SELECT";
			$sql .= "  @timestamp";


			$this->queryWithPDOLog($stmt,$pdo,$sql, '工程予測　タイムスタンプ取得');
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


	/**
	 * 時間一覧取得
	 * @access   public
	 * @return   日付
	 */
	function getTimes() {

		$timeArray = array();


		try {



			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			//就業開始基準時間取得
			$sql  = "SELECT ";
			$sql .= "   DT_SYUGYO_ST_BASE";
			$sql .= " FROM";
			$sql .= "   M_SYSTEM";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "';";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業開始基準時間　取得");

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$dt_syugyo_st_base = $result['DT_SYUGYO_ST_BASE'];
			$times = explode(":", $dt_syugyo_st_base);

			if (count($times) < 1) {
				return $timeArray;
			}

			$hour = (int)$times[0];
			$min = (int)$times[1];

			$timeArray[] = sprintf('%02d', $hour) . ":" . sprintf('%02d', $min);

			for ($i = 0; $i < 71; $i++) {

				if ($min == 30) {

					$hour += 1;
					$min = 0;

				} else {

					$min = 30;

				}

				$timeArray[] = sprintf('%02d', $hour) . ":" . sprintf('%02d', $min);

			}

			$pdo = null;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "TPM060", $e->getMessage());
			$pdo = null;
		}

		return $timeArray;

	}

	public function getLogText() {

		$pdo = null;

		try{

			$array = array();

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql  = "SELECT";
			$sql .= "  TITLE,";
			$sql .= "  LOG";
			$sql .= " FROM";
			$sql .= "  T_LOG_KOTEI_IKKATSU";
			$sql .= " WHERE ";
			$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "'";


			$this->queryWithPDOLog($stmt,$pdo,$sql,"ログ　取得");

			if($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
				    "TITLE" => $result['TITLE'],
					"LOG" => $result['LOG']

				);

			}

			$pdo = null;

			return $array;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "HRD010", $e->getMessage());
			$pdo = null;
		}


		return array();
	}


	public function getHaken() {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql  = "SELECT";
			$sql .= "  KAISYA_CD,";
			$sql .= "  KAISYA_NM";
			$sql .= " FROM";
			$sql .= "  M_HAKEN_KAISYA";
			$sql .= " WHERE ";
			$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "'";
			$sql .= " ORDER BY";
			$sql .= "  KAISYA_CD";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"派遣会社　取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
						"KAISYA_CD" => $result['KAISYA_CD'],
						"KAISYA_NM" => $result['KAISYA_NM']
				);

				$arrayList[] = $array;

			}

			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "HRD010", $e->getMessage());
			$pdo = null;
		}


		return array();
	}

	public function getMinTime() {

		$pdo = null;

		try{

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//タイムスタンプ取得
			$sql  = "SELECT";
			$sql .= "  DT_SYUGYO_ST_BASE";
			$sql .= " FROM";
			$sql .= "  M_SYSTEM";
			$sql .= " WHERE ";
			$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "'";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"システム時間");
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			$base = $result['DT_SYUGYO_ST_BASE'];

			if ($base == null) {
				$base = "00:00:00";
			}

			return $base;

		} catch (Exception $e){

			$pdo = null;
		}

		return date('Y-m-d H:i:s');

	}

	public function getMaxTime() {

		$pdo = null;

		try{

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//タイムスタンプ取得
			$sql  = "SELECT";
			$sql .= "  ADDTIME(TIME(DT_SYUGYO_ST_BASE),'36:00:00') AS DT_SYUGYO_ST_BASE";
			$sql .= " FROM";
			$sql .= "  M_SYSTEM";
			$sql .= " WHERE ";
			$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "'";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"システム時間");
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			$base = $result['DT_SYUGYO_ST_BASE'];

			if ($base == null) {
				$base = "00:00:00";
			}

			return $base;

		} catch (Exception $e){

			$pdo = null;
		}

		return date('Y-m-d H:i:s');

	}

}

?>
