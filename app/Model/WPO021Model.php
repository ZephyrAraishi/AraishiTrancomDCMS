<?php
/**
 * WPO021
 *
 * 作業進捗　棚欠品リスト
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('DCMSAppModel', 'Model');

class WPO021Model extends DCMSAppModel {
	
	public $name = 'WPO021Model';
	public $useTable = false;
	
	/**
	 * ピッキングアイテムデータ_棚欠_取得
	 * @access   public
	 * @return   ピッキングアイテムデータ
	 */
	public function getTPickItem_TKetu($ymd_syori,$s_batch_no_cd,$s_kanri_no) {		
	
	
		$pdo = null;
	
		try{
				
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
			$sql  = "CALL P_WPO021_GET_TANAKETSU(";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'". $s_batch_no_cd. "'," ;
			$sql .= "'". $s_kanri_no. "'," ;
			$sql .= "'". $ymd_syori. "'" ;
			$sql .= ")";
	
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'WPO021 棚欠取得');
				
			$arrayList = array();
				
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){

				$array = array(
				   "S_BATCH_NO_CD" => $result['S_BATCH_NO_CD'],
				   "BATCH_CD" => $result['BATCH_CD'],
				   "S_BATCH_NO" => $result['S_BATCH_NO'],
				   "ZONE" => $result['ZONE'],
				   "S_KANRI_NO" => $result['S_KANRI_NO'],
				   "LOCATION" => $result['LOCATION'],
				   "ITEM_CD" => $result['ITEM_CD'],
				   "ITEM_NM" => $result['ITEM_NM'],
				   "SURYO_T_KETU" => $result['SURYO_T_KETU']
				);
				
				$arrayList[] = $array;

			}
			
				
			$pdo = null;
				
			return $arrayList;
				
		} catch (Exception $e){
	
			$this->printLog("fatal", "例外発生", "WPO021", $e->getMessage());
			$pdo = null;
		}
		
		return array();
		
	}
	
}

?>
 