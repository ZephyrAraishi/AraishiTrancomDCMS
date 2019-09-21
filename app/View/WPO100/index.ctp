<?php
			echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSWPO100_RELOAD', 'DCMSWPO100_CALENDAR',
					 'DCMSWPO100_DELETE'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSWPO'), false, array('inline'=>false));
?>
<!-- WRAPPER -->
<div id="wrapper_s">

<!-- 検索 -->
<div class="box" style="width:1130px;">
	<?php echo $this->Form->create('WPO100Model', array('url' => '/WPO100','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
		<table class="cndt" style="width:1130px">
			<tbody>
			<colgroup>
				<col width="150px">
				<col width="100px">
				<col width="50px">
				<col width="100px">
				<col width="100px">
				<col width="100px">
				<col width="100px">
				<col width="50px">
				<col width="50px">
				<col width="50px">
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
				<td></td>
    			<td>方面名　</td>
    			<td><input id="homen" name="homen" class="zen" type="text" value=""></td>
    			<td></td>
    			<td>バッチ№</td>
    			<td><input id="BatchNo_from" name="BatchNo_from" class="zen" type="text" value=""></td>
    			<td></td>
    			<td></td>
			</tr>
<!-- ２段目 -->
			<tr>
				<td></td>
	   			<td></td>
				<td></td>
				<td></td>
				<td></td>
	   			<td></td>
	   			<td></td>
				<td></td>
	   			<td></td>
	   			<td></td>
			</tr>
<!-- ３段目 -->
			<tr>
				<td></td>
	   			<td></td>
				<td></td>
				<td></td>
	   			<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
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

<!-- データ削除ボタン-->
<?php if (!empty($lsts)) { ?>
<div style="height:40px;width:100%;text-align:right;max-width:1250px;" id="buttons" >
	下記一覧のデータを削除します。<input type="button"  value="データ削除" style="width:180px;height:30px;" id="DeleteButton" >
</div>
<?php } ?>
<!--/ CSV出力ボタン-->

<!-- メインテーブル-->
	<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>
<!-- メインテーブルヘッダ-->
<div class="resizable_long" id="lstTable">
	<div class="tableRowTitle row_ttl_1" style="width:2400px;">
		<div class="tableCell cell_ttl_1" style="width:60px;" ><?php echo Configure::read("Bango") ?></div>
		<div class="tableCell cell_ttl_1" style="width:150px;" >業態倉庫</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >出荷日</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >納品日</div>
		<div class="tableCell cell_ttl_1" style="width:70px;" >バッチ№</div>
		<div class="tableCell cell_ttl_1" style="width:130px;" >方面</div>
		<div class="tableCell cell_ttl_1" style="width:220px;" >店舗</div>
		<div class="tableCell cell_ttl_1" style="width:220px;" >得意先</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >ロケーション</div>
		<div class="tableCell cell_ttl_1" style="width:300px;" >商品</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >賞味期限日</div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >荷１指</div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >荷２指</div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >荷３指</div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >総バラ指</div>
		<div class="tableCell cell_ttl_1" style="width:180px;" >代表倉庫</div>
	</div>

<!-- メインテーブル明細-->
<?php if (!empty($lsts)) { ?>
<?php  $count = 1; ?>
<?php  foreach ($lsts as $array): ?>
	<div class="tableRow row_dat" style="width:2400px;">
	    <div class="tableCell cell_dat bango" style="width:60px;" ><?php echo $count ?></div>
		<div class="tableCell cell_dat" style="width:150px;" align=left><?php if (!empty($array['GYOTAI_SOKO'])) {  echo $array['GYOTAI_SOKO']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['SHUKKA_BI'])) {  echo $array['SHUKKA_BI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['NOHIN_BI'])) {  echo $array['NOHIN_BI']; } ?></div>
		<div class="tableCell cell_dat" style="width:70px;" ><?php if (!empty($array['BATCH_NUMBER'])) {  echo $array['BATCH_NUMBER']; } ?></div>
		<div class="tableCell cell_dat" style="width:130px;"  align=left><?php if (!empty($array['HOMEN'])) {  echo $array['HOMEN']; } ?></div>
		<div class="tableCell cell_dat" style="width:220px;"  align=left><?php if (!empty($array['TENPO'])) {  echo $array['TENPO']; } ?></div>
		<div class="tableCell cell_dat" style="width:220px;"  align=left><?php if (!empty($array['TOKUISAKI']) ){ echo $array['TOKUISAKI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['LOCATION'])) { echo $array['LOCATION']; } ?></div>
		<div class="tableCell cell_dat" style="width:300px;"  align=left><?php if (!empty($array['SHOHIN'])) { echo $array['SHOHIN']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['SYOMI_KIGEN'])) {  echo $array['SYOMI_KIGEN']; } ?></div>
		<div class="tableCell cell_dat" style="width:80px;"  align=right><?php if (!empty($array['NISUGATA_1_SHIJISU'])) {  echo $array['NISUGATA_1_SHIJISU']; } ?></div>
		<div class="tableCell cell_dat" style="width:80px;" align=right><?php if (!empty($array['NISUGATA_2_SHIJISU'])) {  echo $array['NISUGATA_2_SHIJISU']; } ?></div>
		<div class="tableCell cell_dat" style="width:80px;" align=right><?php if (!empty($array['NISUGATA_3_SHIJISU'])) {  echo $array['NISUGATA_3_SHIJISU']; } ?></div>
		<div class="tableCell cell_dat" style="width:80px;" align=right><?php if (!empty($array['SO_BARA_SU'])) { echo $array['SO_BARA_SU']; } ?></div>
		<div class="tableCell cell_dat" style="width:180px;" align=left><?php if (!empty($array['DAIHYO_SOKO']))  {  echo $array['DAIHYO_SOKO']; } ?></div>
	</div>
<?php  $count++; ?>
<?php  endforeach; ?>
<?php } ?>
</div>
<div id="timestamp" style="display:none;" ><?php  echo $timestamp; ?></div>
<div id="YMD_FROM_H" style="display:none;" ><?php  if(!empty($ymd_from)){ echo $ymd_from; } ?></div>
<div id="BATCHNO_FROM_H" style="display:none;" ><?php if(!empty($BatchNo_from)){ echo $BatchNo_from; } ?></div>
<div id="HOMEN" style="display:none;" ><?php if(!empty($homen)){ echo $homen; } ?></div>
<!--/ メインテーブル-->

<!--/ #wrapper-->
