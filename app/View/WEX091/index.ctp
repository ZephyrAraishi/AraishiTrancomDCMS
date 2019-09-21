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
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSWEX091_RELOAD','DCMSKOTEI_DROPDOWN_MENU','DCMSWEX091_CALENDAR'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSWEX'), false, array('inline'=>false));

?>
            <div id="wrapper_s">
            	<!-- 検索条件 start -->
				<div class="box" style="width:800px;">
<?php 				echo $this->Form->create('WEX091Model', array('url' => '/WEX091/',
															      'type' => 'get',
				     											  'inputDefaults' => array('label' => false,
				     											  							'div' => false,
				     											  							'hidden' => false)))
?>
				    	<div class="conditionRow">
							<span style="margin-right:10px;">対象期間</span>
					    	<input name="period_from" id="period_from" maxlength="6" class="calendar han" type="text" style="width:70px;">&nbsp;～
					    	<input name="period_to" id="period_to" maxlength="6" class="calendar han" type="text" style="width:70px;">
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
							<select name="kbn_ktd_mktk" id="kbn_ktd_mktk">
								<option value=""></option>
								<?php foreach($kbnKtdMktkList as $key => $value) { ?>
								<option value="<?php echo $key ?>"><?php echo $value?></option>
								<?php }; ?>
							</select>

							<span style="margin:0 10px 0 38px;">必須</span>
							<select name="kbn_hissu_wk" id="kbn_hissu_wk">
								<option value=""></option>
								<?php foreach($kbnHissuWkList as $key => $value) { ?>
								<option value="<?php echo $key ?>"><?php echo $value?></option>
								<?php }; ?>
							</select>

							<span style="margin:0 17px 0 38px;">業務</span>
							<select name="kbn_gyomu" id="kbn_gyomu">
								<option value=""></option>
								<?php foreach($kbnGyomuList as $key => $value) { ?>
								<option value="<?php echo $key ?>"><?php echo $value?></option>
								<?php }; ?>
							</select>
				    	</div>

				    	<div class="conditionRow">
							<span style="margin-right:38px;">価値</span>
							<select name="kbn_fukakachi" id="kbn_fukakachi">
								<option value=""></option>
								<?php foreach($kbnFukaKachiList as $key => $value) { ?>
								<option value="<?php echo $key ?>"><?php echo $value?></option>
								<?php }; ?>
							</select>

							<span style="margin:0 10px 0 38px;">専門</span>
							<select name="kbn_senmon" id="kbn_senmon">
								<option value=""></option>
								<?php foreach($kbnSenmonList as $key => $value) { ?>
								<option value="<?php echo $key ?>"><?php echo $value?></option>
								<?php }; ?>
							</select>

							<span style="margin:0 10px 0 38px;">タイプ</span>
							<select name="kbn_ktd_type" id="kbn_ktd_type">
								<option value=""></option>
								<?php foreach($kbnKtdTypeList as $key => $value) { ?>
								<option value="<?php echo $key ?>"><?php echo $value?></option>
								<?php }; ?>
							</select>
							<input type="submit" value="検索" id="search" style="position:relative;left:80px;">
				    	</div>
					<input type="hidden" name="action" id="actionName" />
					<input type="hidden" name="pageID" id="pageID" <?php if(isset($pageID)) echo "value=$pageID" ?> />
					<?php echo $this->Form->end() ?>
				</div>
				<!-- 検索条件 end -->

				<!--/ 区切り線 -->
				<div class="line"></div>
				<!--/ 区切り線 -->

				<!-- メインテーブル-->
				<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>
				<div class="resizable_s" id="lstTable" >
					<div class="tableRowTitle row_ttl_1" style="width:3470px;">
						<div class="tableCell cell_ttl_1" style="width:50px;" ><?php echo Configure::read('Bango'); ?></div>
						<div class="tableCell cell_ttl_1" style="" >年月</div>
						<div class="tableCell cell_ttl_1" style="" >大分類</div>
						<div class="tableCell cell_ttl_1" style="" >中分類</div>
						<div class="tableCell cell_ttl_1" style="" >細分類</div>
						<div class="tableCell cell_ttl_1" style="" >単位</div>
						<div class="tableCell cell_ttl_1" style="" >目的</div>
						<div class="tableCell cell_ttl_1" style="" >累計単価</div>
						<div class="tableCell cell_ttl_1" style="" >累計時間</div>
						<div class="tableCell cell_ttl_1" style="" >累計人数</div>
						<div class="tableCell cell_ttl_1" style="" >累計物量</div>
						<div class="tableCell cell_ttl_1" style="" >累計ライン数量</div>
						<div class="tableCell cell_ttl_1" style="" >累計コスト</div>
						<div class="tableCell cell_ttl_1" style="" >累計売上</div>
						<div class="tableCell cell_ttl_1" style="" >累計粗利</div>
						<div class="tableCell cell_ttl_1" style="" >累計品質件数</div>
						<div class="tableCell cell_ttl_1" style="" >物量生産性</div>
						<div class="tableCell cell_ttl_1" style="" >ライン生産性</div>
						<div class="tableCell cell_ttl_1" style="" >平均単価</div>
						<div class="tableCell cell_ttl_1" style="" >平均時間</div>
						<div class="tableCell cell_ttl_1" style="" >平均人数</div>
						<div class="tableCell cell_ttl_1" style="" >平均物量</div>
						<div class="tableCell cell_ttl_1" style="" >平均ライン数量</div>
						<div class="tableCell cell_ttl_1" style="" >平均コスト</div>
						<div class="tableCell cell_ttl_1" style="" >平均売上</div>
						<div class="tableCell cell_ttl_1" style="" >平均粗利</div>
						<div class="tableCell cell_ttl_1" style="" >平均品質件数</div>
						<div class="tableCell cell_ttl_1" style="" >必須</div>
						<div class="tableCell cell_ttl_1" style="" >業務</div>
						<div class="tableCell cell_ttl_1" style="" >価値</div>
						<div class="tableCell cell_ttl_1" style="" >専門</div>
						<div class="tableCell cell_ttl_1" style="" >タイプ</div>
					</div>
