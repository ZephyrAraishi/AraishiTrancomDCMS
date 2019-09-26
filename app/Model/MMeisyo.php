<?php
/**
 * M_Meisyo
 *
 * 名称マスタ
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('DCMSAppModel', 'Model');

class MMeisyo extends DCMSAppModel {
	
	public $name = 'MMeisyo';
	public $useTable = false;
	
	/**
	 * 定数　取得項目名
	 * @access   public
	 */
	const mei_kbn = 'MEI_KBN';                  // 名称区分
	const mei_cd = 'MEI_CD';                    // 名称コード
	const mei_1 = 'MEI_1';                      // 名称１
	const mei_2 = 'MEI_2';                      // 名称２
	const mei_3 = 'MEI_2';                      // 名称３
	const val_free_str_1 = 'VAL_FREE_STR_1';    // 名称値フリー１
	const val_free_str_2 = 'VAL_FREE_STR_2';    // 名称値フリー２
	const val_free_str_3 = 'VAL_FREE_STR_3';    // 名称値フリー３
	const val_free_num_1 = 'VAL_FREE_NUM_1';    // 名称値フリー数値１
	const val_free_num_2 = 'VAL_FREE_NUM_2';    // 名称値スリー数値２
	const val_free_num_3 = 'VAL_FREE_NUM_3';    // 名称値フリー数値３
			
	/**
	 * 名称マスタ取得
	 * @access   public
	 * @param    名称区分
	 * @param    名称コード　(初期時は対象区分全件取得)
	 * @param    取得項目　　(初期時は名称１を取得)
	 * @param    ソート項目　(初期時は名称コードでソート)
	 * @return   名称情報
	 */
	public function getMeisyo($mei_kbn, $mei_cd = null, $items = array(self::mei_1), $orders = array(self::mei_cd)) {
		
		/* ▼取得カラム設定 */
		$fields = self::mei_cd;
		
		foreach ($items as $val) {
			$fields .= ", $val";
		}
		
		/* ▼検索条件設定 */
		// 標準検索条件
		$defaultCondition  = "NINUSI_CD = '$this->_ninusi_cd' ";
		$defaultCondition .= "AND SOSIKI_CD = '$this->_sosiki_cd' ";
		$defaultCondition .= 'AND ' .self::mei_kbn. " = '$mei_kbn' ";
		
		// 検索条件設定
		if (isset($mei_cd)) {
			// 追加検索条件
			if (is_array($mei_cd)) {
				// 配列の場合はIN句による条件構築
				$addCondition = 'AND ' .self::mei_cd. ' IN (';
				foreach ($mei_cd as $key => $value) {
					if ($key == 0) {
						$addCondition .= "'$value'";
					} else {
						$addCondition .= ", '$value'";
					}
				}
				$addCondition .= ')';
			} else {
				// 配列でない場合は直接問い合わせ
				$addCondition = 'AND ' .self::mei_cd. " = '$mei_cd' ";
			}
			
			// 名称コードが設定されている場合は検索条件を追加
			$condition = $defaultCondition.$addCondition;
		} else {
			// 名称コード未設定の場合は標準検索条件をそのまま追加
			$condition = $defaultCondition;
		}
		
		/* ▼ソート順設定 */
		$order = '';
		foreach ($orders as $val) {
			$order .= ", $val";
		}
		$order = substr($order, 2);
				                        
		// ▼名称マスタ情報取得
		$sql  = 'SELECT ';
		$sql .= "$fields ";
		$sql .= 'FROM M_MEISYO ';
		$sql .= 'WHERE ';
		$sql .= $condition;
		$sql .= 'ORDER BY ';
		$sql .= $order;
		
		/* ▼ログ書込 */
		if(isset($_SESSION['select_sosiki_cd'])){
			$data	=	$this->queryWithLog($sql, $condition, "名称マスタ　取得");
		}else{
			$data	=	$this->queryWithLog($sql, $condition, "名称マスタ　取得");
		}
		
		if (count($items) == 1) {
			$data = self::editHashTblData($data);
		} else {
			$data = self::editData($data);
		}
		
		return $data;
	}
	
	/**
	 * 名称情報編集処理
	 * @access   public
	 * @param    編集前情報
	 * @return   編集後情報
	 */
	private function editData($results) {
	
		// テーブル名のキー情報を削除しコントローラへ返却
		$data = array();

		foreach($results as $key => $value) {
			foreach($value as $key2 => $value2) {
				
				$mei_cd = '';
				$array = array();
			
				foreach($value2 as $key3 => $value3) {
				
					if ($key3 == self::mei_cd) {
						$mei_cd = $value3;
					} else {
						$array[$key3] = $value3;
					}
				}
				
				foreach($array as $key4 => $value4) {
					
					$data[$mei_cd][$key4] = $value4;
				}
			}
		}
		return $data;
	}
	
	/**
	 * 名称ハッシュテーブル(キーに対する名称）情報編集処理
	 * @access   public
	 * @param    編集前情報
	 * @return   編集後情報
	 */
	private function editHashTblData($results) {
	
		// テーブル名のキー情報を削除しコントローラへ返却
		$data = array();
	
		foreach($results as $key => $value) {
			foreach($value as $key2 => $value2) {
	
				$mei_cd = '';
					
				foreach($value2 as $key3 => $value3) {
	
					if ($key3 == self::mei_cd) {
						$mei_cd = $value3;
					} else {
						$data[$mei_cd] = $value3;
					}
				}
			}
		}
		return $data;
	}
		
	
	/**
	 * PDO版名称マスタ取得
	 * @access   public
	 * @param    名称区分
	 * @param    名称コード　(初期時は対象区分全件取得)
	 * @param    取得項目　　(初期時は名称１を取得)
	 * @param    ソート項目　(初期時は名称コードでソート)
	 * @return   名称情報
	 */
	public function getMeisyoPDO($mei_kbn, $mei_cd = null) {
		
		$pdo = null;
		$memcache_flg = 0;
		
		try {
			
			// [Memcache]モジュールが導入されているかチェック
			if(extension_loaded('Memcached')) {
				/* ▼memcache読込 */
				$memcache = new Memcached();
				$memcache->addServer('localhost', 11211);
				// キャッシュが保存されているかチェック
				if(!$memcache->get('M_MEISYO_' . $this->_ninusi_cd . '_' . $this->_sosiki_cd . '_' . $mei_kbn . '_' . $mei_cd )){
					$memcache_flg = 1;
				}
				
			}else{
				// チェックフラグ更新
				$memcache_flg = 1;
			}
			
			// キャッシュがなければSQLから取得
			if($memcache_flg == 1){
			
				//DBオブジェクト取得
				$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
				        
				// ▼名称マスタ情報取得
				$sql  = "SELECT";
				$sql .= "  MEI_CD,";
				$sql .= "  MEI_1,";
				$sql .= "  MEI_2,";
				$sql .= "  MEI_3,";
				$sql .= "  VAL_FREE_STR_1,";
				$sql .= "  VAL_FREE_STR_2,";
				$sql .= "  VAL_FREE_STR_3,";
				$sql .= "  VAL_FREE_NUM_1,";
				$sql .= "  VAL_FREE_NUM_2,";
				$sql .= "  VAL_FREE_NUM_3";
				$sql .= " FROM";
				$sql .= "  M_MEISYO";
				$sql .= ' WHERE';
				$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
				$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
				$sql .= "  MEI_KBN='" . $mei_kbn . "'";
				
				if ($mei_cd != null) {
					
					$sql .= ' AND';
					$sql .= " MEI_CD='" . $mei_cd . "'";
				}
				
				$sql .= 'ORDER BY';
				$sql .= ' MEI_CD';
	
				$this->queryWithPDOLog($stmt,$pdo,$sql,"名称マスタ　取得");
					
				$arrayList = array();
		
				while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
		
					$array = array(
						"MEI_CD" => $result['MEI_CD'],
					    "MEI_1" => $result['MEI_1'],
					    "MEI_2" => $result['MEI_2'],
					    "MEI_3" => $result['MEI_3'],
					    "VAL_FREE_STR_1" => $result['VAL_FREE_STR_1'],
					    "VAL_FREE_STR_2" => $result['VAL_FREE_STR_2'],
					    "VAL_FREE_STR_3" => $result['VAL_FREE_STR_3'],
					    "VAL_FREE_NUM_1" => $result['VAL_FREE_NUM_1'],
					    "VAL_FREE_NUM_2" => $result['VAL_FREE_NUM_2'],
					    "VAL_FREE_NUM_3" => $result['VAL_FREE_NUM_3']
					);
					
					$arrayList[] = $array;
					
				}
				
				$pdo = null;
				
				// [Memcache]モジュールが導入されているかチェック
				if(extension_loaded('Memcached')) {
					// [Memcache]モジュールにセット
					$memcache->set('M_MEISYO_' . $this->_ninusi_cd . '_' . $this->_sosiki_cd . '_' . $mei_kbn . '_' . $mei_cd, $arrayList);
				}
				
				return  $arrayList;
				
			} else {
				
				// [Memcache]モジュールから取得
				$arrayList = $memcache->get('M_MEISYO_' . $this->_ninusi_cd . '_' . $this->_sosiki_cd . '_' . $mei_kbn . '_' . $mei_cd );
				
				return  $arrayList;
			}

			return $arrayList;
		
		} catch (Exception $e) {
		
			$pdo = null;
		}
		
		return array();
	}
}
 