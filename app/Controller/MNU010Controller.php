<?php
/**
 * MNU010
 *
 * ログイン画面
 *
 * @category      MNU
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MNUAppController', 'Controller');
class MNU010Controller extends MNUAppController {
	
	public $name = 'MNU010';
	public $layout = "DCMS";
	public $uses = array();
	
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
		$displayName = parent::getDisplayName(self::mei_cd);
		
		// ビューへ設定
		if (isset($displayName)) {
			$this->set('system', Sanitize::clean($displayName));
		} else {
			$this->set('system', null);
		}
		
		// ログアウトを表示しない
		$this->set('nonDispLogOut', true);
		
	}
	
	/**
	 * 初期表示
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function index(){
		
		// 先頭コントロールにフォーカス設定
		$this->set('onload', "document.getElementById('MNU010ModelStaffCd').focus()");
		
		if(!$this->request->is('post')) {
		
		} else  {
			 
			App::uses('MNU010Model', 'Model');
			$this->MNU010Model = new MNU010Model();
			
			/* ▼入力チェック */
			$this->MNU010Model->set(Sanitize::clean($this->request->data));
			$errors = $this->validateErrors($this->MNU010Model);
			if (!empty($errors)) {
				// エラー情報出力
				$this->set("errors",Sanitize::clean($errors));
				return;
			}
				
            /* ▼ログインチェック */
			$staffInfo = $this->MNU010Model->loginStaff();
			if (count($staffInfo) == 0) {
				// ログイン失敗
				$this->MNU010Model->invalidate('staff_cd', $this->MNU010Model->login_err_msg);
				// エラー情報出力
				$errors = $this->MNU010Model->validationErrors;
				$this->set('errors', Sanitize::clean($errors));
				return;
			} elseif ($staffInfo[0]['DEL_FLG'] == 1) {
				// 削除されたスタッフのログイン
				$this->MNU010Model->invalidate('staff_cd', $this->MNU010Model->login_err_msg_del);
				// エラー情報出力
				$errors = $this->MNU010Model->validationErrors;
				$this->set('errors', Sanitize::clean($errors));
				return;
				
			}
			
			/* ▼ログイン完了 */
			// セッション登録	
			foreach($staffInfo[0] as $key => $value) {		
				$this->Session->write(strtolower($key), $value);
			}
			
			$this->redirect(array("controller" => "MNU011", "action" => "index"));
		}
		
	}

}
