<?php
			echo $this->Html->script(array('effects','window','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSWEX010_RELOAD','DCMSWEX010_UPDATE'), array('inline'=>false));
			echo $this->Html->css('DCMSWEX', null, array('inline' => false));
?>

<!-- WRAPPER -->
<div id="wrapper_s">
<?php echo $this->Form->create(array('url' => '/WEX010/update')); ?>


<!-- 更新ボタン-->
<div style="height:40px;width:100%;text-align:right;" id="buttons" >
	<input type="button"  value="更新" style="width:120px;height:30px;" id="updateButton" >
</div>
<!--/ 更新ボタン-->


<table width="100%">
	<tr>
		<td style="border:1px solid #666666;padding:10px;">
			<table width="500px">
				<tr height="450px" valign="top"><td>
					<table class="field">
						<tr>
							<td width="100px">（会社情報）</td>
						</tr>
						<tr>
							<td>会社名</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.NINUSI_NM', array('id'=>'ninusi_nm', 'maxlength' => '20', 'label' => false, 'class' => 'col5 zen')) ?></td>
						</tr>
						<tr>
							<td>会社名略</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.NINUSI_RYAKU', array('id'=>'ninusi_ryaku', 'maxlength' => '6', 'label' => false, 'class' => 'col1 zen')) ?></td>
						</tr>
						<tr>
							<td>本社　〒</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.NINUSI_POST_NO', array('id'=>'ninusi_post_no', 'maxlength' => '10', 'label' => false, 'class' => 'col1 han')) ?></td>
						</tr>
						<tr>
							<td align="right">住所１</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.NINUSI_ADDR_1', array('id'=>'ninusi_addr_1', 'maxlength' => '40', 'label' => false, 'class' => 'col6 zen')) ?></td>
						</tr>
						<tr>
							<td align="right">住所２</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.NINUSI_ADDR_2', array('id'=>'ninusi_addr_2', 'maxlength' => '40', 'label' => false, 'class' => 'col6 zen')) ?></td>
						</tr>
						<tr>
							<td align="right">住所３</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.NINUSI_ADDR_3', array('id'=>'ninusi_addr_3', 'maxlength' => '40', 'label' => false, 'class' => 'col6 zen')) ?></td>
						</tr>
						<tr>
							<td align="right">TEL/FAX</td>
							<td>
								 <?php echo $this->Form->text('M_NINUSI_SOSIKI.NINUSI_TEL', array('id'=>'ninusi_tel', 'maxlength' => '20', 'label' => false, 'div' => 'inline', 'class' => 'col1 han')) ?>
								 <?php echo $this->Form->text('M_NINUSI_SOSIKI.NINUSI_FAX', array('id'=>'ninusi_fax', 'maxlength' => '20', 'label' => false, 'div' => 'inline', 'class' => 'col1 han')) ?>
							</td>
						</tr>
						<tr>
							<td>　</td>
							<td>　</td>
						</tr>
						<tr>
							<td>代表者名</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.NINUSI_DAI_NM', array('id'=>'ninusi_dai_nm', 'maxlength' => '20', 'label' => false,  'class' => 'col2 zen')) ?></td>
						</tr>
						<tr>
							<td>設立</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.NINUSI_EST_YM', array('id'=>'ninusi_est_ym', 'maxlength' => '10', 'label' => false,  'class' => 'col1 zen')) ?></td>
						</tr>
						<tr>
							<td>資本金</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.NINUSI_SIHON', array('id'=>'ninusi_sihon', 'maxlength' => '15', 'label' => false,  'class' => 'col1 han')) ?></td>
						</tr>
						<tr valign="top">
							<td>事業内容</td>
							<td><?php echo $this->Form->textarea('M_NINUSI_SOSIKI.NINUSI_JIGYO', array('id'=>'ninusi_jigyo', 'maxlength' => '100', 'label' => false, 'class' => 'row6col6','style' => 'resize:none;')) ?></td>
						</tr>
					    <tr><td>　</td></tr>
					</table>
				</td></tr>
			</table>
		</td>
		<td  width="30px">
		</td>
		<td style="border:1px solid #666666;padding:10px;">
			<table width="500px">
				<tr height="450px" valign="top"><td>
					<table class="field">
						<tr>
							<td width="100px">（倉庫部署）</td>
						</tr>
						<tr>
							<td>部署名</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.SOSIKI_NM', array('id'=>'sosiki_nm', 'maxlength' => '20', 'label' => false,  'class' => 'col5 zen')) ?></td>
						</tr>
						<tr>
							<td>部署名略</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.SOSIKI_RYAKU', array('id'=>'sosiki_ryaku', 'maxlength' => '6', 'label' => false,  'class' => 'col1 zen')) ?></td>
						</tr>
						<tr>
							<td>担当部署　〒</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.SOSIKI_POST_NO', array('id'=>'sosiki_post_no', 'maxlength' => '10', 'label' => false,  'class' => 'col1 han')) ?></td>
						</tr>
						<tr>
							<td align="right">住所１</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.SOSIKI_ADDR_1', array('id'=>'sosiki_addr_1', 'maxlength' => '40', 'label' => false,  'class' => 'col6 zen')) ?></td>
						</tr>
						<tr>
							<td align="right">住所２</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.SOSIKI_ADDR_2', array('id'=>'sosiki_addr_2', 'maxlength' => '40', 'label' => false,  'class' => 'col6 zen')) ?></td>
						</tr>
						<tr>
							<td align="right">住所３</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.SOSIKI_ADDR_3', array('id'=>'sosiki_addr_3', 'maxlength' => '40', 'label' => false,  'class' => 'col6 zen')) ?></td>
						</tr>
						<tr>
							<td align="right">TEL/FAX</td>
							<td>
							     <?php echo $this->Form->text('M_NINUSI_SOSIKI.SOSIKI_TEL', array('id'=>'sosiki_tel', 'maxlength' => '20', 'label' => false, 'div' => 'inline', 'class' => 'col1 han')) ?>
								 <?php echo $this->Form->text('M_NINUSI_SOSIKI.SOSIKI_FAX', array('id'=>'sosiki_fax', 'maxlength' => '20', 'label' => false, 'div' => 'inline', 'class' => 'col1 han')) ?>
							</td>
						</tr>
						<tr>
							<td>倉庫責任者</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.SOSIKI_DAI_NM', array('id'=>'sosiki_dai_nm', 'maxlength' => '20', 'label' => false,  'class' => 'col2 zen')) ?></td>
						</tr>
						<tr>
							<td>請求部署</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.SEIKYU_BUSYO_NM', array('id'=>'seikyu_busyo_nm', 'maxlength' => '20', 'label' => false,  'class' => 'col2 zen')) ?></td>
						</tr>
						<tr>
							<td align="right">〒</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.SEIKYU_POST_NO', array('id'=>'seikyu_post_no', 'maxlength' => '10', 'label' => false,  'class' => 'col1 han')) ?></td>
						</tr>
						<tr>
							<td align="right">住所１</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.SEIKYU_ADDR_1', array('id'=>'seikyu_addr_1', 'maxlength' => '40', 'label' => false,  'class' => 'col6 zen')) ?></td>
						</tr>
						<tr>
							<td align="right">住所２</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.SEIKYU_ADDR_2', array('id'=>'seikyu_addr_2', 'maxlength' => '40', 'label' => false,  'class' => 'col6 zen')) ?></td>
						</tr>
						<tr>
							<td align="right">住所３</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.SEIKYU_ADDR_3', array('id'=>'seikyu_addr_3', 'maxlength' => '40', 'label' => false,  'class' => 'col6 zen')) ?></td>
						</tr>
						<tr>
							<td align="right">TEL/FAX</td>
							<td>
							     <?php echo $this->Form->text('M_NINUSI_SOSIKI.SEIKYU_TEL', array('id'=>'seikyu_tel', 'maxlength' => '20', 'label' => false, 'div' => 'inline', 'class' => 'col1 han')) ?>
								 <?php echo $this->Form->text('M_NINUSI_SOSIKI.SEIKYU_FAX', array('id'=>'seikyu_fax', 'maxlength' => '20', 'label' => false, 'div' => 'inline', 'class' => 'col1 han')) ?>
							</td>
						</tr>
						<tr>
							<td>請求担当者</td>
							<td><?php echo $this->Form->text('M_NINUSI_SOSIKI.SEIKYU_DAI_NM', array('id'=>'seikyu_dai_nm', 'maxlength' => '20', 'label' => false,  'class' => 'col2 zen')) ?></td>
						</tr>
					</table>
				</td></tr>
			</table>
		</td>
	</tr>
</table>
<br>
<br>
<?php echo $this->Form->end() ?>
</div>
<!--/ #wrapper-->
