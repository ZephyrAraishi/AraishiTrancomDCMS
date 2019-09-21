<?php
/**
 * WPO070Model
 *
 * OZAX商品マスタ取込
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class WPO070Model extends DCMSAppModel{

	public $name = 'WPO070Model';
	public $useTable = false;


    public $errors = array();
    public $infos = array();

    //取込処理カウント数
    public $batchNmLst = null;
    public $allData = null;


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
	 * 商品マスタIFの取得
	 * @access   public
	 * @return   商品マスタIFのデータ
	 */
	public function loadIfSyohin() {

		$sql  = "select * from if_ozax_shohin";
		$sql  .= " where NINUSI_CD = '" . $this->_ninusi_cd . "'";
		$sql  .= " and SOSIKI_CD = '" . $this->_sosiki_cd . "'";
		$data = $this->queryWithLog($sql, "", "WPO070 IF_商品マスタ取得");
		return $data;
	}


	/**
	 * 商品マスタIFのチェック件数の取得
	 * @access   public
	 * @return   商品マスタIFのチェック件数データ
	 */
	public function loadCheckIfSyohin() {

		$sql  = "select count(shohin_code) as A1 ";
		$sql  .= " ,(select count(check_naiyo) from if_ozax_shohin where not(check_naiyo is null or check_naiyo = '')) as A2 ";
		$sql  .= " from if_ozax_shohin ";
		$sql  .= " where NINUSI_CD = '" . $this->_ninusi_cd . "'";
		$sql  .= " and SOSIKI_CD = '" . $this->_sosiki_cd . "'";
		$data = $this->queryWithLog($sql, "", "WPO070 IF_商品マスタ件数取得");
		return $data;
	}

	/**
	 * IF商品マスタへの登録
	 * @access   public
	 * @param    csvファイルの内容
	 * @return   void
	 */
	public function InsertIfSyohin($records, $staffCd) {

		set_time_limit(600);		// 10分

		try{
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "DELETE FROM if_ozax_shohin";
			$sql  .= " where NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql  .= " and SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$this->execWithPDOLog($pdo, $sql, 'WPO070 IF_商品マスタ削除');

			foreach ( $records as $line ) {

				$values = "'" . $this->_ninusi_cd . "',";
				$values .= "'" . $this->_sosiki_cd . "',";
				$values .= "'" . $this->validateLine($line) . "'";

				$counter = 0;
				foreach ( $line as $str ) {

					if ($counter >= 25) { break; }

					$values .= ", ";

					// 入り数（数値項目）
					if ( $counter == 14 || $counter == 15 || $counter == 16 || $counter == 23 ) {
						if ( $str == "" ) {
							$values .= "null";
						} else {
							$values .= trim($str);
						}
					// 賞味期限区分の空白を変換
					} else if ( $counter == 4 ) {
							$values .= trim($str)!='1' ?  "'" .  0  . "'" : "'" . trim($str) . "'";
					// 名称系を変換
					} else if ( $counter == 1 || $counter == 17 || $counter == 18 || $counter == 19 || $counter == 24 ) {
							$values .= "'".mb_convert_encoding( trim($str), "utf-8", "SJIS-win" )."'";
					} else  {
						$values .= "'".trim($str)."'";
					}
					$counter++;
				}

				$values .= ",'" . $staffCd . "',";
				$values .= "CURRENT_TIMESTAMP";

				$sql = "INSERT INTO if_ozax_shohin VALUES (" . $values . ")";
				$this->printLog("info", "例外発生", "WPO070", $sql);
				$this->execWithPDOLog($pdo, $sql, 'WPO070 IF_商品マスタ取込');
			}

			$pdo = null;
			return true;

		} catch (Exception $e) {
			$pdo = null;
			$this->printLog("info", "例外発生", "WPO070", $e->getMessage());
			$this->errors['Check'][] = "商品マスタ取込時にエラーが発生しました。";
			$this->errors['Check'][] = $e->getMessage();
		}

		return false;

	}

	/**
	 * エラーチェック
	 * @access   public
	 * @param    $line 取り込んだ商品情報（１行）
	 * @return   void
	 */
	private function validateLine(&$line) {
		$message = "";

		if ( $line[0] == "" ) {
			$message .= "商品コードが空白です。";
		} else if ( $line[1] == "" ) {
			$message .= "商品名が空白です。";
		}

		if ( $line[14] != "" ) {		// 入数１のチェック
			if (!preg_match('/^[0-9]+$/', $line[14])) {
				$message .= "入数１が数値ではありません。";
				 $line[14] = "";
			}
			//if ( $line[17] == "") {
			//		$message .= "入数１の単位が未登録です。";
			//} else if ( $line[5] == "" && $line[8] == "" && $line[11] == "" ) {
			//		$message .= "入数１のJAN1、ITF1、ハウスコード1が未登録です。";
			//}
		}

		if ( $line[15] != "" ) {		// 入数２のチェック
			if (!preg_match('/^[0-9]+$/', $line[15])) {
				$message .= "入数２が数値ではありません。";
				 $line[15] = "";
			}
			//if ( $line[18] == "") {
			//	$message .= "入数２の単位が未登録です。";
			//} elseif ( $line[6] == "" && $line[9] == "" && $line[12] == "" ) {
			//	$message .= "入数２のJAN2、ITF2、ハウスコード2が未登録です。";
			//}
		}

		if ( $line[16] != "" ) {		// 入数３のチェック
			if (!preg_match('/^[0-9]+$/', $line[16])) {
				$message .= "入数３が数値ではありません。";
				 $line[16] = "";
			}
			//if ( $line[19] == "") {
			//	$message .= "入数３の単位が未登録です。";
			//} else if ( $line[7] == "" && $line[10] == "" && $line[11] == "" ) {
			//	$message .= "入数３のJAN3、ITF3、ハウスコード3が未登録です。";
			//}
		}

		return $message;
	}

	/**
	 * 商品マスタの更新
	 * @access   public
	 * @param   スタッフコード
	 * @return   void
	 */
	public function updateSyohin($staffCd) {

		set_time_limit(600);		// 10分

		$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$pdo->beginTransaction();

		try{

			$sql  = "select * from if_ozax_shohin where (CHECK_NAIYO = '' or CHECK_NAIYO is null)";
			$ifSyohinList = $this->queryWithLog($sql, "", "WPO070 IF_商品マスタ取得");

			foreach ( $ifSyohinList as $line ) {
				foreach ( $line as $syohin ) {

					$sql  = "select * from m_ozax_shohin";
					$sql  .= " where NINUSI_CD = '" . $this->_ninusi_cd . "'";
					$sql  .= " and SOSIKI_CD = '" . $this->_sosiki_cd . "'";
					$sql  .= " and SHOHIN_CODE = '" . $syohin['SHOHIN_CODE'] . "'";

					$Syohin = $this->queryWithLog($sql, "", "WPO070 商品マスタ取得");

					if ( count($Syohin) == 0 ) {
						$execSql = "INSERT INTO m_ozax_shohin (NINUSI_CD, SOSIKI_CD, SHOHIN_CODE, SHOHIN_MEI, ";
						$execSql .= "WMS_JAN, WMS_ITF, SHOMI_KIGEN_KUBUN, JAN_1, JAN_2, JAN_3, ITF_1, ITF_2, ITF_3, ";
						$execSql .= "HOUSECODE_1, HOUSECODE_2, HOUSECODE_3, NISUGATA_1_IRISU, NISUGATA_2_IRISU, NISUGATA_3_IRISU, ";
						$execSql .= "NISUGATA_1_TANI, NISUGATA_2_TANI, NISUGATA_3_TANI, JAN_4, ITF_4, HOUSECODE_4, ";
						$execSql .= "NISUGATA_4_IRISU, NISUGATA_4_TANI, TOROKU_STAFF, TOROKU_DATE, KOSIN_STAFF, KOSIN_DATE, ";
						$execSql .= "NISUGATA_1_CODE, NISUGATA_2_CODE,NISUGATA_3_CODE, NISUGATA_4_CODE) ";
						$execSql .= "VALUES (";

						$execSql .= "'" . $this->_ninusi_cd . "', ";
						$execSql .= "'" . $this->_sosiki_cd . "', ";
						$execSql .= "'" . $syohin['SHOHIN_CODE'] . "', ";
						$execSql .= "'" . str_replace("　","",$syohin['SHOHIN_MEI']) . "', ";
						$execSql .= "'" . $syohin['WMS_JAN'] . "', ";
						$execSql .= "'" . $syohin['WMS_ITF'] . "', ";
						$execSql .= "'" . $syohin['SHOMI_KIGEN_KUBUN'] . "', ";
						$execSql .= "'" . $syohin['JAN_1'] . "', ";
						$execSql .= "'" . $syohin['JAN_2'] . "', ";
						$execSql .= "'" . $syohin['JAN_3'] . "', ";
						$execSql .= "'" . $syohin['ITF_1'] . "', ";
						$execSql .= "'" . $syohin['ITF_2'] . "', ";
						$execSql .= "'" . $syohin['ITF_3'] . "', ";
						$execSql .= "'" . $syohin['HOUSECODE_1'] . "', ";
						$execSql .= "'" . $syohin['HOUSECODE_2'] . "', ";
						$execSql .= "'" . $syohin['HOUSECODE_3'] . "', ";
						$execSql .= "'" . $syohin['NISUGATA_1_IRISU'] . "', ";
						$execSql .= "'" . $syohin['NISUGATA_2_IRISU'] . "', ";
						$execSql .= "'" . $syohin['NISUGATA_3_IRISU'] . "', ";
						$execSql .= "'" . $syohin['NISUGATA_1_TANI'] . "', ";
						$execSql .= "'" . $syohin['NISUGATA_2_TANI'] . "', ";
						$execSql .= "'" . $syohin['NISUGATA_3_TANI'] . "', ";
						$execSql .= "'" . $syohin['JAN_4'] . "', ";
						$execSql .= "'" . $syohin['ITF_4'] . "', ";
						$execSql .= "'" . $syohin['HOUSECODE_4'] . "', ";
						$execSql .= "'" . $syohin['NISUGATA_4_IRISU'] . "', ";
						$execSql .= "'" . $syohin['NISUGATA_4_TANI'] . "', ";
						$execSql .= "'" . $staffCd . "', ";
						$execSql .= "CURRENT_TIMESTAMP, ";
						$execSql .= "'" . $staffCd . "', ";
						$execSql .= "CURRENT_TIMESTAMP,";
						$execSql .= "'" . $syohin['SHOHIN_CODE'] . "001', ";
						$execSql .= "'" . $syohin['SHOHIN_CODE'] . "002', ";
						$execSql .= "'" . $syohin['SHOHIN_CODE'] . "003', ";
						$execSql .= "'" . $syohin['SHOHIN_CODE'] . "004' ";
						$execSql .= ")";

						$this->execWithPDOLog($pdo, $execSql, 'WPO070 商品マスタ追加');
					} else {
						$execSql = "UPDATE m_ozax_shohin SET ";
						$execSql .= "SHOHIN_MEI='" . $syohin['SHOHIN_MEI'] . "', ";
						$execSql .= "WMS_JAN='" . $syohin['WMS_JAN'] . "', ";
						$execSql .= "WMS_ITF='" . $syohin['WMS_ITF'] . "', ";
						$execSql .= "SHOMI_KIGEN_KUBUN='" . $syohin['SHOMI_KIGEN_KUBUN'] . "', ";
						$execSql .= "JAN_1='" . $syohin['JAN_1'] . "', ";
						$execSql .= "JAN_2='" . $syohin['JAN_2'] . "', ";
						$execSql .= "JAN_3='" . $syohin['JAN_3'] . "', ";
						$execSql .= "ITF_1='" . $syohin['ITF_1'] . "', ";
						$execSql .= "ITF_2='" . $syohin['ITF_2'] . "', ";
						$execSql .= "ITF_3='" . $syohin['ITF_3'] . "', ";
						$execSql .= "HOUSECODE_1='" . $syohin['HOUSECODE_1'] . "', ";
						$execSql .= "HOUSECODE_2='" . $syohin['HOUSECODE_2'] . "', ";
						$execSql .= "HOUSECODE_3='" . $syohin['HOUSECODE_3'] . "', ";
						$execSql .= "NISUGATA_1_IRISU='" . $syohin['NISUGATA_1_IRISU'] . "', ";
						$execSql .= "NISUGATA_2_IRISU='" . $syohin['NISUGATA_2_IRISU'] . "', ";
						$execSql .= "NISUGATA_3_IRISU='" . $syohin['NISUGATA_3_IRISU'] . "', ";
						$execSql .= "NISUGATA_1_TANI='" . $syohin['NISUGATA_1_TANI'] . "', ";
						$execSql .= "NISUGATA_2_TANI='" . $syohin['NISUGATA_2_TANI'] . "', ";
						$execSql .= "NISUGATA_3_TANI='" . $syohin['NISUGATA_3_TANI'] . "', ";
						$execSql .= "JAN_4='" . $syohin['JAN_4'] . "', ";
						$execSql .= "ITF_4='" . $syohin['ITF_4'] . "', ";
						$execSql .= "HOUSECODE_4='" . $syohin['HOUSECODE_4'] . "', ";
						$execSql .= "NISUGATA_4_IRISU='" . $syohin['NISUGATA_4_IRISU'] . "', ";
						$execSql .= "NISUGATA_4_TANI='" . $syohin['NISUGATA_4_TANI'] . "', ";
						$execSql .= "KOSIN_STAFF='" . $staffCd . "', ";
						$execSql .= "KOSIN_DATE=CURRENT_TIMESTAMP";
						$execSql .= " WHERE ";
						$execSql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
						$execSql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "'AND";
						$execSql .= " SHOHIN_CODE='" . $syohin['SHOHIN_CODE'] . "'";

						$this->execWithPDOLog($pdo, $execSql, 'WPO070 商品マスタ更新');
					}

				}
			}

			$sql = "DELETE FROM if_ozax_shohin where (CHECK_NAIYO = '' or CHECK_NAIYO is null)";
			$sql  .= " and NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql  .= " and SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$this->execWithPDOLog($pdo, $sql, 'WPO070  IF_商品マスタ削除');

			$pdo->commit();
			$pdo = null;

			return;

		} catch (Exception $e) {
			$this->printLog("info", "例外発生", "WPO070", $e->getMessage());
			$pdo->rollBack();
			$pdo = null;
			$this->errors['Check'][] = "商品マスタ更新時にエラーが発生しました。";
			$this->errors['Check'][] = $e->getMessage();
		}

		return false;


	}

	/**
	 * ＩＦ商品マスタＣＳＶデータ取得
	 * @access   public
	 * @return   ＩＦ商品マスタＣＳＶデータ
	 */
	function getCSV() {

		$text = array();

		//ヘッダー情報
		$header = "チェック内容,商品コード,商品名,WMS_JAN,WMS_ITF,賞味期限管理区分,JAN_1,JAN_2,JAN_3,";
		$header .= "ITF_1,ITF_2,ITF_3,ハウスコード1,ハウスコード2,ハウスコード3,";
		$header .= "荷姿１入数,荷姿２入数,荷姿３入数,荷姿１単位,荷姿２単位,荷姿３単位,JAN_4,ITF_4,ハウスコード4,荷姿４入数,荷姿４単位";

		$text[] = $header;

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			$sql  = "SELECT CHECK_NAIYO AS A1";
			$sql .= ",SHOHIN_CODE AS A2";
			$sql .= ",SHOHIN_MEI AS A3";
			$sql .= ",WMS_JAN AS A4";
			$sql .= ",WMS_ITF AS A5";
			$sql .= ",SHOMI_KIGEN_KUBUN AS A6";
			$sql .= ",JAN_1 AS A7";
			$sql .= ",JAN_2 AS A8";
			$sql .= ",JAN_3 AS A9";
			$sql .= ",ITF_1 AS A10";
			$sql .= ",ITF_2 AS A11";
			$sql .= ",ITF_3 AS A12";
			$sql .= ",HOUSECODE_1 AS A13";
			$sql .= ",HOUSECODE_2 AS A14";
			$sql .= ",HOUSECODE_3 AS A15";
			$sql .= ",NISUGATA_1_IRISU AS A16";
			$sql .= ",NISUGATA_2_IRISU AS A17";
			$sql .= ",NISUGATA_3_IRISU AS A18";
			$sql .= ",NISUGATA_1_TANI AS A19";
			$sql .= ",NISUGATA_2_TANI AS A20";
			$sql .= ",NISUGATA_3_TANI AS A21";
			$sql .= ",JAN_4 AS A22";
			$sql .= ",ITF_4 AS A23";
			$sql .= ",HOUSECODE_4 AS A24";
			$sql .= ",NISUGATA_4_IRISU AS A25";
			$sql .= ",NISUGATA_4_TANI AS A26";

			$sql .= " FROM IF_OZAX_SHOHIN";

			$sql .= ' WHERE ';
			$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "'";

			$sql .= "    ORDER BY";
			$sql .= "      CHECK_NAIYO";
			$sql .= ";";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"ＩＦ商品マスタCSVデータ 取得");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$tmpText = $result['A1'] . ',';
				$tmpText .= $result['A2'] . ',';
				$tmpText .= $result['A3'] . ',';
				$tmpText .= $result['A4'] . ',';
				$tmpText .= $result['A5'] . ',';
				$tmpText .= $result['A6'] . ',';
				$tmpText .= $result['A7'] . ',';
				$tmpText .= $result['A8'] . ',';
				$tmpText .= $result['A9'] . ',';
				$tmpText .= $result['A10'] . ',';
				$tmpText .= $result['A11'] . ',';
				$tmpText .= $result['A12'] . ',';
				$tmpText .= $result['A13'] . ',';
				$tmpText .= $result['A14'] . ',';
				$tmpText .= $result['A15'] . ',';
				$tmpText .= $result['A16'] . ',';
				$tmpText .= $result['A17'] . ',';
				$tmpText .= $result['A18'] . ',';
				$tmpText .= $result['A19'] . ',';
				$tmpText .= $result['A20'] . ',';
				$tmpText .= $result['A21'] . ',';
				$tmpText .= $result['A22'] . ',';
				$tmpText .= $result['A23'] . ',';
				$tmpText .= $result['A24'] . ',';
				$tmpText .= $result['A25'] . ',';
				$tmpText .= $result['A26'] ;

				$text[] = $tmpText;

			}

			$pdo = null;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO070", $e->getMessage());
			$pdo = null;
		}


		return $text;
	}

}
?>

