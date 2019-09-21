<div style= "display:block;position:relative;height:180px;width:100%;margin-top:20px;text-align: center;">

	<div style="display:none;position:absolute;height:30px;top:10px;left:40px;"><?php echo Configure::read('Bango'); ?>：</div>
	<div style="display:none;position:absolute;height:30px;top:10px;left:130px;" id="bango" ></div>

	<div style="display:block;position:absolute;height:30px;top:10px;left:40px;">工程：</div>

<div style="display:block;position:absolute;height:10px;top:10px;left:130px;" >


				    	<!-- 工程コンボ -->
	<div id="popupKoteiArea">

<?php
	for ($i =0; $i < 1 ;$i++) {
?>

		<div class="koteiPopup" >

			<!-- 工程コンボ -->
	    	<div class="kotei_dropdown_menu_div" 
	    		style="display:block;position:relative;width:25px;float:left;text-align:left;">
	    		<div id="menu_img_<?php echo $i; ?>" class="img_top">
	    			<img src="/DCMS/img/search.png" style="width:24px;height:24px" onclick="menuDispPopup('<?php echo $i ?>');">
	    		</div>
	    		<div id="ul_class_<?php echo $i; ?>" class="ul_top" style="display:none;">
	    			<ul style="display:block;" class="ul_first">
	    				<li style="display:block;position:relative;" value= ""
	    							onClick="setMenuProcessPopup('','','','','','','<?php echo $i ?>');">
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
				<div style="height:30px;padding-left:5px;width:120px;cursor:default;"
					onClick="setMenuProcessPopup('<?php echo $koteiDaiCd ."', '" . 
										    '' ."', '" . 
										    '' ."', '" . 
										    $koteiDaiRyaku ."', '" . 
										    '' ."', '" . 
										    '' ."', '" . 
										    $i ?>');"
					>
					<?php echo $koteiDaiRyaku ?>
				</div>
<?php
		} else {
?>
			<li>
				<div style="height:30px;padding-left:5px;width:120px;"
					onClick="setMenuProcessPopup('<?php echo $koteiDaiCd ."', '" . 
										    '' ."', '" . 
										    '' ."', '" . 
										    $koteiDaiRyaku ."', '" . 
										    '' ."', '" . 
										    '' ."', '" . 
										    $i ?>');"
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
						onClick="setMenuProcessPopup('<?php echo $koteiDaiCd ."', '" . 
											    $koteiCyuCd ."', '" . 
											    '' ."', '" . 
											    $koteiDaiRyaku ."', '" . 
											    $koteiCyuRyaku ."', '" . 
											    '' ."', '" . 
											    $i ?>');"
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
					<input type="hidden" name="kotei" class="koteiCd" value="">
				</div>
			</div>
	    	<div class="processSelect" 
	    	style="display:block;position:relative;width:300px;float:left;text-align:left;margin-bottom:10px;">
				<div style="position:relative;width:300px;float:left;margin-top:0px;text-align:left;">
					<input class="koteiDaiComboPopup" type="text" value=""  
						 style="width:123px;height:20px" readonly>
					<input class="koteiCyuComboPopup" type="text" value=""  
						 style="width:123px;height:20px" readonly>
				</div>
			</div>
			<!-- /工程コンボ -->

		</div>

<?php
	}
?>

	</div>
            			<!-- /工程コンボ -->

</div>


	<div style="display:block;position:absolute;height:30px;top:50px;left:40px;">数量：</div>
	<div style="display:block;position:absolute;height:30px;top:50px;left:130px;">
		<input type="text" id="suryo" style="width:110px;" maxlength="5" class="num han">
	</div>

	<div style="display:block;position:absolute;height:30px;top:90px;left:40px;">費用：</div>
	<div style="display:block;position:absolute;height:30px;top:90px;left:130px;">
		<input type="text" id="hiyou" style="width:120px;" maxlength="15" class="num han">
	</div>

	<div style="display:block;position:absolute;height:30px;top:130px;left:40px;">詳細：</div>
	<div style="display:block;position:absolute;height:30px;top:130px;left:130px;">
		<input type="text" id="exp" style="width:300px;" maxlength="20" class="zen">
	</div>
	</div>
</div>
