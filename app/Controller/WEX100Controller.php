<?php
/**
 * WEX100
 *
 * DS用作業実績
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
App::uses('WEX100Model', 'Model');

class WEX100Controller extends AppController {

	public $name = 'WEX100';
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
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['WEX']['100']['kino_nm']));

		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),
		                             $this->Session->read('select_sosiki_cd'),
		                             $this->name
		                            );

		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),
				                     $this->Session->read('select_sosiki_cd'),
				                     $this->name
				                     );

		$this->WEX100Model = new WEX100Model($this->Session->read('select_ninusi_cd'),
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
				    "initCSV('".Router::url('/WEX100/csv', false). "');".
                    "initPDF('".Router::url('/WEX100/pdf', false). "');");

		$ymd_from = '';
		$ymd_to = '';
		$kaisya_cd = '';
		$staff_cd_nm = '';
		$bnri_cyu_cd = '';
		$syaryo_cd = '';
		$area_cd = '';
		$more = '';
		$jikan_from = '';
		$jikan_to = '';
		$kyori_from = '';
		$kyori_to = '';

		$pageID = 1;
		$return_cd = '';
		$display = '';
		$message = '';

		//パラメータ　検索条件を取得
		if (isset($this->request->query['ymd_from'])&&isset($this->request->query['ymd_to'])){
			$ymd_from = $this->request->query['ymd_from'];
			$ymd_to = $this->request->query['ymd_to'];
		}

		if (isset($this->request->query['kaisya_cd'])){
			$kaisya_cd = $this->request->query['kaisya_cd'];
		}

		if (isset($this->request->query['staff_cd_nm'])){
			$staff_cd_nm = $this->request->query['staff_cd_nm'];
		}
		
		if (isset($this->request->query['bnri_cyu_cd'])){
			$bnri_cyu_cd = $this->request->query['bnri_cyu_cd'];
		}
			
		if (isset($this->request->query['syaryo_cd'])){
			$syaryo_cd = $this->request->query['syaryo_cd'];
		}
		
		if (isset($this->request->query['area_cd'])){
			$area_cd = $this->request->query['area_cd'];
		}
		
		if (isset($this->request->query['more'])){
			$more = $this->request->query['more'];
		}
		
		if (isset($this->request->query['jikan_from'])&&isset($this->request->query['jikan_to'])){
			$jikan_from = $this->request->query['jikan_from'];
			$jikan_to = $this->request->query['jikan_to'];
		}
				
		if (isset($this->request->query['kyori_from'])&&isset($this->request->query['kyori_to'])){
			$kyori_from = $this->request->query['kyori_from'];
			$kyori_to = $this->request->query['kyori_to'];
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

		if ($ymd_from == '' && $ymd_to == '') {
			$ymd_from = $this->WEX100Model->getToday();
			$ymd_to = $ymd_from;
			$more = 'on';
		}

		//検索条件を保存
		$this->set("ymd_from",$ymd_from);
		$this->set("ymd_to",$ymd_to);
		$this->set("kaisya_cd",$kaisya_cd);
		$this->set("staff_cd_nm",$staff_cd_nm);
		$this->set("bnri_cyu_cd",$bnri_cyu_cd);
		$this->set("syaryo_cd",$syaryo_cd);
		$this->set("area_cd",$area_cd);
		$this->set("jikan_from",$jikan_from);
		$this->set("jikan_to",$jikan_to);
		$this->set("kyori_from",$kyori_from);
		$this->set("kyori_to",$kyori_to);

		if ($more != "") {
			$this->set("more",$more);
		}


		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {

			$this->Session->write('displayWEX100','true');

			$this->redirect('/WEX100/index?return_cd=' . $return_cd .
										  '&message=' . $message .
										  '&ymd_from=' . $ymd_from .
										  '&ymd_to=' . $ymd_to .
										  '$kaisya_cd=' . $kaisya_cd .
										  'staff_cd_nm=' . $staff_cd_nm .
										  'bnri_cyu_cd=' . $bnri_cyu_cd .
										  'syaryo_cd=' . $syaryo_cd .
										  'area_cd=' . $area_cd .
										  'more=' . $more .
										  'jikan_from=' . $jikan_from .
										  'jikan_to=' . $jikan_to .
										  'kyori_from=' . $kyori_from .
										  'kyori_to=' . $kyori_to .
										  '&pageID=' . $pageID .
										  '&display=false'
										  );

			return;
		} else if ($this->Session->check('displayWEX100') && $display == "false") {

			$this->Session->delete('displayWEX100');

		} else if (!$this->Session->check('displayWEX100') && $display == "false") {

			$this->redirect('/WEX100/index?&ymd_from=' . $ymd_from .
										  '&ymd_to=' . $ymd_to .
										  '$kaisya_cd=' . $kaisya_cd .
										  'staff_cd_nm=' . $staff_cd_nm .
										  'bnri_cyu_cd=' . $bnri_cyu_cd .
										  'syaryo_cd=' . $syaryo_cd .
										  'area_cd=' . $area_cd .
										  'more=' . $more .
										  'jikan_from=' . $jikan_from .
										  'jikan_to=' . $jikan_to .
										  'kyori_from=' . $kyori_from .
										  'kyori_to=' . $kyori_to .
										  '&pageID=' . $pageID
										  );
			return;
		}


		//派遣コンボ設定
		$arrayList = $this->WEX100Model->getHaken();
		$this->set("hakenList",$arrayList);

		//中分類設定
		$arrayList = $this->WEX100Model->getBnriCyu();
		$this->set("bnriCyuList",$arrayList);

		//車両番号
		$arrayList = $this->MMeisyo->getMeisyoPDO("41");
		$this->set("syaryoCdList",$arrayList);

		//エリア
		$arrayList = $this->MMeisyo->getMeisyoPDO("42");
		$this->set("areaCdList",$arrayList);

		//項目
		$komokuList = $this->MMeisyo->getMeisyoPDO("43");
		
		function cmp($a, $b) {
		    if ($a['VAL_FREE_NUM_1'] == $b['VAL_FREE_NUM_1']) {
		        return 0;
		    }
		    return ($a['VAL_FREE_NUM_1'] < $b['VAL_FREE_NUM_1']) ? -1 : 1;
		}

		usort($komokuList, "cmp");
		$this->set("komokuList",$komokuList);

		//検索条件の必須チェック
		if (!$this->Session->check('displayWEX1002') && isset($this->request->query['ymd_from']) &&
				($ymd_from == '' || $ymd_to == '') && !isset($this->request->query['return_cd']) ) {


					//就業日付がないエラー
					$errorArray = array();
					$message = str_replace ("+","%2B",base64_encode ($this->MMessage->getOneMessage('TPME050001')));


					$this->Session->write('displayWEX1002','true');

					$this->redirect('/WEX100/index?return_cd=1' .
										  '&message=' . $message .
										  '&ymd_from=' . $ymd_from .
										  '&ymd_to=' . $ymd_to .
										  '$kaisya_cd=' . $kaisya_cd .
										  'staff_cd_nm=' . $staff_cd_nm .
										  'bnri_cyu_cd=' . $bnri_cyu_cd .
										  'syaryo_cd=' . $syaryo_cd .
										  'area_cd=' . $area_cd .
										  'more=' . $more .
										  'jikan_from=' . $jikan_from .
										  'jikan_to=' . $jikan_to .
										  'kyori_from=' . $kyori_from .
										  'kyori_to=' . $kyori_to .
										  '&pageID=' . $pageID .
										  '&display=true'
										  );



		} else {
				$this->Session->delete('displayWEX1002');
		}

		//検索条件がある場合検索
		if ((isset($ymd_from) && isset($ymd_to)) ||
			isset($kaisya_cd) || isset($staff_cd_nm) || isset($bnri_cyu_cd) ||
			isset($syaryo_cd) || isset($area_cd) || isset($more) ||
			(isset($jikan_from) && isset($jikan_to)) ||
			(isset($kyori_from) && isset($$kyori_to))){

			//リスト一覧
			$lsts =  $this->WEX100Model->getList($komokuList,$ymd_from,$ymd_to,$kaisya_cd,$staff_cd_nm,$bnri_cyu_cd,$syaryo_cd,$area_cd,
												 $more,$jikan_from,$jikan_to,$kyori_from,$kyori_to);

			//ページャー
			$pageNum = 50;

			$options = array(
					"totalItems" => count($lsts),
					"delta" => 10,
					"perPage" => $pageNum,
					"httpMethod" => "GET",
					"path" => "/DCMS/WEX100/index"
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

			//前回の処理でエラーが発生した場合、前回のデータをそのまま表示
			if ($this->Session->check('saveTableWEX100')) {

				//前回のデータを取得
				$getArray = $this->Session->read('saveTableWEX100');

				//前回のデータを設置
				$data = $getArray->data;
				$timestamp = $getArray->timestamp;

				$this->set("timestamp", $timestamp);
				$this->set('lsts',$data);

				//データ構造が違うのでレンダリングを変更
				$this->render("index_error");

				//前回のデータを削除
				$this->Session->delete('saveTableWEX100');

				//エラーが発生していない場合は、検索条件を元に表示
			} else {

				//タイムスタムプ取得
				$this->set("timestamp", $this->WEX100Model->getTimestamp());
				$this->set('lsts',$arrayList);
			}

			//Viewへセット
			$this->set('index',$index);


		//条件が何もない場合
		} else {

			//前回の処理でエラーが発生した場合、前回のデータをそのまま表示
			if ($this->Session->check('saveTableWEX100')) {

				//前回のデータを取得
				$getArray = $this->Session->read('saveTableTMP060');

				//前回のデータを設置
				$data = $getArray->data;
				$timestamp = $getArray->timestamp;

				$this->set("timestamp", $timestamp);
				$this->set('lsts',$data);

				//データ構造が違うのでレンダリングを変更
				$this->render("index_error");

				//前回のデータを削除
				$this->Session->delete('saveTableWEX100');

			//エラーが発生していない場合は、検索条件を元に表示
			} else {

				//タイムスタムプ取得
				$this->set("timestamp", $this->WEX100Model->getTimestamp());
			}

		}

	}

    public function pdf()
    {

        $this->autoLayout = false;
        $this->autoRender = false;

        $ymd_from = '';
        $ymd_to = '';
        $kaisya_cd = '';
        $staff_cd_nm = '';
        $bnri_cyu_cd = '';
        $syaryo_cd = '';
        $area_cd = '';
        $more = '';
        $jikan_from = '';
        $jikan_to = '';
        $kyori_from = '';
        $kyori_to = '';

        //パラメータ　検索条件を取得
        if (isset($this->request->query['ymd_from'])&&isset($this->request->query['ymd_to'])){
            $ymd_from = $this->request->query['ymd_from'];
            $ymd_to = $this->request->query['ymd_to'];
        }

        if (isset($this->request->query['kaisya_cd'])){
            $kaisya_cd = $this->request->query['kaisya_cd'];
        }

        if (isset($this->request->query['staff_cd_nm'])){
            $staff_cd_nm = $this->request->query['staff_cd_nm'];
        }

        if (isset($this->request->query['bnri_cyu_cd'])){
            $bnri_cyu_cd = $this->request->query['bnri_cyu_cd'];
        }

        if (isset($this->request->query['syaryo_cd'])){
            $syaryo_cd = $this->request->query['syaryo_cd'];
        }

        if (isset($this->request->query['area_cd'])){
            $area_cd = $this->request->query['area_cd'];
        }

        if (isset($this->request->query['more'])){
            $more = $this->request->query['more'];
        }

        if (isset($this->request->query['jikan_from'])&&isset($this->request->query['jikan_to'])){
            $jikan_from = $this->request->query['jikan_from'];
            $jikan_to = $this->request->query['jikan_to'];
        }

        if (isset($this->request->query['kyori_from'])&&isset($this->request->query['kyori_to'])){
            $kyori_from = $this->request->query['kyori_from'];
            $kyori_to = $this->request->query['kyori_to'];
        }

        $html_query = '?ymd_from=' . $ymd_from
            . "&ymd_to=" . $ymd_to
            . "&kaisya_cd=" . $kaisya_cd
            . "&staff_cd_nm=" . $staff_cd_nm
            . "&bnri_cyu_cd=" . $bnri_cyu_cd
            . "&syaryo_cd=" . $syaryo_cd
            . "&area_cd=" . $area_cd
            . "&more=" . $more
            . "&jikan_from=" . $jikan_from
            . "&jikan_to=" . $jikan_to
            . "&kyori_from=" . $kyori_from
            . "&kyori_to=" . $kyori_to
            . "&ninusi_cd=" . $this->Session->read('select_ninusi_cd')
            . "&sosiki_cd=" . $this->Session->read('select_sosiki_cd');


        //For Local Test
        if ($_SERVER['SERVER_NAME'] == "wfm.local") {

            $this->redirect('https://wfm-pdf.local/' . $html_query);

        } else if ($_SERVER['SERVER_NAME'] == "wfm.trancom-wfm-test.com") { //for test server

            $this->redirect('https://pdf.trancom-wfm-test.com/' . $html_query);
        } else {

            $this->redirect('https://pdf.trancom-wfm.com/' . $html_query);
        }

        return;

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

		$ymd_from = '';
		$ymd_to = '';
		$kaisya_cd = '';
		$staff_cd_nm = '';
		$bnri_cyu_cd = '';
		$syaryo_cd = '';
		$area_cd = '';
		$more = '';
		$jikan_from = '';
		$jikan_to = '';
		$kyori_from = '';
		$kyori_to = '';
		
				//パラメータ　検索条件を取得
		if (isset($this->request->query['ymd_from'])&&isset($this->request->query['ymd_to'])){
			$ymd_from = $this->request->query['ymd_from'];
			$ymd_to = $this->request->query['ymd_to'];
		}

		if (isset($this->request->query['kaisya_cd'])){
			$kaisya_cd = $this->request->query['kaisya_cd'];
		}

		if (isset($this->request->query['staff_cd_nm'])){
			$staff_cd_nm = $this->request->query['staff_cd_nm'];
		}
		
		if (isset($this->request->query['bnri_cyu_cd'])){
			$bnri_cyu_cd = $this->request->query['bnri_cyu_cd'];
		}
			
		if (isset($this->request->query['syaryo_cd'])){
			$syaryo_cd = $this->request->query['syaryo_cd'];
		}
		
		if (isset($this->request->query['area_cd'])){
			$area_cd = $this->request->query['area_cd'];
		}
		
		if (isset($this->request->query['more'])){
			$more = $this->request->query['more'];
		}
		
		if (isset($this->request->query['jikan_from'])&&isset($this->request->query['jikan_to'])){
			$jikan_from = $this->request->query['jikan_from'];
			$jikan_to = $this->request->query['jikan_to'];
		}
				
		if (isset($this->request->query['kyori_from'])&&isset($this->request->query['kyori_to'])){
			$kyori_from = $this->request->query['kyori_from'];
			$kyori_to = $this->request->query['kyori_to'];
		}
		
		//項目
		$komokuList = $this->MMeisyo->getMeisyoPDO("43");
		
		function cmp($a, $b) {
		    if ($a['VAL_FREE_NUM_1'] == $b['VAL_FREE_NUM_1']) {
		        return 0;
		    }
		    return ($a['VAL_FREE_NUM_1'] < $b['VAL_FREE_NUM_1']) ? -1 : 1;
		}

		usort($komokuList, "cmp");

		// 工程実績情報CSVデータを取得
		$csvData = $this->WEX100Model->getCSV($komokuList,$ymd_from,$ymd_to,$kaisya_cd,$staff_cd_nm,$bnri_cyu_cd,$syaryo_cd,$area_cd,
										      $more,$jikan_from,$jikan_to,$kyori_from,$kyori_to);

		// 作成するファイル名の指定
		$fileName = "作業実績_" . date("Ymdhi") . ".csv";
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
