<div style= "position: relative; height: 200px; width: 100%; margin-top: 20px;">
  <table width="100%" class="field">
		<colgroup width="50px">
		<colgroup width="850px">
		<tbody>

		<tr style="height:30px;">
			<td style="width: 20%;text-align: right;">大分類：</td>
			<td style="width:  5%;"></td>
			<td style="width: 75%;text-align: left;">
			<div id="popupKoteiArea">
				<div class="koteiPopup" >
				    	<!-- 工程コンボ -->
				    	<div id="kotei_dropdown_menu_div" style="display:block;position:relative;width:25px;float:left;margin-top:10px;text-align:left;">
				    		<div id="menu_img_0"><img src="/DCMS/img/search.png" style="width:24px;height:24px" onclick="menuDispPopup('0');"></div>
				    		<div id="ul_class_0" style="display:none;">
				    			<ul style="display:block;" class="ul_first">
				    				<li style="display:block;position:relative;" value= ""
				    							onClick="setMenuProcessPopup('','','','','','','0');">
										<div style="height:30px;padding-left:5px;width:120px;cursor:pointer;">（選択なし）</div>
									</li>
<?php

	foreach ($koteiDaiList as $array1) {
	
		$koteiDaiCd    = (string)$array1['BNRI_DAI_CD'];
		$koteiDaiRyaku = (string)$array1['BNRI_DAI_RYAKU'];

?>

			<li style="display:block;position:relative;" value="<?php echo $koteiDaiCd ?>" >
				<div style="height:30px;padding-left:5px;width:120px;cursor:default;"
					onClick="setMenuProcessPopup('<?php echo $koteiDaiCd ."', '" . 
									    '' ."', '" . 
									    '' ."', '" . 
									    $koteiDaiRyaku ."', '" . 
									    '' ."', '" . 
									    '' ?>', '0');"
					>
					<?php echo $koteiDaiRyaku ?>
				</div>
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
            		</div>
            	</div>
			</td>
		</tr>
		<tr style="height:30px;">
			<td style="width: 20%;text-align: right;">中分類コード：</td>
			<td style="width:  5%;"></td>
			<td style="width: 75%;text-align: left;">
					<?php echo $this->Form->input('bnriCyuCd', 
											array('style' => 'width:100px;', 
												'maxlength' => '3', 
												'error' => false, 
												'label' => false, 
												'div' => false, 
												'hidden' => false, 
												'class' => 'han')) ?>
			</td>
		</tr>
		<tr style="height:30px;">
		   <td style="width: 20%;text-align: right;">中分類名称：</td>
		   <td style="width:  5%;"></td>
		   <td style="width: 75%;text-align: left;">
		   			<?php echo $this->Form->input('bnriCyuNm', 
		   									array('style' => 'width:300px;', 
		   										'maxlength' => '20', 
		   										'error' => false, 
		   										'label' => false, 
		   										'div' => false, 
		   										'hidden' => false, 
		   										'class' => 'zen')) ?>
		   </td>
		</tr>
		<tr style="height:30px;">
		   <td style="width: 20%;text-align: right;">中分類略称：</td>
		   <td style="width:  5%;"></td>
		   <td style="width: 75%;text-align: left;">
		   			<?php echo $this->Form->input('bnriCyuRyaku', 
		   									array('style' => 'width:150px;', 
		   										'maxlength' => '6', 
		   										'error' => false, 
		   										'label' => false, 
		   										'div' => false, 
		   										'hidden' => false, 
		   										'class' => 'zen')) ?>
		   </td>
		</tr>
		<tr style="height:30px;">
		   <td style="width: 20%;text-align: right;">中分類説明：</td>
		   <td style="width:  5%;"></td>
		   <td style="width: 75%;text-align: left;">
		   			<?php echo $this->Form->input('bnriCyuExp', 
		   									array('style' => 'width:450px;', 
		   										'maxlength' => '40', 
		   										'error' => false, 
		   										'label' => false, 
		   										'div' => false, 
		   										'hidden' => false, 
		   										'class' => 'zen')) ?>
		   </td>
		</tr>
		<tr style="height:30px;">
			<td style="width: 20%;text-align: right;">単位区分：</td>
			<td style="width:  5%;"></td>
			<td style="width: 75%;text-align: left;">
				<select id="kbn_tani">
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
		</tr>
		<tr style="height:30px;">
			<td style="width: 20%;text-align: right;">売上区分：</td>
			<td style="width:  5%;"></td>
			<td style="width: 75%;text-align: left;">
				<select id="kbn_uriage">
					<option value=""></option>
					<?php
					foreach($kbnUriages as $key => $value) :
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
