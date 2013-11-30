<?php

function DrawFieldInfo($text,$info){
	$textFixed=htmlentities($text,ENT_HTML5, "UTF-8");
	$infoFixed=htmlentities($info,ENT_HTML5, "UTF-8");
	echo '<div class="field">'."\n";
	echo '<span class="fieldLabel">'.$textFixed.":</span>\n";
	echo '<div class="fieldText">'.$infoFixed."</div>\n";
	echo '</div>'."\n";
}

function DrawFieldCombo($text,$id,$options,$selected){
	$textFixed=htmlentities($text,ENT_HTML5, "UTF-8");
	$idFixed=htmlentities($id,ENT_HTML5, "UTF-8");
	echo '<div class="field">'."\n";
	echo '<span class="fieldLabel">'.$textFixed.":</span>\n";
	echo '<div class="fieldCombo">'."\n";
	echo '<select id="'.$idFixed.'" name="'.$idFixed.'" '.
		'class="combo">'."\n";
	foreach ($options as $key => $value) {
		$keyFixed=htmlentities($key,ENT_HTML5, "UTF-8");
		$valueFixed=htmlentities($value,ENT_HTML5, "UTF-8");
		if($value==$selected){
			echo '<option value="'.$valueFixed.
				'" title="'.$valueFixed.'" selected >'.
				$keyFixed."</option>/n";
		}else{
			echo '<option value="'.$valueFixed.
				'" title="'.$valueFixed.'">'.
				$keyFixed."</option>/n";
		}
	}
	echo "</select>\n";
	echo "</div>\n";
	echo '</div>'."\n";
}

function DrawFieldText($text,$idText,$value){
	$textFixed=htmlentities($text,ENT_HTML5, "UTF-8");
	$idTextFixed=htmlentities($idText,ENT_HTML5, "UTF-8");
	$valueFixed=htmlentities($value,ENT_HTML5, "UTF-8");
	echo '<div class="field">'."\n";
	echo '<span class="fieldLabel">'.$textFixed.":</span>\n";
	echo '<div class="fieldText">'."\n";
	echo '<input type="text" id="'.$idTextFixed.'" name="'.$idTextFixed.'"'.
		' value="'.$valueFixed.'" '.
		' class="textBox"/>'."\n";
	echo "</div>\n";
	echo '</div>'."\n";
}

function DrawFieldCheck($text,$idCheck,$checked,$value){
	$textFixed=htmlentities($text,ENT_HTML5, "UTF-8");
	$idCheckFixed=htmlentities($idCheck,ENT_HTML5, "UTF-8");
	$valueFixed=htmlentities($idText,ENT_HTML5, "UTF-8");
	echo '<div class="field">'."\n";
	echo '<span class="fieldLabel">'.$textFixed.":</span>\n";
	echo '<div class="fieldCombo">'."\n";
	echo '<input type="checkbox" id="'.$idCheckFixed.'" '.
		'name="'.$idCheckFixed.'" ';
	if($checked){ echo " checked "; }
	echo 'class="check">'.$valueFixed."\n";
	echo "</input>\n";
	echo "</div>\n";
	echo '</div>'."\n";
}

function DrawFieldCheckText($text,$idCheck,$checked,$idText,$value){
	$textFixed=htmlentities($text,ENT_HTML5, "UTF-8");
	$idCheckFixed=htmlentities($idCheck,ENT_HTML5, "UTF-8");
	$idTextFixed=htmlentities($idText,ENT_HTML5, "UTF-8");
	$valueFixed=htmlentities($value,ENT_HTML5, "UTF-8");
	echo '<div class="field">'."\n";
	echo '<span class="fieldLabel">'.$textFixed.":</span>\n";
	echo '<div class="fieldCombo">'."\n";
	echo '<input type="checkbox" id="'.$idCheckFixed.'" '.
		'name="'.$idCheckFixed.'" ';
	if($checked){ echo " checked "; }
	echo 'class="check" />'."\n";
	echo '<input type="text" id="'.$idTextFixed.'" name="'.$idTextFixed.'" '.
		'value="'.$valueFixed.'" class="textBox" />';
	echo "</div>\n";
	echo '</div>'."\n";
}

function DrawButton($text,$id){
	$textFixed=htmlentities($text,ENT_HTML5, "UTF-8");
	$idFixed=htmlentities($id,ENT_HTML5, "UTF-8");
	echo '<input type="submit" value="'.$textFixed.'" '.
		'id="'.$idFixed.'" name="'.$idFixed.'" class="button" />';
}


function RequestParm($name,$defaultValue){
	if(isset($_GET[$name])){
		return $_GET[$name];
	}
	if(isset($_POST[$name])){
		return $_POST[$name];
	}
	return $defaultValue;
}


?>