<?php

/**
 * TPM040
 *
 * 優先変更
 *
 * @category      TPM
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */
 
App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');
App::uses('TPM040Model', 'Model');
 
class TPM040Controller extends AppController {
	
	public $name = 'TPM040';
	public $layout = "DCMS";
	public $uses = array();	
	
	/**
	 * コントロール起動時処理
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function beforeFilter() {
	
		parent::beforeFilter();
	
		$this->set('system', Sanitize::stripAll($this->Session->read('system_name')));
		
		// サブシステム名
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['TPM']));
		
		// 機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['TPM']['040']['kino_nm']));
		
		//共通関数モデル
		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);
		
		//メッセージマスタ設定
		$this->MMessage = new MMessage($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);
		
		//TPM040モデル
		$this->TPM040Model = new TPM040Model($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'), $this->name);
		
	}

	/**
	 * 初期表示
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function index() {
	
		//キャッシュクリア
		Cache::clear();
		
		//Javascript設定
		$this->set("onload","initReload();" .
							"initPanel('".Router::url('/TPM040/popup', false)."');");
		
		
		$return_cd = '';
		$display = '';
		$message = '';

		
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
			
			$this->Session->write('displayTPM040','true');
			
			$this->redirect('/TPM040/index?return_cd=' . $return_cd . 
										  '&message=' . $message .
										  '&display=false'
										  );
			
			return;
		} else if ($this->Session->check('displayTPM040') && $display == "false") {
			
			$this->Session->delete('displayTPM040');
			
		} else if (!$this->Session->check('displayTPM040') && $display == "false") {
			
			$this->redirect('/TPM040/index');
			return;
		}
		
		//タイムスタンプ設定
		$this->set("timestamp",$this->TPM040Model->getTimestamp());
		
		//バッチのステータス設定
		$this->TPM040Model->setBATCH_STATUS($this->Session->read('staff_cd'));
		
		//TIME_ST取得
		$data_A =  $this->TPM040Model->getSORTER_KAISHI('A');
		$data_B =  $this->TPM040Model->getSORTER_KAISHI('B');
		$data_C =  $this->TPM040Model->getSORTER_KAISHI('C');
		$this->set('seisanseiA', $data_A['SEISANSEI']);
		$this->set('seisanseiB', $data_B['SEISANSEI']);
		$this->set('seisanseiC', $data_C['SEISANSEI']);
		
		
		$nowDate = date('Y/m/d H:i:s');
		
		if (strtotime($nowDate) > strtotime($data_A['STARTTIME'])) {
			$this->set('datetimeA', $nowDate);
			//データの更新
			if (!$this->TPM040Model->setDateTime($this->Session->read('staff_cd'),'A',$nowDate,$data_A['SEISANSEI'])) {
				
				//エラー吐き出し
				$array = $this->TPM040Model->errors;
			    $message = '';
			    
			    foreach ( $array as $key=>$val ) {
			    
			    	foreach ($val as $key2=>$val2) {
				    
				    	$message = $message . $val2 . "<br>";
			    	}
			    }
			    
			    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
			    return;
			}
		} else {
			$this->set('datetimeA', $data_A['STARTTIME']);
		}
		
		if (strtotime($nowDate) > strtotime($data_B['STARTTIME'])) {
			$this->set('datetimeB', $nowDate);
			if (!$this->TPM040Model->setDateTime($this->Session->read('staff_cd'),'B',$nowDate,$data_B['SEISANSEI'])) {
				
				//エラー吐き出し
				$array = $this->TPM040Model->errors;
			    $message = '';
			    
			    foreach ( $array as $key=>$val ) {
			    
			    	foreach ($val as $key2=>$val2) {
				    
				    	$message = $message . $val2 . "<br>";
			    	}
			    }
			    
			    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
			    return;
			}
		} else {
			$this->set('datetimeB', $data_B['STARTTIME']);
		}
		
		if (strtotime($nowDate) > strtotime($data_C['STARTTIME'])) {
			$this->set('datetimeC', $nowDate);
			if (!$this->TPM040Model->setDateTime($this->Session->read('staff_cd'),'C',$nowDate,$data_C['SEISANSEI'])) {
				
				//エラー吐き出し
				$array = $this->TPM040Model->errors;
			    $message = '';
			    
			    foreach ( $array as $key=>$val ) {
			    
			    	foreach ($val as $key2=>$val2) {
				    
				    	$message = $message . $val2 . "<br>";
			    	}
			    }
			    
			    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
			    return;
			}
		} else {
			$this->set('datetimeC', $data_C['STARTTIME']);
		}
		
		//ソーター名設定
		//A
		$sorterAArray = $this->TPM040Model->getSorter_Info('A');
		$soter_nm = $sorterAArray['sorter_nm'];
		$this->set('sorterNmA', $soter_nm);
		
		//B
		$sorterBArray = $this->TPM040Model->getSorter_Info('B');
		$soter_nm = $sorterBArray['sorter_nm'];
		$this->set('sorterNmB', $soter_nm);
		
		//C
		$sorterCArray = $this->TPM040Model->getSorter_Info('C');
		$soter_nm = $sorterCArray['sorter_nm'];
		$this->set('sorterNmC', $soter_nm);

		//ソーター毎のバッチリスト
		
		//A
		//ソーターAの未処理、処理中、処理済みリスト
		$arrayList = $this->TPM040Model->getSorterList('A');
		
		//ソーターAのリストを設置
		$this->set('sorterListA', $arrayList);
		
		//B
		//ソーターBの未処理、処理中、処理済みリスト
		$arrayList = $this->TPM040Model->getSorterList('B');
		
		//ソーターBのリストを設置
		$this->set('sorterListB', $arrayList);
		
		//C
		//ソーターCの未処理、処理中、処理済みリスト
		$arrayList = $this->TPM040Model->getSorterList('C');
		
		//ソーターCのリストを設置
		$this->set('sorterListC', $arrayList);
		
		//仮置き場
		$arrayList = $this->TPM040Model->getSorter_List_Kari();
		$this->set('sorterListKari', $arrayList);
		
	}
	
	/**
	 * 更新用画面表示
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function update() {
	
		//キャッシュクリア
		Cache::clear();
		
		//Javascript設定
		$this->set("onload","initReload();" . 
						 	"initPanel('".Router::url('/TPM040/popup', false)."');" . 
						 	"initUpdate('".Router::url('/TPM040/dataUpdate', false)."');");
		
		$return_cd = '';
		$display = '';
		$message = '';

		
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
			
			$this->Session->write('displayTPM040','true');
			
			$this->redirect('/TPM040/index?return_cd=' . $return_cd . 
										  '&message=' . $message .
										  '&display=false'
										  );
			
			return;
		} else if ($this->Session->check('displayTPM040') && $display == "false") {
			
			$this->Session->delete('displayTPM040');
			
		} else if (!$this->Session->check('displayTPM040') && $display == "false") {
			
			$this->redirect('/TPM040/index');
			return;
		}
		

		//タイムスタンプ設定
		$this->set("timestamp",$this->TPM040Model->getTimestamp());
		
		//バッチのステータス設定
		$this->TPM040Model->setBATCH_STATUS($this->Session->read('staff_cd'));
		
		//TIME_ST取得
		$data_A =  $this->TPM040Model->getSORTER_KAISHI('A');
		$data_B =  $this->TPM040Model->getSORTER_KAISHI('B');
		$data_C =  $this->TPM040Model->getSORTER_KAISHI('C');
		
		$this->set('datetimeA', $data_A['STARTTIME']);
		$this->set('datetimeB', $data_B['STARTTIME']);
		$this->set('datetimeC', $data_C['STARTTIME']);
		$this->set('seisanseiA', $data_A['SEISANSEI']);
		$this->set('seisanseiB', $data_B['SEISANSEI']);
		$this->set('seisanseiC', $data_C['SEISANSEI']);
		
		//ソーター名設定
		//A
		$sorterAArray = $this->TPM040Model->getSorter_Info('A');
		$soter_nm = $sorterAArray['sorter_nm'];
		$this->set('sorterNmA', $soter_nm);
		
		//B
		$sorterBArray = $this->TPM040Model->getSorter_Info('B');
		$soter_nm = $sorterBArray['sorter_nm'];
		$this->set('sorterNmB', $soter_nm);
		
		//C
		$sorterCArray = $this->TPM040Model->getSorter_Info('C');
		$soter_nm = $sorterCArray['sorter_nm'];
		$this->set('sorterNmC', $soter_nm);

		//ソーター毎のバッチリスト
		
		//A
		//ソーターAの未処理、処理中、処理済みリスト
		$arrayList = $this->TPM040Model->getSorterList('A');
		
		//ソーターAのリストを設置
		$this->set('sorterListA', $arrayList);
		
		//B
		//ソーターBの未処理、処理中、処理済みリスト
		$arrayList = $this->TPM040Model->getSorterList('B');
		
		//ソーターBのリストを設置
		$this->set('sorterListB', $arrayList);
		
		//C
		//ソーターCの未処理、処理中、処理済みリスト
		$arrayList = $this->TPM040Model->getSorterList('C');
		
		//ソーターCのリストを設置
		$this->set('sorterListC', $arrayList);
		
		//仮置き場
		$arrayList = $this->TPM040Model->getSorter_List_Kari();
		$this->set('sorterListKari', $arrayList);
	}
	
	/**
	 * バッチデータ更新処理
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function dataUpdate() {
		
		//データのやり取りだけなのでレンダリングなし
		$this->autoLayout = false;
		$this->autoRender = false;

		//ポストデータの存在確認エラー
		if(!$this->request->is('post')) {
			
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000104'));
			return;
		}
		
		//データを取得
		$data = json_decode($this->request->input());
		
		//データが空の場合、エラー
		if(count($data) == 0) {
			
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000101'));
			return;
		}
		
		//時間のデータ抜き出し
		$dateTimeA = $data->DateTimeA;
		$dateTimeB = $data->DateTimeB;
		$dateTimeC = $data->DateTimeC;
		$seisanseiA = $data->SeisanseiA;
		$seisanseiB = $data->SeisanseiB;
		$seisanseiC = $data->SeisanseiC;
		
		
		//ソータ毎のデータ抜き出し
		$sorterAList = $data->A;
		$sorterBList = $data->B;
		$sorterCList = $data->C;
		$sorterKList = $data->K;
		
		//並び替え
		usort( $sorterAList , array("TPM040Controller", "cmpDataList"));
		usort( $sorterBList , array("TPM040Controller", "cmpDataList"));
		usort( $sorterCList , array("TPM040Controller", "cmpDataList"));
		usort( $sorterKList , array("TPM040Controller", "cmpDataList"));

		//WPO010の排他確認
		if (!$this->TPM040Model->checkWPO010($this->Session->read('staff_cd'))) {
			
			//エラー吐き出し
			$array = $this->TPM040Model->errors;
		    $message = '';
		    
		    foreach ( $array as $key=>$val ) {
		    
		    	foreach ($val as $key2=>$val2) {
			    
			    	$message = $message . $val2 . "<br>";
		    	}
		    }
		    
		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}
		
		//WPO020の排他確認
		if (!$this->TPM040Model->checkWPO020($this->Session->read('staff_cd'))) {
			
			//エラー吐き出し
			$array = $this->TPM040Model->errors;
		    $message = '';
		    
		    foreach ( $array as $key=>$val ) {
		    
		    	foreach ($val as $key2=>$val2) {
			    
			    	$message = $message . $val2 . "<br>";
		    	}
		    }
		    
		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}

		//取得したデータが最新か確認
		if (!$this->TPM040Model->checkTimestamp($data->TimeStamp)) {
			
			//エラー吐き出し
			$array = $this->TPM040Model->errors;
		    $message = '';
		    
		    foreach ( $array as $key=>$val ) {
		    
		    	foreach ($val as $key2=>$val2) {
			    
			    	$message = $message . $val2 . "<br>";
		    	}
		    }
		    
		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}
		
		//データの更新
		if (!$this->TPM040Model->setDateTime($this->Session->read('staff_cd'),'A',$dateTimeA,$seisanseiA)) {
			
			//エラー吐き出し
			$array = $this->TPM040Model->errors;
		    $message = '';
		    
		    foreach ( $array as $key=>$val ) {
		    
		    	foreach ($val as $key2=>$val2) {
			    
			    	$message = $message . $val2 . "<br>";
		    	}
		    }
		    
		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}
		
		if (!$this->TPM040Model->setDateTime($this->Session->read('staff_cd'),'B',$dateTimeB,$seisanseiB)) {
			
			//エラー吐き出し
			$array = $this->TPM040Model->errors;
		    $message = '';
		    
		    foreach ( $array as $key=>$val ) {
		    
		    	foreach ($val as $key2=>$val2) {
			    
			    	$message = $message . $val2 . "<br>";
		    	}
		    }
		    
		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}
		
		if (!$this->TPM040Model->setDateTime($this->Session->read('staff_cd'),'C',$dateTimeC,$seisanseiC)) {
			
			//エラー吐き出し
			$array = $this->TPM040Model->errors;
		    $message = '';
		    
		    foreach ( $array as $key=>$val ) {
		    
		    	foreach ($val as $key2=>$val2) {
			    
			    	$message = $message . $val2 . "<br>";
		    	}
		    }
		    
		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}
		
		
		$arrayList = array_merge($sorterAList,$sorterBList,$sorterCList,$sorterKList);
		
		if (!$this->TPM040Model->setPRIORITY($this->Session->read('staff_cd'),$arrayList)) {
			
			//エラー吐き出し
			$array = $this->TPM040Model->errors;
		    $message = '';
		    
		    foreach ( $array as $key=>$val ) {
		    
		    	foreach ($val as $key2=>$val2) {
			    
			    	$message = $message . $val2 . "<br>";
		    	}
		    }
		    
		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}

		//成功時メッセージ
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
	 * オブジェクト比較
	 * 
	 * @access   public
	 * @param    オブジェクト１
	 * @param    オブジェクト２
	 * @return   １の表示順が同じ:1 先:-1 後:1
	 */
	static function cmpDataList($a,$b){
	
		$orderA = (int)$a->ORDER;
		$orderB = (int)$b->ORDER;
	
	    if ($orderA == $orderB) {
	        return 0;
	    }
	    
	    return ($orderA < $orderB) ? -1 : 1;
	}
  
	
}
