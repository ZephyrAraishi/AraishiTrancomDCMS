<?php
/**
 * MNU012
 *
 * メニュー画面
 *
 * @category      MNU
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MNUAppController', 'Controller');
class MNU012Controller extends MNUAppController {
	
	public $name = 'MNU012';
	public $layout = "DCMS";
	public $uses = array();
		
	/**
	 * 定数　取得項目名
	 * @access   public
	 */
	const mei_cd = '03';     // 名称コード
	
	/**
	 * コントロール起動時処理
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function beforeFilter() {
		
		parent::beforeFilter();
				
		// ビューへ設定
		$this->set('system', Sanitize::clean($this->Session->read('system_name')));
		$displayName = parent::getDisplayName(self::mei_cd);
		$this->set('kino', Sanitize::clean($displayName));
		
		// メニューを表示しない
		$this->set('nonDispMenu', true);
		
	}
	
	/**
	 * 初期表示
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function index(){

		/* ▼セッション(荷主/組織情報)初期化 */
		$this->Session->delete('menu_subsys');
		$this->Session->delete('menu_kino');
				
		/* ▼参照可能機能情報取得 */
		App::uses('MNU012Model', 'Model');
		$this->MNU012Model = new MNU012Model($this->Session->read('select_ninusi_cd'),
				                             $this->Session->read('select_sosiki_cd'),
				                             $this->name
				                             );
		$this->MNU012Model->set(array('staff_cd'  => Sanitize::clean($this->Session->read('staff_cd'))));
		$menus = $this->MNU012Model->getKyoka();
		
		// 拠点選択時の各名称取得用に一覧情報を一時的にセッションに保持
		$this->set('menus',Sanitize::clean($menus));
		
		/* セッション格納用メニュー情報作成 */
		$subSystemId = '';
		$MenuSubSys = array();
		$MenuKino = array();
		
		if (count($menus) == 0) {
			
			$errors = array();
			$errors['Check'][] = $this->MNU012Model->none_err_msg;
			$this->set('errors', Sanitize::clean($errors));
			
		}else{
			foreach ($menus as $key => $value){
				if ($subSystemId != $value['SUB_SYSTEM_ID']) {
					// サブシステムIDが異なった場合
					$subSystemId = $value['SUB_SYSTEM_ID'];
					// サブシステム情報格納
					$MenuSubSys[$subSystemId] = $value['SUB_SYSTEM_NM'];
				}
				// 機能情報格納
				$MenuKino[$subSystemId][$value['KINO_ID']] = array('kino_nm' => $value['KINO_NM'],
						'kino_url' => $value['KINO_URL'],
						'dsp_flg' => $value['DSP_FLG']);
			}
			
			// セッションへ格納
			$this->Session->write('menu_subsys', $MenuSubSys);
			$this->Session->write('menu_kino', $MenuKino);
		}

	     
	}

}
