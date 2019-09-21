<?php
			echo $this->Html->script(array('effects','window','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSWEX050_RELOAD','DCMSWEX050_POPUP',
					'DCMSWEX050_ADDROW','DCMSWEX050_BUNRUI'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','DCMSWEX'), false, array('inline'=>false));
?>
<!-- WRAPPER -->
<div id="wrapper_s">

<!-- 検索 -->
<div class="box" style="width:1100px;">
	<?php echo $this->Form->create('WEX050Model', array('url' => '/WEX050','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?> 
		<table class="cndt" style="width:1100px">
			<tbody>
			<colgroup>
				<col width="170px">
				<col width="180px">
				<col width="170px">
				<col width="180px">
				<col width="170px">
				<col width="180px">
				<col width="150px">
			</colgroup>
			<tbody>
			<!-- １行目 -->
			<tr>
				<td>大中分類</td>
				<td colspan="6">

					<!-- 工程コンボ -->
			    	<div id="kotei_dropdown_menu_div" style="display:block;position:relative;width:25px;float:left;margin-top:10px;text-align:left;">
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
			    	<div id="processSelect" style="display:block;position:relative;width:250px;float:left;margin-top:10px;text-align:left;">
						<div style="position:relative;width:180px;float:left;margin-top:0px;text-align:left;">
							<input id="koteiText" class="calendar han" type="text" value=""  
								 style="width:220px;height:20px" readonly>
						</div>
        			</div>
        			<!-- /工程コンボ -->
				</td>
			</tr>
			<!-- ２行目 -->
			<tr>
				<td>細分類コード</td>
				<td class="colBnriCd" style="text-align:left;">
					<?php echo $this->Form->input('bnri_sai_cd', 
									array('maxlength' => '3', 
										'label' => false, 
										'error' => false, 
										'style' => 'width:120px', 
										'class' => 'han')) ?>
				</td>
				<td>細分類名称</td>
				<td colspan="4" class="colBnriNm" style="text-align:left;">
						<?php echo $this->Form->input('bnri_sai_nm', 
												array('maxlength' => '20', 
													'label' => false, 
													'error' => false, 
													'style' => 'width:300px', 
													'class' => 'zen')) ?>
				</td>
			</tr>
			<!-- ３行目 -->
			<tr>
				<td>単位</td>
				<td class="colBnriCd" style="text-align:left;">
					<select name="kbn_tani"  id="kbnTaniCombo" style="width:120px">
						<option value=""></option>
						<?php
						foreach($kbnTanis as $key => $value) :
						?>
						<option value="<?php echo $key ?>"><?php echo $value?></option>
						<?php
						endforeach;
						?>
					</select>
				</td>
				<td>活動目的</td>
				<td class="colBnriCd" style="text-align:left;">
					<select name="kbn_ktd_mktk"  id="kbnKtdMktkCombo" style="width:120px">
						<option value=""></option>
						<?php
						foreach($kbnKtdMktks as $key => $value) :
						?>
						<option value="<?php echo $key ?>"><?php echo $value?></option>
						<?php
						endforeach;
						?>
					</select>
				</td>
				<td>必須作業</td>
				<td class="colBnriCd" style="text-align:left;" colspan="2">
					<select name="kbn_hissu_wk"  id="kbnHissuWkCombo" style="width:120px">
						<option value=""></option>
						<?php
						foreach($kbnHissuWks as $key => $value) :
						?>
						<option value="<?php echo $key ?>"><?php echo $value?></option>
						<?php
						endforeach;
						?>
					</select>
				</td>
			</tr>
			<!-- ４行目 -->
			<tr>
				<td>業務区分</td>
				<td class="colBnriCd" style="text-align:left;">
					<select name="kbn_gyomu"  id="kbnGyomuCombo" style="width:120px">
						<option value=""></option>
						<?php
						foreach($kbnGyomus as $key => $value) :
						?>
						<option value="<?php echo $key ?>"><?php echo $value?></option>
						<?php
						endforeach;
						?>
					</select>
				</td>
				<td>データ取得区分</td>
				<td class="colBnriCd" style="text-align:left;">
					<select name="kbn_get_data"  id="kbnGetDataCombo" style="width:120px">
						<option value=""></option>
						<?php
						foreach($kbnGetDatas as $key => $value) :
						?>
						<option value="<?php echo $key ?>"><?php echo $value?></option>
						<?php
						endforeach;
						?>
					</select>
				</td>
				<td>付加価値性</td>
				<td class="colBnriCd" style="text-align:left;" colspan="2">
					<select name="kbn_fukakachi"  id="kbnFukakachiCombo" style="width:120px">
						<option value=""></option>
						<?php
						foreach($kbnFukakachis as $key => $value) :
						?>
						<option value="<?php echo $key ?>"><?php echo $value?></option>
						<?php
						endforeach;
						?>
					</select>
				</td>
			</tr>
			<!-- ５行目 -->
			<tr>
				<td>専門区分</td>
				<td class="colBnriCd" style="text-align:left;">
					<select name="kbn_senmon"  id="kbnSenmonCombo" style="width:120px">
						<option value=""></option>
						<?php
						foreach($kbnSenmons as $key => $value) :
						?>
						<option value="<?php echo $key ?>"><?php echo $value?></option>
						<?php
						endforeach;
						?>
					</select>
				</td>
				<td>活動タイプ</td>
				<td class="colBnriCd" style="text-align:left;">
					<select name="kbn_ktd_type"  id="kbnKtdTypeCombo" style="width:120px">
						<option value=""></option>
						<?php
						foreach($kbnKtdTypes as $key => $value) :
						?>
						<option value="<?php echo $key ?>"><?php echo $value?></option>
						<?php
						endforeach;
						?>
					</select>
				</td>
				<td>契約単位代表物量</td>
				<td class="colBnriCd" style="text-align:left;">
					<select name="keiyaku_buturyo_flg" id="keiyakuButuryoFlgCombo" style="width:120px">
						<option value=""></option>
						<?php
						foreach($keiyakuButuryoFlgs as $key => $value) :
						?>
						<option value="<?php echo $key ?>"><?php echo $value?></option>
						<?php
						endforeach;
						?>
					</select>
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
<div style="height:40px;width:100%;text-align:right;max-width:1195px;" id="buttons" >
	<input type="button"  value="追加" style="width:120px;height:30px;" id="addButton" >
	<input type="button"  value="更新" style="width:120px;height:30px;" id="updateButton" >
</div>
<!--/ 更新ボタン-->

<!-- メインテーブル-->
	<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>
<div class="resizable_s" id="lstTable">
	<div class="tableRowTitle row_ttl_1" style="width:1575px;">
		<div class="tableCellBango cell_ttl_1" style="width:60px;" ><?php echo Configure::read('Bango'); ?></div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >大分類</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >中分類</div>
		<div class="tableCell cell_ttl_1" style="width:50px;" >細分類</div>
		<div class="tableCell cell_ttl_1" style="width:200px;" >細分類名称</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >細分類略称</div>
		<div class="tableCell cell_ttl_1" style="width:205px;" >細分類説明</div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >単位</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >活動目的</div>
		<div class="tableCell cell_ttl_1" style="width:60px;" >必須</div>
		<div class="tableCell cell_ttl_1" style="width:60px;" >定型</div>
		<div class="tableCell cell_ttl_1" style="width:60px;" >データ</div>
		<div class="tableCell cell_ttl_1" style="width:60px;" >ボタン順</div>
		<div class="tableCell cell_ttl_1" style="width:60px;" >リスト順</div>
		<div class="tableCell cell_ttl_1" style="width:60px;" >終了無</div>
		<div class="tableCell cell_ttl_1" style="width:60px;" >距離</div>
	</div>
<?php $count = 0; ?>
<?php foreach ($lsts as $array): ?>
	<div class="tableRow row_dat" style="width:1575px;">
		<div class="tableCellBango cell_dat" style="width:60px;"><?php echo ($count + 1) + $index; ?></div>
		<div class="tableCell cell_dat nm" style="width:100px;"><?php echo $array['BNRI_DAI_RYAKU'] ?></div>
		<div class="tableCell cell_dat nm" style="width:100px;"><?php echo $array['BNRI_CYU_RYAKU'] ?></div>
		<div class="tableCell cell_dat cd" style="width:50px;"><?php echo $array['BNRI_SAI_CD'] ?></div>
		<div class="tableCell cell_dat nm" style="width:200px;"><?php echo $array['BNRI_SAI_NM'] ?></div>
		<div class="tableCell cell_dat nm" style="width:100px;"><?php echo $array['BNRI_SAI_RYAKU'] ?></div>
		<div class="tableCell cell_dat nm" style="width:205px;"><?php echo $array['BNRI_SAI_EXP'] ?></div>
		<div class="tableCell cell_dat kbn" style="width:80px;"><?php echo empty($array['KBN_TANI']) ? "" : $kbnTanis[$array['KBN_TANI']]; ?></div>
		<div class="tableCell cell_dat kbn" style="width:100px;"><?php echo empty($array['KBN_KTD_MKTK']) ? "" : $kbnKtdMktks[$array['KBN_KTD_MKTK']]; ?></div>
		<div class="tableCell cell_dat kbn" style="width:60px;"><?php echo empty($array['KBN_HISSU_WK']) ? "" : $kbnHissuWks[$array['KBN_HISSU_WK']]; ?></div>
		<div class="tableCell cell_dat kbn" style="width:60px;"><?php echo empty($array['KBN_GYOMU']) ? "" : $kbnGyomus[$array['KBN_GYOMU']]; ?></div>
		<div class="tableCell cell_dat kbn" style="width:60px;"><?php echo empty($array['KBN_GET_DATA']) ? "" : $kbnGetDatas[$array['KBN_GET_DATA']]; ?></div>
		<div class="tableCell cell_dat kbn" style="width:60px;"><?php echo !isset($array['BUTTON_SEQ']) ? "" : $array['BUTTON_SEQ']; ?></div>
		<div class="tableCell cell_dat kbn" style="width:60px;"><?php echo !isset($array['LIST_SEQ']) ? "" : $array['LIST_SEQ']; ?></div>
		<div class="tableCell cell_dat kbn" style="width:60px;"><?php

			if(isset($array['SYURYO_NASI_FLG'])) {

				if($array['SYURYO_NASI_FLG'] == 0) {
					echo "";
				}else if($array['SYURYO_NASI_FLG'] == 1){
					echo "有";
				}
			}

		?></div>
		<div class="tableCell cell_dat kbn" style="width:60px;"><?php

			if(isset($array['KYORI_FLG'])) {

				if($array['KYORI_FLG'] == 0) {
					echo "";
				}else if($array['KYORI_FLG'] == 1){
					echo "有";
				}
			}

		?></div>

		<div class="hiddenData LINE_COUNT"><?php echo $count ?></div>
		<div class="hiddenData CHANGED">0</div>
		<div class="hiddenData BNRI_DAI_CD"><?php echo $array['BNRI_DAI_CD'] ?></div>
		<div class="hiddenData BNRI_CYU_CD"><?php echo $array['BNRI_CYU_CD'] ?></div>
		<div class="hiddenData KBN_TANI"><?php echo $array['KBN_TANI'] ?></div>
		<div class="hiddenData KBN_KTD_MKTK"><?php echo $array['KBN_KTD_MKTK'] ?></div>
		<div class="hiddenData KBN_HISSU_WK"><?php echo $array['KBN_HISSU_WK'] ?></div>
		<div class="hiddenData KBN_GYOMU"><?php echo $array['KBN_GYOMU'] ?></div>
		<div class="hiddenData KBN_GET_DATA"><?php echo $array['KBN_GET_DATA'] ?></div>
		<div class="hiddenData KBN_FUKAKACHI"><?php echo $array['KBN_FUKAKACHI'] ?></div>
		<div class="hiddenData KBN_SENMON"><?php echo $array['KBN_SENMON'] ?></div>
		<div class="hiddenData KBN_KTD_TYPE"><?php echo $array['KBN_KTD_TYPE'] ?></div>
		<div class="hiddenData KEIYAKU_BUTURYO_FLG"><?php echo $array['KEIYAKU_BUTURYO_FLG'] ?></div>
		<div class="hiddenData BUTTON_SEQ"><?php echo $array['BUTTON_SEQ'] ?></div>
		<div class="hiddenData LIST_SEQ"><?php echo $array['LIST_SEQ'] ?></div>
		<div class="hiddenData SYURYO_NASI_FLG"><?php echo $array['SYURYO_NASI_FLG'] ?></div>
		<div class="hiddenData KYORI_FLG"><?php echo $array['KYORI_FLG'] ?></div>

	</div>
<?php $count++; ?>
<?php endforeach; ?>	
</div>
<div id="timestamp" style="display:none;" ><?php  echo $timestamp; ?></div>
<!--/ メインテーブル-->

</div>
<!--/ #wrapper-->
            