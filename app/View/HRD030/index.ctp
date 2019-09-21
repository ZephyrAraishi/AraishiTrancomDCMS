<?php
echo $this->Html->script(array('effects','window','protocalendar','base64','DCMSMessage','DCMSCommon','DCMSValidation',
		'DCMSHRD030_CALENDAR','DCMSHRD030_RELOAD','DCMSHRD030_BUNRUI'), array('inline'=>false));
echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSWEX'), false, array('inline'=>false));
?>
<div id="wrapper_s">

<div>

	<?php echo $this->Form->create('HRD030Model', array('url' => '/HRD030/index?flg=1','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
<div>

	<?php echo $this->Form->create('HRD030Model', array('url' => '/HRD030/index?flg=3','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
	<b><big>■ピッキング実績情報</big></b>
	<div class="box" style="width:920px;">
		<table class="cndt" style="width:900px">
			<colgroup width="100px">
			<colgroup width="150px">
			<colgroup width="100px">
			<colgroup width="150px">
			<colgroup width="110px">
			<tbody>
			<!-- １行目 -->
			<tr>
				<td>対象期間</td>
				<td class="colBnriCd" style="text-align:left;" colspan="4">
				    	<div style="width:100px;float:left;margin-top:10px;text-align:left;">
					    	<input name="start_ymd_ins3" id="startYmdInsText3" maxlength="8" class="calendar han" type="text" style="width:80px;height:20px">
		    			</div>
				    	<div style="width:50px;float:left;margin-top:15px;text-align:left;">
					　～　
		    			</div>
				    	<div style="width:100px;float:left;margin-top:10px;text-align:left;">
					    	<input name="end_ymd_ins3" id="endYmdInsText3" maxlength="8" class="calendar han" type="text" style="width:80px;height:20px">
		    			</div>
				</td>
			</tr>
			<!-- ２行目 -->
			<tr style="padding: 0px;">
				<td></td>
				<td colspan="6">
				</td>
				<td style="width:130px"></td>
				<td><input value="工程実績&#10;CSV抽出" type="submit";div="btnsubmit"  style="width:100px;height:50px"></td>
			</tr>
			</tbody>
		</table>
	</div>
	<input type="hidden" name="flg" value="3">
	<?php echo $this->Form->end() ?> 

</div>


</div>
