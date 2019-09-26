<?php
			echo $this->Html->script(array('effects','window','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSLSP010_RELOAD','DCMSLSP010_POPUP',
					'DCMSLSP010_ADDROW','staffsearch','DCMSLSP010_BUNRUI','DCMSLSP010_COPY'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','DCMSLSP'), false, array('inline'=>false));
?>             
<!-- WRAPPER -->
<div id="wrapper_s">

<!-- 検索 -->
<div class="box" style="width:1000px">
	<?php echo $this->Form->create('LSP010Model', array('url' => '/LSP010','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?> 
		<input type="hidden" id="mode" name="mode" value="<?php echo $mode ?>"/>
		<input type="hidden" id="sel_date_lsp020" value="<?php echo $sel_date_lsp020 ?>"/>
		<table class="cndt">
			<colgroup>
				<col width="80px">
				<col width="350px">
				<col width="80px">
				<col width="150px">
				<col width="150px">
				<col width="150px">
				<col width="150px">
			</colgroup>
			<tbody>
			<!-- １行目 -->
			<tr>
				<td>工程</td>
				<td colspan="1">

				    	<!-- 工程コンボ -->
				    	<div id="kotei_dropdown_menu_div" style="display:block;position:relative;width:25px;float:left;margin-top:1px;text-align:left;">
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
				<div style="position:absolute;width:20px;height:1px;right:0px;z-index:0;" >&raquo;</div>
				<div style="height:30px;padding-left:5px;width:120px;cursor:default;"
					>
					<?php echo $koteiDaiRyaku ?>
				</div>
<?php
		} else {
?>

			<li value="<?php echo $koteiDaiCd ?>">
				<div style="height:30px;padding-left:5px;width:120px;"
					>
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
?>
				<li value="<?php echo $koteiDaiCd . "_" . $koteiCyuCd ?>" >
					<div style="height:30px;padding-left:5px;width:120px;cursor:default;"
						onClick="setMenuProcess('<?php echo $koteiDaiCd ."', '" . 
										    $koteiCyuCd ."', '" . 
										    '' ."', '" . 
										    $koteiDaiRyaku ."', '" . 
										    $koteiCyuRyaku ."', '" . 
										    '' ?>');"
						>
						 <?php echo $koteiCyuRyaku ?>
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
?>
								</ul>
								<input type="hidden" name="kotei" id="koteiCd" value="">
								<input type="hidden" name="koteiCd_hidden" id="koteiCd_hidden" value="">
								<input type="hidden" name="koteiText_hidden" id="koteiText_hidden" value="">
							</div>
						</div>
				    	<div id="processSelect" style="display:block;position:relative;width:250px;float:left;margin-top:1px;text-align:left;">
							<div style="position:relative;width:180px;float:left;margin-top:0px;text-align:left;">
								<input id="koteiText" class="calendar han" type="text" value=""  
									 style="width:220px;height:20px" readonly>
							</div>
            			</div>
            			<!-- /工程コンボ -->
				</td>


            			<!-- /スタッフ検索 -->
				<td >スタッフ</td>
				<td >
					<div><input type="button" value="検索" onClick="clickStaffSearchPopUp()"  style="width: 65px;height:24px;">
					<input type="button" value="ｸﾘｱ" onClick="clickStaffClear()"  style="width: 35px;height:24px;"></div>
				</td>

				<td class="colBnriCd" style="text-align:left;">

						<div  id="staff_cd" ></div>

						<?php echo $this->Form->input('staff_cd_hidden', 
										array('maxlength' => '10', 
											'label' => false, 
											'error' => false, 
											'style' => 'width:100px', 
											'hidden' => true, 
											'class' => 'han',
											'id'    => 'staff_cd_hidden')) ?>
				</td>

				<td class="colBnriCd" style="text-align:left;" colspan="2">

						<div  id="staff_nm"></div>

						<?php echo $this->Form->input('staff_nm_hidden', 
										array('maxlength' => '20', 
											'label' => false, 
											'error' => false, 
											'style' => 'width:100px', 
											'hidden' => true, 
											'class' => 'han',
											'id'    => 'staff_nm_hidden')) ?>
											
						<input type="hidden" name="staff_cd_srh_hidden" id="staff_cd_srh_hidden" value="">
						<input type="hidden" name="staff_nm_srh_hidden" id="staff_nm_srh_hidden" value="">
				</td>
			</tr>
			<!-- ２行目 -->
			<tr>
				<td>日付</td>
				<td class="colBnriCd" style="text-align:left;" colspan="5">
					<select name="sel_date"  id="selDateCombo">
						<option value=""></option>
						<?php
						foreach($selDate as $key => $value) :
						?>
						<option value="<?php echo $key ?>"><?php echo $value?></option>
						<?php
						endforeach;
						?>
					</select>
					<input type="hidden" id="sel_date_hidden">
				</td>
				<td>
					<input value="検索" type="submit";div="btnsubmit">	
				</td>
			</tr>
			</tbody>
		</table>
</div>
<!--/ 検索 -->

<?php if ($lstViewFlg == true) { ?>

<!--/ 区切り線 -->
<div class="line"></div>
<!--/ 区切り線 -->

<div id="setdatediv" >
	<?php 
		$disabled = 'disabled';
		if ($copyEnabled == true) {
			$disabled = '';
		}
	?>
	<td>コピー先日付</td>
	<td class="colBnriCd" style="text-align:left;">
		<select name="set_date"  id="setDateCombo"  <?php echo $disabled ?>>
			<option value=""></option>
			<?php
			foreach($setDate as $key => $value) :
			?>
			<option value="<?php echo $key ?>"><?php echo $value ?></option>
			<?php
			endforeach;
			?>
		</select>
	</td>
	<input type="button"  value="コピー" style="width:120px;height:30px;" id="copyButton" <?php echo $disabled ?>>
</div>

	<?php echo $this->Form->end() ?> 

<!-- 更新ボタン-->
<div style="height:40px;width:100%;text-align:right; max-width:1260px;" id="buttons" >
	<input type="button"  value="追加" style="width:120px;height:30px;" id="addButton" >
	<input type="button"  value="更新" style="width:120px;height:30px;" id="updateButton" >
</div>
<!--/ 更新ボタン-->

<!-- メインテーブル-->
<div class="resizable_s" id="lstTable">
	<div class="tableRowTitle row_ttl_1" style="width:1370px;">

	<?php
	if (!empty($ttl)) {

		foreach($ttl as $objttl) {

			$cellsArrayTtl = $objttl->cellsTtl;
			$dataArrayTtl = $objttl->dataTtl;
		
	?>
		<div class="tableCell cell_ttl_1" style="width:60px;" ><?php echo Configure::read('Bango'); ?></div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >コード</div>
		<div class="tableCell cell_ttl_1" style="width:130px;" >名前</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >大分類</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >中分類</div>

		<div class="tableCell cell_ttl_1" style="width:110px;" ><?php echo $cellsArrayTtl[5] ?></div>
		<div class="tableCell cell_ttl_1" style="width:110px;" ><?php echo $cellsArrayTtl[6] ?></div>
		<div class="tableCell cell_ttl_1" style="width:110px;" ><?php echo $cellsArrayTtl[7] ?></div>
		<div class="tableCell cell_ttl_1" style="width:110px;" ><?php echo $cellsArrayTtl[8] ?></div>
		<div class="tableCell cell_ttl_1" style="width:110px;" ><?php echo $cellsArrayTtl[9] ?></div>
		<div class="tableCell cell_ttl_1" style="width:110px;" ><?php echo $cellsArrayTtl[10] ?></div>
		<div class="tableCell cell_ttl_1" style="width:110px;" ><?php echo $cellsArrayTtl[11] ?></div>

		<div class="hiddenData DATE_1"><?php echo $dataArrayTtl[0] ?></div>
		<div class="hiddenData DATE_1_YB"><?php echo $dataArrayTtl[1] ?></div>
		<div class="hiddenData DATE_2"><?php echo $dataArrayTtl[2] ?></div>
		<div class="hiddenData DATE_2_YB"><?php echo $dataArrayTtl[3] ?></div>
		<div class="hiddenData DATE_3"><?php echo $dataArrayTtl[4] ?></div>
		<div class="hiddenData DATE_3_YB"><?php echo $dataArrayTtl[5] ?></div>
		<div class="hiddenData DATE_4"><?php echo $dataArrayTtl[6] ?></div>
		<div class="hiddenData DATE_4_YB"><?php echo $dataArrayTtl[7] ?></div>
		<div class="hiddenData DATE_5"><?php echo $dataArrayTtl[8] ?></div>
		<div class="hiddenData DATE_5_YB"><?php echo $dataArrayTtl[9] ?></div>
		<div class="hiddenData DATE_6"><?php echo $dataArrayTtl[10] ?></div>
		<div class="hiddenData DATE_6_YB"><?php echo $dataArrayTtl[11] ?></div>
		<div class="hiddenData DATE_7"><?php echo $dataArrayTtl[12] ?></div>
		<div class="hiddenData DATE_7_YB"><?php echo $dataArrayTtl[13] ?></div>
		<div class="hiddenData KBN_YOUBI"><?php echo $dataArrayTtl[14] ?></div>
	<?php
		}
	}
	?>

	</div>

	<?php
	if (!empty($lsts)) {

		foreach($lsts as $obj) {
		
			$cellsArray = $obj->cells;
			$dataArray = $obj->data;
		
	?>
		<div class="tableRow row_dat" style="width:1370px;">
			<div class="tableCell cell_dat" style="width:60px;"><?php echo $cellsArray[0] ?></div>
			<div class="tableCell cell_dat cd" style="width:90px;"><?php echo $cellsArray[1] ?></div>
			<div class="tableCell cell_dat nm" style="width:130px;"><?php echo $cellsArray[2] ?></div>
			<div class="tableCell cell_dat nm" style="width:100px;"><?php echo $cellsArray[3] ?></div>
			<div class="tableCell cell_dat nm" style="width:100px;"><?php echo $cellsArray[4] ?></div>

			<div class="tableCell cell_dat date" style="width:110px;"><?php echo $cellsArray[5] ?></div>
			<div class="tableCell cell_dat date" style="width:110px;"><?php echo $cellsArray[6] ?></div>
			<div class="tableCell cell_dat date" style="width:110px;"><?php echo $cellsArray[7] ?></div>
			<div class="tableCell cell_dat date" style="width:110px;"><?php echo $cellsArray[8] ?></div>
			<div class="tableCell cell_dat date" style="width:110px;"><?php echo $cellsArray[9] ?></div>
			<div class="tableCell cell_dat date" style="width:110px;"><?php echo $cellsArray[10] ?></div>
			<div class="tableCell cell_dat date" style="width:110px;"><?php echo $cellsArray[11] ?></div>



			<div class="hiddenData LINE_COUNT"><?php echo $dataArray[0] ?></div>
			<div class="hiddenData CHANGED"><?php echo $dataArray[1] ?></div>
			<div class="hiddenData BNRI_DAI_CD"><?php echo $dataArray[2] ?></div>
			<div class="hiddenData BNRI_CYU_CD"><?php echo $dataArray[3] ?></div>

			<div class="hiddenData TIME_SYUGYO_ST_1"><?php echo $dataArray[4] ?></div>
			<div class="hiddenData TIME_SYUGYO_ED_1"><?php echo $dataArray[5] ?></div>
			<div class="hiddenData SYUKIN_FLG_1"><?php echo $dataArray[6] ?></div>
			<div class="hiddenData TIME_SYUGYO_ST_2"><?php echo $dataArray[7] ?></div>
			<div class="hiddenData TIME_SYUGYO_ED_2"><?php echo $dataArray[8] ?></div>
			<div class="hiddenData SYUKIN_FLG_2"><?php echo $dataArray[9] ?></div>
			<div class="hiddenData TIME_SYUGYO_ST_3"><?php echo $dataArray[10] ?></div>
			<div class="hiddenData TIME_SYUGYO_ED_3"><?php echo $dataArray[11] ?></div>
			<div class="hiddenData SYUKIN_FLG_3"><?php echo $dataArray[12] ?></div>
			<div class="hiddenData TIME_SYUGYO_ST_4"><?php echo $dataArray[13] ?></div>
			<div class="hiddenData TIME_SYUGYO_ED_4"><?php echo $dataArray[14] ?></div>
			<div class="hiddenData SYUKIN_FLG_4"><?php echo $dataArray[15] ?></div>
			<div class="hiddenData TIME_SYUGYO_ST_5"><?php echo $dataArray[16] ?></div>
			<div class="hiddenData TIME_SYUGYO_ED_5"><?php echo $dataArray[17] ?></div>
			<div class="hiddenData SYUKIN_FLG_5"><?php echo $dataArray[18] ?></div>
			<div class="hiddenData TIME_SYUGYO_ST_6"><?php echo $dataArray[19] ?></div>
			<div class="hiddenData TIME_SYUGYO_ED_6"><?php echo $dataArray[20] ?></div>
			<div class="hiddenData SYUKIN_FLG_6"><?php echo $dataArray[21] ?></div>
			<div class="hiddenData TIME_SYUGYO_ST_7"><?php echo $dataArray[22] ?></div>
			<div class="hiddenData TIME_SYUGYO_ED_7"><?php echo $dataArray[23] ?></div>
			<div class="hiddenData SYUKIN_FLG_7"><?php echo $dataArray[24] ?></div>
			<div class="hiddenData KOUTEI_INP_FLG"><?php echo $dataArray[25] ?></div>

			<div class="hiddenData STOK_SYUGYO_ST_1"><?php echo $dataArray[26] ?></div>
			<div class="hiddenData STOK_SYUGYO_ST_2"><?php echo $dataArray[27] ?></div>
			<div class="hiddenData STOK_SYUGYO_ST_3"><?php echo $dataArray[28] ?></div>
			<div class="hiddenData STOK_SYUGYO_ST_4"><?php echo $dataArray[29] ?></div>
			<div class="hiddenData STOK_SYUGYO_ST_5"><?php echo $dataArray[30] ?></div>
			<div class="hiddenData STOK_SYUGYO_ST_6"><?php echo $dataArray[31] ?></div>
			<div class="hiddenData STOK_SYUGYO_ST_7"><?php echo $dataArray[32] ?></div>

			<div class="hiddenData SEQ_NO"><?php echo $dataArray[33] ?></div>
			<div class="hiddenData COPY_FLG"><?php echo $dataArray[34] ?></div>
			<div class="hiddenData STAFF_NAME"><?php echo $dataArray[35] ?></div>
			
			<div class="hiddenData AUTO_SETUP_1"><?php echo $dataArray[36] ?></div>
			<div class="hiddenData AUTO_SETUP_2"><?php echo $dataArray[37] ?></div>
			<div class="hiddenData AUTO_SETUP_3"><?php echo $dataArray[38] ?></div>
			<div class="hiddenData AUTO_SETUP_4"><?php echo $dataArray[39] ?></div>
			<div class="hiddenData AUTO_SETUP_5"><?php echo $dataArray[40] ?></div>
			<div class="hiddenData AUTO_SETUP_6"><?php echo $dataArray[41] ?></div>
			<div class="hiddenData AUTO_SETUP_7"><?php echo $dataArray[42] ?></div>

		</div>
	<?php
		}
	}
	?>
</div>
<?php } ?>
<div id="timestamp" style="display:none;" ><?php  echo $timestamp; ?></div>
<!--/ メインテーブル-->

<?php if ($mode == "1") { ?>
<br>
<div style="height:40px;width:100%;text-align:right; max-width:1260px;" id="buttons" >
	<input type="button"  value="完了" style="width:120px;height:30px;" id="completeButton" >
</div>
<?php } ?>

</div>
<!--/ #wrapper-->
