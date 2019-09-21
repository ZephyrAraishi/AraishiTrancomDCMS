<div style= "position: relative; height: 400px; width: 100%; margin-top: 20px;">
　<div id="table_title" style="font-size:12pt;font-weight:bold;text-align:left;"></div>
  <table width="100%" class="field" style="border-collapse:collapse; border:1px solid #666666;">
		<colgroup width="100px">
		<colgroup width="100px">
		<colgroup width="100px">
		<colgroup width="100px">
		<colgroup width="100px">
		<colgroup width="100px">
		<colgroup width="100px">
		<colgroup width="100px">
		<tbody>

			<tr style="height:30px;">
				<td style="background-color:#666666;font-size:10pt;font-weight:bold;color:white;text-align:center;border-collapse:collapse; border:1px solid #666666;">稼働人数</td>
				<td style="background-color:#666666;font-size:10pt;font-weight:bold;color:white;text-align:center;border-collapse:collapse; border:1px solid #666666;">累計人数</td>
				<td style="background-color:#666666;font-size:10pt;font-weight:bold;color:white;text-align:center;border-collapse:collapse; border:1px solid #666666;">累計時間(h)</td>
				<td style="background-color:#666666;font-size:10pt;font-weight:bold;color:white;text-align:center;border-collapse:collapse; border:1px solid #666666;">累計時間(m)</td>
				<td style="background-color:#666666;font-size:10pt;font-weight:bold;color:white;text-align:center;border-collapse:collapse; border:1px solid #666666;">累計物量</td>
				<td style="background-color:#666666;font-size:10pt;font-weight:bold;color:white;text-align:center;border-collapse:collapse; border:1px solid #666666;">物量生産性</td>
				<td style="background-color:#666666;font-size:10pt;font-weight:bold;color:white;text-align:center;border-collapse:collapse; border:1px solid #666666;">予測物量</td>
				<td style="background-color:#666666;font-size:10pt;font-weight:bold;color:white;text-align:center;border-collapse:collapse; border:1px solid #666666;">予測終了時間</td>
			</tr>

			<tr style="height:30px;">
				<td style="background-color:white;font-size:10pt;font-weight:bold;color:black;text-align:center;border-collapse:collapse; border:1px solid #666666;" id="KADO_NINZU" ></td>
				<td style="background-color:white;font-size:10pt;font-weight:bold;color:black;text-align:center;border-collapse:collapse; border:1px solid #666666;" id="RUIKEI_NINZU" ></td>
				<td style="background-color:white;font-size:10pt;font-weight:bold;color:black;text-align:center;border-collapse:collapse; border:1px solid #666666;" id="JIKAN_H" ></td>
				<td style="background-color:white;font-size:10pt;font-weight:bold;color:black;text-align:center;border-collapse:collapse; border:1px solid #666666;" id="JIKAN_M" ></td>
				<td style="background-color:white;font-size:10pt;font-weight:bold;color:black;text-align:center;border-collapse:collapse; border:1px solid #666666;" id="BUTURYO" ></td>
				<td style="background-color:white;font-size:10pt;font-weight:bold;color:black;text-align:center;border-collapse:collapse; border:1px solid #666666;" id="SEISANSEI_JINJI" ></td>
				<td style="background-color:white;font-size:10pt;font-weight:bold;color:black;text-align:center;border-collapse:collapse; border:1px solid #666666;" >
				<?php echo $this->Form->input('YOSOKU_BUTURYO',
										array('style' => 'width:90px;',
										    'id' => 'YOSOKU_BUTURYO',
											'maxlength' => '6',
											'error' => false,
											'label' => false,
											'div' => false,
											'hidden' => false,
											'class' => 'han',)) ?>
				</td>
				<td style="background-color:white;font-size:10pt;font-weight:bold;color:black;text-align:center;border-collapse:collapse; border:1px solid #666666;" id="YOSOKU_JIKAN" ></td>
			</tr>


		</tbody>

  </table>
  <div style="display:block;position:relative;height: 270px;min-height: 270px;overflow-x: auto;overflow-y: scroll;width: 100%;margin-top:20px;">
    <table width="100%" class="field" style="border-collapse:collapse; border:1px solid #666666;">
		<colgroup width="100px">
		<colgroup width="100px">
		<colgroup width="100px">
		<colgroup width="100px">
		<colgroup width="100px">
		<colgroup width="100px">
		<colgroup width="100px">
		<tbody>

			<tr style="height:30px;">
				<td style="background-color:#666666;font-size:10pt;font-weight:bold;color:white;text-align:center;border-collapse:collapse; border:1px solid #666666;">ｽﾀｯﾌｺｰﾄﾞ</td>
				<td style="background-color:#666666;font-size:10pt;font-weight:bold;color:white;text-align:center;border-collapse:collapse; border:1px solid #666666;">ｽﾀｯﾌ</td>
				<td style="background-color:#666666;font-size:10pt;font-weight:bold;color:white;text-align:center;border-collapse:collapse; border:1px solid #666666;">累計時間(h)</td>
				<td style="background-color:#666666;font-size:10pt;font-weight:bold;color:white;text-align:center;border-collapse:collapse; border:1px solid #666666;">累計時間(m)</td>
				<td style="background-color:#666666;font-size:10pt;font-weight:bold;color:white;text-align:center;border-collapse:collapse; border:1px solid #666666;">累計物量</td>
				<td style="background-color:#666666;font-size:10pt;font-weight:bold;color:white;text-align:center;border-collapse:collapse; border:1px solid #666666;">稼動生産性</td>
				<td style="background-color:#666666;font-size:10pt;font-weight:bold;color:white;text-align:center;border-collapse:collapse; border:1px solid #666666;">実質生産性</td>
			</tr>
<?php  $count = 0; ?>
<?php  if (empty($lsts)){$lsts = array();} ?>
<?php  foreach ($lsts as $array): ?>
			<tr style="height:30px;">
				<td style="background-color:white;font-size:10pt;font-weight:bold;color:black;text-align:center;border-collapse:collapse; border:1px solid #666666;" ><?php echo $array['STAFF_CD'] ?></td>
				<td style="background-color:white;font-size:10pt;font-weight:bold;color:black;text-align:center;border-collapse:collapse; border:1px solid #666666;" ><?php echo $array['STAFF_NM'] ?></td>
				<td style="background-color:white;font-size:10pt;font-weight:bold;color:black;text-align:center;border-collapse:collapse; border:1px solid #666666;" ><?php echo $array['JIKAN_H'] ?></td>
				<td style="background-color:white;font-size:10pt;font-weight:bold;color:black;text-align:center;border-collapse:collapse; border:1px solid #666666;" ><?php echo $array['JIKAN_M'] ?></td>
				<td style="background-color:white;font-size:10pt;font-weight:bold;color:black;text-align:center;border-collapse:collapse; border:1px solid #666666;" ><?php echo $array['BUTURYO'] ?></td>
				<td style="background-color:white;font-size:10pt;font-weight:bold;color:black;text-align:center;border-collapse:collapse; border:1px solid #666666;" ><?php echo $array['SEISANSEI_KADO'] ?></td>
				<td style="background-color:white;font-size:10pt;font-weight:bold;color:black;text-align:center;border-collapse:collapse; border:1px solid #666666;" ><?php echo $array['SEISANSEI_JISSEKI'] ?></td>
<?php  $count++; ?>
<?php  endforeach; ?>
		</tbody>
    </table>
  </div>
</div>
