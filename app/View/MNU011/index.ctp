<?php 

echo $this->Html->css('DCMSMNU', null, array('inline' => false));

if(count($kyotens) > 0) {

?>		
		<!-- WRAPPER -->
		<div id="wrapper" >　
            <div style="margin-top:40px;">
                <?php echo $this->Form->create('kyotens', Array('url' => '/MNU011')); ?>               
                    <table class="lst">
                        <tr>
                            <td id="lstHeader" colspan="3"><?php echo $this->Form->submit('選択', array('div' => 'btnsubmit')); ?></td>
                        </tr>                        
                        <th width="50px">選択</th>
                        <th width="250px">荷主</th>
                        <th width="250px">組織</th>
                        <?php
                        // 選択可能拠点情報
                        foreach ($kyotens as $key => $value){
                        ?> 
                        <tr>
                            <td><?php echo "{$this->Form->radio('value', array($key => ''), array('value' => $select_index, 'label'=> false))}" ?></td>
                            <td><?php echo "{$value['NINUSI_NM']}" ?></td>
                            <td><?php echo "{$value['SOSIKI_NM']}" ?></td>
                        </tr>
                        <?php 
                        }
                        ?> 
                    </table>
                <?php echo $this->Form->end(); ?>  
            </div>
		</div>
		<!--/ #wrapper-->
<?php 
}
?>		
		