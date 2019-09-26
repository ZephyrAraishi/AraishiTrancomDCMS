<?php
/**
 * WPO023
 *
 * 緊急ステータス変更画面
 *
 * @category      WPO
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');
App::uses('WPO023Model', 'Model');
App::uses('MMeisyo', 'Model');

class WPO023Controller extends AppController {

	public $name = 'WPO023';
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
		
		// システム名
		$this->set('system', Sanitize::stripAll($this->Session->read('system_name')));
		
		// サブシステム名		
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['WPO']));
		
		// 機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WPO']['023']['kino_nm']));
		
		//メッセージマスタ設定
		$this->MMessage = new MMessage($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'), $this->name);
		
		//共通関数モデル
		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'), $this->name);
		
		//データベース
		$this->WPO023Model = new WPO023Model($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'), $this->name);
		
		//名称マスタ
		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);
		
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
		$this->set("onload","initReload();");
		
		//検索条件、ページID取得
		$batch_nm_cd = '';
		$s_batch_no = '';
		$s_kanri_no = '';
		$zone = '';
		$staff_nm = '';
		$kbn_status = '';
		$pageID = 1;
		$return_cd = '';
		$display = '';
		$message = '';

		//検索条件
		if (isset($this->request->query['batch_nm_cd'])){
			$batch_nm_cd = $this->request->query['batch_nm_cd'];
			$s_batch_no = $this->request->query['s_batch_no'];
			$s_kanri_no = $this->request->query['s_kanri_no'];
			$zone = $this->request->query['zone'];
			$staff_nm = $this->request->query['staff_nm'];
			$kbn_status = $this->request->query['kbn_status'];
		}
		
		//ページID
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
		
		//メッセージディスプレイフラグ
		if(isset($this->request->query['display'])) {
				
			$display = $this->request->query['display'];
		}
		
		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {
			
			$this->Session->write('displayWPO023','true');
			
			$this->redirect('/WPO023/index?return_cd=' . $return_cd . 
										  '&message=' . $message .
										  '&batch_nm_cd=' . $batch_nm_cd . 
										  '&s_batch_no=' . $s_batch_no . 
										  '&s_kanri_no=' . $s_kanri_no . 
										  '&zone=' . $zone . 
										  '&staff_nm=' . $staff_nm . 
										  '&kbn_status=' . $kbn_status . 
										  '&pageID=' . $pageID .
										  '&display=false'
										  );
			
			return;
		} else if ($this->Session->check('displayWPO023') && $display == "false") {
			
			$this->Session->delete('displayWPO023');
			
		} else if (!$this->Session->check('displayWPO023') && $display == "false") {
			
			$this->redirect('/WPO023/index?batch_nm_cd=' . $batch_nm_cd . 
										  '&s_batch_no=' . $s_batch_no . 
										  '&s_kanri_no=' . $s_kanri_no . 
										  '&zone=' . $zone . 
										  '&staff_nm=' . $staff_nm . 
										  '&kbn_status=' . $kbn_status . 
										  '&pageID=' . $pageID
										  );
			return;
		}
		
		//バッチ名コンボ設定
		$batchs = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.bach'));
		$this->set('batchs',$batchs);
		
		//ゾーンコンボ設定
		$zones = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.zone'),
											null,
											array(MMeisyo::mei_1),
											array(MMeisyo::val_free_num_1));
		$this->set('zones',$zones);
		
		//ステータスコンボ設定
		$status = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.sts'),
				                            null,
				                            array(MMeisyo::mei_1),
				                            array(MMeisyo::val_free_num_1));
		$this->set('status',$status);
		
		//データ取得
		$lsts = $this->WPO023Model->getSKanriLst($batch_nm_cd, $s_batch_no, $s_kanri_no, $zone, $staff_nm, $kbn_status);
		
		//データをソート
		$STATUS_SEQ = array();
		$PRIORITY =  array();
		$S_BATCH_NO_CD =  array();
		$S_KANRI_NO =  array();
		$INDEX =  array();
		
		foreach ($lsts as $key => $row) {
		    $STATUS_SEQ[$key]  = $row['STATUS_SEQ'];
		    $PRIORITY[$key]  = $row['PRIORITY'];
		    $S_BATCH_NO_CD[$key]  = $row['S_BATCH_NO_CD'];
		    $S_KANRI_NO[$key]  = $row['S_KANRI_NO'];
		    $INDEX[$key]  = $key;
		}
		
		array_multisort($STATUS_SEQ,SORT_ASC,$PRIORITY,SORT_ASC,$S_BATCH_NO_CD,SORT_ASC,$S_KANRI_NO,SORT_ASC,$INDEX,SORT_ASC,$lsts);

		//ページャー
		$options = array(
		  "totalItems" => count($lsts),
		  "delta" => 10,
		  "perPage" => 100,
		  "httpMethod" => "GET",
		  "path" => "/DCMS/WPO023/index"
		);

		$pager = @Pager::factory($options);
		
		//ナビゲーションを設定
		$navi = $pager -> getLinks();
		$this->set('navi',$navi["all"]);
		
		//項目のインデックスを取得
		$index = ($pageID - 1) * 100;
		$arrayList = array();
		
		$limit = $index + 100;
		
		if ($limit > count($lsts)) {
			
			$limit = count($lsts);
		}
		
		//インデックスから表示項目を取得
		for($i = $index; $i < $limit ; $i++){
		  
		  	$arrayList[] = $lsts[$i];
		}
		
		$this->set('index',$index);
		$this->set('lsts',$arrayList);

	}
	
	/**
	 * 初期表示
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function update(){
	
		//ライブラリ読み込み
		require_once 'Pager.php';
	
		//キャッシュクリア
		Cache::clear();
		
		//Javascript設定
		$this->set("onload","initReload();" . 
							"initPopUp('".Router::url('/WPO023/popup', false)."');" .
							"initUpdate('".Router::url('/WPO023/dataUpdate', false)."');");
		
		//検索条件、ページID取得
		$batch_nm_cd = '';
		$s_batch_no = '';
		$s_kanri_no = '';
		$zone = '';
		$staff_nm = '';
		$kbn_status = '';
		$pageID = 1;
		$return_cd = '';
		$display = '';
		$message = '';

		//検索条件
		if (isset($this->request->query['batch_nm_cd'])){
			$batch_nm_cd = $this->request->query['batch_nm_cd'];
			$s_batch_no = $this->request->query['s_batch_no'];
			$s_kanri_no = $this->request->query['s_kanri_no'];
			$zone = $this->request->query['zone'];
			$staff_nm = $this->request->query['staff_nm'];
			$kbn_status = $this->request->query['kbn_status'];
		}
		
		//ページID
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
		
		//メッセージディスプレイフラグ
		if(isset($this->request->query['display'])) {
				
			$display = $this->request->query['display'];
		}
		
		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {
			
			$this->Session->write('displayWPO023','true');
			
			$this->redirect('/WPO023/index?return_cd=' . $return_cd . 
										  '&message=' . $message .
										  '&batch_nm_cd=' . $batch_nm_cd . 
										  '&s_batch_no=' . $s_batch_no . 
										  '&s_kanri_no=' . $s_kanri_no . 
										  '&zone=' . $zone . 
										  '&staff_nm=' . $staff_nm . 
										  '&kbn_status=' . $kbn_status . 
										  '&pageID=' . $pageID .
										  '&display=false'
										  );
			
			return;
		} else if ($this->Session->check('displayWPO023') && $display == "false") {
			
			$this->Session->delete('displayWPO023');
			
		} else if (!$this->Session->check('displayWPO023') && $display == "false") {
			
			$this->redirect('/WPO023/index?batch_nm_cd=' . $batch_nm_cd . 
										  '&s_batch_no=' . $s_batch_no . 
										  '&s_kanri_no=' . $s_kanri_no . 
										  '&zone=' . $zone . 
										  '&staff_nm=' . $staff_nm . 
										  '&kbn_status=' . $kbn_status . 
										  '&pageID=' . $pageID
										  );
			return;
		}
		
		//バッチ名コンボ設定
		$batchs = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.bach'));
		$this->set('batchs',$batchs);
		
		//ゾーンコンボ設定
		$zones = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.zone'),
											null,
											array(MMeisyo::mei_1),
											array(MMeisyo::val_free_num_1));
		$this->set('zones',$zones);
		
		//ステータスコンボ設定
		$status = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.sts'),
				                            null,
				                            array(MMeisyo::mei_1),
				                            array(MMeisyo::val_free_num_1));
		$this->set('status',$status);
		
		//データ取得
		$lsts = $this->WPO023Model->getSKanriLst($batch_nm_cd, $s_batch_no, $s_kanri_no, $zone, $staff_nm, $kbn_status);
		
		//データをソート
		$STATUS_SEQ = array();
		$PRIORITY =  array();
		$S_BATCH_NO_CD =  array();
		$S_KANRI_NO =  array();
		$INDEX =  array();
		
		foreach ($lsts as $key => $row) {
		    $STATUS_SEQ[$key]  = $row['STATUS_SEQ'];
		    $PRIORITY[$key]  = $row['PRIORITY'];
		    $S_BATCH_NO_CD[$key]  = $row['S_BATCH_NO_CD'];
		    $S_KANRI_NO[$key]  = $row['S_KANRI_NO'];
		    $INDEX[$key]  = $key;
		}
		
		array_multisort($STATUS_SEQ,SORT_ASC,$PRIORITY,SORT_ASC,$S_BATCH_NO_CD,SORT_ASC,$S_KANRI_NO,SORT_ASC,$INDEX,SORT_ASC,$lsts);

		//ページャー
		$options = array(
		  "totalItems" => count($lsts),
		  "delta" => 10,
		  "perPage" => 100,
		  "httpMethod" => "GET",
		  "path" => "/DCMS/WPO023/index"
		);

		$pager = @Pager::factory($options);
		
		//ナビゲーションを設定
		$navi = $pager -> getLinks();
		$this->set('navi',$navi["all"]);
		
		//項目のインデックスを取得
		$index = ($pageID - 1) * 100;
		$arrayList = array();
		
		$limit = $index + 100;
		
		if ($limit > count($lsts)) {
			
			$limit = count($lsts);
		}
		
		//インデックスから表示項目を取得
		for($i = $index; $i < $limit ; $i++){
		  
		  	$arrayList[] = $lsts[$i];
		}
		
		//データをセット
		$this->set('index',$index);
		$this->set('lsts',$arrayList);
		
	}

	/**
	 * ステータス変更処理
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function dataUpdate() {
		
		//データのやり取りだけなのでレンダリングなし
		$this->autoLayout = false;
		$this->autoRender = false;
		
		//ポストデータの存在確認エラー
		if(!$this->request->is('post')) {
			
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000104'));
			return;
		}
		
		//データを取得
		$data = json_decode($this->request->input());
		
		//データがない場合
		if(count($data) == 0) {
			
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000101'));
			return;
		}
		
		//TPM040との排他確認
		if (!$this->WPO023Model->checkTPM040($this->Session->read('staff_cd'))) {
		
			//エラー吐き出し
			$array = $this->WPO023Model->errors;
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
		if (!$this->WPO023Model->setSTATUS($this->Session->read('staff_cd'),$data)) {
		
			//エラー吐き出し
			$array = $this->WPO023Model->errors;
		    $message = '';
		    
		    foreach ( $array as $key=>$val ) {
		    
		    	foreach ($val as $key2=>$val2) {
			    
			    	$message = $message . $val2 . "<br>";
		    	}
		    }
		    
		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}

		//更新終了
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
		
		//ステータス（状態区分 ALL）取得
		$stsall = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.sts'),
				                            array('01','02','09','11','19'),
				                            array(MMeisyo::mei_1),
				                            array(MMeisyo::val_free_num_1));
		
		//Viewにセット
		$this->set('stsall',$stsall);
	}	
}
