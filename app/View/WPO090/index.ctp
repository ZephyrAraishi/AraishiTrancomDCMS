<?php
            echo $this->Html->script(array('scriptaculous','window','base64','DCMSCommon','DCMSMessage','DCMSWPO090_UPDATE'), array('inline'=>false));
            echo $this->Html->script(array('scriptaculous','window','base64','DCMSCommon','DCMSMessage','DCMSWPO090_IMPORT','DCMSWPO090_CSV'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','themes/alert','themes/alphacube.css','DCMSWPO'), false, array('inline'=>false));
?>

			<div id="wrapper_s">

               <div id="errors" sytle="width:800px;">
			<span style="font-size:16px;color:red;">
				<?php echo $message ?>
			</span>
		</div>

                <div id="upload" sytle="position:relative;width:800px;">
                            <div style="position:relative;width:500px;margin-top:3px;text-align:left;">OZAX出荷指示データを取込ます</div>
				<div>
				<form id="shukka_form" action="" method="post" enctype="multipart/form-data" accept-charset="utf-8">
					<input id="shukka_file" name="shukka_file" type="file" style="width:400px;" value="" />
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
				<div style="display:<?php if (!empty($shukkaLst)) echo "block"; else echo "none" ?>;width:780px; max-width:790px;" align="right">
					<input type="button" value="取込" id="updateButton" >
				</div>

                <!-- メインテーブル-->
		  <div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>
                <div class="resizable_s" style="height:600px;" id="lstTable">
	                <div class="tableRowTitle row_ttl_1" style="width:5500px;">
						<div class="tableCell cell_ttl_1" style="width:300px;" >チェック内容</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >出荷日</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >納品日</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >出荷取引区分</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >出荷緊急区分</div>
						<div class="tableCell cell_ttl_1" style="width:50px;" >便</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >方面コード</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >方面名</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >部門コード</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >部門名</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >得意先コード</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >得意先名</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >店舗コード</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >店舗名</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >得意先部門名</div>
						<div class="tableCell cell_ttl_1" style="width:50px;" >エリアコード</div>
						<div class="tableCell cell_ttl_1" style="width:50px;" >エリア名</div>
						<div class="tableCell cell_ttl_1" style="width:50px;" >バッチ№</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >出荷順</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >倉庫コード</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >倉庫名</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >代表倉庫コード</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >代表倉庫名</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >ロケーション</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >商品コード</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >商品名</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >規格</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >ロット№</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >在庫区分</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >在庫区分名</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >良品区分</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >良品区分名</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >入荷日</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >製造日</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >賞味期限日</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >SS№</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >SS№行</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >出荷№</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >出荷№行</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >入庫№</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >荷姿１指示数</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >荷姿２指示数</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >荷姿３指示数</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >総バラ数</div>
						<div class="tableCell cell_ttl_1" style="width:50px;" >予１</div>
						<div class="tableCell cell_ttl_1" style="width:50px;" >予２</div>
						<div class="tableCell cell_ttl_1" style="width:50px;" >予３</div>
						<div class="tableCell cell_ttl_1" style="width:50px;" >予４</div>
						<div class="tableCell cell_ttl_1" style="width:50px;" >予５</div>
						<div class="tableCell cell_ttl_1" style="width:50px;" >予６</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >取込スタッフ</div>
						<div class="tableCell cell_ttl_1" style="width:100px;" >取込日時</div>
	                </div>

<?php
                        if (!empty($shukkaLst)) {

                        	$index = 1;

                            foreach($shukkaLst as $line) {
	                            foreach($line as $linedata) {
?>

	                <div class="tableRow row_dat" style="width:5500px;">
		                <div class="tableCell cell_dat bango" style="width:300px;"><?php echo $linedata['CHECK_NAIYO'] ?></div>
		                <div class="tableCell cell_dat date" style="width:100px;"><?php echo $linedata['SHUKKA_BI'] ?></div>
		                <div class="tableCell cell_dat kbn" style="width:100px;"><?php echo $linedata['NOHIN_BI'] ?></div>
		                <div class="tableCell cell_dat cnt" style="width:100px;"><?php echo $linedata['SHUKKA_TORIHIKI_KUBUN'] ?></div>
		                <div class="tableCell cell_dat kin" style="width:100px;"><?php echo $linedata['SHUKKA_KINKYU_KUBUN'] ?></div>
		                <div class="tableCell cell_dat nm" style="width:50px;"><?php echo $linedata['BIN']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['HOMEN_CODE']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['HOMEN_MEI']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['BUMON_CODE']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['BUMON_MEI']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['TOKUISAKI_CODE']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['TOKUISAKI_MEI']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['TENPO_CODE']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['TENPO_MEI']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['TOKUISAKI_BUMON_MEI']?></div>
		                <div class="tableCell cell_dat nm" style="width:50px;"><?php echo $linedata['AREA_CODE']?></div>
		                <div class="tableCell cell_dat nm" style="width:50px;"><?php echo $linedata['AREA_MEI']?></div>
		                <div class="tableCell cell_dat nm" style="width:50px;"><?php echo $linedata['BATCH_NUMBER']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['SHUKKA_JYUN']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['GYOTAI_SOKO_CODE']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['GYOTAI_SOKO_MEI']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['DAIHYO_SOKO_CODE']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['DAIHYO_SOKO_MEI']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['LOCATION']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['SHOHIN_CODE']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['SHOHIN_MEI']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['KIKAKU']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['ROT_NUMBER']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['ZAIKO_KUBUN']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['ZAIKO_KUBUN_MEI']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['RYOHIN_KUBUN']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['RYOHIN_KUBUN_MEI']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['NYUKA_BI']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['SEIZO_BI']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['SYOMI_KIGEN']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['SS_NUMBER']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['SS_NUMBER_LINE']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['SHUKKA_NUMBER']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['SHUKKA_NUMBER_LINE']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['NYUKO_NUMBER']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['NISUGATA_1_SHIJISU']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['NISUGATA_2_SHIJISU']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['NISUGATA_3_SHIJISU']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['SO_BARA_SU']?></div>
		                <div class="tableCell cell_dat nm" style="width:50px;"><?php echo $linedata['YOBI_1']?></div>
		                <div class="tableCell cell_dat nm" style="width:50px;"><?php echo $linedata['YOBI_2']?></div>
		                <div class="tableCell cell_dat nm" style="width:50px;"><?php echo $linedata['YOBI_3']?></div>
		                <div class="tableCell cell_dat nm" style="width:50px;"><?php echo $linedata['YOBI_4']?></div>
		                <div class="tableCell cell_dat nm" style="width:50px;"><?php echo $linedata['YOBI_5']?></div>
		                <div class="tableCell cell_dat nm" style="width:50px;"><?php echo $linedata['YOBI_6']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['TORIKOMI_STAFF']?></div>
		                <div class="tableCell cell_dat nm" style="width:100px;"><?php echo $linedata['TORIKOMI_NICHIJI']?></div>
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
				<!--/ CXSV出力ボタン-->

            </div>
