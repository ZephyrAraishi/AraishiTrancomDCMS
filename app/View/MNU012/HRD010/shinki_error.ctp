<?php
			echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSHRD010_S_RELOAD','DCMSHRD010_S_IMAGE'
					,'DCMSHRD010_S_UPDATE','DCMSHRD010_S_CALENDAR'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','themes/alert','themes/alphacube','calendar','DCMSHRD'), false, array('inline'=>false));

?>

<script type="text/javascript"><!--
InputCalendar.createOnLoaded('ymd_birth', {lang:'ja',startYear: 1900, weekFirstDay:ProtoCalendar.SUNDAY, format: 'yyyymmdd'});
InputCalendar.createOnLoaded('ymd_syugyo_st', {lang:'ja',startYear: 1900, weekFirstDay:ProtoCalendar.SUNDAY, format: 'yyyymmdd'});
InputCalendar.createOnLoaded('ymd_syugyo_ed', {lang:'ja',startYear: 1900, weekFirstDay:ProtoCalendar.SUNDAY, format: 'yyyymmdd'});
//--></script>

<!-- WRAPPER -->
<div id="wrapper_s">

	<div id="HRDmenu">
		<ul class="tab clear">
			<li class="none"><a href="/DCMS/HRD010/shinki" class="blue">新規</a></li>
			<li><a href="/DCMS/HRD010/index" class="red">更新</a></li>
		</ul>
	</div>
	<div style="position:relative;height:25px;margin-top:20px;text-align:left;font-size:14pt;margin-left:30px;">【基本情報】</div>
	<div style="position:relative;height:270px;margin-top:20px;text-align:left;">
		
		<div style="position:absolute;height:25px;top:00px;left:40px;width:120px;margin-top:2px;">スタッフコード</div>
		<div style="position:absolute;height:25px;top:00px;left:200px;width:140px;">
			<input type="text" style="width:140px;height:20px;" id="staff_cd" name="staff_cd" class="han" maxlength="10" value="<?php echo $data->STAFF_CD ?>">
		</div>
		<div style="position:absolute;height:25px;top:30px;left:40px;width:120px;margin-top:2px;">氏名</div>
		<div style="position:absolute;height:25px;top:30px;left:200px;width:140px;">
			<input type="text" style="width:140px;height:20px;" id="staff_nm" name="staff_nm" maxlength="30" value="<?php echo $data->STAFF_NM ?>">
		</div>
		<div style="position:absolute;height:25px;top:60px;left:40px;width:120px;margin-top:2px;">パスワード</div>
		<div style="position:absolute;height:25px;top:60px;left:200px;width:140px;">
			<input type="text" style="width:140px;height:20px;" id="password" name="password" class="han" maxlength="30" value="<?php echo $data->PASSWORD ?>">
		</div>
		<div style="position:absolute;height:25px;top:90px;left:40px;width:120px;margin-top:2px;">IC番号</div>
		<div style="position:absolute;height:25px;top:90px;left:200px;width:140px;">
			<input type="text" style="width:140px;height:20px;" id="ic_no" name="ic_no" class="han" maxlength="16" value="<?php echo $data->IC_NO ?>">
		</div>
		<div style="position:absolute;height:25px;top:120px;left:40px;width:120px;margin-top:2px;">性別</div>
		<div style="position:absolute;height:25px;top:120px;left:200px;width:140px;">
			<select id="kbn_sex" name="kbn_sex">
				<option value="" <?php if($data->KBN_SEX == "") echo "selected" ?> ></option>
<?php
		foreach($kbn_sex as $array) {
?>
				<option value="<?php echo $array["MEI_CD"] ?>" <?php if($data->KBN_SEX == $array["MEI_CD"]) echo "selected" ?> ><?php echo $array["MEI_1"] ?></option>
<?php
		}
?>
			</select>
		</div>
		<div style="position:absolute;height:25px;top:150px;left:40px;width:120px;margin-top:2px;">生年月日</div>
		<div style="position:absolute;height:25px;top:150px;left:200px;width:140px;">
			<input type="text" style="width:140px;height:20px;" id="ymd_birth" name="ymd_birth" class="han" maxlength="8" value="<?php echo $data->YMD_BIRTH ?>">
		</div>
		<div style="position:absolute;height:25px;top:180px;left:40px;width:120px;margin-top:2px;">派遣会社</div>
		<div style="position:absolute;height:25px;top:180px;left:200px;width:140px;">
			<select id="haken_kaisya_cd" name="haken_kaisya_cd">
				<option value=""  <?php if($data->HAKEN_KAISYA_CD == "") echo "selected" ?>></option>
