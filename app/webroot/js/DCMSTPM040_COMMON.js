//-------------------設定項目-----------------------

var DURATION = 0.1;//アニメーションの早さ
var DIV_PANEL_AREA_ID = "panelArea";//パネル全体を取り囲むDIVのID
var DIV_PANEL_CLASS = "panel";//パネル全体を取り囲むDIVのID
var DIV_PANEL_BASE_CLASS = "panelBase";//パネルのベース部分の描画クラス
var DIV_PANEL_SHADOW_CLASS = "shadow4";//影の種類 1,2,3で違う
var DIV_PANEL_SHADOW_CLASS2 = "shadow4";//影の種類 1,2,3で違う
var DIV_DATA_CURRENT_SORTER_CLASS = "CURRENT_SORTER_CD";//現在のソーターデータ保持クラス
var DIV_DATA_CURRENT_ORDER_CLASS = "CURRENT_ORDER";//現在順番保持クラス
var DIV_DATA_KBN_STATUS_CLASS = "KBN_BATCH_STATUS";//バッチの区分ステータスクラス
var DIV_DATA_S_BATCH_NO_CD_CLASS = "S_BATCH_NO_CD";//バッチNOコード
var DIV_DATA_TOP_PRIORITY_FLG_CLASS = "TOP_PRIORITY_FLG";//優先チェック
var DIV_DATA_TOP_PRIORITY_FLG_SELECTED_CLASS = "TOP_PRIORITY_FLG_SELECTED";//優先チェックが選択されたとき
var DIV_DATA_YMD_SYORI_CLASS = "YMD_SYORI";//処理日
var DIV_DATA_WMS_FLG_CLASS = "WMS_FLG";//WMSフラグ
var DIV_PANEL_DRAG_CARTAIN_CLASS = "overlay";//ドラッグ中のオブジェクトに被せる、黒い透明カーテンクラス
var DIV_PANEL_DONT_MOVE_CLASS = "dontMove";//ドラッグしないオブジェクト
var DIV_PANEL_DRAG_BORDER = "6px solid darkgray";//ドラッグ中のオブジェクトのボーダーＣＳＳ指定
var DIV_PANEL_TMP_MARK_CLASS = "markSpace";//元の位置を表すパネルのクラス
var DIV_PANEL_TMP_SPACE_CLASS = "tmpSpace";//挿入位置に挿入される空白パネルのクラス
var DIV_PANEL_S_BATCH_NM_CLASS = "panelS_BATCH_NM";//パネルのパーツ(Sバッチ名)
var DIV_PANEL_S_BATCH_NO_CLASS = "panelS_BATCH_NO";//パネルのパーツ(SバッチNO)
var DIV_PANEL_SURYO_PIECE_CLASS = "panelSURYO_PIECE";//パネルのパーツ(ピース数)
var DIV_PANEL_KBN_BATCH_STATUS_CLASS = "panelKBN_BATCH_STATUS_NM";//パネルのパーツ(状態)
var DIV_PANEL_TOP_PRIORITY_FLG_CLASS = "panelTOP_PRIORITY_FLG";//パネルのパーツ(最優先)

var DIV_PANEL_COVER_CLASS = "panelCover";//パネルのパーツ(カバー)
var SORTER_CD_1 = "A";//ソーター1
var SORTER_CD_2 = "B";//ソーター2
var SORTER_CD_3 = "C";//ソーター3
var SORTER_CD_4 = "K";//ソーター3

var DIV_SORTER_CLASS = "SORTER_COVER";
var DIV_SORTER_COVER_1 = "sorter_cover1";
var DIV_SORTER_COVER_2 = "sorter_cover2";
var DIV_SORTER_COVER_3 = "sorter_cover3";
var DIV_SORTER_COVER_4 = "sorter_cover4";

//------------------------------------------------

