<?php
            echo $this->Html->script(array('scriptaculous','window','base64','DCMSCommon','DCMSMessage','DCMSWPO010_RELOAD','DCMSWPO010_FILE_UPLOAD','DCMSWPO010_POPUP','DCMSWPO010_UPDATE'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','themes/alert','themes/alphacube.css','DCMSWPO'), false, array('inline'=>false));
?>

			<div id="wrapper_s">
			
                <div id="upload" sytle="position:relative;width:800px;">
                            <div style="position:relative;width:500px;float:left;margin-top:3px;text-align:left;">取込ボタンでエレコムＷＭＳより新しい集計ピッキングデータを抽出します</div>
							<input type="button" value="取込" style="height:24px;width:60px;margin-left:2px;" id="refButton" >
                            <div id="reloadingMessage" style="display:none;padding-left:500px;text-align:left;">
								...データを取得中です。
							</div>
                </div>
                
                <!-- 区切り線 -->
                <div class="line"></div>
                <!--/ 区切り線 -->
                
				<!-- 更新ボタン-->
				<div style="display:<?php if (!empty($batchNmLst)) echo "block"; else echo "none" ?>;width:780px; max-width:790px;" align="right">
					<input type="button" value="更新" id="updateButton" >
				</div>
				<!--/ 更新ボタン-->
				
				<!-- 更新回数　 -->
                <div style="position:relative;width:450px;float:left;margin-top:3px;text-align:left; max-width:790px;">更新回数コード&nbsp;&nbsp;<?php 
                
                if (!empty($batchNmLst)) { 
	                
	                $updateCountArray = array();
	                
	                foreach ($batchNmLst as $batchNm) {
		                
		                $updateCountArray[] = $batchNm["UPD_CNT_CD"];
	                }
	                
	                $updateCountArray = array_unique($updateCountArray);
	                
	                sort($updateCountArray);
	                
	                $tenFlg = 0;
	                
	                foreach ($updateCountArray as $array) {
		                
		                if ($tenFlg == 1) {
			                
			                echo ",";
		                }
		                
		                echo $array;
		                
		                $tenFlg = 1;
	                }
                } 
                
                ?></div>
				<!--/ 更新ボタン-->
				
                <!-- メインテーブル-->
                <div class="resizable_s" style="height:600px;" id="lstTable">
	                <div class="tableRowTitle row_ttl_1" style="width:830px;">
						<div class="tableCell cell_ttl_1" style="width:60px;" ><?php echo Configure::read("Bango") ?></div>
						<div class="tableCell cell_ttl_1" style="width:120px;" >出荷指示日</div>
						<div class="tableCell cell_ttl_1" style="width:80px;" >バッチNo</div>
						<div class="tableCell cell_ttl_1" style="width:140px;" >ピッキングリスト数</div>
						<div class="tableCell cell_ttl_1" style="width:80px;" >ピース数</div>
						<div class="tableCell cell_ttl_1" style="width:290px;" >バッチ名</div>
	                </div>

<?php
                        if (!empty($batchNmLst)) {
                        	
                        	$index = 1;
                        
                            foreach($batchNmLst as $array) {
?>

	                <div class="tableRow row_dat" style="width:830px;">
		                <div class="tableCell cell_dat bango" style="width:60px;"><?php echo $index?></div>
		                <div class="tableCell cell_dat date" style="width:120px;"><?php echo str_replace("-","/",$array['YMD_SYORI'])?></div>
		                <div class="tableCell cell_dat kbn" style="width:80px;"><?php echo $array['S_BATCH_NO']?></div>
		                <div class="tableCell cell_dat cnt" style="width:140px;"><?php echo number_format($array['PICKING_NUM'])?></div>
		                <div class="tableCell cell_dat kin" style="width:80px;"><?php echo number_format($array['TOTAL_PIECE_NUM'])?></div>
		                <div class="tableCell cell_dat nm" style="width:290px;"><?php echo $array['BATCH_NM']?></div>
	                    <div class="hiddenData"><?php echo $index ?></div>
	                    <div class="hiddenData"><?php echo $array['YMD_SYORI'] ?></div>
	                    <div class="hiddenData"><?php echo $array['S_BATCH_NO'] ?></div>
	                    <div class="hiddenData"><?php echo $array['PICKING_NUM'] ?></div>
	                    <div class="hiddenData"><?php echo $array['TOTAL_PIECE_NUM'] ?></div>
	                    <div class="hiddenData"><?php echo $array['BATCH_NM'] ?></div>
	                    <div class="hiddenData"><?php echo $array['BATCH_NM_CD'] ?></div>
	                    <div class="hiddenData"><?php echo $array['CHANGE'] ?></div>
	                    <div class="hiddenData"><?php echo $array['S_BATCH_NO_CD'] ?></div>
	                    <div class="hiddenData"><?php echo $array['UPD_CNT_CD'] ?></div>
	                    <div class="hiddenData"><?php echo $array['YMD_UNYO'] ?></div>
	                </div>
<?php
							
								$index++;
                            }
                        }
?>
                </div>
                <!--/ メインテーブル-->
            </div>