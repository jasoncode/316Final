<?php

    $term = trim($_GET['term']);
    $termSplit = explode(" ", $term);
    $return_arr = array();

	$db = pg_connect("dbname=us_congress host=localhost user=postgres password=kushal941")
    				or die('Could not connect: ' . pg_last_error());

  if(count($termSplit) == 1){
    $sql = "SELECT (first_name || ' ' || last_name) AS FullName FROM persons WHERE first_name ILIKE '%".$term."%' OR last_name ILIKE '%".$term."%' ";
  }
  elseif(count($termSplit) == 2){
    $sql = "SELECT (first_name || ' ' || last_name) AS FullName FROM persons WHERE first_name ILIKE '%".$termSplit[0]."%' AND last_name ILIKE '".$termSplit[1]."%' ";
  }

  $result = pg_query($db, $sql) or die('Query failed: ' . pg_last_error());
	while ($line = pg_fetch_row($result)) {
    				array_push($return_arr, $line[0]);
	}

	pg_free_result($result);

echo json_encode($return_arr);

pg_close($db);

?>
