<?php

//$kek = leaftorootnodes("Latent_TS_Seurated_56_56_fava_upgma_ENSG_no_obsoletes.new");
/*$kek = leaftorootnodes($argv[1]);
$keknum = count($kek);
for($i=0;$i<$keknum;$i++){
    echo "$kek[$i]\n";
}*/

function leaftorootnodes($newickfile){
	//require "prep.php";
	
	$newick=file("$newickfile");
	$newicknum=count($newick);
	
	$line=preprocess($newick,$newicknum);
	$exp=explode(",",$line);
	$expnum=count($exp);
	
	$patternopenpar="/\(/i";
	$patternclosepar="/.*?\)/i";
	
	$parenthesissum=array("");
	
	//count leaf to root nodes
	for($j=0;$j<$expnum;$j++){
		$parenthesissum[$j]=0;
		if(preg_match_all($patternclosepar,$exp[$j],$matchclose)){
			$parenthesissum[$j]=$parenthesissum[$j]-count($matchclose[0]);
		}
		if(preg_match_all($patternopenpar,$exp[$j],$matchopen)){
			$parenthesissum[$j]=$parenthesissum[$j]+count($matchopen[0]);
		}
		$leaftorootnodelength=0;
		for($i=count($parenthesissum);$i>=0;$i--){
			if($i==count($parenthesissum) && $parenthesissum[$i-1]<0){
				//avoids first node closeparenthesis
			}else{
				@$leaftorootnodelength+=$parenthesissum[$i-1];
			}
		}
		//echo "$j\t$leaftorootnodelength\n";
		$nodearr[$j] = $leaftorootnodelength;
	}
	return $nodearr;
}
?>
