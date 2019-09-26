<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	    <?php echo $this->Html->css('style') ?> 
        <?php echo $this->Html->script('prototype') ?> 
        <?php echo $this->Html->script('menu') ?>
        <?php echo $this->Html->css('cake.generic'); ?> 
	</head>
<body>
	<div id="container">
		<div id="header">
			<div id="system"><div>
		</div>
	</div>
	<!-- メニューバー -->
	<div id="menubar">
		<div id="kino"></div>
		<div id="menus">
			<ul id="nav">
				<li></li>
			</ul>
		</div>
	</div>
	<!--/ メニュー -->
	<!-- 上部空白部分 -->
	<div id="upperSpace"></div>
	<!--/ 上部空白部分 -->
	<!-- エラーメッセージ -->
	<div id="errorMsg">
		<h2>※&nbsp;エラーが発生しました</><br />
		<a href="/DCMS/MNU010">【ログイン】</a><br />
		<?php echo $this->fetch('content'); ?>
		</h2>
	</div>
	<!-- エラーメッセージ -->
	<div id="footer"></div>
	<!-- SQLダンプ 残すか残さないか？ -->
	<?php echo $this->element('sql_dump'); ?>
	<!-- SQLダンプ 残すか残さないか？ -->
</body>
</html>
