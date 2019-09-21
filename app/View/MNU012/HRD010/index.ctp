<?php
			echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSHRD010_RELOAD','DCMSHRD010_LINK'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','themes/alert','themes/alphacube','calendar','DCMSHRD'), false, array('inline'=>false));

?>
<!-- WRAPPER -->
<div id="wrapper_s">
	<div id="HRDmenu">
		<ul class="tab clear">
			<li><a href="/DCMS/HRD010/shinki" class="blue">新規</a></li>
			<li class="none"><a href="/DCMS/HRD010/index" class="red">更新</a></li>
		</ul>
	</div>
	
<div class="box" style="width:550px; margin-top: 20px;">
<?php echo $this->Form->create('WPO040Model', array('url' => '/HRD010/index','type' => 'get','inputDefaults' => array('label' => false,'div' => false,'hidden' => false))) ?>
	
	<table class="field">
		<colgroup>
			<col width="100px">
			<col width="300px">
			<col width="150px">
		</colgroup>
		<tr>
			<td>スタッフコード</td>
			<td colspan="2" align="left">
				<input type="text" style="width:140px;" id="staff_cd" name="staff_cd" class="han" maxlength="10">
			</td>
		</tr>
		<tr>
			<td>スタッフ名</td>
			<td colspan="2" align="left">
				<input type="text" style="width:140px;" id="staff_nm" name="staff_nm" maxlength="30">
			</td>
		</tr>
		<tr>
			<td>派遣</td>
			<td align="left">
			<select style="width:140px;" id="haken_cd" name="haken_cd" >
				<option value=""></option>
<?php
			foreach($hakenList as $array) {

				echo	"<option value=\"". $array['KAISYA_CD'] . "\">" . $array['KAISYA_NM'] . "</option>";
				
			}
?>
			</select>
			</td>
			<td><input type="submit" value="検索" class="btn" id="addButton" ></td>
		</tr>
	</table>
<?php echo $this->Form->end() ?>
</div>
	
	<div style="positon:relative;" id="lstTable" >	
		<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>
		<div class="resizable_s" id="lstTable" >
			<div class="tableRowTitle row_ttl_1" style="width:770px;">
				<div class="tableCell cell_ttl_1" style="width:60px;" ><?php echo Configure::read('Bango'); ?></div>
				<div class="tableCell cell_ttl_1" style="width:160px;" >スタッフコード</div>
				<div class="tableCell cell_ttl_1" style="width:200px;" >スタッフ名</div>
				<div class="tableCell cell_ttl_1" style="width:310px;" >派遣会社</div>
			</div>

<?php

	if (!empty($staffList)) {
		$count = 0;
		foreach($staffList as $array) {
			$count++;
?>
			<div class="tableRow row_dat" style="width:770px;">
				<div class="tableCellBango cell_dat" style="width:60px;" ><?php echo $index + $count; ?></div>
				<div class="tableCell cell_dat cd" style="width:160px;" ><?php echo $array['STAFF_CD'] ?></div>
				<div class="tableCell cell_dat nm" style="width:200px;" ><?php echo $array['STAFF_NM'] ?></div>
				<div class="tableCell cell_dat nm" style="width:310px;" ><?php echo $array['KAISYA_NM'] ?></div>
				<div class="hiddenData" ><?php echo $array['DEL_FLG'] ?></div>
			</div>
		
<?php
		}
	}
?>
		</div>
	</div>
</div>
		<!--/ #wrapper-->                 