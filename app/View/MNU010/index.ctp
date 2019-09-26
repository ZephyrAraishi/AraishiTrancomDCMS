        <?php echo $this->Html->css('DCMSMNU', null, array('inline' => false)) ?> 
		<!-- WRAPPER -->
		<div id="wrapper">
			
            <div id="mnuWrapper">
                <?php echo $this->Form->create('MNU010Model', array('url' => '/MNU010', 'inputDefaults' => array('label' => false,'div' => false))) ?> 
                    <table class="login">
                        <tr></tr>
                        <tr><td>スタッフコード</td></tr>     
                        <tr><td><?php echo $this->Form->input('staff_cd', array('maxlength' => '10', 'label' => false, 'error' => false, 'class' => 'han')) ?></td></tr>     
                        <tr><td>パスワード</td></tr>     
                        <tr><td><?php echo $this->Form->input('password', array('maxlength' => '30', 'label' => false, 'error' => false)) ?></td></tr>
                        <tr></tr>     
                        <tr><td><?php echo $this->Form->submit('ログイン', array('div' => 'btnsubmit')); ?></td></tr>
                        <tr></tr>
                    </table>
                   <?php echo $this->Form->end() ?> 
            </div>
		</div>
		<!--/ #wrapper-->            