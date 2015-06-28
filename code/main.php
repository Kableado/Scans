<?php

include_once "config.php";
include_once "utils.php";
include_once "ui.php";
include_once "console.php";
include_once "scanner.php";

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
$formFields.=RenderFieldButton("","btnScan","Scan","var elem=document.getElementById('divLoadBack');elem.style.display='';");
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
$columns.=RenderDiv("divLoadBack",RenderDiv("divLoading","Loading","divLoading"),"divLoadBack","display:none;");
echo RenderForm("frmMain",$columns);


