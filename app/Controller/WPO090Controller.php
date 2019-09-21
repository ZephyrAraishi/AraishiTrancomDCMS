<?php

/**
 * WPO090
 *
 * OZAX出荷指示データ追加取込取込画面
 *
 * @category      OXA
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('MMessage', 'Model');
App::uses('MMeisyo', 'Model');
App::uses('MCommon', 'Model');
App::uses('WPO090Model', 'Model');

class WPO090Controller extends AppController {

	public $name = 'WPO090';
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
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['WPO']));
		// 機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WPO']['080']['kino_nm']));

		//メッセージマスタ設定
		$this->MMessage = new MMessage($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//共通関数モデル
		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//名称マスタ設定
		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//WPO090モデル
		$this->WPO090Model = new WPO090Model($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

	}

	/**
	 * 初期表示
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function index(){

		//キャッシュクリア
		Cache::clear();

		require_once 'Pager.php';

		//Javascript設定
		$this->set("onload",
							"initCSV('".Router::url('/WPO090/CSV', false)."');" .
							"initUpdate('".Router::url('/WPO090/updateData', false)."');" .
							"initImport('".Router::url('/WPO090/importData', false)."');");

		$return_cd = '';
		$display = '';
		$message = '';
		$pageID = 1;

		$this->set("message", "");

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
			$this->set("message", base64_decode($message));
		}

		// 前回取り込みデータが残っている場合は表示
		$shukkaLst = $this->WPO090Model->loadIfShukka();
		$checkLst = $this->WPO090Model->loadCheckIfShukka();
		$this->set("checkLst", $checkLst);

		//ページャー
		$pageNum = 100;	// １ページあたり

		$options = array(
				"totalItems" => count($shukkaLst),
				"delta" => 10,
				"perPage" => $pageNum,
				"httpMethod" => "GET",
				"path" => "/DCMS/WPO090/index"
		);


		$pager = @Pager::factory($options);

		//ナビゲーションを設定
		$navi = $pager -> getLinks();
		$this->set('navi',$navi["all"]);


		//インデックスを取得
		$index = ($pageID - 1) * $pageNum;
		$arrayList = array();

		$limit = $index + $pageNum;

		if ($limit > count($shukkaLst)) {

			$limit = count($shukkaLst);
		}

		for($i = $index; $i < $limit ; $i++){
			$arrayList[] = $shukkaLst[$i];
		}
		$this->set("shukkaLst", $arrayList);

	}

	/**
	 * データをIF出荷指示データにインポートする
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function importData() {

		//データのやり取りだけなのでレンダリングなし
		$this->autoLayout = false;
		$this->autoRender = false;

		$counter = 0;
		$header = "";
		$isData = false;
		$handle = fopen('php://input','r');
		if($handle){
			while ($line = fgetcsv($handle)) {
				if ( !$isData ) {
					// 最初のセパレータ
					if ( strpos($line[0], "-----") === 0 ) {
						$header = $line[0];
					}
					// ファイルの開始かどうかを判定
					elseif ( $header != "" && $line[0] == "" ) {
						$isData = true;
					}
				} else {
					// 最後のセパレータ
					if ( strpos($line[0], $header) === 0 ) {
						break;
					}
					//elseif ( $line[0] != "" ) {
					else {
						$records[] = $line;
					}
				}
			}
		}
		fclose($handle);

		$staffCd = $this->Session->read('staff_cd');

		// 登録
		$this->WPO090Model->InsertIfShukka($records, $staffCd);

		$array = $this->WPO090Model->errors;
		if (!empty($array)) {
		    $message = '';
		    foreach ( $array as $key=>$val ) {
		    	foreach ($val as $key2=>$val2) {
			    	$message = $message . $val2 . "<br>";
		    	}
		    }
		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}

		//更新終了
		echo "return_cd=" . "0". "&message=";

	}

	/**
	 * IF出荷指示のデータを各テーブルに更新する
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function updateData() {

		//データのやり取りだけなのでレンダリングなし
		$this->autoLayout = false;
		$this->autoRender = false;

		$staffCd = $this->Session->read('staff_cd');

		// エラーチェック（IF出荷指示に１件でもエラーがあれば取り込まない）
		if (!$this->WPO090Model->checkIfShukka()) {
			$array = $this->WPO090Model->errors;
			if (!empty($array)) {
			    $message = '';
			    foreach ( $array as $key=>$val ) {
			    	foreach ($val as $key2=>$val2) {
				    	$message = $message . $val2 . "<br>";
			    	}
			    }
			    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
			    return;
			}
		}

		// 更新処理
		$this->WPO090Model->updateShukka($staffCd);

		$array = $this->WPO090Model->errors;
		if (!empty($array)) {
		    $message = '';
		    foreach ( $array as $key=>$val ) {
		    	foreach ($val as $key2=>$val2) {
			    	$message = $message . $val2 . "<br>";
		    	}
		    }
		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}

		//更新終了
		echo "return_cd=" . "0". "&message=";

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

		// トータルピッキング進捗情報CSVデータを取得
		$csvData = $this->WPO090Model->getCSV();

		// 作成するファイル名の指定
		$fileName = "ＩＦ出荷指示追加取込情報_" . date("Ymdhi") . ".csv";
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
