<?php
/**
 * WEX094
 *
 * 日別推移
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
App::uses('WEX094Model', 'Model');

class WEX094Controller extends AppController {

	public $name = 'WEX094';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WEX']['094']['kino_nm']));

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
		$this->WEX094Model = new WEX094Model($this->Session->read('select_ninusi_cd'),
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
	function index(){

		require_once 'Pager.php';

		// キャッシュクリア
		Cache::clear();

		//Javascript設定
		$this->set("onload","initReload();");


		$viewList = array();
		$kznData = array();
		$abcData = array();
		$kznNo = "";
		$startYmd = "";
		$endYmd = "";
		$viewStartYmd = "";
		$viewEndYmd = "";
		$pageID = 1;
		$pageRowCnt = 50;

		// 表示する改善データ情報の改善データ番号を設定
		if (isset($this->request->query['no'])) {
			$kznNo = $this->request->query['no'];
		}

		// ページIDを設定
		if(isset($this->request->query['pageID'])) {
			$pageID = $this->request->query['pageID'];
		}

		// 改善データ情報を取得
		$data = $this->WEX094Model->getKaizenData($kznNo);
		if (!empty($data)) {

			$kznData = $data[0];
			$errors = array();

			// 表示する対象期間を設定
			if (isset($this->request->query['startYmd']) && isset($this->request->query['endYmd'])) {

				$startYmd = $this->request->query['startYmd'];
				$endYmd = $this->request->query['endYmd'];

				// 出力条件の検証
				$errors = $this->validateOutputCondition($kznData);
				if (!empty($errors)) {
					$this->set("errors", $errors);
					$viewStartYmd = $this->request->query['viewStartYmd'];
					$viewEndYmd = $this->request->query['viewEndYmd'];
				} else {
					$viewStartYmd = $startYmd;
					$viewEndYmd = $endYmd;
				}
			} else {
				$startYmd = $kznData["TGT_KIKAN_ST"];
				$endYmd = $kznData["TGT_KIKAN_ED"];
				$viewStartYmd = $startYmd;
				$viewEndYmd = $endYmd;
			}

			if (empty($errors)) {

				// ABCデータベース月別情報を取得
				$abcData = $this->WEX094Model->getAbcDailyData($kznData["BNRI_DAI_CD"], $kznData["BNRI_CYU_CD"], $kznData["BNRI_SAI_CD"], $viewStartYmd, $viewEndYmd);

				// ページャーを設定
				$options = array(
						"totalItems" => count($abcData),
						"delta" => 10,
						"perPage" => $pageRowCnt,
						"httpMethod" => "GET",
						"path" => "/DCMS/WEX094/index"
				);
				$pager = @Pager::factory($options);
				$navi = $pager -> getLinks();
				$this->set('navi', $navi["all"]);

				// 項目のインデックスを取得
				$index = ($pageID - 1) * $pageRowCnt;
				$limit = $index + $pageRowCnt;
				if ($limit > count($abcData)) {
					$limit = count($abcData);
				}

				// インデックスから表示項目を取得
				for($i = $index; $i < $limit ; $i++){
					$viewList[] = $abcData[$i];
				}
			}
		}

		$this->set("kznNo", $kznNo);
		$this->set("kznData", $kznData);
		$this->set("abcData", $viewList);
		$this->set("startYmd", $startYmd);
		$this->set("endYmd", $endYmd);
		$this->set("viewStartYmd", $viewStartYmd);
		$this->set("viewEndYmd", $viewEndYmd);
		$this->set("pageID", $pageID);
		$this->set("pageRowCnt", $pageRowCnt);
	}

	/**
	 * 出力条件の検証
	 * @access   private
	 * @param    $kznData 改善データ
	 * @return   エラー文字列
	 */
	private function validateOutputCondition($kznData) {

		$result = array();

		$startYmd = $this->request->query['startYmd'];
		$endYmd = $this->request->query['endYmd'];
		$kikanStartYmd = $kznData['TGT_KIKAN_ST'];
		$kikanEndYmd = $kznData['TGT_KIKAN_ED'];

		// 出力期間（自）のチェック
		if (empty($startYmd)) {
			$result["startYmdd"][] = $message = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array("出力期間（自）"));
		} else {
			if (preg_match("/^[0-9]{4}[0-9]{2}[0-9]{2}/", $startYmd) == false) {
				$result["startYmd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("出力期間（自）"));
			} else if (checkdate(substr($startYmd, 4, 2), substr($startYmd, 6, 2), substr($startYmd, 0, 4)) == false) {
				$result["startYmd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("出力期間（自）"));
			}
		}

		// 出力期間（至）のチェック
		if (empty($endYmd)) {
			$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array("出力期間（至）"));
		} else {
			if (preg_match("/^[0-9]{4}[0-9]{2}[0-9]{2}/", $endYmd) == false) {
				$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("出力期間（至）"));
			} else if (checkdate(substr($endYmd, 4, 2), substr($endYmd, 6, 2), substr($endYmd, 0, 4)) == false) {
				$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("出力期間（至）"));
			}
		}

		if (empty($result)) {

			// FROM～TOの相関チェック
			if ($endYmd < $startYmd) {
				$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090002'), array("出力期間（至）", "出力期間（自）"));
				return $result;
			}

			// 対象範囲の相関チェック
			if (!($kikanStartYmd <= $startYmd)) {
				$message = vsprintf($this->MMessage->getOneMessage('WEXE090006'), array("出力期間（自）"));
				$result["endYmd"][] = $message;
				return $result;
			}
			if (!($endYmd <= $kikanEndYmd)) {
				$message = vsprintf($this->MMessage->getOneMessage('WEXE090006'), array("出力期間（至）"));
				$result["endYmd"][] = $message;
				return $result;
			}
		}

		return $result;
	}

	/**
	 * グラフ出力
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function glaph() {

		require_once 'phplot/phplot.php';
		$this->autoLayout = false;
		$this->autoRender = false;

		$abcData = array();
		$kznNo = "";
		$startYmd = "";
		$endYmd = "";
		$width = 1200;
		$height = 1080;

		if (!isset($this->request->query['no'])
				|| !isset($this->request->query['startYmd'])
				|| !isset($this->request->query['endYmd'])) {
			return;
		}

		// グラフデータの抽出条件を設定
		$kznNo = $this->request->query['no'];
		$startYmd = $this->request->query['startYmd'];
		$endYmd = $this->request->query['endYmd'];

		// グラフのサイズを設定
		if (isset($this->request->query['width'])
				&& isset($this->request->query['height'])) {
			$width = $this->request->query['width'];
			$height = $this->request->query['height'];
		}

		// グラフを生成
		$plot = new PHPlot($width, $height);

		// フォント設定
		$plot->SetTTFPath(Configure::read("FontDir"));
		$plot->SetDefaultTTFont("ipaexg.ttf");
		// グラフ全体を囲むボーダーの設定
		$plot->SetImageBorderType('plain');


		// 改善データ情報を取得
		$res = $this->WEX094Model->getKaizenData($kznNo);

		// ABCデータベース日別情報を取得
		if (!empty($res)) {
			$kznData = $res[0];
			$abcData = $this->WEX094Model->getAbcDailyData($kznData["BNRI_DAI_CD"], $kznData["BNRI_CYU_CD"], $kznData["BNRI_SAI_CD"], $startYmd, $endYmd);
		}

		if (empty($abcData)) {
			$message = "該当データなし";
			$plot->SetImageBorderWidth(1);
			$plot->SetBackgroundColor('#fafafa');
			$plot->DrawMessage($message, array(
					'draw_background' => TRUE,
					'draw_border' => TRUE,
					'reset_font' => FALSE,
					'wrap_width' => 50,
					'text_color' => 'navy'));
			$plot->PrintImage();
			return;
		}

		$kznKbn = $kznData["KBN_KZN_KMK"];
		$kznCost = $kznData["KZN_COST"];

		$gAbcData = array();  // ABCコストデータ
		$gKzcData = array();  // 改善コストデータ
		$gKzkData = array();  // 改善項目データ


		$gAbcData[] = array('', '');
		$gKzcData[] = array('', $kznCost);
		$gKzkData[] = array('', '', $kznData["KZN_VALUE"]);

		// 範囲年月日を作成
		$ymdArray = $this->createDateRangeArrays($startYmd, $endYmd);

		$maxAbcCost = 0;
		$maxKznCost = 0;
		$minKznCost = 0;

		for ($i = 0; $i < count($ymdArray); $i++) {

			$ymd = $ymdArray[$i];
			$ymdLbl = substr($ymd, 0, 4)."/".substr($ymd, 4, 2)."/".substr($ymd, 6, 2);
			$data = array();

			// 該当のABCデータベース日別情報を取り出す
			foreach ($abcData as $array) {
				if ($array["YMD"] == $ymd) {
					$data = $array;
					break;
				}
			}

			// ABCコストデータを設定
			$abcCost = empty($data) ? 0 : $data["ABC_COST"];
			if ($maxAbcCost < $abcCost) {
				$maxAbcCost = $abcCost;
			}
			$gAbcData[] = array($ymdLbl, $abcCost);

			// 改善コストデータを設定
			$gKzcData[] = array($ymdLbl, $kznCost);

			// 改善項目データを設定
			$kzkCost = 0;
			if (!empty($data)) {
				if ("01" == $kznKbn) {
					$kzkCost = $data["TANKA"];             // 改善項目区分 = "01" (単価)
				} else if ("02" == $kznKbn) {
					$kzkCost = $data["JIKAN"];             // 改善項目区分 = "02" (時間)
				} else if ("03" == $kznKbn) {
					$kzkCost = $data["NINZU"];             // 改善項目区分 = "03" (人数)
				} else if ("04" == $kznKbn) {
					$kzkCost = $data["BUTURYO"];           // 改善項目区分 = "04" (物量)
				} else if ("05" == $kznKbn) {
					$kzkCost = $data["SEISANSEI_BUTURYO"]; // 改善項目区分 = "05" (生産性)
				} else if ("06" == $kznKbn) {
					$kzkCost = $data["URIAGE"];            // 改善項目区分 = "06" (売上)
				} else if ("07" == $kznKbn) {
					$kzkCost = $data["RIEKI"];             // 改善項目区分 = "07" (利益)
				}
			}
			if ($kzkCost < $minKznCost) {
				$minKznCost = $kzkCost;
			}
			if ($maxKznCost < $kzkCost) {
				$maxKznCost = $kzkCost;
			}
			$gKzkData[] = array($ymdLbl, $kzkCost, $kznData["KZN_VALUE"]);
		}

		$gAbcData[] = array('', '');
		$gKzcData[] = array('', $kznCost);
		$gKzkData[] = array('', '', $kznData["KZN_VALUE"]);


		$plot->SetPrintImage(False);

		# グラフ内の背景色の設定
		$plot->SetPlotBgColor('#f9f9f9');

		# グラフ内点線部分の背景色設定
		$plot->SetLightGridColor('DimGrey');

		# ===================================================================================
		# 【ABCコスト出力】
		# ===================================================================================
		$plot->SetDrawPlotAreaBackground(True);
		$plot->SetPlotType('bars');
		$plot->SetShading(3);
		$plot->SetDrawYGrid(false);

		# データ設定 ------------------------------------------------------------------------
		$plot->SetDataType('text-data');
		$plot->SetDataValues($gAbcData);

		# グラフエリアと画像とのマージン設定 ------------------------------------------------
		$plot->SetMarginsPixels(100, 100, 100, 60);

		# Y軸の最大値の設定
		$plot->SetPlotAreaWorld(NULL, 0, NULL, $this->define_Y_MaxValue($kznCost < $maxAbcCost ? $maxAbcCost : $kznCost));


		# Y軸の目盛の設定
		$plot->SetYTitle("ABC コスト", 'plotright');
		$plot->SetYTickIncrement($this->define_Y_Incriment($kznCost < $maxAbcCost ? $maxAbcCost : $kznCost));
		$plot->SetYLabelType('data', 0);
		$plot->SetYTickLabelPos('plotright');
		$plot->SetYTickPos('plotright');
		$plot->SetYTickCrossing(0);

		# X軸の目盛の設定
		$plot->SetXTickIncrement(1);
		$plot->SetXTickPos('plotdown');
		$plot->SetXTickCrossing(0);
		$plot->SetXLabelAngle(45);
		$plot->SetDataColors('#c1e594');

		# 項目見出作成
		$plot->SetLegend(array('ABCコスト'));
		$plot->SetLegendPixels($width - 90, 10);

		$plot->DrawGraph();

		# ===================================================================================
		# 【改善コスト】
		# ===================================================================================

		$plot->SetDrawPlotAreaBackground(False);
		$plot->SetPlotType('linepoints');
		$plot->SetLineWidths(3);
		$plot->SetDrawYGrid(true);
		$shapes = array('none');
		$plot->SetPointShapes($shapes);
		$plot->SetDrawYGrid(false);

		# データ設定 ------------------------------------------------------------------------
		$plot->SetDataType('text-data');
		$plot->SetDataValues($gKzcData);

		# Y軸の最大値の設定
		$plot->SetPlotAreaWorld(NULL, 0, NULL, $this->define_Y_MaxValue($kznCost < $maxAbcCost ? $maxAbcCost : $kznCost));

		# Y軸の目盛の設定
		$plot->SetYTitle('','none');
		$plot->SetYTickIncrement($this->define_Y_Incriment($kznCost < $maxAbcCost ? $maxAbcCost : $kznCost));
		$plot->SetYLabelType('data', 0);
		$plot->SetYTickLabelPos('none');
		$plot->SetYTickPos('none');
		$plot->SetYTickCrossing(5);

		# X軸の目盛の設定
		$plot->SetXTickIncrement(1);
		$plot->SetXDataLabelPos('none');
		$plot->SetXTickPos('none');
		$plot->SetXTickCrossing(0);
		$plot->SetXLabelAngle(45);
		$plot->SetDataColors(array('#837934'));

		# 項目見出作成
		$plot->SetLegend(array('改善コスト'));
		$plot->SetLegendPixels($width - 90, 33);

		$plot->DrawGraph();


		# ===================================================================================
		# 【改善項目出力】
		# ===================================================================================

		$plot->SetDrawPlotAreaBackground(False);
		$plot->SetPlotType('linepoints');
		$plot->SetLineWidths(3);
		$plot->SetDrawYGrid(true);
		$shapes = array('dot', 'none');
		$plot->SetPointShapes($shapes);

		# データ設定 ------------------------------------------------------------------------
		$plot->SetDataType('text-data');
		$plot->SetDataValues($gKzkData);

		# Y軸の最大値の設定
		$plot->SetPlotAreaWorld(NULL, $minKznCost < 0 ? $this->define_Y_MaxValue($minKznCost) : 0, NULL, $this->define_Y_MaxValue($maxKznCost < $kznData["KZN_VALUE"] ? $kznData["KZN_VALUE"] : $maxKznCost));

	    $aaa = $this->define_Y_Incriment(($maxKznCost < $kznData["KZN_VALUE"] ? $kznData["KZN_VALUE"] : $maxKznCost) + abs($minKznCost));

		# Y軸の目盛の設定
		$plot->SetYTitle($kznData["KZN_KMK_VALUE"], 'plotleft');
		$plot->SetYTickIncrement($this->define_Y_Incriment(($maxKznCost < $kznData["KZN_VALUE"] ? $kznData["KZN_VALUE"] : $maxKznCost) + abs($minKznCost)));
		$plot->SetYLabelType('data', 0);
		$plot->SetYTickLabelPos('plotleft');
		$plot->SetYTickPos('plotleft');
		$plot->SetYTickCrossing(5);

		# X軸の目盛の設定
		$plot->SetXTickIncrement(1);
		$plot->SetXDataLabelPos('none');
		$plot->SetXTickPos('none');
		$plot->SetXTickCrossing(0);
		$plot->SetXLabelAngle(45);
		$plot->SetDataColors(array('#476a8d','#d50000'));

		# 項目見出作成
		$plot->SetLegend(array($kznData["KZN_KMK_VALUE"], '数値目標'));
		$plot->SetLegendPixels(10, 10);

		$plot->DrawGraph();

		$plot->PrintImage();
	}

	/**
	 * 指定された年月日の範囲配列を作成します。
	 *
	 * @param $start 開始年月日
	 * @param $end 終了年月日
	 * @return 年月日の範囲配列
	 */
	function createDateRangeArrays($start, $end) {

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

	/**
	 * define Y Max Value
	 * @param $max_value
	 * @return Integer
	 */
	function define_Y_MaxValue($max_value) {

		$flg_minus = 0;

		$max_value = floor($max_value);

		if ($max_value < 0) {
			$flg_minus = 1;
			$max_value = $max_value * -1;
		}
		if( $max_value < 10 ) {
			$yMax = 10;
			return $yMax;
		}

		$keta_num = strlen($max_value);               // keta su-
		$left_2_keta = substr($max_value, 0, 2);      // left 2keta (10~99)
		$left_1_num = substr($left_2_keta, 0, 1);     // left 1 (1~9)
		$left_2_num = substr($left_2_keta, 1, 1);     // left 2 (0~9)

		if( $left_2_keta < 50 ) {
			if( $left_2_num <= 4 ) {
				$yMax = (int)("$left_1_num" . "5");
				$yMax = $yMax * pow(10, $keta_num-2);
			} else {
				$yMax = ($left_1_num+1) * pow(10, $keta_num-1);
			}
		} elseif( $left_2_keta < 90 ) {
			if( $left_2_num <= 3 ) {
				$yMax = (int)("$left_1_num" . "5");
				$yMax = $yMax * pow(10, $keta_num-2);
			} else {
				$yMax = ($left_1_num+1) * pow(10, $keta_num-1);
			}
		} else {
			$yMax = ($left_1_num+1) * pow(10, $keta_num-1);
		}

		if ($flg_minus == 1) {
			$yMax = $yMax * -1;
		}

		return $yMax;
	}

	/**
	 * define Y memori  kankaku
	 * @param $yMax
	 * @return Integer
	 */
	function define_Y_Incriment($yMax) {
		$yMax = floor($yMax);
		$keta_num = strlen($yMax);
		$left_2_keta = substr($yMax, 0, 2);

		if( $left_2_keta <= 15 ) {
			$yIncri = 2 * pow(10, $keta_num-2);
		} elseif( $left_2_keta <= 35 ) {
			$yIncri = 5 * pow(10, $keta_num-2);
		} else {
			$yIncri = 1 * pow(10, $keta_num-1);
		}

		return $yIncri;
	}
}
