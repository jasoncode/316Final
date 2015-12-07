<?php

  $dbconn = pg_connect("dbname=us_congress host=localhost user=postgres password=kushal941")
  or die('Could not connect: ' . pg_last_error());
  //get representative 1
  $request = file_get_contents("php://input");
  $data = json_decode($request);
  $repFirst = $data->repFirst;
  $repLast = $data->repLast;

$agree = array();
$agreeSql = "SELECT catname
FROM   organization_industry_info
WHERE  catcode IN
       (
              SELECT catcode
              FROM   (
                              SELECT   catcode,
                                       agree,
                                       disagree,
                                       total,
                                       CASE
                                                WHEN total>0 THEN (Cast(agree AS FLOAT)- disagree)/total
                                                ELSE 1000
                                       END AS odds
                              FROM     (
                                              SELECT t.catcode,
                                                     agree,
                                                     disagree,
                                                     total
                                              FROM   (
                                                              SELECT   catcode,
                                                                       Count(*) AS total
                                                              FROM     (
                                                                              SELECT *
                                                                              FROM   catcode_positions
                                                                              WHERE  billid IN
                                                                                     (
                                                                                            SELECT bill_id
                                                                                            FROM   (
                                                                                                          SELECT *
                                                                                                          FROM   person_votes
                                                                                                          WHERE  person_id=
                                                                                                                 (
                                                                                                                        SELECT id
                                                                                                                        FROM   persons
                                                                                                                        WHERE  first_name ='".$repFirst."'
                                                                                                                        AND    last_name='".$repLast."' )) AS pv,
                                                                                                   votes_re_bills
                                                                                            WHERE  pv.vote_id=votes_re_bills.vote_id
                                                                                            AND    votes_re_bills.vote_id IN
                                                                                                   (
                                                                                                          SELECT vote_id
                                                                                                          FROM   votes_re_bills,
                                                                                                                 votes
                                                                                                          WHERE  vote_id=id
                                                                                                          AND    category!='amendment'))) AS voted_positions
                                                              GROUP BY catcode) AS t,
                                                     (
                                                              SELECT   catcode,
                                                                       Count(*) AS agree
                                                              FROM     (
                                                                              SELECT *
                                                                              FROM   (
                                                                                              SELECT   x1.bill_id,
                                                                                                       x1.person_id,
                                                                                                       x1.vote
                                                                                              FROM     (
                                                                                                                SELECT   bill_id,
                                                                                                                         person_id,
                                                                                                                         vote,
                                                                                                                         Count(*)
                                                                                                                FROM     (
                                                                                                                                SELECT bill_id,
                                                                                                                                       person_id,
                                                                                                                                       vote
                                                                                                                                FROM   (
                                                                                                                                              SELECT *
                                                                                                                                              FROM   person_votes
                                                                                                                                              WHERE  person_id=
                                                                                                                                                     (
                                                                                                                                                            SELECT id
                                                                                                                                                            FROM   persons
                                                                                                                                                            WHERE  first_name ='".$repFirst."'
                                                                                                                                                            AND    last_name='".$repLast."' )) AS pv,
                                                                                                                                       votes_re_bills
                                                                                                                                WHERE  pv.vote_id=votes_re_bills.vote_id
                                                                                                                                AND    votes_re_bills.vote_id IN
                                                                                                                                       (
                                                                                                                                              SELECT vote_id
                                                                                                                                              FROM   votes_re_bills,
                                                                                                                                                     votes
                                                                                                                                              WHERE  vote_id=id
                                                                                                                                              AND    category!='amendment')) AS repeat_votes
                                                                                                                GROUP BY bill_id,
                                                                                                                         person_id,
                                                                                                                         vote) AS x1,
                                                                                                       (
                                                                                                                SELECT   bill_id,
                                                                                                                         Max(count) AS maxcount
                                                                                                                FROM     (
                                                                                                                                  SELECT   bill_id,
                                                                                                                                           person_id,
                                                                                                                                           vote,
                                                                                                                                           Count(*)
                                                                                                                                  FROM     (
                                                                                                                                                  SELECT bill_id,
                                                                                                                                                         person_id,
                                                                                                                                                         vote
                                                                                                                                                  FROM   (
                                                                                                                                                                SELECT *
                                                                                                                                                                FROM   person_votes
                                                                                                                                                                WHERE  person_id=
                                                                                                                                                                       (
                                                                                                                                                                              SELECT id
                                                                                                                                                                              FROM   persons
                                                                                                                                                                              WHERE  first_name ='".$repFirst."'
                                                                                                                                                                              AND    last_name='".$repLast."' )) AS pv,
                                                                                                                                                         votes_re_bills
                                                                                                                                                  WHERE  pv.vote_id=votes_re_bills.vote_id
                                                                                                                                                  AND    votes_re_bills.vote_id IN
                                                                                                                                                         (
                                                                                                                                                                SELECT vote_id
                                                                                                                                                                FROM   votes_re_bills,
                                                                                                                                                                       votes
                                                                                                                                                                WHERE  vote_id=id
                                                                                                                                                                AND    category!='amendment')) AS repeat_votes
                                                                                                                                  GROUP BY bill_id,
                                                                                                                                           person_id,
                                                                                                                                           vote
                                                                                                                                  ORDER BY bill_id) AS f1
                                                                                                                GROUP BY bill_id) AS x2
                                                                                              WHERE    x1.bill_id=x2.bill_id
                                                                                              AND      x1.count=maxcount
                                                                                              ORDER BY x1.bill_id) AS person,
                                                                                     (
                                                                                            SELECT *
                                                                                            FROM   catcode_positions
                                                                                            WHERE  catcode IN
                                                                                                   (
                                                                                                          SELECT catcode
                                                                                                          FROM   (
                                                                                                                          SELECT   catcode,
                                                                                                                                   Count(*)
                                                                                                                          FROM     catcode_positions
                                                                                                                          GROUP BY catcode
                                                                                                                          ORDER BY count DESC) AS cat_grouped
                                                                                                          WHERE  count>=20)
                                                                                            AND    billid IN
                                                                                                   (
                                                                                                          SELECT bill_id
                                                                                                          FROM   (
                                                                                                                        SELECT *
                                                                                                                        FROM   person_votes
                                                                                                                        WHERE  person_id=
                                                                                                                               (
                                                                                                                                      SELECT id
                                                                                                                                      FROM   persons
                                                                                                                                      WHERE  first_name ='".$repFirst."'
                                                                                                                                      AND    last_name='".$repLast."' )) AS pv,
                                                                                                                 votes_re_bills
                                                                                                          WHERE  pv.vote_id=votes_re_bills.vote_id
                                                                                                          AND    votes_re_bills.vote_id IN
                                                                                                                 (
                                                                                                                        SELECT vote_id
                                                                                                                        FROM   votes_re_bills,
                                                                                                                               votes
                                                                                                                        WHERE  vote_id=id
                                                                                                                        AND    category!='amendment'))) AS category
                                                                              WHERE  person.bill_id=category.billid
                                                                              AND    (((
                                                                                                          vote='Yea'
                                                                                                   OR     vote='Aye')
                                                                                            AND    interest_position='Support')
                                                                                     OR     ((
                                                                                                          vote='Nay'
                                                                                                   OR     vote='No')
                                                                                            AND    interest_position='Oppose'))) AS foo
                                                              GROUP BY catcode) AS agree,
                                                     (
                                                              SELECT   catcode,
                                                                       Count(*) AS disagree
                                                              FROM     (
                                                                              SELECT *
                                                                              FROM   (
                                                                                              SELECT   x1.bill_id,
                                                                                                       x1.person_id,
                                                                                                       x1.vote
                                                                                              FROM     (
                                                                                                                SELECT   bill_id,
                                                                                                                         person_id,
                                                                                                                         vote,
                                                                                                                         Count(*)
                                                                                                                FROM     (
                                                                                                                                SELECT bill_id,
                                                                                                                                       person_id,
                                                                                                                                       vote
                                                                                                                                FROM   (
                                                                                                                                              SELECT *
                                                                                                                                              FROM   person_votes
                                                                                                                                              WHERE  person_id=
                                                                                                                                                     (
                                                                                                                                                            SELECT id
                                                                                                                                                            FROM   persons
                                                                                                                                                            WHERE  first_name ='".$repFirst."'
                                                                                                                                                            AND    last_name='".$repLast."' )) AS pv,
                                                                                                                                       votes_re_bills
                                                                                                                                WHERE  pv.vote_id=votes_re_bills.vote_id
                                                                                                                                AND    votes_re_bills.vote_id IN
                                                                                                                                       (
                                                                                                                                              SELECT vote_id
                                                                                                                                              FROM   votes_re_bills,
                                                                                                                                                     votes
                                                                                                                                              WHERE  vote_id=id
                                                                                                                                              AND    category!='amendment')) AS repeat_votes
                                                                                                                GROUP BY bill_id,
                                                                                                                         person_id,
                                                                                                                         vote) AS x1,
                                                                                                       (
                                                                                                                SELECT   bill_id,
                                                                                                                         Max(count) AS maxcount
                                                                                                                FROM     (
                                                                                                                                  SELECT   bill_id,
                                                                                                                                           person_id,
                                                                                                                                           vote,
                                                                                                                                           Count(*)
                                                                                                                                  FROM     (
                                                                                                                                                  SELECT bill_id,
                                                                                                                                                         person_id,
                                                                                                                                                         vote
                                                                                                                                                  FROM   (
                                                                                                                                                                SELECT *
                                                                                                                                                                FROM   person_votes
                                                                                                                                                                WHERE  person_id=
                                                                                                                                                                       (
                                                                                                                                                                              SELECT id
                                                                                                                                                                              FROM   persons
                                                                                                                                                                              WHERE  first_name ='".$repFirst."'
                                                                                                                                                                              AND    last_name='".$repLast."' )) AS pv,
                                                                                                                                                         votes_re_bills
                                                                                                                                                  WHERE  pv.vote_id=votes_re_bills.vote_id
                                                                                                                                                  AND    votes_re_bills.vote_id IN
                                                                                                                                                         (
                                                                                                                                                                SELECT vote_id
                                                                                                                                                                FROM   votes_re_bills,
                                                                                                                                                                       votes
                                                                                                                                                                WHERE  vote_id=id
                                                                                                                                                                AND    category!='amendment')) AS repeat_votes
                                                                                                                                  GROUP BY bill_id,
                                                                                                                                           person_id,
                                                                                                                                           vote
                                                                                                                                  ORDER BY bill_id) AS f1
                                                                                                                GROUP BY bill_id) AS x2
                                                                                              WHERE    x1.bill_id=x2.bill_id
                                                                                              AND      x1.count=maxcount
                                                                                              ORDER BY x1.bill_id) AS person,
                                                                                     (
                                                                                            SELECT *
                                                                                            FROM   catcode_positions
                                                                                            WHERE  catcode IN
                                                                                                   (
                                                                                                          SELECT catcode
                                                                                                          FROM   (
                                                                                                                          SELECT   catcode,
                                                                                                                                   Count(*)
                                                                                                                          FROM     catcode_positions
                                                                                                                          GROUP BY catcode
                                                                                                                          ORDER BY count DESC) AS cat_grouped
                                                                                                          WHERE  count>=20)
                                                                                            AND    billid IN
                                                                                                   (
                                                                                                          SELECT bill_id
                                                                                                          FROM   (
                                                                                                                        SELECT *
                                                                                                                        FROM   person_votes
                                                                                                                        WHERE  person_id=
                                                                                                                               (
                                                                                                                                      SELECT id
                                                                                                                                      FROM   persons
                                                                                                                                      WHERE  first_name ='".$repFirst."'
                                                                                                                                      AND    last_name='".$repLast."' )) AS pv,
                                                                                                                 votes_re_bills
                                                                                                          WHERE  pv.vote_id=votes_re_bills.vote_id
                                                                                                          AND    votes_re_bills.vote_id IN
                                                                                                                 (
                                                                                                                        SELECT vote_id
                                                                                                                        FROM   votes_re_bills,
                                                                                                                               votes
                                                                                                                        WHERE  vote_id=id
                                                                                                                        AND    category!='amendment'))) AS category
                                                                              WHERE  person.bill_id=category.billid
                                                                              AND    (((
                                                                                                          vote='Yea'
                                                                                                   OR     vote='Aye')
                                                                                            AND    interest_position='Oppose')
                                                                                     OR     ((
                                                                                                          vote='Nay'
                                                                                                   OR     vote='No')
                                                                                            AND    interest_position='Support'))) AS foo
                                                              GROUP BY catcode) AS disagree
                                              WHERE  t.catcode=agree.catcode
                                              AND    agree.catcode=disagree.catcode) AS footoo
                              WHERE    total>14
                              ORDER BY odds DESC limit 10) AS foo3)";

