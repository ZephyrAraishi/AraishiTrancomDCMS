<?php
			echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSHRD010_JISSEKI_RELOAD',
					'DCMSHRD010_S_CALENDAR','DCMSKOTEI_DROPDOWN_MENU'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','themes/alert','themes/alphacube','calendar','kotei_dropdown_menu','DCMSHRD'), false, array('inline'=>false));
?>

<script type="text/javascript">
InputCalendar.createOnLoaded('dailyPeriodFrom', {inputReadOnly: false,lang:'ja', weekFirstDay:ProtoCalendar.SUNDAY, format: 'yyyymmdd'});
InputCalendar.createOnLoaded('dailyPeriodTo', {inputReadOnly: false,lang:'ja', weekFirstDay:ProtoCalendar.SUNDAY, format: 'yyyymmdd'});
InputMonthCalendar.createOnLoaded('monthlyPeriodFrom', {inputReadOnly: false,lang:'ja', weekFirstDay:ProtoCalendar.SUNDAY, format: 'yyyymm'});
InputMonthCalendar.createOnLoaded('monthlyPeriodTo', {inputReadOnly: false,lang:'ja', weekFirstDay:ProtoCalendar.SUNDAY, format: 'yyyymm'});
</script>

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
		<li> <?php echo $this->Html->link("カルテ", array('action' => 'karte', '?' => array('staff_cd'=> $staff["STAFF_CD"]))) ?> </li>
		<li><a class="active" href="#">就業実績</a></li>
	</ul>

	<div style="position:relative;height:50px;margin-top:20px;text-align:left;">
		<div style="position:absolute;height:25px;top:00px;left:40px;width:120px;margin-top:2px;">スタッフコード</div>
		<div style="position:absolute;height:25px;top:00px;left:200px;width:140px;" id="staff_cd"><?php echo $staff["STAFF_CD"] ?></div>
		<div style="position:absolute;height:25px;top:30px;left:40px;width:120px;margin-top:2px;">氏名</div>
		<div style="position:absolute;height:25px;top:30px;left:200px;width:140px;"><?php echo $staff["STAFF_NM"] ?></div>
	</div>

	<div class="resizable_s" style="position:relative;margin-top:30px;margin-left:30px;height:167px;width:1120px" id="lstTable">
		<div class="tableRowTitle row_ttl_sp_2" style="width:1080px">
			<div class="tableCellBango cell_ttl_sp_2" style="width:60px"><?php echo Configure::read('Bango'); ?></div>
			<div class="tableCell cell_ttl_sp_2" style="width:100px">大分類</div>
			<div class="tableCell cell_ttl_sp_2" style="width:100px">中分類</div>
			<div class="tableCell cell_ttl_sp_2" style="width:100px">細分類</div>
			<div class="tableCell cell_ttl_sp_2" style="width:100px">延べ時間</div>
			<div style="float:left;width:180px;height:70px;">
				<div class="tableCell cell_ttl_1" style="width:170px;" >時間生産性</div>
				<div class="tableCell cell_ttl_1" style="width:80px">ピース</div>
				<div class="tableCell cell_ttl_1" style="width:80px">アイテム</div>
			</div>
			<div style="float:left;width:180px;height:70px;">
				<div class="tableCell cell_ttl_1" style="width:170px;" >稼動生産性</div>
				<div class="tableCell cell_ttl_1" style="width:80px">ピース</div>
				<div class="tableCell cell_ttl_1" style="width:80px">アイテム</div>
			</div>
			<div class="tableCell cell_ttl_sp_2" style="width:60px">順位</div>
			<div class="tableCell cell_ttl_sp_2" style="width:60px">品質</div>
			<div class="tableCell cell_ttl_sp_2" style="width:60px">順位</div>
		</div>
