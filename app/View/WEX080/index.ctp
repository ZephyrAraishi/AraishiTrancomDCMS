<?php
			echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSWEX080_RELOAD','DCMSWEX080_CALENDAR','DCMSWEX080_UPDATE'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSWPO'), false, array('inline'=>false));

?>
            <div id="wrapper_s">
				<div style="height:60px;width:1000px;">
 						<div style="position:relative;width:50px;float:left;margin-top:13px;text-align:left;" >日付</div>
				    	<div style="position:relative;width:110px;float:left;margin-top:10px;text-align:left;">
					    	<input name="ymd_sime" id="ymd_sime" maxlength="8" class="calendar han" type="text" style="width:80px;height:20px" value="<?php if(!empty($yesterday)) echo $yesterday; ?>">
				    	</div>
				    	<div style="position:relative;width:300px;float:left;margin-top:6px;text-align:left;text-align:right;">
				    		<input value="処理実行" type="button" id="updateButton" >
				    	</div>
				</div>

				<!-- メインテーブル-->
				
				<div class="resizable_s" style="height: 450px;" id="lstTable" >
					<div class="tableRowTitle row_ttl_1" style="width:750px;">
						<div class="tableCell cell_ttl_1" style="width:60px;" ><?php echo Configure::read('Bango'); ?></div>
						<div class="tableCell cell_ttl_1" style="width:180px;" >実施日時</div>
						<div class="tableCell cell_ttl_1" style="width:110px;" >実施者</div>
						<div class="tableCell cell_ttl_1" style="width:110px;" >締年月日</div>
						<div class="tableCell cell_ttl_1" style="width:110px;" >処理結果</div>
						<div class="tableCell cell_ttl_1" style="width:120px;" >処理スタッフ数</div>
					</div>
<?php
				if (!empty($lsts)) {

					$count = 0;

					foreach($lsts as $array) {
						$count++;
?>
					<div class="tableRow row_dat" style="width:750px;background:<?php 
					
						if ($array["RESULT"] == "02") {
						
							echo "#b0c4de";
						}  else if ($array["RESULT"] == "03") {
						
							echo "pink";
						} else {
						
							echo "none";
						}
						
					 ?>;">
						<div class="tableCell cell_dat bango" style="width:60px;" ><?php echo $count; ?></div>
						<div class="tableCell cell_dat date" style="width:180px;" ><?php echo str_replace("-","/",$array["DT_EXEC"]) ?></div>
						<div class="tableCell cell_dat nm" style="width:110px;" ><?php echo $array["STAFF_NM"] ?></div>
						<div class="tableCell cell_dat date" style="width:110px;" ><?php echo str_replace("-","/",$array["YMD_SIME"]) ?></div>
						<div class="tableCell cell_dat kbn" style="width:110px;" ><?php echo $array["RESULT_NM"] ?></div>
						<div class="tableCell cell_dat cnt" style="width:120px;" ><?php echo $array["STAFF_CNT"] ?></div>
					</div>
<?php
						}
					}
?>
				</div>

            </div>