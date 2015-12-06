<?php


		//get representative 1
		$rep1First = $_POST['rep1First'];
		$rep1Last = $_POST['rep1Last'];

		$dbconn = pg_connect("dbname=us_congress host=localhost user=postgres password=kushal941")
    				or die('Could not connect: ' . pg_last_error());

    	$contributions_Arr = array();

                  $contrib_SQL = "SELECT catname, 
                         agree, 
                         disagree, 
                         total, 
                         contribution_amount 
                  FROM   (SELECT catcode, 
                                 contribution_amount 
                          FROM   senate_contributions 
                          WHERE  senator_first = '".$rep1First."' 
                                 AND senator_last = '".$rep1Last."' 
                          ORDER  BY contribution_amount DESC 
                          LIMIT  10) AS b1, 
                         (SELECT t.catcode, 
                                 agree, 
                                 disagree, 
                                 total 
                          FROM   (SELECT catcode, 
                                         Count(*) AS total 
                                  FROM   (SELECT * 
                                          FROM   catcode_positions 
                                          WHERE  billid IN (SELECT bill_id 
                                                            FROM   (SELECT * 
                                                                    FROM   person_votes 
                                                                    WHERE  person_id = 
                                                                           (SELECT id 
                                                                            FROM 
                                                                           persons 
                                                                                        WHERE 
                                                                           first_name = '".$rep1First."' 
                                                                           AND last_name = '".$rep1Last."' 
                                                                           ) 
                                                                   ) 
                                                                   AS pv, 
                                                                   votes_re_bills 
                                                            WHERE  pv.vote_id = 
                                                                   votes_re_bills.vote_id 
                                                                   AND votes_re_bills.vote_id IN 
                                                                       (SELECT vote_id 
                                                                        FROM 
                                                                       votes_re_bills, 
                                                                       votes 
                                                                       WHERE 
                                                                       vote_id = id 
                                                                       AND category != 
                                                                           'amendment') 
                                                           )) AS voted_positions 
                                  GROUP  BY catcode) AS t, 
                                 (SELECT catcode, 
                                         Count(*) AS agree 
                                  FROM   (SELECT * 
                                          FROM   (SELECT x1.bill_id, 
                                                         x1.person_id, 
                                                         x1.vote 
                                                  FROM   (SELECT bill_id, 
                                                                 person_id, 
                                                                 vote, 
                                                                 Count(*) 
                                                          FROM   (SELECT bill_id, 
                                                                         person_id, 
                                                                         vote 
                                                                  FROM   (SELECT * 
                                                                          FROM   person_votes 
                                                                          WHERE 
                                                                 person_id = (SELECT id 
                                                                              FROM 
                                                                 persons 
                                                                              WHERE 
                                                                 first_name = '".$rep1First."' 
                                                                 AND last_name = '".$rep1Last."') 
                                                                         ) AS 
                                                                         pv, 
                                                                         votes_re_bills 
                                                                  WHERE  pv.vote_id = 
                                                                         votes_re_bills.vote_id 
                                                                         AND 
                                                                 votes_re_bills.vote_id IN ( 
                                                                 SELECT 
                                                                 vote_id 
                                                                 FROM 
                                                                 votes_re_bills, 
                                                                 votes 
                                                                 WHERE 
                                                                 vote_id = id 
                                                                 AND category != 
                                                                     'amendment')) 
                                                                 AS 
                                                                 repeat_votes 
                                                          GROUP  BY bill_id, 
                                                                    person_id, 
                                                                    vote) AS x1, 
                                                         (SELECT bill_id, 
                                                                 Max(count) AS maxCount 
                                                          FROM   (SELECT bill_id, 
                                                                         person_id, 
                                                                         vote, 
                                                                         Count(*) 
                                                                  FROM   (SELECT bill_id, 
                                                                                 person_id, 
                                                                                 vote 
                                                                          FROM   (SELECT * 
                                                                                  FROM 
                                                                         person_votes 
                                                                                  WHERE 
                                                                         person_id = (SELECT id 
                                                                                      FROM 
                                                                         persons 
                                                                                      WHERE 
                                                                         first_name = '".$rep1First."' 
                                                                         AND last_name = '".$rep1Last."') 
                                                                                 ) AS 
                                                                                 pv, 
                                                                                 votes_re_bills 
                                                                          WHERE  pv.vote_id = 
                                                                 votes_re_bills.vote_id 
                                                                 AND 
                                                                         votes_re_bills.vote_id IN 
                                                                         ( 
                                                                         SELECT 
                                                                         vote_id 
                                                                         FROM 
                                                                         votes_re_bills, 
                                                                         votes 
                                                                         WHERE 
                                                                         vote_id = id 
                                                                         AND category != 
                                                                             'amendment')) 
                                                                         AS 
                                                                         repeat_votes 
                                                                  GROUP  BY bill_id, 
                                                                            person_id, 
                                                                            vote 
                                                                  ORDER  BY bill_id) AS f1 
                                                          GROUP  BY bill_id) AS x2 
                                                  WHERE  x1.bill_id = x2.bill_id 
                                                         AND x1.count = maxcount 
                                                  ORDER  BY x1.bill_id) AS person, 
                                                 (SELECT * 
                                                  FROM   catcode_positions 
                                                  WHERE  catcode IN (SELECT catcode 
                                                                     FROM   (SELECT catcode, 
                                                                                    Count(*) 
                                                                             FROM 
                                                                    catcode_positions 
                                                                             GROUP  BY catcode 
                                                                             ORDER  BY count DESC) 
                                                                            AS 
                                                                            cat_grouped 
                                                                     WHERE  count >= 20) 
                                                         AND billid IN (SELECT bill_id 
                                                                        FROM   (SELECT * 
                                                                                FROM 
                                                                       person_votes 
                                                                                WHERE 
                                                                       person_id = (SELECT id 
                                                                                    FROM 
                                                                       persons 
                                                                                    WHERE 
                                                                       first_name = '".$rep1First."' 
                                                                       AND last_name = 
                                                                           '".$rep1Last."')) AS 
                                                                               pv, 
                                                                               votes_re_bills 
                                                                        WHERE  pv.vote_id = 
                                                             votes_re_bills.vote_id 
                                                             AND votes_re_bills.vote_id 
                                                                 IN ( 
                                                                 SELECT 
                                                                 vote_id 
                                                                 FROM 
                                                                 votes_re_bills, 
                                                                 votes 
                                                                 WHERE 
                                                                 vote_id = id 
                                                                 AND category != 
                                                                     'amendment'))) AS 
                                                 category 
                                          WHERE  person.bill_id = category.billid 
                                                 AND ( ( ( vote = 'Yea' 
                                                            OR vote = 'Aye' ) 
                                                         AND interest_position = 'Support' ) 
                                                        OR ( ( vote = 'Nay' 
                                                                OR vote = 'No' ) 
                                                             AND interest_position = 'Oppose' ) )) 
                                         AS foo 
                                  GROUP  BY catcode) AS agree, 
                                 (SELECT catcode, 
                                         Count(*) AS disagree 
                                  FROM   (SELECT * 
                                          FROM   (SELECT x1.bill_id, 
                                                         x1.person_id, 
                                                         x1.vote 
                                                  FROM   (SELECT bill_id, 
                                                                 person_id, 
                                                                 vote, 
                                                                 Count(*) 
                                                          FROM   (SELECT bill_id, 
                                                                         person_id, 
                                                                         vote 
                                                                  FROM   (SELECT * 
                                                                          FROM   person_votes 
                                                                          WHERE 
                                                                 person_id = (SELECT id 
                                                                              FROM 
                                                                 persons 
                                                                              WHERE 
                                                                 first_name = '".$rep1First."' 
                                                                 AND last_name = '".$rep1Last."') 
                                                                         ) AS 
                                                                         pv, 
                                                                         votes_re_bills 
                                                                  WHERE  pv.vote_id = 
                                                                         votes_re_bills.vote_id 
                                                                         AND 
                                                                 votes_re_bills.vote_id IN ( 
                                                                 SELECT 
                                                                 vote_id 
                                                                 FROM 
                                                                 votes_re_bills, 
                                                                 votes 
                                                                 WHERE 
                                                                 vote_id = id 
                                                                 AND category != 
                                                                     'amendment')) 
                                                                 AS 
                                                                 repeat_votes 
                                                          GROUP  BY bill_id, 
                                                                    person_id, 
                                                                    vote) AS x1, 
                                                         (SELECT bill_id, 
                                                                 Max(count) AS maxCount 
                                                          FROM   (SELECT bill_id, 
                                                                         person_id, 
                                                                         vote, 
                                                                         Count(*) 
                                                                  FROM   (SELECT bill_id, 
                                                                                 person_id, 
                                                                                 vote 
                                                                          FROM   (SELECT * 
                                                                                  FROM 
                                                                         person_votes 
                                                                                  WHERE 
                                                                         person_id = (SELECT id 
                                                                                      FROM 
                                                                         persons 
                                                                                      WHERE 
                                                                         first_name = '".$rep1First."' 
                                                                         AND last_name = '".$rep1Last."') 
                                                                                 ) AS 
                                                                                 pv, 
                                                                                 votes_re_bills 
                                                                          WHERE  pv.vote_id = 
                                                                 votes_re_bills.vote_id 
                                                                 AND 
                                                                         votes_re_bills.vote_id IN 
                                                                         ( 
                                                                         SELECT 
                                                                         vote_id 
                                                                         FROM 
                                                                         votes_re_bills, 
                                                                         votes 
                                                                         WHERE 
                                                                         vote_id = id 
                                                                         AND category != 
                                                                             'amendment')) 
                                                                         AS 
                                                                         repeat_votes 
                                                                  GROUP  BY bill_id, 
                                                                            person_id, 
                                                                            vote 
                                                                  ORDER  BY bill_id) AS f1 
                                                          GROUP  BY bill_id) AS x2 
                                                  WHERE  x1.bill_id = x2.bill_id 
                                                         AND x1.count = maxcount 
                                                  ORDER  BY x1.bill_id) AS person, 
                                                 (SELECT * 
                                                  FROM   catcode_positions 
                                                  WHERE  catcode IN (SELECT catcode 
                                                                     FROM   (SELECT catcode, 
                                                                                    Count(*) 
                                                                             FROM 
                                                                    catcode_positions 
                                                                             GROUP  BY catcode 
                                                                             ORDER  BY count DESC) 
                                                                            AS 
                                                                            cat_grouped 
                                                                     WHERE  count >= 20) 
                                                         AND billid IN (SELECT bill_id 
                                                                        FROM   (SELECT * 
                                                                                FROM 
                                                                       person_votes 
                                                                                WHERE 
                                                                       person_id = (SELECT id 
                                                                                    FROM 
                                                                       persons 
                                                                                    WHERE 
                                                                       first_name = '".$rep1First."' 
                                                                       AND last_name = 
                                                                           '".$rep1Last."')) AS 
                                                                               pv, 
                                                                               votes_re_bills 
                                                                        WHERE  pv.vote_id = 
                                                             votes_re_bills.vote_id 
                                                             AND votes_re_bills.vote_id 
                                                                 IN ( 
                                                                 SELECT 
                                                                 vote_id 
                                                                 FROM 
                                                                 votes_re_bills, 
                                                                 votes 
                                                                 WHERE 
                                                                 vote_id = id 
                                                                 AND category != 
                                                                     'amendment'))) AS 
                                                 category 
                                          WHERE  person.bill_id = category.billid 
                                                 AND ( ( ( vote = 'Yea' 
                                                            OR vote = 'Aye' ) 
                                                         AND interest_position = 'Oppose' ) 
                                                        OR ( ( vote = 'Nay' 
                                                                OR vote = 'No' ) 
                                                             AND interest_position = 'Support' ) ) 
                                         ) AS 
                                         foo 
                                  GROUP  BY catcode) AS disagree 
                          WHERE  t.catcode = agree.catcode 
                                 AND agree.catcode = disagree.catcode) AS b2, 
                         organization_industry_info 
                  WHERE  organization_industry_info.catcode = b1.catcode 
                         AND b2.catcode = b1.catcode 
                  ORDER  BY contribution_amount DESC";

		$result = pg_query($dbconn, $contrib_SQL) or die('Query failed: ' . pg_last_error());

		while ($line = pg_fetch_row($result)) {

      $organization = $line[0];
      $agree = $line[1];
      $disagree = $line[2];
      $total = $line[3];
      $amount = $line[4];

      array_push($contributions_Arr, $organization);
      array_push($contributions_Arr, intval($agree));
      array_push($contributions_Arr, intval($disagree));
      array_push($contributions_Arr, intval($total));
      array_push($contributions_Arr, intval($amount));
			}

		pg_free_result($result);

		echo json_encode($contributions_Arr);

pg_close($dbconn);

		?>
