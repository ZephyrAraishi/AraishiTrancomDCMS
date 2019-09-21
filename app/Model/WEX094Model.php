<?php
/**
 * WEX094Model
 *
 * 月別推移
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');

class WEX094Model extends DCMSAppModel{
	
	public $name = 'WEX094Model';
	public $useTable = false;
	
	public $errors = array();
	
	/**
	 * コンストラクタ
	 * @access   public
	 * @param    荷主コード
	 * @param    組織コード
	 * @param integer|string|array $id Set this ID for this model on startup, can also be an array of options, see above.
	 * @param string $table Name of database table to use.
	 * @param string $ds DataSource connection name.
	*/
	function __construct($ninusi_cd = '0000000000', $sosiki_cd = '0000000000', $kino_id = '', $id = false, $table = null, $ds = null) {
		parent::__construct($ninusi_cd, $sosiki_cd,$kino_id, $id, $table, $ds);
		 
		$this->MMessage = new MMessage($ninusi_cd, $sosiki_cd, $kino_id);
		$this->MMeisyo = new MMeisyo($ninusi_cd, $sosiki_cd, $kino_id);
	}
	
	/**
	 * 改善データ情報取得
	 * 
	 * @param $kznDataNo 改善データ番号
	 * @return 改善データ情報
	 */
	function getKaizenData($kznDataNo) {
		
		$fields = '*';
		 
		// 検索条件を設定
		$conditionVal = array();
		$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
		$conditionVal['sosiki_cd'] = $this->_sosiki_cd;
		$conditionVal['kzn_data_no'] = $kznDataNo;
		 
		// 発行するSQLを設定
		$sql  = "select ";
		$sql .= "$fields ";
		$sql .= "from ";
		$sql .= "	V_WEX094_T_KAIZEN_DB ";
		$sql .= "where ";
		$sql .= "    NINUSI_CD = :ninusi_cd ";
		$sql .= "and SOSIKI_CD = :sosiki_cd ";
		$sql .= "and KZN_DATA_NO = :kzn_data_no ";
		
		// 改善データ情報を取得
		$data = $this->queryWithLog($sql, $conditionVal, "改善データ情報");
		$data = self::editData($data);
		
		return $data;
	}
	
	/**
	 * ABCデータベース日別情報取得
	 *
	 * @param $bnriDaiCd 大分類コード
	 * @param $bnriCyuCd 中分類コード
	 * @param $bnriSaiCd 細分類コード
	 * @param $startYmd 対象期間FROM
	 * @param $endYmd 対象期間TO
	 * @return ABCデータベース日別情報取得
	 */
	function getAbcDailyData($bnriDaiCd, $bnriCyuCd, $bnriSaiCd, $startYmd, $endYmd) {
	
		$fields = '*';
			
		// 検索条件を設定
		$conditionVal = array();
		$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
		$conditionVal['sosiki_cd'] = $this->_sosiki_cd;
		if (!empty($bnriDaiCd)) {
			$conditionVal['bnri_dai_cd'] = $bnriDaiCd;
		}
		if (!empty($bnriCyuCd)) {
			$conditionVal['bnri_cyu_cd'] = $bnriCyuCd;
		}
		if (!empty($bnriSaiCd)) {
			$conditionVal['bnri_sai_cd'] = $bnriSaiCd;
		}
		$conditionVal['start_ymd'] = $startYmd;
		$conditionVal['end_ymd'] = $endYmd;
			
		// 発行するSQLを設定
		$sql  = "select ";
		$sql .= "$fields ";
		$sql .= "from ";
		$sql .= "	V_WEX094_T_ABC_DB_DAY_LST ";
		$sql .= "where ";
		$sql .= "    NINUSI_CD = :ninusi_cd ";
		$sql .= "and SOSIKI_CD = :sosiki_cd ";
		if (!empty($bnriDaiCd)) {
			$sql .= "and BNRI_DAI_CD = :bnri_dai_cd ";
		}
		if (!empty($bnriCyuCd)) {
			$sql .= "and BNRI_CYU_CD = :bnri_cyu_cd ";
		}
		if (!empty($bnriSaiCd)) {
			$sql .= "and BNRI_SAI_CD = :bnri_sai_cd ";
		}
		$sql .= "and YMD between :start_ymd and :end_ymd ";
		$sql .= "order by ";
		$sql .= "    YMD desc ";
	
		// 改善データ情報を取得
		$data = $this->queryWithLog($sql, $conditionVal, "ABCデータベース日別情報");
		
		$data = self::editData($data);

		return $data;
	}
	
	/**
	 * SQL取得結果編集処理
	 * @access   public
	 * @param    $results 編集前情報
	 * @return   編集後情報
	 */
	private function editData($results) {
	
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