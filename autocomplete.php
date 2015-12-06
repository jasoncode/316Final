<?php

    $term = trim($_GET['term']);
    $return_arr = array();

	$db = new SQLite3('us_congress.sqlite');
	$sql = "SELECT (first_name || ' ' || last_name) AS FullName FROM persons WHERE first_name LIKE '".$term."%' OR last_name LIKE '".$term."%' ";

	$result = $db->query($sql);

		while ($row = $result->fetchArray(SQLITE3_ASSOC)){
  			array_push($return_arr, $row['FullName']);
		}	

echo json_encode($return_arr);

unset($db);

?>