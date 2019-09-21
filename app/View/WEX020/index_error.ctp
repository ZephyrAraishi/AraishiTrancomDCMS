<?php
	echo $this->Html->script(array('effects', 'window', 'protocalendar', 'base64'
			, 'DCMSMessage', 'DCMSValidation', 'DCMSCommon'
			, 'DCMSWEX020_RELOAD', 'DCMSWEX020_POPUP', 'DCMSWEX020_UPDATE', 'DCMSWEX020_ADDROW','DCMSWEX020_BUNRUI'), array('inline'=>false));
	echo $this->Html->css(array('themes/default','kotei_dropdown_menu', 'themes/alert', 'themes/alphacube', 'calendar', 'DCMSWEX'), false, array('inline'=>false));
?>

<!-- WRAPPER -->
<div id="wrapper_s">

<!-- 検索 -->
<?php echo $this->Form->create('WEX020Model', array('url' => '/WEX020', 'inputDefaults' => array('label' => false,'div' => false))) ?>
<!-- 更新ボタン　キャンセルボタン-->
<div align="right" style="max-width:994px;">
	<input value="更新" id="updateButton" type="button">
</div>
<!--/ 更新ボタン　キャンセルボタン-->

<input type="hidden" id="timestamp" value="<?php  echo $timestamp; ?>">
<input type="hidden" id="pre_kbn_keiyaku" value="<?php echo $pre_kbn_keiyaku ?>">
<input type="hidden" id="pre_seikyu_sime" value="<?php echo $pre_seikyu_sime ?>">
<input type="hidden" id="pre_siharai_sight" value="<?php echo $pre_siharai_sight ?>">

	<table class="cndt">
		<tr>
			<td width="100px">契約形態</td>
			<td>
				<select id="kbn_keiyaku">
						<option value=""></option>
					<?php foreach ($keiyakus as $key => $value) { ?>
						<?php 
							$selected = "";
							if ($key === $kbn_keiyaku) {
								$selected = "selected";
							}
						?>
						<option value="<?php echo $key ?>"  <?php echo $selected ?>><?php echo $value ?></option>
					<?php }  ?>
				</select>
			</td>
		</tr>
		<tr>
			<td width="100px">請求締日</td>
			<td>
				<input type="text" id="seikyu_sime" name="seikyu_sime" size="4" maxlength="2" class="han" value="<?php echo $seikyu_sime ?>">
			</td>
		</tr>
		<tr>
			<td width="100px">支払サイト</td>
			<td>
				<input type="text" id="siharai_sight" name="siharai_sight" size="4" maxlength="3" class="han" value="<?php echo $siharai_sight ?>">
			</td>
		</tr>
	</table>
<br>

<!--/ 区切り線 -->
<div class="line"></div>
<!--/ 区切り線 -->

<!-- 更新ボタン-->
<div style="height:40px;width:100%;text-align:right;" id="buttons" >
	<input type="button"  value="追加" style="width:120px;height:30px;" id="addButton" >
</div>
<!--/ 更新ボタン-->

<!-- メインテーブル-->
<div class="resizable_s" id="lstTable">
	<div class="tableRowTitle row_ttl_1" style="width:1050px;">
		<div class="tableCellBango cell_ttl_1" style="width:60px;" ><?php echo Configure::read('Bango'); ?></div>
		<div class="tableCell cell_ttl_1" style="width:150px;" >大分類</div>
		<div class="tableCell cell_ttl_1" style="width:150px;" >中分類</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >単位</div>
		<div class="tableCell cell_ttl_1" style="width:110px;" >数量</div>
		<div class="tableCell cell_ttl_1" style="width:110px;" >費用</div>
		<div class="tableCell cell_ttl_1" style="width:300px;" >詳細</div>
	</div>
<?php
	if(!empty($keiyakuList)) :
		$i = 1;
		// 契約情報
		foreach ($keiyakuList as $obj) :

?>

    <div class="tableRow row_dat" style="width:1050px;">
		<div class="tableCell cell_dat" style="width:60px;"><?php echo $obj->row ?></div>
		<div class="tableCell cell_dat nm" style="width:150px;"><?php echo $obj->bnri_dai_ryaku  ?></div>
		<div class="tableCell cell_dat nm" style="width:150px;"><?php echo $obj->bnri_cyu_ryaku ?></div>
		<div class="tableCell cell_dat kbn" style="width:100px;"><?php echo $obj->kbn_tani_nm ?></div>
		<div class="tableCell cell_dat cnt" style="width:110px;"><?php echo $obj->suryo ?></div>
		<div class="tableCell cell_dat kin" style="width:110px;"><?php echo $obj->hiyou ?></div>
		<div class="tableCell cell_dat nm" style="width:300px;"><?php echo $obj->exp ?></div>
		<div class="hiddenData"><?php echo $obj->bnri_dai_cd ?></div>
		<div class="hiddenData"><?php echo $obj->bnri_cyu_cd ?></div>
		<div class="hiddenData"><?php echo $obj->kbn_tani ?></div>
		<div class="hiddenData"><?php echo $obj->changed ?></div>
		</div>
		<?php
			$i++;
		endforeach;
	endif;
?>
</div>
<br>

<?php echo $this->Form->end() ?>
<!--/ メインテーブル-->

<div id="timestamp" style="display:none;" ><?php  echo $timestamp; ?></div>

</div>
<!--/ #wrapper-->
