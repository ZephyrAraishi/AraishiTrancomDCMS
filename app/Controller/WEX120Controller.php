<?php
/**
 * WEX120
 *
 * 品質内容登録画面
 *
 * @category      WEX
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');
App::uses('MBunrui', 'Model');
App::uses('MMeisyo', 'Model');
App::uses('WPO040Model', 'Model');

class WEX120Controller extends AppController {

	public $name = 'WEX120';
	public $layout = "DCMS";

	/**
	 * 起動時処理
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function beforeFilter() {

		parent::beforeFilter();

		//システム名
		$this->set('system', Sanitize::stripAll($this->Session->read('system_name')));
		//サブシステム名
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['WEX']));
		//機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WEX']['120']['kino_nm']));

		//メッセージマスタモデル
		$this->MMessage = new MMessage($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//共通関数モデル
		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//分類マスタモデル
		$this->MBunrui = new MBunrui($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//WEX120モデル
		$this->WEX120Model = new WEX120Model($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//名称マスタモデル
		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

	}

	/**
	 * 初期表示
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function index(){

		//ライブラリ読み込み
		require_once 'Pager.php';

		//キャッシュクリア
		Cache::clear();

		//Javascript設定
		$this->set("onload","initReload();".
				"initPopUp('".Router::url('/WEX120/popup_update', false)."');" .
				"initAddRow('".Router::url('/WEX120/popup_insert', false)."');" .
				"initUpdate('".Router::url('/WEX120/dataUpdate', false)."');" .
				"initBunrui();");

		//検索条件、ページID取得
		$ymd_hassei = '';
		$kotei = '';
		$hassei_staff_nm = '';
		$taiou_staff_nm = '';
		$kbn_hinsitu_kanri = '';
		$kbn_hinsitu_naiyo = '';
		$error_flg = '';
		$pageID = 1;
		$return_cd = '';
		$display = '';
		$message = '';

		//検索条件
		if (isset($this->request->query['ymd_hassei'])){
			$ymd_syugyo = $this->request->query['ymd_hassei'];
		}
		if (isset($this->request->query['kotei'])){
			$kotei = $this->request->query['kotei'];
		}
		if (isset($this->request->query['hassei_staff_nm'])){
			$hassei_staff_nm = $this->request->query['hassei_staff_nm'];
		}
		if (isset($this->request->query['taiou_staff_nm'])){
			$taiou_staff_nm = $this->request->query['taiou_staff_nm'];
		}
		if (isset($this->request->query['kbn_hinsitu_kanri'])){
			$kbn_hinsitu_kanri = $this->request->query['kbn_hinsitu_kanri'];
		}
		if (isset($this->request->query['kbn_hinsitu_naiyo'])){
			$kbn_hinsitu_naiyo = $this->request->query['kbn_hinsitu_naiyo'];
		}

		//エラーフラグ
		if (isset($this->request->query['error_flg'])){
			$error_flg = $this->request->query['error_flg'];
		}

		//ページID
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

		//メッセージディスプレイフラグ
		if(isset($this->request->query['display'])) {
			$display = $this->request->query['display'];
		}

		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {

			$this->Session->write('displayWEX120','true');

			if ($return_cd != "") {

				$this->redirect('/WEX120/index?return_cd=' . $return_cd .
											  '&message=' . $message .
											  '&ymd_hassei=' . $ymd_hassei .
											  '&kotei=' . $kotei .
											  '&hassei_staff_nm=' . $hassei_staff_nm .
											  '&taiou_staff_nm=' . $taiou_staff_nm .
											  '&kbn_hinsitu_kanri=' . $kbn_hinsitu_kanri .
											  '&kbn_hinsitu_naiyo=' . $kbn_hinsitu_naiyo .
											  '&error_flg=' . $error_flg .
											  '&pageID=' . $pageID .
											  '&display=true'
											  );

			} else {

				$this->redirect('/WEX120/index?ymd_hassei=' . $ymd_hassei .
											  '&kotei=' . $kotei .
											  '&hassei_staff_nm=' . $hassei_staff_nm .
										      '&taiou_staff_nm=' . $taiou_staff_nm .
											  '&kbn_hinsitu_kanri=' . $kbn_hinsitu_kanri .
											  '&kbn_hinsitu_naiyo=' . $kbn_hinsitu_naiyo .
											  '&error_flg=' . $error_flg .
											  '&pageID=' . $pageID .
											  '&display=false'
											  );

			}

			return;
		} else if ($this->Session->check('displayWEX120') && $display == "false") {

			$this->Session->delete('displayWEX120');

		} else if (!$this->Session->check('displayWEX120') && $display == "false") {

			//検索必須条件
			if ($ymd_syugyo == "") {

				$this->redirect('/WEX120/index');

			} else {

				$this->redirect('/WEX120/index?ymd_hassei=' . $ymd_hassei .
										  	 '&kotei=' . $kotei .
											 '&hassei_staff_nm=' . $hassei_staff_nm .
											 '&taiou_staff_nm=' . $taiou_staff_nm .
											 '&kbn_hinsitu_kanri=' . $kbn_hinsitu_kanri .
											 '&kbn_hinsitu_naiyo=' . $kbn_hinsitu_naiyo .
											 '&error_flg=' . $error_flg .
											 '&pageID=' . $pageID
									         );

			}


			return;
		}

		//分類コンボ設定
		//大に分ける
		$opsions = $this->MBunrui->getBunruiDaiPDO(1);
		$this->set("koteiDaiList",$opsions);

		//中に分ける
		$opsions = $this->MBunrui->getBunruiCyuPDO(1);
		$this->set("koteiCyuList",$opsions);

		//細に分ける
		$opsions = $this->MBunrui->getBunruiSaiPDO(1);
		$this->set("koteiSaiList",$opsions);

		//名称マスタコンボ設定
		//品質管理区分
		$kbn_hinsitu_kanri = $this->MMeisyo->getMeisyo('44');
		$this->set('kbn_hinsitu_kanri', $kbn_hinsitu_kanri);

		//品質内容区分
		$kbn_hinsitu_naiyo = $this->MMeisyo->getMeisyo('45');
		$this->set('kbn_hinsitu_naiyo', $kbn_hinsitu_naiyo);

		//検索条件の必須チェック
		if (!$this->Session->check('displayWEX1202') && isset($this->request->query['ymd_hassei']) &&
		     $ymd_hassei == '' && !isset($this->request->query['return_cd']) ) {


			//就業日付がないエラー
			$errorArray = array();
			$message = str_replace ("+","%2B",base64_encode ($this->MMessage->getOneMessage('WEXE120001')));


			$this->Session->write('displayWEX1202','true');

			$this->redirect('/WEX120/index?return_cd=1' .
										  '&message=' . $message .
										  '&ymd_hassei=' . $ymd_hassei .
										  '&kotei=' . $kotei .
										  '&hassei_staff_nm=' . $hassei_staff_nm .
										  '&taiou_staff_nm=' . $taiou_staff_nm .
										  '&kbn_hinsitu_kanri=' . $kbn_hinsitu_kanri .
										  '&kbn_hinsitu_naiyo=' . $kbn_hinsitu_naiyo .
										  '&error_flg=' . $error_flg .
										  '&pageID=' . $pageID .
										  '&display=true'
							  );



		} else {

			$this->Session->delete('displayWEX1202');
		}


		//検索条件がある場合検索
		if ($ymd_hassei != '' || $kotei != '' || $hassei_staff_nm != '' || $taiou_staff_nm != '' || $kbn_hinsitu_kanri != '' || $kbn_hinsitu_naiyo != '') {

			//必須条件がない場合
			if($ymd_hassei == '' ) {

				//タイムスタムプ取得
				$this->set("timestamp", $this->WEX120Model->getTimestamp());

				return;
			}

			$lsts = $this->WEX120Model->getHinsitsuData($ymd_syugyo,$kotei ,$staff_nm, $error_flg);

			//ページャーの設定
			$pageNum = 50;

			$options = array(
					"totalItems" => count($lsts),
					"delta" => 10,
					"perPage" => $pageNum,
					"httpMethod" => "GET",
					"path" => "/DCMS/WEX120/index"
			);
			$pager = @Pager::factory($options);

			//ページナビゲーションを設定
			$navi = $pager -> getLinks();
			$this->set('navi',$navi["all"]);

			//項目のインデックスを取得
			$index = ($pageID - 1) * $pageNum;
			$arrayList = array();

			$limit = $index + $pageNum;

			if ($limit > count($lsts)) {
				$limit = count($lsts);
			}

			//インデックスから表示項目を取得
			for($i = $index; $i < $limit ; $i++){
				$arrayList[] = $lsts[$i];
			}

			//前回の処理でエラーが発生した場合、前回のデータをそのまま表示
			if ($this->Session->check('saveTableWEX120')) {

				//前回のデータを取得
				$getArray = $this->Session->read('saveTableWEX120');

				//前回のデータを設置
				$data = $getArray->data;
				$timestamp = $getArray->timestamp;

				$this->set("timestamp", $timestamp);
				$this->set('lsts',$data);

				//データ構造が違うのでレンダリングを変更
				$this->render("index_error");

				//前回のデータを削除
				$this->Session->delete('saveTableWEX120');

			//エラーが発生していない場合は、検索条件を元に表示
			} else {

				//タイムスタムプ取得
				$this->set("timestamp", $this->WEX120Model->getTimestamp());
				$this->set('lsts',$arrayList);
			}

			//Viewへセット
			$this->set('index',$index);

		//条件が何もない場合
		} else {

			//前回の処理でエラーが発生した場合、前回のデータをそのまま表示
			if ($this->Session->check('saveTableWEX120')) {

				//前回のデータを取得
				$getArray = $this->Session->read('saveTableWEX120');

				//前回のデータを設置
				$data = $getArray->data;
				$timestamp = $getArray->timestamp;

				$this->set("timestamp", $timestamp);
				$this->set('lsts',$data);

				//データ構造が違うのでレンダリングを変更
				$this->render("index_error");

				//前回のデータを削除
				$this->Session->delete('saveTableWEX120');

			//エラーが発生していない場合は、検索条件を元に表示
			} else {

				//タイムスタムプ取得
				$this->set("timestamp", $this->WEX120Model->getTimestamp());
			}

		}


	}

	/**
	 * 工程データ更新処理
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function dataUpdate(){

		//データのやり取りだけなのでレンダリングなし
		$this->autoLayout =false;
		$this->autoRender = false;

		//ポストデータの存在確認エラー
		if(!$this->request->is('post')) {
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000104'));
			return;
		}

		//データをJSONに変換する
		$getArray = json_decode($this->request->input());

		//データが空の場合、エラー
		if(count($getArray) == 0) {
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000101'));
			return;
		}

		//全データを一時保存
		$this->Session->write('saveTableWEX120',$getArray);

		//データを取り出す
		$data = $getArray->data;
		$timestamp = $getArray->timestamp;

		//入力チェック
		if (!$this->WEX120Model->checkHinsitsuData($data)) {

			//エラー吐き出し
			$array = $this->WEX120Model->errors;
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
		if (!$this->WEX120Model->setHinsitsuData($data,$this->Session->read('staff_cd'))) {

		    //エラー吐き出し
		    $array = $this->WEX120Model->errors;
		    $message = '';

		    foreach ( $array as $key=>$val ) {

		    	foreach ($val as $key2=>$val2) {
			    	$message = $message . $val2 . "<br>";
		    	}

		    }

		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message) ;
		    return;
		}

		//成功時は前回のテーブルデータを消す。
		$this->Session->delete('saveTableWEX120');

		//成功時メッセージ
		echo "return_cd=" . "0" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNI000001'));
	}

	/**
	 * 新規処理用ポップアップ取得
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function popup_insert() {

		//ポップアップデータだけ取得するため、レイアウトは無効にする
		$this->autoLayout = false;

		//分類マスタ設定
		//大に分ける
		$opsions = $this->MBunrui->getBunruiDaiPDO(1);
		$this->set("koteiDaiList",$opsions);

		//中に分ける
		$opsions = $this->MBunrui->getBunruiCyuPDO(1);
		$this->set("koteiCyuList",$opsions);

		//細に分ける
		$opsions = $this->MBunrui->getBunruiSaiPDO(1);
		$this->set("koteiSaiList",$opsions);

		//品質管理区分
		$arrayList = $this->MMeisyo->getMeisyoPDO("45");
		$this->set("hinshitukanriList",$arrayList);

		//品質内容区分
		$arrayList = $this->MMeisyo->getMeisyoPDO("46");
		$this->set("hinsitunaiyoList",$arrayList);

	}

	/**
	 * 更新処理用ポップアップ取得
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function popup_update() {

		//ポップアップデータだけ取得するため、レイアウトは無効にする
		$this->autoLayout = false;

		//分類マスタ設定
		//大に分ける
		$opsions = $this->MBunrui->getBunruiDaiPDO(1);
		$this->set("koteiDaiList",$opsions);

		//中に分ける
		$opsions = $this->MBunrui->getBunruiCyuPDO(1);
		$this->set("koteiCyuList",$opsions);

		//細に分ける
		$opsions = $this->MBunrui->getBunruiSaiPDO(1);
		$this->set("koteiSaiList",$opsions);


		//品質管理区分
		$arrayList = $this->MMeisyo->getMeisyoPDO("45");
		$this->set("hinshitukanriList",$arrayList);

		//品質内容区分
		$arrayList = $this->MMeisyo->getMeisyoPDO("46");
		$this->set("hinsitunaiyoList",$arrayList);

	}

}
