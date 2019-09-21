<?php
/**
 * MNU011
 *
 * 拠点選択
 *
 * @category      MNU
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MNUAppController', 'Controller');
class MNU011Controller extends MNUAppController {
	
	public $name = 'MNU011';
	public $layout = "DCMS";
	public $uses = array();	
	
	/**
	 * 定数　取得項目名
	 * @access   public
	 */
	const mei_cd_kino = '02';       // 名称コード(機能名)
	const mei_cd_system = '01';     // 名称コード(システム名)
	
	/**
	 * コントロール起動時処理
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function beforeFilter() {
		
		parent::beforeFilter();
		
		$displayName = parent::getDisplayName(self::mei_cd_kino);
		
		// ビューへ設定
		if (isset($displayName)) {
			$this->set('system', Sanitize::clean($displayName));
		} else {
			$this->set('system', null);
		}
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
		$this->Session->delete('select_ninusi_cd');
		$this->Session->delete('select_sosiki_cd');
		$this->Session->delete('select_ninusi_nm');
		$this->Session->delete('select_sosiki_nm');
		$this->Session->delete('select_ninusi_ryaku');
		$this->Session->delete('select_sosiki_ryaku');
		
		/* ▼拠点情報取得 */
		App::uses('MNU011Model', 'Model');
		$this->MNU011Model = new MNU011Model();
		$this->MNU011Model->set('staff_cd', Sanitize::clean($this->Session->read('staff_cd')));
		$kyotens = $this->MNU011Model->getKyoten();
		
		$select_index = -1;
		
		// 拠点選択時の各名称取得用に一覧情報を一時的にセッションに保持
		$this->set('kyotens',Sanitize::clean($kyotens));
			
		if (count($kyotens) == 0) {
				
			$errors = array();
			$errors['Check'][] = $this->MNU011Model->none_err_msg;
			$this->set('errors', Sanitize::clean($errors));
			
		} else {
			
			// 代表拠点が存在すれば選択拠点の初期値として設定
			foreach ($kyotens as $key => $value){
				if ($value['DAIHYO_FLG'] == 1) {
					$select_ninusi_cd = $value['NINUSI_CD'];
					$select_sosiki_cd = $value['SOSIKI_CD'];
					$select_ninusi_nm = $value['NINUSI_NM'];
					$select_sosiki_nm = $value['SOSIKI_NM'];
					$select_ninusi_ryaku = $value['NINUSI_RYAKU'];
					$select_sosiki_ryaku = $value['SOSIKI_RYAKU'];
					$select_index = $key;
					break;
				}
			}
			
			// 代表フラグが存在しない場合は1行目の拠点を初期値として設定
			if ($select_index == -1) {
				$select_ninusi_cd = $kyotens[0]['NINUSI_CD'];
				$select_sosiki_cd = $kyotens[0]['SOSIKI_CD'];
				$select_ninusi_nm = $kyotens[0]['NINUSI_NM'];
				$select_sosiki_nm = $kyotens[0]['SOSIKI_NM'];
				$select_ninusi_ryaku = $kyotens[0]['NINUSI_RYAKU'];
				$select_sosiki_ryaku = $kyotens[0]['SOSIKI_RYAKU'];
				$select_index = 0;
			}
			
			$this->set('select_index',$select_index);
				
			/* ▼拠点選択時 */
			if($this->request->is('post')) {
				/* ▼選択拠点情報設定 */
				$idx = $this->request->data['kyotens']['value'];
				$select_ninusi_cd = $kyotens[$idx]['NINUSI_CD'];
				$select_sosiki_cd = $kyotens[$idx]['SOSIKI_CD'];
				$select_ninusi_nm = $kyotens[$idx]['NINUSI_NM'];
				$select_sosiki_nm = $kyotens[$idx]['SOSIKI_NM'];
				$select_ninusi_ryaku = $kyotens[$idx]['NINUSI_RYAKU'];
				$select_sosiki_ryaku = $kyotens[$idx]['SOSIKI_RYAKU'];
			}
				
			// 選択拠点情報をセッションへ格納
			$this->Session->write('select_ninusi_cd', $select_ninusi_cd);
			$this->Session->write('select_sosiki_cd', $select_sosiki_cd);
			$this->Session->write('select_ninusi_nm', $select_ninusi_nm);
			$this->Session->write('select_sosiki_nm', $select_sosiki_nm);
			$this->Session->write('select_ninusi_ryaku', $select_ninusi_ryaku);
			$this->Session->write('select_sosiki_ryaku', $select_sosiki_ryaku);
			
			/* システム名取得 */
			App::uses('MMeisyo', 'Model');
			$this->MMeisyo = new MMeisyo($select_ninusi_cd, $select_sosiki_cd, $this->name);
			// 名称マスタデータ取得
			$data = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.sys'), self::mei_cd_system);
			
			if (!empty($data)) {
				$this->Session->write('system_name', $data[self::mei_cd_system]);
			}
			
			if((count($kyotens) == 1 and $kyotens[0]['DAIHYO_FLG'] == 1) or $this->request->is('post')) {
				$this->redirect(array("controller" => "MNU012", "action" => "index"));
			}
			
		}

	}

}
