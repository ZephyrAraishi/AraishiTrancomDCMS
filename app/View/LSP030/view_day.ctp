<hr>

<div align="left">
	<b>【日別詳細テーブル】</b>
</div>
<br>
<div align="left">
	<b><?php echo $viewYmdLbl ?></b>
</div>

<div class="resizable" style="height: 500px; max-width:1500px;">
<?php 
	$tileWidth = 30;
	$tableSize = (150 * 2) + (70 * 8) + ($tileWidth * ($endTime - $startTime + 1)); 
?>
<table border="1" width="<?php echo $tableSize ?>px" class="glst">
	<colgroup>
		<col width="150px">
		<col width="150px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
	<?php for ($i = $startTime; $i <= $endTime; $i++) { ?>
		<col width="<?php echo $tileWidth ?>px">
	<?php } ?>
	</colgroup>
	<tbody>
	<tr style="height:30px">
		<th rowspan="2">大分類</th>
		<th rowspan="2">中分類</th>
		<th colspan="2">物量</th>
		<th colspan="2">人数</th>
		<th colspan="2">開始時刻</th>
		<th colspan="2">終了時刻</th>
	<?php for ($i = $startTime; $i <= $endTime; $i++) { ?>
		<th rowspan="2" class="glst_t"><?php echo $i ?><br>時</th>
	<?php } ?>
	</tr>
	<tr>
		<th>予測</th>
		<th>実績</th>
		<th>予測</th>
		<th>実績</th>
		<th>予測</th>
		<th>実績</th>
		<th>予測</th>
		<th>実績</th>
	</tr>
<?php foreach ($dailyData as $array): ?>
	<tr>
	<?php if (isset($array["BNRI_DAI_COUNT"])) { ?>
		<th rowspan="<?php echo $array["BNRI_DAI_COUNT"] ?>" style="text-align:left;padding-left:5px"><?php echo $array["BNRI_DAI_NM"] ?></th>
	<?php } ?>
		<th style="text-align:left;padding-left:5px"><?php echo $array["BNRI_CYU_NM"] ?></th>
		<td>
			<?php if ($lydExist == TRUE) { ?>
				<?php if ($array['KBN_URIAGE'] == "01") { ?>
					固定
				<?php } else { ?>
					<?php echo empty($array["LYD_BUTURYO"]) ? "" : number_format($array["LYD_BUTURYO"]) ?>
				<?php } ?>
			<?php } ?>
		</td>
		<td>
			<?php if ($ljdExist == TRUE) { ?>
				<?php if ($array['KBN_URIAGE'] == "01") { ?>
					固定
				<?php } else { ?>
					<?php echo empty($array["LJD_BUTURYO"]) ? "" : number_format($array["LJD_BUTURYO"]) ?>
				<?php } ?>
			<?php } ?>
		</td>
		<td><?php echo !isset($array["LYD_NINZU"]) ? ($lydExist == TRUE ? "0" : "") : number_format($array["LYD_NINZU"]) ?></td>
		<td><?php echo !isset($array["LJD_NINZU"]) ? ($ljdExist == TRUE ? "0" : "") : number_format($array["LJD_NINZU"]) ?></td>
		<td><?php echo !isset($array["LYD_TIME_START"]) || $array["LYD_TIME_START"] == "0000" ? "" : substr($array["LYD_TIME_START"], 0, 2).":".substr($array["LYD_TIME_START"], 2, 2) ?></td>
		<td><?php echo !isset($array["LJD_TIME_START"]) || $array["LJD_TIME_START"] == "0000" ? "" : substr($array["LJD_TIME_START"], 0, 2).":".substr($array["LJD_TIME_START"], 2, 2) ?></td>
		<td><?php echo !isset($array["LYD_TIME_END"]) || $array["LYD_TIME_END"] == "0000" ? "" : substr($array["LYD_TIME_END"], 0, 2).":".substr($array["LYD_TIME_END"], 2, 2) ?></td>
		<td><?php echo !isset($array["LJD_TIME_END"]) || $array["LJD_TIME_END"] == "0000" ? "" : substr($array["LJD_TIME_END"], 0, 2).":".substr($array["LJD_TIME_END"], 2, 2) ?></td>
	<?php for ($i = $startTime; $i <= $endTime; $i++) { ?>
		<td class="gtd">
			<?php $info = getBarInfo($i, $array["LYD_TIME_START"], $array["LYD_TIME_END"]); ?>
			<?php if ($info["size"] != 0) { ?>
				<span class="barY" style="margin-left:<?php echo $info["margin"] ?>px; width:<?php echo $info["size"] ?>px;">&nbsp;</span>
			<?php } else { ?>
				<span class="barN" style="margin-left:0px;width:30px">&nbsp;</span>
			<?php } ?>
			
			<?php $info = getBarInfo($i, $array["LJD_TIME_START"], $array["LJD_TIME_END"]); ?>
			<?php if ($info["size"] != 0) { ?>
				<span class="barJ" style="margin-left:<?php echo $info["margin"] ?>px; width:<?php echo $info["size"] ?>px;">&nbsp;</span>
			<?php } else { ?>
				<span class="barN" style="margin-left:0px;width:30px">&nbsp;</span>
			<?php } ?>
		</td>
	<?php } ?>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>
</div>

<?php 

function getBarInfo($hour, $start, $end) {

	$tileWidth = 30;
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