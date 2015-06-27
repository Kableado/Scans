<?php

$Commands=array();

function ExecCommand($command){
	global $Commands;
	$loggedCommand=array();
	$loggedCommand["Command"]=$command;
	list($returnCode, $stdout, $stderr)=ExecFull($command);
	$loggedCommand["Result"]=$stdout;
	$loggedCommand["Error"]=$stderr;
	$loggedCommand["ReturnCode"]=$returnCode;
	$Commands[]=$loggedCommand;
	return $stdout;
}

function RenderCommandLog(){
	global $Commands;
	$renderLog="";
	foreach($Commands as $loggedCommand){
		$renderLog.=RenderParagraph($loggedCommand["Command"],"command");
		$renderLog.=RenderParagraph($loggedCommand["Result"]);
		$renderLog.=RenderParagraph($loggedCommand["Error"],"error");
	}
	
	return RenderDiv("divConsoleContainer",
		RenderButton("btnToggle","Log","var elem=document.getElementById('divConsole');if(elem.style.display==''){elem.style.display='none';}else{elem.style.display='';}return false;").
		RenderDiv("divConsole",$renderLog,null,"display:none;")
	);
}
