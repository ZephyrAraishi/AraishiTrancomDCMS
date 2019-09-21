<?php
echo $this->Html->script(array('effects','window','protocalendar','base64','DCMSMessage','DCMSCommon','DCMSValidation',
		'DCMSWEX095_CALENDAR','DCMSWEX095_RELOAD','DCMSWEX095_BUNRUI'), array('inline'=>false));
echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSWEX'), false, array('inline'=>false));
?>
<div id="wrapper_s">

<div>

	<?php echo $this->Form->create('WEX095Model', array('url' => '/WEX095','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
	
	<div class="box" style="width:920px;">
		<table class="cndt" style="width:900px">
			<colgroup width="100px">
			<colgroup width="300px">
			<colgroup width="100px">
			<colgroup width="200px">
			<colgroup width="100px">
			<tbody>
			<!-- １行目 -->
			<tr>
				<td>年月日</td>
				<td class="colBnriCd" style="text-align:left;" colspan="4">
				    	<div style="width:100px;float:left;margin-top:10px;text-align:left;">
					    	<input name="start_ymd_ins" id="startYmdInsText" maxlength="8" class="calendar han" type="text" style="width:80px;height:20px">
		    			</div>
				    	<div style="width:50px;float:left;margin-top:15px;text-align:left;">
					　～　
		    			</div>
				    	<div style="width:100px;float:left;margin-top:10px;text-align:left;">
					    	<input name="end_ymd_ins" id="endYmdInsText" maxlength="8" class="calendar han" type="text" style="width:80px;height:20px">
		    			</div>
				</td>
			</tr>
			<!-- ２行目 -->
			<tr>
				<td>就業工程</td>
				<td >
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
				    	<div id="processSelect" style="display:block;position:relative;width:250px;float:left;margin-top:10px;text-align:left;">
							<div style="position:relative;width:180px;float:left;margin-top:0px;text-align:left;">
								<input id="koteiText" class="calendar han" type="text" value=""
									 style="width:220px;height:20px" readonly>
							</div>
            			</div>
            			<!-- /工程コンボ -->
				</td>
				
				<td>スタッフ名</td>
				<td>
					<input name="staff_nm" id="staff_nmText" maxlength="30" type="text" class="zen"  style="width:140px;height:20px">
				</td>
				<td><input value="検索" type="submit";div="btnsubmit"></td>
			</tr>
			</tbody>
		</table>
	</div>
	<?php echo $this->Form->end() ?> 

</div>

<div class="line"></div>

<!-- ページング -->
<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>

<!-- メインテーブル-->
<div class="resizable_s" id="lstTable" style="height:353px">
	<div class="tableRowTitle row_ttl_1" style="width:2110px;">
		<div class="tableCell cell_ttl_1" style="width:60px;" ><?php echo Configure::read('Bango'); ?></div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >スタッフコード</div>
		<div class="tableCell cell_ttl_1" style="width:140px;" >スタッフ名</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >年月日</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >大分類</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >中分類</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >細分類</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >単位</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >単価</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >時間</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >物量</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >ライン数量</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >物量生産性</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >ライン生産性</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >ABCコスト</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >必須</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >業務</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >価値</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >専門</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >タイプ</div>
	</div>
<?php if (!empty($staffData)) { ?>
	<?php 
		$count = 0;
		foreach($staffData as $data) { 
			$count++;
	?>
	<div class="tableRow row_dat" style="width:2110px;" id="<?php echo $data["STAFF_CD"] ?>">
		<div class="hiddenData"><?php echo $data["STAFF_CD"] ?></div>
		<div class="tableCell cell_dat" style="width:60px;" ><?php echo ($index + $count); ?></div>
		<div class="tableCell cell_dat cd" style="width:100px;" ><?php echo $data["STAFF_CD"] ?></div>
		<div class="tableCell cell_dat nm" style="width:140px;" ><?php echo $data["STAFF_NM"] ?></div>
		<div class="tableCell cell_dat date" style="width:90px;" ><?php echo substr($data["YMD"], 0, 4)."/".substr($data["YMD"], 4, 2)."/".substr($data["YMD"], 6, 2) ?></div>
		<div class="tableCell cell_dat nm" style="width:100px;" ><?php echo $data["BNRI_DAI_RYAKU"] ?></div>
		<div class="tableCell cell_dat nm" style="width:100px;" ><?php echo $data["BNRI_CYU_RYAKU"] ?></div>
		<div class="tableCell cell_dat nm" style="width:100px;" ><?php echo $data["BNRI_SAI_RYAKU"] ?></div>
		<div class="tableCell cell_dat kbn" style="width:90px;" ><?php echo $data["KBN_TANI_NM"] ?></div>
		<div class="tableCell cell_dat kin" style="width:90px;" ><?php echo number_format($data["TANKA"]) ?></div>
		<div class="tableCell cell_dat cnt" style="width:90px;" ><?php echo number_format($data["JIKAN"], 2) ?></div>
		<div class="tableCell cell_dat cnt" style="width:90px;" ><?php echo $data["KBN_URIAGE"] == '01' ? $data["BUTURYO"] : number_format($data["BUTURYO"]) ?></div>
		<div class="tableCell cell_dat cnt" style="width:90px;" ><?php echo $data["KBN_URIAGE"] == '01' ? $data["SURYO_LINE"] : number_format($data["SURYO_LINE"]) ?></div>
		<div class="tableCell cell_dat cnt" style="width:90px;" ><?php echo $data["SEISANSEI_TIME_BUTURYO"] ?></div>
		<div class="tableCell cell_dat cnt" style="width:90px;" ><?php echo $data["SEISANSEI_TIME_LINE"] ?></div>
		<div class="tableCell cell_dat cnt" style="width:90px;" ><?php echo number_format($data["ABC_COST"]) ?></div>
		<div class="tableCell cell_dat kbn" style="width:100px;" ><?php echo $data["KBN_HISSU_WK_NM"] ?></div>
		<div class="tableCell cell_dat kbn" style="width:100px;" ><?php echo $data["KBN_GYOMU_NM"] ?></div>
		<div class="tableCell cell_dat kbn" style="width:100px;" ><?php echo $data["KBN_FUKAKACHI_NM"] ?></div>
		<div class="tableCell cell_dat kbn" style="width:100px;" ><?php echo $data["KBN_SENMON_NM"] ?></div>
		<div class="tableCell cell_dat kbn" style="width:100px;" ><?php echo $data["KBN_KTD_TYPE_NM"] ?></div>
	</div>
	<?php } ?>
<?php } ?>
</div>

</div>

</body>