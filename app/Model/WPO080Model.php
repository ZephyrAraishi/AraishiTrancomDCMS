<?php
/**
 * WPO080Model
 *
 * OZAX出荷指示データ取込
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.1
 * @memo          2017/09/28 K.Araishi 出荷指示読替マスタによる項目内容変換ロジックの追加
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class WPO080Model extends DCMSAppModel{

	public $name = 'WPO080Model';
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
	 * 出荷指示データIFの取得
	 * @access   public
	 * @return   出荷指示データIFのデータ
	 */
	public function loadIfShukka() {

		$sql  = "select * from if_ozax_shukka";
		$sql  .= " where NINUSI_CD = '" . $this->_ninusi_cd . "'";
		$sql  .= " and SOSIKI_CD = '" . $this->_sosiki_cd . "'";
		$data = $this->queryWithLog($sql, "", "WPO080 IF_出荷指示データ取得");
		return $data;
	}

	/**
	 * 出荷指示データIFのエラー存在チェック
	 * @access   public
	 * @return   出荷指示データIFのエラー存在チェック
	 */
	public function checkIfShukka() {

		try{
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// IF出荷指示データ内にエラーがあれば取り込まない。
			$sql  = "select count(*) as A1 from if_ozax_shukka";
			$sql  .= " where NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql  .= " and SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$sql  .= " and not(check_naiyo is null or check_naiyo = '')";
			$data = $this->queryWithLog($sql, "", "WPO080 IF_出荷指示データエラーチェック");

			if ($data[0][0]['A1'] != 0 or !empty($data[0][0]['A1'])) {
				$this->errors['Check'][] = "取り込んだ出荷指示データにエラーがある為、取り込めません。";
				return false;
			}
			// IF出荷指示データの商品コードが商品マスタに存在しなければ取込を行わない
			$sql  = "select count(*) as A1 from if_ozax_shukka as sk left join m_ozax_shohin as sh on (";
			$sql  .= "  sk.NINUSI_CD =sh.NINUSI_CD";
			$sql  .= "   and sk.SOSIKI_CD =sh.SOSIKI_CD";
			$sql  .= "   and sk.shohin_code =sh.shohin_code)";
			$sql  .= " where sk.NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql  .= "   and sk.SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$sql  .= "   and sh.shohin_code is null ";
			$data = $this->queryWithLog($sql, "", "WPO080 IF_出荷指示データエラーチェック");

			if ($data[0][0]['A1'] != 0) {
				$this->errors['Check'][] = "取り込んだ出荷指示データの商品コードが商品マスタに存在しません。";

				$sql = "update  if_ozax_shukka set";
				$sql  .= " check_naiyo = concat(check_naiyo, '、商品マスタがありません。')";
				$sql  .= " where NINUSI_CD = '" . $this->_ninusi_cd . "'";
				$sql  .= " and SOSIKI_CD = '" . $this->_sosiki_cd . "'";
				$sql  .= " and shohin_code in (select shohin_code from (";
				$sql  .= "select distinct sk.shohin_code as A1 from if_ozax_shukka as sk left join m_ozax_shohin as sh on (";
				$sql  .= "  sk.NINUSI_CD =sh.NINUSI_CD";
				$sql  .= "   and sk.SOSIKI_CD =sh.SOSIKI_CD";
				$sql  .= "   and sk.shohin_code =sh.shohin_code)";
				$sql  .= " where sk.NINUSI_CD = '" . $this->_ninusi_cd . "'";
				$sql  .= "   and sk.SOSIKI_CD = '" . $this->_sosiki_cd . "'";
				$sql  .= "   and sh.shohin_code is null ";
				$sql  .= ") as tmp)";
				$this->execWithPDOLog($pdo, $sql, 'WPO080 IF_出荷指示データ削除');

				return false;
			}

			// IF出荷指示データの業態倉庫・出荷日・方面名・バッチ№でトータルピッキングが行われていたら取り込まない
			$sql  = "select count(*) as A1 from t_ozax_total_pk";
			$sql  .= " where NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql  .= "   and SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$sql  .= "   and TOTAL_PK_KUBUN > 0 ";
			$sql  .= "   and (GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI) in (select GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI from if_ozax_shukka";
			$sql  .= "        where NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql  .= "          and SOSIKI_CD = '" . $this->_sosiki_cd . "')";
			$data = $this->queryWithLog($sql, "", "WPO080 IF_出荷指示データエラーチェック");

			if ($data[0][0]['A1'] != 0) {
				$this->errors['Check'][] = "取り込んだ出荷指示データは既にトータルピックが行われています。";
				return false;
			}

			return true;
		} catch (Exception $e) {
			$pdo = null;
			$this->printLog("info", "例外発生", "WPO080", $e->getMessage());
			$this->errors['Check'][] = "出荷指示データチェック時にエラーが発生しました。";
			$this->errors['Check'][] = $e->getMessage();
		}
		return false;
}

	/**
	 * 出荷指示データIFのチェック件数の取得
	 * @access   public
	 * @return   出荷指示データIFのチェック件数データ
	 */
	public function loadCheckIfShukka() {

		$sql  = "select count(*) as A1 ";
		$sql  .= " ,(select count(check_naiyo) from if_ozax_shukka where not(check_naiyo is null or check_naiyo = '')) as A2 ";
		$sql  .= " from if_ozax_shukka ";
		$sql  .= " where NINUSI_CD = '" . $this->_ninusi_cd . "'";
		$sql  .= " and SOSIKI_CD = '" . $this->_sosiki_cd . "'";
		$data = $this->queryWithLog($sql, "", "WPO080 IF_出荷指示データ件数取得");
		return $data;
	}

	/**
	 * IF出荷指示データへの登録
	 * @access   public
	 * @param    csvファイルの内容
	 * @return   void
	 */
	public function InsertIfShukka($records, $staffCd) {

		set_time_limit(600);		// 10分

		try{
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "DELETE FROM if_ozax_shukka";
			$sql  .= " where NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql  .= " and SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$this->execWithPDOLog($pdo, $sql, 'WPO080 IF_出荷指示データ削除');

			foreach ( $records as $line ) {

				$values = "'" . $this->_ninusi_cd . "',";
				$values .= "'" . $this->_sosiki_cd . "',";
				$values .= "'" . $this->validateLine($line) . "'";

				$counter = 0;
				foreach ( $line as $str ) {

					if ($counter >= 49) { break; }

					$values .= ", ";

					// 出荷日・納品日変換
					if ( $counter == 0 || $counter == 1  ) {
						if ( mb_strlen($str) == 8 ) {
							$values .= "'" . substr($str,0,4) . "/" . substr($str,4,2) . "/" . substr($str,6,2) . "'" ;
						} else {
							$values .= "'". trim($str) . "'" ;
						}
					// 賞味期限日変換
					} else if ( $counter == 33 ) {
						if ( trim($str) == "00000000" ) {
							$values .= "''" ;
						} else {
							$values .= "'" . substr($str,0,4) . "/" . substr($str,4,2) . "/" . substr($str,6,2) . "'" ;
						}
					// 入り数（数値項目）
					} else if ( $counter == 39 || $counter == 40 || $counter == 41 || $counter == 42 ) {
						if ( $str == "" ) {
							$values .= "0";
						} else {
							$values .= trim($str);
						}
					// ロケーションコード変換
					} else if ( $counter == 22 ) {
						if ( $str == "" ) {
							$values .= "";
						} else {
							$values .= "'" . substr($str,0,3) . "-" . substr($str,3,2) . "-" . substr($str,5,2) . "'" ;
						}
					} else  {
						$values .= "'".mb_convert_encoding( str_replace("'","",trim($str)) , "utf-8", "SJIS-win" )."'";
					}
					$counter++;
				}

				$values .= ",'" . $staffCd . "',";
				$values .= "CURRENT_TIMESTAMP";

				$sql = "INSERT INTO if_ozax_shukka VALUES (" . $values . ")";
				$this->printLog("info", "IF出荷取込", "WPO080", $sql);
				$this->execWithPDOLog($pdo, $sql, 'WPO080 IF_出荷指示データ取込');
			}

			$pdo = null;
			return true;

		} catch (Exception $e) {
			$pdo = null;
			$this->printLog("info", "例外発生", "WPO080", $e->getMessage());
			$this->errors['Check'][] = "出荷指示データ取込時にエラーが発生しました。";
			$this->errors['Check'][] = $e->getMessage();
		}
		return false;
	}

	/**
	 * エラーチェック
	 * @access   public
	 * @param    $line 取り込んだ出荷指示情報（１行）
	 * @return   void
	 */
	private function validateLine(&$line) {
		$message = "";

		if ( $line[0] == "" ) {
			$message .= "出荷日が空白です。";
		} else if (!$this->MCommon->isValidDate($line[0])) {
			$message .= "出荷日には日付を指定してください。";
		} else if ($line[1] == "") {
			$message .= "納品日が空白です。";
		} else if (!$this->MCommon->isValidDate($line[1])) {
			$message .= "納品日には日付を指定してください。";
		} else if ( $line[6] == "" ) {
			$message .= "方面名が空白です。";
		} else if ( $line[11] == "" ) {
//			$message .= "店舗コードが空白です。";
		} else if ( $line[12] == "" ) {
//			$message .= "店舗名が空白です。";
		} else if ( $line[16] == "" ) {
			$message .= "バッチ№が空白です。";
		} else if ( $line[18] == "" ) {
			$message .= "業態倉庫コードが空白です。";
		} else if ( $line[19] == "" ) {
			$message .= "業態倉庫名が空白です。";
		} else if ( $line[22] == "" ) {
			$message .= "ロケーションコードが空白です。";
		} else if ( $line[23] == "" ) {
			$message .= "商品コードが空白です。";
		} else if ( $line[24] == "" ) {
			$message .= "商品名が空白です。";
		} else if ( $line[34] == "" ) {
			$message .= "ＳＳ№が空白です。";
		} else if ( $line[35] == "" ) {
			$message .= "ＳＳ№行が空白です。";
		} else if ( $line[39] == "" ) {
			$message .= "荷姿１の出荷指示数が空白です。";
		} else if ( $line[40] == "" ) {
			$message .= "荷姿２の出荷指示数が空白です。";
		} else if ( $line[41] == "" ) {
			$message .= "荷姿３の出荷指示数が空白です。";
		} else if ( $line[42] == "" ) {
			$message .= "総バラの出荷指示数が空白です。";
		}

		if ( $line[39] != "" ) {		// 指示数チェック
			if (!preg_match('/^[0-9]+$/', $line[39])) {
				$message .= "荷姿１の出荷指示数が数値ではありません。";
				 $line[39] = "0";
			}
		}

		if ( $line[40] != "" ) {		// 指示数チェック
			if (!preg_match('/^[0-9]+$/', $line[40])) {
				$message .= "荷姿２の出荷指示数が数値ではありません。";
				 $line[40] = "";
			}
		}

		if ( $line[41] != "" ) {		// 指示数チェック
			if (!preg_match('/^[0-9]+$/', $line[41])) {
				$message .= "荷姿３の出荷指示数が数値ではありません。";
				 $line[41] = "";
			}
		}

		if ( $line[42] != "" ) {		// 指示数チェック
			if (!preg_match('/^[0-9]+$/', $line[42])) {
				$message .= "総バラの出荷指示数が数値ではありません。";
				 $line[42] = "";
			}
		}

		return $message;
	}

	/**
	 * 出荷指示データの更新
	 * @access   public
	 * @param   スタッフコード
	 * @return   void
	 */
	public function updateShukka($staffCd) {

		$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$pdo->beginTransaction();

		try{

			// 出荷指示データの削除(IF_OZAX_SHUKKAの業態倉庫・出荷日・方面名・バッチ№でデータを削除）
			$sql = "DELETE FROM T_OZAX_SHUKKA ";
			$sql .= " WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "   AND SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$sql .= "   AND (GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI) IN (SELECT GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI FROM IF_OZAX_SHUKKA";
			$sql .= "        WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "          AND SOSIKI_CD = '" . $this->_sosiki_cd . "')";
			$this->execWithPDOLog($pdo, $sql, 'WPO080  出荷指示データ削除');

			// トータルピッキングデータの削除(IF_OZAX_SHUKKAの業態倉庫・出荷日・方面名・バッチ№でデータを削除）
			$sql = "DELETE FROM T_OZAX_TOTAL_PK ";
			$sql .= " WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "   AND SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$sql .= "   AND (GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI) IN (SELECT GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI FROM IF_OZAX_SHUKKA";
			$sql .= "        WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "          AND SOSIKI_CD = '" . $this->_sosiki_cd . "')";
			$this->execWithPDOLog($pdo, $sql, 'WPO080  トータルピッキングデータデータ削除');

			$sql = "DELETE FROM T_OZAX_IPOD_TOTAL_PK ";
			$sql .= " WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "   AND SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$sql .= "   AND (GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI) IN (SELECT GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI FROM IF_OZAX_SHUKKA";
			$sql .= "        WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "          AND SOSIKI_CD = '" . $this->_sosiki_cd . "')";
			$this->execWithPDOLog($pdo, $sql, 'WPO080  トータルピッキングデータiPodデータ削除');

			// 種蒔データの削除(IF_OZAX_SHUKKAの業態倉庫・出荷日・方面名・バッチ№でデータを削除）
			$sql = "DELETE FROM T_OZAX_TANEMAKI ";
			$sql .= " WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "   AND SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$sql .= "   AND (GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI) IN (SELECT GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI FROM IF_OZAX_SHUKKA";
			$sql .= "        WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "          AND SOSIKI_CD = '" . $this->_sosiki_cd . "')";
			$this->execWithPDOLog($pdo, $sql, 'WPO080  種蒔データ削除');

			$sql = "DELETE FROM T_OZAX_IPOD_TANEMAKI ";
			$sql .= " WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "   AND SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$sql .= "   AND (GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI) IN (SELECT GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI FROM IF_OZAX_SHUKKA";
			$sql .= "        WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "          AND SOSIKI_CD = '" . $this->_sosiki_cd . "')";
			$this->execWithPDOLog($pdo, $sql, 'WPO080  種蒔iPodデータ削除');

			// 店舗仕分けデータの削除(IF_OZAX_SHUKKAの業態倉庫・出荷日・方面名・バッチ№でデータを削除）
			$sql = "DELETE FROM T_OZAX_TENPO_SIWAKE ";
			$sql .= " WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "   AND SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$sql .= "   AND (NOHIN_BI,HOMEN_MEI) IN (SELECT NOHIN_BI,HOMEN_MEI FROM IF_OZAX_SHUKKA";
			$sql .= "        WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "          AND SOSIKI_CD = '" . $this->_sosiki_cd . "')";
			$this->execWithPDOLog($pdo, $sql, 'WPO080  店舗仕分データ削除');

			$sql = "DELETE FROM T_OZAX_ANDROID_TENPO_SIWAKE ";
			$sql .= " WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "   AND SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$sql .= "   AND (NOHIN_BI,HOMEN_MEI) IN (SELECT NOHIN_BI,HOMEN_MEI FROM IF_OZAX_SHUKKA";
			$sql .= "        WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "          AND SOSIKI_CD = '" . $this->_sosiki_cd . "')";
			$this->execWithPDOLog($pdo, $sql, 'WPO080  店舗仕分Androidデータ削除');

			// ケースラベルデータの削除(IF_OZAX_SHUKKAの業態倉庫・出荷日・方面名・バッチ№でデータを削除）
			$sql = "DELETE FROM T_OZAX_CASE_LABEL ";
			$sql .= " WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "   AND SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$sql .= "   AND (GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI) IN (SELECT GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI FROM IF_OZAX_SHUKKA";
			$sql .= "        WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "          AND SOSIKI_CD = '" . $this->_sosiki_cd . "')";
			$this->execWithPDOLog($pdo, $sql, 'WPO080  ケースラベルデータ削除');

			// バララベルデータの削除(IF_OZAX_SHUKKAの業態倉庫・出荷日・方面名・バッチ№でデータを削除）
			$sql = "DELETE FROM T_OZAX_BARA_LABEL ";
			$sql .= " WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "   AND SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$sql .= "   AND (GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI) IN (SELECT GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI FROM IF_OZAX_SHUKKA";
			$sql .= "        WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "          AND SOSIKI_CD = '" . $this->_sosiki_cd . "')";
			$this->execWithPDOLog($pdo, $sql, 'WPO080  バララベルデータ削除');

			// 店舗看板データの削除(IF_OZAX_SHUKKAの業態倉庫・出荷日・方面名・バッチ№でデータを削除）
			$sql = "DELETE FROM T_OZAX_KANBAN ";
			$sql .= " WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "   AND SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$sql .= "   AND (GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI) IN (SELECT GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI FROM IF_OZAX_SHUKKA";
			$sql .= "        WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "          AND SOSIKI_CD = '" . $this->_sosiki_cd . "')";
			$this->execWithPDOLog($pdo, $sql, 'WPO080  店舗看板データ削除');

			// 出荷帳票データの削除(IF_OZAX_SHUKKAの業態倉庫・出荷日・方面名・バッチ№でデータを削除）
			$sql = "DELETE FROM T_OZAX_SHUKKA_CHOHYO ";
			$sql .= " WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "   AND SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$sql .= "   AND (GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI) IN (SELECT GYOTAI_SOKO_CODE,SHUKKA_BI,BATCH_NUMBER,HOMEN_MEI FROM IF_OZAX_SHUKKA";
			$sql .= "        WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "          AND SOSIKI_CD = '" . $this->_sosiki_cd . "')";
			$this->execWithPDOLog($pdo, $sql, 'WPO080  出荷帳票データ削除');

			// 2017/09/28 K.Araishi Modify Start 出荷指示読替マスタによるデータ変換
			$sql = " SELECT NINUSI_CD";
			$sql .= ",SOSIKI_CD";
			$sql .= ",MOTO_DAIHYO_SOKO_CODE";
			$sql .= ",MOTO_GYOTAI_SOKO_CODE";
			$sql .= ",MOTO_HOMEN_MEI";
			$sql .= ",MOTO_TOKUISAKI_CODE";
			$sql .= ",MOTO_TENPO_CODE";
			$sql .= ",DAIHYO_SOKO_CODE";
			$sql .= ",DAIHYO_SOKO_MEI";
			$sql .= ",GYOTAI_SOKO_CODE";
			$sql .= ",GYOTAI_SOKO_MEI";
			$sql .= ",HOMEN_CODE";
			$sql .= ",HOMEN_MEI";
			$sql .= ",TOKUISAKI_CODE";
			$sql .= ",TOKUISAKI_MEI";
			$sql .= ",TENPO_CODE";
			$sql .= ",TENPO_MEI";
			$sql .= " FROM M_OZAX_SHUKKA_HENKAN";
			$sql .= ' WHERE ';
			$sql .= " NINUSI_CD='" . $this->_ninusi_cd . "'";
			$sql .= " AND SOSIKI_CD='" . $this->_sosiki_cd . "'";
			$sql .= ";";
			$this->queryWithPDOLog($stmt_henkan,$pdo,$sql,"出荷指示読替マスタデータ 取得");

			while($result_henkan = $stmt_henkan->fetch(PDO::FETCH_ASSOC)){
				// 出荷指示の変換更新クエリーの作成
				$sql  = "UPDATE IF_OZAX_SHUKKA SET ";
				$sql  .= "TORIKOMI_STAFF  = '" . $staffCd . "'";
				$sql  .= ",TORIKOMI_NICHIJI  = NOW()";
				$where = " WHERE 1=1 ";

				// 代表倉庫コードの変換
				if  (!empty($result_henkan['MOTO_DAIHYO_SOKO_CODE']) && !is_null($result_henkan['MOTO_DAIHYO_SOKO_CODE'])) {
					$sql .= ",DAIHYO_SOKO_CODE = '" . $result_henkan['DAIHYO_SOKO_CODE'] . "'";
					$where .= " AND DAIHYO_SOKO_CODE = '" . $result_henkan['MOTO_DAIHYO_SOKO_CODE'] . "'";
					// 代表倉庫名の変換
					if (!empty($result_henkan['DAIHYO_SOKO_MEI']) && !is_null($result_henkan['DAIHYO_SOKO_MEI'])) {
						$sql .= ",DAIHYO_SOKO_MEI = '" . $result_henkan['DAIHYO_SOKO_MEI'] . "'";
					}
				}
				// 業態倉庫コードの変換
				if (!empty($result_henkan['MOTO_GYOTAI_SOKO_CODE']) && !is_null($result_henkan['MOTO_GYOTAI_SOKO_CODE'])) {
					$sql .= ",GYOTAI_SOKO_CODE = '" . $result_henkan['GYOTAI_SOKO_CODE'] . "'";
					$where .= " AND GYOTAI_SOKO_CODE = '" . $result_henkan['MOTO_GYOTAI_SOKO_CODE'] . "'";
					// 業態倉庫名の変換
					if (!empty($result_henkan['GYOTAI_SOKO_MEI']) && !is_null($result_henkan['GYOTAI_SOKO_MEI'])) {
						$sql .= ",GYOTAI_SOKO_MEI = '" . $result_henkan['GYOTAI_SOKO_MEI'] . "'";
					}
				}
				// 方面コード・名の変換
				if (!empty($result_henkan['MOTO_HOMEN_MEI']) && !is_null($result_henkan['MOTO_HOMEN_MEI'])) {
					if (!empty($result_henkan['HOMEN_CODE']) && !is_null($result_henkan['HOMEN_CODE'])) {
						$sql .= ",HOMEN_CODE = '" . $result_henkan['HOMEN_CODE'] . "'";
					}
					$sql .= ",HOMEN_MEI = '" . $result_henkan['HOMEN_MEI'] . "'";
					$where .= " AND HOMEN_MEI = '" . $result_henkan['MOTO_HOMEN_MEI'] . "'";
				}
				// 得意先コード・名の変換
				if (!empty($result_henkan['MOTO_TOKUISAKI_CODE']) && !is_null($result_henkan['MOTO_TOKUISAKI_CODE'])) {
					if (!empty($result_henkan['TOKUISAKI_MEI']) && !is_null($result_henkan['TOKUISAKI_MEI'])) {
						$sql .= ",TOKUISAKI_MEI = '" . $result_henkan['TOKUISAKI_MEI'] . "'";
					}
					$sql .= ",TOKUISAKI_CODE = '" . $result_henkan['TOKUISAKI_CODE'] . "'";
					$where .= " AND TOKUISAKI_CODE = '" . $result_henkan['MOTO_TOKUISAKI_CODE'] . "'";
				}
				// 店舗コード・名の変換
				if (!empty($result_henkan['MOTO_TENPO_CODE']) && !is_null($result_henkan['MOTO_TENPO_CODE'])) {
					if (!empty($result_henkan['TENPO_MEI']) && !is_null($result_henkan['TENPO_MEI'])) {
						$sql .= ",TENPO_MEI = '" . $result_henkan['TENPO_MEI'] . "'";
					}
					$sql .= ",TENPO_CODE = '" . $result_henkan['TENPO_CODE'] . "'";
					$where .= " AND TENPO_CODE = '" . $result_henkan['MOTO_TENPO_CODE'] . "'";
				}
				// 出荷指示データ変換クエリー実行
				$sql .= $where;
				$this->execWithPDOLog($pdo, $sql, 'WPO080 出荷指示データ変換クエリー実行');

			}
			// 2017/09/28 K.Araishi Modify End

			// 出荷指示データのデータ作成
			$sql = "INSERT INTO T_OZAX_SHUKKA(NINUSI_CD,SOSIKI_CD,GYOTAI_SOKO_CODE,GYOTAI_SOKO_MEI,SHUKKA_BI ";
			$sql .= ",NOHIN_BI,SHUKKA_TORIHIKI_KUBUN,SHUKKA_KINKYU_KUBUN,BIN,HOMEN_CODE";
			$sql .= ",HOMEN_MEI,BUMON_CODE,BUMON_MEI,TOKUISAKI_CODE,TOKUISAKI_MEI";
			$sql .= ",TENPO_CODE,TENPO_MEI,TOKUISAKI_BUMON_MEI,AREA_CODE,AREA_MEI";
			$sql .= ",BATCH_NUMBER,SHUKKA_JYUN,DAIHYO_SOKO_CODE,DAIHYO_SOKO_MEI,LOCATION,SHOHIN_CODE";
			$sql .= ",SHOHIN_MEI,KIKAKU,ROT_NUMBER,ZAIKO_KUBUN,ZAIKO_KUBUN_MEI";
			$sql .= ",RYOHIN_KUBUN,RYOHIN_KUBUN_MEI,NYUKA_BI,SEIZO_BI,SYOMI_KIGEN";
			$sql .= ",SS_NUMBER,SS_NUMBER_LINE,SHUKKA_NUMBER,SHUKKA_NUMBER_LINE,NYUKO_NUMBER";
			$sql .= ",NISUGATA_1_SHIJISU,NISUGATA_2_SHIJISU,NISUGATA_3_SHIJISU,SO_BARA_SU,TOROKU_STAFF";
			$sql .= ",TOROKU_DATE,KOSIN_STAFF,KOSIN_DATE";
			$sql .= ")";
			$sql .= " SELECT NINUSI_CD,SOSIKI_CD,GYOTAI_SOKO_CODE,GYOTAI_SOKO_MEI,SHUKKA_BI";
			$sql .= ",NOHIN_BI,SHUKKA_TORIHIKI_KUBUN,SHUKKA_KINKYU_KUBUN,BIN,HOMEN_CODE";
			$sql .= ",HOMEN_MEI,BUMON_CODE,BUMON_MEI,TOKUISAKI_CODE,TOKUISAKI_MEI";
			$sql .= ",TENPO_CODE,TENPO_MEI,TOKUISAKI_BUMON_MEI,AREA_CODE,AREA_MEI";
			$sql .= ",BATCH_NUMBER,SHUKKA_JYUN,DAIHYO_SOKO_CODE,DAIHYO_SOKO_MEI,LOCATION,SHOHIN_CODE";
			$sql .= ",SHOHIN_MEI,KIKAKU,ROT_NUMBER,ZAIKO_KUBUN,ZAIKO_KUBUN_MEI";
			$sql .= ",RYOHIN_KUBUN,RYOHIN_KUBUN_MEI,NYUKA_BI,SEIZO_BI,SYOMI_KIGEN";
			$sql .= ",SS_NUMBER,SS_NUMBER_LINE,SHUKKA_NUMBER,SHUKKA_NUMBER_LINE,NYUKO_NUMBER";
			$sql .= ",NISUGATA_3_SHIJISU,NISUGATA_2_SHIJISU,NISUGATA_1_SHIJISU,SO_BARA_SU,";
			$sql .= $staffCd . ",NOW()," . $staffCd .",NOW()";
			$sql .= " FROM IF_OZAX_SHUKKA";
			$sql .= " WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "   AND SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$this->execWithPDOLog($pdo, $sql, 'WPO080  出荷指示データ作成');

			// トータルピッキングデータの作成
			$sql  = "INSERT INTO T_OZAX_TOTAL_PK";
			$sql .= " SELECT NINUSI_CD";
			$sql .= ",SOSIKI_CD";
			$sql .= ",GYOTAI_SOKO_CODE";
			$sql .= ",GYOTAI_SOKO_MEI";
			$sql .= ",SHUKKA_BI";
			$sql .= ",NOHIN_BI";
			$sql .= ",TOKUISAKI_CODE";
			$sql .= ",TOKUISAKI_MEI";
			$sql .= ",BATCH_NUMBER";
			$sql .= ",HOMEN_CODE";
			$sql .= ",HOMEN_MEI";
			$sql .= ",LOCATION";
			$sql .= ",SHOHIN_CODE";
			$sql .= ",SHOHIN_MEI";
			$sql .= ",SYOMI_KIGEN";
			$sql .= ",NULL";
			$sql .= ",SUM(NISUGATA_3_SHIJISU)";
			$sql .= ",SUM(NISUGATA_2_SHIJISU)";
			$sql .= ",SUM(NISUGATA_1_SHIJISU)";
			$sql .= ",SUM(SO_BARA_SU)";
			$sql .= ",0,0,0,0,'0',NULL,NULL";
			$sql .= ",'" . $staffCd . "',NOW()";
			$sql .= ",'" . $staffCd . "',NOW()";
			$sql .= " FROM IF_OZAX_SHUKKA";
			$sql .= ' WHERE ';
			$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "'";
			$sql .= " GROUP BY NINUSI_CD";
			$sql .= ",SOSIKI_CD";
			$sql .= ",GYOTAI_SOKO_CODE";
			$sql .= ",SHUKKA_BI";
			$sql .= ",BATCH_NUMBER";
			$sql .= ",HOMEN_MEI";
			$sql .= ",LOCATION";
			$sql .= ",SHOHIN_CODE";
			$sql .= ",SYOMI_KIGEN";
			$sql .= ";";
			$this->execWithPDOLog($pdo, $sql, 'WPO080 トータルピッキングデータ作成');

			$sql = " SELECT NINUSI_CD";
			$sql .= ",SOSIKI_CD";
			$sql .= ",GYOTAI_SOKO_CODE";
			$sql .= ",SHUKKA_BI";
			$sql .= ",BATCH_NUMBER";
			$sql .= ",HOMEN_MEI";
			$sql .= ",LOCATION_CODE";
			$sql .= ",SHOHIN_CODE";
			$sql .= ",SYOMI_KIGEN";
			$sql .= ",TOTAL_PK_NUMBER";
			$sql .= " FROM T_OZAX_TOTAL_PK";
			$sql .= ' WHERE ';
			$sql .= " NINUSI_CD='" . $this->_ninusi_cd . "'";
			$sql .= " AND SOSIKI_CD='" . $this->_sosiki_cd . "'";
			$sql .= " AND (TOTAL_PK_NUMBER = '' or TOTAL_PK_NUMBER IS NULL)";
			$sql .= " GROUP BY NINUSI_CD";
			$sql .= ",SOSIKI_CD";
			$sql .= ",GYOTAI_SOKO_CODE";
			$sql .= ",SHUKKA_BI";
			$sql .= ",BATCH_NUMBER";
			$sql .= ",HOMEN_MEI";
			$sql .= ",LOCATION_CODE";
			$sql .= ",SHOHIN_CODE";
			$sql .= ",SYOMI_KIGEN";
			$sql .= ",TOTAL_PK_NUMBER";
			$sql .= ";";
			$this->queryWithPDOLog($stmt_totalpk,$pdo,$sql,"トータルピッキング№更新データ 取得");

			while($result_totalpk = $stmt_totalpk->fetch(PDO::FETCH_ASSOC)){
				// 採番テーブルより採番値を取得
				$sql = "CALL P_GET_SAIBAN(";
				$sql .= "'" . $result_totalpk['NINUSI_CD'] . "'";
				$sql .= ",'" . $result_totalpk['SOSIKI_CD'] . "'";
				$sql .= ",'4'";
				$sql .= ",@renban";
				$sql .= ")";
				$this->execWithPDOLog($pdo, $sql, 'WPO080 採番テーブル更新');

				$sql = " SELECT @renban";
				$this->queryWithPDOLog($stmt_saiban,$pdo,$sql,"採番データ 取得");
				$result_saiban = $stmt_saiban->fetch(PDO::FETCH_ASSOC);
				$saiban = $result_saiban["@renban"];

				// トータルピッキング№を更新する
				$sql = "UPDATE T_OZAX_TOTAL_PK SET";
				$sql .= " TOTAL_PK_NUMBER = '" . $saiban . "'";
				$sql .= " WHERE NINUSI_CD='" . $result_totalpk['NINUSI_CD'] . "'";
				$sql .= "   AND SOSIKI_CD='" . $result_totalpk['SOSIKI_CD'] . "'";
				$sql .= "   AND SHUKKA_BI='" . $result_totalpk['SHUKKA_BI'] . "'";
				$sql .= "   AND BATCH_NUMBER='" . $result_totalpk['BATCH_NUMBER'] . "'";
				$sql .= "   AND HOMEN_MEI='" . $result_totalpk['HOMEN_MEI'] . "'";
				$sql .= "   AND LOCATION_CODE='" . $result_totalpk['LOCATION_CODE'] . "'";
				$sql .= "   AND SHOHIN_CODE='" . $result_totalpk['SHOHIN_CODE'] . "'";
				$sql .= "   AND SYOMI_KIGEN='" . $result_totalpk['SYOMI_KIGEN'] . "';";
				$this->execWithPDOLog($pdo, $sql, 'WPO080 トータルピッキングデータ採番更新');

				// トータルピッキングiPodデータ作成
				$sql  = "INSERT INTO T_OZAX_IPOD_TOTAL_PK";
				$sql .= " SELECT NINUSI_CD";
				$sql .= ",SOSIKI_CD";
				$sql .= ",GYOTAI_SOKO_CODE";
				$sql .= ",GYOTAI_SOKO_MEI";
				$sql .= ",SHUKKA_BI";
				$sql .= ",NOHIN_BI";
				$sql .= ",TOKUISAKI_CODE";
				$sql .= ",TOKUISAKI_MEI";
				$sql .= ",BATCH_NUMBER";
				$sql .= ",HOMEN_CODE";
				$sql .= ",HOMEN_MEI";
				$sql .= ",LOCATION_CODE";
				$sql .= ",SHOHIN_CODE";
				$sql .= ",SHOHIN_MEI";
				$sql .= ",SYOMI_KIGEN";
				$sql .= ",''";
				$sql .= ",TOTAL_PK_NUMBER";
				$sql .= ",NISUGATA_1_SHIJISU";
				$sql .= ",NISUGATA_2_SHIJISU";
				$sql .= ",NISUGATA_3_SHIJISU";
				$sql .= ",SO_BARA_SU";
				$sql .= ",NISUGATA_1_SHIJISU";
				$sql .= ",NISUGATA_2_SHIJISU";
				$sql .= ",NISUGATA_3_SHIJISU";
				$sql .= ",SO_BARA_SU";
				$sql .= ",0,0,0,0,0";
				$sql .= ",'" . $staffCd . "',NOW()";
				$sql .= ",'" . $staffCd . "',NOW()";
				$sql .= " FROM T_OZAX_TOTAL_PK";
				$sql .= " WHERE NINUSI_CD='" . $result_totalpk['NINUSI_CD'] . "'";
				$sql .= "   AND SOSIKI_CD='" . $result_totalpk['SOSIKI_CD'] . "'";
				$sql .= "   AND SHUKKA_BI='" . $result_totalpk['SHUKKA_BI'] . "'";
				$sql .= "   AND BATCH_NUMBER='" . $result_totalpk['BATCH_NUMBER'] . "'";
				$sql .= "   AND HOMEN_MEI='" . $result_totalpk['HOMEN_MEI'] . "'";
				$sql .= "   AND LOCATION_CODE='" . $result_totalpk['LOCATION_CODE'] . "'";
				$sql .= "   AND SHOHIN_CODE='" . $result_totalpk['SHOHIN_CODE'] . "'";
				$sql .= "   AND SYOMI_KIGEN='" . $result_totalpk['SYOMI_KIGEN'] . "';";
				$sql .= ";";
				$this->execWithPDOLog($pdo, $sql, 'WPO080 トータルピッキングiPodデータ作成');

			}

			// IF出荷指示データの削除
			$sql = "DELETE FROM if_ozax_shukka";
			$sql .= " WHERE NINUSI_CD = '" . $this->_ninusi_cd . "'";
			$sql .= "   AND SOSIKI_CD = '" . $this->_sosiki_cd . "'";
			$this->execWithPDOLog($pdo, $sql, 'WPO080  IF_商品マスタ削除');

			$pdo->commit();
			$pdo = null;

			return;

		} catch (Exception $e) {
			$this->printLog("info", "例外発生", "WPO080", $e->getMessage());
			$pdo->rollBack();
			$pdo = null;
			$this->errors['Check'][] = "出荷指示更新時にエラーが発生しました。";
			$this->errors['Check'][] = $e->getMessage();
		}

		return false;

	}

	/**
	 * ＩＦ出荷指示ＣＳＶデータ取得
	 * @access   public
	 * @return   ＩＦ出荷指示ＣＳＶデータ
	 */
	function getCSV() {

		$text = array();

		//ヘッダー情報
		$header = "チェック内容,倉庫コード,倉庫名,出荷日,納品日,出荷取引区分,出荷緊急区分";
		$header .= ",便,方面コード,方面名,部門コード,部門名";
		$header .= ",得意先コード,得意先名,店舗コード,店舗名,得意先部門名";
		$header .= ",エリアコード,エリア名,バッチ№,出荷順";
		$header .= ",代表倉庫コード,代表倉庫名,ロケーション,商品コード";
		$header .= ",商品名,規格,ロット№,在庫区分,在庫区分名";
		$header .= ",良品区分,良品区分名,入荷日,製造日,賞味期限";
		$header .= ",SS№,SS№行,出荷№,出荷№行,入庫№";
		$header .= ",荷姿１指示数,荷姿２指示数,荷姿３指示数,総バラ数,予備項目１";
		$header .= ",予備項目２,予備項目３,予備項目４,予備項目５,予備項目６";
		$header .= ",取込スタッフ,取込日時";

		$text[] = $header;

		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			$sql  = "SELECT *";
			$sql .= " FROM IF_OZAX_SHUKKA";
			$sql .= ' WHERE ';
			$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "'";
			$sql .= " ORDER BY";
			$sql .= "  CHECK_NAIYO";
			$sql .= ";";
			$this->queryWithPDOLog($stmt,$pdo,$sql,"ＩＦ出荷指示CSVデータ 取得");
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$tmpText = '';
				$tmpText .= $result['CHECK_NAIYO'] . ',';
				$tmpText .= $result['GYOTAI_SOKO_CODE'] . ',';
				$tmpText .= $result['GYOTAI_SOKO_MEI'] . ',';
				$tmpText .= $result['SHUKKA_BI'] . ',';
				$tmpText .= $result['NOHIN_BI'] . ',';
				$tmpText .= $result['SHUKKA_TORIHIKI_KUBUN'] . ',';
				$tmpText .= $result['SHUKKA_KINKYU_KUBUN'] . ',';
				$tmpText .= $result['BIN'] . ',';
				$tmpText .= $result['HOMEN_CODE'] . ',';
				$tmpText .= $result['HOMEN_MEI'] . ',';
				$tmpText .= $result['BUMON_CODE'] . ',';
				$tmpText .= $result['BUMON_MEI'] . ',';
				$tmpText .= $result['TOKUISAKI_CODE'] . ',';
				$tmpText .= $result['TOKUISAKI_MEI'] . ',';
				$tmpText .= $result['TENPO_CODE'] . ',';
				$tmpText .= $result['TENPO_MEI'] . ',';
				$tmpText .= $result['TOKUISAKI_BUMON_MEI'] . ',';
				$tmpText .= $result['AREA_CODE'] . ',';
				$tmpText .= $result['AREA_MEI'] . ',';
				$tmpText .= $result['BATCH_NUMBER'] . ',';
				$tmpText .= $result['SHUKKA_JYUN'] . ',';
				$tmpText .= $result['DAIHYO_SOKO_CODE'] . ',';
				$tmpText .= $result['DAIHYO_SOKO_MEI'] . ',';
				$tmpText .= $result['LOCATION'] . ',';
				$tmpText .= $result['SHOHIN_CODE'] . ',';
				$tmpText .= $result['SHOHIN_MEI'] . ',';
				$tmpText .= $result['KIKAKU'] . ',';
				$tmpText .= $result['ROT_NUMBER'] . ',';
				$tmpText .= $result['ZAIKO_KUBUN'] . ',';
				$tmpText .= $result['ZAIKO_KUBUN_MEI'] . ',';
				$tmpText .= $result['RYOHIN_KUBUN'] . ',';
				$tmpText .= $result['RYOHIN_KUBUN_MEI'] . ',';
				$tmpText .= $result['NYUKA_BI'] . ',';
				$tmpText .= $result['SEIZO_BI'] . ',';
				$tmpText .= $result['SYOMI_KIGEN'] . ',';
				$tmpText .= $result['SS_NUMBER'] . ',';
				$tmpText .= $result['SS_NUMBER_LINE'] . ',';
				$tmpText .= $result['SHUKKA_NUMBER'] . ',';
				$tmpText .= $result['SHUKKA_NUMBER_LINE'] . ',';
				$tmpText .= $result['NYUKO_NUMBER'] . ',';
				$tmpText .= $result['NISUGATA_3_SHIJISU'] . ',';
				$tmpText .= $result['NISUGATA_2_SHIJISU'] . ',';
				$tmpText .= $result['NISUGATA_1_SHIJISU'] . ',';
				$tmpText .= $result['SO_BARA_SU'] . ',';
				$tmpText .= $result['YOBI_1'] . ',';
				$tmpText .= $result['YOBI_2'] . ',';
				$tmpText .= $result['YOBI_3'] . ',';
				$tmpText .= $result['YOBI_4'] . ',';
				$tmpText .= $result['YOBI_5'] . ',';
				$tmpText .= $result['YOBI_6'] . ',';
				$tmpText .= $result['TORIKOMI_STAFF'] . ',';
				$tmpText .= $result['TORIKOMI_NICHIJI'];

				$text[] = $tmpText;
			}

			$pdo = null;

		} catch (Exception $e) {

			$this->printLog("fatal", "例外発生", "WPO080", $e->getMessage());
			$pdo = null;
		}

		return $text;
	}

}

?>