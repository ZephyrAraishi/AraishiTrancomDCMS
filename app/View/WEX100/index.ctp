<?php
			echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSWEX100_RELOAD', 'DCMSWEX100_CALENDAR',
					'DCMSWEX100_CSV','DCMSWEX100_PDF'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSWEX'), false, array('inline'=>false));
?>
<!-- WRAPPER -->
<div id="wrapper_s">

<!-- 検索 -->
<div class="box" style="width:1000px;">
	<?php echo $this->Form->create('WEX100Model', array('url' => '/WEX100','type'=>'get','id' => 'search_form', 'inputDefaults' => array('label' => false,'div' => false))) ?>
		<table class="cndt">
			<tbody>
			<colgroup>
				<col width="100px">
				<col width="90px">
				<col width="20px">
				<col width="150px">
				<col width="100px">
				<col width="200px">
				<col width="100px">
				<col width="100px">
			</colgroup>
			<tr>
				<td>年月日</td>
				<td class="colBnriCd" style="text-align:left;" colspan="1">
						<?php echo $this->Form->input('ymd_from',
											array('maxlength' => '40',
												'label' => false,
												'error' => false,
												'style' => 'width:100px',
												'name' => 'ymd_from',
												'id' => 'ymd_from',
												'value' => $ymd_from,
												'class' => 'zen')) ?>
				<td style="text-align:center;font-size:13px;color:#222">～</td>
				<td class="colBnriCd" style="text-align:left;" colspan="1">
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

				<td>所属</td>
				<td>
					<select name='kaisya_cd' id='kaisya_cd' style="width:150px;">
						<option value=''></option>
<?php
	foreach ($hakenList as $array) {
		echo '<option value="' . $array['KAISYA_CD'] . '">' . $array['KAISYA_NM'] . '</option>';
	}
?>
					</select>
				</td>
				<td>スタッフコード<br />/名称</td>
				<td>
						<?php echo $this->Form->input('staff_cd_nm',
											array('maxlength' => '40',
												'label' => false,
												'error' => false,
												'style' => 'width:100px;height:25px;',
												'name' => 'staff_cd_nm',
												'id' => 'staff_cd_nm',
												'class' => 'zen')) ?>
				</td>
			</tr>
			<tr style="height:30px;">
				<td>コース</td>
				<td  colspan="3">
					<select name='bnri_cyu_cd' id='bnri_cyu_cd' style="width:150px;">
						<option value=''></option>
<?php
	foreach ($bnriCyuList as $array) {
		echo '<option value="' . $array['BNRI_CYU_CD'] . '">' . $array['BNRI_CYU_RYAKU'] . '</option>';
	}
?>
					</select>
        			</td>
        			<td>車両番号</td>
        			<td colspan="3" style='vertical-align:bottom;'>
					<select name='syaryo_cd' id='syaryo_cd' style="width:150px;">
						<option value=''></option>
<?php
	foreach ($syaryoCdList as $array) {
		echo '<option value="' . $array['MEI_CD'] . '">' . $array['MEI_1'] . '</option>';
	}
?>
					</select>
				</td>
			</tr>
			<tr style="height:30px;">
				<td>地区・エリア</td>
				<td  colspan="3">
					<select name='area_cd' id='area_cd' style="width:150px;">
						<option value=''></option>
<?php
	foreach ($areaCdList as $array) {
		echo '<option value="' . $array['MEI_CD'] . '">' . $array['MEI_1'] . '</option>';
	}
?>
					</select>
        		</td>
        		<td colspan="4" style='vertical-align:bottom;'>
					<input type="checkbox" name="more" id="more" <?= (!empty($more)&&$more=="on")?"checked": "" ?> />　就業終了漏れを抽出
				</td>
			</tr>
			<tr style="height:30px;">
				<td>時間</td>
				<td class="jikan_from" style="text-align:left;" colspan="1">
					<?php echo $this->Form->input('jikan_from',
					array('maxlength' => '40',
						'label' => false,
						'error' => false,
						'style' => 'width:100px',
						'name' => 'jikan_from',
						'id' => 'jikan_from',
						'maxlength' => '4',
						'class' => 'han')) ?>
				</td>
				<td style="text-align:center;font-size:13px;color:#222">～</td>
				<td colspan="5" class="colBnriCd" style="text-align:left;" colspan="1">
					<?php echo $this->Form->input('jikan_to',
					array('maxlength' => '40',
						'label' => false,
						'error' => false,
						'style' => 'width:100px',
						'name' => 'jikan_to',
						'id' => 'jikan_to',
						'maxlength' => '4',
						'class' => 'han')) ?>　工程開始・終了の時間の範囲
				</td>
			</tr>
			<tr style="height:30px;">
				<td>距離</td>
				<td class="jikan_from" style="text-align:left;" colspan="1">
					<?php echo $this->Form->input('kyori_from',
					array('maxlength' => '40',
						'label' => false,
						'error' => false,
						'style' => 'width:100px',
						'name' => 'kyori_from',
						'id' => 'kyori_from',
						'class' => 'han')) ?>
				</td>
				<td style="text-align:center;font-size:13px;color:#222">～</td>
				<td colspan="4" class="colBnriCd" style="text-align:left;" colspan="1">
					<?php echo $this->Form->input('kyori_to',
					array('maxlength' => '40',
						'label' => false,
						'error' => false,
						'style' => 'width:100px',
						'name' => 'kyori_to',
						'id' => 'kyori_to',
						'class' => 'han')) ?>　工程開始・終了の距離の範囲
				</td>
				<td><input value="検索" type="submit";div="btnsubmit"></td>
			</tr>
			</tbody>
		</table>
	<?php echo $this->Form->end() ?>