<?php
		if (!empty($jissekiNearList)) {
			$count = 0;
			foreach($jissekiNearList as $entity) {
				$count++;
?>
			<div class="tableRow row_dat" style="width:1080px">
				<div class="tableCell cell_dat bango" style="width:60px"><?php echo $count ?></div>
				<div class="tableCell cell_dat nm" style="width:100px"><?php echo $entity["BNRI_DAI_RYAKU"] ?></div>
				<div class="tableCell cell_dat nm" style="width:100px"><?php echo $entity["BNRI_CYU_RYAKU"] ?></div>
				<div class="tableCell cell_dat nm" style="width:100px"><?php echo $entity["BNRI_SAI_RYAKU"] ?></div>
				<div class="tableCell cell_dat cnt" style="width:100px"><?php echo number_format($entity["TIME_SYUGYO_KOTEI"], 2) ?></div>
				<div class="tableCell cell_dat cnt" style="width:80px"><?php echo $entity["KBN_URIAGE"] != "01" ? number_format($entity["SEISANSEI_TIME_BUTURYO"], 1) : "" ?></div>
				<div class="tableCell cell_dat cnt" style="width:80px"><?php echo $entity["KBN_URIAGE"] != "01" ? number_format($entity["SEISANSEI_TIME_LINE"], 1) : "" ?></div>
				<div class="tableCell cell_dat cnt" style="width:80px"><?php echo $entity["KBN_URIAGE"] != "01" ? number_format($entity["SEISANSEI_KADO_BUTURYO"], 1) : "" ?></div>
				<div class="tableCell cell_dat cnt" style="width:80px"><?php echo $entity["KBN_URIAGE"] != "01" ? number_format($entity["SEISANSEI_KADO_LINE"], 1) : "" ?></div>
				<div class="tableCell cell_dat cnt" style="width:60px"><?php echo number_format($entity["RANK_SEISANSEI"]) ?></div>
				<div class="tableCell cell_dat cnt" style="width:60px"><?php echo number_format($entity["HINSITU_CNT"]) ?></div>
				<div class="tableCell cell_dat cnt" style="width:60px"><?php echo number_format($entity["RANK_HINSITU"]) ?></div>
			</div>
<?php
			}
		}
?>
	</div>

	<hr style="width:1120px;margin:15px 30px 15px;"/>
<?php echo $this->Form->create(false, array('url' => "/HRD010/jisseki?staff_cd={$staff["STAFF_CD"]}",'type'=>'post', 'inputDefaults' => array('label' => false,'div' => false, 'error' => false))) ?>
	<div style="position:relative;margin-left:40px;text-align:left;height:40px;width:100%;max-width:1020px;margin-top:30px;">
		<div style="position:absolute;margin-top:3px;">
	 	<?php
		 	$radioInitialValue = isset($isDailyType) ? 'daily' : 'monthly';
		  	 echo $this->Form->radio("periodType",
				array('monthly' => '月別', 'daily' => '日別'),
				array('legend' =>false, 'separator' => '&nbsp;&nbsp;', 'value' => $radioInitialValue)
			);
		?>
		</div>
	  	<div style="position:absolute;top:0px;left:150px;margin-top:3px;">実績期間</div>
	  	<?php
	  		// カレンダー付きとそうでないものの切り替え
		  	$monthlyDisplay = '';
		  	$dailyDisplay = '';
		  	if (isset($isDailyType)) {
				$monthlyDisplay = 'display:none;';
			} else {
				$dailyDisplay = 'display:none;';
			}
	  	?>
	  	<div style="position:absolute;top:0px;left:220px;">
	 		<?php echo $this->Form->input('monthlyPeriodFrom', array('maxlength' => '6', 'style' => "height:20px;width:70px;{$monthlyDisplay}", 'class' => 'han')) ?>
	 		<?php echo $this->Form->input('dailyPeriodFrom', array('maxlength' => '8', 'style' => "height:20px;width:70px;{$dailyDisplay}", 'class' => 'han')) ?>
	 		&nbsp;～&nbsp;
	  	</div>
	  	 <div style="position:absolute;top:0px;left:321px;">
	 		<?php echo $this->Form->input('monthlyPeriodTo', array('maxlength' => '6', 'style' => "height:20px;width:70px;{$monthlyDisplay}", 'class' => 'han')) ?>
	 		<?php echo $this->Form->input('dailyPeriodTo', array('maxlength' => '8', 'style' => "height:20px;width:70px;{$dailyDisplay}", 'class' => 'han')) ?>
	  	</div>
	  	<div style="position:absolute;top:0px;left:438px;margin-top:3px;">就業工程</div>
	  	<div style="position:absolute;top:0px;left:508px;">
	    	<!-- 工程コンボ -->
	    	<div id="kotei_dropdown_menu_div" style="display:block;position:relative;width:25px;float:left;margin-top:1px;text-align:left;">
	    		<div id="menu_img"><img src="/DCMS/img/search.png" style="width:24px;height:24px" onclick="menuDisp();"></div>
	    		<div id="ul_class" style="display:none;">
	    			<ul style="display:block;" class="ul_first">
	    				<li style="display:block;position:relative;" value= ""
	    							onClick="setMenuProcess('','','','','','');">
							<div style="height:30px;padding-left:5px;width:120px;cursor:pointer;">（選択なし）</div>
						</li>
