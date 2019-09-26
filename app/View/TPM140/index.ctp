<?php
			echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSTPM140_RELOAD', 'DCMSTPM140_CALENDAR', 'DCMSTPM140_CSV'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSTPM'), false, array('inline'=>false));
?>
<!-- WRAPPER -->
<div id="wrapper_s">

<!-- 検索 -->
<div class="box" style="width:700px;">
	<?php echo $this->Form->create('TPM140Model', array('url' => '/TPM140','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
		<table class="cndt" style="width:700px">
			<tbody>
			<colgroup>
				<col width="100px">
				<col width="250px">
				<col width="100px">
			</colgroup>
			<tr>
				<td>出荷日</td>
				<td>
					<?php echo $this->Form->input('ymd_from',
											array('maxlength' => '40',
												'label' => false,
												'error' => false,
												'style' => 'width:100px',
												'name' => 'ymd_from',
												'id' => 'ymd_from',
												'value' => $ymd_from,
												'class' => 'zen')) ?>

					<span style="padding-left:10px;padding-right:10px;">～</span>

					<?php echo $this->Form->input('ymd_to',
											array('maxlength' => '40',
												'label' => false,
												'error' => false,
												'style' => 'width:100px',
												'name' => 'ymd_to',
												'value' => $ymd_to,
												'id' => 'ymd_to',
												'class' => 'zen')) ?>
				</td>
        			<td><input value="検索" type="submit" div="btnsubmit"></td>
			</tr>
			</tbody>
		</table>
	<?php echo $this->Form->end() ?>
</div>
<!--/ 検索 -->

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

</style>

<!-- メインテーブル-->
<div class="resizable_s" id="lstTable">
	<div class="tableRowTitle row_ttl_1" style="width:1100px;">
		<div class="tableCell cell_ttl_1" style="width:100px;" >得意先コード</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >店舗コード</div>
		<div class="tableCell cell_ttl_1" style="width:300px;" >得意先名</div>
		<div class="tableCell cell_ttl_1" style="width:300px;" >店舗名</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >個数</div>
	</div>

<?php  foreach ($lsts as $array): ?>
	<div class="tableRow row_dat" style="width:1100px;">
		<div class="tableCell cell_dat" style="width:100px;text-align:left;" >
			<?php if (!empty($array['TOKUISAKI_CODE'])) {  echo $array['TOKUISAKI_CODE']; } ?>
		</div>
		<div class="tableCell cell_dat" style="width:100px;text-align:left;" >
			<?php if (!empty($array['TENPO_CODE'])) {  echo $array['TENPO_CODE']; } ?>
		</div>
		<div class="tableCell cell_dat" style="width:300px;text-align:left;" >
			<?php if (!empty($array['TOKUISAKI_MEI'])) {  echo $array['TOKUISAKI_MEI']; } ?>
		</div>
		<div class="tableCell cell_dat" style="width:300px;text-align:left;" >
			<?php if (!empty($array['TENPO_MEI'])) {  echo $array['TENPO_MEI']; } ?>
		</div>
		<div class="tableCell cell_dat" style="width:100px;text-align:left;" >
			<?php echo $array['KOSU']; ?>
		</div>

	</div>
<?php  endforeach; ?>

</div>

</div>
<!--/ #wrapper-->
