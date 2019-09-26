<?php
	echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSWEX070_RELOAD','DCMSWEX070_POPUP','DCMSWEX070_FILE_UPLOAD',
					'DCMSWEX070_CALENDAR','DCMSWEX070_BUNRUI'), array('inline'=>false));
        echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSWEX'), false, array('inline'=>false));
?>             
<!-- WRAPPER -->
<div id="wrapper_s">


	<div id="upload" sytle="position:relative;width:800px;">
<?php
		echo $this->Form->create('WEX070Model',
		array('id' => 'uploadForm', 'url' => '/WEX070/fileUpdate', 'type' => 'file',
				'inputDefaults' => array('label' => false,'div' => false, 'hidden' => false)))
?>

		<table class="field">
			<tbody>
			<tr>
				<td>

					<div style="position:relative;width:190px;float:left;margin-top:3px;text-align:left;">作業工程CSVファイル</div>
					<div style="position:relative;width:610px;overflow:hidden;float:left;margin-top:3px">
						<?php echo $this->Form->file('file_name', array('id' => 'refText', 'size' => '150')) ?>
					</div>
					<div style="position:relative;width:60px;float:left;margin-top:30px;margin-top:3px">
						<input type="button" value="参照" style="height:24px;width:60px;margin-left:2px;" id="refButton" >
					</div>
						<?php echo $this->Form->submit('取込', array('id' => false, 'div' => 'btnsubmit')) ?>

				</td>
			</tr>
			</tbody>
		</table>

<?php
		echo $this->Form->end()
?>

	</div>
	

<div class="box" style="position:relative;width:600px;height:90px;margin-top:20px;">