<?php

	foreach ($koteiDaiList as $array1) {

		$koteiDaiCd    = (string)$array1['BNRI_DAI_CD'];
		$koteiDaiRyaku = (string)$array1['BNRI_DAI_RYAKU'];
		$dai_flg       = 0;

		foreach ($koteiCyuList as $array2) {

			if ($koteiDaiCd == $array2['BNRI_DAI_CD']) {
				$dai_flg = 1;
				break;
			}
		}

		if($dai_flg == 1){
?>

			<li style="display:block;position:relative;" value="<?php echo $koteiDaiCd ?>" >
				<div style="position:absolute;width:20px;height:10px;right:0px;z-index:0;" >&raquo;</div>
				<div style="height:30px;padding-left:5px;width:120px;cursor:default;"
					onClick="setMenuProcess('<?php echo $koteiDaiCd ."', '" .
									    '' ."', '" .
									    '' ."', '" .
									    $koteiDaiRyaku ."', '" .
									    '' ."', '" .
									    '' ?>');"
					>
					<?php echo $koteiDaiRyaku ?>
				</div>
<?php
		} else {
?>

			<li value="<?php echo $koteiDaiCd ?>">
				<div style="height:30px;padding-left:5px;width:120px;"
					onClick="setMenuProcess('<?php echo $koteiDaiCd ."', '" .
									    '' ."', '" .
									    '' ."', '" .
									    $koteiDaiRyaku ."', '" .
									    '' ."', '" .
									    '' ?>');"
					>
					<?php echo $koteiDaiRyaku ?>
				</div>
<?php
		}
?>
				<ul value="<?php echo $koteiDaiCd ?>" style="display:none;" class="ul_second">
<?php
		foreach ($koteiCyuList as $array2) {

			if ($koteiDaiCd == $array2['BNRI_DAI_CD']) {

				$koteiCyuCd = (string)$array2['BNRI_CYU_CD'];
				$koteiCyuRyaku = (string)$array2['BNRI_CYU_RYAKU'];
				$cyu_flg       = 0;

				foreach ($koteiSaiList as $array3) {

					if ($koteiDaiCd == $array3['BNRI_DAI_CD'] &&
						$koteiCyuCd == $array3['BNRI_CYU_CD']) {
						$cyu_flg = 1;
					}
				}

				if($cyu_flg == 1) {
?>
					<li value="<?php echo $koteiDaiCd . "_" . $koteiCyuCd ?>" >
						<div style="position:absolute;width:20px;height:10px;right:0px;z-index:0;" >&raquo;</div>
						<div style="height:30px;padding-left:5px;width:120px;cursor:default;"
							onClick="setMenuProcess('<?php echo $koteiDaiCd ."', '" .
											    $koteiCyuCd ."', '" .
											    '' ."', '" .
											    $koteiDaiRyaku ."', '" .
											    $koteiCyuRyaku ."', '" .
											    '' ?>');"
							>
							 <?php echo $koteiCyuRyaku ?>
						</div>
<?php
				} else {
?>
					<li value="<?php echo $koteiDaiCd . "_" . $koteiCyuCd ?>" >
						<div style="height:30px;padding-left:5px;width:120px;"
							onClick="setMenuProcess('<?php echo $koteiDaiCd ."', '" .
											    $koteiCyuCd ."', '" .
											    '' ."', '" .
											    $koteiDaiRyaku ."', '" .
											    $koteiCyuRyaku ."', '" .
											    '' ?>');"
							>
							 <?php echo $koteiCyuRyaku ?>
						</div>
<?php
				}

?>
						<ul value="<?php echo $koteiDaiCd . "_" . $koteiCyuCd ?>" style="display:none;" class="ul_third">
<?php
				foreach ($koteiSaiList as $array3) {

					if ($koteiDaiCd == $array3['BNRI_DAI_CD'] &&
						$koteiCyuCd == $array3['BNRI_CYU_CD']) {

							$koteiSaiCd = (string)$array3['BNRI_SAI_CD'];
							$koteiSaiRyaku = (string)$array3['BNRI_SAI_RYAKU'];
?>
							<li value="<?php echo $koteiDaiCd . "_" . $koteiCyuCd  . "_" . $koteiSaiCd ?>">
								<div style="height:30px;padding-left:5px;width:120px;cursor:pointer;"
											onClick="setMenuProcess('<?php echo $koteiDaiCd ."', '" .
								 											 $koteiCyuCd ."', '" .
									 										 $koteiSaiCd ."', '" .
										 								     $koteiDaiRyaku ."', '" .
											 								 $koteiCyuRyaku ."', '" .
												 						     $koteiSaiRyaku ?>');">
									<?php echo $koteiSaiRyaku ?>
								</div>
							</li>
<?php
					}
				}
?>
						</ul>
					</li>
<?php
			}
		}
?>
				</ul>
			</li>
<?php
	}
