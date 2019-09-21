<?php
/**
 * WPO021
 *
 * 棚欠品リスト画面
 *
 * @category      WPO
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('WPO021Model', 'Model');

class WPO021Controller extends AppController {

	public $name = 'WPO021';
	public $layout = "DCMS";
	public $uses = array();

	public $autolayout;

	/**
	 * 定数　取得項目名
	 * @access   public
	 */
	const mei_cd = '01';     // 名称コード

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
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['WPO']));
		// 機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WPO']['021']['kino_nm']));

		$this->WPO021Model = new WPO021Model($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'), $this->name);

	}

	/**
	 * 初期表示
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function index(){

		$ymd_syori = $this->request->query['ymd_syori'];
		$s_batch_no_cd = $this->request->query['s_batch_no_cd'];
		$s_kanri_no = $this->request->query['s_kanri_no'];

		//棚欠発生一覧
		$t_ketus = $this->WPO021Model->getTPickItem_TKetu($ymd_syori,$s_batch_no_cd,$s_kanri_no);

		// 一覧情報を一時的にセッションに保持
		$this->set('t_ketus',$t_ketus);
	}

}
