<?php
/**
 * TPM160 Controller
 *
 * 工程配置進捗2(大高事業所用）TPM060ベース
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
App::uses('TPM160Model', 'Model');

class TPM160Controller extends AppController {

	public $name = 'TPM160';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['TPM']['160']['kino_nm']));

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

		$this->TPM160Model = new TPM160Model($this->Session->read('select_ninusi_cd'),
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
					"initCSV('".Router::url('/TPM160/csv', false). "');" .
					"initBunrui();");

		$ymd_from = '';
		$ymd_to = '';
		$kotei = '';
		$kotei2 = '';
		$timeFrom = '';
		$timeTo = '';
		$ninzu = '';
		$jikan = '';


		$pageID = 1;
		$return_cd = '';
		$display = '';
		$message = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['ymd_from'])){
			$ymd_from = $this->request->query['ymd_from'];
			$ymd_to = $this->request->query['ymd_to'];
			$kotei = $this->request->query['kotei'];
			$kotei2 = $this->request->query['kotei2'];
			$timeFrom = $this->request->query['timeFrom'];
			$timeTo = $this->request->query['timeTo'];
		}

		//パラメータ　検索条件を取得
		if (isset($this->request->query['ninzu'])){
			$ninzu = $this->request->query['ninzu'];
		}

		//パラメータ　検索条件を取得
		if (isset($this->request->query['jikan'])){
			$jikan = $this->request->query['jikan'];
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
			$ymd_from = $this->TPM160Model->getToday();
			$ymd_to = $ymd_from;
			$ninzu = 'on';
			$jikan = 'on';
		}

		//検索条件を保存
		$this->set("ymd_from",$ymd_from);
		$this->set("ymd_to",$ymd_to);

		if ($kotei != "") {
			$this->set("kotei",$kotei);
		}

		if ($kotei2 != "") {
			$this->set("kotei2",htmlspecialchars($kotei2));
		}

		if ($ninzu != "") {
			$this->set("ninzu",$ninzu);
		}

		if ($jikan != "") {
			$this->set("jikan",$jikan);
		}

		if ($timeTo != "") {
			$this->set("timeToHidden",$timeTo);
		}

		if ($timeFrom != "") {
			$this->set("timeFromHidden",$timeFrom);
		}


		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {

			$this->Session->write('displayTPM160','true');

			$this->redirect('/TPM160/index?return_cd=' . $return_cd .
										  '&message=' . $message .
										  '&ymd_from=' . $ymd_from .
										  '&ymd_to=' . $ymd_to .
										  '&kotei=' . $kotei .
										  '&kotei2=' . $kotei2 .
										  '&timeFrom=' . $timeFrom .
										  '&timeTo=' . $timeTo .
										  '&ninzu=' . $ninzu .
										  '&jikan=' . $jikan .
										  '&pageID=' . $pageID .
										  '&display=false'
										  );

			return;
		} else if ($this->Session->check('displayTPM160') && $display == "false") {

			$this->Session->delete('displayTPM160');

		} else if (!$this->Session->check('displayTPM160') && $display == "false") {

			$this->redirect('/TPM160/index?&ymd_from=' . $ymd_from .
										  '&ymd_to=' . $ymd_to .
										  '&kotei=' . $kotei .
										  '&kotei2=' . $kotei2 .
										  '&timeFrom=' . $timeFrom .
										  '&timeTo=' . $timeTo .
										  '&ninzu=' . $ninzu .
										  '&jikan=' . $jikan .
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

		//時間設定

		$lsts = array();
		$timeArray = $this->TPM160Model->getTimes();
		$this->set("timeFrom",$timeArray);
		$this->set("timeTo",$timeArray);

		if ($timeFrom != '' && $timeTo != '' ) {

			$from = (int)str_replace(":", "", $timeFrom);
			$to = (int)str_replace(":", "", $timeTo);

			$tempTimeArray = array();

			foreach ($timeArray as $array) {

				$time = (int)str_replace(":", "", $array);

				if ($from <= $time && $time <= $to) {
					$tempTimeArray[] = $array;
				}

			}

			$timeArray = $tempTimeArray;

		} else if ($timeFrom != '' && $timeTo == '' ) {

			$from = (int)str_replace(":", "", $timeFrom);

			$tempTimeArray = array();

			foreach ($timeArray as $array) {

				$time = (int)str_replace(":", "", $array);

				if ($from <= $time) {
					$tempTimeArray[] = $array;
				}

			}

			$timeArray = $tempTimeArray;

		} else if ($timeFrom == '' && $timeTo != '' ) {

			$to = (int)str_replace(":", "", $timeTo);

			$tempTimeArray = array();

			foreach ($timeArray as $array) {

				$time = (int)str_replace(":", "", $array);

				if ($time <= $to) {
					$tempTimeArray[] = $array;
				}

			}

			$timeArray = $tempTimeArray;

		}

		$this->set("timeList",$timeArray);




		//検索条件の必須チェック
		if (!$this->Session->check('displayTPM1602') && isset($this->request->query['ymd_from']) &&
				($ymd_from == '' || $ymd_to == '') && !isset($this->request->query['return_cd']) ) {


					//就業日付がないエラー
					$errorArray = array();
					$message = str_replace ("+","%2B",base64_encode ($this->MMessage->getOneMessage('TPME050001')));


					$this->Session->write('displayTPM1602','true');

					$this->redirect('/TPM160/index?return_cd=1' .
										  '&message=' . $message .
										  '&ymd_from=' . $ymd_from .
										  '&ymd_to=' . $ymd_to .
										  '&kotei=' . $kotei .
										  '&kotei2=' . $kotei2 .
										  '&timeFrom=' . $timeFrom .
										  '&timeTo=' . $timeTo .
										  '&ninzu=' . $ninzu .
										  '&jikan=' . $jikan .
										  '&pageID=' . $pageID .
										  '&display=true'
										  );



		} else {
				$this->Session->delete('displayTPM1602');
		}

		//検索条件がある場合検索
		if (($ymd_from != '' && $ymd_to != '') || $kotei != '' || $timeFrom || $timeTo) {

			//リスト一覧
			$lsts =  $this->TPM160Model->getKoteiList($ymd_from, $ymd_to, $kotei, $timeArray, $ninzu, $jikan, $kotei2);

			//ページャー
			$pageNum = 50;

			$options = array(
					"totalItems" => count($lsts),
					"delta" => 10,
					"perPage" => $pageNum,
					"httpMethod" => "GET",
					"path" => "/DCMS/TPM160/index"
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
			if ($this->Session->check('saveTableTPM160')) {

				//前回のデータを取得
				$getArray = $this->Session->read('saveTableTPM160');

				//前回のデータを設置
				$data = $getArray->data;
				$timestamp = $getArray->timestamp;

				$this->set("timestamp", $timestamp);
				$this->set('lsts',$data);

				//データ構造が違うのでレンダリングを変更
				$this->render("index_error");

				//前回のデータを削除
				$this->Session->delete('saveTableTPM160');

				//エラーが発生していない場合は、検索条件を元に表示
			} else {

				//タイムスタムプ取得
				$this->set("timestamp", $this->TPM160Model->getTimestamp());
				$this->set('lsts',$arrayList);
			}

			//Viewへセット
			$this->set('index',$index);


		//条件が何もない場合
		} else {

			//前回の処理でエラーが発生した場合、前回のデータをそのまま表示
			if ($this->Session->check('saveTableTPM160')) {

				//前回のデータを取得
				$getArray = $this->Session->read('saveTableTMP160');

				//前回のデータを設置
				$data = $getArray->data;
				$timestamp = $getArray->timestamp;

				$this->set("timestamp", $timestamp);
				$this->set('lsts',$data);

				//データ構造が違うのでレンダリングを変更
				$this->render("index_error");

				//前回のデータを削除
				$this->Session->delete('saveTableTPM160');

			//エラーが発生していない場合は、検索条件を元に表示
			} else {

				//タイムスタムプ取得
				$this->set("timestamp", $this->TPM160Model->getTimestamp());
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

		$YMD_FROM = '';
		$YMD_TO = '';
		$KOTEI = '';
		$timeFrom = '';
		$timeTo = '';
		$jikan = '';
		$ninzu = '';
		$kotei2 = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['YMD_FROM'])){
			$KOTEI = $this->request->query['KOTEI'];
			$YMD_FROM = $this->request->query['YMD_FROM'];
			$YMD_TO = $this->request->query['YMD_TO'];
			$timeFrom = $this->request->query['TIME_FROM'];
			$timeTo = $this->request->query['TIME_TO'];
			$jikan = $this->request->query['JIKAN'];
			$ninzu = $this->request->query['NINZU'];
			$kotei2 = $this->request->query['KOTEI2'];
		}

		$timeArray = $this->TPM160Model->getTimes();

		if ($timeFrom != '' && $timeTo != '' ) {

			$from = (int)str_replace(":", "", $timeFrom);
			$to = (int)str_replace(":", "", $timeTo);

			$tempTimeArray = array();

			foreach ($timeArray as $array) {

				$time = (int)str_replace(":", "", $array);

				if ($from <= $time && $time <= $to) {
					$tempTimeArray[] = $array;
				}

			}

			$timeArray = $tempTimeArray;

		} else if ($timeFrom != '' && $timeTo == '' ) {

			$from = (int)str_replace(":", "", $timeFrom);

			$tempTimeArray = array();

			foreach ($timeArray as $array) {

				$time = (int)str_replace(":", "", $array);

				if ($from <= $time) {
					$tempTimeArray[] = $array;
				}

			}

			$timeArray = $tempTimeArray;

		} else if ($timeFrom == '' && $timeTo != '' ) {

			$to = (int)str_replace(":", "", $timeTo);

			$tempTimeArray = array();

			foreach ($timeArray as $array) {

				$time = (int)str_replace(":", "", $array);

				if ($time <= $to) {
					$tempTimeArray[] = $array;
				}

			}

			$timeArray = $tempTimeArray;

		}

		// 工程実績情報CSVデータを取得
		$csvData = $this->TPM160Model->getCSV($YMD_FROM, $YMD_TO, $KOTEI, $timeArray, $ninzu, $jikan, $kotei2);

		// 作成するファイル名の指定
		$fileName = "工程実績（時間別）_" . date("Ymdhi") . ".csv";
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
