<?php
/**
 * LSP020
 *
 * 予測シュミレーション
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
App::uses('LSP020Model', 'Model');

class LSP020Controller extends AppController {

	public $name = 'LSP020';
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
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['LSP']));
		// 機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['LSP']['020']['kino_nm']));

		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
				$this->Session->read('select_sosiki_cd'),
				$this->name
		);

		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),
				$this->Session->read('select_sosiki_cd'),
				$this->name
		);

		$this->LSP020Model = new LSP020Model($this->Session->read('select_ninusi_cd'),
				$this->Session->read('select_sosiki_cd'),
				$this->name
		);

		/* メッセージマスタ呼び出し */
		$this->MMessage = new MMessage($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

	}

	/**
	 * 初期表示
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function index() {

		//キャッシュクリア
		Cache::clear();

		//Javascript設定
		$this->set("onload", 
				"initPopup('".Router::url('/LSP020/popup', false)."','".Router::url('/LSP020/staff_list', false)."');".
				"initUpdate('".Router::url('/LSP020/update', false)."');".
				"initReload('".Router::url('/LSP020/view_day_input', false)."','".Router::url('/LSP020/view_day_glaph', false)."');");
		
		// シュミレーション日付選択コンボボックスを設定
		$this->setWeekSelectBox();
		
		$monthViewFlg = "";
		$startYmd = "";
		$endYmd = "";
		$endYmd = "";
		$ymd = "";

		// 「表示」ボタン押下時
		if (isset($this->request->query['monthViewFlg']) && $this->request->query['monthViewFlg'] != "") {
			
			$startYmd = $this->request->query['startYmd'];
			$endYmd = $this->request->query['endYmd'];
				
			// 検証結果が正常な場合、月別サマリテーブルを表示
			$errors = $this->validateSeachCondition();
			if (count($errors) != 0) {
				$this->set("errors", $errors);
			} else {
				$monthViewFlg = "1";
				$this->setMonthData($startYmd, $endYmd);
				if (isset($this->request->query['ymd']) && $this->request->query['ymd'] != "") { // 更新ボタン後の画面リロード時に実行される
					$ymd = $this->request->query['ymd'];
					$this->setDayData($ymd);
				}
			}
		}
		
		// 更新処理後の画面遷移時
		if (isset($this->request->query['return_cd'])) {
			$returnCd = $this->request->query['return_cd'];
			$msg = base64_decode($this->request->query['message']);
			$msgArray = array();
			$msgArray["msg"][] = $msg;
			if ($returnCd == "0") {
				$this->set("infos", $msgArray);
			} else {
				$this->set("errors", $msgArray);
				
				$array = $this->Session->read('saveTableLSP020');
				
				/* 入力値でシュミレーション情報を展開 */
				// 按分率情報
				$inputAnbun = $array->anbun;
				$anbunInfo = array();
				foreach ($inputAnbun as $data) {
					$values = array();
					$values["YOSOKU_TIME"] = $data->time;
					$values["ANBUN_RATE"] = $data->rate;
					$anbunInfo[] = $values;
				}
				$this->set("anbunRate", $anbunInfo);
				
				// 工程別シュミレーション情報
				$inputSimData = $array->simulation;
				$yosokuInfo = array();
				foreach ($inputSimData as $data) {
					$values = array();
					$values["K_BNRI_DAI_CD"] = $data->daiCd;
					$values["K_BNRI_DAI_NM"] = $data->daiNm;
					$values["K_BNRI_CYU_CD"] = $data->cyuCd;
					$values["K_BNRI_CYU_NM"] = $data->cyuNm;
					$values["K_BNRI_CYU_COUNT"] = $data->cyuCnt;
					$values["K_KBN_URIAGE"] = $data->uriageKbn;
					$values["K_BUTURYO"] = $data->buturyo;
					$values["K_NINZU"] = $data->ninzu;
					$values["K_TIME_START"] = $data->start;
					$values["K_TIME_END"] = $data->end;
					$yosokuInfo[] = $values;
				}
				$this->set("dailyData", $yosokuInfo);
				
				$this->Session->delete('saveTableLSP020');
			}
		}
		
		$this->set("monthViewFlg", $monthViewFlg);
		$this->set("startYmd", $startYmd);
		$this->set("endYmd", $endYmd);
		$this->set("ymd", $ymd);
	}
	
	/**
	 * CSVダウンロード
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function downloadCsv() {
		
		$this->autoRender = false;
		
		// 対象期間を設定
		$startYmd = $this->request->data['csvStartYmd'];
		$endYmd = $this->request->data['csvEndYmd'];
		$dateArray = $this->createDateRangeArrays($startYmd, $endYmd);
		
		$datas = array();
		
		$COLUMN = array(
			"物量" => "BUTURYO",
			"売上" => "URIAGE",
			"原価" => "GENKA",
			"粗利" => "ARARI"
		);

		// CSV出力対象のLSP予測情報を取得
		foreach ($dateArray as $key => $value) {
			$infos = $this->LSP020Model->getDailyYosokuInfo($value);
			$datas[] = $infos;
		}
		
		// CSVヘッダ部を出力
		$buff = "";
		$buff .= "項目,大分類,中分類";
		foreach ($dateArray as $key => $value) {
			$buff .= "," . $value;
		}
		$buff .= ",合計";
		
		$buff .= "\r\n";
		
		// CSVボディ部を設定
		foreach ($COLUMN as $key => $value) {
			for ($i = 0; $i < count($datas[0]); $i++) {
				$sum = 0;
				$buff .= $key;
				$buff .= "," . $datas[0][$i]["BNRI_DAI_NM"];
				$buff .= "," . $datas[0][$i]["BNRI_CYU_NM"];
				for ($j = 0; $j < count($dateArray); $j++) {
					$num = isset($datas[$j][$i][$value]) ? $datas[$j][$i][$value] : 0;
					$sum += $num;
					$buff .= "," . $num;
				}
				$buff .= "," . $sum;
				$buff .= "\r\n";
			}
		}

		// CSVファイル出力
		$fileName = "LSP予測情報_" . $startYmd . "-" . $endYmd . ".csv";
		header("Content-disposition: attachment; filename=" . $fileName);
		header("Content-type: application/octet-stream; name=" . $fileName);
		print(mb_convert_encoding($buff, 'SJIS', 'UTF-8'));
	}
	
	/**
	 * 日別詳細データ取得（予測入力）
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function view_day_input() {
		$this->autoLayout = false;
		$array = json_decode($this->request->input());
		$ymd = $array->ymd;
		$this->setDayData($ymd);
	}
	
	/**
	 * 日別詳細データ取得（グラフ表示）
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function view_day_glaph() {
		$this->autoLayout = false;
		$array = json_decode($this->request->input());
		$ymd = $array->ymd;
		$this->setDayData($ymd);
	}
	
	/**
	 * スタッフ工程配置ポップアップ取得
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function popup() {
	
		$this->autoLayout = false;
		
		$bnriDaiCd = $this->request->query['daiCd'];
		$bnriCyuCd = $this->request->query['cyuCd'];
		$ymd = $this->request->query['ymd'];
		
		$this->setStaffKinmuData($bnriDaiCd, $bnriCyuCd, $ymd);
	}
	
	/**
	 * スタッフ一覧取得
	 * 
	 */
	public function staff_list() {
		
		$this->autoLayout = false;
		
		$array = json_decode($this->request->input());
		$bnriDaiCd = $array->daiCd;
		$bnriCyuCd = $array->cyuCd;
		$ymd = $array->ymd;
		
		$this->setStaffKinmuData($bnriDaiCd, $bnriCyuCd, $ymd);
	}

	/**
	 * 更新処理
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function update() {
		
		$this->autoLayout = false;
		$this->autoRender = false;
		
		// POSTでない場合エラー
		if (!$this->request->is('post')) {
			echo "return_cd="."1" ."&message=".base64_encode($this->MMessage->getOneMessage('CMNE000104'));
			return;
		}
		
		$array = json_decode($this->request->input());
		
		// データを一時保存（エラー時の画面描画に使用するため）
		$this->Session->write('saveTableLSP020', $array);
		
		// 入力値を検証
		$errors = $this->validateSimulation();
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
		
		// 年月日を取り出す
		$ymd = $array->ymd;
		
		// シュミレーション実行用の按分率情報を作成
		$anbunInfo = array();
		$anbun = $array->anbun;
		foreach ($anbun as $data) {
			$values = array();
			$values["YOSOKU_TIME"] = $data->time;
			$values["ANBUN_RATE"] = $data->rate;
			$anbunInfo[] = $values;
		}
		
		// シュミレーション実行用の予測情報を作成
		$yosokuInfo = array();
		$simData = $array->simulation;
		foreach ($simData as $data) {
			$values = array();
			$values["BNRI_DAI_CD"] = $data->daiCd;
			$values["BNRI_DAI_NM"] = $data->daiNm;
			$values["BNRI_CYU_CD"] = $data->cyuCd;
			$values["BNRI_CYU_NM"] = $data->cyuNm;
			$values["KBN_URIAGE"] = $data->uriageKbn;
			$values["BUTURYO"] = $data->buturyo;
			$values["TIME_START"] = $data->start;
			$values["TIME_END"] = $data->end;
			$yosokuInfo[] = $values;
		}
		
		$result = false;
		
		// シュミレーション処理を実行
		$simResult = $this->LSP020Model->executeSimulation($ymd, $yosokuInfo, $anbunInfo, true);
		if ($simResult == null) {
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000107'));
			return;
		}
		
		// LSP予測情報更新処理を実行
		$result = $this->LSP020Model->updateLSPSimulation($simResult, $anbunInfo);
		if ($result == false) {
			$errArray = $this->LSP020Model->errors;
			$message = '';
			foreach ($errArray as $key => $val) {
				foreach ($val as $key2 => $val2) {
					$message = $message . $val2 . "<br>";
				}
			}
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
			return;
		}
		
		$this->Session->delete('saveTableLSP020');
		
		echo "return_cd=" . "0" . "&message=" .  base64_encode($this->MMessage->getOneMessage('CMNI000001'));
	}
	
	/**
	 * 週選択コンボボックス設定
	 * @access   private
	 * @param    void
	 * @return   void
	 */
	private function setWeekSelectBox() {
		
		// 先頭曜日区分を取得
		$kbnYoubi = $this->MMeisyo->getMeisyo('18');
		
		// 当週の指定曜日を取得
		$befCnt = 0;
		while (date("w",strtotime($befCnt . " day")) != $kbnYoubi['01']) {
			$befCnt = $befCnt - 1;
		}
		
		// 開始日付選択用リストを設定
		$startYmdList = array();
		for ($i = 0; $i < 5; $i++) {
			$startYmdList[] = date("Ymd",strtotime($befCnt - ($i * 7) . " day"));
		}
		$this->set("startYmdList", $startYmdList);
		
		// 終了日付選択用リストを設定
		$endYmdList = array();
		for ($i = 0; $i < 9; $i++) {
			$endYmdList[] = date("Ymd",strtotime(28 + $befCnt - ($i * 7) . " day"));
		}
		$this->set("endYmdList", $endYmdList);
		
		// シュミレーション日のデフォルト値を設定
		$selectedStartYmd = $startYmdList[0];
		$selectedEndYmd = $endYmdList[3];
		if (isset($this->request->query['startYmd']) && $this->request->query['startYmd'] != "") {
			$selectedStartYmd = $this->request->query['startYmd'];
			$selectedEndYmd = $this->request->query['endYmd'];
		}
		
		$this->set("selectedStartYmd", $selectedStartYmd);
		$this->set("selectedEndYmd", $selectedEndYmd);
	}
	
	/**
	 * 検索条件の検証
	 * @access   private
	 * @return   エラー文字列
	 */
	private function validateSeachCondition() {
	
		$result = array();
		
		$startYmd = $this->request->query['startYmd'];
		$endYmd = $this->request->query['endYmd'];
		
		// 年月日（自）～年月日（至）の相関チェック
		if ($startYmd >= $endYmd) {
			$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('LSPE020003'), array("年月日（至）", "年月日（自）"));
			return $result;
		}
		$dateDiff = gmmktime(0, 0, 0, substr($endYmd, 4, 2), substr($endYmd, 6, 2), substr($endYmd, 0, 4))
						- gmmktime(0, 0, 0, substr($startYmd, 4, 2), substr($startYmd, 6, 2), substr($startYmd, 0, 4));
		$dateCnt = $dateDiff / (60 * 60 * 24);
		if (31 < $dateCnt) {
			$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('LSPE020004'), array("年月日（自）", "年月日（至）", "4週間"));
			return $result;
		}
		
		return $result;
	}
	
	/**
	 * シュミレーション入力値の検証
	 * @access   private
	 * @return   エラー文字列
	 */
	private function validateSimulation() {
		
		$result = array();
		
		$array = json_decode($this->request->input());
		$anbun = $array->anbun;
		$simData = $array->simulation;
		
		// マスタの検証
		$infos = $this->LSP020Model->getKeiyakuTaniDaihyoButuryoCntInfo();
		
		$row = 0;
		$errKoteiMsg = "";
		$temp = array();
		foreach ($simData as $data) {
			$row++;
			$bnriDaiCd = $data->daiCd;
			$bnriDaiNm = $data->daiNm;
			$bnriCyuCd = $data->cyuCd;
			$bnriCyuNm = $data->cyuNm;
			foreach ($infos as $info) {
				if ($info["BNRI_DAI_CD"] != $bnriDaiCd || $info["BNRI_CYU_CD"] != $bnriCyuCd) {
					continue;
				}
				$kyBnriDaiCd = $info["KY_BNRI_DAI_CD"];
				$kyBnriCyuCd = $info["KY_BNRI_CYU_CD"];
				$kyDaihyoCntDai = $info["KY_DAIHYO_CNT_DAI"];
				$kyDaihyoCntCyu = $info["KY_DAIHYO_CNT_CYU"];
				if ($kyBnriDaiCd != '000'
						&& (($kyBnriDaiCd == '000' && 1 < $kyDaihyoCntDai)
								|| ($kyBnriCyuCd != '000' && 1 < $kyDaihyoCntCyu))) {
					$cd = $kyBnriDaiCd . "-" . $kyBnriCyuCd;
					if (array_key_exists($cd, $temp)) {
						continue;
					}
					$temp[$cd] = $cd;
					$msg = "";
					if ($kyBnriDaiCd != '000') {
						$msg = "大分類：[" . $bnriDaiNm . "] " . "中分類：[" . $bnriCyuNm . "] <br>";
					} else {
						$msg = "大分類：[" . $bnriDaiNm . "] " . "中分類：[分類なし] <br>";
					}
					$errKoteiMsg .= $msg;
				}
			}
		}
		if (!empty($errKoteiMsg)) {
			$result["check"][] = vsprintf($this->MMessage->getOneMessage('LSPE020014'), array($errKoteiMsg));
			return $result;
		}
		
		// 按分値エリアの検証
		$idx = 0;
		$sumRate = 0;
		foreach ($anbun as $data) {
			$rate = $data->rate;
			$time = $data->time;
			// 必須チェック
			if ($rate == "") {
				$result["anbun"][] = vsprintf($this->MMessage->getOneMessage('LSPE020005'), array($time, "按分値"));
				continue;
			}
			// 形式チェック
			if (!preg_match("/^[0-9]+$/", $rate)) {
				$result["anbun"][] = vsprintf($this->MMessage->getOneMessage('LSPE020006'), array($time, "按分値"));
				continue;
			}
			$sumRate += $rate;
		}
		if (empty($result)) {
			if ($sumRate != 100) {
				$result["anbun"][] = $this->MMessage->getOneMessage('LSPE020007');
			}
		}
		
		// 予測入力エリアの検証
		$row = 0;
		foreach ($simData as $data) {
			$row++;
			$uriageKbn = $data->uriageKbn;
			$buturyo = $data->buturyo;
			$start = $data->start;
			$end = $data->end;
			if ($buturyo == "") {
				continue;
			}
			// 必須チェック
			if ($uriageKbn == "01") {
				if (empty($start) && empty($end)) {
					continue;
				}
				if (empty($start) && !empty($end)) {
					$result["check" + $row][] = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($row, "開始時刻"));
					continue;
				}
				if (!empty($start) && empty($end)) {
					$result["check" + $row][] = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($row, "終了時刻"));
					continue;
				}
			} else {
				if (empty($start)) {
					$result["check" + $row][] = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($row, "開始時刻"));
					continue;
				}
				if (empty($end)) {
					$result["check" + $row][] = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($row, "終了時刻"));
					continue;
				}
			}
			// 形式チェック
			if (!preg_match("/^[0-9]+$/", $buturyo)) {
				$result["check" + $row][] = vsprintf($this->MMessage->getOneMessage('LSPE020008'), array($row, "物量"));
				continue;
			}
			if (!preg_match("/^[0-9]+$/", $start)) {
				$result["check" + $row][] = vsprintf($this->MMessage->getOneMessage('LSPE020008'), array($row, "開始時刻"));
				continue;
			}
			if (strlen($start) != 4) {
				$result["check" + $row][] = vsprintf($this->MMessage->getOneMessage('LSPE020011'), array($row, "開始時刻", "4"));
				continue;
			}
			if ('60' <= substr($start, 2, 2)) {
				$result["check" + $row][] = vsprintf($this->MMessage->getOneMessage('LSPE020010'), array($row, "開始時刻", "4"));
				continue;
			}
			if (!("0700" <= $start && $start <= "3100")) {
				$result["check" + $row][] = vsprintf($this->MMessage->getOneMessage('LSPE020013'), array($row, "開始時刻", "0700", "3100"));
				continue;
			}
			if (!preg_match("/^[0-9]+$/", $end)) {
				$result["check" + $row][] = vsprintf($this->MMessage->getOneMessage('LSPE020008'), array($row, "終了時刻"));
				continue;
			}
			if (strlen($end) != 4) {
				$result["check" + $row][] = vsprintf($this->MMessage->getOneMessage('LSPE020011'), array($row, "終了時刻", "4"));
				continue;
			}
			if ('60' <= substr($end, 2, 2)) {
				$result["check" + $row][] = vsprintf($this->MMessage->getOneMessage('LSPE020010'), array($row, "終了時刻", "4"));
				continue;
			}
			if (!("0700" <= $end && $end <= "3100")) {
				$result["check" + $row][] = vsprintf($this->MMessage->getOneMessage('LSPE020013'), array($row, "終了時刻", "0700", "3100"));
				continue;
			}
			// 相関チェック
			if ($end <= $start) {
				$result["check" + $row][] = vsprintf($this->MMessage->getOneMessage('LSPE020012'), array($row, "開始時刻", "終了時刻"));
				continue;
			}
		}
		
		return $result;
	}
	
	/**
	 * 月別サマリテーブルデータ設定
	 * @access   private
	 * @param    $startYmd 開始日（yyyyMMdd形式）
	 * @param    $endYmd 終了日（yyyyMMdd形式）
	 * @return   void
	 */
	private function setMonthData($startYmd, $endYmd) {
		
		// 登録されているLSP予測・実績情報を取得
		$infos = $this->LSP020Model->getMonthlyYosokuInfo($startYmd, $endYmd);
		
		// 指定範囲の日付を作成
		$dates = $this->createDateRangeArrays($startYmd, $endYmd);
		
		$idx = 0;
		
		$sumUriage = 0;
		$sumGenka = 0;
		$sumArari = 0;
		$sumNinzu = 0;
		$sumJikan = 0;
		
		// 指定範囲の日付情報と、登録済みのLSP情報をマージ
		$week = array("日", "月", "火", "水", "木", "金", "土");
		foreach ($dates as $idx => $day) {
			$monthlyData[$idx]["day"] = $day;
			$monthlyData[$idx]["exist"] = "false";
			$monthlyData[$idx]["dayLbl"] = date("y/m/d", strtotime($day))."（".$week[date("w", strtotime($day))]."）";
			foreach ($infos as $key => $value) {
				if ($value['YMD'] == $day) {
					$monthlyData[$idx]["data"] = $value;
					$sumUriage += $value["URIAGE"];
					$sumGenka += $value["GENKA"];
					$sumArari += $value["ARARI"];
					$sumNinzu += $value["NINZU"];
					$sumJikan += $value["JIKAN"];
					break;
				}
			}
		}
		
		// 合計値の情報を設定
		$this->set("sumUriage", $sumUriage);
		$this->set("sumGenka", $sumGenka);
		$this->set("sumArari", $sumArari);
		$this->set("sumNinzu", $sumNinzu);
		$this->set("sumJikan", $sumJikan);
		
		// 各日付単位の情報を設定
		$this->set("monthlyData", $monthlyData);
	}
	
	/**
	 * 日別詳細テーブルデータ設定
	 * @access   private
	 * @param    $ymd 対象日付（yyyyMMdd形式）
	 * @return   void
	 */
	private function setDayData($ymd) {
		
		$anbunRate = array();
		$start = 7;
		$end = 31;
		
		// LSP予測明細情報を取得
		$yosokuInfo = $this->LSP020Model->getDailyYosokuInfo($ymd);

		// LSP予測按分率情報を取得
		$anbunRate = $this->LSP020Model->getAnbunRate($ymd);
		if (count($anbunRate) == 0) {
			$anbunRate = $this->getDefaultAnbunRate($start, $end);
		}
		$this->set("anbunRate", $anbunRate);

		// シュミレーション処理を実行
		$result = $this->LSP020Model->executeSimulation($ymd, $yosokuInfo, $anbunRate, false);
		$dailyData = $result["KOTEI_SIMULATION_RESULT"];
		$this->set("dailyData", $dailyData);
		
		// 登録済み件数を設定
		$dataCnt = empty($yosokuInfo[0]["LYD_BNRI_DAI_CD"]) ? 0 : count($yosokuInfo);
		$this->set("regCnt", $dataCnt);
		
		// 対象の日付ラベルを設定
		$week = array("日", "月", "火", "水", "木", "金", "土");
		$viewYmdLbl = date("Y/m/d", strtotime($ymd))."（".$week[date("w", strtotime($ymd))]."曜日）";
		$this->set("viewYmdLbl", $viewYmdLbl);
		
		// 就業開始時刻・終了時刻を設定
		$this->set("startTime", $start);
		$this->set("endTime", $end);
		
		$this->set("ymd", $ymd);
	}
	
	/**
	 * スタッフ工程配置データ設定
	 * @access   private
	 * @param $bnriDaiCd 大分類コード
	 * @param $bnriCyuCd 中分類コード
	 * @param $ymd 対象年月日
	 * @return   void
	 */
	private function setStaffKinmuData($bnriDaiCd, $bnriCyuCd, $ymd) {
		
		$sumSeisansei = 0;
		$sumTanka = 0;
		
		// スタッフ勤務設定情報を取得
		$infos = $this->LSP020Model->getStaffKinmuData($bnriDaiCd, $bnriCyuCd, $ymd);
		
		$temp = array();
		
		foreach ($infos as $data) {
			$staffCd = $data["STAFF_CD"];
			if (!array_key_exists($staffCd, $temp)) {
				$sumTanka += empty($data["KIN_SYOTEI"]) ? 0 : $data["KIN_SYOTEI"];
				$sumSeisansei += empty($data["SEISANSEI_TIME_BUTURYO"]) ? 0 : $data["SEISANSEI_TIME_BUTURYO"];
				$temp[$staffCd] = $staffCd;
			}
		}
		
		// 工程の単位を取得
		$tani = $this->LSP020Model->getTaniName($bnriDaiCd, $bnriCyuCd);

		$this->set("tani", $tani);
		$this->set("sumSeisansei", $sumSeisansei);
		$this->set("sumTanka", $sumTanka);
		$this->set("staffCnt", count($temp));
		$this->set("staffData", $infos);
	}
	
	/**
	 * デフォルトの按分率取得
	 * @access   private
	 * @param    $start 開始時刻
	 * @param    $end 終了時刻
	 * @return   デフォルトの按分率情報
	 */
	private function getDefaultAnbunRate($start, $end) {

		$anbunRate = array();
		
		$defRate = $this->MMeisyo->getMeisyo('28');
		$key = 0;
		for ($i = $start; $i <= $end; $i++) {
			$array = array();
			$array["YOSOKU_TIME"] = $i;
			$array["ANBUN_RATE"] = $defRate[$i];
			$anbunRate[$key] = $array;
			$key++;
		}
		
		return $anbunRate;
	}
	
	/**
	 * 期間日付の作成
	 * @access   private
	 * @param    $start 開始日（yyyyMMdd形式）
	 * @param    $end 終了日（yyyyMMdd形式）
	 * @return   範囲日付配列
	 */
	private function createDateRangeArrays($start, $end) {

		$su = mktime(0, 0, 0, substr($start, 4, 2), substr($start, 6, 2), substr($start, 0, 4));
		$eu = mktime(0, 0, 0, substr($end, 4, 2), substr($end, 6, 2), substr($end, 0, 4));

		$sec = 60 * 60 * 24; // 60秒 × 60分 × 24時間

		$key = 0;
		for ($i = $su; $i <= $eu; $i += $sec) {
			$dates[$key] = date("Ymd",$i);
			$key ++;
		}
		
		return $dates;
	}
}