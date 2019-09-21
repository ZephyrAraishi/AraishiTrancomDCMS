<?php
/**
 * WEX020
 *
 * 荷主契約情報画面
 *
 * @category      WEX
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMeisyo', 'Model');
App::uses('WEX020Model', 'Model');
App::uses('MCommon', 'Model');
App::uses('MMessage', 'Model');
App::uses('MBunrui', 'Model');

class WEX020Controller extends AppController {

	public $name = 'WEX020';
	public $layout = "DCMS";
	public $uses = array();

	/**
	 * コントロール起動時処理
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WEX']['020']['kino_nm']));

		// 名称マスタ取得
		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),
									 $this->Session->read('select_sosiki_cd'),
									 $this->name);

		//分類マスタモデル
		$this->MBunrui = new MBunrui($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//WEX020モデル
		$this->WEX020Model = new WEX020Model($this->Session->read('select_ninusi_cd'),
											 $this->Session->read('select_sosiki_cd'),
											 $this->name);
											 
		//共通関数モデル
		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
									 $this->Session->read('select_sosiki_cd'),
									 $this->name);
									 
		/* メッセージマスタ呼び出し */
		$this->MMessage = new MMessage($this->Session->read('select_ninusi_cd'),
		                               $this->Session->read('select_sosiki_cd'),
		                               $this->name);

	}

	/**
	 * 初期表示
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function index(){
	
		//キャッシュクリア
		Cache::clear();
		
		//Javascript設定
		$this->set("onload","initReload();" . 
							"initPopUp('".Router::url('/WEX020/popup', false)."');" . 
							"initAddRow('".Router::url('/WEX020/popup_insert', false)."');" .
							"initUpdate('".Router::url('/WEX020/dataUpdate', false)."');");
		
		$return_cd = '';
		$display = '';
		$message = '';
		$kbn_keiyaku = '';
		$seikyu_sime = '';
		$siharai_sight = '';
		$pre_kbn_keiyaku = '';
		$pre_seikyu_sime = '';
		$pre_siharai_sight = '';
		
		//リターンCD
		if(isset($this->request->query['return_cd'])) {
				
			$return_cd = $this->request->query['return_cd'];
		}
		
		//メッセージ
		if(isset($this->request->query['message'])) {
				
			$message = $this->MCommon->escapePHP($this->request->query['message']);
		}
		
		// ヘッダ部入力値
		if (isset($this->request->query['kbn_keiyaku'])) {
			$kbn_keiyaku = $this->request->query['kbn_keiyaku'];
			$seikyu_sime = $this->request->query['seikyu_sime'];
			$siharai_sight = $this->request->query['siharai_sight'];
		}
		if (isset($this->request->query['pre_kbn_keiyaku'])) {
			$pre_kbn_keiyaku = $this->request->query['pre_kbn_keiyaku'];
			$pre_seikyu_sime = $this->request->query['pre_seikyu_sime'];
			$pre_siharai_sight = $this->request->query['pre_siharai_sight'];
		}
		
		//メッセージディスプレイフラグ
		if(isset($this->request->query['display'])) {
				
			$display = $this->request->query['display'];
		}
		
		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {
			
			$this->Session->write('displayWEX020','true');
			
			$this->redirect('/WEX020/index?return_cd=' . $return_cd . 
										  '&message=' . $message .
										  '&kbn_keiyaku=' . $kbn_keiyaku .
										  '&seikyu_sime=' . $seikyu_sime .
										  '&siharai_sight=' . $siharai_sight .
										  '&pre_kbn_keiyaku=' . $pre_kbn_keiyaku .
										  '&pre_seikyu_sime=' . $pre_seikyu_sime .
										  '&pre_siharai_sight=' . $pre_siharai_sight .
										  '&display=false'
										  );
			
			return;
		} else if ($this->Session->check('displayWEX020') && $display == "false") {
			
			$this->Session->delete('displayWEX020');
			
		} else if (!$this->Session->check('displayWEX020') && $display == "false") {
			
			$this->redirect('/WEX020/index');
			return;
		}
		
		$keiyakus = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.keiyaku'));
		$this->set('keiyakus', $keiyakus);
		
		//単位区分
		$kbnTanis = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.tani'));
		$this->set('kbnTanis', $kbnTanis);
		

		//エラーの場合
		if ($this->Session->check('saveTableWEX020')) {
			//データを取得
			$sesData = $this->Session->read('saveTableWEX020');

			$getArray = $sesData->data;
			$timestamp = $sesData->timestamp;
			
			
			$this->set('keiyakuList', $getArray);
			$this->set("timestamp", $timestamp);

			$this->Session->delete('saveTableWEX020');
			//単位区分
			$kbnTanis = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.tani'));
			// 一覧情報を一時的にセッションに保持
			$this->set('kbnTanis', $kbnTanis);

			$this->set('kbn_keiyaku', $kbn_keiyaku);
			$this->set('seikyu_sime', $seikyu_sime);
			$this->set('siharai_sight', $siharai_sight);
			$this->set('pre_kbn_keiyaku', $pre_kbn_keiyaku);
			$this->set('pre_seikyu_sime', $pre_seikyu_sime);
			$this->set('pre_siharai_sight', $pre_siharai_sight);

			//Viewへセット
			$this->render("index_error");

		} else {
		
			$ret = $this->WEX020Model->getNKeiyakuLst($this->data);
			$this->set("keiyakuList", $ret);
			$this->set("timestamp", $this->WEX020Model->getTimestamp());
	
			if(count($ret) > 0){
				$kbn_keiyaku = $ret['0']['KBN_KEIYAKU'];
				$seikyu_sime = $ret['0']['SEIKYU_SIME'];
				$siharai_sight = $ret['0']['SIHARAI_SIGHT'];
				$pre_kbn_keiyaku = $kbn_keiyaku;
				$pre_seikyu_sime = $seikyu_sime;
				$pre_siharai_sight = $siharai_sight;
			}
			$this->set('kbn_keiyaku', $kbn_keiyaku);
			$this->set('seikyu_sime', $seikyu_sime);
			$this->set('siharai_sight', $siharai_sight);
			$this->set('pre_kbn_keiyaku', $pre_kbn_keiyaku);
			$this->set('pre_seikyu_sime', $pre_seikyu_sime);
			$this->set('pre_siharai_sight', $pre_siharai_sight);
		}
	}

	/**
	 * 更新処理
	 */
	public function dataUpdate() {

		$this->autoLayout = false;
		$this->autoRender = false;

		if(!$this->request->is('post')) {

			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('WEX020004'));
			return;
		}

		App::uses('WEX020Model', 'Model');
		$this->WEX020Model = new WEX020Model($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'), $this->name);

		//データを取得
		$jsonData = json_decode($this->request->input());
		
		$data = $jsonData->data;
		$timestamp = $jsonData->timestamp;

		if(count($data) == 0) {
			echo "return_cd=" . "90" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('WEXE020001'));
			return;
		}

		//データを一時保存
		$this->Session->write('saveTableWEX020',$jsonData);

		//入力チェック
		if(!$this->WEX020Model->checkBeforeUpdate($jsonData)){

			$array = $this->WEX020Model->errors;

		    $message = '';
		    foreach ( $array as $key=>$val ) {
		    	foreach ($val as $key2=>$val2) {
			    	$message = $message . $val2 . "<br>";
		    	}
		    }

		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);

		    return;
		}
		
		// タイムスタンプチェック
		if (!$this->WEX020Model->checkTimestamp($timestamp)) {
			$errArray = $this->WEX020Model->errors;
			$message = '';
			foreach ($errArray as $key => $val) {
				foreach ($val as $key2 => $val2) {
					$message = $message . $val2 . "<br>";
				}
			}
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
			return;
		}

		//更新処理
		if(!$this->WEX020Model->dataUpdate($jsonData)){
		
			$array = $this->WEX020Model->errors;
			
		    $message = '';
		    foreach ( $array as $key=>$val ) {
		    	foreach ($val as $key2=>$val2) {
			    	$message = $message . $val2 . "<br>";
		    	}
		    }
		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}

		
		
		//成功なのでテーブルをセッションから消す
		$this->Session->delete('saveTableWEX020');
		
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
				

		//単位区分
		$kbnTanis = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.tani'));
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnTanis', $kbnTanis);


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

	}	

}
