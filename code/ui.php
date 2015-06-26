<?php

function RenderLabel($text){
	if($text!="" && $text!=null){
		$textFixed=htmlentities($text,ENT_HTML5, "UTF-8");
		return '<span class="fieldLabel">'.$textFixed.":</span>\n";
	}else{
		return '<span class="fieldLabel"></span>'."\n";
	}
}

function RenderInfo($text){
	$textFixed=htmlentities($text,ENT_HTML5, "UTF-8");
	return '<div class="fieldContent">'.$textFixed."</div>\n";
}

function RenderText($idText,$value){
	$idTextFixed=htmlentities($idText,ENT_HTML5, "UTF-8");
	$valueFixed=htmlentities($value,ENT_HTML5, "UTF-8");
	$render='<div class="fieldContent">'."\n";
	$render.='<input type="text" id="'.$idTextFixed.'" name="'.$idTextFixed.'"'.
		' value="'.$valueFixed.'" '.
		' class="textBox"/>'."\n";
	$render.="</div>\n";
	return $render;
}

function RenderCheck($idCheck,$checked,$value){
	$idCheckFixed=htmlentities($idCheck,ENT_HTML5, "UTF-8");
	$valueFixed=htmlentities($idText,ENT_HTML5, "UTF-8");
	$render='<div class="fieldContent">'."\n";
	$render.='<input type="checkbox" id="'.$idCheckFixed.'" '.
		'name="'.$idCheckFixed.'" ';
	if($checked){ $render.=" checked "; }
	$render.='class="check">'.$valueFixed."\n";
	$render.="</input>\n";
	$render.="</div>\n";
	return $render;
}


function RenderCheckText($idCheck,$checked,$idText,$value){
	$idCheckFixed=htmlentities($idCheck,ENT_HTML5, "UTF-8");
	$idTextFixed=htmlentities($idText,ENT_HTML5, "UTF-8");
	$valueFixed=htmlentities($value,ENT_HTML5, "UTF-8");
	$render='<div class="fieldContent">'."\n";
	$render.='<input type="checkbox" id="'.$idCheckFixed.'" '.
		'name="'.$idCheckFixed.'" ';
	if($checked){ $render.=" checked "; }
	$render.='class="check" />'."\n";
	$render.='<input type="text" id="'.$idTextFixed.'" name="'.$idTextFixed.'" '.
		'value="'.$valueFixed.'" class="textBox" />';
	$render.="</input>\n";
	$render.="</div>\n";
	return $render;
}

function RenderCombo($id,$options,$selected){
	$idFixed=htmlentities($id,ENT_HTML5, "UTF-8");
	$render='<div class="fieldContent">'."\n";
	$render.='<select id="'.$idFixed.'" name="'.$idFixed.'" '.
		'class="combo">'."\n";
	foreach ($options as $key => $value) {
		$keyFixed=htmlentities($key,ENT_HTML5, "UTF-8");
		$valueFixed=htmlentities($value,ENT_HTML5, "UTF-8");
		if($value==$selected){
			$render.='<option value="'.$valueFixed.
				'" title="'.$valueFixed.'" selected >'.
				$keyFixed."</option>/n";
		}else{
			$render.='<option value="'.$valueFixed.
				'" title="'.$valueFixed.'">'.
				$keyFixed."</option>/n";
		}
	}
	$render.="</select>\n";
	$render.="</div>\n";
	return $render;
}

function RenderButton($id,$value){
	$idFixed=htmlentities($id,ENT_HTML5, "UTF-8");
	$valueFixed=htmlentities($value,ENT_HTML5, "UTF-8");
	return '<input type="submit" value="'.$valueFixed.'" '.
		'id="'.$idFixed.'" name="'.$idFixed.'" class="button" />';
}

function RenderFieldInfo($text,$info){
	$render='<div class="field">'."\n";
	$render.=RenderLabel($text);
	$render.=RenderInfo($info);
	$render.='</div>'."\n";
	return $render;
}

function RenderFieldText($text,$idText,$value){
	$render='<div class="field">'."\n";
	$render.=RenderLabel($text);
	$render.=RenderText($idText,$value);
	$render.='</div>'."\n";
	return $render;
}

function RenderFieldCheck($text,$idCheck,$checked,$value){
	$render='<div class="field">'."\n";
	$render.=RenderLabel($text);
	$render.=RenderCheck($idCheck,$checked,$value);
	$render.='</div>'."\n";
	return $render;
}

function RenderFieldCheckText($text,$idCheck,$checked,$idText,$value){
	$render='<div class="field">'."\n";
	$render.=RenderLabel($text);
	$render.=RenderCheckText($idCheck,$checked,$idText,$value);
	$render.='</div>'."\n";
	return $render;
}

function RenderFieldCombo($text,$idCombo,$options,$selected){
	$render='<div class="field">'."\n";
	$render.=RenderLabel($text);
	$render.=RenderCombo($idCombo,$options,$selected);
	$render.='</div>'."\n";
	return $render;
}

function RenderFieldButton($text,$idButton,$value){
	$render='<div class="field">'."\n";
	$render.=RenderLabel($text);
	$render.=RenderButton($idButton,$value);
	$render.='</div>'."\n";
	return $render;
}


function RenderParagraph($text,$style=null){
	if($text==null  || $text==""){ return ""; }
	$textFixed=htmlentities($text,ENT_HTML5, "UTF-8");
	if($style==null){
		return "<p>".$textFixed."</p>\n";
	}else{
		return "<p style=\"".$style."\">".$textFixed."</p>\n";
	}
}


