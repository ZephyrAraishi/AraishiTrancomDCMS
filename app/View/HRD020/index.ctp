<?php
echo $this->Html->script(array('effects','window','protocalendar','base64','DCMSMessage','DCMSCommon','DCMSValidation',
		'DCMSHRD020_CALENDAR','DCMSHRD020_RELOAD','DCMSHRD020_BUNRUI'), array('inline'=>false));
echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSWEX'), false, array('inline'=>false));
?>
<div id="wrapper_s">

<div>

	<?php echo $this->Form->create('HRD020Model', array('url' => '/HRD020/index?flg=1','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
	<b><big>■人材基本情報</big></b>
	<div class="box" style="width:920px;">
		<table class="cndt" style="width:900px">
			<colgroup width="100px">
			<colgroup width="200px">
			<colgroup width="100px">
			<colgroup width="180px">
			<colgroup width="120px">
			<tbody>
			<!-- １行目 -->
			<tr>
				<td>スタッフコード</td>
				<td class="colBnriCd" style="text-align:left;">
			    	<div style="width:100px;float:left;margin-top:10px;text-align:left;">
				    	<input name="staff_cd1" id="staff_cdText1" maxlength="10" class="zen" type="text" style="width:100px;height:20px;ime-mode:disabled">
	    			</div>
				</td>
				<td>所属会社</td>
				<td class="colBnriCd" style="text-align:left;">
					<div style="position:relative;width:180px;float:left;margin-top:0px;text-align:left;">
					<select name="haken_kaisya_cd1"  id="hakenkaisyaCombo1" style="width:120px">
						<option value=""></option>
						<?php
						foreach($haken_kaisya_cd as $data) :
						?>
						<option value="<?php echo $data['KAISYA_CD'] ?>"><?php echo $data['KAISYA_NM']?></option>
						<?php
						endforeach;
						?>
					</select>
				</div>
				</td>
			</tr>
			<!-- ２行目 -->
			<tr>
				<td>スタッフ名</td>
				<td >
					<input name="staff_nm1" id="staff_nmText1" maxlength="30" type="text" class="zen"  style="width:180px;height:20px;ime-mode:active">
				</td>

				<td></td>
				<td>
					<input type="checkbox" name="chkkahiflg1" value="0" checked>  既存スタッフのみ
				</td>
				<td><input value="人材基本情報&#10;CSV抽出" type="submit";div="btnsubmit" style="width:100px;height:50px"></td>
			</tr>
			</tbody>
		</table>
	</div>
	<input type="hidden" name="flg" value="1">
	<?php echo $this->Form->end() ?>

</div>

<div>

	<?php echo $this->Form->create('HRD020Model', array('url' => '/HRD020/index?flg=2','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
	<b><big>■就業実績情報</big></b>
	<div class="box" style="width:920px;">
		<table class="cndt" style="width:900px">
			<colgroup width="100px">
			<colgroup width="300px">
			<colgroup width="100px">
			<colgroup width="160px">
			<colgroup width="140px">
			<tbody>
			<!-- １行目 -->
			<tr>
				<td>対象期間</td>
				<td class="colBnriCd" style="text-align:left;">
				    	<div style="width:100px;float:left;margin-top:10px;text-align:left;">
					    	<input name="start_ymd_ins2" id="startYmdInsText2" maxlength="8" class="calendar han" type="text" style="width:80px;height:20px">
		    			</div>
				    	<div style="width:50px;float:left;margin-top:15px;text-align:left;">
					　～　
		    			</div>
				    	<div style="width:100px;float:left;margin-top:10px;text-align:left;">
					    	<input name="end_ymd_ins2" id="endYmdInsText2" maxlength="8" class="calendar han" type="text" style="width:80px;height:20px">
		    			</div>
				</td>
				<td>スタッフコード</td>
				<td class="colBnriCd" style="text-align:left;">
			    	<div style="width:100px;float:left;margin-top:10px;text-align:left;">
				    	<input name="staff_cd2" id="staff_cdText2" maxlength="10" class="zen" type="text" style="width:100px;height:20px;ime-mode:disabled">
	    			</div>
				</td>
				<td>
					<input type="checkbox" name="chkkahiflg2" value="0" checked>  既存スタッフのみ
				</td>
			</tr>
			<!-- ２行目 -->
			<tr style="height:50px">
				<td>所属会社</td>
				<td class="colBnriCd" style="text-align:left;">
					<div style="position:relative;width:180px;float:left;margin-top:0px;text-align:left;">
					<select name="haken_kaisya_cd2"  id="hakenkaisyaCombo2" style="width:120px">
						<option value=""></option>
						<?php
						foreach($haken_kaisya_cd as $data) :
						?>
						<option value="<?php echo $data['KAISYA_CD'] ?>"><?php echo $data['KAISYA_NM']?></option>
						<?php
						endforeach;
						?>
					</select>
				</div>
				</td>
				<td>スタッフ名</td>
				<td >
					<input name="staff_nm2" id="staff_nmText2" maxlength="30" type="text" class="zen"  style="width:180px;height:20px;ime-mode:active">
				</td>
				<td><input value="就業実績&#10;CSV抽出" type="submit";div="btnsubmit"  style="width:100px;height:50px"></td>
			</tr>
			</tbody>
		</table>
	</div>
	<input type="hidden" name="flg" value="2">
	<?php echo $this->Form->end() ?>

</div>

<div>

	<?php echo $this->Form->create('HRD020Model', array('url' => '/HRD020/index?flg=3','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
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
