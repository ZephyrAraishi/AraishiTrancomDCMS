<?php
echo $this->Html->script(array('effects','window','base64','DCMSMessage','DCMSCommon','DCMSWPO020_RELOAD','DCMSWPO020_POPUP','DCMSWPO020_UPDATE'), array('inline'=>false));
echo $this->Html->css(array('themes/default','themes/alert','themes/alphacube','DCMSWPO'), false, array('inline'=>false));
?>
<div id="wrapper_l">

<!-- トグルボタン -->
<div id="toggle">
		<ul class="toggle-list">
			<li><a><label id="jidoButton" style="width:100px;text-align:center">自動リロード</label></a></li>
			<li><label  class="selected" id="koshinButton"  style="width:100px;text-align:center" >更新</label></li>
		</ul>
		<div id="reloadingMessage" style="display:none;">...データを取得中です。</div>
</div>
<!--/ トグルボタン -->
<div id="reloadingMessage" style="display:none;">...データを取得中です。</div>

<!-- 検索 -->
<div class="box" style="width:1130px;">
	<?php echo $this->Form->create('WPO020Model', array('url' => '/WPO020/update','type'=>'get', 'inputDefaults' => array('label' => false,'div' => false))) ?>
		<table class="cndt">
			<tr>
				<td class="col1">バッチ名</td>
				<td class="col2">
					<?php echo $this->Form->select('batch_nm_cd', $batchs) ?>
                </td>
				<td class="col1">ＳバッチNo</td>
				<td class="col2"><?php echo $this->Form->input('s_batch_no', array('class' => 'han','maxlength' => '4', 'label' => false, 'error' => false,'style' => 'width:80px')) ?></td>
				<td class="col1">集計管理No</td>
				<td class="col2"><?php echo $this->Form->input('s_kanri_no', array('class' => 'han','maxlength' => '9', 'label' => false, 'error' => false,'style' => 'width:80px')) ?></td>
			</tr>
			<tr>
				<td class="col1">ゾーン</td>
				<td class="col2">
					<?php echo $this->Form->select('zone', $zones) ?>
				</td>
				<td class="col1">スタッフ</td>
				<td class="col2"><?php echo $this->Form->input('staff_nm', array('class' => 'zen','maxlength' => '20', 'label' => false, 'error' => false,'style' => 'width:120px')) ?></td>
				<td class="col1">ステータス</td>
				<td class="col2">
					<?php echo $this->Form->select('kbn_status', $status) ?>
				</td>
				<td class="col4"><?php echo $this->Form->submit('検索', array('div' => 'btnsubmit')); ?></td>
			</tr>
		</table>
	<?php echo $this->Form->end() ?>
</div>
<!--/ 検索 -->

<!-- 区切り線 -->
<div class="line"></div>
<!--/ 区切り線 -->

<!-- 更新ボタン-->
<div style="width:100%;text-align:right;"><input type="button" value="更新" style="width:120px;height:30px;" id="updateButton" ></div>
<!--/ 更新ボタン-->

<!-- メインテーブル-->
<div class="resizable_s" style="height:600px;" id="lstTable">
    <div style="display:block;position:relative;height:25px;text-align:left;"><?php print($navi);?></div>
	<div class="tableRowTitle row_ttl_sp_1" style="width:1545px;">
		<div class="tableCell cell_ttl_sp_1" style="width:60px;" ><?php echo Configure::read("Bango") ?></div>
		<div class="tableCell cell_ttl_sp_1" style="width:180px;" >バッチ名</div>
		<div class="tableCell cell_ttl_sp_1" style="width:90px;" >ＳバッチＮＯ</div>
		<div class="tableCell cell_ttl_sp_1" style="width:60px;" >ソーター</div>
		<div class="tableCell cell_ttl_sp_1" style="width:60px;" >ゾーン</div>
		<div class="tableCell cell_ttl_sp_1" style="width:110px;" >集計管理No</div>
		<div class="tableCell cell_ttl_sp_1" style="width:90px;" >ステータス</div>
		<div class="tableCell cell_ttl_sp_1" style="width:70px;" >外箱数</div>
		<div class="tableCell cell_ttl_sp_1" style="width:70px;" >内箱数</div>
		<div class="tableCell cell_ttl_sp_1" style="width:80px;" >アイテム数</div>
		<div class="tableCell cell_ttl_sp_1" style="width:70px;" >ピース数</div>
		<div class="tableCell cell_ttl_sp_1" style="width:100px;" >担当スタッフ</div>
		<div class="tableCell cell_ttl_sp_1" style="width:50px;" >ランク</div>
		<div style="float:left;width:325px;height:76px;">
			<div class="tableCell cell_ttl_1" style="width:315px;">時刻</div>
			<div class="tableCell cell_ttl_2_1" style="width:55px;">未処理</div>
			<div class="tableCell cell_ttl_2_1" style="width:55px;">処理中</div>
			<div class="tableCell cell_ttl_2_2" style="width:55px;">棚決<br>発生</div>
			<div class="tableCell cell_ttl_2_1" style="width:55px;">処理済</div>
			<div class="tableCell cell_ttl_2_2" style="width:55px;">棚欠<br>処理済</div>
		</div>
	</div>
