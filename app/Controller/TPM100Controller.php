<?php
/**
 * TPM100
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
App::uses('TPM100Model', 'Model');

class TPM100Controller extends AppController {

	public $name = 'TPM100';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['TPM']['100']['kino_nm']));

		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
		                             $this->Session->read('select_sosiki_cd'),
		                             $this->name
		                            );

		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),
				                     $this->Session->read('select_sosiki_cd'),
				                     $this->name
				                     );

		$this->TPM100Model = new TPM100Model($this->Session->read('select_ninusi_cd'),
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

		require_once 'Pager.php';

		//Javascript設定
		$this->set("onload","initReload();".
				"initCSV('".Router::url('/TPM100/csv', false). "');" .
				"initLabelCSV('".Router::url('/TPM100/labelcsv', false). "');" );

		$ymd_from = '';
		$ymd_to = '';
		$ymd_from_n = '';
		$ymd_to_n = '';
		$Tokuisaki = '';
		$homen = '';
		$tenpo = '';
		$shohin = '';

		$pageID = 1;
		$return_cd = '';
		$display = '';
		$message = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['ymd_from'])){
			$ymd_from = $this->request->query['ymd_from'];
			$ymd_to = $this->request->query['ymd_to'];
			$ymd_from_n = $this->request->query['ymd_from_n'];
			$ymd_to_n = $this->request->query['ymd_to_n'];
			$Tokuisaki = $this->request->query['Tokuisaki'];
			$homen = $this->request->query['homen'];
			$tenpo = $this->request->query['tenpo'];
			$Shohin = $this->request->query['Shohin'];
		}

		//POST時
		if(isset($this->request->query['pageID'])) {
			$pageID = $this->request->query['pageID'];
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

		if ($ymd_from == '' && $ymd_to == '') {
			$ymd_from = $this->TPM100Model->getToday();
			$ymd_to = $ymd_from;
		}

		//検索条件を保存
		$this->set("ymd_from",$ymd_from);
		$this->set("ymd_to",$ymd_to);

		if ($ymd_from_n != "") {
			$this->set("ymd_from_n",$ymd_from_n);
		}

		if ($ymd_to_n != "") {
			$this->set("ymd_to_n",$ymd_to_n);
		}

		if ($Tokuisaki != "") {
			$this->set("Tokuisaki",htmlspecialchars($Tokuisaki));
		}

		if ($homen != "") {
			$this->set("homen",htmlspecialchars($homen));
		}

		if ($tenpo != "") {
			$this->set("tenpo",$tenpo);
		}

		if ($Shohin != "") {
			$this->set("Shohin",htmlspecialchars($Shohin));
		}

		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {

			$this->Session->write('displayTPM100','true');

			$this->redirect('/TPM100/index?return_cd=' . $return_cd .
										  '&message=' . $message .
										  '&ymd_from=' . $ymd_from .
										  '&ymd_to=' . $ymd_to .
										  '&ymd_from_n=' . $ymd_from_n .
										  '&ymd_to_n=' . $ymd_to_n .
										  '&Tokuisaki=' . $Tokuisaki .
										  '&homen=' . $homen .
										  '&tenpo=' . $tenpo .
										  '&Shohin=' . $Shohin .
										  '&pageID=' . $pageID .
										  '&display=false'
										  );

			return;
		} else if ($this->Session->check('displayTPM100') && $display == "false") {

			$this->Session->delete('displayTPM100');

		} else if (!$this->Session->check('displayTPM100') && $display == "false") {

			$this->redirect('/TPM100/index?&ymd_from=' . $ymd_from .
											'&ymd_to=' . $ymd_to .
											'&ymd_from_n=' . $ymd_from_n .
											'&ymd_to_n=' . $ymd_to_n .
											'&Tokuisaki=' . $Tokuisaki .
											'&homen=' . $homen .
											'&tenpo=' . $tenpo .
										  	'&Shohin=' . $Shohin .
											'&pageID=' . $pageID
										  );
			return;
		}

		//検索条件の必須チェック
		if (!$this->Session->check('displayTPM1002') && isset($this->request->query['ymd_from']) &&
				($ymd_from == '' || $ymd_to == '') && !isset($this->request->query['return_cd']) ) {


					//出荷日がないエラー
					$errorArray = array();
					$message = str_replace ("+","%2B",base64_encode ($this->MMessage->getOneMessage('TPME080001')));


					$this->Session->write('displayTPM1002','true');

					$this->redirect('/TPM100/index?return_cd=1' .
										 '&message=' . $message .
										 '&ymd_from=' . $ymd_from .
										 '&ymd_to=' . $ymd_to .
										 '&ymd_from_n=' . $ymd_from_n .
										 '&ymd_to_n=' . $ymd_to_n .
										 '&Tokuisaki=' . $Tokuisaki .
										 '&homen=' . $homen .
										 '&tenpo=' . $tenpo .
										 '&Shohin=' . $Shohin .
										 '&pageID=' . $pageID .
										 '&display=true'
					 );
		} else {
				$this->Session->delete('displayTPM1002');
		}

		//検索条件がある場合検索
		if (($ymd_from != '' && $ymd_to != '') || $BatchNo_from != '' || $BatchNo_to != ''  || $Tokuisaki != ''  || $homen != ''  || $tenpo != ''  || $Sinchoku != '' || $Shohin != '') {

			//リスト一覧
			$lsts =  $this->TPM100Model->getList($ymd_from, $ymd_to, $BatchNo_from, $BatchNo_to, $Tokuisaki, $homen, $tenpo, $Sinchoku, $Shohin);

			//サマリーデータ
			$sumlst =  $this->TPM100Model->getSumallyList($ymd_from, $ymd_to, $BatchNo_from, $BatchNo_to, $Tokuisaki, $homen, $tenpo, $Sinchoku, $Shohin);

				//ページャー
			$pageNum = 50;

			$options = array(
					"totalItems" => count($lsts),
					"delta" => 10,
					"perPage" => $pageNum,
					"httpMethod" => "GET",
					"path" => "/DCMS/TPM100/index"
			);


			$pager = @Pager::factory($options);

			//ナビゲーションを設定
			$navi = $pager -> getLinks();
			$this->set('navi',$navi["all"]);

			//インデックスを取得
			$index = ($pageID - 1) * $pageNum;
			$arrayList = array();

			$limit = $index + $pageNum;

			if ($limit > count($lsts)) {

				$limit = count($lsts);
			}

			for($i = $index; $i < $limit ; $i++){
				$arrayList[] = $lsts[$i];
			}

			//前回の処理でエラーが発生した場合、前回のデータをそのまま表示
			if ($this->Session->check('saveTableTPM100')) {

				//前回のデータを取得
				$getArray = $this->Session->read('saveTableTPM100');

				//前回のデータを設置
				//$data = $getArray->data;
				$lstdata = $getArray->lsts;
				$timestamp = $getArray->timestamp;
				$sumlstdata = $getArray->sumlsts;

				$this->set("timestamp", $timestamp);
				$this->set('lsts',$lstdata);
				$this->set('sumlst',$sumlstdata);

				//データ構造が違うのでレンダリングを変更
				$this->render("index_error");

				//前回のデータを削除
				$this->Session->delete('saveTableTPM100');

				//エラーが発生していない場合は、検索条件を元に表示
			} else {

				//タイムスタムプ取得
				$this->set("timestamp", $this->TPM100Model->getTimestamp());
				$this->set('lsts',$arrayList);
				$this->set('sumlst',$sumlst);
			}

			//Viewへセット
			$this->set('index',$index);


		//条件が何もない場合
		} else {

			//前回の処理でエラーが発生した場合、前回のデータをそのまま表示
			if ($this->Session->check('saveTableTPM100')) {

				//前回のデータを取得
				$getArray = $this->Session->read('saveTableTMP080');

				//前回のデータを設置
				$data = $getArray->data;
				$timestamp = $getArray->timestamp;

				$this->set("timestamp", $timestamp);
				$this->set('lsts',$data);

				//データ構造が違うのでレンダリングを変更
				$this->render("index_error");

				//前回のデータを削除
				$this->Session->delete('saveTableTPM100');

			//エラーが発生していない場合は、検索条件を元に表示
			} else {

				//タイムスタムプ取得
				$this->set("timestamp", $this->TPM100Model->getTimestamp());
			}

		}

	}


	/**
	 * バーコード・荷物探しCSV出力処理
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
		$BatchNo_from = '';
		$BatchNo_to = '';
		$Tokuisaki = '';
		$homen = '';
		$shomikigen_henko = '';
		$Sinchoku='';
		$Shohin='';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['ymd_from'])){
			$ymd_from = $this->request->query['ymd_from'];
			$ymd_to = $this->request->query['ymd_to'];
			$BatchNo_from = $this->request->query['BatchNo_from'];
			$BatchNo_to = $this->request->query['BatchNo_to'];
			$Tokuisaki = $this->request->query['Tokuisaki'];
			$homen = $this->request->query['homen'];
			$tenpo = $this->request->query['tenpo'];
			$Shohin = $this->request->query['Shohin'];
		}

		//パラメータ　検索条件を取得
		if (isset($this->request->query['Sinchoku'])){
			$Sinchoku = $this->request->query['Sinchoku'];
		}

		// 種蒔き進捗情報CSVデータを取得
		$csvData = $this->TPM100Model->getCSV($ymd_from, $ymd_to, $BatchNo_from, $BatchNo_to, $Tokuisaki, $homen, $tenpo, $Sinchoku, $Shohin);

		// 作成するファイル名の指定
		$fileName = "種蒔き進捗情報_" . date("Ymdhi") . ".csv";
		$buff = '';
		foreach ($csvData as $data) {
			$buff .= $data . "\r\n";
		}

		// CSVファイル出力
		header("Content-disposition: attachment; filename=" . $fileName);
		header("Content-type: application/octet-stream; name=" . $fileName);
		print(mb_convert_encoding($buff, 'sjis-win', 'UTF-8'));
		exit();

		return;

	}

	/**
	 * ラベル出力情報CSV出力処理
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function labelcsv() {

		//ポップアップデータだけ取得するため、レイアウトは無効にする
		$this->autoLayout = false;

		$ymd_from = '';
		$ymd_to = '';
		$BatchNo_from = '';
		$BatchNo_to = '';
		$Tokuisaki = '';
		$homen = '';
		$shomikigen_henko = '';
		$Sinchoku='';
		$Shohin='';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['ymd_from'])){
			$ymd_from = $this->request->query['ymd_from'];
			$ymd_to = $this->request->query['ymd_to'];
			$BatchNo_from = $this->request->query['BatchNo_from'];
			$BatchNo_to = $this->request->query['BatchNo_to'];
			$Tokuisaki = $this->request->query['Tokuisaki'];
			$homen = $this->request->query['homen'];
			$tenpo = $this->request->query['tenpo'];
			$Shohin = $this->request->query['Shohin'];
		}

		//パラメータ　検索条件を取得
		if (isset($this->request->query['Sinchoku'])){
			$Sinchoku = $this->request->query['Sinchoku'];
		}

		// 種蒔き進捗情報CSVデータを取得
		$csvData = $this->TPM100Model->getCSV($ymd_from, $ymd_to, $BatchNo_from, $BatchNo_to, $Tokuisaki, $homen, $tenpo, $Sinchoku, $Shohin);

		// 作成するファイル名の指定
		$fileName = "種蒔き進捗情報_" . date("Ymdhi") . ".csv";
		$buff = '';
		foreach ($csvData as $data) {
			$buff .= $data . "\r\n";
		}

		// CSVファイル出力
		header("Content-disposition: attachment; filename=" . $fileName);
		header("Content-type: application/octet-stream; name=" . $fileName);
		print(mb_convert_encoding($buff, 'sjis-win', 'UTF-8'));
		exit();

		return;

	}

}

?>