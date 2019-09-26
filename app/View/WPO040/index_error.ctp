<?php
			echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSWPO040_RELOAD','DCMSWPO040_POPUP',
					'DCMSWPO040_ADDROW','DCMSWPO040_CALENDAR','staffsearch','DCMSWPO040_UPDATE','DCMSKOTEI_DROPDOWN_MENU'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSWPO'), false, array('inline'=>false));

?>
            <div id="wrapper_l">				<!-- 検索 -->
				<div class="box" style="width:1000px;">
<?php 				echo $this->Form->create('WPO040Model', array('url' => '/WPO040/index',
															      'type' => 'get',
				     											  'inputDefaults' => array('label' => false,
				     											  							'div' => false,
				     											  							'hidden' => false)))

?>
					<table class="cndt" style="width:1000px;">
						<colgroup>
							<col width="120px">
							<col width="150px">
							<col width="60px">
							<col width="300px">
							<col width="60px">
							<col width="150px">
							<col width="220px">
						</colgroup>
						<tr>
							<td >日付</td>
					    	<td >
						    	<input name="ymd_syugyo" id="syugyoText" maxlength="8" class="calendar han" type="text" style="width:80px;height:20px">
					    	</td>

					    	<td >工程</td>
					    	<!-- 工程コンボ -->
					    	<td  style="width:300px">
						    	<div id="kotei_dropdown_menu_div" style="display:block;position:relative;width:25px;height:25px;float:left;text-align:left;">
						    		<div id="menu_img"><img src="/DCMS/img/search.png" style="width:24px;height:24px" onclick="menuDisp();"></div>
						    		<div id="ul_class" style="display:none;">
						    			<ul style="display:block;" class="ul_first">
						    				<li style="display:block;position:relative;" value= ""
						    							onClick="setMenuProcess('','','','','','');">
												<div style="height:30px;padding-left:5px;width:120px;cursor:pointer;">（選択なし）</div>
											</li>
<?php

	foreach ($koteiDaiList as $array1) {

		$koteiDaiCd    = (string)$array1['BNRI_DAI_CD'];
		$koteiDaiRyaku = (string)$array1['BNRI_DAI_RYAKU'];
		$dai_flg       = 0;

		foreach ($koteiCyuList as $array2) {

			if ($koteiDaiCd == $array2['BNRI_DAI_CD']) {
				$dai_flg = 1;
				break;
			}
		}

		if($dai_flg == 1){
?>

			<li style="display:block;position:relative;" value="<?php echo $koteiDaiCd ?>" >
				<div style="position:absolute;width:20px;height:0px;right:0px;z-index:0;" >&raquo;</div>
				<div style="height:30px;padding-left:5px;width:120px;cursor:default;">
					<?php echo $koteiDaiRyaku ?>
				</div>
<?php
		} else {
?>

			<li>
				<div style="height:30px;padding-left:5px;width:120px;height:30px;">
					<?php echo $koteiDaiRyaku ?>
				</div>
<?php
		}
?>
				<ul value="<?php echo $koteiDaiCd ?>" style="display:none;" class="ul_second">
<?php
		foreach ($koteiCyuList as $array2) {

			if ($koteiDaiCd == $array2['BNRI_DAI_CD']) {

				$koteiCyuCd = (string)$array2['BNRI_CYU_CD'];
				$koteiCyuRyaku = (string)$array2['BNRI_CYU_RYAKU'];
				$cyu_flg       = 0;

				foreach ($koteiSaiList as $array3) {

					if ($koteiDaiCd == $array3['BNRI_DAI_CD'] &&
						$koteiCyuCd == $array3['BNRI_CYU_CD']) {
						$cyu_flg = 1;
					}
				}

				if($cyu_flg == 1) {
?>
					<li value="<?php echo $koteiDaiCd . "_" . $koteiCyuCd ?>" >
						<div style="position:absolute;width:20px;height:10px;right:0px;z-index:0;" >&raquo;</div>
						<div style="height:30px;padding-left:5px;width:120px;cursor:default;">
							 <?php echo $koteiCyuRyaku ?>
						</div>
<?php
				} else {
?>
					<li>
						<div style="height:30px;padding-left:5px;width:120px;">
							 <?php echo $koteiCyuRyaku ?>
						</div>
<?php
				}

?>
						<ul value="<?php echo $koteiDaiCd . "_" . $koteiCyuCd ?>" style="display:none;" class="ul_third">
<?php
				foreach ($koteiSaiList as $array3) {

					if ($koteiDaiCd == $array3['BNRI_DAI_CD'] &&
						$koteiCyuCd == $array3['BNRI_CYU_CD']) {

							$koteiSaiCd = (string)$array3['BNRI_SAI_CD'];
							$koteiSaiRyaku = (string)$array3['BNRI_SAI_RYAKU'];
?>
							<li value="<?php echo $koteiDaiCd . "_" . $koteiCyuCd  . "_" . $koteiSaiCd ?>">
								<div style="height:30px;padding-left:5px;width:120px;cursor:pointer;"
											onClick="setMenuProcess('<?php echo $koteiDaiCd ."', '" .
								 											 $koteiCyuCd ."', '" .
									 										 $koteiSaiCd ."', '" .
										 								     $koteiDaiRyaku ."', '" .
											 								 $koteiCyuRyaku ."', '" .
												 						     $koteiSaiRyaku ?>');">
									<?php echo $koteiSaiRyaku ?>
								</div>
							</li>
<?php
					}
				}
?>
						</ul>
					</li>
<?php
			}
		}
?>
				</ul>
			</li>
<?php
	}
