<?php
	echo $this->Html->script(array('DCMSTPM010_RELOAD'), array('inline'=>false));
	echo $this->Html->css(array('DCMSTPM'), false, array('inline'=>false));
?>

<!-- WRAPPER -->
<div id="wrapper_l" style="width:95%; max-width:1500px;">

<div id="debug"></div>

<!-- トグルボタン -->
<div id="toggle">
	<form name="form1" method="post" action="">
		<ul class="toggle-list">
			<li><label class="selected" id="reloadButton" style="width:100px;cursor:default;text-align:center;" >自動リロード</label></li>
		</ul>
	</form>
</div>
<!--/ トグルボタン -->

<!-- 全体進捗グラフ -->
<div class="resizable" id="shinchokuGraph">
	<div class="graphArea">
		<div class="graphMajor">
			<div style="float:left;width:25%">0%</div>
			<div style="float:left;width:25%">25%</div>
			<div style="float:left;width:25%">50%</div>
			<div style="float:left;width:25%">75%</div>
		</div>
		<div style="float:left;width:20px;height:5px;font-size:8pt;">100%</div>
		<div class="graphMoji">パッチ進捗</div>
		<div class="graph"><div class="graphAmount" style="width:<?php echo $batchShinchoku ?>%;background-image:url('../img/graph/tb04_dr2.gif');"></div></div>
		<div class="graphNumArea" style="padding-top:10px;"><?php echo $batchShinchokuSu ?></div>
	</div>
	<div class="graphArea" >
		<div class="graphMoji">物量進捗</div>
		<div class="graph"><div class="graphAmount" style="width:<?php echo $butsuryoShinchoku ?>%;background-image:url('../img/graph/tb04_ol3.gif');"></div></div>
		<div class="graphNumArea" style="padding-top:10px;"><?php echo $butsuryoShinchokuSu ?></div>
	</div>
</div>
<!--/ 全体進捗グラフ -->

<!-- A号機-->
<div class="resizable" style="height:50px;">
	<div class="graphArea" style="height:50px;">
		<div class="graphMajor">
			<div style="float:left;width:25%">0%</div>
			<div style="float:left;width:25%">25%</div>
			<div style="float:left;width:25%">50%</div>
			<div style="float:left;width:25%">75%</div>
		</div>
		<div style="float:left;width:20px;height:5px;font-size:8pt;">100%</div>
		<div class="graphMoji"><?php echo $sorterNmA ?></div>
		<div class="graph"><div class="graphAmount" style="width:<?php echo $AbatchShinchoku ?>%;background-image:url('../img/graph/tb04_br3.gif');"></div></div>
		<div class="graphNumArea" style="padding-top:3px;" ><?php echo $AbatchShinchokuSu ?><br><?php echo $AbutsuryoShinchokuSu ?></div>
	</div>
</div>
<div class="resizable" style="height:165px;">
	<div class="tableRowTitle row_ttl_sp_2" style="width:1480px;">
		<div class="tableCell cell_ttl_sp_2" style="width:60px;"><?php echo Configure::read("Bango") ?></div>
		<div class="tableCell cell_ttl_sp_2" style="width:180px;">バッチ名</div>
		<div class="tableCell cell_ttl_sp_2" style="width:80px;">SバッチNo</div>
		<div class="tableCell cell_ttl_sp_2" style="width:80px;">状態</div>
		<div style="float:left;width:360px;height:70px;">
			<div class="tableCell cell_ttl_1" style="width:350px;">集計管理No数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">総数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">未処理</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">処理中</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">処理済</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">棚欠品</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">棚欠済</div>
		</div>
		<div style="float:left;width:180px;height:70px;">
			<div class="tableCell cell_ttl_1" style="width:170px;">ピース数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">総数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">済数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">残数</div>
		</div>
		<div style="float:left;width:180px;height:70px;">
			<div class="tableCell cell_ttl_1" style="width:170px;">ソーター</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">総数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">済数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">残数</div>
		</div>
		<div class="tableCell cell_ttl_sp_2_2" style="width:60px;">稼動<br>スタッフ</div>
		<div style="float:left;width:180px;height:70px;">
			<div class="tableCell cell_ttl_1" style="width:170px;">時間</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">開始</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">終了</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">予測</div>
		</div>
		<div class="tableCell cell_ttl_sp_2" style="width:60px;">生産性</div>
	</div>
	<?php $count = 1; ?>
	<?php foreach ($sorterListA as $array): ?>
	<div class="tableRow row_dat" style="width:1480px;">
	  	<div class="tableCell cell_dat bango" style="width:60px;"><?php echo $count++ ?></div>
		<div class="tableCell cell_dat nm" style="width:180px;"><?php echo $array['BATCH_NM'] ?></div>
		<div class="tableCell cell_dat kbn" style="width:80px;"><?php echo $array['S_BATCH_NO'] ?></div>
		<div class="tableCell cell_dat kbn" style="width:80px;"><?php echo $array['STATUS_NM'] ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['S_SOSU']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['S_MISYORI']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['S_SYORICHU']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['S_SYORIZUMI']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['S_TANAKETSU']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['S_TANAKETSUZUMI']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['PIECE_SOSU']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['PIECE_SUMI']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['PIECE_ZAN']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['SORTER_SOSU']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['SORTER_SUMI']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['SORTER_ZAN']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:60px;"><?php echo number_format($array['KADO_STAFF']) ?></div>
		<div class="tableCell cell_dat date" style="width:50px;"><?php echo $array['TIME_KAISHI'] ?></div> 
		<div class="tableCell cell_dat date" style="width:50px;"><?php echo $array['TIME_SYURYO'] ?></div>
		<div class="tableCell cell_dat date" style="width:50px;"><?php echo $array['TIME_YOSO'] ?></div>
		<div class="tableCell cell_dat seisansei" style="width:60px;"><?php echo $array['SEISANSEI'] ?></div>
	</div>
	<?php endforeach; ?>
