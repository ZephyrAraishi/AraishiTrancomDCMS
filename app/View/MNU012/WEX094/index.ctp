<?php
echo $this->Html->script(array('effects','window','protocalendar','base64','DCMSMessage','DCMSCommon','DCMSValidation',
		'DCMSWEX094_RELOAD','DCMSWEX094_CALENDAR'), array('inline'=>false));
echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSWEX'), false, array('inline'=>false));
?>
<div id="wrapper_s">
	
<div id="kaizen_header">

<?php echo $this->Form->create('WEX094Model', array('url' => '/WEX094','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
<input type="hidden" name="no" value="<?php echo $kznNo ?>">
<input type="hidden" name="viewStartYmd" value="<?php echo $viewStartYmd ?>">
<input type="hidden" name="viewEndYmd" value="<?php echo $viewEndYmd ?>">
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
		<td>改善表題</td>
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
			<?php echo empty($kznData) ? "" : substr($kznData["TGT_KIKAN_ST"], 0, 4)."/".substr($kznData["TGT_KIKAN_ST"], 4, 2)."/".substr($kznData["TGT_KIKAN_ST"], 6, 2) ?>
			&nbsp;～&nbsp;
			<?php echo empty($kznData) ? "" : substr($kznData["TGT_KIKAN_ED"], 0, 4)."/".substr($kznData["TGT_KIKAN_ED"], 4, 2)."/".substr($kznData["TGT_KIKAN_ED"], 6, 2) ?>
			<div class="underline"></div>
		</td>
		<td>大分類</td>
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
				<input id="startYmdInsText" name="startYmd" maxlength="8" type="text" style="width:80px;height:20px" value="<?php echo $startYmd ?>">
				&nbsp;～&nbsp;
				<input id="endYmdInsText" name="endYmd" maxlength="8" type="text" style="width:80px;height:20px" value="<?php echo $endYmd ?>">
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
	<div class="tableRowTitle row_ttl_1" style="width:2050px;">
		<div class="tableCellBango cell_ttl_1" style="width:60px;" ><?php echo Configure::read('Bango'); ?></div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >年月日</div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >単位</div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >目的</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >単価</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >時間</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >人数</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >物量</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >ライン数量</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >物量生産性</div>
		<div class="tableCell cell_ttl_1" style="width:90px;" >ライン生産性</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >ABCコスト</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >売上</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >粗利</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >品質件数</div>
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
	<div class="tableRow row_dat" style="width:2050px;">
		<div class="tableCellBango cell_dat" style="width:60px;" ><?php echo $no ?></div>
		<div class="tableCell cell_dat date" style="width:100px;" ><?php echo substr($data["YMD"], 0, 4)."/".substr($data["YMD"], 4, 2)."/".substr($data["YMD"], 6, 2) ?></div>
		<div class="tableCell cell_dat kbn" style="width:80px;" ><?php echo $data["KBN_TANI_NM"] ?></div>
		<div class="tableCell cell_dat kbn" style="width:80px;" ><?php echo $data["KBN_KTD_MKTK_NM"] ?></div>
		<div class="tableCell cell_dat kin" style="width:90px;" ><?php echo number_format($data["TANKA"]) ?></div>
		<div class="tableCell cell_dat cnt" style="width:90px;" ><?php echo number_format($data["JIKAN"], 2) ?></div>
		<div class="tableCell cell_dat cnt" style="width:90px;" ><?php echo number_format($data["NINZU"]) ?></div>
		<div class="tableCell cell_dat cnt" style="width:90px;" ><?php echo $data["KBN_URIAGE"] != "01" ? number_format($data["BUTURYO"]) : $data["BUTURYO"] ?></div>
		<div class="tableCell cell_dat cnt" style="width:90px;" ><?php echo $data["KBN_URIAGE"] != "01" ? number_format($data["SURYO_LINE"]) : $data["SURYO_LINE"] ?></div>
		<div class="tableCell cell_dat cnt" style="width:90px;" ><?php echo number_format($data["SEISANSEI_BUTURYO"], 1) ?></div>
		<div class="tableCell cell_dat cnt" style="width:90px;" ><?php echo number_format($data["SEISANSEI_LINE"], 1) ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;" ><?php echo number_format($data["ABC_COST"]) ?></div>
		<div class="tableCell cell_dat kin" style="width:100px;" ><?php echo number_format($data["URIAGE"]) ?></div>
		<div class="tableCell cell_dat kin" style="width:100px;" ><?php echo number_format($data["RIEKI"]) ?></div>
		<div class="tableCell cell_dat cnt" style="width:100px;" ><?php echo number_format($data["HINSITU_CNT"]) ?></div>
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
		$param .= "&startYmd=".$viewStartYmd;
		$param .= "&endYmd=".$viewEndYmd;
		$param .= "&width=1180";
		$param .= "&height=540";
	?>
	<img src="/DCMS/WEX094/glaph<?php echo $param ?>" alt="">
</div>

</div>

</body>
</html>