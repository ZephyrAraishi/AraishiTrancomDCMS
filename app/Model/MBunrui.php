<?php
/**
 * MBunrui
 *
 * 各種分類マスタ
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('DCMSAppModel', 'Model');

class MBunrui extends DCMSAppModel {
	
	public $name = 'MBunrui';
	public $useTable = false;
	
		
	//分類マスタ
	
	//分類マスタ(大)
	public function getBunruiDaiPDO($mode = 0) {
		
		/* ▼キャッシュがあるかのフラグ */
		$memcache_flg = 0;
		$pdo = null;
		
		try{
			// [Memcache]モジュールが導入されているかチェック
			if(extension_loaded('Memcached')) {
				/* ▼memcache読込 */
				$memcache = new Memcached();
				$memcache->addServer('localhost', 11211);
				// キャッシュが保存されているかチェック
				if(!$memcache->get('M_BNRI_DAI_' . $this->_ninusi_cd . '_' . $this->_sosiki_cd . '_' . $mode)){
					$memcache_flg = 1;
				}
			}else{
				// チェックフラグ更新
				$memcache_flg = 1;
			}
			// キャッシュがなければSQLから取得
			if($memcache_flg == 1){
				$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			
				$sql = "";
			
				if ($mode == "0") {
			
					$sql = "";
					$sql .= "SELECT ";
					$sql .= "  A.BNRI_DAI_CD,";
					$sql .= "  A.BNRI_DAI_NM,";
					$sql .= "  A.BNRI_DAI_RYAKU";
					$sql .= " FROM";
					$sql .= "  M_BNRI_DAI A";
					$sql .= " WHERE ";
					$sql .= "  A.NINUSI_CD='" . $this->_ninusi_cd ."' AND";
					$sql .= "  A.SOSIKI_CD='" . $this->_sosiki_cd ."'";
					$sql .= " GROUP BY";
					$sql .= "  A.BNRI_DAI_CD ";
					$sql .= " ORDER BY";
					$sql .= "  A.BNRI_DAI_CD ";
					
				} else if ($mode == "1") {
					
					$sql = "";
					$sql .= "SELECT ";
					$sql .= "  A.BNRI_DAI_CD,";
					$sql .= "  A.BNRI_DAI_NM,";
					$sql .= "  A.BNRI_DAI_RYAKU";
					$sql .= " FROM";
					$sql .= "  M_BNRI_DAI A";
					$sql .= "    LEFT JOIN M_CALC_TGT_KOTEI B ON";
					$sql .= "      A.NINUSI_CD=B.NINUSI_CD AND";
					$sql .= "      A.SOSIKI_CD=B.SOSIKI_CD AND";
					$sql .= "      A.BNRI_DAI_CD=B.BNRI_DAI_CD AND";
					$sql .= "      B.KOTEI_KBN=2";
					$sql .= " WHERE ";
					$sql .= "  A.NINUSI_CD='" . $this->_ninusi_cd ."' AND";
					$sql .= "  A.SOSIKI_CD='" . $this->_sosiki_cd ."' AND";
					$sql .= "  B.BNRI_DAI_CD IS NULL";
					$sql .= " GROUP BY";
					$sql .= "  A.BNRI_DAI_CD ";
					$sql .= " ORDER BY";
					$sql .= "  A.BNRI_DAI_CD ";
				}

				$this->queryWithPDOLog($stmt,$pdo,$sql, "大分類マスタ 取得");

				$bunruiArray = array();
				$count = 0;
				
				while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

					$bunruiArray[$count]['BNRI_DAI_CD']    = $result['BNRI_DAI_CD'];
					$bunruiArray[$count]['BNRI_DAI_NM']    = $result['BNRI_DAI_NM'];
					$bunruiArray[$count]['BNRI_DAI_RYAKU'] = $result['BNRI_DAI_RYAKU'];
					
					$count++;
				}
				// [Memcache]モジュールが導入されているかチェック
				if(extension_loaded('Memcached')) {
					// [Memcache]モジュールにセット
					$memcache->set('M_BNRI_DAI_' . $this->_ninusi_cd . '_' . $this->_sosiki_cd  . '_' . $mode, $bunruiArray);
				}
			}else{
				// [Memcache]モジュールから取得
				$bunruiArray = $memcache->get('M_BNRI_DAI_' . $this->_ninusi_cd . '_' . $this->_sosiki_cd . '_' . $mode);
			}
				
			$pdo = null;
		    return $bunruiArray;

		}catch (Exception $e){
		
			$pdo = null;
		}
			

		return array();
	}
	
	//分類マスタ(中
	public function getBunruiCyuPDO($mode = 0) {
		
		/* ▼キャッシュがあるかのフラグ */
		$memcache_flg = 0;
		$pdo = null;
		
		try{
			// [Memcache]モジュールが導入されているかチェック
			if(extension_loaded('Memcached')) {
				/* ▼memcache読込 */
				$memcache = new Memcached();
				$memcache->addServer('localhost', 11211);
				// キャッシュが保存されているかチェック
				if(!$memcache->get('M_BNRI_CYU_' . $this->_ninusi_cd . '_' . $this->_sosiki_cd . '_' . $mode)){
					$memcache_flg = 1;
				}
			}else{
				// チェックフラグ更新
				$memcache_flg = 1;
			}
			
			// キャッシュがなければSQLから取得
			if($memcache_flg == 1){
				$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			
				$sql = "";
			
				if ($mode == "0") {
			
					$sql = "";
					$sql .= "SELECT ";
					$sql .= "  BNRI_DAI_CD,";
					$sql .= "  BNRI_CYU_CD,";
					$sql .= "  BNRI_CYU_NM,";
					$sql .= "  BNRI_CYU_RYAKU";
					$sql .= " FROM";
					$sql .= "  M_BNRI_CYU";
					$sql .= " WHERE ";
					$sql .= "  NINUSI_CD='" . $this->_ninusi_cd ."' AND";
					$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd ."'";
					$sql .= " ORDER BY";
					$sql .= "  BNRI_DAI_CD,BNRI_CYU_CD";
					
				} else if ($mode == "1") {
				
					$sql = "";
					$sql .= "SELECT ";
					$sql .= "  C.BNRI_DAI_CD,";
					$sql .= "  C.BNRI_CYU_CD,";
					$sql .= "  C.BNRI_CYU_NM,";
					$sql .= "  C.BNRI_CYU_RYAKU";
					$sql .= " FROM";
					$sql .= "  M_BNRI_DAI A";
					$sql .= "    LEFT JOIN M_CALC_TGT_KOTEI B ON";
					$sql .= "      A.NINUSI_CD=B.NINUSI_CD AND";
					$sql .= "      A.SOSIKI_CD=B.SOSIKI_CD AND";
					$sql .= "      A.BNRI_DAI_CD=B.BNRI_DAI_CD AND";
					$sql .= "      B.KOTEI_KBN=2";
					$sql .= "    INNER JOIN M_BNRI_CYU C ON";
					$sql .= "      A.NINUSI_CD=C.NINUSI_CD AND";
					$sql .= "      A.SOSIKI_CD=C.SOSIKI_CD AND";
					$sql .= "      A.BNRI_DAI_CD=C.BNRI_DAI_CD";
					$sql .= " WHERE ";
					$sql .= "  A.NINUSI_CD='" . $this->_ninusi_cd ."' AND";
					$sql .= "  A.SOSIKI_CD='" . $this->_sosiki_cd ."' AND";
					$sql .= "  B.BNRI_DAI_CD IS NULL AND";
					$sql .= "  B.BNRI_CYU_CD IS NULL";
					$sql .= " GROUP BY";
					$sql .= "  A.BNRI_DAI_CD,C.BNRI_CYU_CD";
					$sql .= " ORDER BY";
					$sql .= "  A.BNRI_DAI_CD,C.BNRI_CYU_CD";
				
				}

				$this->queryWithPDOLog($stmt,$pdo,$sql, "中分類マスタ 取得");

				$bunruiArray = array();
				$count = 0;
				
				while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
					
					$bunruiArray[$count]['BNRI_DAI_CD']    = $result['BNRI_DAI_CD'];
					$bunruiArray[$count]['BNRI_CYU_CD']    = $result['BNRI_CYU_CD'];
					$bunruiArray[$count]['BNRI_CYU_NM']    = $result['BNRI_CYU_NM'];
					$bunruiArray[$count]['BNRI_CYU_RYAKU'] = $result['BNRI_CYU_RYAKU'];

					$count++;
				}
				// [Memcache]モジュールが導入されているかチェック
				if(extension_loaded('Memcached')) {
					// [Memcache]モジュールにセット
					$memcache->set('M_BNRI_CYU_' . $this->_ninusi_cd . '_' . $this->_sosiki_cd . '_' . $mode, $bunruiArray);
				}
			}else{
				// [Memcache]モジュールから取得
				$bunruiArray = $memcache->get('M_BNRI_CYU_' . $this->_ninusi_cd . '_' . $this->_sosiki_cd . '_' . $mode);
			}
			
			$pdo = null;
		    return $bunruiArray;

		}catch (Exception $e){
		
			$pdo = null;
		}
			

		return array();
	}
	
	//分類マスタ(細)
	public function getBunruiSaiPDO($mode = 0) {
		
		/* ▼キャッシュがあるかのフラグ */
		$memcache_flg = 0;
		$pdo = null;
		
		try{
			
			// [Memcache]モジュールが導入されているかチェック
			if(extension_loaded('Memcached')) {
				/* ▼memcache読込 */
				$memcache = new Memcached();
				$memcache->addServer('localhost', 11211);
				// キャッシュが保存されているかチェック
				if(!$memcache->get('M_BNRI_SAI_' . $this->_ninusi_cd . '_' . $this->_sosiki_cd . '_' . $mode)){
					$memcache_flg = 1;
				}
			}else{
				// チェックフラグ更新
				$memcache_flg = 1;
			}
			
			// キャッシュがなければSQLから取得
			if($memcache_flg == 1){
				$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
				
				$sql = "";
			
				if ($mode == "0") {
				
					$sql = "";
					$sql .= "SELECT ";
					$sql .= "  BNRI_DAI_CD,";
					$sql .= "  BNRI_CYU_CD,";
					$sql .= "  BNRI_SAI_CD,";
					$sql .= "  BNRI_SAI_NM,";
					$sql .= "  BNRI_SAI_RYAKU";
					$sql .= " FROM";
					$sql .= "  M_BNRI_SAI";
					$sql .= " WHERE ";
					$sql .= "  NINUSI_CD='" . $this->_ninusi_cd ."' AND";
					$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd ."'";
					$sql .= " ORDER BY";
					$sql .= "  BNRI_DAI_CD,BNRI_CYU_CD,BNRI_SAI_CD";
					
					
				} else if ($mode == "1") {
				
					$sql = "";
					$sql .= "SELECT ";
					$sql .= "  A.BNRI_DAI_CD,";
					$sql .= "  C.BNRI_CYU_CD,";
					$sql .= "  D.BNRI_SAI_CD,";
					$sql .= "  D.BNRI_SAI_NM,";
					$sql .= "  D.BNRI_SAI_RYAKU";
					$sql .= " FROM";
					$sql .= "  M_BNRI_SAI A";
					$sql .= "    LEFT JOIN M_CALC_TGT_KOTEI B ON";
					$sql .= "      A.NINUSI_CD=B.NINUSI_CD AND";
					$sql .= "      A.SOSIKI_CD=B.SOSIKI_CD AND";
					$sql .= "      A.BNRI_DAI_CD=B.BNRI_DAI_CD AND";
					$sql .= "      A.BNRI_CYU_CD=B.BNRI_CYU_CD AND";
					$sql .= "      A.BNRI_SAI_CD=B.BNRI_SAI_CD AND";
					$sql .= "      B.KOTEI_KBN=2";
					$sql .= "    INNER JOIN M_BNRI_CYU C ON";
					$sql .= "      A.NINUSI_CD=C.NINUSI_CD AND";
					$sql .= "      A.SOSIKI_CD=C.SOSIKI_CD AND";
					$sql .= "      A.BNRI_DAI_CD=C.BNRI_DAI_CD";
					$sql .= "    INNER JOIN M_BNRI_SAI D ON";
					$sql .= "      A.NINUSI_CD=D.NINUSI_CD AND";
					$sql .= "      A.SOSIKI_CD=D.SOSIKI_CD AND";
					$sql .= "      C.BNRI_DAI_CD=D.BNRI_DAI_CD AND";
					$sql .= "      C.BNRI_CYU_CD=D.BNRI_CYU_CD";
					$sql .= " WHERE ";
					$sql .= "  A.NINUSI_CD='" . $this->_ninusi_cd ."' AND";
					$sql .= "  A.SOSIKI_CD='" . $this->_sosiki_cd ."' AND";
					$sql .= "  B.BNRI_DAI_CD IS NULL AND";
					$sql .= "  B.BNRI_CYU_CD IS NULL AND";
					$sql .= "  B.BNRI_SAI_CD IS NULL";
					$sql .= " GROUP BY";
					$sql .= "  A.BNRI_DAI_CD,C.BNRI_CYU_CD,D.BNRI_SAI_CD";
					$sql .= " ORDER BY";
					$sql .= "  A.BNRI_DAI_CD,C.BNRI_CYU_CD,D.BNRI_SAI_CD";
				
				}

				$this->queryWithPDOLog($stmt,$pdo,$sql, "細分類マスタ 取得");

				$bunruiArray = array();
				$count = 0;

				while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

					$bunruiArray[$count]['BNRI_DAI_CD']    = $result['BNRI_DAI_CD'];
					$bunruiArray[$count]['BNRI_CYU_CD']    = $result['BNRI_CYU_CD'];
					$bunruiArray[$count]['BNRI_SAI_CD']    = $result['BNRI_SAI_CD'];
					$bunruiArray[$count]['BNRI_SAI_NM']    = $result['BNRI_SAI_NM'];
					$bunruiArray[$count]['BNRI_SAI_RYAKU'] = $result['BNRI_SAI_RYAKU'];

					$count++;
				}
				// [Memcache]モジュールが導入されているかチェック
				if(extension_loaded('Memcached')) {
					// [Memcache]モジュールにセット
					$memcache->set('M_BNRI_SAI_' . $this->_ninusi_cd . '_' . $this->_sosiki_cd . '_' . $mode, $bunruiArray);
				}
			}else{
				// [Memcache]モジュールから取得
				$bunruiArray = $memcache->get('M_BNRI_SAI_' . $this->_ninusi_cd . '_' . $this->_sosiki_cd . '_' . $mode);
			}

			$pdo = null;
		    return $bunruiArray;

		}catch (Exception $e){
		
			$pdo = null;
		}
			

		return array();
	}
	
	// memcache 分類全削除
	public function deleteMemcache() {
	
		// [Memcache]モジュールが導入されているかチェック
		if(extension_loaded('Memcached')) {
			
			/* ▼memcache読込 */
			$memcache = new Memcached();
			$memcache->addServer('localhost', 11211);
			
			// キャッシュデータ削除
			$memcache->delete('M_BNRI_DAI_' . $this->_ninusi_cd . '_' . $this->_sosiki_cd . '_0'); // 大分類
			$memcache->delete('M_BNRI_CYU_' . $this->_ninusi_cd . '_' . $this->_sosiki_cd . '_0'); // 中分類
			$memcache->delete('M_BNRI_SAI_' . $this->_ninusi_cd . '_' . $this->_sosiki_cd . '_0'); // 細分類
			
			// キャッシュデータ削除
			$memcache->delete('M_BNRI_DAI_' . $this->_ninusi_cd . '_' . $this->_sosiki_cd . '_1'); // 大分類
			$memcache->delete('M_BNRI_CYU_' . $this->_ninusi_cd . '_' . $this->_sosiki_cd . '_1'); // 中分類
			$memcache->delete('M_BNRI_SAI_' . $this->_ninusi_cd . '_' . $this->_sosiki_cd . '_1'); // 細分類
		}
		return;
	}
}

 