<?php
/**
 * MNU000
 *
 * 初期表示画面
 *
 * @category      MNU
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */

class ERR010Controller extends AppController {
	
	public $name = 'ERR010';
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
		$this->set('kino', "原因不明エラー");
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
