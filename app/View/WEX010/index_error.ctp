<?php
			echo $this->Html->script(array('effects','window','base64',
					'DCMSMessage','DCMSCommon','DCMSValidation','DCMSWEX010_RELOAD','DCMSWEX010_UPDATE'), array('inline'=>false));
			echo $this->Html->css('DCMSWEX', null, array('inline' => false));
?>

<!-- WRAPPER -->
<div id="wrapper_s">

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
							<td>　</td>
						</tr>
						<tr>
							<td>会社名</td>
							<td><input type='text' id='ninusi_nm' maxlength='20' class='col5 zen' value="<?php echo $data['M_NINUSI_SOSIKI']['NINUSI_NM'] ?>"></td>
						</tr>
						<tr>
							<td>会社名略</td>
							<td><input type='text' id='ninusi_ryaku' maxlength='6' class='col1 zen' value="<?php echo $data['M_NINUSI_SOSIKI']['NINUSI_RYAKU'] ?>"></td>
						</tr>
						<tr>
							<td>本社　〒</td>
							<td><input type='text' id='ninusi_post_no' maxlength='10' class='col1 han' value="<?php echo $data['M_NINUSI_SOSIKI']['NINUSI_POST_NO'] ?>"></td>
						</tr>
						<tr>
							<td align="right">住所１</td>
							<td><input type='text' id='ninusi_addr_1' maxlength='40' class='col6 zen' value="<?php echo $data['M_NINUSI_SOSIKI']['NINUSI_ADDR_1']?>"></td>
						</tr>
						<tr>
							<td align="right">住所２</td>
							<td><input type='text' id='ninusi_addr_2' maxlength='40' class='col6 zen' value="<?php echo $data['M_NINUSI_SOSIKI']['NINUSI_ADDR_2'] ?>"></td>
						</tr>
						<tr>
							<td align="right">住所３</td>
							<td><input type='text' id='ninusi_addr_3' maxlength='40' class='col6 zen' value="<?php echo $data['M_NINUSI_SOSIKI']['NINUSI_ADDR_3'] ?>"></td>
						</tr>
						<tr>
							<td align="right">TEL/FAX</td>
							<td>
								 <div style="display:inline;"><input type='text' id='ninusi_tel' maxlength='20' class='col1 han' value="<?php echo $data['M_NINUSI_SOSIKI']['NINUSI_TEL'] ?>"></div>
								 <div style="display:inline;"><input type='text' id='ninusi_fax' maxlength='20' class='col1 han' value="<?php echo $data['M_NINUSI_SOSIKI']['NINUSI_FAX'] ?>"></div>
							</td>
						</tr>
						<tr>
							<td>　</td>
							<td>　</td>
						</tr>
						<tr>
							<td>代表者名</td>
							<td><input type='text' id='ninusi_dai_nm' maxlength='20' class='col2 zen' value="<?php echo $data['M_NINUSI_SOSIKI']['NINUSI_DAI_NM'] ?>"></td>
						</tr>
						<tr>
							<td>設立</td>
							<td><input type='text' id='ninusi_est_ym' maxlength='10' class='col1 zen' value="<?php echo $data['M_NINUSI_SOSIKI']['NINUSI_EST_YM'] ?>"></td>
						</tr>
						<tr>
							<td>資本金</td>
							<td><input type='text' id='ninusi_sihon' maxlength='15'  class='col1 han' value="<?php echo $data['M_NINUSI_SOSIKI']['NINUSI_SIHON'] ?>"></td>
						</tr>
						<tr valign="top">
							<td>事業内容</td>
							<td><textarea id='ninusi_jigyo' maxlength='100' class='row6col6' style="resize:none;"><?php echo $data['M_NINUSI_SOSIKI']['NINUSI_JIGYO'] ?></textarea></td>
						</tr>
						<tr><td>　</td></tr>
					</table>
				</td></tr>
			</table>
		</td>
		<td  width="30px">
		</td>
		<td style="border:1px solid #666666;padding:10px;">
			<table width="500px" >
				<tr height="450px" valign="top"><td>
					<table class="field">
						<tr>
							<td width="100px">（倉庫部署）</td>
						</tr>
						<tr>
							<td>部署名</td>
							<td><input type="text" id='sosiki_nm' maxlength='20' class='col5 zen' value="<?php echo $data['M_NINUSI_SOSIKI']['SOSIKI_NM'] ?>"></td>
						</tr>
						<tr>
							<td>部署名略</td>
							<td><input type="text" id='sosiki_ryaku' maxlength='6' class='col1 zen' value="<?php echo $data['M_NINUSI_SOSIKI']['SOSIKI_RYAKU'] ?>"></td>
						</tr>
						<tr>
							<td>担当部署　〒</td>
							<td><input type="text" id='sosiki_post_no' maxlength='10' class='col1 han' value="<?php echo $data['M_NINUSI_SOSIKI']['SOSIKI_POST_NO'] ?>"></td>
						</tr>
						<tr>
							<td align="right">住所１</td>
							<td><input type='text' id='sosiki_addr_1' maxlength='40' class='col6 zen' value="<?php echo $data['M_NINUSI_SOSIKI']['SOSIKI_ADDR_1'] ?>"></td>
						</tr>
						<tr>
							<td align="right">住所２</td>
							<td><input type='text' id='sosiki_addr_2' maxlength='40' class='col6 zen' value="<?php echo $data['M_NINUSI_SOSIKI']['SOSIKI_ADDR_2'] ?>"></td>
						</tr>
						<tr>
							<td align="right">住所３</td>
							<td><input type='text' id='sosiki_addr_3' maxlength='40' class='col6 zen' value="<?php echo $data['M_NINUSI_SOSIKI']['SOSIKI_ADDR_3'] ?>"></td>
						</tr>
						<tr>
							<td align="right">TEL/FAX</td>
							<td>
							     <div style="display:inline;"><input type='text' id='sosiki_tel' maxlength='20' class='col1 han' value="<?php echo $data['M_NINUSI_SOSIKI']['SOSIKI_TEL'] ?>"></div>
								 <div style="display:inline;"><input type='text' id='sosiki_fax' maxlength='20' class='col1 han' value="<?php echo $data['M_NINUSI_SOSIKI']['SOSIKI_FAX'] ?>"></div>
							</td>
						</tr>
						<tr>
							<td>倉庫責任者</td>
							<td><input type='text' id='sosiki_dai_nm' maxlength='20' class='col2 zen' value="<?php echo $data['M_NINUSI_SOSIKI']['SOSIKI_DAI_NM'] ?>"></td>
						</tr>
						<tr>
							<td>請求部署</td>
							<td><input type='text' id='seikyu_busyo_nm' maxlength='20' class='col2 zen' value="<?php echo $data['M_NINUSI_SOSIKI']['SEIKYU_BUSYO_NM'] ?>"></td>
						</tr>
						<tr>
							<td align="right">〒</td>
							<td><input type='text' id='seikyu_post_no' maxlength='10' class='col1 han' value="<?php echo $data['M_NINUSI_SOSIKI']['SEIKYU_POST_NO'] ?>"></td>
						</tr>
						<tr>
							<td align="right">住所１</td>
							<td><input type='text' id='seikyu_addr_1' maxlength='40' class='col6 zen' value="<?php echo $data['M_NINUSI_SOSIKI']['SEIKYU_ADDR_1'] ?>"></td>
						</tr>
						<tr>
							<td align="right">住所２</td>
							<td><input type='text' id='seikyu_addr_2' maxlength='40' class='col6 zen' value="<?php echo $data['M_NINUSI_SOSIKI']['SEIKYU_ADDR_2'] ?>"></td>
						</tr>
						<tr>
							<td align="right">住所３</td>
							<td><input type='text' id='seikyu_addr_3' maxlength='40' class='col6 zen' value="<?php echo $data['M_NINUSI_SOSIKI']['SEIKYU_ADDR_3'] ?>"></td>
						</tr>
						<tr>
							<td align="right">TEL/FAX</td>
							<td>
							     <div style="display:inline;"><input type='text' id='seikyu_tel' maxlength='20' class='col1 han' value="<?php echo $data['M_NINUSI_SOSIKI']['SEIKYU_TEL'] ?>"></div>
								 <div style="display:inline;"><input type='text' id='seikyu_fax' maxlength='20' class='col1 han' value="<?php echo $data['M_NINUSI_SOSIKI']['SEIKYU_FAX'] ?>"></div>
							</td>
						</tr>
						<tr>
							<td>請求担当者</td>
							<td><input type='text' id='seikyu_dai_nm' maxlength='20' class='col2 zen' value="<?php echo $data['M_NINUSI_SOSIKI']['SEIKYU_DAI_NM'] ?>"></td>
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