<?php
				if (!empty($resultList)) {
					foreach($resultList as $entity) {
?>
					<div class="tableRow row_dat" style="width:3470px;">
						<div class="tableCell cell_dat" style="width:50px;" ><?php echo ++$index ?></div>
						<div class="tableCell cell_dat date" style="" ><?php echo date_format(date_create($entity["YM"] . '01'),"Y/m"); ?></div>
						<div class="tableCell cell_dat nm" style="" ><?php echo $entity["BNRI_DAI_RYAKU"] ?></div>
						<div class="tableCell cell_dat nm" style="" ><?php echo $entity["BNRI_CYU_RYAKU"] ?></div>
						<div class="tableCell cell_dat nm" style="" ><?php echo $entity["BNRI_SAI_RYAKU"] ?></div>
						<div class="tableCell cell_dat nm" style="" ><?php echo $entity["KBN_TANI_MEI"] ?></div>
						<div class="tableCell cell_dat kbn" style="" ><?php echo $entity["KBN_KTD_MKTK_MEI"] ?></div>
						<div class="tableCell cell_dat kin" style="" ><?php echo $this->Number->format($entity["SUM_TANKA"]) ?></div>
						<div class="tableCell cell_dat cnt" style="" ><?php echo $entity["SUM_JIKAN"] ?></div>
						<div class="tableCell cell_dat cnt" style="" ><?php echo $this->Number->format($entity["SUM_NINZU"]) ?></div>
						<div class="tableCell cell_dat cnt" style="" ><?php echo is_numeric($entity["SUM_BUTURYO"]) ? $this->Number->format($entity["SUM_BUTURYO"]) : $entity["SUM_BUTURYO"] ?></div>
						<div class="tableCell cell_dat cnt" style="" ><?php echo is_numeric($entity["SUM_SURYO_LINE"]) ? $this->Number->format($entity["SUM_SURYO_LINE"]) : $entity["SUM_SURYO_LINE"] ?></div>
						<div class="tableCell cell_dat kin" style="" ><?php echo $this->Number->format($entity["SUM_ABC_COST"]) ?></div>
						<div class="tableCell cell_dat kin" style="" ><?php echo $this->Number->format($entity["SUM_URIAGE"]) ?></div>
						<div class="tableCell cell_dat kin" style="" ><?php echo $this->Number->format($entity["SUM_RIEKI"]) ?></div>
						<div class="tableCell cell_dat cnt" style="" ><?php echo $this->Number->format($entity["SUM_HINSITU_CNT"]) ?></div>
						<div class="tableCell cell_dat cnt" style="" ><?php echo is_numeric($entity["SEISANSEI_BUTURYO"]) ? $this->Number->format($entity["SEISANSEI_BUTURYO"], 1) : $entity["SEISANSEI_BUTURYO"] ?></div>
						<div class="tableCell cell_dat cnt" style="" ><?php echo is_numeric($entity["SEISANSEI_LINE"]) ? $this->Number->format($entity["SEISANSEI_LINE"], 1) : $entity["SEISANSEI_LINE"] ?></div>
						<div class="tableCell cell_dat kin" style="" ><?php echo $this->Number->format($entity["AVG_TANKA"]) ?></div>
						<div class="tableCell cell_dat cnt" style="" ><?php echo $entity["AVG_JIKAN"] ?></div>
						<div class="tableCell cell_dat cnt" style="" ><?php echo $this->Number->format($entity["AVG_NINZU"]) ?></div>
						<div class="tableCell cell_dat cnt" style="" ><?php echo is_numeric($entity["AVG_BUTURYO"]) ? $this->Number->format($entity["AVG_BUTURYO"]) : $entity["AVG_BUTURYO"] ?></div>
						<div class="tableCell cell_dat cnt" style="" ><?php echo is_numeric($entity["AVG_SURYO_LINE"]) ? $this->Number->format($entity["AVG_SURYO_LINE"]) : $entity["AVG_SURYO_LINE"] ?></div>
						<div class="tableCell cell_dat kin" style="" ><?php echo $this->Number->format($entity["AVG_ABC_COST"]) ?></div>
						<div class="tableCell cell_dat kin" style="" ><?php echo $this->Number->format($entity["AVG_URIAGE"]) ?></div>
						<div class="tableCell cell_dat kin" style="" ><?php echo $this->Number->format($entity["AVG_RIEKI"]) ?></div>
						<div class="tableCell cell_dat cnt" style="" ><?php echo $this->Number->format($entity["AVG_HINSITU_CNT"]) ?></div>
						<div class="tableCell cell_dat kbn" style="" ><?php echo $entity["KBN_HISSU_WK_MEI"] ?></div>
						<div class="tableCell cell_dat kbn" style="" ><?php echo $entity["KBN_GYOMU_MEI"] ?></div>
						<div class="tableCell cell_dat kbn" style="" ><?php echo $entity["KBN_FUKAKACHI_MEI"] ?></div>
						<div class="tableCell cell_dat kbn" style="" ><?php echo $entity["KBN_SENMON_MEI"] ?></div>
						<div class="tableCell cell_dat kbn" style="" ><?php echo $entity["KBN_KTD_TYPE_MEI"] ?></div>
					</div>
<?php
					}
				}
?>
				</div>
            </div>
