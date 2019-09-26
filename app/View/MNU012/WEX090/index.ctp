<?php
echo $this->Html->script(array('effects','window','protocalendar','base64','DCMSMessage','DCMSCommon','DCMSValidation',
		'DCMSWEX090_CALENDAR','DCMSWEX090_UPDATE','DCMSWEX090_RELOAD','DCMSWEX090_POPUP','DCMSWEX090_BUNRUI'), array('inline'=>false));
echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSWEX'), false, array('inline'=>false));
?>
<div id="wrapper_s">

<div align="right" style="height:35px;">
	<ul class="toggle-list">
		<li><label id="monthlyButton" style="width:100px;text-align:center" <?php echo ($type == '1' ? "class=\"selected\"" : "") ?>>月別</label></li>
		<li><label id="dailyButton" style="width:100px;text-align:center" <?php echo ($type == '2' ? "class=\"selected\"" : "") ?>>日別</label></li>
	</ul>
	
</div>

<div align="right" style="height:35px;">
	<input type="button" id="jissekiButton" value="実績参照">
</div>

<div id="timestamp" style="display:none;" ><?php echo $timestamp ?></div>

<div class="kaizen_box" style="width:1130px;">
<?php echo $this->Form->create('WEX090Model', array('url' => '/WEX090','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
	<input type="hidden" name="type" id="type" value="<?php echo $type ?>">
	<input type="hidden" name="maxCnt" id="maxCnt" value="<?php echo $maxCnt ?>">
	<input type="hidden" name="viewFrom" id="viewFrom" value="<?php echo $viewFrom ?>">
	<input type="hidden" name="viewTo" id="viewTo" value="<?php echo $viewTo ?>">
	<table class="cndt" width="1110px">
		<colgroup>
			<col width="120px">
			<col width="300px">
			<col width="100px">
			<col width="330px">
			<col width="120px">
			<col width="100px">
			<col width="150px">
		</colgroup>
		<tr>
			<td>対象期間</td>
			<td colspan="6">
				<div id="kikan">
				<input id="from" name="from" maxlength="<?php echo $type == '1' ? '6' : '8' ?>" type="text" class="calendar han" style="width:80px;height:22px" value="<?php echo $from ?>">
				&nbsp;&nbsp;～&nbsp;&nbsp;
				<input id="to" name="to" maxlength="<?php echo $type == '1' ? '6' : '8' ?>" type="text" class="calendar han" style="width:80px;height:22px" value="<?php echo $to ?>">
				</div>
			</td>
		</tr>
		<tr>
			<td>改善表題</td>
			<td><input type="text" name="kznTitle" id="kznTitle" class="zen" style="width:250px;height:22px" maxlength="15" value="<?php echo $kznTitle ?>"></td>
			<td>工程</td>
			<td>
				    	<!-- 工程コンボ -->
				    	<div id="kotei_dropdown_menu_div" style="display:block;position:relative;width:25px;float:left;margin-top:2px;text-align:left;">
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
				<div style="height:30px;padding-left:5px;width:120px;cursor:default;"
					onClick="setMenuProcess('<?php echo $koteiDaiCd ."', '" . 
									    '' ."', '" . 
									    '' ."', '" . 
									    $koteiDaiRyaku ."', '" . 
									    '' ."', '" . 
									    '' ?>');"
					>
					<?php echo $koteiDaiRyaku ?>
				</div>
<?php
		} else {
?>

			<li value="<?php echo $koteiDaiCd ?>">
				<div style="height:30px;padding-left:5px;width:120px;"
					onClick="setMenuProcess('<?php echo $koteiDaiCd ."', '" . 
									    '' ."', '" . 
									    '' ."', '" . 
									    $koteiDaiRyaku ."', '" . 
									    '' ."', '" . 
									    '' ?>');"
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
<?php
				} else {
?>
					<li value="<?php echo $koteiDaiCd . "_" . $koteiCyuCd ?>" >
						<div style="height:30px;padding-left:5px;width:120px;"
							onClick="setMenuProcess('<?php echo $koteiDaiCd ."', '" . 
											    $koteiCyuCd ."', '" . 
											    '' ."', '" . 
											    $koteiDaiRyaku ."', '" . 
											    $koteiCyuRyaku ."', '" . 
											    '' ?>');"
							>
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
				    	<div id="processSelect" style="display:block;position:relative;width:250px;float:left;margin-top:2px;text-align:left;">
							<div style="position:relative;width:180px;float:left;margin-top:0px;text-align:left;">
								<input id="koteiText" class="calendar han" type="text" value=""
									 style="width:220px;height:20px" readonly>
							</div>
            			</div>
            			<!-- /工程コンボ -->
			</td>
			<td>改善項目</td>
			<td>
				<select name="kznKmk" id="kznKmk" style="width:100px;height:24px">
					<option value=""></option>
					<?php foreach ($komokuList as $key => $value) { ?>
						<?php $sd = $kznKmk == $key ? "selected" : "";  ?>
						<option value="<?php echo $key ?>" <?php echo $sd ?>><?php echo $value ?></option>
					<?php }  ?>
				</select>
			</td>
			<td align="right"><input type="submit" value="検索"></td>
		</tr>
	</table>
<?php echo $this->Form->end() ?>
</div>


<div class="line"></div>

<div class="resizable" style="height:40px;overflow-y:hidden;">
	<table width="100%">
	<tr>
		<td align="right" valign="bottom">
			<input type="button" value="追加" class="btn" id="addButton">
			<input type="button" value="更新" class="btn" id="updateButton">
		</td>
	</tr>
	</table>
</div>

<!-- ページング -->
<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>

<!-- メインテーブル -->
<div class="resizable_s" id="lstTable">
	<div class="tableRowTitle row_ttl_1" style="width:1160px">
		<div class="tableCellBango cell_ttl_1" style="width:60px;" ><?php echo Configure::read('Bango'); ?></div>
		<div class="tableCell cell_ttl_1" style="width:200px;" >改善表題</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >対象期間（自）</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >対象期間（至）</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >大分類</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >中分類</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >細分類</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >改善項目</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >数値目標</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >改善コスト</div>
	</div>
<?php if (!empty($kaizenData)) { ?>
	<?php for ($i = 0; $i < count($kaizenData); $i++) { ?>
		<?php 
			$no = (($pageID - 1) * $rowMaxCnt) + ($i + 1); 
			$data = $kaizenData[$i];
			$kznValue = '0';
			if ($data["KBN_KZN_KMK"] == '02') {
				$kznValue = number_format($data["KZN_VALUE"], 2);
			} else if ($data["KBN_KZN_KMK"] == '05') {
				$kznValue = number_format($data["KZN_VALUE"], 1);
			} else {
				$kznValue = number_format($data["KZN_VALUE"]);
			}
		?>
	<div class="tableRow row_dat" style="width:1160px;<?php echo $data["KBN_SYURYO"] == '1' ? "background:#eeeeee;" : ""; ?>">
	
		<div class="hiddenData CHANGED"><?php echo isset($data["CHANGED"]) ? $data["CHANGED"] : '0' ?></div>
		<div class="hiddenData KZN_DATA_NO"><?php echo $data["KZN_DATA_NO"] ?></div>
		<div class="hiddenData TGT_KIKAN_ST"><?php echo $data["TGT_KIKAN_ST"] ?></div>
		<div class="hiddenData TGT_KIKAN_ED"><?php echo $data["TGT_KIKAN_ED"] ?></div>
		<div class="hiddenData BNRI_DAI_CD"><?php echo $data["BNRI_DAI_CD"] ?></div>
		<div class="hiddenData BNRI_CYU_CD"><?php echo $data["BNRI_CYU_CD"] ?></div>
		<div class="hiddenData BNRI_SAI_CD"><?php echo $data["BNRI_SAI_CD"] ?></div>
		<div class="hiddenData KBN_KZN_KMK"><?php echo $data["KBN_KZN_KMK"] ?></div>
		<div class="hiddenData KZN_TITLE"><?php echo $data["KZN_TITLE"] ?></div>
		<div class="hiddenData KZN_TEXT"><?php echo $data["KZN_TEXT"] ?></div>
		<div class="hiddenData KZN_VALUE"><?php echo str_replace(",", "", $kznValue); ?></div>
		<div class="hiddenData KZN_COST"><?php echo $data["KZN_COST"] ?></div>
		<div class="hiddenData KBN_SYURYO"><?php echo $data["KBN_SYURYO"] ?></div>
		
			
		<div class="tableCell cell_dat" style="width:60px;" ><?php echo isset($data["ROW"]) ? $data["ROW"] : $no ?></div>
		<div class="tableCell cell_dat nm" style="width:200px;" id="hrefTitle">
			<?php if (!empty($data["KZN_DATA_NO"])) { ?>
				<a href="" onclick="openSui('<?php echo $data["KZN_DATA_NO"] ?>')" class="hrefKznTitle"><?php echo $data["KZN_TITLE"] ?></a>
			<?php } else { ?>
				<?php echo $data["KZN_TITLE"] ?>
			<?php } ?>
		</div>
		<div class="tableCell cell_dat date" style="width:100px;" >
			<?php if ($type == "1") { ?>
				<?php echo substr($data["TGT_KIKAN_ST"], 0, 4)."/".substr($data["TGT_KIKAN_ST"], 4, 2) ?>
			<?php } else { ?>
				<?php echo substr($data["TGT_KIKAN_ST"], 0, 4)."/".substr($data["TGT_KIKAN_ST"], 4, 2)."/".substr($data["TGT_KIKAN_ST"], 6, 2) ?>
			<?php } ?>
			</div>
		<div class="tableCell cell_dat date" style="width:100px;" >
			<?php if ($type == "1") { ?>
				<?php echo substr($data["TGT_KIKAN_ED"], 0, 4)."/".substr($data["TGT_KIKAN_ED"], 4, 2) ?>
			<?php } else { ?>
				<?php echo substr($data["TGT_KIKAN_ED"], 0, 4)."/".substr($data["TGT_KIKAN_ED"], 4, 2)."/".substr($data["TGT_KIKAN_ED"], 6, 2) ?>
			<?php } ?>
			</div>
		<div class="tableCell cell_dat nm" style="width:100px;" ><?php echo $data["BNRI_DAI_NM"] ?></div>
		<div class="tableCell cell_dat nm" style="width:100px;" ><?php echo $data["BNRI_CYU_NM"] ?></div>
		<div class="tableCell cell_dat nm" style="width:100px;" ><?php echo $data["BNRI_SAI_NM"] ?></div>
		<div class="tableCell cell_dat kbn" style="width:100px;" ><?php echo $data["KZN_KMK_VALUE"] ?></div>
		<div class="tableCell cell_dat kin" style="width:100px;" ><?php echo $kznValue ?></div>
		<div class="tableCell cell_dat kin" style="width:100px;" ><?php echo number_format($data["KZN_COST"]) ?></div>
	</div>
	<?php } ?>
<?php } ?>
</div>

</div>