<?php
/**
 * TPM110
 *
 * 種蒔照会画面
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
App::uses('TPM110Model', 'Model');

class TPM110Controller extends AppController {

	public $name = 'TPM110';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['TPM']['110']['kino_nm']));

		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
		                             $this->Session->read('select_sosiki_cd'),
		                             $this->name
		                            );

		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),
				                     $this->Session->read('select_sosiki_cd'),
				                     $this->name
				                     );

		$this->TPM110Model = new TPM110Model($this->Session->read('select_ninusi_cd'),
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
				"initCSV('".Router::url('/TPM110/csv', false). "');" );

		$ymd_from = '';
		$ymd_to = '';
		$homen = '';
		$shohin_code = '';
		$shohin_mei = '';
		$tenpo = '';
		$kanban_barcode = '';
		
		$pageID = 1;
		$pageID2 = 1;
		$return_cd = '';
		$display = '';
		$message = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['ymd_from'])){										
			$ymd_from = $this->request->query['ymd_from'];									//出荷日
			$ymd_to = $this->request->query['ymd_to'];										//出荷日
			$homen = $this->request->query['homen'];										//方面名
			$shohin_code = $this->request->query['shohin_code'];							//商品コード
			$shohin_mei = $this->request->query['shohin_mei'];								//商品名
			$tenpo = $this->request->query['tenpo'];										//店舗
			$kanban_barcode = $this->request->query['kanban_barcode'];						//看板バーコード
		}

		//パラメータ　検索条件を取得


		//POST時
		if(isset($this->request->query['pageID'])) {
			$pageID = $this->request->query['pageID'];
		}
		if(isset($this->request->query['pageID2'])) {
			$pageID2 = $this->request->query['pageID2'];
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
			$ymd_from = $this->TPM110Model->getToday();
			$ymd_to = $ymd_from;
		}

		//検索条件を保存
		$this->set("ymd_from",$ymd_from);
		$this->set("ymd_to",$ymd_to);

		if ($homen != "") {
			$this->set("homen",htmlspecialchars($homen));
		}

		if ($shohin_code != "") {
			$this->set("shohin_code",$shohin_code);
		}

		if ($shohin_mei != "") {
			$this->set("shohin_mei",$shohin_mei);
		}

		if ($tenpo != "") {
			$this->set("tenpo",$tenpo);
		}

		if ($kanban_barcode != "") {
			$this->set("kanban_barcode",$kanban_barcode);
		}

		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {

			$this->Session->write('displayTPM110','true');

			$this->redirect('/TPM110/index?return_cd=' . $return_cd .
										  '&message=' . $message .
										  '&ymd_from=' . $ymd_from .
										  '&ymd_to=' . $ymd_to .
										  '&tenpo=' . $tenpo .
										  '&shohin_code=' . $shohin_code .
										  '&shohin_mei=' . $shohin_mei .
										  '&homen=' . $homen .
										  '&kanban_barcode=' . $kanban_barcode .
										  '&pageID=' . $pageID .
										  '&pageID2=' . $pageID2 .
										  '&display=false'
			);

			return;
		} else if ($this->Session->check('displayTPM110') && $display == "false") {

			$this->Session->delete('displayTPM110');

		} else if (!$this->Session->check('displayTPM110') && $display == "false") {

			$this->redirect('/TPM110/index?&ymd_from=' . $ymd_from .
											'&ymd_to=' . $ymd_to .
											'&tenpo=' . $tenpo .
											'&shohin_code=' . $shohin_code .
											'&shohin_mei=' . $shohin_mei .
											'&homen=' . $homen .
											'&kanban_barcode=' . $kanban_barcode .
											'&pageID=' . $pageID .
											'&pageID2=' . $pageID2
			);
			return;
		}

		//検索条件の必須チェック
		if (!$this->Session->check('displayTPM1102') && isset($this->request->query['ymd_from']) &&
				($ymd_from == '' || $ymd_to == '') && !isset($this->request->query['return_cd']) ) {

					//出荷日がないエラー
					$errorArray = array();
					$message = str_replace ("+","%2B",base64_encode ($this->MMessage->getOneMessage('TPME090001')));

					$this->Session->write('displayTPM1102','true');

					$this->redirect('/TPM110/index?return_cd=1' .
										 '&message=' . $message .
										 '&ymd_from=' . $ymd_from .
										 '&ymd_to=' . $ymd_to .
										 '&tenpo=' . $tenpo .
										 '&shohin_code=' . $shohin_code .
										 '&shohin_mei=' . $shohin_mei .
										 '&homen=' . $homen .
										 '&kanban_barcode=' . $kanban_barcode .
										 '&pageID=' . $pageID .
										 '&pageID2=' . $pageID2 .
										 '&display=true'
					 );
		} else {
				$this->Session->delete('displayTPM1102');
		}

		//検索条件がある場合検索
		if (($ymd_from != '' && $ymd_to != '') || $homen != '' || $tenpo != '' || $shohin_code != '' || $shohin_mei != '' || $kanban_barcode != '') {

			//リスト一覧
			$lsts =  $this->TPM110Model->getList($ymd_from, $ymd_to, $homen, $shohin_code, $shohin_mei, $tenpo, $kanban_barcode);

			//一覧ページャー
			$pageNum = 50;

			$options = array(
					"totalItems" => count($lsts),
					"delta" => 10,
					"perPage" => $pageNum,
					"httpMethod" => "GET",
					"path" => "/DCMS/TPM110/index",
					"urlVar" => "pageID"
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


			$pager2 = @Pager::factory($options);

			//ナビゲーションを設定
			$navi2 = $pager2 -> getLinks();
			$this->set('navi2',$navi2["all"]);

			//インデックスを取得
			$index = ($pageID2 - 1) * $pageNum;
			$zanarrayList = array();

			$limit = $index + $pageNum;
			
			//前回の処理でエラーが発生した場合、前回のデータをそのまま表示
			if ($this->Session->check('saveTableTPM110')) {

				//前回のデータを取得
				$getArray = $this->Session->read('saveTableTPM110');

				//前回のデータを設置
				$lstdata = $getArray->lsts;
				$zanlstdata = $getArray->zanlsts;
				$timestamp = $getArray->timestamp;
				$sumlstdata = $getArray->sumlsts;

				$this->set("timestamp", $timestamp);
				$this->set('lsts',$lstdata);
				$this->set('zanlsts',$zanlstdata);
				$this->set('sumlst',$sumlstdata);

				//データ構造が違うのでレンダリングを変更
				$this->render("index_error");

				//前回のデータを削除
				$this->Session->delete('saveTableTPM110');

				//エラーが発生していない場合は、検索条件を元に表示
			} else {

				//タイムスタムプ取得
				$this->set("timestamp", $this->TPM110Model->getTimestamp());
				$this->set('lsts',$arrayList);
				$this->set('zanlsts',$zanarrayList);
//				$this->set('sumlst',$sumlst);
			}

			//Viewへセット
			$this->set('index',$index);


		//条件が何もない場合
		} else {

			//前回の処理でエラーが発生した場合、前回のデータをそのまま表示
			if ($this->Session->check('saveTableTPM110')) {

				//前回のデータを取得
				$getArray = $this->Session->read('saveTableTMP110');

				//前回のデータを設置
				$data = $getArray->data;
				$timestamp = $getArray->timestamp;

				$this->set("timestamp", $timestamp);
				$this->set('lsts',$data);

				//データ構造が違うのでレンダリングを変更
				$this->render("index_error");

				//前回のデータを削除
				$this->Session->delete('saveTableTPM110');

			//エラーが発生していない場合は、検索条件を元に表示
			} else {

				//タイムスタムプ取得
				$this->set("timestamp", $this->TPM110Model->getTimestamp());
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
		$homen = '';
		$shohin_code = '';
		$shohin_mei = '';
		$tenpo = '';
		$kanban_barcode = '';
//		$shomikigen_henko = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['ymd_from'])){
			$ymd_from = $this->request->query['ymd_from'];
			$ymd_to = $this->request->query['ymd_to'];
			$homen = $this->request->query['homen'];
			$shohin_code = $this->request->query['shohin_code'];
			$shohin_mei = $this->request->query['shohin_mei'];
			$tenpo = $this->request->query['tenpo'];
			$kanban_barcode = $this->request->query['kanban_barcode'];
		}


		// 種蒔照会情報CSVデータを取得
		$csvData = $this->TPM110Model->getCSV($ymd_from, $ymd_to, $homen, $shohin_code, $shohin_mei, $tenpo, $kanban_barcode );


		// 作成するファイル名の指定
		$fileName = "種蒔照会情報_" . date("Ymdhi") . ".csv";
		$buff = '';
		foreach ($csvData as $data) {
			$buff .= $data . "\r\n";
		}
		//foreach ($csvData2 as $data) {
		//	$buff .= $data . "\r\n";
		//}

		// CSVファイル出力
//		header("Content-disposition: attachment; filename=" . $fileName);
//		header("Content-type: application/octet-stream; name=" . $fileName);
		header("Content-disposition: attachment; filename=" . rawurlencode($fileName));
		header("Content-type: application/octet-stream; name=" . rawurlencode($fileName));
		print(mb_convert_encoding($buff, 'sjis-win', 'UTF-8'));
		exit();

		return;

	}

}

?>