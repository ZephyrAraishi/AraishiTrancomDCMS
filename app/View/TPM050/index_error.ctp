<?php
			echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSTPM050_RELOAD','DCMSTPM050_POPUP', 'DCMSTPM050_CALENDAR',
					 'DCMSTPM050_BUNRUI', 'DCMSTPM050_UPDATE','DCMSTPM050_CSV'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSTPM'), false, array('inline'=>false));
?>
<!-- WRAPPER -->
<div id="wrapper_s">

<!-- 検索 -->
<div class="box" style="width:700px;">
	<?php echo $this->Form->create('TPM050Model', array('url' => '/TPM050','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
		<table class="cndt" style="width:700px">
			<tbody>
			<colgroup>
				<col width="100px">
				<col width="100px">
				<col width="100px">
				<col width="100px">
				<col width="100px">
				<col width="200px">
			</colgroup>
			<tr>
				<td>年月日</td>
				<td class="colBnriCd" style="text-align:left;" colspan="1">
						<?php echo $this->Form->input('ymd_from',
												array('maxlength' => '40',
													'label' => false,
													'error' => false,
													'style' => 'width:100px',
													'name' => 'ymd_from',
													'id' => 'ymd_from',
													'value' => $ymd_from,
													'class' => 'zen')) ?>
				<td style="display:table-cell;width:10px;padding-right:2px;padding-left:2px;text-align:center;font-size:13px;color:#222">～</td>
				<td ~ class="colBnriCd" style="text-align:left;" colspan="1">
						<?php echo $this->Form->input('ymd_to',
												array('maxlength' => '40',
													'label' => false,
													'error' => false,
													'style' => 'width:100px',
													'name' => 'ymd_to',
													'value' => $ymd_to,
													'id' => 'ymd_to',
													'class' => 'zen')) ?>
				</td>
			</tr>
			<form>
				<td>大中細分類</td>
				<td class="colBnriNm"　 colspan="4">
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
        			<td><input value="検索" type="submit";div="btnsubmit"></td>
			</tr>
			</tbody>
		</table>
	<?php echo $this->Form->end() ?>
</div>
<!--/ 検索 -->

<!--/ 区切り線 -->
<div class="line"></div>
<!--/ 区切り線 -->

<!-- 更新ボタン-->
<div style="height:40px;width:100%;text-align:right;max-width:1180px;" id="buttons" >
	<input type="button"  value="更新" style="width:120px;height:30px;" id="updateButton" >
	<input type="button"  value="スタッフ進捗CSV出力" style="width:180px;height:30px;" id="csvButton" >
	<input type="button"  value="スタッフ実績CSV出力" style="width:180px;height:30px;" id="csvButton2" >
</div>
<!--/ 更新ボタン-->

<!-- メインテーブル-->
	<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>
<div class="resizable_s" id="lstTable">
	<div class="tableRowTitle row_ttl_1" style="width:1420px;">
		<div class="tableCell cell_ttl_1" style="width:100px;" >年月日</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >大分類</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >中分類</div>
		<div class="tableCell cell_ttl_1" style="width:200px;" >細分類</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >稼動人数</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >累計人数</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >累計時間(h)</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >累計期間(m)</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >累計物量</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >物量生産性</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >予測物量</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >予測終了時間</div>
	</div>
<?php
				if (!empty($lsts)) {

					foreach($lsts as $obj) {
						$cellsArray = $obj->cells;
						$dataArray = $obj->data;

?>
	<div class="tableRow row_dat" style="width:1420px;">
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $cellsArray[0] ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $cellsArray[1] ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $cellsArray[2] ?></div>
		<div class="tableCell cell_dat" style="width:200px;" ><?php echo $cellsArray[3] ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $cellsArray[4] ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $cellsArray[5] ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $cellsArray[6] ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $cellsArray[7] ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $cellsArray[8] ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $cellsArray[9] ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $cellsArray[10] ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $cellsArray[11] ?></div>

			<div class="hiddenData"><?php echo $dataArray[0] ?></div>
			<div class="hiddenData"><?php echo $dataArray[1] ?></div>
			<div class="hiddenData"><?php echo $dataArray[2] ?></div>
			<div class="hiddenData"><?php echo $dataArray[3] ?></div>
	</div>
<?php
					}
				}
?>
</div>
<div id="timestamp" style="display:none;" ><?php  echo $timestamp; ?></div>
<div id="YMD_FROM_H" style="display:none;" ><?php  echo $ymd_from; ?></div>
<div id="YMD_TO_H" style="display:none;" ><?php  echo $ymd_to; ?></div>
<div id="KOTEI" style="display:none;" ><?php  if(!empty($kotei)){ echo $kotei; }?></div>
<!--/ メインテーブル-->

</div>
<!--/ #wrapper-->
