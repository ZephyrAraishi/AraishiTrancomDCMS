<?php

/**
 * TPM010
 *
 * 全体進捗
 *
 * @category      TPM
 * @package       DCMS
 * @subpackage    Controller
 * @author        ZephyrLabo
 * @version       1.0
 */
 
App::uses('TPM010Model', 'Model');
 
class TPM010Controller extends AppController {
	
	public $name = 'TPM010';
	public $layout = "DCMS";
	public $uses = array();	
	
	/**
	 * 起動時処理
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function beforeFilter() {
	
		parent::beforeFilter();
					

		// システム名
		$this->set('system', Sanitize::stripAll($this->Session->read('system_name')));
		// サブシステム名
		$this->set('sub_system', Sanitize::stripAll($this->Session->read('menu_subsys')['TPM']));
		// 機能名
		$this->set('kino', Sanitize::stripAll($this->Session->read('menu_kino')['TPM']['010']['kino_nm']));
		
		//TPM010Model
		$this->TPM010Model = new TPM010Model($this->Session->read('select_ninusi_cd'),$this->Session->read('select_sosiki_cd'), $this->name);
	}
	
	/**
	 * 初期表示
	 * @access   public
	 * @param    void
	 * @return   void
	 */
	public function index() {
	
		//キャッシュクリア
		Cache::clear();
		
		//Javascript設定
		$this->set("onload","initReload();");
		
		//上記全体の進捗部
		$zentaiArray = $this->TPM010Model->getZENTAI_INFO($this->Session->read('staff_cd'));
		
		
		//未処理、処理中の基準日
		$base_ymd = $zentaiArray['ymd_syori'];
		
		//全体の未処理、処理中、処理済みのバッチ数
		$misyori = $zentaiArray['misyori'];
		$syorichu = $zentaiArray['syorichu'];
		$syorizumi = $zentaiArray['syorizumi'];
		
		//全体のバッチ数
		$zentai = $misyori + $syorichu + $syorizumi;
		
		//全体のパッチ進捗グラフの割合を設置
		if ($zentai != 0) {
			$this->set('batchShinchoku',round($syorizumi / $zentai * 100));
		} else {
			$this->set('batchShinchoku',0);
		}
		
		//全体のパッチ進捗数を設置
		$this->set('batchShinchokuSu', number_format($syorizumi) . '/' . number_format($zentai));
		
		//全体の未処理、処理中、処理済みのピース数
		$piece_misyori = $zentaiArray['piece_misyori'];
		$piece_syorichu = $zentaiArray['piece_syorichu'];
		$piece_syorizumi = $zentaiArray['piece_syorizumi'];
		
		//全体のピース数
		$piece_zentai = $piece_misyori + $piece_syorichu + $piece_syorizumi;
		
		//全体の物量進捗グラフの割合を設置
		if ($piece_zentai != 0) {
			$this->set('butsuryoShinchoku',round($piece_syorizumi / $piece_zentai * 100));
		} else {
			$this->set('butsuryoShinchoku',0);
		}
		
		//全体の物量進捗数を設置
		$this->set('butsuryoShinchokuSu', number_format($piece_syorizumi) . '/' . number_format($piece_zentai));
		
		
		//ソーター進捗表示
		
		//ソーターA全体の進捗
		$sorterAArray = $this->TPM010Model->getSorter_Suryo('A',$base_ymd);
		
		//ソーターAの名前
		$soter_nm = $sorterAArray['sorter_nm'];
		
		//ソーターAの未処理、処理中、処理済みのバッチ数
		$misyori = $sorterAArray['misyori'];
		$syorichu = $sorterAArray['syorichu'];
		$syorizumi = $sorterAArray['syorizumi'];
		
		//ソーターAの全体のバッチ数
		$zentai = $misyori + $syorichu + $syorizumi;
		
		//ソーターAの未処理、処理中、処理済みのピース数
		$piece_misyori = $sorterAArray['piece_misyori'];
		$piece_syorichu = $sorterAArray['piece_syorichu'];
		$piece_syorizumi = $sorterAArray['piece_syorizumi'];
		
		//ソーターAの全体のピース数
		$piece_zentai = $piece_misyori + $piece_syorichu + $piece_syorizumi;
		
		//ソーターAの名前を設置
		$this->set('sorterNmA', $soter_nm);
		
		//ソーターAのバッチ進捗グラフの割合を設置
		if ($zentai != 0) {
			$this->set('AbatchShinchoku',round($syorizumi / $zentai * 100));
		} else {
			$this->set('AbatchShinchoku',0);
		}
		
		//ソーターAのバッチ進捗、物量進捗を設置
		$this->set('AbatchShinchokuSu', number_format($syorizumi) . '/' . number_format($zentai));
		$this->set('AbutsuryoShinchokuSu', number_format($piece_syorizumi) . '/' . number_format($piece_zentai));
		
		//ソーターB全体の進捗
		$sorterAArray = $this->TPM010Model->getSorter_Suryo('B',$base_ymd);
		
		//ソーターBの名前
		$soter_nm = $sorterAArray['sorter_nm'];
		
		//ソーターBの未処理、処理中、処理済みのバッチ数
		$misyori = $sorterAArray['misyori'];
		$syorichu = $sorterAArray['syorichu'];
		$syorizumi = $sorterAArray['syorizumi'];
		
		//ソーターBの全体のバッチ数
		$zentai = $misyori + $syorichu + $syorizumi;
		
		//ソーターBの未処理、処理中、処理済みのピース数
		$piece_misyori = $sorterAArray['piece_misyori'];
		$piece_syorichu = $sorterAArray['piece_syorichu'];
		$piece_syorizumi = $sorterAArray['piece_syorizumi'];
		
		//ソーターBの全体のピース数
		$piece_zentai = $piece_misyori + $piece_syorichu + $piece_syorizumi;
		
		//ソーターBの名前を設置
		$this->set('sorterNmB', $soter_nm);
		
		//ソーターBのバッチ進捗グラフの割合を設置
		if ($zentai != 0) {
			$this->set('BbatchShinchoku',round($syorizumi / $zentai * 100));
		} else {
			$this->set('BbatchShinchoku',0);
		}
		
		//ソーターBのバッチ進捗、物量進捗を設置
		$this->set('BbatchShinchokuSu', number_format($syorizumi) . '/' . number_format($zentai));
		$this->set('BbutsuryoShinchokuSu', number_format($piece_syorizumi) . '/' . number_format($piece_zentai));
		
		//ソーターC全体の進捗
		$sorterAArray = $this->TPM010Model->getSorter_Suryo('C',$base_ymd);
		
		//ソーターCの名前
		$soter_nm = $sorterAArray['sorter_nm'];
		
		//ソーターCの未処理、処理中、処理済みのバッチ数
		$misyori = $sorterAArray['misyori'];
		$syorichu = $sorterAArray['syorichu'];
		$syorizumi = $sorterAArray['syorizumi'];
		
		//ソーターCの全体のバッチ数
		$zentai = $misyori + $syorichu + $syorizumi;
		
		//ソーターCの未処理、処理中、処理済みのピース数
		$piece_misyori = $sorterAArray['piece_misyori'];
		$piece_syorichu = $sorterAArray['piece_syorichu'];
		$piece_syorizumi = $sorterAArray['piece_syorizumi'];
		
		//ソーターCの全体のピース数
		$piece_zentai = $piece_misyori + $piece_syorichu + $piece_syorizumi;
		
		//ソーターCの名前を設置
		$this->set('sorterNmC', $soter_nm);
		
		//ソーターCのバッチ進捗グラフの割合を設置
		if ($zentai != 0) {
			$this->set('CbatchShinchoku',round($syorizumi / $zentai * 100));
		} else {
			$this->set('CbatchShinchoku',0);
		}
		
		//ソーターCのバッチ進捗、物量進捗を設置
		$this->set('CbatchShinchokuSu', number_format($syorizumi) . '/' . number_format($zentai));
		$this->set('CbutsuryoShinchokuSu', number_format($piece_syorizumi) . '/' . number_format($piece_zentai));
		
		
		//ソータ毎のバッチリスト
		
		//ソーターAのバッチリスト
		
		//ソーターAの未処理、処理中、処理済みリスト
		$syorichu_List = $this->TPM010Model->getSorter_List_Syorichu('A',$base_ymd);
		$misyori_List = $this->TPM010Model->getSorter_List_Mishori('A',$base_ymd);
		$sumi_List = $this->TPM010Model->getSorter_List_Sumi('A',$base_ymd);
		
		//ソーターAリストを優先チェック有り、処理中、未処理、処理済みでソートする
		$arrayList = array_merge($syorichu_List,$misyori_List,$sumi_List);
		$TOP_PRIORITY_FLG = array();
		$PRIORITY =  array();
		
		foreach ($arrayList as $key => $row) {
		    $TOP_PRIORITY_FLG[$key]  = $row['TOP_PRIORITY_FLG'];
		    $PRIORITY[$key]  = $key;
		}

		array_multisort($TOP_PRIORITY_FLG,SORT_DESC,$PRIORITY,SORT_ASC,$arrayList);
		
		//ソーターAのリストを設置
		$this->set('sorterListA', $arrayList);
		
		//ソーターBのバッチリスト
		
		//ソーターBの未処理、処理中、処理済みリスト
		$syorichu_List = $this->TPM010Model->getSorter_List_Syorichu('B',$base_ymd);
		$misyori_List = $this->TPM010Model->getSorter_List_Mishori('B',$base_ymd);
		$sumi_List = $this->TPM010Model->getSorter_List_Sumi('B',$base_ymd);
		
		//ソーターBリストを優先チェック有り、処理中、未処理、処理済みでソートする
		$arrayList = array_merge($syorichu_List,$misyori_List,$sumi_List);
		$TOP_PRIORITY_FLG = array();
		$PRIORITY =  array();
		
		foreach ($arrayList as $key => $row) {
		    $TOP_PRIORITY_FLG[$key]  = $row['TOP_PRIORITY_FLG'];
		    $PRIORITY[$key]  = $key;
		}

		array_multisort($TOP_PRIORITY_FLG,SORT_DESC,$PRIORITY,SORT_ASC,$arrayList);
		
		//ソーターBのリストを設置
		$this->set('sorterListB', $arrayList);
		
		//ソーターCのバッチリスト
		
		//ソーターCの未処理、処理中、処理済みリスト
		$syorichu_List = $this->TPM010Model->getSorter_List_Syorichu('C',$base_ymd);
		$misyori_List = $this->TPM010Model->getSorter_List_Mishori('C',$base_ymd);
		$sumi_List = $this->TPM010Model->getSorter_List_Sumi('C',$base_ymd);
		
		//ソーターCリストを優先チェック有り、処理中、未処理、処理済みでソートする
		$arrayList = array_merge($syorichu_List,$misyori_List,$sumi_List);
		$TOP_PRIORITY_FLG = array();
		$PRIORITY =  array();
		
		foreach ($arrayList as $key => $row) {
		    $TOP_PRIORITY_FLG[$key]  = $row['TOP_PRIORITY_FLG'];
		    $PRIORITY[$key]  = $key;
		}

		array_multisort($TOP_PRIORITY_FLG,SORT_DESC,$PRIORITY,SORT_ASC,$arrayList);
		
		//ソーターCのリストを設置
		$this->set('sorterListC', $arrayList);
		
	}
	
}

