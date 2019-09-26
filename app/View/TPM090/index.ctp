<?php
			echo $this->Html->script(array('effects','window','protocalendar','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSTPM090_RELOAD', 'DCMSTPM090_CALENDAR',
					 'DCMSTPM090_CSV'), array('inline'=>false));
            echo $this->Html->css(array('themes/default','kotei_dropdown_menu','themes/alert','themes/alphacube','calendar','DCMSTPM'), false, array('inline'=>false));
?>
<!-- WRAPPER -->
<div id="wrapper_s">

<!-- 検索 -->
<div class="box" style="width:850px;">
	<?php echo $this->Form->create('TPM090Model', array('url' => '/TPM090','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
		<table class="cndt" style="width:840px">
			<tbody>
			<colgroup>
				<col width="150px">
				<col width="100px">
				<col width="30px">
				<col width="100px">
				<col width="200px">
				<col width="100px">
				<col width="30px">
				<col width="100px">
				<col width="30px">
			</colgroup>
<!-- １段目 -->
			<tr>
				<td>納品日</td>
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
				<td style="display:table-cell;width:10px;padding-right:2px;padding-left:2px;text-align:center;font-size:13px;color:#222">～</td>
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
    			<td>ＴＣ倉庫名</td>
    			<td><input id="homen" name="homen" class="zen" type="text" value="" autocomplete="on" list="TC"></td>
				<datalist id="TC">
						<option value=""></option>
						<?php
						foreach($homen_mei as $data) :
						?>
						<option value="<?php echo $data['HOMEN_MEI'] ?>"><?php echo $data['HOMEN_MEI']?></option>
						<?php
						endforeach;
						?>
				</datalist>    			
    			<td></td>
    			<td></td>
			</tr>
<!-- ２段目 -->
			<tr>
				<td>店舗名　</td>
	   			<td><input id="tenpo" name="tenpo" class="zen" type="text" value=""></td>
				<td></td>
				<td></td>
				<td></td>
				<td>得意先名　</td>
	   			<td><input id="tokuisaki" name="tokuisaki" class="zen" type="text" value=""></td>
				<td></td>
				<td></td>
			</tr>
<!-- ３段目 -->
			<tr>
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
				<td></td>
				<td></td>
				<td></td>
				<td><input value="検索" type="submit";div="btnsubmit"></td>
				<td></td>
				<td></td>
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
<div style="height:40px;width:100%;text-align:right;max-width:1180px;" id="buttons" >
	<input type="button"  value="CSV出力" style="width:180px;height:30px;" id="csvButton" >
</div>
<!--/ CSV出力ボタン-->

<!-- メインテーブル-->
	<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi)) print($navi);?></div>
<!-- メインテーブルヘッダ-->
<div class="resizable_s" id="lstTable">
	<div class="tableRowTitle row_ttl_1" style="width:1900px;">
		<div class="tableCell cell_ttl_1" style="width:60px;" ><?php echo Configure::read("Bango") ?></div>
		<div class="tableCell cell_ttl_1" style="width:60px;" >積込区分</div>
		<div class="tableCell cell_ttl_1" style="width:100px;" >納品日</div>
		<div class="tableCell cell_ttl_1" style="width:120px;" >方面名</div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >得意先CD</div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >店舗CD</div>
		<div class="tableCell cell_ttl_1" style="width:200px;" >店舗名</div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >総梱包数</div>
		<div class="tableCell cell_ttl_1" style="width:30px;" ></div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >積込梱包数</div>
		<div class="tableCell cell_ttl_1" style="width:30px;" ></div>
		<div class="tableCell cell_ttl_1" style="width:80px;" >残梱包数</div>
		<div class="tableCell cell_ttl_1" style="width:30px;" ></div>
		<div class="tableCell cell_ttl_1" style="width:150px;" >開始時間</div>
		<div class="tableCell cell_ttl_1" style="width:150px;" >終了時間</div>
		<div class="tableCell cell_ttl_1" style="width:200px;" >得意先名</div>
	</div>

