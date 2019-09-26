<div style= "position: relative; height: 350px; width: 100%; margin-top: 20px;">
  <table width="100%" class="field">

	<colgroup width="50px">
	<colgroup width="100px">
	<colgroup width="30px">
	<colgroup width="20px">
	<colgroup width="30px">
	<colgroup width="10px">
	<colgroup width="80px">

	<tbody>

	<!-- １行目 -->
	<tr style="height:30px;">
		<td ></td>
		<td >スタッフ</td>
		<td colspan="3" class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('staffCd', 
							array('style' => 'width:200px;', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han', 
								'disabled' => 'disabled')) ?>
		</td>
	</tr>
	<!-- ２行目 -->
	<tr style="height:30px;">
		<td ></td>
		<td >工程</td>
		<td colspan="3" class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('koutei', 
							array('style' => 'width:200px;', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han', 
								'disabled' => 'disabled')) ?>
		</td>
	</tr>

	<!-- 改行 -->
	<tr style="height:30px;">
	</tr>

	<!-- ３行目 -->
	<tr style="height:30px;">
		<td ></td>
		<td id="Youbi1"></td>
		<td style="text-align:left;">
			<?php echo $this->Form->input('stTime1', 
							array('style' => 'width:40px;', 
								'maxlength' => '10', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han')) ?>
		</td>
		<td style="text-align:left;">～</td>
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
