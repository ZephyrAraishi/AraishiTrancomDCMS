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

class ERR030Controller extends AppController {
	
	public $name = 'ERR030';
	public $layout = "DCMS";

	/**
	 * コントロール起動時処理
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function beforeFilter() {
	
		parent::beforeFilter();
	
		// ログアウトを表示しない
		$this->set('nonSysLink', true);
		
		//システム名
		$this->set('system', "エラー");
		//機能名
		$this->set('kino', "権限エラー");
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
