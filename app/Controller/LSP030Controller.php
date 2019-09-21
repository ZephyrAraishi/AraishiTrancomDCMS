<?php
/**
 * LSP030
 *
 * 作業実績照会
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
App::uses('LSP030Model', 'Model');

class LSP030Controller extends AppController {

	public $name = 'LSP030';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['LSP']['030']['kino_nm']));

		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
				$this->Session->read('select_sosiki_cd'),
				$this->name
		);

		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),
				$this->Session->read('select_sosiki_cd'),
				$this->name
		);

		$this->LSP030Model = new LSP030Model($this->Session->read('select_ninusi_cd'),
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
				"initReload('".Router::url('/LSP030/view_day', false)."');");
		
		$monthViewFlg = "";
		$startYmd = "";
		$endYmd = "";
		
		// 「表示」ボタン押下時
		if (isset($this->request->data['monthViewFlg']) && $this->request->data['monthViewFlg'] != "") {
			
			$startYmd = $this->request->data['startYmd'];
			$endYmd = $this->request->data['endYmd'];
			
			// 検証結果が正常な場合、月別サマリテーブルを表示
			$errors = $this->validateSeachCondition();
			if (count($errors) != 0) {
				$this->set("errors", $errors);
			} else {
				$monthViewFlg = "1";
				$this->setMonthData($startYmd, $endYmd);
			}
		}
		
		$this->set("monthViewFlg", $monthViewFlg);
		$this->set("startYmd", $startYmd);
		$this->set("endYmd", $endYmd);
	}
	
	/**
	 * 日別詳細データ取得
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function view_day() {
		
		$this->autoLayout = false;
		
		$array = json_decode($this->request->input());
		
		$ymd = $array->ymd;
		
		// 登録されているLSP予測・実績情報を取得
		$infos = $this->LSP030Model->getDailyWorkInfo($ymd);
		
		$daiCd = "";
		$cnt = 0;
		
		// 大分類単位の件数を設定
		for ($i = 0; $i < count($infos); $i++) {
			if ($infos[$i]["BNRI_DAI_CD"] != $daiCd) {
				$daiCd = $infos[$i]["BNRI_DAI_CD"];
				if ($i == 0) {
					$cnt += 1;
					continue;
				}
				$infos[$i - $cnt]["BNRI_DAI_COUNT"] = $cnt;
				$cnt = 0;
			}
			$cnt += 1;
		}
		$infos[count($infos) - $cnt]["BNRI_DAI_COUNT"] = $cnt;
		
		// 予測・実績データが存在するかどうか判定
		$lydExist = FALSE;
		$ljdExist = FALSE;
		foreach ($infos as $array) {
			if (isset($array["LYD_NINZU"])) {
				$lydExist = TRUE;
			}
			if (isset($array["LJD_NINZU"])) {
				$ljdExist = TRUE;
			}
		}
		
		$week = array("日", "月", "火", "水", "木", "金", "土");
		$viewYmdLbl = date("Y/m/d", strtotime($ymd))."（".$week[date("w", strtotime($ymd))]."曜日）";
		
		$this->set("lydExist", $lydExist);
		$this->set("ljdExist", $ljdExist);
		
		// 対象の日付ラベルを設定
		$this->set("viewYmdLbl", $viewYmdLbl);
		
		// 就業開始時刻・終了時刻を設定
		$this->set("startTime", "7");
		$this->set("endTime", "31");
		
		// 各日付単位の情報を設定
		$this->set("dailyData", $infos);
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
			$infos = $this->LSP030Model->getJissekiCsvInfo($value);
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
		$fileName = "LSP実績情報_" . $startYmd . "-" . $endYmd . ".csv";
		header("Content-disposition: attachment; filename=" . $fileName);
		header("Content-type: application/octet-stream; name=" . $fileName);
		print(mb_convert_encoding($buff, 'SJIS', 'UTF-8'));
	}
	
	/**
	 * 検索条件の検証
	 * @access   private
	 * @return   エラー文字列
	 */
	private function validateSeachCondition() {
		
		$result = array();
		
		$startYmd = $this->request->data['startYmd'];
		$endYmd = $this->request->data['endYmd'];
		
		// 年月日（自）のチェック
		if (empty($startYmd)) {
			$result["startYmd"][] = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array("年月日（自）"));
		} else {
			if (preg_match("/^[0-9]{4}[0-9]{2}[0-9]{2}/", $startYmd) == false
					|| checkdate(substr($startYmd, 4, 2), substr($startYmd, 6, 2), substr($startYmd, 0, 4)) == false) {
				$result["startYmd"][] = vsprintf($this->MMessage->getOneMessage('LSPE030002'), array("年月日（自）"));
			}
		}

		// 年月日（至）のチェック
		if (empty($endYmd)) {
			$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array("年月日（至）"));
		} else {
			if (preg_match("/^[0-9]{4}[0-9]{2}[0-9]{2}/", $endYmd) == false
					|| checkdate(substr($endYmd, 4, 2), substr($endYmd, 6, 2), substr($endYmd, 0, 4)) == false) {
				$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('LSPE030002'), array("年月日（至）"));
			}	
		}
		
		// 年月日（自）～年月日（至）の相関チェック
		if (empty($result)) {
			if ($startYmd > $endYmd) {
				$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('LSPE030003'), array("年月日（至）", "年月日（自）"));
			} else {
				$dateDiff = gmmktime(0, 0, 0, substr($endYmd, 4, 2), substr($endYmd, 6, 2), substr($endYmd, 0, 4))
				- gmmktime(0, 0, 0, substr($startYmd, 4, 2), substr($startYmd, 6, 2), substr($startYmd, 0, 4));
				$dateCnt = $dateDiff / (60 * 60 * 24);
				if (31 <= $dateCnt) {
					$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('LSPE030004'), array("年月日（自）", "年月日（至）", "31日"));
				}	
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
		
		$monthlyData = array();
			
		// 登録されているLSP予測・実績情報を取得
		$infos = $this->LSP030Model->getMonthlyWorkInfo($startYmd, $endYmd);
			
		// 指定範囲の日付を作成
		$dates = $this->createDateRangeArrays($startYmd, $endYmd);
			
		$idx = 0;
			
		$sumUriageY = 0;
		$sumUriageJ = 0;
		$sumGenkaY = 0;
		$sumGenkaJ = 0;
		$sumArariY = 0;
		$sumArariJ = 0;
		$sumNinzuY = 0;
		$sumNinzuJ = 0;
		$sumJikanY = 0;
		$sumJikanJ = 0;
			
		// 指定範囲の日付情報と、登録済みのLSP情報をマージ
		$week = array("日", "月", "火", "水", "木", "金", "土");
		foreach ($dates as $idx => $day) {
			$monthlyData[$idx]["day"] = $day;
			$monthlyData[$idx]["exist"] = "false";
			$monthlyData[$idx]["dayLbl"] = date("y/m/d", strtotime($day))."（".$week[date("w", strtotime($day))]."）";
			foreach ($infos as $key => $value) {
				if ($value['YMD'] == $day) {
					$monthlyData[$idx]["exist"] = "true";
					$monthlyData[$idx]["data"] = $value;
					$sumUriageY += $value["LYH_URIAGE"];
					$sumUriageJ += $value["LJH_URIAGE"];
					$sumGenkaY += $value["LYH_GENKA"];
					$sumGenkaJ += $value["LJH_GENKA"];
					$sumArariY += $value["LYH_ARARI"];
					$sumArariJ += $value["LJH_ARARI"];
					$sumNinzuY += $value["LYH_NINZU"];
					$sumNinzuJ += $value["LJH_NINZU"];
					$sumJikanY += $value["LYH_JIKAN"];
					$sumJikanJ += $value["LJH_JIKAN"];
					break;
				}
			}
		}
			
		$this->set("startYmd", $startYmd);
		$this->set("endYmd", $endYmd);
			
		// 合計値の情報を設定
		$this->set("sumUriageY", $sumUriageY);
		$this->set("sumUriageJ", $sumUriageJ);
		$this->set("sumGenkaY", $sumGenkaY);
		$this->set("sumGenkaJ", $sumGenkaJ);
		$this->set("sumArariY", $sumArariY);
		$this->set("sumArariJ", $sumArariJ);
		$this->set("sumNinzuY", $sumNinzuY);
		$this->set("sumNinzuJ", $sumNinzuJ);
		$this->set("sumJikanY", $sumJikanY);
		$this->set("sumJikanJ", $sumJikanJ);
			
		// 各日付単位の情報を設定
		$this->set("monthlyData", $monthlyData);
	}

	/**
	 * 指定された期間の日付を作成します。
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