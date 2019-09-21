<?php
			echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSTPM130_RELOAD', 'DCMSTPM130_CALENDAR', 'DCMSTPM130_POPUP', 'DCMSTPM130_CSV'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSTPM'), false, array('inline'=>false));
?>
<!-- WRAPPER -->
<div id="wrapper_s">

<!-- 検索 -->
<div class="box" style="width:700px;">
	<?php echo $this->Form->create('TPM130Model', array('url' => '/TPM130','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
		<table class="cndt" style="width:700px">
			<tbody>
			<colgroup>
				<col width="100px">
				<col width="250px">
				<col width="100px">
			</colgroup>
			<tr>
				<td>出荷日</td>
				<td class="colBnriCd" style="text-align:left;" colspan="1">
					<?php echo $this->Form->input('ymd',
											array('maxlength' => '40',
												'label' => false,
												'error' => false,
												'style' => 'width:100px',
												'name' => 'ymd',
												'id' => 'ymd',
												'value' => $ymd,
												'class' => 'zen')) ?>
        			<td><input value="検索" type="submit" div="btnsubmit"></td>
			</tr>
			</tbody>
		</table>
	<?php echo $this->Form->end() ?>
</div>
<!--/ 検索 -->

<div style="width:100%;overflow: hidden;">
	<div  style="width:49%;text-align:left;float:left;">表示期間：<?php  echo $ymd_from; ?>&nbsp;～&nbsp;<?php  echo $ymd_to; ?></div>
	<div  id="nextReload" style="width:49%;text-align:right;float:left;"></div>
	<input type="hidden"  id="current_ymd" value="<?php  echo $ymd; ?>">
</div>

<!--/ 区切り線 -->
<div class="line"></div>
<!--/ 区切り線 -->

<!-- CSV出力ボタン-->
<div style="height:40px;width:100%;text-align:right;max-width:1180px;" id="buttons" >
	<input type="button"  value="CSV出力" style="width:180px;height:30px;" id="csvButton" >
</div>
<!--/ CSV出力ボタン-->


<style>
.legend {
	float:left;
	font-size:20px;
	margin:0 10px 10px 0;
	width:50px;
	height:30px;
	text-align:center;
}

.progress {
	width:250px;
	height:15px;
	background:#dcdcdc;
	float:left;
}

.progBar {
	height:15px;
	font-size:2px;
}

.prog30per {
	background-color:red;
}

.prog50per {
	background-color:yellow;
}

.prog70per {
	background-color:green;
}

.prog90per {
	background-color:blue;
}

.prog100per {
	background-color:#FFFFFF;
}

.progValue {
	float:left;
	text-align:left;
	margin-left:10px;
}

.progFinish {
	background-color:navy;
	color:white;
}

</style>

<div style="background-color:#fff;height:30px;width:300px;margin:0 auto;">
	<div class="legend" style="color:red;">30%</div>
	<div class="legend" style="color:yellow;">50%</div>
	<div class="legend" style="color:green;">70%</div>
	<div class="legend" style="color:blue;">90%</div>
	<div class="legend progFinish">完了</div>
</div>

<!-- メインテーブル-->
<div class="resizable_s" style="height:100%;" id="lstTable">
	<div class="tableRowTitle row_ttl_1" style="width:1100px;">
		<div class="tableCell cell_ttl_1" style="width:100px;" >ルート</div>
		<div class="tableCell cell_ttl_1" style="width:320px;" >トータルピック</div>
		<div class="tableCell cell_ttl_1" style="width:320px;" >種蒔検品</div>
		<div class="tableCell cell_ttl_1" style="width:320px;" >積込検品</div>
	</div>

<?php  foreach ($lsts as $array): ?>
	<div class="tableRow row_dat" style="width:1100px;">
		<div class="tableCell cell_dat <?php  echo $array['CLASS_ALL_FINISH']; ?>" style="width:100px;text-align:left;" >
			<?php if (!empty($array['HOMEN_MEI'])) {  echo $array['HOMEN_MEI']; } ?>
		</div>

		<div class="tableCell cell_dat <?php  echo $array['CLASS_TOTAL_PK_FINISH']; ?>" style="width:320px;" >
			<div class="progress">
				<div class="progBar <?php  echo $array['CLASS_TOTAL_PK']; ?>" style="width:<?php  echo $array['TOTAL_PK']; ?>%"></div>
			</div>
			<div class="progValue <?php  echo $array['CLASS_TOTAL_PK_FINISH']; ?>"><?php  echo $array['TOTAL_PK']; ?>%</div>
		</div>

		<div class="tableCell cell_dat <?php  echo $array['CLASS_TANEMAKI_FINISH']; ?>" style="width:320px;" >
			<div class="progress">
				<div class="progBar <?php  echo $array['CLASS_TANEMAKI']; ?>" style="width:<?php  echo $array['TANEMAKI']; ?>%"></div>
			</div>
			<div class="progValue <?php  echo $array['CLASS_TANEMAKI_FINISH']; ?>">
				<?php  echo $array['TANEMAKI']; ?><?php if ($array['TANEMAKI'] !== '-' ) { ?>%<?php } ?>
			</div>
		</div>

		<div class="tableCell cell_dat <?php  echo $array['CLASS_KENPIN_FINISH']; ?>" style="width:320px;" >
			<div class="progress">
				<div class="progBar <?php  echo $array['CLASS_KENPIN']; ?>" style="width:<?php  echo $array['KENPIN']; ?>%"></div>
			</div>
			<div class="progValue <?php  echo $array['CLASS_KENPIN_FINISH']; ?>"><?php  echo $array['KENPIN']; ?>%</div>
		</div>

	</div>
<?php  endforeach; ?>

</div>

</div>
<!--/ #wrapper-->
