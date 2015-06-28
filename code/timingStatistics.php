<?php

class TimingStatistics{
	private $filename="temp/timingStatistics.json";
	private $values=null;
	
	private function InitValues(){
		$this->values=array();
	}
	
	private function LoadValues(){
		if($this->values!=null){return;}
		if(!is_file($this->filename)){ 
			$this->InitValues();
			return;
		}
		$overviewContents=@file_get_contents($this->filename);
		if($overviewContents===false){ 
			$this->InitValues();
			return;
		}
		$this->values=json_decode($overviewContents,true);
		if($this->values==null){
			$this->InitValues();
			return;
		}
	}
	
	private function SaveValues(){
		if($this->values==null){return;}
			
		$valuesContent=json_encode($this->values,JSON_PRETTY_PRINT);
		file_put_contents($this->filename,$valuesContent);
	}
	
	public function RegisterTiming($keyName,$time){
		$this->LoadValues();
		if(isset($this->values[$keyName])==false){
			$this->values[$keyName]=array();
		}
		$this->values[$keyName][]=$time;
		if(count($this->values[$keyName])>5){
			unset($this->values[$keyName][0]);
		}
		$this->SaveValues();
	}
	
	public function GetTimingsAverage(){
		$this->LoadValues();
		$result=array();
		foreach ($this->values as $key => $value) {
			$totalTime=0;
			$totalCount=0;
			foreach ($value as $time) {
				$totalTime=$totalTime+$time;
				$totalCount=$totalCount+1;
			}
			$result[$key]=($totalTime/$totalCount);
		}
		return $result;
	}
}
