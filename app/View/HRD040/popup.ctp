<div style= "position: relative; height: 150px; width: 100%; margin-top: 20px;">
  <table width="100%" class="field">
		<colgroup width="50px">
		<colgroup width="850px">
		<tbody>

		<tr style="height:30px;">
			<td style="width: 20%;text-align: right;">スタッフコード：</td>
			<td style="width:  5%;"></td>
			<td style="width: 75%;text-align: left;">
				<?php echo $this->Form->input('inp_staff_cd', 
										array('style' => 'width:100px;', 
											'maxlength' => '10', 
											'error' => false, 
											'label' => false, 
											'div' => false, 
											'hidden' => false, 
											'class' => 'han', 
											'disabled' => 'disabled')) ?>
			</td>
		</tr>
		<tr style="height:30px;">
		   <td style="width: 20%;text-align: right;">サブシステムID：</td>
		   <td style="width:  5%;"></td>
		   <td style="width: 75%;text-align: left;">
		   		<?php echo $this->Form->input('inp_subsystem_id', 
		   								array('style' => 'width:100px;', 
		   									'maxlength' => '3', 
		   									'error' => false, 
		   									'label' => false, 
		   									'div' => false, 
		   									'hidden' => false, 
		   									'class' => 'zen')) ?>
		   	</td>
		</tr>
		<tr style="height:30px;">
		   <td style="width: 20%;text-align: right;">機能ID：</td>
		   <td style="width:  5%;"></td>
		   <td style="width: 75%;text-align: left;">
		   		<?php echo $this->Form->input('inp_kino_id', 
		   								array('style' => 'width:100px;', 
		   									'maxlength' => '3', 
		   									'error' => false, 
		   									'label' => false, 
		   									'div' => false, 
		   									'hidden' => false, 
		   									'class' => 'zen')) ?>
		   </td>
		</tr>
		<tr">
		</tr>
		<tr>
		</tr>

		</tbody>

  </table>
</div>
