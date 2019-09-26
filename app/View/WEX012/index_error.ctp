<?php
			echo $this->Html->script(array('effects','window','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSWEX040_RELOAD','DCMSWEX040_POPUP',
					'DCMSWEX040_ADDROW','DCMSWEX040_BUNRUI'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','DCMSWEX'), false, array('inline'=>false));
?>             
<!-- WRAPPER -->
<div id="wrapper_s">

<!-- 検索 -->
<div class="box" style="width:720px">
	<?php echo $this->Form->create('WEX040Model', array('url' => '/WEX040','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?> 
		<table class="cndt" style="width:720px">
			<tbody>
			<colgroup>
				<col width="120px">
				<col width="500px">
				<col width="100px">
			</colgroup>
			<tr>
				<td>大分類</td>
				<td colspan="2">

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

?>

			<li style="display:block;position:relative;" value="<?php echo $koteiDaiCd ?>" >
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
			</li>
<?php
	}
?>
								</ul>
								<input type="hidden" name="kotei" id="koteiCd" value="">
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
			</tr>
			<tr>
				<td>中分類コード</td>
				<td class="colBnriCd" style="text-align:left;" colspan="2">
						<?php echo $this->Form->input('bnri_cyu_cd', 
												array('maxlength' => '3', 
													'label' => false, 
													'error' => false, 
													'style' => 'width:100px', 
													'class' => 'han')) ?>
				</td>
			</tr>
			<tr>
				<td>中分類名称</td>
				<td class="colBnriNm" style="text-align:left;" colspan="2">
						<?php echo $this->Form->input('bnri_cyu_nm', 
												array('maxlength' => '20', 
													'label' => false, 
													'error' => false, 
													'style' => 'width:300px', 
													'class' => 'zen')) ?>
				</td>
			</tr>
			<tr>
				<td>中分類説明</td>
				<td class="colBnriExp" style="text-align:left;">
						<?php echo $this->Form->input('bnri_cyu_exp', 
												array('maxlength' => '40', 
													'label' => false, 
													'error' => false, 
													'style' => 'width:450px', 
													'class' => 'zen')) ?>
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
<div style="height:40px;width:100%;text-align:right;max-width:1185px;" id="buttons" >
	<input type="button"  value="追加" style="width:120px;height:30px;" id="addButton" >
	<input type="button"  value="更新" style="width:120px;height:30px;" id="updateButton" >
</div>
<!--/ 更新ボタン-->

<!-- メインテーブル-->
	<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>
<div class="resizable_s" id="lstTable">
	<div class="tableRowTitle row_ttl_2" style="width:1235px;">
		<div class="tableCell cell_ttl_2_1" style="width:50px;" ><?php echo Configure::read('Bango'); ?></div>
		<div class="tableCell cell_ttl_2_1" style="width:140px;" >大分類</div>
		<div class="tableCell cell_ttl_2_2" style="width:90px;" >中分類<br>コード</div>
		<div class="tableCell cell_ttl_2_1" style="width:240px;" >中分類名称</div>
		<div class="tableCell cell_ttl_2_1" style="width:140px;" >中分類略称</div>
		<div class="tableCell cell_ttl_2_1" style="width:320px;" >中分類説明</div>
		<div class="tableCell cell_ttl_2_2" style="width:75px;" >単位<br>区分</div>
		<div class="tableCell cell_ttl_2_2" style="width:40px;" >売上<br>区分</div>
		<div class="tableCell cell_ttl_2_2" style="width:50px;" >細分類<br>件数</div>
	</div>
<?php
				if (!empty($lsts)) {

					foreach($lsts as $obj) {
						$bango = $obj->bango;
						$cellsArray = $obj->cells;
						$dataArray = $obj->data;
					
?>
	<div class="tableRow row_dat" style="width:1235px;">
			<div class="tableCellBango cell_dat" style="width:50px;"><?php echo $bango ?></div>
			<div class="tableCell cell_dat nm" style="width:140px;"><?php echo $cellsArray[0] ?></div>
			<div class="tableCell cell_dat cd" style="width:90px;"><?php echo $cellsArray[1] ?></div>
			<div class="tableCell cell_dat nm" style="width:240px;"><?php echo $cellsArray[2] ?></div>
			<div class="tableCell cell_dat nm" style="width:140px;"><?php echo $cellsArray[3] ?></div>
			<div class="tableCell cell_dat nm" style="width:320px;"><?php echo $cellsArray[4] ?></div>
 			<div class="tableCell cell_dat kbn" style="width:75px;"><?php echo $cellsArray[5] ?></div>
 			<div class="tableCell cell_dat kbn" style="width:40px;"><?php echo $cellsArray[6] ?></div>
			<div class="tableCell cell_dat kbn" style="width:50px;"><?php echo $cellsArray[7] ?></div>

			<div class="hiddenData LINE_COUNT"><?php echo $dataArray[0] ?></div>
			<div class="hiddenData CHANGED"><?php echo $dataArray[1] ?></div>
			<div class="hiddenData KBN_TANI"><?php echo $dataArray[2] ?></div>
			<div class="hiddenData KBN_URIAGE"><?php echo $dataArray[3] ?></div>
			<div class="hiddenData BNRI_DAI_CD"><?php echo $dataArray[4] ?></div>
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
