<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppModel', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class DCMSAppModel extends AppModel {
	
	/**
	 * 【プロパティ】
	 * @access   private
	 */
	protected $_ninusi_cd = null;				 // 荷主コード
	protected $_sosiki_cd = null;                // 組織コード
	protected $_kino_id = null;                  // 機能ID
	
	protected $db_user_name;
	protected $db_user_password;
	protected $db_dsn;
	
	protected $oracle_host;
	protected $oracle_port;
	protected $oracle_sid;
	protected $oracle_user_name;
	protected $oracle_password;
	protected $oracle_home;
	
	/**
	 * コンストラクタ
	 * @access   public
	 * @param 機能ID
	 * @param モデルのID.
	 * @param モデルのテーブル名.
	 * @param モデルのデータソース.
	 */
	public function __construct($ninusi_cd = '0000000000', $sosiki_cd = '0000000000', $kino_id = '', $id = false, $table = null, $ds = null) {
	
		// 親クラスのコンストラクタ
		parent::__construct($id, $table, $ds);
	
		// 荷主コード/組織コードを設定（スタッフ基本情報以外）
		$this->_ninusi_cd = $ninusi_cd;
		$this->_sosiki_cd = $sosiki_cd;
		$this->_kino_id = $kino_id;
		
	    $db = ConnectionManager::getDataSource($this->useDbConfig);
	    $this->db_user_name = $db->config['login'];
	    $this->db_user_password = $db->config['password'];
	    $this->db_dsn = 'mysql:dbname='.$db->config['database'].';host='.$db->config['host'];
	    
	    $this->oracle_host = $db->config['oraHost'];
	    $this->oracle_port = $db->config['oraPort'];
	    $this->oracle_sid = $db->config['oraDatabase'];
	    $this->oracle_user_name = $db->config['oraLogin'];
	    $this->oracle_password = $db->config['oraPassword'];
	    $this->oracle_home = $db->config['homedirectory'];
	}
	
	/**
	 * PDO用exec
	 * @access   public
	 * @param    PDOオブジェクト
	 * @param    SQL
	 * @param    プロセス名
	 */
	public function execWithPDOLog(&$pdo,$sql,$processName = '') {	
		
		try {
			$t = microtime(true);
			
	  		$pdo->exec($sql);
	  		//$this->printLog($sql);
	  		
	  		/* ▼経過時間、返却行数 */
			$took		=	round((microtime(true) - $t) , 4);
			$dataSource	=	$this->getDataSource();
			$rows		=	$dataSource->lastAffected();
			/* ▼経過時間、桁数 統一*/
			$tooks		=	number_format($took , '3');
			// 返却行数が10以下の場合は0を付加
			if($rows < 10){
				$rows	=	"0" . $rows;
			}
	   		
	   		$kino_nm = '';
			
			if (!empty($this->_kino_id)) {
				$kino_nm = $this->_kino_id;
			}
			
	  		$this->printLog('info', $processName, $kino_nm,"経過時間[{$tooks}]". $sql);
		} catch (Exception $e) {

			throw $e;
		}
	}
	
	/**
	 * PDO用query
	 * @access   public
	 * @param    PDOステートメント
	 * @param    PDOオブジェクト
	 * @param    SQL
	 * @param    プロセス名
	 */
	public function queryWithPDOLog(&$stmt,&$pdo,$sql,$processName = '') {	
			
		try {
			$t = microtime(true);
			
	  		$stmt = $pdo->query($sql);
			$took = round((microtime(true) - $t) , 4);
	  		$dataSource	=	$this->getDataSource();
			$rows		=	$dataSource->lastAffected();
			/* ▼経過時間、桁数 統一*/
			$tooks		=	number_format($took , '3');
			// 返却行数が10以下の場合は0を付加
			if($rows < 10){
				$rows	=	"0" . $rows;
			}
			
			$kino_nm = '';
			
			if (!empty($this->_kino_id)) {
				$kino_nm = $this->_kino_id;
			}
	   		
	   		$this->printLog('info', $processName, $kino_nm,"経過時間[{$tooks}]". $sql);
	   		
		} catch (Exception $e) {
	  
			throw $e;
		}
	}
	
	
	/**
	 * CakePHP標準SQLクエリー発行
	 * @access   public
	 * @param    バインドする変数
	 * @param    プロセス名
	 * @return   void
	 */
	public function queryWithLog($sql, $conditionVal=null, $processName = ''){
		
		/* ▼SQL発行 */
		if ($conditionVal == null) {
			$data	=	$this->query($sql, false);
			
		} else {
			$data	=	$this->query($sql, $conditionVal, false);	
		}
		/* ▼時間測定開始 */
		$t	=	microtime(true);
		
		/* ▼経過時間、返却行数 */
		$took		=	round((microtime(true) - $t) , 4);
		$dataSource	=	$this->getDataSource();
		$rows		=	$dataSource->lastAffected();
		/* ▼経過時間、桁数 統一*/
		$tooks		=	number_format($took , '3');
		// 返却行数が10以下の場合は0を付加
		if($rows < 10){
			$rows	=	"0" . $rows;
		}
		
		/* ▼文字列置換 */
		if(is_array($conditionVal)){
			foreach($conditionVal as $key => $val){
				$value = substr($val, 0, 1);
				if($value == '0'){
					$sql = str_replace(":".$key, "'".$val."'", $sql);
				}else{
					if(is_numeric($val)) {
						$sql = str_replace(":".$key, $val, $sql);
					}else{
						$sql = str_replace(":".$key, "'".$val."'", $sql);
					}
				}
			}
			$logSql = $sql;
		}else{
			$logSql = $sql;
		}
		
		$kino_nm = '';
		
		if (!empty($this->_kino_id)) {
			$kino_nm = $this->_kino_id;
		}
		
		/* ▼ログ書込 */
		$this->printLog('info', $processName, $kino_nm,"経過時間[{$tooks}]". $logSql);
		
		return $data;
	}
	
	/**
	 * SQLログ出力
	 * @access   public
	 * @param    レベル
	 * @param    プロセス名
	 * @param    機能名
	 * @param    メッセージ
	 */
	public function printLog($level, $processName, $menuName, $message){
	
		// セッションの[select_ninusi_cd] or [select_sosiki_cd]  がない場合はログを書かない
		if(!isset($_SESSION['select_ninusi_cd']) || !isset($_SESSION['select_sosiki_cd'])){
			return;
		}
		
		// ログファイルディレクトリ
		$fileDir		= Configure::read("LogDir.path") . $_SESSION['select_ninusi_cd'];
		// 日時
		$datetime		=	date( "Y-m-d H:i:s", time() );
		// クライアントのIP
		$clientIp		=	$_SERVER["REMOTE_ADDR"];
		// スタッフコード
		if(isset($_SESSION['staff_cd'])){
			$staffCode	=	$_SESSION['staff_cd'];
			if(strlen($staffCode) < 10){
				$space = "";
				for($i=0;$i<(10 - strlen($staffCode));$i++){
					// スタッフコードにスーペースを追加
					$space	= $space . ' ';
				}
				$staffCode = $staffCode . $space;
			}
		}else{
			// スタッフコードは「ログ書込初期スタッフコード」を設定
			$staffCode	=	Configure::read("LogStaffCord.default");
		}
		
		// メッセージ
		$msg			=	$datetime . " " . strtoupper($level) . " [" . $clientIp . "][" . $staffCode . "][" . $menuName . "][" . $processName . "] " . $message . "\r\n";
		
		// 文字コード変換
		$msg = mb_convert_encoding($msg, "UTF-8","AUTO");
		
		// ログレベルの判定
		$logLevel = "LogLevel." . $level;
		$readLogLevel = Configure::read($logLevel);
		
		
		// ログレベルが設定されていない場合は「error.log」に出力
		if(!isset($readLogLevel)){
			$this->log($msg);
			return;
		}
		
		// ディレクトリがない場合は作成する(階層1)
		if (!file_exists($fileDir)) {
			mkdir($fileDir . "/" , 0755);
		}
		
		// ディレクトリがない場合は作成する(階層2)
		$fileDir = $fileDir . "/" . $_SESSION['select_sosiki_cd'] . '/';
		if (!file_exists($fileDir)) {
			mkdir($fileDir . "/" , 0755);
		}
		
		// 書き込みファイル
		$writeFile		=	$fileDir . date( "Y-m-d", time()). ".log";
		
	    // ログ書込
		$fp = @fopen($writeFile , "a");
		@fwrite($fp, $msg);
		@fclose($fp);

		return;
	}
}

