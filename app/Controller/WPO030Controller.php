<?php
/**
 * WPO030
 *
 * ピッキング割振画面
 *
 * @category      WPO
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');
App::uses('WPO030Model', 'Model');
App::uses('MMeisyo', 'Model');

class WPO030Controller extends AppController {

	public $name = 'WPO030';
	public $layout = "DCMS";

	/**
	 * 起動時処理
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function beforeFilter() {

		parent::beforeFilter();

		//ヘッダー設定

		// システム名
		$this->set('system', Sanitize::stripAll($this->Session->read('system_name')));

		// サブシステム名
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['WPO']));

		// 機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WPO']['030']['kino_nm']));

		//メッセージマスタ設定
		$this->MMessage = new MMessage($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'), $this->name);

		//共通関数モデル
		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'), $this->name);

		//データベース
		$this->WPO030Model = new WPO030Model($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'), $this->name);

		//名称マスタ
		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

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
 		$this->set("onload","initReload();" .
 							"initPopUpRank('".Router::url('/WPO030/popupRank', false)."');" .
 							"initPopUpStaff('".Router::url('/WPO030/popupStaff', false)."');" .
 							"initUpdateRank('".Router::url('/WPO030/dataUpdateRank', false)."');" .
 							"initUpdateStaff('".Router::url('/WPO030/dataUpdateStaff', false)."');");

		//メッセージ関連の設定
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
			
			$this->Session->write('displayWPO030','true');
			
			$this->redirect('/WPO030/index?return_cd=' . $return_cd . 
										  '&message=' . $message .
										  '&display=false'
										  );
			
			return;
		} else if ($this->Session->check('displayWPO030') && $display == "false") {
			
			$this->Session->delete('displayWPO030');
			
		} else if (!$this->Session->check('displayWPO030') && $display == "false") {
			
			$this->redirect('/WPO030/index');
			return;
		}

		//前回の処理でエラーが発生した場合、前回のデータ(ランク情報またはスタッフ情報)をそのまま表示
		//更新対象でない情報(ランクまたはスタッフ)は再取得
		if ($this->Session->check('saveTableWPO030_Rank') || $this->Session->check('saveTableWPO030_Staff')) {
			//ランク更新時のエラー
			if ($this->Session->check('saveTableWPO030_Rank')){
				//ランク情報の取得(セッション)
				$getData = $this->Session->read('saveTableWPO030_Rank');
				
				$rankLsts = $getData["DATA"];
				$timestamp = $getData["TIMESTAMP"];
				
				//前回のデータを削除
				$this->Session->delete('saveTableWPO030_Rank');

				//スタッフ情報の取得(DB)
				$staffLsts = $this->WPO030Model->getStaffList();
				
				$this->set("timestamp1", $timestamp);
				$this->set("timestamp2", $this->WPO030Model->getTimestamp2());

			}
			//スタッフ更新時のエラー
			else {
				//ランク情報の取得(DB)
				$rankLsts = $this->WPO030Model->getRankList();

				//スタッフ情報の取得(セッション)
				$getData = $this->Session->read('saveTableWPO030_Staff');
				//前回のデータを削除
				$this->Session->delete('saveTableWPO030_Staff');
				
				$staffLsts = $getData["DATA"];
				$timestamp = $getData["TIMESTAMP"];
				
				$this->set("timestamp1", $this->WPO030Model->getTimestamp1());
				$this->set("timestamp2", $timestamp);
			}
		} else {
			//ランク情報の取得
			$rankLsts = $this->WPO030Model->getRankList();
			//スタッフ情報の取得
			$staffLsts = $this->WPO030Model->getStaffList();
			
			//タイムスタンプ
			$this->set("timestamp1", $this->WPO030Model->getTimestamp1());
			$this->set("timestamp2", $this->WPO030Model->getTimestamp2());
		}

		//データをセット
		$this->set('rankLsts',$rankLsts);
		$this->set('staffLsts',$staffLsts);

		

	}

	/**
	 * ランクデータ更新処理
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function dataUpdateRank() {

		//データのやり取りだけなのでレンダリングなし
		$this->autoLayout = false;
		$this->autoRender = false;

		//ポストデータの存在確認エラー
		if(!$this->request->is('post')) {
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000104'));
			return;
		}

		//データを取得
		$getData = json_decode($this->request->input());

		//データがない場合エラー
		if(count($getData) == 0) {
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000101'));
			return;
		}
		
		$obj = $getData->DATA;
		$timestamp = $getData->TIMESTAMP;

		//オブジェクトを配列に変換
		$data = $this->WPO030Model->getConvertArrayToRequestObject($obj);
		
		$saveArray = array(
		
			"TIMESTAMP" => $timestamp,
			"DATA" => $data
		);
		
		//全データを一時保存(配列データ)
		$this->Session->write('saveTableWPO030_Rank',$saveArray);
		
		//画面取得時のタイムスタンプが最新か確認
		if (!$this->WPO030Model->checkTimestampRank($timestamp)) {
			
			//エラー吐き出し
			$array = $this->WPO030Model->errors;
		    $message = '';
		    
		    foreach ( $array as $key=>$val ) {
		    
		    	foreach ($val as $key2=>$val2) {
			    
			    	$message = $message . $val2 . "<br>";
		    	}
		    }
		    
		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}

		//入力チェック
		if (!$this->WPO030Model->checkRankMasterData($obj)) {
			$array = $this->WPO030Model->errors;
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
		if (!$this->WPO030Model->setRankMaster($this->Session->read('staff_cd'),$obj)) {

			$array = $this->WPO030Model->errors;
		    $message = '';

		    foreach ( $array as $key=>$val ) {
		    	foreach ($val as $key2=>$val2) {
			    	$message = $message . $val2 . "<br>";
		    	}
		    }

		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}

		//成功時は前回のテーブルデータを消す。
		$this->Session->delete('saveTableWPO030_Rank');

		//更新終了
		echo "return_cd=" . "0" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNI000001'));
	}

	/**
	 * スタッフデータ更新処理
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function dataUpdateStaff() {

		//データのやり取りだけなのでレンダリングなし
		$this->autoLayout = false;
		$this->autoRender = false;

		//ポストデータの存在確認エラー
		if(!$this->request->is('post')) {
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000104'));
			return;
		}

		//データを取得
		$getData = json_decode($this->request->input());

		//データがない場合エラー
		if(count($getData) == 0) {
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000101'));
			return;
		}
		
		$obj = $getData->DATA;
		$timestamp = $getData->TIMESTAMP;

		//オブジェクトを配列に変換
		$data = $this->WPO030Model->getConvertArrayToRequestObject($obj);
		//全データを一時保存(配列データ)
		
		$saveArray = array(
		
			"TIMESTAMP" => $timestamp,
			"DATA" => $data
		);
		
		
		$this->Session->write('saveTableWPO030_Staff',$saveArray);
		
		//画面取得時のタイムスタンプが最新か確認
		if (!$this->WPO030Model->checkTimestampKihon($timestamp)) {
			
			//エラー吐き出し
			$array = $this->WPO030Model->errors;
		    $message = '';
		    
		    foreach ( $array as $key=>$val ) {
		    
		    	foreach ($val as $key2=>$val2) {
			    
			    	$message = $message . $val2 . "<br>";
		    	}
		    }
		    
		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}

		//入力チェック
		if (!$this->WPO030Model->checkStaffUpdRank($obj)) {
			$array = $this->WPO030Model->errors;
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
		if (!$this->WPO030Model->setStaffUpdRank($this->Session->read('staff_cd'),$obj)) {

			$array = $this->WPO030Model->errors;
		    $message = '';

		    foreach ( $array as $key=>$val ) {
		    	foreach ($val as $key2=>$val2) {
			    	$message = $message . $val2 . "<br>";
		    	}
		    }

		    echo "return_cd=" . "1" . "&message=" .  base64_encode ($message);
		    return;
		}

		//成功時は前回のテーブルデータを消す。
		$this->Session->delete('saveTableWPO030_Staff');

		//更新終了
		echo "return_cd=" . "0" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNI000001'));
	}

	/**
	 * ランクマスタポップアップ取得
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function popupRank() {

		$this->autoLayout = false;
	}

	/**
	 * スタッフランクポップアップ取得
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function popupStaff() {

		$this->autoLayout = false;

		// 変更ランクの取得
		$rankOption = $this->WPO030Model->getRankOption();
		// 変更ランクの設定
		$this->set('rankOption',$rankOption);

	}

}
