<?php
/**
 * WEX080Model
 *
 * 日時締更新
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class WEX080Model extends DCMSAppModel{
	
	public $name = 'WEX080Model';
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
	 * データ取得
	 * @access   public
	 * @return   工程管理データ
	 */
	public function getData() {
		
		$pdo = null;
		
		try{
		
			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql  = "SELECT ";
			$sql .= "   A.DT_EXEC,";
			$sql .= "   A.EXEC_STAFF_CD,";
			$sql .= "   B.STAFF_NM,";
			$sql .= "   A.YMD_SIME,";
			$sql .= "   C.MEI_1 AS RESULT_NM,";
			$sql .= "   A.RESULT,";
			$sql .= "   A.STAFF_CNT";
			$sql .= " FROM";
			$sql .= "   T_SIME_PROC_RIREKI A";
			$sql .= "     LEFT JOIN M_STAFF_KIHON B ON";
			$sql .= "       A.EXEC_STAFF_CD = B.STAFF_CD";
			$sql .= "     LEFT JOIN M_MEISYO C ON";
			$sql .= "       A.NINUSI_CD = C.NINUSI_CD AND";
			$sql .= "       A.SOSIKI_CD = C.SOSIKI_CD AND";
			$sql .= "       A.RESULT = C.MEI_CD AND";
			$sql .= "       C.MEI_KBN = '26'";
			$sql .= " WHERE";
			$sql .= "   A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   A.SUB_SYSTEM_ID='WEX'";
			$sql .= " ORDER BY";
			$sql .= "  A.DT_EXEC DESC";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql,"締処理履歴情報　取得");
			
			$arrayList = array();
			
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
	
				$array = array(
				    "DT_EXEC" => $result['DT_EXEC'],
					"EXEC_STAFF_CD" => $result['EXEC_STAFF_CD'],
					"STAFF_NM" => $result['STAFF_NM'],
					"YMD_SIME" => $result['YMD_SIME'],
					"RESULT_NM" => $result['RESULT_NM'],
					"RESULT" => $result['RESULT'],
					"STAFF_CNT" => $result['STAFF_CNT'],
				);
				
				$arrayList[] = $array;
			}
			
			$pdo = null;

			return $arrayList;

		} catch (Exception $e) {
			
			$this->printLog("fatal", "例外発生", "WEX080", $e->getMessage());
			$pdo = null;
		}
		

		return array();
	}
	
	/**
	 * データ更新
	 * @access   public
	 * @return   正常/異常
	 */
	public function setData($date,$staff_cd,$updateFlg) {
		
		$pdo = null;
		$pdo2 = null;
		
		try{
		
			$tableData = null;
		
			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WEX',";
			$sql .= "'080',";
			$sql .= "1";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql,'WEX080　排他制御オン');
			
			$pdo->beginTransaction();
			
		
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
			
			//日付取得
			$year = substr($date, 0,4);
			$month = substr($date, 4,2);
			$day = substr($date, 6,2);
			
			$ymd_sime = $year ."-" . $month . "-" . $day;
			$ym = $year . $month;
			
			$tmpDate = $year . "-" . $month . "-" . $day . " " . $dt_syugyo_st_base;
			$dt = new DateTime($tmpDate);
			$date1 = $dt->format('Y-m-d H:i:s');
			$dt->add(new DateInterval("P1D"));
			$date2 = $dt->format('Y-m-d H:i:s');
			
			//売り上げデータ作成
			
			//1入力データ取得
			$sql  = "SELECT ";
			$sql .= "   A.NINUSI_CD,";
			$sql .= "   A.SOSIKI_CD,";
			$sql .= "   A.YMD,";
			$sql .= "   A.BNRI_DAI_CD,";
			$sql .= "   A.BNRI_CYU_CD,";
			$sql .= "   A.BNRI_SAI_CD,";
			$sql .= "   A.TIME_KOTEI_ST,";
			$sql .= "   A.TIME_KOTEI_ED,";
			$sql .= "   A.ABC_COST,";
			$sql .= "   A.BUTURYO,";
			$sql .= "   B.KEIYAKU_BUTURYO_FLG";
			$sql .= " FROM";
			$sql .= "   T_ABC_DB_DAY A";
			$sql .= "    LEFT JOIN M_BNRI_SAI B ON";
			$sql .= "      A.NINUSI_CD=B.NINUSI_CD AND";
			$sql .= "      A.SOSIKI_CD=B.SOSIKI_CD AND";
			$sql .= "      A.BNRI_DAI_CD=B.BNRI_DAI_CD AND";
			$sql .= "      A.BNRI_CYU_CD=B.BNRI_CYU_CD AND";
			$sql .= "      A.BNRI_SAI_CD=B.BNRI_SAI_CD";
			$sql .= " WHERE";
			$sql .= "   A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   A.YMD='" . $ymd_sime . "'";
			$sql .= " ORDER BY";
			$sql .= "   A.NINUSI_CD,";
			$sql .= "   A.SOSIKI_CD,";
			$sql .= "   A.YMD,";
			$sql .= "   A.BNRI_DAI_CD,";
			$sql .= "   A.BNRI_CYU_CD,";
			$sql .= "   A.BNRI_SAI_CD";
			
	
			
			$this->queryWithPDOLog($stmt,$pdo,$sql,"ABCデーターベース日別　取得");
			
			$hibetuArray = array();
			
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
	
				$array = array(
				    "NINUSI_CD" => $result['NINUSI_CD'],
					"SOSIKI_CD" => $result['SOSIKI_CD'],
					"YMD" => $result['YMD'],
					"BNRI_DAI_CD" => $result['BNRI_DAI_CD'],
					"BNRI_CYU_CD" => $result['BNRI_CYU_CD'],
					"BNRI_SAI_CD" => $result['BNRI_SAI_CD'],
					"ABC_COST" => $result['ABC_COST'],
					"BUTURYO" => $result['BUTURYO'],
					"KEIYAKU_BUTURYO_FLG" => $result['KEIYAKU_BUTURYO_FLG'],
				);
				
				$hibetuArray[] = $array;
			}
			
			$uriageArray = $this->MCommon->getUriage($hibetuArray);

			//売上算出データ更新処理
			
			//売上算出データ削除
			$sql = "CALL P_WEX080_DELETE_CALC_DATA(";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "@return_cd";
			$sql .= ")";
			
			$this->execWithPDOLog($pdo,$sql, '売上算出データ　削除');
			
			$sql = "SELECT";
			$sql .= " @return_cd";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, '売上算出データ　削除');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];
			
			if ($return_cd == "1") {
			
				throw new Exception('売上算出データ削除　例外発生');
			}
			
			//売上算出データ登録
			foreach($uriageArray as $array) {
			
				$sql = "CALL P_WEX080_SET_CALC_DATA(";
				$sql .= "'" . $staff_cd . "',";
				$sql .= "'" . $array['NINUSI_CD'] . "',";
				$sql .= "'" . $array['SOSIKI_CD'] . "',";
				$sql .= "'" . $ymd_sime . "',";
				$sql .= "'" . $array['BNRI_DAI_CD'] . "',";
				$sql .= "'" . $array['BNRI_CYU_CD'] . "',";
				$sql .= "'" . $array['BNRI_SAI_CD'] . "',";
				$sql .= ($array['ABC_COST'] != "" ?  $array['ABC_COST'] . "," : "0,");
				$sql .= ($array['BUTURYO'] != "" ?  $array['BUTURYO'] . "," : "0,");
				$sql .= ($array['KBN_URIAGE'] != "" ? "'" . $array['KBN_URIAGE'] . "'," : "NULL,");
				$sql .= ($array['KEIYAKU_BNRI_DAI_CD'] != "" ? "'" . $array['KEIYAKU_BNRI_DAI_CD'] . "'," : "NULL,");
				$sql .= ($array['KEIYAKU_BNRI_CYU_CD'] != "" ? "'" . $array['KEIYAKU_BNRI_CYU_CD'] . "'," : "NULL,");
				$sql .= ($array['KEIYAKU_BUTURYO'] != "" ?  $array['KEIYAKU_BUTURYO'] . "," : "0,");
				$sql .= ($array['KEIYAKU_URIAGE'] != "" ?  $array['KEIYAKU_URIAGE'] . "," : "0,");
				$sql .= ($array['ANBUN'] != "" ?  $array['ANBUN'] . "," : "0,");
				$sql .= ($array['URIAGE'] != "" ?  $array['URIAGE'] . "," : "0,");
				$sql .= "@return_cd";
				$sql .= ")";
			
				$this->execWithPDOLog($pdo,$sql, '売上算出データ　追加');
				
				$sql = "SELECT";
				$sql .= " @return_cd";
				
				$this->queryWithPDOLog($stmt,$pdo,$sql, '売上算出データ　追加');
				
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$return_cd = $result["@return_cd"];
				
				if ($return_cd == "1") {
				
					throw new Exception('売上算出データ追加　例外発生');
				}
			}
		
			
			
			//ABCデータベーススタッフ別情報更新
			$sql = "CALL P_WEX080_SET_ABC_DB_STAFF(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "@return_cd";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'ABCデータベーススタッフ別　更新');
			
			$sql = "SELECT";
			$sql .= " @return_cd";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ABCデータベーススタッフ別　更新');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];
			
			if ($return_cd == "1") {
			
				throw new Exception('ABCデータベーススタッフ別更新　例外発生');
			}
			
			//ABCデーターベース日別情報更新
			$sql = "CALL P_WEX080_SET_ABC_DB_HIBETU(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "@return_cd";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'ABCデータベース日別　更新');
			
			$sql = "SELECT";
			$sql .= " @return_cd";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ABCデータベース日別　更新');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];
			
			if ($return_cd == "1") {
			
				throw new Exception('ABCデータベース日別更新　例外発生');
			}
			
			//ABCデーターベース日別情報更新
			$sql = "CALL P_WEX080_SET_ABC_DB_HIBETU_URIAGE(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "@return_cd";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'ABCデータベース日別売上　更新');
			
			$sql = "SELECT";
			$sql .= " @return_cd";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ABCデータベース日別売上　更新');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];
			
			if ($return_cd == "1") {
			
				throw new Exception('ABCデータベース日別売上更新　例外発生');
			}
			
			//ABCデーターベーススタッフ別物量情報更新
			$sql = "CALL P_WEX080_SET_ABC_DB_STAFF_BUTURYO(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "@return_cd";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'ABCデータベーススタッフ別物量　更新');
			
			$sql = "SELECT";
			$sql .= " @return_cd";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ABCデータベーススタッフ別物量　更新');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];
			
			if ($return_cd == "1") {
			
				throw new Exception('ABCデータベーススタッフ別物量　例外発生');
			}
			
			//ABCデータベース月別情報作成
			$sql = "CALL P_WEX080_SET_ABC_DB_MONTH(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "'" . $ym . "',";
			$sql .= "@return_cd";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'ABCデータベース月別情報　登録');
			
			$sql = "SELECT";
			$sql .= " @return_cd";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'ABCデータベース月別情報　登録');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];
			
			if ($return_cd == "1") {
			
				throw new Exception('ABCデータベース月別情報登録　例外発生');
			}
			
			//就業実績日別情報
			$sql = "CALL P_WEX080_SET_STAFF_SYUGYO(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "@return_cd";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, '就業実績情報　更新');
			
			$sql = "SELECT";
			$sql .= " @return_cd";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, '就業実績情報　更新');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];
			
			if ($return_cd == "1") {
			
				throw new Exception('就業実績情報更新　例外発生');
			}
			
			//就業実績情報展開処理呼び出し
			$sql = "CALL P_WPO050_SET_TENKAI(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "@return_cd";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, '就業実績情報展開処理　登録');
			
			$sql = "SELECT";
			$sql .= " @return_cd";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, '就業実績情報展開処理　登録');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];
			
			if ($return_cd == "1") {
			
				throw new Exception('就業実績情報展開処理登録　例外発生');
			}
			
			//スタッフ就業実績情報順位設定呼び出し
			$sql = "CALL P_WPO050_SET_RANK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "@return_cd";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, '就業実績情報順位設定　登録');
			
			$sql = "SELECT";
			$sql .= " @return_cd";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, '就業実績情報順位設定　登録');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];
			
			if ($return_cd == "1") {
			
				throw new Exception('就業情報順位設定登録　例外発生');
			}
			
			//LSP実績ヘッダ情報作成
			$sql = "CALL P_WEX080_SET_LSP_JISSEKI_H(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "@return_cd";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'LSP実績ヘッダ情報　登録');
			
			$sql = "SELECT";
			$sql .= " @return_cd";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'LSP実績ヘッダ情報　登録');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];
			
			if ($return_cd == "1") {
			
				throw new Exception('LSP実績ヘッダ情報登録　例外発生');
			}
			
			//LSP実績明細情報作成
			$sql = "CALL P_WEX080_SET_LSP_JISSEKI_D(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $ymd_sime . "',";
			$sql .= "@return_cd";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'LSP実績明細情報　登録');
			
			$sql = "SELECT";
			$sql .= " @return_cd";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'LSP実績明細情報　登録');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];
			
			if ($return_cd == "1") {
			
				throw new Exception('LSP実績明細情報登録　例外発生');
			}
			
			$pdo->commit();
			
			//排他処理
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WEX',";
			$sql .= "'080',";
			$sql .= "0";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql,'WEX080　排他制御オフ');
			
			$pdo = null;
			

		} catch (Exception $e) {
			
			$this->printLog("fatal", "例外発生", "WEX080", $e->getMessage());
			$updateFlg = "2";
			$pdo->rollBack();
			
			//排他処理
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'WEX',";
			$sql .= "'080',";
			$sql .= "0";
			$sql .= ")";
			
			$this->execWithPDOLog($pdo,$sql,'WEX080　排他制御オフ');
			
			$pdo = null;
		}
		
		try {
		
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$pdo->beginTransaction();
		
			//締処理履歴情報登録
			$RESULT = "01";
			
			if ($updateFlg == "1") {
			
				$RESULT = "02";
			} else if ($updateFlg == "2") {
				
				$RESULT = "03";
			}
			
			$sql = "CALL P_WEX080_SET_RIREKI(";
			$sql .= "'" . $staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $date . "',";
			$sql .= "'" . $RESULT . "',";
			$sql .= "@return_cd";
			$sql .= ")";

			$this->execWithPDOLog($pdo,$sql, '締処理履歴　登録');
			
			$sql = "SELECT";
			$sql .= " @return_cd";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, '締処理履歴　登録');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return_cd = $result["@return_cd"];
			
			if ($return_cd == "1") {
			
				throw new Exception('締処理履歴登録　例外発生');
			}
			
			$pdo->commit();
			$pdo = null;
			
			return true;
		} catch (Exception $e) {
			
			$this->printLog("fatal", "例外発生", "WEX080", $e->getMessage());
			
			$pdo->rollBack();
			$pdo = null;
		}

		$message = $this->MMessage->getOneMessage('WEXE080107');
		$this->errors['Check'][] =  $message;
		return false;
	}
	
	/**
	 * 入力チェック・存在チェック
	 * @access   public
	 * @param    データ
	 * @param    スタッフCD
	 * @return   成否情報(true,false)
	 */
	function checkData($date,$staff_cd) {
	
	
	 	try {

			
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			

				
			//データがない
			if ($date == "") {
			
				$message = $this->MMessage->getOneMessage('WEXE080001');
				$this->errors['Check'][] =  $message;
				return false;
			}
			
			//締日付のデータフォーマットが不正
			if (preg_match("/^[0-9]{8}$/",$date) == false) {

				$message = $this->MMessage->getOneMessage('WEXE080002');
				$this->errors['Check'][] =  $message;
				return false;
			}
			
			$year = substr($date, 0,4);
			$month = substr($date, 4,2);
			$day = substr($date, 6,2);
			
			//締日付が不正
			if (checkdate($month, $day, $year) == false) {
			
				$message = $this->MMessage->getOneMessage('WEXE080002');
				$this->errors['Check'][] =  $message;
				return false;
			}
			
			$ymd_sime = $year . "-" . $month . "-" . $day;
			
			//当日以降過チェック
			
			$tmpDate = date("Y-m-d 00:00:00");
			$dt = new DateTime($tmpDate);
			$dt->add(new DateInterval("P1D"));
			$date = $dt->format('Y-m-d H:i:s');//明日
			
			if ((strtotime($year . "-" . $month . "-" . $day . " 00:00:00") >= strtotime($date))) {

				$message = $this->MMessage->getOneMessage('WEXE080003');
				$this->errors['Check'][] =  $message;
				return false;

			}

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

			$tmpDate = $year . "-" . $month . "-" . $day . " " . $dt_syugyo_st_base;

			$dt = new DateTime($tmpDate);
			$date1 = $dt->format('Y-m-d H:i:s');
			$dt->add(new DateInterval("P1D"));
			$date2 = $dt->format('Y-m-d H:i:s');
			
			//存在チェック		
			$sql  = "SELECT ";
			$sql .= "   COUNT(*) AS COUNT";
			$sql .= " FROM";
			$sql .= "   T_SIME_CALC_DATA_WPO_COST";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   YMD_SIME='" . $ymd_sime . "'";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql,"作業指示日時締コスト情報　存在チェック");
	
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$countKOTEI = $result['COUNT'];
			
			//存在してない場合エラー
			if ($countKOTEI == 0) {
				
				$message = $this->MMessage->getOneMessage('WEXE080004');
				$this->errors['Check'][] =  $message;
				return false;
			}
			
			$pdo = null;		
			
			
			return true;
			
		} catch (Exception $e) {
			
			$this->printLog("fatal", "例外発生", "WEX080", $e->getMessage());
			$pdo = null;
		}
		
		$message = $this->MMessage->getOneMessage('WEXE080107');
		$this->errors['Check'][] =  $message;
		return false;
		
	}
	
	/**
	 * 警告チェック
	 * @access   public
	 * @param    データ
	 * @param    スタッフCD
	 * @return   成否情報(true,false)
	 */
	function checkData2($date,$staff_cd) {
	
	
	 	try {

			
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			//売上算出不可警告
			$sql  = "SELECT ";
			$sql .= "   A.BNRI_DAI_CD AS BNRI_DAI_CD,";
			$sql .= "   A.BNRI_CYU_CD AS BNRI_CYU_CD,";
			$sql .= "   A.BNRI_SAI_CD AS BNRI_SAI_CD,";
			$sql .= "   A.BUTURYO AS BUTURYO,";
			$sql .= "   IFNULL(B.BNRI_DAI_CD, '000') AS KEIYAKU_BNRI_DAI_CD,";
			$sql .= "   IFNULL(B.BNRI_CYU_CD, '000') AS KEIYAKU_BNRI_CYU_CD,";
			$sql .= "   CASE";
			$sql .= "     WHEN B.SURYO IS NULL THEN";
			$sql .= "       0";
			$sql .= "     ELSE";
			$sql .= "       B.SURYO";
			$sql .= "   END AS KEIYAKU_SURYO,";
			$sql .= "   CASE";
			$sql .= "     WHEN B.HIYOU IS NULL THEN";
			$sql .= "       0";
			$sql .= "     ELSE";
			$sql .= "       B.HIYOU";
			$sql .= "   END AS KEIYAKU_HIYOU,";
			$sql .= "   F.KBN_URIAGE AS KBN_URIAGE,";
			$sql .= "   G.KEIYAKU_BUTURYO_FLG AS KEIYAKU_BUTURYO_FLG,";
			$sql .= "   E.BNRI_DAI_RYAKU AS BNRI_DAI_RYAKU,";
			$sql .= "   F.BNRI_CYU_RYAKU AS BNRI_CYU_RYAKU,";
			$sql .= "   G.BNRI_SAI_RYAKU AS BNRI_SAI_RYAKU,";
			$sql .= "   IFNULL(H.KENSU_BNRI_DAI, 0) AS KENSU_BNRI_DAI,";
			$sql .= "   IFNULL(I.KENSU_BNRI_CYU, 0) AS KENSU_BNRI_CYU";
			$sql .= " FROM";
			$sql .= "   T_ABC_DB_DAY A";
			$sql .= "     LEFT JOIN M_NINUSI_KEIYAKU B ON";
			$sql .= "       A.NINUSI_CD=B.NINUSI_CD AND";
			$sql .= "       A.SOSIKI_CD=B.SOSIKI_CD AND";
			$sql .= "     (";
			$sql .= "       (";
			$sql .= "         A.BNRI_DAI_CD=B.BNRI_DAI_CD AND";
			$sql .= "         B.BNRI_CYU_CD='000'";
			$sql .= "       )";
			$sql .= "       OR";
			$sql .= "       (";
			$sql .= "         A.BNRI_DAI_CD=B.BNRI_DAI_CD AND";
			$sql .= "         A.BNRI_CYU_CD=B.BNRI_CYU_CD AND";
			$sql .= "         B.BNRI_CYU_CD<>'000'";
			$sql .= "       )";
			$sql .= "     )";
			$sql .= "     INNER JOIN M_CALC_TGT_KOTEI D ON";
			$sql .= "       A.NINUSI_CD=D.NINUSI_CD AND";
			$sql .= "       A.SOSIKI_CD=D.SOSIKI_CD AND";
			$sql .= "       A.BNRI_DAI_CD=D.BNRI_DAI_CD AND";
			$sql .= "       A.BNRI_CYU_CD=D.BNRI_CYU_CD AND";
			$sql .= "       A.BNRI_SAI_CD=D.BNRI_SAI_CD";
			$sql .= "     LEFT JOIN M_BNRI_DAI E ON";
			$sql .= "       A.NINUSI_CD=E.NINUSI_CD AND";
			$sql .= "       A.SOSIKI_CD=E.SOSIKI_CD AND";
			$sql .= "       A.BNRI_DAI_CD=E.BNRI_DAI_CD";
			$sql .= "     LEFT JOIN M_BNRI_CYU F ON";
			$sql .= "       A.NINUSI_CD=F.NINUSI_CD AND";
			$sql .= "       A.SOSIKI_CD=F.SOSIKI_CD AND";
			$sql .= "       A.BNRI_DAI_CD=F.BNRI_DAI_CD AND";
			$sql .= "       A.BNRI_CYU_CD=F.BNRI_CYU_CD";
			$sql .= "     LEFT JOIN M_BNRI_SAI G ON";
			$sql .= "       A.NINUSI_CD=G.NINUSI_CD AND";
			$sql .= "       A.SOSIKI_CD=G.SOSIKI_CD AND";
			$sql .= "       A.BNRI_DAI_CD=G.BNRI_DAI_CD AND";
			$sql .= "       A.BNRI_CYU_CD=G.BNRI_CYU_CD AND";
			$sql .= "       A.BNRI_SAI_CD=G.BNRI_SAI_CD";
			$sql .= "     LEFT JOIN (";
			$sql .= "                 SELECT ";
			$sql .= "                   J.NINUSI_CD AS NINUSI_CD,";
			$sql .= "                   J.SOSIKI_CD AS SOSIKI_CD,";
			$sql .= "                   J.BNRI_DAI_CD AS BNRI_DAI_CD,";
			$sql .= "                   COUNT(J.KEIYAKU_BUTURYO_FLG) AS KENSU_BNRI_DAI";
			$sql .= "                 FROM";
			$sql .= "                   M_BNRI_SAI J";
			$sql .= "                 WHERE";
			$sql .= "                   J.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "                   J.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "                   J.KEIYAKU_BUTURYO_FLG=1";
			$sql .= "                 GROUP BY";
			$sql .= "                   J.NINUSI_CD,";
			$sql .= "                   J.SOSIKI_CD,";
			$sql .= "                   J.BNRI_DAI_CD";
			$sql .= "               ) H ON";
			$sql .= "       B.NINUSI_CD=H.NINUSI_CD AND";
			$sql .= "       B.SOSIKI_CD=H.SOSIKI_CD AND";
			$sql .= "       B.BNRI_DAI_CD=H.BNRI_DAI_CD";
			$sql .= "     LEFT JOIN (";
			$sql .= "                 SELECT ";
			$sql .= "                   K.NINUSI_CD AS NINUSI_CD,";
			$sql .= "                   K.SOSIKI_CD AS SOSIKI_CD,";
			$sql .= "                   K.BNRI_DAI_CD AS BNRI_DAI_CD,";
			$sql .= "                   K.BNRI_CYU_CD AS BNRI_CYU_CD,";
			$sql .= "                   COUNT(K.KEIYAKU_BUTURYO_FLG) AS KENSU_BNRI_CYU";
			$sql .= "                 FROM";
			$sql .= "                   M_BNRI_SAI K";
			$sql .= "                 WHERE";
			$sql .= "                   K.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "                   K.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "                   K.KEIYAKU_BUTURYO_FLG=1";
			$sql .= "                 GROUP BY";
			$sql .= "                   K.NINUSI_CD,";
			$sql .= "                   K.SOSIKI_CD,";
			$sql .= "                   K.BNRI_DAI_CD,";
			$sql .= "                   K.BNRI_CYU_CD";
			$sql .= "               ) I ON";
			$sql .= "       B.NINUSI_CD=I.NINUSI_CD AND";
			$sql .= "       B.SOSIKI_CD=I.SOSIKI_CD AND";
			$sql .= "       B.BNRI_DAI_CD=I.BNRI_DAI_CD AND";
			$sql .= "       B.BNRI_CYU_CD=I.BNRI_CYU_CD";
			$sql .= " WHERE";
			$sql .= "   A.NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   A.SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   A.YMD='" . $date . "'";
			$sql .= " ORDER BY";
			$sql .= "   A.BNRI_DAI_CD,";
			$sql .= "   A.BNRI_CYU_CD,";
			$sql .= "   A.BNRI_SAI_CD";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql,"売上算出不可確認情報　取得");
			
			$hibetuArray = array();
			
			//契約単位代表物量工程設定エラー
			$ERROR_ARRAY1 = array();
			$WARN_ARRAY1 = array();
			$WARN_ARRAY2 = array();
			$WARN_ARRAY3 = array();
			$WARN_ARRAY4 = array();
			
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
	
				//エラー
				//a)契約単位代表物量フラグが複数設定される場合
				if($result["KEIYAKU_BNRI_DAI_CD"] <> '000' &&
					(($result["KEIYAKU_BNRI_CYU_CD"] == '000' && $result["KENSU_BNRI_DAI"] > 1) ||
				     ($result["KEIYAKU_BNRI_CYU_CD"] <> '000' && $result["KENSU_BNRI_CYU"] > 1))) {
				
					    $bnri_cyu_nm = '';
					    
					    if ($result["KEIYAKU_BNRI_CYU_CD"] == "000") {
						    
						    $bnri_cyu_nm = "分類なし";
					    } else {
						    
						    $bnri_cyu_nm = $result["BNRI_CYU_RYAKU"];
					    }
				
					    $array = array(
					    	"BNRI_DAI_RYAKU" => $result["BNRI_DAI_RYAKU"],
					    	"BNRI_CYU_RYAKU" => $bnri_cyu_nm
					    );
					    
					    $ERROR_ARRAY1[] = $array;
					    
				}
				
				//警告
				//a)荷主契約情報が存在しない
				if($result["KEIYAKU_SURYO"] == 0 || 
				   $result["KEIYAKU_HIYOU"] == 0) {
				   
					    $bnri_cyu_nm = '';
					    
					    if ($result["KEIYAKU_BNRI_CYU_CD"] == "000") {
						    
						    $bnri_cyu_nm = "分類なし";
					    } else {
						    
						    $bnri_cyu_nm = $result["BNRI_CYU_RYAKU"];
					    }
				
					    $array = array(
					    	"BNRI_DAI_RYAKU" => $result["BNRI_DAI_RYAKU"],
					    	"BNRI_CYU_RYAKU" => $bnri_cyu_nm
					    );
					    
					    $WARN_ARRAY1[] = $array;
				}
						
				//b)契約単位代表フラグが設定されてない場合
				if($result["KEIYAKU_BNRI_DAI_CD"] <> '000' && 
				    (($result["KEIYAKU_BNRI_CYU_CD"] == '000' && $result["KENSU_BNRI_DAI"] == 0) ||
				     ($result["KEIYAKU_BNRI_CYU_CD"] <> '000' && $result["KENSU_BNRI_CYU"] == 0))) {
				
					    $bnri_cyu_nm = '';
					    
					    if ($result["KEIYAKU_BNRI_CYU_CD"] == "000") {
						    
						    $bnri_cyu_nm = "分類なし";
					    } else {
						    
						    $bnri_cyu_nm = $result["BNRI_CYU_RYAKU"];
					    }
				
					    $array = array(
					    	"BNRI_DAI_RYAKU" => $result["BNRI_DAI_RYAKU"],
					    	"BNRI_CYU_RYAKU" => $bnri_cyu_nm
					    );
					    
					    $WARN_ARRAY2[] = $array;
					    
				}
				
				//c)契約単位物量の設定が行われてない場合
				if($result["KEIYAKU_BNRI_DAI_CD"] <> '000' &&
						($result["KBN_URIAGE"] != "01" && 
				         $result["KEIYAKU_BUTURYO_FLG"] == 1 && 
				         $result["BUTURYO"] == 0)) {
				
					    $array = array(
					    	"BNRI_DAI_RYAKU" => $result["BNRI_DAI_RYAKU"],
					    	"BNRI_CYU_RYAKU" => $result["BNRI_CYU_RYAKU"],
					    	"BNRI_SAI_RYAKU" => $result["BNRI_SAI_RYAKU"]
					    );
					    
					    $WARN_ARRAY3[] = $array;
				}
				
				//d)ABCデーターベース登録にて物量が設定されていない場合
				if($result["KBN_URIAGE"] != "01" && 
				   $result["KEIYAKU_BUTURYO_FLG"] == 0 &&
				   $result["BUTURYO"] == 0) {
				   
					    $array = array(
					    	"BNRI_DAI_RYAKU" => $result["BNRI_DAI_RYAKU"],
					    	"BNRI_CYU_RYAKU" => $result["BNRI_CYU_RYAKU"],
					    	"BNRI_SAI_RYAKU" => $result["BNRI_SAI_RYAKU"]
					    );
					    
					    $WARN_ARRAY4[] = $array;
				}
			}
			
			//重複を削除
			$tmp = array();
			
			foreach($ERROR_ARRAY1 as $val) {
			
				if (!in_array($val, $tmp)) {
					$tmp[] = $val;
				}
			}
			
			$ERROR_ARRAY1 = $tmp;
			
			$tmp = array();
			
			foreach($WARN_ARRAY1 as $val) {
			
				if (!in_array($val, $tmp)) {
					$tmp[] = $val;
				}
			}
			
			$WARN_ARRAY1 = $tmp;
			
			$tmp = array();
			
			foreach($WARN_ARRAY2 as $val) {
			
				if (!in_array($val, $tmp)) {
					$tmp[] = $val;
				}
			}
			
			$WARN_ARRAY2 = $tmp;
			
			//エラー
			if (count($ERROR_ARRAY1) > 0){
			
				$message = "";
				
				foreach($ERROR_ARRAY1 as $array) {
					$message .= "大分類：[" . $array["BNRI_DAI_RYAKU"] . "] " .
						        "中分類：[" . $array["BNRI_CYU_RYAKU"] . "] <br>";
						   
				}
			
				$message = vsprintf($this->MMessage->getOneMessage('WEXE080006'),array($message));
				
				
				$this->errors['Check'][] =  $message;
				return false;
				
			}
			
			//警告
			$message = "";
			
			if (count($WARN_ARRAY1) > 0 ||
				count($WARN_ARRAY2) > 0 ||
				count($WARN_ARRAY3) > 0 ||
				count($WARN_ARRAY4) > 0) {
				
				$message1 = "";
				
				if (count($WARN_ARRAY1) > 0){
				
					
					foreach($WARN_ARRAY1 as $array) {
					
						$message1 .= "    大分類：[" . $array["BNRI_DAI_RYAKU"] . "]" .
							   		 "  中分類：[" . $array["BNRI_CYU_RYAKU"] . "]\n";
					}
				
				} else {
					$message1 .= "    なし\n";
				}
				
				$message2 = "";
				
				if (count($WARN_ARRAY2) > 0){
				
					foreach($WARN_ARRAY2 as $array) {
					
						$message2 .= "    大分類：[" . $array["BNRI_DAI_RYAKU"] . "]" .
							   		 "  中分類：[" . $array["BNRI_CYU_RYAKU"] . "]\n";
					}
				
				} else {
					$message2 .= "    なし\n";
				}
			
				$message3 = "";
				
				if (count($WARN_ARRAY3) > 0){
				
					foreach($WARN_ARRAY3 as $array) {
					
						$message3 .= "    大分類：[" . $array["BNRI_DAI_RYAKU"] . "]" .
							   		"  中分類：[" . $array["BNRI_CYU_RYAKU"] . "]" .
							   		"  細分類：[" . $array["BNRI_SAI_RYAKU"] . "]\n";
					}
				
				} else {
					$message3 .= "    なし\n";
				}
				
				$message4 = "";
							
				if (count($WARN_ARRAY4) > 0){
				
					foreach($WARN_ARRAY4 as $array) {
					
						$message4 .= "    大分類：[" . $array["BNRI_DAI_RYAKU"] . "]" .
							   		 "  中分類：[" . $array["BNRI_CYU_RYAKU"] . "]" .
							   		 "  細分類：[" . $array["BNRI_SAI_RYAKU"] . "]\n";
					}
				
				} else {
					$message4 .= "    なし\n";
				} 
				
				$message = vsprintf($this->MMessage->getOneMessage('WEXQ080002'),array($message1,$message2,$message3,$message4));
			}
			
			
			$pdo = null;
			
			return $message;

		} catch (Exception $e) {
			
			$this->printLog("fatal", "例外発生", "WEX080", $e->getMessage());
			$pdo = null;
		}
		
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
		
	}


	/**
	 * 上書きチェック
	 * @access   public
	 * @param    データ
	 * @param    スタッフCD
	 * @return   成否情報(true,false)
	 */
	function checkData3($date,$staff_cd) {
	
	
	 	try {

			
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql  = "SELECT ";
			$sql .= "   COUNT(*) AS COUNT";
			$sql .= " FROM";
			$sql .= "   T_SIME_CALC_DATA_WEX_URIAGE";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
			$sql .= "   YMD_SIME='" . $date . "'";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql,"既存上書き確認　取得");
	
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$count = $result['COUNT'];
			
			$pdo = null;
			
			//存在してない場合エラー
			if ($count == 0) {
				
				return "0_0";
			} else {
				
				return "0_1";
			}

		} catch (Exception $e) {
			
			$this->printLog("fatal", "例外発生", "WEX080", $e->getMessage());
			$pdo = null;
		}
		
		$message = $this->MMessage->getOneMessage('WEXE080107');
		$this->errors['Check'][] =  $message;
		return false;
		
	}
	
	/**
	 * 排他チェック
	 * @access   public
	 * @param    データ
	 * @param    スタッフCD
	 * @return   成否情報(true,false)
	 */
	function checkData4($date,$staff_cd) {
	
	
	 	try {

			
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			//排他チェック
			$sql = "CALL P_GET_LOCK(";
			$sql .= " '" . $staff_cd . "',";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " 'WPO',";
			$sql .= " '050',";
			$sql .= " @return";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'WPO050　排他確認');
			
			$sql = "";
			$sql .= "SELECT";
			$sql .= " @return";

		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'WPO050　排他確認');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			if ($result['@return'] != "0") {
				
				$message = vsprintf($this->MMessage->getOneMessage('WEXE080005'), array("作業指示日時締更新"));
				$this->errors['Check'][] =  $message;
				return false;
			}
			
			//排他チェック
			$sql = "CALL P_GET_LOCK(";
			$sql .= " '" . $staff_cd . "',";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " 'WEX',";
			$sql .= " '070',";
			$sql .= " @return";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'WEX070　排他確認');
			
			$sql = "";
			$sql .= "SELECT";
			$sql .= " @return";

		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'WEX070　排他確認');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			
			if ($result['@return'] != "0") {
				
				$message = vsprintf($this->MMessage->getOneMessage('WEXE080005'), array("ABCデータベース登録"));
				$this->errors['Check'][] =  $message;
				return false;
			}
			
			//排他チェック
			$sql = "CALL P_GET_LOCK(";
			$sql .= " '" . $staff_cd . "',";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " 'WEX',";
			$sql .= " '080',";
			$sql .= " @return";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'WEX080　排他確認');
			
			$sql = "";
			$sql .= "SELECT";
			$sql .= " @return";

		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'WEX080　排他確認');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			if ($result['@return'] != "0") {
				
				$message = vsprintf($this->MMessage->getOneMessage('WEXE080005'), array("作業実績日次締更新"));
				$this->errors['Check'][] =  $message;
				return false;
			}
			
			//排他チェック
			$sql = "CALL P_GET_LOCK(";
			$sql .= " '" . $staff_cd . "',";
			$sql .= " '" . $this->_ninusi_cd . "',";
			$sql .= " '" . $this->_sosiki_cd . "',";
			$sql .= " 'WEX',";
			$sql .= " '020',";
			$sql .= " @return";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'WEX020　排他確認');
			
			$sql = "";
			$sql .= "SELECT";
			$sql .= " @return";

		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'WEX020　排他確認');
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			if ($result['@return'] != "0") {
				
				$message = vsprintf($this->MMessage->getOneMessage('WPOE050005'), array("荷主契約情報"));
				$this->errors['Check'][] =  $message;
				return false;
			}
			
			$pdo = null;
			
			return true;

		} catch (Exception $e) {
			
			$this->printLog("fatal", "例外発生", "WEX080", $e->getMessage());
			$pdo = null;
		}
		
		$message = $this->MMessage->getOneMessage('WEXE080107');
		$this->errors['Check'][] =  $message;
		return false;
		
	}
	
	/**
	 * 締日初期値取得
	 * @access   public
	 * @return   昨日の日付
	 */
	function getYesterday() {
	
	
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
			$dt->sub(new DateInterval("P1D"));
			$ymd_yesterday = $dt->format('Ymd');
			
			$pdo = null;
			
			return  $ymd_yesterday;

		} catch (Exception $e) {
			
			$this->printLog("fatal", "例外発生", "WEX080", $e->getMessage());
			$pdo = null;
		}
		
		return date("Ymd",strtotime("-1 day"));
		
	}

}

?>