?>
								</ul>
								<input type="hidden" name="kotei" id="koteiCd" value="">
							</div>
						</div>
	    	<div id="processSelect" style="display:block;position:relative;width:250px;float:left;margin-top:1px;text-align:left;">
				<div style="position:relative;width:180px;float:left;margin-top:0px;text-align:left;">
					<input id="koteiText" class="calendar han" type="text" value=""
						 style="width:220px;height:20px" readonly>
				</div>
			</div>
			<div style="display:none;" id="kotei_hidden"><?php if(!empty($kotei_hidden)) echo $kotei_hidden;?></div>
			<!-- /工程コンボ -->
        </div>
	  	<?php //echo $this->Form->hidden("staff_cd", array("value"=>$staff["STAFF_CD"]));?>
	  	<div style="position:absolute;top:-3px;width:100px;left:900px;">
	  		<div style="text-align:right"><?php echo $this->Form->submit('検索', array('div' => false)); ?></div>
		</div>
	</div>

<?php echo $this->Form->end() ?>

	<hr style="width:1120px;margin:15px 30px 15px;"/>

	<div class="pagaerNavi" style="text-align:left;float:left;margin-left:30px;" ><?php if (!empty($navi)) print($navi);?></div>
	<div class="resizable_s" style="margin-left:30px;height:375px;max-width:1120px;" id="lstTable">
		<div class="tableRowTitle row_ttl_sp_2" style="width:1170px">
			<div class="tableCell cell_ttl_sp_2" style="width:60px"><?php echo Configure::read('Bango'); ?></div>
			<div class="tableCell cell_ttl_sp_2" style="width:100px;" >日付</div>
			<div class="tableCell cell_ttl_sp_2" style="width:100px;">大分類</div>
			<div class="tableCell cell_ttl_sp_2" style="width:100px;">中分類</div>
			<div class="tableCell cell_ttl_sp_2" style="width:100px;">細分類</div>
			<div class="tableCell cell_ttl_sp_2" style="width:80px;">延べ時間</div>
			<div style="float:left;width:180px;height:70px;">
				<div class="tableCell cell_ttl_1" style="width:170px;" >時間生産性</div>
				<div class="tableCell cell_ttl_1" style="width:80px;">ピース</div>
				<div class="tableCell cell_ttl_1" style="width:80px;">アイテム</div>
			</div>
			<div style="float:left;width:180px;height:70px;">
				<div class="tableCell cell_ttl_1" style="width:170px;" >稼動生産性</div>
				<div class="tableCell cell_ttl_1" style="width:80px;">ピース</div>
				<div class="tableCell cell_ttl_1" style="width:80px;">アイテム</div>
			</div>
			<div class="tableCell cell_ttl_sp_2" style="width:60px">順位</div>
			<div class="tableCell cell_ttl_sp_2" style="width:60px">品質</div>
			<div class="tableCell cell_ttl_sp_2" style="width:60px">順位</div>
		</div>
