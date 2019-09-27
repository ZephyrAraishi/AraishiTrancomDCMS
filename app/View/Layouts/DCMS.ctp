<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php echo (empty($system)) ? '' : $system ?></title>
        <?php echo $this->Html->charset(); ?>
        <?php echo $this->Html->css('style') ?>
        <?php echo $this->Html->script('prototype') ?>
        <?php echo $this->Html->script('menu') ?>
		<?php echo $scripts_for_layout ?>
    </head>
    <body onload="initMenu();<?php echo (empty($onload)) ? '' : $onload ?>">
        <!-- ヘッダ -->
        <div id="header" style="z-index:600">
            <div id="system"><?php if (isset($system_name_link) and !isset($nonSysLink)) echo "<a href='/DCMS/MNU012'>"; ?>
            				 <?php echo (empty($system)) ? '' : $system ?>
            				 <?php if (isset($system_name_link)) echo '</a>'; ?></div>
            <div id="sub_system"><?php echo (empty($sub_system)) ? '' : $sub_system ?></div>
            <div id="staff"><?php if ($this->Session->check('staff_cd')) {
                     				echo $this->Session->read('select_ninusi_ryaku'). ' '. $this->Session->read('select_sosiki_ryaku');
                     				echo '<br>';
                     				echo $this->Session->read('staff_nm') ;
                     				}
           ?></div>
        </div>
        <!--/ ヘッダ -->
        <!-- メニューバー -->
        <div id="menubar" style="z-index:700">
            <div id="kino"><?php echo (empty($kino)) ? '' : $kino ?> </div>
            <div id="menus">
<?php
 				if ($this->Session->check('menu_kino') and !isset($nonDispMenu)){
	 				$subsys = $this->Session->read('menu_subsys');
	 				$kino = $this->Session->read('menu_kino');
?>
				<ul id="nav">
<?php
    				foreach ($subsys as $key => $value){
?>
						<li class="menuSubject">
	                        <a  class="menuSubjectA" href="#">▼ <?php echo $value ?></a>
	                        <ul class="menuContents">
<?php
						foreach ($kino[$key] as $key2 => $value2){

//							if (substr($key2,2,1) != "0") {
							if ($value2['dsp_flg'] != 1) {
								 continue;
							}

                            echo "<li>";
                            echo $this->Html->link($value2['kino_nm'], $value2['kino_url']);
                            echo "</li>";
                            echo "\n";

                       }
?>
	                        </ul>
	                    </li>
<?php
                    }
?>
                </ul>
<?php         }
?>
            </div>
            <div id="logout">
<?php
                if (!isset($nonDispLogOut)) {
?>
                <ul id="nav">
                    <li><a href="javascript:if(confirm('ログアウトします。\nよろしいですか？')){location='/DCMS/MNU010'}">ログアウト</a></li>
                </ul>
<?php
                }
?>
            </div>
        </div>
        <!--/ メニュー -->
        <!-- 上部空白部分 -->
        <div id="upperSpace"></div>
	   <!--/ 上部空白部分 -->

	   <!-- メッセージ表示部分 -->
	    <div id="upperMsg">
	        <!-- エラーメッセージ -->
	        <div id="errorMsg"><?php
	                if(!empty($errors)){
	                    foreach ($errors as $key => $value){
	                        foreach ($value as $key2 => $value2){
	                        echo $value2 . "<br/>";
	                        }
	                    }
	                }
	      ?></div>
	        <!-- エラーメッセージ -->
	        <!-- 通知メッセージ -->
	        <div id="infoMsg"><?php
	                if(!empty($infos)){
	                    foreach ($infos as $key => $value){
	                        foreach ($value as $key2 => $value2){
	                        echo $value2 . "<br/>";
	                        }
	                    }
	                }
	       ?></div>
           <!-- 通知メッセージ -->
        </div>
        <!-- メッセージ表示部分 -->

        <?php echo $content_for_layout ?>
        <div style="position:relative;bottom:0;margin-top:20px;">Copyright (C) 2013-2019 Kasei Co., Ltd. All Rights Reserved.</div>
    </body>
</html>