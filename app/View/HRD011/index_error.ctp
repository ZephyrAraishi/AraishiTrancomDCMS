<?php
			echo $this->Html->script(array('effects','window','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSHRD011_RELOAD','DCMSHRD011_POPUP',
					'DCMSHRD011_ADDROW'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','themes/alert','themes/alphacube','DCMSHRD'), false, array('inline'=>false));

?>
<!-- WRAPPER -->
<div id="wrapper_s">

<!-- 検索 -->
<div class="box" style="width:1000px;">
	<?php echo $this->Form->create('HRD011Model', array('url' => '/HRD011','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
		<table class="cndt" style="width:1000px">
			<tbody>
			<colgroup>
				<col width="150px">
				<col width="700px">
				<col width="150px">
			</colgroup>
			<tr>
				<td>スタッフコード</td>
				<td class="colStaffCd" style="text-align:left;" colspan="2">
						<?php echo $this->Form->input('staff_cd',
												array('maxlength' => '10',
													'label' => false,
													'error' => false,
													'style' => 'width:150px',
													'class' => 'han')) ?>
				</td>
			</tr>
			<tr>
				<td>サブシステム</td>
				<td class="colSubSystemId" style="text-align:left;" colspan="2">
						<?php echo $this->Form->input('sub_systemid',
												array('maxlength' => '10',
													'label' => false,
													'error' => false,
													'style' => 'width:200px',
													'class' => 'zen')) ?>
				</td>
			</tr>
			<tr>
				<td>機能ＩＤ</td>
				<td class="colKinoId" style="text-align:left;">
						<?php echo $this->Form->input('kinoid',
												array('maxlength' => '10',
													'label' => false,
													'error' => false,
													'style' => 'width:200px',
													'class' => 'zen')) ?>
				</td>
				<td><input value="検索" type="submit" name="btnsubmit"></td>
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
		<div class="tableCell cell_ttl_2_1" style="width:150px;" >スタッフコード</div>
		<div class="tableCell cell_ttl_2_1" style="width:300px;" >スタッフ氏名</div>
		<div class="tableCell cell_ttl_2_1" style="width:150px;" >サブシステム</div>
		<div class="tableCell cell_ttl_2_1" style="width:415px;" >機能ＩＤ</div>
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
			<div class="tableCell cell_dat cd" style="width:150px;"><?php echo $cellsArray[0] ?></div>
			<div class="tableCell cell_dat nm" style="width:300px;"><?php echo $cellsArray[1] ?></div>
			<div class="tableCell cell_dat cd" style="width:150px;"><?php echo $cellsArray[2] ?></div>
			<div class="tableCell cell_dat cd" style="width:415px;"><?php echo $cellsArray[3] ?></div>

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