<?php
		foreach($haken_kaisya_cd as $array) {
?>
				<option value="<?php echo $array["KAISYA_CD"] ?>" <?php if($data->HAKEN_KAISYA_CD == $array["KAISYA_CD"]) echo "selected" ?> ><?php echo $array["KAISYA_NM"] ?></option>
<?php
		}
?>
			</select>
		</div>
		<div style="position:absolute;height:260px;top:0px;left:440px;width:360px;text-align:center;border:1px solid lightGray;">
			<iframe name="temp" width="0" height="0" frameborder="0"></iframe>
			<form enctype="multipart/form-data" action="/DCMS/HRD010/image_upload" method="POST" target="temp">
				<div id="uplist" style="margin-top:15px;">
					<img src="<?php if($data->IMAGE != "") echo "data:image/png;base64," . $data->IMAGE ; else echo "/DCMS/img/no-image.gif";?>" id="upload-image"><br>
				</div>
				<div style="position:absolute;height:30px;top:180px;left:60px;width:260px;">
					<div style="position:relative;width:200px;overflow:hidden;float:left;margin-top:3px;text-align:left;">
                        <input name="imagePath" type="file" size="300" id="imagePath">
                    </div>
                    <div style="position:relative;width:60px;float:left;margin-top:30px;margin-top:3px">
						<input type="button" value="参照" style="height:24px;width:60px;margin-left:2px;" id="refButton" >
					</div>
				</div>
				<div style="position:absolute;height:30px;top:220px;left:0px;width:360px">
					<input type="submit" name="save" value="画像アップロード" style="width:200px;" id="imageUploadButton">
				</div>
			</form>
		</div>
	</div>
	<div style="position:relative;margin-top:10px;margin-left:40px;" id="lstTable1" >
		<div style="text-align:left;position:relative;" >拠点選択</div>
		<div class="tableRowTitle row_ttl_1" style="height:26px;width:780px;margin-left:1px;">
			<div class="tableCell cell_ttl_1" style="width:70px;height:22px;padding-top:3px;" >代表拠点</div>
			<div class="tableCell cell_ttl_1" style="width:70px;height:22px;padding-top:3px;" >可能拠点</div>
			<div class="tableCell cell_ttl_1" style="width:300px;height:22px;padding-top:3px;" >荷主</div>
			<div class="tableCell cell_ttl_1" style="width:300px;height:22px;padding-top:3px;" >組織</div>
		</div>
<?php
		$kyotenArray = $data->KYOTEN;
		$count = 0;

		foreach($kyoten as $array) {
		
			$cellsArray = $kyotenArray[$count]->cells;
			$dataArray = $kyotenArray[$count]->data;
?>
		<div class="tableRow row_dat" style="height:26px;width:780px;margin-left:1px;">
			<div class="tableCell cell_dat" style="width:70px;height:22px;padding-top:3px;" >
				<input class="daihyo" type="radio" name="daihyo" <?php if ($cellsArray[0] == true) echo "checked"; ?>>
			</div>
			<div class="tableCell cell_dat" style="width:70px;height:22px;padding-top:3px;" >
				<input class="kanoKyoten" type="checkbox" <?php if ($cellsArray[1] == true) echo "checked"; ?>></div>
			<div class="tableCell cell_dat nm" style="width:300px;height:22px;padding-top:3px;" ><?php echo $array["NINUSI_RYAKU"] ?></div>
			<div class="tableCell cell_dat nm" style="width:300px;height:22px;padding-top:3px;" ><?php echo $array["SOSIKI_RYAKU"] ?></div>
			<div class="hiddenData"><?php echo $array["NINUSI_CD"] ?></div>
			<div class="hiddenData"><?php echo $array["SOSIKI_CD"] ?></div>
		</div>
<?php
			$count++;
		}
?>
	</div>
	<div style="position:relative;height:25px;margin-top:20px;text-align:left;font-size:14pt;margin-left:30px;">【職務設定】</div>
	<div style="position:relative;height:60px;margin-top:20px;text-align:left;">
		<div style="position:absolute;height:25px;top:00px;left:40px;width:120px;margin-top:2px;">役職</div>
		<div style="position:absolute;height:25px;top:00px;left:200px;width:140px;">
			<select id="kbn_pst" name="kbn_pst">
				<option value="" <?php if($data->KBN_PST == "") echo "selected" ?>></option>
