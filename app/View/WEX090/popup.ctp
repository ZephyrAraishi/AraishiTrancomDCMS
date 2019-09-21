<div style= "display:block;position:relative;margin-top:20px;">
<input type="hidden" name="pop_type" id="pop_type" value="<?php echo $type ?>">
<input type="hidden" name="pop_kzn_data_no" id="pop_kzn_data_no" value="">
<table class="kaizen_input">
	<colgroup>
		<col width="100px">
		<col width="1000px">
	</colgroup>
	<tr height="30px">
		<td>改善表題</td>
		<td>
			<input type="text" id="pop_kzn_title" style="width:300px" maxlength="15">
		</td>
	</tr>
	<tr height="200px">
		<td>改善内容</td>
		<td>
			<textarea style="width:1080px;height:200px;resize: none;font-size: 10pt;" id="pop_kzn_text"></textarea>
		</td>
	</tr>
	<tr height="30px">
		<td>対象期間</td>
		<td>
			<input type="text" class="calendar han" id="pop_tgt_kikan_st" maxlength="<?php echo ($type == '1' ? '6' : '8')?>">
				&nbsp;&nbsp;～&nbsp;&nbsp;
			<input type="text" class="calendar han" id="pop_tgt_kikan_ed" maxlength="<?php echo ($type == '1' ? '6' : '8')?>">
		</td>
	</tr>
	<tr height="30px">
		<td>工程</td>
		<td>
			<!-- 工程コンボ -->
	    	<div id="kotei_dropdown_menu_div" style="width:25px;float:left;">
	    		<div id="menu_img_0"><img src="/DCMS/img/search.png" style="width:24px;height:24px" onclick="menuDispPopup('0');"></div>
	    		<div id="ul_class_0" class="ul_top" style="display:none;">
	    			<ul style="display:block;" class="ul_first">
	    				<li style="display:block;position:relative;" value= ""
	    							onClick="setMenuProcessPopup('','','','','','','0');">
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
												onClick="setMenuProcessPopup('<?php echo $koteiDaiCd ."', '" .
									 											 $koteiCyuCd ."', '" .
										 										 $koteiSaiCd ."', '" .
											 								     $koteiDaiRyaku ."', '" .
												 								 $koteiCyuRyaku ."', '" .
													 						     $koteiSaiRyaku ?>','0');">
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
				</div>
			</div>
			<input type="text" id="pop_kotei_text" class="calendar han" value="" style="width:220px;height:20px" readonly>
			<input type="hidden" id="pop_kotei_cd" value="" />
			<input type="hidden" id="pop_bnri_dai_cd" value="">
			<input type="hidden" id="pop_bnri_cyu_cd" value="">
			<input type="hidden" id="pop_bnri_sai_cd" value="">
			<input type="hidden" id="pop_bnri_dai_nm" value="">
			<input type="hidden" id="pop_bnri_cyu_nm" value="">
			<input type="hidden" id="pop_bnri_sai_nm" value="">
			<!-- 工程コンボ -->
		</td>
	</tr>
	<tr height="30px">
		<td>改善項目</td>
		<td>
			<select id="pop_kbn_kzn_kmk" style="height:22px">
					<option value=""></option>
				<?php foreach ($komokuList as $key => $value) { ?>
					<option value="<?php echo $key ?>"><?php echo $value ?></option>
				<?php }  ?>
			</select>
		</td>
	</tr>
	<tr height="30px">
		<td>数値目標</td>
		<td>
			<input type="text" id="pop_kzn_value" class="han" maxlength="12">
		</td>
	</tr>
	<tr height="30px">
		<td>改善コスト</td>
		<td>
			<input type="text" id="pop_kzn_cost" class="han" maxlength="12">
		</td>
	</tr>
</table>
</div>
