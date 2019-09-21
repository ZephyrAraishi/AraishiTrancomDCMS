<?php
/**
 * WEX010
 *
 * 荷主基本情報画面
 *
 * @category      WEX
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');
App::uses('WEX010Model', 'Model');

class WEX010Controller extends AppController {

	public $name = 'WEX010';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WEX']['010']['kino_nm']));
		
		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
		                             $this->Session->read('select_sosiki_cd'),
		                             $this->name);

		/* メッセージマスタ呼び出し */
		$this->MMessage = new MMessage($this->Session->read('select_ninusi_cd'),
		                               $this->Session->read('select_sosiki_cd'),
		                               $this->name);
		                            
		                            
	    $this->WEX010Model = new WEX010Model($this->Session->read('select_ninusi_cd'),
										     $this->Session->read('select_sosiki_cd'),
									         $this->name);

	}

	/**
	 * 初期表示
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function index(){

		//Javascript設定
		$this->set("onload","initReload();".
							"initUpdate('".Router::url('/WEX010/dataUpdate', false)."');");

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

		//エラーコード
		if(isset($this->request->query['display'])) {
				
			$display = $this->request->query['display'];
		}


		
		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {
			
			$this->Session->write('displayWEX010','true');
			
			$this->redirect('/WEX010/index?return_cd=' . $return_cd . 
										  '&message=' . $message .
										  '&display=false'
										  );
			
			return;
		} else if ($this->Session->check('displayWEX010') && $display == "false") {
			
			$this->Session->delete('displayWEX010');
			
		} else if (!$this->Session->check('displayWEX010') && $display == "false") {
			
			$this->redirect('/WEX010/index');
			return;
		}


		//前回の処理でエラーが発生した場合、前回のデータをそのまま表示
		if ($this->Session->check('saveTableWEX010')) {
		
			//前回のデータを取得
			$data = $this->Session->read('saveTableWEX010');
			
			$this->set('data',$data);
			
			//データ構造が違うのでレンダリングを変更
			$this->render("index_error");
			
			//前回のデータを削除
			$this->Session->delete('saveTableWEX010');
		
		//エラーが発生していない場合は、検索条件を元に表示
		} else {
		
			//データ取得
			$data = $this->WEX010Model->getNinusiInfo();
			
			//サニタイズを戻す
			$escapedData = array();
			
			foreach ($data as  $key => $array) {
			
				foreach($array as $key2 => $value) {
					
					$escapedData[$key][$key2] = htmlspecialchars_decode ($value,ENT_QUOTES);
				}
			}
			
			$this->data = $escapedData;
		}

	}

	/**
	 * 更新
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function dataUpdate(){

		
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


		$data = array();
		$data['M_NINUSI_SOSIKI']['NINUSI_NM']       = $getArray->ninusi_nm;
		$data['M_NINUSI_SOSIKI']['NINUSI_RYAKU']    = $getArray->ninusi_ryaku;
		$data['M_NINUSI_SOSIKI']['NINUSI_POST_NO']  = $getArray->ninusi_post_no;
		$data['M_NINUSI_SOSIKI']['NINUSI_ADDR_1']   = $getArray->ninusi_addr_1;
		$data['M_NINUSI_SOSIKI']['NINUSI_ADDR_2']   = $getArray->ninusi_addr_2;
		$data['M_NINUSI_SOSIKI']['NINUSI_ADDR_3']   = $getArray->ninusi_addr_3;
		$data['M_NINUSI_SOSIKI']['NINUSI_TEL']      = $getArray->ninusi_tel;
		$data['M_NINUSI_SOSIKI']['NINUSI_FAX']      = $getArray->ninusi_fax;
		$data['M_NINUSI_SOSIKI']['NINUSI_DAI_NM']   = $getArray->ninusi_dai_nm;
		$data['M_NINUSI_SOSIKI']['NINUSI_EST_YM']   = $getArray->ninusi_est_ym;
		$data['M_NINUSI_SOSIKI']['NINUSI_SIHON']    = $getArray->ninusi_sihon;
		$data['M_NINUSI_SOSIKI']['NINUSI_JIGYO']    = $getArray->ninusi_jigyo;
		$data['M_NINUSI_SOSIKI']['SOSIKI_NM']       = $getArray->sosiki_nm;
		$data['M_NINUSI_SOSIKI']['SOSIKI_RYAKU']    = $getArray->sosiki_ryaku;
		$data['M_NINUSI_SOSIKI']['SOSIKI_POST_NO']  = $getArray->sosiki_post_no;
		$data['M_NINUSI_SOSIKI']['SOSIKI_ADDR_1']   = $getArray->sosiki_addr_1;
		$data['M_NINUSI_SOSIKI']['SOSIKI_ADDR_2']   = $getArray->sosiki_addr_2;
		$data['M_NINUSI_SOSIKI']['SOSIKI_ADDR_3']   = $getArray->sosiki_addr_3;
		$data['M_NINUSI_SOSIKI']['SOSIKI_TEL']      = $getArray->sosiki_tel;
		$data['M_NINUSI_SOSIKI']['SOSIKI_FAX']      = $getArray->sosiki_fax;
		$data['M_NINUSI_SOSIKI']['SOSIKI_DAI_NM']   = $getArray->sosiki_dai_nm;
		$data['M_NINUSI_SOSIKI']['SEIKYU_BUSYO_NM'] = $getArray->seikyu_busyo_nm;
		$data['M_NINUSI_SOSIKI']['SEIKYU_POST_NO']  = $getArray->seikyu_post_no;
		$data['M_NINUSI_SOSIKI']['SEIKYU_ADDR_1']   = $getArray->seikyu_addr_1;
		$data['M_NINUSI_SOSIKI']['SEIKYU_ADDR_2']   = $getArray->seikyu_addr_2;
		$data['M_NINUSI_SOSIKI']['SEIKYU_ADDR_3']   = $getArray->seikyu_addr_3;
		$data['M_NINUSI_SOSIKI']['SEIKYU_TEL']      = $getArray->seikyu_tel;
		$data['M_NINUSI_SOSIKI']['SEIKYU_FAX']      = $getArray->seikyu_fax;
		$data['M_NINUSI_SOSIKI']['SEIKYU_DAI_NM']   = $getArray->seikyu_dai_nm;


		//全データを一時保存
		$this->Session->write('saveTableWEX010',$data);
		
		
		//更新前チェック
		if(!$this->WEX010Model->checkBeforeUpdate($data)){

		    $array = $this->WEX010Model->errors;
		    $message = '';
		    
		    foreach ( $array as $key=>$val ) {
		    
		    	foreach ($val as $key2=>$val2) {
			    
			    	$message = $message . $val2 . "<br>";	
		    	}
		    }
		    

		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message) ;
		    return;


		}
		
				//更新前チェック
		if(!$this->WEX010Model->update($data)){

		    $array = $this->WEX010Model->errors;
		    $message = '';
		    
		    foreach ( $array as $key=>$val ) {
		    
		    	foreach ($val as $key2=>$val2) {
			    
			    	$message = $message . $val2 . "<br>";	
		    	}
		    }
		    

		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message) ;
		    return;


		}
		
		
		//成功時は前回のテーブルデータを消す。
		$this->Session->delete('saveTableWEX010');

		echo "return_cd=" . "0" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNI000001'));

	}

}
