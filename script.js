
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

