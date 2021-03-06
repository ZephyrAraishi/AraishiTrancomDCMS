<?php
/**
 * WEX110
 *
 * 名称マスタ設定
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
App::uses('WEX110Model', 'Model');

class WEX110Controller extends AppController {

	public $name = 'WEX110';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WEX']['110']['kino_nm']));
		
		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
		                             $this->Session->read('select_sosiki_cd'),
		                             $this->name
		                            );

		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),
				                     $this->Session->read('select_sosiki_cd'),
				                     $this->name
				                     );

		$this->WEX110Model = new WEX110Model($this->Session->read('select_ninusi_cd'),
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
		
		//Viewへセット
		$this->set("onload","initReload();" .
							"initPopUp('".Router::url('/WEX110/popup', false)."');" .
							"initAddRow('".Router::url('/WEX110/popup_insert', false)."');".
							"initUpdate('".Router::url('/WEX110/dataUpdate', false)."');");
		
		$meisyo = '';
		$kbn = '';

		$pageID = 1;
		$return_cd = '';
		$display = '';
		$message = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['meisyo'])){
			$meisyo = $this->request->query['meisyo'];
		}
		
		if (isset($this->request->query['kbn'])){
			$kbn = $this->request->query['kbn'];
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
			
			$this->Session->write('displayWEX110','true');
			
			$this->redirect('/WEX110/index?return_cd=' . $return_cd . 
										  '&message=' . $message .
										  '&meisyo=' . $meisyo . 
										  '&kbn=' . $kbn . 
										  '&pageID=' . $pageID .
										  '&display=false'
										  );
			
			return;
		} else if ($this->Session->check('displayWEX110') && $display == "false") {
			
			$this->Session->delete('displayWEX110');
			
		} else if (!$this->Session->check('displayWEX110') && $display == "false") {
			
			$this->redirect('/WEX110/index?&meisyo=' . $meisyo .
										  '&kbn=' . $kbn . 
										  '&pageID=' . $pageID
										  );
			return;
		}
	    
	    //区分一覧取得
	    $meisyoArray = $this->WEX110Model->getMeisyo('','');
	    $kbnArray = array();
	    foreach($meisyoArray as $mei) {
		    $kbnArray[] = $mei['MEI_KBN'];
	    }
	    
	    $kbnArray = array_unique ($kbnArray);
	    $this->set('kbnArray',$kbnArray);
		

		//検索条件がある場合検索
		if (isset($meisyo)){

			//リスト一覧
			$lsts = $this->WEX110Model->getMeisyo($meisyo,$kbn);
			
			//ページャー
			$pageNum = 50;

			$options = array(
					"totalItems" => count($lsts),
					"delta" => 10,
					"perPage" => $pageNum,
					"httpMethod" => "GET",
					"path" => "/DCMS/WEX110/index"
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

			//前回の処理でエラーが発生した場合、前回のデータをそのまま表示
			if ($this->Session->check('saveTableWEX110')) {

				//前回のデータを取得
				$getArray = $this->Session->read('saveTableWEX110');

				//前回のデータを設置
				$data = $getArray->data;
				$this->set('lsts',$data);

				//データ構造が違うのでレンダリングを変更
				$this->render("index_error");

				//前回のデータを削除
				$this->Session->delete('saveTableWEX110');

				//エラーが発生していない場合は、検索条件を元に表示
			} else {

				//タイムスタムプ取得
				$this->set('lsts',$arrayList);
			}
		}
		
		//Viewへセット
		$this->set('index',$index);
		

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
		$this->Session->write('saveTableWEX110',$getArray);

		$data = $getArray->data;

		//チェック
		if (!$this->WEX110Model->checkData($data)) {
			
			$array = $this->WEX110Model->errors;

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
		if (!$this->WEX110Model->setData($data, $this->Session->read('staff_cd'))) {
		    
		    
		    $array = $this->WEX110Model->errors;
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
		$this->Session->delete('saveTableWEX110');


		
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
				
		// 名称マスタ取得
		//単位区分
		$kbnTanis = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.tani'));
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnTanis', $kbnTanis);

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
				
		// 名称マスタ取得
		//単位区分
		$kbnTanis = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.tani'));
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnTanis', $kbnTanis);

	}	


}
