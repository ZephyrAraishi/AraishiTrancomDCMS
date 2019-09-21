<?php
/**
 * ERR020
 *
 * セッションエラー画面
 *
 * @category      ERR
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */

class ERR020Controller extends AppController {
	
	public $name = 'ERR020';
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
	
		// ログアウトを表示しない
		$this->set('nonDispLogOut', true);
		$this->set('nonDispMenu', true);
		$this->set('nonSysLink', true);
		
		//サブシステム名
		$this->set('system', "エラー");
		//機能名
		$this->set('kino', "セッションエラー");

	}
	
	/**
	 * 初期表示
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function index(){		
	}
}
