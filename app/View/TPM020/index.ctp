<?php
	echo $this->Html->script(array('base64','DCMSCommon', 'DCMSTPM020_RELOAD'), array('inline'=>false));
	echo $this->Html->css(array('DCMSTPM'), false, array('inline'=>false));
?>

<!-- WRAPPER -->
<div id="wrapper_l" style="width:95%; max-width:1500px;">

<!-- トグルボタン -->
<div id="toggle">
	<form name="form1" method="post" action="">
		<ul class="toggle-list">
			<li><label class="selected" id="reloadButton" style="width:100px;cursor:default;text-align:center;" >自動リロード</label></li>
		</ul>
	</form>
</div>


<!-- タイトル -->
<div class="lineTitle" >【棚欠品】</div>
<div class="line tableBlock"></div>
<!-- テーブル五段目 -->
<div class="tableBlock" >

	<div class="resizable" style="height:300px;">
		<div class="tanaketsuLst">
			<div class="tableRowTitle row_ttl_1" style="width:630px;">
				<div class="tableCell cell_ttl_1" style="width:60px;"><?php echo Configure::read("Bango") ?></div>
				<div class="tableCell cell_ttl_1" style="width:50px;">ゾーン</div>
				<div class="tableCell cell_ttl_1" style="width:80px;">SバッチNo</div>
				<div class="tableCell cell_ttl_1" style="width:100px;">集計管理No</div>
				<div class="tableCell cell_ttl_1" style="width:90px;">ロケーション</div>
				<div class="tableCell cell_ttl_1" style="width:130px;">商品コード</div>
				<div class="tableCell cell_ttl_1" style="width:50px;">数量</div>
			</div>
<?php
	// 選択可能拠点情報
	$index = 1;
	
	foreach ($t_ketus as $key => $value){
		$LOCA = substr($value['LOCATION'], 2,3) . '-' . substr($value['LOCATION'], 5,3);
?>
			<div class="tableRow row_dat" style="width:630px;">
				<div class="tableCell cell_dat bango" style="width:60px;"><?php echo $index++ ?></div>
				<div class="tableCell cell_dat kbn" style="width:50px;"><?php echo "{$value['ZONE']}" ?></div>
				<div class="tableCell cell_dat kbn" style="width:80px;"><?php echo "{$value['S_BATCH_NO']}" ?></div>
				<div class="tableCell cell_dat kbn" style="width:100px;"><?php echo "{$value['S_KANRI_NO']}" ?></div>
				<div class="tableCell cell_dat kbn" style="width:90px;"><?php echo $LOCA ?></div>
				<div class="tableCell cell_dat kbn" style="width:130px;"><?php echo "{$value['ITEM_CD']}" ?></div>
				<div class="tableCell cell_dat cnt" style="width:50px;"><?php echo "{$value['SURYO_T_KETU']}" ?></div>
			</div>
<?php
	}
?>
		</div>
	</div>
</div>
<!--/ テーブル五段目 -->


<!-- タイトル -->
<div class="lineTitle">【時間別進捗】</div>
<!--/ タイトル -->

<!-- 区切り線 -->
<div class="line tableBlock"></div>
<!--/ 区切り線 -->

<!-- テーブル一段目 -->
<div class="tableBlock">
	<div class="jikanList">
		<div class="tableRowTitle row_ttl_2" style="width:340px;">
			<div class="tableCell cell_ttl_2_1" style="width:50px;">時間</div>
			<div class="tableCell cell_ttl_2_1" style="width:70px;">物量</div>
			<div class="tableCell cell_ttl_2_2" style="width:90px;">ピッキング<br>生産性</div>
			<div class="tableCell cell_ttl_2_2" style="width:90px;">ソーター<br>生産性</div>
		</div>
