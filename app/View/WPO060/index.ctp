<?php
			echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSWPO060_RELOAD', 'DCMSWPO060_CALENDAR',
					'DCMSWPO060_POPUP', 'DCMSWPO060_BUNRUI', 'DCMSWPO060_UPDATE'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSTPM'), false, array('inline'=>false));
?>
<!-- WRAPPER -->
<div id="wrapper_s">

<!-- 検索 -->
<div class="box" style="width:790px;">
	<?php echo $this->Form->create('WPO060Model', array('url' => '/WPO060','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
		<table class="cndt" style="width:790px">
			<tbody>
			<colgroup>
				<col width="80px">
				<col width="170px">
				<col width="80px">
				<col width="100px">
				<col width="80px">
				<col width="100px">
				<col width="30px">
				<col width="140px">
			</colgroup>
			<tr>
				<td>就労日付</td>
				<td class="colBnriCd" style="text-align:left;" colspan="1">
						<?php echo $this->Form->input('ymd_from',
												array('maxlength' => '40',
													'label' => false,
													'error' => false,
													'style' => 'width:100px',
													'name' => 'ymd',
													'id' => 'ymd',
													'value' => $ymd,
													'class' => 'zen')) ?>
				<td>開始時間</td>
				<td>
					<select name='timeFrom' id='timeFrom'>
<?php
	foreach ($timeFrom as $array) {
		echo '<option value="' . $array . '">' . $array . '</option>';
	}
?>
					</select>
				<td>終了時間</td>
				<td>
					<select name='timeTo' id='timeTo'>
						<option value=""></option>
<?php
	foreach ($timeTo as $array) {
		echo '<option value="' . $array . '">' . $array . '</option>';
	}
?>
					</select>
				</td>
				<td></td>
				<td></td>
			</tr>
			<form>
				<td>所属会社</td>
				<td>
					<select name='haken' id='haken'>
						<option value=''></option>
<?php
	foreach ($hakenList as $array) {
		echo '<option value="' . $array['KAISYA_CD'] . '">' . $array['KAISYA_NM'] . '</option>';
	}
?>
					</select>
				</td>
				<td>役職</td>
				<td>
					<select name='yakushoku' id='yakushoku'>
				<option value=""></option>
<?php
		foreach($kbn_pst as $array) {
		echo '<option value="' . $array['MEI_CD'] . '">' . $array['MEI_1'] . '</option>';
		}
?>
					</select>
				</td>
				<td>契約形態</td>
				<td>
					<select name='keiyaku' id='keiyaku'>
				<option value=""></option>
<?php
		foreach($kbn_keiyaku as $array) {
		echo '<option value="' . $array['MEI_CD'] . '">' . $array['MEI_1'] . '</option>';
		}
?>
					</select>
				</td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>ｽﾀｯﾌCD</td>
				<td>
					<input name="staff_cd" id="staff_cd" maxlength="6" type="text" class="han"  style="width:100px;height:20px">
				</td>
				<td>ｽﾀｯﾌ名</td>
				<td colspan='3'>
					<input name="staff_nm" id="staff_nm" maxlength="30" type="text" class="zen"  style="width:140px;height:20px">
				</td>
				<td></td>
				<td><input value="検索" type="submit"></td>
			</tr>
			</tbody>
		</table>
	<?php echo $this->Form->end() ?>
</div>
<!--/ 検索 -->

<div class="box" style="width:790px;">
		<table class="cndt" style="width:790px">
			<tbody>
			<colgroup>
				<col width="80px">
				<col width="300px">
				<col width="80px">
				<col width="30px">
				<col width="80px">
				<col width="0px">
				<col width="0px">
				<col width="210px">
			</colgroup>
			<tr>
				<td>一括差込</td>
				<td>※チェックが入ると検索した全スタッフの工程を差込</td>
				<td>開始時間</td>
				<td></td>
				<td>終了時間</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td style='padding-top:15px;'>差込工程</td>
				<td>
<!-- 工程コンボ -->
			    	<div id="kotei_dropdown_menu_div" style="display:block;position:relative;width:25px;float:left;margin-top:10px;text-align:left;">
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
				<div style="position:absolute;width:20px;height:10px;right:0px;z-index:0;" >&raquo;</div>
				<div style="height:30px;padding-left:5px;width:120px;cursor:default;">
					<?php echo $koteiDaiRyaku ?>
				</div>
<?php
		} else {
?>

			<li value="<?php echo $koteiDaiCd ?>">
				<div style="height:30px;padding-left:5px;width:120px;">
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
					<li value="<?php echo $koteiDaiCd . "_" . $koteiCyuCd ?>" >
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
			    	<div id="processSelect" style="display:block;position:relative;width:250px;float:left;margin-top:10px;text-align:left;">
						<div style="position:relative;width:180px;float:left;margin-top:0px;text-align:left;">
							<input id="koteiText" class="calendar han" type="text" value=""
								 style="width:220px;height:20px" readonly>
						</div>
        			</div>
        			<!-- /工程コンボ -->
				</td>
				<td style='padding-top:15px;'>
					<input type='text'  name='timeFrom2' id='timeFrom2' maxlength='4' class='han' style='width:50px;;height:20px'>
				<td style='padding-top:15px;'>～</td>
				<td style='padding-top:15px;'>
					<input type='text'  name='timeTo2' id='timeTo2' maxlength='4' class='han' style='width:50px;;height:20px'>
				</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			</tbody>
		</table>
	<?php echo $this->Form->end() ?>
</div>

<!-- 更新ボタン-->
<div style="height:40px;width:100%;text-align:right;max-width:1180px;" id="buttons" >
	<input type="button"  value="前回更新内容" style="width:120px;height:30px;" id="popupButton" >
	<input type="button"  value="登録" style="width:120px;height:30px;" id="updateButton" >
</div>
<!--/ 更新ボタン-->

<!-- メインテーブル-->
<div class="resizable_s" id="lstTable">
	<div class="tableRowTitle row_ttl_1" style="width:960px;">
		<div class="tableCell cell_ttl_1" style="width:30px;" ></div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >就業日</div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >スタッフコード</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >スタッフ名</div>
		<div class="tableCell cell_ttl_1" style="width:130px;" >所属</div>
		<div class="tableCell cell_ttl_1" style="width:50px;" >役職</div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >形態</div>
		<div class="tableCell cell_ttl_1" style="width:150px;" >就業開始</div>
		<div class="tableCell cell_ttl_1" style="width:150px;" >就業終了</div>
	</div>
<?php  $count = 0; ?>
<?php  foreach ($lsts as $array): ?>
	<div class="tableRow row_dat" style="width:960px;">
		<div class="tableCell cell_dat" style="width:30px;" ><input type='checkbox' checked="checked" class='check'></div>
		<div class="tableCell cell_dat date" style="width:100px;" ><?php echo $array['YMD_SYUGYO'] ?></div>
		<div class="tableCell cell_dat cd" style="width:80px;" ><?php echo $array['STAFF_CD'] ?></div>
		<div class="tableCell cell_dat nm" style="width:100px;" ><?php echo $array['STAFF_NM'] ?></div>
		<div class="tableCell cell_dat nm" style="width:130px;" ><?php echo $array['KAISYA_NM'] ?></div>
		<div class="tableCell cell_dat nm" style="width:50px;" ><?php echo $array['YAKUSHOKU'] ?></div>
		<div class="tableCell cell_dat nm" style="width:80px;" ><?php echo $array['KEIYAKU'] ?></div>
		<div class="tableCell cell_dat date" style="width:150px;" ><?php echo $array['DT_SYUGYO_ST'] ?></div>
		<div class="tableCell cell_dat date" style="width:150px;" ><?php echo $array['DT_SYUGYO_ED'] ?></div>
		<div class="hiddenData"><?php echo $array['YMD_SYUGYO'] ?></div>
		<div class="hiddenData"><?php echo $array['STAFF_CD'] ?></div>
	</div>
<?php  $count++; ?>
<?php  endforeach; ?>
</div>
<div id="timestamp" style="display:none;" ><?php  echo $timestamp; ?></div>
<div id="YMD" style="display:none;" ><?php  echo $ymd; ?></div>
<div id="TIME_FROM" style="display:none;" ><?php  echo $timeFrom_hidden; ?></div>
<div id="TIME_TO" style="display:none;" ><?php  if(!empty($timeTo_hidden)){ echo $timeTo_hidden; }?></div>
<div id="HAKEN" style="display:none;" ><?php  if(!empty($haken)){ echo $haken; }?></div>
<div id="YAKUSHOKU" style="display:none;" ><?php  if(!empty($yakushoku)){ echo $yakushoku; }?></div>
<div id="KEIYAKU" style="display:none;" ><?php  if(!empty($keiyaku)){ echo $keiyaku; }?></div>
<div id="STAFF_CD" style="display:none;" ><?php  if(!empty($staff_cd)){ echo $staff_cd; }?></div>
<div id="STAFF_NM" style="display:none;" ><?php  if(!empty($staff_nm)){ echo $staff_nm; }?></div>
<div id="MIN_TIME" style="display:none;" ><?php  if(!empty($min_time)){ echo $min_time; }?></div>
<div id="MAX_TIME" style="display:none;" ><?php  if(!empty($max_time)){ echo $max_time; }?></div>
<!--/ メインテーブル-->

</div>
<!--/ #wrapper-->
