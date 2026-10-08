<?php
include "prep.php";
function leafnames($newick){
		
	//$newick=file("$newickfile");
	//$protexistnum=count($protexist);

	$newicknum=count($newick);
	$line=preprocess($newick,$newicknum);

	$pattern="/((.*?):(.*?)[,\(\);])/i";
	$patternpar="/\)(.*?):.*/i";
	$c=0;
	$b=0;
	$vi=0;

	//here finds all
	preg_match_all($pattern,$line,$match);
	$matchnum=count($match[1]);


	//this is for identifying  )Bacteria:0.15 types
	$matchnum2=$matchnum-1;
	$patend="/\)$/i";
	for($h=0;$h<$matchnum2;$h++){
		if (preg_match($patend,$match[1][$h],$rand)){
			$plusone=$h+1;
			$match[1][$plusone]=")".$match[1][$plusone];
		}
	}
	$cnter=0;
	//fill an array with 0,1,2,3... for use in the "for" following
	for($i=0;$i<$matchnum;$i++){
		$tobecut[$i]=$i;
	}
	//here divides nodes and leaves
	for($i=0;$i<$matchnum;$i++){
		$match[2][$i]=trim($match[2][$i],"()");
		if(preg_match($patternpar,$match[1][$i],$matchpar)){
			$matchpar[0]=trim($matchpar[0],"(,);");
			//here creates the array for finding all distances
			$minvi=$i-$vi;
			$minone=$minvi-1;
			$mintwo=$minvi-2;
			$pointarrayzero[$b]="$tobecut[$minvi]";
			$pointarrayone[$b]="$tobecut[$minone]";
			$pointarraytwo[$b]="$tobecut[$mintwo]";
			array_splice($tobecut, $mintwo, 2);
			$b++;
			$vi=$vi+2;
			$nodearray[$i]=$match[3][$i];
		}
		else{
			$match[1][$i]=trim($match[1][$i],"(,);");
			//here makes an array with the numbers of each leaf on the array key and the values of each key
			$leafarray[$i]=$match[3][$i];
			$c++;
			//edw gurizei ta names
			$namestoreturn[$cnter]=$match[2][$i];
			$cnter++;
		}
	}
	$pointarraynum=count($pointarrayzero);
	$leafarraynum=count($leafarray);
	//return the names, delete the null values

	
	return $namestoreturn;
}
?>