<?php
	$BR_POINT = 7; //縦に並べる数
	$br_cnt = 1;
	for($a = 7; $a <= 13; $a++) {

		$sum_p = "";
		$p_prd = "";
		$s_prd = "";
		for($i = 0 ; $i <count($t_tps); $i++) {
			if($t_tps[$i]['END_H'] == $a) {
				$sum_p = number_format($t_tps[$i]['SUM_PIECE']);
				$p_prd = 0;
				if ($t_tps[$i]['CNT_STAFF'] != 0) {
					$p_prd = floor(($t_tps[$i]['SUM_PIECE'] / $t_tps[$i]['CNT_STAFF']) * 10) / 10;
				}
				$p_prd = number_format($p_prd, 1);
				
// *** ソーター生産性 とりあえず非表示にて対応 ***
//				$s_prd = 0;
//				$s_prd = floor(($t_tps[$i]['SUM_PIECE'] / 3) * 10) / 10;
//				$s_prd = number_format($s_prd, 1);
// *** ソーター生産性 とりあえず非表示にて対応 ***

				break;
		    }
		}
?>
		<div class="tableRow row_dat" style="width:340px;">
			<div class="tableCell cell_dat date" style="width:50px;"><?php echo "{$a}" ?>:00</div>
	        <div class="tableCell cell_dat cnt" style="width:70px;"><?php echo "{$sum_p}" ?></div>
	        <div class="tableCell cell_dat seisansei" style="width:90px;"><?php echo "{$p_prd}" ?></div>
	        <div class="tableCell cell_dat seisansei" style="width:90px;"><?php echo "{$s_prd}" ?></div>
		</div>
<?php

	}
?>
	</div>
	<div class="jikanList">
		<div class="tableRowTitle row_ttl_2" style="width:340px;">
			<div class="tableCell cell_ttl_2_1" style="width:50px;">時間</div>
			<div class="tableCell cell_ttl_2_1" style="width:70px;">物量</div>
			<div class="tableCell cell_ttl_2_2" style="width:90px;">ピッキング<br>生産性</div>
			<div class="tableCell cell_ttl_2_2" style="width:90px;">ソーター<br>生産性</div>
		</div>
<?php
	$BR_POINT = 7; //縦に並べる数
	$br_cnt = 1;
	for($a = 14; $a <= 20; $a++) {

		$sum_p = "";
		$p_prd = "";
		$s_prd = "";
		for($i = 0 ; $i <count($t_tps); $i++) {
			if($t_tps[$i]['END_H'] == $a) {
				$sum_p = number_format($t_tps[$i]['SUM_PIECE']);
				$p_prd = 0;
				if ($t_tps[$i]['CNT_STAFF'] != 0) {
					$p_prd = floor(($t_tps[$i]['SUM_PIECE'] / $t_tps[$i]['CNT_STAFF']) * 10) / 10;
				}
				$p_prd = number_format($p_prd, 1);
				
// *** ソーター生産性 とりあえず非表示にて対応 ***
//				$s_prd = 0;
//				$s_prd = floor(($t_tps[$i]['SUM_PIECE'] / 3) * 10) / 10;
//				$s_prd = number_format($s_prd, 1);
// *** ソーター生産性 とりあえず非表示にて対応 ***

				break;
		    }
		}
?>
		<div class="tableRow row_dat" style="width:340px;">
			<div class="tableCell cell_dat date" style="width:50px;"><?php echo "{$a}" ?>:00</div>
	        <div class="tableCell cell_dat cnt" style="width:70px;"><?php echo "{$sum_p}" ?></div>
	        <div class="tableCell cell_dat seisansei" style="width:90px;"><?php echo "{$p_prd}" ?></div>
	        <div class="tableCell cell_dat seisansei" style="width:90px;"><?php echo "{$s_prd}" ?></div>
		</div>

<?php

	}
?>
	</div>
	<div class="jikanList">
		<div class="tableRowTitle row_ttl_2" style="width:340px;">
			<div class="tableCell cell_ttl_2_1" style="width:50px;">時間</div>
			<div class="tableCell cell_ttl_2_1" style="width:70px;">物量</div>
			<div class="tableCell cell_ttl_2_2" style="width:90px;">ピッキング<br>生産性</div>
			<div class="tableCell cell_ttl_2_2" style="width:90px;">ソーター<br>生産性</div>
		</div>
