<div style="padding-left:10px;width:280px">
	<table width="260px">
		<colgroup>
			<col width="100px">
			<col width="60px">
			<col width="100px">
		</colgroup>
		<tr>
			<td align="left">時間生産性合計</td>
			<td align="right"><?php echo number_format($sumSeisansei, 1) ?></td>
			<td align="right"><?php echo $tani ?>／ｈ</td>
		</tr>
		<tr>
			<td align="left">時間単価合計</td>
			<td align="right"><?php echo number_format($sumTanka) ?></td>
			<td align="right">円／ｈ</td>
		</tr>
	</table>
</div>

<div style="overflow-y:auto;" align="center">
<input type="hidden" name="staffCnt" id=staffCnt value="<?php echo $staffCnt ?>" >
<table border="1" width="730px" class="lst">
	<colgroup>
		<col width="50px"/>
		<col width="140px"/>
		<col width="190px"/>
		<col width="100px"/>
		<col width="100px"/>
		<col width="80px"/>
		<col width="120px"/>
	</colgroup>
	<tr>
		<th><?php echo Configure::read('Bango'); ?></th>
		<th>氏名</th>
		<th>所属</th>
		<th>開始時間</th>
		<th>終了時間</th>
		<th>単価</th>
		<th>当月生産性</th>
	</tr>
	<?php for ($i = 0; $i < count($staffData); $i++) { ?>
	<tr>
		<td><?php echo $i + 1 ?></td>
		<td>
			<?php echo $staffData[$i]["DEL_FLG"] == '1' ? '×' : '' ?>
			<?php echo $staffData[$i]["STAFF_NM"] ?>	
		</td>
		<td><?php echo $staffData[$i]["KAISYA_NM"] ?></td>
		<td><?php echo substr($staffData[$i]["TIME_SYUGYO_ST"], 0, 2).":".substr($staffData[$i]["TIME_SYUGYO_ST"], 2, 2) ?></td>
		<td><?php echo substr($staffData[$i]["TIME_SYUGYO_ED"], 0, 2).":".substr($staffData[$i]["TIME_SYUGYO_ED"], 2, 2) ?></td>
		<td><?php echo empty($staffData[$i]["KIN_SYOTEI"]) ? "" : number_format($staffData[$i]["KIN_SYOTEI"]) ?></td>
		<td><?php echo empty($staffData[$i]["SEISANSEI_TIME_BUTURYO"]) ? "" : number_format($staffData[$i]["SEISANSEI_TIME_BUTURYO"], 1) ?></td>
	</tr>
	<?php } ?>
</table>
</div>