</div>
<!--/ 検索 -->

<!--/ 区切り線 -->
<div class="line"></div>
<!--/ 区切り線 -->

<!-- 更新ボタン-->
<div style="height:40px;width:100%;text-align:right;max-width:1180px;" id="buttons" >
	<input type="button"  value="CSV出力" style="width:180px;height:30px;" id="csvButton" >
	<input type="button"  value="運転日報出力" style="width:180px;height:30px;" id="pdfButton" >
</div>
<!--/ 更新ボタン-->

<!-- メインテーブル-->
	<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>
<div class="resizable_s" id="lstTable">
	<div class="tableRowTitle row_ttl_1" style="width:<?= 1660 + count($komokuList) * 570; ?>px;height:49px;">
		<div class="tableCell cell_ttl_1" style="width:60px;height:32px;padding-top:16px" ><?php echo Configure::read("Bango") ?></div>
		<div class="tableCell cell_ttl_1" style="width:100px;height:32px;padding-top:16px" >荷主コード</div>
		<div class="tableCell cell_ttl_1" style="width:100px;height:32px;padding-top:16px" >荷主名</div>
		<div class="tableCell cell_ttl_1" style="width:100px;font-size:8pt !important;height:48px;padding-top:0px;" >営業所<br />or<br/>センターコード</div>
		<div class="tableCell cell_ttl_1" style="width:100px;font-size:8pt !important;height:48px;padding-top:0px;" >営業所<br />or<br />センター名</div>
		<div class="tableCell cell_ttl_1" style="width:130px;height:32px;padding-top:16px" >配達日</div>
		<div class="tableCell cell_ttl_1" style="width:100px;height:32px;padding-top:16px" >車両No.</div>
		<div class="tableCell cell_ttl_1" style="width:100px;height:32px;padding-top:16px" >コース</div>
		<div class="tableCell cell_ttl_1" style="width:100px;height:32px;padding-top:16px" >地区・エリア</div>
		<div class="tableCell cell_ttl_1" style="width:100px;height:32px;padding-top:16px" >社員番号</div>
		<div class="tableCell cell_ttl_1" style="width:130px;height:32px;padding-top:16px" >担当者</div>
		<div class="tableCell cell_ttl_1" style="width:50px;height:32px;padding-top:16px" >性別</div>
		<div class="tableCell cell_ttl_1" style="width:130px;height:32px;padding-top:16px" >所属部署</div>
		<div class="tableCell cell_ttl_1" style="width:100px;height:32px;padding-top:16px" >契約形態</div>
		<div class="tableCell cell_ttl_1" style="width:50px;font-size:8pt !important;height:40px;padding-top:8px;" >業務開始<br />時間</div>
		<div class="tableCell cell_ttl_1" style="width:50px;font-size:8pt !important;height:40px;padding-top:8px;" >業務終了<br />時間</div>
<?php   $count = 0; ?>
<?php   foreach ($komokuList as $komoku) : ?>
		<div class="tableCell cell_ttl_1" style="width:60px;font-size:8pt !important;height:40px;padding-top:8px;background-color:<?= ($count%2==0)?"#5f9e10":"#bc8f8f;" ?>;" ><?= $komoku['MEI_1'] ?><br />開始時間</div>
		<div class="tableCell cell_ttl_1" style="width:60px;font-size:8pt !important;height:40px;padding-top:8px;background-color:<?= ($count%2==0)?"#5f9e10":"#bc8f8f;" ?>;" ><?= $komoku['MEI_1'] ?><br />終了時間</div>
		<div class="tableCell cell_ttl_1" style="width:60px;font-size:8pt !important;height:40px;padding-top:8px;background-color:<?= ($count%2==0)?"#5f9e10":"#bc8f8f;" ?>;" ><?= $komoku['MEI_1'] ?><br />作業時間</div>
		<div class="tableCell cell_ttl_1" style="width:80px;font-size:8pt !important;height:40px;padding-top:8px;background-color:<?= ($count%2==0)?"#5f9e10":"#bc8f8f;" ?>;" ><?= $komoku['MEI_1'] ?><br />開始距離</div>
		<div class="tableCell cell_ttl_1" style="width:80px;font-size:8pt !important;height:40px;padding-top:8px;background-color:<?= ($count%2==0)?"#5f9e10":"#bc8f8f;" ?>;" ><?= $komoku['MEI_1'] ?><br />終了距離</div>
		<div class="tableCell cell_ttl_1" style="width:80px;font-size:8pt !important;height:40px;padding-top:8px;background-color:<?= ($count%2==0)?"#5f9e10":"#bc8f8f;" ?>;" ><?= $komoku['MEI_1'] ?><br />累計時間</div>
		<div class="tableCell cell_ttl_1" style="width:80px;font-size:8pt !important;height:40px;padding-top:8px;background-color:<?= ($count%2==0)?"#5f9e10":"#bc8f8f;" ?>;" ><?= $komoku['MEI_1'] ?><br />累計距離</div>
