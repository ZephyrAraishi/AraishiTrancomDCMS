<?php
/**
 * WEX093
 *
 * 日別作業実績参照
 *
 * @category      WEX
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');
App::uses('MMeisyo', 'Model');
App::uses('MBunrui', 'Model');
App::uses('WEX093Model', 'Model');

class WEX093Controller extends AppController {

	public $name = 'WEX093';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WEX']['093']['kino_nm']));

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

		$this->WEX093Model = new WEX093Model($this->Session->read('select_ninusi_cd'),
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

		//ライブラリ読み込み
		require_once 'Pager.php';

		//キャッシュクリア
		Cache::clear();

		//Javascript設定
		$this->set("onload","initReload();".
							"initPopUp('".Router::url('/WEX093/popup', false)."');" .
							"initBunrui();");


		$date_from = '';
		$date_to = '';

		$bnri_dai_cd = '';
		$bnri_cyu_cd = '';
		$bnri_sai_cd = '';

		$kotei = '';

		$kbn_ktd_mktk  = '';
		$kbn_hissu_wk  = '';
		$kbn_gyomu     = '';
		$kbn_fukakachi = '';
		$kbn_senmon    = '';
		$kbn_ktd_type  = '';

		$pageID    = 1;
		$return_cd = '';
		$message   = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['date_from'])){
			$kotei = $this->request->query['kotei'];

			$date_from      = $this->request->query['date_from'];
			$date_to  = $this->request->query['date_to'];

			$kbn_ktd_mktk  = $this->request->query['kbn_ktd_mktk'];
			$kbn_hissu_wk  = $this->request->query['kbn_hissu_wk'];
			$kbn_gyomu     = $this->request->query['kbn_gyomu'];
			$kbn_fukakachi = $this->request->query['kbn_fukakachi'];
			$kbn_senmon    = $this->request->query['kbn_senmon'];
			$kbn_ktd_type  = $this->request->query['kbn_ktd_type'];

		}

		//POST時
		if(isset($this->request->query['pageID'])) {

			$pageID = $this->request->query['pageID'];
		}

		//リターンCD
		if(isset($this->request->query['return_cd'])) {

			$return_cd = $this->request->query['return_cd'];
		}

		//メッセージ
		if(isset($this->request->query['message'])) {

			$message = $this->MCommon->escapePHP($this->request->query['message']);
		}


		//分類コンボ設定
	    //大に分ける
	    $opsions = $this->MBunrui->getBunruiDaiPDO();
	    $this->set("koteiDaiList",$opsions);
	    //中に分ける
	    $opsions = $this->MBunrui->getBunruiCyuPDO();
	    $this->set("koteiCyuList",$opsions);
	    //細に分ける
	    $opsions = $this->MBunrui->getBunruiSaiPDO();
		$this->set("koteiSaiList",$opsions);


		//活動目的
		$kbnKtdMktks = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.ktd_mktk'));
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnKtdMktks', $kbnKtdMktks);
		//必須作業
		$kbnHissuWks = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.hissu'));
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnHissuWks', $kbnHissuWks);
		//業務区分
		$kbnGyomus = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.gyomu'));
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnGyomus', $kbnGyomus);
		//付加価値性
		$kbnFukakachis = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.fukakachi'));
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnFukakachis', $kbnFukakachis);
		//専門区分
		$kbnSenmons = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.senmon'));
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnSenmons', $kbnSenmons);
		//活動タイプ
		$kbnKtdTypes = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.ktd_type'));
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnKtdTypes', $kbnKtdTypes);



		//入力チェック
		$errorArray = "";
		if (isset($this->request->query['date_from'])) {
			$abc = $this->WEX093Model->checkInputSearch($date_from, $date_to);
			$errorArray = $this->WEX093Model->errors;
		}

		if (!empty($errorArray)) {

			$this->set("errors",$errorArray);

		} else {



			//入力チェックOK時、検索

			//検索可能最大件数取得
			$SearchMaxCnt = $this->WEX093Model->getMSystem();

			$errorArray = $this->WEX093Model->errors;
			if (!empty($errorArray)) {
				$this->set("errors",$errorArray);
			} else {

				$bnri_dai_cd = substr($kotei, 0,3);
				$bnri_cyu_cd = substr($kotei, 4,3);
				$bnri_sai_cd = substr($kotei, 8,3);

				//作業実績日別データ一覧
				$lsts = $this->WEX093Model->getAbcDbDayList($bnri_dai_cd, $bnri_cyu_cd,  $bnri_sai_cd, $date_from, $date_to,
				                                           $kbn_ktd_mktk, $kbn_hissu_wk, $kbn_gyomu,
				                                           $kbn_fukakachi, $kbn_senmon , $kbn_ktd_type, $SearchMaxCnt);

				$errorArray = $this->WEX093Model->errors;
				if (!empty($errorArray)) {
					$this->set("errors",$errorArray);
				} else {

					$lsts  = Sanitize::clean($lsts);

					//ページャー
					$pageNum = 50;

					$options = array(
							"totalItems" => count($lsts),
							"delta" => 10,
							"perPage" => $pageNum,
							"httpMethod" => "GET",
							"path" => "/DCMS/WEX093/index"
					);


					$pager = @Pager::factory($options);

					//ナビゲーションを設定
					$navi = $pager -> getLinks();
					$this->set('navi',$navi["all"]);

					//インデックスを取得
					$index = ($pageID - 1) * $pageNum;
					$arrayList = array();

					$limit = $index + $pageNum;

					if ($limit > count($lsts)) {

						$limit = count($lsts);
					}

					for($i = $index; $i < $limit ; $i++){
						$arrayList[] = $lsts[$i];
					}

					//Viewへセット
					$this->set('index',$index);


					//エラーの場合
					if ($this->Session->check('saveTableWEX093')) {

						//データを取得
						$getArray = $this->Session->read('saveTableWEX093');

						$data = $getArray->data;
						$timestamp = $getArray->timestamp;

						$this->set("timestamp", $timestamp);
						$this->set('lsts',$data);
						$this->render("index_error");

						$this->Session->delete('saveTableWEX093');

					//エラー以外で条件付きの場合
					} else {

						$this->set('lsts',$arrayList);
					}
				}
			}

		}



	}




}
