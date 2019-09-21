<?php
/**
 * WPO022
 *
 * 作業進捗　集計ピッキングリスト
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('DCMSAppModel', 'Model');

class WPO022Model extends DCMSAppModel {
	
	public $name = 'WPO022Model';
	public $useTable = false;
	
	/**
	 * ピッキングアイテムデータ_棚欠_取得
	 * @access   public
	 * @return   ピッキングアイテムデータ
	 */
	public function getTPickItem($ymd_syori,$s_batch_no_cd,$s_kanri_no) {		
	
	
		$pdo = null;
	
		try{
				
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
			$sql  = "CALL P_WPO022_GET_ITEMDATA(";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'". $s_batch_no_cd. "'," ;
			$sql .= "'". $s_kanri_no. "'," ;
			$sql .= "'". $ymd_syori. "'" ;
			$sql .= ")";
	
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'WPO022 集Ｐリスト');
				
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
				   "IRI_HAKO_SOTO" => $result['IRI_HAKO_SOTO'],
				   "IRI_HAKO_UCHI" => $result['IRI_HAKO_UCHI'],
				   "SURYO_HAKO_SOTO" => $result['SURYO_HAKO_SOTO'],
				   "SURYO_HAKO_UCHI" => $result['SURYO_HAKO_UCHI'],
				   "SURYO_BARA" => $result['SURYO_BARA'],
				   "SURYO_TOTAL" => $result['SURYO_TOTAL'],
				   "SURYO_T_KETU" => $result['SURYO_T_KETU']
				);
				
				$arrayList[] = $array;

			}
			
				
			$pdo = null;
				
			return $arrayList;
				
		} catch (Exception $e){
	
			$this->printLog("fatal", "例外発生", "WPO022", $e->getMessage());
			$pdo = null;
		}
		
		return array();
		
	}
	
}

?>
 