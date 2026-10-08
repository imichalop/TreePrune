<?php
error_reporting( E_ALL );
ini_set( "display_errors", 1 );
ini_set('memory_limit','-1');
ini_set('max_execution_time', 0);
include("largest_distance_from_root.php");
include("leafnamestoarr.php");
include("leafnamestoarr_newick.php");
include("node_number.php");
include("count_leaf_to_root_nodes.php");

if($argc==3){
    
    $fh1 = fopen("subtree.new","w");
    $fh2 = fopen("genelist.txt","w");

    //$og_tree = ("tree.new");
    $og_tree = $argv[1];
    $nodes = 5;

    //$ENSG = "ENSG00000206951";
    $ENSG = $argv[2];

    $nodearr = leaftorootnodes($og_tree);
    $leaf_names = leafnames_new($og_tree);
    $leafnum = count($leaf_names);
    for($i=0;$i<$leafnum;$i++){
            $max_node_arr[$leaf_names[$i]] = $nodearr[$i];
    }

    $maxnode = $max_node_arr[$ENSG];

    if($maxnode<=5){
        $nodes = $maxnode - 2;
    }
    ////Here starts the additional code to produce the default tree    
    $node_cut = 25;
    $node_min = $node_cut/5;
    $write_tree = nodenumber($og_tree,$ENSG,$nodes);
    $newick=array($write_tree);
    $leaf_names = leafnames($newick);
    $leafnum = count($leaf_names);
    if($leafnum<$node_cut){
        while($leafnum<$node_cut){
            $nodes++;
            $write_tree = nodenumber($og_tree,$ENSG,$nodes);
            $newick=array($write_tree);
            $leaf_names = leafnames($newick);
            $leafnum = count($leaf_names);
        }
        if($leafnum>$node_cut){
            $nodes--;
        }
        $write_tree = nodenumber($og_tree,$ENSG,$nodes);
        $newick=array($write_tree);
        $leaf_names = leafnames($newick);
        $leafnum = count($leaf_names);
        $nodes2 = $nodes+1;
        $write_tree2 = nodenumber($og_tree,$ENSG,$nodes2);
        $newick2=array($write_tree2);
        $leaf_names2 = leafnames($newick2);
        $leafnum2 =  count($leaf_names2);
        $diff1 = abs($node_cut - $leafnum);
        $diff2 = abs($node_cut - $leafnum2);
    }
    elseif($leafnum>$node_cut){
        while($leafnum>$node_cut && $nodes > 1){
            $nodes--;
            $write_tree = nodenumber($og_tree,$ENSG,$nodes);
            $newick=array($write_tree);
            $leaf_names = leafnames($newick);
            $leafnum = count($leaf_names);
        }
        $write_tree = nodenumber($og_tree,$ENSG,$nodes);
        $newick=array($write_tree);
        $leaf_names = leafnames($newick);
        $leafnum = count($leaf_names);
        $nodes2 = $nodes+1;
        $write_tree2 = nodenumber($og_tree,$ENSG,$nodes2);
        $newick2=array($write_tree2);
        $leaf_names2 = leafnames($newick2);
        $leafnum2 =  count($leaf_names2);
        $diff1 = abs($node_cut - $leafnum);
        $diff2 = abs($node_cut - $leafnum2);
    }

    if($diff1 > $diff2){
        $nodes = $nodes2;
    }

    $write_tree = nodenumber($og_tree,$ENSG,$nodes);
    $newick=array($write_tree);
    $leaf_names = leafnames($newick);
    $leafnum = count($leaf_names);

    if($leafnum <= $node_min){
        $nodes++;
    }
        
    ////Here ends the additional code to produce the default tree
    
    
    //echo "$newick[0]";
    fwrite($fh1,$newick[0]);
    $genes_newline = implode("\n", $leaf_names);
    //echo "$genes_newline";
    fwrite($fh2,$genes_newline);
    fclose($fh1);
    fclose($fh2);
    
}
elseif($argc==4){
    
    $fh1 = fopen("subtree.new","w");
    $fh2 = fopen("genelist.txt","w");

    //$og_tree = ("tree.new");
    $og_tree = $argv[1];
    
    //$ENSG = "ENSG00000206951";
    $ENSG = $argv[2];
    
    $nodes = $argv[3];

    $nodearr = leaftorootnodes($og_tree);
    $leaf_names = leafnames_new($og_tree);
    $leafnum = count($leaf_names);
    for($i=0;$i<$leafnum;$i++){
            $max_node_arr[$leaf_names[$i]] = $nodearr[$i];
    }

    $maxnode = $max_node_arr[$ENSG];

    if($nodes >= $maxnode){
        $nodes = $maxnode-1;
    }

    $n=$nodes;

    /*for($nodes=$n;$nodes>0;$nodes--) {
        $write_tree = nodenumber($og_tree,$ENSG,$nodes);
        $newick=array($write_tree);
        $leaf_names = leafnames($newick);
        $leafnum = count($leaf_names);
    }*/

    $write_tree = nodenumber($og_tree,$ENSG,$nodes);

    $newick=array($write_tree);
    $leaf_names = leafnames($newick);
    $leafnum = count($leaf_names);
    
    fwrite($fh1,$newick[0]);
    $genes_newline = implode("\n", $leaf_names);
    //echo "$genes_newline";
    fwrite($fh2,$genes_newline);
    fclose($fh1);
    fclose($fh2);
    
}
else{
    die("Usage: php $argv[0] <Newick tree file> <ENSG Stable Gene ID> <Node Number>\n");
}
?>
