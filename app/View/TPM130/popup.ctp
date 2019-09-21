<div style= "position: relative; height: 100%; width: 90%; margin-top: 20px;margin-left:auto;margin-right:auto;">
  <div style="font-size:12pt;font-weight:bold;text-align:left;height:20px;">
    <?php if(!empty($homen)){ echo $homen; }; ?>
  </div>

<style>
.tableStaffTitle {
	font-weight:bold;
	text-align:left;
	margin-top:30px;
}
.tableStaff {
	width:410px;
}
.staffCodeTitleCell {
	width:100px;
}
.staffNameTitleCell {
	width:290px;
}
.staffCodeCell {
	width:100px;
	text-align:left;
}
.staffNameCell {
	width:290px;
	text-align:left;
}
</style>


<div class="tableStaffTitle">
トータルピック
</div>
<div class="resizable_ss" style="height:100%;" id="lstTable">
	<div class="tableRowTitle row_ttl_1 tableStaff">
		<div class="tableCell cell_ttl_1 staffCodeTitleCell">スタッフコード</div>
		<div class="tableCell cell_ttl_1 staffNameTitleCell" >スタッフ名</div>
	</div>
<?php  foreach ($totalPk as $array): ?>
	<div class="tableRow row_dat tableStaff">
		<div class="tableCell cell_dat staffCodeCell" >
			<?php if (!empty($array['STAFF_CD'])) {  echo $array['STAFF_CD']; } ?>
		</div>
		<div class="tableCell cell_dat staffNameCell" >
			<?php if (!empty($array['STAFF_NM'])) {  echo $array['STAFF_NM']; } ?>
		</div>
	</div>
<?php  endforeach; ?>
</div>


<div class="tableStaffTitle">
種蒔検品
</div>
<div class="resizable_ss" style="height:100%;" id="lstTable">
	<div class="tableRowTitle row_ttl_1 tableStaff">
		<div class="tableCell cell_ttl_1 staffCodeTitleCell" >スタッフコード</div>
		<div class="tableCell cell_ttl_1 staffNameTitleCell" >スタッフ名</div>
	</div>
<?php  foreach ($tanemaki as $array): ?>
	<div class="tableRow row_dat tableStaff">
		<div class="tableCell cell_dat staffCodeCell" >
			<?php if (!empty($array['STAFF_CD'])) {  echo $array['STAFF_CD']; } ?>
		</div>
		<div class="tableCell cell_dat staffNameCell" >
			<?php if (!empty($array['STAFF_NM'])) {  echo $array['STAFF_NM']; } ?>
		</div>
	</div>
<?php  endforeach; ?>
</div>
<!--
<div class="tableStaffTitle">
積込検品
</div>
<div class="resizable_ss" style="height:100%;" id="lstTable">
	<div class="tableRowTitle row_ttl_1 tableStaff">
		<div class="tableCell cell_ttl_1 staffCodeTitleCell" >スタッフコード</div>
		<div class="tableCell cell_ttl_1 staffNameTitleCell" >スタッフ名</div>
	</div>
<?php  foreach ($kenpin as $array): ?>
	<div class="tableRow row_dat tableStaff">
		<div class="tableCell cell_dat staffCodeCell" >
			<?php if (!empty($array['STAFF_CD'])) {  echo $array['STAFF_CD']; } ?>
		</div>
		<div class="tableCell cell_dat staffNameCell" >
			<?php if (!empty($array['STAFF_NM'])) {  echo $array['STAFF_NM']; } ?>
		</div>
	</div>
<?php  endforeach; ?>
</div>
-->

</div>