</div>
<!--/ A号機-->

<!-- B号機-->
<div class="resizable" style="height:50px;">
	<div class="graphArea">
		<div class="graphMajor">
			<div style="float:left;width:25%">0%</div>
			<div style="float:left;width:25%">25%</div>
			<div style="float:left;width:25%">50%</div>
			<div style="float:left;width:25%">75%</div>
		</div>
		<div style="float:left;width:20px;height:5px;font-size:8pt;">100%</div>
		<div class="graphMoji"><?php echo $sorterNmB ?></div>
		<div class="graph"><div class="graphAmount" style="width:<?php echo $BbatchShinchoku ?>%;background-image:url('../img/graph/tb04_br3.gif');"></div></div>
		<div class="graphNumArea" style="padding-top:3px;" ><?php echo $BbatchShinchokuSu ?><br><?php echo $BbutsuryoShinchokuSu ?></div>
	</div>
</div>
<div class="resizable" style="height:165px;">
	<div class="tableRowTitle row_ttl_sp_2" style="width:1480px;">
		<div class="tableCell cell_ttl_sp_2" style="width:60px;"><?php echo Configure::read("Bango") ?></div>
		<div class="tableCell cell_ttl_sp_2" style="width:180px;">バッチ名</div>
		<div class="tableCell cell_ttl_sp_2" style="width:80px;">SバッチNo</div>
		<div class="tableCell cell_ttl_sp_2" style="width:80px;">状態</div>
		<div style="float:left;width:360px;height:70px;">
			<div class="tableCell cell_ttl_1" style="width:350px;">集計管理No数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">総数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">未処理</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">処理中</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">処理済</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">棚欠品</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">棚欠済</div>
		</div>
		<div style="float:left;width:180px;height:70px;">
			<div class="tableCell cell_ttl_1" style="width:170px;">ピース数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">総数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">済数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">残数</div>
		</div>
		<div style="float:left;width:180px;height:70px;">
			<div class="tableCell cell_ttl_1" style="width:170px;">ソーター</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">総数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">済数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">残数</div>
		</div>
		<div class="tableCell cell_ttl_sp_2_2" style="width:60px;">稼動<br>スタッフ</div>
		<div style="float:left;width:180px;height:70px;">
			<div class="tableCell cell_ttl_1" style="width:170px;">時間</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">開始</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">終了</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">予測</div>
		</div>
		<div class="tableCell cell_ttl_sp_2" style="width:60px;">生産性</div>
	</div>
	<?php $count = 1; ?>
	<?php foreach ($sorterListB as $array): ?>
	<div class="tableRow row_dat" style="height:30px;width:1480px;">
	  	<div class="tableCell cell_dat bango" style="width:60px;"><?php echo $count++ ?></div>
		<div class="tableCell cell_dat nm" style="width:180px;"><?php echo $array['BATCH_NM'] ?></div>
		<div class="tableCell cell_dat kbn" style="width:80px;"><?php echo $array['S_BATCH_NO'] ?></div>
		<div class="tableCell cell_dat kbn" style="width:80px;"><?php echo $array['STATUS_NM'] ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['S_SOSU']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['S_MISYORI']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['S_SYORICHU']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['S_SYORIZUMI']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['S_TANAKETSU']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['S_TANAKETSUZUMI']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['PIECE_SOSU']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['PIECE_SUMI']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['PIECE_ZAN']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['SORTER_SOSU']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['SORTER_SUMI']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['SORTER_ZAN']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:60px;"><?php echo number_format($array['KADO_STAFF']) ?></div>
		<div class="tableCell cell_dat date" style="width:50px;"><?php echo $array['TIME_KAISHI'] ?></div> 
		<div class="tableCell cell_dat date" style="width:50px;"><?php echo $array['TIME_SYURYO'] ?></div>
		<div class="tableCell cell_dat date" style="width:50px;"><?php echo $array['TIME_YOSO'] ?></div>
		<div class="tableCell cell_dat seisansei" style="width:60px;"><?php echo $array['SEISANSEI'] ?></div>
	</div>
	<?php endforeach; ?>

