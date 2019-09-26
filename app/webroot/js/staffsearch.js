	
	var bM_click = new Array();
	var focused;
	var bM_click_id  = ""; // スタッフコードID
	var cd_click_val = ""; // スタッフコード値
	var nM_click_id  = ""; // スタッフ名ID
	var nm_click_val = ""; // スタッフ名値
	var kC_click_id  = ""; // 派遣会社コードID
	var kc_click_val = ""; // 派遣会社コード値
	var kN_click_id  = ""; // 派遣会社名ID
	var kn_click_val = ""; // 派遣会社名値
	var MouseDownStaffSearchFlg = 0;
	var areaCoverElementStaffSearch = null;
	var viewStaffSearchPopup = null;
	
	function createAreaCoverElementStaffSearch(){
		//端末遅い人用に、ポップアップがでるまでのカバーをつける
		var acelement = new Element("div");
		var height = Math.max.apply( null, [document.body.clientHeight ,
		                                    document.body.scrollHeight,
		                                    document.documentElement.scrollHeight,
		                                    document.documentElement.clientHeight] );
	
		acelement.setStyle({
			height: height + "px",
			zIndex: "10000"
		});
		acelement.addClassName("displayCoverStaffSearch");
		return acelement;
	}

	//マウスが乗ったとき。
	function M_over(obj, window){
		//クリックされていなければ[ffff99]にする。
		if(!bM_click[obj.id]){On(obj,"#ffff99");}
	}

	//クリックされたとき
	function M_click(obj, window){
		//クリックされたIDをセット
		bM_click_id  = "hidden_cd_" + obj.id;
		
		if(document.getElementById(bM_click_id) != null){
			cd_click_val = document.getElementById(bM_click_id).value;
		}
		
		nM_click_id  = "hidden_nm_" + obj.id;
		
		if(document.getElementById(nM_click_id) != null){
			nm_click_val = document.getElementById(nM_click_id).value;
		}
		
		kC_click_id  = "hidden_kc_" + obj.id;
		
		if(document.getElementById(kC_click_id) != null){
			kc_click_val = document.getElementById(kC_click_id).value;
		}
		
		kN_click_id  = "hidden_kn_" + obj.id;
		
		if(document.getElementById(kN_click_id) != null){
			kn_click_val = document.getElementById(kN_click_id).value;
		}
		
		// データがない場合は何もしない
		if((cd_click_val != "") || (nm_click_val != "") || (kc_click_val != "") || (kn_click_val != "")){
			// データの引渡し
			if(document.getElementById('staff_cd') != null){
				document.getElementById('staff_cd').innerHTML = cd_click_val; // スタッフコードのhtmlに埋込
			}
			
			if(document.getElementById('staff_nm') != null){
				document.getElementById('staff_nm').innerHTML = nm_click_val; // スタッフ名のhtmlに埋込
			}
			
			if(document.getElementById('haken_kaisya_cd') != null){
				document.getElementById('haken_kaisya_cd').innerHTML = kc_click_val; // 派遣会社IDのhtmlに埋込
			}
			
			if(document.getElementById('haken_kaisya_nm') != null){
				document.getElementById('haken_kaisya_nm').innerHTML = cd_click_val; // 派遣会社名のhtmlに埋込
			}

			if(document.getElementById('staff_cd_hidden') != null){
				document.getElementById('staff_cd_hidden').value = cd_click_val; // スタッフコードのhtmlに埋込
			}

			if(document.getElementById('staff_nm_hidden') != null){
				document.getElementById('staff_nm_hidden').value = nm_click_val; // スタッフ名のhtmlに埋込
			}
		}
		
		/*
		
		alert(cd_click_val); // スタッフコード
		alert(nm_click_val); // スタッフ名
		alert(kc_click_val); // 派遣会社ID
		alert(kn_click_val); // 派遣会社名
		*/
		
		if((document.getElementById(bM_click_id) != null) && (document.getElementById(nM_click_id) != null)){
			Dialog.okCallback()
		}
		
		MouseDownStaffSearchFlg = 0;

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

	//ajax
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
		//ポップアップを読み込む
		var obj = {};
		obj['staff_cd'] = $('staffsearchStaffCd').value
		obj['staff_nm'] = $('staffsearchStaffNm').value
		obj['dispatch'] = $('dispatch').value
		
		new Ajax.Request( "/DCMS/StaffSearch/ajax", {
			method : 'post',
			parameters : obj,
			onSuccess : function(res) {
			
				var data = eval("(" + res.responseText + ")");
			
				if(data.length > 0){
				
					for(i = 0; i < data.length; i++){
						innerHtml += "<input type=\"hidden\" id=\"hidden_cd_tr"+ i +"\" value=\"" + data[i].STAFF_CD + "\">";
						innerHtml += "<input type=\"hidden\" id=\"hidden_nm_tr"+ i +"\" value=\"" + data[i].STAFF_NM + "\">";
						innerHtml += "<input type=\"hidden\" id=\"hidden_kc_tr"+ i +"\" value=\"" + data[i].HAKEN_KAISYA_CD + "\">";
						innerHtml += "<input type=\"hidden\" id=\"hidden_kn_tr"+ i +"\" value=\"" + data[i].KAISYA_NM + "\">";
						innerHtml += "<tr id=\"tr" + i + "\" name=\""+ data[i].STAFF_CD +"\" onmouseover=\"M_over(this)\" onmouseout=\"M_out(this)\" onclick=\"M_click(this)\">";
						innerHtml += "<td style=\"width:150px\">" + data[i].STAFF_CD + "</td>";
						innerHtml += "<td style=\"width:110px\">" + data[i].STAFF_NM + "</td>";
						innerHtml += "<td style=\"width:130px\">" + data[i].KAISYA_NM + "</td>";
						innerHtml += "</tr>";
					}
				}else{
					innerHtml += "<tr><td colspan=\"3\">検索データは見つかりませんでした。</td></tr>";
				}
				document.getElementById('staff_search_list').innerHTML = innerHeader + innerHtml + innerFooter;
				cd_click_val = null;
				nm_click_val = null;
				kc_click_val = null;
				kn_click_val = null;
			},
		  
			onFailure : function( event )  {
			},
			onException : function( event, ex )  {
			}
		});
		
		//サブミット後、ページをリロードしないようにする
		return false;
	}

	//テーブル行クリックイベントハンドラ
	function clickStaffSearchPopUp(event) {
		
		var staffSearchVal  = '';
		var staffSearchVal2 = '';
		var staffSearchVal3 = '';
		
		if (MouseDownStaffSearchFlg == 1) {
			return;
		}
		MouseDownStaffSearchFlg = 1;
		
		//端末遅い人用に、ポップアップがでるまでのカバーをつける
		areaCoverElementStaffSearch = createAreaCoverElementStaffSearch();
		$$("body")[0].insert(areaCoverElementStaffSearch);
		
		if(document.getElementById('staff_cd') != null){
			staffSearchVal  = document.getElementById('staff_cd').innerHTML;
		}
		
		if(document.getElementById('staff_nm') != null){
			staffSearchVal2 = document.getElementById('staff_nm').innerHTML;
		}
		
		if(document.getElementById('dispatch') != null){
			staffSearchVal3 = document.getElementById('dispatch').value;
		}
		
		var requestUrl     = '';
		
		requestUrl = '/DCMS/StaffSearch/popup02?staff_cd=' + staffSearchVal + '&staff_nm=' + staffSearchVal2 + '&dispatch=' + staffSearchVal3;
		
		//ポップアップを読み込む
		new Ajax.Request( requestUrl, {
		  method : 'post',

		  onSuccess : staffSearchViewPopUp,
		  
		  onFailure : function( event )  {
		  },
		  onException : function( event, ex )  {
		  }
		});
	}


	/* ポップアップ */
	function staffSearchViewPopUp(event) {
		
		
		//画面に配置
		var popup2 = Dialog.confirm(event.responseText, 
					   {
						id: "popup2",
						className: "alphacube",
						title: "スタッフ検索",
						width:700,
						height:500,
						draggable: true,
						destroyOnclose: true,
						recenterAuto: false,
						buttonClass : "buttons",
						cancelLabel:"キャンセル",
						onCancel: clickCANCELSTAFFSEARCH,
						showEffectOptions: {duration:0.2},
						hideEffectOptions: {duration:0}
					   });
					   
		popup2.setZIndex(1000);

		areaCoverElementStaffSearch.remove();
					   
	}

    function clickCANCELSTAFFSEARCH(window) {
	    
	    MouseDownStaffSearchFlg = 0;
	    
    }