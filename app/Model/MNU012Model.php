<?php
/**
 * MNU012
 *
 * メニュー　機能選択画面
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('DCMSAppModel', 'Model');
App::uses('MMessage', 'Model');

class MNU012Model extends DCMSAppModel {
	
	public $name = 'MNU012Model';
	public $useTable = false;
	public $none_err_msg;
	
	/**
	 * コンストラクタ
	 * @access   public
	 * @param integer|string|array $id Set this ID for this model on startup, can also be an array of options, see above.
	 * @param string $table Name of database table to use.
	 * @param string $ds DataSource connection name.
	 */
	
	public function __construct($id = false, $table = null, $ds = null){
	
		/* ▼親クラスのコンストラクタ */
		parent::__construct($id, $table, $ds);
	
		/* ▼メッセージマスタ呼び出し */
		$this->MMessage = new MMessage($id, $table, $ds);
	
		/* ▼メッセージ識別条件設定 */
		$conditions = array(
				array(MMessage::msg_cd	=> 'MNUE012001'),
		);
	
		/* ▼メッセージ情報取得 */
		$data = $this->MMessage->getMessage($conditions);
	
		// 選択機能なしエラー
		$this->none_err_msg = $data['MNUE012001'];

	
	}
		
	/**
	 * スタッフ利用許可機能情報取得
	 * @access   public
	 * @return   スタッフ利用許可機能情報
	 */
	public function getKyoka() {
		
		/* ▼取得カラム設定 */
		$fields = '*';
		
		/* ▼検索条件設定 */
		$condition  = "STAFF_CD = '". $this->data['MNU012Model']['staff_cd']. "'";
		$condition .= " AND NINUSI_CD = '". $this->_ninusi_cd. "'";
		$condition .= " AND SOSIKI_CD = '". $this->_sosiki_cd. "'";
		
		
		// ▼スタッフ利用許可機能情報
		$sql  = 'SELECT ';
		$sql .= "$fields ";
		$sql .= 'FROM V_MNU012_KYOKA ';
		$sql .= 'WHERE ';
		$sql .= $condition;
		
		/* ▼ログ書込 */
		$data = $this->queryWithLog($sql, $condition, Configure::read("LogAccessMenu.kyoka"));
		//$data = $this->query($sql, false);
		$data = self::editData($data);
		
		return $data;
	}

	/**
	 * スタッフ利用許可機能情報編集処理
	 * @access   public
	 * @param    編集前情報
	 * @return   編集後情報
	 */
	public function editData($results) {
	
		// テーブル名のキー情報を削除しコントローラへ返却
		$data = array();
		foreach($results as $key => $value) {
			foreach($value as $key2 => $value2) {
				foreach($value2 as $key3 => $value3) {
					$data[$key][$key3] = $value3;
				}
			}
		}
		return $data;
	}
	
}

?>
 