<?php
/**
 * WEX030
 *
 * スタッフ許可マスタ設定
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
App::uses('HRD011Model', 'Model');

class HRD011Controller extends AppController {

	public $name = 'HRD011';
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
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['HRD']));
		// 機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['HRD']['011']['kino_nm']));

		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
		                             $this->Session->read('select_sosiki_cd'),
		                             $this->name
		                            );

		$this->HRD011Model = new HRD011Model($this->Session->read('select_ninusi_cd'),
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

		$staff_cd = '';
		$sub_system_id = '';
		$kino_id = '';

		$pageID = 1;
		$return_cd = '';
		$display = '';
		$message = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['staff_cd'])){
			$staff_cd = $this->request->query['staff_cd'];
		}
		if (isset($this->request->query['sub_systemid'])){
			$sub_system_id = $this->request->query['sub_systemid'];
		}
		if (isset($this->request->query['kinoid'])){
			$kino_id = $this->request->query['kinoid'];
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

			$this->Session->write('displayHRD011','true');

			$this->redirect('/HRD011/index?return_cd=' . $return_cd .
										  '&message=' . $message .
										  '&staff_cd=' . $staff_cd .
										  '&sub_system_id=' . $sub_system_id .
										  '&kino_id=' . $kino_id .
										  '&pageID=' . $pageID .
										  '&display=false'
										  );

			return;
		} else if ($this->Session->check('displayHRD011') && $display == "false") {

			$this->Session->delete('displayHRD011');

		} else if (!$this->Session->check('displayHRD011') && $display == "false") {

			$this->redirect('/HRD011/index?&staff_cd=' . $staff_cd .
										  '&sub_system_id=' . $sub_system_id .
										  '&kino_id=' . $kino_id .
										  '&pageID=' . $pageID
										  );
			return;
		}

		//スタッフ許可マスタ一覧
		$lsts = $this->HRD011Model->getMStaffKyokaLst($staff_cd, $sub_system_id, $kino_id);

		//ページャー
		$pageNum = 50;

		$options = array(
				"totalItems" => count($lsts),
				"delta" => 10,
				"perPage" => $pageNum,
				"httpMethod" => "GET",
				"path" => "/DCMS/HRD011/index"
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
		$this->set("onload","initReload();initPopUp('".Router::url('/HRD011/popup', false)."');initAddRow('".Router::url('/HRD011/popup_insert', false)."');initUpdate('".Router::url('/HRD011/dataUpdate', false)."');");

		//Viewへセット
		$this->set('index',$index);

		//エラーの場合
		if ($this->Session->check('saveTableHRD011')) {

			//データを取得
			$getArray = $this->Session->read('saveTableHRD011');

			$data = $getArray->data;
			$timestamp = $getArray->timestamp;

			$this->set("timestamp", $timestamp);
			$this->set('lsts',$data);
			$this->render("index_error");

			$this->Session->delete('saveTableHRD011');

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
		$this->Session->write('saveTableHRD011',$getArray);

		$data = $getArray->data;
		$timestamp = $getArray->timestamp;

		//タイムスタンプチェック
		if (!$this->HRD011Model->checkTimestamp($timestamp)) {

			$array = $this->HRD011Model->errors;
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
		$abc = $this->HRD011Model->checkStaffKyokaMst($data);

		$array = $this->HRD011Model->errors;

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
		if (!$this->HRD011Model->setStaffKyokaMst($data, $this->Session->read('staff_cd'))) {


		    $array = $this->HRD011Model->errors;
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
		$this->Session->delete('saveTableHRD011');



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

	}


}