<?php
		foreach($kbn_pst as $array) {
?>
				<option value="<?php echo $array["MEI_CD"] ?>" <?php if($data->KBN_PST == $array["MEI_CD"]) echo "selected" ?>><?php echo $array["MEI_1"] ?></option>
<?php
		}
?>
			</select>
		</div>
		<div style="position:absolute;height:25px;top:30px;left:40px;width:120px;margin-top:2px;">契約形態</div>
		<div style="position:absolute;height:25px;top:30px;left:200px;width:140px;">
			<select id="kbn_keiyaku" name="kbn_keiyaku">
				<option value="" <?php if($data->KBN_KEIYAKU == "") echo "selected" ?>></option>
<?php
		foreach($kbn_keiyaku as $array) {
?>
				<option value="<?php echo $array["MEI_CD"] ?>" <?php if($data->KBN_KEIYAKU == $array["MEI_CD"]) echo "selected" ?>><?php echo $array["MEI_1"] ?></option>
<?php
		}
?>
			</select>
		</div>
		<div style="position:absolute;height:25px;top:00px;left:400px;width:120px;margin-top:2px;">ピッキング</div>
		<div style="position:absolute;height:25px;top:00px;left:540px;width:140px;">
			<select id="kbn_pick" name="kbn_pick">
				<option value="" <?php if($data->KBN_PICK == "") echo "selected" ?>></option>
<?php
		foreach($kbn_pick as $array) {
?>
				<option value="<?php echo $array["MEI_CD"] ?>" <?php if($data->KBN_PICK == $array["MEI_CD"]) echo "selected" ?>><?php echo $array["MEI_1"] ?></option>
<?php
		}
?>
			</select>
		</div>
	</div>
	<div style="position:relative;margin-left:40px;margin-top:10px;" id="lstTable2">
		<div style="text-align:left;position:relative;" >優先ゾーン</div>
		<div class="tableRowTitle row_ttl_1" style="height:26px;width:390px;margin-left:1px;">
			<div class="tableCell cell_ttl_1" style="width:70px;height:22px;padding-top:3px;" >選択</div>
			<div class="tableCell cell_ttl_1" style="width:300px;height:22px;padding-top:3px;" >ゾーン</div>
		</div>
<?php
		$yusenzoneArray = $data->YUSEN_ZONE;
		$count = 0;

		foreach($zone as $array) {
		
		
			$cellsArray = $yusenzoneArray[$count]->cells;
			$dataArray = $yusenzoneArray[$count]->data;
?>
		<div class="tableRow row_dat" style="height:26px;width:390px;margin-left:1px;">
			<div class="tableCell cell_dat" style="width:70px;height:22px;padding-top:3px;" >
				<input type="checkbox" <?php if ($cellsArray[0] == true) echo "checked"; ?>>
			</div>
			<div class="tableCell cell_dat" style="width:300px;height:22px;padding-top:3px;" ><?php echo $array["MEI_1"] ?></div>
			<div class="hiddenData"><?php echo $array["MEI_CD"] ?></div>
		</div>
<?php
			$count++;
		}
?>
	</div>
	<div style="position:relative;height:25px;margin-top:20px;text-align:left;font-size:14pt;margin-left:30px;">【原価設定】</div>
	<div style="position:relative;height:120px;margin-top:20px;text-align:left;">
		<div style="position:absolute;height:25px;top:00px;left:40px;width:120px;margin-top:2px;">給与ランク</div>
		<div style="position:absolute;height:25px;top:00px;left:200px;width:140px;">
			<select id="rank" name="rank">
				<option value="" <?php if($data->RANK == "") echo "selected" ?>></option>
<?php
		foreach($rank as $array) {
?>
				<option value="<?php echo $array["RANK"] ?>" <?php if($data->RANK == $array["RANK"]) echo "selected" ?>><?php echo $array["RANK"] ?></option>
<?php
		}
