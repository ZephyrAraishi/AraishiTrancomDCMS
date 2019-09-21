<?php
/**
 * MNU011
 *
 * メニュー　拠点選択画面
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('DCMSAppModel', 'Model');
App::uses('MMessage', 'Model');

class MNU011Model extends DCMSAppModel {
	
	public $name = 'MNU011Model';
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
		$this->MMessage = new MMessage();
	
		/* ▼メッセージ識別条件設定 */
		$conditions = array(
				array(MMessage::msg_cd	=> 'MNUE011001'),
		);
	
		/* ▼メッセージ情報取得 */
		$data = $this->MMessage->getMessage($conditions);
	
		// 選択機能なしエラー
		$this->none_err_msg = $data['MNUE011001'];
	
	
	}
	
	/**
	 * 拠点情報取得
	 * @access   public
	 * @return   拠点情報
	 */
	public function getKyoten() {
		
		/* ▼取得カラム設定 */
		$fields = '*';
		
		/* ▼検索条件設定 */
		$condition  = "STAFF_CD = '". $this->data['MNU011Model']['staff_cd']. "'";
		
		/* ▼拠点情報取得 */
		$sql  = 'SELECT ';
		$sql .= "$fields ";
		$sql .= 'FROM V_MNU011_KYOTEN ';
		$sql .= 'WHERE ';
		$sql .= $condition;
		
		$data = $this->query($sql, false);
		$data = self::editData($data);
		
		return $data;
	}
	
	/**
	 * 拠点情報編集処理
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
 