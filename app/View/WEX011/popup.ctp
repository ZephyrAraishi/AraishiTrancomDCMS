<div style= "position: relative; height: 150px; width: 100%; margin-top: 20px;">
  <table width="100%" class="field">
		<colgroup width="50px">
		<colgroup width="850px">
		<tbody>

		<tr style="height:30px;">
			<td style="width: 20%;text-align: right;">荷主コード：</td>
			<td style="width:  5%;"></td>
			<td style="width: 75%;text-align: left;">
				<?php echo $this->Form->input('NinusiCd',
										array('style' => 'width:200px;',
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
		   <td style="width: 20%;text-align: right;">荷主名称：</td>
		   <td style="width:  5%;"></td>
		   <td style="width: 75%;text-align: left;">
		   		<?php echo $this->Form->input('NinusiNm',
		   								array('style' => 'width:600px;',
		   									'maxlength' => '20',
		   									'error' => false,
		   									'label' => false,
		   									'div' => false,
		   									'hidden' => false,
		   									'class' => 'zen')) ?>
		   	</td>
		</tr>
		<tr style="height:30px;">
		   <td style="width: 20%;text-align: right;">荷主略称：</td>
		   <td style="width:  5%;"></td>
		   <td style="width: 75%;text-align: left;">
		   		<?php echo $this->Form->input('NinusiRyaku',
		   								array('style' => 'width:250px;',
		   									'maxlength' => '6',
		   									'error' => false,
		   									'label' => false,
		   									'div' => false,
		   									'hidden' => false,
		   									'class' => 'zen')) ?>
		   </td>
		</tr>
		</tbody>

  </table>
</div>
