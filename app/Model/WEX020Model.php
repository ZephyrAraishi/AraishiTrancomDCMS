<?php
/**
 * WEX020
 *
 * 荷主契約情報登録画面
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('DCMSAppModel', 'Model');
App::uses('MMessage', 'Model');
App::uses('Validation', 'Utility');

class WEX020Model extends DCMSAppModel {

	public $name = 'WEX020Model';
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
       
		//メッセージマスタ呼び出し
		$this->MMessage = new MMessage($ninusi_cd,$sosiki_cd,$kino_id);
		
    }

	/**
	 * 荷主契約情報データ取得
	 * @access   public
	 * @return   荷主契約情報データ
	 */
	public function getNKeiyakuLst($data) {
	
		try {

			/* ▼取得カラム設定 */
			$fields = '*';
	
			/* ▼検索条件設定 */
			$condition  = "NINUSI_CD = '". $this->_ninusi_cd. "' AND SOSIKI_CD = '". $this->_sosiki_cd. "'" ;
	
			/* 荷主契約情報取得 */
			$sql  = 'SELECT ';
			$sql .= "$fields ";
			$sql .= 'FROM V_WEX020_M_NINUSI_KEIYAKU M_NINUSI_KEIYAKU ';
			$sql .= 'WHERE ';
			$sql .= $condition;
	
			$ret = $this->queryWithLog($sql, $condition, "中分類情報データ取得");
	
			$editData = $this->editData($ret);
			return $editData;
		
		} catch (Exception $e) {
			
			$this->printLog("fatal", "例外発生", "WEX020", $e->getMessage());
		}
		
		return null;
	}

	/**
	 * 更新処理
	 * @param unknown $data
	 * @throws Exception
	 * @return boolean
	 */
	public function dataUpdate($jsonData){
		
		$data = $jsonData->data;
		$changeHeaderFlg = false;
		
		// ヘッダ部が変更されているかどうか判定
		if ($jsonData->pre_kbn_keiyaku != $jsonData->kbn_keiyaku
				|| $jsonData->pre_seikyu_sime != $jsonData->seikyu_sime
				|| $jsonData->pre_siharai_sight != $jsonData->siharai_sight) {
			$changeHeaderFlg = true;
		}

		$dataSource = $this->getDataSource();
		$dataSource->begin();
		
		foreach($data as $val) {
			try {

				// 登録
				if ($val->changed == 2) {

					$params = "";
		 	 		$params .=  " '".$this->_ninusi_cd."'";
		  			$params .=  ",'".$this->_sosiki_cd."'" ;
					$params .=  ",'".$val->bnri_dai_cd."'";
					$params .=  ",'".$val->bnri_cyu_cd."'";
					if(Validation::notEmpty($val->suryo)){
						$params .=  ",".$val->suryo;
					} else {
						$params .=  ", null";
					}
					if(Validation::notEmpty($val->hiyou)){
						$params .=  ",".$val->hiyou;
					} else {
						$params .=  ", null";
					}
					$params .=  ",'".$val->exp."'";
					$params .=  ",'".$jsonData->kbn_keiyaku."'";
					$params .=  "," .$jsonData->seikyu_sime;
					$params .=  "," .$jsonData->siharai_sight;
					$params .=  ",'".$_SESSION['staff_cd']."'";
					$params .=  ',@r';

					$sql = "CALL P_WEX020_INS_NINUSI_KEIYAKU($params)";

					$this->queryWithLog($sql, $params, "荷主契約情報　登録");

					$data = $this->query("SELECT @r",false);
					
					if ($data[0][0]['@r'] != 0) {
	 					$dataSource->rollback();
						$this->printLog('error', '荷主契約情報　登録', "WEX020", '異常終了 戻り値='.$data[0][0]['@r']);
						
						$message = $this->MMessage->getOneMessage('CMNE000107');
						$this->errors['Check'][] =  $message;
		 				return false;
					}

					// 削除
				} else if ($val->changed == 3 || $val->changed == 4) {
					
					$params = "";
					$params .=  " '".$this->_ninusi_cd."'";
					$params .=  ",'".$this->_sosiki_cd."'" ;
					$params .=  ",'".$val->bnri_dai_cd."'";
					$params .=  ",'".$val->bnri_cyu_cd."'";
					$params .=  ",'".$_SESSION['staff_cd']."'";
					$params .=  ',@r';
					
					$sql = "CALL P_WEX020_DEL_NINUSI_KEIYAKU($params)";
					
					$this->queryWithLog($sql, $params, "荷主契約情報　削除");
					
					$data = $this->query("SELECT @r",false);
						
					if ($data[0][0]['@r'] != 0) {
						$dataSource->rollback();
						$this->printLog('error', '荷主契約情報　削除', "WEX020", '異常終了 戻り値='.$data[0][0]['@r']);
					
						$message = $this->MMessage->getOneMessage('CMNE000107');
						$this->errors['Check'][] =  $message;
						return false;
					}
					
					// 更新
				} else if ($changeHeaderFlg == true || $val->changed == 1) {
					
					$params = "";
					$params .=  " '".$this->_ninusi_cd."'";
					$params .=  ",'".$this->_sosiki_cd."'" ;
					$params .=  ",'".$val->bnri_dai_cd."'";
					$params .=  ",'".$val->bnri_cyu_cd."'";
					if(Validation::notEmpty($val->suryo)){
						$params .=  ",".$val->suryo;
					} else {
						$params .=  ", null";
					}
					if(Validation::notEmpty($val->hiyou)){
						$params .=  ",".$val->hiyou;
					} else {
						$params .=  ", null";
					}
					$params .=  ",'".$val->exp."'";
					$params .=  ",'".$jsonData->kbn_keiyaku."'";
					$params .=  "," .$jsonData->seikyu_sime;
					$params .=  "," .$jsonData->siharai_sight;
					$params .=  ",'".$_SESSION['staff_cd']."'";
					$params .=  ',@r';
						
					$sql = "CALL P_WEX020_UPD_NINUSI_KEIYAKU($params)";
						
					$this->queryWithLog($sql, $params, "荷主契約情報　更新");
						
					$data = $this->query("SELECT @r",false);
					
					if ($data[0][0]['@r'] != 0) {
						$dataSource->rollback();
						$this->printLog('error', '荷主契約情報　更新', "WEX020", '異常終了 戻り値='.$data[0][0]['@r']);
							
						$message = $this->MMessage->getOneMessage('CMNE000107');
						$this->errors['Check'][] =  $message;
						return false;
					}
				}
				
			} catch (Exception $e) {
			
				$dataSource->rollback();
				$this->printLog("fatal", "例外発生", "WEX020", $e->getMessage());
				
				$message = $this->MMessage->getOneMessage('CMNE000107');
				$this->errors['Check'][] =  $message;
				return false;
			}
		}
		
	 	$this->printLog('info', '荷主契約情報更新', "WEX020", '正常終了');
		$dataSource->commit();
		return true;
	}

	/**
	 * 更新前チェック
	 */
	public function checkBeforeUpdate($jsonData){
		
		$changeHeaderFlg = false;
		
		// ヘッダ部が変更されているかどうか判定
		if ($jsonData->pre_kbn_keiyaku != $jsonData->kbn_keiyaku
				|| $jsonData->pre_seikyu_sime != $jsonData->seikyu_sime
				|| $jsonData->pre_siharai_sight != $jsonData->siharai_sight) {
			$changeHeaderFlg = true;
		}
		
		// ヘッダ部のチェック
		if(!Validation::notEmpty($jsonData->kbn_keiyaku)){
			$msg = vsprintf($this->MMessage->getOneMessage('CMNE000001'), '契約形態');
			$this->errors['Check'][] =  $msg;
			return false;
		}
		if (!Validation::notEmpty($jsonData->seikyu_sime)) {
			$msg = vsprintf($this->MMessage->getOneMessage('CMNE000001'), '請求締日');
			$this->errors['Check'][] =  $msg;
			return false;
		} else if(!Validation::maxLength($jsonData->seikyu_sime, 2)) {
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE020002'),  array('請求締日', '2'));
			$this->errors['Check'][] =  $msg;
			return false;
		} else if(!Validation::numeric($jsonData->seikyu_sime)) {
			$msg = vsprintf($this->MMessage->getOneMessage('CMNE000002'),  '請求締日');
			$this->errors['Check'][] =  $msg;
			return false;
		} else if(!Validation::range($jsonData->seikyu_sime, 0, 32)){
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE020004'), array('請求締日', '1', '31'));
			$this->errors['Check'][] =  $msg;
			return false;
		}
		if (!Validation::notEmpty($jsonData->siharai_sight)) {
			$msg = vsprintf($this->MMessage->getOneMessage('CMNE000001'),  '支払サイト');
			$this->errors['Check'][] =  $msg;
			return false;
		} else if(!Validation::maxLength($jsonData->siharai_sight, 3)) {
			$msg = vsprintf($this->MMessage->getOneMessage('WEXE020002'), array('支払サイト','3'));
			$this->errors['Check'][] =  $msg;
			return false;
		} else if(!Validation::numeric($jsonData->siharai_sight)) {
			$msg = vsprintf($this->MMessage->getOneMessage('CMNE000002'), '支払サイト');
			$this->errors['Check'][] =  $msg;
			return false;
		}
	
		$i = 0;
		$updCnt = 0;
		$data = $jsonData->data;
		foreach($data as $val) {
			$i++;
			$premsg = $i."行目 ";
			if ($val->changed == '1' || $val->changed == '2' || $val->changed == '3' || $val->changed == '4') {
				$updCnt++;
			}
			if ($val->changed == '4' || $val->changed == '5') {
				continue;
			}

			// 数量と費用は一方入力は不可
			if(!Validation::notEmpty($val->suryo)
				&& Validation::notEmpty($val->hiyou)){
				$msg = vsprintf($this->MMessage->getOneMessage('CMNE000001'), $premsg.'数量');
				$this->errors['Check'][] =  $msg;
				return false;
			} else if(Validation::notEmpty($val->suryo)
				&& !Validation::notEmpty($val->hiyou)){
				$msg = vsprintf($this->MMessage->getOneMessage('CMNE000001'), $premsg.'費用');
				$this->errors['Check'][] =  $msg;
				return false;
			}
			// 数量
			if(Validation::notEmpty($val->suryo)
					&& !Validation::numeric($val->suryo)){
				$msg = vsprintf($this->MMessage->getOneMessage('CMNE000002'), $premsg.'数量');
				$this->errors['Check'][] =  $msg;
				return false;
			}
			// 費用
			if(Validation::notEmpty($val->hiyou)
					&& !Validation::numeric($val->hiyou)){
				$msg = vsprintf($this->MMessage->getOneMessage('CMNE000002'), $premsg.'費用');
				$this->errors['Check'][] =  $msg;
				return false;
			}
			// 説明
			if(!Validation::maxLength($val->exp, 20)){
				$msg = vsprintf($this->MMessage->getOneMessage('CMNE000001'), $premsg.'説明');
				$msg = vsprintf($this->MMessage->getOneMessage('WEXE020002'), array($premsg.'説明','20'));
  				$this->errors['Check'][] =  $msg;
				return false;
			} else if(!Validation::notEmpty($val->suryo) && !Validation::notEmpty($val->hiyou)
				 && Validation::notEmpty($val->exp)){
				$msg = vsprintf($this->MMessage->getOneMessage('CMNE000001'), $premsg.'説明');
				$this->errors['Check'][] =  $msg;
				return false;
			}
		}

		//CHECK用にSORT　　---start
		$dataSort = array();
		$dataSort_DAI_CD = array();
		$dataSort_CYU_CD = array();

		//配列をソート用にコピー
		$count = 0;
		while ( $count <count($data)){
			$val = $data[$count];

			$dataSort[$count]['bnri_dai_cd'] = $val->bnri_dai_cd;
			$dataSort[$count]['bnri_cyu_cd'] = $val->bnri_cyu_cd;
			$dataSort[$count]['changed']     = $val->changed;
			$dataSort[$count]['line_count']  = $count;

			$dataSort_DAI_CD[$count]   = $val->bnri_dai_cd;
			$dataSort_CYU_CD[$count]   = $val->bnri_cyu_cd;
			$dataSort_COUNT[$count]    = $count;

			$count++;
		}

		//配列をソート
		array_multisort($dataSort_DAI_CD,SORT_ASC,
		                $dataSort_CYU_CD,SORT_ASC,
		                $dataSort_COUNT,SORT_ASC,
		                $dataSort);
		//CHECK用にSORT　　---end

		// 重複　＆　大分類と中分類の整合性チェック
		if ($this->checkInputKey($dataSort) == false) {
			
			return false;
		}
		
		// 変更がない場合
		if ($changeHeaderFlg == false && $updCnt == 0) {
			$msg = $this->MMessage->getOneMessage('CMNE000101');
			$this->errors['Check'][] =  $msg;
			return false;
		}

		return true;
	}



	/**
	 * 重複　＆　大分類と中分類の整合性チェック
	 */
	private function checkInputKey($data){

		$stok_bnri_dai_cd = "";
		$stok_bnri_cyu_cd = "";
		$dai_cd_only_flag = "0";
		$err_flag         = "0";

		$i = 0;

		foreach($data as $val) {

			$bnri_dai_cd = $val['bnri_dai_cd'];
			$bnri_cyu_cd = $val['bnri_cyu_cd'];
			$changed     = $val['changed'];
			$line_count  = $val['line_count'];
			
			if (3 < $changed) {
				continue;
			}
			
			if ( $bnri_dai_cd == $stok_bnri_dai_cd
			and  $bnri_cyu_cd == $stok_bnri_cyu_cd){
				//キー重複エラー
				$msg = vsprintf($this->MMessage->getOneMessage('WEXE020006'), ($line_count + 1));
				$this->errors['Check'][] =  $msg;
				$err_flag = "1";
			} else {

				//　０：なし　　　　　　何もしない
				//　１：　修正　　　　　UPDATE
				//　２：　追加　　　　　INSERT
				//　３：　削除　　　　　DELETE
				//　４：　修正→削除　　DELETE
				//　５：　追加→削除　　何もしない

				if ( $bnri_dai_cd == $stok_bnri_dai_cd
				and  $bnri_cyu_cd != "000"){
					//大分類のみがあるのに、中分類も入力してるのがあったらエラー
					if ( $dai_cd_only_flag == "1" ){
						$msg = vsprintf($this->MMessage->getOneMessage('WEXE020005'), ($line_count + 1));
						$this->errors['Check'][] =  $msg;
						$err_flag = "1";
					}
				}
			}

			if ( $bnri_dai_cd != $stok_bnri_dai_cd ){
					//大分類のみのレコード有無で、フラグセット
				if ($bnri_cyu_cd == "000") {
					$dai_cd_only_flag = "1";
				} else {
					$dai_cd_only_flag = "0";
				}
			}

			//次用に退避
			$stok_bnri_dai_cd = $bnri_dai_cd;
			$stok_bnri_cyu_cd = $bnri_cyu_cd;

			$i++;
		}

		if ( $err_flag == "1" ){
			return false;
		}

		return true;
	}
	
	/**
	 * タイムスタンプチェック
	 *
	 * @access   public
	 * @param    $initTimestamp 初期表示時のタイムスタンプ
	 * @return 正常な場合true、そうでない場合false
	 */
	public function checkTimestamp($initTimestamp) {
	
		$timestamp = $this->getTimestamp();
		
		if ((empty($initTimestamp) != empty($timestamp)) || $timestamp != $initTimestamp) {
			$message =$this->MMessage->getOneMessage('CMNE000106');
			$this->errors['Check'][] = $message;
			return false;
		}
		
		return true;
	}

	/**
	 * タイムスタンプ取得
	 * 
	 * @return タイムスタンプ
	 */
	public function getTimestamp() {

		$pdo = null;

		try{

			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql  = "SELECT ";
			$sql .= "    MAX(MODIFIED) AS MODIFIED ";
			$sql .= "FROM ";
			$sql .= "    M_NINUSI_KEIYAKU ";
	    	$sql .= "WHERE ";
	    	$sql .= "    NINUSI_CD = '" . $this->_ninusi_cd ."' ";
	    	$sql .= "AND SOSIKI_CD = '" . $this->_sosiki_cd ."' ";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql, '荷主契約情報　タイムスタンプ取得');
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$pdo = null;
			
			$timestamp = $result['MODIFIED'];
			
			if ($timestamp == null) {
				return '';
			}
			
			$pdo = null;
			
			return $timestamp;

		} catch (Exception $e){

			$pdo = null;
		}

		return '';
	}
	
	/**
	 * 拠点情報編集処理
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
