<?php

    $term = trim($_GET['term']);
    $return_arr = array();

	$db = pg_connect("dbname=us_congress host=localhost user=postgres password=Bd3nM2!Vg27aJ!0")
    				or die('Could not connect: ' . pg_last_error());

	$sql = "SELECT (first_name || ' ' || last_name) AS FullName FROM persons WHERE first_name ILIKE '".$term."%' OR last_name ILIKE '".$term."%' ";

	$result = pg_query($db, $sql) or die('Query failed: ' . pg_last_error());

		while ($line = pg_fetch_row($result)) {
    				array_push($return_arr, $line[0]);
			}

	pg_free_result($result);

echo json_encode($return_arr);

pg_close($db);

?>
