<hr>
<input type="hidden" id="ymd" value="<?php echo $ymd ?>">
<input type="hidden" id="regCnt" value="<?php echo $regCnt ?>"> 
<div align="left">
	<b>【日別詳細テーブル】</b>
<?php if ($regCnt != 0) { ?>
	<div align="right">
		<ul class="toggle-list">
			<li><label id="inputButton" class="selected" style="width:100px;text-align:center">予測入力</label></li>
			<li><a><label id="glaphButton" style="width:100px;text-align:center" >グラフ表示</label></a></li>
		</ul>
	</div>
<?php } ?>
</div>

<?php if ($regCnt != 0) { ?>
	<div class="resizable" style="height:58px;max-width:1500px;overflow-y:hidden;">
		<table width="100%">
			<tr>
				<td align="left" width="745px" valign="bottom"><b><?php echo $viewYmdLbl ?></b></td>
				<td align="left" valign="bottom">
					<span style="font-size:9pt" >上段：出荷指示物量<br>中段：作業予定物量<br>下段：生産性合計</span>
				</td>
				<td align="right" valign="bottom"><input type="button" value="更新" class="btn" id="updateButton" onclick="clickUploadButton()"></td>
			</tr>
		</table>
	</div>
<?php } else { ?>
<div class="resizable" style="height:35px;max-width:1500px;overflow-y:hidden;">
	<table width="100%">
		<tr>
			<td align="left" width="735px" valign="bottom"><b><?php echo $viewYmdLbl ?></b></td>
			<td align="right" valign="bottom"><input type="button" value="更新" class="btn" id="updateButton" onclick="clickUploadButton()"></td>
		</tr>
	</table>
</div>
<?php } ?>


<div class="resizable" style="height: 500px; max-width:1500px;">
<?php 
	$tileWidth = 80;
	$tableSize = (120 * 2) + 90 + 100 + (80 * 4) + ($tileWidth * ($endTime - $startTime + 1)); 
?>
<table border="1" width="<?php echo $tableSize ?>px" class="lst" id="inputTable">
	<colgroup>
		<col width="120px">
		<col width="120px">
		<col width="90px">
		<col width="100px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
	<?php foreach ($anbunRate as $array): ?>
		<col width="<?php echo $tileWidth ?>px">
	<?php endforeach; ?>
	</colgroup>
	<tbody>
	<tr class="anbunRow">
		<th colspan="8" style="text-align:left"></th>
	<?php foreach ($anbunRate as $array): ?>
		<td class="inputCell">
			<div class="hiddenData"><?php echo $array['YOSOKU_TIME'] ?></div>
			<input type="text" name="rate_<?php echo $array["YOSOKU_TIME"] ?>" class="han" style="width:25px" maxlength="3" value="<?php echo $array["ANBUN_RATE"] ?>">&nbsp;%
		</td>
	<?php endforeach; ?>
	</tr>
	<tr>
		<th>大分類</th>
		<th>中分類</th>
		<th>物量</th>
		<th>人数</th>
		<th>開始</th>
		<th>終了</th>
		<th>予測</th>
		<th>警告</th>
	<?php foreach ($anbunRate as $array): ?>
		<th><?php echo number_format($array["YOSOKU_TIME"]) ?>時</th>
	<?php endforeach; ?>
	</tr>
