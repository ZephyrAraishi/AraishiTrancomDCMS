function initMenu() {

	var lis = $("menus").getElementsBySelector("li.menuSubject");

	for(var i =0 ; i < lis.length; i++)
	{
		lis[i].observe("mouseover",overMenuSubject);
		lis[i].observe("mouseout",outMenuSubject);
	}
	
}

var menuSubjectThreadObj = null;
			
function overMenuSubject(event) {

	if (menuSubjectThreadObj != null) {
		
		menuSubjectThreadObj.stopRemove();
	}

	var element = Event.element(event);
    
    if (element.hasClassName("menuSubjectA")) {
		
        this.addClassName("hover");
	
        var lis = $("menus").getElementsBySelector("li.menuSubject");
	
		for (var i=0; i < lis.length; i++) {
			
			if (lis[i] != this) {
				
				lis[i].removeClassName("hover");
			}
		}
		
		var ul = this.getElementsBySelector("ul.menuContents")[0];
		
		ul.setStyle({
			display: "block"
		});
		
		var uls = $("menus").getElementsBySelector("ul.menuContents");
		
		for (var i=0; i < uls.length; i++) {
			
			if (uls[i] != ul) {
				
				uls[i].setStyle({
					display: "none"
				});
			}
		}
	
	} else {
	
		this.addClassName("hover");
	
		var li = Event.element(event).up("li");
		
        var lis = li.up("ul").getElementsBySelector("li");
	
		for (var i=0; i < lis.length; i++) {
			
			lis[i].removeClassName("hover")
		}
		
		li.addClassName("hover");
		
	}
   
			
    
}
		    
function outMenuSubject(event) {

	var element = Event.element(event);
    
    if (element.hasClassName("menuSubjectA")) {
		
		
		
	} else if (element.tagName == "li" || element.tagName == "LI"){
		
		
	} else {
		
		var li = Event.element(event).up("li");
		
		li.removeClassName("hover");
	}

	if (menuSubjectThreadObj != null) {
		
		menuSubjectThreadObj.stopRemove();
	}
	
	menuSubjectThreadObj = new RemoveMenuSubjectClass();
	menuSubjectThreadObj.remove();
	
}

var RemoveMenuSubjectClass = Class.create();

RemoveMenuSubjectClass.prototype = {

	initialize : function() {
	
        this.notRemove = 0;
        
    },
    
    remove : function() {
        
	    setTimeout(this.removeThread.bind(this), 500);  
	},
	
	stopRemove : function() {
	
		this.notRemove = 1;	
	},
  
	removeThread: function() {
	
		if(this.notRemove == 0){
		
		    var lis = $("menus").getElementsBySelector("li.menuSubject");
	
			for(var i =0 ; i < lis.length; i++)
			{
				lis[i].removeClassName("hover");
				var ul = lis[i].getElementsBySelector("ul.menuContents")[0];
			
				ul.setStyle({
					display: "none"
				});
			}
		}
	}  
    
    
}; 