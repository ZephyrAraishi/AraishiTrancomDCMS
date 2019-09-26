<div style= "position: relative; height: 170px; width: 100%; margin-top: 20px;">
  <table width="100%">
  	<tr style="height:30px;">
  	   <td style="width: 50%;text-align: right;">出荷指示日：</td>
  	   <td style="width: 10%;"></td>
  	   <td style="width: 40%;text-align: left;" id="sijiDay"></td>
  	</tr>
  	<tr style="height:30px;">
  	   <td style="width: 50%;text-align: right;">バッチコード：</td>
  	   <td style="width: 10%;"></td>
  	   <td style="width: 40%;text-align: left;" id="batCD"></td>
  	</tr>
  	<tr style="height:30px;">
  	   <td style="width: 50%;text-align: right;">ピッキングリスト数：</td>
  	   <td style="width: 10%;"></td>
  	   <td style="width: 40%;text-align: left;" id="pickCnt"></td>
  	</tr>
  	<tr style="height:30px;">
  	   <td style="width: 50%;text-align: right;">ピース総数：</td>
  	   <td style="width: 10%;"></td>
  	   <td style="width: 40%;text-align: left;" id="pieceCnt"></td>
  	</tr>
  	<tr style="height:30px;">
  	   <td style="width: 50%;text-align: right;">バッチ名：</td>
  	   <td style="width: 10%;"></td>
  	   <td style="width: 40%;text-align: left;">
  	       <select id="batchNm" size="1">
  	       <option value=""></option>
           <?php 
  	           foreach($batNmLst as $key => $value) {
           ?>
                   <option value="<?php echo $key ?>"><?php echo $value?></option>
  	       <?php
			   }
  	       ?> 
  	       </select>
  	   </td>
  	</tr>
  	
  </table>
</div>
