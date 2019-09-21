<?php
/**
 * HRD020
 *
 * 実績CSV抽出  
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
App::uses('HRD020Model', 'Model');

class HRD020Controller extends AppController {

	public $name = 'HRD020';
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
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['HRD']));
		// 機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['HRD']['020']['kino_nm']));

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

		$this->HRD020Model = new HRD020Model($this->Session->read('select_ninusi_cd'),
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

		require_once 'Pager.php';

		// キャッシュクリア
		Cache::clear();

		//Javascript設定
		$this->set("onload","initReload();initBunrui();");

		//派遣コンボ設定
		$arrayList = $this->HRD020Model->getHaken();
		$this->set("haken_kaisya_cd",$arrayList);

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

		if (isset($this->request->query['flg'])) {
			$flg = $this->request->query['flg'];
			if ($flg == 1) {
				$errors = $this->csvjinzai();
			} elseif ($flg == 2) {
				$errors = $this->csvsyugyo();
			} elseif ($flg == 3) {
				$errors = $this->csvkotei();
			}

			if (count($errors) != 0) {
				$this->set("errors", $errors);
				return;
			}
		}

	}

	function csvjinzai() {

		$staff_cd = $this->request->query['staff_cd1'];
		$haken_kaisya_cd = $this->request->query['haken_kaisya_cd1'];
		$staff_nm = $this->request->query['staff_nm1'];

		//未チェック時はchkkahiflg2自体飛んでこないので、ここでハンドリング
		if (isset($this->request->query['chkkahiflg1'])){
			$del_flg = $this->request->query['chkkahiflg1'];
		} else {
			$del_flg = "";
		}

		$buff = '';
		$bufHeader = 'スタッフコード,スタッフ氏名,派遣会社,役職,ピッキング,契約形態,給与ランク,所定時間単価,時間外手当,深夜時間手当,休出時間手当,法定休出時間手当,法定休出深夜時間手当,就業開始日,就業終了日,出勤日数上限,出勤時間上限,賃金上限';
		// 人材基本情報CSVデータを取得
		$csvData = null;
		$csvData = $this->HRD020Model->getJinzaiCsvData($staff_cd, $haken_kaisya_cd, $staff_nm, $del_flg);


		// 作成するファイル名の指定
		$fileName = "人材基本情報_" . date("Ymd") . ".CSV";
		foreach ($csvData as $data) {
			foreach ($data as $key => $value) {
				$buff .= '"' . $value . '",';
			}
			$buff .= "\r\n";
		}

		// CSVファイル出力
		$buff = mb_convert_encoding($bufHeader  . "\r\n" .  $buff, 'sjis-win', 'UTF-8');
		header("Content-type: application/octet-stream");
		header("Content-disposition: attachment; filename=" . $fileName);
		print($buff);
		exit();
		return;

	}

	function csvsyugyo() {

		$result = array();
		$startYmd = "";
		$endYmd = "";
		$staff_cd ="";
		$haken_kaisya_cd = "";
		$staff_nm = "";
		$del_flg = "";
		$buff = "";

		//20151120 Mofified By Morimatsu For Trancom
		//$bufHeader = '就業日,データ取込日,スタッフコード,スタッフ氏名,派遣会社,所定時間単価,時間外手当,深夜時間手当,休出時間手当,法定休出時間手当,法定休出深夜時間手当,出勤時打刻,退勤時打刻,拘束時間,出勤時間,退勤時間,給与換算時間,休憩１開始,休憩１終了,休憩１トータル,休憩１通常,休憩１深夜,休憩１通常(丸め),休憩１深夜(丸め),休憩２開始,休憩２終了,休憩２トータル,休憩２通常,休憩２深夜,休憩２通常(丸め),休憩２深夜(丸め),休憩３開始,休憩３終了,休憩３トータル,休憩３通常,休憩３深夜,休憩３通常(丸め),休憩３深夜(丸め),休憩４開始,休憩４終了,休憩４トータル,休憩４通常,休憩４深夜,休憩４通常(丸め),休憩４深夜(丸め),休憩５開始,休憩５終了,休憩５トータル,休憩５通常,休憩５深夜,休憩５通常(丸め),休憩５深夜(丸め),休憩時間(通常),休憩時間(深夜),休憩時間(合計),休憩時間丸め(通常),休憩時間丸め(深夜),休憩時間丸め(合計),通常時間,残業時間,深夜時間,通常時間丸め,残業時間丸め,深夜時間丸め,通常時間費用,残業時間費用,深夜時間費用,費用合計,通常時間費用丸め,残業時間費用丸め,深夜時間費用丸め,費用合計丸め';
		$bufHeader = '就業日,データ取込日,スタッフコード,スタッフ氏名,派遣会社,所定時間単価,時間外手当,深夜時間手当,休出時間手当,法定休出時間手当,法定休出深夜時間手当,出勤時打刻,退勤時打刻,拘束時間,出勤時間,退勤時間,給与換算時間,休憩時間';

		if (isset($this->request->query['start_ymd_ins2'])){
			$startYmd = $this->request->query['start_ymd_ins2'];
			$endYmd = $this->request->query['end_ymd_ins2'];
			$staff_cd = $this->request->query['staff_cd2'];
			$haken_kaisya_cd = $this->request->query['haken_kaisya_cd2'];
			$staff_nm = $this->request->query['staff_nm2'];

			//未チェック時はchkkahiflg2自体飛んでこないので、ここでハンドリング
			if (isset($this->request->query['chkkahiflg2'])){
				$del_flg = $this->request->query['chkkahiflg2'];
			} else {
				$del_flg = "";
			}
		}

		// 年月日（自）のチェック
		if (empty($startYmd)) {
			$result["start_ymd"][] = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array("年月日（自）"));
		} else {
			if (preg_match("/^[0-9]{4}[0-9]{2}[0-9]{2}/", $startYmd) == false) {
				$result["start_ymd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("年月日（自）"));
			} else if (checkdate(substr($startYmd, 4, 2), substr($startYmd, 6, 2), substr($startYmd, 0, 4)) == false) {
				$result["startYmdd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("年月日（自）"));
			}
		}

		// 年月日（至）のチェック
		if (empty($endYmd)) {
			$result["end_ymd_ins"][] = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array("年月日（至）"));
		} else {
			if (preg_match("/^[0-9]{4}[0-9]{2}[0-9]{2}/", $endYmd) == false) {
				$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("年月日（至）"));
			} else if (checkdate(substr($endYmd, 4, 2), substr($endYmd, 6, 2), substr($endYmd, 0, 4)) == false) {
				$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("年月日（至）"));
			}
		}

		// FROM～TOの相関チェック
		if (empty($result) && $endYmd < $startYmd) {
			$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090002'), array("年月日（至）", "年月日（自）"));
		}

		if (count($result) != 0) {
			return $result;
		}

		// 就業実績情報CSVデータを取得
		$csvData = null;
		$csvData = $this->HRD020Model->getSyugyoCsvData($startYmd, $endYmd, $staff_cd, $haken_kaisya_cd, $staff_nm,$del_flg);
		// 作成するファイル名の指定
		$fileName = "就業実績情報_" . date("Ymd") . ".CSV";

		foreach ($csvData as $data) {
			foreach ($data as $key => $value) {
				$buff .= '"' . $value . '",';
			}
			$buff .= "\r\n";
		}

		// CSVファイル出力

		$buff = $bufHeader . "\r\n" . $buff;
		header("Content-disposition: attachment; filename=" . $fileName);
		header("Content-type: application/octet-stream; name=" . $fileName);
		print(mb_convert_encoding($buff, 'sjis-win', 'UTF-8'));
		exit();

		return;

	}

	function csvkotei() {

		$result = array();
		$startYmd = "";
		$endYmd = "";
		$kotei = '';
		$daibunruicd ="";
		$cyubunruicd = "";
		$saibunruicd = "";
		$buff = "";
		$bufHeader = '処理日付,バッチ番号,バッチ名称,ゾーン,伝票単位-外箱数,伝票単位-内箱数,伝票単位-アイテム数,伝票単位-バラ数,スタッフコード,開始時間,終了時間,時間差,商品コード,ロケーション,明細-外箱数,明細-内箱数,明細-バラ数,明細-合計バラ数';

		if (isset($this->request->query['start_ymd_ins3'])){
			$startYmd = $this->request->query['start_ymd_ins3'];
			$endYmd = $this->request->query['end_ymd_ins3'];
			if (isset($this->request->query['kotei'])){
				$kotei = $this->request->query['kotei'];
				$daibunruicd = substr($kotei, 0,3);
				$cyubunruicd = substr($kotei, 4,3);
				$saibunruicd = substr($kotei, 8,3);

			}
		}

		// 年月日（自）のチェック
		if (empty($startYmd)) {
			$result["start_ymd"][] = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array("年月日（自）"));
		} else {
			if (preg_match("/^[0-9]{4}[0-9]{2}[0-9]{2}/", $startYmd) == false) {
				$result["start_ymd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("年月日（自）"));
			} else if (checkdate(substr($startYmd, 4, 2), substr($startYmd, 6, 2), substr($startYmd, 0, 4)) == false) {
				$result["startYmdd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("年月日（自）"));
			} else if (date("Ymd") <= $startYmd) {
				$result["startYmdd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("年月日（自）"));
			}
		}

		// 年月日（至）のチェック
		if (empty($endYmd)) {
			$result["end_ymd_ins"][] = vsprintf($this->MMessage->getOneMessage('CMNE000001'), array("年月日（至）"));
		} else {
			if (preg_match("/^[0-9]{4}[0-9]{2}[0-9]{2}/", $endYmd) == false) {
				$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("年月日（至）"));
			} else if (checkdate(substr($endYmd, 4, 2), substr($endYmd, 6, 2), substr($endYmd, 0, 4)) == false) {
				$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("年月日（至）"));
			} else if (date("Ymd") <= $endYmd) {
				$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090001'), array("年月日（至）"));
			}
		}

		// FROM～TOの相関チェック
		if (empty($result) && $endYmd < $startYmd) {
			$result["endYmd"][] = vsprintf($this->MMessage->getOneMessage('WEXE090002'), array("年月日（至）", "年月日（自）"));
		}

		if (count($result) != 0) {
			return $result;
		}

		// 工程実績情報CSVデータを取得
		$csvData = null;
		$csvData = $this->HRD020Model->getKoteiCsvData($startYmd, $endYmd, $daibunruicd, $cyubunruicd, $saibunruicd);

		// 作成するファイル名の指定
		$fileName = "工程実績情報_" . date("Ymd") . ".CSV";
		foreach ($csvData as $data) {
			foreach ($data as $key => $value) {
				$buff .= '"' . $value . '",';
			}
			$buff .= "\r\n";
		}

		// CSVファイル出力

		$buff = $bufHeader . "\r\n" . $buff;
		header("Content-disposition: attachment; filename=" . $fileName);
		header("Content-type: application/octet-stream; name=" . $fileName);
		print(mb_convert_encoding($buff, 'sjis-win', 'UTF-8'));
		exit();

		return;

	}

}