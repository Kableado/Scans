<?php

include_once "config.php";
include_once "utils.php";
include_once "ui.php";
include_once "console.php";
include_once "scanner.php";

include_once "multilang.php";
MultiLang::LoadFile("ui");

include_once "timingStatistics.php";
$timings=new TimingStatistics();

function RenderDocument($filePath){
	$render="";
	if($filePath!=null){
		$filePathFixed=htmlentities($filePath,ENT_HTML5, "UTF-8");
		$render.='<iframe src="'.$filePathFixed.'" '.
			'class="previewDoc" ></iframe>';
	}else{
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
$Prefix=RequestParm("txtPrefix",$Prefix);

$CurrentKey=$Scanner["ScanDevice"]."_".$Resolution."_".$Format."_".$Size;

// Preprocess
$DestFile=null;
if(RequestParm("btnScan",false)){
	$timeThen=time()+microtime();
	CleanUp();
	$baseName=$Prefix."-".date("Y-m-d_H_i_s");
	$DestFile=Scan($Scanner["ScanDevice"],$Resolution,$Format,$Size,$baseName);
	if($Crop){
		CropImage($DestFile);
	}
	MoveToDest($DestFile);
	$timeNow=time()+microtime();
	$timings->RegisterTiming($CurrentKey,$timeNow-$timeThen);
}

// Pass the JSON object of timings to client
echo "<script>\n";
echo "var timings=";
echo json_encode($timings->GetTimingsAverage(),JSON_PRETTY_PRINT);
echo ";\n";
echo "</script>\n";


// Render Form
$formFields="";
$formFields.=RenderFieldInfo(MultiLang::GetString("Scanner"),$Scanner["ScanModel"]);
$formFields.=RenderFieldCombo(MultiLang::GetString("Resolution"),"ddlResolution",$Resolutions,$Resolution);
$formFields.=RenderFieldCombo(MultiLang::GetString("Format"),"ddlFormat",$Formats,$Format);
$formFields.=RenderFieldCombo(MultiLang::GetString("Size"),"ddlSize",MultiLang::ApplyArrayKeys($Sizes),$Size);
//$formFields.=RenderFieldCheckText("Cropping","chkCrop",$Crop,"txtCropFuzz",$CropFuzz);
$formFields.=RenderFieldText(MultiLang::GetString("Prefix"),"txtPrefix",$Prefix);
$formFields.=RenderFieldButton("","btnScan",MultiLang::GetString("Scan"),"ShowProgressDialog();");
$formFields.=RenderFieldLinkButton("","btnDownload",MultiLang::GetString("Download"),$DestFile,pathinfo($DestFile)["basename"],"");
$formFields.=RenderHidden("hidScanDevice",$Scanner["ScanDevice"]);
$formFields.=RenderHidden("hidScanModel",$Scanner["ScanModel"]);
$columns="";
$columns.=renderDiv("divColLeft",$formFields);
$columns.=renderDiv("divColRight",RenderDocument($DestFile));
$columns.=RenderCommandLog();
$columns.=RenderDiv("divLoadBack",
		RenderDiv("divLoading",
			MultiLang::GetString("Loading").
			RenderDiv("divProgressCont",RenderDiv("divProgressBar","","divProgressBar"),"divProgressCont"),
			"divLoading"),
	"divLoadBack","display:none;");
echo RenderForm("frmMain",$columns);


