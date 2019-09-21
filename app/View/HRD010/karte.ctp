<?php
			echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSHRD010_KARTE_RELOAD','DCMSHRD010_KARTE_UPDATE','DCMSHRD010_KARTE_GET'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','themes/alert','themes/alphacube','calendar','DCMSHRD'), false, array('inline'=>false));
?>

<style type="text/css">

#lstTable.karteBase .tableCell {
	width:333px;
}


input[type="text"].errorItem, .errorItem {
	background-color: hotpink;
}

a {
	color : #2673C9;
	text-decoration : underline;
}

a:hover {
	color : #ff0000;
	text-decoration:underline;
	cursor : pointer;
}

h2 {
    margin : 15px 0 35px;
    padding-left : 5px;
}

</style>

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
		<li> <?php echo $this->Html->link("品質", array('action' => 'hinshitsu', '?' => array('staff_cd'=> $staff["STAFF_CD"]))) ?> </li>
		<li><a class="active" href="#">カルテ</a></li>
		<li> <?php echo $this->Html->link("就業実績", array('action' => 'jisseki', '?' => array('staff_cd'=> $staff["STAFF_CD"]))) ?> </li>
	</ul>

	<div style="position:relative;height:50px;margin-top:20px;text-align:left;">
		<div style="position:absolute;height:25px;top:00px;left:40px;width:120px;margin-top:2px;">スタッフコード</div>
		<div style="position:absolute;height:25px;top:00px;left:200px;width:140px;" id="staff_cd"><?php echo $staff["STAFF_CD"] ?></div>
		<div style="position:absolute;height:25px;top:00px;left:400px;width:240px;margin-top:2px;">
			<?php echo isset($alreadyExist) ? "評価月" : "<font color='red'>※カルテ情報が登録されていません</font>"; ?>
		</div>
		<div style="position:absolute;height:25px;top:00px;left:500px;width:140px;" id="hyoka_date"><?php echo $karteInfo["YMD_HYOKA"] ?></div>
		<div style="position:absolute;height:25px;top:00px;left:600px;" id="paging">
			<a id="prevKarte" style="position:absolute;width:50px;<?php if(!isset($isPrev)) echo "display:none;"; ?>">＜前へ</a>
			<a id="nextKarte" style="position:absolute;width:50px;left:70px;<?php if(!isset($isNext)) echo "display:none;"; ?>">次へ＞</a>
		</div>
		<div style="position:absolute;height:25px;top:30px;left:40px;width:120px;margin-top:2px;">氏名</div>
		<div style="position:absolute;height:25px;top:30px;left:200px;width:140px;"><?php echo $staff["STAFF_NM"] ?></div>
		<div style="position:absolute;height:25px;top:30px;left:400px;width:120px;margin-top:2px;">評価月</div>
		<div style="position:absolute;height:25px;top:30px;left:500px;width:140px;">
			<input type="text" style="width:55px;height:20px;" id="hyoka_date_input" class="han" maxlength="6" value="<?php echo $karteInfo["YMD_HYOKA_INPUT"] ?>" />
		</div>
		<div id="buttons" style="position:absolute;height:30px;top:40px;left:923px;">
			<input type="button" id="updateButton" style="width:120px;height:30px;" value="更新" />
		</div>
	</div>

	<div style="position:relative;margin-top:30px;margin-left:40px;height:115px;" id="lstTable" class="karteBase">
		<div class="tableRowTitle row_ttl_1" style="width:1020px">
			<div class="tableCell cell_ttl_1" style="width:330px;">強み</div>
			<div class="tableCell cell_ttl_1" style="width:330px;">弱み</div>
			<div class="tableCell cell_ttl_1" style="width:330px;">コメント</div>
		</div>
		<div class="tableRow row_dat" style="width:1020px;height:117px">
			<div class="tableCell cell_dat" style="width:330px;height:110px;overflow:visible;"><textarea id="tuyomi" cols=41 rows=5 maxlength="200" style="position:relative;z-index:526"><?php echo $karteInfo["TUYOMI"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:330px;height:110px;overflow:visible;"><textarea id="yowami" cols=41 rows=5 maxlength="200" style="position:relative;z-index:525"><?php echo $karteInfo["YOWAMI"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:330px;height:110px;overflow:visible;"><textarea id="comment" cols=41 rows=5 maxlength="200" style="position:relative;z-index:524"><?php echo $karteInfo["COMENT"] ?></textarea></div>
		</div>
	</div>

<!-- 基本能力 ---------------------------------------------------------------------------------------------------->
<!-- DOMへのアクセス速度を考慮するためのマーカーdiv -->
<div id="baseSkillArea">
	<div style="position:relative;top:30px;width:200px;text-align:left;margin-left:20px;"><h2 id="baseSkillTitle">【基本能力】</h2></div>
<?php
$remainder = count($kbnSkill);
$colCounter = 0;
$skillNameCounter = 0;
$skillValueCounter = 0;
$roopCounter = 10;
$kmkNmCd = array();

if ($remainder < 10) {
	$roopCounter = $remainder;
}
foreach($kbnSkill as $key => $value) {
	$skillNameCounter++;
	$colCounter++;
	$remainder--;
	array_push($kmkNmCd, $key);

	if ($colCounter == 1) {
?>
	<div style="position:relative;margin-top:30px;margin-left:40px;height:40px;" id="lstTable">
		<div class="tableRowTitle row_ttl_1" <?php if($roopCounter < 10) { echo "style='width:" . ($roopCounter * 110) . "px;'";} else { echo "style='width:1100px'"; } ?>>
<?php
	}
?>
			<div class="tableCell cell_ttl_1" <?php echo "id='baseSkillTitle" . $skillNameCounter . "'" ?> style="width:100px">
				<div <?php echo "id='baseSkillName" . $skillNameCounter . "'" ?>><?php echo $value ?></div>
				<div class="hiddenData KMK_NM_CD"><?php echo $key ?></div>
			</div>
	<?php
	if ($colCounter == 10 || $remainder == 0) {
	?>
		</div>
		<?php 
			$wh = $roopCounter < 10 ? $roopCounter * 110 : 1100;
		?>
		<div class="tableRow row_dat" style="width:<?php echo $wh ?>px; height:34px">
		<?php
		for ($i = 0; $i < $roopCounter; $i++) {
			$skillValueCounter++;
			$selectedSkillValue = array_key_exists($kmkNmCd[$i], $skillInfo) ? $skillInfo[$kmkNmCd[$i]] : "";
		?>
			<div class="tableCell cell_dat" style="width:100px;height:28px">
				<select <?php echo "id='baseSkill" . $skillValueCounter . "'" ?>>
					<option value=""></option>
					<?php
					foreach($kbnSkillLv as $key => $value) {
					?>
					<option value="<?php echo $kmkNmCd[$i] . ":" . $key ?>" <?php if($selectedSkillValue == $key) echo "selected" ?>><?php echo $value?></option>
					<?php
					}
					?>
				</select>
			</div>
		<?php
		}
		?>
		</div>
	</div>
<?php
		$colCounter = 0;
		$kmkNmCd = array();
		if ($remainder < 10) {
			$roopCounter = $remainder;
		}
	}
}
?>
</div>

<!-- 特殊能力 ---------------------------------------------------------------------------------------------------->
<!-- DOMへのアクセス速度を考慮するためのマーカーdiv -->
<div id="SPSkillArea">
	<div style="position:relative;top:30px;width:200px;text-align:left;margin-left:20px;"><h2 id="SPSkillTitle">【業務特殊能力】</h2></div>
<?php
$remainder = count($kbnSkillSp);
$colCounter = 0;
$skillNameCounter = 0;
$skillValueCounter = 0;
$roopCounter = 10;
$kmkNmCd = array();

if ($remainder < 10) {
	$roopCounter = $remainder;
}
foreach($kbnSkillSp as $key => $value) {
	$skillNameCounter++;
	$colCounter++;
	$remainder--;
	array_push($kmkNmCd, $key);

	if ($colCounter == 1) {
?>
	<div style="position:relative;margin-top:30px;margin-left:40px;height:40px;" id="lstTable">
		<div class="tableRowTitle row_ttl_1" <?php if($roopCounter < 10) { echo "style='width:" . ($roopCounter * 110) . "px;'";} else { echo "style='width:1100px'"; } ?>>
<?php
	}
?>
			<div class="tableCell cell_ttl_1" <?php echo "id='SPSkillTitle" . $skillNameCounter . "'" ?> style="width:100px">
				<div <?php echo "id='SPSkillName" . $skillNameCounter . "'" ?>><?php echo $value ?></div>
				<div class="hiddenData KMK_NM_CD"><?php echo $key ?></div>
			</div>
	<?php
	if ($colCounter == 10 || $remainder == 0) {
	?>
		<?php 
			$wh = $roopCounter < 10 ? $roopCounter * 110 : 1100;
		?>
		</div>
		<div class="tableRow row_dat" style="width:<?php echo $wh ?>px; height:34px">
		<?php
		for ($i = 0; $i < $roopCounter; $i++) {
			$skillValueCounter++;
			$selectedSkillValue = array_key_exists($kmkNmCd[$i], $skillSPInfo) ? $skillSPInfo[$kmkNmCd[$i]] : "";
		?>
			<div class="tableCell cell_dat" style="width:100px;height:28px">
				<select <?php echo "id='SPSkill" . $skillValueCounter . "'" ?>>
					<option value=""></option>
					<?php
					foreach($kbnSkillLv as $key => $value) {
					?>
					<option value="<?php echo $kmkNmCd[$i] . ":" . $key ?>" <?php if($selectedSkillValue == $key) echo "selected" ?>><?php echo $value?></option>
					<?php
					}
					?>
				</select>
			</div>
		<?php
		}
		?>
		</div>
	</div>
<?php
		$colCounter = 0;
		$kmkNmCd = array();
		if ($remainder < 10) {
			$roopCounter = $remainder;
		}
	}
}
?>
</div>

<!-- 目標 ---------------------------------------------------------------------------------------------------->
	<div style="position:relative;top:30px;width:200px;text-align:left;margin-left:20px;"><h2>【目標】</h2></div>
	<div id="lstTable" class="objective" style="position:relative;margin-top:30px;margin-left:40px;height:640px">
		<div class="tableRowTitle row_ttl_1" style="width:1050px">
			<div class="tableCell cell_ttl_1" style="width:165px;">１月</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">２月</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">３月</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">４月</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">５月</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">６月</div>
		</div>
		<div class="tableRow row_dat" style="width:1050px;height:117px">
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="mokuhyo1" cols=17 rows=5 maxlength="200" style="position:relative;z-index:523"><?php echo $karteInfo["JAN_MOKUHYO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="mokuhyo2" cols=17 rows=5 maxlength="200" style="position:relative;z-index:522"><?php echo $karteInfo["FEB_MOKUHYO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="mokuhyo3" cols=17 rows=5 maxlength="200" style="position:relative;z-index:521"><?php echo $karteInfo["MAR_MOKUHYO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="mokuhyo4" cols=17 rows=5 maxlength="200" style="position:relative;z-index:520"><?php echo $karteInfo["APR_MOKUHYO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="mokuhyo5" cols=17 rows=5 maxlength="200" style="position:relative;z-index:519"><?php echo $karteInfo["MAY_MOKUHYO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="mokuhyo6" cols=17 rows=5 maxlength="200" style="position:relative;z-index:518"><?php echo $karteInfo["JUN_MOKUHYO"] ?></textarea></div>
		</div>
		<div class="tableRowTitle row_ttl_1" style="width:1050px">
			<div class="tableCell cell_ttl_1" style="width:165px;">達成度</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">達成度</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">達成度</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">達成度</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">達成度</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">達成度</div>
		</div>
		<div class="tableRow row_dat" style="width:1050px;height:117px">
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="tasseido1" cols=17 rows=5 maxlength="200" style="position:relative;z-index:517"><?php echo $karteInfo["JAN_TASSEIDO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="tasseido2" cols=17 rows=5 maxlength="200" style="position:relative;z-index:516"><?php echo $karteInfo["FEB_TASSEIDO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="tasseido3" cols=17 rows=5 maxlength="200" style="position:relative;z-index:515"><?php echo $karteInfo["MAR_TASSEIDO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="tasseido4" cols=17 rows=5 maxlength="200" style="position:relative;z-index:514"><?php echo $karteInfo["APR_TASSEIDO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="tasseido5" cols=17 rows=5 maxlength="200" style="position:relative;z-index:513"><?php echo $karteInfo["MAY_TASSEIDO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="tasseido6" cols=17 rows=5 maxlength="200" style="position:relative;z-index:512"><?php echo $karteInfo["JUN_TASSEIDO"] ?></textarea></div>
		</div>
		<div class="tableRowTitle row_ttl_1" style="width:1050px">
			<div class="tableCell cell_ttl_1" style="width:165px;">７月</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">８月</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">９月</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">１０月</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">１１月</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">１２月</div>
		</div>
		<div class="tableRow row_dat" style="width:1050px;height:117px">
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="mokuhyo7" cols=17 rows=5 maxlength="200" style="position:relative;z-index:511"><?php echo $karteInfo["JUL_MOKUHYO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="mokuhyo8" cols=17 rows=5 maxlength="200" style="position:relative;z-index:510"><?php echo $karteInfo["AUG_MOKUHYO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="mokuhyo9" cols=17 rows=5 maxlength="200" style="position:relative;z-index:509"><?php echo $karteInfo["SEP_MOKUHYO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="mokuhyo10" cols=17 rows=5 maxlength="200" style="position:relative;z-index:508"><?php echo $karteInfo["OCT_MOKUHYO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="mokuhyo11" cols=17 rows=5 maxlength="200" style="position:relative;z-index:507"><?php echo $karteInfo["NOV_MOKUHYO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="mokuhyo12" cols=17 rows=5 maxlength="200" style="position:relative;z-index:506"><?php echo $karteInfo["DEC_MOKUHYO"] ?></textarea></div>
		</div>
		<div class="tableRowTitle row_ttl_1" style="width:1050px">
			<div class="tableCell cell_ttl_1" style="width:165px;">達成度</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">達成度</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">達成度</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">達成度</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">達成度</div>
			<div class="tableCell cell_ttl_1" style="width:165px;">達成度</div>
		</div>
		<div class="tableRow row_dat" style="width:1050px;height:117px">
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="tasseido7" cols=17 rows=5 maxlength="200" style="position:relative;z-index:505"><?php echo $karteInfo["JUL_TASSEIDO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="tasseido8" cols=17 rows=5 maxlength="200" style="position:relative;z-index:504"><?php echo $karteInfo["AUG_TASSEIDO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="tasseido9" cols=17 rows=5 maxlength="200" style="position:relative;z-index:503"><?php echo $karteInfo["SEP_TASSEIDO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="tasseido10" cols=17 rows=5 maxlength="200" style="position:relative;z-index:502"><?php echo $karteInfo["OCT_TASSEIDO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="tasseido11" cols=17 rows=5 maxlength="200" style="position:relative;z-index:501"><?php echo $karteInfo["NOV_TASSEIDO"] ?></textarea></div>
			<div class="tableCell cell_dat" style="width:165px;height:110px;overflow:visible;"><textarea id="tasseido12" cols=17 rows=5 maxlength="200" style="position:relative;z-index:500"><?php echo $karteInfo["DEC_TASSEIDO"] ?></textarea></div>
		</div>
	</div>
	<div id="buttons" style="position:absolute;left:978px;">
		<input type="button" id="updateButton" style="width:120px;height:30px;" value="更新" />
		<div class="footer"></div>
	</div>
</div>
<div class="hiddenData STAFF_CODE" ><?php echo $staff["STAFF_CD"] ?></div>
<div class="hiddenData TIME_STAMP" ><?php  echo $timestamp; ?></div>

<!--/ #wrapper-->