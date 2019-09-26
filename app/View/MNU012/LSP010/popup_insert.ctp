<div style= "position: relative; height: 350px; width: 100%; margin-top: 20px;">
  <table width="100%" class="field">
	<colgroup width="50px">
	<colgroup width="80px">
	<colgroup width="80px">
	<colgroup width="80px">
	<colgroup width="300px">
	<tbody>
	<!-- １行目 -->
	<tr style="height:30px;">
		<td ></td>
		<td >スタッフ</td>

		<td>
			<div id="staff_btn"><input type="button" value="検索" onClick="clickStaffSearchPopUp()"  style="width: 80px;height:24px;"></div>
		</td>

		<td>
			<div  id="staff_cd" ></div>

			<div id="staffDsp1" align="left">
				<?php echo $this->Form->input('staffCdDsp', 
								array('style' => 'width:80px;', 
									'error' => false, 
									'label' => false, 
									'div' => false, 
									'hidden' => false, 
									'class' => 'han', 
									'disabled' => 'disabled')) ?>
			</div>
		</td>

		<td>
			<div  id="staff_nm"></div>

			<div id="staffDsp2" align="left">
				<?php echo $this->Form->input('staffNmDsp', 
								array('style' => 'width:180px;', 
									'error' => false, 
									'label' => false, 
									'div' => false, 
									'hidden' => false, 
									'class' => 'han', 
									'disabled' => 'disabled')) ?>
			</div>

			<div >
				<?php echo $this->Form->input('staffFlag', 
								array('style' => 'width:10px;', 
									'error' => false, 
									'label' => false, 
									'div' => false, 
									'hidden' => true, 
									'class' => 'han', 
									'disabled' => 'disabled')) ?>
			</div>
		</td>
	</tr>


	</tbody>

  </table>


  <table width="100%" class="field">
	<colgroup width="50px">
	<colgroup width="80px">
	<colgroup width="450px">
	<tbody>



	<!-- ２行目 -->
	<tr style="height:30px;">
				<td></td>
				<td>工程:</td>
				<td colspan="3">

				    	<!-- 工程コンボ -->
	<div id="popupKoteiArea">

