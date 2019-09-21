<?php
			echo $this->Html->script(array('effects','window','base64','DCMSMessage','DCMSCommon'), array('inline'=>false));
            echo $this->Html->css(array('DCMSWPO'), false, array('inline'=>false));

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">

    <div id="wrapper_l">
    <!-- ヘッダ情報 -->
	<div style="width:96%;text-align:center;">
<?php foreach ($t_spick as $array){ ?>
	
	<span id="batNoCd"><?php echo $array['S_BATCH_NO_CD'] ?>　</span>
	</div>
	<div style="width:96%">
	    <table align="center">
	        <tr>
	            <td>集計管理No</td>
	            <td>：</td>
	            <td><?php echo $array['S_KANRI_NO'] ?></td>
	        </tr>
	        <tr>
	          <td>バッチNo</td>
	            <td>：</td>
	            <td><?php echo $array['BATCH_CD'] ?></td>
	        </tr>
	       <tr>
	           <td>SバッチNo</td>
	            <td>：</td>
	            <td><?php echo $array['S_BATCH_NO'] ?></td>
	        </tr>
	    </table>
	</div>
	<div style="width:96%">
	<table>
	    <tr>
	       <td>ゾーン</td>
	        <td><span id="zone"><?php echo $array['ZONE'] ?></span></td>
	    </tr>
	</table>
	</div>
	<!--/ ヘッダ情報 -->
	<!-- メインテーブル-->
	<div>
	    <table border="1" width="100%" class="lst">
	       <tr>
	           <th style="width:7%">ロケーション</th>
	           <th style="width:7%">ＪＡＮ</th>
	          <th style="width:30%">商品名</th>
	            <th style="width:8%">外箱入数</th>
	            <th style="width:8%">内箱入数</th>
	            <th style="width:8%">外箱数</th>
	            <th style="width:8%">内箱数</th>
	            <th style="width:8%">バラ数</th>
	            <th style="width:8%">総バラ数</th>
	            <th style="width:8%">不足数</th>
	        </tr>
<?php
		break;
	}
	// 集計ピッキング情報
	foreach ($t_spick as $array){
	
	
		$LOCA_1 = substr($array['LOCATION'], 2,3);
		$LOCA_2 = substr($array['LOCATION'], 5,3);
		$JAN = substr($array['ITEM_CD'], 6);
		$JAN_1 = substr($JAN, 1,3);
		$JAN_2 = substr($JAN, 4,3);
		$IRI_HAKO_SOTO = number_format($array['IRI_HAKO_SOTO']);
		$IRI_HAKO_UCHI = number_format($array['IRI_HAKO_UCHI']);
		$SURYO_HAKO_SOTO = number_format($array['SURYO_HAKO_SOTO']);
		$SURYO_HAKO_UCHI = number_format($array['SURYO_HAKO_UCHI']);
		$SURYO_BARA = number_format($array['SURYO_BARA']);
		$SURYO_TOTAL = number_format($array['SURYO_TOTAL']);
		$SURYO_T_KETU = number_format($array['SURYO_T_KETU']);
?>
  	        <tr>
	            <td>
					<span id="locLeft"><?php echo $LOCA_1 ?></span>
	                <span id="locCenter">-</span>
					<span id="locRight"><?php echo $LOCA_2 ?></span>
	            </td>
	            <td>
					<span id="janLeft"><?php echo $JAN_1 ?></span>
					<span id="janRight"><?php echo $JAN_2 ?></span>
	            </td>
	            <td id="itemNm">
	                <?php echo $array['ITEM_NM'] ?>
	            </td>
	            <td id="suryo">
	                <?php echo $IRI_HAKO_SOTO ?>
	            </td>
	            <td id="suryo">
	                <?php echo $IRI_HAKO_UCHI ?>
	            </td>
	            <td id="suryo">
	                <?php echo $SURYO_HAKO_SOTO ?>
	            </td>
	            <td id="suryo">
	                <?php echo $SURYO_HAKO_UCHI ?>
	            </td>
	            <td id="suryo">
	                <?php echo $SURYO_BARA ?>
	            </td>
	            <td id="suryo">
	                <?php echo $SURYO_TOTAL ?>
	            </td>
	            <td id="suryo">
	                <?php echo $SURYO_T_KETU ?>
	            </td>
	        </tr>
<?php
	}		
?> 
	</table>
</div>
<!--/ メインテーブル-->

</div>
<!--/ #wrapper-->