<!-- 検索 -->
	<?php echo $this->Form->create('WEX070Model', array('url' => '/WEX070','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?> 
		<table class="cndt">
			<colgroup width="80px">
			<colgroup width="610px">
			<tbody>
			<!-- １行目 -->
			<tr>
				<td>年月日</td>
				<td class="colBnriCd" style="text-align:left;">
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
				<td>工程</td>
				<td >

				    	<!-- 工程コンボ -->
				    	<div id="kotei_dropdown_menu_div" style="display:block;position:relative;width:25px;float:left;margin-top:10px;text-align:left;">
				    		<div id="menu_img"><img src="/DCMS/img/search.png" style="width:24px;height:24px" onclick="menuDisp();"></div>
				    		<div id="ul_class" style="display:none;">
				    			<ul style="display:block;" class="ul_first">
				    				<li style="display:block;position:relative;" value= ""
				    							onClick="setMenuProcess('','','','','','');">
										<div style="padding-left:5px;width:120px;cursor:pointer;">（選択なし）</div>
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
				<div style="padding-left:5px;width:120px;cursor:default;"
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
				<div style="padding-left:5px;width:120px;"
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
						<div style="padding-left:5px;width:120px;cursor:default;"
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
						<div style="padding-left:5px;width:120px;"
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
								<div style="padding-left:5px;width:120px;cursor:pointer;"
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
			</tr>
			</tbody>
		</table>
		<div style="display:block;position:absolute;height:40px;width:100px;top:80px;left:500px;">
	  		<input value="検索" type="submit";div="btnsubmit">
		</div>
	<?php echo $this->Form->end() ?> 
<!--/ 検索 -->

</div>

<!--/ 区切り線 -->
<div class="line" style="margin-top:30px;margin-bottom:20px;"></div>
<!--/ 区切り線 -->


<!-- 更新ボタン-->
<div style="height:40px;width:100%;text-align:right;max-width:1170px;" id="buttons" >
	<input type="button"  value="更新" style="width:120px;height:30px;" id="updateButton" >
</div>
<!--/ 更新ボタン-->

<!-- メインテーブル-->
<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>
<div class="resizable_s" id="lstTable">
	<div class="tableRowTitle row_ttl_2" style="width:1740px;">
		<div class="tableCellBango cell_ttl_2_1" style="width:60px;" ><?php echo Configure::read('Bango'); ?></div>
		<div class="tableCell cell_ttl_2_1" style="width:90px;" >年月日</div>
		<div class="tableCell cell_ttl_2_1" style="width:100px;" >大分類</div>
		<div class="tableCell cell_ttl_2_1" style="width:100px;" >中分類</div>
		<div class="tableCell cell_ttl_2_1" style="width:100px;" >細分類</div>
		<div class="tableCell cell_ttl_2_1" style="width:60px;" >単位</div>
		<div class="tableCell cell_ttl_2_1" style="width:100px;" >活動目的</div>
		<div class="tableCell cell_ttl_2_1" style="width:90px;" >単価</div>
		<div class="tableCell cell_ttl_2_1" style="width:80px;" >時間</div>
		<div class="tableCell cell_ttl_2_1" style="width:60px;" >人数</div>
		<div class="tableCell cell_ttl_2_1" style="width:80px;" >物量</div>
		<div class="tableCell cell_ttl_2_1" style="width:80px;" >ライン数量</div>
		<div class="tableCell cell_ttl_2_1" style="width:100px;" >ABCコスト</div>
		<div class="tableCell cell_ttl_2_1" style="width:100px;" >売上</div>
		<div class="tableCell cell_ttl_2_1" style="width:100px;" >利益</div>
		<div class="tableCell cell_ttl_2_1" style="width:100px;" >物量生産性</div>
		<div class="tableCell cell_ttl_2_1" style="width:100px;" >ライン生産性</div>
		<div class="tableCell cell_ttl_2_2" style="width:60px;" >データ<br>取得</div>
	</div>

	<?php
	if (!empty($lsts)) {

		foreach($lsts as $obj) {
		
			$bango = $obj->bango;
			$cellsArray = $obj->cells;
			$dataArray = $obj->data;
		
	?>
	<div class="tableRow row_dat" style="width:1740px;">
		<div class="tableCellBango cell_dat bango" style="width:60px;"><?php echo $bango ?></div>
		<div class="tableCell cell_dat date" style="width:90px;"><?php echo $cellsArray[0] ?></div>
		<div class="tableCell cell_dat nm" style="width:100px;"><?php echo $cellsArray[1] ?></div>
		<div class="tableCell cell_dat nm" style="width:100px;"><?php echo $cellsArray[2] ?></div>
		<div class="tableCell cell_dat nm" style="width:100px;"><?php echo $cellsArray[3] ?></div>
		<div class="tableCell cell_dat kbn" style="width:60px;"><?php echo $cellsArray[4] ?></div>
		<div class="tableCell cell_dat nm" style="width:100px;"><?php echo $cellsArray[5] ?></div>
		<div class="tableCell cell_dat kin" style="width:90px;"><?php echo $cellsArray[6] ?></div>
		<div class="tableCell cell_dat cnt" style="width:80px;"><?php echo $cellsArray[7] ?></div>
		<div class="tableCell cell_dat cnt" style="width:60px;"><?php echo $cellsArray[8] ?></div>
		<div class="tableCell cell_dat cnt" style="width:80px;"><?php echo $cellsArray[9] ?></div>
		<div class="tableCell cell_dat cnt" style="width:80px;" ><?php echo $cellsArray[10] ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;"><?php echo $cellsArray[11] ?></div>
		<div class="tableCell cell_dat kin" style="width:100px;"><?php echo $cellsArray[12] ?></div>
		<div class="tableCell cell_dat kin" style="width:100px;"><?php echo $cellsArray[13] ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;" ><?php echo $cellsArray[14] ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;" ><?php echo $cellsArray[15] ?></div>
		<div class="tableCell cell_dat kbn" style="width:60px;"><?php echo $cellsArray[16] ?></div>

		<div class="hiddenData LINE_COUNT"><?php echo $dataArray[0] ?></div>
		<div class="hiddenData CHANGED"><?php echo $dataArray[1] ?></div>
		<div class="hiddenData BNRI_DAI_CD"><?php echo $dataArray[2] ?></div>
		<div class="hiddenData BNRI_CYU_CD"><?php echo $dataArray[3] ?></div>
		<div class="hiddenData BNRI_SAI_CD"><?php echo $dataArray[4] ?></div>
		<div class="hiddenData"><?php echo $dataArray[5] ?></div>
		<div class="hiddenData"><?php echo $dataArray[6] ?></div>
		<div class="hiddenData"><?php echo $dataArray[7] ?></div>
		<div class="hiddenData"><?php echo $dataArray[8] ?></div>
		<div class="hiddenData"><?php echo $dataArray[9] ?></div>
		<div class="hiddenData"><?php echo $dataArray[10] ?></div>
		
	</div>
	<?php
		}
	}
	?>
</div>
<div id="timestamp" style="display:none;" ><?php  echo $timestamp; ?></div>
<!--/ メインテーブル-->

</div>
<!--/ #wrapper-->
