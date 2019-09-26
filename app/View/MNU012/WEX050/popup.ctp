<div style= "position: relative; height: 250px; width: 100%; margin-top: 20px; margin-left: 20px;">
  <table width="100%" class="field">
	<colgroup width="150px">
	<colgroup width="200px">
	<colgroup width="150px">
	<colgroup width="200px">
	<colgroup width="150px">
	<colgroup width="200px">
	<tbody>

	<!-- １行目 -->
	<tr style="height:30px;">
		<td style="text-align:left;" >大分類：</td>
		<td class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('bnriDaiCd', 
							array('style' => 'width:100px;', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han', 
								'disabled' => 'disabled')) ?>
		</td>
		<td style="text-align:left;" >中分類：</td>
		<td colspan="3" class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('bnriCyuCd', 
							array('style' => 'width:100px;', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han', 
								'disabled' => 'disabled')) ?>
		</td>
	</tr>
	<!-- ２行目 -->
	<tr style="height:30px;">
		<td style="text-align:left;" >細分類コード：</td>
		<td colspan="5"  class="colBnriCd" style="text-align:left;">
			<?php echo $this->Form->input('bnriSaiCd', 
							array('style' => 'width:100px;', 
								'maxlength' => '3', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'han', 
								'disabled' => 'disabled')) ?>
		</td>
	</tr>
	<!-- ３行目 -->
	<tr style="height:30px;">
	   <td style="text-align:left;" >細分類名称：</td>
	   <td colspan="5"  class="colBnriCd" style="text-align:left;">
	   		<?php echo $this->Form->input('bnriSaiNm', 
							array('style' => 'width:300px;', 
								'maxlength' => '20', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'zen')) ?>
	   	</td>
	</tr>
	<!-- ４行目 -->
	<tr style="height:30px;">
	   <td style="text-align:left;" >細分類略称：</td>
	   <td colspan="5"  class="colBnriCd" style="text-align:left;">
	   		<?php echo $this->Form->input('bnriSaiRyaku', 
							array('style' => 'width:150px;', 
								'maxlength' => '6', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'zen')) ?>
	   </td>
	</tr>
	<!-- ５行目 -->
	<tr style="height:30px;">
	   <td style="text-align:left;" >細分類説明：</td>
	   <td colspan="5"  class="colBnriCd" style="text-align:left;">
	   		<?php echo $this->Form->input('bnriSaiExp', 
							array('style' => 'width:450px;', 
								'maxlength' => '40', 
								'error' => false, 
								'label' => false, 
								'div' => false, 
								'hidden' => false, 
								'class' => 'zen')) ?>
	   </td>
	</tr>
	<!-- ６行目 -->
	<tr style="height:30px;">
		<td style="text-align:left;" >単位：</td>
		<td  class="colBnriCd" style="text-align:left;">
			<select id="kbn_tani" style="width:120px">
				<option value=""></option>
				<?php
				foreach($kbnTanis as $key => $value) :
				?>
				<option value="<?php echo $key ?>"><?php echo $value?></option>
				<?php
				endforeach;
				?>
			</select>
		</td>
		<td style="text-align:left;">活動目的：</td>
		<td  class="colBnriCd" style="text-align:left;">
			<select id="kbn_ktd_mktk" style="width:120px">
				<option value=""></option>
				<?php
				foreach($kbnKtdMktks as $key => $value) :
				?>
				<option value="<?php echo $key ?>"><?php echo $value?></option>
				<?php
				endforeach;
				?>
			</select>
		</td>
		<td style="text-align:left;" >必須作業：</td>
		<td  class="colBnriCd" style="text-align:left;">
			<select id="kbn_hissu_wk" style="width:120px">
				<option value=""></option>
				<?php
				foreach($kbnHissuWks as $key => $value) :
				?>
				<option value="<?php echo $key ?>"><?php echo $value?></option>
				<?php
				endforeach;
				?>
			</select>
		</td>
	</tr>
	<!-- ７行目 -->
	<tr style="height:30px;">
		<td style="text-align:left;" >業務区分：</td>
		<td  class="colBnriCd" style="text-align:left;">
			<select id="kbn_gyomu" style="width:120px">
				<option value=""></option>
				<?php
				foreach($kbnGyomus as $key => $value) :
				?>
				<option value="<?php echo $key ?>"><?php echo $value?></option>
				<?php
				endforeach;
				?>
			</select>
		</td>
		<td style="text-align:left;" >データ取得区分：</td>
		<td  class="colBnriCd" style="text-align:left;">
			<select id="kbn_get_data" style="width:120px">
				<option value=""></option>
				<?php
				foreach($kbnGetDatas as $key => $value) :
				?>
				<option value="<?php echo $key ?>"><?php echo $value?></option>
				<?php
				endforeach;
				?>
			</select>
		</td>
		<td style="text-align:left;" >付加価値性：</td>
		<td  class="colBnriCd" style="text-align:left;">
			<select id="kbn_fukakachi" style="width:120px">
				<option value=""></option>
				<?php
				foreach($kbnFukakachis as $key => $value) :
				?>
				<option value="<?php echo $key ?>"><?php echo $value?></option>
				<?php
				endforeach;
				?>
			</select>
		</td>
	</tr>
	<!-- ８行目 -->
	<tr style="height:30px;">
		<td style="text-align:left;" >専門区分：</td>
		<td  class="colBnriCd" style="text-align:left;">
			<select id="kbn_senmon" style="width:120px">
				<option value=""></option>
				<?php
				foreach($kbnSenmons as $key => $value) :
				?>
				<option value="<?php echo $key ?>"><?php echo $value?></option>
				<?php
				endforeach;
				?>
			</select>
		</td>
		<td style="text-align:left;" >活動タイプ：</td>
		<td  class="colBnriCd" style="text-align:left;">
			<select id="kbn_ktd_type" style="width:120px">
				<option value=""></option>
				<?php
				foreach($kbnKtdTypes as $key => $value) :
				?>
				<option value="<?php echo $key ?>"><?php echo $value?></option>
				<?php
				endforeach;
				?>
			</select>
		</td>
		<td style="text-align:left;" >契約単位代表物量：</td>
		<td  class="colBnriCd" style="text-align:left;">
			<select id="keiyaku_buturyo_flg" style="width:120px">
				<?php
				foreach($keiyakuButuryoFlgs as $key => $value) :
				?>
				<option value="<?php echo $key ?>"><?php echo $value?></option>
				<?php
				endforeach;
				?>
			</select>
		</td>
	</tr>

	</tbody>

  </table>
</div>
