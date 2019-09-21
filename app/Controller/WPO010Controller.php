<?php

/**
 * WPO010
 *
 * バッチ編成取込画面
 *
 * @category      WPO
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('MMessage', 'Model');
App::uses('MMeisyo', 'Model');
App::uses('MCommon', 'Model');
App::uses('WPO010Model', 'Model');

class WPO010Controller extends AppController {
	
	public $name = 'WPO010';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WPO']['010']['kino_nm']));
		
		//メッセージマスタ設定
		$this->MMessage = new MMessage($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);
		
		//共通関数モデル
		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);
		
		//名称マスタ設定
		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);
		
		//WPO010モデル
		$this->WPO010Model = new WPO010Model($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);
		
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
		$this->set("onload","initReload();" . 
							"initOracleload('".Router::url('/WPO010/getOracleData', false)."');" . 
							"initPopUp('".Router::url('/WPO010/popup', false)."');" . 
							"initUpdate('".Router::url('/WPO010/dataUpdate', false)."');");
		
		$return_cd = '';
		$display = '';
		$message = '';
		
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
			
			$this->Session->write('displayWPO010','true');
			
			$this->redirect('/WPO010/index?return_cd=' . $return_cd . 
										  '&message=' . $message .
										  '&display=false'
										  );
			
			return;
		} else if ($this->Session->check('displayWPO010') && $display == "false") {
			
			$this->Session->delete('displayWPO010');
			
		} else if (!$this->Session->check('displayWPO010') && $display == "false") {
			
			$this->redirect('/WPO010/index');
			return;
		}



		if ($this->Session->check('batchNmLst')) {
			//【取込処理実行時】取込時のデータが存在する場合は表示
			
			$this->set("batchNmLst", $this->Session->read('batchNmLst'));
			$this->Session->delete('batchNmLst');
			
		
		} elseif ($this->Session->check('saveTableWPO010')) {
			//【取込処理実行後】前回のテーブルがある場合は表示
		
			//データを取得
			$data = $this->Session->read('saveTableWPO010');
		
			//データを設置
			$arrayList = array();
			
			foreach ($data as $array) {
			
				$tempArray  = array(
	               'LINE_COUNT' => $array->LINE_COUNT,
	               'YMD_SYORI' => $array->YMD_SYORI,
	               'S_BATCH_NO' => $array->S_BATCH_NO,
	               'PICKING_NUM' => $array->PICKING_NUM,
	               'TOTAL_PIECE_NUM' => $array->TOTAL_PIECE_NUM,
	               'BATCH_NM' => $array->BATCH_NM,
	               'BATCH_NM_CD' => $array->BATCH_NM_CD,
	               'CHANGE' => $array->CHANGE,
	               'S_BATCH_NO_CD' => $array->S_BATCH_NO_CD,
	               'UPD_CNT_CD' => $array->UPD_CNT_CD,
	               'YMD_UNYO' => $array->YMD_UNYO,
				);
				 
				 $arrayList[] = $tempArray;
			}

			$this->set("batchNmLst", $arrayList);
			
			$this->Session->delete('saveTableWPO010');
			
		} 
		
	}
	
	/**
	 * Oracleデータ取り込み処理
	 * @access   public
	 * @param    void
	 * @return   Boolean
	 */
	public function getOracleData() {

		//データのやり取りだけなのでレンダリングなし
		$this->autoLayout = false;
		$this->autoRender = false;
		
		
		//Oracle(WMS)からデータを取得　getMySQLPickingData
		if (!$this->WPO010Model->getOraclePickingData()) {
		//if (!$this->WPO010Model->getMySQLPickingData()) {
					
			//エラー吐き出し
			$array = $this->WPO010Model->errors;
		    $message = '';
		    
		    foreach ( $array as $key=>$val ) {
		    
		    	foreach ($val as $key2=>$val2) {
			    
			    	$message = $message . $val2 . "<br>";
		    	}
		    }
		    
		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}
		
		$this->Session->write('batchAllLst', $this->WPO010Model->allData);
		$this->Session->write('batchNmLst', $this->WPO010Model->batchNmLst);
		
		
		//更新終了
		echo "return_cd=" . "0". "&message=";
		
	}
	
	/**
	 * バッチデータ取り込み処理
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
			
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('WPOE010104'));
			return;
		}
		
		//データを取得
		$data = json_decode($this->request->input());
		
		//データがない場合
		if (empty($data)) {
			
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('WPOE010101'));
			return;
		}
		
		// ピッキングデータ登録
		if (!$this->WPO010Model->setPickData($this->Session->read('staff_cd'),
				                             $this->Session->read('batchAllLst'),
				                             $data)) {
	
			$this->Session->write('saveTableWPO010',$data);
		    
		    
		    $array = $this->WPO010Model->errors;
		    $message = '';
		    
		    foreach ( $array as $key=>$val ) {
		    
		    	foreach ($val as $key2=>$val2) {
			    
			    	$message = $message . $val2 . "<br>";	
		    	}
		    }
		    
		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}
		
		//取り込み成功
		$array = $this->WPO010Model->infos;
	    $message = '';
	    
	    foreach ( $array as $key=>$val ) {
	    
	    	foreach ($val as $key2=>$val2) {
		    
		    	$message = $message . $val2 . "<br>";	
	    	}
	    }
		
		echo "return_cd=" . "0" . "&message=" .  base64_encode ($message);
		
		$this->Session->delete('batchAllLst');
		$this->Session->delete('saveTableWPO010');

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
		
		// 名称マスタデータ取得
		$data = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.bach'));
		$this->set("batNmLst",$data);
	}

}
