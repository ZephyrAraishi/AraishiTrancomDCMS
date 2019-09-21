<?php
/**
 * M_Message
 *
 * メッセージマスタ
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */
App::uses('DCMSAppModel', 'Model');

class MMessage extends DCMSAppModel {
	
	public $name = 'MMessage';
	public $useTable = false;
	
	/**
	 * 定数 取得項目名
	 * @access   public
	 */
	const subsys_id = 'SUBSYS_ID';    // サブシステムID
	const msg_kbn   = 'MSG_KBN';      // メッセージ区分
	const msg_id    = 'MSG_ID';       // メッセージID
	const msg_txt   = 'MSG_TXT';      // メッセージ取得ID
	
	const msg_cd    = 'MSG_CD';        // サブシステムID/メッセージ区分/メッセージIDの結合フィールド
		
	
	/**
	 * メッセージ取得処理
	 * @access   public
	 * @param    サブシステムID
	 * @param    メッセージ区分
	 * @param    メッセージID
	 * @return   スタッフ情報
	 */
	public function getMessage($p_conditions) {
		
		/* ▼キャッシュがあるかのフラグ */
		$memcache_flg = 0;
		$chkMeVal     = '';
		$memcache     = '';
		
		try{
			// [Memcache]モジュールが導入されているかチェック
			if(extension_loaded('Memcache')) {
				/* ▼memcache読込 */
				$memcache = new Memcache();
				$memcache->connect('localhost', 11211);
				
				/* ▼キャッシュチェック */
				if(is_array($p_conditions)){
					foreach($p_conditions as $key => $val){
						// キャッシュキーを判定
						$chkMeVal = $val['MSG_CD'];
						// キャッシュデータが存在するかチェック
						if(!$memcache->get($chkMeVal)){
							// チェックフラグ更新
							$memcache_flg = 1;
							break;
						}else{
							// キャッシュデータ格納
							$data[$chkMeVal] = $memcache->get($chkMeVal);
						}
					}
				}
			}else{
				// チェックフラグ更新
				$memcache_flg = 1;
			}
			
		}catch (Exception $e){
			// チェックフラグ更新
			$memcache_flg = 1;
		}
		
		// キャッシュがなければSQLから取得
		if($memcache_flg == 1){
		
			/* ▼取得カラム設定 */
			
			// 取得項目設定
			// サブシステムID/メッセージ区分/メッセージIDの結合フィールドを定義
			$msg_cd  = 'concat('. self::subsys_id. 
	 	    			    ', '. self::msg_kbn. 
		    			    ', '. self::msg_id. ')';
			
			$fields  = $msg_cd. ' AS '. self::msg_cd;
			$fields .= ', '. self::msg_txt;
					
			/* ▼検索条件設定 */
			for ( $i = 0; $i <= count($p_conditions) - 1; ++$i ) {
		    	// OR条件
		    	if ($i == 0) {
		    		$conditions = $msg_cd. " = '". $p_conditions[$i][self::msg_cd]. "'";
		    	} else {
		    		$conditions .= " OR ". $msg_cd. " = '". $p_conditions[$i][self::msg_cd]. "'";
		    	}	            	    	
	        }
	        	
			/* ▼メッセージ情報取得 */
	        $sql  = 'SELECT ';
	        $sql .= "$fields ";
	        $sql .= 'FROM M_MESSAGE ';
	        $sql .= 'WHERE ';
	        $sql .= $conditions;
	        
	        $data	=	$this->queryWithLog($sql, null, "メッセージマスタ 取得");
	        $data = self::editData($data, $memcache);
		}
		
		return $data;
	}
	
	/**
	 * メッセージ取得処理
	 * @access   public
	 * @param    サブシステムID
	 * @param    メッセージ区分
	 * @param    メッセージID
	 * @return   スタッフ情報
	 */
	public function getOneMessage($p_conditions) {
		
		/* ▼キャッシュがあるかのフラグ */
		$memcache_flg = 0;
		$chkMeVal     = '';
		$memcache     = '';
		
		
		try{
			// [Memcache]モジュールが導入されているかチェック
			if(extension_loaded('Memcache')) {
				/* ▼memcache読込 */
				$memcache = new Memcache();
				$memcache->connect('localhost', 11211);
				/* ▼キャッシュチェック */
				if(!$memcache->get($p_conditions)){
					// チェックフラグ更新
					$memcache_flg = 1;
				}else{
					// キャッシュデータ格納
					$returnData = $memcache->get($p_conditions);
				}
			}else{
				// チェックフラグ更新
				$memcache_flg = 1;
			}
			
		}catch (Exception $e){
			// チェックフラグ更新
			$memcache_flg = 1;
		}
		// キャッシュがなければSQLから取得
		if($memcache_flg == 1){
		
			/* ▼取得カラム設定 */
			
			// 取得項目設定
			// サブシステムID/メッセージ区分/メッセージIDの結合フィールドを定義
			$msg_cd  = 'concat('. self::subsys_id. 
	 	    			    ', '. self::msg_kbn. 
		    			    ', '. self::msg_id. ')';
			
			$fields  = $msg_cd. ' AS '. self::msg_cd;
			$fields .= ', '. self::msg_txt;
					
			/* ▼検索条件設定 */
    		$conditions = $msg_cd. " = '". $p_conditions . "'";
	        	
			/* ▼メッセージ情報取得 */
	        $sql  = 'SELECT ';
	        $sql .= "$fields ";
	        $sql .= 'FROM M_MESSAGE ';
	        $sql .= 'WHERE ';
	        $sql .= $conditions;
	        
	        $data	=	$this->queryWithLog($sql, null , "メッセージマスタ 取得");
	        $data = self::editData($data, $memcache);
	        $returnData = $data[$p_conditions];
		}
		
		return $returnData;
	}
	
	/**
	 * メッセージ情報編集処理
	 * @access   public
	 * @param    編集前情報
	 * @return   編集後情報
	 */
	private function editData($results, $memcache) {
	
		// テーブル名のキー情報を削除しコントローラへ返却
		$data = array();
		foreach($results as $key => $value) {
			$data[$value[0][self::msg_cd]] = $value['M_MESSAGE'][self::msg_txt];
			// モジュールがなければセットしない
			if($memcache != ''){
				// memchaed のセット
				$memcache->set($value[0][self::msg_cd], $value['M_MESSAGE'][self::msg_txt]);
			}
		}
		return $data;
	}
	
}

	
