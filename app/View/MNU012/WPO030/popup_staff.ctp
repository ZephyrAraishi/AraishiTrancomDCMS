<div style= "display:block;position:relative;height:100px;width:100%;margin-top:20px;text-align: center;">
	<div style="display:block;position:relative;width:400px;margin-left:auto;margin-right:auto;height:80px;">

		<!-- スタッフコード -->
		<div style="display:block;position:absolute;height:30px;width:140px;top:10px;left:40px;text-align:right;">スタッフコード：</div>
		<div style="display:block;position:absolute;height:30px;top:10px;left:230px;" id="staffCd"></div>

		<!-- スタッフ名 -->
		<div style="display:block;position:absolute;height:30px;width:140px;top:40px;left:40px;text-align:right;">スタッフ名：</div>
		<div style="display:block;position:absolute;height:30px;top:40px;left:230px;" id="staffNm"></div>

		<!-- 変更ランク -->
		<div style="display:block;position:absolute;height:30px;width:140px;top:70px;left:40px;text-align:right;">変更ランク：</div>
		<div style="display:block;position:absolute;height:30px;top:70px;left:230px;">
			<select id="rankUpdOption">
				<option value=""></option>
				<?php foreach($rankOption as $val): ?>
				<option value="<?php echo $val['RANK']?>"><?php echo $val['RANK']?></option>
				<?php endforeach; ?>
			</select>
		</div>

	</div>
	<div style="width:250px;margin-left:auto;margin-right:auto;margin-top:10px;margin-bottom:10px;padding:0px;">
	</div>
</div>

