<?php
/**
 * WEX012
 *
 * 組織マスタ設定
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
App::uses('WEX012Model', 'Model');

class WEX012Controller extends AppController {

	public $name = 'WEX012';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WEX']['012']['kino_nm']));

		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
		                             $this->Session->read('select_sosiki_cd'),
		                             $this->name
		                            );

		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),
				                     $this->Session->read('select_sosiki_cd'),
				                     $this->name
				                     );

		$this->WEX012Model = new WEX012Model($this->Session->read('select_ninusi_cd'),
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
							"initPopUp('".Router::url('/WEX012/popup', false)."');" .
							"initAddRow('".Router::url('/WEX012/popup_insert', false)."');" .
							"initUpdate('".Router::url('/WEX012/dataUpdate', false)."');" .
							"initBunrui();");

		$ninusi_cd = '';
		$sosiki_cd = '';

		$sosiki_nm = '';
		$sosiki_ryaku = '';

		$pageID = 1;
		$return_cd = '';
		$display = '';
		$message = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['ninusi_cd'])){
			$bnri_sai_cd = $this->request->query['ninusi_cd'];
		}
		if (isset($this->request->query['sosiki_cd'])){
			$bnri_sai_cd = $this->request->query['sosiki_cd'];
		}
		if (isset($this->request->query['sosiki_nm'])){
			$bnri_sai_cd = $this->request->query['sosiki_nm'];
		}
		if (isset($this->request->query['sosiki_ryaku'])){
			$bnri_sai_cd = $this->request->query['sosiki_ryaku'];
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

		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {

			$this->Session->write('displayWEX012','true');

			$this->redirect('/WEX012/index?return_cd=' . $return_cd .
										  '&message=' . $message .
										  '&ninusi_cd=' . $ninusi_cd .
										  '&sosiki_cd=' . $sosiki_cd .
										  '&sosiki_nm=' . $sosiki_nm .
										  '&sosiki_ryaku=' . $sosiki_ryaku .
										  '&pageID=' . $pageID .
										  '&display=false'
										  );

			return;
		} else if ($this->Session->check('displayWEX012') && $display == "false") {

			$this->Session->delete('displayWEX012');

		} else if (!$this->Session->check('displayWEX012') && $display == "false") {

			$this->redirect('/WEX012/index?&ninusi_cd=' . $ninusi_cd .
										  '&sosiki_cd=' . $sosiki_cd .
										  '&sosiki_nm=' . $sosiki_nm .
										  '&pageID=' . $pageID
										  );
			return;
		}


		//組織マスタ一覧
		$lsts = $this->WEX012Model->getMSosikiLst($ninusi_cd, $sosiki_cd, $sosiki_nm, $sosiki_ryaku);

		//ページャー
		$pageNum = 50;

		$options = array(
				"totalItems" => count($lsts),
				"delta" => 10,
				"perPage" => $pageNum,
				"httpMethod" => "GET",
				"path" => "/DCMS/WEX012/index"
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

		//荷主マスタ
		$mninusi = $this->WEX012Model->getMNinusi();
		// 一覧情報を一時的にセッションに保持
		$this->set('mninusi', $mninusi);

		//Viewへセット
		$this->set('index',$index);


		//エラーの場合
		if ($this->Session->check('saveTableWEX012')) {

			//データを取得
			$getArray = $this->Session->read('saveTableWEX012');

			$data = $getArray->data;
			$timestamp = $getArray->timestamp;

			$this->set("timestamp", $timestamp);
			$this->set('lsts',$data);
			$this->render("index_error");

			$this->Session->delete('saveTableWEX012');

		//エラー以外で条件付きの場合
		} else {

			/* タイムスタムプ取得 */
			$this->set("timestamp", $this->MCommon->getTimestamp());
			$this->set('lsts',$arrayList);
		}


	}


	/**
	 * DB更新処理
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function dataUpdate() {



		$this->autoLayout = false;
		$this->autoRender = false;

		if(!$this->request->is('post')) {

			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000104'));
			return;
		}



		//データを取得
		$getArray = json_decode($this->request->input());

		if(count($getArray) == 0) {

			echo "return_cd=" . "90" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000101'));
			return;
		}


		//データを一時保存
		$this->Session->write('saveTableWEX012',$getArray);

		$data = $getArray->data;
		$timestamp = $getArray->timestamp;

		//タイムスタンプチェック
		if (!$this->WEX012Model->checkTimestamp($timestamp)) {

			$array = $this->WEX012Model->errors;
		    $message = '';

		    foreach ( $array as $key=>$val ) {

		    	foreach ($val as $key2=>$val2) {

			    	$message = $message . $val2 . "<br>";
		    	}
		    }

		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}

		//必須チェック
		$abc = $this->WEX012Model->checkSosikiMst($data);

		$array = $this->WEX050Model->errors;

		if (!empty($array)) {

		    $message = '';

		    foreach ( $array as $key=>$val ) {

		    	foreach ($val as $key2=>$val2) {

			    	$message = $message . $val2 . "<br>";
		    	}
		    }

		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;

		}

		//更新処理
		if (!$this->WEX012Model->setSosikiMst($data, $this->Session->read('staff_cd'))) {


		    $array = $this->WEX012Model->errors;
		    $message = '';

		    foreach ( $array as $key=>$val ) {

		    	foreach ($val as $key2=>$val2) {

			    	$message = $message . $val2 . "<br>";
		    	}
		    }

		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message) ;
		    return;
		}


		//成功なのでテーブルをセッションから消す
		$this->Session->delete('saveTableWEX012');



		echo "return_cd=" . "0" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNI000001'));

	}




	/**
	 * ポップアップ取得
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function popup() {

		$this->autoLayout = false;

		//荷主マスタ
		$mninusi = $this->WEX012Model->getMNinusi();
		// 一覧情報を一時的にセッションに保持
		$this->set('mninusi', $mninusi);

	}


	/**
	 * ポップアップ取得
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function popup_insert() {

		$this->autoLayout = false;

		//荷主マスタ
		$mninusi = $this->WEX012Model->getMNinusi();
		// 一覧情報を一時的にセッションに保持
		$this->set('mninusi', $mninusi);

	}


}
