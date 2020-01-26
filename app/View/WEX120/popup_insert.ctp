<div style= "display:block;position:relative;height:500px;width:100%;margin-top:20px;text-align: center;">
	<div style="display:block;position:relative;width:1000px;margin-left:auto;margin-right:auto;height:90px;">

		<div style="display:none;position:absolute;height:0px;top:0px;left:330px;"  id="bango" ></div>

		<div style="display:block;position:absolute;height:90px;top:5px;left:250px;">発生日付：</div>
 		<div style="display:block;position:absolute;height:90px;top:0px;left:330px;" id="ymd_syugyo_area">

 		</div>

		<div style="display:block;position:absolute;height:90px;top:130px;left:250px;">品質管理区分：</div>
    	<div style="display:block;position:absolute;height:90px;top:130px;left:3300px;">
            	    		<select style="width:120px;" class="area_cd">
            				<option value="" ></option>
            <?php

            			foreach ($hinsitukanriList as $hinsitukanri) {

            				$hinsitukanriCd = (string)$hinsitukanri['MEI_CD'];
            				$hinsitukanriNm = (string)$hinsitukanri['MEI_1'];
            				echo '<option value="' . $hinsitukanriCd . '">' . $hinsitukanriNm . '</option>';
            			}
            ?>
            			</select>
        </div>

		<div style="display:block;position:absolute;height:90px;top:130px;left:250px;">品質内容区分：</div>
    	<div style="display:block;position:absolute;height:90px;top:130px;left:500px;">
            	    		<select style="width:120px;" class="area_cd">
            				<option value="" ></option>
            <?php

            			foreach ($hinsitunaiyoList as $hinsitunaiyo) {

            				$hinsitunaiyoCd = (string)$hinsitunaiyo['MEI_CD'];
            				$hinsitunaiyoNm = (string)$hinsitunaiyo['MEI_1'];
            				echo '<option value="' . $hinsitunaiyoCd . '">' . $hinsitunaiyoNm . '</option>';
            			}
            ?>
            			</select>
        </div>

		<!-- 工程コンボ -->
		<div style="display:block;position:absolute;height:90px;top:130px;left:250px;">工程名：</div>
    	<div style="display:block;position:absolute;height:90px;top:130px;left:500px;">
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
        </div>

		<div style="display:block;position:absolute;height:30px;top:5px;left:250px;text-align:right;">発生スタッフ：</div>
		<div style="display:block;position:absolute;height:30px;top:0px;left:330px;text-align:right;" ><input type="button" value="検索" onClick="clickStaffSearchPopUp()"  style="width: 80px;height:24px;"></div>
		<div style="display:block;position:absolute;height:30px;top:5px;left:430px;"  id="staff_cd" ></div>
		<div style="display:block;position:absolute;height:30px;top:5px;left:520px;"  id="staff_nm"></div>

		<div style="display:block;position:absolute;height:30px;top:5px;left:250px;text-align:right;">対応スタッフ：</div>
		<div style="display:block;position:absolute;height:30px;top:0px;left:330px;text-align:right;" ><input type="button" value="検索" onClick="clickStaffSearchPopUp()"  style="width: 80px;height:24px;"></div>
		<div style="display:block;position:absolute;height:30px;top:5px;left:430px;"  id="staff_cd" ></div>
		<div style="display:block;position:absolute;height:30px;top:5px;left:520px;"  id="staff_nm"></div>

		<div style="display:block;position:absolute;height:30px;top:5px;left:250px;text-align:right;">対応金額：</div>
		<div style="display:block;position:absolute;height:90px;top:40px;left:430px;" >
			<input type="text" id="dt_syugyo_st" style="width:40px;" class="han" maxlength="4">
		</div>
	</div>
</div>