<!-- メインテーブル明細-->
<?php  $count = 1; ?>
<?php  foreach ($lsts as $array): ?>
	<div class="tableRow row_dat" style="width:1900px;">
	    <div class="tableCell cell_dat bango" style="width:60px;" ><?php echo $count ?></div>
		<div class="tableCell cell_dat" style="width:60px;" ><?php if (!empty($array['TENPO_SIWAKE_KUBUN_MEI'])) {  echo $array['TENPO_SIWAKE_KUBUN_MEI']; } ?></div>
		<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['NOHIN_BI'])) {  echo $array['NOHIN_BI']; } ?></div>
		<div class="tableCell cell_dat" style="width:120px;" ><?php if (!empty($array['HOMEN_MEI'])) {  echo $array['HOMEN_MEI']; } ?></div>
		<div class="tableCell cell_dat" style="width:80px;" ><?php if (!empty($array['TOKUISAKI_CODE'])) {  echo $array['TOKUISAKI_CODE']; } ?></div>
		<div class="tableCell cell_dat" style="width:80px;" ><?php if (!empty($array['TENPO_CODE'])) {  echo $array['TENPO_CODE']; } ?></div>
		<div class="tableCell cell_dat" style="width:200px;" ><?php if (!empty($array['TENPO_MEI'])) {  echo $array['TENPO_MEI']; } ?></div>
		<div class="tableCell cell_dat" style="width:80px;" ><?php if (!empty($array['KONPO_SU'])) {  echo $array['KONPO_SU']; } ?></div>
		<div class="tableCell cell_dat" style="width:30px;" >個</div>
		<div class="tableCell cell_dat" style="width:80px;" ><?php if (!empty($array['SUMI_KONPO_SU'])) {  echo $array['SUMI_KONPO_SU']; } ?></div>
		<div class="tableCell cell_dat" style="width:30px;" >個</div>
		<div class="tableCell cell_dat" style="width:80px;" ><?php if (!empty($array['ZAN_KONPO_SU'])) { echo $array['ZAN_KONPO_SU']; } ?></div>
		<div class="tableCell cell_dat" style="width:30px;" >個</div>
		<div class="tableCell cell_dat" style="width:150px;" ><?php if (!empty($array['KAISI_JIKAN'])) {  echo $array['KAISI_JIKAN']; } ?></div>
		<div class="tableCell cell_dat" style="width:150px;" ><?php if (!empty($array['SYURYO_JIKAN'])) {  echo $array['SYURYO_JIKAN']; } ?></div>
		<div class="tableCell cell_dat" style="width:200px;" ><?php if (!empty($array['TOKUISAKI_MEI'])) {  echo $array['TOKUISAKI_MEI']; } ?></div>
	</div>
<?php  $count++; ?>
<?php  endforeach; ?>
</div>
<div id="timestamp" style="display:none;" ><?php  echo $timestamp; ?></div>
<div id="YMD_FROM_H" style="display:none;" ><?php  echo $ymd_from; ?></div>
<div id="YMD_TO_H" style="display:none;" ><?php  echo $ymd_to; ?></div>
<div id="TOKUISAKI" style="display:none;" ><?php  if(!empty($tokuisaki)){ echo $tokuisaki; }?></div>
<div id="HOMEN" style="display:none;" ><?php  if(!empty($homen)){ echo $homen; }?></div>
<div id="TENPO" style="display:none;" ><?php  if(!empty($tenpo)){ echo $tenpo; }?></div>
<!--/ メインテーブル-->

