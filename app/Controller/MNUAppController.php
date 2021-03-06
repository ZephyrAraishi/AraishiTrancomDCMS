<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppController', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class MNUAppController extends AppController {
	
	/**
	 * 機能名取得処理
	 * @access   public
	 * @param    名称区分
	 * @param    名称コード
	 * @return   void
	 */
	function getDisplayName($mei_cd) {

		App::uses('MMeisyo', 'Model');
		
		// ログイン/メニュー機能
		$this->MMeisyo = new MMeisyo($ninusi_cd = '0000000000', $sosiki_cd = '0000000000', $this->name);
		// 名称マスタデータ取得
		$data = $this->MMeisyo->getMeisyo(Configure::read('mei_kbn.sys'), $mei_cd);
		return $data[$mei_cd];
		
	}
	
}