$agreeResult = pg_query($dbconn, $agreeSql) or die('Query failed: ' . pg_last_error());
while ($line = pg_fetch_row($agreeResult)) {
    array_push($agree, $line);
}

$disagree = array();
$disagreeSql = "SELECT catname
FROM organization_industry_info
WHERE catcode IN (
		SELECT catcode
		FROM (
			SELECT catcode
				,agree
				,disagree
				,total
				,CASE
					WHEN total > 0
						THEN (cast(agree AS FLOAT) - disagree) / total
					ELSE 1000
					END AS odds
			FROM (
				SELECT t.catcode
					,agree
					,disagree
					,total
				FROM (
					SELECT catcode
						,count(*) AS total
					FROM (
						SELECT *
						FROM catcode_positions
						WHERE billid IN (
								SELECT bill_id
								FROM (
									SELECT *
									FROM person_votes
									WHERE person_id = (
											SELECT id
											FROM persons
											WHERE first_name = 'Sherrod'
												AND last_name = 'Brown'
											)
									) AS pv
									,votes_re_bills
								WHERE pv.vote_id = votes_re_bills.vote_id
									AND votes_re_bills.vote_id IN (
										SELECT vote_id
										FROM votes_re_bills
											,votes
										WHERE vote_id = id
											AND category != 'amendment'
										)
								)
						) AS voted_positions
					GROUP BY catcode
					) AS t
					,(
						SELECT catcode
							,count(*) AS agree
						FROM (
							SELECT *
							FROM (
								SELECT x1.bill_id
									,x1.person_id
									,x1.vote
								FROM (
									SELECT bill_id
										,person_id
										,vote
										,count(*)
									FROM (
										SELECT bill_id
											,person_id
											,vote
										FROM (
											SELECT *
											FROM person_votes
											WHERE person_id = (
													SELECT id
													FROM persons
													WHERE first_name = 'Sherrod'
														AND last_name = 'Brown'
													)
											) AS pv
											,votes_re_bills
										WHERE pv.vote_id = votes_re_bills.vote_id
											AND votes_re_bills.vote_id IN (
												SELECT vote_id
												FROM votes_re_bills
													,votes
												WHERE vote_id = id
													AND category != 'amendment'
												)
										) AS repeat_votes
									GROUP BY bill_id
										,person_id
										,vote
									) AS x1
									,(
										SELECT bill_id
											,max(count) AS maxCount
										FROM (
											SELECT bill_id
												,person_id
												,vote
												,count(*)
											FROM (
												SELECT bill_id
													,person_id
													,vote
												FROM (
													SELECT *
													FROM person_votes
													WHERE person_id = (
															SELECT id
															FROM persons
															WHERE first_name = 'Sherrod'
																AND last_name = 'Brown'
															)
													) AS pv
													,votes_re_bills
												WHERE pv.vote_id = votes_re_bills.vote_id
													AND votes_re_bills.vote_id IN (
														SELECT vote_id
														FROM votes_re_bills
															,votes
														WHERE vote_id = id
															AND category != 'amendment'
														)
												) AS repeat_votes
											GROUP BY bill_id
												,person_id
												,vote
											ORDER BY bill_id
											) AS f1
										GROUP BY bill_id
										) AS x2
								WHERE x1.bill_id = x2.bill_id
									AND x1.count = maxCount
								ORDER BY x1.bill_id
								) AS person
								,(
									SELECT *
									FROM catcode_positions
									WHERE catcode IN (
											SELECT catcode
											FROM (
												SELECT catcode
													,count(*)
												FROM catcode_positions
												GROUP BY catcode
												ORDER BY count DESC
												) AS cat_grouped
											WHERE count >= 20
											)
										AND billid IN (
											SELECT bill_id
											FROM (
												SELECT *
												FROM person_votes
												WHERE person_id = (
														SELECT id
														FROM persons
														WHERE first_name = 'Sherrod'
															AND last_name = 'Brown'
														)
												) AS pv
												,votes_re_bills
											WHERE pv.vote_id = votes_re_bills.vote_id
												AND votes_re_bills.vote_id IN (
													SELECT vote_id
													FROM votes_re_bills
														,votes
													WHERE vote_id = id
														AND category != 'amendment'
													)
											)
									) AS category
							WHERE person.bill_id = category.billid
								AND (
									(
										(
											vote = 'Yea'
											OR vote = 'Aye'
											)
										AND interest_position = 'Support'
										)
									OR (
										(
											vote = 'Nay'
											OR vote = 'No'
											)
										AND interest_position = 'Oppose'
										)
									)
							) AS foo
						GROUP BY catcode
						) AS agree
					,(
						SELECT catcode
							,count(*) AS disagree
						FROM (
							SELECT *
							FROM (
								SELECT x1.bill_id
									,x1.person_id
									,x1.vote
								FROM (
									SELECT bill_id
										,person_id
										,vote
										,count(*)
									FROM (
										SELECT bill_id
											,person_id
											,vote
										FROM (
											SELECT *
											FROM person_votes
											WHERE person_id = (
													SELECT id
													FROM persons
													WHERE first_name = 'Sherrod'
														AND last_name = 'Brown'
													)
											) AS pv
											,votes_re_bills
										WHERE pv.vote_id = votes_re_bills.vote_id
											AND votes_re_bills.vote_id IN (
												SELECT vote_id
												FROM votes_re_bills
													,votes
												WHERE vote_id = id
													AND category != 'amendment'
												)
										) AS repeat_votes
									GROUP BY bill_id
										,person_id
										,vote
									) AS x1
									,(
										SELECT bill_id
											,max(count) AS maxCount
										FROM (
											SELECT bill_id
												,person_id
												,vote
												,count(*)
											FROM (
												SELECT bill_id
													,person_id
													,vote
												FROM (
													SELECT *
													FROM person_votes
													WHERE person_id = (
															SELECT id
															FROM persons
															WHERE first_name = 'Sherrod'
																AND last_name = 'Brown'
															)
													) AS pv
													,votes_re_bills
												WHERE pv.vote_id = votes_re_bills.vote_id
													AND votes_re_bills.vote_id IN (
														SELECT vote_id
														FROM votes_re_bills
															,votes
														WHERE vote_id = id
															AND category != 'amendment'
														)
												) AS repeat_votes
											GROUP BY bill_id
												,person_id
												,vote
											ORDER BY bill_id
											) AS f1
										GROUP BY bill_id
										) AS x2
								WHERE x1.bill_id = x2.bill_id
									AND x1.count = maxCount
								ORDER BY x1.bill_id
								) AS person
								,(
									SELECT *
									FROM catcode_positions
									WHERE catcode IN (
											SELECT catcode
											FROM (
												SELECT catcode
													,count(*)
												FROM catcode_positions
												GROUP BY catcode
												ORDER BY count DESC
												) AS cat_grouped
											WHERE count >= 20
											)
										AND billid IN (
											SELECT bill_id
											FROM (
												SELECT *
												FROM person_votes
												WHERE person_id = (
														SELECT id
														FROM persons
														WHERE first_name = 'Sherrod'
															AND last_name = 'Brown'
														)
												) AS pv
												,votes_re_bills
											WHERE pv.vote_id = votes_re_bills.vote_id
												AND votes_re_bills.vote_id IN (
													SELECT vote_id
													FROM votes_re_bills
														,votes
													WHERE vote_id = id
														AND category != 'amendment'
													)
											)
									) AS category
							WHERE person.bill_id = category.billid
								AND (
									(
										(
											vote = 'Yea'
											OR vote = 'Aye'
											)
										AND interest_position = 'Oppose'
										)
									OR (
										(
											vote = 'Nay'
											OR vote = 'No'
											)
										AND interest_position = 'Support'
										)
									)
							) AS foo
						GROUP BY catcode
						) AS disagree
				WHERE t.catcode = agree.catcode
					AND agree.catcode = disagree.catcode
				) AS footoo
			WHERE total > 14
			ORDER BY odds limit 10
			) AS foo3
		)";

$disagreeResult = pg_query($dbconn, $disagreeSql) or die('Query failed: ' . pg_last_error());
while ($line = pg_fetch_row($disagreeResult)) {
    array_push($disagree, $line);
}

$result_array = $array($agree, $disagree);
echo json_encode($result_array);

pg_close($dbconn);

?>
