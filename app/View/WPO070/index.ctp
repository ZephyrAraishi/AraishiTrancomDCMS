<?php
            echo $this->Html->script(array('scriptaculous','window','base64','DCMSCommon','DCMSMessage','DCMSWPO070_UPDATE'), array('inline'=>false));
            echo $this->Html->script(array('scriptaculous','window','base64','DCMSCommon','DCMSMessage','DCMSWPO070_IMPORT','DCMSWPO070_CSV'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','themes/alert','themes/alphacube.css','DCMSWPO'), false, array('inline'=>false));
?>

			<div id="wrapper_s">

               <div id="errors" sytle="width:800px;">
			<span style="font-size:16px;color:red;">
				<?php echo $message ?>
			</span>
		</div>

                <div id="upload" sytle="position:relative;width:800px;">
                            <div style="position:relative;width:500px;margin-top:3px;text-align:left;">OZAX商品マスタを取込ます</div>
				<div>
				<form id="shohin_form" action="" method="post" enctype="multipart/form-data" accept-charset="utf-8">
					<input id="shohin_file" name="shohin_file" type="file" style="width:400px;" value="" />
					<input type="button" value="アップロード" style=";margin-left:2px;" id="importButton" >
				</form>
				</div>
                            <div id="reloadingMessage" style="display:none;padding-left:500px;text-align:left;">
								...データを取得中です。
							</div>
                </div>

                <!-- 区切り線 -->
                <div class="line"></div>
                <!--/ 区切り線 -->

				<!-- 更新ボタン-->
				<div style="display:<?php if (!empty($shohinLst)) echo "block"; else echo "none" ?>;width:780px; max-width:790px;" align="right">
					<input type="button" value="取込" id="updateButton" >
				</div>

                <!-- メインテーブル-->
		  <div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>
                <div class="resizable_s" style="height:600px;" id="lstTable">
	                <div class="tableRowTitle row_ttl_1" style="width:2310px;">
						<div class="tableCell cell_ttl_1" style="width:300px;" >チェック内容</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >商品コード</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >商品名</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >WMS_JAN</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >WMS_ITF</div>
						<div class="tableCell cell_ttl_1" style="width:50px;" >賞味期限区分</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >JANコード1</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >JANコード2</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >JANコード3</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >ITFコード1</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >ITFコード2</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >ITFコード3</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >ハウスコード1</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >ハウスコード2</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >ハウスコード3</div>
						<div class="tableCell cell_ttl_1" style="width:50px;" >入数1</div>
						<div class="tableCell cell_ttl_1" style="width:50px;" >入数2</div>
						<div class="tableCell cell_ttl_1" style="width:50px;" >入数3</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >単位名1</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >単位名2</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >単位名3</div>
	                </div>

<?php
                        if (!empty($shohinLst)) {

                        	$index = 1;

                            foreach($shohinLst as $line) {
	                            foreach($line as $shohin) {
?>

	                <div class="tableRow row_dat" style="width:2310px;">
		                <div class="tableCell cell_dat bango" style="width:300px;"><?php echo $shohin['CHECK_NAIYO'] ?></div>
		                <div class="tableCell cell_dat date" style="width:100px;"><?php echo $shohin['SHOHIN_CODE'] ?></div>
		                <div class="tableCell cell_dat kbn" style="width:100px;"><?php echo $shohin['SHOHIN_MEI'] ?></div>
		                <div class="tableCell cell_dat cnt" style="width:100px;"><?php echo $shohin['WMS_JAN'] ?></div>
		                <div class="tableCell cell_dat kin" style="width:100px;"><?php echo $shohin['WMS_ITF'] ?></div>
		                <div class="tableCell cell_dat nm" style="width:50px;"><?php echo $shohin['SHOMI_KIGEN_KUBUN']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $shohin['JAN_1']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $shohin['JAN_2']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $shohin['JAN_3']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $shohin['ITF_1']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $shohin['ITF_2']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $shohin['ITF_3']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $shohin['HOUSECODE_1']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $shohin['HOUSECODE_2']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $shohin['HOUSECODE_3']?></div>
		                <div class="tableCell cell_dat nm" style="width:50px;"><?php echo $shohin['NISUGATA_1_IRISU']?></div>
		                <div class="tableCell cell_dat nm" style="width:50px;"><?php echo $shohin['NISUGATA_2_IRISU']?></div>
		                <div class="tableCell cell_dat nm" style="width:50px;"><?php echo $shohin['NISUGATA_3_IRISU']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $shohin['NISUGATA_1_TANI']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $shohin['NISUGATA_2_TANI']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $shohin['NISUGATA_3_TANI']?></div>
	                </div>
<?php

								$index++;
	                            }
                            }
                        }
?>
                </div>
                <!--/ メインテーブル-->
                
                <div>取込件数　　<?php if (!empty($checkLst[0][0]['A1'])) { echo $checkLst[0][0]['A1']; }?>　件</div>
                <div>エラー件数　<?php if (!empty($checkLst[0][0]['A2'])) { echo $checkLst[0][0]['A2']; }?>　件</div>

				<!-- CSV出力ボタン-->
				<div style="height:40px;width:100%;text-align:right;max-width:1180px;" id="buttons" >
					<input type="button"  value="CSV出力" style="width:180px;height:30px;" id="csvButton" >
				</div>
				<!--/ CSV出力ボタン-->

            </div>
