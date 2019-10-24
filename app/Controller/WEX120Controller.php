<?php
/**
 * WPO040
 *
 * 工程管理画面
 *
 * @category      WPO
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

class WPO040Controller extends AppController {

	public $name = 'WPO040';
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
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['WPO']));
		//機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WPO']['040']['kino_nm']));

		//メッセージマスタモデル
		$this->MMessage = new MMessage($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//共通関数モデル
		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//分類マスタモデル
		$this->MBunrui = new MBunrui($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//WPO040モデル
		$this->WPO040Model = new WPO040Model($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),
				                     $this->Session->read('select_sosiki_cd'),
				                     $this->name
				                     );

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
				"initPopUp('".Router::url('/WPO040/popup_update', false)."');" .
				"initAddRow('".Router::url('/WPO040/popup_insert', false)."');" .
				"initUpdate('".Router::url('/WPO040/dataUpdate', false)."');" .
				"initBunrui();");

		//検索条件、ページID取得
		$ymd_syugyo = '';
		$kotei = '';
		$staff_nm = '';
		$error_flg = '';
		$pageID = 1;
		$return_cd = '';
		$display = '';
		$message = '';

		//検索条件
		if (isset($this->request->query['ymd_syugyo'])){
			$ymd_syugyo = $this->request->query['ymd_syugyo'];
			$kotei = $this->request->query['kotei'];
			$staff_nm = $this->request->query['staff_nm'];
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

			$this->Session->write('displayWPO040','true');

			if ($return_cd != "") {

				$this->redirect('/WPO040/index?return_cd=' . $return_cd .
											  '&message=' . $message .
											  '&ymd_syugyo=' . $ymd_syugyo .
											  '&kotei=' . $kotei .
											  '&staff_nm=' . $staff_nm .
											  '&error_flg=' . $error_flg .
											  '&pageID=' . $pageID .
											  '&display=false'
											  );

			} else {

				$this->redirect('/WPO040/index?&ymd_syugyo=' . $ymd_syugyo .
											  '&kotei=' . $kotei .
											  '&staff_nm=' . $staff_nm .
											  '&error_flg=' . $error_flg .
											  '&pageID=' . $pageID .
											  '&display=false'
											  );

			}

			return;
		} else if ($this->Session->check('displayWPO040') && $display == "false") {

			$this->Session->delete('displayWPO040');

		} else if (!$this->Session->check('displayWPO040') && $display == "false") {

			//検索必須条件
			if ($ymd_syugyo == "") {

				$this->redirect('/WPO040/index');

			} else {

				$this->redirect('/WPO040/index?ymd_syugyo=' . $ymd_syugyo .
											  '&kotei=' . $kotei .
											  '&staff_nm=' . $staff_nm .
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

		//検索条件の必須チェック
		if (!$this->Session->check('displayWPO0402') && isset($this->request->query['ymd_syugyo']) &&
		     $ymd_syugyo == '' && !isset($this->request->query['return_cd']) ) {


			//就業日付がないエラー
			$errorArray = array();
			$message = str_replace ("+","%2B",base64_encode ($this->MMessage->getOneMessage('WPOE040001')));


			$this->Session->write('displayWPO0402','true');

			$this->redirect('/WPO040/index?return_cd=1' .
										  '&message=' . $message .
										  '&ymd_syugyo=' . $ymd_syugyo .
										  '&kotei=' . $kotei .
										  '&staff_nm=' . $staff_nm .
										  '&error_flg=' . $error_flg .
										  '&pageID=' . $pageID .
										  '&display=true'
							  );



		} else {

			$this->Session->delete('displayWPO0402');
		}


		//検索条件がある場合検索
		if ($ymd_syugyo != '' || $kotei != '' || $staff_nm != '') {

			//必須条件がない場合
			if($ymd_syugyo == '' ) {

				//タイムスタムプ取得
				$this->set("timestamp", $this->WPO040Model->getTimestamp());

				return;
			}

			$lsts = $this->WPO040Model->getKoteiData($ymd_syugyo,$kotei ,$staff_nm, $error_flg);

			//ページャーの設定
			$pageNum = 50;

			$options = array(
					"totalItems" => count($lsts),
					"delta" => 10,
					"perPage" => $pageNum,
					"httpMethod" => "GET",
					"path" => "/DCMS/WPO040/index"
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
			if ($this->Session->check('saveTableWPO040')) {

				//前回のデータを取得
				$getArray = $this->Session->read('saveTableWPO040');

				//前回のデータを設置
				$data = $getArray->data;
				$timestamp = $getArray->timestamp;

				$this->set("timestamp", $timestamp);
				$this->set('lsts',$data);

				//データ構造が違うのでレンダリングを変更
				$this->render("index_error");

				//前回のデータを削除
				$this->Session->delete('saveTableWPO040');

			//エラーが発生していない場合は、検索条件を元に表示
			} else {

				//タイムスタムプ取得
				$this->set("timestamp", $this->WPO040Model->getTimestamp());
				$this->set('lsts',$arrayList);
			}

			//Viewへセット
			$this->set('index',$index);

		//条件が何もない場合
		} else {

			//前回の処理でエラーが発生した場合、前回のデータをそのまま表示
			if ($this->Session->check('saveTableWPO040')) {

				//前回のデータを取得
				$getArray = $this->Session->read('saveTableWPO040');

				//前回のデータを設置
				$data = $getArray->data;
				$timestamp = $getArray->timestamp;

				$this->set("timestamp", $timestamp);
				$this->set('lsts',$data);

				//データ構造が違うのでレンダリングを変更
				$this->render("index_error");

				//前回のデータを削除
				$this->Session->delete('saveTableWPO040');

			//エラーが発生していない場合は、検索条件を元に表示
			} else {

				//タイムスタムプ取得
				$this->set("timestamp", $this->WPO040Model->getTimestamp());
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
		$this->Session->write('saveTableWPO040',$getArray);

		//データを取り出す
		$data = $getArray->data;
		$timestamp = $getArray->timestamp;

		//画面取得時のタイムスタンプが最新か確認
//		if (!$this->WPO040Model->checkTimestamp($timestamp)) {
//
//			//エラー吐き出し
//			$array = $this->WPO040Model->errors;
//		    $message = '';
//
//		    foreach ( $array as $key=>$val ) {
//
//		    	foreach ($val as $key2=>$val2) {
//
//			    	$message = $message . $val2 . "<br>";
//		    	}
//		    }
//
//		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
//		    return;
//		}

		//入力チェック
		if (!$this->WPO040Model->checkKoteiData($data)) {

			//エラー吐き出し
			$array = $this->WPO040Model->errors;
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
		if (!$this->WPO040Model->setKoteiData($data,$this->Session->read('staff_cd'))) {

		    //エラー吐き出し
		    $array = $this->WPO040Model->errors;
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
		$this->Session->delete('saveTableWPO040');

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

		//大に分ける
		$opsions = $this->MBunrui->getBunruiDaiPDO(1);
		$this->set("koteiDaiList",$opsions);

		//中に分ける
		$opsions = $this->MBunrui->getBunruiCyuPDO(1);
		$this->set("koteiCyuList",$opsions);

		//細に分ける
		$opsions = $this->MBunrui->getBunruiSaiPDO(1);
		$this->set("koteiSaiList",$opsions);

		//車両
		$arrayList = $this->MMeisyo->getMeisyoPDO("41");
		$this->set("syaryoList",$arrayList);

		//エリア
		$arrayList = $this->MMeisyo->getMeisyoPDO("42");
		$this->set("areaList",$arrayList);

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

		//大に分ける
		$opsions = $this->MBunrui->getBunruiDaiPDO(1);
		$this->set("koteiDaiList",$opsions);

		//中に分ける
		$opsions = $this->MBunrui->getBunruiCyuPDO(1);
		$this->set("koteiCyuList",$opsions);

		//細に分ける
		$opsions = $this->MBunrui->getBunruiSaiPDO(1);
		$this->set("koteiSaiList",$opsions);


		//車両
		$arrayList = $this->MMeisyo->getMeisyoPDO("41");
		$this->set("syaryoList",$arrayList);

		//エリア
		$arrayList = $this->MMeisyo->getMeisyoPDO("42");
		$this->set("areaList",$arrayList);

	}

}
