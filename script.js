
function GetElement(element){
	if(typeof element == "string"){
		element=document.getElementById(element);
	}
	return element;
}

function Element_SetVisibility(element,visible){
	element=GetElement(element);
	if(visible){
		element.style.display="";
	}else{
		element.style.display="none"
	}
}

function Element_ToggleVisibility(element){
	element=GetElement(element);
	if(element.style.display=="none"){
		element.style.display="";
	}else{
		element.style.display="none"
	}
}

function DropDown_GetValue(dropdown){
	return dropdown.options[dropdown.selectedIndex].value;
}

function ShowProgressDialog(){
	var divLoadBack=GetElement("divLoadBack");
	var divLoading=GetElement("divLoading");
	
	var hidScanDevice=GetElement("hidScanDevice");
	var ddlResolution=GetElement("ddlResolution");
	var ddlFormat=GetElement("ddlFormat");
	var ddlSize=GetElement("ddlSize");
	
	Element_SetVisibility("divLoadBack",true);
	
	var keyValue=
		hidScanDevice.value+"_"+
		DropDown_GetValue(ddlResolution)+"_"+
		DropDown_GetValue(ddlFormat)+"_"+
		DropDown_GetValue(ddlSize);
	var startTime=new Date().getTime() / 1000;
	var estimatedTime=0;
	if(timings.hasOwnProperty(keyValue)){
		estimatedTime=timings[keyValue];
	}
	var divProgressBar=GetElement("divProgressBar");
	
	var timerFunction=function(){
		var timeNow=new Date().getTime() / 1000;
		var value=(timeNow-startTime)/estimatedTime;
		if(value>1.0){value=1.0;}
		if(value<0){value=0;}
		divProgressBar.style.width=parseInt(value*100)+"%";
		
		window.setTimeout(timerFunction,300);
	};
	window.setTimeout(timerFunction,300);
}
