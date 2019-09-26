<?php
/**
 * WEX090
 *
 * 改善情報設定
 *
 * @category      LSP
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');
App::uses('MMeisyo', 'Model');
App::uses('MBunrui', 'Model');
App::uses('WEX090Model', 'Model');
class WEX090Controller extends AppController {
	
	public $name = 'WEX090';
	public $layout = "DCMS";
	
	/**
	 * 起動時処理
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function beforeFilter() {
		
		parent::beforeFilter();
		
		/* ビューへ設定 */
		// システム名
		$this->set('system', Sanitize::stripAll($this->Session->read('system_name')));
		// サブシステム名		
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['WEX']));
		// 機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WEX']['090']['kino_nm']));

		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
				$this->Session->read('select_sosiki_cd'),
				$this->name
		);
		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),
				$this->Session->read('select_sosiki_cd'),
				$this->name
		);
		$this->MBunrui = new MBunrui($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);
		$this->MMessage = new MMessage($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);
		$this->WEX090Model = new WEX090Model($this->Session->read('select_ninusi_cd'),
				$this->Session->read('select_sosiki_cd'),
				$this->name
		);
	}
	
	/**
	 * 初期表示
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function index() {

		require_once 'Pager.php';
		
		// キャッシュクリア
		Cache::clear();
		
		$type = "1";
		$from = "";
		$to = "";
		$viewFrom = "";
		$viewTo = "";
		$kznTitle = "";
		$kznKmk = "";
		$title = "";
		$bnriDaiCd = "";
		$bnriCyuCd = "";
		$bnriSaiCd = "";
		$maxCnt = 0;
		$pageID = 1;
		$rowMaxCnt = 50;
		$kaizenData = array();
		$searchFlg = true;
		$timestamp = "";
		
		//Javascript設定
		$this->set("onload","initReload();".
				"initPopup('".Router::url('/WEX090/popup', false)."');" .
				"initUpdate('".Router::url('/WEX090/update', false)."');" .
				"initBunrui();");
		
		// 分類選択コンボの情報を設定
		$opsions = $this->MBunrui->getBunruiDaiPDO();
		$this->set("koteiDaiList", $opsions);
		$opsions = $this->MBunrui->getBunruiCyuPDO();
		$this->set("koteiCyuList", $opsions);
		$opsions = $this->MBunrui->getBunruiSaiPDO();
		$this->set("koteiSaiList", $opsions);
		
		// 改善項目選択コンボの情報を設定
		$komokuList = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.kaizen_kmk'));
		$this->set("komokuList", $komokuList);
		
		if (isset($this->request->query['type'])) {
			$type = $this->request->query['type'];
		}
		
		// 検索条件を設定
		if (isset($this->request->query['from'])) {
			$from = $this->request->query['from'];
			$to = $this->request->query['to'];
			$kznTitle = $this->request->query['kznTitle'];
			$kznKmk = $this->request->query['kznKmk'];
			$kotei = $this->request->query['kotei'];
			if ($kotei != "") {
				$array = split("_", $kotei);
				if (1 <= count($array)) {
					$bnriDaiCd = $array[0];
				}
				if (2 <= count($array)) {
					$bnriCyuCd = $array[1];
				}
				if (3 <= count($array)) {
					$bnriSaiCd = $array[2];
				}
			}
		}
		
		if (isset($this->request->query['pageID'])) {
			$pageID = $this->request->query['pageID'];
		}
		
		// 更新処理の再描画時
		if (isset($this->request->query['message'])) {
			$msg = base64_decode($this->request->query['message']);
			$array = array();
			$array["msg"][] = $msg;
			if (isset($this->request->query['return_cd']) && $this->request->query['return_cd'] == '0') {
				$this->set("infos", $array);
			} else {
				$this->set("errors", $array);
			}
			if (isset($this->request->query['viewFrom'])) {
				$viewFrom = $this->request->query['viewFrom'];
				$viewTo = $this->request->query['viewTo'];
				$from = $viewFrom;
				$to = $viewTo;
			}
		}
		
		// 検索最大可能件数を設定
		if (isset($this->request->query['maxCnt'])) { // 初回表示以降、クライアント側から最大件数が飛んでくる
			$maxCnt = $this->request->query['maxCnt'];
		} else {
			$maxCnt = $this->WEX090Model->getSearchMaxCnt();
			$searchFlg = false;
		}
		
		if ($searchFlg) {

			$errors = array();
			
			// 検索条件の検証
			if (!isset($this->request->query['return_cd'])) {
				$errors = $this->validateSearchCondition();
				if (!empty($errors)) {
					$this->set("errors", $errors);
					$viewFrom = $this->request->query['viewFrom'];
					$viewTo = $this->request->query['viewTo'];
				} else {
					$viewFrom = $from;
					$viewTo = $to;
				}	
			}
			
			if (!empty($viewFrom) && !empty($viewTo) && empty($errors)) {

				// 該当データの件数を取得
				$cnt = $this->WEX090Model->getKaizenDataCount($viewFrom, $viewTo, $type, $bnriDaiCd, $bnriCyuCd, $bnriSaiCd, $kznTitle, $kznKmk);
					
				// 最大件数オーバーの場合、エラー
				if ($maxCnt < $cnt) {
					$errors["error"][] = vsprintf($this->MMessage->getOneMessage('CMNE000102'), array($maxCnt));
					$this->set("errors", $errors);
						
				} else {
				
					// 改善データベース情報を取得
					$lsts = $this->WEX090Model->getKaizenDataList($viewFrom, $viewTo, $type, $bnriDaiCd, $bnriCyuCd, $bnriSaiCd, $kznTitle, $kznKmk);
				
					// ページャーを設定
					$options = array(
							"totalItems" => count($lsts),
							"delta" => 10,
							"perPage" => $rowMaxCnt,
							"httpMethod" => "GET",
							"path" => "/DCMS/WEX090/index"
					);
					$pager = @Pager::factory($options);
					$navi = $pager -> getLinks();
					$this->set('navi',$navi["all"]);
						
						
					// 画面に表示するデータを取り出す
					$index = ($pageID - 1) * $rowMaxCnt;
					$arrayList = array();
					$limit = $index + $rowMaxCnt;
					if ($limit > count($lsts)) {
						$limit = count($lsts);
					}
					for($i = $index; $i < $limit ; $i++){
						$kaizenData[] = $lsts[$i];
					}
				}
			}
		}

		// 更新エラー時の画面再描画時
		if (isset($this->request->query['return_cd']) && $this->request->query['return_cd'] != '0') {
			
			// エラー時の画面内容で、画面に表示する内容を作成
			$sessionData = $this->Session->read('saveTableWEX090');
			
			if (!empty($sessionData)) {
				$timestamp = $sessionData->timestamp;
				$inputData = $sessionData->kaizenData;
				$kaizenData = array();
				foreach ($inputData as $data) {
					$row = array(
							"CHANGED" => $data->updType,
							"KZN_DATA_NO" => $data->kznDataNo,
							"TGT_KIKAN_ST" => $data->tgtKikanSt,
							"TGT_KIKAN_ED" => $data->tgtKikanEd,
							"BNRI_DAI_CD" => $data->bnriDaiCd,
							"BNRI_CYU_CD" => $data->bnriCyuCd,
							"BNRI_SAI_CD" => $data->bnriSaiCd,
							"KBN_KZN_KMK" => $data->kbnKznKmk,
							"KZN_TITLE" => $data->kznTitle,
							"KZN_TEXT" => $data->kznText,
							"KZN_VALUE" => $data->kznValue,
							"KZN_COST" => $data->kznCost,
							"KBN_SYURYO" => $data->kbnSyuryo,
							"ROW" => $data->row,
							"BNRI_DAI_NM" => $data->bnriDaiNm,
							"BNRI_CYU_NM" => $data->bnriCyuNm,
							"BNRI_SAI_NM" => $data->bnriSaiNm,
							"KZN_KMK_VALUE" => $data->kznKmkValue,
					);
					$kaizenData[] = $row;
				}
			}
			$this->Session->delete('saveTableWEX090');
			
			
		} else {
			
			$timestamp = $this->MCommon->getTimestamp();
		}
		
		$this->set('maxCnt', $maxCnt);
		$this->set('type', $type);
		$this->set('from', $from);
		$this->set('to', $to);
		$this->set('viewFrom', $viewFrom);
		$this->set('viewTo', $viewTo);
		$this->set('kznTitle', $kznTitle);
		$this->set('kznKmk', $kznKmk);
		$this->set("pageID", $pageID);
		$this->set('rowMaxCnt', $rowMaxCnt);
		$this->set('kaizenData', $kaizenData);
		$this->set("timestamp", $timestamp);
	}
	
	/**
	 * ポップアップ表示
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function popup() {
		
		$this->autoLayout = false;
		
		// 分類選択コンボの情報を設定
		$opsions = $this->MBunrui->getBunruiDaiPDO();
		$this->set("koteiDaiList", $opsions);
		$opsions = $this->MBunrui->getBunruiCyuPDO();
		$this->set("koteiCyuList", $opsions);
		$opsions = $this->MBunrui->getBunruiSaiPDO();
		$this->set("koteiSaiList", $opsions);
		
		// 改善項目選択コンボの情報を設定
		$komokuList = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.kaizen_kmk'));
		$this->set("komokuList", $komokuList);
		
		$this->set("type", $this->request->query['type']);
	}
	
	/**
	 * 更新処理
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function update() {
		
		$this->autoLayout = false;
		$this->autoRender = false;
		
		// POSTでない場合エラー
		if (!$this->request->is('post')) {
			echo "return_cd="."1" ."&message=".base64_encode($this->MMessage->getOneMessage('CMNE000104'));
			return;
		}
		
		// 画面からの情報を取り出す
		$array = json_decode($this->request->input());
		$timestamp = $array->timestamp;
		$kikanKbn = $array->kikanKbn;
		$kaizenData = $array->kaizenData;
		
		// データを一時保存（エラー時の画面描画に使用するため）
		$this->Session->write('saveTableWEX090', $array);
		
		// タイムスタンプチェック
		if (!$this->WEX090Model->checkTimestamp($timestamp)) {
			$errArray = $this->WEX090Model->errors;
			$message = '';
			foreach ($errArray as $key => $val) {
				foreach ($val as $key2 => $val2) {
					$message = $message . $val2 . "<br>";
				}
			}
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
			return;
		}
		
		// 登録対象のデータを検証
		$errors = $this->validateUpdateData($kikanKbn, $kaizenData);
		if (!empty($errors)) {
			$message = '';
			foreach ($errors as $key => $val) {
				foreach ($val as $key2 => $val2) {
					$message = $message . $val2 . "<br>";
				}
			}
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
			return;
		}
		
		// 改善DB設定情報を更新
		if (!$this->WEX090Model->updateKaizenData($kikanKbn, $kaizenData)) {
			$errArray = $this->WEX090Model->errors;
			$message = '';
			foreach ($errArray as $key => $val) {
				foreach ($val as $key2 => $val2) {
					$message = $message . $val2 . "<br>";
				}
			}
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
			return;
		}
		
		// 更新処理に成功した場合、セッションに一時保存している情報は不要になるため削除
		$this->Session->delete('saveTableWEX090');
		
		echo "return_cd=" . "0" . "&message=" .  base64_encode($this->MMessage->getOneMessage('CMNI000001'));
	}

	/**
	 * 検索条件の検証
	 * @access   private
	 * @return   エラー文字列
	 */
	private function validateSearchCondition() {
	
		$result = array();
	
		$type = $this->request->query['type'];
		$from = $this->request->query['from'];
		$to = $this->request->query['to'];
	
		// 対象期間（自）
		if (empty($from)) {
			$result["msg"][] = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array("対象期間（自）"));
		} else {
			if (($type == '1' && preg_match("/^[0-9]{4}[0-9]{2}/", $from) == false)
					|| ($type == '2' && preg_match("/^[0-9]{4}[0-9]{2}[0-9]{2}/", $from) == false)) {
				$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("対象期間（自）"));
			} else if (($type == '1' && checkdate(substr($from, 4, 2), 1, substr($from, 0, 4)) == false)
					|| ($type == '2' && checkdate(substr($from, 4, 2), substr($from, 6, 2), substr($from, 0, 4)) == false)) {
				$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("対象期間（自）"));
			}	
		}
	
		// 対象期間（至）
		if (empty($to)) {
			$result["msg"][] = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array("対象期間（至）"));
		} else {
			if (($type == '1' && preg_match("/^[0-9]{4}[0-9]{2}/", $to) == false)
					|| ($type == '2' && preg_match("/^[0-9]{4}[0-9]{2}[0-9]{2}/", $to) == false)) {
				$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("対象期間（至）"));
			} else if (($type == '1' && checkdate(substr($to, 4, 2), 1, substr($to, 0, 4)) == false)
					|| ($type == '2' && checkdate(substr($to, 4, 2), substr($to, 6, 2), substr($to, 0, 4)) == false)) {
				$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("対象期間（至）"));
			}	
		}
	
		// FROM～TOの相関チェック
		if (empty($result) && $to < $from) {
			$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090002'), array("対象期間（至）", "対象期間（自）"));
		}
	
		return $result;
	}
	
	/**
	 * 更新対象データの検証
	 * @access   private
	 * @param    $kikanKbn 対象期間区分
	 * @param    $kaizenData 更新対象データ
	 * @return   エラー文字列
	 */
	private function validateUpdateData($kikanKbn, $kaizenData) {
		
		$result = array();
		
		$rowCnt = 0;
		$targetCnt = 0;
		
		foreach ($kaizenData as $data) {
			
			$rowCnt++;
			
			if ($data->updType == '1' || $data->updType == '2' || $data->updType == '3' || $data->updType == '4') {
				$targetCnt++;
			}
			
			// 登録・更新対象のデータのみチェック
			if (!($data->updType == '1' || $data->updType == '2')) {
				continue;
			}
			
			// 改善表題
			$kznTitle = $data->kznTitle;
			if (empty($kznTitle)) {
				$result["msg"][] = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($rowCnt, "改善表題"));
				continue;
			}
			if (15 < mb_strlen($kznTitle)) {
				$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090003'), array($rowCnt, "改善表題", 15));
				continue;
			}
			
			// 改善内容
			$kznText = $data->kznText;
			if (!empty($kznText) && 5000 < mb_strlen($kznText)) {
				$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090003'), array($rowCnt, "改善内容", 5000));
				continue;
			}
			
			// 対象期間
			$tgtKikanSt = $data->tgtKikanSt;
			$tgtKikanEd = $data->tgtKikanEd;
			if (empty($tgtKikanSt)) {
				$result["msg"][] = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($rowCnt, "対象期間（自）"));
				continue;
			}
			if (empty($tgtKikanEd)) {
				$result["msg"][] = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($rowCnt, "対象期間（至）"));
				continue;
			}
			if ($kikanKbn == "1") {
				if (preg_match("/^[0-9]{4}[0-9]{2}/", $tgtKikanSt) == false) {
					$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090004'), array($rowCnt, "対象期間（自）"));
					continue;
				}
				if (checkdate(substr($tgtKikanSt, 4, 2), 1, substr($tgtKikanSt, 0, 4)) == false) {
					$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090004'), array($rowCnt, "対象期間（自）"));
					continue;
				}
				if (preg_match("/^[0-9]{4}[0-9]{2}/", $tgtKikanEd) == false) {
					$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090004'), array($rowCnt, "対象期間（至）"));
					continue;
				}
				if (checkdate(substr($tgtKikanEd, 4, 2), 1, substr($tgtKikanEd, 0, 4)) == false) {
					$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090004'), array($rowCnt, "対象期間（至）"));
					continue;
				}
			} else {
				if (preg_match("/^[0-9]{4}[0-9]{2}[0-9]{2}/", $tgtKikanSt) == false) {
					$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090004'), array($rowCnt, "対象期間（自）"));
					continue;
				}
				if (checkdate(substr($tgtKikanSt, 4, 2), substr($tgtKikanSt, 6, 2), substr($tgtKikanSt, 0, 4)) == false) {
					$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090004'), array($rowCnt, "対象期間（自）"));
					continue;
				}
				if (preg_match("/^[0-9]{4}[0-9]{2}[0-9]{2}/", $tgtKikanEd) == false) {
					$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090004'), array($rowCnt, "対象期間（至）"));
					continue;
				}
				if (checkdate(substr($tgtKikanEd, 4, 2), substr($tgtKikanEd, 6, 2), substr($tgtKikanEd, 0, 4)) == false) {
					$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090004'), array($rowCnt, "対象期間（至）"));
					continue;
				}
			}
			if ($tgtKikanEd < $tgtKikanSt) {
				$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090005'), array($rowCnt, "対象期間（至）", "対象期間（自）"));
				continue;
			}
			
			// 工程
			$bnriDaiCd = $data->bnriDaiCd;
			if (empty($bnriDaiCd)) {
				$result["msg"][] = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($rowCnt, "工程"));
				continue;
			}
			
			// 改善項目
			$kbnKznKmk = $data->kbnKznKmk;
			if (empty($kbnKznKmk)) {
				$result["msg"][] = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($rowCnt, "改善項目"));
				continue;
			}
			
			// 数値目標
			$kznValue = $data->kznValue;
			if (empty($kznValue)) {
				$result["msg"][] = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($rowCnt, "数値目標"));
				continue;
			}
			$intLen = 0;
			$decLen = 0;
			if ($kbnKznKmk == "01" 
				|| $kbnKznKmk == "06" 
				|| $kbnKznKmk == "07") {  /* 単価／売上／利益 */
				$intLen = 12;
			} else if ($kbnKznKmk == "02") { /* 時間 */
				$intLen = 9; $decLen = 2;
			} else if ($kbnKznKmk == "03") { /* 人数 */
				$intLen = 4;
			} else if ($kbnKznKmk == "04") { /* 平均物量 */
				$intLen = 10;
			} else if ($kbnKznKmk == "05") { /* 平均生産性 */
				$intLen = 6;
				$decLen = 1;
			}
			if ($decLen != 0) {
				$errFlg = false;
				if (preg_match("/^[0-9]+(\.[0-9]+)?$/", $kznValue) == false) {
					$errFlg = true;
				} else {
					$pos = strpos($kznValue, '.');
					$seisu = substr($kznValue, 0, $pos);
					$syousu = substr($kznValue, $pos + 1);
					if(mb_strlen($seisu) > $intLen){
						$errFlg = true;
					} else if(!empty($pos) && mb_strlen($syousu) > $decLen){
						$errFlg = true;
					}
				}
				if ($errFlg) {
					$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090007'), array($rowCnt, "数値目標", $intLen, $decLen));
					continue;
				}
			} else {
				if (preg_match("/^[0-9]+$/", $kznValue) == false || $intLen < mb_strlen($kznValue)) {
					$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090008'), array($rowCnt, "数値目標", $intLen));
					continue;
				}
			}
			
			// 改善コスト
			$kznCost = $data->kznCost;
			if (empty($kznValue)) {
				$result["msg"][] = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($rowCnt, "改善コスト"));
				continue;
			}
			if (preg_match("/^[0-9]+$/", $kznCost) == false) {
				$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090004'), array($rowCnt, "改善コスト"));
				continue;
			}
			if (12 < strlen($kznCost)) {
				$result["msg"][] = vsprintf($this->MMessage->getOneMessage('WEXE090003'), array($rowCnt, "改善コスト", 12));
				continue;
			}
		}
		
		if ($targetCnt == 0) {
			$result["msg"][] = $this->MMessage->getOneMessage('CMNE000101');
		}
		
		return $result;
	}
}