?>
										</ul>
										<input type="hidden" name="kotei" id="koteiCd" value="">
									</div>
								</div>
						    	<div id="processSelect" style="display:block;position:relative;width:250px;float:left;text-align:left;">
									<div style="position:relative;width:180px;float:left;margin-top:0px;text-align:left;">
										<input id="koteiText" class="calendar han" type="text" value=""
											 style="width:220px;height:20px" readonly>
									</div>
		            			</div>
		            			<!-- /工程コンボ -->
		            		</td>
					    	<td >スタッフ</td>
					    	<td >
					    		<input name="staff_nm" id="staff_nmText" maxlength="30" type="text" class="zen"  style="width:140px;height:20px">
					    	</td>
					    	<td style="text-align:right;">
					    		<input value="検索" type="submit">
					    	</td>
				    	</tr>
                        <tr>
                            <td >就業終了エラー</td>
                            <td>
                                <select name="error_flg" id="error_flg">
                                    <option value="0"></option>
                                    <option value="1">就業日付のエラー</option>
                                    <option value="2">就業日付より過去のエラー</option>
                                </select>
                            </td>
                        </tr>
				    </table>
					<?php echo $this->Form->end() ?>
				</div>
				<!--/ 検索 -->

				<!--/ 区切り線 -->
				<div class="line"></div>
				<!--/ 区切り線 -->


				<!-- ボタン-->
				<div style="height:40px;width:100%;text-align:right;" id="buttons" >
					<input type="button" value="追加" class="btn" id="addButton" >
					<input type="button" value="更新" class="btn" id="updateButton" >
				</div>
				<!--/ ボタン-->

				<!-- メインテーブル-->
				<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>
				<div class="resizable_s" style="height:650px;" id="lstTable" >
					<div class="tableRowTitle row_ttl_sp_2" style="width:5380px">
						<div class="tableCell cell_ttl_sp_2" style="width:60px;" ><?php echo Configure::read("Bango") ?></div>
						<div class="tableCell cell_ttl_sp_2" style="width:120px;" >スタッフコード</div>
						<div class="tableCell cell_ttl_sp_2" style="width:100px;" >スタッフ名</div>
						<div class="tableCell cell_ttl_sp_2" style="width:100px;" >日付</div>
						<div class="tableCell cell_ttl_sp_2" style="width:70px;" >就業開始</div>
						<div class="tableCell cell_ttl_sp_2" style="width:70px;" >就業終了</div>
