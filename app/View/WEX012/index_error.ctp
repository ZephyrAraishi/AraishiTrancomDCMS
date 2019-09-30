<?php
			echo $this->Html->script(array('effects','window','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSWEX012_RELOAD','DCMSWEX012_POPUP',
					'DCMSWEX012_ADDROW'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','DCMSWEX'), false, array('inline'=>false));
?>
<!-- WRAPPER -->
<div id="wrapper_s">

<!-- 検索 -->
<div class="box" style="width:1100px">
	<?php echo $this->Form->create('WEX012Model', array('url' => '/WEX012','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
		<table class="cndt" style="width:1100px">
			<tbody>
			<colgroup>
				<col width="170px">
				<col width="180px">
				<col width="170px">
				<col width="180px">
				<col width="170px">
				<col width="180px">
				<col width="150px">
			</colgroup>
			<tbody>
			<!-- １行目 -->
			<tr>
				<td>荷主</td>
				<td colspan="5">
					<select name="ninusi_cd"  id="mninusiCombo" style="width:120px">
						<option value=""></option>
						<?php
						foreach($mninusi as $value) :
						?>
						<option value="<?php echo $value['NINUSI_CD'] ?>"><?php echo $value['NINUSI_NM']?></option>
						<?php
						endforeach;
						?>
					</select>
				</td>
			</tr>
			<!-- ２行目 -->
			<tr>
				<td>組織コード</td>
				<td class="colSosikiCd" style="text-align:left;">
					<?php echo $this->Form->input('sosiki_cd',
									array('maxlength' => '10',
										'label' => false,
										'error' => false,
										'style' => 'width:120px',
										'class' => 'han')) ?>
				</td>
				<td>組織名称</td>
				<td colspan="3" class="colSosikiNm" style="text-align:left;">
						<?php echo $this->Form->input('sosiki_nm',
												array('maxlength' => '20',
													'label' => false,
													'error' => false,
													'style' => 'width:300px',
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
<div style="height:40px;width:100%;text-align:right;max-width:1190px;" id="buttons" >
	<input type="button"  value="追加" style="width:120px;height:30px;" id="addButton" >
	<input type="button"  value="更新" style="width:120px;height:30px;" id="updateButton" >
</div>
<!--/ 更新ボタン-->

<!-- メインテーブル-->
	<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>
<div class="resizable_s" id="lstTable">
	<div class="tableRowTitle row_ttl_1" style="width:1575px;">
		<div class="tableCellBango cell_ttl_1" style="width:60px;" ><?php echo Configure::read('Bango'); ?></div>
		<div class="tableCell cell_ttl_1" style="width:150px;" >荷主コード</div>
		<div class="tableCell cell_ttl_1" style="width:300px;" >荷主名称</div>
		<div class="tableCell cell_ttl_1" style="width:150px;" >組織コード</div>
		<div class="tableCell cell_ttl_1" style="width:300px;" >組織名称</div>
		<div class="tableCell cell_ttl_1" style="width:150px;" >組織略称</div>
	</div>

	<?php
	if (!empty($lsts)) {

		foreach($lsts as $obj) {
			$bango = $obj->bango;
			$cellsArray = $obj->cells;
			$dataArray = $obj->data;

	?>
		<div class="tableRow row_dat" style="width:1575px;">
			<div class="tableCellBango cell_dat" style="width:60px;"><?php echo $bango ?></div>
			<div class="tableCell cell_dat nm" style="width:150px;"><?php echo $cellsArray[0] ?></div>
			<div class="tableCell cell_dat nm" style="width:300px;"><?php echo $cellsArray[1] ?></div>
			<div class="tableCell cell_dat cd" style="width:150px;"><?php echo $cellsArray[2] ?></div>
			<div class="tableCell cell_dat nm" style="width:300px;"><?php echo $cellsArray[3] ?></div>
			<div class="tableCell cell_dat nm" style="width:150px;"><?php echo $cellsArray[4] ?></div>

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
