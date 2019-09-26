<?php
/**
 * WPO050
 *
 * 作業指示日時締更新
 *
 * @category      WPO
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');
App::uses('MBunrui', 'Model');
App::uses('WPO050Model', 'Model');

class WPO050Controller extends AppController {

	public $name = 'WPO050';
	public $layout = "DCMS";

	/**
	 * 起動時処理
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function beforeFilter() {

		parent::beforeFilter();

		//システム名
		$this->set('system', Sanitize::stripAll($this->Session->read('system_name')));
		//サブシステム名
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['WPO']));
		//機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WPO']['050']['kino_nm']));

		//メッセージマスタモデル
		$this->MMessage = new MMessage($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//共通関数モデル
		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//分類マスタモデル
		$this->MBunrui = new MBunrui($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//WPO050モデル
		$this->WPO050Model = new WPO050Model($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

	}

	/**
	 * 初期表示
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function index(){

		//キャッシュクリア
		Cache::clear();

		//Javascript設定
		$this->set("onload","initReload();".
							"initUpdate('".Router::url('/WPO050/dataUpdate', false)."'," .
									   "'".Router::url('/WPO050/dataUpdate2', false)."'," .
									   "'".Router::url('/WPO050/dataUpdate3', false)."',".
									   "'".Router::url('/WPO050/dataUpdate4', false)."');"
							);

		//検索条件、ページID取得
		$return_cd = '';
		$display = '';
		$message = '';
		$ymd_sime = '';


		//リターンCD
		if(isset($this->request->query['return_cd'])) {

			$return_cd = $this->request->query['return_cd'];
		}

		//メッセージ
		if(isset($this->request->query['message'])) {

			$message = $this->MCommon->escapePHP($this->request->query['message']);
		}

		//メッセージディスプレイフラグ
		if(isset($this->request->query['display'])) {

			$display = $this->request->query['display'];
		}

		//メッセージディスプレイフラグ
		if(isset($this->request->query['ymd_sime'])) {

			$ymd_sime = $this->request->query['ymd_sime'];
		}

		if ($ymd_sime == "") {
			$ymd_sime = $this->WPO050Model->getYesterday();
			$this->set("yesterday",$ymd_sime);
		}

		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {

			$this->Session->write('displayWPO050','true');

			$this->redirect('/WPO050/index?return_cd=' . $return_cd .
										  '&message=' . $message .
										  '&ymd_sime=' . $ymd_sime .
										  '&display=false'
										  );

			return;
		} else if ($this->Session->check('displayWPO050') && $display == "false") {

			$this->Session->delete('displayWPO050');

		} else if (!$this->Session->check('displayWPO050') && $display == "false") {

			$this->redirect('/WPO050/index');
			return;
		}

		$data = $this->WPO050Model->getData();
		$this->set('lsts',$data);


	}

	/**
	 * 入力チェック・存在チェック
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function dataUpdate(){

		//データのやり取りだけなのでレンダリングなし
		$this->autoLayout =false;
		$this->autoRender = false;


		//ポストデータの存在確認エラー
		if(!$this->request->is('post')) {

			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000104'));
			return;
		}

		//データをJSONに変換する
		$getArray = json_decode($this->request->input());

		//データが空の場合、エラー
		if(count($getArray) == 0) {

			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000101'));
			return;
		}

		//データを取り出す
		$date = $getArray->date;


		//入力チェック
		if (!$this->WPO050Model->checkData($date,$this->Session->read('staff_cd'))) {

			//エラー吐き出し
			$array = $this->WPO050Model->errors;
		    $message = '';

		    foreach ( $array as $key=>$val ) {

		    	foreach ($val as $key2=>$val2) {

			    	$message = $message . $val2 . "<br>";
		    	}
		    }

		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}


		//成功時メッセージ
		echo "return_cd=0";
	}

	/**
	 * 警告チェック
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function dataUpdate2(){

		//データのやり取りだけなのでレンダリングなし
		$this->autoLayout =false;
		$this->autoRender = false;

		//データを取り出す
		$getArray = json_decode($this->request->input());
		$date = $getArray->date;


		//データ取得
		$result = $this->WPO050Model->checkData2($date,$this->Session->read('staff_cd'));

		if ($result === false) {

		    //エラー吐き出し
		    $array = $this->WPO050Model->errors;
		    $message = '';

		    foreach ( $array as $key=>$val ) {

		    	foreach ($val as $key2=>$val2) {

			    	$message = $message . $val2 . "<br>";
		    	}
		    }

		    echo "return_cd=1" . "&message=" .  base64_encode ($message) ;
		    return;
		}


		if ($result != "") {

			return "return_cd=0_1&message=" .  str_replace("+", "%2B",base64_encode ($result));
		} else {

			return "return_cd=0_2";
		}

	}

	/**
	 * 存在確認
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function dataUpdate3(){

		//データのやり取りだけなのでレンダリングなし
		$this->autoLayout =false;
		$this->autoRender = false;

		//データを取り出す
		$getArray = json_decode($this->request->input());
		$date = $getArray->date;


		//データ取得
		$result = $this->WPO050Model->checkData3($date,$this->Session->read('staff_cd'));

		if ($result === false) {

		    //エラー吐き出し
		    $array = $this->WPO050Model->errors;
		    $message = '';

		    foreach ( $array as $key=>$val ) {

		    	foreach ($val as $key2=>$val2) {

			    	$message = $message . $val2 . "<br>";
		    	}
		    }

		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message) ;
		    return;
		}


		if ($result == "0_0") {

			return "return_cd=0_0";
		} else {

			return "return_cd=0_1";
		}

	}

	/**
	 * 更新処理
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function dataUpdate4(){

		//データのやり取りだけなのでレンダリングなし
		$this->autoLayout =false;
		$this->autoRender = false;

		//データを取り出す
		$getArray = json_decode($this->request->input());
		$date = $getArray->date;
		$updateFlg = $getArray->updateFlg;


		//排他チェック
		if (!$this->WPO050Model->checkData4($date,$this->Session->read('staff_cd'))) {

			//エラー吐き出し
			$array = $this->WPO050Model->errors;
		    $message = '';

		    foreach ( $array as $key=>$val ) {

		    	foreach ($val as $key2=>$val2) {

			    	$message = $message . $val2 . "<br>";
		    	}
		    }

		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}

		//データ取得
		if (!$this->WPO050Model->setData($date,$this->Session->read('staff_cd'),$updateFlg)) {

		    //エラー吐き出し
		    $array = $this->WPO050Model->errors;
		    $message = '';

		    foreach ( $array as $key=>$val ) {

		    	foreach ($val as $key2=>$val2) {

			    	$message = $message . $val2 . "<br>";
		    	}
		    }

		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message) ;
		    return;
		}

		if ($updateFlg == "0") {

			return "return_cd=0" . "&message=" . base64_encode($this->MMessage->getOneMessage('WPOI050001'));
		} else {

			return "return_cd=0" . "&message=" . base64_encode($this->MMessage->getOneMessage('WPOI050002'));
		}

	}

}
