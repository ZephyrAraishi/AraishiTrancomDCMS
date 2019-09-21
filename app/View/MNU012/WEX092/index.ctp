<?php
echo $this->Html->script(array('effects','window','protocalendar','base64','DCMSMessage','DCMSCommon','DCMSValidation',
		'DCMSWEX092_RELOAD','DCMSWEX092_CALENDAR'), array('inline'=>false));
echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSWEX'), false, array('inline'=>false));
?>
<div id="wrapper_s">
	
<div id="kaizen_header">

<?php echo $this->Form->create('WEX092Model', array('url' => '/WEX092','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
<input type="hidden" name="no" value="<?php echo $kznNo ?>">
<input type="hidden" name="viewStartYm" value="<?php echo $viewStartYm ?>">
<input type="hidden" name="viewEndYm" value="<?php echo $viewEndYm ?>">
<table class="kaizen">
	<colgroup>
		<col width="90px">
		<col width="190px">
		<col width="90px">
		<col width="150px">
		<col width="90px">
		<col width="150px">
		<col width="90px">
		<col width="150px">
	</colgroup>
	<tr>
		<td class="kaizen_tt">改善表題</td>
		<td colspan="2">
			<?php echo empty($kznData) ? "" : $kznData["KZN_TITLE"] ?>
			<div class="underline"></div>
		</td>
		<td colspan="5"></td>
	</tr>
	<tr>
		<td>改善内容</td>
		<td colspan="7">
			<textarea cols="20" rows="4" disabled><?php echo empty($kznData) ? "" : $kznData["KZN_TEXT"] ?></textarea>
		</td>
	</tr>
	<tr>
		<td>対象期間</td>
		<td>
			<?php echo empty($kznData) ? "" : substr($kznData["TGT_KIKAN_ST"], 0, 4)."/".substr($kznData["TGT_KIKAN_ST"], 4, 2) ?>
			&nbsp;～&nbsp;
			<?php echo empty($kznData) ? "" : substr($kznData["TGT_KIKAN_ED"], 0, 4)."/".substr($kznData["TGT_KIKAN_ED"], 4, 2) ?>
			<div class="underline"></div>
		</td>
		<td class="kaizen_tt">大分類</td>
		<td>
			<?php echo empty($kznData) ? "" : $kznData["BNRI_DAI_NM"] ?>
			<div class="underline"></div>
		</td>
		<td>中分類</td>
		<td>
			<?php echo empty($kznData) ? "" : $kznData["BNRI_CYU_NM"] ?>
			<div class="underline"></div>
		</td>
		<td>細分類</td>
		<td>
			<?php echo empty($kznData) ? "" : $kznData["BNRI_SAI_NM"] ?>
			<div class="underline"></div>
		</td>
	</tr>
	<tr>
		<td>改善項目</td>
		<td>
			<?php echo empty($kznData) ? "" : $kznData["KZN_KMK_VALUE"] ?>
			<div class="underline"></div>
		</td>
		<td>数値目標</td>
		<td>
			<?php if (!empty($kznData)) { ?>
				<?php if ($kznData["KBN_KZN_KMK"] == '02') { ?>
					<?php echo number_format($kznData["KZN_VALUE"], 2) ?>
				<?php } else if ($kznData["KBN_KZN_KMK"] == '05') { ?>
					<?php echo number_format($kznData["KZN_VALUE"], 1) ?>
				<?php } else { ?>
					<?php echo number_format($kznData["KZN_VALUE"]) ?>
				<?php } ?>
			<?php } ?>
			<div class="underline"></div>
		</td>
		<td>改善コスト</td>
		<td>
			<?php echo empty($kznData) ? "" : number_format($kznData["KZN_COST"]) ?>
			<div class="underline"></div>
		</td>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td>出力期間</td>
		<td colspan="7">
				<input id="startYm" name="startYm" maxlength="6" type="text" class="calendar han"  style="width:80px;height:20px" value="<?php echo $startYm ?>">
				&nbsp;～&nbsp;
				<input id="endYm" name="endYm" maxlength="6" type="text" class="calendar han"  style="width:80px;height:20px" value="<?php echo $endYm ?>">
				&nbsp;
				<input type="submit" value="出力">
		</td>
	</tr>
</table>
<?php echo $this->Form->end() ?>
</div>

<br>

<!-- ページング -->
<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>

<!-- メインテーブル-->
<div class="resizable_s" id="lstTable">
	<div class="tableRowTitle row_ttl_1" style="width:3060px;">
		<div class="tableCellBango cell_ttl_1" style="width:60px;" ><?php echo Configure::read('Bango'); ?></div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >年月</div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >単位</div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >目的</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >累計単価</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >累計時間</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >累計人数</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >累計物量</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >累計ライン数量</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >累計コスト</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >累計売上</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >累計粗利</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >累計品質件数</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >物量生産性</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >ライン生産性</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >平均単価</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >平均時間</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >平均人数</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >平均物量</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >平均ライン数量</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >平均コスト</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >平均売上</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >平均粗利</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >平均品質件数</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >必須</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >業務</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >価値</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >専門</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >タイプ</div>
	</div>
<?php if (!empty($abcData)) { ?>
	<?php for ($i = 0; $i < count($abcData); $i++) { ?>
		<?php 
			$no = (($pageID - 1) * $pageRowCnt) + ($i + 1); 
			$data = $abcData[$i];
		?>
	<div class="tableRow row_dat" style="width:3060px;">
		<div class="tableCellBango cell_dat" style="width:60px;" ><?php echo $no ?></div>
		<div class="tableCell cell_dat date" style="width:90px;" ><?php echo substr($data["YM"], 0, 4)."/".substr($data["YM"], 4, 2) ?></div>
		<div class="tableCell cell_dat kbn" style="width:80px;" ><?php echo $data["KBN_TANI_NM"] ?></div>
		<div class="tableCell cell_dat kbn" style="width:80px;" ><?php echo $data["KBN_KTD_MKTK_NM"] ?></div>
		<div class="tableCell cell_dat kin" style="width:90px;" ><?php echo number_format($data["SUM_TANKA"]) ?></div>
		<div class="tableCell cell_dat cnt" style="width:90px;" ><?php echo number_format($data["SUM_JIKAN"], 2) ?></div>
		<div class="tableCell cell_dat cnt" style="width:90px;" ><?php echo number_format($data["SUM_NINZU"]) ?></div>
		<div class="tableCell cell_dat cnt" style="width:90px;" ><?php echo $data["KBN_URIAGE"] != "01" ? number_format($data["SUM_BUTURYO"]) : $data["SUM_BUTURYO"] ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;" ><?php echo $data["KBN_URIAGE"] != "01" ? number_format($data["SUM_SURYO_LINE"]) : $data["SUM_SURYO_LINE"] ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;" ><?php echo number_format($data["SUM_ABC_COST"]) ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;" ><?php echo number_format($data["SUM_URIAGE"]) ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;" ><?php echo number_format($data["SUM_RIEKI"]) ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;" ><?php echo number_format($data["SUM_HINSITU_CNT"]) ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;" ><?php echo number_format($data["SEISANSEI_BUTURYO"], 1)  ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;" ><?php echo number_format($data["SEISANSEI_LINE"], 1) ?></div>
		<div class="tableCell cell_dat kin" style="width:100px;" ><?php echo number_format($data["AVG_TANKA"]) ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;" ><?php echo number_format($data["AVG_JIKAN"], 2) ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;" ><?php echo number_format($data["AVG_NINZU"]) ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;" ><?php echo $data["KBN_URIAGE"] != "01" ? number_format($data["AVG_BUTURYO"]) : $data["AVG_BUTURYO"] ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;" ><?php echo $data["KBN_URIAGE"] != "01" ? number_format($data["AVG_SURYO_LINE"]) : $data["AVG_SURYO_LINE"] ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;" ><?php echo number_format($data["AVG_ABC_COST"]) ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;" ><?php echo number_format($data["AVG_URIAGE"]) ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;" ><?php echo number_format($data["AVG_RIEKI"]) ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;" ><?php echo number_format($data["AVG_HINSITU_CNT"]) ?></div>
		<div class="tableCell cell_dat kbn" style="width:100px;" ><?php echo $data["KBN_HISSU_WK_NM"] ?></div>
		<div class="tableCell cell_dat kbn" style="width:100px;" ><?php echo $data["KBN_GYOMU_NM"] ?></div>
		<div class="tableCell cell_dat kbn" style="width:100px;" ><?php echo $data["KBN_FUKAKACHI_NM"] ?></div>
		<div class="tableCell cell_dat kbn" style="width:100px;" ><?php echo $data["KBN_SENMON_NM"] ?></div>
		<div class="tableCell cell_dat kbn" style="width:100px;" ><?php echo $data["KBN_KTD_TYPE_NM"] ?></div>
	</div>
	<?php } ?>
<?php } ?>
</div>

<hr>

<div id="glaph">
	<?php 
		$param  = "?no=".$kznNo;
		$param .= "&startYm=".$viewStartYm;
		$param .= "&endYm=".$viewEndYm;
		$param .= "&width=1180";
		$param .= "&height=540";
	?>
	<img src="/DCMS/WEX092/glaph<?php echo $param ?>" alt="">
</div>

</div>

</body>
</html>