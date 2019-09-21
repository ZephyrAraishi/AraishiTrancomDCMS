<?php
	echo $this->Html->script(array('effects','window','dropshadow','base64','protocalendar',
								   'DCMSCommon','DCMSTPM040_COMMON','DCMSMessage','DCMSValidation',
								   'DCMSTPM040_RELOAD','DCMSTPM040_POPUP','DCMSTPM040_PANEL2',
								   'DCMSTPM040_CALENDAR','DCMSTPM040_UPDATE'), array('inline'=>false));
	echo $this->Html->css(array('themes/default','themes/alert','themes/alphacube','calendar','shadows','DCMSTPM'), false, array('inline'=>false));
?>

<!-- WRAPPER -->
<div id="wrapper_l" style="width:1280px;">

	<!-- トグルボタン -->
	
	<div id="toggle">
		<div id="Attention">【更新モード中】　必ず変更完了ボタンを押してください。</div>
		<form name="form1" method="post" action="">
			<ul class="toggle-list">
				<li><a href="index"><label id="jidoButton" style="width:100px;text-align:center">自動リロード</label></a></li>
				<li><label class="selected" id="koshinButton"  style="width:100px;text-align:center" >更新モード</label></li>
			</ul>
		</form>
	</div>
	<div id="reloadingMessage" style="display:none;">...データを取得中です。</div>
	
	<!--/ トグルボタン -->
	<div style="text-align:right;font-size:15px">
		<input type="button" value="変更完了" style="width:120px;height:30px;" id="updateButton" >
	</div>
	<div id="yusenSorterTitleArea1">
		<div class="yusenSorterTitle" style="left:0px;top:30px;" id="nameA">
			<?php echo $sorterNmA ?>
		</div>
		<div class="yusenSorterTitle" style="left:330px;top:30px;" id="nameB">
			<?php echo $sorterNmB ?>
		</div>
		<div class="yusenSorterTitle" style="left:660px;top:30px;" id="nameC">
			<?php echo $sorterNmC ?>
		</div>
		<div class="yusenSorterTitle" style="left:990px;top:30px;">仮置場</div>
	</div>
	
	<div id="yusenSorterTitleArea2">
		<div class="yusenSorterTitle" style="left:0px;top:10px;">
			<div style="display:block;position:absolute;top:0px;height:20px;left:0px;width:80px;font-size:15px;text-align:right;"　>生産性:</div>
			<div style="display:block;position:absolute;top:0px;left:100px;width:50px;text-align:left;">
				<input type="text" style="width:50px;height:20px;" maxlength="4" class="han" id="seisanseiA" value="<?php 
					
						echo $seisanseiA;
					
				?>">
			</div>
			<div style="display:block;position:absolute;top:30px;height:20px;left:0px;width:80px;font-size:15px;text-align:right;"　>開始時間:</div>
			<div style="display:block;position:absolute;top:30px;left:100px;width80px;">
				<input type="text" style="width:80px;height:20px;" id="dateA" value="<?php 
					
					if (!empty($datetimeA) && $datetimeA != "" ){
						
						echo substr($datetimeA, 0,4) . substr($datetimeA, 5,2) . substr($datetimeA, 8,2);
					}
				?>">
			</div>
			<div style="display:block;position:absolute;top:30px;left:190px;width:50px;">
				<input type="text" style="width:50px;height:20px;" maxlength="4" class="han" id="timeA" value="<?php 
					
					if (!empty($datetimeA) && $datetimeA != "" ){
						
						echo substr($datetimeA, 11,2) . substr($datetimeA, 14,2);
					}
					
				?>">
			</div>
		</div>
		<div class="yusenSorterTitle" style="left:330px;top:10px;">
			<div style="display:block;position:absolute;top:0px;height:20px;left:0px;width:80px;font-size:15px;text-align:right;"　>生産性:</div>
			<div style="display:block;position:absolute;top:0px;left:100px;width:50px;text-align:left;">
				<input type="text" style="width:50px;height:20px;" maxlength="4" class="han" id="seisanseiB" value="<?php 
					
						echo $seisanseiB;
					
				?>">
			</div>
			<div style="display:block;position:absolute;top:30px;height:20px;left:0px;width:80px;font-size:15px;text-align:right;"　>開始時間:</div>
			<div style="display:block;position:absolute;top:30px;left:100px;width80px;">
				<input type="text" style="width:80px;height:20px;" id="dateB" value="<?php 
					
					if (!empty($datetimeB) && $datetimeB != "" ){
						
						echo substr($datetimeB, 0,4) . substr($datetimeB, 5,2) . substr($datetimeB, 8,2);
					}
					
				?>">
			</div>
			<div style="display:block;position:absolute;top:30px;left:190px;width:50px;">
				<input type="text" style="width:50px;height:20px;" maxlength="4" class="han" id="timeB" value="<?php 
					
					if (!empty($datetimeB) && $datetimeB != "" ){
						
						echo substr($datetimeB, 11,2) . substr($datetimeB, 14,2);
					}
					
				?>">
			</div>
		</div>
		<div class="yusenSorterTitle" style="left:660px;top:10px;">
			<div style="display:block;position:absolute;top:0px;height:20px;left:0px;width:80px;font-size:15px;text-align:right;"　>生産性:</div>
			<div style="display:block;position:absolute;top:0px;left:100px;width:50px;text-align:left;">
				<input type="text" style="width:50px;height:20px;" maxlength="4" class="han" id="seisanseiC" value="<?php 
					
						echo $seisanseiC;
					
				?>">
			</div>
			<div style="display:block;position:absolute;top:30px;height:20px;left:0px;width:80px;font-size:15px;text-align:right;"　>開始時間:</div>
			<div style="display:block;position:absolute;top:30px;left:100px;width80px;">
				<input type="text" style="width:80px;height:20px;" id="dateC" value="<?php 
					
					if (!empty($datetimeC) && $datetimeC != "" ){
						
						echo substr($datetimeC, 0,4) . substr($datetimeC, 5,2) . substr($datetimeC, 8,2);
					}
					
				?>">
			</div>
			<div style="display:block;position:absolute;top:30px;left:190px;width:50px;">
				<input type="text" style="width:50px;height:20px;" maxlength="4" class="han" id="timeC" value="<?php 
					
					if (!empty($datetimeC) && $datetimeC != "" ){
						
						echo substr($datetimeC, 11,2) . substr($datetimeC, 14,2);
					}
					
				?>">
			</div>
		</div>
	</div>