<?php foreach ($dailyData as $array): ?>
	<tr class="simulationRow">
	<?php if (isset($array["K_BNRI_CYU_COUNT"]) && !empty($array["K_BNRI_CYU_COUNT"])) { ?>
		<th rowspan="<?php echo $array["K_BNRI_CYU_COUNT"] ?>" style="text-align:left"><?php echo $array["K_BNRI_DAI_NM"] ?></th>
	<?php } ?>
		<th style="text-align:left">
			<div class="hiddenData"><?php echo $array['K_BNRI_DAI_CD'] ?></div>
			<div class="hiddenData"><?php echo $array['K_BNRI_DAI_NM'] ?></div>
			<div class="hiddenData"><?php echo $array['K_BNRI_CYU_CD'] ?></div>
			<div class="hiddenData"><?php echo $array['K_BNRI_CYU_NM'] ?></div>
			<div class="hiddenData"><?php echo $array['K_KBN_URIAGE'] ?></div>
			<div class="hiddenData"><?php echo isset($array["K_BNRI_CYU_COUNT"]) ? $array['K_BNRI_CYU_COUNT'] : '' ?></div>
			<div class="hiddenData" id="nh_<?php echo $array["K_BNRI_DAI_CD"] ?>_<?php echo $array["K_BNRI_CYU_CD"] ?>"><?php echo $array['K_NINZU'] ?></div>
			<?php echo $array["K_BNRI_CYU_NM"] ?>
		</th>
		<td class="inputCell">
			<?php if ($array['K_KBN_URIAGE'] == "01") { ?>
				固定
				<input type="hidden" name="buturyo" value="0">
			<?php } else { ?>
				<input type="text" style="width:70px" class="han" name="buturyo" maxlength="8" value="<?php echo $array["K_BUTURYO"] ?>">
			<?php } ?>
		</td>
		<td>
			<div style="float:right;"><span id="ni_<?php echo $array["K_BNRI_DAI_CD"] ?>_<?php echo $array["K_BNRI_CYU_CD"] ?>"><?php echo number_format($array["K_NINZU"]) ?></span>&nbsp;
			<input type="button" value="設定" onclick="clickStaffSetupButton('<?php echo $array["K_BNRI_DAI_CD"] ?>', '<?php echo $array["K_BNRI_CYU_CD"] ?>')" style="width:50px; height:25px;">
			</div>
		</td>
		<td class="inputCell"><input type="text" style="width:50px" name="timeStart" class="han" maxlength="4" value="<?php echo $array["K_TIME_START"] == "0000" ? "" : $array["K_TIME_START"] ?>"></td>
		<td class="inputCell"><input type="text" style="width:50px" name="timeEnd" class="han" maxlength="4" value="<?php echo $array["K_TIME_END"] == "0000" ? "" : $array["K_TIME_END"] ?>"></td>
		<td>
			<?php if (!empty($array["K_YOSOKU_TIME_END"])) { ?>
				<?php if ($array["K_YOSOKU_TIME_END"] == "0000") { ?>
					<font color="red">計測不能</font>
				<?php } else { ?>
					<?php echo substr($array["K_YOSOKU_TIME_END"], 0, 2).":".substr($array["K_YOSOKU_TIME_END"], 2, 2) ?>
				<?php } ?>
			<?php } ?>
		</td>
		<td>
		<?php if (!empty($array["K_KEIKOKU_BUTURYO"])) { ?>
			<?php if (0 < $array["K_KEIKOKU_BUTURYO"]) { ?>
				<font color="blue"><?php echo number_format($array["K_KEIKOKU_BUTURYO"]) ?></font>
			<?php } else if ($array["K_KEIKOKU_BUTURYO"] < 0)  { ?>
				<font color="red"><?php echo number_format(abs($array["K_KEIKOKU_BUTURYO"])) ?></font>
			<?php } else { ?>
				<?php echo $array["K_KEIKOKU_BUTURYO"] ?>
			<?php } ?>	
		<?php } ?>
		</td>
		<?php if (!empty($array["K_TIME_SIMULATION"])) { ?>
			<?php $tm = 7;  foreach ($array["K_TIME_SIMULATION"] as $info): ?>
			<td style="text-align:right">
				<div style="background-color:#dedede;font-size:10px;color:#222;text-align:left; padding-left: 2px;"><?php echo $tm++; ?></div>
				<?php echo number_format($info["T_SHIJI_BUTURYO"]) ?><div style="width:100%;height:1px;background-color:#bbb; overflow:hidden;"></div>
				<?php echo number_format($info["T_SAGYO_BUTURYO"]) ?><div style="width:100%;height:1px;background-color:#bbb; overflow:hidden;"></div>
				<?php echo number_format($info["T_SEISANSEI"]) ?>
			</td>
			<?php endforeach; ?>
		<?php } else { ?>
			<?php foreach ($anbunRate as $array): ?>
			<td>
				&nbsp;<br>&nbsp;<br>&nbsp;
			</td>
			<?php endforeach; ?>
		<?php } ?>
	</tr>
<?php endforeach; ?>
	<tbody>
</table>
</div>