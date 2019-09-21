<?php
	echo $this->Html->script(array('effects','window','protocalendar','base64',
			'DCMSMessage','DCMSCommon','DCMSValidation','DCMSTPM120_RELOAD', 'DCMSTPM120_CALENDAR',
			 'DCMSTPM120_CSV'), array('inline'=>false));
	echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSTPM'), false, array('inline'=>false));
?>
<!-- WRAPPER -->
<div id="wrapper_s">

<!-- 検索エリア -->
<div class="box" style="width:900px;">
	<?php echo $this->Form->create('TPM120Model', array('url' => '/TPM120','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
		<table class="cndt" style="width:900px">
			<tbody>
				<colgroup>
					<col width="200px">
					<col width="50px">
					<col width="30px">
					<col width="100px">
					<col width="200px">
					<col width="100px">
					<col width="30px">
					<col width="100px">
					<col width="30px">
				</colgroup>
				<tr>
					<td>■当週と先週【物量比較】</td>
					<td>出荷日</td>
					<td>
					<?php echo $this->Form->input('ymd',
											array('maxlength' => '40',
												'label' => false,
												'error' => false,
												'style' => 'width:100px',
												'name' => 'ymd',
												'id' => 'ymd',
												'value' => $ymd,
												'class' => 'zen')) ?>
					</td>
					<td><input value="検索" type="submit" ; div="btnsubmit"></td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
		</table>
	<?php echo $this->Form->end() ?>
</div>
<!--/ 検索エリア -->

<!--/ 区切り線 -->
	<div class="line"></div>
<!--/ 区切り線 -->

<!-- メインテーブル-->
	<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>
<!-- 月曜日メインテーブルヘッダ-->
	<?php
		echo '月曜日：';
		$w = date("w",strtotime($ymd));
			if ($w == 1){
				$ymd_m = date('Y/m/d',strtotime($ymd));
			}else{
				$ymd_m = date('Y/m/d', strtotime("last Monday", strtotime($ymd)));
			}
				$ymd_from = date("Y/m/d～", strtotime("$ymd_m +1 day"  ));
				$ymd_to = date("Y/m/d", strtotime("$ymd_m +6 day"  ));
    	echo $ymd_m;
	?>
<div class="resizable_s" id="lstTable" style="min-height:0px;width:100%;overflow-x: auto;overflow-y: auto;height:100%";>
	<div class="tableRowTitle row_ttl_1" style="width:890px;">
		<div class="tableCell cell_ttl_1" style="width:60px;" ><?php echo Configure::read("Bango") ?></div>
		<div class="tableCell cell_ttl_1" style="width:250px;" >得意先名</div>
		<div class="tableCell cell_ttl_1" style="width:75px;" >当週個数</div>
		<div class="tableCell cell_ttl_1" style="width:75px;" >先週個数</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >先週比(個数)</div>
		<div class="tableCell cell_ttl_1" style="width:75px;" >当週行数</div>
		<div class="tableCell cell_ttl_1" style="width:75px;" >先週行数</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >先週比(行)</div>
	</div>
<!-- 月曜日メインテーブル明細-->
	<?php  $count = 1; ?>
	<?php  foreach ((array)$lsts as $array): ?>
		<div class="tableRow row_dat" style="width:890px;">
		    <div class="tableCell cell_dat bango" style="width:60px;" ><?php echo $count ?></div>
			<div class="tableCell cell_dat" style="width:250px;" ><?php if (!empty($array['TOKUISAKI_MEI'])) {  echo $array['TOKUISAKI_MEI']; } ?></div>
			<div class="tableCell cell_dat" style="width:75px;" ><?php echo $array['SHIJISU'];  ?></div>
			<div class="tableCell cell_dat" style="width:75px;" ><?php echo $array['S_SHIJISU'];  ?></div>
			<div class="tableCell cell_dat" style="width:100px;" ><?php echo $array['SYUKEI'];  ?>%</div>
			<div class="tableCell cell_dat" style="width:75px;" ><?php echo $array['GYOSU'];  ?></div>
			<div class="tableCell cell_dat" style="width:75px;" ><?php echo $array['S_GYOSU'];  ?></div>
			<div class="tableCell cell_dat" style="width:100px;" ><?php echo $array['G_SYUKEI'];  ?>%</div>
		</div>
	<?php  $count++; ?>
	<?php  endforeach; ?>
</div>
	<div id="timestamp" style="display:none;" ><?php  echo $timestamp; ?></div>
	<div id="YMD" style="display:none;" ><?php  echo $ymd; ?></div>
<!--/ 月曜日エリア小計-->
	<div class="tableRow row_dat" style="width:890px; background-color:#99FFCC;">
	    <div class="tableCell cell_dat bango" style="width:60px;" ></div>
		<div class="tableCell cell_dat" style="width:250px;" >小計</div>
		<div class="tableCell cell_dat" style="width:75px;" ><?php echo $lkei['SHIJISU'];  ?></div>
		<div class="tableCell cell_dat" style="width:75px;" ><?php echo $lkei['S_SHIJISU'];  ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $lkei['SYUKEI'];  ?>%</div>
		<div class="tableCell cell_dat" style="width:75px;" ><?php echo $lkei['GYOSU'];  ?></div>
		<div class="tableCell cell_dat" style="width:75px;" ><?php echo $lkei['S_GYOSU'];  ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $lkei['G_SYUKEI'];  ?>%</div>

	</div>
<!--/ メインテーブル-->
<!--/ 区切り線 -->
	<div class="line"></div>
<!--/ 区切り線 -->

<!-- メインテーブル2-->

	<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>
<!-- 火曜日メインテーブルヘッダ-->
	<?php 
	echo '火曜日：';
    echo $ymd_from;
    echo $ymd_to;
	?>
<div class="resizable_s" id="lstTable" style="min-height:0px;width:100%;overflow-x: auto;overflow-y: auto;height:100%";>
	<div class="tableRowTitle row_ttl_1" style="width:890px;">
		<div class="tableCell cell_ttl_1" style="width:60px;" ><?php echo Configure::read("Bango") ?></div>
		<div class="tableCell cell_ttl_1" style="width:250px;" >得意先名</div>
		<div class="tableCell cell_ttl_1" style="width:75px;" >当週個数</div>
		<div class="tableCell cell_ttl_1" style="width:75px;" >先週個数</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >先週比(個数)</div>
		<div class="tableCell cell_ttl_1" style="width:75px;" >当週行数</div>
		<div class="tableCell cell_ttl_1" style="width:75px;" >先週行数</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >先週比(行)</div>
	</div>
<!-- 火曜日メインテーブル明細-->
	<?php  $count = 1; ?>
	<?php  foreach ((array)$blsts as $array): ?>
		<div class="tableRow row_dat" style="width:890px;">
		    <div class="tableCell cell_dat bango" style="width:60px;" ><?php echo $count ?></div>
			<div class="tableCell cell_dat" style="width:250px;" ><?php if (!empty($array['TOKUISAKI_MEI'])) {  echo $array['TOKUISAKI_MEI']; } ?></div>
			<div class="tableCell cell_dat" style="width:75px;" ><?php echo $array['SHIJISU'];  ?></div>
			<div class="tableCell cell_dat" style="width:75px;" ><?php echo $array['S_SHIJISU'];  ?></div>
			<div class="tableCell cell_dat" style="width:100px;" ><?php echo $array['SYUKEI'];  ?>%</div>
			<div class="tableCell cell_dat" style="width:75px;" ><?php echo $array['GYOSU'];  ?></div>
			<div class="tableCell cell_dat" style="width:75px;" ><?php echo $array['S_GYOSU'];  ?></div>
			<div class="tableCell cell_dat" style="width:100px;" ><?php echo $array['G_SYUKEI'];  ?>%</div>
		</div>
	<?php  $count++; ?>
	<?php  endforeach; ?>
</div>
	<div id="timestamp" style="display:none;" ><?php  echo $timestamp; ?></div>
	<div id="YMD" style="display:none;" ><?php  echo $ymd; ?></div>
<!--/ 火曜日エリア小計-->
	<div class="tableRow row_dat" style="width:890px; background-color:#99FFCC;">
	    <div class="tableCell cell_dat bango" style="width:60px;" ></div>
		<div class="tableCell cell_dat" style="width:250px;" >小計</div>
		<div class="tableCell cell_dat" style="width:75px;" ><?php echo $blkei['SHIJISU'];  ?></div>
		<div class="tableCell cell_dat" style="width:75px;" ><?php echo $blkei['S_SHIJISU'];  ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $blkei['SYUKEI'];  ?>%</div>
		<div class="tableCell cell_dat" style="width:75px;" ><?php echo $blkei['GYOSU'];  ?></div>
		<div class="tableCell cell_dat" style="width:75px;" ><?php echo $blkei['S_GYOSU'];  ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $blkei['G_SYUKEI'];  ?>%</div>
</div>
<!--/ 区切り線 -->
	<div class="line"></div>
<!--/ 区切り線 -->
<!--/ 合計エリア-->
	<div class="tableRow row_dat" style="width:890px ; background-color:#FFCC66;">
	    <div class="tableCell cell_dat bango" style="width:60px;" ></div>
		<div class="tableCell cell_dat" style="width:250px;" >合計</div>
		<div class="tableCell cell_dat" style="width:75px;" ><?php echo $clkei['SHIJISU'];  ?></div>
		<div class="tableCell cell_dat" style="width:75px;" ><?php echo $clkei['S_SHIJISU'];  ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $clkei['SYUKEI'];  ?>%</div>
		<div class="tableCell cell_dat" style="width:75px;" ><?php echo $clkei['GYOSU'];  ?></div>
		<div class="tableCell cell_dat" style="width:75px;" ><?php echo $clkei['S_GYOSU'];  ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $clkei['G_SYUKEI'];  ?>%</div>
<!--/ 合計エリア-->
<!--/ メインテーブル-->

<!--/ #wrapper-->
