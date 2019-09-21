<?php
	echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSLSP020_POPUP','DCMSLSP020_UPDATE','DCMSLSP020_RELOAD'), 
			array('inline'=>false));
        echo $this->Html->css(array('themes/default','themes/alert','themes/alphacube','calendar','DCMSLSP'), false, array('inline'=>false));
?>
<div id="wrapper_s" style="width:95%; max-width:2000px;">


<?php echo $this->Form->create('LSP020Model', array('url' => '/LSP020','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?> 
<input type="hidden" id="monthViewFlg" name="monthViewFlg" value="<?php echo $monthViewFlg ?>">
<input type="hidden" id="viewYmd" name="viewYmd" value="<?php echo $ymd ?>">

<div align="left">
	<b>【予測シュミレーション】</b>
</div>
<div>
	<table class="field">
		<tbody>
		<tr>
			<td>シュミレーション日</td>
			<td>
				<select name="startYmd" id="startYmd">
					<?php foreach ($startYmdList as $key => $value): ?>
						<?php 
							$selected = "";
							if ($value == $selectedStartYmd) {
								$selected = "selected";
							}
						?>
						<option value="<?php echo $value ?>" <?php echo $selected ?>><?php echo substr($value, 0, 4) ?>/<?php echo substr($value, 4, 2) ?>/<?php echo substr($value, 6, 2) ?></option>
					<?php endforeach; ?>
				</select>
				&nbsp;～&nbsp;
				<select name="endYmd" id="endYmd">
					<?php foreach ($endYmdList as $key => $value): ?>
						<?php 
							$selected = "";
							if ($value == $selectedEndYmd) {
								$selected = "selected";
							}
						?>
						<option value="<?php echo $value ?>"  <?php echo $selected ?>><?php echo substr($value, 0, 4) ?>/<?php echo substr($value, 4, 2) ?>/<?php echo substr($value, 6, 2) ?></option>
					<?php endforeach; ?>
				</select>
			</td>
			<td>
				<input type="button" value="表示" class="btn" id="viewButton">
			</td>
		</tr>
		</tbody>
	</table>
</div>
<?php echo $this->Form->end() ?>

<?php echo $this->Form->create('LSP020Model', array('url' => '/LSP020/downloadCsv','type'=>'post', 'inputDefaults' => array('label' => false,'div' => false))) ?>
	<input type="hidden" id="csvStartYmd" name="csvStartYmd" value="">
	<input type="hidden" id="csvEndYmd" name="csvEndYmd" value="">
<?php echo $this->Form->end() ?>

<div id="month_table">
	<?php if ($monthViewFlg == "1") {?>
	
	<hr>
	
	<div style="height:20px;max-width:1500px;text-align:left;">
		<b>【月別サマリーテーブル】</b>
	</div>
	
	<div class="resizable" style="height:40px;max-width:1500px;text-align:right;overflow:hidden;">
		<input type="button" value="CSV出力" class="btn" id="csvButton" >
	</div>
		
	<div class="resizable" style="height: 200px; max-width:1500px;" id="lstTable">
		<?php $tableSize = 125 + (count($monthlyData) * 115) + 120; ?>
		<table border="1" width="<?php echo $tableSize ?>px" class="lst" id="monthTable">
			<colgroup>
				<col width="125px">
			<?php foreach ($monthlyData as $array): ?>
				<col width="115px">
			<?php endforeach; ?>
				<col width="120px">
			</colgroup>
			<tbody>
			<tr>
				<th height="34px">　</th>
			<?php foreach ($monthlyData as $array): ?>
				<th><?php echo $array["dayLbl"] ?></th>
			<?php endforeach; ?>
				<th>合計</th>
			</tr>
			<tr>
				<th style="text-align:left">売上金額</th>
			<?php foreach ($monthlyData as $array): ?>
				<td id="<?php echo $array["day"] ?>_1" class="tableCol"><?php echo !isset($array["data"]["URIAGE"]) ? "" : number_format($array["data"]["URIAGE"]) ?></td>
			<?php endforeach; ?>
				<td id="99999999_1" class="tableCol"><?php echo number_format($sumUriage) ?></td>
			</tr>
			<tr>
				<th style="text-align:left">原価</th>
			<?php foreach ($monthlyData as $array): ?>
				<td id="<?php echo $array["day"] ?>_2" class="tableCol"><?php echo !isset($array["data"]["GENKA"]) ? "" : number_format($array["data"]["GENKA"]) ?></td>
			<?php endforeach; ?>
				<td id="99999999_2" class="tableCol"><?php echo number_format($sumGenka) ?></td>
			</tr>
			<tr>
				<th style="text-align:left">粗利</th>
			<?php foreach ($monthlyData as $array): ?>
				<td id="<?php echo $array["day"] ?>_3" class="tableCol"><?php echo !isset($array["data"]["ARARI"]) ? "" : number_format($array["data"]["ARARI"]) ?></td>
			<?php endforeach; ?>
				<td id="99999999_3" class="tableCol"><?php echo number_format($sumArari) ?></td>
			</tr>
			<tr>
				<th style="text-align:left">合計人数</th>
			<?php foreach ($monthlyData as $array): ?>
				<td id="<?php echo $array["day"] ?>_4" class="tableCol"><?php echo !isset($array["data"]["NINZU"]) ? "" : number_format($array["data"]["NINZU"]) ?></td>
			<?php endforeach; ?>
				<td id="99999999_4" class="tableCol"><?php echo number_format($sumNinzu) ?></td>
			</tr>
			<tr>
				<th style="text-align:left">合計時間</th>
			<?php foreach ($monthlyData as $array): ?>
				<td id="<?php echo $array["day"] ?>_5" class="tableCol"><?php echo !isset($array["data"]["JIKAN"]) ? "" : number_format($array["data"]["JIKAN"], 2) ?></td>
			<?php endforeach; ?>
				<td id="99999999_5" class="tableCol"><?php echo number_format($sumJikan, 2) ?></td>
			</tr>
			<?php foreach ($monthlyData as $array): ?>
				<input type="hidden" id="<?php echo $array["day"] ?>" value="<?php echo $array["exist"] ?>">
			<?php endforeach; ?>
			<tbody>
		</table>
	</div>
	
	<?php } ?>
</div>

<div id="day_table">
	<?php if (!empty($ymd)) {?>
		<?php echo $this->element("../LSP020/view_day_input")?>
	<?php } ?>
</div>
	
</div>