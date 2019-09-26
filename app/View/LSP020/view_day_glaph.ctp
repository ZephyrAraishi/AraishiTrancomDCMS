<hr>
<input type="hidden" id="regCnt" value="<?php echo $regCnt ?>">
<div align="left">
	<b>【日別詳細テーブル】</b>
	<div align="right">
		<ul class="toggle-list">
			<li><a><label id="inputButton" style="width:100px;text-align:center">予測入力</label></a></li>
			<li><label id="glaphButton" class="selected" style="width:100px;text-align:center" >グラフ表示</label></li>
		</ul>
	</div>
</div>

<div style="width:1150px; height:35px">
	<table width="100%">
		<tr>
			<td><b><?php echo $viewYmdLbl ?></b></td>
		</tr>
	</table>
</div>

<div class="resizable" style="height: 500px; max-width:1500px;">
<?php 
	$tileWidth = 20;
	$tableSize = (150 * 2) + (80 * 2) + (70 * 4) + ($tileWidth * ($endTime - $startTime + 1)); 
?>
<table border="1" width="<?php echo $tableSize ?>px" class="glst">
	<colgroup>
		<col width="150px">
		<col width="150px">
		<col width="80px">
		<col width="80px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
<?php for ($i = $startTime; $i <= $endTime; $i++) { ?>
	<col width="<?php echo $tileWidth ?>px">
<?php } ?>
	</colgroup>
	<tbody>
	<tr height="50px">
		<th>大分類</th>
		<th>中分類</th>
		<th>物量</th>
		<th>人数</th>
		<th>開始</th>
		<th>終了</th>
		<th>予測</th>
		<th>警告</th>
	<?php for ($i = $startTime; $i <= $endTime; $i++) { ?>
		<th class="glst_t"><?php echo $i ?><br>時</th>
	<?php } ?>
	</tr>
	
<?php foreach ($dailyData as $array): ?>
	<tr>
	<?php if (isset($array["K_BNRI_CYU_COUNT"])) { ?>
		<th rowspan="<?php echo $array["K_BNRI_CYU_COUNT"] ?>" style="text-align: left;padding-left: 5px;"><?php echo $array["K_BNRI_DAI_NM"] ?></th>
	<?php } ?>
		<th style="text-align: left;padding-left: 5px;"><?php echo $array["K_BNRI_CYU_NM"] ?></th>
		<td>
			<?php if ($array['K_KBN_URIAGE'] == "01") { ?>
				固定
				<input type="hidden" name="buturyo" value="0">
			<?php } else { ?>
				<?php echo empty($array["K_BUTURYO"]) ? "" : number_format($array["K_BUTURYO"]) ?>
			<?php } ?>
		</td>
		<td><?php echo number_format($array["K_NINZU"]) ?></td>
		<td><?php echo empty($array["K_TIME_START"]) || $array["K_TIME_START"] == "0000" ? "" : substr($array["K_TIME_START"], 0, 2).":".substr($array["K_TIME_START"], 2, 2) ?></td>
		<td><?php echo empty($array["K_TIME_END"]) || $array["K_TIME_END"] == "0000" ? "" : substr($array["K_TIME_END"], 0, 2).":".substr($array["K_TIME_END"], 2, 2) ?></td>
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
				<?php } else { ?>
					<font color="red"><?php echo number_format(abs($array["K_KEIKOKU_BUTURYO"])) ?></font>
				<?php } ?>	
			<?php } ?>
		</td>
	<?php for ($i = $startTime; $i <= $endTime; $i++) { ?>
		<td class="gtd">
			<?php $info = getBarInfo($i, $array["K_TIME_START"], $array["K_TIME_END"]); ?>
			<?php if ($info["size"] != 0) { ?>
				<span class="barY" style="margin-left:<?php echo $info["margin"] ?>px; width:<?php echo $info["size"] ?>px;">&nbsp;</span>
			<?php } else { ?>
				<span class="barN" style="margin-left:0px;width:100%">&nbsp;</span>
			<?php } ?>
			
			<?php $info = getBarInfo($i, $array["K_YOSOKU_TIME_START"], $array["K_YOSOKU_TIME_END"]); ?>
			<?php if ($info["size"] != 0) { ?>
				<span class="barJ" style="margin-left:<?php echo $info["margin"] ?>px; width:<?php echo $info["size"] ?>px;">&nbsp;</span>
			<?php } else { ?>
				<span class="barN" style="margin-left:0px;width:100%">&nbsp;</span>
			<?php } ?>
		</td>
	<?php } ?>
	</tr>
<?php endforeach; ?>
	<tbody>
</table>

<?php

function getBarInfo($hour, $start, $end) {
	
	$tileWidth = 20;
	$size = 0;
	$margin = 0;
	
	$from = $hour * 60;
	$to = $from + 60;
	$st = 0;
	$ed = 0;
	
	if (!empty($start) && !empty($end)) {
		$st = (substr($start, 0, 2) * 60) + (substr($start, 2, 2));
		$ed = (substr($end, 0, 2) * 60) + (substr($end, 2, 2));
	}
	
	if ($st < $to && $from < $ed) {
		$size = $tileWidth;
		if ($from < $st) {
			$margin = round($tileWidth * (($st - $from) / 60));
			$size -= $margin;
		}
		if ($ed < $to) {
			$size -= round($tileWidth * (($to - $ed) / 60));
		}
	}
	
	$info = array (
			"size"  => $size,
			"margin" => $margin
	);

	return $info;
}
?>
