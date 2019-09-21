<div style= "position: relative; height: 300px; width: 100%; margin-top: 20px;">
	  <table  width="300px" align="center" class="field">
		<tbody>
	
			<tr style="height:30px;">
				<td  style="width:100px;text-align: right;">年月日:</td>
				<td style="width:20px;"></td>
			  	<td style="text-align: left;" id="ymd"></td>
			</tr>

			<tr style="height:30px;">
				<td style="width:100px;text-align: right;">大分類:</td>
				<td style="width:20px;"></td>
			  	<td  class="colBnriCd" style="text-align: left;" id="bnriDaiNm"></td>
			</tr>
			
			<tr style="height:30px;">
				<td style="width:100px;text-align: right;">中分類:</td>
				<td style="width:20px;"></td>
			  	<td  class="colBnriCd" style="text-align: left;" id="bnriCyuNm"></td>
			</tr>
			
			<tr style="height:30px;">
				<td style="width:100px;text-align: right;">細分類:</td>
				<td style="width:20px;"></td>
			  	<td  class="colBnriCd" style="text-align: left;" id="bnriSaiNm"></td>
			</tr>
			
			<tr style="height:30px;">
				<td style="width:100px;text-align: right;">単価:</td>
				<td style="width:20px;"></td>
			  	<td  class="colBnriCd" style="text-align: left;" id="tanka"></td>
			</tr>
			
			<tr style="height:30px;">
				<td style="width:100px;text-align: right;">時間:</td>
				<td style="width:20px;"></td>
			  	<td  class="colBnriCd" style="text-align: left;" id="jikan"></td>
			</tr>
			
			<tr style="height:30px;">
				<td style="width:100px;text-align: right;">人数:</td>
				<td style="width:20px;"></td>
			  	<td  class="colBnriCd" style="text-align: left;" id="ninzu"></td>
			</tr>
	
		</tbody>
	
	  </table>
	
	  <table class="field" width="400px" align="center" style="margin-top:30px;">
		<tbody>
	
		<!-- ３行目 -->
		<tr style="height:30px;">
		   <td style="width:50px;">物量</td>
		   <td class="colBnriCd" style="text-align:left;width:150px;">
		   		<?php echo $this->Form->input('buturyo', 
								array('style' => 'width:100px; text-align:right;', 
									'maxlength' => '8', 
									'error' => false, 
									'label' => false, 
									'div' => false, 
									'hidden' => false, 
									'class' => 'han')) ?>
		   </td>
		   <td style="width:100px;">ライン数量</td>
		   <td class="colBnriCd" style="text-align:left;width:150px;">
		   		<?php echo $this->Form->input('suryo_line', 
								array('style' => 'width:100px; text-align:right;', 
									'maxlength' => '8', 
									'error' => false, 
									'label' => false, 
									'div' => false, 
									'hidden' => false, 
									'class' => 'han')) ?>
		   </td>
		   <td></td>
		</tr>
	
		</tbody>
	
	  </table>
</div>
