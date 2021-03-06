<?php
/**
 * StaffSearch
 *
 * メニュー　スタッフ検索画面
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('DCMSAppModel', 'Model');
App::uses('MMessage', 'Model');

class StaffSearchModel extends DCMSAppModel {

	public $name     = 'StaffSearchModel';
	public $useTable = false;
	public $mei_kbn  = '17'; // 派遣会社識別コード

	/**
	 * 派遣情報取得
	 * @access   public
	 * @return   拠点情報
	 */
	public function getDispatch($select_ninusi_cd, $select_sosiki_cd) {

		/* ▼取得カラム設定 */
		$fields = ' KAISYA_CD, KAISYA_NM ';

		/* ▼検索条件設定 */
		$condition  = "NINUSI_CD = '". $select_ninusi_cd. "' AND SOSIKI_CD = '". $select_sosiki_cd. "'";

		/* ▼派遣情報取得 */
		$sql  = 'SELECT ';
		$sql .= "$fields ";
		$sql .= 'FROM M_HAKEN_KAISYA ';
		$sql .= 'WHERE ';
		$sql .= $condition;

		$data = $this->query($sql, false);
		$data = self::editData($data);

		/* ▼派遣情報加工 */
		if(is_array($data)){
			foreach($data as $key => $val){
				$haken_key 				= $val['KAISYA_CD'];
				$haken_data[$haken_key]	= $val['KAISYA_NM'];
			}
		}

		return $haken_data;
	}

	/**
	 * スタッフ情報取得
	 * @access   public
	 * @return   拠点情報
	 */
	public function getStaffSearch($input_data) {

		/* ▼ログインユーザ選択情報読込 */
		$select_ninusi_cd = $_SESSION['select_ninusi_cd'];
		$select_sosiki_cd = $_SESSION['select_sosiki_cd'];

		/* ▼取得カラム設定 */
		$fields = ' DISTINCT t_S.STAFF_CD, t_S.STAFF_NM, t_S.HAKEN_KAISYA_CD, t_H.KAISYA_NM ';

		/* ▼検索条件設定 */
		$condition = " t_K.NINUSI_CD = '" . $select_ninusi_cd . "' AND t_K.SOSIKI_CD = '" . $select_sosiki_cd . "'";
		$condition .= " AND IFNULL(t_S.DEL_FLG, 0) <> 1";

		if($input_data['dispatch'] != ""){
			$condition .= " AND t_S.HAKEN_KAISYA_CD = '". $input_data['dispatch']. "'";
		}

		if($input_data['staff_cd'] != ""){
			$condition .= " AND ( t_S.STAFF_CD LIKE '%". $input_data['staff_cd']. "%' )";
		}
		if($input_data['staff_nm'] != ""){
			$condition .= " AND ( t_S.STAFF_NM LIKE '%". $input_data['staff_nm']. "%' )";
		}


		/* ▼表示順 */
		$order = ' STAFF_CD ASC ';

		/* ▼派遣情報取得 */
		$sql	=	' SELECT ';
		$sql	.=		$fields ;
		$sql	.=	' FROM ';
		$sql	.=	' 	M_STAFF_KIHON as t_S ';
		$sql	.=	' LEFT JOIN ';
		$sql	.=	' 	M_STAFF_KYOTEN as t_K ';
		$sql	.=	' ON ';
		$sql	.=	' 	t_S.STAFF_CD	= t_K.STAFF_CD';
		$sql	.=	' LEFT JOIN ';
		$sql	.=	' 	M_HAKEN_KAISYA as t_H ';
		$sql	.=	' ON ';
		$sql	.=	' 	t_S.HAKEN_KAISYA_CD	= t_H.KAISYA_CD AND';
		$sql	.=	' 	t_K.SOSIKI_CD	= t_H.SOSIKI_CD AND';
		$sql	.=	' 	t_K.NINUSI_CD	= t_H.NINUSI_CD';


		/* ▼検索条件設定があれば追記 */
		if($condition != ""){
			$sql	.=	' WHERE ';
			$sql	.=		$condition;
		}

		$sql	.=	' ORDER BY ';
		$sql	.=		$order;

		$data = $this->query($sql, false);
		$data = self::editData($data);

		return $data;

	}
	/**
	 * 派遣情報編集処理
	 * @access   public
	 * @param    編集前情報
	 * @return   編集後情報
	 */
	public function editData($results) {

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
