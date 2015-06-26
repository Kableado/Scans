<?php

include_once "config.php";
include_once "utils.php";
include_once "ui.php";

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
		$renderLog.=RenderParagraph($loggedCommand["Command"],"font-weight: bold;");
		$renderLog.=RenderParagraph($loggedCommand["Result"]);
		$renderLog.=RenderParagraph($loggedCommand["Error"],"color: red;");
	}
	
	return RenderDiv("divConsoleContainer",
		RenderButton("btnToggle","Log","var elem=document.getElementById('divConsole');if(elem.style.display==''){elem.style.display='none';}else{elem.style.display='';}return false;").
		RenderDiv("divConsole",$renderLog,"display:none;")
	);
}

function Scan($device,$resolution,$format,$size,$destFileBase){
	global $PreviewDir;
	global $ScanImage;
	global $PNMtoJPEG;
	global $PNMtoPNG;
	global $PNMtoPS;
	global $PStoPDF;

	$DestFile=$PreviewDir.$destFileBase;
	$Command=$ScanImage." -d ".$device.
		" --resolution ".$resolution."dpi";
		
	// Apply size
	if($size=="A4"){
		$Command.=" -x 210 -y 297";
	}
	if($size=="A5Port"){
		$Command.=" -x 148 -y 210";
	}
	if($size=="A5Land"){
		$Command.=" -x 210 -y 148";
	}
	if($size=="Letter"){
		$Command.=" -x 216 -y 279";
	}
	
	// Apply format
	if($format=="jpg"){
		$DestFile.=".jpg";
		$Command.=" | {$PNMtoJPEG} --quality=100 > ".$DestFile;
		$Scan=ExecCommand($Command);
	}
	if($format=="png"){
		$DestFile.=".png";
		$Command.=" | {$PNMtoPNG} > ".$DestFile;
		$Scan=ExecCommand($Command);
	}
	if($format=="pdf"){
		$DestFile2=$DestFile.".pnm";
		$Command.=" > {$DestFile2}";
		$Scan=ExecCommand($Command);

		$DestFile.=".pdf";
		$Command="cat {$DestFile2} | {$PNMtoPS}";
		if($size=="A4"){
			$Command.=" -width=8.3 -height=11.7 ";
		}
		if($size=="A5Port"){
			$Command.=" -width=5.8 -height=8.3 ";
		}
		if($size=="A5Land"){
			$Command.=" -width=8.3 -height=5.8 ";
		}
		if($size=="Letter"){
			$Command.=" -width=8.5 -height=11 ";
		}
		$Command.=" | {$PStoPDF} - {$DestFile}";
		$Convert=ExecCommand($Command);
	}
	return $DestFile;
}

function CleanUp(){
	global $PreviewDir;
	$Command="rm -rf "
		.$PreviewDir."*.pnm "
		.$PreviewDir."*.png "
		.$PreviewDir."*.jpg "
		.$PreviewDir."*.pdf ";
	$Delete=ExecCommand($Command);
}

function MoveToDest($origFile){
	global $FinalDestDir;
	$destFile=basename($origFile);
	$destFile=$FinalDestDir.$destFile;
	$Command="cp ".$origFile." ".$destFile;
	$Copy=ExecCommand($Command);
}

function CropImage($file){
	global $ImageMagik;
	global $CropFuzz;
	$Command=$ImageMagik." ".$file.' -fuzz '.$CropFuzz.'% -trim '.$file."\n";
	$Cropping=ExecCommand($Command);
}


// Detect scanner
$CMD=$ScanImage." --list-devices | grep device";
$SaneScanner = ExecCommand($CMD);
unset($cmd);
$start=strpos($SaneScanner,"`")+1;
$laenge=strpos($SaneScanner,"'")-$start;
$Scanner = "\"".substr($SaneScanner,$start,$laenge)."\"";
unset($start);
unset($laenge);


// Configure with formdata
$Resolution=RequestParm("ddlResolution",$Resolution);
$Format=RequestParm("ddlFormat",$Format);
$Size=RequestParm("ddlSize",$Size);
$Crop=RequestParm("chkCrop",$Crop)!=false;
$CropFuzz=RequestParm("txtCropFuzz",$CropFuzz);

// Preprocess
$DestFile=null;
if(RequestParm("btnScan",false)){
	CleanUp();
	$baseName="Scan-".date("Y-m-d_H_i_s");
	$DestFile=Scan($Scanner,$Resolution,$Format,$Size,$baseName);
	if($Crop){
		CropImage($DestFile);
	}
	MoveToDest($DestFile);
}


echo '<form id="frmMain" method="GET" action="index.php">'."\n";

// Render header info
echo RenderFieldInfo("Scanner",$SaneScanner);
echo RenderFieldCombo("Resolution","ddlResolution",$Resolutions,$Resolution);
echo RenderFieldCombo("Format","ddlFormat",$Formats,$Format);
echo RenderFieldCombo("Size","ddlSize",$Sizes,$Size);
//echo RenderFieldCheckText("Cropping","chkCrop",$Crop,"txtCropFuzz",$CropFuzz);
echo RenderFieldButton("","btnScan","Scan");
if($DestFile!=null){
	$DestFileFixed=htmlentities($DestFile,ENT_HTML5, "UTF-8");
	echo '<div><a href="'.$DestFileFixed.'">'.
		'Download '.$DestFileFixed.'</a></div>'."\n";
	echo '<div><img alt="preview" src="'.$DestFileFixed.'" '.
		'class="previewImage" /></div>';
}

echo "</form>\n";

echo RenderCommandLog();

