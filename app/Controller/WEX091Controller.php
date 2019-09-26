<?php
/**
 * WEX091
 *
 * 月別作業実績参照
 *
 * @category      WEX
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');
App::uses('MBunrui', 'Model');
App::uses('MMeisyo', 'Model');
App::uses('WEX091Model', 'Model');

class WEX091Controller extends AppController {

	public $name = 'WEX091';
	public $layout = "DCMS";

	/**
	 * 起動時処理
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function beforeFilter() {

		parent::beforeFilter();

		//ヘッダー設定

		//システム名
		$this->set('system', Sanitize::stripAll($this->Session->read('system_name')));
		//サブシステム名
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['WEX']));
		//機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WEX']['091']['kino_nm']));

		//メッセージマスタモデル
		$this->MMessage = new MMessage($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//共通関数モデル
		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//分類マスタモデル
		$this->MBunrui = new MBunrui($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//名称マスタ
		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//WEX091モデル
		$this->WEX091Model = new WEX091Model($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);
	}

	/**
	 * 初期表示
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function index(){

		// 初期処理
		$this->init();

		// 指定されたアクションにディスパッチ
		if(isset($this->request->query['action'])) {
			$actionName = $this->request->query['action'];
			if (method_exists($this, $actionName)) {
				$this->$actionName();
				return;
			}
		}

		// 初期表示のためセッションをクリア
		$this->Session->delete('saveTableWEX091');

	}

	/**
	 * 初期処理
	 */
	private function init() {
		//キャッシュクリア
		Cache::clear();

		//Javascript設定
		$this->set("onload","initReload();initBunrui();");

		// 工程コンボ作成
		$this->setBunruiCombo();

		// 名称マスタからプルダウン作成
		$this->setMeisyoInfo();
	}

	/**
	 * 検索ボタン押下処理
	 */
	private function search() {
		$result = array();
		$conditions = $this->request->query;

		//検索可能最大件数取得
		$SearchMaxCnt = $this->WEX091Model->getMSystem();

		$errorArray = $this->WEX091Model->errors;
		if (!empty($errorArray)) {
			// エラーの場合
			// エラーメッセージ設定
			$this->set("errors",$errorArray);

			// セッションに結果があれば復元する
			if ($this->Session->check('saveTableWEX091')) {
				$resultDto = $this->Session->read('saveTableWEX091');
				$resultDto = $this->createPaging($resultDto->baseList, $resultDto->pageID);
				$result = $resultDto->pageList;
			}
		} else {

			// 入力チェック
			if ($this->WEX091Model->checkBeforeSearch($conditions, $SearchMaxCnt)) {
				$abcDbMonthList = $this->WEX091Model->getAbcDbMonthList($conditions);
				$abcDbMonthList = Sanitize::clean($abcDbMonthList);
				$resultDto = $this->createPaging($abcDbMonthList);

				// バリデータエラー時などに結果を復元させるため、センションに保存
				$this->Session->write('saveTableWEX091', $resultDto);
				$result = $resultDto->pageList;

			} else {
				// エラーの場合
				// エラーメッセージ設定
				$this->set("errors", $this->WEX091Model->errors);

				// セッションに結果があれば復元する
				//if ($this->Session->check('saveTableWEX091')) {
				//	$resultDto = $this->Session->read('saveTableWEX091');
				//	$resultDto = $this->createPaging($resultDto->baseList, $resultDto->pageID);
				//	$result = $resultDto->pageList;
				//}
			}

		}

		$this->set('resultList', $result);
	}

	/**
	 * 工程コンボ作成
	 */
	private function setBunruiCombo() {
		//大に分ける
		$opsions = $this->MBunrui->getBunruiDaiPDO();
		$this->set("koteiDaiList",$opsions);

		//中に分ける
		$opsions = $this->MBunrui->getBunruiCyuPDO();
		$this->set("koteiCyuList",$opsions);

		//細に分ける
		$opsions = $this->MBunrui->getBunruiSaiPDO();
		$this->set("koteiSaiList",$opsions);
	}

	/**
	 * 名称マスタからプルダウン作成
	 */
	private function setMeisyoInfo () {
		// 活動目的
		$kbnKtdMktkList = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.ktd_mktk'));
		$this->set('kbnKtdMktkList', $kbnKtdMktkList);

		// 必須
		$kbnHissuWkList = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.hissu'));
		$this->set('kbnHissuWkList', $kbnHissuWkList);

		// 業務
		$kbnGyomuList = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.gyomu'));
		$this->set('kbnGyomuList', $kbnGyomuList);

		// 価値
		$kbnFukaKachiList = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.fukakachi'));
		$this->set('kbnFukaKachiList', $kbnFukaKachiList);

		// 専門
		$kbnSenmonList = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.senmon'));
		$this->set('kbnSenmonList', $kbnSenmonList);

		// タイプ
		$kbnKtdTypeList = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.ktd_type'));
		$this->set('kbnKtdTypeList', $kbnKtdTypeList);
	}

	/**
	 * ページング情報作成
	 * @param unknown $lsts
	 * @param $requestPageID
	 */
	private function createPaging($lsts, $requestPageID = false) {
		if (empty($lsts)) {
			$returnDto = new stdClass();
			$returnDto->baseList = $lsts;
			$returnDto->pageList = $lsts;
			$returnDto->pageID = false;

			return $returnDto;
		}

		//ライブラリ読み込み
		require_once 'Pager.php';

		//ページャーの設定
		define("PER_PAGE", 50);

		$options = array(
				"totalItems" => count($lsts),
				"delta" => 10,
				"perPage" => PER_PAGE,
				"httpMethod" => "GET",
				"path" => "/DCMS/WEX091/index",
		);

		if ($requestPageID) {
			$options["currentPage"] = $requestPageID;
		}

		$pager = @Pager::factory($options);

		//ページNo
		$pageID = 1;
		if($requestPageID) {
			$pageID = $this->MCommon->getValidPageID($pager, $requestPageID);
		} else if (isset($this->request->query['pageID'])) {
			$pageID = $this->MCommon->getValidPageID($pager, $this->request->query['pageID']);
		}

		$this->set("pageID", $pageID);

		//ページナビゲーションを設定
		$navi = $pager -> getLinks();
		$this->set('navi',$navi["all"]);

		//項目のインデックスを取得
		$index = ($pageID - 1) * PER_PAGE;
		$arrayList = array();

		$limit = $index + PER_PAGE;

		if ($limit > count($lsts)) {
			$limit = count($lsts);
		}

		//インデックスから表示項目を取得
		for($i = $index; $i < $limit ; $i++){
			$arrayList[] = $lsts[$i];
		}

		// 検索結果の連番に使うためViewに設定
		$this->set('index',$index);

		$returnDto = new stdClass();
		$returnDto->baseList = $lsts;
		$returnDto->pageList = $arrayList;
		$returnDto->pageID = $pageID;

		return $returnDto;
	}


}