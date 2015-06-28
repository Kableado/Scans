<?php

######################################################
##        Copyright (c) 2015 Valeriano Alfonso      ##
######################################################


################################
# MultiLang
#
class MultiLang {
	public static $defaultLang="en";

	private static $literals=array();
	public static function GetString($string){
		if(!isset(self::$literals[$string])) { return $string; }
		return self::$literals[$string];
	}

	private static function LoadFileLang($file,$lang){
		$fullFilename="literals/".$file.".".$lang.".json";
		if(file_exists($fullFilename)){
			$fileContents=@file_get_contents($fullFilename);
			$newLiterals=json_decode($fileContents,true);
			if(is_array($newLiterals)){
				self::$literals=array_merge(self::$literals,$newLiterals);
				return true;
			}
		}
		return false;
	}

	public static function LoadFile($file="text"){
		$lang=self::GetUserLang();
		if(!self::LoadFileLang($file,$lang)){
			self::LoadFileLang($file,self::$defaultLang);
		}
	}

	private static $lang=null;

	public static function GetUserLang(){
		if(self::$lang!=null){ return self::$lang; }
		$lang = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : "";
		if ($lang == "") {
			if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
				$lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
			} else {
				$lang = self::$defaultLang;
			}
		}
		$lang = substr($lang, 0, 2);
		self::$lang = $lang;
		return $lang;
	}

}
################################
