<?php
/**
 * TPM030Model
 *
 * 作業進捗(詳細進捗)
 *
 * @category      TPM
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');
App::uses('MCommon', 'Model');

class TPM030Model extends DCMSAppModel{

	public $name = 'TPM030Model';
	public $useTable = false;

	/**
	 * コンストラクタ
	 * @access   public
	 * @param 荷主コード
	 * @param 組織コード
	 * @param 機能ID
	 * @param モデルのID.
	 * @param モデルのテーブル名.
	 * @param モデルのデータソース.
	 */
    function __construct($ninusi_cd = '0000000000', $sosiki_cd = '0000000000', $kino_id = '', $id = false, $table = null, $ds = null) {
       parent::__construct($ninusi_cd, $sosiki_cd,$kino_id, $id, $table, $ds);

		//メッセージマスタ呼び出し
		$this->MMessage = new MMessage($ninusi_cd,$sosiki_cd,$kino_id);

		//共通関数モデル
		$this->MCommon = new MCommon($ninusi_cd,$sosiki_cd,$kino_id);

	}

	/**
	 * 作業進捗(詳細進捗) 表示一覧情報取得
	 * @access   public
	 * @return   表示一覧情報
	 */
	public function getDisplayList() {

		//詳細進捗データ(ピッキング集計管理No)取得
		$dataList = $this->getPickData();


 		//詳細進捗データのソート
 		$this->orderPickData($dataList);

		return $dataList;
	}

	/**
	 * スタッフ別 ピッキング集計管理Noデータ取得
	 * @access   public
	 * @return   スタッフ別 ピッキング集計管理Noデータ
	 */
	public function getPickData() {

		$stmt = null;
		$pdo = null;

		try{

			//DBオブジェクト取得
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			//就業開始基準時間取得
			$sql  = "SELECT ";
			$sql .= "   DT_SYUGYO_ST_BASE,";
			$sql .= "   CURRENT_DATE,";
			$sql .= "   NOW() AS NOW";
			$sql .= " FROM";
			$sql .= "   M_SYSTEM";
			$sql .= " WHERE";
			$sql .= "   NINUSI_CD='" . $this->_ninusi_cd . "' AND";
			$sql .= "   SOSIKI_CD='" . $this->_sosiki_cd . "';";
			
			$this->queryWithPDOLog($stmt,$pdo,$sql,"就業開始基準時間　取得");
	
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$dt_syugyo_st_base = $result['DT_SYUGYO_ST_BASE'];
			$ymd_syori = $result['CURRENT_DATE'];
			$now = $result['NOW'];
			
			//日付取得			
			$tmpDate = $ymd_syori . " " . $dt_syugyo_st_base;
			
			if (strtotime($now) < strtotime($tmpDate)) {
			
				$dt = new DateTime($tmpDate);
				$dt->sub(new DateInterval("P1D"));
				$ymd_syori = $dt->format('Y-m-d');
				$tmpDate = $ymd_syori . " " . $dt_syugyo_st_base;
			}
			
			$dt = new DateTime($tmpDate);
			$date1 = $dt->format('Y-m-d H:i:s');
			$dt->add(new DateInterval("P1D"));
			$date2 = $dt->format('Y-m-d H:i:s');

			$sql = "CALL P_TPM030_GET_PICK_S_KANRI(";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'" . $date1 . "',";
			$sql .= "'" . $date2 . "'";
			$sql .= ")";

			$this->queryWithPDOLog($stmt,$pdo,$sql,"スタッフ別 ピッキング集計管理Noデータ");

			$arrayList = array();

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			
				$seisanseiArray = $this->MCommon->getSeisansei(null,null,null,$result['STAFF_CD']);
				$kado_time = $seisanseiArray["KADO_TIME"];
				$buturyoSeisansei = $seisanseiArray["SEISANSEI"];
				
				$seisanseiArray = $this->MCommon->getSeisansei(null,null,null,$result['STAFF_CD'],1);
				$lineSeisansei = $seisanseiArray["SEISANSEI"];

				$array = array(
						"STAFF_CD" => $result['STAFF_CD'],
						"STAFF_NM" => $result['STAFF_NM'],
						"ACT_PROD_PIECE" => $result['ACT_PROD_PIECE'],
						"ACT_PROD_ITEM" => $result['ACT_PROD_ITEM'],
						"CNT_SET_SYORI_SUMI" => $result['CNT_SET_SYORI_SUMI'],
						"SUM_PIECE" => $result['SUM_PIECE'],
						"SUM_ITEM" => $result['SUM_ITEM'],
						"CNT_SYORI_SUMI" => $result['CNT_SYORI_SUMI'],
						"CNT_T_KETSU" => $result['CNT_T_KETSU'],
						"TOD_PROD_PIECE" => $buturyoSeisansei,
						"STAFF_KADO_HOUR" => $kado_time / 60,
						"TOD_PROD_ITEM" => $lineSeisansei,
				);

				$arrayList[] = $array;

			}

			$pdo = null;
			return $arrayList;

		}catch (Exception $e){

			$this->printLog("fatal", "例外発生", "TPM030", $e->getMessage());
			$pdo = null;
			throw $e;
		}

		return null;

	}

	/**
	 * 詳細進捗データのソート
	 * @access   public
	 * @return   true
	 */
	public function orderPickData(&$dataList) {
	
	    try {

			//データをソート
			$TOD_PROD_PIECE = array();
			$STAFF_CD =  array();
	
			foreach( $dataList as $key => $row ){
				$TOD_PROD_PIECE[$key]  = $row['TOD_PROD_PIECE'];
				$STAFF_CD[$key]  = $row['STAFF_CD'];
			}
	
			array_multisort($TOD_PROD_PIECE,SORT_DESC,$STAFF_CD,SORT_ASC,$dataList);
	
			return true;
		
		} catch (Exception $e) {
			
			$this->printLog("fatal", "例外発生", "TPM030", $e->getMessage());
		}
		
		return false;
	}

}

?>