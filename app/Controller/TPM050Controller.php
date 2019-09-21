<?php
/**
 * TPM050
 *
 * 工程配置進捗
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
App::uses('MBunrui', 'Model');
App::uses('TPM050Model', 'Model');

class TPM050Controller extends AppController {

	public $name = 'TPM050';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['TPM']['050']['kino_nm']));

		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
		                             $this->Session->read('select_sosiki_cd'),
		                             $this->name
		                            );

		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),
				                     $this->Session->read('select_sosiki_cd'),
				                     $this->name
				                     );

		//分類マスタモデル
		$this->MBunrui = new MBunrui($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		$this->TPM050Model = new TPM050Model($this->Session->read('select_ninusi_cd'),
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
					"initPopUp('".Router::url('/TPM050/popup', false)."');" .
					"initUpdate('".Router::url('/TPM050/dataUpdate', false)."');" .
					"initCSV('".Router::url('/TPM050/csv', false)."','".Router::url('/TPM050/csv2', false) . "');" .
					"initBunrui();");

		$ymd_from = '';
		$ymd_to = '';
		$kotei = '';

		$pageID = 1;
		$return_cd = '';
		$display = '';
		$message = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['ymd_from'])){
			$ymd_from = $this->request->query['ymd_from'];
			$ymd_to = $this->request->query['ymd_to'];
			$kotei = $this->request->query['kotei'];
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
			$ymd_from = $this->TPM050Model->getToday();
			$ymd_to = $ymd_from;
		}

		//検索条件を保存
		$this->set("ymd_from",$ymd_from);
		$this->set("ymd_to",$ymd_to);

		if ($kotei != "") {
			$this->set("kotei",$kotei);
		}



		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {

			$this->Session->write('displayTPM050','true');

			$this->redirect('/TPM050/index?return_cd=' . $return_cd .
										  '&message=' . $message .
										  '&ymd_from=' . $ymd_from .
										  '&ymd_to=' . $ymd_to .
										  '&kotei=' . $kotei .
										  '&pageID=' . $pageID .
										  '&display=false'
										  );

			return;
		} else if ($this->Session->check('displayTPM050') && $display == "false") {

			$this->Session->delete('displayTPM050');

		} else if (!$this->Session->check('displayTPM050') && $display == "false") {

			$this->redirect('/TPM050/index?&ymd_from=' . $ymd_from .
										  '&ymd_to=' . $ymd_to .
										  '&kotei=' . $kotei .
										  '&pageID=' . $pageID
										  );
			return;
		}


		//分類コンボ設定

		//大に分ける
		$opsions = $this->MBunrui->getBunruiDaiPDO();
		$this->set("koteiDaiList",$opsions);

		//中に分ける
		$opsions = $this->MBunrui->getBunruiCyuPDO();
		$this->set("koteiCyuList",$opsions);

		$opsions = $this->MBunrui->getBunruiSaiPDO();
		$this->set("koteiSaiList",$opsions);

		$lsts = array();

		//検索条件の必須チェック
		if (!$this->Session->check('displayTPM0502') && isset($this->request->query['ymd_from']) &&
				($ymd_from == '' || $ymd_to == '') && !isset($this->request->query['return_cd']) ) {


					//就業日付がないエラー
					$errorArray = array();
					$message = str_replace ("+","%2B",base64_encode ($this->MMessage->getOneMessage('TPME050001')));


					$this->Session->write('displayTPM0502','true');

					$this->redirect('/TPM050/index?return_cd=1' .
										  '&message=' . $message .
										  '&ymd_from=' . $ymd_from .
										  '&ymd_to=' . $ymd_to .
										  '&kotei=' . $kotei .
										  '&pageID=' . $pageID .
										  '&display=true'
										  );



		} else {
				$this->Session->delete('displayTPM0502');
		}

		//検索条件がある場合検索
		if (($ymd_from != '' && $ymd_to != '') || $kotei != '') {

			//リスト一覧
			$lsts =  $this->TPM050Model->getKoteiList($ymd_from, $ymd_to, $kotei);

			//ページャー
			$pageNum = 50;

			$options = array(
					"totalItems" => count($lsts),
					"delta" => 10,
					"perPage" => $pageNum,
					"httpMethod" => "GET",
					"path" => "/DCMS/TPM050/index"
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
			if ($this->Session->check('saveTableTPM050')) {

				//前回のデータを取得
				$getArray = $this->Session->read('saveTableTPM050');

				//前回のデータを設置
				$data = $getArray->data;
				$timestamp = $getArray->timestamp;

				$this->set("timestamp", $timestamp);
				$this->set('lsts',$data);

				//データ構造が違うのでレンダリングを変更
				$this->render("index_error");

				//前回のデータを削除
				$this->Session->delete('saveTableTPM050');

				//エラーが発生していない場合は、検索条件を元に表示
			} else {

				//タイムスタムプ取得
				$this->set("timestamp", $this->TPM050Model->getTimestamp());
				$this->set('lsts',$arrayList);
			}

			//Viewへセット
			$this->set('index',$index);


		//条件が何もない場合
		} else {

			//前回の処理でエラーが発生した場合、前回のデータをそのまま表示
			if ($this->Session->check('saveTableTPM050')) {

				//前回のデータを取得
				$getArray = $this->Session->read('saveTableTPM050');

				//前回のデータを設置
				$data = $getArray->data;
				$timestamp = $getArray->timestamp;

				$this->set("timestamp", $timestamp);
				$this->set('lsts',$data);

				//データ構造が違うのでレンダリングを変更
				$this->render("index_error");

				//前回のデータを削除
				$this->Session->delete('saveTableTPM050');

			//エラーが発生していない場合は、検索条件を元に表示
			} else {

				//タイムスタムプ取得
				$this->set("timestamp", $this->TPM050Model->getTimestamp());
			}

		}

	}


	/**
	 * DB更新処理
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function dataUpdate() {



		$this->autoLayout = false;
		$this->autoRender = false;

		if(!$this->request->is('post')) {

			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000104'));
			return;
		}



		//データを取得
		$getArray = json_decode($this->request->input());

		if(count($getArray) == 0) {

			echo "return_cd=" . "90" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000101'));
			return;
		}


		//データを一時保存
		$this->Session->write('saveTableTPM050',$getArray);

		$data = $getArray->data;
		$timestamp = $getArray->timestamp;

		//タイムスタンプチェック
		if (!$this->TPM050Model->checkTimestamp($timestamp)) {

			$array = $this->TPM050Model->errors;
		    $message = '';

		    foreach ( $array as $key=>$val ) {

		    	foreach ($val as $key2=>$val2) {

			    	$message = $message . $val2 . "<br>";
		    	}
		    }

		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}

		//更新処理
		if (!$this->TPM050Model->setKoteiYosokuData($data, $this->Session->read('staff_cd'))) {


		    $array = $this->TPM050Model->errors;
		    $message = '';

		    foreach ( $array as $key=>$val ) {

		    	foreach ($val as $key2=>$val2) {

			    	$message = $message . $val2 . "<br>";
		    	}
		    }

		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message) ;
		    return;
		}

		//成功なのでテーブルをセッションから消す
		$this->Session->delete('saveTableTPM050');



		echo "return_cd=" . "0" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNI000001'));

	}




	/**
	 * 更新処理用ポップアップ取得
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function popup() {

		//ポップアップデータだけ取得するため、レイアウトは無効にする
		$this->autoLayout = false;

		$YMD_SYUGYO = '';
		$BNRI_DAI_CD = '';
		$BNRI_CYU_CD = '';
		$BNRI_SAI_CD = '';
		$YMD_FROM = '';
		$YMD_TO = '';


		//パラメータ　検索条件を取得
		if (isset($this->request->query['YMD_SYUGYO'])){
			$YMD_SYUGYO = $this->request->query['YMD_SYUGYO'];
			$BNRI_DAI_CD = $this->request->query['BNRI_DAI_CD'];
			$BNRI_CYU_CD = $this->request->query['BNRI_CYU_CD'];
			$BNRI_SAI_CD = $this->request->query['BNRI_SAI_CD'];
			$YMD_FROM = $this->request->query['YMD_FROM'];
			$YMD_TO = $this->request->query['YMD_TO'];
		}

		//リスト一覧
		$lsts =  $this->TPM050Model->getKoteiDetailList($YMD_SYUGYO, $BNRI_DAI_CD, $BNRI_CYU_CD, $BNRI_SAI_CD, $YMD_FROM, $YMD_TO);
		$this->set('lsts',$lsts);

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

		$YMD_FROM = '';
		$YMD_TO = '';
		$KOTEI = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['YMD_FROM'])){
			$KOTEI = $this->request->query['KOTEI'];
			$YMD_FROM = $this->request->query['YMD_FROM'];
			$YMD_TO = $this->request->query['YMD_TO'];
		}

		// 工程実績情報CSVデータを取得
		$csvData = $this->TPM050Model->getCSV($YMD_FROM, $YMD_TO, $KOTEI);

		// 作成するファイル名の指定
		$fileName = "スタッフ進捗_" . date("Ymdhi") . ".csv";
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
	 * CSV出力処理
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function csv2() {

		//ポップアップデータだけ取得するため、レイアウトは無効にする
		$this->autoLayout = false;

		$YMD_FROM = '';
		$YMD_TO = '';
		$KOTEI = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['YMD_FROM'])){
			$KOTEI = $this->request->query['KOTEI'];
			$YMD_FROM = $this->request->query['YMD_FROM'];
			$YMD_TO = $this->request->query['YMD_TO'];
		}

		// 工程実績情報CSVデータを取得
		$csvData = $this->TPM050Model->getCSV2($YMD_FROM, $YMD_TO, $KOTEI);

		// 作成するファイル名の指定
		$fileName = "スタッフ実績_" . date("Ymdhi") . ".csv";
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
