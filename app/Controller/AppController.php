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

App::uses('Controller', 'Controller');
App::uses('Sanitize', 'Utility');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	public $components = array('Session');

	/**
	 * Constructor.
	 *
	 * @param CakeRequest $request Request object for this controller. Can be null for testing,
	 *  but expect that features that use the request parameters will not work.
	 * @param CakeResponse $response Response object for this controller.
	 */
	public function __construct($request = null, $response = null) {

		parent::__construct($request, $response);

	}

	public function beforeFilter() {

		parent::beforeFilter();

		if ($this->name == 'MNU010') {
			// セッション初期化
			$this->Session->read('staff_code');
			$this->Session->destroy();
		}

		//タイトルにリンクを付与
		if ($this->Session->check('system_name')) {
			$this->set("system_name_link","true");
		}

		if ($this->name == 'MNU010') {
			return;
		}

		if ($this->name == 'Auto' || $this->name == 'WEX100PDF') {
			$this->Session->read('staff_code');
			$this->Session->destroy();
			return;
		}

		//セッションチェック
		if(!$this->Session->check('staff_cd')){

			if ($this->name != "MNU010" &&
			    substr($this->name, 0, 3) != "ERR") {

			  	$this->redirect('/ERR020/');
			}

		}

		if ($this->name == "StaffSearch") {
			return;
		}


		//エラーの場合
		if ($this->name == "CakeError"){

		//	$this->redirect('/ERR010/');
		}

		//権限がない場合
		if (substr($this->name, 0, 3) != "MNU" &&
		    substr($this->name, 0, 3) != "ERR") {

			$menyu_kino = $this->Session->read('menu_kino');
	 		$subSysId = substr($this->name, 0, 3);
			$kinoId = substr($this->name, 3);

			if (!isset($menyu_kino[$subSysId][$kinoId])) {
				// 利用可能機能として存在しない
			//	$this->redirect('/ERR030/');
			}
		}


	}


}
