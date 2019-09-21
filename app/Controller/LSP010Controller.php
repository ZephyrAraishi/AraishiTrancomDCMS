<?php
/**
 * LSP010
 *
 * 勤務情報設定
 *
 * @category      LSP
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');
App::uses('MMeisyo', 'Model');
App::uses('MBunrui', 'Model');
App::uses('LSP010Model', 'Model');

class LSP010Controller extends AppController {

	public $name = 'LSP010';
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
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['LSP']));
		// 機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['LSP']['010']['kino_nm']));
		
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

		$this->LSP010Model = new LSP010Model($this->Session->read('select_ninusi_cd'),
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
	
		//キャッシュクリア
		Cache::clear();

		//Javascript設定
		$this->set("onload","initReload();".
							"initPopUp('".Router::url('/LSP010/popup', false)."');" .
							"initAddRow('".Router::url('/LSP010/popup_insert', false)."');" .
							"initUpdate('".Router::url('/LSP010/dataUpdate', false)."');" . 
							"initCopy('".Router::url('/LSP010/dataCopy', false)."');" . 
							"initBunrui();");
		
		$kotei = '';
		$sel_date = '';
		$set_date = '';
		$staff_cd_hidden = '';
		$staff_nm_hidden = '';

		$return_cd = '';
		$display = '';
		$message = '';

		// 表示モードを設定
		$mode = '';
		if (isset($this->request->query['mode'])) {
			$mode = $this->request->query['mode'];
		}
		$this->set("mode",$mode);
		if ($mode == "1") {
			$this->set("nonDispLogOut", "1");
			$this->set("nonDispMenu", "1");
			$this->set("nonSysLink", "1");
		}
		
		//パラメータ　検索条件を取得
		if (isset($this->request->query['sel_date'])){
			$kotei = $this->request->query['kotei'];
			$sel_date        = $this->request->query['sel_date'];
			if (isset($this->request->query['set_date'])) {
				$set_date        = $this->request->query['set_date'];
			}
			$staff_cd_hidden = $this->request->query['staff_cd_hidden'];
			$staff_nm_hidden = $this->request->query['staff_nm_hidden'];

			if ($sel_date == "") {
				$sel_date = 0;
				$set_date = "";
			}
			if ($set_date == "") {
				$set_date = "";
			}

		} else {
			//初めて開いた時の日付初期値
			$sel_date      = 0;
			$set_date      = "";
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

//		} else {
//
//			//検索とみなして、無理やりクリア
//			$set_date = "";
//
		}
		
		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {
			
			$this->Session->write('displayLSP010','true');
			
			$this->redirect('/LSP010/index?return_cd=' . $return_cd . 
										  '&message=' . $message .
										  '&kotei=' . $kotei . 
										  '&staff_cd_hidden=' . $staff_cd_hidden . 
										  '&staff_nm_hidden=' . $staff_nm_hidden . 
										  '&sel_date=' . $sel_date . 
										  '&set_date=' . $set_date . 
										  '&mode=' . $mode . 
										  '&display=false'
										  );
			
			return;
		} else if ($this->Session->check('displayLSP010') && $display == "false") {
			
			$this->Session->delete('displayLSP010');
			
		} else if (!$this->Session->check('displayLSP010') && $display == "false") {
			
			$this->redirect('/LSP010/index?&kotei=' . $kotei . 
										  '&staff_cd_hidden=' . $staff_cd_hidden . 
										  '&staff_nm_hidden=' . $staff_nm_hidden . 
										  '&mode=' . $mode . 
										  '&sel_date=' . $sel_date . 
										  '&set_date=' . $set_date 
										  );
			return;
		}


		//分類コンボ設定
	    
	    //大に分ける
	    $opsions = $this->MBunrui->getBunruiDaiPDO(1);
	    $this->set("koteiDaiList",$opsions);
	    
	    //中に分ける
	    $opsions = $this->MBunrui->getBunruiCyuPDO(1);
	    $this->set("koteiCyuList",$opsions);
	    

		//先頭曜日区分
		$kbnYoubi = $this->MMeisyo->getMeisyo('18');

		//	当週の指定曜日を取得
		$befCnt = 0;
		while (date("w",strtotime($befCnt . " day")) != $kbnYoubi['01']) {
			$befCnt = $befCnt - 1;
		}

		//	前　1か月の各週先頭を取得
		$selDate = array();

		$selDate[-4] = date("Y/m/d",strtotime($befCnt - 28 . " day"));
		$selDate[-3] = date("Y/m/d",strtotime($befCnt - 21 . " day"));
		$selDate[-2] = date("Y/m/d",strtotime($befCnt - 14 . " day"));
		$selDate[-1] = date("Y/m/d",strtotime($befCnt -  7 . " day"));

		$selDate[0] = date("Y/m/d",strtotime($befCnt -  0 . " day"));
		$selDate[1] = date("Y/m/d",strtotime($befCnt +  7 . " day"));
		$selDate[2] = date("Y/m/d",strtotime($befCnt + 14 . " day"));
		$selDate[3] = date("Y/m/d",strtotime($befCnt + 21 . " day"));
		$selDate[4] = date("Y/m/d",strtotime($befCnt + 28 . " day"));
		$selDate[5] = date("Y/m/d",strtotime($befCnt + 35 . " day"));

		//	前後　1か月の各週先頭を取得
		$setDate = array();
		$setDate[-4] = date("Y/m/d",strtotime($befCnt - 28 . " day"));
		$setDate[-3] = date("Y/m/d",strtotime($befCnt - 21 . " day"));
		$setDate[-2] = date("Y/m/d",strtotime($befCnt - 14 . " day"));
		$setDate[-1] = date("Y/m/d",strtotime($befCnt -  7 . " day"));
		$setDate[0] = date("Y/m/d",strtotime($befCnt  . " day"));
		$setDate[1] = date("Y/m/d",strtotime($befCnt +  7 . " day"));
		$setDate[2] = date("Y/m/d",strtotime($befCnt + 14 . " day"));
		$setDate[3] = date("Y/m/d",strtotime($befCnt + 21 . " day"));
		$setDate[4] = date("Y/m/d",strtotime($befCnt + 28 . " day"));
		$setDate[5] = date("Y/m/d",strtotime($befCnt + 35 . " day"));

		//	VIEWへセット
		$this->set('selDate',$selDate);
		$this->set('setDate',$setDate);
		//	VIEWへセット END

		
		/* ******* LSP予測シュミレーション（LSP020）からの呼び出し時 start */
		
		// 対象日付を設定
		$sel_date_lsp020 = '';
		if (isset($this->request->query['target_ymd'])) {
			$ymd = $this->request->query['target_ymd'];
		
			// 指定日付の週開始日付を算出
			$dayOfWeek = date("w", mktime(0, 0, 0, substr($ymd, 4, 2), substr($ymd, 6, 2), substr($ymd, 0, 4)));
			$cnt = $kbnYoubi['01'] - $dayOfWeek;
			$baseSec = mktime(0, 0, 0, substr($ymd, 4, 2), substr($ymd, 6, 2), substr($ymd, 0, 4));
			$addSec = $cnt * 86400; // 日数×１日の秒数
			$targetSec = $baseSec + $addSec;
			$ymd = date("Y/m/d", $targetSec);
		
			// 選択週の値を設定
			foreach ($selDate as $key => $value) {
				if ($ymd == $value) {
					$sel_date = $key;
					break;
				}
			}
		}
		
		$this->set("sel_date_lsp020",$sel_date);
		
		/* ******* LSP予測シュミレーション（LSP020）からの呼び出し時 end */

		//	見出しの日と曜日をセット
		//	selDateの選択により変える必要あり
		$sel_date2 = $sel_date * 7;
		$date_01 = date("Y/m/d",strtotime($befCnt + $sel_date2 + 0 . " day"));
		$date_02 = date("Y/m/d",strtotime($befCnt + $sel_date2 + 1 . " day"));
		$date_03 = date("Y/m/d",strtotime($befCnt + $sel_date2 + 2 . " day"));
		$date_04 = date("Y/m/d",strtotime($befCnt + $sel_date2 + 3 . " day"));
		$date_05 = date("Y/m/d",strtotime($befCnt + $sel_date2 + 4 . " day"));
		$date_06 = date("Y/m/d",strtotime($befCnt + $sel_date2 + 5 . " day"));
		$date_07 = date("Y/m/d",strtotime($befCnt + $sel_date2 + 6 . " day"));


		$week = array("日", "月", "火", "水", "木", "金", "土");

		$date_01_yb = $week[date("w", strtotime($date_01))];
		$date_02_yb = $week[date("w", strtotime($date_02))];
		$date_03_yb = $week[date("w", strtotime($date_03))];
		$date_04_yb = $week[date("w", strtotime($date_04))];
		$date_05_yb = $week[date("w", strtotime($date_05))];
		$date_06_yb = $week[date("w", strtotime($date_06))];
		$date_07_yb = $week[date("w", strtotime($date_07))];


		$this->set('date_01_yb',$date_01_yb);
		$this->set('date_02_yb',$date_02_yb);
		$this->set('date_03_yb',$date_03_yb);
		$this->set('date_04_yb',$date_04_yb);
		$this->set('date_05_yb',$date_05_yb);
		$this->set('date_06_yb',$date_06_yb);
		$this->set('date_07_yb',$date_07_yb);

		$this->set('date_01',$date_01);
		$this->set('date_02',$date_02);
		$this->set('date_03',$date_03);
		$this->set('date_04',$date_04);
		$this->set('date_05',$date_05);
		$this->set('date_06',$date_06);
		$this->set('date_07',$date_07);

		$this->set('kbn_youbi',$kbnYoubi['01']);
		//	VIEWへセット END
		


		if ($kotei == '') {
			$bnri_dai_cd = "";
			$bnri_cyu_cd = "";
		} else {
			$bnri_dai_cd = substr($kotei, 0,3);
			$bnri_cyu_cd = substr($kotei, 4,3);
		}
		
		//入力チェック
		$inputCheckFlg = '0';

		if ($bnri_dai_cd == null
		or  $bnri_cyu_cd == null){
			$koutei_inp_flg = 1;
		} else {
			$koutei_inp_flg = 0;
		}
		
		$lstViewFlg = false;
		$arrayList = array();

		if (isset($this->request->query['kotei'])) {
			//勤務情報設定一覧
			$lstViewFlg = true;
			$arrayList = $this->LSP010Model->getTStaffKinmuLst($bnri_dai_cd, $bnri_cyu_cd,  $selDate[$sel_date], $staff_cd_hidden, $koutei_inp_flg);
			
		}
		
		$this->set('lstViewFlg',$lstViewFlg);
		
		//エラーの場合
		if ($this->Session->check('saveTableLSP010')) {
		
			//データを取得
			$getArray = $this->Session->read('saveTableLSP010');
			
			$ttl = $getArray->ttl;
			$data = $getArray->data;
			$timestamp = $getArray->timestamp;
			
			$copyEnabled = false;
			if (!empty($data) && empty($bnri_dai_cd) && empty($staff_cd_hidden)) {
				$copyEnabled = true;
			}
			
			$this->set("timestamp", $timestamp);
			$this->set('ttl',$ttl);
			$this->set('lsts',$data);
			$this->set('copyEnabled',$copyEnabled);
			$this->render("index_error");
			
			$this->Session->delete('saveTableLSP010');
		
		//エラー以外で条件付きの場合
		} else {
			
			$copyEnabled = false;
			if (!empty($arrayList) && empty($bnri_dai_cd) && empty($staff_cd_hidden)) {
				$copyEnabled = true;
			}
			
			/* タイムスタムプ取得 */
			$this->set('copyEnabled',$copyEnabled);
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
		$this->Session->write('saveTableLSP010',$getArray);

		$ttl = $getArray->ttl;
		$data = $getArray->data;
		$timestamp = $getArray->timestamp;
		
		//タイムスタンプチェック
		if (!$this->LSP010Model->checkTimestamp($timestamp)) {
			$array = $this->LSP010Model->errors;
		    $message = '';
		    foreach ( $array as $key=>$val ) {
		    	foreach ($val as $key2=>$val2) {
			    	$message = $message . $val2 . "<br>";
		    	}
		    }
		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}

		//未入力箇所　マスタから取得
		$data = $this->LSP010Model->getMStaffKihon($data, $ttl);

		$array = $this->LSP010Model->errors;
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


		//入力（必須・桁数・数字・範囲）チェック　＆　同一行内、時間前後重なりチェック
		$abc = $this->LSP010Model->checkTStaffKinmu($data, $ttl);
		
		$array = $this->LSP010Model->errors;
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


		//配列内、他行との、時間前後重なりチェック
		$abc = $this->LSP010Model->checkTStaffKinmuTbl($data, $ttl);
		
		$array = $this->LSP010Model->errors;

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


		//ＤＢとの時間重なりチェック
		$abc = $this->LSP010Model->checkTStaffKinmuDb($data, $ttl);
		
		$array = $this->LSP010Model->errors;
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
		if (!$this->LSP010Model->setTStaffKinmu($data, $ttl, $this->Session->read('staff_cd'))) {
		    
		    $array = $this->LSP010Model->errors;
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
		$this->Session->delete('saveTableLSP010');

		
		echo "return_cd=" . "0" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNI000001'));

	}
	
	/**
	 * DB更新処理（複製処理）
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function dataCopy() {
		
		$this->autoLayout = false;
		$this->autoRender = false;
		$getArray = json_decode($this->request->input());
		$staffCd = $this->Session->read('staff_cd');
		$data = $getArray->data;
		$srcDate = $getArray->srcDate;
		$destDate = $getArray->destDate;
		
		//ＤＢとの時間重なりチェック
		if (!$this->LSP010Model->checkTStaffKinmuCopyDb($data, $srcDate, $destDate)) {
			$array = $this->LSP010Model->errors;
			$message = '';
			foreach ( $array as $key=>$val ) {
				foreach ($val as $key2=>$val2) {
					$message = $message . $val2 . "<br>";
				}
			}
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($message) ;
			return;
		}
		
		// コピー元日付からコピー先日付までの日数を算出
		
		//更新処理
		if (!$this->LSP010Model->copyStaffKinmu($staffCd, $srcDate, $destDate)) {
			$array = $this->LSP010Model->errors;
			$message = '';
			foreach ( $array as $key=>$val ) {
				foreach ($val as $key2=>$val2) {
					$message = $message . $val2 . "<br>";
				}
			}
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($message) ;
			return;
		}
		
		
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

	}	


}
