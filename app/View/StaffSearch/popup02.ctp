	<?php echo $this->Form->create('staffsearch', Array('url' => '/StaffSearch')); ?>
	<table align="center"  width="400px" border="1" bordercolor="#7F9DB9" rules="none" cellspacing="10">
		<tr>
			<td align="left" width="30%" >スタッフコード</td>
			<td align="left" width="70%">
			<?php echo $this->Form->input('staff_cd', array('maxlength' => '10', 'label' => false, 'error' => false, 'value' => $staff_cd)) ?></td>
		</tr>
		<tr>
			<td align="left" width="30%">スタッフ名</td>
			<td align="left" width="70%"><?php echo $this->Form->input('staff_nm', array('maxlength' => '30', 'label' => false, 'error' => false, 'value' => $staff_nm)) ?></td>
		</tr>
		<tr>
			<td align="left" width="30%">所属</td>
			<td align="left" width="70%"><?php echo $this->form->input('dispatch', array('options' => $dispatch, 'empty' => '選択してください', 'label' => false, 'error' => false, 'value'=>$dispatch_val)); ?>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="2">
			<?php
				if($mode == '1'){
			?>
				<input type="button" value="検索" onClick="staffSearchAjax();" style="width:80px;height:25px;">
			<?php
				}else{
				 echo $this->Form->submit('検索', array('div' => 'btnsubmit'));
				}
			?>

			</div></td>
		</tr>
	</table>
	<?php echo $this->Form->end() ?>
	<br />
	<!-- メインテーブル-->
	<div class="resizable" style="height: 250px;">
		<table border="1" width="600px" class="lst" id="lstTable" align="center">
		<thead>
			<tr>
				<th style="width:150px" rowspan="2">スタッフコード</th>
				<th style="width:110px" rowspan="2">スタッフ名</th>
				<th style="width:130px" rowspan="2">所属会社</th>
			</tr>
		</tr>
		<?php
			if($mode == '1'){
		?>
			</thead>
			</table>
				<div id="staff_search_list"></div>
		<?php
				}else{
					// 検索情報があれば表示
					if(count($search_data) > 0){
		?>
						<tbody id="listBody">
		<?php
							foreach($search_data as $key => $value){
		?>
								<input type="hidden" id="hidden_tr<?php echo $key ?>" value="<?php echo $value['STAFF_CD'] ?>">
								<tr id="tr<?php echo $key ?>" onmouseover="M_over(this)" onmouseout="M_out(this)" onclick="M_click(this)">
									<td><?php echo $value['STAFF_CD'] ?></td>
									<td><?php echo $value['STAFF_NM'] ?></td>
									<td><?php echo $value['KAISYA_NM'] ?></td>
								</tr>
		<?php
							}
		?>
						</tbody>
		<?php
					}
		?>
					</thead>
					</table>
		<?php
				}
		?>

	</div>
	<!--/ #wrapper-->