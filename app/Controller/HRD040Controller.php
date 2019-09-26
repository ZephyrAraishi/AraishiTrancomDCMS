<?php
/**
 * HRD040
 *
 * スタッフ許可マスタ設定
 *
 * @category      HRD
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');
App::uses('MMeisyo', 'Model');
App::uses('HRD040Model', 'Model');

class HRD040Controller extends AppController {

	public $name = 'HRD040';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WEX']['030']['kino_nm']));
		
		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
		                             $this->Session->read('select_sosiki_cd'),
		                             $this->name
		                            );

		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),
				                     $this->Session->read('select_sosiki_cd'),
				                     $this->name
				                     );

		$this->HRD040Model = new HRD040Model($this->Session->read('select_ninusi_cd'),
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
		$subsystem_id = '';
		$kino_id = '';

		$pageID = 1;
		$return_cd = '';
		$display = '';
		$message = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['staff_cd'])){
			$bnri_dai_cd = $this->request->query['subsystem_id'];
			$bnri_dai_nm = $this->request->query['kino_id'];
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
			
			$this->Session->write('displayHRD040','true');
			
			$this->redirect('/HRD040/index?return_cd=' . $return_cd . 
										  '&message=' . $message .
										  '&staff_cd=' . $staff_cd . 
										  '&subsystem_id=' . $subsystem_id . 
										  '&kino_id=' . $kino_id . 
										  '&pageID=' . $pageID .
										  '&display=false'
										  );
			
			return;
		} else if ($this->Session->check('displayHRD040') && $display == "false") {
			
			$this->Session->delete('displayHRD040');
			
		} else if (!$this->Session->check('displayHRD040') && $display == "false") {
			
			$this->redirect('/HRD040/index?&staff_cd=' . $staff_cd . 
										  '&subsystem_id=' . $subsystem_id . 
										  '&kino_id=' . $kino_id . 
										  '&pageID=' . $pageID
										  );
			return;
		}
	    
		//スタッフ許可マスタ一覧
		$lsts = $this->HRD040Model->getMStaffKyokaLst($staff_cd, $subsystem_id, $kino_id);

		//ページャー
		$pageNum = 50;
		
		$options = array(
				"totalItems" => count($lsts),
				"delta" => 10,
				"perPage" => $pageNum,
				"httpMethod" => "GET",
				"path" => "/DCMS/HRD040/index"
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
		$this->set("onload","initReload();initPopUp('".Router::url('/HRD040/popup', false)."');initAddRow('".Router::url('/HRD040/popup_insert', false)."');initUpdate('".Router::url('/HRD040/dataUpdate', false)."');");


		//Viewへセット
		$this->set('index',$index);

		
		//エラーの場合
		if ($this->Session->check('saveTableHRD040')) {
		
			//データを取得
			$getArray = $this->Session->read('saveTableHRD040');
			
			$data = $getArray->data;
			$timestamp = $getArray->timestamp;
			
			$this->set("timestamp", $timestamp);
			$this->set('lsts',$data);
			$this->render("index_error");
			
			$this->Session->delete('saveTableHRD040');
		
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
		$this->Session->write('saveTableHRD040',$getArray);

		$data = $getArray->data;
		$timestamp = $getArray->timestamp;
		
		//タイムスタンプチェック
		if (!$this->HRD040Model->checkTimestamp($timestamp)) {
			
			$array = $this->HRD040Model->errors;
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
		$abc = $this->HRD040Model->checkStaffKyokaMst($data);
			
		$array = $this->HRD040Model->errors;

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
		if (!$this->HRD040Model->setStaffKyokaMst($data, $this->Session->read('staff_cd'))) {
		    
		    
		    $array = $this->HRD040Model->errors;
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
		$this->Session->delete('saveTableHRD040');


		
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
