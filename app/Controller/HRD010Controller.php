<?php
/**
 * HRD010
 *
 * 人材登録
 *
 * @category      HRD
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');
App::uses('MBunrui', 'Model');
App::uses('MMeisyo', 'Model');
App::uses('HRD010Model', 'Model');

class HRD010Controller extends AppController {

	public $name = 'HRD010';
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

		/* ビューへ設定 */
		// システム名
		$this->set('system', Sanitize::stripAll($this->Session->read('system_name')));
		// サブシステム名
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['HRD']));
		// 機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['HRD']['010']['kino_nm']));

		//メッセージマスタモデル
		$this->MMessage = new MMessage($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//共通関数モデル
		$this->MCommon = new MCommon($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//HRD010モデル
		$this->HRD010Model = new HRD010Model($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//名称マスタ
		$this->MMeisyo = new MMeisyo($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

		//分類マスタモデル
		$this->MBunrui = new MBunrui($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'),$this->name);

	}

	/**
	 * 初期表示
	 *
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
		$this->set("onload","initReload();initLink();");

		//検索条件、ページID取得
		$staff_cd = '';
		$staff_nm = '';
		$haken_cd = '';
		$pageID = 1;
		$return_cd = '';
		$display = '';
		$message = '';

		//検索条件
		if (isset($this->request->query['staff_cd'])){
			$staff_cd = $this->request->query['staff_cd'];
			$staff_nm = $this->request->query['staff_nm'];
			$haken_cd = $this->request->query['haken_cd'];
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

			$this->Session->write('displayHRD010','true');

			$this->redirect('/HRD010/index?return_cd=' . $return_cd .
					'&message=' . $message .
					'&pageID=' . $pageID .
					'&display=false'
			);

			return;
		} else if ($this->Session->check('displayHRD010') && $display == "false") {

			$this->Session->delete('displayHRD010');

		} else if (!$this->Session->check('displayHRD010') && $display == "false") {

			$this->redirect('/HRD010/index');
			return;
		}


		//派遣コンボ設定
		$arrayList = $this->HRD010Model->getHaken();
		$this->set("hakenList",$arrayList);


		if (isset($this->request->query['staff_cd'])) {

			//スタッフリスト取得
			$lsts = $this->HRD010Model->getStaffList($staff_cd, $staff_nm, $haken_cd);

			//ページャーの設定
			$pageNum = 50;

			$options = array(
					"totalItems" => count($lsts),
					"delta" => 10,
					"perPage" => $pageNum,
					"httpMethod" => "GET",
					"path" => "/DCMS/HRD010/index"
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

			//Viewへセット
			$this->set('staffList',$arrayList);
			$this->set('index',$index);

		}


	}

	/**
	 * 基本情報
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function kihon(){

		//Javascript設定
		$this->set("onload","initReload();
				initImage();
				initUpdate('".Router::url('/HRD010/dataKihonUpdate', false)."');");

		//検索条件、ページID取得
		$return_cd = '';
		$display = '';
		$message = '';
		$staff_cd = '';

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

		//スタッフコード
		if(isset($this->request->query['staff_cd'])) {

			$staff_cd = $this->request->query['staff_cd'];
		}

		//スタッフ存在チェック
		$this->existsStaff($staff_cd);

		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {

			$this->Session->write('displayHRD010kihon','true');

			$this->redirect('/HRD010/kihon?staff_cd=' . $staff_cd .
					'&return_cd=' . $return_cd .
					'&message=' . $message .
					'&display=false'
			);

			return;
		} else if ($this->Session->check('displayHRD010kihon') && $display == "false") {

			$this->Session->delete('displayHRD010kihon');

		} else if (!$this->Session->check('displayHRD010kihon') && $display == "false") {

			$this->redirect('/HRD010/kihon?staff_cd=' . $staff_cd);
			return;
		}

		//性別
		$arrayList = $this->MMeisyo->getMeisyoPDO("16");
		$this->set('kbn_sex',$arrayList);

		//派遣コンボ設定
		$arrayList = $this->HRD010Model->getHaken();
		$this->set("haken_kaisya_cd",$arrayList);

		//拠点選択
		$arrayList = $this->HRD010Model->getKyoten();
		$this->set("kyoten",$arrayList);

		//役職
		$arrayList = $this->MMeisyo->getMeisyoPDO("17");
		$this->set("kbn_pst",$arrayList);

		//契約形態
		$arrayList = $this->MMeisyo->getMeisyoPDO("30");
		$this->set("kbn_keiyaku",$arrayList);

		//ピッキング
		$arrayList = $this->MMeisyo->getMeisyoPDO("19");
		$this->set("kbn_pick",$arrayList);

		//優先ゾーン
		$arrayList = $this->MMeisyo->getMeisyoPDO("05");
		$this->set("zone",$arrayList);

		//給与ランク
		$arrayList = $this->HRD010Model->getRANK();
		$this->set("rank",$arrayList);

		//スタッフ
		$arrayList = $this->HRD010Model->getStaff($staff_cd);
		$this->set("staff",$arrayList);

		//イメージ
		$arrayList = $this->HRD010Model->getStaffImage($staff_cd);
		$this->set("image",$arrayList);


		//拠点
		$arrayList = $this->HRD010Model->getStaffKyoten($staff_cd);
		$this->set("kyoka_kyoten",$arrayList);

		//優先ゾーン
		$arrayList = $this->HRD010Model->getStaffYusenZone($staff_cd);
		$this->set("yusen_zone",$arrayList);

		//前回の処理でエラーが発生した場合、前回のデータをそのまま表示
		if ($this->Session->check('saveTableHRD010')) {

			//前回のデータを取得
			$data = $this->Session->read('saveTableHRD010');
			$this->set('data',$data);

			//データ構造が違うのでレンダリングを変更
			$this->render("kihon_error");

			//前回のデータを削除
			$this->Session->delete('saveTableHRD010');

		}
	}

	/**
	 * 新規登録
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function shinki(){


		//Javascript設定
		$this->set("onload","initReload();
				initImage();
				initUpdate('".Router::url('/HRD010/dataShinkiUpdate', false)."');");

		//検索条件、ページID取得
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

			$this->Session->write('displayHRD010shinki','true');

			$this->redirect('/HRD010/shinki?return_cd=' . $return_cd .
					'&message=' . $message .
					'&display=false'
			);

			return;
		} else if ($this->Session->check('displayHRD010shinki') && $display == "false") {

			$this->Session->delete('displayHRD010shinki');

		} else if (!$this->Session->check('displayHRD010shinki') && $display == "false") {

			$this->redirect('/HRD010/shinki');
			return;
		}

		//性別
		$arrayList = $this->MMeisyo->getMeisyoPDO("16");
		$this->set('kbn_sex',$arrayList);

		//派遣コンボ設定
		$arrayList = $this->HRD010Model->getHaken();
		$this->set("haken_kaisya_cd",$arrayList);

		//拠点選択
		$arrayList = $this->HRD010Model->getKyoten();
		$this->set("kyoten",$arrayList);

		//役職
		$arrayList = $this->MMeisyo->getMeisyoPDO("17");
		$this->set("kbn_pst",$arrayList);

		//契約形態
		$arrayList = $this->MMeisyo->getMeisyoPDO("30");
		$this->set("kbn_keiyaku",$arrayList);

		//ピッキング
		$arrayList = $this->MMeisyo->getMeisyoPDO("19");
		$this->set("kbn_pick",$arrayList);

		//優先ゾーン
		$arrayList = $this->MMeisyo->getMeisyoPDO("05");
		$this->set("zone",$arrayList);

		//給与ランク
		$arrayList = $this->HRD010Model->getRANK();
		$this->set("rank",$arrayList);

		//前回の処理でエラーが発生した場合、前回のデータをそのまま表示
		if ($this->Session->check('saveTableHRD010')) {

			//前回のデータを取得
			$data = $this->Session->read('saveTableHRD010');
			$this->set('data',$data);

			//データ構造が違うのでレンダリングを変更
			$this->render("shinki_error");

			//前回のデータを削除
			$this->Session->delete('saveTableHRD010');

		}
	}

	/**
	 * 画像アップデート
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function image_upload(){
		
		$this->autoLayout = false;
		$this->autoRender = false;

		// ファイルがない
		if(!isset($_FILES['imagePath'])){

			echo "<html><head>";
			echo "<script type='text/javascript' src='/DCMS/js/DCMSMessage.js'></script>";
			echo "</head><body>";
			echo "<script type='text/javascript'>";
			echo "alert(DCMSMessage.format('CMNE000020'))";
			echo "</script></body></html>";
			return;
		}


		// ファイル内容取得
		$file = $_FILES['imagePath'];

		// ファイルアップロードに関するエラー 0:成功
		if($file['error'] != '0'){

			echo "<html><head>";
			echo "<script type='text/javascript' src='/DCMS/js/DCMSMessage.js'></script>";
			echo "</head><body>";
			echo "<script type='text/javascript'>";
			echo "alert(DCMSMessage.format('CMNE000020'))";
			echo "</script></body></html>";
			return;
		}

		//ファイル名チェック
		$checkArray = array ("jpg","jpeg","gif","png","JPG","JPEG","GIF","PNG");

		if($file['name'] != ''){
			// ファイル拡張子チェック
			$finfo = pathinfo($file['name']);
			$ext   = $finfo["extension"];
			if (!in_array($ext, $checkArray)){
				echo "<html><head>";
				echo "<script type='text/javascript' src='/DCMS/js/DCMSMessage.js'></script>";
				echo "</head><body>";
				echo "<script type='text/javascript'>";
				echo "alert(DCMSMessage.format('CMNE000021'))";
				echo "</script></body></html>";
				return;
			}
		}else{

			echo "<html><head>";
			echo "<script type='text/javascript' src='/DCMS/js/DCMSMessage.js'></script>";
			echo "</head><body>";
			echo "<script type='text/javascript'>";
			echo "alert(DCMSMessage.format('CMNE000020'))";
			echo "</script></body></html>";
			return;
		}

		// 各種設定 → 設定ファイルに書くとか？その辺は適当に
		$image_path     = $file['tmp_name'];                                // アップロードしたファイルの保存先
		$save_path      = TMP;                                       // 画像保存先
		$saveUploadFile =  $save_path . md5(uniqid(rand(), true)) . '.png'; // 保存するファイルパスおよびファイル名
		$max_width      = 150;                                              // 横幅
		$max_height     = 150;                                              // 高さ
		$quality        = 90;                                               // PNG、JPEG時のクオリティー

		// 画像の情報を取得
		$size = getimagesize($image_path);

		// ファイルから画像の作成。画像のタイプによって関数を使い分ける
		switch($size[2]) {
			case IMAGETYPE_GIF:
				$image = imagecreatefromgif($image_path);
				break;
			case IMAGETYPE_JPEG:
				$image = imagecreatefromjpeg($image_path);
				break;
			case IMAGETYPE_PNG:
				$image = imagecreatefrompng($image_path);
				break;
			default:
				$image = imagecreatefrompng($image_path);
				break;
		}

		// 指定したサイズ以上のものを縮小
		$width  = $size[0];
		$height = $size[1];
		// 横幅設定
		if($width > $max_width) {
			$height *= $max_width / $width;
			$width = $max_width;
		}

		// 高さ設定
		if($height > $max_height) {
			$width *= $max_height / $height;
			$height = $max_height;
		}

		// 新規画像の作成
		$new_image = imagecreatetruecolor($width, $height);
		// GIFとPNGの透過情報をあれこれ
		if($size[2] === IMAGETYPE_GIF || $size[2] === IMAGETYPE_PNG) {
			$index = imagecolortransparent($image);
			if($index >= 0) {
				$color = imagecolorsforindex($image, $index);
				$alpha = imagecolorallocate($new_image, $color['red'], $color['green'], $color['blue']);
				imagefill($new_image, 0, 0, $alpha);
				imagecolortransparent($new_image, $alpha);
			} else if($size[2] === IMAGETYPE_PNG) {
				imagealphablending($new_image, false);
				$color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
				imagefill($new_image, 0, 0, $color);
				imagesavealpha($new_image, true);
			}
		}
		// リサンプル
		imagecopyresampled($new_image, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
		// ファイルの保存
		imagepng($new_image, $saveUploadFile, floor($quality * 0.09));

		// メモリ上の画像データを破棄
		imagedestroy($image);
		imagedestroy($new_image);
		// 戻り値でファイル名を戻します。

		$result = "";
		//バイナリファイル読込
		if (file_exists($saveUploadFile)){

			if (!($fp = fopen($saveUploadFile, 'rb'))) exit;
			flock($fp, 2);
			$size = filesize($saveUploadFile);
			$result = fread($fp, $size);
			fclose($fp);

		}

		// データを読み込めなかった場合
		if(!$result){
			echo "<html><head>";
			echo "<script type='text/javascript' src='/DCMS/js/DCMSMessage.js'></script>";
			echo "</head><body>";
			echo "<script type='text/javascript'>";
			echo "alert(DCMSMessage.format('CMNE000020'))";
			echo "</script></body></html>";
			return;
		}else{
			// base64エンコード
			$dispImage  =  base64_encode($result);
			// バイナリデータ読込後アップロードしたファイルを削除
			unlink($saveUploadFile);
		}

		//以下処理後の出力
		echo "<html><head>";
		echo "<script type='text/javascript' src='/DCMS/js/DCMSMessage.js'></script>";
		echo "</head><body>";
		echo "<script type='text/javascript'>";
		echo "window.parent.showImage('" . $dispImage ."')";
		echo "</script></body></html>";

		return;
	}

	/**
	 * 新規登録画面データ更新
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function dataShinkiUpdate() {

		//データのやり取りだけなのでレンダリングなし
		$this->autoLayout =false;
		$this->autoRender = false;


		//ポストデータの存在確認エラー
		if(!$this->request->is('post')) {

			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000104'));
			return;
		}

		//データをJSONに変換する
		$data = json_decode($this->request->input());

		//データが空の場合、エラー
		if(count($data) == 0) {

			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000101'));
			return;
		}

		//全データを一時保存
		$this->Session->write('saveTableHRD010',$data);

		//入力チェック
		if (!$this->HRD010Model->checkKihonData($data)) {

			//エラー吐き出し
			$array = $this->HRD010Model->errors;
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
		if (!$this->HRD010Model->setShinkiData($this->Session->read('staff_cd'),$data)) {

			//エラー吐き出し
			$array = $this->HRD010Model->errors;
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
		$this->Session->delete('saveTableHRD010');

		//成功時メッセージ
		echo "return_cd=" . "0" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('HRDI010002'));
	}

	/**
	 * 基本画面データ更新
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function dataKihonUpdate() {

		//データのやり取りだけなのでレンダリングなし
		$this->autoLayout =false;
		$this->autoRender = false;


		//ポストデータの存在確認エラー
		if(!$this->request->is('post')) {

			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000104'));
			return;
		}

		//データをJSONに変換する
		$data = json_decode($this->request->input());

		//データが空の場合、エラー
		if(count($data) == 0) {

			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000101'));
			return;
		}

		//全データを一時保存
		$this->Session->write('saveTableHRD010',$data);

		//入力チェック
		if (!$this->HRD010Model->checkKihonData($data,1)) {

			//エラー吐き出し
			$array = $this->HRD010Model->errors;
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
		if (!$this->HRD010Model->setKoshinData($this->Session->read('staff_cd'),$data)) {

			//エラー吐き出し
			$array = $this->HRD010Model->errors;
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
		$this->Session->delete('saveTableHRD010');

		//成功時メッセージ
		echo "return_cd=" . "0" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNI000001'));
	}

	/**
	 * 品質情報
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function hinshitsu(){

		//Javascript設定
		$this->set("onload","initReload();
				initPopUp('".Router::url('/HRD010/popup_hinshitsu_update', false)."');
				initAddRow('".Router::url('/HRD010/popup_hinshitsu_insert', false)."');
				initUpdate('".Router::url('/HRD010/dataHinshitsuUpdate', false)."');");

		//検索条件、ページID取得
		$pageID = 1;
		$return_cd = '';
		$display = '';
		$message = '';
		$staff_cd = '';

		//ページNo
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

		//スタッフコード
		if(isset($this->request->query['staff_cd'])) {
			$staff_cd = $this->request->query['staff_cd'];
		}

		//スタッフ存在チェック
		$this->existsStaff($staff_cd);

		//メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {
			$this->Session->write('displayHRD010hinshitsu','true');
			$this->redirect('/HRD010/hinshitsu?staff_cd=' . $staff_cd . '&return_cd=' . $return_cd . '&message=' . $message . '&pageID=' . $pageID . '&display=false');
			return;

		} else if ($this->Session->check('displayHRD010hinshitsu') && $display == "false") {
			$this->Session->delete('displayHRD010hinshitsu');

		} else if (!$this->Session->check('displayHRD010hinshitsu') && $display == "false") {
			$this->redirect('/HRD010/hinshitsu?staff_cd=' . $staff_cd . '&pageID=' . $pageID);
			return;
		}

		//スタッフ
		$arrayList = $this->HRD010Model->getStaff($staff_cd);
		$this->set("staff",$arrayList);

		//品質一覧取得
		$lsts = $this->HRD010Model->getStaffHinsituList($staff_cd);

		//ページャー
		$arrayList = $this->createPaging($lsts, $pageID, 'hinshitsu');

		//エラーの場合
		if ($this->Session->check('saveTableHRD010hinshitsu')) {
			//データを取得
			$getArray = $this->Session->read('saveTableHRD010hinshitsu');

			$data = $getArray->data;
			$timestamp = $getArray->timestamp;

			$this->set("timestamp", $timestamp);
			$this->set('lsts',$data);
			$this->render("hinshitsu_error");

			$this->Session->delete('saveTableHRD010hinshitsu');

			// エラー以外で条件付きの場合
		} else {

			// タイムスタンプ取得
			$this->set("timestamp", $this->MCommon->getTimestamp());
			$this->set('lsts',$arrayList);
		}

	}

	/**
	 * 追加ダイアログ表示処理
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function popup_hinshitsu_insert() {
		$this->initHinshitsuDialog();
	}

	/**
	 * 追加ダイアログ表示処理
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function popup_hinshitsu_update() {
		$this->initHinshitsuDialog();
	}

	/**
	 * ダイアログ表示の初期化
	 */
	private function initHinshitsuDialog() {
		$this->autoLayout = false;

		// 名称マスタ取得
		//品質区分
		$kbnHinshitsu = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.hnst'));
		$this->set('kbnHinshitsu', $kbnHinshitsu);

		//分類コンボ設定
		$this->createBunruiCombo();
	}

	/**
	 * 品質画面データ更新
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function dataHinshitsuUpdate() {
		$this->autoLayout = false;
		$this->autoRender = false;

		if(!$this->request->is('post')) {
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000104'));
			return;
		}

		// データを取得
		$getArray = json_decode($this->request->input());

		//データを一時保存
		$this->Session->write('saveTableHRD010hinshitsu',$getArray);

		//データリストを取得
		$data = $getArray->data;

		// 入力チェック
		$this->HRD010Model->validateHinshitsuData($data);

		$array = $this->HRD010Model->errors;

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

		// 更新処理
		if (!$this->HRD010Model->setHinshitsuData($data, $getArray->staff_cd, $this->Session->read('staff_cd'))) {
			$array = $this->HRD010Model->errors;
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
		$this->Session->delete('saveTableHRD010hinshitsu');

		// 成功メッセージ設定
		echo "return_cd=" . "0" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNI000001'));

	}

	/**
	 * カルテ情報
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function karte(){

		// Javascript設定
		$this->set("onload","initReload();
				initUpdate('".Router::url('/HRD010/dataKarteUpdate', false)."');
				initGet('".Router::url('/HRD010/dataKarteGet', false)."');");

		// 検索条件、ページID取得
		$return_cd = '';
		$display = '';
		$message = '';
		$staff_cd = '';
		$hyoka_date = '';

		// リターンCD
		if(isset($this->request->query['return_cd'])) {
			$return_cd = $this->request->query['return_cd'];
		}

		// メッセージ
		if(isset($this->request->query['message'])) {
			$message = $this->MCommon->escapePHP($this->request->query['message']);
		}

		// メッセージディスプレイフラグ
		if(isset($this->request->query['display'])) {
			$display = $this->request->query['display'];
		}

		// スタッフコード
		if(isset($this->request->query['staff_cd'])) {
			$staff_cd = $this->request->query['staff_cd'];
		}

		// 評価日付
		if(isset($this->request->query['hyoka_date'])) {
			$hyoka_date = $this->request->query['hyoka_date'];
			if (!$this->MCommon->isDateYM($hyoka_date)) {
				$hyoka_date = "";
			}
		}

		// スタッフ存在チェック
		$this->existsStaff($staff_cd);

		// メッセージとリターンコードを出すか判断
		if (isset($this->request->query['display']) && $display == "true") {
			$this->Session->write('displayHRD010karte','true');
			$this->redirect('/HRD010/karte?staff_cd=' . $staff_cd . '&hyoka_date=' . $hyoka_date . '&return_cd=' . $return_cd . '&message=' . $message . '&display=false');
			return;

		} else if ($this->Session->check('displayHRD010karte') && $display == "false") {
			$this->Session->delete('displayHRD010karte');

		} else if (!$this->Session->check('displayHRD010karte') && $display == "false") {
			$this->redirect('/HRD010/karte?staff_cd=' . $staff_cd . '&hyoka_date=' . $hyoka_date);
			return;
		}

		// スタッフ
		$arrayList = $this->HRD010Model->getStaff($staff_cd);
		$this->set("staff",$arrayList);

		// 名称マスタ取得
		// 基本能力区分
		$kbnSkill = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.skill'));
		$this->set('kbnSkill', $kbnSkill);

		// 特殊能力区分
		$kbnSkillSp = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.skill_sp'));
		$this->set('kbnSkillSp', $kbnSkillSp);

		// 能力レベル区分
		$kbnSkillLv = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.skill_lv'));
		$this->set('kbnSkillLv', $kbnSkillLv);

		// エラーの場合
		if ($this->Session->check('saveTableHRD010karte')) {
			// データを取得
			$postData = $this->Session->read('saveTableHRD010karte');
			$hyoka_date = $postData->hyoka_date;

			// 画面用にデータを変換
			// カルテ情報
			$karteInfo = array (
					"YMD_HYOKA" => empty($hyoka_date) ? "" : date_format(date_create($hyoka_date . "01"),"Y/m"),
					"YMD_HYOKA_INPUT" => $postData->hyoka_date_input,
					"TUYOMI" => $postData->tuyomi,
					"YOWAMI" => $postData->yowami,
					"COMENT" => $postData->comment,
					"JAN_MOKUHYO" => $postData->mokuhyo1,
					"FEB_MOKUHYO" => $postData->mokuhyo2,
					"MAR_MOKUHYO" => $postData->mokuhyo3,
					"APR_MOKUHYO" => $postData->mokuhyo4,
					"MAY_MOKUHYO" => $postData->mokuhyo5,
					"JUN_MOKUHYO" => $postData->mokuhyo6,
					"JUL_MOKUHYO" => $postData->mokuhyo7,
					"AUG_MOKUHYO" => $postData->mokuhyo8,
					"SEP_MOKUHYO" => $postData->mokuhyo9,
					"OCT_MOKUHYO" => $postData->mokuhyo10,
					"NOV_MOKUHYO" => $postData->mokuhyo11,
					"DEC_MOKUHYO" => $postData->mokuhyo12,
					"JAN_TASSEIDO" => $postData->tasseido1,
					"FEB_TASSEIDO" => $postData->tasseido2,
					"MAR_TASSEIDO" => $postData->tasseido3,
					"APR_TASSEIDO" => $postData->tasseido4,
					"MAY_TASSEIDO" => $postData->tasseido5,
					"JUN_TASSEIDO" => $postData->tasseido6,
					"JUL_TASSEIDO" => $postData->tasseido7,
					"AUG_TASSEIDO" => $postData->tasseido8,
					"SEP_TASSEIDO" => $postData->tasseido9,
					"OCT_TASSEIDO" => $postData->tasseido10,
					"NOV_TASSEIDO" => $postData->tasseido11,
					"DEC_TASSEIDO" => $postData->tasseido12
			);

			// 基本能力
			$skillInfo = array();
			foreach ($postData->baseSkillArray as $baseSkill) {
				if (isset($baseSkill->kmk_lv_cd)) {
					$skillInfo[$baseSkill->kmk_nm_cd] = $baseSkill->kmk_lv_cd;
				}
			}

			// 特殊能力
			$skillSPInfo = array();
			foreach ($postData->SPSkillArray as $SPSkill) {
				if (isset($SPSkill->kmk_lv_cd)) {
					$skillSPInfo[$SPSkill->kmk_nm_cd] = $SPSkill->kmk_lv_cd;
				}
			}

			$this->set("timestamp", $postData->timestamp);
			$this->set('karteInfo',$karteInfo);
			$this->set('skillInfo',$skillInfo);
			$this->set('skillSPInfo', $skillSPInfo);
			// ページング情報設定
			$this->getDisplayKarteInfo($staff_cd, $hyoka_date);

			$this->Session->delete('saveTableHRD010karte');

		} else {
			// カルテ情報取得
			$karteInfo = $this->getDisplayKarteInfo($staff_cd, $hyoka_date);

			if ($karteInfo) {
				// 登録されているカルテがあったため、引き続き能力の取得を行う
				$hyoka_date = $karteInfo["YMD_HYOKA"];

				// 基本能力情報取得
				$skillInfo = $this->HRD010Model->getStaffSkillInfo($staff_cd, $hyoka_date);

				// 特殊能力情報取得
				$skillSPInfo = $this->HRD010Model->getStaffSkillSPInfo($staff_cd, $hyoka_date);

				// 画面表示用に日付をフォーマット
				$karteInfo["YMD_HYOKA"] = date_format(date_create($hyoka_date),"Y/m");

			} else {
				// まだこのスタッフのカルテが登録されていないため、新規登録用にデータを設定
				$karteInfo = array (
						"YMD_HYOKA" => "",
						"YMD_HYOKA_INPUT" => "",
						"TUYOMI" => "",
						"YOWAMI" => "",
						"COMENT" => "",
						"JAN_MOKUHYO" => "",
						"FEB_MOKUHYO" => "",
						"MAR_MOKUHYO" => "",
						"APR_MOKUHYO" => "",
						"MAY_MOKUHYO" => "",
						"JUN_MOKUHYO" => "",
						"JUL_MOKUHYO" => "",
						"AUG_MOKUHYO" => "",
						"SEP_MOKUHYO" => "",
						"OCT_MOKUHYO" => "",
						"NOV_MOKUHYO" => "",
						"DEC_MOKUHYO" => "",
						"JAN_TASSEIDO" => "",
						"FEB_TASSEIDO" => "",
						"MAR_TASSEIDO" => "",
						"APR_TASSEIDO" => "",
						"MAY_TASSEIDO" => "",
						"JUN_TASSEIDO" => "",
						"JUL_TASSEIDO" => "",
						"AUG_TASSEIDO" => "",
						"SEP_TASSEIDO" => "",
						"OCT_TASSEIDO" => "",
						"NOV_TASSEIDO" => "",
						"DEC_TASSEIDO" => "",
				);
				$skillInfo = array();
				$skillSPInfo = array();
			}

			$karteInfo["YMD_HYOKA_INPUT"] = date_format(date_create($hyoka_date),"Ym");

			// タイムスタンプ取得
			$this->set("timestamp", $this->MCommon->getTimestamp());
			$this->set('karteInfo', $karteInfo);
			$this->set('skillInfo', $skillInfo);
			$this->set('skillSPInfo', $skillSPInfo);
		}
	}

	/**
	 * 画面に表示するカルテ情報を取得する
	 *
	 * @param $staff_cd スタッフコード
	 * @param $hyoka_date 評価月
	 * @param $which "prev" or "next" を指定
	 * @return 表示対象のカルテ情報。なければnull（そのスタッフに１つもカルテが登録されていない場合）
	 */
	private function getDisplayKarteInfo($staff_cd, $hyoka_date = "", $which = "") {
		$karteInfoList = $this->HRD010Model->getStaffKarteInfo($staff_cd);
		$karteInfo = null;
		$isPrev = false;
		$isNext = false;

		if (empty($karteInfoList)) {
			return $karteInfo;
		}

		$this->set("alreadyExist", true);
		$resultCount = count($karteInfoList);
		$karteIndex = 0;

		if ($hyoka_date == "") {
			// 評価月の指定がないため最新を表示
			$karteIndex = 0;
			$karteInfo = $karteInfoList[$karteIndex];

		} else {
			// 評価月の指定があったため対象を探す
			for ($i = 0; $i < $resultCount; $i++) {
				$entity = $karteInfoList[$i];
				if (date_format(date_create($entity["YMD_HYOKA"]), "Ym") == $hyoka_date) {
					// 見つかった
					$karteIndex = $i;
					$karteInfo = $entity;
					break;
				}
			}

			if (is_null($karteInfo)) {
				// 見つからなかったため最新を表示
				// URLパラメータを直接編集された場合などに発生
				$karteIndex = 0;
				$karteInfo = $karteInfoList[$karteIndex];
			}
		}

		/**
		 * ページング表示有無判断
		 */
		$setPagingExistence = function () use (&$karteIndex, $resultCount, &$isPrev, &$isNext) {
			if ($karteIndex == 0) {
				// 「前へ」表示判定
				$isPrev = $resultCount > 1 ? true : false;
				$isNext = false;
			} else {
				// 「次へ」を表示判定
				$isNext = $karteIndex > 0 ? true : false;
				// 「前へ」を表示判定
				$isPrev = $karteIndex < $resultCount - 1 ? true : false;
			}
		};

		$setPagingExistence($karteIndex);

		if ($isPrev && $which == "prev") {
			// prevが存在している場合だけ有効にする
			$karteInfo = $karteInfoList[++$karteIndex];
			$setPagingExistence($karteIndex);
		} else if ($isNext && $which == "next") {
			// nextが存在している場合だけ有効にする
			$karteInfo = $karteInfoList[--$karteIndex];
			$setPagingExistence($karteIndex);
		}

		// 「前へ」を表示
		if ($isPrev) $this->set("isPrev", true);
		// 「次へ」を表示
		if ($isNext) $this->set("isNext", true);

		return $karteInfo;
	}

	/**
	 * カルテ画面データ更新
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function dataKarteUpdate() {
		$this->autoLayout = false;
		$this->autoRender = false;

		if(!$this->request->is('post')) {
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000104'));
			return;
		}

		// データを取得
		$postData = json_decode($this->request->input());

		//データを一時保存
		$this->Session->write('saveTableHRD010karte',$postData);

		// 入力チェック
		$this->HRD010Model->validateKarteData($postData);

		$array = $this->HRD010Model->errors;

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

		// 更新処理
		if (!$this->HRD010Model->setKarteData($postData, $this->Session->read('staff_cd'))) {
			$array = $this->HRD010Model->errors;
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
		$this->Session->delete('saveTableHRD010karte');

		// 成功メッセージ設定
		echo "return_cd=" . "0" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNI000001'));

	}

	/**
	 * カルテ画面データ取得
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function dataKarteGet() {
		$this->autoLayout = false;
		$this->autoRender = false;

		if(!$this->request->is('post')) {
			echo "return_cd=" . "1" . "&message=" .  base64_encode ($this->MMessage->getOneMessage('CMNE000104'));
			return;
		}

		// データを取得
		$postData = json_decode($this->request->input());
		$staff_cd = $postData->staff_cd;

		$karteInfo = $this->getDisplayKarteInfo($staff_cd, $postData->hyoka_date, $postData->which);
		$hyoka_date = $karteInfo["YMD_HYOKA"];

		// 基本能力情報取得
		$skillInfo = $this->HRD010Model->getStaffSkillInfo($staff_cd, $hyoka_date);

		// 特殊能力情報取得
		$skillSPInfo = $this->HRD010Model->getStaffSkillSPInfo($staff_cd, $hyoka_date);

		// 画面表示用に日付をフォーマット
		$date = date_create($hyoka_date);
		$karteInfo["YMD_HYOKA"] = date_format($date,"Y/m");
		$karteInfo["YMD_HYOKA_INPUT"] = date_format($date,"Ym");

		$resArray = array(
				"karteInfo" => $karteInfo,
				"skillInfo" => $skillInfo,
				"skillSPInfo" => $skillSPInfo,
				"isPrev" => isset($this->viewVars["isPrev"]),
				"isNext" => isset($this->viewVars["isNext"]),
		);

		$resData = json_encode($resArray);

		// カルテ情報返却
		return $resData;

	}

	/**
	 * 就業実績情報
	 *
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function jisseki() {
		//Javascript設定
		$this->set("onload","initReload();initBunrui();");

		$pageID = 1;
		$staff_cd = '';

		//ページNo
		if(isset($this->request->query['pageID'])) {
			$pageID = $this->request->query['pageID'];
		}
		$this->set("pageID", $pageID);
		$this->set("pageStartIdx", ($pageID - 1) * 50);

		//スタッフコード
		if(isset($this->request->query['staff_cd'])) {
			$staff_cd = $this->request->query['staff_cd'];
		}

		//スタッフ存在チェック
		$this->existsStaff($staff_cd);

		// スタッフ
		$arrayList = $this->HRD010Model->getStaff($staff_cd);
		$this->set("staff",$arrayList);

		// 分類コンボ設定
		$this->createBunruiCombo();

		// スタッフ就業実績直近情報 取得
		$jissekiNearList = $this->HRD010Model->getStaffSyugyoJskNear($staff_cd);
		$this->set("jissekiNearList",$jissekiNearList);

		if ($this->request->isGet()) {
			if ($this->Session->check('saveTableHRD010jisseki')) {
				$this->Session->delete('saveTableHRD010jisseki');
			}
			$this->set('jissekiList', array());
			// GETの場合は初期表示のためここで終了
			return;
		}

		$result = array();
		
		if (!empty($this->data)) {
		
			$koteiCd = $this->data['kotei'];

			if (!empty($koteiCd)) {
				$this->set("kotei_hidden",$koteiCd);
			}
		}

		// 入力チェック
		if ($this->HRD010Model->validateJissekiConditions($this->data)) {
			$jissekiList = $this->HRD010Model->getStaffSyugyoJsk($staff_cd, $this->data);
			


			if (empty($jissekiList)) {
//				// 0件メッセージ表示
//				$errorArray['error'][] = $this->MMessage->getOneMessage('HRDE010108');
//				$this->set("errors", $errorArray);
			}

			$result = $this->createPaging($jissekiList, $pageID, 'jisseki');

			// バリデータエラー時などに結果を復元させるため、センションに保存
			$this->Session->write('saveTableHRD010jisseki', $jissekiList);

		} else {
			// エラーの場合
			$array = $this->HRD010Model->errors;
			$message = '';
			foreach ( $array as $key=>$val ) {
				foreach ($val as $key2=>$val2) {
					$errorArray['error'][] = $val2;
				}
			}
			$this->set("errors", $errorArray);

// 			if ($this->Session->check('saveTableHRD010jisseki')) {
// 				// セッションに結果があれば復元
// 				$jissekiList = $this->Session->read('saveTableHRD010jisseki');
// 				$result = $this->createPaging($jissekiList, $pageID, 'jisseki');
// 			}
		}

		if ($this->data['periodType'] == 'daily') {
			$this->set('isDailyType', true);
		}

		$this->set('jissekiList', $result);
	}

	/**
	 * 存在するスタッフかを確認<br/>
	 * 存在しない場合、検索画面に遷移させる
	 */
	private function existsStaff($staff_cd) {
		//スタッフコードは必須
		if ($staff_cd == '') {
			$this->redirect('/HRD010/index?return_cd=1' . '&message=' . base64_encode($this->MMessage->getOneMessage('HRDE010001')) . '&display=true');
		}

		//存在チェック
		if (!$this->HRD010Model->checkStaff($staff_cd)) {
			//エラー吐き出し
			$array = $this->HRD010Model->errors;
			$message = '';

			foreach ( $array as $key=>$val ) {
				foreach ($val as $key2=>$val2) {
					$message = $message . $val2 . "<br>";
				}
			}

			$this->redirect('/HRD010/index?return_cd=1' . '&message=' . str_replace("+","%2B",base64_encode($message)) . '&display=true');
		}
	}

	/**
	 * 分類コンボのデータを設定
	 */
	private function createBunruiCombo() {
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

	/**
	 * ページングを作成
	 * @param unknown $lsts
	 * @param unknown $pageID
	 * @param unknown $pageName
	 * @return multitype:unknown
	 */
	private function createPaging($lsts, $pageID, $pageName, $httpMethod = 'GET') {
		require_once 'Pager.php';

		if (!$pageID) $pageID = 1;

		//ページャー
		$pageNum = 50;

		$options = array(
				"totalItems" => count($lsts),
				"delta" => 10,
				"perPage" => $pageNum,
				"httpMethod" => $httpMethod,
				"paramType" => "querystring",
				"path" => "/DCMS/HRD010/{$pageName}"
		);

		$pager = @Pager::factory($options);

		//ナビゲーションを設定
		$navi = $pager -> getLinks();
		$this->set('navi',$navi["all"]);

		//インデックスを取得
		$index = ($pageID - 1) * $pageNum;
		$arrayList = array();

		$limit = $index + $pageNum;

		if ($limit > count($lsts)) $limit = count($lsts);

		for($i = $index; $i < $limit ; $i++){
			$arrayList[] = $lsts[$i];
		}

		return $arrayList;
	}

}