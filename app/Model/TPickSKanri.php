<?php
/**
 * t_pick_s_kanris
 *
 * ピッキング集計管理№データ
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('DCMSAppModel', 'Model');

class TPickSKanri extends DCMSAppModel {
	
	public $name = 'TPickSKanri';
		
	/**
	 * ゾーン別_ピッキング集計管理№データ取得
	 * @access   public
	 * @return   ピッキング集計管理№データ
	 */
	public function getTPickSKanri_Zone() {
		
		/* ▼結合条件設定 */
		$options['joins'] = array(
				array('type'       => 'INNER',
                       'table'      => 'm_meisyos',
                       'alias'      => 'MMeisyo',
                       'conditions' => array('MMeisyo.ninusi_cd = TPickSKanri.ninusi_cd',
                       		                  'MMeisyo.sosiki_cd = TPickSKanri.sosiki_cd',
                       		                  'MMeisyo.mei_cd = TPickSKanri.zone',
                       		                  'MMeisyo.mei_kbn' => '05'))				
				);
		
		/* ▼取得カラム設定 */
		$options['fields'] = array('MMeisyo.mei_1 AS zone_mei', 
				                    'Sum(1) As all_cnt',
				                    'Sum(Case TPickSKanri.kbn_status when "01" Then 1 Else 0 End) As syori_mi_cnt',
				                    'Sum(Case TPickSKanri.kbn_status when "02" Then 1 Else 0 End) As syori_cyu_cnt',
				                    'Sum(Case TPickSKanri.kbn_status when "11" Then 1 Else 0 End) As syori_sumi_cnt',
				                    'Sum(Case TPickSKanri.kbn_status when "09" Then 1 Else 0 End) As t_ketsu_cnt',
				                    'Sum(Case TPickSKanri.ymd_syori when "19" Then 1 Else 0 End) As t_ketsu_sumi_cnt');
		
		/* ▼検索条件設定 */
		$options['conditions'] = array('TPickSKanri.ninusi_cd' => $this->_ninusi_cd,
				                        'TPickSKanri.sosiki_cd' => $this->_sosiki_cd,
								        '(TPickSKanri.ymd_syori = current_date Or TPickSKanri.ymd_syori >= (Select Min(ymd_syori) From t_pick_s_kanris Where kbn_status in ("01","02","11")))');
						
		/* ▼グループ設定 */
		$options['group'] = array('MMeisyo.mei_1');
		
		/* ▼ソート順設定 */
		$options['order'] = array('MMeisyo.val_free_num_1');
						
		// ▼ログイン情報取得
		$data = $this->find('all', $options);
		return $data;
	}

	/**
	 * ゾーン別_ピッキング集計管理№データ取得
	 * @access   public
	 * @return   ピッキング集計管理№データ
	 */
	public function getTPickSKanri_time_tp() {
	
		/* ▼取得カラム設定 */
		$options['fields'] = array('Case TPickSKanri.kbn_status When "09" Then HOUR(TPickSKanri.dt_syori_sumi) When "19" Then HOUR(TPickSKanri.dt_t_ketsu_sumi) End As end_h',
									'Sum(TPickSKanri.suryo_piece) As sum_piece',
									'count(TPickSKanri.staff_cd) cnt_staff');
	
		/* ▼検索条件設定 */
		$options['conditions'] = array('TPickSKanri.ninusi_cd' => $this->_ninusi_cd,
										'TPickSKanri.sosiki_cd' => $this->_sosiki_cd,
										'TPickSKanri.ymd_syori' => '2013/02/07',
										'TPickSKanri.kbn_status in ("09","19")');
	
		/* ▼グループ設定 */
		$options['group'] = array('Case TPickSKanri.kbn_status When "09" Then HOUR(TPickSKanri.dt_syori_sumi) When "19" Then HOUR(TPickSKanri.dt_t_ketsu_sumi) End');
	
		/* ▼ソート順設定 */
		$options['order'] = array('end_h');
	
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
 