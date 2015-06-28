<?php

include_once "config.php";
include_once "utils.php";
include_once "ui.php";
include_once "console.php";
include_once "scanner.php";

include_once "multilang.php";
MultiLang::LoadFile("ui");


function RenderDocument($filePath){
	$render="";
	if($filePath!=null){
		$filename=pathinfo($filePath)["basename"];
		$filePathFixed=htmlentities($filePath,ENT_HTML5, "UTF-8");
		$filenameFixed=htmlentities($filename,ENT_HTML5, "UTF-8");
		$render.='<div><a href="'.$filePathFixed.'" class="button" download="'.$filenameFixed.'">'.
			MultiLang::GetString("Download").'</a></div>'."\n";
		$render.='<iframe src="'.$filePathFixed.'" '.
			'class="previewDoc" ></iframe>';
	}else{
		$render.='<div><button class="button" disabled="disabled">'.
			MultiLang::GetString("Download").'</button></div>'."\n";
		$render.='<iframe src="about:blank" '.
			'class="previewDoc" ></iframe>';
	}
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
$formFields.=RenderFieldInfo(MultiLang::GetString("Scanner"),$Scanner["ScanModel"]);
$formFields.=RenderFieldCombo(MultiLang::GetString("Resolution"),"ddlResolution",$Resolutions,$Resolution);
$formFields.=RenderFieldCombo(MultiLang::GetString("Format"),"ddlFormat",$Formats,$Format);
$formFields.=RenderFieldCombo(MultiLang::GetString("Size"),"ddlSize",$Sizes,$Size);
//$formFields.=RenderFieldCheckText("Cropping","chkCrop",$Crop,"txtCropFuzz",$CropFuzz);
$formFields.=RenderFieldButton("","btnScan",MultiLang::GetString("Scan"),"Element_SetVisibility('divLoadBack',true);");
$formFields.=RenderHidden("hidScanDevice",$Scanner["ScanDevice"]);
$formFields.=RenderHidden("hidScanModel",$Scanner["ScanModel"]);
$columns="";
$columns.=renderDiv("divColLeft",$formFields);
$columns.=renderDiv("divColRight",RenderDocument($DestFile));
$columns.=RenderCommandLog();
$columns.=RenderDiv("divLoadBack",RenderDiv("divLoading",MultiLang::GetString("Loading"),"divLoading"),"divLoadBack","display:none;");
echo RenderForm("frmMain",$columns);


