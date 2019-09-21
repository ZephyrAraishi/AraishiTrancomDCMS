<?php
/**
 * WEX050
 *
 * 細分類マスタ設定
 *
 * @category      WEX
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');
App::uses('MMeisyo', 'Model');
App::uses('MBunrui', 'Model');
App::uses('WEX050Model', 'Model');

class WEX050Controller extends AppController {

	public $name = 'WEX050';
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
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['WEX']));
		// 機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WEX']['050']['kino_nm']));
		
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

		$this->WEX050Model = new WEX050Model($this->Session->read('select_ninusi_cd'),
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
	
		//ライブラリ読み込み
		require_once 'Pager.php';

		//キャッシュクリア
		Cache::clear();

		//Javascript設定
		$this->set("onload","initReload();".
							"initPopUp('".Router::url('/WEX050/popup', false)."');" .
							"initAddRow('".Router::url('/WEX050/popup_insert', false)."');" .
							"initUpdate('".Router::url('/WEX050/dataUpdate', false)."');" . 
							"initBunrui();");
		
		$bnri_dai_cd = '';
		$bnri_cyu_cd = '';

		$kotei = '';
		$bnri_sai_cd = '';
		$bnri_sai_nm = '';
		$kbn_tani = '';
		$kbn_ktd_mktk = '';
		$kbn_hissu_wk = '';
		$kbn_gyomu = '';
		$kbn_get_data = '';
		$kbn_fukakachi = '';
		$kbn_senmon = '';
		$kbn_ktd_type = '';
		$keiyaku_buturyo_flg = '';
		
		$pageID = 1;
		$return_cd = '';
		$display = '';
		$message = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['bnri_sai_cd'])){
			$kotei = $this->request->query['kotei'];
			$bnri_sai_cd = $this->request->query['bnri_sai_cd'];
			$bnri_sai_nm = $this->request->query['bnri_sai_nm'];

			$kbn_tani      = $this->request->query['kbn_tani'];
			$kbn_ktd_mktk  = $this->request->query['kbn_ktd_mktk'];
			$kbn_hissu_wk  = $this->request->query['kbn_hissu_wk'];
			$kbn_gyomu     = $this->request->query['kbn_gyomu'];
			$kbn_get_data  = $this->request->query['kbn_get_data'];
			$kbn_fukakachi = $this->request->query['kbn_fukakachi'];
			$kbn_senmon    = $this->request->query['kbn_senmon'];
			$kbn_ktd_type  = $this->request->query['kbn_ktd_type'];
			$keiyaku_buturyo_flg  = $this->request->query['keiyaku_buturyo_flg'];

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
		
		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {
			
			$this->Session->write('displayWEX050','true');
			
			$this->redirect('/WEX050/index?return_cd=' . $return_cd . 
										  '&message=' . $message .
										  '&kotei=' . $kotei . 
										  '&bnri_sai_cd=' . $bnri_sai_cd . 
										  '&bnri_sai_nm=' . $bnri_sai_nm . 
										  '&kbn_tani=' . $kbn_tani . 
										  '&kbn_ktd_mktk=' . $kbn_ktd_mktk . 
										  '&kbn_hissu_wk=' . $kbn_hissu_wk . 
										  '&kbn_gyomu=' . $kbn_gyomu . 
										  '&kbn_get_data=' . $kbn_get_data . 
										  '&kbn_fukakachi=' . $kbn_fukakachi . 
										  '&kbn_senmon=' . $kbn_senmon . 
										  '&kbn_ktd_type=' . $kbn_ktd_type . 
										  '&keiyaku_buturyo_flg=' . $keiyaku_buturyo_flg . 
										  '&pageID=' . $pageID .
										  '&display=false'
										  );
			
			return;
		} else if ($this->Session->check('displayWEX050') && $display == "false") {
			
			$this->Session->delete('displayWEX050');
			
		} else if (!$this->Session->check('displayWEX050') && $display == "false") {
			
			$this->redirect('/WEX050/index?&kotei=' . $kotei . 
										  '&bnri_sai_cd=' . $bnri_sai_cd . 
										  '&bnri_sai_nm=' . $bnri_sai_nm . 
										  '&kbn_tani=' . $kbn_tani . 
										  '&kbn_ktd_mktk=' . $kbn_ktd_mktk . 
										  '&kbn_hissu_wk=' . $kbn_hissu_wk . 
										  '&kbn_gyomu=' . $kbn_gyomu . 
										  '&kbn_get_data=' . $kbn_get_data . 
										  '&kbn_fukakachi=' . $kbn_fukakachi . 
										  '&kbn_senmon=' . $kbn_senmon . 
										  '&kbn_ktd_type=' . $kbn_ktd_type . 
										  '&keiyaku_buturyo_flg=' . $keiyaku_buturyo_flg . 
										  '&pageID=' . $pageID
										  );
			return;
		}


		$bnri_dai_cd = substr($kotei, 0,3);
		$bnri_cyu_cd = substr($kotei, 4,3);
		
		//細分類マスタ一覧
		$lsts = $this->WEX050Model->getMBnriSaiLst($bnri_dai_cd, $bnri_cyu_cd,  $bnri_sai_cd, $bnri_sai_nm, 
		                                           $kbn_tani,    $kbn_ktd_mktk, $kbn_hissu_wk, 
		                                           $kbn_gyomu,   $kbn_get_data, $kbn_fukakachi, 
		                                           $kbn_senmon , $kbn_ktd_type, $keiyaku_buturyo_flg);

		//ページャー
		$pageNum = 50;
		
		$options = array(
				"totalItems" => count($lsts),
				"delta" => 10,
				"perPage" => $pageNum,
				"httpMethod" => "GET",
				"path" => "/DCMS/WEX050/index"
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


		//分類コンボ設定
	    
	    //大に分ける
	    $opsions = $this->MBunrui->getBunruiDaiPDO();
	    $this->set("koteiDaiList",$opsions);
	    
	    //中に分ける
	    $opsions = $this->MBunrui->getBunruiCyuPDO();
	    $this->set("koteiCyuList",$opsions);
	    
	    $opsions = $this->MBunrui->getBunruiSaiPDO();
	    $this->set("koteiSaiList",$opsions);
	    

		//単位区分
		$kbnTanis = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.tani'));
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnTanis', $kbnTanis);

		//活動目的
		$kbnKtdMktks = $this->MMeisyo->getMeisyo("14");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnKtdMktks', $kbnKtdMktks);

		//必須作業
		$kbnHissuWks = $this->MMeisyo->getMeisyo("07");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnHissuWks', $kbnHissuWks);

		//業務区分
		$kbnGyomus = $this->MMeisyo->getMeisyo("08");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnGyomus', $kbnGyomus);

		//データ取得区分
		$kbnGetDatas = $this->MMeisyo->getMeisyo("09");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnGetDatas', $kbnGetDatas);

		//付加価値性
		$kbnFukakachis = $this->MMeisyo->getMeisyo("12");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnFukakachis', $kbnFukakachis);

		//専門区分
		$kbnSenmons = $this->MMeisyo->getMeisyo("10");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnSenmons', $kbnSenmons);

		//活動タイプ
		$kbnKtdTypes = $this->MMeisyo->getMeisyo("15");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnKtdTypes', $kbnKtdTypes);

		//契約単位代表物量フラグ
		$keiyakuButuryoFlgs = $this->MMeisyo->getMeisyo("29");
		// 一覧情報を一時的にセッションに保持
		$this->set('keiyakuButuryoFlgs', $keiyakuButuryoFlgs);
		
		//Viewへセット
		$this->set('index',$index);

		
		//エラーの場合
		if ($this->Session->check('saveTableWEX050')) {
		
			//データを取得
			$getArray = $this->Session->read('saveTableWEX050');
			
			$data = $getArray->data;
			$timestamp = $getArray->timestamp;
			
			$this->set("timestamp", $timestamp);
			$this->set('lsts',$data);
			$this->render("index_error");
			
			$this->Session->delete('saveTableWEX050');
		
		//エラー以外で条件付きの場合
		} else {
			
			/* タイムスタムプ取得 */
			$this->set("timestamp", $this->MCommon->getTimestamp());
			$this->set('lsts',$arrayList);
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
		$this->Session->write('saveTableWEX050',$getArray);

		$data = $getArray->data;
		$timestamp = $getArray->timestamp;
		
		//タイムスタンプチェック
		if (!$this->WEX050Model->checkTimestamp($timestamp)) {
			
			$array = $this->WEX050Model->errors;
		    $message = '';
		    
		    foreach ( $array as $key=>$val ) {
		    
		    	foreach ($val as $key2=>$val2) {
			    
			    	$message = $message . $val2 . "<br>";
		    	}
		    }
		    
		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}

		//必須チェック
