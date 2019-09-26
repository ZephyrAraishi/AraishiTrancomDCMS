<script type="text/javascript" src="/DCMS/js/jquery.js"></script>
<script><!--
jQuery.noConflict();
jQuery(document).ready(function(){
	// ここでは、$はprototypeの動作をします。
	// jQueryオブジェクトとしての$は一切使えず、その場合は$()ではなくjQuery()と表記する必要があります。
});
//--></script>
	<?php echo $this->Form->create('staffsearch', Array('url' => '/STAFFSEARCH')); ?>
	<table align="center"  width="400px" border="1" bordercolor="#7F9DB9" rules="none" cellspacing="10">
		<tr>
			<td align="left" width="30%" >スタッフコード</td>
			<td align="left" width="70%"><?php echo $this->Form->input('staff_cd', array('maxlength' => '10', 'label' => false, 'error' => false)) ?></td>
		</tr>
		<tr>
			<td align="left" width="30%">スタッフ名</td>
			<td align="left" width="70%"><?php echo $this->Form->input('staff_nm', array('maxlength' => '30', 'label' => false, 'error' => false)) ?></td>
		</tr>
		<tr>
			<td align="left" width="30%">所属</td>
			<td align="left" width="70%"><?php echo $this->form->input('dispatch', array('options' => $dispatch, 'empty' => '選択してください', 'label' => false, 'error' => false)); ?>
			</td>
		</tr>
		<tr>
			<td align="left" width="30%"></td>
			<td align="left" width="70%"><div class="btnsubmit"><input type="button" value="検索" onClick="staffSearchAjax();"></div></td>
		</tr>
	</table>
	<?php echo $this->Form->end() ?>
	<br />
	<!-- メインテーブル-->
	<div class="resizable" style="height: 250px;">
		<table border="1" width="600px" class="lst" id="lstTable" align="center">
		<thead>
			<tr>
				<th style="width:150px" rowspan="2">スタッフコード</th>
				<th style="width:110px" rowspan="2">スタッフ名</th>
				<th style="width:130px" rowspan="2">所属会社</th>
			</tr>
		</tr>
		</thead>
		</table>
			<div id="staff_search_list"></div>
	</div>
	<br>
	<input type="button" value="決定" onClick="testtest();">

	<script type="text/javascript">
	<!--
		var bM_click = new Array();
		var focused;
		var bM_click_id  = "";
		var bm_click_val = "";

		//マウスが乗ったとき。
		function M_over(obj){
			//クリックされていなければ[ffff99]にする。
			if(!bM_click[obj.id]){On(obj,"#ffff99");}
		}

		//クリックされたとき
		function M_click(obj){
			//クリックされたIDをセット
			bM_click_id  = "hidden_" + obj.id;
			bm_click_val = document.getElementById(bM_click_id).value;

			//オンになっていた（フォーカスされていた）自分をクリックしたなら自分自身をオフ。
			//フォーカスをオフ。
			//クリックフラグ[自分自身]をオフ。
			if (focused == obj.id){
				Off(obj);
				focused = null;
				bM_click[obj.id] = 0;
				bm_click_val = null;
			}else{
				//フォーカスされていたものが他にあったならそっちをオフ。
				//クリックフラグ[フォーカス]をオフ。
				if (focused != null){
					Off( document.getElementById(focused) );
					bM_click[focused] = 0;
			}
			//自分自身をオン。背景色を引数で渡す。
			//フォーカスを自分自身に移す。
			//クリックフラグ[自分自身]をオン
			On(obj,"#ff9900");
			focused = obj.id;
			bM_click[obj.id] = 1;
		}

		}
		//マウスが去ったとき
		function M_out(obj){
			//クリックされていなければオフ。
			if(!bM_click[obj.id]){Off(obj);}
		}

		//背景色と文字色チェンジ：オン
		function On(obj,c){
			obj.style.backgroundColor = c;
		}

		//背景色と文字色チェンジ：オフ
		function Off(obj){
			obj.style.backgroundColor = "transparent";
		}

		//値保持のテスト
		function testtest(){

			alert(bm_click_val);

		}

		//ajaxテスト
		function staffSearchAjax(){
			var innerHeader = "<table border=\"1\" width=\"600px\" class=\"lst\" id=\"lstTable\" align=\"center\"><thead><tbody id=\"listBody\">";
			var innerHtml   = '';
			var innerFooter = "</tbody></thead></table>";
            /**
             * Ajax通信メソッド
             * @param type  : HTTP通信の種類
             * @param url   : リクエスト送信先のURL
             * @param data  : サーバに送信する値
             */
            $(jQuery).ajax({
                type: "POST",
                url: "/DCMS/StaffSearch/ajax",
                dataType: 'json',
                data:{
					 staff_cd:$('staffsearchStaffCd').value
					,staff_nm:$('staffsearchStaffNm').value
					,dispatch:$('dispatch').value
				},
                /**
                 * Ajax通信が成功した場合に呼び出されるメソッド
                 */
                success: function(data, dataType)
                {
                	for(i = 0; i < data.length; i++){
                		innerHtml += "<input type=\"hidden\" id=\"hidden_tr"+ i +"\" value=\"" + data[i].STAFF_CD + "\">";
                		innerHtml += "<tr id=\"tr" + i + "\" name=\""+ data[i].STAFF_CD +"\" onmouseover=\"M_over(this)\" onmouseout=\"M_out(this)\" onclick=\"M_click(this)\">";
                		innerHtml += "<td style=\"width:150px\">" + data[i].STAFF_CD + "</td>";
                		innerHtml += "<td style=\"width:110px\">" + data[i].STAFF_NM + "</td>";
                		innerHtml += "<td style=\"width:130px\">" + data[i].KAISYA_NM + "</td>";
                	}
                	document.getElementById('staff_search_list').innerHTML = innerHeader + innerHtml + innerFooter;
                	bm_click_val = null;
                },
                /**
                 * Ajax通信が失敗した場合に呼び出されるメソッド
                 */
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    //通常はここでtextStatusやerrorThrownの値を見て処理を切り分けるか、単純に通信に失敗した際の処理を記述します。

                    //this;
                    //thisは他のコールバック関数同様にAJAX通信時のオプションを示します。

                    //エラーメッセージの表示
                    alert('Error : ' + errorThrown);
                }
            });
            //サブミット後、ページをリロードしないようにする
            return false;
		}
	// -->
	</script>


	<!--/ #wrapper-->