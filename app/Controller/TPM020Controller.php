<?php
/**
 * TPM020
 *
 * ログイン画面
 *
 * @category      TPM
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */
 
App::uses('MMessage', 'Model');
App::uses('TPM020Model', 'Model');

class TPM020Controller extends AppController {
	
	public $name = 'TPM020';
	public $layout = "DCMS";
	public $uses = array();
	
	/**
	 * 全体の進捗情報取得
	 * @access   public
	 * @return   進捗情報
	 */
	public function beforeFilter() {
		
		parent::beforeFilter();
		

		/* ビューへ設定 */
		// システム名
		$this->set('system', Sanitize::stripAll($this->Session->read('system_name')));
		// サブシステム名
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['TPM']));
		// 機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['TPM']['020']['kino_nm']));
		
		//メッセージマスタ設定
		$this->MMessage = new MMessage($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);
		
		//TPM020モデル
		$this->TPM020Model = new TPM020Model($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'), $this->name);
			
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
		$this->set("onload","initReload();");
		
		//時間別進捗一覧
		$t_tps = $this->TPM020Model->getTPickSKanri_time_tp();
		
		// 一覧情報を一時的にセッションに保持
		$this->set('t_tps',$t_tps);
		
		//ゾーン別進捗一覧
		$zones = $this->TPM020Model->getTPickSKanri_Zone($this->Session->read('staff_cd'));
		
		// 一覧情報を一時的にセッションに保持
		$this->set('zones',$zones);
		
		//棚欠発生一覧
		$t_ketus = $this->TPM020Model->getTPickItem_TKetu();
		
		// 一覧情報を一時的にセッションに保持
		$this->set('t_ketus',$t_ketus);
		
		
	}


}
