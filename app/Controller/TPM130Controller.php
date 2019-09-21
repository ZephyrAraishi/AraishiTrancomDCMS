<?php
/**
 * TPM130
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
App::uses('TPM130Model', 'Model');

class TPM130Controller extends AppController {

	public $name = 'TPM130';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['TPM']['130']['kino_nm']));

		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
		                             $this->Session->read('select_sosiki_cd'),
		                             $this->name
		                            );

		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),
				                     $this->Session->read('select_sosiki_cd'),
				                     $this->name
				                     );

		$this->TPM130Model = new TPM130Model($this->Session->read('select_ninusi_cd'),
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
				"initPopup('".Router::url('/TPM130/popup', false). "');" .
				"initCSV('".Router::url('/TPM130/csv', false). "');" );

		$ymd = '';
		$ymd_from = '';
		$ymd_to = '';
		$return_cd = '';
		$display = '';
		$message = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['ymd'])){
			$ymd = $this->request->query['ymd'];
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

		if ($ymd == '') {
			$ymd = $this->TPM130Model->getToday();
		}
		
		$this->calcWeek($ymd, $ymd_from, $ymd_to);

		//検索条件を保存
		$this->set("ymd", $ymd);
		$this->set("ymd_from", $this->MCommon->strToDateString($ymd_from));
		$this->set("ymd_to", $this->MCommon->strToDateString($ymd_to));
		$this->set('lsts', array());

		// エラーが発生した場合は何もせずに終了
		if ( $return_cd == '1' ) {
			return;
		}

		try{
			$lsts = $this->TPM130Model->getProgressList($ymd_from, $ymd_to);
			$this->set('lsts',$lsts);
		} catch (Exception $e) {
			$this->TPM130Model->printLog("fatal", "例外発生", "TPM130", $e->getMessage());
			$message = str_replace ("+","%2B",base64_encode ($this->MMessage->getOneMessage('CMNE000107')));
			$this->redirect('/TPM130/index?return_cd=1' .
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

		$ymd = '';
		if (isset($this->request->query['ymd'])){
			$ymd = $this->request->query['ymd'];
		}

		$this->calcWeek($ymd, $ymd_from, $ymd_to);

		try{
			// 作業進捗情報CSVデータを取得
			$csvData = $this->TPM130Model->getCSV($ymd_from, $ymd_to);
		} catch (Exception $e) {
			$this->TPM130Model->printLog("fatal", "例外発生", "TPM130", $e->getMessage());
			$message = str_replace ("+","%2B",base64_encode ($this->MMessage->getOneMessage('CMNE000107')));
			$this->redirect('/TPM130/index?return_cd=1' .
								 '&message=' . $message .
								 '&ymd=' . $ymd .
								 '&display=true'
					 );
		}

		// 作成するファイル名の指定
		$fileName = "作業進捗_" . $ymd_from . "-" .  $ymd_to . ".csv";
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

	/**
	 * ポップアップ処理
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function popup() {

		//ポップアップデータだけ取得するため、レイアウトは無効にする
		$this->autoLayout = false;
		
		$ymd = '';
		if (isset($this->request->query['ymd'])){
			$ymd = $this->request->query['ymd'];
		}

		$this->calcWeek($ymd, $ymd_from, $ymd_to);

		$homen = '';
		
		if (isset($this->request->query['homen'])){
			$homen = $this->request->query['homen'];
		}

		$totalPk = array();
		$tanemaki = array();
		$kenpin = array();
		
		try{
			$this->TPM130Model->getStaffList($ymd_from, $ymd_to, $homen, $totalPk, $tanemaki, $kenpin);
		} catch (Exception $e) {
			$this->TPM130Model->printLog("fatal", "例外発生", "TPM130", $e->getMessage());
			throw new InternalErrorException( $e->getMessage());
			return;
		}

		$this->set('homen', $homen);
		$this->set('totalPk', $totalPk);
		$this->set('tanemaki', $tanemaki);
		$this->set('kenpin', $kenpin);

	}

}

?>