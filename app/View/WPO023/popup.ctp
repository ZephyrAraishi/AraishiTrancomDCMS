<div style= "position: relative; height: 110px; width: 100%; margin-top: 20px;">
  <table width="100%">
   	<tr style="height:30px;display:none;">
  	   <td style="width: 40%;text-align: right;">番号：</td>
  	   <td style="width: 20%;"></td>
  	   <td style="width: 40%;text-align: left;" id="bango" ></td>
  	</tr>
  	<tr style="height:30px;">
  	   <td style="width: 40%;text-align: right;">バッチ名：</td>
  	   <td style="width: 20%;"></td>
  	   <td style="width: 40%;text-align: left;" id="batNM"></td>
  	</tr>
  	<tr style="height:30px;">
  	   <td style="width: 40%;text-align: right;">集計管理No：</td>
  	   <td style="width: 20%;"></td>
  	   <td style="width: 40%;text-align: left;" id="sKanriNo"></td>
  	</tr>
  	<tr style="height:30px;">
  	   <td style="width: 40%;text-align: right;">ステータス：</td>
  	   <td style="width: 20%;"></td>
  	   <td style="width: 40%;text-align: left;">
  	       <select id="kbnStatus" size="1">
           <?php 
  	           foreach($stsall as $key => $value) {
           ?>
                   <option value="<?php echo $key?>"><?php echo $value?></option>
  	       <?php
			   }
  	       ?> 
  	       </select>
  	   </td>
  	</tr>

  </table>
</div>