<?php
		if (!empty($jissekiList)) {
			$count = 0;
			foreach($jissekiList as $entity) {
				$count++;
				if (isset($entity['YMD'])) {
					$jissekiDate = date_format(date_create($entity['YMD']),"Y/m/d");
				} else {
					$jissekiDate = date_format(date_create($entity['YM'] . '01'),"Y/m");
				}
?>
			<div class="tableRow row_dat" style="width:1170px">
				<div class="tableCell cell_dat bango" style="width:60px"><?php echo $pageStartIdx + $count ?></div>
				<div class="tableCell cell_dat date" style="width:100px;"><?php echo $jissekiDate ?></div>
				<div class="tableCell cell_dat nm" style="width:100px;"><?php echo $entity["BNRI_DAI_RYAKU"] ?></div>
				<div class="tableCell cell_dat nm" style="width:100px;"><?php echo $entity["BNRI_CYU_RYAKU"] ?></div>
				<div class="tableCell cell_dat nm" style="width:100px;"><?php echo $entity["BNRI_SAI_RYAKU"] ?></div>
				<div class="tableCell cell_dat cnt" style="width:80px;"><?php echo number_format($entity["TIME_SYUGYO_KOTEI"], 2) ?></div>
				<div class="tableCell cell_dat cnt" style="width:80px;"><?php echo $entity["KBN_URIAGE"] != "01" ? number_format($entity["SEISANSEI_TIME_BUTURYO"], 1) : '' ?></div>
				<div class="tableCell cell_dat cnt" style="width:80px;"><?php echo $entity["KBN_URIAGE"] != "01" ? number_format($entity["SEISANSEI_TIME_LINE"], 1) : '' ?></div>
				<div class="tableCell cell_dat cnt" style="width:80px;"><?php echo $entity["KBN_URIAGE"] != "01" ? number_format($entity["SEISANSEI_KADO_BUTURYO"], 1) : '' ?></div>
				<div class="tableCell cell_dat cnt" style="width:80px;"><?php echo $entity["KBN_URIAGE"] != "01" ? number_format($entity["SEISANSEI_KADO_LINE"], 1) : '' ?></div>
				<div class="tableCell cell_dat cnt" style="width:60px"><?php echo number_format($entity["RANK_SEISANSEI"]) ?></div>
				<div class="tableCell cell_dat cnt" style="width:60px"><?php echo number_format($entity["HINSITU_CNT"]) ?></div>
				<div class="tableCell cell_dat cnt" style="width:60px"><?php echo number_format($entity["RANK_HINSITU"]) ?></div>
			</div>
<?php
			}
		}
?>
	</div>
	<div class="footer"></div>
</div>
<div class="hiddenData RESULT_COUNT"><?php echo count($jissekiList) ?></div>

<!--/ #wrapper-->