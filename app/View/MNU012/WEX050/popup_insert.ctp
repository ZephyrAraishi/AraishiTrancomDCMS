<div style= "position: relative; height: 250px; width: 100%; margin-top: 20px; margin-left: 20px;">


  <table width="100%" class="field">
	<colgroup width="150px">
	<colgroup width="200px">
	<colgroup width="150px">
	<colgroup width="200px">
	<colgroup width="150px">
	<colgroup width="200px">
	<tbody>

	<!-- １行目 -->
	<tr style="height:30px;">
				<td style="text-align:left;">分類：</td>
				<td colspan="5">

				    	<!-- 工程コンボ -->
	<div id="popupKoteiArea">

<?php
	for ($i =0; $i < 1 ;$i++) {
?>

		<div class="koteiPopup" >

			<!-- 工程コンボ -->
	    	<div class="kotei_dropdown_menu_div" 
	    		style="display:block;position:relative;width:25px;float:left;text-align:left;">
	    		<div id="menu_img_<?php echo $i; ?>" class="img_top">
	    			<img src="/DCMS/img/search.png" style="width:24px;height:24px" onclick="menuDispPopup('<?php echo $i ?>');">
	    		</div>
	    		<div id="ul_class_<?php echo $i; ?>" class="ul_top" style="display:none;">
	    			<ul style="display:block;" class="ul_first">
	    				<li style="display:block;position:relative;" value= ""
	    							onClick="setMenuProcessPopup('','','','','','','<?php echo $i ?>');">
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
				<div style="height:30px;padding-left:5px;width:120px;cursor:default;">
					<?php echo $koteiDaiRyaku ?>
				</div>
<?php
		} else {
?>
			<li value="<?php echo $koteiDaiCd ?>">
				<div style="height:30px;padding-left:5px;width:120px;">
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
?>
				<li value="<?php echo $koteiDaiCd . "_" . $koteiCyuCd ?>" >
					<div style="height:30px;padding-left:5px;width:120px;cursor:default;"
						onClick="setMenuProcessPopup('<?php echo $koteiDaiCd ."', '" . 
											    $koteiCyuCd ."', '" . 
											    '' ."', '" . 
											    $koteiDaiRyaku ."', '" . 
											    $koteiCyuRyaku ."', '" . 
											    '' ."', '" . 
											    $i ?>');"
						>
						 <?php echo $koteiCyuRyaku ?>
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
?>
					</ul>
					<input type="hidden" name="kotei" class="koteiCd" value="">
				</div>
			</div>
	    	<div class="processSelect" 
	    	style="display:block;position:relative;width:300px;float:left;text-align:left;margin-bottom:10px;">
				<div style="position:relative;width:300px;float:left;margin-top:0px;text-align:left;">
					<input class="koteiDaiComboPopup" type="text" value=""  
						 style="width:123px;height:20px" readonly>
					<input class="koteiCyuComboPopup" type="text" value=""  
						 style="width:123px;height:20px" readonly>
				</div>
			</div>
			<!-- /工程コンボ -->

		</div>

<?php
	}
?>

	</div>
            			<!-- /工程コンボ -->
				</td>
	</tr>

	<!-- ２行目 -->
	<tr style="height:30px;">
		<td style="text-align:left;" >細分類コード：</td>
		<td colspan="5"  class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('bnriSaiCd', 
							array('style' => 'width:100px;', 
								'maxlength' => '3', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han')) ?>
		</td>
	</tr>
	<!-- ３行目 -->
	<tr style="height:30px;">
	   <td style="text-align:left;" >細分類名称：</td>
	   <td colspan="5"  class="colBnriCd" style="text-align:left;">
   			<?php echo $this->Form->input('bnriSaiNm', 
							array('style' => 'width:300px;', 
								'maxlength' => '20', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'zen')) ?>
	   </td>
	</tr>
	<!-- ４行目 -->
	<tr style="height:30px;">
	   <td style="text-align:left;" >細分類略称：</td>
	   <td colspan="5"  class="colBnriCd" style="text-align:left;">
   			<?php echo $this->Form->input('bnriSaiRyaku', 
							array('style' => 'width:150px;', 
								'maxlength' => '6', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'zen')) ?>
	   </td>
	</tr>
	<!-- ５行目 -->
	<tr style="height:30px;">
	   <td style="text-align:left;" >細分類説明：</td>
	   <td colspan="5"  class="colBnriCd" style="text-align:left;">
   			<?php echo $this->Form->input('bnriSaiExp', 
							array('style' => 'width:450px;', 
								'maxlength' => '40', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'zen')) ?>
	   </td>
	</tr>
	<!-- ６行目 -->
	<tr style="height:30px;">
		<td style="text-align:left;" >単位：</td>
		<td  class="colBnriCd" style="text-align:left;">
			<select id="kbn_tani" style="width:120px">
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
		<td style="text-align:left;" >活動目的：</td>
		<td  class="colBnriCd" style="text-align:left;">
			<select id="kbn_ktd_mktk" style="width:120px">
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
		<td style="text-align:left;" >必須作業：</td>
		<td  class="colBnriCd" style="text-align:left;">
			<select id="kbn_hissu_wk" style="width:120px">
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
	<!-- ７行目 -->
	<tr style="height:30px;">
		<td style="text-align:left;" >業務区分：</td>
		<td  class="colBnriCd" style="text-align:left;">
			<select id="kbn_gyomu" style="width:120px">
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
		<td style="text-align:left;" >データ取得区分：</td>
		<td  class="colBnriCd" style="text-align:left;">
			<select id="kbn_get_data" style="width:120px">
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
		<td style="text-align:left;" >付加価値性：</td>
		<td  class="colBnriCd" style="text-align:left;">
			<select id="kbn_fukakachi" style="width:120px">
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
	<!-- ８行目 -->
	<tr style="height:30px;">
		<td style="text-align:left;" >専門区分：</td>
		<td  class="colBnriCd" style="text-align:left;">
			<select id="kbn_senmon" style="width:120px">
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
		<td style="text-align:left;" >活動タイプ：</td>
		<td  class="colBnriCd" style="text-align:left;">
			<select id="kbn_ktd_type" style="width:120px">
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
		<td style="text-align:left;" >契約単位代表物量：</td>
		<td  class="colBnriCd" style="text-align:left;">
			<select id="keiyaku_buturyo_flg" style="width:120px">
				<?php
				foreach($keiyakuButuryoFlgs as $key => $value) :
				?>
				<option value="<?php echo $key ?>"><?php echo $value?></option>
				<?php
				endforeach;
				?>
			</select>
		</td>
	</tr>

	</tbody>

  </table>
</div>
