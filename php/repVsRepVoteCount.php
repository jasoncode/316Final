<?php


		//get representative 1
		$rep1First = $_POST['rep1First'];
		$rep1Last = $_POST['rep1Last'];

		//get representative 2
		$rep2First = $_POST['rep2First'];
		$rep2Last = $_POST['rep2Last'];

		$dbconn = pg_connect("dbname=us_congress host=localhost user=postgres password=Bd3nM2!Vg27aJ!0")
    				or die('Could not connect: ' . pg_last_error());

    	$agree_arr = array();
    	$disagree_arr = array();
    	$agreeWithPres_arr = array();
    	$disagreeWithPres_arr = array();


$agreeSQL = "SELECT COUNT(*) AS agreedCount,YEAR, Person1, Person2
						FROM
			(SELECT EXTRACT (YEAR FROM p1votes.date) AS YEAR, (p1votes.first_name || ' ' || p1votes.last_name) AS Person1, (p2votes.first_name || ' ' || p2votes.last_name) AS Person2,p1votes.vote_id
			FROM
					(((SELECT first_name, last_name, vote_id, vote
					FROM (persons JOIN person_votes ON persons.id = person_votes.person_id)
					WHERE first_name = '".$rep1First."' AND last_name = '".$rep1Last."') p1 JOIN votes ON p1.vote_id = votes.id) p1votes
								JOIN
									((SELECT first_name, last_name, vote_id, vote
									FROM (persons JOIN person_votes ON persons.id = person_votes.person_id)
									WHERE first_name = '".$rep2First."' AND last_name = '".$rep2Last."') p1 JOIN votes ON p1.vote_id = votes.id) p2votes
								ON p1votes.vote_id = p2votes.vote_id AND
								   p1votes.vote = p2votes.vote)
									 GROUP BY p1votes.first_name, p1votes.last_name, p2votes.first_name, p2votes.last_name,EXTRACT(YEAR FROM p1votes.date), p1votes.vote_id) AS allAgreements


		  JOIN

			(SELECT votes.id, votes.date, relevantVoteIDs.bill_id, relevantVoteIDs.type
					     FROM votes JOIN
							(SELECT bill_id, vote_id, type
			 				FROM votes_re_bills JOIN (
									    SELECT *
									    FROM bills
									    WHERE bills.type = 'hr' OR bills.type = 's' OR bills.type = 'hjres' or bills.type = 'sjres') AS relevantBills
									    ON votes_re_bills.bill_id = relevantBills.id) AS relevantVoteIDs
							ON votes.id = relevantVoteIDs.vote_id) AS relevantVotes

				ON relevantVotes.id = allAgreements.vote_id

			GROUP BY YEAR, Person1, Person2";

			$result1 = pg_query($dbconn, $agreeSQL) or die('Query failed: ' . pg_last_error());

			while ($line = pg_fetch_row($result1)) {
    				array_push($agree_arr, intval($line[0]));
			}

	pg_free_result($result1);

$disagreeSQL = "SELECT COUNT(*) as agreedCount,YEAR, Person1, Person2
						FROM
			(SELECT EXTRACT (YEAR FROM p1votes.date) AS YEAR, (p1votes.first_name || ' ' || p1votes.last_name) AS Person1, (p2votes.first_name || ' ' || p2votes.last_name) AS Person2, p1votes.vote_id
			FROM
					(((SELECT first_name, last_name, vote_id, vote
					FROM (persons JOIN person_votes ON persons.id = person_votes.person_id)
					WHERE first_name = '".$rep1First."' AND last_name = '".$rep1Last."') p1 JOIN votes ON p1.vote_id = votes.id) p1votes
								JOIN
									((SELECT first_name, last_name, vote_id, vote
									FROM (persons JOIN person_votes ON persons.id = person_votes.person_id)
									WHERE first_name = '".$rep2First."' AND last_name = '".$rep2Last."') p1 JOIN votes ON p1.vote_id = votes.id) p2votes
									ON
									p1votes.vote_id = p2votes.vote_id AND
										 p1votes.vote <> p2votes.vote      AND
										 (p1votes.vote <> 'Not Voting' OR
											p2votes.vote <> 'Not Voting'))

									 GROUP BY p1votes.first_name, p1votes.last_name, p2votes.first_name, p2votes.last_name,EXTRACT(YEAR FROM p1votes.date), p1votes.vote_id) AS allDisagreements


		  JOIN

			(SELECT votes.id, votes.date, relevantVoteIDs.bill_id, relevantVoteIDs.type
					     FROM votes JOIN
							(SELECT bill_id, vote_id, type
			 				FROM votes_re_bills JOIN (
									    SELECT *
									    FROM bills
									    WHERE bills.type = 'hr' OR bills.type = 's' OR bills.type = 'hjres' or bills.type = 'sjres') AS relevantBills
									    ON votes_re_bills.bill_id = relevantBills.id) AS relevantVoteIDs
							ON votes.id = relevantVoteIDs.vote_id) AS relevantVotes

				ON relevantVotes.id = allDisagreements.vote_id

			GROUP BY YEAR, Person1, Person2";

			$result2 = pg_query($dbconn, $disagreeSQL) or die('Query failed: ' . pg_last_error());


			while ($line = pg_fetch_row($result2)) {
    				array_push($disagree_arr, intval($line[0]));
			}

			pg_free_result($result2);


			$resultArray = array($agree_arr, $disagree_arr);

			echo json_encode($resultArray);

pg_close($dbconn);

?>
