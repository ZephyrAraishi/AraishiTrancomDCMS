<?php
	echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSLSP030_RELOAD','DCMSLSP030_CALENDAR'), 
			array('inline'=>false));
        echo $this->Html->css(array('themes/default','themes/alert','themes/alphacube','calendar','DCMSLSP'), false, array('inline'=>false));
?>

<div id="wrapper_s" style="width:95%; max-width:2000px;">

<?php echo $this->Form->create('LSP030Model', array('url' => '/LSP030','type'=>'post', 'inputDefaults' => array('label' => false,'div' => false))) ?> 
<input type="hidden" id="monthViewFlg" name="monthViewFlg" value="<?php echo $monthViewFlg ?>">

<div align="left">
	<b>【作業実績照会】</b>
</div>
<div>
	<table class="field">
		<tbody>
		<tr>
			<td>実績日付</td>
			<td>
				<input id="startYmd" name="startYmd" maxlength="8" class="calendar han" type="text" style="width:80px;height:20px" value="<?php echo $startYmd ?>">
				&nbsp;～&nbsp;
				<input id="endYmd" name="endYmd" maxlength="8" class="calendar han" type="text" style="width:80px;height:20px" value="<?php echo $endYmd ?>">
			</td>
			<td>
				<input type="button" value="表示" style="width:120px;height:30px;" id="viewButton" >
			</td>
		</tr>
		</tbody>
	</table>
</div>
<?php echo $this->Form->end() ?>

<?php echo $this->Form->create('LSP030Model', array('url' => '/LSP030/downloadCsv','type'=>'post', 'inputDefaults' => array('label' => false,'div' => false))) ?>
	<input type="hidden" id="csvStartYmd" name="csvStartYmd" value="">
	<input type="hidden" id="csvEndYmd" name="csvEndYmd" value="">
<?php echo $this->Form->end() ?>

<div id="month_table">

