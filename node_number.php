<?php

function nodenumber($newickfile,$leafname,$nodenum){

        $newick=file("$newickfile");
        $newicknum=count($newick);

        $line=preprocess($newick,$newicknum);
        $exp=explode(",",$line);
        $expnum=count($exp);
        //print_r($exp);
        $pattern="/$leafname/i";
        $patternopenpar="/\(/i";
        $patternclosepar="/.*?\)/i";
        $patternopenparleaf="/[^\(]+/i";
        $patternnames="/([A-Z][^,:]*)/i";
        $patterndoublecomma="/\,\,/i";
        $cnt=0;
        $parenthesissum=0;
        //the list
        $list="";
        $reconstruct=NULL;
        //the implode number is need to identify where to stop
        $impnumber=0;

        for($i=0;$i<$expnum;$i++){
                if(preg_match($pattern,$exp[$i])){
                        #parenthesis close
                        for($j=$i;$j<$expnum;$j++){
                                if(preg_match_all($patternclosepar,$exp[$j],$matchclose)){
                                        $parenthesissum=$parenthesissum+count($matchclose[0]);
                                }
                                if(preg_match_all($patternopenpar,$exp[$j],$matchopen)){
                                        $parenthesissum=$parenthesissum-count($matchopen[0]);
                                }
                                //for the first one if you have many open parenthesis
                                if($j==$i && $parenthesissum<=0){
                                        $parenthesissum=0;
                                }
                                if($parenthesissum>=$nodenum){
                                        //where to stop
                                        //add comma
                                        $reconstruct=$reconstruct.",";
                                        for($concat=0;$concat<$nodenum-$impnumber;$concat++){
                                                $reconstruct=$reconstruct.$matchclose[0][$concat];
                                        }
                                        $parenthesissum=$nodenum;
                                        break;
                                }else{
                                        //diff than 0 to avoid showing the first one with open parenthesis
                                        if($j==$i && $parenthesissum==0){
                                        }else{
                                                $reconstruct=$reconstruct.",".$exp[$j];
                                                $impnumber=$parenthesissum;
                                        }
                                }
                        }
                        $flagenter=0;
                        #parenthesis open
                        #-1 because the first has been read from the previous for
                        for($j=$i;$j<$expnum;$j--){

                                if(preg_match_all($patternopenpar,$exp[$j],$matchopen)){
                                        $parenthesissum=$parenthesissum-count($matchopen[0]);
                                        preg_match($patternopenparleaf,$exp[$j],$matchleaf);
                                }
                                else{
                                        if(preg_match_all($patternclosepar,$exp[$j],$matchclose)){
                                                //to avoid re-reading the first line
                                                if($j==$i && $parenthesissum>0){
                                                        continue;
                                                }
                                                $parenthesissum=$parenthesissum+count($matchclose[0]);
                                        }
                                }
                                if($parenthesissum<=0){
                                        //add remain leaf
                                        $reconstruct=$matchleaf[0].",".$reconstruct;
                                        //add extra parenthesis
                                        if(!$flagenter){
                                                $impnumber=$nodenum;
                                        }
                                        for($concat=0;$concat<$impnumber;$concat++){
                                                $reconstruct="(".$reconstruct;
                                        }
                                        break;
                                }else{
                                        //to increase impnumber for the above if
                                        $flagenter=1;
                                        $reconstruct=$exp[$j].",".$reconstruct;
                                        $impnumber=$parenthesissum;
                                }
                        }
                        $reconstruct=$reconstruct.";";
                        $reconstruct=preg_replace($patterndoublecomma,",",$reconstruct);
                        break;
                }
        }
        return $reconstruct;
}
