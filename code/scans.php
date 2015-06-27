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
		$renderLog.=RenderParagraph($loggedCommand["Command"],"command");
		$renderLog.=RenderParagraph($loggedCommand["Result"]);
		$renderLog.=RenderParagraph($loggedCommand["Error"],"error");
	}
	
	return RenderDiv("divConsoleContainer",
		RenderButton("btnToggle","Log","var elem=document.getElementById('divConsole');if(elem.style.display==''){elem.style.display='none';}else{elem.style.display='';}return false;").
		RenderDiv("divConsole",$renderLog,null,"display:none;")
	);
}


function ScannerDetect(){
	global $ScanImage;
	$Command=$ScanImage." -f\"%d|%v %m%n\"";
	$scansResults=ExecCommand($Command);
	$devicesResults=explode("\n",$scansResults);
	list($ScannerDevice,$ScannerModel)=explode("|",$devicesResults[0]);
	
	$Scanner=array();
	$Scanner["ScanDevice"]=$ScannerDevice;
	$Scanner["ScanModel"]=$ScannerModel;

	return $Scanner;
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


function RenderDocument($filePath){
	$filename=pathinfo($filePath)["basename"];
	$filePathFixed=htmlentities($filePath,ENT_HTML5, "UTF-8");
	$filenameFixed=htmlentities($filename,ENT_HTML5, "UTF-8");
	$render="";
	$render.='<div><a href="'.$filePathFixed.'" class="button" download="'.$filenameFixed.'">'.
		'Download</a></div>'."\n";
	$render.='<iframe src="'.$filePathFixed.'" '.
		'class="previewDoc" ></iframe>';
	return $render;
}


// Detect scanner
$Scanner=array();
$Scanner["ScanDevice"]=RequestParm("hidScanDevice","");
$Scanner["ScanModel"]=RequestParm("hidScanModel","");
if($Scanner["ScanDevice"]=="" || $Scanner["ScanModel"]==""){
	$Scanner=ScannerDetect();
}

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
	$DestFile=Scan($Scanner["ScanDevice"],$Resolution,$Format,$Size,$baseName);
	if($Crop){
		CropImage($DestFile);
	}
	MoveToDest($DestFile);
}

// Render Form
$formFields="";
$formFields.=RenderFieldInfo("Scanner",$Scanner["ScanModel"]);
$formFields.=RenderFieldCombo("Resolution","ddlResolution",$Resolutions,$Resolution);
$formFields.=RenderFieldCombo("Format","ddlFormat",$Formats,$Format);
$formFields.=RenderFieldCombo("Size","ddlSize",$Sizes,$Size);
$formFields.=RenderFieldCheckText("Cropping","chkCrop",$Crop,"txtCropFuzz",$CropFuzz);
$formFields.=RenderFieldButton("","btnScan","Scan");
$formFields.=RenderHidden("hidScanDevice",$Scanner["ScanDevice"]);
$formFields.=RenderHidden("hidScanModel",$Scanner["ScanModel"]);
$columns="";
$columns.=renderDiv("divColLeft",$formFields);
$result="";
if($DestFile!=null){
	$result.=RenderDocument($DestFile);
}
$columns.=renderDiv("divColRight",$result);
$columns.=RenderCommandLog();
echo RenderForm("frmMain",$columns);


