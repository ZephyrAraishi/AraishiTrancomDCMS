<style type="text/css">
div.box input[type="text"] {
	height : 20px;
}

div.box select {
	height : 22px;
}

div.box select {
	width : 110px;
}

div.box div.conditionRow {
	height : 35px;
	font-size: 10pt;
}

div#lstTable div.tableCell {
	width: 100px;
}

</style>

<?php
			echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSWEX093_RELOAD','DCMSWEX093_POPUP',
					'DCMSWEX093_CALENDAR','DCMSWEX093_BUNRUI'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSWEX'), false, array('inline'=>false));
?>

			<!-- WRAPPER -->
			<div id="wrapper_s">
			
			
            	<!-- 検索条件 start -->
				<div class="box" style="width:800px;">
			
					<?php echo $this->Form->create('WEX093Model', array('url' => '/WEX093','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?> 

                        <div class="conditionRow">
                            <span style="margin-right:10px;">対象期間</span>
                            <input name="date_from" id="date_from" maxlength="8" class="calendar han" type="text" style="width:70px;">&nbsp;～
                            <input name="date_to" id="date_to" maxlength="8" class="calendar han" type="text" style="width:70px;">
                        </div>

                        <div class="conditionRow">
                            <div style="width:70px;float:left;margin-top:3px;">工程</div>
                            <!-- 工程コンボ start -->
                            <div id="kotei_dropdown_menu_div" style="display:block;position:relative;width:25px;float:left;">
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
							<div style="position:absolute;width:20px;height:1px;right:0px;z-index:0;" >&raquo;</div>
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
                            <div id="processSelect" style="display:block;position:relative;width:250px;float:left;">
                                <div style="position:relative;width:180px;margin-top:0px;text-align:left;">
                                    <input id="koteiText" class="calendar han" type="text" value="" style="width:220px;" readonly>
                                </div>
                            </div>
                        </div>
                        <!-- 工程コンボ end -->

                        <div  class="conditionRow">
                            <span style="margin-right:38px;">目的</span>
        					<select name="kbn_ktd_mktk" id="kbnKtdMktkCombo">
        						<option value=""></option>
        						<?php
        						foreach($kbnKtdMktks as $key => $value) :
        						?>
        						<option value="<?php echo $key ?>"><?php echo $value?></option>
        						<?php
        						endforeach;
        						?>
        					</select>

                            <span style="margin:0 10px 0 38px;">必須</span>
        					<select name="kbn_hissu_wk" id="kbnHissuWkCombo">
        						<option value=""></option>
        						<?php
        						foreach($kbnHissuWks as $key => $value) :
        						?>
        						<option value="<?php echo $key ?>"><?php echo $value?></option>
        						<?php
        						endforeach;
        						?>
        					</select>

                            <span style="margin:0 17px 0 38px;">業務</span>
        					<select name="kbn_gyomu" id="kbnGyomuCombo">
        						<option value=""></option>
        						<?php
        						foreach($kbnGyomus as $key => $value) :
        						?>
        						<option value="<?php echo $key ?>"><?php echo $value?></option>
        						<?php
        						endforeach;
        						?>
        					</select>
                        </div>

                        <div class="conditionRow">
                            <span style="margin-right:38px;">価値</span>
                            <select name="kbn_fukakachi" id="kbnFukakachiCombo">
                                <option value=""></option>
                                <?php
                                foreach($kbnFukakachis as $key => $value) :
                                ?>
                                <option value="<?php echo $key ?>"><?php echo $value?></option>
                                <?php
                                endforeach;
                                ?>
                            </select>

                            <span style="margin:0 10px 0 38px;">専門</span>
                            <select name="kbn_senmon" id="kbnSenmonCombo">
                                <option value=""></option>
                                <?php
                                foreach($kbnSenmons as $key => $value) :
                                ?>
                                <option value="<?php echo $key ?>"><?php echo $value?></option>
                                <?php
                                endforeach;
                                ?>
                            </select>

                            <span style="margin:0 10px 0 38px;">タイプ</span>
                            <select name="kbn_ktd_type" id="kbnKtdTypeCombo">
                                <option value=""></option>
                                <?php
                                foreach($kbnKtdTypes as $key => $value) :
                                ?>
                                <option value="<?php echo $key ?>"><?php echo $value?></option>
                                <?php
                                endforeach;
                                ?>
                            </select>
                            <input type="submit" value="検索" id="search" style="position:relative;left:80px;">
                        </div>
                    <?php echo $this->Form->end() ?>
                </div>
            	<!-- 検索条件 end -->

				<!--/ 区切り線 -->
				<div class="line"></div>
				<!--/ 区切り線 -->

				<!-- メインテーブル-->
				<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>
				<div class="resizable_s" id="lstTable">
					<div class="tableRowTitle row_ttl_1" style="width:2490px;">
						<div class="tableCell cell_ttl_1" style="width:60px;" ><?php echo Configure::read('Bango'); ?></div>
						<div class="tableCell cell_ttl_1" style="" >年月日</div>
						<div class="tableCell cell_ttl_1" style="" >大分類</div>
						<div class="tableCell cell_ttl_1" style="" >中分類</div>
						<div class="tableCell cell_ttl_1" style="" >細分類</div>
						<div class="tableCell cell_ttl_1" style="" >単位</div>
						<div class="tableCell cell_ttl_1" style="" >目的</div>
						<div class="tableCell cell_ttl_1" style="" >単価</div>
						<div class="tableCell cell_ttl_1" style="" >時間</div>
						<div class="tableCell cell_ttl_1" style="" >人数</div>
						<div class="tableCell cell_ttl_1" style="" >物量</div>
						<div class="tableCell cell_ttl_1" style="" >ライン数量</div>
						<div class="tableCell cell_ttl_1" style="" >物量生産性</div>
						<div class="tableCell cell_ttl_1" style="" >ライン生産性</div>
						<div class="tableCell cell_ttl_1" style="" >ABCコスト</div>
						<div class="tableCell cell_ttl_1" style="" >売上</div>
						<div class="tableCell cell_ttl_1" style="" >利益</div>
						<div class="tableCell cell_ttl_1" style="" >品質件数</div>
						<div class="tableCell cell_ttl_1" style="" >必須</div>
						<div class="tableCell cell_ttl_1" style="" >業務</div>
						<div class="tableCell cell_ttl_1" style="" >価値</div>
						<div class="tableCell cell_ttl_1" style="" >専門</div>
						<div class="tableCell cell_ttl_1" style="" >タイプ</div>
					</div>
				
				
				<?php
					if (!empty($lsts)) {
						$count = 0;
						foreach($lsts as $array) {
				?>
				
					<div class="tableRow row_dat" style="width:2490px;">
						<div class="tableCell cell_dat" style="width:60px;"><?php echo $index + $count + 1 ?></div>
						<div class="tableCell cell_dat date" style=""><?php echo str_replace("-","/",$array['YMD']) ?></div>
						<div class="tableCell cell_dat nm" style=""><?php echo $array['BNRI_DAI_RYAKU'] ?></div>
						<div class="tableCell cell_dat nm" style=""><?php echo $array['BNRI_CYU_RYAKU'] ?></div>
						<div class="tableCell cell_dat nm" style=""><?php echo $array['BNRI_SAI_RYAKU'] ?></div>
						<div class="tableCell cell_dat kbn" style=""><?php echo $array['MEI_1_TANI'] ?></div>
						<div class="tableCell cell_dat nm" style=""><?php echo $array['MEI_1_MOKUTEKI'] ?></div>
						<div class="tableCell cell_dat kin" style="" ><?php echo $this->Number->format($array["TANKA"]) ?></div>
						<div class="tableCell cell_dat cnt" style=""><?php echo number_format($array['JIKAN_H'],2) ?></div>
						<div class="tableCell cell_dat cnt" style=""><?php echo number_format($array['NINZU']) ?></div>
						<div class="tableCell cell_dat cnt" style=""><?php if (is_numeric($array['BUTURYO'])) { echo number_format($array['BUTURYO']); } else { echo $array['BUTURYO']; } ?></div>
						<div class="tableCell cell_dat cnt" style=""><?php if (is_numeric($array['BUTURYO'])) { echo number_format($array['SURYO_LINE']); } else { echo $array['SURYO_LINE']; } ?></div>
						<div class="tableCell cell_dat cnt" style=""><?php if (is_numeric($array['BUTURYO'])) { echo number_format($array['BUTURYO_SEISAN'],1); } else { echo $array['BUTURYO_SEISAN']; } ?></div>
						<div class="tableCell cell_dat cnt" style=""><?php if (is_numeric($array['BUTURYO'])) { echo number_format($array['SURYO_LINE_SEISAN'],1); } else { echo $array['SURYO_LINE_SEISAN']; } ?></div>
						<div class="tableCell cell_dat kin" style=""><?php echo number_format($array['ABC_COST']) ?></div>
						<div class="tableCell cell_dat kin" style=""><?php echo number_format($array['URIAGE']) ?></div>
						<div class="tableCell cell_dat kin" style=""><?php echo number_format($array['RIEKI']) ?></div>
						<div class="tableCell cell_dat cnt" style=""><?php echo number_format($array['HINSITU_CNT']) ?></div>
						<div class="tableCell cell_dat kbn" style=""><?php echo $array['MEI_1_HISSU'] ?></div>
						<div class="tableCell cell_dat kbn" style=""><?php echo $array['MEI_1_GYOUMU'] ?></div>
						<div class="tableCell cell_dat kbn" style=""><?php echo $array['MEI_1_KACHI'] ?></div>
						<div class="tableCell cell_dat kbn" style=""><?php echo $array['MEI_1_SENMON'] ?></div>
						<div class="tableCell cell_dat kbn" style=""><?php echo $array['MEI_1_TYPE'] ?></div>

						<div class="hiddenData LINE_COUNT"><?php echo $count ?></div>
						<div class="hiddenData CHANGED">0</div>
						<div class="hiddenData BNRI_DAI_CD"><?php echo $array['BNRI_DAI_CD'] ?></div>
						<div class="hiddenData BNRI_CYU_CD"><?php echo $array['BNRI_CYU_CD'] ?></div>
						<div class="hiddenData BNRI_SAI_CD"><?php echo $array['BNRI_SAI_CD'] ?></div>
						<div class="hiddenData KBN_URIAGE"><?php echo $array['KBN_URIAGE'] ?></div>
						<div class="hiddenData MEI_1_URIAGE"><?php echo $array['MEI_1_URIAGE'] ?></div>
				
					</div>
				
				<?php
							$count++;
						}
					}
				?>
			
				</div>
				<div id="timestamp" style="display:none;" ><?php  echo $timestamp; ?></div>
				<!--/ メインテーブル-->

				<div style="display:none;" id="ymd_syugyo_storage"><input type="text" style="width:80px;" class="han" maxlength="8" id="ymd_syugyo" ></div>

			</div>
			<!--/ #wrapper-->