<?php   $count++; ?>
<?php   endforeach;?>
	</div>
<?php  $count = 1; ?>
<?php  foreach ($lsts as $array): ?>
	<div class="tableRow row_dat" style="width:<?= 1660 + count($komokuList) * 570; ?>px;">
	    <div class="tableCell cell_dat bango" style="width:60px;" ><?php echo $count ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $array['NINUSI_CD'] ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $array['NINUSI_NM'] ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $array['SOSIKI_CD'] ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $array['SOSIKI_NM'] ?></div>
		<div class="tableCell cell_dat" style="width:130px;" ><?php echo $array['YMD_SYUGYO'] ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $array['SYARYO_NM'] ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $array['BNRI_CYU_NM'] ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $array['AREA_NM'] ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $array['STAFF_CD'] ?></div>
		<div class="tableCell cell_dat" style="width:130px;" ><?php echo $array['STAFF_NM'] ?></div>
		<div class="tableCell cell_dat" style="width:50px;" ><?php echo $array['SEX'] ?></div>
		<div class="tableCell cell_dat" style="width:130px;" ><?php echo $array['KAISYA_NM'] ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php echo $array['KEIYAKU_NM'] ?></div>
		<div class="tableCell cell_dat" style="width:50px;" ><?php echo $array['DT_SYUGYO_ST'] ?></div>
		<div class="tableCell cell_dat" style="width:50px;" ><?php echo $array['DT_SYUGYO_ED'] ?></div>
<?php  	foreach ($komokuList as $komoku): ?>
		<div class="tableCell cell_dat" style="width:60px;" ><?php echo $array[$komoku['MEI_CD'] . "_MIN_KOTEI_ST"] ?></div>
		<div class="tableCell cell_dat" style="width:60px;" ><?php echo $array[$komoku['MEI_CD'] . "_MAX_KOTEI_ED"] ?></div>
		<div class="tableCell cell_dat" style="width:60px;" ><?php echo $array[$komoku['MEI_CD'] . "_DIFF_KOTEI"] ?></div>
		<div class="tableCell cell_dat" style="width:80px;" ><?php echo $array[$komoku['MEI_CD'] . "_MIN_KAISI_KYORI"] ?></div>
		<div class="tableCell cell_dat" style="width:80px;" ><?php echo $array[$komoku['MEI_CD'] . "_MAX_SYURYO_KYORI"] ?></div>
		<div class="tableCell cell_dat" style="width:80px;" ><?php echo $array[$komoku['MEI_CD'] . "_SUM_KOTEI_M"] ?></div>
		<div class="tableCell cell_dat" style="width:80px;" ><?php echo $array[$komoku['MEI_CD'] . "_SUM_KYORI"] ?></div>
<?php   endforeach; ?>
	</div>
<?php  $count++; ?>
<?php  endforeach; ?>
</div>
<div id="h_ymd_from" style="display:none;" ><?= (isset($ymd_from))?$ymd_from:"" ?></div>
<div id="h_ymd_to" style="display:none;" ><?= (isset($ymd_to))?$ymd_to:"" ?></div>
<div id="h_kaisya_cd" style="display:none;" ><?= (isset($kaisya_cd))?$kaisya_cd:"" ?></div>
<div id="h_staff_cd_nm" style="display:none;" ><?= (isset($staff_cd_nm))?$staff_cd_nm:"" ?></div>
<div id="h_bnri_cyu_cd" style="display:none;" ><?= (isset($bnri_cyu_cd))?$bnri_cyu_cd:"" ?></div>
<div id="h_syaryo_cd" style="display:none;" ><?= (isset($syaryo_cd))?$syaryo_cd:"" ?></div>
<div id="h_area_cd" style="display:none;" ><?= (isset($area_cd))?$area_cd:"" ?></div>
<div id="h_jikan_from" style="display:none;" ><?= (isset($jikan_from))?$jikan_from:"" ?></div>
<div id="h_jikan_to" style="display:none;" ><?= (isset($jikan_to))?$jikan_to:"" ?></div>
<div id="h_kyori_from" style="display:none;" ><?= (isset($kyori_from))?$kyori_from:"" ?></div>
<div id="h_kyori_to" style="display:none;" ><?= (isset($kyori_to))?$kyori_to:"" ?></div>
<div id="h_more" style="display:none;" ><?= (isset($more))?$more:"" ?></div>
<!--/ メインテーブル-->

</div>
<!--/ #wrapper-->
