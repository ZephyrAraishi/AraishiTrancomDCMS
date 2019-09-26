<style type="text/css">

table.dialogTable {
	text-align:left;
	margin:20px 30px 0;
}

table.dialogTable tr {
	height : 40px;
}

table.dialogTable select, table.dialogTable input {
	height : 20px;
}

table.dialogTable span.colTitle {
	margin-right:10px;
}

</style>
<table class="dialogTable">
	<!-- １行目 -->
	<tr>
		<td style="padding-right : 50px;">発生日付</td>
		<td>
			<input name="ymd_hassei" id="hassei_date" maxlength="8" class="calendar han" type="text">
			<span class="colTitle" style="margin-left:40px;">品質内容</span>
			<select id="kbn_hinshitsu_naiyo">
				<option value=""></option>
				<?php
				foreach($kbnHinshitsu as $key => $value) :
				?>
				<option value="<?php echo $key ?>"><?php echo $value?></option>
				<?php
				endforeach;
				?>
			</select>
		</td>
	</tr>

	<!-- ２行目 -->
	<tr>
		<td>担当業務 工程</td>
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
			<input id="koteiText" class="calendar han" type="text" value="" style="width:220px;height:20px" readonly>
			<input type="hidden" name="kotei" id="koteiCd" value="" />
		</td>
	</tr>

	<!-- ３行目 -->
	<tr>
	   <td>集計管理No.</td>
	   <td>
			<?php echo $this->Form->input('s_kanri_no',
									array('style' => 'width:100px;',
										'maxlength' => '10',
										'error' => false,
										'label' => false,
										'div' => false,
										'hidden' => false,
										'class' => 'han'))
			?>
			<span class="colTitle" style="margin-left:80px;">対応者</span>
			<?php echo $this->Form->input('staff_nm',
									array('style' => 'width:130px;',
										'maxlength' => '10',
										'error' => false,
										'label' => false,
										'div' => false,
										'hidden' => false))
			?>
		</td>
	</tr>

	<!-- ４行目 -->
	<tr>
	   <td >対応金額</td>
	   <td>
			<?php echo $this->Form->input('t_kin',
									array('style' => 'width:100px;',
										'maxlength' => '12',
										'error' => false,
										'label' => false,
										'div' => false,
										'hidden' => false,
										'class' => 'han'))
			?>
		</td>
	</tr>

	<!-- ５行目 -->
	<tr>
	   <td style="padding : 30px 0">対応方法</td>
	   <td colspan="0"><?php echo $this->Form->textarea("t_hoho", array("cols"=>50, "rows" => 2, 'maxlength' => '500', "style" => "resize: none;")); ?></td>
	</tr>

</table>
