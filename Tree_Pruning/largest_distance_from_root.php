<?php
function largest_distance_from_root($name){
#read file line by line (less RAM)
$handle = fopen($name, "r");

#output array
$leaf_dist_to_node=array();

if ($handle) {
	while (($buffer = fgets($handle)) !== false) {
		#explode commas => leaves
		$expcomma=explode(",",$buffer);
		$root_max=0;
		foreach($expcomma as $key=>$value){
			#explode distance
			$expsemi=explode(":",$value);

			#save to array sample as key, distance as value
			$expseminum=count($expsemi);
			$root_dist=0;
			for($i=1;$i<$expseminum;$i++){
                                #trim parenthesis and for last number carret and ;
                                $expsemi[$i]=trim($expsemi[$i]);
				$expsemi[$i]=trim($expsemi[$i],";");
				$expsemi[$i]=trim($expsemi[$i],"()");
				$root_dist+=$expsemi[$i];
			}
			if($root_max<$root_dist){
				$root_max=$root_dist;
			}
		}
	}
	if (!feof($handle)) {
		echo "Error: unexpected fgets() fail\n";
	}
	fclose($handle);
}

#echo array
return $root_max;
}
?>