<?php
	for ($i =0; $i < 1 ;$i++) {
?>

		<div >
			<?php echo $this->Form->input('kouteiFlag', 
							array('style' => 'width:10px;', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => true, 
								'class' => 'han', 
								'disabled' => 'disabled')) ?>
		</div>

		<div id="koteiDsp" align="left">
			<?php echo $this->Form->input('kouteiCdDsp', 
							array('style' => 'width:50px;', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han', 
								'disabled' => 'disabled')) ?>

			<?php echo $this->Form->input('kouteiNmDsp', 
							array('style' => 'width:150px;', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han', 
								'disabled' => 'disabled')) ?>
		</div>


		<div class="koteiPopup" id="koteiIn" >

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

	<!-- 改行 -->
	<tr style="height:30px;">
	</tr>

	</tbody>

  </table>


  <table width="100%" class="field">
	<colgroup width="100px">
	<colgroup width="80px">
	<colgroup width="40px">
	<colgroup width="10px">
	<colgroup width="40px">
	<colgroup width="10px">
	<colgroup width="80px">
	<colgroup width="200px">
	<tbody>

	<!-- ３行目 -->
	<tr style="height:30px;">
		<td ></td>
		<td id="Youbi1"></td>
		<td class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('stTime1', 
							array('style' => 'width:40px;', 
								'maxlength' => '4', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han')) ?>
		</td>
		<td class="colBnriCd" style="text-align:left;">～</td>
		<td class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('edTime1', 
							array('style' => 'width:40px;', 
								'maxlength' => '4', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han')) ?>
		</td>
		<td ></td>
		<td class="colBnriCd" style="text-align:left;">
			<input id="syukin1" name="syukin1n" type="checkbox" value="checked" >　休
		</td>
		<td class="colBnriCd" style="text-align:left;">
			<input id="auto1" name="auto1" type="checkbox" value="checked" >自動設定
		</td>
	</tr>
	<!-- ４行目 -->
	<tr style="height:30px;">
		<td ></td>
		<td id="Youbi2"></td>
		<td class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('stTime2', 
							array('style' => 'width:40px;', 
								'maxlength' => '4', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han')) ?>
		</td>
		<td class="colBnriCd" style="text-align:left;">～</td>
		<td class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('edTime2', 
							array('style' => 'width:40px;', 
								'maxlength' => '4', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han')) ?>
		</td>
		<td ></td>
		<td class="colBnriCd" style="text-align:left;">
			<input id="syukin2" name="syukin2n" type="checkbox" value="checked" >　休
		</td>
		<td class="colBnriCd" style="text-align:left;">
			<input id="auto2" name="auto2" type="checkbox" value="checked" >自動設定
		</td>
	</tr>
	<!-- ５行目 -->
	<tr style="height:30px;">
		<td ></td>
		<td id="Youbi3"></td>
		<td class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('stTime3', 
							array('style' => 'width:40px;', 
								'maxlength' => '4', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han')) ?>
		</td>
		<td class="colBnriCd" style="text-align:left;">～</td>
		<td class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('edTime3', 
							array('style' => 'width:40px;', 
								'maxlength' => '4', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han')) ?>
		</td>
		<td ></td>
		<td class="colBnriCd" style="text-align:left;">
			<input id="syukin3" name="syukin3n" type="checkbox" value="checked" >　休
		</td>
		<td class="colBnriCd" style="text-align:left;">
			<input id="auto3" name="auto3" type="checkbox" value="checked" >自動設定
		</td>
	</tr>
	<!-- ６行目 -->
	<tr style="height:30px;">
		<td ></td>
		<td id="Youbi4"></td>
		<td class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('stTime4', 
							array('style' => 'width:40px;', 
								'maxlength' => '4', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han')) ?>
		</td>
		<td class="colBnriCd" style="text-align:left;">～</td>
		<td class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('edTime4', 
							array('style' => 'width:40px;', 
								'maxlength' => '4', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han')) ?>
		</td>
		<td ></td>
		<td class="colBnriCd" style="text-align:left;">
			<input id="syukin4" name="syukin4n" type="checkbox" value="checked" >　休
		</td>
		<td class="colBnriCd" style="text-align:left;">
			<input id="auto4" name="auto4" type="checkbox" value="checked" >自動設定
		</td>
	</tr>
	<!-- ７行目 -->
	<tr style="height:30px;">
		<td ></td>
		<td id="Youbi5"></td>
		<td class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('stTime5', 
							array('style' => 'width:40px;', 
								'maxlength' => '4', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han')) ?>
		</td>
		<td class="colBnriCd" style="text-align:left;">～</td>
		<td class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('edTime5', 
							array('style' => 'width:40px;', 
								'maxlength' => '4', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han')) ?>
		</td>
		<td ></td>
		<td class="colBnriCd" style="text-align:left;">
			<input id="syukin5" name="syukin5n" type="checkbox" value="checked" >　休
		</td>
		<td class="colBnriCd" style="text-align:left;">
			<input id="auto5" name="auto5" type="checkbox" value="checked" >自動設定
		</td>
	</tr>
	<!-- ８行目 -->
	<tr style="height:30px;">
		<td ></td>
		<td id="Youbi6"></td>
		<td class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('stTime6', 
							array('style' => 'width:40px;', 
								'maxlength' => '4', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han')) ?>
		</td>
		<td class="colBnriCd" style="text-align:left;">～</td>
		<td class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('edTime6', 
							array('style' => 'width:40px;', 
								'maxlength' => '4', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han')) ?>
		</td>
		<td ></td>
		<td class="colBnriCd" style="text-align:left;">
			<input id="syukin6" name="syukin6n" type="checkbox" value="checked" >　休
		</td>
		<td class="colBnriCd" style="text-align:left;">
			<input id="auto6" name="auto6" type="checkbox" value="checked" >自動設定
		</td>
	</tr>
	<!-- ９行目 -->
	<tr style="height:30px;">
		<td ></td>
		<td id="Youbi7"></td>
		<td class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('stTime7', 
							array('style' => 'width:40px;', 
								'maxlength' => '4', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han')) ?>
		</td>
		<td class="colBnriCd" style="text-align:left;">～</td>
		<td class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('edTime7', 
							array('style' => 'width:40px;', 
								'maxlength' => '4', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han')) ?>
		</td>
		<td ></td>
		<td class="colBnriCd" style="text-align:left;">
			<input id="syukin7" name="syukin7n" type="checkbox" value="checked" >　休
		</td>
		<td class="colBnriCd" style="text-align:left;">
			<input id="auto7" name="auto7" type="checkbox" value="checked" >自動設定
		</td>
	</tr>

	<tr style="height:40px;">
		<td >
		</td>
		<td colspan="6" class="colBnriCd" style="text-align:left;">
			※　未入力の場合は登録時にスタッフ基本情報の就業条件が適応されます。
		</td>
	</tr>

	</tbody>

  </table>
</div>