</div>
<!--/ B号機-->

<!-- C号機-->
<div class="resizable" style="height:50px;">
	<div class="graphArea">
		<div class="graphMajor">
			<div style="float:left;width:25%">0%</div>
			<div style="float:left;width:25%">25%</div>
			<div style="float:left;width:25%">50%</div>
			<div style="float:left;width:25%">75%</div>
		</div>
		<div style="float:left;width:20px;height:5px;font-size:8pt;">100%</div>
		<div class="graphMoji"><?php echo $sorterNmC ?></div>
		<div class="graph"><div class="graphAmount" style="width:<?php echo $CbatchShinchoku ?>%;background-image:url('../img/graph/tb04_br3.gif');"></div></div>
		<div class="graphNumArea" style="padding-top:3px;" ><?php echo $CbatchShinchokuSu ?><br><?php echo $CbutsuryoShinchokuSu ?></div>
	</div>
</div>
<div class="resizable" style="height:165px;">
	<div class="tableRowTitle row_ttl_sp_2" style="width:1480px;">
		<div class="tableCell cell_ttl_sp_2" style="width:60px;"><?php echo Configure::read("Bango") ?></div>
		<div class="tableCell cell_ttl_sp_2" style="width:180px;">バッチ名</div>
		<div class="tableCell cell_ttl_sp_2" style="width:80px;">SバッチNo</div>
		<div class="tableCell cell_ttl_sp_2" style="width:80px;">状態</div>
		<div style="float:left;width:360px;height:70px;">
			<div class="tableCell cell_ttl_1" style="width:350px;">集計管理No数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">総数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">未処理</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">処理中</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">処理済</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">棚欠品</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">棚欠済</div>
		</div>
		<div style="float:left;width:180px;height:70px;">
			<div class="tableCell cell_ttl_1" style="width:170px;">ピース数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">総数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">済数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">残数</div>
		</div>
		<div style="float:left;width:180px;height:70px;">
			<div class="tableCell cell_ttl_1" style="width:170px;">ソーター</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">総数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">済数</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">残数</div>
		</div>
		<div class="tableCell cell_ttl_sp_2_2" style="width:60px;">稼動<br>スタッフ</div>
		<div style="float:left;width:180px;height:70px;">
			<div class="tableCell cell_ttl_1" style="width:170px;">時間</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">開始</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">終了</div>
			<div class="tableCell cell_ttl_1" style="width:50px;">予測</div>
		</div>
		<div class="tableCell cell_ttl_sp_2" style="width:60px;">生産性</div>
	</div>
	<?php $count = 1; ?>
	<?php foreach ($sorterListC as $array): ?>
	<div class="tableRow row_dat" style="height:30px;width:1480px;">
	  	<div class="tableCell cell_dat bango" style="width:60px;"><?php echo $count++ ?></div>
		<div class="tableCell cell_dat nm" style="width:180px;"><?php echo $array['BATCH_NM'] ?></div>
		<div class="tableCell cell_dat kbn" style="width:80px;"><?php echo $array['S_BATCH_NO'] ?></div>
		<div class="tableCell cell_dat kbn" style="width:80px;"><?php echo $array['STATUS_NM'] ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['S_SOSU']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['S_MISYORI']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['S_SYORICHU']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['S_SYORIZUMI']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['S_TANAKETSU']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['S_TANAKETSUZUMI']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['PIECE_SOSU']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['PIECE_SUMI']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['PIECE_ZAN']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['SORTER_SOSU']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['SORTER_SUMI']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo number_format($array['SORTER_ZAN']) ?></div>
		<div class="tableCell cell_dat cnt" style="width:60px;"><?php echo number_format($array['KADO_STAFF']) ?></div>
		<div class="tableCell cell_dat date" style="width:50px;"><?php echo $array['TIME_KAISHI'] ?></div> 
		<div class="tableCell cell_dat date" style="width:50px;"><?php echo $array['TIME_SYURYO'] ?></div>
		<div class="tableCell cell_dat date" style="width:50px;"><?php echo $array['TIME_YOSO'] ?></div>
		<div class="tableCell cell_dat seisansei" style="width:60px;"><?php echo $array['SEISANSEI'] ?></div>
	</div>
	<?php endforeach; ?>
</div>
<!--/ C号機-->

</div>
<!--/ #wrapper-->