<?php
						for ($i=1;$i<=40;$i++) {
?>
						<div style="float:left;width:120px;height:70px;">
							<div class="tableCell cell_ttl_1" style="width:110px;">工程名<?php echo mb_convert_kana($i, "A", "UTF-8") ?></div>
							<div class="tableCell cell_ttl_1" style="width:50px;">開始</div>
							<div class="tableCell cell_ttl_1" style="width:50px;">終了</div>
						</div>
<?php
						}
?>
					</div>
<?php
				if (!empty($lsts)) {

					foreach($lsts as $obj) {

						$cellsArray = $obj->cells;
						$dataArray = $obj->data;
						$koteisArray = $obj->koteis;

?>
					<div class="tableRow row_dat_sp_2" style="width:5380px;">
						<div class="tableCell cell_dat_sp_2 bango" style="width:60px;" ><?php echo $cellsArray[0] ?></div>
						<div class="tableCell cell_dat_sp_2 kbn" style="width:120px;" ><?php echo $cellsArray[1] ?></div>
						<div class="tableCell cell_dat_sp_2 nm" style="width:100px;" ><?php echo $cellsArray[2] ?></div>
						<div class="tableCell cell_dat_sp_2 date" style="width:100px;" ><?php echo $cellsArray[3] ?></div>
						<div class="tableCell cell_dat_sp_2 date" style="width:70px;" ><?php echo $cellsArray[4] ?></div>
						<div class="tableCell cell_dat_sp_2 date" style="width:70px;" ><?php echo $cellsArray[5] ?></div>
						<div class="hiddenData"><?php echo $dataArray[0] ?></div>
						<div class="hiddenData"><?php echo $dataArray[1] ?></div>
						<div class="hiddenData"><?php echo $dataArray[2] ?></div>
						<div class="hiddenData"><?php echo $dataArray[3] ?></div>
						<div class="hiddenData"><?php echo $dataArray[4] ?></div>
<?php
						foreach($koteisArray as $obj2) {

							$cellsKoteiArray = $obj2->cells;
							$dataKoteiArray = $obj2->data;
?>
						<div class="tableCellGroupKotei" style="float:left;width:120px;height:60px;">
							<div class="tableCellKotei cell_dat nm" style="width:110px;"><?php echo $cellsKoteiArray[0] ?></div>
							<div class="tableCellKotei cell_dat date" style="width:50px;"><?php echo $cellsKoteiArray[1] ?></div>
							<div class="tableCellKotei cell_dat date" style="width:50px;"><?php echo $cellsKoteiArray[2] ?></div>
							<div class="hiddenDataKotei"><?php echo $dataKoteiArray[0] ?></div>
							<div class="hiddenDataKotei"><?php echo $dataKoteiArray[1] ?></div>
							<div class="hiddenDataKotei"><?php echo $dataKoteiArray[2] ?></div>
							<div class="hiddenDataKotei"><?php echo $dataKoteiArray[3] ?></div>
							<div class="hiddenDataKotei"><?php echo $dataKoteiArray[4] ?></div>
							<div class="hiddenDataKotei"><?php echo $dataKoteiArray[5] ?></div>
							<div class="hiddenDataKotei"><?php echo $dataKoteiArray[6] ?></div>
							<div class="hiddenDataKotei"><?php echo $dataKoteiArray[7] ?></div>
							<div class="hiddenDataKotei"><?php echo $dataKoteiArray[8] ?></div>
							<div class="hiddenDataKotei"><?php echo $dataKoteiArray[9] ?></div>
						</div>
<?php
						}
?>
					</div>
<?php
					}
				}
?>
				</div>

            </div>
            <div id="timestamp" style="display:none;" ><?php echo $timestamp; ?></div>
            <div style="display:none;" id="ymd_syugyo_storage"><input type="text" style="width:80px;" class="han" maxlength="8" id="ymd_syugyo" ></div>