<?php
/**
 * TPM120
 *
 * 物量比較画面
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
App::uses('TPM120Model', 'Model');

class TPM120Controller extends AppController {

	public $name = 'TPM120';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['TPM']['120']['kino_nm']));

		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
		                             $this->Session->read('select_sosiki_cd'),
		                             $this->name
		                            );

		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),
				                     $this->Session->read('select_sosiki_cd'),
				                     $this->name
				                     );

		$this->TPM120Model = new TPM120Model($this->Session->read('select_ninusi_cd'),
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

		$ymd = '';
		
		$return_cd = '';
		$display = '';
		$message = '';
		
		
		//パラメータ　検索条件を取得
		if (isset($this->request->query['ymd'])){										
			$ymd = $this->request->query['ymd'];									//出荷日
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

		if ($ymd == '' ) {
			$ymd = $this->TPM120Model->getToday();
		}

		//検索条件を保存
		$this->set("ymd",$ymd);

		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {

			$this->Session->write('displayTPM120','true');

			$this->redirect('/TPM120/index?return_cd=' . $return_cd .
										  '&message=' . $message .
										  '&ymd=' . $ymd .
										  '&display=false'
			);

			return;
		} else if ($this->Session->check('displayTPM120') && $display == "false") {

			$this->Session->delete('displayTPM120');

		} else if (!$this->Session->check('displayTPM120') && $display == "false") {

			$this->redirect('/TPM120/index?&ymd=' . $ymd 
			);
			return;
		}
		

		//検索条件の必須チェック
		if (!$this->Session->check('displayTPM1202') && isset($this->request->query['ymd']) &&
				($ymd == '' ) && !isset($this->request->query['return_cd']) ) {

					//出荷日がないエラー
					$errorArray = array();
					$message = str_replace ("+","%2B",base64_encode ($this->MMessage->getOneMessage('TPME090001')));
					
					$this->Session->write('displayTPM1202','true');

					$this->redirect('/TPM120/index?return_cd=1' .
										 '&message=' . $message .
										 '&ymd=' . $ymd .
										 '&display=true'
					 );
		} else {
				$this->Session->delete('displayTPM1202');
		}

		//検索条件がある場合検索
		
		if ($ymd != '') {
		
		$w = date("w",strtotime($ymd));
			if ($w == 1){
				$ymd_m = date('Ymd',strtotime($ymd));
			}else{
				$ymd_m = date('Ymd', strtotime("last Monday", strtotime($ymd)));
			}
				$tue = date("Ymd", strtotime("$ymd_m +1 day"  ));
				$sun = date("Ymd", strtotime("$ymd_m +6 day"  ));
				$lmon = date("Ymd", strtotime("$ymd_m -7 day"  ));
				$ltue = date("Ymd", strtotime("$ymd_m -6 day"  ));
				$lsun = date("Ymd", strtotime("$ymd_m -1 day"  ));
			//リスト一覧
			$lsts =  $this->TPM120Model->getList($ymd_m,$ymd_m);			//得意先名,行数,個数（月曜日
			$lsts6 =  $this->TPM120Model->getList($lmon,$lmon);				//行数,個数（先週（月曜日
			
			$blsts =  $this->TPM120Model->getList($tue,$sun);				//得意先名,行数,個数（火曜日～
			$blsts6 =  $this->TPM120Model->getList($ltue,$lsun);			//行数,個数（先週（火曜日～
			
			$lkei['SHIJISU']=0;
			$lkei['S_SHIJISU']=0;
			$lkei['GYOSU']=0;
			$lkei['S_GYOSU']=0;
			$blkei['SHIJISU']=0;
			$blkei['S_SHIJISU']=0;
			$blkei['GYOSU']=0;
			$blkei['S_GYOSU']=0;
			
			//月曜日データの取得
			foreach($lsts as & $ar ){																//得意先名
			
			$ar['S_SHIJISU']=0;
			$ar['SHIJISU']=0;
			$ar['S_GYOSU']=0;
			
			$ar['SHIJISU']+=$ar["SHIJISU1"];											//指示数1
			$ar['SHIJISU']+=$ar["SHIJISU2"];											//指示数2
			$ar['SHIJISU']+=$ar["SHIJISU3"];											//指示数3
			
				foreach($lsts6 as &$ar6 ){
					if($ar["TOKUISAKI_CODE"] == $ar6["TOKUISAKI_CODE"]){
					
			  			$ar['S_GYOSU']=$ar6["GYOSU"];											//行数(先週
			  			$ar['S_SHIJISU']+=$ar6["SHIJISU1"];											//指示数1(先週
			  			$ar['S_SHIJISU']+=$ar6["SHIJISU2"];											//指示数2(先週
			  			$ar['S_SHIJISU']+=$ar6["SHIJISU3"];											//指示数3(先週
			  			break;
			  		}
			  	}
			  	if($ar["S_SHIJISU"] == 0){
			  		$ar['SYUKEI']=0;
			  	}else{
			  		$ar['SYUKEI']=floor($ar['SHIJISU']/$ar['S_SHIJISU']*1000)/10;
			  	}
			  	
			  	if($ar["S_GYOSU"] == 0){
			  		$ar['G_SYUKEI']=0;
			  	}else{
			  		$ar['G_SYUKEI']=floor($ar['GYOSU']/$ar['S_GYOSU']*1000)/10;
			  	}
				$lkei['SHIJISU']+=$ar['SHIJISU'];
				$lkei['S_SHIJISU']+=$ar['S_SHIJISU'];
				$lkei['GYOSU']+=$ar['GYOSU'];
				$lkei['S_GYOSU']+=$ar['S_GYOSU'];
			}
			//月曜日小計
				if($lkei["S_SHIJISU"] == 0){
					$lkei['SYUKEI']=0;
				}else{
					$lkei['SYUKEI']=floor($lkei['SHIJISU']/$lkei['S_SHIJISU']*1000)/10;
				}
				if($lkei["S_GYOSU"] == 0){
					$lkei['G_SYUKEI']=0;
				}else{
					$lkei['G_SYUKEI']=floor($lkei['GYOSU']/$lkei['S_GYOSU']*1000)/10;
				}
			//月曜日データここまで
			//火曜日データの取得
			foreach($blsts as & $bar ){																//得意先名
			
			$bar['S_SHIJISU']=0;
			$bar['SHIJISU']=0;
			$bar['S_GYOSU']=0;
			
			$bar['SHIJISU']+=$bar["SHIJISU1"];											//指示数1
			$bar['SHIJISU']+=$bar["SHIJISU2"];											//指示数2
			$bar['SHIJISU']+=$bar["SHIJISU3"];											//指示数3
			
				foreach($blsts6 as &$bar6 ){
					if($bar["TOKUISAKI_CODE"] == $bar6["TOKUISAKI_CODE"]){
					
			  			$bar['S_GYOSU']=$bar6["GYOSU"];											//行数(先週
			  			$bar['S_SHIJISU']+=$bar6["SHIJISU1"];									//指示数1(先週
			  			$bar['S_SHIJISU']+=$bar6["SHIJISU2"];									//指示数2(先週
			  			$bar['S_SHIJISU']+=$bar6["SHIJISU3"];									//指示数3(先週
			  			break;
			  		}
			  	}
			  	if($bar["S_SHIJISU"] == 0){
			  		$bar['SYUKEI']=0;
			  	}else{
			  		$bar['SYUKEI']=floor($bar['SHIJISU']/$bar['S_SHIJISU']*1000)/10;
			  	}
			  	
			  	if($bar["S_GYOSU"] == 0){
			  		$bar['G_SYUKEI']=0;
			  	}else{
			  		$bar['G_SYUKEI']=floor($bar['GYOSU']/$bar['S_GYOSU']*1000)/10;
			  	}
				$blkei['SHIJISU']+=$bar['SHIJISU'];
				$blkei['S_SHIJISU']+=$bar['S_SHIJISU'];
				$blkei['GYOSU']+=$bar['GYOSU'];
				$blkei['S_GYOSU']+=$bar['S_GYOSU'];
			}
			//火曜日小計
				if($blkei["S_SHIJISU"] == 0){
					$blkei['SYUKEI']=0;
				}else{
					$blkei['SYUKEI']=floor($blkei['SHIJISU']/$blkei['S_SHIJISU']*1000)/10;
				}
				if($blkei["S_GYOSU"] == 0){
					$blkei['G_SYUKEI']=0;
				}else{
					$blkei['G_SYUKEI']=floor($blkei['GYOSU']/$blkei['S_GYOSU']*1000)/10;
				}
			//火曜日データここまで
			$clkei['SHIJISU']=$lkei['SHIJISU']+$blkei['SHIJISU'];
			$clkei['S_SHIJISU']=$lkei['S_SHIJISU']+$blkei['S_SHIJISU'];
			$clkei['GYOSU']=$lkei['GYOSU']+$blkei['GYOSU'];
			$clkei['S_GYOSU']=$lkei['S_GYOSU']+$blkei['S_GYOSU'];
			
			if($clkei["S_SHIJISU"] == 0){
				$clkei['SYUKEI']=0;
			}else{
				$clkei['SYUKEI']=floor($clkei['SHIJISU']/$clkei['S_SHIJISU']*1000)/10;
			}
			if($clkei["S_GYOSU"] == 0){
				$clkei['G_SYUKEI']=0;
			}else{
				$clkei['G_SYUKEI']=floor($clkei['GYOSU']/$clkei['S_GYOSU']*1000)/10;
			}
			$this->set('lsts',$lsts);
			$this->set('lkei',$lkei);
			$this->set('blsts',$blsts);
			$this->set('blkei',$blkei);
			$this->set('clkei',$clkei);
			
		}
		
	}

}

?>