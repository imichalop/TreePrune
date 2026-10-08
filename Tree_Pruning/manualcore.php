<?php
function treetrim($newickfile, $protexist, $delp){
	include_once "prep.php";
		
	$newick=file("$newickfile");
	$protexistnum=count($protexist);
	
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
		}
	}
	$pointarraynum=count($pointarrayzero);
	$leafarraynum=count($leafarray);
	
	/////////////////////////////////////////////////////////////////////
	//MAIN PREPROCESS FOR LASHPRUNE//////////////////////////////////////
	/////////////////////////////////////////////////////////////////////
	$keysarrayleaf=array_keys($leafarray);
	if($delp=="r"){
		if($protexistnum<2){
			exit("Need 2 or more leaves for \"remain\" function. Currenly: $protexistnum");
		}
		$cmc=0;
		for($g=0;$g<$leafarraynum;$g++){
			$match[2][$keysarrayleaf[$g]]=trim($match[2][$keysarrayleaf[$g]],"()");
			$oleflag=0;
			$innerflag=1;
			for($v=0;$v<$protexistnum;$v++){
				if($protexist[$v]==$match[2][$keysarrayleaf[$g]] || $protexist[$v]==trim($match[2][$keysarrayleaf[$g]],"'")){
					$innerflag=0;
				}
			}
			if($innerflag){
				if($match[2][$keysarrayleaf[$g]]!=NULL){
					$oleflag=1;	
				}
			}
			if($oleflag){
				$olemagic[$cmc]=$keysarrayleaf[$g];
				$cmc++;
			}
		}
	}
	else if($delp=="d"){
		$cmc=0;
		for($g=0;$g<$leafarraynum;$g++){
			$match[2][$keysarrayleaf[$g]]=trim($match[2][$keysarrayleaf[$g]],"()");
			for($v=0;$v<$protexistnum;$v++){
				if($protexist[$v]==$match[2][$keysarrayleaf[$g]] || $protexist[$v]==trim($match[2][$keysarrayleaf[$g]],"'")){
					$olemagic[$cmc]=$keysarrayleaf[$g];
					$cmc++;
				}
			}
		}
	}
	if(isset($olemagic)){
		$olemagicnum=count($olemagic);
	}else{
		$olemagicnum=0;
		exit("Nothing to prune.");
	}

	for($tri=0;$tri<$olemagicnum;$tri++){
		$unique=NULL;
		//sort arrays from lower to higher and their keys
		asort($leafarray);
		asort($nodearray);
		//here we get only the keys after asort in order to use them in for
		$nodekeys=array_keys($nodearray);
		$nodekeysnum=count($nodekeys);
		$leafkeys=array_keys($leafarray);
		$leafkeysnum=count($leafkeys);
		$flipone=array_flip($pointarrayone);
		$fliptwo=array_flip($pointarraytwo);
		$magic=$olemagic[$tri];
		//
		//Here we change arrays for repeats pointarray,leafarray,nodearray,match[3]
		
		$alpha=1;
		while($alpha){
			if(isset($flipone[$magic])){
				$newadd=$match[3][$pointarrayzero[$flipone[$magic]]]+$match[3][$pointarraytwo[$flipone[$magic]]];
				$old=$pointarrayzero[$flipone[$magic]];
				$change=$pointarraytwo[$flipone[$magic]];
				$newnodeonly=$match[3][$pointarrayzero[$flipone[$magic]]];
				array_splice($pointarrayzero, $flipone[$magic], 1);
				array_splice($pointarrayone, $flipone[$magic], 1);
				array_splice($pointarraytwo, $flipone[$magic], 1);
				$pointarraynum--;
				break;
			}
			else if(isset($fliptwo[$magic])){
				$newadd=$match[3][$pointarrayzero[$fliptwo[$magic]]]+$match[3][$pointarrayone[$fliptwo[$magic]]];
				$old=$pointarrayzero[$fliptwo[$magic]];
				$change=$pointarrayone[$fliptwo[$magic]];
				$newnodeonly=$match[3][$pointarrayzero[$fliptwo[$magic]]];
				array_splice($pointarrayzero, $fliptwo[$magic], 1);
				array_splice($pointarrayone, $fliptwo[$magic], 1);
				array_splice($pointarraytwo, $fliptwo[$magic], 1);
				$pointarraynum--;
				break;
			}
		}
		//Here we cant use index cos flipone and fliptwo change
		for($e=0;$e<$pointarraynum;$e++){

			if($pointarrayone[$e]==$old){
				$pointarrayone[$e]=$change;
				break;
			}
			elseif($pointarraytwo[$e]==$old){
				$pointarraytwo[$e]=$change;
				break;
			}
		}
		//now to delete leaf from leafarray 
		unset($leafarray[$magic]);
		//and here to replace the other leaf with the new one (leaf+node)
		if(array_key_exists($change,$leafarray)){
			$leafarray[$change]=$newadd;
		}
		$leafarraynum--;
		unset($nodearray[$old]);
		unset($match[3][$magic]);
		unset($match[3][$old]);
		$match[3][$change]=$newadd;
	}

	$newick=NULL;
	$c=-1;
	//for single quotes, removes them if there is no violation
	//does not removes or intruduces them if there is violation
	$patternautakia="/^'|'$/i";
	for($e=0;$e<$pointarraynum;$e++){
 			//for autakia
                        if (strpos($match[2][$pointarraytwo[$e]], 'zibiiiiit82') !== FALSE) {
                                if(!preg_match($patternautakia,$match[2][$pointarraytwo[$e]],$matchautakia)){
                                        $match[2][$pointarraytwo[$e]]="'".$match[2][$pointarraytwo[$e]]."'";
                                }
                        }else if(preg_match($patternautakia,$match[2][$pointarraytwo[$e]],$matchautakia)){
                                $match[2][$pointarraytwo[$e]]=trim($match[2][$pointarraytwo[$e]],"'");
                        }
			//for autakia
                        if (strpos($match[2][$pointarrayone[$e]], 'zibiiiiit82') !== FALSE) {
                                if(!preg_match($patternautakia,$match[2][$pointarrayone[$e]],$matchautakia)){
                                        $match[2][$pointarrayone[$e]]="'".$match[2][$pointarrayone[$e]]."'";
                                }
                        }else if(preg_match($patternautakia,$match[2][$pointarrayone[$e]],$matchautakia)){
                                $match[2][$pointarrayone[$e]]=trim($match[2][$pointarrayone[$e]],"'");
                        }
		if(array_key_exists($pointarrayone[$e],$leafarray) && array_key_exists($pointarraytwo[$e],$leafarray)){
			$c++;
			$newick[$c]="(".$match[2][$pointarraytwo[$e]].":".$match[3][$pointarraytwo[$e]].",".$match[2][$pointarrayone[$e]].":".$match[3][$pointarrayone[$e]]."):".$match[3][$pointarrayzero[$e]];
			$newickkey[$c]=$pointarrayzero[$e];
		}
		else if(array_key_exists($pointarrayone[$e],$leafarray)){
			$thecis=array_search($pointarraytwo[$e],$newickkey);
			$newick[$thecis]="(".$newick[$thecis].",".$match[2][$pointarrayone[$e]].":".$match[3][$pointarrayone[$e]]."):".$match[3][$pointarrayzero[$e]];
			$newickkey[$thecis]=$pointarrayzero[$e];
		}
		else if(array_key_exists($pointarraytwo[$e],$leafarray)){
			$thecis=array_search($pointarrayone[$e],$newickkey);
			$newick[$thecis]="(".$match[2][$pointarraytwo[$e]].":".$match[3][$pointarraytwo[$e]].",".$newick[$thecis]."):".$match[3][$pointarrayzero[$e]];
			$newickkey[$thecis]=$pointarrayzero[$e];
		}
		else{
			$inone=array_search($pointarrayone[$e],$newickkey);
			$intwo=array_search($pointarraytwo[$e],$newickkey);
			//intwo is always smaller
			$newick[$intwo]="(".$newick[$intwo].",".$newick[$inone]."):".$match[3][$pointarrayzero[$e]];
			unset($newick[$inone]);
			$newickkey[$intwo]=$pointarrayzero[$e];
			unset($newickkey[$inone]);
		}
	}
	//replace zibiiiiit82,83
	$newick[0]=str_replace("zibiiiiit82","(",$newick[0]);
	$newick[0]=str_replace("zibiiiiit83",")",$newick[0]);
	$new="(".$newick[0].");";
	return $new;
}
?>
