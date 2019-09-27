<div style= "position: relative; height: 295px; width: 100%; margin-top: 20px; margin-left: 20px;">
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
		<td style="text-align:left;" >荷主コード：</td>
		<td class="colNinusiCd" style="text-align:left;">
			<?php echo $this->Form->input('ninusiCd',
							array('style' => 'width:100px;',
								'error' => false,
								'label' => false,
								'div' => false,
								'hidden' => false,
								'class' => 'han',
								'disabled' => 'disabled')) ?>
		</td>
		<td style="text-align:left;" >荷主名称：</td>
		<td colspan="3" class="colNinusiCd" style="text-align:left;">
			<?php echo $this->Form->input('ninusiNm',
							array('style' => 'width:100px;',
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
		<td style="text-align:left;" >組織コード：</td>
		<td colspan="5"  class="colSosikiCd" style="text-align:left;">
			<?php echo $this->Form->input('sosikiCd',
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
	<!-- ３行目 -->
	<tr style="height:30px;">
	   <td style="text-align:left;" >組織名称：</td>
	   <td colspan="5"  class="colSosikiCd" style="text-align:left;">
	   		<?php echo $this->Form->input('sosikiNm',
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
	   <td style="text-align:left;" >組織略称：</td>
	   <td colspan="5"  class="colSosikiCd" style="text-align:left;">
	   		<?php echo $this->Form->input('sosikiRyaku',
							array('style' => 'width:150px;',
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