<?php
	$BR_POINT = 7; //縦に並べる数
	$br_cnt = 1;
	for($a = 21; $a <= 27; $a++) {

		$sum_p = "";
		$p_prd = "";
		$s_prd = "";
		for($i = 0 ; $i <count($t_tps); $i++) {
			if($t_tps[$i]['END_H'] == $a) {
				$sum_p = number_format($t_tps[$i]['SUM_PIECE']);
				$p_prd = 0;
				if ($t_tps[$i]['CNT_STAFF'] != 0) {
					$p_prd = floor(($t_tps[$i]['SUM_PIECE'] / $t_tps[$i]['CNT_STAFF']) * 10) / 10;
				}
				$p_prd = number_format($p_prd, 1);
				
// *** ソーター生産性 とりあえず非表示にて対応 ***
//				$s_prd = 0;
//				$s_prd = floor(($t_tps[$i]['SUM_PIECE'] / 3) * 10) / 10;
//				$s_prd = number_format($s_prd, 1);
// *** ソーター生産性 とりあえず非表示にて対応 ***

				break;
		    }
		}
?>
		<div class="tableRow row_dat" style="width:340px;">
			<div class="tableCell cell_dat date" style="width:50px;"><?php echo "{$a}" ?>:00</div>
	        <div class="tableCell cell_dat cnt" style="width:70px;"><?php echo "{$sum_p}" ?></div>
	        <div class="tableCell cell_dat seisansei" style="width:90px;"><?php echo "{$p_prd}" ?></div>
	        <div class="tableCell cell_dat seisansei" style="width:90px;"><?php echo "{$s_prd}" ?></div>
		</div>

<?php

	}
?>
	</div>
</div>
<!--/ テーブル一段目 -->

<!-- タイトル -->
<div class="lineTitle">【ゾーン別進捗】</div>
<!--/ タイトル -->

<!-- 区切り線 -->
<div class="line tableBlock"></div>
<!--/ 区切り線 -->


<div class="tableBlock">

<?php
	$BR_POINT = 3; //横に並べる数
	$br_cnt = 1;
	// 選択可能拠点情報
	foreach ($zones as $key => $value){
?>
	<div class="sorterList">
		<div class="tableRowTitle row_ttl_sp_2" style="width:405px;">
			<div class="tableCell cell_ttl_1" style="width:395px;"><?php echo "{$value['ZONE_MEI']}" ?></div>
			<div class="tableCell cell_ttl_1" style="width:45px;">総数</div>
			<div class="tableCell cell_ttl_1" style="width:60px;">未処理</div>
			<div class="tableCell cell_ttl_1" style="width:60px;">処理中</div>
			<div class="tableCell cell_ttl_1" style="width:60px;">処理済</div>
			<div class="tableCell cell_ttl_1" style="width:60px;">棚欠品</div>
			<div class="tableCell cell_ttl_1" style="width:60px;">棚欠済</div>
		</div>
		<div class="tableRow row_dat" style="width:405px;">
			<div class="tableCell cell_dat cnt" style="width:45px;"><?php echo "{$value['ALL_CNT']}" ?></div>
	        <div class="tableCell cell_dat cnt" style="width:60px;"><?php echo "{$value['SYORI_MI_CNT']}" ?></div>
	        <div class="tableCell cell_dat cnt" style="width:60px;"><?php echo "{$value['SYORI_CYU_CNT']}" ?></div>
	        <div class="tableCell cell_dat cnt" style="width:60px;"><?php echo "{$value['SYORI_SUMI_CNT']}" ?></div>
	        <div class="tableCell cell_dat cnt" style="width:60px;"><?php echo "{$value['T_KETSU_CNT']}" ?></div>
	        <div class="tableCell cell_dat cnt" style="width:60px;"><?php echo "{$value['T_KETSU_SUMI_CNT']}" ?></div>
		</div>
	</div>
	<?php
		}
	?>
</div>
<!--/ テーブル二段目 -->

</div>
<!--/ #wrapper-->
