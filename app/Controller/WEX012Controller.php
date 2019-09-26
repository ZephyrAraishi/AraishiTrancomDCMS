<?php
/**
 * WEX040
 *
 * 中分類マスタ設定
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
App::uses('WEX040Model', 'Model');

class WEX040Controller extends AppController {

	public $name = 'WEX040';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WEX']['040']['kino_nm']));
		
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

		$this->WEX040Model = new WEX040Model($this->Session->read('select_ninusi_cd'),
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
							"initPopUp('".Router::url('/WEX040/popup', false)."');" .
							"initAddRow('".Router::url('/WEX040/popup_insert', false)."');" .
							"initUpdate('".Router::url('/WEX040/dataUpdate', false)."');" . 
							"initBunrui();");
		
		$bnri_dai_cd = '';

		$kotei = '';
		$bnri_cyu_cd = '';
		$bnri_cyu_nm = '';
		$bnri_cyu_exp = '';

		$pageID = 1;
		$return_cd = '';
		$display = '';
		$message = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['bnri_cyu_cd'])){
			$kotei = $this->request->query['kotei'];
			$bnri_cyu_cd = $this->request->query['bnri_cyu_cd'];
			$bnri_cyu_nm = $this->request->query['bnri_cyu_nm'];
			$bnri_cyu_exp = $this->request->query['bnri_cyu_exp'];
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
			
			$this->Session->write('displayWEX040','true');
			
			$this->redirect('/WEX040/index?return_cd=' . $return_cd . 
										  '&message=' . $message .
										  '&kotei=' . $kotei . 
										  '&bnri_cyu_cd=' . $bnri_cyu_cd . 
										  '&bnri_cyu_nm=' . $bnri_cyu_nm . 
										  '&bnri_cyu_exp=' . $bnri_cyu_exp. 
										  '&pageID=' . $pageID .
										  '&display=false'
										  );
			
			return;
		} else if ($this->Session->check('displayWEX040') && $display == "false") {
			
			$this->Session->delete('displayWEX040');
			
		} else if (!$this->Session->check('displayWEX040') && $display == "false") {
			
			$this->redirect('/WEX040/index?&kotei=' . $kotei . 
										  '&bnri_cyu_cd=' . $bnri_cyu_cd . 
										  '&bnri_cyu_nm=' . $bnri_cyu_nm . 
										  '&bnri_cyu_exp=' . $bnri_cyu_exp. 
										  '&pageID=' . $pageID
										  );
			return;
		}

		$bnri_dai_cd = substr($kotei, 0,3);

		//中分類マスタ一覧
		$lsts = $this->WEX040Model->getMBnriCyuLst($bnri_dai_cd, $bnri_cyu_cd, $bnri_cyu_nm, $bnri_cyu_exp);

		//ページャー
		$pageNum = 50;
		
		$options = array(
				"totalItems" => count($lsts),
				"delta" => 10,
				"perPage" => $pageNum,
				"httpMethod" => "GET",
				"path" => "/DCMS/WEX040/index"
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

		//分類コンボ設定
	    
	    //大に分ける
	    $opsions = $this->MBunrui->getBunruiDaiPDO();
	    $this->set("koteiDaiList",$opsions);

		//売上区分
		$kbnUriages = $this->MMeisyo->getMeisyo("11");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnUriages', $kbnUriages);

		//単位区分
		$kbnTanis = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.tani'));
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnTanis', $kbnTanis);

		//Viewへセット
		$this->set('index',$index);

			
		//エラーの場合
		if ($this->Session->check('saveTableWEX040')) {
		
			//データを取得
			$getArray = $this->Session->read('saveTableWEX040');
			
			$data = $getArray->data;
			$timestamp = $getArray->timestamp;
			
			$this->set("timestamp", $timestamp);
			$this->set('lsts',$data);
			$this->render("index_error");
			
			$this->Session->delete('saveTableWEX040');
		
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
		$this->Session->write('saveTableWEX040',$getArray);

		$data = $getArray->data;
		$timestamp = $getArray->timestamp;
		
		//タイムスタンプチェック
		if (!$this->WEX040Model->checkTimestamp($timestamp)) {
			
			$array = $this->WEX040Model->errors;
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
//		if (!$this->WEX040Model->checkCYUMst($data)) {
		$abc = $this->WEX040Model->checkCyuMst($data);
			
		$array = $this->WEX040Model->errors;

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
		if (!$this->WEX040Model->setCyuMst($data, $this->Session->read('staff_cd'))) {
		    
		    
		    $array = $this->WEX040Model->errors;
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
		$this->Session->delete('saveTableWEX040');


		
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
		//売上区分
		$kbnUriages = $this->MMeisyo->getMeisyo("11");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnUriages', $kbnUriages);

//		//大分類コード
//		$bnriDaiCd = $this->WEX040Model->getMBnriDaiCd();
//		// 一覧情報を一時的にセッションに保持
//		$this->set('bnriDaiCd', $bnriDaiCd);

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
		//売上区分
		$kbnUriages = $this->MMeisyo->getMeisyo("11");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnUriages', $kbnUriages);

		//分類コンボ設定
	    
	    //大に分ける
	    $opsions = $this->MBunrui->getBunruiDaiPDO();
	    $this->set("koteiDaiList",$opsions);

	}	


}