//		if (!$this->WEX040Model->checkSaiMst($data)) {
		$abc = $this->WEX050Model->checkSaiMst($data);
			
		$array = $this->WEX050Model->errors;

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

		//更新処理
		if (!$this->WEX050Model->setSaiMst($data, $this->Session->read('staff_cd'))) {
		    
		    
		    $array = $this->WEX050Model->errors;
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
		$this->Session->delete('saveTableWEX050');


		
		echo "return_cd=" . "0" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNI000001'));

	}
	
	


	/**
	 * ポップアップ取得
	 * 
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function popup() {
	
		$this->autoLayout = false;
				
		//単位区分
		$kbnTanis = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.tani'));
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnTanis', $kbnTanis);

		//活動目的
		$kbnKtdMktks = $this->MMeisyo->getMeisyo("14");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnKtdMktks', $kbnKtdMktks);

		//必須作業
		$kbnHissuWks = $this->MMeisyo->getMeisyo("07");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnHissuWks', $kbnHissuWks);

		//業務区分
		$kbnGyomus = $this->MMeisyo->getMeisyo("08");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnGyomus', $kbnGyomus);

		//データ取得区分
		$kbnGetDatas = $this->MMeisyo->getMeisyo("09");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnGetDatas', $kbnGetDatas);

		//付加価値性
		$kbnFukakachis = $this->MMeisyo->getMeisyo("12");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnFukakachis', $kbnFukakachis);

		//専門区分
		$kbnSenmons = $this->MMeisyo->getMeisyo("10");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnSenmons', $kbnSenmons);

		//活動タイプ
		$kbnKtdTypes = $this->MMeisyo->getMeisyo("15");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnKtdTypes', $kbnKtdTypes);

		//契約単位代表物量フラグ
		$keiyakuButuryoFlgs = $this->MMeisyo->getMeisyo("29");
		// 一覧情報を一時的にセッションに保持
		$this->set('keiyakuButuryoFlgs', $keiyakuButuryoFlgs);
	}	


	/**
	 * ポップアップ取得
	 * 
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function popup_insert() {
	
		$this->autoLayout = false;
				


		//分類コンボ設定
	    
	    //大に分ける
	    $opsions = $this->MBunrui->getBunruiDaiPDO();
	    $this->set("koteiDaiList",$opsions);
	    
	    //中に分ける
	    $opsions = $this->MBunrui->getBunruiCyuPDO();
	    $this->set("koteiCyuList",$opsions);
	    
	    //細に分ける
	    $opsions = $this->MBunrui->getBunruiSaiPDO();
		$this->set("koteiSaiList",$opsions);




		//単位区分
		$kbnTanis = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.tani'));
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnTanis', $kbnTanis);

		//活動目的
		$kbnKtdMktks = $this->MMeisyo->getMeisyo("14");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnKtdMktks', $kbnKtdMktks);

		//必須作業
		$kbnHissuWks = $this->MMeisyo->getMeisyo("07");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnHissuWks', $kbnHissuWks);

		//業務区分
		$kbnGyomus = $this->MMeisyo->getMeisyo("08");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnGyomus', $kbnGyomus);

		//データ取得区分
		$kbnGetDatas = $this->MMeisyo->getMeisyo("09");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnGetDatas', $kbnGetDatas);

		//付加価値性
		$kbnFukakachis = $this->MMeisyo->getMeisyo("12");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnFukakachis', $kbnFukakachis);

		//専門区分
		$kbnSenmons = $this->MMeisyo->getMeisyo("10");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnSenmons', $kbnSenmons);

		//活動タイプ
		$kbnKtdTypes = $this->MMeisyo->getMeisyo("15");
		// 一覧情報を一時的にセッションに保持
		$this->set('kbnKtdTypes', $kbnKtdTypes);

		//契約単位代表物量フラグ
		$keiyakuButuryoFlgs = $this->MMeisyo->getMeisyo("29");
		// 一覧情報を一時的にセッションに保持
		$this->set('keiyakuButuryoFlgs', $keiyakuButuryoFlgs);
	}	


}
