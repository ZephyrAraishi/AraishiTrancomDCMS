<?php
/**
 * LSP010Model
 *
 * 勤務情報設定
 *
 * @category      Staff
 * @package       DCMS
 * @subpackage    Model
 * @author        ZephyrLabo
 * @version       1.0
 */

App::uses('MMessage', 'Model');

class LSP010Model extends DCMSAppModel{
	
	public $name = 'LSP010Model';
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
       
		/* ▼メッセージマスタ呼び出し */
		$this->MMessage = new MMessage($ninusi_cd, $sosiki_cd, $kino_id);
    }


	/**
	 * 勤務情報取得
	 * @access   public
	 * @return   勤務情報
	 */
	public function getTStaffKinmuLst($bnri_dai_cd, $bnri_cyu_cd,  $sel_date, $staff_cd, $kotei_inp_flg) {
		/* ▼取得カラム設定 */
		$fields = '*';

		/* ▼検索条件設定 */
		
		$conditionVal = array();
		$conditionKey = '';

		$sel_date1 = date("Y-m-d", strtotime($sel_date . " +0 day"  ));
		$sel_date2 = date("Y-m-d", strtotime($sel_date . " +6 day"  ));


		
		/* ▼検索条件設定 */
		$condition  = "DAI_NINUSI_CD       = :ninusi_cd";
		$condition .= " AND DAI_SOSIKI_CD  = :sosiki_cd";

		// 条件値指定
		$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
		$conditionVal['sosiki_cd'] = $this->_sosiki_cd;

		//勤務日付　開始
		$condition .= " AND YMD_KINMU  >= :sel_date1";
		$conditionVal['sel_date1'] = $sel_date1;

		//勤務日付　終了
		$condition .= " AND YMD_KINMU  <= :sel_date2";
		$conditionVal['sel_date2'] = $sel_date2;

//		if ($bnri_dai_cd == null
//		and $bnri_cyu_cd == null
//		and $staff_cd == null){
//			$condition .= " AND BNRI_DAI_CD = :bnri_dai_cd " ;
//			$conditionVal['bnri_dai_cd'] = '';
//			$condition .= " AND BNRI_CYU_CD = :bnri_cyu_cd " ;
//			$conditionVal['bnri_cyu_cd'] = '';
//		}





		//大分類コード
		if ($bnri_dai_cd != null){
			$condition .= " AND BNRI_DAI_CD = :bnri_dai_cd " ;
			$conditionVal['bnri_dai_cd'] = $bnri_dai_cd;
		}
		//中分類コード
		if ($bnri_cyu_cd != null){
			$condition .= " AND BNRI_CYU_CD = :bnri_cyu_cd " ;
			$conditionVal['bnri_cyu_cd'] = $bnri_cyu_cd;
		}

		//staffコード
		if ($staff_cd != null){
			$condition .= " AND STAFF_CD = :staff_cd " ;
			$conditionVal['staff_cd'] = $staff_cd;
		}
		
		/* ▼拠点情報取得 */
		$sql  = 'SELECT ';
		$sql .= "$fields ";
		$sql .= 'FROM V_LSP010_T_STAFF_KINMU_LST ';
		$sql .= 'WHERE ';
		$sql .= $condition;
		$sql .= $conditionKey;
		
		$data = $this->query($sql, $conditionVal, false);
		$data = self::editData($data);

//print_r($conditionVal);


		$tss     = array();
		$tse     = array();
		$skf     = array();
		$day     = array();

		$tss[0] = "TIME_SYUGYO_ST_1";
		$tse[0] = "TIME_SYUGYO_ED_1";
		$skf[0] = "SYUKIN_FLG_1";
		$day[0] = "DAY_1";
		$tss[1] = "TIME_SYUGYO_ST_2";
		$tse[1] = "TIME_SYUGYO_ED_2";
		$skf[1] = "SYUKIN_FLG_2";
		$day[1] = "DAY_2";
		$tss[2] = "TIME_SYUGYO_ST_3";
		$tse[2] = "TIME_SYUGYO_ED_3";
		$skf[2] = "SYUKIN_FLG_3";
		$day[2] = "DAY_3";
		$tss[3] = "TIME_SYUGYO_ST_4";
		$tse[3] = "TIME_SYUGYO_ED_4";
		$skf[3] = "SYUKIN_FLG_4";
		$day[3] = "DAY_4";
		$tss[4] = "TIME_SYUGYO_ST_5";
		$tse[4] = "TIME_SYUGYO_ED_5";
		$skf[4] = "SYUKIN_FLG_5";
		$day[4] = "DAY_5";
		$tss[5] = "TIME_SYUGYO_ST_6";
		$tse[5] = "TIME_SYUGYO_ED_6";
		$skf[5] = "SYUKIN_FLG_6";
		$day[5] = "DAY_6";
		$tss[6] = "TIME_SYUGYO_ST_7";
		$tse[6] = "TIME_SYUGYO_ED_7";
		$skf[6] = "SYUKIN_FLG_7";
		$day[6] = "DAY_7";

		$rec_cnt = -1;
		$STAFF_CD = "";
		$DEL_FLG = "";
		$BNRI_DAI_CD = "";
		$BNRI_CYU_CD = "";
		$SEQ_NO = "";

		$data2 = array();
		foreach($data as $key => $value) {

			if ($value['STAFF_CD']    != $STAFF_CD
			OR  $value['BNRI_DAI_CD'] != $BNRI_DAI_CD
			OR  $value['BNRI_CYU_CD'] != $BNRI_CYU_CD
			OR  $value['SEQ_NO']      != $SEQ_NO) {
				if ($rec_cnt != -1) {
					$data2[$rec_cnt]['STAFF_CD'] 			= $STAFF_CD;
					$data2[$rec_cnt]['STAFF_NAME'] 			= $STAFF_NAME;
					$data2[$rec_cnt]['BNRI_DAI_RYAKU'] 		= $BNRI_DAI_RYAKU;
					$data2[$rec_cnt]['BNRI_CYU_RYAKU'] 		= $BNRI_CYU_RYAKU;
					$data2[$rec_cnt]['BNRI_DAI_CD'] 		= $BNRI_DAI_CD;
					$data2[$rec_cnt]['BNRI_CYU_CD'] 		= $BNRI_CYU_CD;
					$data2[$rec_cnt]['SEQ_NO'] 				= $SEQ_NO;
					$data2[$rec_cnt]['KOUTEI_INP_FLG'] 		= $kotei_inp_flg;
					$data2[$rec_cnt]['COPY_FLG'] 			= 0;

					//画面の各列を順次処理
					for ($i = 0; $i <= 6; $i++) {

						$data2[$rec_cnt][$tss[$i]]	= $TIME_SYUGYO_ST[$i];
						$data2[$rec_cnt][$tse[$i]]	= $TIME_SYUGYO_ED[$i];
						$data2[$rec_cnt][$skf[$i]]	= $SYUKIN_FLG[$i];

						if ($SYUKIN_FLG[$i] == 1) {
							$data2[$rec_cnt][$day[$i]]			= "休";
						} else {
							if ($TIME_SYUGYO_ST[$i] == "") {
								$data2[$rec_cnt][$day[$i]]			= "";
							} else {
								$data2[$rec_cnt][$day[$i]]			= substr($TIME_SYUGYO_ST[$i], 0, 2) . ":" . substr($TIME_SYUGYO_ST[$i], 2, 2)
								                                    . "～"
								                                    . substr($TIME_SYUGYO_ED[$i], 0, 2) . ":" . substr($TIME_SYUGYO_ED[$i], 2, 2);
							}
						}
					}

				}

				$rec_cnt = $rec_cnt + 1;

				$STAFF_CD			= $value['STAFF_CD'];
				$STAFF_NAME			= $value['STAFF_NM'];
				$DEL_FLG            = $value['DEL_FLG'];
				$BNRI_DAI_RYAKU		= $value['BNRI_DAI_RYAKU'];
				$BNRI_CYU_RYAKU		= $value['BNRI_CYU_RYAKU'];
				$BNRI_DAI_CD		= $value['BNRI_DAI_CD'];
				$BNRI_CYU_CD		= $value['BNRI_CYU_CD'];
				$SEQ_NO				= $value['SEQ_NO'];

				$TIME_SYUGYO_ST = array();
				$TIME_SYUGYO_ED = array();
				$SYUKIN_FLG     = array();
				$TIME_SYUGYO_ST[0]	= "";
				$TIME_SYUGYO_ED[0]	= "";
				$SYUKIN_FLG[0]		= "";
				$TIME_SYUGYO_ST[1]	= "";
				$TIME_SYUGYO_ED[1]	= "";
				$SYUKIN_FLG[1]		= "";
				$TIME_SYUGYO_ST[2]	= "";
				$TIME_SYUGYO_ED[2]	= "";
				$SYUKIN_FLG[2]		= "";
				$TIME_SYUGYO_ST[3]	= "";
				$TIME_SYUGYO_ED[3]	= "";
				$SYUKIN_FLG[3]		= "";
				$TIME_SYUGYO_ST[4]	= "";
				$TIME_SYUGYO_ED[4]	= "";
				$SYUKIN_FLG[4]		= "";
				$TIME_SYUGYO_ST[5]	= "";
				$TIME_SYUGYO_ED[5]	= "";
				$SYUKIN_FLG[5]		= "";
				$TIME_SYUGYO_ST[6]	= "";
				$TIME_SYUGYO_ED[6]	= "";
				$SYUKIN_FLG[6]		= "";
			}

			$pos = 0;

			if ($value['YMD_KINMU'] == date("Y-m-d", strtotime($sel_date . " +0 day"  ))) {
				$pos = 0;
			}
			if ($value['YMD_KINMU'] == date("Y-m-d", strtotime($sel_date . " +1 day"  ))) {
				$pos = 1;
			}
			if ($value['YMD_KINMU'] == date("Y-m-d", strtotime($sel_date . " +2 day"  ))) {
				$pos = 2;
			}
			if ($value['YMD_KINMU'] == date("Y-m-d", strtotime($sel_date . " +3 day"  ))) {
				$pos = 3;
			}
			if ($value['YMD_KINMU'] == date("Y-m-d", strtotime($sel_date . " +4 day"  ))) {
				$pos = 4;
			}
			if ($value['YMD_KINMU'] == date("Y-m-d", strtotime($sel_date . " +5 day"  ))) {
				$pos = 5;
			}
			if ($value['YMD_KINMU'] == date("Y-m-d", strtotime($sel_date . " +6 day"  ))) {
				$pos = 6;
			}


			$TIME_SYUGYO_ST[$pos]	= substr($value['TIME_SYUGYO_ST'], 0, 2) . substr($value['TIME_SYUGYO_ST'], 3, 2);
			$TIME_SYUGYO_ED[$pos]	= substr($value['TIME_SYUGYO_ED'], 0, 2) . substr($value['TIME_SYUGYO_ED'], 3, 2);
			$SYUKIN_FLG[$pos]		= $value['SYUKIN_FLG'];
			if ($SYUKIN_FLG[$pos] == 1
			AND $TIME_SYUGYO_ST[$pos] == "0000"
			AND $TIME_SYUGYO_ED[$pos] == "0000") {
				$TIME_SYUGYO_ST[$pos]	= "";
				$TIME_SYUGYO_ED[$pos]	= "";
			}

		}
		if ($rec_cnt != -1) {
			$data2[$rec_cnt]['STAFF_CD'] 			= $STAFF_CD;
			$data2[$rec_cnt]['STAFF_NAME'] 			= $STAFF_NAME;
			$data2[$rec_cnt]['DEL_FLG'] 			= $DEL_FLG;
			$data2[$rec_cnt]['BNRI_DAI_RYAKU'] 		= $BNRI_DAI_RYAKU;
			$data2[$rec_cnt]['BNRI_CYU_RYAKU'] 		= $BNRI_CYU_RYAKU;
			$data2[$rec_cnt]['BNRI_DAI_CD'] 		= $BNRI_DAI_CD;
			$data2[$rec_cnt]['BNRI_CYU_CD'] 		= $BNRI_CYU_CD;
			$data2[$rec_cnt]['SEQ_NO'] 				= $SEQ_NO;
			$data2[$rec_cnt]['KOUTEI_INP_FLG'] 		= $kotei_inp_flg;
			$data2[$rec_cnt]['COPY_FLG'] 			= 0;

			for ($i = 0; $i <= 6; $i++) {

				$data2[$rec_cnt][$tss[$i]]	= $TIME_SYUGYO_ST[$i];
				$data2[$rec_cnt][$tse[$i]]	= $TIME_SYUGYO_ED[$i];
				$data2[$rec_cnt][$skf[$i]]	= $SYUKIN_FLG[$i];

				if ($SYUKIN_FLG[$i] == 1) {
					$data2[$rec_cnt][$day[$i]]			= "休";
				} else {
					if ($TIME_SYUGYO_ST[$i] == "") {
						$data2[$rec_cnt][$day[$i]]			= "";
					} else {
						$data2[$rec_cnt][$day[$i]]			= substr($TIME_SYUGYO_ST[$i], 0, 2) . ":" . substr($TIME_SYUGYO_ST[$i], 2, 2)
						                                    . "～"
						                                    . substr($TIME_SYUGYO_ED[$i], 0, 2) . ":" . substr($TIME_SYUGYO_ED[$i], 2, 2);
					}
				}
			}
		}

	
		return $data2;
	}
	
	/**
	 * 拠点情報編集処理
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

	
	public function getMStaffKihon($data, $ttl) {

		$count4  = 0;

		//タイトルから、曜日と先頭曜日区分を取得
		$youbi  = array();
		$in_day = array();
		foreach($ttl as $obj) {
			$dataArray = $obj->dataTtl;

			$youbi[0]   = $dataArray[1];
			$youbi[1]   = $dataArray[3];
			$youbi[2]   = $dataArray[5];
			$youbi[3]   = $dataArray[7];
			$youbi[4]   = $dataArray[9];
			$youbi[5]   = $dataArray[11];
			$youbi[6]   = $dataArray[13];


			$in_day[0]  = $dataArray[0];
			$in_day[1]  = $dataArray[2];
			$in_day[2]  = $dataArray[4];
			$in_day[3]  = $dataArray[6];
			$in_day[4]  = $dataArray[8];
			$in_day[5]  = $dataArray[10];
			$in_day[6]  = $dataArray[12];

			$kbn_youbi  = $dataArray[14];
		}

		//未入力箇所にマスタからセット
		$message = "";
		$count   = 1;
		$count4  = 0;
		foreach($data as $obj) {
			//画面情報取得
			$cellsArray = $obj->cells;
			$dataArray = $obj->data;
			
			$STAFF_CD   = $cellsArray[1];

			$LINE_COUNT   = $dataArray[0];
			$CHANGED      = $dataArray[1];
			$BNRI_DAI_CD  = $dataArray[2];
			$BNRI_CYU_CD  = $dataArray[3];
			$KOUTEI_INP_FLG = $dataArray[25];
			$SEQ_NO       = $dataArray[33];
			$COPY_FLG     = $dataArray[34];

			//更新対象件数取得
			if ($CHANGED != '0' 
			and $CHANGED != '5') {
				$count4++;
			}

			//追加と修正のみ
			if ($CHANGED == '1' 
			or  $CHANGED == '2') {

				//スタッフコード入力時のみ
				if (!empty($STAFF_CD)) {

					//マスタから取得
					// ▼取得カラム設定 //
					$fields = '*';

					// ▼検索条件設定 //
					
					$conditionVal = array();
					$conditionKey = '';
					
					// ▼検索条件設定 //
					$condition  = "STAFF_CD       = :staff_cd";

					// 条件値指定
					$conditionVal['staff_cd'] = $STAFF_CD;
					
					// ▼拠点情報取得 //
					$sql  = 'SELECT ';
					$sql .= "$fields ";
					$sql .= 'FROM V_LSP010_M_STAFF_KIHON_GET ';
					$sql .= 'WHERE ';
					$sql .= $condition;
					$sql .= $conditionKey;
					
					$dbread = $this->query($sql, $conditionVal, false);
					$dbread = self::editData($dbread);
					
					$autoIdx = 0;

					if (empty($dbread)) {
						//レコードなし
						$message = vsprintf($this->MMessage->getOneMessage('LSPE010012'), array($count));
						$this->errors['Check'][] =   $message;
					} else {
						//レコードあり
						//マスタの値を退避
						$DB_TIME_ST = array();
						$DB_TIME_ED = array();
						foreach($dbread as $key => $value) {
							$DB_TIME_ST[0]    = $value['SUN_TIME_ST'];
							$DB_TIME_ED[0]    = $value['SUN_TIME_ED'];
							$DB_TIME_ST[1]    = $value['MON_TIME_ST'];
							$DB_TIME_ED[1]    = $value['MON_TIME_ED'];
							$DB_TIME_ST[2]    = $value['TUE_TIME_ST'];
							$DB_TIME_ED[2]    = $value['TUE_TIME_ED'];
							$DB_TIME_ST[3]    = $value['WED_TIME_ST'];
							$DB_TIME_ED[3]    = $value['WED_TIME_ED'];
							$DB_TIME_ST[4]    = $value['THU_TIME_ST'];
							$DB_TIME_ED[4]    = $value['THU_TIME_ED'];
							$DB_TIME_ST[5]    = $value['FRI_TIME_ST'];
							$DB_TIME_ED[5]    = $value['FRI_TIME_ED'];
							$DB_TIME_ST[6]    = $value['SAT_TIME_ST'];
							$DB_TIME_ED[6]    = $value['SAT_TIME_ED'];
						}

						//画面の各列を順次処理
						for ($i = 4; $i <= 22; $i=$i+3) {
							//画面の列の値を退避
							$TIME_ST    = $dataArray[$i];
							$TIME_ED    = $dataArray[$i + 1];
							$SYUKIN_FLG = $dataArray[$i + 2];
							$AUTO_FLG   = $dataArray[36 + $autoIdx];
							$autoIdx++;

							//未入力のみ処理
							if (empty($TIME_ST) 
							&&  empty($TIME_ED) 
							&&  empty($SYUKIN_FLG)
							&& $AUTO_FLG == '1') {

								//hidden項目位置を0-6に変換
								$j = ($i - 4) / 3;

								if ( !$this->checkOtherRecFound($data, $STAFF_CD, $count, $i, $in_day[$j], $BNRI_DAI_CD, $BNRI_CYU_CD, $KOUTEI_INP_FLG) ) {
								
									//先頭曜日区分ずらす　マスタの値を取得する為
									$k = $j + $kbn_youbi;
									if ($k >= 7) {
										$k = $k -7;
									}

									if (empty($DB_TIME_ST[$k])) {
										//休日扱い　画面項目にセット
										$obj->data[$i]     = "";
										$obj->data[$i + 1] = "";
										$obj->data[$i + 2] = 1;

										$obj->cells[$j + 5] = "休";
									} else {
										//出勤扱い　画面項目にセット
										$obj->data[$i]     = substr($DB_TIME_ST[$k], 0, 2) . substr($DB_TIME_ST[$k], 3, 2);
										$obj->data[$i + 1] = "2400";
										$obj->data[$i + 2] = 0;

										$obj->cells[$j + 5] = substr($DB_TIME_ST[$k], 0, 2) . ":" . substr($DB_TIME_ST[$k], 3, 2)
					                                        . "～"
					                                        . substr($DB_TIME_ED[$k], 0, 2) . ":" . substr($DB_TIME_ED[$k], 3, 2);
									}

								}

							}

						}

					}

				}

			}

			$count++;

		}

		if (count($this->errors) != 0) {
			return $data;
		}

		if ($count4 == 0) {
			$message = "更新データがありません。";
			$this->errors['Check'][] = $message;
			return $data;
		}

		return $data;

	}

	
	public function checkOtherRecFound($data, $prmStaff_cd, $prm_gyo, $prm_retu, $prm_ymd, $prm_dai_cd, $prm_cyu_cd, $prm_koutei_inp_flg) {

		$return_cd = false;

		//配列内の存在チェック
		$count   = 1;
		foreach($data as $obj) {
			//画面情報取得
			$cellsArray = $obj->cells;
			$dataArray = $obj->data;
			
			$STAFF_CD   = $cellsArray[1];

			$LINE_COUNT   = $dataArray[0];
			$CHANGED      = $dataArray[1];
			$BNRI_DAI_CD  = $dataArray[2];
			$BNRI_CYU_CD  = $dataArray[3];

			//他行でかつスタッフコードが同じ時のみ
			if ( $count    != $prm_gyo
			and  $STAFF_CD == $prmStaff_cd) {
				//画面の列の値を退避
				$TIME_ST    = $dataArray[$prm_retu];
				$TIME_ED    = $dataArray[$prm_retu + 1];
				$SYUKIN_FLG = $dataArray[$prm_retu + 2];

				//未入力以外はエラー　休日もダメ
				if (empty($TIME_ST) 
				&&  empty($TIME_ED) 
				&&  empty($SYUKIN_FLG) ) {
				} else {
					$return_cd = true;
				}
			}

			$count++;
		}

		if ( $return_cd == false
		AND  $prm_koutei_inp_flg == 0 ) {

			//スタッフ勤務の存在チェック
			// ▼取得カラム設定 //
			$fields = '*';

			// ▼検索条件設定 //
			
			$conditionVal = array();
			$conditionKey = '';

			// ▼検索条件設定 //
			$condition  = "STAFF_CD       = :staff_cd";
			$condition .= " AND YMD_KINMU = :ymd_kinmu";
			$condition .= " AND ( BNRI_DAI_CD      <> :bnri_dai_cd ";
			$condition .= " OR    BNRI_CYU_CD      <> :bnri_cyu_cd ) ";

			// 条件値指定
			$conditionVal['staff_cd'] = $prmStaff_cd;
			$conditionVal['ymd_kinmu'] = $prm_ymd;
			$conditionVal['bnri_dai_cd'] = $prm_dai_cd;
			$conditionVal['bnri_cyu_cd'] = $prm_cyu_cd;

			
			// ▼拠点情報取得 //
			$sql  = 'SELECT ';
			$sql .= "$fields ";
			$sql .= 'FROM V_LSP010_T_STAFF_KINMU_CHK ';
			$sql .= 'WHERE ';
			$sql .= $condition;
			$sql .= $conditionKey;
			
			$dbread = $this->query($sql, $conditionVal, false);
			$dbread = self::editData($dbread);

			if (empty($dbread)) {
				//レコードなし
			} else {
				//レコードあり　エラー
				$return_cd = true;
			}

		}

		return $return_cd;

	}

	
	public function checkTStaffKinmu($data, $ttl) {

		//タイトルから、曜日と先頭曜日区分を取得
		$youbi = array();
		foreach($ttl as $obj) {
			$dataArray = $obj->dataTtl;

			$youbi[0]   = $dataArray[1];
			$youbi[1]   = $dataArray[3];
			$youbi[2]   = $dataArray[5];
			$youbi[3]   = $dataArray[7];
			$youbi[4]   = $dataArray[9];
			$youbi[5]   = $dataArray[11];
			$youbi[6]   = $dataArray[13];

			$kbn_youbi  = $dataArray[14];
		}


		//　入力チェック

		$message = "";
		$count = 1;			//行
		$count2 = 0;		//キー重複チェック用
		$count3 = 0;		//キー重複チェック用
		$count4 = 0;		//更新対象件数取得
		foreach($data as $obj) {
			
			$cellsArray = $obj->cells;
			$dataArray = $obj->data;
			
			$STAFF_CD   = $cellsArray[1];

			$LINE_COUNT   = $dataArray[0];
			$CHANGED      = $dataArray[1];
			$BNRI_DAI_CD  = $dataArray[2];
			$BNRI_CYU_CD  = $dataArray[3];

			$TIME_ST = array();
			$TIME_ED = array();
			$SYUKIN_FLG = array();
			$TIME_ST[0]    = $dataArray[4];
			$TIME_ED[0]    = $dataArray[5];
			$SYUKIN_FLG[0] = $dataArray[6];
			$TIME_ST[1]    = $dataArray[7];
			$TIME_ED[1]    = $dataArray[8];
			$SYUKIN_FLG[1] = $dataArray[9];
			$TIME_ST[2]    = $dataArray[10];
			$TIME_ED[2]    = $dataArray[11];
			$SYUKIN_FLG[2] = $dataArray[12];
			$TIME_ST[3]    = $dataArray[13];
			$TIME_ED[3]    = $dataArray[14];
			$SYUKIN_FLG[3] = $dataArray[15];
			$TIME_ST[4]    = $dataArray[16];
			$TIME_ED[4]    = $dataArray[17];
			$SYUKIN_FLG[4] = $dataArray[18];
			$TIME_ST[5]    = $dataArray[19];
			$TIME_ED[5]    = $dataArray[20];
			$SYUKIN_FLG[5] = $dataArray[21];
			$TIME_ST[6]    = $dataArray[22];
			$TIME_ED[6]    = $dataArray[23];
			$SYUKIN_FLG[6] = $dataArray[24];

			//更新対象件数取得
			if ($CHANGED != '0' 
			and $CHANGED != '5') {
				$count4++;
			}

			//追加と修正のみ
			if ($CHANGED == '1' 
			or  $CHANGED == '2') {

				//必須チェック
				if (empty($BNRI_DAI_CD)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '大分類コード'));
					$this->errors['Check'][] =   $message;
				}
				if (empty($BNRI_CYU_CD)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '中分類コード'));
					$this->errors['Check'][] =   $message;
				}
				if (empty($STAFF_CD)) {
					$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, 'スタッフ'));
					$this->errors['Check'][] =   $message;
				}




				//画面の各列を順次処理
				for ($i = 0; $i <= 6; $i++) {

					//時刻チェック
					if ($SYUKIN_FLG[$i] == 1) {
						if (!empty($TIME_ST[$i]) 
						||  !empty($TIME_ED[$i])) {
							$message = vsprintf($this->MMessage->getOneMessage('LSPE010010'), array($count, $youbi[$i]));
							$this->errors['Check'][] =   $message;
						}
					} else {
						if (!empty($TIME_ST[$i]) 
						||  !empty($TIME_ED[$i])) {
							//必須チェック
							if (empty($TIME_ST[$i])) {
								$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '[' . $youbi[$i] . '曜]就業開始'));
								$this->errors['Check'][] =   $message;
							}
							if (empty($TIME_ED[$i])) {
								$message = vsprintf($this->MMessage->getOneMessage('CMNE010001'), array($count, '[' . $youbi[$i] . '曜]就業終了'));
								$this->errors['Check'][] =   $message;
							}
							//桁数チェック
							if (strlen($TIME_ST[$i]) != 4) {
								$message = vsprintf($this->MMessage->getOneMessage('LSPE010002'), array($count, '[' . $youbi[$i] . '曜]就業開始', '4'));
								$this->errors['Check'][] =   $message;
							}
							if (strlen($TIME_ED[$i]) != 4) {
								$message = vsprintf($this->MMessage->getOneMessage('LSPE010002'), array($count, '[' . $youbi[$i] . '曜]就業終了', '4'));
								$this->errors['Check'][] =   $message;
							}
							//数字チェック
							if (!preg_match("/^[0-9]+$/",$TIME_ST[$i])) {
								$message = vsprintf($this->MMessage->getOneMessage('LSPE010003'), array($count, '[' . $youbi[$i] . '曜]就業開始'));
								$this->errors['Check'][] =   $message;
							}
							if (!preg_match("/^[0-9]+$/",$TIME_ED[$i])) {
								$message = vsprintf($this->MMessage->getOneMessage('LSPE010003'), array($count, '[' . $youbi[$i] . '曜]就業終了'));
								$this->errors['Check'][] =   $message;
							}
							//範囲チェック
							if ($TIME_ST[$i] < '0000' 
							||  $TIME_ST[$i] > '2400') {
								$message = vsprintf($this->MMessage->getOneMessage('LSPE010004'), array($count, '[' . $youbi[$i] . '曜]就業開始', '0000', '2400'));
								$this->errors['Check'][] =   $message;
							}
							if (substr($TIME_ST[$i], 2, 2) < '00' 
							||  substr($TIME_ST[$i], 2, 2) > '59') {
								$message = vsprintf($this->MMessage->getOneMessage('LSPE010004'), array($count, '[' . $youbi[$i] . '曜]就業開始', '00', '59'));
								$this->errors['Check'][] =   $message;
							}
							if ($TIME_ED[$i] < '0000' 
							||  $TIME_ED[$i] > '3100') {
								$message = vsprintf($this->MMessage->getOneMessage('LSPE010004'), array($count, '[' . $youbi[$i] . '曜]就業終了', '0000', '3100'));
								$this->errors['Check'][] =   $message;
							}
							if (substr($TIME_ED[$i], 2, 2) < '00' 
							||  substr($TIME_ED[$i], 2, 2) > '59') {
								$message = vsprintf($this->MMessage->getOneMessage('LSPE010004'), array($count, '[' . $youbi[$i] . '曜]就業終了', '00', '59'));
								$this->errors['Check'][] =   $message;
							}
							if ($TIME_ST[$i] >= $TIME_ED[$i]) {
								$message = vsprintf($this->MMessage->getOneMessage('LSPE010005'), array($count, $youbi[$i]));
								$this->errors['Check'][] =   $message;
							}
						}
					}

				}


				//画面の各列を順次処理
				for ($i = 0; $i <= 6; $i++) {

					//前後重なりチェック
					if ($SYUKIN_FLG[$i] != 1) {

						if (!empty($TIME_ST[$i]) 
						||  !empty($TIME_ED[$i])) {

							$tmpTimeSt = substr($TIME_ST[$i], 0, 2) + 24;
							$tmpTimeSt = $tmpTimeSt . substr($TIME_ST[$i], 2, 2);

							if ($TIME_ED[$i] >= 2400) {
								$tmpTimeEd = date("Hi", mktime(substr($TIME_ED[$i], 0, 2) - 24, substr($TIME_ED[$i], 2, 2), 00));
							} else {
								$tmpTimeEd =  "0000";
							}

							//前日の終了時刻と開始時刻をチェック
							if ($i != 0) {
								$j = $i - 1;
								if ($TIME_ED[$j]    >  $tmpTimeSt
								&&  $SYUKIN_FLG[$j] != 1
								&&  !empty($TIME_ED[$j])) {
									$message = vsprintf($this->MMessage->getOneMessage('LSPE010004'), array($count, '[' . $youbi[$i] . '曜]就業開始', sprintf('%04d', $TIME_ED[$j] - 2400), '2400'));
									$this->errors['Check'][] =   $message;
								}
							}

							//翌日の開始時刻と終了時刻をチェック
							if ($i != 6) {
								$j = $i + 1;
								if ($TIME_ST[$j] < $tmpTimeEd
								&&  $SYUKIN_FLG[$j] != 1
								&&  !empty($TIME_ST[$j])) {
									$message = vsprintf($this->MMessage->getOneMessage('LSPE010004'), array($count, '[' . $youbi[$i] . '曜]就業終了', '', sprintf('%04d', $TIME_ST[$j] + 2400)));
									$this->errors['Check'][] =   $message;
								}
							}
						}

					}

				}

			}

			$count++;

		}

		if (count($this->errors) != 0) {
			return false;
		}

		if ($count4 == 0) {
			$message = "更新データがありません。";
			$this->errors['Check'][] = $message;
			return false;
		}

	}


	
	public function checkTStaffKinmuTbl($data, $ttl) {

		//タイトルから、曜日と先頭曜日区分を取得
		$youbi = array();
		foreach($ttl as $obj) {
			$dataArray = $obj->dataTtl;

			$youbi[0]   = $dataArray[1];
			$youbi[1]   = $dataArray[3];
			$youbi[2]   = $dataArray[5];
			$youbi[3]   = $dataArray[7];
			$youbi[4]   = $dataArray[9];
			$youbi[5]   = $dataArray[11];
			$youbi[6]   = $dataArray[13];

			$kbn_youbi  = $dataArray[14];
		}


		$message = "";
		$count = 1;			//行
		foreach($data as $obj) {
			
			$cellsArray = $obj->cells;
			$dataArray = $obj->data;
			
			$STAFF_CD   = $cellsArray[1];

			$LINE_COUNT   = $dataArray[0];
			$CHANGED      = $dataArray[1];
			$BNRI_DAI_CD  = $dataArray[2];
			$BNRI_CYU_CD  = $dataArray[3];

			$TIME_ST = array();
			$TIME_ED = array();
			$SYUKIN_FLG = array();
			$TIME_ST[0]    = $dataArray[4];
			$TIME_ED[0]    = $dataArray[5];
			$SYUKIN_FLG[0] = $dataArray[6];
			$TIME_ST[1]    = $dataArray[7];
			$TIME_ED[1]    = $dataArray[8];
			$SYUKIN_FLG[1] = $dataArray[9];
			$TIME_ST[2]    = $dataArray[10];
			$TIME_ED[2]    = $dataArray[11];
			$SYUKIN_FLG[2] = $dataArray[12];
			$TIME_ST[3]    = $dataArray[13];
			$TIME_ED[3]    = $dataArray[14];
			$SYUKIN_FLG[3] = $dataArray[15];
			$TIME_ST[4]    = $dataArray[16];
			$TIME_ED[4]    = $dataArray[17];
			$SYUKIN_FLG[4] = $dataArray[18];
			$TIME_ST[5]    = $dataArray[19];
			$TIME_ED[5]    = $dataArray[20];
			$SYUKIN_FLG[5] = $dataArray[21];
			$TIME_ST[6]    = $dataArray[22];
			$TIME_ED[6]    = $dataArray[23];
			$SYUKIN_FLG[6] = $dataArray[24];

			//追加と修正のみ
			if ($CHANGED == '1' 
			or  $CHANGED == '2') {

				//配列内時間チェック
				$return_cd = self::checkTStaffKinmuTblSub($data, $count, $STAFF_CD, $TIME_ST, $TIME_ED, $SYUKIN_FLG);

				//前日の終了時刻と開始時刻　エラー
				if ($return_cd >= 10
				and $return_cd <= 19) {
					$m = $return_cd - 10;
					$message = vsprintf($this->MMessage->getOneMessage('LSPE010015'), array($count, $youbi[$m] . '曜][就業開始と前日の終了時刻' , $TIME_ST[$m], $TIME_ED[$m]));
					$this->errors['Check'][] =   $message;
				}

				//翌日の開始時刻と終了時刻　エラー
				if ($return_cd >= 20
				and $return_cd <= 29) {
					$m = $return_cd - 20;
					$message = vsprintf($this->MMessage->getOneMessage('LSPE010015'), array($count, $youbi[$m] . '曜][就業終了と翌日の開始時刻' , $TIME_ST[$m], $TIME_ED[$m]));
					$this->errors['Check'][] =   $message;
				}

				//同一日の開始時刻と終了時刻　エラー
				if ($return_cd >= 30
				and $return_cd <= 39) {
					$m = $return_cd - 30;
					$message = vsprintf($this->MMessage->getOneMessage('LSPE010015'), array($count, $youbi[$m] . '曜][就業開始と同日の就業終了', $TIME_ST[$m], $TIME_ED[$m]));
					$this->errors['Check'][] =   $message;
				}

				//同一日に休日とシフトが混在している　エラー
				if ($return_cd >= 40
				and $return_cd <= 49) {
					$m = $return_cd - 40;
					$message = vsprintf($this->MMessage->getOneMessage('LSPE010015'), array($count, $youbi[$m] . '曜][休日とシフトが混在', $TIME_ST[$m], $TIME_ED[$m]));
					$this->errors['Check'][] =   $message;
				}

			}

			$count++;

		}

		if (count($this->errors) != 0) {
			return false;
		}

	}

	
	public function checkTStaffKinmuTblSub($data, $prm_count, $prm_staff_cd, $prm_time_st, $prm_time_ed, $prm_syukin_flg) {

		$return_cd = 0;

		//チェック元の時間整形
		$tmpTimeSt = array();
		$tmpTimeEd = array();
		for ($i = 0; $i <= 6; $i++) {
			if (empty($prm_time_st[$i]) 
			&&  empty($prm_time_ed[$i]) 
			&&  empty($prm_syukin_flg[$i]) ) {
				//未入力はないのと同じなので、チェック対象外
				$tmpTimeSt[$i] = "";
				$tmpTimeEd[$i] =  "";
			} else {

				if ($prm_syukin_flg[$i] != 1) {
					//チェック元の時間整形
					$tmpTimeSt[$i] = substr($prm_time_st[$i], 0, 2) + 24;
					$tmpTimeSt[$i] = $tmpTimeSt[$i] . substr($prm_time_st[$i], 2, 2);

					if ($prm_time_ed[$i] >= 2400) {
						$tmpTimeEd[$i] = date("Hi", mktime(substr($prm_time_ed[$i], 0, 2) - 24, substr($prm_time_ed[$i], 2, 2), 00));
					} else {
						$tmpTimeEd[$i] =  "0000";
					}
				} else {
					$tmpTimeSt[$i] = "";
					$tmpTimeEd[$i] =  "";
				}
			}
		}


		$count = 1;			//行
		foreach($data as $obj) {

			$cellsArray = $obj->cells;
			$dataArray = $obj->data;
			
			$STAFF_CD   = $cellsArray[1];

			$LINE_COUNT   = $dataArray[0];
			$CHANGED      = $dataArray[1];
			$BNRI_DAI_CD  = $dataArray[2];
			$BNRI_CYU_CD  = $dataArray[3];

			//チェック元以外の行で、同一スタッフの行
			if ($count    != $prm_count
			and $STAFF_CD == $prm_staff_cd) {
			

				$TIME_ST = array();
				$TIME_ED = array();
				$SYUKIN_FLG = array();
				$TIME_ST[0]    = $dataArray[4];
				$TIME_ED[0]    = $dataArray[5];
				$SYUKIN_FLG[0] = $dataArray[6];
				$TIME_ST[1]    = $dataArray[7];
				$TIME_ED[1]    = $dataArray[8];
				$SYUKIN_FLG[1] = $dataArray[9];
				$TIME_ST[2]    = $dataArray[10];
				$TIME_ED[2]    = $dataArray[11];
				$SYUKIN_FLG[2] = $dataArray[12];
				$TIME_ST[3]    = $dataArray[13];
				$TIME_ED[3]    = $dataArray[14];
				$SYUKIN_FLG[3] = $dataArray[15];
				$TIME_ST[4]    = $dataArray[16];
				$TIME_ED[4]    = $dataArray[17];
				$SYUKIN_FLG[4] = $dataArray[18];
				$TIME_ST[5]    = $dataArray[19];
				$TIME_ED[5]    = $dataArray[20];
				$SYUKIN_FLG[5] = $dataArray[21];
				$TIME_ST[6]    = $dataArray[22];
				$TIME_ED[6]    = $dataArray[23];
				$SYUKIN_FLG[6] = $dataArray[24];

				//画面の各列を順次処理
				for ($i = 0; $i <= 6; $i++) {

					if (empty($prm_time_st[$i]) 
					&&  empty($prm_time_ed[$i]) 
					&&  empty($prm_syukin_flg[$i]) ) {
						//未入力はないのと同じなので、チェック対象外

					} else {

						//前後重なりチェック
						//チェック元が休日以外
						if ($prm_syukin_flg[$i] != 1) {

							//前日の終了時刻と開始時刻をチェック
							if ($i != 0) {
								$j = $i - 1;
								if (empty($TIME_ST[$j]) 
								&&  empty($TIME_ED[$j]) 
								&&  empty($SYUKIN_FLG[$j]) ) {
									//未入力はないのと同じなので、チェック対象外
								} else {
									if ($TIME_ED[$j]    >  $tmpTimeSt[$i]
									&&  $SYUKIN_FLG[$j] != 1) {
										$return_cd = $i + 10;
										return $return_cd;
									}
								}
							}

							//翌日の開始時刻と終了時刻をチェック
							if ($i != 6) {
								$j = $i + 1;
								if (empty($TIME_ST[$j]) 
								&&  empty($TIME_ED[$j]) 
								&&  empty($SYUKIN_FLG[$j]) ) {
									//未入力はないのと同じなので、チェック対象外
								} else {
									if ($TIME_ST[$j] < $tmpTimeEd[$i]
									&&  $SYUKIN_FLG[$j] != 1) {
										$return_cd = $i + 20;
										return $return_cd;
									}
								}
							}

							//同一日の開始時刻と終了時刻をチェック
							if ($SYUKIN_FLG[$i] != 1) {
								if (empty($TIME_ST[$i]) 
								&&  empty($TIME_ED[$i]) 
								&&  empty($SYUKIN_FLG[$i]) ) {
									//未入力はないのと同じなので、チェック対象外
								} else {
									if ( ($TIME_ST[$i] >= $prm_time_st[$i]
									and   $TIME_ST[$i] <  $prm_time_ed[$i])
									or   ($TIME_ED[$i] >  $prm_time_st[$i]
									and   $TIME_ED[$i] <= $prm_time_ed[$i])
									or   ($TIME_ST[$i] <= $prm_time_st[$i]
									and   $TIME_ED[$i] >= $prm_time_ed[$i]) ) {
										$return_cd = $i + 30;
										return $return_cd;
									}
								}
							} else {
								//入力対象が、シフトの場合、休日があったらダメ
								if (empty($TIME_ST[$i]) 
								&&  empty($TIME_ED[$i]) 
								&&  empty($SYUKIN_FLG[$i]) ) {
									//未入力はないのと同じなので、チェック対象外
								} else {
									$return_cd = $i + 40;
									return $return_cd;
								}
							}
						} else {
							//入力対象が、休日の場合、シフトがあったらダメ（休日も）
							if (empty($TIME_ST[$i]) 
							&&  empty($TIME_ED[$i]) 
							&&  empty($SYUKIN_FLG[$i]) ) {
								//未入力はないのと同じなので、チェック対象外
							} else {
								$return_cd = $i + 40;
								return $return_cd;
							}
						}
					}
				}
			}

			$count++;

		}

		return $return_cd;

	}

	
	public function checkTStaffKinmuDb($data, $ttl) {

		$count4  = 0;

		//タイトルから、曜日と先頭曜日区分を取得
		$youbi = array();
		$in_day = array();
		foreach($ttl as $obj) {
			$dataArray = $obj->dataTtl;

			$youbi[0]   = $dataArray[1];
			$youbi[1]   = $dataArray[3];
			$youbi[2]   = $dataArray[5];
			$youbi[3]   = $dataArray[7];
			$youbi[4]   = $dataArray[9];
			$youbi[5]   = $dataArray[11];
			$youbi[6]   = $dataArray[13];

			$in_day[0]  = $dataArray[0];
			$in_day[1]  = $dataArray[2];
			$in_day[2]  = $dataArray[4];
			$in_day[3]  = $dataArray[6];
			$in_day[4]  = $dataArray[8];
			$in_day[5]  = $dataArray[10];
			$in_day[6]  = $dataArray[12];

			$kbn_youbi  = $dataArray[14];
		}



		//DBに重なる時間があるかチェック
		$message = "";
		$count   = 1;
		$count4  = 0;
		foreach($data as $obj) {
			//画面情報取得
			$cellsArray = $obj->cells;
			$dataArray = $obj->data;
			
			$STAFF_CD   = $cellsArray[1];

			$LINE_COUNT   = $dataArray[0];
			$CHANGED      = $dataArray[1];
			$BNRI_DAI_CD  = $dataArray[2];
			$BNRI_CYU_CD  = $dataArray[3];
			$KOUTEI_INP_FLG  = $dataArray[25];

			//更新対象件数取得
			if ($CHANGED != '0' 
			and $CHANGED != '5') {
				$count4++;
			}

			//追加と修正のみ
			if ($CHANGED == '1' 
			or  $CHANGED == '2') {

				//スタッフコード入力時のみ
				if (!empty($STAFF_CD)) {

					//画面の各列を順次処理
					for ($i = 4; $i <= 22; $i=$i+3) {
						//画面の列の値を退避
						$TIME_ST    = $dataArray[$i];
						$TIME_ED    = $dataArray[$i + 1];
						$SYUKIN_FLG = $dataArray[$i + 2];

						if (empty($TIME_ST) 
						&&  empty($TIME_ED) 
						&&  empty($SYUKIN_FLG) ) {
							//未入力はないのと同じなので、チェック対象外
						} else {

							if ($SYUKIN_FLG == 0) {
								//出勤日の場合のチェック

								//マスタから取得
								// ▼取得カラム設定 //
								$fields = '*';

								// ▼検索条件設定 //
								
								$conditionVal = array();
								$conditionKey = '';
								
								// ▼検索条件設定 //

								$condition  = "";

								//検索で工程入力時のみ
								if ($KOUTEI_INP_FLG != 1) {

									//入力日から見て、当日チェック
									//入力日で、入力した時間内に、
									//DBのスタートタイムまたはエンドタイムが入っている
									//またはDBのスタートタイム・エンドタイムが入力した時間を挟んでいる
									//この時、入力外の工程のみ
									//時間は、入力値をそのまま使用
									$condition  .= "( ";
									$condition  .= "STAFF_CD               =  :staff_cd ";
									$condition  .= "AND   YMD_KINMU        =  :today ";
									$condition  .= "AND   SYUKIN_FLG       <> 1 ";
									$condition  .= "AND ( BNRI_DAI_CD      <> :bnri_dai_cd ";
									$condition  .= "OR    BNRI_CYU_CD      <> :bnri_cyu_cd ) ";
									$condition  .= "AND ( ( TIME_SYUGYO_ST >= :in_time_syugyo_st_now ";
									$condition  .= "AND     TIME_SYUGYO_ST <  :in_time_syugyo_ed_now ) ";
									$condition  .= "OR    ( TIME_SYUGYO_ED >  :in_time_syugyo_st_now ";
									$condition  .= "AND     TIME_SYUGYO_ED <= :in_time_syugyo_ed_now ) ";
									$condition  .= "OR    ( TIME_SYUGYO_ST <= :in_time_syugyo_st_now ";
									$condition  .= "AND     TIME_SYUGYO_ED >= :in_time_syugyo_ed_now ) ) ";
									$condition  .= ") ";

									$condition  .= "OR ";

									//休日が存在するとダメ
									$condition  .= "( ";
									$condition  .= "STAFF_CD               =  :staff_cd ";
									$condition  .= "AND   YMD_KINMU        =  :today ";
									$condition  .= "AND   SYUKIN_FLG       =  1 ";
									$condition  .= "AND ( BNRI_DAI_CD      <> :bnri_dai_cd ";
									$condition  .= "OR    BNRI_CYU_CD      <> :bnri_cyu_cd ) ";
									$condition  .= ") ";

									$condition  .= "OR ";

								}

								//検索で工程入力時　または　工程未入力で１日目　のみ
								if ( $KOUTEI_INP_FLG != 1
								or  ($KOUTEI_INP_FLG == 1
								and  $i              == 4) ) {

									//入力日から見て、前日チェック
									//入力前日で、DBのエンドタイムが、入力のスタートタイムを超えている
									//この時、入力外の工程のみ。ただし、入力の先頭日の場合は、入力の工程も含む
									//　　　　　　→　PHP内で、WHERE区書く
									//日付は、－１
									//時間は、入力値（スタートタイム）＋２４を使用
									$condition  .= "( ";
									$condition  .= "      STAFF_CD   = :staff_cd ";
									$condition  .= "AND   YMD_KINMU  = :yesterday ";
									$condition  .= "AND   SYUKIN_FLG       <> 1 ";
									//入力範囲の１日目は、前日が、入力範囲外なので、条件不要
									if ($i != 4) {
										$condition  .= "AND ( BNRI_DAI_CD <> :bnri_dai_cd ";
										$condition  .= "OR    BNRI_CYU_CD <> :bnri_cyu_cd ) ";
									}
									$condition  .= "AND    TIME_SYUGYO_ED >  :in_time_syugyo_st_24 ";
									$condition  .= ") ";

									if ( $KOUTEI_INP_FLG != 1) {
										$condition  .= "OR ";
									}

								}

								//検索で工程入力時　または　工程未入力で７日目　のみ
								if ( $KOUTEI_INP_FLG != 1
								or  ($KOUTEI_INP_FLG == 1
								and  $i              == 22) ) {

									//入力日から見て、翌日チェック
									//入力翌日で、DBのスタートタイムが、入力のエンドタイムより前
									//この時、入力外の工程のみ。ただし、入力の最終日の場合は、入力の工程も含む
									//　　　　　　→　PHP内で、WHERE区書く
									//日付は、＋１
									//時間は、入力値（エンドタイム）－２４を使用
									$condition  .= "( ";
									$condition  .= "      STAFF_CD   = :staff_cd ";
									$condition  .= "AND   YMD_KINMU  = :tomorrow ";
									$condition  .= "AND   SYUKIN_FLG       <> 1 ";
									//入力範囲の７日目は、翌日が、入力範囲外なので、条件不要
									if ($i != 22) {
										$condition  .= "AND ( BNRI_DAI_CD <> :bnri_dai_cd ";
										$condition  .= "OR    BNRI_CYU_CD <> :bnri_cyu_cd ) ";
									}
									$condition  .= "AND    TIME_SYUGYO_ST <  :in_time_syugyo_ed_24";
									$condition  .= ") ";

								}

								//上記で、検索条件を指定した　＝　検索が必要な場合のみ　ＤＢ読込チェック
								if ($condition != "") {

									//hidden項目位置を0-6に変換
									$j = ($i - 4) / 3;

									// 条件値指定
									if (strpos($condition, ':staff_cd') !== false) {
										$conditionVal['staff_cd']             = $STAFF_CD;
									}
									if (strpos($condition, ':bnri_dai_cd') !== false) {
										$conditionVal['bnri_dai_cd']          = $BNRI_DAI_CD;
									}
									if (strpos($condition, ':bnri_cyu_cd') !== false) {
										$conditionVal['bnri_cyu_cd']          = $BNRI_CYU_CD;
									}

									if (strpos($condition, ':today') !== false) {
										$conditionVal['today']                = $in_day[$j];
									}
									if (strpos($condition, ':yesterday') !== false) {
										$conditionVal['yesterday']            = date("Y/m/d", strtotime($in_day[$j] . " -1 day"  ));
									}
									if (strpos($condition, ':tomorrow') !== false) {
										$conditionVal['tomorrow']             = date("Y/m/d", strtotime($in_day[$j] . " +1 day"  ));
									}

									if (strpos($condition, ':in_time_syugyo_st_now') !== false) {
										$conditionVal['in_time_syugyo_st_now']    = substr($TIME_ST, 0, 2) . ":" . substr($TIME_ST, 2, 2) . ":" . "00";
									}
									if (strpos($condition, ':in_time_syugyo_ed_now') !== false) {
										$conditionVal['in_time_syugyo_ed_now']    = substr($TIME_ED, 0, 2) . ":" . substr($TIME_ED, 2, 2) . ":" . "00";
									}

									if (strpos($condition, ':in_time_syugyo_st_24') !== false) {
										$tmpTime = substr($TIME_ST, 0, 2) + 24;
										$conditionVal['in_time_syugyo_st_24'] = $tmpTime . ":" . substr($TIME_ST, 2, 2) . ":" . "00";
									}

									if (strpos($condition, ':in_time_syugyo_ed_24') !== false) {
										if ($TIME_ED >= 2400) {
											$conditionVal['in_time_syugyo_ed_24'] = date("H:i:s", mktime(substr($TIME_ED, 0, 2) - 24, substr($TIME_ED, 2, 2), 00));
										} else {
											$conditionVal['in_time_syugyo_ed_24'] = "00:00:00";
										}
									}
									
									// ▼拠点情報取得 //
									$sql  = 'SELECT ';
									$sql .= "$fields ";
									$sql .= 'FROM V_LSP010_T_STAFF_KINMU_CHK ';
									$sql .= 'WHERE ';
									$sql .= $condition;
									$sql .= $conditionKey;
									
									$dbread = $this->query($sql, $conditionVal, false);
									$dbread = self::editData($dbread);

									if (!empty($dbread)) {
										//レコードあり
										$message = vsprintf($this->MMessage->getOneMessage('LSPE010011'), array($count, $youbi[$j]));
										$this->errors['Check'][] =   $message;
									}

								}

							} else {
								//休日の場合のチェック

								//マスタから取得
								// ▼取得カラム設定 //
								$fields = '*';

								// ▼検索条件設定 //
								
								$conditionVal = array();
								$conditionKey = '';
								
								// ▼検索条件設定 //

								$condition  = "";

								//検索で工程入力時のみ
								if ($KOUTEI_INP_FLG != 1) {

									//シフトが存在するとダメ（休日も）
									$condition  .= "( ";
									$condition  .= "STAFF_CD               =  :staff_cd ";
									$condition  .= "AND   YMD_KINMU        =  :today ";
									$condition  .= "AND ( BNRI_DAI_CD      <> :bnri_dai_cd ";
									$condition  .= "OR    BNRI_CYU_CD      <> :bnri_cyu_cd ) ";
									$condition  .= ") ";

								}

								//上記で、検索条件を指定した　＝　検索が必要な場合のみ　ＤＢ読込チェック
								if ($condition != "") {

									//hidden項目位置を0-6に変換
									$j = ($i - 4) / 3;

									// 条件値指定
									if (strpos($condition, ':staff_cd') !== false) {
										$conditionVal['staff_cd']             = $STAFF_CD;
									}
									if (strpos($condition, ':bnri_dai_cd') !== false) {
										$conditionVal['bnri_dai_cd']          = $BNRI_DAI_CD;
									}
									if (strpos($condition, ':bnri_cyu_cd') !== false) {
										$conditionVal['bnri_cyu_cd']          = $BNRI_CYU_CD;
									}

									if (strpos($condition, ':today') !== false) {
										$conditionVal['today']                = $in_day[$j];
									}
									
									// ▼拠点情報取得 //
									$sql  = 'SELECT ';
									$sql .= "$fields ";
									$sql .= 'FROM V_LSP010_T_STAFF_KINMU_CHK ';
									$sql .= 'WHERE ';
									$sql .= $condition;
									$sql .= $conditionKey;
									
									$dbread = $this->query($sql, $conditionVal, false);
									$dbread = self::editData($dbread);

									if (!empty($dbread)) {
										//レコードあり
										$message = vsprintf($this->MMessage->getOneMessage('LSPE010011'), array($count, $youbi[$j]));
										$this->errors['Check'][] =   $message;
									}

								}

							}

						}

					}

				}

			}




			$count++;

		}



		if (count($this->errors) != 0) {
			return false;
		}

		if ($count4 == 0) {
			$message = "更新データがありません。";
			$this->errors['Check'][] = $message;
			return false;
		}

		return true;

	}
	
	/**
	 * コピーチェック日付
	 * 
	 * @param   $data     画面情報
	 * @param   $srcDate  コピー元基準日
	 * @param   $destDate コピー先基準日
	 * @return チェック結果OKの場合true、そうでない場合false
	 */
	public function checkTStaffKinmuCopyDb($data, $srcDate, $destDate) {
		
		$checkResult = true;
		
		// 検索条件を設定
		$conditionVal = array();
		$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
		$conditionVal['sosiki_cd'] = $this->_sosiki_cd;

		// コピー先の開始日を基準にチェック
		$date1 = $srcDate;
		$date2 = $this->calcDate($destDate, -1);
		$infos = $this->getStaffKinmuDuplicateData($date1, $date2);
		if (!empty($infos)) {
			foreach ($infos as $info) {
				$row = 0;
				foreach ($data as $array) {
					$row++;
					if ($array->staffCd == $info["STAFF_CD"]
							&& $array->syugyoSt == $info["TIME_SYUGYO_ST"]) {
						$checkResult = false;
						$message = vsprintf($this->MMessage->getOneMessage('LSPE010011'), array($row, $array->syugyoStLbl));
						$this->errors['Check'][] = $message;
						break;
					}
				}
			}
		}
		
		if ($checkResult == false) {
			return $checkResult;
		}
		
		
		// コピー先の終了日を基準にチェック
		$date1 = $this->calcDate($destDate, 7);
		$date2 = $this->calcDate($srcDate, 6);
		$infos = $this->getStaffKinmuDuplicateData($date1, $date2);
		if (!empty($infos)) {
			foreach ($infos as $info) {
				$row = 0;
				foreach ($data as $array) {
					$row++;
					if ($array->staffCd == $info["STAFF_CD"]
							&& $array->syugyoEd == $info["TIME_SYUGYO_ED"]) {
						$checkResult = false;
						$message = vsprintf($this->MMessage->getOneMessage('LSPE010011'), array($row, $array->syugyoEdLbl));
						$this->errors['Check'][] = $message;
						break;
					}
				}
			}
		}
		
		return $checkResult;
	}
	
	/**
	 * スタッフ勤務設定重複データ取得
	 * 
	 * @return 重複データ
	 */
	private function getStaffKinmuDuplicateData($date1, $date2) {
		
		// 検索条件を設定
		$conditionVal = array();
		$conditionVal['ninusi_cd'] = $this->_ninusi_cd;
		$conditionVal['sosiki_cd'] = $this->_sosiki_cd;
		$conditionVal['date1'] = $date1;
		$conditionVal['date2'] = $date2;
		
		$sql  = "select ";
		$sql .= "    KINMU_1.STAFF_CD, ";
		$sql .= "    time_format(KINMU_1.TIME_SYUGYO_ST, '%H%i') as TIME_SYUGYO_ST, ";
		$sql .= "    time_format(KINMU_2.TIME_SYUGYO_ED, '%H%i') as TIME_SYUGYO_ED ";
		$sql .= "from ";
		$sql .= "    ( ";
		$sql .= "        select ";
		$sql .= "            TSK.STAFF_CD, ";
		$sql .= "            MIN(TIME_SYUGYO_ST) as TIME_SYUGYO_ST ";
		$sql .= "        from ";
		$sql .= "            T_STAFF_KINMU TSK ";
		$sql .= "            inner join M_STAFF_KIHON MSK ";
		$sql .= "                on MSK.DAI_NINUSI_CD = :ninusi_cd ";
		$sql .= "                and MSK.DAI_SOSIKI_CD = :sosiki_cd ";
		$sql .= "        where ";
		$sql .= "            TSK.YMD_KINMU = STR_TO_DATE(:date1, '%Y%m%d') ";
		$sql .= "        group by ";
		$sql .= "            TSK.STAFF_CD, ";
		$sql .= "            TSK.YMD_KINMU ";
		$sql .= "    ) KINMU_1 ";
		$sql .= "    inner join ( ";
		$sql .= "                select ";
		$sql .= "                    TSK.STAFF_CD, ";
		$sql .= "                    MAX(TSK.TIME_SYUGYO_ED) as TIME_SYUGYO_ED, ";
		$sql .= "                    max(TIMEDIFF(TSK.TIME_SYUGYO_ED, '24:00:00')) as TIME_SYUGYO_ED_COMP ";
		$sql .= "                from ";
		$sql .= "                    T_STAFF_KINMU TSK ";
		$sql .= "                    inner join M_STAFF_KIHON MSK ";
		$sql .= "                        on MSK.DAI_NINUSI_CD = :ninusi_cd ";
		$sql .= "                        and MSK.DAI_SOSIKI_CD = :sosiki_cd ";
		$sql .= "                where ";
		$sql .= "                    TSK.YMD_KINMU = STR_TO_DATE(:date2, '%Y%m%d') ";
		$sql .= "                and '24:00:00' < TSK.TIME_SYUGYO_ED ";
		$sql .= "                group by ";
		$sql .= "                    TSK.STAFF_CD, ";
		$sql .= "                    TSK.YMD_KINMU ";
		$sql .= "            ) KINMU_2 ";
		$sql .= "        on KINMU_1.STAFF_CD = KINMU_2.STAFF_CD ";
		$sql .= "        and KINMU_1.TIME_SYUGYO_ST < KINMU_2.TIME_SYUGYO_ED_COMP ";
		
		// LSP按分率情報を取得
		$data = $this->queryWithLog($sql, $conditionVal, "スタッフ勤務設定重複データ取得");
		$data = self::editData($data);
		
		return $data;
		
	}
	
	
	/**
	 * 勤務設定複製
	 * @access   public
	 * @param    $staffCd スタッフコード
	 * @param    $srcDate コピー元基準日
	 * @param    $destDate コピー先基準日
	 * @return   更新結果
	 */
	public function copyStaffKinmu($staffCd, $srcDate, $destDate) {
	
		$pdo = null;
		$pdo2 = null;
		$return_cd = "0";
	
		$dateCnt = $this->getDateDiff($destDate, $srcDate);
		
		try {
	
			// 排他制御
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staffCd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'LSP',";
			$sql .= "'010',";
			$sql .= "1";
			$sql .= ")";
			$this->execWithPDOLog($pdo,$sql,'LSP010 排他制御オン');
	
			try {
	
				$pdo2 = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
				$pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$pdo2->beginTransaction();
	
				// 勤務設定情報を複製
				$params = "";
				$params .=  " '".$this->_ninusi_cd."'";
				$params .=  ",'".$this->_sosiki_cd."'";
				$params .=  ",'".$srcDate."'";
				$params .=  ",".$dateCnt;
				$params .=  ",'".$staffCd."'";
				$params .=  ',@return_cd';
				$sql = "CALL P_LSP010_COPY_T_STAFF_KINMU($params)";
				$this->execWithPDOLog($pdo2, $sql, '勤務設定情報　複製');
				$sql = "SELECT";
				$sql .= " @return_cd";
				$this->queryWithPDOLog($stmt,$pdo2,$sql, '勤務設定情報　複製');
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$return_cd = $result["@return_cd"];
				if ($return_cd == "1") {
					throw new Exception('勤務設定情報　複製');
				}

				// LSP勤務設定情報を複製
				$params = "";
				$params .=  " '".$this->_ninusi_cd."'";
				$params .=  ",'".$this->_sosiki_cd."'";
				$params .=  ",'".$srcDate."'";
				$params .=  ",".$dateCnt;
				$params .=  ",'".$staffCd."'";
				$params .=  ',@return_cd';
				$sql = "CALL P_LSP010_COPY_T_LSP_STAFF_KINMU($params)";
				$this->execWithPDOLog($pdo2, $sql, 'LSP勤務設定情報　複製');
				$sql = "SELECT";
				$sql .= " @return_cd";
				$this->queryWithPDOLog($stmt,$pdo2,$sql, 'LSP勤務設定情報　複製');
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$return_cd = $result["@return_cd"];
				if ($return_cd == "1") {
					throw new Exception('LSP勤務設定情報　複製');
				}
				
				$pdo2->commit();
					
			} catch (Exception $e) {
				$return_cd = '1';
				$this->printLog("fatal", "例外発生", "LSP010", $e->getMessage());
				$pdo2->rollBack();
			}
	
			$pdo2 = null;
	
			//排他制御オフ
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $staffCd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'LSP',";
			$sql .= "'010',";
			$sql .= "0";
			$sql .= ")";
	
			$this->execWithPDOLog($pdo,$sql,'LSP010 排他制御オフ');
	
			$pdo = null;
	
			if ($return_cd == "1") {
				$message = $this->MMessage->getOneMessage('CMNE000107');
				$this->errors['Check'][] =  $message;
				return false;
			}
	
			return true;
	
		} catch (Exception $e) {
			$this->printLog("fatal", "例外発生", "LSP010", $e->getMessage());
			$pdo = null;
		}
	
		//例外発生の場合、エラー
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}

	
	public function setTStaffKinmu($data, $ttl, $inp_staff_cd) {

		$pdo = null;
		$pdo2 = null;
		$return_cd = "0";
	
		try{
			
			//排他制御
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $inp_staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'LSP',";
			$sql .= "'010',";
			$sql .= "1";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql,'LSP010 排他制御オン');

			try {

				$pdo2 = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
				$pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$pdo2->beginTransaction();

				//タイトルから、日付を取得
				$youbi = array();
				$in_day = array();
				foreach($ttl as $obj) {
					$dataArray = $obj->dataTtl;
					$in_day[0]  = $dataArray[0];
					$in_day[1]  = $dataArray[2];
					$in_day[2]  = $dataArray[4];
					$in_day[3]  = $dataArray[6];
					$in_day[4]  = $dataArray[8];
					$in_day[5]  = $dataArray[10];
					$in_day[6]  = $dataArray[12];
				}

				//行ずつ処理　　レコード削除
				$count = 0;
				foreach($data as $obj) {

					$count++;

					//画面情報取得
					$cellsArray = $obj->cells;
					$dataArray = $obj->data;
					
					$STAFF_CD        = $cellsArray[1];

					$LINE_COUNT      = $dataArray[0];
					$CHANGED         = $dataArray[1];
					$BNRI_DAI_CD     = $dataArray[2];
					$BNRI_CYU_CD     = $dataArray[3];
					$KOUTEI_INP_FLG  = $dataArray[25];
					$SEQ_NO          = $dataArray[33];
					$COPY_FLG        = $dataArray[34];

					//　０：なし　　　　　　何もしない
					//　１：　修正　　　　　DELETE　→　INSERT
					//　２：　追加　　　　　DELETE　→　INSERT
					//　３：　削除　　　　　DELETE
					//　４：　修正→削除　　DELETE
					//　５：　追加→削除　　何もしない

					//追加・修正・削除時　かつ　追加に絡むもの以外（$SEQ_NO  !=  0）
					if ($CHANGED != '0'
						AND $CHANGED != '5'
						AND $SEQ_NO  !=  0 ) {
					
						//検索で工程入力時
						$sql = "CALL P_LSP010_DEL_T_STAFF_KINMU(";
						$sql .= "'" . $STAFF_CD . "',";
						$sql .= "'" . $BNRI_DAI_CD . "',";
						$sql .= "'" . $BNRI_CYU_CD . "',";
						$sql .= "'" . $in_day[0] . "',";
						$sql .= "'" . $in_day[6] . "',";
						$sql .= "'" . $SEQ_NO . "',";
						$sql .= "0,";
						$sql .= "@return_cd";
						$sql .= ")";
					
						$this->execWithPDOLog($pdo2,$sql, '勤務情報　削除');
						
						$sql = "SELECT";
						$sql .= " @return_cd";
						
						$this->queryWithPDOLog($stmt,$pdo2,$sql, '勤務情報　削除');
						
						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$return_cd = $result["@return_cd"];
						
						if ($return_cd == "1") {
						
							throw new Exception('勤務情報　削除');
						}
					}

				}

				//SEQ_NO振り直し　　---start
				$dataSort = array();
				$dataSort_STAFF_CD = array();
				$dataSort_DAI_CD = array();
				$dataSort_CYU_CD = array();
				$dataSort_SEQ_NO = array();

				//配列をソート用にコピー
				$count   = 0;
				foreach($data as $obj) {
					//画面情報取得
					$cellsArray = $obj->cells;
					$dataArray = $obj->data;
					
					$STAFF_CD   = $cellsArray[1];

					$LINE_COUNT   = $dataArray[0];
					$CHANGED      = $dataArray[1];
					$BNRI_DAI_CD  = $dataArray[2];
					$BNRI_CYU_CD  = $dataArray[3];
					$SEQ_NO       = $dataArray[33];

					$dataSort[$count]['STAFF_CD']    = $STAFF_CD;
					$dataSort[$count]['BNRI_DAI_CD'] = $BNRI_DAI_CD;
					$dataSort[$count]['BNRI_CYU_CD'] = $BNRI_CYU_CD;
					$dataSort[$count]['SEQ_NO']      = $SEQ_NO;
					$dataSort[$count]['LINE_COUNT']  = $count;

					$dataSort_STAFF_CD[$count] = $STAFF_CD;
					$dataSort_DAI_CD[$count]   = $BNRI_DAI_CD;
					$dataSort_CYU_CD[$count]   = $BNRI_CYU_CD;
					if ($SEQ_NO == 0) {
						$dataSort_SEQ_NO[$count]   = 99999;
					} else {
						$dataSort_SEQ_NO[$count]   = $SEQ_NO;
					}

					$count++;
				}

				//配列をソート
				array_multisort($dataSort_STAFF_CD,SORT_ASC,
				                $dataSort_DAI_CD,SORT_ASC,
				                $dataSort_CYU_CD,SORT_ASC,
				                $dataSort_SEQ_NO,SORT_ASC,
				                $dataSort);

				//SEQ_NOを振り直し、セット
				$count           = 0;
				$tmp_STAFF_CD    = "";
				$tmp_BNRI_DAI_CD = "";
				$tmp_BNRI_CYU_CD = "";
				$tmp_SEQ_NO      = 0;
				foreach($dataSort as $obj) {
					//スタッフ・大分類・中分類が変わったら、SEQ_NO　リセット
					if ( $tmp_STAFF_CD    != $obj['STAFF_CD']
					or   $tmp_BNRI_DAI_CD != $obj['BNRI_DAI_CD']
					or   $tmp_BNRI_CYU_CD != $obj['BNRI_CYU_CD'] ) {
						$tmp_STAFF_CD    = $obj['STAFF_CD'];
						$tmp_BNRI_DAI_CD = $obj['BNRI_DAI_CD'];
						$tmp_BNRI_CYU_CD = $obj['BNRI_CYU_CD'];
						$tmp_SEQ_NO= 0;
					}

					//SEQ_NO　セット
					$i = $obj['LINE_COUNT'];
					$CHANGED = $data[$i]->data[1];

					//削除以外は、カウントアップ　・　削除の場合は、カウントせず、ゼロをセット
					if ($CHANGED == '0' 
					or  $CHANGED == '1'
					or  $CHANGED == '2') {
						if ($obj['SEQ_NO'] == 0) {
							//SEQ_NO　カウントアップ
							$tmp_SEQ_NO = $tmp_SEQ_NO + 1;
						} else {
							//SEQ_NO　最大値として退避
							$tmp_SEQ_NO = $obj['SEQ_NO'];
						}

						$data[$i]->data[33] = $tmp_SEQ_NO;
					} else {
						$data[$i]->data[33] = 0;
					}
				}
				//SEQ_NO振り直し　　---end


				//行ずつ・列ずつ処理　　レコード追加
				$count = 0;
				foreach($data as $obj) {

					$count++;

					//画面情報取得
					$cellsArray = $obj->cells;
					$dataArray = $obj->data;
					
					$STAFF_CD   = $cellsArray[1];

					$LINE_COUNT   = $dataArray[0];
					$CHANGED      = $dataArray[1];
					$BNRI_DAI_CD  = $dataArray[2];
					$BNRI_CYU_CD  = $dataArray[3];
					$seqNo        = $dataArray[33];

					//　０：なし　　　　　　何もしない
					//　１：　修正　　　　　DELETE　→　INSERT
					//　２：　追加　　　　　DELETE　→　INSERT
					//　３：　削除　　　　　DELETE
					//　４：　修正→削除　　DELETE
					//　５：　追加→削除　　何もしない

					//追加と修正のみ
					if ($CHANGED == '1' 
					or  $CHANGED == '2') {

						//画面の各列を順次処理
						for ($i = 4; $i <= 22; $i=$i+3) {

							if (empty($dataArray[$i]) 
							&&  empty($dataArray[$i + 1]) 
							&&  empty($dataArray[$i + 2]) ) {
								//未入力は何もしない
							} else {
								//入力時のみ

								//hidden項目位置を0-6に変換
								$j = ($i - 4) / 3;

								//画面の列の値を退避
								$TIME_ST    = substr($dataArray[$i], 0, 2) . ":" . substr($dataArray[$i], 2, 2) . ":" . "00";
								$TIME_ED    = substr($dataArray[$i + 1], 0, 2) . ":" . substr($dataArray[$i + 1], 2, 2) . ":" . "00";
								$SYUKIN_FLG = $dataArray[$i + 2];
								if ($SYUKIN_FLG == '1') {
									$TIME_ST    = "";
									$TIME_ED    = "";
								}

								$IN_DATE    = $in_day[$j];

								//勤務情報を追加する
								$sql = "CALL P_LSP010_INS_T_STAFF_KINMU(";
								$sql .= "'" . $inp_staff_cd . "',";
								$sql .= "'" . $STAFF_CD . "',";
								$sql .= "'" . $BNRI_DAI_CD . "',";
								$sql .= "'" . $BNRI_CYU_CD . "',";
								$sql .= "'" . $seqNo . "',";
								$sql .= "'" . $IN_DATE . "',";
								$sql .= "'" . $SYUKIN_FLG . "',";
								$sql .= "'" . $TIME_ST . "',";
								$sql .= "'" . $TIME_ED . "',";
								$sql .= "@return_cd";
								$sql .= ")";

							
								$this->execWithPDOLog($pdo2,$sql, '勤務情報　追加');
								
								$sql = "SELECT";
								$sql .= " @return_cd";

								
								$this->queryWithPDOLog($stmt,$pdo2,$sql, '勤務情報　追加');
								
								$result = $stmt->fetch(PDO::FETCH_ASSOC);
								$return_cd = $result["@return_cd"];
								
								if ($return_cd == "1") {
								
									throw new Exception('勤務情報　追加');
								}

							
							}
						}
					}
				}

				//
				//ＬＳＰスタッフ勤務情報更新
				//

				//画面から処理対象となるスタッフコードを取得
				$arrStaffCd = array();
				$arrStaffCdMax = -1;
				$j = 0;
				$k = 0;
				foreach($data as $obj) {
					//画面情報取得
					$cellsArray = $obj->cells;
					$dataArray  = $obj->data;
					$STAFF_CD   = $cellsArray[1];
					$CHANGED      = $dataArray[1];

					//追加・修正・削除時
					if ($CHANGED != '0'
					AND $CHANGED != '5') {
						$k = 0;
						for($j = 0; $j <= $arrStaffCdMax ; $j++){
							if ($arrStaffCd[$j] == $STAFF_CD) {
								$k = 1;
							}
						}
						if ($k ==0) {
							$arrStaffCdMax++;
							$arrStaffCd[$arrStaffCdMax] = $STAFF_CD;
						}
					}
				}

				//スタッフコードのレコード削除
				for($j = 0; $j <= $arrStaffCdMax ; $j++){

					$sql = "CALL P_LSP010_DEL_T_LSP_STAFF_KINMU(";
					$sql .= "'" . $arrStaffCd[$j] . "',";
					$sql .= "'" . $in_day[0] . "',";
					$sql .= "'" . $in_day[6] . "',";
					$sql .= "@return_cd";
					$sql .= ")";
					
					$this->execWithPDOLog($pdo2,$sql, 'LSPスタッフ勤務情報　削除');
					
					$sql = "SELECT";
					$sql .= " @return_cd";
					
					$this->queryWithPDOLog($stmt,$pdo2,$sql, 'LSPスタッフ勤務情報　削除');
					
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
					$return_cd = $result["@return_cd"];
					
					if ($return_cd == "1") {
					
						throw new Exception('LSPスタッフ勤務情報　削除');
					}

				}

				//スタッフコード毎に処理
				for($arrStaffCdLoop = 0; $arrStaffCdLoop <= $arrStaffCdMax ; $arrStaffCdLoop++){

					$nowStaffCd = $arrStaffCd[$arrStaffCdLoop];		//処理対象　スタッフコード　退避

					//日付ごとに処理
					for($in_day_loop = 0; $in_day_loop <= 6 ; $in_day_loop++){

						$nowInDay = $in_day[$in_day_loop];		//処理対象　日付　退避

						//最小の就業開始時刻を取得　（休日を除いた同スタッフ、同日付の最小値）
						$sql  = "SELECT ";
						$sql .= "  STAFF_CD ";
						$sql .= "  ,YMD_KINMU ";
						$sql .= "  ,TIME_SYUGYO_ST_MIN ";
						$sql .= " FROM V_LSP010_T_STAFF_KINMU_MIN_TIME ";
						$sql .= " WHERE ";
						$sql .= "  STAFF_CD='" . $nowStaffCd . "' AND";
						$sql .= "  YMD_KINMU='" . $nowInDay . "'";
						
						$this->queryWithPDOLog($stmt,$pdo2,$sql,"勤務取得");

						//最小の就業開始時刻 Loop Start　データは、１件のはず
						//　　　　　　　　　　　　　　　また、レコードなしは、休日を除き、対象データがないので処理不要
						while($dbMinTimeValue = $stmt->fetch(PDO::FETCH_ASSOC)){

							$nowSyugyoStMin = $dbMinTimeValue['TIME_SYUGYO_ST_MIN'];		//就業開始時刻　退避

							//スタッフ勤務情報から対象レコードを取得　（休日を除いた同スタッフ、同日付）
							$sql  = "SELECT ";
							$sql .= "  STAFF_CD ";
							$sql .= "  ,BNRI_DAI_CD ";
							$sql .= "  ,BNRI_CYU_CD ";
							$sql .= "  ,YMD_KINMU ";
							$sql .= "  ,TIME_SYUGYO_ST ";
							$sql .= "  ,TIME_SYUGYO_ED ";
							$sql .= " FROM V_LSP010_T_STAFF_KINMU_GET ";
							$sql .= " WHERE ";
							$sql .= "  STAFF_CD='" . $nowStaffCd . "' AND";
							$sql .= "  YMD_KINMU='" . $nowInDay . "'";
							
							$this->queryWithPDOLog($stmt2,$pdo2,$sql,"スタッフ勤務情報取得");

							//スタッフ勤務情報 Loop Start
							while($dbKinmuValue = $stmt2->fetch(PDO::FETCH_ASSOC)){

								$nowBnriDaiCd    = $dbKinmuValue['BNRI_DAI_CD'];		//大分類コード　退避
								$nowBnriCyuCd    = $dbKinmuValue['BNRI_CYU_CD'];		//中分類コード　退避
								$nowTimeSyugyoSt = $dbKinmuValue['TIME_SYUGYO_ST'];		//工程開始時刻　退避
								$nowTimeSyugyoEd = $dbKinmuValue['TIME_SYUGYO_ED'];		//工程終了時刻　退避

								//休憩時間マスタを取得
								$sql  = "SELECT ";
								$sql .= "  NINUSI_CD ";
								$sql .= "  ,SOSIKI_CD ";
								$sql .= "  ,BNRI_DAI_CD ";
								$sql .= "  ,BNRI_CYU_CD ";
								$sql .= "  ,T_SYUGYO_ST ";
								$sql .= "  ,T_REST_ST ";
								$sql .= "  ,T_REST_ED ";
								$sql .= " FROM V_LSP010_M_REST_GET ";
								$sql .= " WHERE ";
								$sql .= "  NINUSI_CD='" . $this->_ninusi_cd . "' AND";
								$sql .= "  SOSIKI_CD='" . $this->_sosiki_cd . "' AND";
								$sql .= "  BNRI_DAI_CD='" . $nowBnriDaiCd . "' AND";
								$sql .= "  BNRI_CYU_CD='" . $nowBnriCyuCd . "' AND";
								$sql .= "  T_SYUGYO_ST='" . $nowSyugyoStMin . "'";
								
								$this->queryWithPDOLog($stmt3,$pdo2,$sql,"休憩時間マスタ取得");
								
								//最小の就業開始時刻 Loop Start　データは、１件のはず
								//　　　　　　　　　　　　　　　また、レコードなしは、休日を除き、対象データがないので処理不要
								while($dbRestValue = $stmt3->fetch(PDO::FETCH_ASSOC)){

									$nowRestSt = $dbRestValue['T_REST_ST'];		//休憩開始時刻　退避
									$nowRestEd = $dbRestValue['T_REST_ED'];		//休憩終了時刻　退避

									//工程開始が工程終了より大きければ、おわり条件設定
									if ($nowTimeSyugyoEd < $nowTimeSyugyoSt) {
										$nowTimeSyugyoSt = '99:99:99';
										$nowTimeSyugyoEd = '99:99:99';
									}

									//休憩終了が工程開始より大きければ対象
									//この時、休憩終了が工程開始より小さい場合は、対象外なので、何もせず、次のレコードへ
									if ($nowRestEd > $nowTimeSyugyoSt) {

										//開始時刻チェック
										if ($nowRestSt <= $nowTimeSyugyoSt) {
											//工程開始を休憩終了とする（休憩の為、破棄）
											$nowTimeSyugyoSt = $nowRestEd;
										} else {

											//今回出力対象時間取得
											$picupTimeSyugyoSt = $nowTimeSyugyoSt;

											if ($nowTimeSyugyoEd > $nowRestSt) {
												$picupTimeSyugyoEd = $nowRestSt;
											} else {
												$picupTimeSyugyoEd = $nowTimeSyugyoEd;
											}

											//対象レコード出力
											$sql = "CALL P_LSP010_INS_T_LSP_STAFF_KINMU(";
											$sql .= "'" . $inp_staff_cd . "',";
											$sql .= "'" . $nowStaffCd . "',";
											$sql .= "'" . $nowBnriDaiCd . "',";
											$sql .= "'" . $nowBnriCyuCd . "',";
											$sql .= "'" . $nowInDay . "',";
											$sql .= "'" . $picupTimeSyugyoSt . "',";
											$sql .= "'" . $picupTimeSyugyoEd . "',";
											$sql .= "@return_cd";
											$sql .= ")";
										
											$this->execWithPDOLog($pdo2,$sql, 'LSPスタッフ勤務情報　追加');
											
											$sql = "SELECT";
											$sql .= " @return_cd";
											
											$this->queryWithPDOLog($stmt,$pdo2,$sql, 'LSPスタッフ勤務情報　追加');
											
											$result = $stmt->fetch(PDO::FETCH_ASSOC);
											$return_cd = $result["@return_cd"];
											
											if ($return_cd == "1") {
											
												throw new Exception('LSPスタッフ勤務情報　追加');
											}

											//工程開始を休憩終了とする（休憩の為、破棄）
											$nowTimeSyugyoSt = $nowRestEd;

										}

									}

								//休憩時間マスタ Loop End
								}

								//工程開始が工程終了より大きければ、おわり条件設定
								if ($nowTimeSyugyoEd <= $nowTimeSyugyoSt) {
									$nowTimeSyugyoSt = '99:99:99';
									$nowTimeSyugyoEd = '99:99:99';
								}

								//レコード出力
								if ($nowTimeSyugyoSt < '99:99:99') {
									$sql = "CALL P_LSP010_INS_T_LSP_STAFF_KINMU(";
									$sql .= "'" . $inp_staff_cd . "',";
									$sql .= "'" . $nowStaffCd . "',";
									$sql .= "'" . $nowBnriDaiCd . "',";
									$sql .= "'" . $nowBnriCyuCd . "',";
									$sql .= "'" . $nowInDay . "',";
									$sql .= "'" . $nowTimeSyugyoSt . "',";
									$sql .= "'" . $nowTimeSyugyoEd . "',";
									$sql .= "@return_cd";
									$sql .= ")";
								
									$this->execWithPDOLog($pdo2,$sql, 'LSPスタッフ勤務情報　追加');
									
									$sql = "SELECT";
									$sql .= " @return_cd";
									
									$this->queryWithPDOLog($stmt,$pdo2,$sql, 'LSPスタッフ勤務情報　追加');
									
									$result = $stmt->fetch(PDO::FETCH_ASSOC);
									$return_cd = $result["@return_cd"];
									
									if ($return_cd == "1") {
									
										throw new Exception('LSPスタッフ勤務情報　追加');
									}
								}

							//スタッフ勤務情報 Loop End
							}

						//最小の就業開始時刻 Loop End
						}

					//日付ごとに処理 Loop End
					}

				//スタッフコード毎に処理 Loop End
				}


				$pdo2->commit();
			
			} catch (Exception $e) {
			
				$this->printLog("fatal", "例外発生", "LSP010", $e->getMessage());
				$pdo2->rollBack();
			}
			
			$pdo2 = null;
			
			//排他制御オフ
			$sql = "CALL P_SET_LOCK(";
			$sql .= "'" . $inp_staff_cd . "',";
			$sql .= "'" . $this->_ninusi_cd . "',";
			$sql .= "'" . $this->_sosiki_cd . "',";
			$sql .= "'LSP',";
			$sql .= "'010',";
			$sql .= "0";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql,'LSP010 排他制御オフ');
			
			$pdo = null;

			if ($return_cd == "1") {
				
				//例外の場合、例外メッセージ
				$message = $this->MMessage->getOneMessage('CMNE000107');
				$this->errors['Check'][] =  $message;
				return false;
			}

			return true;
		
		} catch (Exception $e) {
			
			$this->printLog("info", "例外発生", "LSP010", $e->getMessage());
			$pdo = null;
			
		}
		
		//例外発生の場合、エラー
		$message = $this->MMessage->getOneMessage('LSP010103');
		$this->errors['Check'][] =  $message;
		return false;

	}
	
	/**
	 * 日付の差を算出
	 * @param unknown $ymd1
	 * @param unknown $ymd2
	 * @return number
	 */
	function getDateDiff($ymd1, $ymd2) {
	
		$dateDiff = gmmktime(0,0,0,substr($ymd1, 4, 2),substr($ymd1, 6, 2),substr($ymd1, 0, 4))
		- gmmktime(0,0,0,substr($ymd2, 4, 2),substr($ymd2, 6, 2),substr($ymd2, 0, 4));
	
		return $dateDiff / (60 * 60 * 24);
	}

	/**
	 * 日付計算
	 * @param   $ymd     対象日付(yyyyMMdd)
	 * @param   $addDays 加算日付
	 * @return 加算後の日付
	 */
	function calcDate($ymd, $addDays) {
	
		$year = substr($ymd, 0, 4);
		$month = substr($ymd, 4, 2);
		$day = substr($ymd, 6, 2);
	
		$baseSec = mktime(0, 0, 0, $month, $day, $year);
		$addSec = $addDays * 86400;
		$targetSec = $baseSec + $addSec;
		return date("Ymd", $targetSec);
	}

	public function checkTimestamp($currentTimestamp) {

		$pdo = null;
	
		try{
			
			//排他制御
			$pdo = new PDO($this->db_dsn, $this->db_user_name, $this->db_user_password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			$sql = "CALL P_LSP010_GET_TIMESTAMP(";
			$sql .= "@timestamp";
			$sql .= ")";
		
			$this->execWithPDOLog($pdo,$sql, 'データ整合性チェックの為にタイムスタンプ取得');
			
			$sql = "";
			$sql .= "SELECT ";
			$sql .= "@timestamp";
		
		
			$this->queryWithPDOLog($stmt,$pdo,$sql, 'データ整合性チェックの為にタイムスタンプ取得');
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			$timestamp = $result['@timestamp'];
			
			if ($timestamp == null) {
				$timestamp = "1900-01-01 00:00:00";
			}
				
			$pdo = null;
			
			if (strtotime($currentTimestamp) >= strtotime($timestamp)) {
//				$this->errors['Check'][] =$currentTimestamp . ' ' . $timestamp;
				return true;
			} else {
				
				$message =$this->MMessage->getOneMessage('CMNE000106');
				$this->errors['Check'][] = $message;
				return false;
			}

			
		} catch (Exception $e){
	
			$this->printLog("fatal", "例外発生", "LSP010", $e->getMessage());
			$pdo = null;
		    throw $e;
		}
		
		$message = $this->MMessage->getOneMessage('CMNE000107');
		$this->errors['Check'][] =  $message;
		return false;
	}


}

?>
 