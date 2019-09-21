<?php
echo $this->Html->script(array('DCMSTPM030_RELOAD'), array('inline'=>false));
echo $this->Html->css(array('DCMSTPM'), false, array('inline'=>false));
?>
<!-- WRAPPER -->
<div id="wrapper_l">

	<!-- トグルボタン -->
	<div id="toggle" style="max-width:1500px;">
		<form name="form1" method="post" action="">
			<ul class="toggle-list">
				<li><label class="selected" id="reloadButton" style="width:100px;cursor:default;text-align:center;" >自動リロード</label></li>
			</ul>
		</form>
	</div>
	<!--/ トグルボタン -->

	<!-- メインテーブル-->
	<div class="resizable" style="height:700px;" id="lstTable">
	
	<div class="tableRowTitle row_ttl_sp_2" style="width:1000px;">
		<div class="tableCell cell_ttl_sp_2" style="width:60px;" ><?php echo Configure::read("Bango") ?></div>
		<div class="tableCell cell_ttl_sp_2" style="width:110px;" >スタッフ</div>
		<div style="float:left;width:140px;height:69px;">
			<div class="tableCell cell_ttl_1" style="width:130px;">当日生産性</div>
			<div class="tableCell cell_ttl_1" style="width:60px;">ピース</div>
			<div class="tableCell cell_ttl_1" style="width:60px;">アイテム</div>
		</div>
		<div style="float:left;width:140px;height:69px;">
			<div class="tableCell cell_ttl_1" style="width:130px;">実績生産性</div>
			<div class="tableCell cell_ttl_1" style="width:60px;">ピース</div>
			<div class="tableCell cell_ttl_1" style="width:60px;">アイテム</div>
		</div>
		<div class="tableCell cell_ttl_sp_2" style="width:110px;" >処理済セット数</div>
		<div class="tableCell cell_ttl_sp_2" style="width:80px;" >稼働時間</div>
		<div class="tableCell cell_ttl_sp_2" style="width:60px;" >ピース数</div>
		<div class="tableCell cell_ttl_sp_2" style="width:80px;" >アイテム数</div>
		<div class="tableCell cell_ttl_sp_2" style="width:70px;" >処理済数</div>
		<div class="tableCell cell_ttl_sp_2" style="width:70px;" >棚欠済数</div>
	</div>
<?php foreach ($lsts as $key => $value): ?>
	<div class="tableRow row_dat" style="width:1000px;">
		<div class="tableCell cell_dat bango" style="width:60px;"><?php echo $key+1 ?></div>
		<div class="tableCell cell_dat nm" style="width:110px;"><?php echo $value['STAFF_NM'] ?></div>
		<div class="tableCell cell_dat seisansei" style="width:60px;"><?php if(!empty($value['TOD_PROD_PIECE'])) echo number_format($value['TOD_PROD_PIECE'],1) ?></div>
		<div class="tableCell cell_dat seisansei" style="width:60px;"><?php if(!empty($value['TOD_PROD_ITEM'])) echo number_format($value['TOD_PROD_ITEM'],1) ?></div>
		<div class="tableCell cell_dat seisansei" style="width:60px;"><?php if(!empty($value['ACT_PROD_PIECE'])) echo number_format($value['ACT_PROD_PIECE'],1) ?></div>
		<div class="tableCell cell_dat seisansei" style="width:60px;"><?php if(!empty($value['ACT_PROD_ITEM'])) echo number_format($value['ACT_PROD_ITEM'],1) ?></div>
		<div class="tableCell cell_dat cnt" style="width:110px;"><?php if(!empty($value['CNT_SET_SYORI_SUMI'])) echo number_format($value['CNT_SET_SYORI_SUMI']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:80px;"><?php  if(!empty($value['STAFF_KADO_HOUR'])) echo number_format($value['STAFF_KADO_HOUR'],1) ?></div>
		<div class="tableCell cell_dat cnt" style="width:60px;"><?php if(!empty($value['SUM_PIECE'])) echo number_format($value['SUM_PIECE']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:80px;"><?php if(!empty($value['SUM_ITEM'])) echo number_format($value['SUM_ITEM']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:70px;"><?php if(!empty($value['CNT_SYORI_SUMI'])) echo number_format($value['CNT_SYORI_SUMI']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:70px;"><?php if(!empty($value['CNT_T_KETSU'])) echo number_format($value['CNT_T_KETSU']) ?></div>
	</div>
<?php endforeach; ?>
	</div>
	<!--/ メインテーブル-->

</div>
<!--/ #wrapper-->
