<?php
			echo $this->Html->script(array('effects','window','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSWEX030_RELOAD','DCMSWEX030_POPUP',
					'DCMSWEX030_ADDROW'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','themes/alert','themes/alphacube','DCMSWEX'), false, array('inline'=>false));
?>
<!-- WRAPPER -->
<div id="wrapper_s">

<!-- 検索 -->
<div class="box" style="width:1000px;">
	<?php echo $this->Form->create('WEX030Model', array('url' => '/WEX030','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?> 
		<table class="cndt" style="width:1000px">
			<tbody>
			<colgroup>
				<col width="150px">
				<col width="700px">
				<col width="150px">
			</colgroup>
			<tr>
				<td>大分類コード</td>
				<td class="colBnriCd" style="text-align:left;" colspan="2">
						<?php echo $this->Form->input('bnri_dai_cd', 
												array('maxlength' => '3', 
													'label' => false, 
													'error' => false, 
													'style' => 'width:100px', 
													'class' => 'han')) ?>
				</td>
			</tr>
			<tr>
				<td>大分類名称</td>
				<td class="colBnriNm" style="text-align:left;" colspan="2">
						<?php echo $this->Form->input('bnri_dai_nm', 
												array('maxlength' => '20', 
													'label' => false, 
													'error' => false, 
													'style' => 'width:300px', 
													'class' => 'zen')) ?>
				</td>
			</tr>
			<tr>
				<td>大分類説明</td>
				<td class="colBnriExp" style="text-align:left;">
						<?php echo $this->Form->input('bnri_dai_exp', 
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
		<div class="tableCell cell_ttl_2_1" style="width:100px;" >大分類コード</div>
		<div class="tableCell cell_ttl_2_1" style="width:300px;" >大分類名称</div>
		<div class="tableCell cell_ttl_2_1" style="width:150px;" >大分類略称</div>
		<div class="tableCell cell_ttl_2_1" style="width:415px;" >大分類説明</div>
		<div class="tableCell cell_ttl_2_2" style="width:85px;" >単位<br>区分</div>
		<div class="tableCell cell_ttl_2_2" style="width:50px;" >中分類<br>件数</div>
	</div>
<?php $count = 0; ?>
<?php foreach ($lsts as $array): ?>
	<div class="tableRow row_dat" style="width:1230px;">
		<div class="tableCellBango cell_dat" style="width:60px;"><?php echo ($count + 1) + $index; ?></div>
		<div class="tableCell cell_dat cd" style="width:100px;"><?php echo $array['BNRI_DAI_CD'] ?></div>
		<div class="tableCell cell_dat nm" style="width:300px;"><?php echo $array['BNRI_DAI_NM'] ?></div>
		<div class="tableCell cell_dat nm" style="width:150px;"><?php echo $array['BNRI_DAI_RYAKU'] ?></div>
		<div class="tableCell cell_dat nm" style="width:415px;"><?php echo $array['BNRI_DAI_EXP'] ?></div>
 			<div class="tableCell cell_dat kbn" style="width:85px;"><?php echo empty($array['KBN_TANI']) ? "" : $kbnTanis[$array['KBN_TANI']]; ?></div>
		<div class="tableCell cell_dat kbn" style="width:50px;"><?php echo $array['BNRI_DAI_CD_CNT'] ?></div>

		<div class="hiddenData LINE_COUNT"><?php echo $count ?></div>
		<div class="hiddenData CHANGED">0</div>
		<div class="hiddenData KBN_TANI"><?php echo $array['KBN_TANI'] ?></div>
	</div>
<?php $count++; ?>
<?php endforeach; ?>	
</div>
<div id="timestamp" style="display:none;" ><?php  echo $timestamp; ?></div>
<!--/ メインテーブル-->

</div>
<!--/ #wrapper-->
            