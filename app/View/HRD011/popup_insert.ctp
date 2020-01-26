<div style= "position: relative; height: 150px; width: 100%; margin-top: 20px;">
  <table width="100%" class="field">
		<colgroup width="50px">
		<colgroup width="850px">
		<tbody>

		<tr style="height:30px;">
			<td style="width: 20%;text-align: right;">スタッフコード：</td>
			<td style="width:  5%;"></td>
			<td style="width: 75%;text-align: left;">
					<?php echo $this->Form->input('staffcd',
											array('style' => 'width:150px;',
												'maxlength' => '10',
												'error' => false,
												'label' => false,
												'div' => false,
												'hidden' => false,
												'class' => 'han')) ?>
			</td>
		</tr>
		<tr style="height:30px;">
		   <td style="width: 20%;text-align: right;">サブシステム：</td>
		   <td style="width:  5%;"></td>
		   <td style="width: 75%;text-align: left;">
		   			<?php echo $this->Form->input('subSystemid',
		   									array('style' => 'width:300px;',
		   										'maxlength' => '3',
		   										'error' => false,
		   										'label' => false,
		   										'div' => false,
		   										'hidden' => false,
		   										'class' => 'han')) ?>
		   </td>
		</tr>
		<tr style="height:30px;">
		   <td style="width: 20%;text-align: right;">機能ＩＤ：</td>
		   <td style="width:  5%;"></td>
		   <td style="width: 75%;text-align: left;">
		   			<?php echo $this->Form->input('kinoid',
		   									array('style' => 'width:150px;',
		   										'maxlength' => '3',
		   										'error' => false,
		   										'label' => false,
		   										'div' => false,
		   										'hidden' => false,
		   										'class' => 'han')) ?>
		   </td>
		</tr>
		</tbody>

  </table>
</div>