<!-- サマリーテーブル明細-->
<div class="line" style="height:40px;width:100%;text-align:right;max-width:1180px;"></div>
	<div class="box" style="width:100%;text-align:right;max-width:1140px;">
		<table class="cndt" style="width:1100px" border=1>
			<tbody>
				<colgroup>
					<col width="70px">
					<col width="3px">
					<col width="70px">
					<col width="50px">
					<col width="10px">
					<col width="70px">
					<col width="3px">
					<col width="70px">
					<col width="50px">
					<col width="10px">
					<col width="70px">
					<col width="3px">
					<col width="70px">
					<col width="50px">
					<col width="10px">
				</colgroup>
				<?php  $count = 1; ?>
				<?php  foreach ($sumlst as $array): ?>
						<!--/ １段目-->
						<?php
						 if( $count==1) {
						 ?>
							<tr>
							<td>指示方面</td>
							<td></td>
							<td><?php if (!empty($array['HOMEN_SU'])) { echo $array['HOMEN_SU']; } ?></td>
							<td>方面</td>
							<td></td>
							<td>指示店舗</td>
							<td></td>
							<td><?php if (!empty($array['TENPO_SU'])) { echo $array['TENPO_SU']; } ?></td>
							<td>店</td>
							<td></td>
							<td>指示梱包</td>
							<td></td>
							<td><?php if (!empty($array['KONPO_SU'])) { echo $array['KONPO_SU']; } ?></td>
							<td>個</td>
							<td></td>
							</tr>
						<?php
						 }
						 ?>
						<!--/ ２段目-->
						<?php
						 if( $count==2) {
						 ?>
							<tr>
							<td>済OR途中方面</td>
							<td></td>
							<td><?php if (!empty($array['HOMEN_SU'])) { echo $array['HOMEN_SU']; } ?></td>
							<td>方面</td>
							<td></td>
							<td>済OR途中店舗</td>
							<td></td>
							<td><?php if (!empty($array['TENPO_SU'])) { echo $array['TENPO_SU']; } ?></td>
							<td>店</td>
							<td></td>
							<td>完了梱包数</td>
							<td></td>
							<td><?php if (!empty($array['KONPO_SU'])) { echo $array['KONPO_SU']; } ?></td>
							<td>個</td>
							<td></td>
							</tr>
						<?php
						 }
						 ?>
						<!--/ ３段目-->
						<?php
						 if( $count==3) {
						 ?>
							<tr>
							<td>残OR途中方面</td>
							<td></td>
							<td><?php if (!empty($array['HOMEN_SU'])) { echo $array['HOMEN_SU']; } ?></td>
							<td>方面</td>
							<td></td>
							<td>残OR途中店舗</td>
							<td></td>
							<td><?php if (!empty($array['TENPO_SU'])) { echo $array['TENPO_SU']; } ?></td>
							<td>店</td>
							<td></td>
							<td>残梱包数</td>
							<td></td>
							<td><?php if (!empty($array['KONPO_SU'])) { echo $array['KONPO_SU']; } ?></td>
							<td>個</td>
							<td></td>
							</tr>
						<?php
						 }
						 ?>
			<?php  $count++; ?>
			<?php  endforeach; ?>
			</tdbody>
		</table>
	</div>

	<!-- /残データテーブル明細-->
	<!--/ 区切り線 -->
	<div class="line"></div>
	<!--/ 区切り線 -->
	<div>【残ラベル一覧】</div>
	<!-- 残データテーブル-->
		<div class="pagaerNavi" style="text-align:left;" ><?php if (!empty($navi2)) print($navi2);?></div>
	<!-- 残データテーブルヘッダ-->
	<div class="resizable_s" id="lstTable">
		<div class="tableRowTitle row_ttl_1" style="width:1400px;">
			<div class="tableCell cell_ttl_1" style="width:60px;" ><?php echo Configure::read("Bango") ?></div>
			<div class="tableCell cell_ttl_1" style="width:100px;" >納品日</div>
			<div class="tableCell cell_ttl_1" style="width:200px;" >方面名</div>
			<div class="tableCell cell_ttl_1" style="width:200px;" >店舗名</div>
			<div class="tableCell cell_ttl_1" style="width:150px;" >ケース</div>
			<div class="tableCell cell_ttl_1" style="width:150px;" >バラ</div>
			<div class="tableCell cell_ttl_1" style="width:250px;" >商品名</div>
		</div>

	<!-- 残データテーブル明細-->
	<?php  $count = 1; ?>
	<?php  foreach ($zanlsts as $array): ?>
		<div class="tableRow row_dat" style="width:1400px;">
		    <div class="tableCell cell_dat bango" style="width:60px;" ><?php echo $count ?></div>
			<div class="tableCell cell_dat" style="width:100px;" ><?php if (!empty($array['NOHIN_BI'])) {  echo $array['NOHIN_BI']; } ?></div>
			<div class="tableCell cell_dat" style="width:200px;" ><?php if (!empty($array['HOMEN_MEI'])) {  echo $array['HOMEN_MEI']; } ?></div>
			<div class="tableCell cell_dat" style="width:200px;" ><?php if (!empty($array['TENPO_MEI'])) {  echo $array['TENPO_MEI']; } ?></div>
			<div class="tableCell cell_dat" style="width:150px;" ><?php if (!empty($array['CASE_BARCODE'])) {  echo $array['CASE_BARCODE']; } ?></div>
			<div class="tableCell cell_dat" style="width:150px;" ><?php if (!empty($array['BARA_BARCODE'])) {  echo $array['BARA_BARCODE']; } ?></div>
			<div class="tableCell cell_dat" style="width:250px;" ><?php if (!empty($array['SHOHIN'])) {  echo $array['SHOHIN']; } ?></div>
		</div>
	<?php  $count++; ?>
	<?php  endforeach; ?>
	</div>
</div>
<!--/ #wrapper-->