?>
			</select>
		</div>
		<div style="position:absolute;height:25px;top:30px;left:40px;width:120px;margin-top:2px;">所定時間</div>
		<div style="position:absolute;height:25px;top:30px;left:200px;width:140px;">
			<input type="text" style="width:110px;height:20px;" id="kin_syotei" name="kin_syotei" class="han" maxlength="12" value="<?php echo $data->KIN_SYOTEI ?>">　円
		</div>
		<div style="position:absolute;height:25px;top:60px;left:40px;width:120px;margin-top:2px;">時間外</div>
		<div style="position:absolute;height:25px;top:60px;left:200px;width:140px;">
			<input type="text" style="width:110px;height:20px;" id="kin_jikangai" name="kin_jikangai" class="han" maxlength="12" value="<?php echo $data->KIN_JIKANGAI ?>">　円
		</div>
		<div style="position:absolute;height:25px;top:90px;left:40px;width:120px;margin-top:2px;">深夜時間</div>
		<div style="position:absolute;height:25px;top:90px;left:200px;width:140px;">
			<input type="text" style="width:110px;height:20px;" id="kin_sny" name="kin_sny" class="han" maxlength="12" value="<?php echo $data->KIN_SNY ?>">　円
		</div>
		<div style="position:absolute;height:25px;top:30px;left:400px;width:120px;margin-top:2px;">休出時間</div>
		<div style="position:absolute;height:25px;top:30px;left:540px;width:140px;">
			<input type="text" style="width:110px;height:20px;" id="kin_kst" name="kin_kst" class="han" maxlength="12" value="<?php echo $data->KIN_KST ?>">　円
		</div>
		<div style="position:absolute;height:25px;top:60px;left:400px;width:120px;margin-top:2px;">法廷休出時間</div>
		<div style="position:absolute;height:25px;top:60px;left:540px;width:140px;">
			<input type="text" style="width:110px;height:20px;" id="kin_h_kst" name="kin_h_kst" class="han" maxlength="12" value="<?php echo $data->KIN_H_KST ?>">　円
		</div>
		<div style="position:absolute;height:25px;top:90px;left:400px;width:120px;margin-top:2px;">法廷休出深夜時間</div>
		<div style="position:absolute;height:25px;top:90px;left:540px;width:140px;">
			<input type="text" style="width:110px;height:20px;" id="kin_h_kst_sny" name="kin_h_kst_sny" class="han" maxlength="12" value="<?php echo $data->KIN_H_KST_SNY ?>">　円
		</div>
	</div>
	<div style="position:relative;height:25px;margin-top:20px;text-align:left;font-size:14pt;margin-left:30px;">【就業条件】</div>
	<div style="position:relative;margin-top:30px;margin-left:40px;">
		<div class="tableRowTitle row_ttl_1" style="height:26px;width:709px;margin-left:1px;">
			<div class="tableCell" style="width:70px;height:22px;padding-top:3px;" >曜日</div>
			<div class="tableCell" style="width:90px;height:22px;padding-top:3px;" >出社</div>
			<div class="tableCell" style="width:90px;height:22px;padding-top:3px;" >退社</div>
			<div class="tableCell" style="width:419px;height:22px;padding-top:3px;" >備考</div>
		</div>
		<div class="tableRow row_dat" style="height:32px;width:709px;margin-left:1px;">			
			<div class="tableCell cell_dat" style="width:70px;height:25px;padding-top:6px;" >日曜日</div>
			<div class="tableCell cell_dat" style="width:90px;height:28px;padding-top:3px;" >
				<input type="text" style="width:70px;height:20px;" id="sun_time_st" name="sun_time_st" class="han" maxlength="4" value="<?php echo $data->SUN_TIME_ST ?>">
			</div>
			<div class="tableCell cell_dat" style="width:90px;height:28px;padding-top:3px;" >
				<input type="text" style="width:70px;height:20px;" id="sun_time_ed" name="sun_time_ed" class="han" maxlength="4" value="<?php echo $data->SUN_TIME_ED ?>">
			</div>
			<div class="tableCell cell_dat" style="width:419px;height:28px;padding-top:3px;" >
				<input type="text" style="width:400px;height:20px;" id="sun_biko" name="sun_biko" maxlength="30" value="<?php echo $data->SUN_BIKO ?>">
			</div>
		</div>
		<div class="tableRow row_dat" style="height:32px;width:709px;margin-left:1px;">			
			<div class="tableCell cell_dat" style="width:70px;height:25px;padding-top:6px;" >月曜日</div>
			<div class="tableCell cell_dat" style="width:90px;height:28px;padding-top:3px;" >
				<input type="text" style="width:70px;height:20px;" id="mon_time_st" name="mon_time_st" class="han" maxlength="4" value="<?php echo $data->MON_TIME_ST ?>">
			</div>
			<div class="tableCell cell_dat" style="width:90px;height:28px;padding-top:3px;" >
				<input type="text" style="width:70px;height:20px;" id="mon_time_ed" name="mon_time_ed" class="han" maxlength="4" value="<?php echo $data->MON_TIME_ED ?>">
			</div>
			<div class="tableCell cell_dat" style="width:419px;height:28px;padding-top:3px;" >
				<input type="text" style="width:400px;height:20px;" id="mon_biko" name="mon_biko" maxlength="30" value="<?php echo $data->MON_BIKO ?>">
			</div>
		</div>
		<div class="tableRow row_dat" style="height:32px;width:709px;margin-left:1px;">			
			<div class="tableCell cell_dat" style="width:70px;height:25px;padding-top:6px;" >火曜日</div>
			<div class="tableCell cell_dat" style="width:90px;height:28px;padding-top:3px;" >
				<input type="text" style="width:70px;height:20px;" id="tue_time_st" name="tue_time_st" class="han" maxlength="4" value="<?php echo $data->TUE_TIME_ST ?>">
			</div>
			<div class="tableCell cell_dat" style="width:90px;height:28px;padding-top:3px;" >
				<input type="text" style="width:70px;height:20px;" id="tue_time_ed" name="tue_time_ed" class="han" maxlength="4" value="<?php echo $data->TUE_TIME_ED ?>">
			</div>
			<div class="tableCell cell_dat" style="width:419px;height:28px;padding-top:3px;" >
				<input type="text" style="width:400px;height:20px;" id="tue_biko" name="tue_biko" maxlength="30" value="<?php echo $data->TUE_BIKO ?>">
			</div>
		</div>
		<div class="tableRow row_dat" style="height:32px;width:709px;margin-left:1px;">			
			<div class="tableCell cell_dat" style="width:70px;height:25px;padding-top:6px;" >水曜日</div>
			<div class="tableCell cell_dat" style="width:90px;height:28px;padding-top:3px;" >
				<input type="text" style="width:70px;height:20px;" id="wed_time_st" name="wed_time_st" class="han" maxlength="4" value="<?php echo $data->WED_TIME_ST ?>">
			</div>
			<div class="tableCell cell_dat" style="width:90px;height:28px;padding-top:3px;" >
				<input type="text" style="width:70px;height:20px;" id="wed_time_ed" name="wed_time_ed" class="han" maxlength="4" value="<?php echo $data->WED_TIME_ED ?>">
			</div>
			<div class="tableCell cell_dat" style="width:419px;height:28px;padding-top:3px;" >
				<input type="text" style="width:400px;height:20px;" id="wed_biko" name="wed_biko" maxlength="30" value="<?php echo $data->WED_BIKO ?>">
			</div>
		</div>
		<div class="tableRow row_dat" style="height:32px;width:709px;margin-left:1px;">			
			<div class="tableCell cell_dat" style="width:70px;height:25px;padding-top:6px;" >木曜日</div>
			<div class="tableCell cell_dat" style="width:90px;height:28px;padding-top:3px;" >
				<input type="text" style="width:70px;height:20px;" id="thu_time_st" name="thu_time_st" class="han" maxlength="4" value="<?php echo $data->THU_TIME_ST ?>">
			</div>
			<div class="tableCell cell_dat" style="width:90px;height:28px;padding-top:3px;" >
				<input type="text" style="width:70px;height:20px;" id="thu_time_ed" name="thu_time_ed" class="han" maxlength="4" value="<?php echo $data->THU_TIME_ED ?>">
			</div>
			<div class="tableCell cell_dat" style="width:419px;height:28px;padding-top:3px;" >
				<input type="text" style="width:400px;height:20px;" id="thu_biko" name="thu_biko" maxlength="30" value="<?php echo $data->THU_BIKO ?>">
			</div>
		</div>
		<div class="tableRow row_dat" style="height:32px;width:709px;margin-left:1px;">			
			<div class="tableCell cell_dat" style="width:70px;height:25px;padding-top:6px;" >金曜日</div>
			<div class="tableCell cell_dat" style="width:90px;height:28px;padding-top:3px;" >
				<input type="text" style="width:70px;height:20px;" id="fri_time_st" name="fri_time_st" class="han" maxlength="4" value="<?php echo $data->FRI_TIME_ST ?>">
			</div>
			<div class="tableCell cell_dat" style="width:90px;height:28px;padding-top:3px;" >
				<input type="text" style="width:70px;height:20px;" id="fri_time_ed" name="fri_time_ed" class="han" maxlength="4" value="<?php echo $data->FRI_TIME_ED ?>">
			</div>
			<div class="tableCell cell_dat" style="width:419px;height:28px;padding-top:3px;" >
				<input type="text" style="width:400px;height:20px;" id="fri_biko" name="fri_biko" maxlength="30" value="<?php echo $data->FRI_BIKO ?>">
			</div>
		</div>
		<div class="tableRow row_dat" style="height:32px;width:709px;margin-left:1px;">			
			<div class="tableCell cell_dat" style="width:70px;height:25px;padding-top:6px;" >土曜日</div>
			<div class="tableCell cell_dat" style="width:90px;height:28px;padding-top:3px;" >
				<input type="text" style="width:70px;height:20px;" id="sat_time_st" name="sat_time_st" class="han" maxlength="4" value="<?php echo $data->SAT_TIME_ST ?>">
			</div>
			<div class="tableCell cell_dat" style="width:90px;height:28px;padding-top:3px;" >
				<input type="text" style="width:70px;height:20px;" id="sat_time_ed" name="sat_time_ed" class="han" maxlength="4" value="<?php echo $data->SAT_TIME_ED ?>">
			</div>
			<div class="tableCell cell_dat" style="width:419px;height:28px;padding-top:3px;" >
				<input type="text" style="width:400px;height:20px;" id="sat_biko" name="sat_biko" maxlength="30" value="<?php echo $data->SAT_BIKO ?>">
			</div>
		</div>
	</div>
	<div style="position:relative;height:90px;margin-top:20px;text-align:left;">
		<div style="position:absolute;height:25px;top:00px;left:40px;width:120px;margin-top:2px;">就業開始日</div>
		<div style="position:absolute;height:25px;top:00px;left:200px;width:140px;">
			<input type="text" style="width:140px;height:20px;" id="ymd_syugyo_st" name="ymd_syugyo_st" class="han" maxlength="8" value="<?php echo $data->YMD_SYUGYO_ST ?>">
		</div>
		<div style="position:absolute;height:25px;top:30px;left:40px;width:120px;margin-top:2px;">就業終了日</div>
		<div style="position:absolute;height:25px;top:30px;left:200px;width:140px;">
			<input type="text" style="width:140px;height:20px;" id="ymd_syugyo_ed" name="ymd_syugyo_ed" class="han" maxlength="8" value="<?php echo $data->YMD_SYUGYO_ED ?>">
		</div>
		<div style="position:absolute;height:25px;top:60px;left:40px;width:120px;margin-top:2px;">出勤日数上限</div>
		<div style="position:absolute;height:25px;top:60px;left:200px;width:140px;">
			<input type="text" style="width:110px;height:20px;" id="syukin_nisu_max" name="syukin_nisu_max" class="han" maxlength="2" value="<?php echo $data->SYUKIN_NISU_MAX ?>">　日
		</div>
		<div style="position:absolute;height:25px;top:0px;left:400px;width:120px;margin-top:2px;">出勤時間上限</div>
		<div style="position:absolute;height:25px;top:0px;left:560px;width:160px;">
			<input type="text" style="width:110px;height:20px;" id="syukin_time_max" name="syukin_time_max" class="han" maxlength="4" value="<?php echo $data->SYUKIN_TIME_MAX ?>">　時間
		</div>
		<div style="position:absolute;height:25px;top:30px;left:400px;width:120px;margin-top:2px;">賃金上限</div>
		<div style="position:absolute;height:25px;top:30px;left:560px;width:140px;">
			<input type="text" style="width:110px;height:20px;" id="chingin_max" name="chingin_max" class="han"  maxlength="12" value="<?php echo $data->CHINGIN_MAX ?>">　円
		</div>
	</div>
	<div style="position:relative;height:25px;margin-top:20px;text-align:left;font-size:14pt;margin-left:30px;">【追記事項】</div>
	<div style="position:relative;height:180px;margin-top:20px;text-align:left;">
		<div style="position:absolute;height:180px;top:0px;left:40px;width:700px;margin-top:2px;">
			<textarea style="width:700px;height:150px;resize: none;" id="tuiki_jiko"  maxlength="3000"><?php echo $data->TUIKI_JIKO ?></textarea>
		</div>
	</div>
	<div style="position:relative;height:50px;margin-left:40px;margin-top:20px;text-align:right;" >
		<div style="position:absolute;height:30px;top:0px;left:40px;width:650px;margin-top:2px;">
			<input type="button" value="登録" class="btn" id="updateButton" >
		</div>
	</div>
</div>
<!--/ #wrapper-->                 