<?php if ($monthViewFlg == "1") {?>
<hr>

<div>
	
	<div style="height:20px;max-width:1500px;text-align:left;">
		<b>【月別サマリーテーブル】</b>
	</div>
	
	<div class="resizable" style="height:40px;max-width:1500px;text-align:right;overflow:hidden;">
		<input type="button" value="CSV出力" class="btn" id="csvButton" >
	</div>

	<div class="resizable" style="height: 215px; max-width:1500px;" id="lstTable">
		<?php $tableSize = 140 + (count($monthlyData) * (100 * 2)) + (120 * 2); ?>
		<table border="1" width="<?php echo $tableSize ?>px" class="lst" id="monthTable">
			<colgroup>
				<col width="140px">
			<?php foreach ($monthlyData as $array): ?>
				<col width="100px">
				<col width="100px">
			<?php endforeach; ?>
				<col width="120px">
				<col width="120px">
			</colgroup>
			<tbody>
			<tr>
				<th rowspan="2">　</th>
			<?php foreach ($monthlyData as $array): ?>
				<th colspan="2"><?php echo $array["dayLbl"] ?></th>
			<?php endforeach; ?>
				<th colspan="2">合計</th>
			</tr>
			<tr>
			<?php foreach ($monthlyData as $array): ?>
				<th>予測</th>
				<th>実績</th>
			<?php endforeach; ?>
				<th>予測</th>
				<th>実績</th>
			</tr>
			<tr>
				<th style="text-align:left">売上金額</th>
			<?php foreach ($monthlyData as $array): ?>
				<td id="<?php echo $array["day"] ?>_y_1" class="tableCol"><?php echo !isset($array["data"]["LYH_URIAGE"]) ? "" : number_format($array["data"]["LYH_URIAGE"]) ?></td>
				<td id="<?php echo $array["day"] ?>_j_1" class="tableCol"><?php echo !isset($array["data"]["LJH_URIAGE"]) ? "" : number_format($array["data"]["LJH_URIAGE"]) ?></td>
			<?php endforeach; ?>
				<td id="99999999_y_1" class="tableCol"><?php echo number_format($sumUriageY) ?></td>
				<td id="99999999_j_1" class="tableCol"><?php echo number_format($sumUriageJ) ?></td>
			</tr>
			<tr>
				<th style="text-align:left">原価</th>
			<?php foreach ($monthlyData as $array): ?>
				<td id="<?php echo $array["day"] ?>_y_2" class="tableCol"><?php echo !isset($array["data"]["LYH_GENKA"]) ? "" : number_format($array["data"]["LYH_GENKA"]) ?></td>
				<td id="<?php echo $array["day"] ?>_j_2" class="tableCol"><?php echo !isset($array["data"]["LJH_GENKA"]) ? "" : number_format($array["data"]["LJH_GENKA"]) ?></td>
			<?php endforeach; ?>
				<td id="99999999_y_2" class="tableCol"><?php echo number_format($sumGenkaY) ?></td>
				<td id="99999999_j_2" class="tableCol"><?php echo number_format($sumGenkaJ) ?></td>
			</tr>
			<tr>
				<th style="text-align:left">粗利</th>
			<?php foreach ($monthlyData as $array): ?>
				<td id="<?php echo $array["day"] ?>_y_3" class="tableCol"><?php echo !isset($array["data"]["LYH_ARARI"]) ? "" : number_format($array["data"]["LYH_ARARI"]) ?></td>
				<td id="<?php echo $array["day"] ?>_j_3" class="tableCol"><?php echo !isset($array["data"]["LJH_ARARI"]) ? "" : number_format($array["data"]["LJH_ARARI"]) ?></td>
			<?php endforeach; ?>
				<td id="99999999_y_3" class="tableCol"><?php echo number_format($sumArariY) ?></td>
				<td id="99999999_j_3" class="tableCol"><?php echo number_format($sumArariJ) ?></td>
			</tr>
			<tr>
				<th style="text-align:left">合計人数</th>
			<?php foreach ($monthlyData as $array): ?>
				<td id="<?php echo $array["day"] ?>_y_4" class="tableCol"><?php echo !isset($array["data"]["LYH_NINZU"]) ? "" : number_format($array["data"]["LYH_NINZU"]) ?></td>
				<td id="<?php echo $array["day"] ?>_j_4" class="tableCol"><?php echo !isset($array["data"]["LJH_NINZU"]) ? "" : number_format($array["data"]["LJH_NINZU"]) ?></td>
			<?php endforeach; ?>
				<td id="99999999_y_4" class="tableCol"><?php echo number_format($sumNinzuY) ?></td>
				<td id="99999999_j_4" class="tableCol"><?php echo number_format($sumNinzuJ) ?></td>
			</tr>
			<tr>
				<th style="text-align:left">合計時間</th>
			<?php foreach ($monthlyData as $array): ?>
				<td id="<?php echo $array["day"] ?>_y_5" class="tableCol"><?php echo !isset($array["data"]["LYH_JIKAN"]) ? "" : number_format($array["data"]["LYH_JIKAN"], 2) ?></td>
				<td id="<?php echo $array["day"] ?>_j_5" class="tableCol"><?php echo !isset($array["data"]["LJH_JIKAN"]) ? "" : number_format($array["data"]["LJH_JIKAN"], 2) ?></td>
			<?php endforeach; ?>
				<td id="99999999_y_5" class="tableCol"><?php echo number_format($sumJikanY, 2) ?></td>
				<td id="99999999_j_5" class="tableCol"><?php echo number_format($sumJikanJ, 2) ?></td>
			</tr>
			<?php foreach ($monthlyData as $array): ?>
				<input type="hidden" id="<?php echo $array["day"] ?>" value="<?php echo $array["exist"] ?>">
			<?php endforeach; ?>
			<tbody>
		</table>
	</div>
</div>
<?php } ?>
</div>

<div id="day_table">
</div>

</div>
<!--/ #wrapper-->
            