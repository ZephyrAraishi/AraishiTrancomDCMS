<?php
/**
 * TPM140
 *
 * バーコード・荷物探し画面
 *
 * @category      TPM
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');
App::uses('MMeisyo', 'Model');
App::uses('TPM140Model', 'Model');

class TPM140Controller extends AppController {

	public $name = 'TPM140';
	public $layout = "DCMS";

	/**
	 * 起動時処理
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
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['TPM']));
		// 機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['TPM']['140']['kino_nm']));

		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
		                             $this->Session->read('select_sosiki_cd'),
		                             $this->name
		                            );

		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),
				                     $this->Session->read('select_sosiki_cd'),
				                     $this->name
				                     );

		$this->TPM140Model = new TPM140Model($this->Session->read('select_ninusi_cd'),
                                     $this->Session->read('select_sosiki_cd'),
                                     $this->name
                                     );

		/* メッセージマスタ呼び出し */
		$this->MMessage = new MMessage($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

	}

	/**
	 * 初期表示
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function index(){

		Cache::clear();

		//Javascript設定
		$this->set("onload","initReload();".
				"initCSV('".Router::url('/TPM140/csv', false). "');" );

		$ymd_from = '';
		$ymd_to = '';
		$return_cd = '';
		$display = '';
		$message = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['ymd_from'])){
			$ymd_from = $this->request->query['ymd_from'];
		}
		if (isset($this->request->query['ymd_to'])){
			$ymd_to = $this->request->query['ymd_to'];
		}

		//リターンCD
		if(isset($this->request->query['return_cd'])) {
			$return_cd = $this->request->query['return_cd'];
		}

		//メッセージ
		if(isset($this->request->query['message'])) {
			$message = $this->MCommon->escapePHP($this->request->query['message']);
		}

		//エラーコード
		if(isset($this->request->query['display'])) {
			$display = $this->request->query['display'];
		}

		if ($ymd_from == '') {
			$ymd_from = $this->TPM140Model->getToday();
		}

		if ($ymd_to == '') {
			$ymd_to = $this->TPM140Model->getToday();
		}

		//検索条件を保存
		$this->set("ymd_from", $ymd_from);
		$this->set("ymd_to", $ymd_to);
		$this->set('lsts', array());

		// エラーが発生した場合は何もせずに終了
		if ( $return_cd == '1' ) {
			return;
		}

		try{
			$lsts = $this->TPM140Model->getHassoList($ymd_from, $ymd_to);
			$this->set('lsts',$lsts);
		} catch (Exception $e) {
			$this->TPM140Model->printLog("fatal", "例外発生", "TPM140", $e->getMessage());
			$message = str_replace ("+","%2B",base64_encode ($this->MMessage->getOneMessage('CMNE000107')));
			$this->redirect('/TPM140/index?return_cd=1' .
								 '&message=' . $message .
								 '&ymd=' . $ymd .
								 '&display=true'
					 );
		}

	}
	
	private function calcWeek($ymd, &$ymd_from, &$ymd_to) {
		
		$datetime = new DateTime($ymd);
		$w = (int)$datetime->format('w');
		if ( $w == 0 ) {
			$ymd_from = $this->addDays($ymd,  -6);
			$ymd_to = $ymd;
		} else {
			$ymd_from = $this->addDays($ymd, -($w - 1));
			$ymd_to = $this->addDays($ymd,  (7 - $w));
		}
	}
	
	private function addDays($ymd, $dt) {
		return date("Ymd", strtotime("$ymd $dt day"));
	}

	/**
	 * CSV出力処理
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function csv() {

		//ポップアップデータだけ取得するため、レイアウトは無効にする
		$this->autoLayout = false;

		$ymd_from = '';
		$ymd_to = '';
		if (isset($this->request->query['ymd_from'])){
			$ymd_from = $this->request->query['ymd_from'];
		}
		if (isset($this->request->query['ymd_to'])){
			$ymd_to = $this->request->query['ymd_to'];
		}

		// 作業進捗情報CSVデータを取得
		$csvData = $this->TPM140Model->getCSV($ymd_from, $ymd_to);

		// 作成するファイル名の指定
		$fileName = "シネコン発送明細_" . $ymd_from . "-" . $ymd_to . ".csv";
		$buff = '';
		foreach ($csvData as $data) {
			$buff .= $data . "\r\n";
		}

		// CSVファイル出力
		header("Content-disposition: attachment; filename=" . rawurlencode($fileName));
		header("Content-type: application/octet-stream;");
		print(mb_convert_encoding($buff, 'sjis-win', 'UTF-8'));
		exit();

		return;
	}

}

?>