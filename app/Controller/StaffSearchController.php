<?php
/**
 * StaffSearch
 *
 * スタッフ検索画面
 *
 * @category      MNU
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 * @example       通常時[indexアクション]
 *                      [post通信]
 *                Ajax時[popup02アクション]
 *                      [ajax通信]
 *                画面表示処理
 *                      通常時[引数($mode)は何もなし]
 *                        画面の処理ボタンは「submit」にて処理を行う
 *                      Ajax時[引数($mode)は"1"を指定する]
 *                        画面の処理ボタンは「onCick」にてJavascriptにて処理を行う
 */

App::uses('AppController', 'Controller');

class StaffSearchController extends AppController {
	
	public $name = 'StaffSearch';
	public $layout = "DCMS";
	public $uses = array();
	
	
	/**
	 * 初期表示
	 * @access   public
	 * @param    void
	 * @return   void
	 * @example  post通信の場合に検索情報を取得
	 *           引数($mode)には何も処理を行わない
	 */
	function index(){
		
		/* パラメータ引数用 */
		$mode           = '';
		$staff_code     = '';
		$staff_nm       = '';
		$dispatch_val   = '';
		
		/* パラメータ引数チェック */
		/*
		if(isset($this->params->query['mode'])){

			$mode = $this->params->query['mode'];
		}
		*/
			
		/* ▼選択拠点情報読込 */
		$select_ninusi_cd = $this->Session->read('select_ninusi_cd');
		$select_sosiki_cd = $this->Session->read('select_sosiki_cd');
		
		/* ▼派遣情報取得 */
		App::uses('StaffSearchModel', 'Model');
		$this->StaffSearchModel = new StaffSearchModel();
		$dispatch = $this->StaffSearchModel->getDispatch($select_ninusi_cd, $select_sosiki_cd);
		/* ▼派遣情報セット */
		$this->set('dispatch',Sanitize::clean($dispatch));
		
		/* ▼検索情報セット用配列 */
		$search_data = array();
		
		/* ▼検索時 */
		if($this->request->is('post')) {
			// ▼検索情報設定
			$input_data['staff_cd'] = trim(mb_convert_kana($this->request->data['StaffSearch']['staff_cd'], "a", "UTF-8")); // スタッフコード
			$input_data['staff_nm'] = trim($this->request->data['StaffSearch']['staff_nm']);                                // スタッフ名
			$input_data['dispatch'] = $this->request->data['dispatch'];                                                     // 派遣会社
			// ▼検索処理
			$search_data = $this->StaffSearchModel->getStaffSearch($input_data);
		}
		/* ▼検索データセット */
		$this->set('search_data',Sanitize::clean($search_data));
		/* ▼モード情報セット */
		$this->set('mode',Sanitize::clean($mode));
		/* ▼スタッフコードセット */
		$this->set('staff_cd',Sanitize::clean($staff_code));
		/* ▼スタッフ名セット */
		$this->set('staff_nm',Sanitize::clean($staff_nm));
		/* ▼派遣IDセット */
		$this->set('dispatch_val',Sanitize::clean($dispatch_val));
		
		//表示するviewの変更
		$this->render('popup02');
		
	}
	
	/**
	 * ポップアップ取得
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 * @example  ポップアップされた場合に遷移される
	 *           引数($mode)に"1"を設定し、画面上のボタンをAjax用に設定する
	 */
	public function popup02() {
		
		/* パラメータ引数用 */
		$mode           = '';
		$staff_code     = '';
		$staff_nm       = '';
		$dispatch_val   = '';
		
		if(isset($this->params->query['staff_cd']) AND $this->params->query['staff_cd'] != 'undefined'){
			/* パラメータ引数取得 */
			$staff_code = $this->params->query['staff_cd'];
		}
		if(isset($this->params->query['staff_nm']) AND $this->params->query['staff_nm'] != 'undefined'){
			/* パラメータ引数取得 */
			$staff_nm = $this->params->query['staff_nm'];
		}
		if(isset($this->params->query['dispatch']) AND $this->params->query['dispatch'] != 'undefined'){
			/* パラメータ引数取得 */
			$dispatch_val = $this->params->query['dispatch'];
		}
		
		/* ▼選択拠点情報読込 */
		$select_ninusi_cd = $this->Session->read('select_ninusi_cd');
		$select_sosiki_cd = $this->Session->read('select_sosiki_cd');
		
		/* ▼派遣情報取得 */
		App::uses('StaffSearchModel', 'Model');
		$this->StaffSearchModel = new StaffSearchModel();
		$dispatch = $this->StaffSearchModel->getDispatch($select_ninusi_cd, $select_sosiki_cd);
		/* ▼派遣情報セット */
		$this->set('dispatch',Sanitize::clean($dispatch));
		
		/* ▼検索情報セット用配列 */
		$search_data = array();
		/* ▼検索データセット */
		$this->set('search_data',Sanitize::clean($search_data));
		
		/* ▼モード情報 1:ajax その他:post */
		$mode = '1';
		/* ▼モード情報セット */
		$this->set('mode',Sanitize::clean($mode));
		/* ▼スタッフコードセット */
		$this->set('staff_cd',Sanitize::clean($staff_code));
		/* ▼スタッフ名セット */
		$this->set('staff_nm',Sanitize::clean($staff_nm));
		/* ▼派遣IDセット */
		$this->set('dispatch_val',Sanitize::clean($dispatch_val));

		$this->autoLayout = false;
	
	}
	
	/**
	 * ajax処理
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 * @example  検索処理を行い、JSON形式にてデータを返却する
	 */
	public function ajax() {
		/* 検索データ */
		$search_data  = '';
		$search_value = '';
		
		/* ▼検索時 */
		if($this->request->is('post')) {
			// ▼検索情報設定
			$input_data['staff_cd'] = trim(mb_convert_kana($this->request->data['staff_cd'], "a", "UTF-8")); // スタッフコード
			$input_data['staff_nm'] = trim($this->request->data['staff_nm']);                                // スタッフ名
			$input_data['dispatch'] = $this->request->data['dispatch'];                                      // 派遣会社
			// ▼検索処理 
			App::uses('StaffSearchModel', 'Model');
			$this->StaffSearchModel = new StaffSearchModel();
			$search_data  = $this->StaffSearchModel->getStaffSearch($input_data);
			echo json_encode($search_data, JSON_UNESCAPED_UNICODE);
		}else{
			echo "error";
		}
		exit;
	}

}
