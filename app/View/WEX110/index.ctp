<?php
			echo $this->Html->script(array('effects','window','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSWEX110_RELOAD','DCMSWEX110_POPUP','DCMSWEX110_UPDATE',
					'DCMSWEX110_ADDROW'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','themes/alert','themes/alphacube','DCMSWEX'), false, array('inline'=>false));
?>
<!-- WRAPPER -->
<div id="wrapper_s">

<!-- 検索 -->
<div class="box" style="width:1000px;">
	<?php echo $this->Form->create('WEX110Model', array('url' => '/WEX110','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?> 
		<table class="cndt" style="width:1000px">
			<tbody>
			<colgroup>
				<col width="150px">
				<col width="700px">
				<col width="150px">
			</colgroup>
			<tr>
				<td>名称区分</td>
				<td class="colBnriExp" style="text-align:left;">
					<select name="kbn" id="kbn" style="width:200px;">
						<option value=""></option>
						<?php foreach($kbnArray as $kbn) : ?>
						<option value="<?= $kbn ?>"><?= $kbn ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td></td>
			</tr>
			<tr>
				<td>名称</td>
				<td class="colBnriExp" style="text-align:left;">
						<?php echo $this->Form->input('meisyo', 
												array('maxlength' => '40', 
													'label' => false, 
													'error' => false, 
													'id' => 'meisyo',
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
<div style="height:40px;width:100%;text-align:right;max-width:1180px;" id="buttons" >
	<input type="button"  value="追加" style="width:120px;height:30px;" id="addButton" >
	<input type="button"  value="更新" style="width:120px;height:30px;" id="updateButton" >
</div>
<!--/ 更新ボタン-->

<!-- メインテーブル-->
<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>
<div class="resizable" style="height:400px;" id="lstTable">
	<div class="tableRowTitle row_ttl_2" style="width:1540px;">
		<div class="tableCell cell_ttl_2_1" style="width:60px;" ><?php echo Configure::read('Bango'); ?></div>
		<div class="tableCell cell_ttl_2_1" style="width:80px;" >名称区分</div>
		<div class="tableCell cell_ttl_2_1" style="width:80px;" >名称コード</div>
		<div class="tableCell cell_ttl_2_1" style="width:150px;" >名称１</div>
		<div class="tableCell cell_ttl_2_1" style="width:150px;" >名称２</div>
		<div class="tableCell cell_ttl_2_1" style="width:150px;" >名称３</div>
		<div class="tableCell cell_ttl_2_1" style="width:150px;" >フリー文字１</div>
		<div class="tableCell cell_ttl_2_1" style="width:150px;" >フリー文字２</div>
		<div class="tableCell cell_ttl_2_1" style="width:150px;" >フリー文字３</div>
		<div class="tableCell cell_ttl_2_1" style="width:100px;" >フリー数字１</div>
		<div class="tableCell cell_ttl_2_1" style="width:100px;" >フリー数字２</div>
		<div class="tableCell cell_ttl_2_1" style="width:100px;" >フリー数字３</div>
	</div>
<?php $count = 0; ?>
<?php foreach ($lsts as $array): ?>
	<div class="tableRow row_dat" style="width:1540px;">
		<div class="tableCell cell_dat" style="width:60px;"><?php echo ($count + 1) + $index; ?></div>
		<div class="tableCell cell_dat KBN" style="width:80px;"><?php echo $array['MEI_KBN'] ?></div>
		<div class="tableCell cell_dat KBN" style="width:80px;"><?php echo $array['MEI_CD'] ?></div>
		<div class="tableCell cell_dat nm" style="width:150px;"><?php echo $array['MEI_1'] ?></div>
		<div class="tableCell cell_dat nm" style="width:150px;"><?php echo $array['MEI_2'] ?></div>
		<div class="tableCell cell_dat nm" style="width:150px;"><?php echo $array['MEI_3'] ?></div>
		<div class="tableCell cell_dat nm" style="width:150px;"><?php echo $array['VAL_FREE_STR_1'] ?></div>
		<div class="tableCell cell_dat nm" style="width:150px;"><?php echo $array['VAL_FREE_STR_2'] ?></div>
		<div class="tableCell cell_dat nm" style="width:150px;"><?php echo $array['VAL_FREE_STR_3'] ?></div>
		<div class="tableCell cell_dat kin" style="width:100px;"><?php echo $array['VAL_FREE_NUM_1'] ?></div>
		<div class="tableCell cell_dat kin" style="width:100px;"><?php echo $array['VAL_FREE_NUM_2'] ?></div>
		<div class="tableCell cell_dat kin" style="width:100px;"><?php echo $array['VAL_FREE_NUM_3'] ?></div>
		<div class="hiddenData LINE_COUNT"><?php echo $count ?></div>
		<div class="hiddenData CHANGED">0</div>
	</div>
<?php $count++; ?>
<?php endforeach; ?>	
</div>
<!--/ メインテーブル-->

</div>
<!--/ #wrapper-->
            