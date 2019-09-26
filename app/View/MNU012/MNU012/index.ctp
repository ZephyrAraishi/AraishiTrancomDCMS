<?php
		echo $this->Html->css('DCMSMNU', null, array('inline' => false)); 
		if (count($menus) > 0) {
?>
		<!-- WRAPPER -->
		<div id="wrapper" >
			<div id="menu">
		        <ul id="menubar">
			        <table class="menuLst">
<?php 
				    // サブシステムID初期化
				    $subSystemId = '';
						    	
			// メニュー情報
			foreach ($menus as $key => $value){
			
				// メニュー表示許可判断
				if ($value['DSP_FLG'] == 1) {
	
					// 行判断①　サブシステム名表示
					if ($subSystemId != $value['SUB_SYSTEM_ID']) {
						// サブシステムIDが異なった場合
						 
						$subSystemId = $value['SUB_SYSTEM_ID'];
						$kinoCnt = 1;
?>
			            <tr>
			                <td><li class="title"><?php echo $value['SUB_SYSTEM_NM'] ?></li></td>
			            </tr>
<?php                
			        }
			        // 行判断②　1機能目
			        if (fmod($kinoCnt, 3) == 1) {
?> 
			            <tr>
			                <td></td>
<?php
		        	}
?> 
			                <td><li><?php echo $this->Html->link($value['KINO_NM'], $value['KINO_URL']) ?></li></td>
<?php
			        // 行判断③　3機能目
		        	if (fmod($kinoCnt, 3) == 0) {
?> 
						</tr>
<?php
			        }
			        $kinoCnt += 1;
			    }
		    }
?>         
						    
    				</table>
		        </ul>
			</div>     
		</div>
		<!--/ #wrapper-->
<?php
		}
?>

