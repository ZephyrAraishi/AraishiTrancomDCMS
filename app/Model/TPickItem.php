<?php
/**
 * t_pick_items
 *
 * ピッキングアイテムデータ
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('DCMSAppModel', 'Model');

class TPickItem extends DCMSAppModel {
	
	public $name = 'TPickItem';
		
	/**
	 * ピッキングアイテムデータ_棚欠_取得
	 * @access   public
	 * @return   ピッキングアイテムデータ
	 */
	public function getTPickItem_TKetu() {
		
		/* ▼結合条件設定 */
		$options['joins'] = array(
				array('type'       => 'INNER',
                       'table'      => 't_pick_s_kanris',
                       'alias'      => 'TPickSKanri',
                       'conditions' => array('TPickSKanri.id = TPickItem.t_pick_s_kanri_id'))				
				);
		
		/* ▼取得カラム設定 */
		$options['fields'] = array('TPickSKanri.zone',
				                    'TPickItem.s_batch_no_cd',
				                    'TPickItem.s_kanri_no',
				                    'TPickItem.location',
				                    'TPickItem.item_cd',
				                    'TPickItem.suryo_t_ketu'
				);
		
		/* ▼検索条件設定 */
		$options['conditions'] = array('TPickItem.ninusi_cd' => $this->_ninusi_cd,
				                        'TPickItem.sosiki_cd' => $this->_sosiki_cd,
		                                'TPickSKanri.kbn_status' => '11',
				                        'TPickItem.suryo_t_ketu >=' => 1);
						
		/* ▼ソート順設定 */
		$options['order'] = array('TPickItem.ymd_syori');
						
		// ▼ログイン情報取得
		$data = $this->find('all', $options);
		return $data;
	}

	/**
	 * <コールバック>
	 * 拠点情報編集処理
	 * @access   public
	 */
	public function afterFind($results,$primary=false) {
	
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
 