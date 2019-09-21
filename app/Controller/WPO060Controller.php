<?php
/**
 * WPO060
 *
 * 工程一括更新
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
App::uses('WPO060Model', 'Model');

class WPO060Controller extends AppController {

	public $name = 'WPO060';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WPO']['060']['kino_nm']));

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

		$this->WPO060Model = new WPO060Model($this->Session->read('select_ninusi_cd'),
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
					"initUpdate('".Router::url('/WPO060/dataUpdate', false)."');" .
					"initLog('".Router::url('/WPO060/popup', false)."');" .
					"initBunrui();");

		$ymd = '';
		$timeFrom = '';
		$timeTo = '';
		$haken = '';
		$keiyaku = '';
		$yakushoku = '';
		$staff_cd = '';
		$staff_nm = '';
		$kotei = '';
		$timeFrom2 = '';
		$timeTo2 = '';

		$return_cd = '';
		$display = '';
		$message = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['ymd'])){
			$ymd = $this->request->query['ymd'];
			$timeFrom = $this->request->query['timeFrom'];
			$timeTo = $this->request->query['timeTo'];
			$haken = $this->request->query['haken'];
			$keiyaku = $this->request->query['keiyaku'];
			$yakushoku = $this->request->query['yakushoku'];
			$staff_cd = $this->request->query['staff_cd'];
			$staff_nm = $this->request->query['staff_nm'];
		}

		if (isset($this->request->query['kotei'])){
			$kotei = $this->request->query['kotei'];
			$timeFrom2 = $this->request->query['timeFrom2'];
			$timeTo2 = $this->request->query['timeTo2'];
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

		if ($ymd == '') {
			$ymd = $this->WPO060Model->getToday();
		}

		//検索条件を保存
		$this->set("ymd",$ymd);
		$this->set("timeFrom_hidden",$timeFrom);


		if ($timeTo != "") {
			$this->set("timeTo_hidden",$timeTo);
		}

		if ($haken != "") {
			$this->set("haken",$haken);
		}

		if ($keiyaku != "") {
			$this->set("keiyaku",$keiyaku);
		}

		if ($yakushoku != "") {
			$this->set("yakushoku",$yakushoku);
		}

		if ($staff_cd != "") {
			$this->set("staff_cd",$staff_cd);
		}

		if ($staff_nm != "") {
			$this->set("staff_nm",$staff_nm);
		}



		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {

			$this->Session->write('displayWPO060','true');

			$this->redirect('/WPO060/index?return_cd=' . $return_cd .
										  '&message=' . $message .
										  '&ymd=' . $ymd .
										  '&timeFrom=' . $timeFrom .
										  '&timeTo=' . $timeTo .
										  '&haken=' . $haken .
										  '&keiyaku=' . $keiyaku .
										  '&yakushoku=' . $yakushoku .
										  '&staff_cd=' . $staff_cd .
										  '&staff_nm=' . $staff_nm .
										  '&kotei=' . $kotei .
										  '&timeFrom2=' . $timeFrom2 .
										  '&timeTo2=' . $timeTo2 .
										  '&display=false'
										  );

			return;
		} else if ($this->Session->check('displayWPO060') && $display == "false") {

			$this->Session->delete('displayWPO060');

		} else if (!$this->Session->check('displayWPO060') && $display == "false") {

			$this->redirect('/WPO060/index?&ymd=' . $ymd .
										  '&timeFrom=' . $timeFrom .
										  '&timeTo=' . $timeTo .
										  '&haken=' . $haken .
										  '&keiyaku=' . $keiyaku .
										  '&yakushoku=' . $yakushoku .
										  '&staff_cd=' . $staff_cd .
										  '&staff_nm=' . $staff_nm .
										  '&kotei=' . $kotei .
										  '&timeFrom2=' . $timeFrom2 .
										  '&timeTo2=' . $timeTo2
										  );
			return;
		}


		//日付
		$this->set("ymd",$ymd);


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
		$timeArray = $this->WPO060Model->getTimes();
		$this->set("timeFrom",$timeArray);
		$this->set("timeTo",$timeArray);

		//派遣コンボ設定
		$arrayList = $this->WPO060Model->getHaken();
		$this->set("hakenList",$arrayList);

		//役職
		$arrayList = $this->MMeisyo->getMeisyoPDO("17");
		$this->set("kbn_pst",$arrayList);

		//契約形態
		$arrayList = $this->MMeisyo->getMeisyoPDO("30");
		$this->set("kbn_keiyaku",$arrayList);

		//時間
		$minTime = $this->WPO060Model->getMinTime();
		$this->set("min_time",$minTime);
		
		$maxTime = $this->WPO060Model->getMaxTime();
		$this->set("max_time",$maxTime);

		$lsts = array();

		//検索条件の必須チェック
		if (!$this->Session->check('displayWPO0602') && isset($this->request->query['ymd']) &&
				($ymd == '' || $timeFrom == '') && !isset($this->request->query['return_cd']) ) {


					//就業日付がないエラー
					$errorArray = array();
					$message = str_replace ("+","%2B",base64_encode ($this->MMessage->getOneMessage('TPME050001')));


					$this->Session->write('displayWPO0602','true');

					$this->redirect('/WPO060/index?return_cd=1' .
										  '&message=' . $message .
										  '&ymd=' . $ymd .
										  '&timeFrom=' . $timeFrom .
										  '&timeTo=' . $timeTo .
										  '&haken=' . $haken .
										  '&keiyaku=' . $keiyaku .
										  '&yakushoku=' . $yakushoku .
										  '&staff_cd=' . $staff_cd .
										  '&staff_nm=' . $staff_nm .
										  '&kotei=' . $kotei .
										  '&timeFrom2=' . $timeFrom2 .
										  '&timeTo2=' . $timeTo2 .
										  '&display=true'
										  );



		} else {
				$this->Session->delete('displayWPO0602');
		}

		//検索条件がある場合検索
		if ($ymd != '') {

			//リスト一覧
			$arrayList =  $this->WPO060Model->getKoteiList($ymd, $timeFrom, $timeTo, $haken, $keiyaku, $yakushoku, $staff_cd, $staff_nm);

			//前回の処理でエラーが発生した場合、前回のデータをそのまま表示
			if ($this->Session->check('saveTableWPO060')) {

				//前回のデータを取得
				$getArray = $this->Session->read('saveTableWPO060');

				//前回のデータを設置
				$data = $getArray->data;
				$timestamp = $getArray->timestamp;

				$this->set("timestamp", $timestamp);
				$this->set('lsts',$data);

				//データ構造が違うのでレンダリングを変更
				$this->render("index_error");

				//前回のデータを削除
				$this->Session->delete('saveTableWPO060');

				//エラーが発生していない場合は、検索条件を元に表示
			} else {

				//タイムスタムプ取得
				$this->set("timestamp", $this->WPO060Model->getTimestamp());
				$this->set('lsts',$arrayList);
			}


		//条件が何もない場合
		} else {

			//前回の処理でエラーが発生した場合、前回のデータをそのまま表示
			if ($this->Session->check('saveTableWPO060')) {

				//前回のデータを取得
				$getArray = $this->Session->read('saveTableWPO060');

				//前回のデータを設置
				$data = $getArray->data;
				$timestamp = $getArray->timestamp;

				$this->set("timestamp", $timestamp);
				$this->set('lsts',$data);

				//データ構造が違うのでレンダリングを変更
				$this->render("index_error");

				//前回のデータを削除
				$this->Session->delete('saveTableWPO060');

			//エラーが発生していない場合は、検索条件を元に表示
			} else {

				//タイムスタムプ取得
				$this->set("timestamp", $this->WPO060Model->getTimestamp());
			}

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

		$ymd = '';
		$timeFrom = '';
		$timeTo = '';
		$haken = '';
		$yakushoku = '';
		$keiyaku = '';
		$staff_cd = '';
		$staff_nm = '';
		$kotei = '';
		$timeFrom2 = '';
		$timeTo2 = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['kotei'])){
			$ymd = $this->request->query['ymd'];
			$timeFrom = $this->request->query['timeFrom'];
			$timeTo = $this->request->query['timeTo'];
			$haken = $this->request->query['haken'];
			$yakushoku = $this->request->query['yakushoku'];
			$keiyaku = $this->request->query['keiyaku'];
			$staff_cd = $this->request->query['staff_cd'];
			$staff_nm = $this->request->query['staff_nm'];
			$kotei = $this->request->query['kotei'];
			$timeFrom2 = $this->request->query['timeFrom2'];
			$timeTo2 = $this->request->query['timeTo2'];
		}


		//データを一時保存
		$this->Session->write('saveTableWPO060',$getArray);

		$data = $getArray->data;
		$timestamp = $getArray->timestamp;

		//更新処理
		$result = $this->WPO060Model->setKotei($data,$kotei,$timeFrom2,$timeTo2, $this->Session->read('staff_cd'));

		//メッセージ用初期化
		$message = '';

		if ($result === false) {

			$errorArray = $this->WPO060Model->errors;

			foreach ( $errorArray as $str ) {

				$message .= $str . "<br>";
				$return_cd = 1;
			}

			echo "return_cd=1" . "&message=" .  base64_encode ($message) ;
			return;
		}

		$return_cd = 0;

		//表示メッセージ
		$infoArray = $this->WPO060Model->messages;
		foreach ( $infoArray as $str ) {

			$message .= $str . "<br>";
		}

		$message .= "<br>";

		//表示エラー
		$errorArray = $this->WPO060Model->errors;

		$count = 0;
		foreach ( $errorArray as $str ) {

			if ($count >= 50) {
				$message .= vsprintf("%d件省略されました。", array(count($errorArray) - $count)) . "<br>";
				break;
			}
			$message .= $str . "<br>";
			$return_cd = 1;

			$count++;
		}

		//成功なのでテーブルをセッションから消す
		$this->Session->delete('saveTableWPO060');


		echo "return_cd=". $return_cd . "&message=" . base64_encode ($message)
								   . "&ymd=" . $ymd
								   . "&timeFrom=" . $timeFrom
								   . "&timeTo=" . $timeTo
								   . "&haken=" . $haken
								   . "&yakushoku=" . $yakushoku
								   . "&keiyaku=" . $keiyaku
								   . "&staff_cd=" . $staff_cd
								   . "&staff_nm=" . $staff_nm
								   . "&kotei=" . $kotei
								   . '&timeFrom2=' . $timeFrom2
								   . "&timeTo2=" . $timeTo2
								   . "&display=true";

	}


	/**
	 * 更新処理用ポップアップ取得
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function popup() {

		//ポップアップデータだけ取得するため、レイアウトは無効にする
		$this->autoLayout = false;

		$array = $this->WPO060Model->getLogText();

		$title = $array['TITLE'];
		$message = $array['LOG'];

		$this->set('titleText',str_replace("\n",'<br/>',$title));
		$this->set('message',str_replace("\n",'<br/>',$message));

	}


}
