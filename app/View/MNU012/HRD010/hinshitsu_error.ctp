<?php
			echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSHRD010_HINSHITSU_RELOAD',
					'DCMSHRD010_HINSHITSU_POPUP','DCMSHRD010_HINSHITSU_ADDROW','DCMSHRD010_S_CALENDAR','DCMSKOTEI_DROPDOWN_MENU'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','themes/alert','themes/alphacube','calendar','kotei_dropdown_menu','DCMSHRD'), false, array('inline'=>false));
?>

<!-- WRAPPER -->
<div id="wrapper_s">

	<div id="HRDmenu">
		<ul class="tab clear">
			<li><a href="/DCMS/HRD010/shinki" class="blue">新規</a></li>
			<li class="none"><a href="/DCMS/HRD010/index" class="red">更新</a></li>
		</ul>
	</div>
	<ul id="hrd_nav">
		<li><a href="/DCMS/HRD010/index">検索</a></li>
		<li> <?php echo $this->Html->link("基本", array('action' => 'kihon', '?' => array('staff_cd'=> $staff["STAFF_CD"]))) ?> </li>
		<li><a class="active" href="#">品質</a></li>
		<li> <?php echo $this->Html->link("カルテ", array('action' => 'karte', '?' => array('staff_cd'=> $staff["STAFF_CD"]))) ?> </li>
		<li> <?php echo $this->Html->link("就業実績", array('action' => 'jisseki', '?' => array('staff_cd'=> $staff["STAFF_CD"]))) ?> </li>
	</ul>

	<div style="position:relative;height:50px;margin-top:20px;text-align:left;">
		<div style="position:absolute;height:25px;top:00px;left:40px;width:120px;margin-top:2px;">スタッフコード</div>
		<div style="position:absolute;height:25px;top:00px;left:200px;width:140px;" id="staff_cd"><?php echo $staff["STAFF_CD"] ?></div>
		<div style="position:absolute;height:25px;top:30px;left:40px;width:120px;margin-top:2px;">氏名</div>
		<div style="position:absolute;height:25px;top:30px;left:200px;width:140px;"><?php echo $staff["STAFF_NM"] ?></div>

	</div>
	<div id="buttons" style="position:relative;text-align:right;max-width:1120px;margin-left:30px;">
		<input type="button" id="addButton" style="width:120px;height:30px;" value="追加">
		<input type="button" id="updateButton" style="width:120px;height:30px;" value="更新">
	</div>

	<div class="pagaerNavi" style="text-align:left;margin-left:30px;" ><?php if (!empty($navi)) print($navi);?></div>
	<div class="resizable_s" style="position:relative;height:360px;max-width:1120px;margin-left:30px;margin-top:10px;" id="lstTable">
		<div class="tableRowTitle row_ttl_sp_2" style="width:1390px;">
			<div class="tableCellBango cell_ttl_sp_2" style="width:60px;"><?php echo Configure::read('Bango'); ?></div>
			<div class="tableCell cell_ttl_sp_2" style="width:100px;">発生日付</div>
			<div class="tableCell cell_ttl_sp_2" style="width:150px;">品質内容</div>
			<div style="float:left;width:330px;height:70px;">
				<div class="tableCell cell_ttl_1" style="width:320px;">担当業務 工程</div>
				<div class="tableCell cell_ttl_1" style="width:100px;">大分</div>
				<div class="tableCell cell_ttl_1" style="width:100px;">中分</div>
				<div class="tableCell cell_ttl_1" style="width:100px;">細分</div>
			</div>
			<div class="tableCell cell_ttl_sp_2" style="width:100px;">集計管理No.</div>
			<div class="tableCell cell_ttl_sp_2" style="width:140px;">対応者</div>
			<div class="tableCell cell_ttl_sp_2" style="width:100px;">対応金額</div>
			<div class="tableCell cell_ttl_sp_2" style="width:340px;">対応方法</div>
		</div>

<?php
		if (!empty($lsts)) {
			$count = 0;
			foreach($lsts as $rowData) {
				$count++;
?>
			<div class="tableRow row_dat" style="width:1390px;">
				<div class="tableCellBango cell_dat bango" style="width:60px;"><?php echo $count ?></div>
				<div class="tableCell hiddenData"><?php echo $rowData->pre_hassei_date ?></div>
				<div class="tableCell cell_dat date" style="width:100px;"><?php echo date_format(date_create($rowData->hassei_date),"Y/m/d") ?></div>
				<div class="tableCell hiddenData"><?php echo $rowData->gyo_no ?></div>
				<div class="tableCell cell_dat nm" style="width:150px;"><?php echo $rowData->kbn_hinshitsu_naiyo_nm ?></div>
				<div class="tableCell hiddenData"><?php echo $rowData->kbn_hinshitsu_naiyo_cd ?></div>
				<div class="tableCell cell_dat nm" style="width:100px;"><?php echo $rowData->dai_bunrui_nm ?></div>
				<div class="tableCell hiddenData"><?php echo $rowData->dai_bunrui_cd ?></div>
				<div class="tableCell cell_dat nm" style="width:100px;"><?php echo $rowData->chu_bunrui_nm ?></div>
				<div class="tableCell hiddenData"><?php echo $rowData->chu_bunrui_cd ?></div>
				<div class="tableCell cell_dat nm" style="width:100px;"><?php echo $rowData->sai_bunrui_nm ?></div>
				<div class="tableCell hiddenData"><?php echo $rowData->sai_bunrui_cd ?></div>
				<div class="tableCell cell_dat kbn" style="width:100px;"><?php echo $rowData->s_kanri_no ?></div>
				<div class="tableCell cell_dat nm" style="width:140px;"><?php echo h($rowData->staff_nm) ?></div>
				<div class="tableCell cell_dat kin" style="width:100px;"><?php echo strlen($rowData->t_kin) == 0 ? "" : $this->Number->format($rowData->t_kin) ?></div>
				<div class="tableCell cell_dat nm" style="width:340px;"><?php echo h($rowData->t_hoho) ?></div>
				<div class="tableCell hiddenData CHANGED"><?php echo $rowData->changed ?></div>
			</div>
<?php
			}
		}
?>
	</div>

</div>
<div class="hiddenData STAFF_CODE" ><?php echo $staff["STAFF_CD"] ?></div>
<div class="hiddenData TIME_STAMP" ><?php  echo $timestamp; ?></div>

<!--/ #wrapper-->