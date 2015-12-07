<?php


		//get representative 1
		$rep1First = trim($_GET['rep1First']);
		$rep1Last = trim($_GET['rep1Last']);

		//get representative 2
		$rep2First = trim($_GET['rep2First']);
		$rep2Last = trim($_GET['rep2Last']);

		$db = new SQLite3('us_congress.sqlite');

    	$return_arr = array();

    	echo $rep1First;
    	echo $rep1Last;

    	echo $rep2First;
    	echo $rep2Last;

$sql = "SELECT cont(*) AS agreedCount, EXTRACT (YEAR FROM p1votes.date) AS YEAR, (p1votes.first_name || ' ' || p1votes.last_name) AS Person1, (p2votes.first_name || ' ' || p2votes.last_name) AS Person2
			FROM
					(((SELECT first_name, last_name, vote_id, vote
					FROM (persons JOIN person_votes ON persons.id = person_votes.person_id)
					WHERE first_name = '".$rep1First."%' AND last_name = '".$rep1Last."%') p1 JOIN votes ON p1.vote_id = votes.id) p1votes
								JOIN 
									((SELECT first_name, last_name, vote_id, vote
									FROM (persons JOIN person_votes ON persons.id = person_votes.person_id)
									WHERE first_name = '".$rep2First."%' AND last_name = '".$rep2Last."%') p1 JOIN votes ON p1.vote_id = votes.id) p2votes
								ON p1votes.vote_id = p2votes.vote_id AND
								   p1votes.vote = p2votes.vote)
			GROUP BY EXTRACT (YEAR FROM p1votes.date), p1votes.first_name, p1votes.last_name, p2votes.first_name, p2votes.last_name";

			$result = $db->query($sql);

			while ($row = $result->fetchArray(SQLITE3_ASSOC)){
  			array_push($return_arr, $row['agreedCount']);
			}		

			echo json_encode($return_arr);

unset($db);

?>
