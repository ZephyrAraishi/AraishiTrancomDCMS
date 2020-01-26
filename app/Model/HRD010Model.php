<?php
/**
 * HRD010
 *
 * 人材管理
 *
 * @category      HRD
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');
App::uses('Validation', 'Utility');

class HRD010Model extends DCMSAppModel {

	public $name = 'HRD010Model';
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

	public function getNinusi() {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql  = "SELECT";
			$sql .= "  NINUSI_CD,";
			$sql .= "  NINUSI_RYAKU";
			$sql .= " FROM";
			$sql .= "  M_NINUSI";
			$sql .= " ORDER BY";
			$sql .= "  NINUSI_CD";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"荷主マスタ　取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
						"NINUSI_CD" => $result['NINUSI_CD'],
						"NINUSI_RYAKU" => $result['NINUSI_RYAKU']
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

	public function getSosiki() {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql  = "SELECT";
			$sql .= "  SOSIKI_CD,";
			$sql .= "  SOSIKI_RYAKU";
			$sql .= " FROM";
			$sql .= "  M_SOSIKI";
			$sql .= " GROUP BY";
			$sql .= "  SOSIKI_CD";
			$sql .= " ORDER BY";
			$sql .= "  SOSIKI_CD";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"組織マスタ　取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
						"SOSIKI_CD" => $result['SOSIKI_CD'],
						"SOSIKI_RYAKU" => $result['SOSIKI_RYAKU']
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

	public function getKyoten() {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql  = "SELECT";
			$sql .= "  A.NINUSI_CD,";
			$sql .= "  B.SOSIKI_CD,";
			$sql .= "  A.NINUSI_RYAKU,";
			$sql .= "  B.SOSIKI_RYAKU";
			$sql .= " FROM";
			$sql .= "  M_NINUSI A";
			$sql .= "   LEFT JOIN M_SOSIKI B ON";
			$sql .= "    A.NINUSI_CD = B.NINUSI_CD";
			$sql .= " ORDER BY";
			$sql .= "  A.NINUSI_CD";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"組織マスタ　取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$checked = "";

				if ($result['NINUSI_CD'] == $this->_ninusi_cd &&
				$result['SOSIKI_CD'] == $this->_sosiki_cd) {
					$checked = "checked";
				}

				$array = array(
						"NINUSI_CD" => $result['NINUSI_CD'],
						"SOSIKI_CD" => $result['SOSIKI_CD'],
						"NINUSI_RYAKU" => $result['NINUSI_RYAKU'],
						"SOSIKI_RYAKU" => $result['SOSIKI_RYAKU'],
						"CHECKED" => $checked

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

	public function getRank() {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql  = "SELECT";
			$sql .= "  RANK";
			$sql .= " FROM";
			$sql .= "  M_STAFF_RANK";
			$sql .= " WHERE ";
			$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "'";
			$sql .= " ORDER BY";
			$sql .= "  SORT_SEQ";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"組織マスタ　取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
						"RANK" => $result['RANK']
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

	public function getStaffList($staff_cd,$staff_nm,$haken_cd,$del_flg) {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql  = "SELECT";
			$sql .= "  A.STAFF_CD,";
			$sql .= "  A.STAFF_NM,";
			$sql .= "  A.DEL_FLG,";
			$sql .= "  B.KAISYA_CD,";
			$sql .= "  B.KAISYA_NM";
			$sql .= " FROM";
			$sql .= "  M_STAFF_KIHON A";
			$sql .=	" LEFT JOIN ";
			$sql .=	' 	M_HAKEN_KAISYA B';
			$sql .=	' ON ';
			$sql .=	' 	A.HAKEN_KAISYA_CD = B.KAISYA_CD AND';
			$sql .= "    B.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "    B.SOSIKI_CD='" . $this->_sosiki_cd . "'";


			$firstFlg = 0;

			if ($staff_cd != null) {

				if ($firstFlg != 0) {

					$sql .= " AND";
				} else {

					$sql .= " WHERE ";
				}

				$sql .= " A.STAFF_CD LIKE '%" . $staff_cd . "%'";

				$firstFlg = 1;
			}

			if ($staff_nm != null) {

				if ($firstFlg != 0) {

					$sql .= " AND";
				} else {

					$sql .= " WHERE ";
				}

				$sql .= " A.STAFF_NM LIKE '%" . $staff_nm . "%'";

				$firstFlg = 1;
			}

			if ($haken_cd != null) {

				if ($firstFlg != 0) {

					$sql .= " AND";
				} else {

					$sql .= " WHERE ";
				}
				$sql .= " B.KAISYA_CD='" . $haken_cd . "'";

				$firstFlg = 1;
			}

			if ($del_flg != null) {

				if ($firstFlg != 0) {

					$sql .= " AND";
				} else {

					$sql .= " WHERE ";
				}
				$sql .= " A.DEL_FLG=" . $del_flg . "";

				$firstFlg = 1;
			}

				$sql .= " ORDER BY";
			$sql .= "  DEL_FLG,STAFF_CD";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"スタッフリスト　取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
						"STAFF_CD" => $result['STAFF_CD'],
						"STAFF_NM" => $result['STAFF_NM'],
						"KAISYA_CD" => $result['KAISYA_CD'],
						"KAISYA_NM" => $result['KAISYA_NM'],
						"DEL_FLG" => $result['DEL_FLG'],
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

	public function checkStaff($staff_cd) {

		$pdo = null;

		try {

			//スタッフコード存在チェック
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql  = 'SELECT ';
			$sql .= '  COUNT(*) AS COUNT';
			$sql .= ' FROM';
			$sql .= '  M_STAFF_KIHON';
			$sql .= ' WHERE';
			$sql .= "  STAFF_CD='" . $staff_cd . "'";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"スタッフコード　存在チェック");

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$countKOTEI = $result['COUNT'];

			$pdo = null;

			if ($countKOTEI == 0) {

				$message = vsprintf($this->MMessage->getOneMessage('HRDE010010'), array("スタッフ"));
				$this->errors['Check'][] =  $message;
				return false;
			}

			return true;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "HRD010", $e->getMessage());
			$pdo = null;
		}

		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}

	public function checkKihonData($data,$flag = 0) {

		try {

			//スタッフコード必須チェック
			$staff_cd = $data->STAFF_CD;

			if ($flag == 0) {

				if ($staff_cd == "") {

					$message = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array("スタッフコード"));
					$this->errors['Check'][] =  $message;
				} else if (preg_match("/[0-9]+/",$staff_cd) == false) {

					$message = vsprintf($this->MMessage->getOneMessage('CMNE000002'), array("スタッフコード"));
					$this->errors['Check'][] =  $message;
				} else {

					//スタッフコード存在チェック
					$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);

					$sql  = 'SELECT ';
					$sql .= '  COUNT(*) AS COUNT';
					$sql .= ' FROM';
					$sql .= '  M_STAFF_KIHON';
					$sql .= ' WHERE';
					$sql .= "  STAFF_CD='" . $staff_cd . "'";

					$this->queryWithPDOLog($stmt,$pdo,$sql,"スタッフコード　存在チェック");

					$result = $stmt->fetch(PDO::FETCH_ASSOC);
					$countKOTEI = $result['COUNT'];

					$pdo = null;

					if ($countKOTEI != 0) {

						$message = vsprintf($this->MMessage->getOneMessage('HRDE010006'), array("スタッフコード"));
						$this->errors['Check'][] =  $message;
					}
				}

			}

			//スタッフ名必須チェック
			$staff_nm = $data->STAFF_NM;

			if ($staff_nm == "") {

				$message = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array("氏名"));
				$this->errors['Check'][] =  $message;
			}

			//パスワード必須チェック
			$password = $data->PASSWORD;

			if ($password == "") {

				$message = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array("パスワード"));
				$this->errors['Check'][] =  $message;
			}

			//IC番号必須チェック
			$ic_no = $data->IC_NO;

			if ($ic_no != "") {

				//IC番号フォーマットチェック
				if (preg_match("/[0-9]{16}/",$ic_no) == false) {

					$message = vsprintf($this->MMessage->getOneMessage('HRDE010005'), array("IC番号","16"));
					$this->errors['Check'][] =  $message;
				}
			}

			//誕生日必須チェック
			$ymd_birth = $data->YMD_BIRTH;

			if ($ymd_birth != "") {

				//生年月日フォーマットチェック
				if (preg_match("/[0-9]{8}/",$ymd_birth) == false) {

					$message = vsprintf($this->MMessage->getOneMessage('CMNE000002'), array("生年月日"));
					$this->errors['Check'][] =  $message;
				} else {
					$year = substr($ymd_birth, 0,4);
					$month = substr($ymd_birth, 4,2);
					$day = substr($ymd_birth, 6,2);

					//生年月日日付チェック
					if (checkdate($month, $day, $year) == false) {

						$message = vsprintf($this->MMessage->getOneMessage('CMNE000002'), array("生年月日"));
						$this->errors['Check'][] =  $message;
					}
				}

			}


			//派遣会社必須チェック
			$haken_kaisya_cd = $data->HAKEN_KAISYA_CD;

			if ($haken_kaisya_cd == "") {

				$message = vsprintf($this->MMessage->getOneMessage('HRDE010007'), array("派遣会社"));
				$this->errors['Check'][] =  $message;
			}

			// 2019/02/01 Debug Start K.Araishi 可能拠点選択チェック
			$kyotenArray = $data->KYOTEN;
			$kyotenFlg = false;

			foreach($kyotenArray as $array) {

				$cellsArray = $array->cells;
				$dataArray = $array->data;

				if ($cellsArray[1] == true) {
					$kyotenFlg = true;
				}

			}
			if (!$kyotenFlg) {
				$message = vsprintf($this->MMessage->getOneMessage('HRDE010007'), array("拠点選択－可能拠点"));
				$this->errors['Check'][] =  $message;
			}
			// 2019/02/01 Debug End K.Araishi

				//役職必須チェック
			$kbn_pst = $data->KBN_PST;

			if ($kbn_pst == "") {

				$message = vsprintf($this->MMessage->getOneMessage('HRDE010007'), array("役職"));
				$this->errors['Check'][] =  $message;
			}


			//ピッキング必須チェック
			$kbn_pick = $data->KBN_PICK;

			if ($kbn_pick == "") {

				$message = vsprintf($this->MMessage->getOneMessage('HRDE010007'), array("ピッキング"));
				$this->errors['Check'][] =  $message;
			}


			//給与ランク必須チェック
			$rank = $data->RANK;

			if ($rank == "") {

				$message = vsprintf($this->MMessage->getOneMessage('HRDE010007'), array("給与ランク"));
				$this->errors['Check'][] =  $message;
			}

			//所定時間必須チェック
			$kin_syotei = $data->KIN_SYOTEI;

			if ($kin_syotei == "") {

				$message = vsprintf($this->MMessage->getOneMessage('HRDE010007'), array("所定時間"));
				$this->errors['Check'][] =  $message;
				//所定時間フォーマットチェック
			} else {
				if (preg_match("/[0-9]+/",$kin_syotei) == false) {

					$message = vsprintf($this->MMessage->getOneMessage('CMNE000002'), array("所定時間"));
					$this->errors['Check'][] =  $message;
				}
			}

			//時間外必須チェック
			$kin_jikangai = $data->KIN_JIKANGAI;

			if ($kin_jikangai == "") {

				$message = vsprintf($this->MMessage->getOneMessage('HRDE010007'), array("時間外"));
				$this->errors['Check'][] =  $message;

				//時間外フォーマットチェック
			} else if (preg_match("/[0-9]+/",$kin_jikangai) == false) {

				$message = vsprintf($this->MMessage->getOneMessage('CMNE000002'), array("時間外"));
				$this->errors['Check'][] =  $message;
			}

			//深夜時間必須チェック
			$kin_sny = $data->KIN_SNY;

			if ($kin_sny == "") {

				$message = vsprintf($this->MMessage->getOneMessage('HRDE010007'), array("深夜時間"));
				$this->errors['Check'][] =  $message;

				//深夜時間フォーマットチェック
			} else if (preg_match("/[0-9]+/",$kin_sny) == false) {

				$message = vsprintf($this->MMessage->getOneMessage('CMNE000002'), array("深夜時間"));
				$this->errors['Check'][] =  $message;
			}

			//休出時間フォーマットチェック
			$kin_kst = $data->KIN_KST;

			if ($kin_kst == "") {

				$message = vsprintf($this->MMessage->getOneMessage('HRDE010007'), array("休出時間"));
				$this->errors['Check'][] =  $message;

			} else if (preg_match("/[0-9]+/",$kin_kst) == false) {

				$message = vsprintf($this->MMessage->getOneMessage('CMNE000002'), array("休出時間"));
				$this->errors['Check'][] =  $message;
			}

			//法廷休出時間フォーマットチェック
			$kin_h_kst = $data->KIN_H_KST;

			if (preg_match("/[0-9]*/",$kin_h_kst) == false) {

				$message = vsprintf($this->MMessage->getOneMessage('CMNE000002'), array("法廷休出時間"));
				$this->errors['Check'][] =  $message;
			}

			//法廷休出深夜時間フォーマットチェック
			$kin_h_kst_sny = $data->KIN_H_KST_SNY;

			if (preg_match("/[0-9]*/",$kin_h_kst_sny) == false) {

				$message = vsprintf($this->MMessage->getOneMessage('CMNE000002'), array("法廷休出深夜時間"));
				$this->errors['Check'][] =  $message;
			}


			//日曜日存在チェック
			$sun_time_st = $data->SUN_TIME_ST;
			$sun_time_ed = $data->SUN_TIME_ED;

			if ($sun_time_st != "" || $sun_time_ed != "") {

				if (($sun_time_st != "" && $sun_time_ed == "") ||
				$sun_time_st == "" && $sun_time_ed != "") {

					$message = vsprintf($this->MMessage->getOneMessage('HRDE010008'), array("日曜日"));
					$this->errors['Check'][] =  $message;

				} else {

					$errFlg = false;

					if (preg_match("/[0-9]{4}/",$sun_time_st) == false) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010005'), array("日曜日の出社","4"));
						$this->errors['Check'][] =  $message;
					} else if ((int)substr($sun_time_st,2,2) >= 60 || (int)substr($sun_time_st,0,2) >= 25 ||
							((int)substr($sun_time_st,2,2) != 0 && (int)substr($sun_time_st,0,2) == 24)) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010009'), array("日曜日の出社"));
						$this->errors['Check'][] =  $message;
					}

					if (preg_match("/[0-9]{4}/",$sun_time_ed) == false) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010005'), array("日曜日の退社","4"));
						$this->errors['Check'][] =  $message;
					} else if ((int)substr($sun_time_ed,2,2) >= 60 || (int)substr($sun_time_ed,0,2) >= 25 ||
							((int)substr($sun_time_ed,2,2) != 0 && (int)substr($sun_time_ed,0,2) == 24)) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010009'), array("日曜日の退社"));
						$this->errors['Check'][] =  $message;
					}


					if ($errFlg == false && intval($sun_time_st) >= intval($sun_time_ed)) {
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010011'), array("日曜日"));
						$this->errors['Check'][] =  $message;
					}
				}

			}

			//月曜日存在チェック
			$mon_time_st = $data->MON_TIME_ST;
			$mon_time_ed = $data->MON_TIME_ED;

			if ($mon_time_st != "" || $mon_time_ed != "") {

				if (($mon_time_st != "" && $mon_time_ed == "") ||
				$mon_time_st == "" && $mon_time_ed != "") {

					$message = vsprintf($this->MMessage->getOneMessage('HRDE010008'), array("月曜日"));
					$this->errors['Check'][] =  $message;

				} else {

					$errFlg = false;

					if (preg_match("/[0-9]{4}/",$mon_time_st) == false) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010005'), array("月曜日の出社","4"));
						$this->errors['Check'][] =  $message;
					} else if ((int)substr($mon_time_st,2,2) >= 60 || (int)substr($mon_time_st,0,2) >= 25 ||
							((int)substr($mon_time_st,2,2) != 0 && (int)substr($mon_time_st,0,2) == 24)) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010009'), array("月曜日の出社"));
						$this->errors['Check'][] =  $message;
					}

					if (preg_match("/[0-9]{4}/",$mon_time_ed) == false) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010005'), array("月曜日の退社","4"));
						$this->errors['Check'][] =  $message;
					} else if ((int)substr($mon_time_ed,2,2) >= 60 || (int)substr($mon_time_ed,0,2) >= 25 ||
							((int)substr($mon_time_ed,2,2) != 0 && (int)substr($mon_time_ed,0,2) == 24)) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010009'), array("月曜日の退社"));
						$this->errors['Check'][] =  $message;
					}

					if ($errFlg == false && intval($mon_time_st) >= intval($mon_time_ed)) {
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010011'), array("月曜日"));
						$this->errors['Check'][] =  $message;
					}
				}

			}

			//火曜日存在チェック
			$tue_time_st = $data->TUE_TIME_ST;
			$tue_time_ed = $data->TUE_TIME_ED;

			if ($tue_time_st != "" || $tue_time_ed != "") {

				if (($tue_time_st != "" && $tue_time_ed == "") ||
				$tue_time_st == "" && $tue_time_ed != "") {

					$message = vsprintf($this->MMessage->getOneMessage('HRDE010008'), array("火曜日"));
					$this->errors['Check'][] =  $message;

				} else {

					$errFlg = false;

					if (preg_match("/[0-9]{4}/",$tue_time_st) == false) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010005'), array("火曜日の出社","4"));
						$this->errors['Check'][] =  $message;
					} else if ((int)substr($tue_time_st,2,2) >= 60 || (int)substr($tue_time_st,0,2) >= 25 ||
							((int)substr($tue_time_st,2,2) != 0 && (int)substr($tue_time_st,0,2) == 24)) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010009'), array("火曜日の出社"));
						$this->errors['Check'][] =  $message;
					}

					if (preg_match("/[0-9]{4}/",$tue_time_ed) == false) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010005'), array("火曜日の退社","4"));
						$this->errors['Check'][] =  $message;
					} else if ((int)substr($tue_time_ed,2,2) >= 60 || (int)substr($tue_time_ed,0,2) >= 25 ||
							((int)substr($tue_time_ed,2,2) != 0 && (int)substr($tue_time_ed,0,2) == 24)) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010009'), array("火曜日の退社"));
						$this->errors['Check'][] =  $message;
					}

					if ($errFlg == false && intval($tue_time_st) >= intval($tue_time_ed)) {
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010011'), array("火曜日"));
						$this->errors['Check'][] =  $message;
					}
				}

			}

			//水曜日存在チェック
			$wed_time_st = $data->WED_TIME_ST;
			$wed_time_ed = $data->WED_TIME_ED;

			if ($wed_time_st != "" || $wed_time_ed != "") {

				if (($wed_time_st != "" && $wed_time_ed == "") ||
					$wed_time_st == "" && $wed_time_ed != "") {

					$message = vsprintf($this->MMessage->getOneMessage('HRDE010008'), array("水曜日"));
					$this->errors['Check'][] =  $message;

				} else {

					$errFlg = false;

					if (preg_match("/[0-9]{4}/",$wed_time_st) == false) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010005'), array("水曜日の出社","4"));
						$this->errors['Check'][] =  $message;
					} else if ((int)substr($wed_time_st,2,2) >= 60 || (int)substr($wed_time_st,0,2) >= 25 ||
							((int)substr($wed_time_st,2,2) != 0 && (int)substr($wed_time_st,0,2) == 24)) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010009'), array("水曜日の出社"));
						$this->errors['Check'][] =  $message;
					}

					if (preg_match("/[0-9]{4}/",$wed_time_ed) == false) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010005'), array("水曜日の退社","4"));
						$this->errors['Check'][] =  $message;
					} else if ((int)substr($wed_time_ed,2,2) >= 60 || (int)substr($wed_time_ed,0,2) >= 25 ||
							((int)substr($wed_time_ed,2,2) != 0 && (int)substr($wed_time_ed,0,2) == 24)) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010009'), array("水曜日の退社"));
						$this->errors['Check'][] =  $message;
					}


					if ($errFlg == false && intval($wed_time_st) >= intval($wed_time_ed)) {
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010011'), array("水曜日"));
						$this->errors['Check'][] =  $message;
					}
				}



			}

			//木曜日存在チェック
			$thu_time_st = $data->THU_TIME_ST;
			$thu_time_ed = $data->THU_TIME_ED;

			if ($thu_time_st != "" || $thu_time_ed != "") {

				if (($thu_time_st != "" && $thu_time_ed == "") ||
				$thu_time_st == "" && $thu_time_ed != "") {

					$message = vsprintf($this->MMessage->getOneMessage('HRDE010008'), array("木曜日"));
					$this->errors['Check'][] =  $message;

				} else {

					$errFlg = false;

					if (preg_match("/[0-9]{4}/",$thu_time_st) == false) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010005'), array("木曜日の出社","4"));
						$this->errors['Check'][] =  $message;
					} else if ((int)substr($thu_time_st,2,2) >= 60 || (int)substr($thu_time_st,0,2) >= 25 ||
							((int)substr($thu_time_st,2,2) != 0 && (int)substr($thu_time_st,0,2) == 24)) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010009'), array("木曜日の出社"));
						$this->errors['Check'][] =  $message;
					}

					if (preg_match("/[0-9]{4}/",$thu_time_ed) == false) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010005'), array("木曜日の退社","4"));
						$this->errors['Check'][] =  $message;
					} else if ((int)substr($thu_time_ed,2,2) >= 60 || (int)substr($thu_time_ed,0,2) >= 25 ||
							((int)substr($thu_time_ed,2,2) != 0 && (int)substr($thu_time_ed,0,2) == 24)) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010009'), array("木曜日の退社"));
						$this->errors['Check'][] =  $message;
					}

					if ($errFlg == false && intval($thu_time_st) >= intval($thu_time_ed)) {
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010011'), array("木曜日"));
						$this->errors['Check'][] =  $message;
					}
				}

			}

			//金曜日存在チェック
			$fri_time_st = $data->FRI_TIME_ST;
			$fri_time_ed = $data->FRI_TIME_ED;

			if ($fri_time_st != "" || $fri_time_ed != "") {

				if (($fri_time_st != "" && $fri_time_ed == "") ||
					$fri_time_st == "" && $fri_time_ed != "") {
					$message = vsprintf($this->MMessage->getOneMessage('HRDE010008'), array("金曜日"));
					$this->errors['Check'][] =  $message;

				} else {

					$errFlg = false;

					if (preg_match("/[0-9]{4}/",$fri_time_st) == false) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010005'), array("金曜日の出社","4"));
						$this->errors['Check'][] =  $message;
					} else if ((int)substr($fri_time_st,2,2) >= 60 || (int)substr($fri_time_st,0,2) >= 25 ||
							((int)substr($fri_time_st,2,2) != 0 && (int)substr($fri_time_st,0,2) == 24)) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010009'), array("木曜日の出社"));
						$this->errors['Check'][] =  $message;
					}

					if (preg_match("/[0-9]{4}/",$fri_time_ed) == false) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010005'), array("金曜日の退社","4"));
						$this->errors['Check'][] =  $message;
					} else if ((int)substr($fri_time_ed,2,2) >= 60 || (int)substr($fri_time_ed,0,2) >= 25 ||
							((int)substr($fri_time_ed,2,2) != 0 && (int)substr($fri_time_ed,0,2) == 24)) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010009'), array("木曜日の退社"));
						$this->errors['Check'][] =  $message;
					}

					if ($errFlg ==  false && intval($fri_time_st) >= intval($fri_time_ed)) {
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010011'), array("金曜日"));
						$this->errors['Check'][] =  $message;
					}
				}
			}

			//土曜日存在チェック
			$sat_time_st = $data->SAT_TIME_ST;
			$sat_time_ed = $data->SAT_TIME_ED;

			if ($sat_time_st != "" || $sat_time_ed != "") {

				if (($sat_time_st != "" && $sat_time_ed == "") ||
				$sat_time_st == "" && $sat_time_ed != "") {
					$errFlg = true;
					$message = vsprintf($this->MMessage->getOneMessage('HRDE010008'), array("土曜日"));
					$this->errors['Check'][] =  $message;

				} else {

					$errFlg = false;

					if (preg_match("/[0-9]{4}/",$sat_time_st) == false) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010005'), array("土曜日の出社","4"));
						$this->errors['Check'][] =  $message;
					} else if ((int)substr($sat_time_st,2,2) >= 60) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010009'), array("土曜日の出社"));
						$this->errors['Check'][] =  $message;
					}

					if (preg_match("/[0-9]{4}/",$sat_time_ed) == false) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010005'), array("土曜日の退社","4"));
						$this->errors['Check'][] =  $message;
					} else if ((int)substr($sat_time_ed,2,2) >= 60) {
						$errFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010009'), array("土曜日の退社"));
						$this->errors['Check'][] =  $message;
					}

					if ($errFlg == false && intval($sat_time_st) >= intval($sat_time_ed)) {
						$message = vsprintf($this->MMessage->getOneMessage('HRDE010011'), array("土曜日"));
						$this->errors['Check'][] =  $message;
					}
				}

			}

			$ymdSyugyoStErrFlg = false;
			$ymdSyugyoEdErrFlg = false;

			//就業開始日
			$ymd_syugyo_st = $data->YMD_SYUGYO_ST;

			if ($ymd_syugyo_st == "") {
				$ymdSyugyoStErrFlg = true;
				$message = vsprintf($this->MMessage->getOneMessage('HRDE010007'), array("就業開始日"));
				$this->errors['Check'][] =  $message;
			} else if (preg_match("/[0-9]{8}/",$ymd_syugyo_st) == false) {
				$ymdSyugyoStErrFlg = true;
				$message = vsprintf($this->MMessage->getOneMessage('CMNE000002'), array("就業開始日"));
				$this->errors['Check'][] =  $message;
			} else {

				$year = substr($ymd_syugyo_st, 0,4);
				$month = substr($ymd_syugyo_st, 4,2);
				$day = substr($ymd_syugyo_st, 6,2);

				if (checkdate($month, $day, $year) == false) {
					$ymdSyugyoStErrFlg = true;
					$message = vsprintf($this->MMessage->getOneMessage('CMNE000002'), array("就業終了日"));
					$this->errors['Check'][] =  $message;
				}
			}


			//就業終了日
			$ymd_syugyo_ed = $data->YMD_SYUGYO_ED;

			if ($ymd_syugyo_ed != "") {

				if (preg_match("/[0-9]{8}/",$ymd_syugyo_ed) == false) {
					$ymdSyugyoEdErrFlg = true;
					$message = vsprintf($this->MMessage->getOneMessage('CMNE000002'), array("就業終了日"));
					$this->errors['Check'][] =  $message;
				} else {

					$year = substr($ymd_syugyo_ed, 0,4);
					$month = substr($ymd_syugyo_ed, 4,2);
					$day = substr($ymd_syugyo_ed, 6,2);

					if (checkdate($month, $day, $year) == false) {
						$ymdSyugyoEdErrFlg = true;
						$message = vsprintf($this->MMessage->getOneMessage('CMNE000002'), array("就業終了日"));
						$this->errors['Check'][] =  $message;
					}
				}
			}

			if ($ymdSyugyoStErrFlg == false
					&& $ymdSyugyoEdErrFlg == false
					&& $ymd_syugyo_st != ""
					&& $ymd_syugyo_ed != "") {

				if (intval($ymd_syugyo_st) >= intval($ymd_syugyo_ed)) {
					$message = $this->MMessage->getOneMessage('HRDE010012');
					$this->errors['Check'][] =  $message;
				}
			}

			//出勤日数上限
			$syukin_nisu_max = $data->SYUKIN_NISU_MAX;

			if (preg_match("/[0-9]*/",$syukin_nisu_max) == false) {

				$message = vsprintf($this->MMessage->getOneMessage('CMNE000002'), array("出勤日数上限"));
				$this->errors['Check'][] =  $message;
			}


			//出勤時間上限
			$syukin_time_max = $data->SYUKIN_TIME_MAX;

			if ($syukin_time_max != "") {

				if (preg_match("/[0-9]*/",$syukin_time_max) == false) {

					$message = vsprintf($this->MMessage->getOneMessage('CMNE000002'), array("出勤時間上限"));
					$this->errors['Check'][] =  $message;
				}

			}

			//賃金上限
			$chingin_max = $data->CHINGIN_MAX;

			if ($chingin_max != "") {

				if (preg_match("/[0-9]+/",$chingin_max) == false) {

					$message = vsprintf($this->MMessage->getOneMessage('CMNE000002'), array("賃金上限"));
					$this->errors['Check'][] =  $message;
				}

			}

			if (!empty($this->errors)) {
				return false;
			}

			return true;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "HRD010", $e->getMessage());
		}

		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}

	public function setShinkiData($staff_cd,$data) {

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
			$sql .= "'HRD',";
			$sql .= "'010',";
			$sql .= "1";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'HRD010　排他制御オン');

			try {

				$pdo2 = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
				$pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$pdo2->beginTransaction();
				$count = 1;

				$dai_ninusi_cd = '';
				$dai_sosiki_cd = '';

				$kyotenArray = $data->KYOTEN;

				foreach($kyotenArray as $array) {

					$cellsArray = $array->cells;
					$dataArray = $array->data;

					if ($cellsArray[0] == true) {
						$dai_ninusi_cd = $dataArray[0];
						$dai_sosiki_cd = $dataArray[1];

						break;
					}

				}

				//スタッフ基本情報
				$sql = "CALL P_HRD010_SET_M_STAFF_KIHON(";
				$sql .= "'" . $staff_cd . "',";
				$sql .= "'" . $data->STAFF_CD . "',";
				$sql .= "'" . Sanitize::clean($data->STAFF_NM) . "',";
				$sql .= "'" . Sanitize::clean($data->PASSWORD) . "',";
				$sql .= "'" . $data->IC_NO  . "',";
				$sql .= "'" . $data->KBN_SEX  . "',";
				if($data->YMD_BIRTH=="") $sql .="NULL,"; else $sql .= "'" . $data->YMD_BIRTH  . "',";
				$sql .= "'" . $data->HAKEN_KAISYA_CD  . "',";
				$sql .= "'" . $dai_ninusi_cd . "',";
				$sql .= "'" . $dai_sosiki_cd . "',";
				$sql .= "'" . $data->KBN_PST . "',";
				$sql .= "'" . $data->KBN_KEIYAKU  . "',";
				$sql .= "'" . $data->KBN_PICK  . "',";
				$sql .= "'" . $data->RANK  . "',";
				if($data->KIN_SYOTEI=="") $sql .="NULL,"; else $sql .= "" . $data->KIN_SYOTEI  . ",";
				if($data->KIN_JIKANGAI=="") $sql .="NULL,"; else $sql .= "" . $data->KIN_JIKANGAI  . ",";
				if($data->KIN_H_KST=="") $sql .="NULL,"; else $sql .= "" . $data->KIN_H_KST  . ",";
				if($data->KIN_KST=="") $sql .="NULL,"; else $sql .= "" . $data->KIN_KST  . ",";
				if($data->KIN_H_KST_SNY=="") $sql .="NULL,"; else $sql .= "" . $data->KIN_H_KST_SNY  . ",";
				if($data->KIN_SNY=="") $sql .="NULL,"; else $sql .= "" . $data->KIN_SNY  . ",";
				if($data->SUN_TIME_ST=="") $sql .="NULL,"; else $sql .= "'" . $data->SUN_TIME_ST . "00',";
				if($data->SUN_TIME_ED=="") $sql .="NULL,"; else $sql .= "'" . $data->SUN_TIME_ED  . "00',";
				if($data->SUN_BIKO=="") $sql .="NULL,"; else $sql .= "'" . Sanitize::clean($data->SUN_BIKO)  . "',";
				if($data->MON_TIME_ST=="") $sql .="NULL,"; else $sql .= "'" . $data->MON_TIME_ST  . "00',";
				if($data->MON_TIME_ED=="") $sql .="NULL,"; else $sql .= "'" . $data->MON_TIME_ED  . "00',";
				if($data->MON_BIKO=="") $sql .="NULL,"; else $sql .= "'" . Sanitize::clean($data->MON_BIKO)  . "',";
				if($data->TUE_TIME_ST=="") $sql .="NULL,"; else $sql .= "'" . $data->TUE_TIME_ST  . "00',";
				if($data->TUE_TIME_ED=="") $sql .="NULL,"; else $sql .= "'" . $data->TUE_TIME_ED  . "00',";
				if($data->TUE_BIKO=="") $sql .="NULL,"; else $sql .= "'" . Sanitize::clean($data->TUE_BIKO)  . "',";
				if($data->WED_TIME_ST=="") $sql .="NULL,"; else $sql .= "'" . $data->WED_TIME_ST  . "00',";
				if($data->WED_TIME_ED=="") $sql .="NULL,"; else $sql .= "'" . $data->WED_TIME_ED  . "00',";
				if($data->WED_BIKO=="") $sql .="NULL,"; else $sql .= "'" . Sanitize::clean($data->WED_BIKO)  . "',";
				if($data->THU_TIME_ST=="") $sql .="NULL,"; else $sql .= "'" . $data->THU_TIME_ST  . "00',";
				if($data->THU_TIME_ED=="") $sql .="NULL,"; else $sql .= "'" . $data->THU_TIME_ED  . "00',";
				if($data->SUN_BIKO=="") $sql .="NULL,"; else $sql .= "'" . Sanitize::clean($data->THU_BIKO)  . "',";
				if($data->FRI_TIME_ST=="") $sql .="NULL,"; else $sql .= "'" . $data->FRI_TIME_ST  . "00',";
				if($data->FRI_TIME_ED=="") $sql .="NULL,"; else $sql .= "'" . $data->FRI_TIME_ED  . "00',";
				if($data->FRI_BIKO=="") $sql .="NULL,"; else $sql .= "'" . Sanitize::clean($data->FRI_BIKO)  . "',";
				if($data->SAT_TIME_ST=="") $sql .="NULL,"; else $sql .= "'" . $data->SAT_TIME_ST  . "00',";
				if($data->SAT_TIME_ED=="") $sql .="NULL,"; else $sql .= "'" . $data->SAT_TIME_ED  . "00',";
				if($data->SAT_BIKO=="") $sql .="NULL,"; else $sql .= "'" . Sanitize::clean($data->SAT_BIKO)  . "',";
				if($data->YMD_SYUGYO_ST=="") $sql .="NULL,"; else $sql .= "'" . $data->YMD_SYUGYO_ST  . "',";
				if($data->YMD_SYUGYO_ED=="") $sql .="NULL,"; else $sql .= "'" . $data->YMD_SYUGYO_ED  . "',";
				if($data->SYUKIN_NISU_MAX=="") $sql .="NULL,"; else $sql .= "" . $data->SYUKIN_NISU_MAX  . ",";
				if($data->SYUKIN_TIME_MAX=="") $sql .="NULL,"; else $sql .= "" . $data->SYUKIN_TIME_MAX  . ",";
				if($data->CHINGIN_MAX=="") $sql .="NULL,"; else $sql .= "" . $data->CHINGIN_MAX  . ",";
				if($data->TUIKI_JIKO=="") $sql .="NULL,"; else $sql .= "'" . Sanitize::clean($data->TUIKI_JIKO)  . "',";
				$sql .= "0,";
				$sql .= "0,";
				$sql .= "@return_cd";
				$sql .= ")";

				$this->execWithPDOLog($pdo2,$sql, 'スタッフ基本情報　登録');

				$sql = "SELECT";
				$sql .= " @return_cd";

				$this->queryWithPDOLog($stmt,$pdo2,$sql, 'スタッフ基本情報　登録');

				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$return_cd = $result["@return_cd"];

				if ($return_cd == "1") {

					throw new Exception("スタッフ基本情報登録　例外発生");
				}

				$kyotenArray = $data->KYOTEN;

				foreach($kyotenArray as $array) {

					$cellsArray = $array->cells;
					$dataArray = $array->data;

					if ($cellsArray[1] == true) {

						$ninusi_cd = $dataArray[0];
						$sosiki_cd = $dataArray[1];

						$sql = "CALL P_HRD010_SET_M_STAFF_KYOTEN(";
						$sql .= "'" . $staff_cd . "',";
						$sql .= "'" . $data->STAFF_CD . "',";
						$sql .= "'" . $ninusi_cd . "',";
						$sql .= "'" . $sosiki_cd . "',";
						$sql .= "@return_cd";
						$sql .= ")";

						$this->execWithPDOLog($pdo2,$sql, 'スタッフ拠点　登録');

						$sql = "SELECT";
						$sql .= " @return_cd";

						$this->queryWithPDOLog($stmt,$pdo2,$sql, 'スタッフ拠点　登録');

						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$return_cd = $result["@return_cd"];

						if ($return_cd == "1") {

							throw new Exception("スタッフ拠点登録　例外発生");
						}
					}

				}

				$yusenzoneArray = $data->YUSEN_ZONE;

				foreach($yusenzoneArray as $array) {

					$cellsArray = $array->cells;
					$dataArray = $array->data;

					if ($cellsArray[0] == true) {

						$zone = $dataArray[0];

						//スタッフ拠点
						$sql = "CALL P_HRD010_SET_M_STAFF_YUSEN_ZONE(";
						$sql .= "'" . $staff_cd . "',";
						$sql .= "'" . $data->STAFF_CD . "',";
						$sql .= "'" . $zone . "',";
						$sql .= "@return_cd";
						$sql .= ")";

						$this->execWithPDOLog($pdo2,$sql, 'スタッフ優先ゾーン　登録');

						$sql = "SELECT";
						$sql .= " @return_cd";

						$this->queryWithPDOLog($stmt,$pdo2,$sql, 'スタッフ優先ゾーン　登録');

						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$return_cd = $result["@return_cd"];

						if ($return_cd == "1") {

							throw new Exception("スタッフ優先ゾーン登録　例外発生");
						}
					}

				}

				$sql = "CALL P_HRD010_SET_M_STAFF_IMG(";
				$sql .= "'" . $staff_cd . "',";
				$sql .= "'" . $data->STAFF_CD . "',";
				if($data->IMAGE=="") $sql .="NULL,"; else $sql .= "'" . addslashes(base64_decode($data->IMAGE)) . "',";
				$sql .= "@return_cd";
				$sql .= ")";

				$pdo2->exec($sql);

				$sql = "SELECT";
				$sql .= " @return_cd";

				$this->queryWithPDOLog($stmt,$pdo2,$sql, 'スタッフイメージ　登録');

				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$return_cd = $result["@return_cd"];

				if ($return_cd == "1") {

					throw new Exception("スタッフイメージ登録　例外発生");
				}

				$pdo2->commit();

			} catch (Exception $e) {

				$this->printLog("fatal", "例外発生", "HRD010", $e->getMessage());
				$pdo2->rollBack();
			}

			$pdo2 = null;

			//排他解除

			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'HRD',";
			$sql .= "'010',";
			$sql .= "0";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'HRD010　排他制御オフ');
			$pdo = null;

			if ($return_cd == "1") {

				//例外の場合、例外メッセージ
				$message = $this->MMessage->getOneMessage('CMNE000107');
				$this->errors['Check'][] =  $message;
				return false;
			}

			return true;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "HRD010", $e->getMessage());
			$pdo = null;
		}

		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}

	public function getStaff($staff_cd) {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql  = "SELECT";
			$sql .= "  STAFF_CD,";
			$sql .= "  STAFF_NM,";
			$sql .= "  PASSWORD,";
			$sql .= "  IC_NO,";
			$sql .= "  KBN_SEX,";
			$sql .= "  YMD_BIRTH,";
			$sql .= "  HAKEN_KAISYA_CD,";
			$sql .= "  DAI_NINUSI_CD,";
			$sql .= "  DAI_SOSIKI_CD,";
			$sql .= "  KBN_PST,";
			$sql .= "  KBN_KEIYAKU,";
			$sql .= "  KBN_PICK,";
			$sql .= "  RANK,";
			$sql .= "  KIN_SYOTEI,";
			$sql .= "  KIN_JIKANGAI,";
			$sql .= "  KIN_H_KST,";
			$sql .= "  KIN_KST,";
			$sql .= "  KIN_H_KST_SNY,";
			$sql .= "  KIN_SNY,";
			$sql .= "  SUN_TIME_ST,";
			$sql .= "  SUN_TIME_ED,";
			$sql .= "  SUN_BIKO,";
			$sql .= "  MON_TIME_ST,";
			$sql .= "  MON_TIME_ED,";
			$sql .= "  MON_BIKO,";
			$sql .= "  TUE_TIME_ST,";
			$sql .= "  TUE_TIME_ED,";
			$sql .= "  TUE_BIKO,";
			$sql .= "  WED_TIME_ST,";
			$sql .= "  WED_TIME_ED,";
			$sql .= "  WED_BIKO,";
			$sql .= "  THU_TIME_ST,";
			$sql .= "  THU_TIME_ED,";
			$sql .= "  THU_BIKO,";
			$sql .= "  FRI_TIME_ST,";
			$sql .= "  FRI_TIME_ED,";
			$sql .= "  FRI_BIKO,";
			$sql .= "  SAT_TIME_ST,";
			$sql .= "  SAT_TIME_ED,";
			$sql .= "  SAT_BIKO,";
			$sql .= "  YMD_SYUGYO_ST,";
			$sql .= "  YMD_SYUGYO_ED,";
			$sql .= "  SYUKIN_NISU_MAX,";
			$sql .= "  SYUKIN_TIME_MAX,";
			$sql .= "  CHINGIN_MAX,";
			$sql .= "  TUIKI_JIKO,";
			$sql .= "  DEL_FLG";
			$sql .= " FROM";
			$sql .= "  M_STAFF_KIHON";
			$sql .= " WHERE";
			$sql .= "  STAFF_CD='" . $staff_cd . "'";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"スタッフマスタ　取得");

			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			$array = array(
					"STAFF_CD" => $result['STAFF_CD'],
					"STAFF_NM" => $result['STAFF_NM'],
					"PASSWORD" => $result['PASSWORD'],
					"IC_NO" => $result['IC_NO'],
					"KBN_SEX" => $result['KBN_SEX'],
					"YMD_BIRTH" => $result['YMD_BIRTH'],
					"HAKEN_KAISYA_CD" => $result['HAKEN_KAISYA_CD'],
					"DAI_NINUSI_CD" => $result['DAI_NINUSI_CD'],
					"DAI_SOSIKI_CD" => $result['DAI_SOSIKI_CD'],
					"KBN_PST" => $result['KBN_PST'],
					"KBN_KEIYAKU" => $result['KBN_KEIYAKU'],
					"KBN_PICK" => $result['KBN_PICK'],
					"RANK" => $result['RANK'],
					"KIN_SYOTEI" => $result['KIN_SYOTEI'],
					"KIN_JIKANGAI" => $result['KIN_JIKANGAI'],
					"KIN_H_KST" => $result['KIN_H_KST'],
					"KIN_KST" => $result['KIN_KST'],
					"KIN_H_KST_SNY" => $result['KIN_H_KST_SNY'],
					"KIN_SNY" => $result['KIN_SNY'],
					"SUN_TIME_ST" => $result['SUN_TIME_ST'],
					"SUN_TIME_ED" => $result['SUN_TIME_ED'],
					"SUN_BIKO" => $result['SUN_BIKO'],
					"MON_TIME_ST" => $result['MON_TIME_ST'],
					"MON_TIME_ED" => $result['MON_TIME_ED'],
					"MON_BIKO" => $result['MON_BIKO'],
					"TUE_TIME_ST" => $result['TUE_TIME_ST'],
					"TUE_TIME_ED" => $result['TUE_TIME_ED'],
					"TUE_BIKO" => $result['TUE_BIKO'],
					"WED_TIME_ST" => $result['WED_TIME_ST'],
					"WED_TIME_ED" => $result['WED_TIME_ED'],
					"WED_BIKO" => $result['WED_BIKO'],
					"THU_TIME_ST" => $result['THU_TIME_ST'],
					"THU_TIME_ED" => $result['THU_TIME_ED'],
					"THU_BIKO" => $result['THU_BIKO'],
					"FRI_TIME_ST" => $result['FRI_TIME_ST'],
					"FRI_TIME_ED" => $result['FRI_TIME_ED'],
					"FRI_BIKO" => $result['FRI_BIKO'],
					"SAT_TIME_ST" => $result['SAT_TIME_ST'],
					"SAT_TIME_ED" => $result['SAT_TIME_ED'],
					"SAT_BIKO" => $result['SAT_BIKO'],
					"YMD_SYUGYO_ST" => $result['YMD_SYUGYO_ST'],
					"YMD_SYUGYO_ED" => $result['YMD_SYUGYO_ED'],
					"SYUKIN_NISU_MAX" => $result['SYUKIN_NISU_MAX'],
					"SYUKIN_TIME_MAX" => $result['SYUKIN_TIME_MAX'],
					"CHINGIN_MAX" => $result['CHINGIN_MAX'],
					"TUIKI_JIKO" => $result['TUIKI_JIKO'],
					"DEL_FLG" => $result['DEL_FLG']
			);

			$pdo = null;

			return $array;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "HRD010", $e->getMessage());
			$pdo = null;
		}


		return array();
	}

	public function getStaffYusenZone($staff_cd) {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql  = "SELECT";
			$sql .= "  STAFF_CD,";
			$sql .= "  ZONE";
			$sql .= " FROM";
			$sql .= "  M_STAFF_YUSEN_ZONE";
			$sql .= " WHERE";
			$sql .= "  STAFF_CD='" . $staff_cd . "'";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"スタッフ優先ゾーンマスタ　取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
						"STAFF_CD" => $result['STAFF_CD'],
						"ZONE" => $result['ZONE']
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

	public function getStaffKyoten($staff_cd) {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql  = "SELECT";
			$sql .= "  STAFF_CD,";
			$sql .= "  NINUSI_CD,";
			$sql .= "  SOSIKI_CD";
			$sql .= " FROM";
			$sql .= "  M_STAFF_KYOTEN";
			$sql .= " WHERE";
			$sql .= "  STAFF_CD='" . $staff_cd . "'";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"スタッフ拠点マスタ取得　取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
						"STAFF_CD" => $result['STAFF_CD'],
						"NINUSI_CD" => $result['NINUSI_CD'],
						"SOSIKI_CD" => $result['SOSIKI_CD']
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

	public function getStaffImage($staff_cd) {

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql  = "SELECT";
			$sql .= "  STAFF_CD,";
			$sql .= "  PHOTO_IMG";
			$sql .= " FROM";
			$sql .= "  M_STAFF_IMG";
			$sql .= " WHERE";
			$sql .= "  STAFF_CD='" . $staff_cd . "'";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"スタッフ拠点マスタ取得　取得");

			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			$array = array(
					"STAFF_CD" => $result['STAFF_CD'],
					"PHOTO_IMG" => base64_encode($result['PHOTO_IMG'])
			);

			$pdo = null;

			return $array;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "HRD010", $e->getMessage());
			$pdo = null;
		}

		return array();
	}

	public function setKoshinData($staff_cd,$data) {

		$pdo = null;
		$pdo2 = null;

		try{

			//排他制御
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'HRD',";
			$sql .= "'010',";
			$sql .= "1";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'HRD010　排他制御オン');


			try {

				$pdo2 = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
				$pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$pdo2->beginTransaction();
				$count = 1;

				$dai_ninusi_cd = '';
				$dai_sosiki_cd = '';

				$kyotenArray = $data->KYOTEN;

				foreach($kyotenArray as $array) {

					$cellsArray = $array->cells;
					$dataArray = $array->data;

					if ($cellsArray[0] == true) {
						$dai_ninusi_cd = $dataArray[0];
						$dai_sosiki_cd = $dataArray[1];

						break;
					}

				}
				//スタッフ基本情報
				$sql = "CALL P_HRD010_SET_M_STAFF_KIHON(";
				$sql .= "'" . $staff_cd . "',";
				$sql .= "'" . $data->STAFF_CD . "',";
				$sql .= "'" . Sanitize::clean($data->STAFF_NM) . "',";
				$sql .= "'" . Sanitize::clean($data->PASSWORD) . "',";
				$sql .= "'" . $data->IC_NO  . "',";
				$sql .= "'" . $data->KBN_SEX  . "',";
				if($data->YMD_BIRTH=="") $sql .="NULL,"; else $sql .= "'" . $data->YMD_BIRTH  . "',";
				$sql .= "'" . $data->HAKEN_KAISYA_CD  . "',";
				$sql .= "'" . $dai_ninusi_cd . "',";
				$sql .= "'" . $dai_sosiki_cd . "',";
				$sql .= "'" . $data->KBN_PST . "',";
				$sql .= "'" . $data->KBN_KEIYAKU  . "',";
				$sql .= "'" . $data->KBN_PICK  . "',";
				$sql .= "'" . $data->RANK  . "',";
				if($data->KIN_SYOTEI=="") $sql .="NULL,"; else $sql .= "" . $data->KIN_SYOTEI  . ",";
				if($data->KIN_JIKANGAI=="") $sql .="NULL,"; else $sql .= "" . $data->KIN_JIKANGAI  . ",";
				if($data->KIN_H_KST=="") $sql .="NULL,"; else $sql .= "" . $data->KIN_H_KST  . ",";
				if($data->KIN_KST=="") $sql .="NULL,"; else $sql .= "" . $data->KIN_KST  . ",";
				if($data->KIN_H_KST_SNY=="") $sql .="NULL,"; else $sql .= "" . $data->KIN_H_KST_SNY  . ",";
				if($data->KIN_SNY=="") $sql .="NULL,"; else $sql .= "" . $data->KIN_SNY  . ",";
				if($data->SUN_TIME_ST=="") $sql .="NULL,"; else $sql .= "'" . $data->SUN_TIME_ST . "00',";
				if($data->SUN_TIME_ED=="") $sql .="NULL,"; else $sql .= "'" . $data->SUN_TIME_ED  . "00',";
				if($data->SUN_BIKO=="") $sql .="NULL,"; else $sql .= "'" . Sanitize::clean($data->SUN_BIKO)  . "',";
				if($data->MON_TIME_ST=="") $sql .="NULL,"; else $sql .= "'" . $data->MON_TIME_ST  . "00',";
				if($data->MON_TIME_ED=="") $sql .="NULL,"; else $sql .= "'" . $data->MON_TIME_ED  . "00',";
				if($data->MON_BIKO=="") $sql .="NULL,"; else $sql .= "'" . Sanitize::clean($data->MON_BIKO)  . "',";
				if($data->TUE_TIME_ST=="") $sql .="NULL,"; else $sql .= "'" . $data->TUE_TIME_ST  . "00',";
				if($data->TUE_TIME_ED=="") $sql .="NULL,"; else $sql .= "'" . $data->TUE_TIME_ED  . "00',";
				if($data->TUE_BIKO=="") $sql .="NULL,"; else $sql .= "'" . Sanitize::clean($data->TUE_BIKO)  . "',";
				if($data->WED_TIME_ST=="") $sql .="NULL,"; else $sql .= "'" . $data->WED_TIME_ST  . "00',";
				if($data->WED_TIME_ED=="") $sql .="NULL,"; else $sql .= "'" . $data->WED_TIME_ED  . "00',";
				if($data->WED_BIKO=="") $sql .="NULL,"; else $sql .= "'" . Sanitize::clean($data->WED_BIKO)  . "',";
				if($data->THU_TIME_ST=="") $sql .="NULL,"; else $sql .= "'" . $data->THU_TIME_ST  . "00',";
				if($data->THU_TIME_ED=="") $sql .="NULL,"; else $sql .= "'" . $data->THU_TIME_ED  . "00',";
				if($data->SUN_BIKO=="") $sql .="NULL,"; else $sql .= "'" . Sanitize::clean($data->THU_BIKO)  . "',";
				if($data->FRI_TIME_ST=="") $sql .="NULL,"; else $sql .= "'" . $data->FRI_TIME_ST  . "00',";
				if($data->FRI_TIME_ED=="") $sql .="NULL,"; else $sql .= "'" . $data->FRI_TIME_ED  . "00',";
				if($data->FRI_BIKO=="") $sql .="NULL,"; else $sql .= "'" . Sanitize::clean($data->FRI_BIKO)  . "',";
				if($data->SAT_TIME_ST=="") $sql .="NULL,"; else $sql .= "'" . $data->SAT_TIME_ST  . "00',";
				if($data->SAT_TIME_ED=="") $sql .="NULL,"; else $sql .= "'" . $data->SAT_TIME_ED  . "00',";
				if($data->SAT_BIKO=="") $sql .="NULL,"; else $sql .= "'" . Sanitize::clean($data->SAT_BIKO)  . "',";
				if($data->YMD_SYUGYO_ST=="") $sql .="NULL,"; else $sql .= "'" . $data->YMD_SYUGYO_ST  . "',";
				if($data->YMD_SYUGYO_ED=="") $sql .="NULL,"; else $sql .= "'" . $data->YMD_SYUGYO_ED  . "',";
				if($data->SYUKIN_NISU_MAX=="") $sql .="NULL,"; else $sql .= "" . $data->SYUKIN_NISU_MAX  . ",";
				if($data->SYUKIN_TIME_MAX=="") $sql .="NULL,"; else $sql .= "" . $data->SYUKIN_TIME_MAX  . ",";
				if($data->CHINGIN_MAX=="") $sql .="NULL,"; else $sql .= "" . $data->CHINGIN_MAX  . ",";
				if($data->TUIKI_JIKO=="") $sql .="NULL,"; else $sql .= "'" . Sanitize::clean($data->TUIKI_JIKO)  . "',";
				if($data->DEL_FLG=="") $sql .="NULL,"; else $sql .= "'" . $data->DEL_FLG . "',";
				$sql .= "1,";
				$sql .= "@return_cd";
				$sql .= ")";

				$this->execWithPDOLog($pdo2,$sql, 'スタッフ基本情報　更新');

				$sql = "SELECT";
				$sql .= " @return_cd";

				$this->queryWithPDOLog($stmt,$pdo2,$sql, 'スタッフ基本情報　更新');

				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$return_cd = $result["@return_cd"];

				if ($return_cd == "1") {

					throw new Exception('スタッフ基本情報更新 例外発生');
				}

				//スタッフ拠点
				$sql = "CALL P_HRD010_DELETE_M_STAFF_KYOTEN(";
				$sql .= "'" . $data->STAFF_CD . "',";
				$sql .= "@return_cd";
				$sql .= ")";

				$this->execWithPDOLog($pdo2,$sql, 'スタッフ拠点　削除');

				$sql = "SELECT";
				$sql .= " @return_cd";

				$this->queryWithPDOLog($stmt,$pdo2,$sql, 'スタッフ拠点　削除');

				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$return_cd = $result["@return_cd"];

				if ($return_cd == "1") {

					throw new Exception();
				}

				$kyotenArray = $data->KYOTEN;

				foreach($kyotenArray as $array) {

					$cellsArray = $array->cells;
					$dataArray = $array->data;

					if ($cellsArray[1] == true) {

						$ninusi_cd = $dataArray[0];
						$sosiki_cd = $dataArray[1];

						$sql = "CALL P_HRD010_SET_M_STAFF_KYOTEN(";
						$sql .= "'" . $staff_cd . "',";
						$sql .= "'" . $data->STAFF_CD . "',";
						$sql .= "'" . $ninusi_cd . "',";
						$sql .= "'" . $sosiki_cd . "',";
						$sql .= "@return_cd";
						$sql .= ")";

						$this->execWithPDOLog($pdo2,$sql, 'スタッフ拠点　登録');

						$sql = "SELECT";
						$sql .= " @return_cd";

						$this->queryWithPDOLog($stmt,$pdo2,$sql, 'スタッフ拠点　登録');

						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$return_cd = $result["@return_cd"];

						if ($return_cd == "1") {

							throw new Exception('スタッフ拠点登録　例外発生');
						}
					}

				}

				//優先ゾーン
				$sql = "CALL P_HRD010_DELETE_M_STAFF_YUSEN_ZONE(";
				$sql .= "'" . $data->STAFF_CD . "',";
				$sql .= "@return_cd";
				$sql .= ")";

				$this->execWithPDOLog($pdo2,$sql, 'スタッフ優先ゾーン　削除');

				$sql = "SELECT";
				$sql .= " @return_cd";

				$this->queryWithPDOLog($stmt,$pdo2,$sql, 'スタッフ優先ゾーン　削除');

				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$return_cd = $result["@return_cd"];

				if ($return_cd == "1") {

					throw new Exception('スタッフ優先ゾーン削除　例外発生');
				}

				$yusenzoneArray = $data->YUSEN_ZONE;

				foreach($yusenzoneArray as $array) {

					$cellsArray = $array->cells;
					$dataArray = $array->data;

					if ($cellsArray[0] == true) {

						$zone = $dataArray[0];

						//スタッフ拠点
						$sql = "CALL P_HRD010_SET_M_STAFF_YUSEN_ZONE(";
						$sql .= "'" . $staff_cd . "',";
						$sql .= "'" . $data->STAFF_CD . "',";
						$sql .= "'" . $zone . "',";
						$sql .= "@return_cd";
						$sql .= ")";

						$this->execWithPDOLog($pdo2,$sql, 'スタッフ優先ゾーン　登録');

						$sql = "SELECT";
						$sql .= " @return_cd";

						$this->queryWithPDOLog($stmt,$pdo2,$sql, 'スタッフ優先ゾーン　登録');

						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$return_cd = $result["@return_cd"];

						if ($return_cd == "1") {

							throw new Exception('スタッフ優先ゾーン登録　例外発生');
						}
					}

				}

				//イメージ
				$sql = "CALL P_HRD010_DELETE_M_STAFF_IMG(";
				$sql .= "'" . $data->STAFF_CD . "',";
				$sql .= "@return_cd";
				$sql .= ")";

				$this->execWithPDOLog($pdo2,$sql, 'スタッフイメージ　削除');

				$sql = "CALL P_HRD010_SET_M_STAFF_IMG(";
				$sql .= "'" . $staff_cd . "',";
				$sql .= "'" . $data->STAFF_CD . "',";
				if($data->IMAGE=="") $sql .="NULL,"; else $sql .= "'" . addslashes(base64_decode($data->IMAGE)) . "',";
				$sql .= "@return_cd";
				$sql .= ")";

				$pdo2->exec($sql);

				$sql = "SELECT";
				$sql .= " @return_cd";

				$this->queryWithPDOLog($stmt,$pdo2,$sql, 'スタッフイメージ　登録');

				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$return_cd = $result["@return_cd"];

				if ($return_cd == "1") {

					throw new Exception('スタッフイメージ登録　例外発生');
				}

				$pdo2->commit();

			} catch (Exception $e) {

				$this->printLog("fatal", "例外発生", "HRD010", $e->getMessage());
				$pdo2->rollBack();
			}

			$pdo2 = null;

			//排他解除
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'HRD',";
			$sql .= "'010',";
			$sql .= "0";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'HRD010　排他制御オフ');
			$pdo = null;

			if ($return_cd == "1") {

				//例外の場合、例外メッセージ
				$message = $this->MMessage->getOneMessage('CMNE000107');
				$this->errors['Check'][] =  $message;
				return false;
			}

			return true;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "HRD010", $e->getMessage());
			$pdo = null;
		}

		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}

	/**
	 * 品質データの検証を行う
	 * @param unknown $rowList
	 * @return boolean
	 */
	public function validateHinshitsuData($rowList) {

		$message = "";
		$count = 1;			//行
		$count4 = 0;		//更新対象件数取得
		foreach($rowList as $rowData) {

			$hassei_date = $rowData->hassei_date;
			$kbn_hinshitsu_naiyo_cd = $rowData->kbn_hinshitsu_naiyo_cd;
			$dai_bunrui_cd = $rowData->dai_bunrui_cd;
			$chu_bunrui_cd = $rowData->chu_bunrui_cd;
			$sai_bunrui_cd = $rowData->sai_bunrui_cd;
			$s_kanri_no = $rowData->s_kanri_no;
			$staff_nm = $rowData->staff_nm;
			$t_kin = $rowData->t_kin;
			$t_hoho = $rowData->t_hoho;
			$changed = $rowData->changed;

			/*
			 * ◆ $changed の説明
			*
			* 0：何もなし
			* 1：更新
			* 2：新規(追加)
			* 3：何もなし⇒削除
			* 4：更新⇒削除
			* 5：新規⇒削除
			*/
			//更新対象件数取得
			if ($changed != '0' and $changed != '5') {
				$count4++;
			}

			//追加と修正のみ
			if ($changed == '1' or  $changed == '2') {
				//必須チェック

				// 発生日付
				if (!Validation::notEmpty($hassei_date)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '発生日付'));
					$this->errors['Check'][] = $message;
				}

				// 品質内容
				if (!Validation::notEmpty($kbn_hinshitsu_naiyo_cd)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '品質内容'));
					$this->errors['Check'][] = $message;
				}

				// 担当業務
				if (!Validation::notEmpty($dai_bunrui_cd)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '担当業務'));
					$this->errors['Check'][] = $message;
				}

				//桁数チェック

				// 集計管理No
				if (!Validation::maxLength($s_kanri_no, 10)) {
					$message = vsprintf($this->MMessage->getOneMessage('HRDE010014'), array($count, '集計管理No', '10'));
					$this->errors['Check'][] = $message;
				}

				// 対応者
				if (!Validation::maxLength($staff_nm, 10)) {
					$message = vsprintf($this->MMessage->getOneMessage('HRDE010014'), array($count, '対応者', '10'));
					$this->errors['Check'][] = $message;
				}

				// 対応金額
				if (!Validation::maxLength($t_kin, 12)) {
					$message = vsprintf($this->MMessage->getOneMessage('HRDE010014'), array($count, '対応金額', '12'));
					$this->errors['Check'][] = $message;
				}

				// 対応方法
				if (!Validation::maxLength($t_hoho, 500)) {
					$message = vsprintf($this->MMessage->getOneMessage('HRDE010014'), array($count, '対応方法', '500'));
					$this->errors['Check'][] = $message;
				}

				// 日付チェック

				// 発生日付
				if (Validation::notEmpty($hassei_date) && !$this->MCommon->isDateYMD($hassei_date)) {
					$message = vsprintf($this->MMessage->getOneMessage('HRDE010016'), array($count, "発生日付"));
					$this->errors['Check'][] = $message;
				}

				//数字チェック

				// 集計管理No
				if (Validation::notEmpty($s_kanri_no) && !Validation::numeric($s_kanri_no)) {
					$message = vsprintf($this->MMessage->getOneMessage('HRDE010015'), array($count, '集計管理No'));
					$this->errors['Check'][] =   $message;
				}

				// 対応金額
				if (Validation::notEmpty($t_kin) && !Validation::numeric($t_kin)) {
					$message = vsprintf($this->MMessage->getOneMessage('HRDE010015'), array($count, '対応金額'));
					$this->errors['Check'][] =   $message;
				}

			}

			$count++;

		}

		if (count($this->errors) != 0) {
			return false;
		}


		if ($count4 == 0) {
			$message = "更新データがありません。";
			$this->errors['Check'][] = $message;
			return false;
		}
	}

	/**
	 * 品質情報を更新する
	 * @param $rowList
	 * @param $staff_cd 更新対象のスタッフコード
	 * @param $self_staff_cd 更新者（ログイン中のスタッフ）
	 * @return boolean
	 */
	public function setHinshitsuData($rowList, $target_staff_cd, $self_staff_cd) {

		$pdo = null;
		$pdo2 = null;

		try{
			//排他制御
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $self_staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'HRD',";
			$sql .= "'010',";
			$sql .= "1";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'HRD010 排他制御オン');

			try {

				$pdo2 = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
				$pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$pdo2->beginTransaction();
				$count = 0;

				//行ずつ処理
				foreach($rowList as $rowData) {

					$count++;

					$pre_hassei_date = $rowData->pre_hassei_date;
					$hassei_date = $rowData->hassei_date;
					$gyo_no = $rowData->gyo_no;
					$kbn_hinshitsu_naiyo_cd = $rowData->kbn_hinshitsu_naiyo_cd;
					$dai_bunrui_cd = $rowData->dai_bunrui_cd;
					$chu_bunrui_cd = $rowData->chu_bunrui_cd;
					$sai_bunrui_cd = $rowData->sai_bunrui_cd;
					$s_kanri_no = $rowData->s_kanri_no;
					$staff_nm = $rowData->staff_nm;
					$t_kin = $rowData->t_kin;
					$t_hoho = Sanitize::clean($rowData->t_hoho);
					$changed = $rowData->changed;

					//　０：なし　　　　　　何もしない
					//　１：修正　　　　　UPDATE
					//　２：追加　　　　　INSERT
					//　３：削除　　　　　DELETE
					//　４：修正→削除　　DELETE
					//　５：追加→削除　　何もしない

					if ($changed == 1) {
						//更新の場合
						//スタッフ品質情報を更新する
						$sql = "CALL P_HRD010_UPD_STAFF_HINSHITSU(";
						$sql .= "'" . $target_staff_cd . "',";
						$sql .= "'" . $hassei_date . "',";
						$sql .= "'" . $gyo_no . "',";
						$sql .= "'" . $kbn_hinshitsu_naiyo_cd . "',";
						$sql .= "'" . $dai_bunrui_cd . "',";
						$sql .= "'" . $chu_bunrui_cd . "',";
						$sql .= "'" . $sai_bunrui_cd . "',";
						$sql .= Validation::notEmpty($s_kanri_no) ? "'" . $s_kanri_no . "'," : "NULL,";
						$sql .= Validation::notEmpty($staff_nm) ? "'" . Sanitize::clean($staff_nm) . "'," : "NULL,";
						$sql .= Validation::notEmpty($t_hoho) ? "'" . str_replace("\\n", "\n" , Sanitize::clean($t_hoho)) . "'," : "NULL,";
						$sql .= Validation::notEmpty($t_kin) ? "'" . $t_kin . "'," : "NULL,";
						$sql .= "'" . $self_staff_cd . "',";
						$sql .= "'" . $pre_hassei_date . "'";
						$sql .= ")";

						$this->execWithPDOLog($pdo2,$sql, 'スタッフ品質情報　更新');

					} else if ($changed == 2) {
						//新規の場合
						//スタッフ品質情報を追加する
						$sql = "CALL P_HRD010_INS_STAFF_HINSHITSU(";
						$sql .= "'" . $target_staff_cd . "',";
						$sql .= "'" . $hassei_date . "',";
						$sql .= "'" . $kbn_hinshitsu_naiyo_cd . "',";
						$sql .= "'" . $dai_bunrui_cd . "',";
						$sql .= "'" . $chu_bunrui_cd . "',";
						$sql .= "'" . $sai_bunrui_cd . "',";
						$sql .= Validation::notEmpty($s_kanri_no) ? "'" . $s_kanri_no . "'," : "NULL,";
						$sql .= Validation::notEmpty($staff_nm) ? "'" . Sanitize::clean($staff_nm) . "'," : "NULL,";
						$sql .= Validation::notEmpty($t_hoho) ? "'" . str_replace("\\n", "\n" , Sanitize::clean($t_hoho)) . "'," : "NULL,";
						$sql .= Validation::notEmpty($t_kin) ? "'" . $t_kin . "'," : "NULL,";
						$sql .= "'" . $self_staff_cd . "'";
						$sql .= ")";

						$this->execWithPDOLog($pdo2,$sql, 'スタッフ品質情報　追加');

					} else if ($changed == 3 || $changed == 4) {
						// 削除の場合
						//スタッフ品質情報を削除する
						$sql = "CALL P_HRD010_DEL_STAFF_HINSHITSU(";
						$sql .= "'" . $target_staff_cd . "',";
						$sql .= "'" . $pre_hassei_date . "',";
						$sql .= "'" . $gyo_no . "'";
						$sql .= ")";

						$this->execWithPDOLog($pdo2,$sql, 'スタッフ品質情報　削除');
					}
				}

				$pdo2->commit();

			} catch (Exception $e) {
				$this->printLog("fatal", "例外発生", "HRD010", $e->getMessage());
				$pdo2->rollBack();
			}

			$pdo2 = null;

			//排他制御オフ
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $self_staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'HRD',";
			$sql .= "'010',";
			$sql .= "0";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'HRD010 排他制御オフ');

			$pdo = null;

			return true;

		} catch (Exception $e) {
			$this->printLog("fatal", "例外発生", "HRD010", $e->getMessage());
			$pdo = null;
		}

		//例外発生の場合、エラー
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;

	}

	/**
	 * スタッフ品質情報取得
	 * @access   public
	 * @param $staff_cd スタッフコード
	 * @return   スタッフ品質情報
	 */
	public function getStaffHinsituList($staff_cd) {
		// 取得カラム設定
		$fields = '*';

		// 検索条件設定

		$conditionVal = array();
		$conditionKey = '';

		// 検索条件設定
		$condition  = "NINUSI_CD = :ninusi_cd";
		$condition  .= " AND SOSIKI_CD = :sosiki_cd";
		$condition  .= " AND STAFF_CD = :staff_cd";

		// 条件値指定
		$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
		$conditionVal['sosiki_cd'] = $this->_sosiki_cd;
		$conditionVal['staff_cd'] = $staff_cd;

		// スタッフ品質情報取得
		$sql  = 'SELECT ';
		$sql .= "$fields ";
		$sql .= 'FROM V_HRD010_T_STAFF_HINSITU_LST ';
		$sql .= 'WHERE ';
		$sql .= $condition;
		$sql .= $conditionKey;

		$data = $this->query($sql, $conditionVal, false);
		$data = $this->editData($data);

		return $data;
	}

	/**
	 * スタッフカルテ情報取得
	 * <pre>
	 * 評価日付が指定されいない場合、
	 * 登録中データの中で最新の評価日付のものを取得します
	 * </pre>
	 *
	 * @access   public
	 * @param $staff_cd スタッフコード
	 * @param $hyoka_date 評価月（yyyymmを指定）
	 * @return   スタッフ品質情報
	 */
	public function getStaffKarteInfo($staff_cd, $hyoka_date = "") {
		$pdo = null;

		try{
			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$common = $this->MCommon;

			// 取得カラム設定
			$fields = '*';

			// 検索条件設定
			$condition  = "STAFF_CD = " . $common->encloseString($staff_cd);

			// ソート設定
			$sort = "";
			if (empty($hyoka_date)) {
				$sort = " ORDER BY YMD_HYOKA DESC";
			} else {
				$condition  .= " AND YMD_HYOKA = " . $common->encloseString($hyoka_date . "01");
			}

			$sql  = 'SELECT ';
			$sql .= "$fields ";
			$sql .= 'FROM T_STAFF_KARTE ';
			$sql .= 'WHERE ';
			$sql .= $condition;
			$sql .= $sort;

			$this->queryWithPDOLog($stmt, $pdo, $sql,"スタッフカルテ情報　取得");

			$result = array();
			while ($entity = $stmt->fetch(PDO::FETCH_ASSOC)) {
				array_push($result, $entity);
			}

			$pdo = null;

			return $result;

		} catch (Exception $e) {
			$this->printLog("fatal", "例外発生", "HRD010", $e->getMessage());
			$pdo = null;
		}

		return array();
	}

	/**
	 * スタッフ基本能力情報取得
	 *
	 * @access   public
	 * @param $staff_cd スタッフコード
	 * @param $hyoka_date 評価日付
	 * @return   スタッフ基本能力情報
	 */
	public function getStaffSkillInfo($staff_cd, $hyoka_date) {

		$pdo = null;

		try{
			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$common = $this->MCommon;

			// 取得カラム設定
			$fields = '*';

			// 検索条件設定
			$condition  = "STAFF_CD = " . $common->encloseString($staff_cd);
			$condition  .= " AND YMD_HYOKA = " . $common->encloseString($hyoka_date);

			$sql  = 'SELECT ';
			$sql .= "$fields ";
			$sql .= 'FROM T_STAFF_SKILL ';
			$sql .= 'WHERE ';
			$sql .= $condition;

			$this->queryWithPDOLog($stmt, $pdo, $sql,"スタッフ基本能力情報　取得");

			$result = array();

			while ($entity = $stmt->fetch(PDO::FETCH_OBJ)) {
				$result[$entity->KMK_NM_CD] = $entity->KMK_LV_CD;
			}

			$pdo = null;

			return $result;

		} catch (Exception $e) {
			$this->printLog("fatal", "例外発生", "HRD010", $e->getMessage());
			$pdo = null;
		}

		return array();
	}

	/**
	 * スタッフ特殊能力情報取得
	 *
	 * @access   public
	 * @param $staff_cd スタッフコード
	 * @param $hyoka_date 評価日付
	 * @return   スタッフ特殊能力情報
	 */
	public function getStaffSkillSPInfo($staff_cd, $hyoka_date) {

		$pdo = null;

		try{
			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$common = $this->MCommon;

			// 取得カラム設定
			$fields = '*';

			// 検索条件設定
			$condition  = "STAFF_CD = " . $common->encloseString($staff_cd);
			$condition  .= " AND YMD_HYOKA = " . $common->encloseString($hyoka_date);

			$sql  = 'SELECT ';
			$sql .= "$fields ";
			$sql .= 'FROM T_STAFF_SKILL_SP ';
			$sql .= 'WHERE ';
			$sql .= $condition;

			$this->queryWithPDOLog($stmt, $pdo, $sql,"スタッフ特殊能力情報　取得");

			$result = array();

			while ($entity = $stmt->fetch(PDO::FETCH_OBJ)) {
				$result[$entity->KMK_NM_CD] = $entity->KMK_LV_CD;
			}

			$pdo = null;

			return $result;

		} catch (Exception $e) {
			$this->printLog("fatal", "例外発生", "HRD010", $e->getMessage());
			$pdo = null;
		}

		return array();
	}

	/**
	 * カルテデータの検証を行う
	 * @param unknown $data
	 * @return boolean
	 */
	public function validateKarteData($data) {

		$message = "";

		$hyoka_date = $data->hyoka_date_input;
		$tuyomi = $data->tuyomi;
		$yowami = $data->yowami;
		$comment = $data->comment;
		$baseSkillArray = $data->baseSkillArray;
		$SPSkillArray = $data->SPSkillArray;
		$mokuhyo1 = $data->mokuhyo1;
		$mokuhyo2 = $data->mokuhyo2;
		$mokuhyo3 = $data->mokuhyo3;
		$mokuhyo4 = $data->mokuhyo4;
		$mokuhyo5 = $data->mokuhyo5;
		$mokuhyo6 = $data->mokuhyo6;
		$mokuhyo7 = $data->mokuhyo7;
		$mokuhyo8 = $data->mokuhyo8;
		$mokuhyo9 = $data->mokuhyo9;
		$mokuhyo10 = $data->mokuhyo10;
		$mokuhyo11 = $data->mokuhyo11;
		$mokuhyo12 = $data->mokuhyo12;
		$tasseido1 = $data->tasseido1;
		$tasseido2 = $data->tasseido2;
		$tasseido3 = $data->tasseido3;
		$tasseido4 = $data->tasseido4;
		$tasseido5 = $data->tasseido5;
		$tasseido6 = $data->tasseido6;
		$tasseido7 = $data->tasseido7;
		$tasseido8 = $data->tasseido8;
		$tasseido9 = $data->tasseido9;
		$tasseido10 = $data->tasseido10;
		$tasseido11 = $data->tasseido11;
		$tasseido12 = $data->tasseido12;
		$staff_cd = $data->staff_cd;
		$timestamp = $data->timestamp;

		//必須チェック

		// 評価月
		if (!Validation::notEmpty($hyoka_date)) {
			$message = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array('評価月'));
			$this->errors['Check'][] = $message;
		}

		// 基本能力
		foreach($baseSkillArray as $baseSkill) {
			if (!Validation::notEmpty($baseSkill->kmk_nm_cd)) {
				$message = vsprintf($this->MMessage->getOneMessage('HRDE010007'), array('基本能力'));
				$this->errors['Check'][] = $message;
				break;
			}
		}

		// 特殊能力
		foreach($SPSkillArray as $SPSkill) {
			if (!Validation::notEmpty($SPSkill->kmk_nm_cd)) {
				$message = vsprintf($this->MMessage->getOneMessage('HRDE010007'), array('特殊能力'));
				$this->errors['Check'][] = $message;
				break;
			}
		}

		//桁数チェック

		// 強み
		if (!Validation::maxLength($tuyomi, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('強み', '200'));
			$this->errors['Check'][] = $message;
		}

		// 弱み
		if (!Validation::maxLength($yowami, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('弱み', '200'));
			$this->errors['Check'][] = $message;
		}

		// コメント
		if (!Validation::maxLength($comment, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('コメント', '200'));
			$this->errors['Check'][] = $message;
		}


		// １月目標
		if (!Validation::maxLength($mokuhyo1, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('１月目標', '200'));
			$this->errors['Check'][] = $message;
		}

		// ２月目標
		if (!Validation::maxLength($mokuhyo2, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('２月目標', '200'));
			$this->errors['Check'][] = $message;
		}

		// ３月目標
		if (!Validation::maxLength($mokuhyo3, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('３月目標', '200'));
			$this->errors['Check'][] = $message;
		}

		// ４月目標
		if (!Validation::maxLength($mokuhyo4, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('４月目標', '200'));
			$this->errors['Check'][] = $message;
		}

		// ５月目標
		if (!Validation::maxLength($mokuhyo5, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('５月目標', '200'));
			$this->errors['Check'][] = $message;
		}

		// ６月目標
		if (!Validation::maxLength($mokuhyo6, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('６月目標', '200'));
			$this->errors['Check'][] = $message;
		}

		// ７月目標
		if (!Validation::maxLength($mokuhyo7, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('７月目標', '200'));
			$this->errors['Check'][] = $message;
		}

		// ８月目標
		if (!Validation::maxLength($mokuhyo8, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('８月目標', '200'));
			$this->errors['Check'][] = $message;
		}

		// ９月目標
		if (!Validation::maxLength($mokuhyo9, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('９月目標', '200'));
			$this->errors['Check'][] = $message;
		}

		// １０月目標
		if (!Validation::maxLength($mokuhyo10, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('１０月目標', '200'));
			$this->errors['Check'][] = $message;
		}

		// １１月目標
		if (!Validation::maxLength($mokuhyo11, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('１１月目標', '200'));
			$this->errors['Check'][] = $message;
		}

		// １２月目標
		if (!Validation::maxLength($mokuhyo12, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('１２月目標', '200'));
			$this->errors['Check'][] = $message;
		}

		// １月達成度
		if (!Validation::maxLength($tasseido1, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('１月達成度', '200'));
			$this->errors['Check'][] = $message;
		}

		// ２月達成度
		if (!Validation::maxLength($tasseido2, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('２月達成度', '200'));
			$this->errors['Check'][] = $message;
		}

		// ３月達成度
		if (!Validation::maxLength($tasseido3, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('３月達成度', '200'));
			$this->errors['Check'][] = $message;
		}

		// ４月達成度
		if (!Validation::maxLength($tasseido4, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('４月達成度', '200'));
			$this->errors['Check'][] = $message;
		}

		// ５月達成度
		if (!Validation::maxLength($tasseido5, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('５月達成度', '200'));
			$this->errors['Check'][] = $message;
		}

		// ６月達成度
		if (!Validation::maxLength($tasseido6, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('６月達成度', '200'));
			$this->errors['Check'][] = $message;
		}

		// ７月達成度
		if (!Validation::maxLength($tasseido7, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('７月達成度', '200'));
			$this->errors['Check'][] = $message;
		}

		// ８月達成度
		if (!Validation::maxLength($tasseido8, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('８月達成度', '200'));
			$this->errors['Check'][] = $message;
		}

		// ９月達成度
		if (!Validation::maxLength($tasseido9, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('９月達成度', '200'));
			$this->errors['Check'][] = $message;
		}

		// １０月達成度
		if (!Validation::maxLength($tasseido10, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('１０月達成度', '200'));
			$this->errors['Check'][] = $message;
		}

		// １１月達成度
		if (!Validation::maxLength($tasseido11, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('１１月達成度', '200'));
			$this->errors['Check'][] = $message;
		}

		// １２月達成度
		if (!Validation::maxLength($tasseido12, 200)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010017'), array('１２月達成度', '200'));
			$this->errors['Check'][] = $message;
		}

		// 日付チェック

		// 評価月
		if (Validation::notEmpty($hyoka_date) && !$this->MCommon->isDateYM($hyoka_date)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010018'), array("評価月"));
			$this->errors['Check'][] = $message;
		}

		if (count($this->errors) != 0) {
			return false;
		}

	}

	/**
	 * カルテ情報を更新する
	 * @param $data
	 * @param $self_staff_cd 更新者（ログイン中のスタッフ）
	 * @return boolean
	 */
	public function setKarteData($data, $self_staff_cd) {

		// 排他制御用
		$pdo = null;
		// データ更新用
		$pdo2 = null;

		try{
			//排他制御
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$common = $this->MCommon;

			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $self_staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'HRD',";
			$sql .= "'010',";
			$sql .= "1";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'HRD010 排他制御オン');

			try {

				$pdo2 = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
				$pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$pdo2->beginTransaction();
				$count = 0;

				$staff_cd = $data->staff_cd;
				$hyoka_date = $data->hyoka_date_input . '01';
				$tuyomi = $data->tuyomi;
				$yowami = $data->yowami;
				$comment = $data->comment;
				$baseSkillArray = $data->baseSkillArray;
				$SPSkillArray = $data->SPSkillArray;
				$mokuhyo1 = $data->mokuhyo1;
				$mokuhyo2 = $data->mokuhyo2;
				$mokuhyo3 = $data->mokuhyo3;
				$mokuhyo4 = $data->mokuhyo4;
				$mokuhyo5 = $data->mokuhyo5;
				$mokuhyo6 = $data->mokuhyo6;
				$mokuhyo7 = $data->mokuhyo7;
				$mokuhyo8 = $data->mokuhyo8;
				$mokuhyo9 = $data->mokuhyo9;
				$mokuhyo10 = $data->mokuhyo10;
				$mokuhyo11 = $data->mokuhyo11;
				$mokuhyo12 = $data->mokuhyo12;
				$tasseido1 = $data->tasseido1;
				$tasseido2 = $data->tasseido2;
				$tasseido3 = $data->tasseido3;
				$tasseido4 = $data->tasseido4;
				$tasseido5 = $data->tasseido5;
				$tasseido6 = $data->tasseido6;
				$tasseido7 = $data->tasseido7;
				$tasseido8 = $data->tasseido8;
				$tasseido9 = $data->tasseido9;
				$tasseido10 = $data->tasseido10;
				$tasseido11 = $data->tasseido11;
				$tasseido12 = $data->tasseido12;

				// カルテ登録・更新
				$sql = "CALL P_HRD010_SET_STAFF_KARTE(";
				$sql .= $common->encloseString($staff_cd) . ", ";
				$sql .= $common->encloseString($hyoka_date) . ", ";
				$sql .= Validation::notEmpty($tuyomi) ? $common->encloseString(Sanitize::clean($tuyomi)) . "," : "NULL,";
				$sql .= Validation::notEmpty($yowami) ? $common->encloseString(Sanitize::clean($yowami)) . "," : "NULL,";
				$sql .= Validation::notEmpty($comment) ? $common->encloseString(Sanitize::clean($comment)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo1) ? $common->encloseString(Sanitize::clean($mokuhyo1)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo1) ? $common->encloseString(Sanitize::clean($tasseido1)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo2) ? $common->encloseString(Sanitize::clean($mokuhyo2)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo1) ? $common->encloseString(Sanitize::clean($tasseido2)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo3) ? $common->encloseString(Sanitize::clean($mokuhyo3)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo1) ? $common->encloseString(Sanitize::clean($tasseido3)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo4) ? $common->encloseString(Sanitize::clean($mokuhyo4)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo1) ? $common->encloseString(Sanitize::clean($tasseido4)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo5) ? $common->encloseString(Sanitize::clean($mokuhyo5)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo1) ? $common->encloseString(Sanitize::clean($tasseido5)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo6) ? $common->encloseString(Sanitize::clean($mokuhyo6)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo1) ? $common->encloseString(Sanitize::clean($tasseido6)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo7) ? $common->encloseString(Sanitize::clean($mokuhyo7)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo1) ? $common->encloseString(Sanitize::clean($tasseido7)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo8) ? $common->encloseString(Sanitize::clean($mokuhyo8)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo1) ? $common->encloseString(Sanitize::clean($tasseido8)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo9) ? $common->encloseString(Sanitize::clean($mokuhyo9)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo1) ? $common->encloseString(Sanitize::clean($tasseido9)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo10) ? $common->encloseString(Sanitize::clean($mokuhyo10)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo1) ? $common->encloseString(Sanitize::clean($tasseido10)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo11) ? $common->encloseString(Sanitize::clean($mokuhyo11)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo1) ? $common->encloseString(Sanitize::clean($tasseido11)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo12) ? $common->encloseString(Sanitize::clean($mokuhyo12)) . "," : "NULL,";
				$sql .= Validation::notEmpty($mokuhyo1) ? $common->encloseString(Sanitize::clean($tasseido12)) . "," : "NULL,";
				$sql .= $common->encloseString($self_staff_cd) . ", ";
				$sql .= "@return_cd";
				$sql .= ")";

				$this->execWithPDOLog($pdo2, $sql, 'スタッフカルテ情報　登録・更新');

				$sql = "SELECT";
				$sql .= " @return_cd";

				$this->queryWithPDOLog($stmt, $pdo2, $sql, 'スタッフカルテ情報　登録・更新結果');

				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$return_cd = $result["@return_cd"];

				if ($return_cd == 9) {
					throw new Exception();
				}

				$processing_mode = $return_cd;

				// 基本能力登録・更新
				foreach ($baseSkillArray as $baseSkill) {
					$sql = "CALL P_HRD010_SET_STAFF_SKILL(";
					$sql .= $common->encloseString($staff_cd) . ", ";
					$sql .= $common->encloseString($hyoka_date) . ", ";
					$sql .= $common->encloseString($baseSkill->kmk_nm_cd) . ",";
					$sql .= $common->encloseString($baseSkill->kmk_lv_cd) . ",";
					$sql .= $common->encloseString($self_staff_cd) . ", ";
					$sql .= $processing_mode . ",";
					$sql .= "@return_cd";
					$sql .= ")";

					$this->execWithPDOLog($pdo2, $sql, 'スタッフ基本能力情報　登録・更新');

					$sql = "SELECT";
					$sql .= " @return_cd";

					$this->queryWithPDOLog($stmt, $pdo2, $sql, 'スタッフ基本能力情報　登録・更新結果');

					$result = $stmt->fetch(PDO::FETCH_ASSOC);
					$return_cd = $result["@return_cd"];

					if ($return_cd == 9) {
						throw new Exception();
					}
				}

				// 特殊能力登録・更新
				foreach ($SPSkillArray as $SPSkill) {
					$sql = "CALL P_HRD010_SET_STAFF_SKILL_SP(";
					$sql .= $common->encloseString($staff_cd) . ", ";
					$sql .= $common->encloseString($hyoka_date) . ", ";
					$sql .= $common->encloseString($SPSkill->kmk_nm_cd) . ",";
					$sql .= $common->encloseString($SPSkill->kmk_lv_cd) . ",";
					$sql .= $common->encloseString($self_staff_cd) . ", ";
					$sql .= $processing_mode . ",";
					$sql .= "@return_cd";
					$sql .= ")";

					$this->execWithPDOLog($pdo2, $sql, 'スタッフ特殊能力情報　登録・更新');

					$sql = "SELECT";
					$sql .= " @return_cd";

					$this->queryWithPDOLog($stmt, $pdo2, $sql, 'スタッフ特殊能力情報　登録・更新結果');

					$result = $stmt->fetch(PDO::FETCH_ASSOC);
					$return_cd = $result["@return_cd"];

					if ($return_cd == 9) {
						throw new Exception();
					}
				}

				$pdo2->commit();

			} catch (Exception $e) {
				$this->printLog("fatal", "例外発生", "HRD010", $e->getMessage());
				$pdo2->rollBack();
			}

			$pdo2 = null;

			//排他制御オフ
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $self_staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'HRD',";
			$sql .= "'010',";
			$sql .= "0";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql,'HRD010 排他制御オフ');
			$pdo = null;

			if ($return_cd == 9) {
				//例外の場合、例外メッセージ
				$message = $this->MMessage->getOneMessage('CMNE000107');
				$this->errors['Check'][] =  $message;
				return false;
			}

			return true;

		} catch (Exception $e) {
			$this->printLog("fatal", "例外発生", "HRD010", $e->getMessage());
			$pdo = null;
		}

		//例外発生の場合、エラー
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;

	}

	/**
	 * スタッフ就業実績直近情報 取得
	 */
	public function getStaffSyugyoJskNear($staff_cd) {
		$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$common = $this->MCommon;

		$sql  = 'SELECT';
		$sql .= " *";
		$sql .= ' FROM V_HRD010_T_STAFF_SYUGYO_JSK_NEAR_LST ';
		$sql .= ' WHERE ';
		$sql .= ' NINUSI_CD = ' . $common->encloseString($this->_ninusi_cd);
		$sql .= ' AND SOSIKI_CD = ' . $common->encloseString($this->_sosiki_cd);
		$sql .= ' AND STAFF_CD = ' . $common->encloseString($staff_cd);

		$this->queryWithPDOLog($stmt, $pdo, $sql, 'スタッフ就業実績直近情報 取得');

		$result = array();
		while($entity = $stmt->fetch(PDO::FETCH_ASSOC)) {
			array_push($result, $entity);
		}

		return $result;
	}

	/**
	 * 累積実績一覧検索条件の検証を行う
	 * @param unknown $data
	 * @return boolean
	 */
	public function validateJissekiConditions($data) {

		if (!isset($data)) {
			return true;
		}

		$message = "";

		$periodType = $data['periodType'];
		if ($periodType == 'daily') {
			$periodFrom = $data['dailyPeriodFrom'];
			$periodTo = $data['dailyPeriodTo'];
		} else {
			$periodFrom = $data['monthlyPeriodFrom'];
			$periodTo = $data['monthlyPeriodTo'];
		}

		//必須チェック

		// 期間From
		if (!Validation::notEmpty($periodFrom)) {
			$message = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array('実績期間（自）'));
			$this->errors['Check'][] = $message;
		}

		// 期間To
		if (!Validation::notEmpty($periodTo)) {
			$message = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array('実績期間（至）'));
			$this->errors['Check'][] = $message;
		}

		// 日付チェック

		// 期間From
		if (Validation::notEmpty($periodFrom)) {
			if ($periodType == 'daily') {
				if (!$this->MCommon->isDateYMD($periodFrom)) {
					$message = vsprintf($this->MMessage->getOneMessage('HRDE010019'), array('実績期間（自）'));
					$this->errors['Check'][] = $message;
				}
			} else {
				if (!$this->MCommon->isDateYM($periodFrom)) {
					$message = vsprintf($this->MMessage->getOneMessage('HRDE010018'), array('実績期間（自）'));
					$this->errors['Check'][] = $message;
				}
			}
		}

		// 期間To
		if (Validation::notEmpty($periodTo)) {
			if ($periodType == 'daily') {
				if (!$this->MCommon->isDateYMD($periodTo)) {
					$message = vsprintf($this->MMessage->getOneMessage('HRDE010019'), array('実績期間（至）'));
					$this->errors['Check'][] = $message;
				}
			} else {
				if (!$this->MCommon->isDateYM($periodTo)) {
					$message = vsprintf($this->MMessage->getOneMessage('HRDE010018'), array('実績期間（至）'));
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
		if ($this->MCommon->comparisonDate($periodFrom, '>', $periodTo)) {
			$message = vsprintf($this->MMessage->getOneMessage('HRDE010020'), array('実績期間（至）', '実績期間（自）'));
			$this->errors['Check'][] = $message;
		}

		// 日別の時のみ、期間が１年以内であること
		if ($periodType == 'daily') {
			// Fromの1年後を計算
			$oneYearLater = date('Ymd', strtotime("$periodFrom +1 year"));
			if ($this->MCommon->comparisonDate($oneYearLater, '<', $periodTo)) {
				$message = vsprintf($this->MMessage->getOneMessage('HRDE010021'), array('実績期間'));
				$this->errors['Check'][] = $message;
			}
		}

		if (count($this->errors) != 0) {
			return false;
		}

		return true;
	}

	/**
	 * スタッフ就業実績を取得（月別 OR 日別）
	 *
	 * @access   public
	 * @param $staff_cd スタッフコード
	 * @param $conditions 検索条件
	 * @return   スタッフ就業実績情報
	 */
	public function getStaffSyugyoJsk($staff_cd, $conditions) {

		$pdo = null;
		$result = array();

		try{
			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$common = $this->MCommon;

			// 検索条件
			$periodType = $conditions['periodType'];
			if ($periodType == 'daily') {
				$periodFrom = $conditions['dailyPeriodFrom'];
				$periodTo = $conditions['dailyPeriodTo'];
			} else {
				$periodFrom = $conditions['monthlyPeriodFrom'];
				$periodTo = $conditions['monthlyPeriodTo'];
			}
			$koteiCd = $conditions['kotei'];

			if (!empty($koteiCd)) {
				$koteiCdArray = explode('_', $koteiCd);

				if(count($koteiCd) >= 1) $koteiDaiCd = $koteiCdArray[0];
				if(count($koteiCd) >= 2) $koteiCyuCd = $koteiCdArray[1];
				if(count($koteiCd) >= 3) $koteiSaiCd = $koteiCdArray[2];
			}

			$sql  = 'SELECT';
			$sql .= " *";
			if ($periodType == 'daily') {
				$sql .= ' FROM V_HRD010_T_STAFF_SYUGYO_JSK_DAY_LST ';
			} else {
				$sql .= ' FROM V_HRD010_T_STAFF_SYUGYO_JSK_MONTH_LST ';
			}
			$sql .= ' WHERE ';
			$sql .= ' NINUSI_CD = ' . $common->encloseString($this->_ninusi_cd);
			$sql .= ' AND SOSIKI_CD = ' . $common->encloseString($this->_sosiki_cd);
			$sql .= ' AND STAFF_CD = ' . $common->encloseString($staff_cd);
			if ($periodType == 'daily') {
				$sql .= " AND YMD BETWEEN {$common->encloseString($periodFrom)} AND {$common->encloseString($periodTo)} ";
			} else {
				// 月別のYMはintのため、シングルクォートで囲まない
				$sql .= " AND YM BETWEEN $periodFrom AND $periodTo ";
			}
			if (isset($koteiDaiCd)) $sql .= ' AND BNRI_DAI_CD = ' . $common->encloseString($koteiDaiCd);
			if (isset($koteiCyuCd)) $sql .= ' AND BNRI_CYU_CD = ' . $common->encloseString($koteiCyuCd);
			if (isset($koteiSaiCd)) $sql .= ' AND BNRI_SAI_CD = ' . $common->encloseString($koteiSaiCd);

			$this->queryWithPDOLog($stmt, $pdo, $sql,"スタッフ就業実績情報　取得");

			while ($entity = $stmt->fetch(PDO::FETCH_ASSOC)) {
				array_push($result, $entity);
			}

			$pdo = null;

			return $result;

		} catch (Exception $e) {
			$this->printLog("fatal", "例外発生", "HRD010", $e->getMessage());
			$pdo = null;
		}

		return $result;
	}

	/**
	 * 情報編集処理
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
}
?>
