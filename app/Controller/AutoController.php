<?php
/**
 * AutoController
 *
 * 自動処理のみ
 *
 * @category      Auto
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('DCMSAppModel', 'Model');
App::uses('WPO050Model', 'Model');

class AutoController extends AppController {

	public $name = 'Auto';
	public $uses = array();
	public $ninusi_cd1 = "0000000001";
	public $sosiki_cd1 = "0000000001";
	public $layout = "DCMS";


	/**
	 * コントロール起動時処理
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function beforeFilter() {

		parent::beforeFilter();
		$this->autoLayout =false;
		$this->autoRender = false;


		//WPO050モデル
		$this->WPO050Model = new WPO050Model($this->ninusi_cd1,$this->sosiki_cd1,$this->name);

	}

	/**
	 * 初期表示
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function index(){

		echo "Forbidden";
	}

	/**
	 * 更新処理
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	function shimeAuto1(){

		//データのやり取りだけなのでレンダリングなし
		$this->autoLayout =false;
		$this->autoRender = false;
		
		$ninusi_cd = '';
		$sosiki_cd = '';
		$ymd_syori = '';
		
		if(isset($this->request->query['ninusi_cd'])) {
			
			$ninusi_cd = $this->request->query['ninusi_cd'];
		}
		
		if(isset($this->request->query['sosiki_cd'])) {
			
			$sosiki_cd= $this->request->query['sosiki_cd'];
		}
		
		if(isset($this->request->query['ymd_syori'])) {
			
			$ymd_syori = $this->request->query['ymd_syori'];
		}
		
		//データ取得
		if (!$this->WPO050Model->setAutoShime($ninusi_cd,$sosiki_cd,$ymd_syori)) {

			//エラー吐き出し
			$array = $this->WPO050Model->errors;
			$message = '';

			foreach ( $array as $key=>$val ) {

				foreach ($val as $key2=>$val2) {

					$message = $message . $val2 . "<br>";
				}
			}

			echo $message;
		}

		echo "Success";

	}

}
