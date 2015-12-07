<?php


		//get representative 1
		$request = file_get_contents("php://input");
	  $data = json_decode($request);
	  $rep1First = $data->rep1First;
	  $rep1Last = $data->rep1Last;
    $rep2First = $data->rep2First;
    $rep2Last = $data->rep2Last;

		$dbconn = pg_connect("dbname=us_congress host=localhost user=postgres password=kushal941")
						or die('Could not connect: ' . pg_last_error());

    $categoryTable = array();
    $categorySql = "SELECT catname, agree,disagree
                 FROM   organization_industry_info
                 NATURAL JOIN (SELECT t7.catcode, agree, disagree,total
                     FROM   (SELECT catcode,Sum(agree) AS agree,
                                    Sum(CASE
                                          WHEN agree = 0 THEN 1
                                          ELSE 0
                                        END)   AS disagree
                             FROM   (SELECT x1.bill_id,agree
                                     FROM   (SELECT bill_id, agree,Count(*)
                                             FROM   (SELECT bill_id,
                                                            CASE
                                                              WHEN
                                                    t1.vote = t2.vote
                                                            THEN 1
                                                              ELSE 0
                                                            END AS agree
                                                     FROM   (SELECT *
                                                             FROM   person_votes
                                                             WHERE
                                                    person_id = (SELECT
                                                    id
                                                                 FROM
                                                    persons
                                                                 WHERE
                                                    first_name = '".$rep1First."' AND last_name = '".$rep1Last."')) AS
                                                            t1,
                                                            (SELECT *
                                                             FROM   person_votes
                                                             WHERE
                                                    person_id = (SELECT
                                                    id
                                                                 FROM
                                                    persons
                                                                 WHERE
                                                    first_name = '".$rep2First."' AND last_name = '".$rep2Last."'))
                                                            AS t2,
                                                            votes_re_bills
                                                     WHERE
                                            t1.vote_id = t2.vote_id
                                            AND
       t2.vote_id = votes_re_bills.vote_id)
       AS t3
       GROUP  BY bill_id,
          agree) AS x1,
       (SELECT bill_id,
       Max(agree) AS maxCount
       FROM   (SELECT bill_id,
               agree,
               Count(*)
        FROM   (SELECT bill_id,
                       CASE
                         WHEN
               t1.vote = t2.vote THEN 1
                         ELSE 0
                       END AS agree
                FROM   (SELECT *
                        FROM
               person_votes
                        WHERE
               person_id = (SELECT id
                            FROM
               persons
                            WHERE
               first_name = '".$rep1First."' AND last_name = '".$rep1Last."')
                       ) AS
                       t1,
                       (SELECT *
                        FROM
               person_votes
                        WHERE
               person_id = (SELECT id
                            FROM
               persons
                            WHERE
               first_name = '".$rep2First."' AND last_name = '".$rep2Last."'))
                       AS t2,
                       votes_re_bills
                WHERE
       t1.vote_id = t2.vote_id
       AND t2.vote_id =
           votes_re_bills.vote_id)
               AS t3
        GROUP  BY bill_id,
                  agree) AS t4
       GROUP  BY bill_id) AS x2
       WHERE  x1.bill_id = x2.bill_id
       AND x1.agree = maxcount
       ORDER  BY x1.bill_id) AS t5,
       (SELECT *
       FROM   catcode_positions
       WHERE  catcode IN (SELECT catcode
           FROM   (SELECT catcode,
                          Count(*)
                   FROM
          catcode_positions
                   GROUP  BY catcode
                   ORDER  BY count DESC)
                  AS cat_grouped
           WHERE  count >= 20)
       AND billid IN (SELECT bill_id
              FROM   (SELECT *
                      FROM   person_votes
                      WHERE
             person_id = (SELECT id
                          FROM
             persons
                          WHERE
             first_name = '".$rep1First."' AND last_name = '".$rep1Last."')) AS
                     pv,
                     votes_re_bills
              WHERE  pv.vote_id =
       votes_re_bills.vote_id)
       AND catcode IN (SELECT catcode
               FROM
       (SELECT catcode,
           Count(*)
       FROM
              catcode_positions
                       GROUP  BY catcode
                       ORDER  BY count DESC
       ) AS
                      cat_grouped
               WHERE  count >= 20)
       AND billid IN (SELECT bill_id
              FROM   (SELECT *
                      FROM   person_votes
                      WHERE
             person_id = (SELECT id
                          FROM
             persons
                          WHERE
            first_name = '".$rep2First."' AND last_name = '".$rep2Last."'))
                     AS pv
                     ,
                     votes_re_bills
              WHERE  pv.vote_id =
       votes_re_bills.vote_id)) AS
       t6
       WHERE  t5.bill_id = t6.billid
       GROUP  BY catcode) AS t7,
       (SELECT catcode,
       Count(*) AS total
       FROM   (SELECT *
       FROM   catcode_positions
       WHERE  catcode IN (SELECT catcode
           FROM   (SELECT catcode,
                          Count(*)
                   FROM
          catcode_positions
                   GROUP  BY catcode
                   ORDER  BY count DESC)
                  AS cat_grouped
           WHERE  count >= 20)
       AND billid IN (SELECT bill_id
              FROM   (SELECT *
                      FROM   person_votes
                      WHERE
             person_id = (SELECT id
                          FROM
             persons
                          WHERE
             first_name = '".$rep1First."' AND last_name = '".$rep1Last."')) AS
                     pv,
                     votes_re_bills
              WHERE  pv.vote_id =
       votes_re_bills.vote_id
       AND votes_re_bills.vote_id IN (
       SELECT
       vote_id
       FROM
       votes_re_bills,
       votes
       WHERE
       vote_id = id
       AND category !=
           'amendment'))
       AND catcode IN (SELECT catcode
               FROM
       (SELECT catcode,
           Count(*)
       FROM
              catcode_positions
                       GROUP  BY catcode
                       ORDER  BY count DESC
       ) AS
                      cat_grouped
               WHERE  count >= 20)
       AND billid IN (SELECT bill_id
              FROM   (SELECT *
                      FROM   person_votes
                      WHERE
             person_id = (SELECT id
                          FROM
             persons
                          WHERE
            first_name = '".$rep2First."' AND last_name = '".$rep2Last."'))
                     AS pv
                     ,
                     votes_re_bills
              WHERE  pv.vote_id =
       votes_re_bills.vote_id
       AND votes_re_bills.vote_id IN (
       SELECT
       vote_id
       FROM
       votes_re_bills,
       votes
       WHERE
       vote_id = id
       AND category !=
           'amendment'))) AS t9
       GROUP  BY catcode) AS t8
       WHERE  t7.catcode = t8.catcode) AS counts
			 ORDER BY agree DESC";

    $result = pg_query($dbconn, $categorySql) or die('Query failed: ' . pg_last_error());

 		while ($line = pg_fetch_row($result)) {
       array_push($categoryTable, $line);
 			}


   	pg_free_result($result);



   	echo json_encode($categoryTable);

    pg_close($dbconn);

?>
