<?php

error_reporting(0);
$oldErrorHandler = set_error_handler("userErrorHandler");
function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars){
	$strErrorsFile=__DIR__."/../errors.csv";
	$time=date("c");

	# Error level description
	$errorLevels = array (
		1 => "Error",
		2 => "Warning",
		4 => "Parsing Error",
		8 => "Notice",
		16 => "Core Error",
		32 => "Core Warning",
		64 => "Compile Error",
		128 => "Compile Warning",
		256 => "User Error",
		512 => "User Warning",
		1024 => "User Notice",
		2048 => "Strict",
		4096 => "Recoverable error",
		8192 => "Deprecated");
	$errLevel=$errorLevels[$errno];

	# Log the error to the error file
	$errorFile=fopen($strErrorsFile,"a");
	if($errorFile!=false){
		fputs($errorFile,"\"$time\",\"$filename:$linenum\",\"($errno $errLevel) $errmsg\"\n");
		fclose($errorFile);
	}else{
		echo("\"$time\",\"$filename:$linenum\",\"($errno $errLevel) $errmsg\"\n");
	}
	
	if($errno!=2 && $errno!=8 &&
	   $errno!=32 && $errno!=128 &&
	   $errno!=512 && $errno!=1024 &&
	   $errno!=8192)
	{
		# Terminate on fatal errors
		die("A fatal error has occurred. Script execution has been aborted");
	}
}

register_shutdown_function("customError");
function customError(){
	$arrStrErrorInfo = error_get_last();
	if($arrStrErrorInfo!=null){
		userErrorHandler(
			$arrStrErrorInfo["type"],
			$arrStrErrorInfo["message"],
			$arrStrErrorInfo["file"],
			$arrStrErrorInfo["line"],
			null);
	}
}