<?php
/**
 * TPM030
 *
 * 詳細進捗画面
 *
 * @category      TPM
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');
App::uses('TPM030Model', 'Model');

class TPM030Controller extends AppController {

	public $name = 'TPM030';
	public $layout = 'DCMS';

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
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['TPM']));
		//機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['TPM']['030']['kino_nm']));

		//メッセージマスタ設定
		$this->MMessage = new MMessage($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//共通関数モデル
		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//TPM030モデル
		$this->TPM030Model = new TPM030Model($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

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

	    //詳細進捗データ取得
		$lsts = $this->TPM030Model->getDisplayList();

		$this->set("lsts",$lsts);

	}

}
