<div style= "position: relative; height: 200px; width: 100%; margin-top: 20px;">
  <table width="100%" class="field">
		<colgroup width="50px">
		<colgroup width="850px">
		<tbody>
		<tr style="height:30px;">
			<td style="width: 20%;text-align: right;">大分類：</td>
			<td style="width:  5%;"></td>
			<td style="width: 75%;text-align: left;">
				<?php echo $this->Form->input('bnriDaiCd', 
										array('style' => 'width:100px;', 
											'maxlength' => '3', 
											'error' => false, 
											'label' => false, 
											'div' => false, 
											'hidden' => false, 
											'class' => 'han', 
											'disabled' => 'disabled')) ?>
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
											'class' => 'han', 
											'disabled' => 'disabled')) ?>
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
