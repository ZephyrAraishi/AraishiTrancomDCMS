<?php
/**
 * WEX095
 *
 * ABCデータベース参照
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
App::uses('WEX095Model', 'Model');

class WEX095Controller extends AppController {

	public $name = 'WEX095';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WEX']['095']['kino_nm']));

		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
		                             $this->Session->read('select_sosiki_cd'),
		                             $this->name
		                            );

		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),
				                     $this->Session->read('select_sosiki_cd'),
				                     $this->name
				                     );

		//分類マスタモデル
		$this->MBunrui = new MBunrui($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		$this->WEX095Model = new WEX095Model($this->Session->read('select_ninusi_cd'),
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
	function index(){

		require_once 'Pager.php';

		// キャッシュクリア
		Cache::clear();

		//Javascript設定
		$this->set("onload","initReload();".
				"initBunrui();");


		// 分類選択コンボの情報を設定
		$opsions = $this->MBunrui->getBunruiDaiPDO();
		$this->set("koteiDaiList", $opsions);
		$opsions = $this->MBunrui->getBunruiCyuPDO();
		$this->set("koteiCyuList", $opsions);
		$opsions = $this->MBunrui->getBunruiSaiPDO();
		$this->set("koteiSaiList", $opsions);

		// 検索条件の検証を実行
		$errors = $this->validationSearchCondition();
		if (count($errors) != 0) {
			$this->set("errors", $errors);
			return;
		}

		$staffData = array();
		$start_ymd = "";
		$end_ymd = "";
		$bnri_dai_cd = "";
		$bnri_cyu_cd = "";
		$bnri_sai_cd = "";
		$staff_nm = "";
		$pageID = 1;
		$index = 0;
		$searchMaxCnt = 0;
		$rowMaxCnt = 50;

		// 検索条件を取得
		if (isset($this->request->query['start_ymd_ins'])) {
			$start_ymd = $this->request->query['start_ymd_ins'];
			$end_ymd = $this->request->query['end_ymd_ins'];
			$staff_nm = Sanitize::clean($this->request->query['staff_nm']);
			$kotei = $this->request->query['kotei'];
			if ($kotei != "") {
				$array = split("_", $kotei);
				if (1 <= count($array)) {
					$bnri_dai_cd = $array[0];
				}
				if (2 <= count($array)) {
					$bnri_cyu_cd = $array[1];
				}
				if (3 <= count($array)) {
					$bnri_sai_cd = $array[2];
				}
			}
		}
		if (isset($this->request->query['pageID'])) {
			$pageID = $this->request->query['pageID'];
		}

		// 検索可能最大件数を設定
		$searchMaxCnt = $this->WEX095Model->getSearchMaxCnt();

		// 該当データの件数を取得
		$cnt = $this->WEX095Model->getABCStaffCount($start_ymd, $end_ymd, $bnri_dai_cd, $bnri_cyu_cd, $bnri_sai_cd, $staff_nm);

		// 最大件数オーバーの場合、エラー
		if ($searchMaxCnt < $cnt) {
			$errors = array();
			$errors["error"][] = vsprintf($this->MMessage->getOneMessage('CMNE000102'), array($searchMaxCnt));
			$this->set("errors", $errors);
			return;
		}

		if ($cnt != 0) {

			// ABCデータベーススタッフ別情報を取得
			$lsts = $this->WEX095Model->getABCStaffData($start_ymd, $end_ymd, $bnri_dai_cd, $bnri_cyu_cd, $bnri_sai_cd, $staff_nm);

			// ページャーを設定
			$options = array(
					"totalItems" => count($lsts),
					"delta" => 10,
					"perPage" => $rowMaxCnt,
					"httpMethod" => "GET",
					"path" => "/DCMS/WEX095/index"
			);
			$pager = @Pager::factory($options);
			$navi = $pager -> getLinks();
			$this->set('navi',$navi["all"]);


			// 画面に表示するデータを取り出す
			$index = ($pageID - 1) * $rowMaxCnt;
			$limit = $index + $rowMaxCnt;
			if ($limit > count($lsts)) {
				$limit = count($lsts);
			}
			for($i = $index; $i < $limit ; $i++){
				$staffData[] = $lsts[$i];
			}
		}

		$this->set('index', $index);
		$this->set('rowMaxCnt', $rowMaxCnt);
		$this->set('searchMaxCnt', $searchMaxCnt);
		$this->set('staffData', $staffData);
	}

	/**
	 * 検索条件の検証を行います。
	 *
	 */
	private function validationSearchCondition() {

		$result = array();

		$startYmd = "";
		$endYmd = "";

		if (isset($this->request->query['start_ymd_ins'])){
			$startYmd = $this->request->query['start_ymd_ins'];
			$endYmd = $this->request->query['end_ymd_ins'];
		}

		// 年月日（自）のチェック
		if (empty($startYmd)) {
			$result["start_ymd"][] = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array("年月日（自）"));
		} else {
			if (preg_match("/^[0-9]{4}[0-9]{2}[0-9]{2}/", $startYmd) == false) {
				$result["start_ymd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("年月日（自）"));
			} else if (checkdate(substr($startYmd, 4, 2), substr($startYmd, 6, 2), substr($startYmd, 0, 4)) == false) {
				$result["startYmdd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("年月日（自）"));
			}
		}

		// 年月日（至）のチェック
		if (empty($endYmd)) {
			$result["end_ymd_ins"][] = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array("年月日（至）"));
		} else {
			if (preg_match("/^[0-9]{4}[0-9]{2}[0-9]{2}/", $endYmd) == false) {
				$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("年月日（至）"));
			} else if (checkdate(substr($endYmd, 4, 2), substr($endYmd, 6, 2), substr($endYmd, 0, 4)) == false) {
				$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("年月日（至）"));
			}
		}

		// FROM～TOの相関チェック
		if (empty($result) && $endYmd < $startYmd) {
			$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090002'), array("年月日（至）", "年月日（自）"));
		}

		return $result;
	}
}