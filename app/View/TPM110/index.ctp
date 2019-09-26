<?php
			echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSTPM110_RELOAD', 'DCMSTPM110_CALENDAR',
					 'DCMSTPM110_CSV'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSTPM'), false, array('inline'=>false));
?>
<!-- WRAPPER -->
<div id="wrapper_s">

<!-- 検索 -->
<div class="box" style="width:900px;">
	<?php echo $this->Form->create('TPM110Model', array('url' => '/TPM110','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
		<table class="cndt" style="width:900px">
			<tbody>
			<colgroup>
				<col width="150px">
				<col width="100px">
				<col width="30px">
				<col width="100px">
				<col width="200px">
				<col width="100px">
				<col width="30px">
				<col width="100px">
				<col width="30px">
			</colgroup>
<!-- １段目 -->
			<tr>
				<td>出荷日</td>
				<td>
						<?php echo $this->Form->input('ymd_from',
												array('maxlength' => '40',
													'label' => false,
													'error' => false,
													'style' => 'width:100px',
													'name' => 'ymd_from',
													'id' => 'ymd_from',
													'value' => $ymd_from,
													'class' => 'zen')) ?>
				</td>
				<td style="display:table-cell;width:10px;padding-right:2px;padding-left:2px;text-align:center;font-size:13px;color:#222">～</td>
				<td>
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
				<td></td>
    			<td width="200">方面名</td>
    			<td><input id="homen" name="homen" class="zen" type="text" value=""></td>
    			<td></td>
    			<td></td>
			</tr>
<!-- ２段目 -->
			<tr>
				<td>商品コード</td>
	   			<td><input id="shohin_code" name="shohin_code" class="zen" type="text" value=""></td>
				<td></td>
				<td></td>
				<td></td>
				<td>商品名</td>
	   			<td><input id="shohin_mei" name="shohin_mei" class="zen" type="text" value=""></td>
				<td></td>
				<td></td>
			</tr>
<!-- ３段目 -->
			<tr>
				<td>店舗名</td>
	   			<td><input id="tenpo" name="tenpo" class="zen" type="text" value=""></td>
				<td></td>
				<td></td>
				<td></td>
				<td>看板バーコード</td>
	   			<td><input id="kanban_barcode" name="kanban_barcode" class="zen" type="text" value=""></td>
				<td></td>
				<td></td>
			</tr>
<!-- ４段目 -->
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td><input value="検索" type="submit" ; div="btnsubmit"></td>
				<td></td>
				<td></td>
			</tr>
			</tbody>
		</table>
	<?php echo $this->Form->end() ?>
</div>
<!--/ 検索 -->

<!--/ 区切り線 -->
<div class="line"></div>
<!--/ 区切り線 -->

<!-- CSV出力ボタン-->
<div style="height:40px;width:100%;text-align:right;max-width:1180px;" id="buttons" >
	<input type="button"  value="CSV出力" style="width:180px;height:30px;" id="csvButton" >
</div>
<!--/ CSV出力ボタン-->

<!-- メインテーブル-->
	<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>
<!-- メインテーブルヘッダ-->
<div class="resizable_s" id="lstTable">
	<div class="tableRowTitle row_ttl_1" style="width:2060px;">
		<div class="tableCell cell_ttl_1" style="width:60px;" ><?php echo Configure::read("Bango") ?></div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >出荷日</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >納品日</div>
		<div class="tableCell cell_ttl_1" style="width:120px;" >方面名</div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >店舗CD</div>
		<div class="tableCell cell_ttl_1" style="width:200px;" >店舗名</div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >商品CD</div>
		<div class="tableCell cell_ttl_1" style="width:250px;" >商品名</div>
		<div class="tableCell cell_ttl_1" style="width:150px;" >看板BC</div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >荷姿１格納数</div>
		<div class="tableCell cell_ttl_1" style="width:40px;" ></div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >荷姿２格納数</div>
		<div class="tableCell cell_ttl_1" style="width:40px;" ></div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >荷姿３格納数</div>
		<div class="tableCell cell_ttl_1" style="width:40px;" ></div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >総バラ格納数</div>
		<div class="tableCell cell_ttl_1" style="width:150px;" >作業者CD</div>
		<div class="tableCell cell_ttl_1" style="width:150px;" >作業者名</div>
	</div>

<!-- メインテーブル明細-->
<?php  $count = 1; ?>
<?php  foreach ($lsts as $array): ?>
	<div class="tableRow row_dat" style="width:2060px;">
	    <div class="tableCell cell_dat bango" style="width:60px;" ><?php echo $count ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['SHUKKA_BI'])) {  echo $array['SHUKKA_BI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['NOHIN_BI'])) {  echo $array['NOHIN_BI']; } ?></div>
		<div class="tableCell cell_dat" style="width:120px;" ><?php if (!empty($array['HOMEN_MEI'])) {  echo $array['HOMEN_MEI']; } ?></div>
		<div class="tableCell cell_dat" style="width:80px;" ><?php if (!empty($array['TENPO_CODE'])) {  echo $array['TENPO_CODE']; } ?></div>
		<div class="tableCell cell_dat" style="width:200px;" ><?php if (!empty($array['TENPO_MEI'])) {  echo $array['TENPO_MEI']; } ?></div>
		<div class="tableCell cell_dat" style="width:80px;" ><?php if (!empty($array['SHOHIN_CODE'])) {  echo $array['SHOHIN_CODE']; } ?></div>
		<div class="tableCell cell_dat" style="width:250px;" ><?php if (!empty($array['SHOHIN_MEI'])) {  echo $array['SHOHIN_MEI']; } ?></div>
		<div class="tableCell cell_dat" style="width:150px;" ><?php if (!empty($array['KANBAN_BARCODE'])) {  echo $array['KANBAN_BARCODE']; } ?></div>
		<div class="tableCell cell_dat" style="width:80px;" ><?php if (!empty($array['NISUGATA_1_KAKUNOSU'])) {  echo $array['NISUGATA_1_KAKUNOSU']; } ?></div>
		<div class="tableCell cell_dat" style="width:40px;" ><?php if (!empty($array['NISUGATA_1_TANI'])) {  echo $array['NISUGATA_1_TANI']; } ?></div>
		<div class="tableCell cell_dat" style="width:80px;" ><?php if (!empty($array['NISUGATA_2_KAKUNOSU'])) {  echo $array['NISUGATA_2_KAKUNOSU']; } ?></div>
		<div class="tableCell cell_dat" style="width:40px;" ><?php if (!empty($array['NISUGATA_2_TANI'])) {  echo $array['NISUGATA_2_TANI']; } ?></div>
		<div class="tableCell cell_dat" style="width:80px;" ><?php if (!empty($array['NISUGATA_3_KAKUNOSU'])) {  echo $array['NISUGATA_3_KAKUNOSU']; } ?></div>
		<div class="tableCell cell_dat" style="width:40px;" ><?php if (!empty($array['NISUGATA_3_TANI'])) {  echo $array['NISUGATA_3_TANI']; } ?></div>
		<div class="tableCell cell_dat" style="width:80px;" ><?php if (!empty($array['SO_BARA_KAKUNOSU'])) {  echo $array['SO_BARA_KAKUNOSU']; } ?></div>
		<div class="tableCell cell_dat" style="width:150px;" ><?php if (!empty($array['KOSIN_STAFF'])) {  echo $array['KOSIN_STAFF']; } ?></div>
		<div class="tableCell cell_dat" style="width:150px;" ><?php if (!empty($array['STAFF_NM'])) {  echo $array['STAFF_NM']; } ?></div>
	</div>
<?php  $count++; ?>
<?php  endforeach; ?>
</div>
<div id="timestamp" style="display:none;" ><?php  echo $timestamp; ?></div>
<div id="YMD_FROM_H" style="display:none;" ><?php  echo $ymd_from; ?></div>
<div id="YMD_TO_H" style="display:none;" ><?php  echo $ymd_to; ?></div>
<div id="HOMEN" style="display:none;" ><?php  if(!empty($homen)){ echo $homen; }?></div>
<div id="SHOHIN_C" style="display:none;" ><?php  if(!empty($shohin_code)){ echo $shohin_code; }?></div>
<div id="SHOHIN_M" style="display:none;" ><?php  if(!empty($shohin_mei)){ echo $shohin_mei; }?></div>
<div id="TENPO" style="display:none;" ><?php  if(!empty($tenpo)){ echo $tenpo; }?></div>
<div id="KANBAN" style="display:none;" ><?php  if(!empty($kanban_barcode)){ echo $kanban_barcode; }?></div>
<!--/ メインテーブル-->

</div>
<!--/ #wrapper-->
