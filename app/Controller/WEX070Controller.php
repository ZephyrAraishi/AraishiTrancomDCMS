<?php
/**F
 * WEX070
 *
 * ABCデータベース登録
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
App::uses('WEX070Model', 'Model');

class WEX070Controller extends AppController {

	public $name = 'WEX070';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WEX']['070']['kino_nm']));
		
		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
		                             $this->Session->read('select_sosiki_cd'),
		                             $this->name
		                            );

		//分類マスタモデル
		$this->MBunrui = new MBunrui($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		$this->WEX070Model = new WEX070Model($this->Session->read('select_ninusi_cd'),
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
							"initFileUpload();" . 
							"initPopUp('".Router::url('/WEX070/popup', false)."');" .
							"initUpdate('".Router::url('/WEX070/dataUpdate', false)."');" . 
							"initBunrui();");
		
		$start_ymd_ins = '';
		$end_ymd_ins = '';
		$kotei = '';

		$bnri_dai_cd = '';
		$bnri_cyu_cd = '';
		$bnri_sai_cd = '';

		$pageID = 1;
		$return_cd = '';
		$display = '';
		$message = '';
		$search = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['start_ymd_ins'])){
			$start_ymd_ins = $this->request->query['start_ymd_ins'];
			$end_ymd_ins   = $this->request->query['end_ymd_ins'];
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
		
		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {
			
			$this->Session->write('displayWEX070','true');
			
			if ($return_cd != "") {

				$this->redirect('/WEX070/index?return_cd=' . $return_cd . 
											  '&message=' . $message .
											  '&start_ymd_ins=' . $start_ymd_ins . 
											  '&end_ymd_ins=' . $end_ymd_ins . 
											  '&kotei=' . $kotei . 
											  '&pageID=' . $pageID .
											  '&display=false'
											  );
										  
			} else {
											  
				$this->redirect('/WEX070/index?&start_ymd_ins=' . $start_ymd_ins . 
											  '&end_ymd_ins=' . $end_ymd_ins . 
											  '&kotei=' . $kotei . 
											  '&pageID=' . $pageID .
											  '&display=false'
											  );
			}
			
			return;
		} else if ($this->Session->check('displayWEX070') && $display == "false") {
			
			$this->Session->delete('displayWEX070');
			
		} else if (!$this->Session->check('displayWEX070') && $display == "false") {
			
			$this->redirect('/WEX070/index?&start_ymd_ins=' . $start_ymd_ins . 
										  '&end_ymd_ins=' . $end_ymd_ins . 
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
	    
	    //細に分ける
	    $opsions = $this->MBunrui->getBunruiSaiPDO();
		$this->set("koteiSaiList",$opsions);

		//入力チェック
		$inputCheckFlg = '0';

		$errorArray = array();

		if ($start_ymd_ins != "" && $end_ymd_ins != "") {
			
			//年月日（自）チェック
			if ($start_ymd_ins == '') {
				//ないエラー
				$errorArray['ymd_syugyo'][] = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array('年月日（自）'));
				$inputCheckFlg = '1';
			} else {
				//データフォーマットが不正
				if (preg_match("/^[0-9]{4}[0-9]{2}[0-9]{2}/",$start_ymd_ins) == false) {
					$errorArray['ymd_syugyo'][] = vsprintf($this->MMessage->getOneMessage('WEXE070010'), array('年月日（自）'));
					$inputCheckFlg = '1';
				}
					
				$year = substr($start_ymd_ins, 0,4);
				$month = substr($start_ymd_ins, 4,2);
				$day = substr($start_ymd_ins, 6,2);
					
				//不正
				if (checkdate($month, $day, $year) == false) {
					$errorArray['ymd_syugyo'][] = vsprintf($this->MMessage->getOneMessage('WEXE070010'), array('年月日（自）'));
					$inputCheckFlg = '1';
				}
			}
			
			
			//年月日（至）チェック
			if ($end_ymd_ins == '') {
				//ないエラー
				$errorArray['ymd_syugyo'][] = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array('年月日（至）'));
				$inputCheckFlg = '1';
			} else {
				//データフォーマットが不正
				if (preg_match("/^[0-9]{4}[0-9]{2}[0-9]{2}/",$end_ymd_ins) == false) {
					$errorArray['ymd_syugyo'][] = vsprintf($this->MMessage->getOneMessage('WEXE070010'), array('年月日（至）'));
					$inputCheckFlg = '1';
				}
					
				$year = substr($end_ymd_ins, 0,4);
				$month = substr($end_ymd_ins, 4,2);
				$day = substr($end_ymd_ins, 6,2);
					
				//不正
				if (checkdate($month, $day, $year) == false) {
					$errorArray['ymd_syugyo'][] = vsprintf($this->MMessage->getOneMessage('WEXE070010'), array('年月日（至）'));
					$inputCheckFlg = '1';
				}
			}
			
			
			//範囲チェック
			if ($inputCheckFlg == '0'
					&&  isset($this->request->query['start_ymd_ins'])
					&&  isset($this->request->query['end_ymd_ins']) ) {
				//範囲チェックエラー
				if ($start_ymd_ins > $end_ymd_ins ) {
					$errorArray['ymd_syugyo'][] = vsprintf($this->MMessage->getOneMessage('WEXE070011'), array('年月日（至）','年月日（自）'));
					$inputCheckFlg = '1';
				}
			}
				
			if ($inputCheckFlg != '0') {
				
				$message = '';
	    
			    foreach ( $errorArray as $key=>$val ) {
			    
			    	foreach ($val as $key2=>$val2) {
				    
				    	$message = $message . $val2 . "<br>";
				    	
			    	}
			    	
		
			    }
			    
			    $message = str_replace("+","%2B",base64_encode ($message));
			    
			    if (!$this->Session->check('displayWEX07001') && !isset($this->request->query['return_cd'])) {
			    
			    	$this->Session->write('displayWEX07001','true');
				
					$this->redirect('/WEX070/index?return_cd=1' . 
											  '&message=' . $message .
											  '&start_ymd_ins=' . $start_ymd_ins . 
											  '&end_ymd_ins=' . $end_ymd_ins . 
											  '&kotei=' . $kotei . 
											  '&pageID=' . $pageID .
											  '&display=true'
											  );
										  
					return;
				
				} else {
					
					$this->Session->delete('displayWEX07001');
					$this->Session->delete('saveTableWEX070');
					return;
				}
			}
			
		} else {
			
			$max_ymd = $this->WEX070Model->getTAbcDbLstMaxYMD();
			
			if ($return_cd != "") {

				$this->redirect('/WEX070/index?return_cd=' . $return_cd . 
											  '&message=' . $message .
											  '&start_ymd_ins=' . str_replace("-","",$max_ymd) . 
											  '&end_ymd_ins=' . str_replace("-","",$max_ymd) . 
											  '&kotei=' . $kotei . 
											  '&pageID=' . $pageID .
											  '&display=true'
											  );
										  
			} else {
				
				$this->redirect('/WEX070/index?&start_ymd_ins=' . str_replace("-","",$max_ymd) . 
											  '&end_ymd_ins=' . str_replace("-","",$max_ymd) . 
											  '&kotei=' . $kotei . 
											  '&pageID=' . $pageID .
											  '&display=true'
											  );
			}
			return;
		}
		
		
					
		$bnri_dai_cd = substr($kotei, 0,3);
		$bnri_cyu_cd = substr($kotei, 4,3);
		$bnri_sai_cd = substr($kotei, 8,3);
		
		$countResult = $this->WEX070Model->checMaxCount($bnri_dai_cd, $bnri_cyu_cd,  $bnri_sai_cd, 
		                                                $start_ymd_ins , $end_ymd_ins);
		
		if (!$countResult && !$this->Session->check('displayWEX07002')
		    && !isset($this->request->query['return_cd'])) {
			    
			    $this->Session->write('displayWEX07002','true');
		                                         
				$array = $this->WEX070Model->errors;
			    $message = '';
			    
			    foreach ( $array as $key=>$val ) {
			    
			    	foreach ($val as $key2=>$val2) {
				    
				    	$message = $message . $val2 . "<br>";
			    	}
			    	
		
			    }
			    
			    $message = str_replace("+","%2B",base64_encode ($message));
			
				$this->redirect('/WEX070/index?return_cd=1' . 
										  '&message=' . $message .
										  '&start_ymd_ins=' . $start_ymd_ins . 
										  '&end_ymd_ins=' . $end_ymd_ins . 
										  '&pageID=' . $pageID .
										  '&display=true'
										  );
									  
				return;
		
		} else if ($this->Session->check('displayWEX07002')) {
			
			$this->Session->delete('displayWEX07002');
			$this->Session->delete('saveTableWEX070');
			return;
		}

		//ABCデータベース一覧
		$lsts = $this->WEX070Model->getTAbcDbLst($bnri_dai_cd, $bnri_cyu_cd,  $bnri_sai_cd, 
		                                         $start_ymd_ins , $end_ymd_ins);

		//ページャー
		$pageNum = 50;
	
		$options = array(
				"totalItems" => count($lsts),
				"delta" => 10,
				"perPage" => $pageNum,
				"httpMethod" => "GET",
				"path" => "/DCMS/WEX070/index"
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

		//Viewへセット
		$this->set('index',$index);

		
		//エラーの場合
		if ($this->Session->check('saveTableWEX070')) {
		
			//データを取得
			$getArray = $this->Session->read('saveTableWEX070');
			
			$data = $getArray->data;
			$timestamp = $getArray->timestamp;
			
			$this->set("timestamp", $timestamp);
			$this->set('lsts',$data);
			$this->render("index_error");
			
			$this->Session->delete('saveTableWEX070');
		
		//エラー以外で条件付きの場合
		} else {
			
			/* タイムスタムプ取得 */
			$this->set("timestamp", $this->WEX070Model->getTimestamp());
			$this->set('lsts',$arrayList);
		}
	}
	
	
	/**
	 * FILE更新処理
	 * 
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function fileUpdate() {

		$this->autoLayout = false;
		$this->autoRender = false;
			

		//更新処理
		$this->WEX070Model->setAbcDbCsv($_FILES['csv'], $this->Session->read('staff_cd'));
		
		$array = $this->WEX070Model->errors;
	    $message1 = '';
	    $count = 0;
	    
	    foreach ( $array as $key=>$val ) {
	    
	    	foreach ($val as $key2=>$val2) {
		    
		    	$message1 = $message1 . $val2 . "<br>";
		    	
		    	$count++;
	    	}
	    	

	    }
		
		$array = $this->WEX070Model->infos;
	    $message2 = '';
	    $count = 0;
	    
	    foreach ( $array as $key=>$val ) {
	    
	    	foreach ($val as $key2=>$val2) {
		    
		    	$message2 = $message2 . $val2 . "<br>";
		    	
		    	$count++;
	    	}
	    	

	    }
	    
	    $message = '';
	    $return_cd = "0";
	    
	    if ($message1 != "") {
		    
		    $message = $message1;
		    $return_cd = "1";
	    } else {
		    $message = $message2;
		    $return_cd = "0";
	    }
	    
		
		$message = str_replace("+","%2B",base64_encode ($message));
		
		$max_ymd = str_replace("-","",$this->WEX070Model->getTAbcDbLstMaxYMD());
	
		$this->redirect('/WEX070/index?return_cd=' . $return_cd .
									  '&message=' . $message .
									  '&start_ymd_ins=' . $max_ymd . 
									  '&end_ymd_ins=' . $max_ymd .
									  '&kotei=' .
									  '&display=true'
									  );
		return;
		
		
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
		$this->Session->write('saveTableWEX070',$getArray);

		$data = $getArray->data;
		$timestamp = $getArray->timestamp;
		
		//タイムスタンプチェック
		if (!$this->WEX070Model->checkTimestamp($timestamp)) {
			
			$array = $this->WEX070Model->errors;
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
		if (!$this->WEX070Model->checkAbcDb($data, $this->Session->read('staff_cd'))) {

			$array = $this->WEX070Model->errors;
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
		if (!$this->WEX070Model->setAbcDb($data, $this->Session->read('staff_cd'))) {
		    
		    
		    $array = $this->WEX070Model->errors;
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
		$this->Session->delete('saveTableWEX070');


		
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

	}	


}
