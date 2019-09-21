<div style= "display:block;position:relative;height:500px;width:100%;margin-top:20px;text-align: center;">
	<div style="display:block;position:relative;width:1000px;margin-left:auto;margin-right:auto;height:90px;">

		<div style="display:none;position:absolute;height:0px;top:0px;left:330px;"  id="bango" ></div>

		<div style="display:block;position:absolute;height:30px;top:5px;left:250px;text-align:right;">スタッフ：</div>
		<div style="display:block;position:absolute;height:30px;top:0px;left:330px;text-align:right;" ><input type="button" value="検索" onClick="clickStaffSearchPopUp()"  style="width: 80px;height:24px;"></div>
		<div style="display:block;position:absolute;height:30px;top:5px;left:430px;"  id="staff_cd" ></div>
		<div style="display:block;position:absolute;height:30px;top:5px;left:520px;"  id="staff_nm"></div>

		<div style="display:block;position:absolute;height:90px;top:40px;left:250px;">就業日時：</div>
 		<div style="display:block;position:absolute;height:90px;top:40px;left:330px;" id="ymd_syugyo_area">

 		</div>
		<div style="display:block;position:absolute;height:90px;top:40px;left:430px;" >
			<input type="text" id="dt_syugyo_st" style="width:40px;" class="han" maxlength="4">
		</div>
		<div style="display:block;position:absolute;height:30px;top:40px;left:480px;">～</div>
		<div style="display:block;position:absolute;height:30px;top:40px;left:500px;">
			<input type="text" id="dt_syugyo_ed" style="width:40px;" class="han" maxlength="4">
		</div>
	</div>
	<div class="line" style="border-bottom:1px dashed #777777;" ></div>
	<div style="width:800px;height:400px;overflow: scroll;margin-left:auto;margin-right:auto;margin-top:10px;margin-bottom:10px;padding:0px;" id="popupKoteiArea">
<?php
	for ($i =0; $i < 40 ;$i++) {
?>
		<div style="display:block;position:relative;width:1670px;height:35px;margin-right:10px;margin-bottom:15px;" class="koteiPopup" >
			<div style="display:block;position:relative;width:100px;height:25px;float:left">工程名<?php echo mb_convert_kana($i + 1); ?></div>
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
				<div style="position:absolute;width:20px;height:10px;right:0px;z-index:0;" >&raquo;</div>
				<div style="height:30px;padding-left:5px;width:120px;cursor:default;">
					<?php echo $koteiDaiRyaku ?>
				</div>
<?php
		} else {
?>

			<li>
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
												 						     $koteiSaiRyaku ."', '" .
												 						     $i ?>');">
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
					<input type="hidden" name="kotei" class="koteiCd" value="">
				</div>
			</div>
	    	<div class="processSelect"
	    	style="display:block;position:relative;width:230px;float:left;text-align:left;margin-bottom:10px;">
				<div style="position:relative;width:200px;float:left;margin-top:0px;text-align:left;">
					<input class="koteiComboPopup" type="text" value=""
						 style="width:200px;height:20px" readonly>
				</div>
			</div>
			<!-- /工程コンボ -->
    		<div style="display:block;position:relative;float:left;width:60px;">
    			<input type="text" size="5" maxlength="4" class= "dt_kotei_st han" >
    		</div>
	    	<div style="display:block;position:relative;float:left;width:30px;">～</div>
	    	<div style="display:block;position:relative;float:left;width:60px;">
	    		<input type="text" size="5" maxlength="4" class= "dt_kotei_ed han" >
	    	</div>
	    	<div style="display:block;position:relative;float:left;width:90px;margin-left:10px;">
            	    		<input type="text" size="10" maxlength="50" class= "kaisi_kyori" >
            	    	</div>
            	    	<div style="display:block;position:relative;float:left;width:90px;margin-left:10px;">
            	    		<input type="text" size="10" maxlength="50" class= "syuryo_kyori" >
            	    	</div>
            	    	<div style="display:block;position:relative;float:left;width:120px;margin-left:10px;">
            	    		<select style="width:120px;" class="syaryo_cd">
            				<option value="" ></option>
            <?php

            			foreach ($syaryoList as $syaryo) {

            				$syaryoCd = (string)$syaryo['MEI_CD'];
            				$syaryoNm = (string)$syaryo['MEI_1'];
            				echo '<option value="' . $syaryoCd . '">' . $syaryoNm . '</option>';
            			}
            ?>
            			</select>
            	    	</div>
            	    	<div style="display:block;position:relative;float:left;width:120px;margin-left:10px;">
            	    		<select style="width:120px;" class="area_cd">
            				<option value="" ></option>
            <?php

            			foreach ($areaList as $area) {

            				$areaCd = (string)$area['MEI_CD'];
            				$areaNm = (string)$area['MEI_1'];
            				echo '<option value="' . $areaCd . '">' . $areaNm . '</option>';
            			}
            ?>
            			</select>
            </div>
	    	<div style="display:block;position:relative;float:left;width:220px;margin-left:20px;">
	    		<input type="text" size="30" maxlength="50" class= "biko1" >
	    	</div>
	    	<div style="display:block;position:relative;float:left;width:220px;margin-left:10px;">
	    		<input type="text" size="30" maxlength="50" class= "biko2" >
	    	</div>
	    	<div style="display:block;position:relative;float:left;width:220px;margin-left:10px;">
	    		<input type="text" size="30" maxlength="50" class= "biko3" >
	    	</div>

		</div>
<?php
	}
?>
	</div>
</div>
