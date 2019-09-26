<?php
/**
 * WPO100
 *
 * 出荷指示データ削除画面
 *
 * @category      WPO
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 * @memo          2017/09/29 K.Araishi TPM070を元に新規作成
 */
App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');
App::uses('MMeisyo', 'Model');
App::uses('WPO100Model', 'Model');

class WPO100Controller extends AppController {

	public $name = 'WPO100';
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
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['WPO']));
		// 機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WPO']['100']['kino_nm']));

		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
		                             $this->Session->read('select_sosiki_cd'),
		                             $this->name
		                            );

		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),
				                     $this->Session->read('select_sosiki_cd'),
				                     $this->name
				                     );

		$this->WPO100Model = new WPO100Model($this->Session->read('select_ninusi_cd'),
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

		Cache::clear();

		require_once 'Pager.php';

		//Javascript設定
		$this->set("onload","initReload();".
				"initDELETE('".Router::url('/WPO100/DeleteData', false). "');" );

		$ymd_from = '';
		$BatchNo_from = '';
		$homen = '';

		$pageID = 1;
		$return_cd = '';
		$display = '';
		$message = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['ymd_from'])){
			$ymd_from = $this->request->query['ymd_from'];
		}
		if (isset($this->request->query['BatchNo_from'])){
			$BatchNo_from = $this->request->query['BatchNo_from'];
		}
		if (isset($this->request->query['homen'])){
			$homen = $this->request->query['homen'];
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
		//エラーコード
		if(isset($this->request->query['display'])) {
			$display = $this->request->query['display'];
		}

		//出荷日は当日日付を初期設定する
		if ($ymd_from == '') {
			$ymd_from = $this->WPO100Model->getToday();
		}

		//検索条件を保存
		if ($ymd_from != "") {
			$this->set("ymd_from",$ymd_from);
		}
		if ($BatchNo_from != "") {
			$this->set("BatchNo_from",$BatchNo_from);
		}
		if ($homen != "") {
			$this->set("homen",htmlspecialchars($homen));
		}

		//検索条件の必須チェック
		if ($this->Session->check('displayWPO1002') && $homen=='') {

					//方面がないエラー
					$errorArray = array();
					$message = base64_encode ($this->MMessage->getOneMessage('WPOE100002'));

					$this->Session->delete('displayWPO1002','true');

					$this->redirect('/WPO100/index?return_cd=1' .
										 '&message=' . $message .
										 '&ymd_from=' . $ymd_from .
										 '&BatchNo_from=' . $BatchNo_from .
										 '&homen=' . $homen .
										 '&pageID=' . $pageID .
										 '&display=true'
					 );
		}

		//検索条件がある場合検索
		if ($ymd_from != '' && $homen != '') {

			//リスト一覧
			$lsts =  $this->WPO100Model->getList($ymd_from, $BatchNo_from, $homen);

			//ページャー
			$pageNum = 50;

			$options = array(
					"totalItems" => count($lsts),
					"delta" => 10,
					"perPage" => $pageNum,
					"httpMethod" => "GET",
					"path" => "/DCMS/WPO100/index"
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

			//タイムスタムプ取得
			$this->set("timestamp", $this->WPO100Model->getTimestamp());
			$this->set('lsts',$arrayList);

			//Viewへセット
			$this->set('index',$index);

			$this->Session->delete('displayWPO1002','true');

		//条件が何もない場合
		} else {

			//タイムスタムプ取得
			$this->set("timestamp", $this->WPO100Model->getTimestamp());
			$this->Session->write('displayWPO1002','true');

		}
	}


	/**
	 * データ削除処理
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function DeleteData() {

		//ポップアップデータだけ取得するため、レイアウトは無効にする
		$this->autoLayout = false;

		$ymd_from = '';
		$BatchNo_from = '';
		$homen = '';

		$pageID = 1;
		$display = '';
		$message = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['ymd_from'])){
			$ymd_from = $this->request->query['ymd_from'];
		}
		if (isset($this->request->query['BatchNo_from'])){
			$BatchNo_from = $this->request->query['BatchNo_from'];
		}
		if (isset($this->request->query['homen'])){
			$homen = $this->request->query['homen'];
		}
		//POST時
		if(isset($this->request->query['pageID'])) {
			$pageID = $this->request->query['pageID'];
		}

		// 出荷関連データを削除する
		//$returnStatus = $this->WPO100Model->deleteShukkaData($ymd_from, $BatchNo_from, $homen);
		$returnStatus = $this->WPO100Model->updateShukkaData($ymd_from, $BatchNo_from, $homen);
		$message = base64_encode($returnStatus);

		$this->redirect('/WPO100/index?return_cd=1' .
							 '&message=' . $message .
							 '&ymd_from=' . $ymd_from .
							 '&BatchNo_from=' . $BatchNo_from .
							 '&homen=' . $homen .
							 '&pageID=' . $pageID .
							 '&display=true'
		 );

		return;

	}

}

?>