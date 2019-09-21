<?php
/**
 * MNU010
 *
 * メニュー　ログイン画面
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('DCMSAppModel', 'Model');
App::uses('MMessage', 'Model');

class MNU010Model extends DCMSAppModel {
	
	public $name = 'MNU010Model';
	public $useTable = false;
	
	// バリデート
	public $validate;
    public $login_err_msg; 
    public $login_err_msg_del;
    public $login_ok_msg;
	
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
				array(MMessage::msg_cd	=> 'MNUE010001'),
				array(MMessage::msg_cd	=> 'MNUE010002'),
				array(MMessage::msg_cd	=> 'MNUE010003'),
				array(MMessage::msg_cd	=> 'MNUE010004'),
				array(MMessage::msg_cd	=> 'MNUE010005'),
				array(MMessage::msg_cd	=> 'MNUI010001'),
		);
		
		/* ▼メッセージ情報取得 */
		$data = $this->MMessage->getMessage($conditions);
		
		// バリデートエラー
		$this->validate = array(
				'staff_cd' => array(
						          array('rule' => 'notEmpty', 'message' => $data['MNUE010001']) ,
						          array('rule' => 'numeric', 'message' => $data['MNUE010002']) 
						      ),
				'password' => array('rule' => 'notEmpty', 'message' => $data['MNUE010003'])
		);
		
		// ログインエラー
		$this->login_err_msg = $data['MNUE010004'];
		
		// ログインエラー（スタッフ削除）
		$this->login_err_msg_del = $data['MNUE010005'];
		
		// ログイン完了
		$this->login_ok_msg = $data['MNUI010001'];
		
	}
		
	/**
	 * ログイン処理
	 * @access   public
	 * @return   スタッフ情報
	 */
	public function loginStaff() {
	
		/* ▼取得カラム設定 */
		$fields = '*'; 
				
		/* ▼検索条件設定 */
		$condition = array('staff_cd' => $this->data['MNU010Model']['staff_cd'],
		                  'password' => $this->data['MNU010Model']['password']);
		
		/* ▼ログイン情報取得 */
		$sql  = 'SELECT ';
		$sql .= "$fields ";
		$sql .= 'FROM V_MNU010_LOGIN ';
		$sql .= 'WHERE ';
		$sql .= ' STAFF_CD = :staff_cd';
		$sql .= ' AND PASSWORD = :password';
		
		
		$data = $this->query($sql, $condition, false);
		$data = self::editData($data);
			
		return $data;
	}
	
	/**
	 * ログインスタッフ情報編集処理
	 * @access   public
	 * @param    編集前情報
	 * @return   編集後情報
	 */
	private function editData($results) {
		
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
 