<?php $count = $index + 1; ?>
<?php foreach ($lsts as $key => $value): ?>
	<div class="tableRow row_dat" style="width:1545px;">
			<div class="tableCell cell_dat bango" style="width:60px;"><?php echo $count ?></div>
			<div class="tableCell cell_dat nm" style="width:180px;"><?php echo $value['BATCH_NM'] ?></div>
			<div class="tableCell cell_dat kbn" style="width:90px;"><?php echo $value['S_BATCH_NO'] ?></div>
			<div class="tableCell cell_dat kbn" style="width:60px;"><?php echo $value['SORTER_NM'] ?></div>
			<div class="tableCell cell_dat kbn" style="width:60px;"><?php echo $value['ZONE_NM'] ?></div>
			<div class="tableCell cell_dat kbn" style="width:110px;"><?php echo $value['S_KANRI_NO'] ?></div>
			<div class="tableCell cell_dat kbn" style="width:90px;"><?php
			
			if ($value['KBN_STATUS'] == "11" || $value['KBN_STATUS'] == "19") {
				
				echo "<a href='/DCMS/WPO021?ymd_syori=" . $value['YMD_SYORI'] .
	                                           "&s_batch_no_cd=" . $value['S_BATCH_NO_CD'] .
	                                           "&s_kanri_no=" . $value['S_KANRI_NO'] .
	                                           "' target='_blank'> " . $value['KBN_STATUS_NM'] . "</a>";
				
			} else {
				
				echo $value['KBN_STATUS_NM'];
			}
			
			?></div>
			<div class="tableCell cell_dat cnt" style="width:70px;"><?php echo number_format($value['SURYO_HAKO_SOTO']) ?></div>
			<div class="tableCell cell_dat cnt" style="width:70px;"><?php echo number_format($value['SURYO_HAKO_UCHI']) ?></div>
			<div class="tableCell cell_dat cnt" style="width:80px;"><?php echo number_format($value['SURYO_ITEM']) ?></div>
			<div class="tableCell cell_dat cnt" style="width:70px;"><?php echo number_format($value['SURYO_PIECE']) ?></div>
			<div class="tableCell cell_dat nm" style="width:100px;"><?php echo $value['STAFF_NM'] ?></div>
			<div class="tableCell cell_dat kbn" style="width:50px;"><?php echo $value['RANK'] ?></div>
			<div class="tableCell cell_dat date" style="width:55px;"><?php echo $value['DT_SYORI_MI'] ?></div>
			<div class="tableCell cell_dat date" style="width:55px;"><?php echo $value['DT_SYORI_CYU'] ?></div>
			<div class="tableCell cell_dat date" style="width:55px;"><?php echo $value['DT_T_KETSU'] ?></div>
			<div class="tableCell cell_dat date" style="width:55px;"><?php echo $value['DT_SYORI_SUMI'] ?></div>
			<div class="tableCell cell_dat date" style="width:55px;"><?php echo $value['DT_T_KETSU_SUMI'] ?></div>
			<div class="hiddenData"><?php echo $count ?></div>
			<div class="hiddenData"><?php echo  $value['S_BATCH_NO_CD'] ?></div>
			<div class="hiddenData"><?php echo $value['BATCH_NM'] ?></div>
			<div class="hiddenData"><?php echo  $value['S_KANRI_NO'] ?></div>
			<div class="hiddenData"><?php echo  $value['KBN_STATUS_NM'] ?></div>
			<div class="hiddenData"><?php echo  $value['KBN_STATUS'] ?></div>
			<div class="hiddenData"><?php echo  $value['YMD_SYORI'] ?></div>
			<div class="hiddenData"><?php echo $value['DT_SYORI_SUMI'] ?></div>
			<div class="hiddenData">0</div>
	</div>
<?php $count++; ?>
<?php endforeach; ?>
</div>
<!--/ メインテーブル-->

</div>
<!--/ #wrapper-->
