<?php
/**
 * TPM070
 *
 * トータルピッキング進捗画面
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
App::uses('TPM070Model', 'Model');

class TPM070Controller extends AppController {

	public $name = 'TPM070';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['TPM']['070']['kino_nm']));

		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
		                             $this->Session->read('select_sosiki_cd'),
		                             $this->name
		                            );

		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),
				                     $this->Session->read('select_sosiki_cd'),
				                     $this->name
				                     );

		$this->TPM070Model = new TPM070Model($this->Session->read('select_ninusi_cd'),
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
				"initCSV('".Router::url('/TPM070/csv', false). "');" );

		$ymd_from = '';
		$ymd_to = '';
		$BatchNo_from = '';
		$BatchNo_to = '';
		$Tokuisaki = '';
		$homen = '';
		$shomikigen_henko = '';
		$Sinchoku = '';
		$Shohin = '';


		$pageID = 1;
		$return_cd = '';
		$display = '';
		$message = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['ymd_from'])){
			$ymd_from = $this->request->query['ymd_from'];
			$ymd_to = $this->request->query['ymd_to'];
			$BatchNo_from = $this->request->query['BatchNo_from'];
			$BatchNo_to = $this->request->query['BatchNo_to'];
			$Tokuisaki = $this->request->query['Tokuisaki'];
			$homen = $this->request->query['homen'];
			$Shohin = $this->request->query['Shohin'];
		}

		//パラメータ　検索条件を取得
		if (isset($this->request->query['Sinchoku'])){
			$Sinchoku = $this->request->query['Sinchoku'];
		}

		//パラメータ　検索条件を取得
		if (isset($this->request->query['shomikigen_henko'])){
			$shomikigen_henko = $this->request->query['shomikigen_henko'];
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
			$ymd_from = $this->TPM070Model->getToday();
			$ymd_to = $ymd_from;
		}

		//検索条件を保存
		$this->set("ymd_from",$ymd_from);
		$this->set("ymd_to",$ymd_to);

		if ($BatchNo_from != "") {
			$this->set("BatchNo_from",$BatchNo_from);
		}

		if ($BatchNo_to != "") {
			$this->set("BatchNo_to",$BatchNo_to);
		}

		if ($Tokuisaki != "") {
			$this->set("Tokuisaki",htmlspecialchars($Tokuisaki));
		}

		if ($homen != "") {
			$this->set("homen",htmlspecialchars($homen));
			}

		if ($shomikigen_henko != "") {
			$this->set("shomikigen_henko",$shomikigen_henko);
		}

		if ($Sinchoku != "") {
			$this->set("Sinchoku",$Sinchoku);
		}

		if ($Shohin != "") {
			$this->set("Shohin",htmlspecialchars($Shohin));
		}

		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {

			$this->Session->write('displayTPM070','true');

			$this->redirect('/TPM070/index?return_cd=' . $return_cd .
										  '&message=' . $message .
										  '&ymd_from=' . $ymd_from .
										  '&ymd_to=' . $ymd_to .
										  '&BatchNo_from=' . $BatchNo_from .
										  '&BatchNo_to=' . $BatchNo_to .
										  '&Tokuisaki=' . $Tokuisaki .
										  '&homen=' . $homen .
										  '&shomikigen_henko=' . $shomikigen_henko .
										  '&Sinchoku=' . $Sinchoku .
										  '&Shohin=' . $Shohin .
										  '&pageID=' . $pageID .
										  '&display=false'
										  );

			return;
		} else if ($this->Session->check('displayTPM070') && $display == "false") {

			$this->Session->delete('displayTPM070');

		} else if (!$this->Session->check('displayTPM070') && $display == "false") {

			$this->redirect('/TPM070/index?&ymd_from=' . $ymd_from .
											 '&ymd_to=' . $ymd_to .
											'&BatchNo_from=' . $BatchNo_from .
											'&BatchNo_to=' . $BatchNo_to .
											'&Tokuisaki=' . $Tokuisaki .
											'&homen=' . $homen .
											'&shomikigen_henko=' . $shomikigen_henko .
											'&Sinchoku=' . $Sinchoku .
										  	'&Shohin=' . $Shohin .
											'&pageID=' . $pageID
										  );
			return;
		}

		//進捗区分
		$arrayList = $this->MMeisyo->getMeisyoPDO("37");
		$this->set("kbn_sinchoku",$arrayList);

		//検索条件の必須チェック
		if (!$this->Session->check('displayTPM0702') && isset($this->request->query['ymd_from']) &&
				($ymd_from == '' || $ymd_to == '') && !isset($this->request->query['return_cd']) ) {


					//出荷日がないエラー
					$errorArray = array();
					$message = str_replace ("+","%2B",base64_encode ($this->MMessage->getOneMessage('TPME070001')));


					$this->Session->write('displayTPM0702','true');

					$this->redirect('/TPM070/index?return_cd=1' .
										 '&message=' . $message .
										 '&ymd_from=' . $ymd_from .
										 '&ymd_to=' . $ymd_to .
										'&BatchNo_from=' . $BatchNo_from .
										'&BatchNo_to=' . $BatchNo_to .
										'&Tokuisaki=' . $Tokuisaki .
										'&homen=' . $homen .
										'&shomikigen_henko=' . $shomikigen_henko .
										'&Sinchoku=' . $Sinchoku .
										'&Shohin=' . $Shohin .
										'&pageID=' . $pageID .
										 '&display=true'
					 );
		} else {
				$this->Session->delete('displayTPM0702');
		}

		//検索条件がある場合検索
		if (($ymd_from != '' && $ymd_to != '') || $BatchNo_from != '' || $BatchNo_to != ''  || $Tokuisaki != ''  || $homen != ''  || $shomikigen_henko != '' || $Sinchoku != '' || $Shohin != '') {

			//リスト一覧
			$lsts =  $this->TPM070Model->getList($ymd_from, $ymd_to, $BatchNo_from, $BatchNo_to, $Tokuisaki, $homen, $shomikigen_henko, $Sinchoku, $Shohin);

			//サマリーデータ
			$sumlst =  $this->TPM070Model->getCaseSumallyList($ymd_from, $ymd_to, $BatchNo_from, $BatchNo_to, $Tokuisaki, $homen, $shomikigen_henko, $Sinchoku, $Shohin);

			//サマリーデータ
			$Bsumlst =  $this->TPM070Model->getBaraSumallyList($ymd_from, $ymd_to, $BatchNo_from, $BatchNo_to, $Tokuisaki, $homen, $shomikigen_henko, $Sinchoku, $Shohin);

			//ページャー
			$pageNum = 50;

			$options = array(
					"totalItems" => count($lsts),
					"delta" => 10,
					"perPage" => $pageNum,
					"httpMethod" => "GET",
					"path" => "/DCMS/TPM070/index"
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
			if ($this->Session->check('saveTableTPM070')) {

				//前回のデータを取得
				$getArray = $this->Session->read('saveTableTPM070');

				//前回のデータを設置
				//$data = $getArray->data;
				$lstdata = $getArray->lsts;
				$timestamp = $getArray->timestamp;
				$sumlstdata = $getArray->sumlst;
				$Bsumlstdata = $getArray->Bsumlst;

				$this->set("timestamp", $timestamp);
				$this->set('lsts',$lstdata);
				$this->set('sumlst',$sumlstdata);
				$this->set('Bsumlst',$Bsumlstdata);

				//データ構造が違うのでレンダリングを変更
				$this->render("index_error");

				//前回のデータを削除
				$this->Session->delete('saveTableTPM070');

				//エラーが発生していない場合は、検索条件を元に表示
			} else {

				//タイムスタムプ取得
				$this->set("timestamp", $this->TPM070Model->getTimestamp());
				$this->set('lsts',$arrayList);
				$this->set('sumlst',$sumlst);
				$this->set('Bsumlst',$Bsumlst);
			}

			//Viewへセット
			$this->set('index',$index);


		//条件が何もない場合
		} else {

			//前回の処理でエラーが発生した場合、前回のデータをそのまま表示
			if ($this->Session->check('saveTableTPM070')) {

				//前回のデータを取得
				$getArray = $this->Session->read('saveTableTMP070');

				//前回のデータを設置
				$data = $getArray->data;
				$timestamp = $getArray->timestamp;

				$this->set("timestamp", $timestamp);
				$this->set('lsts',$data);

				//データ構造が違うのでレンダリングを変更
				$this->render("index_error");

				//前回のデータを削除
				$this->Session->delete('saveTableTPM070');

			//エラーが発生していない場合は、検索条件を元に表示
			} else {

				//タイムスタムプ取得
				$this->set("timestamp", $this->TPM070Model->getTimestamp());
			}

		}

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
		$BatchNo_from = '';
		$BatchNo_to = '';
		$Tokuisaki = '';
		$homen = '';
		$shomikigen_henko = '';
		$Sinchoku = '';
		$Shohin = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['ymd_from'])){
			$ymd_from = $this->request->query['ymd_from'];
			$ymd_to = $this->request->query['ymd_to'];
			$BatchNo_from = $this->request->query['BatchNo_from'];
			$BatchNo_to = $this->request->query['BatchNo_to'];
			$Tokuisaki = $this->request->query['Tokuisaki'];
			$homen = $this->request->query['homen'];
			$Shohin = $this->request->query['Shohin'];
		}

		//パラメータ　検索条件を取得
		if (isset($this->request->query['Sinchoku'])){
			$Sinchoku = $this->request->query['Sinchoku'];
		}

		//パラメータ　検索条件を取得
		if (isset($this->request->query['shomikigen_henko'])){
			$shomikigen_henko = $this->request->query['shomikigen_henko'];
		}

		// トータルピッキング進捗情報CSVデータを取得
		$csvData = $this->TPM070Model->getCSV($ymd_from, $ymd_to, $BatchNo_from, $BatchNo_to, $Tokuisaki, $homen, $shomikigen_henko, $Sinchoku, $Shohin);

		// 作成するファイル名の指定
		$fileName = "トータルピッキング進捗情報_" . date("Ymdhi") . ".csv";
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