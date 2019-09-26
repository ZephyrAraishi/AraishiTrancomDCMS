<?php
			echo $this->Html->script(array('effects','window','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSWEX011_RELOAD','DCMSWEX011_POPUP',
					'DCMSWEX011_ADDROW'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','themes/alert','themes/alphacube','DCMSWEX'), false, array('inline'=>false));

?>
<!-- WRAPPER -->
<div id="wrapper_s">

<!-- 検索 -->
<div class="box" style="width:1000px;">
	<?php echo $this->Form->create('WEX011Model', array('url' => '/WEX011','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
		<table class="cndt" style="width:1000px">
			<tbody>
			<colgroup>
				<col width="150px">
				<col width="700px">
				<col width="150px">
			</colgroup>
			<tr>
				<td>荷主コード</td>
				<td class="colNinusiCd" style="text-align:left;" colspan="2">
						<?php echo $this->Form->input('ninusi_cd',
												array('maxlength' => '10',
													'label' => false,
													'error' => false,
													'style' => 'width:100px',
													'class' => 'han')) ?>
				</td>
			</tr>
			<tr>
				<td>荷主名称</td>
				<td class="colNinusiNm" style="text-align:left;" colspan="2">
						<?php echo $this->Form->input('ninusi_nm',
												array('maxlength' => '20',
													'label' => false,
													'error' => false,
													'style' => 'width:300px',
													'class' => 'zen')) ?>
				</td>
			</tr>
			<tr>
				<td>荷主略称</td>
				<td class="colNinusiRyaku" style="text-align:left;">
						<?php echo $this->Form->input('ninusi_ryaku',
												array('maxlength' => '40',
													'label' => false,
													'error' => false,
													'style' => 'width:200px',
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
<div class="resizable_s" id="lstTable">
	<div class="tableRowTitle row_ttl_2" style="width:1230px;">
		<div class="tableCellBango cell_ttl_2_1" style="width:60px;" ><?php echo Configure::read('Bango'); ?></div>
		<div class="tableCell cell_ttl_2_1" style="width:200px;" >荷主コード</div>
		<div class="tableCell cell_ttl_2_1" style="width:600px;" >荷主名称</div>
		<div class="tableCell cell_ttl_2_1" style="width:250px;" >荷主略称</div>
	</div>
<?php
				if (!empty($lsts)) {

					foreach($lsts as $obj) {
						$bango = $obj->bango;
						$cellsArray = $obj->cells;
						$dataArray = $obj->data;

?>
	<div class="tableRow row_dat" style="width:1230px;">
			<div class="tableCellBango cell_dat" style="width:60px;"><?php echo $bango; ?></div>
			<div class="tableCell cell_dat cd" style="width:200px;"><?php echo $cellsArray[0] ?></div>
			<div class="tableCell cell_dat nm" style="width:600px;"><?php echo $cellsArray[1] ?></div>
			<div class="tableCell cell_dat nm" style="width:250px;"><?php echo $cellsArray[2] ?></div>

			<div class="hiddenData LINE_COUNT"><?php echo $dataArray[0] ?></div>
			<div class="hiddenData CHANGED"><?php echo $dataArray[1] ?></div>
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