</div>


<div id="panelArea" >

	 <div class="SORTER_COVER" id="sorter_cover1"></div>
	 <div class="SORTER_COVER" id="sorter_cover2"></div>
	 <div class="SORTER_COVER" id="sorter_cover3"></div>
	 <div class="SORTER_COVER" id="sorter_cover4"></div>

	 <!--/ A号機-->
	 <?php $count = 1;$pieceA = 0; ?>
	 <?php foreach ($sorterListA as $array): ?>
	 <div class="panel" style="top:<?php echo (160 * ($count - 1)) . "" ?>px;left:0px;" id="S<?php echo $array['S_BATCH_NO_CD'] ."D" . $array['YMD_SYORI'] ?>">
	 	<div class="panelBase" style="background:<?php

	 	if (substr($array['S_BATCH_NO'],0,1) == 'A') {
	 		echo "pink";
	 	} else if(substr($array['S_BATCH_NO'],0,1) == 'B') {
	 		echo "orange";
	 	} else if(substr($array['S_BATCH_NO'],0,1) == 'C') {
	 		echo "greenyellow";
	 	} else {
	 		echo "red";
	 	}

	 	?>;"></div>
	 	<div class="panelData panelS_BATCH_NM"><?php echo $array['BATCH_NM'] ?></div>
	 	<div class="panelData panelS_BATCH_NO" style="<?php if($array['WMS_FLG'] == "1") echo "color:red" ?>"><?php echo $array['S_BATCH_NO'] ?></div>
	 	<div class="panelData panelSURYO_PIECE"><?php echo number_format($array['PIECE_SOSU']) ?></div>
	 	<div class="panelData panelKBN_BATCH_STATUS_NM <?php if($array['KBN_BATCH_STATUS'] == "01") {
	 														 	echo "KBN_BATCH_STATUS_NM_SYORICYU";
	 														 } else if ($array['KBN_BATCH_STATUS'] == "89") {
	 														 	echo "KBN_BATCH_STATUS_NM_SYORISUMI";
	 														 } else {
	 														 	echo "KBN_BATCH_STATUS_NM_MISYORI";
	 														 } ?>"><?php echo $array['STATUS_NM'] ?></div>
	 	<div class="panelData panelYMD_SYORI"><?php echo str_replace ("-","/",substr($array['YMD_SYORI'],5)); ?></div>
	 	<div class="panelData panelSURYO_SHOOT"><?php if ($array['SURYO_SHOOT'] != "") echo number_format($array['SURYO_SHOOT']); ?></div>
	 	<div class="panelData panelPICKING_TIME"><?php 
	 	
	 	
	 				if($array['KBN_BATCH_STATUS'] == "01") {
					 	echo $array['TIME_YOSO'];
					 } else if ($array['KBN_BATCH_STATUS'] == "89") {
						echo "<u>" . $array['TIME_SYURYO'] . "</u>";
					 }
	 	?></div>
	 	<div class="panelData panelSORTER_TIME"><?php 
	 	
	 				if($array['SORTER_SEISANSEI'] != 0 && $array['SORTER_SEISANSEI'] != "") {
	 	
		 				$zanPiece = 0;
		 				
		 				if($array['SORTER_ZAN'] != null) {
			 				
			 				$zanPiece = $array['SORTER_ZAN'];
		 				} else {
			 				
			 				$zanPiece = $array['PIECE_SOSU'];
		 				}
	 	
		 				$pieceA += $zanPiece;
	
						$dt = new DateTime($datetimeA);
						$second = round($pieceA / ($array['SORTER_SEISANSEI'] / 60 / 60));
						
						if ($second != null && is_numeric($second) && $second > 0) {
						
							$dt->add(new DateInterval('PT' . $second . 'S'));
						
						}
						echo $dt->format('H:i');
					}

		 	
	 	?></div>
	 	<div class="panelData panelTOP_PRIORITY_FLG <?php if($array['TOP_PRIORITY_FLG'] == "1") echo "TOP_PRIORITY_FLG_SELECTED"; ?>"></div>
	 	<div class="panelCover"></div>
	 	<div class="hiddenData S_BATCH_NO_CD"><?php echo $array['S_BATCH_NO_CD'] ?></div>
	 	<div class="hiddenData S_BATCH_NO"><?php echo $array['S_BATCH_NO'] ?></div>
	 	<div class="hiddenData S_BATCH_NM"><?php echo $array['BATCH_NM'] ?></div>
	 	<div class="hiddenData TOP_PRIORITY_FLG"><?php echo $array['TOP_PRIORITY_FLG'] ?></div>
	 	<div class="hiddenData PRIORITY"><?php echo $array['PRIORITY'] ?></div>
	 	<div class="hiddenData KBN_BATCH_STATUS"><?php echo $array['KBN_BATCH_STATUS'] ?></div>
	 	<div class="hiddenData YMD_SYORI"><?php echo $array['YMD_SYORI'] ?></div>
	 	<div class="hiddenData SORTER_CD">A</div>
	 	<div class="hiddenData ORDER"><?php echo $count ?></div>
	 	<div class="hiddenData CURRENT_SORTER_CD">A</div>
	 	<div class="hiddenData CURRENT_ORDER"><?php echo $count++ ?></div>
	 	<div class="hiddenData WMS_FLG"><?php echo $array['WMS_FLG'] ?></div>
	 </div>
	 <?php endforeach; ?>
	 <!--/ A号機-->

	 <!-- B号機-->
	 <?php $count = 1;$pieceB = 0; ?>
	 <?php foreach ($sorterListB as $array): ?>
	 <div class="panel" style="top:<?php echo (160 * ($count - 1)) . "" ?>px;left:330px;" id="S<?php echo $array['S_BATCH_NO_CD'] ."D" . $array['YMD_SYORI'] ?>">
	 	<div class="panelBase" style="background:<?php

	 	if (substr($array['S_BATCH_NO'],0,1) == 'A') {
	 		echo "pink";
	 	} else if(substr($array['S_BATCH_NO'],0,1) == 'B') {
	 		echo "orange";
	 	} else if(substr($array['S_BATCH_NO'],0,1) == 'C') {
	 		echo "greenyellow";
	 	} else {
	 		echo "red";
	 	}

	 	?>;"></div>
	 	<div class="panelData panelS_BATCH_NM"><?php echo $array['BATCH_NM'] ?></div>
	 	<div class="panelData panelS_BATCH_NO" style="<?php if($array['WMS_FLG'] == "1") echo "color:red" ?>"><?php echo $array['S_BATCH_NO'] ?></div>
	 	<div class="panelData panelSURYO_PIECE"><?php echo number_format($array['PIECE_SOSU']) ?></div>
	 	<div class="panelData panelKBN_BATCH_STATUS_NM <?php if($array['KBN_BATCH_STATUS'] == "01") {
	 														 	echo "KBN_BATCH_STATUS_NM_SYORICYU";
	 														 } else if ($array['KBN_BATCH_STATUS'] == "89") {
	 														 	echo "KBN_BATCH_STATUS_NM_SYORISUMI";
	 														 } else {
	 														 	echo "KBN_BATCH_STATUS_NM_MISYORI";
	 														 } ?>"><?php echo $array['STATUS_NM'] ?></div>
	 	<div class="panelData panelYMD_SYORI"><?php echo str_replace ("-","/",substr($array['YMD_SYORI'],5)); ?></div>
	 	<div class="panelData panelSURYO_SHOOT"><?php if ($array['SURYO_SHOOT'] != "") echo number_format($array['SURYO_SHOOT']); ?></div>
	 	<div class="panelData panelPICKING_TIME"><?php 
	 	
	 	
	 				if($array['KBN_BATCH_STATUS'] == "01") {
					 	echo $array['TIME_YOSO'];
					 } else if ($array['KBN_BATCH_STATUS'] == "89") {
						echo "<u>" . $array['TIME_SYURYO'] . "</u>";
					 }
	 	?></div>
	 	<div class="panelData panelSORTER_TIME"><?php 
	 	
	 				if($array['SORTER_SEISANSEI'] != 0 && $array['SORTER_SEISANSEI'] != "") {
	 	
		 				$zanPiece = 0;
		 				
		 				if($array['SORTER_ZAN'] != null) {
			 				
			 				$zanPiece = $array['SORTER_ZAN'];
		 				} else {
			 				
			 				$zanPiece = $array['PIECE_SOSU'];
		 				}
	 	
		 				$pieceB += $zanPiece;
	
						$dt = new DateTime($datetimeB);
						$second = round($pieceB / ($array['SORTER_SEISANSEI'] / 60 / 60));
						
						if ($second != null && is_numeric($second) && $second > 0) {
						
							$dt->add(new DateInterval('PT' . $second . 'S'));
						
						}
						echo $dt->format('H:i');
						
					}
		 	
	 	?></div>
	 	<div class="panelData panelTOP_PRIORITY_FLG <?php if($array['TOP_PRIORITY_FLG'] == "1") echo "TOP_PRIORITY_FLG_SELECTED"; ?>"></div>
	 	<div class="panelCover"></div>
	 	<div class="hiddenData S_BATCH_NO_CD"><?php echo $array['S_BATCH_NO_CD'] ?></div>
	 	<div class="hiddenData S_BATCH_NO"><?php echo $array['S_BATCH_NO'] ?></div>
	 	<div class="hiddenData S_BATCH_NM"><?php echo $array['BATCH_NM'] ?></div>
	 	<div class="hiddenData TOP_PRIORITY_FLG"><?php echo $array['TOP_PRIORITY_FLG'] ?></div>
	 	<div class="hiddenData PRIORITY"><?php echo $array['PRIORITY'] ?></div>
	 	<div class="hiddenData KBN_BATCH_STATUS"><?php echo $array['KBN_BATCH_STATUS'] ?></div>
	 	<div class="hiddenData YMD_SYORI"><?php echo $array['YMD_SYORI'] ?></div>
	 	<div class="hiddenData SORTER_CD">B</div>
	 	<div class="hiddenData ORDER"><?php echo $count ?></div>
	 	<div class="hiddenData CURRENT_SORTER_CD">B</div>
	 	<div class="hiddenData CURRENT_ORDER"><?php echo $count++ ?></div>
	 	<div class="hiddenData WMS_FLG"><?php echo $array['WMS_FLG'] ?></div>
	 </div>
	 <?php endforeach; ?>
	 <!--/ B号機-->

	 <!-- C号機-->
	 <?php $count = 1;$pieceC = 0; ?>
	 <?php foreach ($sorterListC as $array): ?>
	 <div class="panel" style="top:<?php echo (160 * ($count - 1)) . "" ?>px;left:660px;" id="S<?php echo $array['S_BATCH_NO_CD'] ."D" . $array['YMD_SYORI'] ?>">
	 	<div class="panelBase" style="background:<?php

	 	if (substr($array['S_BATCH_NO'],0,1) == 'A') {
	 		echo "pink";
	 	} else if(substr($array['S_BATCH_NO'],0,1) == 'B') {
	 		echo "orange";
	 	} else if(substr($array['S_BATCH_NO'],0,1) == 'C') {
	 		echo "greenyellow";
	 	} else {
	 		echo "red";
	 	}

	 	?>;"></div>
	 	<div class="panelData panelS_BATCH_NM"><?php echo $array['BATCH_NM'] ?></div>
	 	<div class="panelData panelS_BATCH_NO" style="<?php if($array['WMS_FLG'] == "1") echo "color:red" ?>"><?php echo $array['S_BATCH_NO'] ?></div>
	 	<div class="panelData panelSURYO_PIECE"><?php echo number_format($array['PIECE_SOSU']) ?></div>
	 	<div class="panelData panelKBN_BATCH_STATUS_NM <?php if($array['KBN_BATCH_STATUS'] == "01") {
	 														 	echo "KBN_BATCH_STATUS_NM_SYORICYU";
	 														 } else if ($array['KBN_BATCH_STATUS'] == "89") {
	 														 	echo "KBN_BATCH_STATUS_NM_SYORISUMI";
	 														 } else {
	 														 	echo "KBN_BATCH_STATUS_NM_MISYORI";
	 														 } ?>"><?php echo $array['STATUS_NM'] ?></div>
	 	<div class="panelData panelYMD_SYORI"><?php echo str_replace ("-","/",substr($array['YMD_SYORI'],5)); ?></div>
	 	<div class="panelData panelSURYO_SHOOT"><?php if ($array['SURYO_SHOOT'] != "") echo number_format($array['SURYO_SHOOT']); ?></div>
	 	<div class="panelData panelPICKING_TIME"><?php 
	 	
	 	
	 				if($array['KBN_BATCH_STATUS'] == "01") {
					 	echo $array['TIME_YOSO'];
					 } else if ($array['KBN_BATCH_STATUS'] == "89") {
						echo "<u>" . $array['TIME_SYURYO'] . "</u>";
					 }
	 	?></div>
	 	<div class="panelData panelSORTER_TIME"><?php 
	 	
	 	
	 				if($array['SORTER_SEISANSEI'] != 0 && $array['SORTER_SEISANSEI'] != "") {
	 	
		 				$zanPiece = 0;
		 				
		 				if($array['SORTER_ZAN'] != null) {
			 				
			 				$zanPiece = $array['SORTER_ZAN'];
		 				} else {
			 				
			 				$zanPiece = $array['PIECE_SOSU'];
		 				}
	 	
		 				$pieceC += $zanPiece;
			 	
						$dt = new DateTime($datetimeC);
						$second = round($pieceC / ($array['SORTER_SEISANSEI'] / 60 / 60));
						
						if ($second != null && is_numeric($second) && $second > 0) {
						
							$dt->add(new DateInterval('PT' . $second . 'S'));
						
						}
						echo $dt->format('H:i');
					
					}
		 	
	 	?></div>
	 	<div class="panelData panelTOP_PRIORITY_FLG <?php if($array['TOP_PRIORITY_FLG'] == "1") echo "TOP_PRIORITY_FLG_SELECTED"; ?>"></div>
	 	<div class="panelCover"></div>
	 	<div class="hiddenData S_BATCH_NO_CD"><?php echo $array['S_BATCH_NO_CD'] ?></div>
	 	<div class="hiddenData S_BATCH_NO"><?php echo $array['S_BATCH_NO'] ?></div>
	 	<div class="hiddenData S_BATCH_NM"><?php echo $array['BATCH_NM'] ?></div>
	 	<div class="hiddenData TOP_PRIORITY_FLG"><?php echo $array['TOP_PRIORITY_FLG'] ?></div>
	 	<div class="hiddenData PRIORITY"><?php echo $array['PRIORITY'] ?></div>
	 	<div class="hiddenData KBN_BATCH_STATUS"><?php echo $array['KBN_BATCH_STATUS'] ?></div>
	 	<div class="hiddenData YMD_SYORI"><?php echo $array['YMD_SYORI'] ?></div>
	 	<div class="hiddenData SORTER_CD">C</div>
	 	<div class="hiddenData ORDER"><?php echo $count ?></div>
	 	<div class="hiddenData CURRENT_SORTER_CD">C</div>
	 	<div class="hiddenData CURRENT_ORDER"><?php echo $count++ ?></div>
	 	<div class="hiddenData WMS_FLG"><?php echo $array['WMS_FLG'] ?></div>
	 </div>
	 <?php endforeach; ?>
	 <!--/ C号機-->

	 <!-- 仮置き場-->
	 <?php $count = 1; ?>
	 <?php foreach ($sorterListKari as $array): ?>
	 <div class="panel" style="top:<?php echo (160 * ($count - 1)) . "" ?>px;left:990px;" id="S<?php echo $array['S_BATCH_NO_CD'] ."D" . $array['YMD_SYORI'] ?>">
	 	<div class="panelBase" style="background:<?php

	 	if (substr($array['S_BATCH_NO'],0,1) == 'A') {
	 		echo "pink";
	 	} else if(substr($array['S_BATCH_NO'],0,1) == 'B') {
	 		echo "orange";
	 	} else if(substr($array['S_BATCH_NO'],0,1) == 'C') {
	 		echo "greenyellow";
	 	} else {
	 		echo "red";
	 	}

	 	?>;"></div>
	 	<div class="panelData panelS_BATCH_NM"><?php echo $array['BATCH_NM'] ?></div>
	 	<div class="panelData panelS_BATCH_NO" style="<?php if($array['WMS_FLG'] == "1") echo "color:red" ?>"><?php echo $array['S_BATCH_NO'] ?></div>
	 	<div class="panelData panelSURYO_PIECE"><?php echo number_format($array['PIECE_SOSU']) ?></div>
	 	<div class="panelData panelKBN_BATCH_STATUS_NM <?php if($array['KBN_BATCH_STATUS'] == "01") {
	 														 	echo "KBN_BATCH_STATUS_NM_SYORICYU";
	 														 } else if ($array['KBN_BATCH_STATUS'] == "89") {
	 														 	echo "KBN_BATCH_STATUS_NM_SYORISUMI";
	 														 } else {
	 														 	echo "KBN_BATCH_STATUS_NM_MISYORI";
	 														 } ?>"><?php echo $array['STATUS_NM'] ?></div>
	 	<div class="panelData panelYMD_SYORI"><?php echo str_replace ("-","/",substr($array['YMD_SYORI'],5)); ?></div>
	 	<div class="panelData panelSURYO_SHOOT"><?php if ($array['SURYO_SHOOT'] != "") echo number_format($array['SURYO_SHOOT']) ?></div>
	 	<div class="panelData panelPICKING_TIME"><?php if($array['KBN_BATCH_STATUS'] == "01") {
	 														 	echo $array['TIME_YOSO'];
	 														 } ?></div>
	 	<div class="panelData panelSORTER_TIME"><?php echo ""; ?></div>
	 	<div class="panelData panelTOP_PRIORITY_FLG <?php if($array['TOP_PRIORITY_FLG'] == "1") echo "TOP_PRIORITY_FLG_SELECTED"; ?>"></div>
	 	<div class="panelCover"></div>
	 	<div class="hiddenData S_BATCH_NO_CD"><?php echo $array['S_BATCH_NO_CD'] ?></div>
	 	<div class="hiddenData S_BATCH_NO"><?php echo $array['S_BATCH_NO'] ?></div>
	 	<div class="hiddenData S_BATCH_NM"><?php echo $array['BATCH_NM'] ?></div>
	 	<div class="hiddenData TOP_PRIORITY_FLG"><?php echo $array['TOP_PRIORITY_FLG'] ?></div>
	 	<div class="hiddenData PRIORITY"><?php echo $array['PRIORITY'] ?></div>
	 	<div class="hiddenData KBN_BATCH_STATUS"><?php echo $array['KBN_BATCH_STATUS'] ?></div>
	 	<div class="hiddenData YMD_SYORI"><?php echo $array['YMD_SYORI'] ?></div>
	 	<div class="hiddenData SORTER_CD">K</div>
	 	<div class="hiddenData ORDER"><?php echo $count ?></div>
	 	<div class="hiddenData CURRENT_SORTER_CD">K</div>
	 	<div class="hiddenData CURRENT_ORDER"><?php echo $count++ ?></div>
	 	<div class="hiddenData WMS_FLG"><?php echo $array['WMS_FLG'] ?></div>
	 </div>
	 <?php endforeach; ?>
	 <!--/ 仮置き場-->
	 
</div>
<!--/ #wrapper-->
<div id="timeStamp" style="display:none;" ><?php echo $timestamp; ?></div>
