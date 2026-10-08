<?php
function preprocess($newickstring, $newicksize){
	$newick=$newickstring;
	$newicknum=$newicksize;
	// If more than one line; like clustalw
	//////////////////////////////////////////////////////////////
	/////////////////////FIRST PREPROCESS/////////////////////////
	//////////////////////////////////////////////////////////////
	$line="";
	for($i=0;$i<$newicknum;$i++){
		$newick[$i]=trim($newick[$i]);
		$newick[$i]=htmlentities($newick[$i]);
		$line.=$newick[$i];
	}

	//////////////////////////////////////////////////////////////
	/////////////////////SECOND PREPROCESS/////////////////////////
	//////////////////////////////////////////////////////////////
	//remove parenthesis on names
	$patternparnocomma="/\(([^\(\,]*?)\)/i";
	preg_match_all($patternparnocomma,$line,$match);
	//replace with "zifiiix82" so you can replace it back later
	if(isset($match[1])){
		$line=preg_replace($patternparnocomma,"zibiiiiit82".'${1}'."zibiiiiit83",$line);
	}

	//////////////////////////////////////////////////////////////
	/////////////////////THIRD PREPROCESS////////////////////////
	//////////////////////////////////////////////////////////////
	$secflag=0;
	while(true){
		$ifflag=1;
		$pr=0;
		$cm=0;
		$newnum=strlen($line);
		for($i=0;$i<$newnum;$i++){
			if($line[$i]=='('){
				$pr++;
				$prnum=$i+1;
			}
			if($line[$i]==','){
				$cm++;
				$cmnum=$i;
			}
			if($cm>$pr){
				$frompr=substr($line,0,$prnum);
				$middle=substr($line,$prnum,$cmnum-$prnum);
				$tocoma=substr($line,$cmnum);
				$line=$frompr."(".$middle."):0.0".$tocoma;
				$ifflag=0;
				$secflag=1;
				break;
			}
		}
		if($ifflag){
			break;
		}
	}
	//Here we delete the ):0.0
	/*if($secflag){
		$newnum=strlen($line);
		$pr=0;
		$cpr=0;
		for($i=0;$i<$newnum;$i++){
			if($line[$i]=='('){
				$pr++;
				$prnum=$i+1;
			}
			if($line[$i]==')'){
				$cpr++;
				$cprnum=$i;
			}
			if($cpr==$pr-1 & $cpr>0){
				$frompr=substr($line,0,$prnum-1);
				$middle=substr($line,$prnum,$cprnum-$prnum);
				$tocoma=substr($line,$cprnum+5);
				$line=$frompr.$middle.$tocoma;
				break;
			}
		}
	}*/
	//////////////////////////////////////////////////////////////
	/////////////////////FOURTH PREPROCESS/////////////////////////
	//////////////////////////////////////////////////////////////
	if((substr_count($line, ',')*2)==substr_count($line, ':')){
		$line=rtrim($line,";");
		$line="$line:0.0;";
	}
	//////////////////////////////////////////////////////////////
	/////////////////////FIFTH PREPROCESS////////////////////////
	//////////////////////////////////////////////////////////////
	//$nochars=array("?", "*", "+", "\\", "^", "$", "[", "]", "{", "}", "|", "/");
	$noparenthesis=array(", (", ",\t(");
	//characters to be replaced to prevent block pattern matching
	//$line=str_replace($nochars, "", $line);
	$line=str_replace($noparenthesis, ",(" ,$line);
	
	//////////////////////////////////////////////////////////////
	/////////////////////////////////////////////
	//////////////////////////////////////////////////////////////
	//remove parenthesis on names
	//$patternparnocomma="/\(([^\(\,]*?)\)/i";
	//preg_match_all($patternparnocomma,$line,$match);
	//replace with "zifiiix82" so you can replace it back later
	//if(isset($match[1])){
	//	$line=preg_replace($patternparnocomma,"zibiiiiit82".'${1}'."zibiiiiit83",$line);
	//}

	return $line;
}
?>
