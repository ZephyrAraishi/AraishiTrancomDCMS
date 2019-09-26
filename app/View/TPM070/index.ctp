<?php
			echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSTPM070_RELOAD', 'DCMSTPM070_CALENDAR',
					 'DCMSTPM070_CSV'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSTPM'), false, array('inline'=>false));
?>
<!-- WRAPPER -->
<div id="wrapper_s">

<!-- 検索 -->
<div class="box" style="width:1250px;">
	<?php echo $this->Form->create('TPM070Model', array('url' => '/TPM070','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
		<table class="cndt" style="width:1250px">
			<tbody>
			<colgroup>
				<col width="150px">
				<col width="100px">
				<col width="50px">
				<col width="100px">
				<col width="100px">
				<col width="100px">
				<col width="100px">
				<col width="50px">
				<col width="100px">
				<col width="100px">
			</colgroup>
<!-- １段目 -->
			<tr>
				<td>出荷日</td>
				<td>
						<?php echo $this->Form->input('ymd_from',
												array('maxlength' => '40',
													'label' => false,
													'error' => false,
													'style' => 'width:100px',
													'name' => 'ymd_from',
													'id' => 'ymd_from',
													'value' => $ymd_from,
													'class' => 'zen')) ?>
				</td>
				<td>～</td>
				<td>
						<?php echo $this->Form->input('ymd_to',
												array('maxlength' => '40',
													'label' => false,
													'error' => false,
													'style' => 'width:100px',
													'name' => 'ymd_to',
													'value' => $ymd_to,
													'id' => 'ymd_to',
													'class' => 'zen')) ?>
				</td>
				<td></td>
    			<td>バッチ№</td>
    			<td><input id="BatchNo_from" name="BatchNo_from" class="zen" type="text" value=""></td>
    			<td>～</td>
    			<td><input id="BatchNo_to" name="BatchNo_to" class="zen" type="text" value=""></td>
			</tr>
<!-- ２段目 -->
			<td>得意先名</td>
   			<td><input id="Tokuisaki" name="Tokuisaki" class="zen" type="text" value=""></td>
			<td></td>
			<td></td>
			<td></td>
   			<td>方面名　</td>
   			<td><input id="homen" name="homen" class="zen" type="text" value=""></td>
			<td></td>
			</tr>
<!-- ３段目 -->
			<tr>
				<td>賞味期限日変更　<input type="checkbox" name="shomikigen_henko" id="shomikigen_henko" /></td>
				<td></td>
				<td>進捗</td>
				<td class="colBnriCd" style="text-align:left;">
					<div>
					<select name="Sinchoku"  id="sinchokukubunCombo" style="width:120px">
						<option value=""></option>
						<?php
						foreach($kbn_sinchoku as $data) :
						?>
						<option value="<?php echo $data['MEI_CD'] ?>"><?php echo $data['MEI_1']?></option>
						<?php
						endforeach;
						?>
					</select>
				</div>
				</td>
				<td></td>
			<td>商品コード/名</td>
   			<td><input id="Shohin" name="Shohin" class="zen" type="text" value=""></td>
			<td></td>
			<td><input value="検索" type="submit";div="btnsubmit"></td>
			</tr>
			</tbody>
		</table>
	<?php echo $this->Form->end() ?>
</div>
<!--/ 検索 -->

<!--/ 区切り線 -->
<div class="line"></div>
<!--/ 区切り線 -->

<!-- CSV出力ボタン-->
<div style="height:40px;width:100%;text-align:right;max-width:1250px;" id="buttons" >
	<input type="button"  value="CSV出力" style="width:180px;height:30px;" id="csvButton" >
</div>
<!--/ CXSV出力ボタン-->

<!-- メインテーブル-->
	<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>
<!-- メインテーブルヘッダ-->
<div class="resizable_s" id="lstTable">
	<div class="tableRowTitle row_ttl_1" style="width:4200px;">
		<div class="tableCell cell_ttl_1" style="width:60px;" ><?php echo Configure::read("Bango") ?></div>
		<div class="tableCell cell_ttl_1" style="width:60px;" >PK区分</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >出荷日</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >方面</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >バッチ№</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >商品CD</div>
		<div class="tableCell cell_ttl_1" style="width:300px;" >商品名</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >期限日</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >変更期限日</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >ロケーション</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >開始時間</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >終了時間</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >荷１指</div>
		<div class="tableCell cell_ttl_1" style="width:40px;" ></div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >荷２指</div>
		<div class="tableCell cell_ttl_1" style="width:40px;" ></div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >荷３指</div>
		<div class="tableCell cell_ttl_1" style="width:40px;" ></div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >総バラ指</div>
		<div class="tableCell cell_ttl_1" style="width:40px;" ></div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >荷１PK</div>
		<div class="tableCell cell_ttl_1" style="width:40px;" ></div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >荷２PK</div>
		<div class="tableCell cell_ttl_1" style="width:40px;" ></div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >荷３PK</div>
		<div class="tableCell cell_ttl_1" style="width:40px;" ></div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >総バラPK</div>
		<div class="tableCell cell_ttl_1" style="width:40px;" ></div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >荷１残</div>
		<div class="tableCell cell_ttl_1" style="width:40px;" ></div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >荷２残</div>
		<div class="tableCell cell_ttl_1" style="width:40px;" ></div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >荷３残</div>
		<div class="tableCell cell_ttl_1" style="width:40px;" ></div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >総バラ残</div>
		<div class="tableCell cell_ttl_1" style="width:40px;" ></div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >トータルPK№</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >賞味期限管理</div>
		<div class="tableCell cell_ttl_1" style="width:300px;" >得意先名</div>
		<div class="tableCell cell_ttl_1" style="width:300px;" >作業者</div>
	</div>

<!-- メインテーブル明細-->
<?php  $count = 1; ?>
<?php  foreach ($lsts as $array): ?>
	<div class="tableRow row_dat" style="width:4200px;">
	    <div class="tableCell cell_dat bango" style="width:60px;" ><?php echo $count ?></div>
		<div class="tableCell cell_dat" style="width:60px;" ><?php if (!empty($array['TOTAL_PK_KUBUN_MEI'])) {  echo $array['TOTAL_PK_KUBUN_MEI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['SHUKKA_BI'])) {  echo $array['SHUKKA_BI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['HOMEN_MEI'])) {  echo $array['HOMEN_MEI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['BATCH_NUMBER'])) {  echo $array['BATCH_NUMBER']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['SHOHIN_CODE'])) {  echo $array['SHOHIN_CODE']; } ?></div>
		<div class="tableCell cell_dat" style="width:300px;" ><?php if (!empty($array['SHOHIN_MEI'])) {  echo $array['SHOHIN_MEI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['SYOMI_KIGEN']) ){ echo $array['SYOMI_KIGEN']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['HENKO_SYOMI_KIGEN'])) { echo $array['HENKO_SYOMI_KIGEN']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['LOCATION_CODE'])) { echo $array['LOCATION_CODE']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['KAISI_JIKAN'])) {  echo $array['KAISI_JIKAN']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['SYURYO_JIKAN'])) {  echo $array['SYURYO_JIKAN']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['NISUGATA_1_SHIJISU'])) {  echo $array['NISUGATA_1_SHIJISU']; } ?></div>
		<div class="tableCell cell_dat" style="width:40px;" ><?php if (!empty($array['NISUGATA_1_TANI'])) {  echo $array['NISUGATA_1_TANI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['NISUGATA_2_SHIJISU'])) { echo $array['NISUGATA_2_SHIJISU']; } ?></div>
		<div class="tableCell cell_dat" style="width:40px;" ><?php if (!empty($array['NISUGATA_2_TANI']))  {  echo $array['NISUGATA_2_TANI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['NISUGATA_3_SHIJISU'])) { echo $array['NISUGATA_3_SHIJISU']; } ?></div>
		<div class="tableCell cell_dat" style="width:40px;" ><?php if (!empty($array['NISUGATA_3_TANI'])) { echo $array['NISUGATA_3_TANI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['SO_BARA_SU'])) { echo $array['SO_BARA_SU']; } ?></div>
		<div class="tableCell cell_dat" style="width:40px;" ><?php if (!empty($array['NISUGATA_3_TANI'])) {  echo $array['NISUGATA_3_TANI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['NISUGATA_1_SUMISU'])) { echo $array['NISUGATA_1_SUMISU']; } ?></div>
		<div class="tableCell cell_dat" style="width:40px;" ><?php if (!empty($array['NISUGATA_1_TANI'])) { echo $array['NISUGATA_1_TANI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['NISUGATA_2_SUMISU'])) {  echo $array['NISUGATA_2_SUMISU']; } ?></div>
		<div class="tableCell cell_dat" style="width:40px;" ><?php if (!empty($array['NISUGATA_2_TANI'])) { echo $array['NISUGATA_2_TANI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['NISUGATA_3_SUMISU'])) {  echo $array['NISUGATA_3_SUMISU']; } ?></div>
		<div class="tableCell cell_dat" style="width:40px;" ><?php if (!empty($array['NISUGATA_3_TANI'])) {  echo $array['NISUGATA_3_TANI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['SO_BARA_SUMISU'])) {  echo $array['SO_BARA_SUMISU']; } ?></div>
		<div class="tableCell cell_dat" style="width:40px;" ><?php if (!empty($array['NISUGATA_3_TANI'])) { echo $array['NISUGATA_3_TANI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['NISUGATA_1_ZANSU'])) {  echo $array['NISUGATA_1_ZANSU']; } ?></div>
		<div class="tableCell cell_dat" style="width:40px;" ><?php if (!empty($array['NISUGATA_1_TANI'])) { echo $array['NISUGATA_1_TANI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['NISUGATA_2_ZANSU'])) { echo $array['NISUGATA_2_ZANSU']; } ?></div>
		<div class="tableCell cell_dat" style="width:40px;" ><?php if (!empty($array['NISUGATA_2_TANI'])) {  echo $array['NISUGATA_2_TANI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['NISUGATA_3_ZANSU'])) {  echo $array['NISUGATA_3_ZANSU']; } ?></div>
		<div class="tableCell cell_dat" style="width:40px;" ><?php if (!empty($array['NISUGATA_3_TANI'])) {  echo $array['NISUGATA_3_TANI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['SO_BARA_ZANSU'])) {  echo $array['SO_BARA_ZANSU']; } ?></div>
		<div class="tableCell cell_dat" style="width:40px;" ><?php if (!empty($array['NISUGATA_3_TANI'])) { echo $array['NISUGATA_3_TANI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['TOTAL_PK_NUMBER'])) {  echo $array['TOTAL_PK_NUMBER']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['SHOMI_KIGEN_KUBUN'])) {  echo $array['SHOMI_KIGEN_KUBUN']; } ?></div>
		<div class="tableCell cell_dat" style="width:300px;" ><?php if (!empty($array['TOKUISAKI_MEI'])) { echo $array['TOKUISAKI_MEI']; } ?></div>
		<div class="tableCell cell_dat" style="width:300px;" ><?php if (!empty($array['STAFF_NM'])) { echo $array['STAFF_NM']; } ?></div>
	</div>
<?php  $count++; ?>
<?php  endforeach; ?>
</div>
<div id="timestamp" style="display:none;" ><?php  echo $timestamp; ?></div>
<div id="YMD_FROM_H" style="display:none;" ><?php  echo $ymd_from; ?></div>
<div id="YMD_TO_H" style="display:none;" ><?php  echo $ymd_to; ?></div>
<div id="BATCHNO_FROM_H" style="display:none;" ><?php  if(!empty($BatchNo_from)){echo $BatchNo_from;} ?></div>
<div id="BATCHNO_TO_H" style="display:none;" ><?php  if(!empty($BatchNo_to)){echo $BatchNo_to;} ?></div>
<div id="TOKUISAKI" style="display:none;" ><?php  if(!empty($Tokuisaki)){ echo $Tokuisaki; }?></div>
<div id="HOMEN" style="display:none;" ><?php  if(!empty($homen)){ echo $homen; }?></div>
<div id="SHOMIKIGEN_HENKO" style="display:none;" ><?php  if(!empty($shomikigen_henko)){ echo $shomikigen_henko; }?></div>
<div id="SHOHIN" style="display:none;" ><?php  if(!empty($Shohin)){ echo $Shohin; }?></div>
<!--/ メインテーブル-->

<!-- サマリーテーブル明細-->
<div class="line" style="height:40px;width:100%;text-align:right;max-width:1600px;"></div>
【ケースサマリーデータ】
	<div class="box" style="width:100%;text-align:right;max-width:1600px;">
		<table id="SummaryList" class="cndt" style="width:1170px" border="1">
			<tbody>
				<colgroup>
					<col width="100px">
					<col width="40px">
					<col width="50px">
					<col width="25px">
					<col width="100px">
					<col width="40px">
					<col width="50px">
					<col width="25px">
					<col width="100px">
					<col width="40px">
					<col width="50px">
					<col width="25px">
					<col width="100px">
					<col width="40px">
					<col width="50px">
					<col width="25px">
					<col width="100px">
					<col width="40px">
					<col width="50px">
					<col width="25px">
					<col width="100px">
					<col width="40px">
					<col width="50px">
					<col width="25px">
					<col width="100px">
					<col width="40px">
					<col width="50px">
				</colgroup>
				<?php  $count = 1; ?>
				<?php  foreach ($sumlst as $array): ?>
						<!--/ １段目-->
						<?php
						 if( $count==1) {
						 ?>
							<tr id="SummaryList">
							<td id="SummaryList">指示合計</td>
							<td id="SummaryList"><?php if (!empty($array['GOKEI_SU'])) { echo number_format($array['GOKEI_SU']); } ?></td>
							<td id="SummaryList">数</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">指示方面</td>
							<td id="SummaryList"><?php if (!empty($array['HOMEN_SU'])) { echo number_format($array['HOMEN_SU']); } ?></td>
							<td id="SummaryList">方面</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">指示明細</td>
							<td id="SummaryList"><?php if (!empty($array['ITEM_SU'])) { echo number_format($array['ITEM_SU']); } ?></td>
							<td id="SummaryList">明細</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">指示荷１</td>
							<td id="SummaryList"><?php if (!empty($array['SO_1_SU'])) { echo number_format($array['SO_1_SU']); } ?></td>
							<td id="SummaryList">数</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">指示荷２</td>
							<td id="SummaryList"><?php if (!empty($array['SO_2_SU'])) { echo number_format($array['SO_2_SU']); } ?></td>
							<td id="SummaryList">数</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">指示荷３</td>
							<td id="SummaryList"><?php if (!empty($array['SO_3_SU'])) { echo number_format($array['SO_3_SU']); } ?></td>
							<td id="SummaryList">数</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">指示バラ</td>
							<td id="SummaryList"><?php if (!empty($array['SO_BARA_SU'])) { echo number_format($array['SO_BARA_SU']); } ?></td>
							<td id="SummaryList">Pcs</td>
							</tr>
						<?php
						 }
						 ?>
						<!--/ ２段目-->
						<?php
						 if( $count==2) {
						 ?>
							<tr>
							<td id="SummaryList">済合計</td>
							<td id="SummaryList"><?php if (!empty($array['GOKEI_SU'])) { echo number_format($array['GOKEI_SU']); } ?></td>
							<td id="SummaryList">数</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">済OR途中</td>
							<td id="SummaryList"><?php if (!empty($array['HOMEN_SU'])) { echo number_format($array['HOMEN_SU']); } ?></td>
							<td id="SummaryList">方面</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">済明細</td>
							<td id="SummaryList"><?php if (!empty($array['ITEM_SU'])) { echo number_format($array['ITEM_SU']); } ?></td>
							<td id="SummaryList">明細</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">済荷１</td>
							<td id="SummaryList"><?php if (!empty($array['SO_1_SU'])) { echo number_format($array['SO_1_SU']); } ?></td>
							<td id="SummaryList">数</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">済荷２</td>
							<td id="SummaryList"><?php if (!empty($array['SO_1_SU'])) { echo number_format($array['SO_2_SU']); } ?></td>
							<td id="SummaryList">数</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">済荷３</td>
							<td id="SummaryList"><?php if (!empty($array['SO_1_SU'])) { echo number_format($array['SO_3_SU']); } ?></td>
							<td id="SummaryList">数</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">済バラ</td>
							<td id="SummaryList"><?php if (!empty($array['SO_1_SU'])) { echo number_format($array['SO_BARA_SU']); } ?></td>
							<td id="SummaryList">Pcs</td>
							</tr>
						<?php
						 }
						 ?>
						<!--/ ３段目-->
						<?php
						 if( $count==3) {
						 ?>
							<tr>
							<td id="SummaryList">残合計</td>
							<td id="SummaryList"><?php if (!empty($array['GOKEI_SU'])) { echo number_format($array['GOKEI_SU']); } ?></td>
							<td id="SummaryList">数</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">残OR途中
							</td>
							<td id="SummaryList"><?php if (!empty($array['HOMEN_SU'])) { echo number_format($array['HOMEN_SU']); } ?></td>
							<td id="SummaryList">方面</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">残明細</td>
							<td id="SummaryList"><?php if (!empty($array['ITEM_SU'])) { echo number_format($array['ITEM_SU']); } ?></td>
							<td id="SummaryList">明細</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">残荷１</td>
							<td id="SummaryList"><?php if (!empty($array['SO_1_SU'])) { echo number_format($array['SO_1_SU']); } ?></td>
							<td id="SummaryList">数</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">残荷２</td>
							<td id="SummaryList"><?php if (!empty($array['SO_2_SU'])) { echo number_format($array['SO_2_SU']); } ?></td>
							<td id="SummaryList">数</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">残荷３</td>
							<td id="SummaryList"><?php if (!empty($array['SO_3_SU'])) { echo number_format($array['SO_3_SU']); } ?></td>
							<td id="SummaryList">数</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">残バラ</td>
							<td id="SummaryList"><?php if (!empty($array['SO_BARA_SU'])) { echo number_format($array['SO_BARA_SU']); } ?></td>
							<td id="SummaryList">Pcs</td>
							</tr>
						<?php
						 }
						 ?>
			<?php  $count++; ?>
			<?php  endforeach; ?>
			</tdbody>
		</table>
	</div>
	【バラサマリーデータ】
	<div class="box" style="width:100%;text-align:right;max-width:1600px;">
		<table id="SummaryList" class="cndt" style="width:800px" border="1">
			<tbody>
				<colgroup>
					<col width="100px">
					<col width="40px">
					<col width="50px">
					<col width="25px">
					<col width="100px">
					<col width="40px">
					<col width="50px">
					<col width="25px">
					<col width="100px">
					<col width="40px">
					<col width="50px">
					<col width="25px">
					<col width="100px">
					<col width="40px">
					<col width="50px">
				</colgroup>
				<?php  $count = 1; ?>
				<?php  foreach ($Bsumlst as $array): ?>
						<!--/ １段目-->
						<?php
						 if( $count==1) {
						 ?>
							<tr>
							<td id="SummaryList">指示合計</td>
							<td id="SummaryList"><?php if (!empty($array['GOKEI_SU'])) { echo number_format($array['GOKEI_SU']); } ?></td>
							<td id="SummaryList">数</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">指示方面</td>
							<td id="SummaryList"><?php if (!empty($array['HOMEN_SU'])) { echo number_format($array['HOMEN_SU']); } ?></td>
							<td id="SummaryList">方面</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">指示明細</td>
							<td id="SummaryList"><?php if (!empty($array['ITEM_SU'])) { echo number_format($array['ITEM_SU']); } ?></td>
							<td id="SummaryList">明細</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">指示バラ</td>
							<td id="SummaryList"><?php if (!empty($array['SO_BARA_SU'])) { echo number_format($array['SO_BARA_SU']); } ?></td>
							<td id="SummaryList">PCS</td>
							</tr>
						<?php
						 }
						 ?>
						<!--/ ２段目-->
						<?php
						 if( $count==2) {
						 ?>
							<tr>
							<td id="SummaryList">済合計</td>
							<td id="SummaryList"><?php if (!empty($array['GOKEI_SU'])) { echo number_format($array['GOKEI_SU']); } ?></td>
							<td id="SummaryList">数</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">済OR途中</td>
							<td id="SummaryList"><?php if (!empty($array['HOMEN_SU'])) { echo number_format($array['HOMEN_SU']); } ?></td>
							<td id="SummaryList">方面</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">済明細</td>
							<td id="SummaryList"><?php if (!empty($array['ITEM_SU'])) { echo number_format($array['ITEM_SU']); } ?></td>
							<td id="SummaryList">明細</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">済バラ</td>
							<td id="SummaryList"><?php if (!empty($array['SO_1_SU'])) { echo number_format($array['SO_BARA_SU']); } ?></td>
							<td id="SummaryList">PCS</td>
							</tr>
						<?php
						 }
						 ?>
						<!--/ ３段目-->
						<?php
						 if( $count==3) {
						 ?>
							<tr>
							<td id="SummaryList">残合計</td>
							<td id="SummaryList"><?php if (!empty($array['GOKEI_SU'])) { echo number_format($array['GOKEI_SU']); } ?></td>
							<td id="SummaryList">数</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">残OR途中
							</td>
							<td id="SummaryList"><?php if (!empty($array['HOMEN_SU'])) { echo number_format($array['HOMEN_SU']); } ?></td>
							<td id="SummaryList">方面</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">残明細</td>
							<td id="SummaryList"><?php if (!empty($array['ITEM_SU'])) { echo number_format($array['ITEM_SU']); } ?></td>
							<td id="SummaryList">明細</td>
							<td id="SummaryList"></td>
							<td id="SummaryList">残バラ</td>
							<td id="SummaryList"><?php if (!empty($array['SO_BARA_SU'])) { echo number_format($array['SO_BARA_SU']); } ?></td>
							<td id="SummaryList">Pcs</td>
							</tr>
						<?php
						 }
						 ?>
			<?php  $count++; ?>
			<?php  endforeach; ?>
			</tdbody>
		</table>
	</div>
</div>
<!-- /サマリーテーブル明細-->
<!--/ #wrapper-->
