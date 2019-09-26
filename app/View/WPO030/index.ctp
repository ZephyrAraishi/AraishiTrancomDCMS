<?php
echo $this->Html->script(
		array(
				'effects','window','base64','DCMSMessage','DCMSValidation','DCMSCommon',
				'DCMSWPO030_RELOAD','DCMSWPO030_S_RANK_POPUP','DCMSWPO030_S_KIHON_POPUP',
				'DCMSWPO030_S_RANK_UPDATE','DCMSWPO030_S_KIHON_UPDATE'),
				array('inline'=>false));
echo $this->Html->css(array('themes/default','themes/alert','themes/alphacube','DCMSWPO'), false, array('inline'=>false));
?>
<div id="wrapper_s">


<div class="tableBlock">
<!-- メインテーブル(ランク)-->
<div style="margin-bottom: 20px;float:left;" id="lstTableRank">
	<!-- 更新ボタン-->
	<div align="right" style="width:100%;"><input type="button" value="更新" style="width:120px;height:30px;" id="updateButton1" ></div>
	<!--/ 更新ボタン-->
	<div class="resizable_s" style="height:500px;max-width:420px;" id="lstTable">
		<div class="tableRowTitle row_ttl_1" style="width:400px;">
			<div class="tableCell cell_ttl_1" style="width:60px;" ><?php echo Configure::read("Bango") ?></div>
			<div class="tableCell cell_ttl_1" style="width:60px;" >ランク</div>
			<div class="tableCell cell_ttl_1" style="width:120px;" >アイテム数</div>
			<div class="tableCell cell_ttl_1" style="width:120px;" >ピース数</div>
		</div>
<?php foreach ($rankLsts as $key1 => $val1): ?>
		<div class="tableRow row_dat" style="width:400px;">
			<div class="tableCell cell_dat bango" style="width:60px;"><?php echo $key1+1 ?></div>
			<div class="tableCell cell_dat kbn" style="width:60px;"><?php echo $val1['RANK'] ?></div>
			<div class="tableCell cell_dat kbn" style="width:120px;"><?php echo "{$val1['SURYO_ITEM_FR']}～{$val1['SURYO_ITEM_TO']}" ?></div>
			<div class="tableCell cell_dat kbn" style="width:120px;"><?php echo "{$val1['SURYO_PIECE_FR']}～{$val1['SURYO_PIECE_TO']}" ?></div>
			<div class="hiddenData">0</div>
			<div class="hiddenData"><?php echo $key1+1 ?></div>
			<div class="hiddenData"><?php echo $val1['RANK'] ?></div>
			<div class="hiddenData"><?php echo $val1['SURYO_ITEM_FR'] ?></div>
			<div class="hiddenData"><?php echo $val1['SURYO_ITEM_TO'] ?></div>
			<div class="hiddenData"><?php echo $val1['SURYO_PIECE_FR'] ?></div>
			<div class="hiddenData"><?php echo $val1['SURYO_PIECE_TO'] ?></div>
		</div>
<?php endforeach; ?>
	</div>
</div>
<!--/ メインテーブル(ランク)-->

<!-- スペース-->
<div style="margin-bottom: 20px;width:10px;float:left;"></div>
<!--/ スペース-->

<!-- メインテーブル(スタッフ)-->
<div style="margin-bottom:20px;float:left;" id="lstTableStaff">
	<!-- 更新ボタン-->
	<div align="right" style="width:100%;"><input type="button" value="更新" style="width:120px;height:30px;" id="updateButton2" ></div>
	<!--/ 更新ボタン-->
	<div class="resizable_s" style="height: 800px;max-width:720px;" id="lstTable">
		<div class="tableRowTitle row_ttl_1" style="width:700px;">
			<div class="tableCell cell_ttl_1" style="width:60px;" ><?php echo Configure::read("Bango") ?></div>
			<div class="tableCell cell_ttl_1" style="width:110px;" >スタッフコード</div>
			<div class="tableCell cell_ttl_1" style="width:100px;" >スタッフ名</div>
			<div class="tableCell cell_ttl_1" style="width:100px;" >当日生産性</div>
			<div class="tableCell cell_ttl_1" style="width:100px;" >実績生産性</div>
			<div class="tableCell cell_ttl_1" style="width:60px;" >ランク</div>
			<div class="tableCell cell_ttl_1" style="width:100px;" >変更ランク</div>
		</div>
<?php foreach ($staffLsts as $key2 => $val2): ?>
		<div class="tableRow row_dat" style="width:700px;">
			<div class="tableCell cell_dat bango" style="width:60px;"><?php echo $key2+1 ?></div>
			<div class="tableCell cell_dat kbn" style="width:110px;"><?php echo $val2['STAFF_CD'] ?></div>
			<div class="tableCell cell_dat nm" style="width:100px;"><?php echo $val2['STAFF_NM'] ?></div>
			<div class="tableCell cell_dat seisansei" style="width:100px;"><?php if(!empty($val2['TOD_PROD'])) echo number_format($val2['TOD_PROD'],1) ?></div>
			<div class="tableCell cell_dat seisansei" style="width:100px;"><?php if(!empty($val2['ACT_PROD'])) echo number_format($val2['ACT_PROD'],1) ?></div>
			<div class="tableCell cell_dat kbn" style="width:60px;"><?php echo $val2['RANK'] ?></div>
			<div class="tableCell cell_dat kbn" style="width:100px;"><?php echo $val2['RANK_UPD'] ?></div>
			<div class="hiddenData">0</div>
		</div>
<?php endforeach; ?>
	</div>
</div>
<!--/ メインテーブル(スタッフ)-->
</div>

</div>
<!--/ #wrapper-->
<div id="timeStamp1" style="display:none;" ><?php echo $timestamp1; ?></div>
<div id="timeStamp2" style="display:none;" ><?php echo $timestamp2; ?></div>