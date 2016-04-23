<?php

/////////
// System

// Executable paths
$ScanImage    = "/usr/bin/scanimage";
$PNMtoJPEG    = "/usr/bin/pnmtojpeg";
$PNMtoPNG     = "/usr/bin/pnmtopng";
$ImageMagik   = "/usr/bin/convert";

// Destination dirs
$PreviewDir   = "temp/";
$FinalDestDir = "/store/Escaneos/";

/////////
// Options

// Resolution
$Resolutions=array(
	"100 DPI"=>100,
	"150 DPI"=>150,
	"200 DPI"=>200,
	"300 DPI"=>300,
	"600 DPI"=>600
);
$Resolution=200;

// Formats
$Formats=array(
	"PNG"=>"png",
	"JPEG/JPG"=>"jpg",
	"PDF"=>"pdf"
);
$Format="pdf";

// Size
$Sizes=array(
	"Full"=>"Full",
	"A4"=>"A4",
	"A5 Portrait"=>"A5Port",
	"A5 Landscape"=>"A5Land",
	"Letter"=>"Letter"
);
$Size="A4";

// Cropping
$Crop=false;
$CropFuzz=50;

// Prefix
$Prefix="Scan";
