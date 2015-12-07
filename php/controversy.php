<?php


		//get representative 1
		$rep1First = $_POST['rep1First'];
		$rep1Last = $_POST['rep1Last'];

		$dbconn = pg_connect("dbname=us_congress host=localhost user=postgres password=kushal941")
    				or die('Could not connect: ' . pg_last_error());

    	$bill_agree_arr = array();
    	$bill_disagree_arr = array();

    	$agreeControversialSQL = "SELECT *
							FROM   (SELECT vote_id
        							FROM   (SELECT *
                							FROM   person_votes
                       								JOIN (SELECT id, party
                             							  FROM   ((SELECT id
                                     							   FROM   persons
                                     							   WHERE  first_name = '".$rep1First."'
                                            							  AND last_name = '".$rep1Last."') AS NAME
                                     							JOIN person_roles
                                       							ON NAME.id = person_roles.person_id)) AS allInfo
                         							ON person_votes.person_id = allInfo.id) AS t1, partycontroversy
        							WHERE  t1.vote_id = partycontroversy.id
               						AND partycontroversy.vote = t1.vote
               						AND t1.party = partycontroversy.party) AS cont
       							JOIN billcontroversy
         						ON cont.vote_id = billcontroversy.id";

         $disagreeControversialSQL = "SELECT *
							FROM   (SELECT vote_id
        							FROM   (SELECT *
                							FROM   person_votes
                       								JOIN (SELECT id, party
                             							  FROM   ((SELECT id
                                     							   FROM   persons
                                     							   WHERE  first_name = '".$rep1First."'
                                            							  AND last_name = '".$rep1Last."') AS NAME
                                     							JOIN person_roles
                                       							ON NAME.id = person_roles.person_id)) AS allInfo
                         							ON person_votes.person_id = allInfo.id) AS t1, partycontroversy
        							WHERE  t1.vote_id = partycontroversy.id
               						AND partycontroversy.vote <> t1.vote
               						AND t1.party = partycontroversy.party) AS cont
       							JOIN billcontroversy
         						ON cont.vote_id = billcontroversy.id";

		$result = pg_query($dbconn, $agreeControversialSQL) or die('Query failed: ' . pg_last_error());
		$result1 = pg_query($dbconn, $disagreeControversialSQL) or die('Query failed: ' . pg_last_error());

		while ($line = pg_fetch_row($result)) {
				if ((strcmp($line[0], "0") !== 0) && (strcmp($line[0], "-1") !== 0))
				{
            $line2 = array($line[0], $line[1], intval($line[2]), intval($line[3]), intval($line[4]), intval($line[5]), intval($line[6]), intval($line[7]));
    				array_push($bill_agree_arr, $line2);
    			}
			}

		while ($line = pg_fetch_row($result1)) {
				if ((strcmp($line[0], "0") !== 0) && (strcmp($line[0], "-1") !== 0))
				{
          $line2 = array($line[0], $line[1], intval($line[2]), intval($line[3]), intval($line[4]), intval($line[5]), intval($line[6]), intval($line[7]));
    				array_push($bill_disagree_arr, $line2);
    			}
			}

		pg_free_result($result);
		pg_free_result($result1);

		$result_arr = array($bill_agree_arr, $bill_disagree_arr);

		echo json_encode($result_arr);

pg_close($dbconn);

		?>
