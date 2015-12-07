<?php
$dbconn = pg_connect("dbname=us_congress host=localhost user=postgres password=kushal941")
        or die('Could not connect: ' . pg_last_error());

$request = file_get_contents("php://input");
$data = json_decode($request);
$rep1First = $data->rep1First;
$rep1Last = $data->rep1Last;
$rep2First = $data->rep2First;
$rep2Last = $data->rep2Last;

$p1Bills = array();
$p1BillsSql = "SELECT *
               FROM bills
               WHERE id IN
                    (SELECT sponsors.bill_id
                     FROM sponsors, cosponsors
                     WHERE sponsor_id=
                            (SELECT id_govtrack
                             FROM persons
                             WHERE first_name = '".$rep1First."' AND last_name = '".$rep1Last."')
                             AND cosponsor_id=
                                        (SELECT id_govtrack
                                         FROM persons
                                         WHERE first_name = '".$rep2First."' AND last_name = '".$rep2Last."') and sponsors.bill_id=cosponsors.bill_id)";

  $result1 = pg_query($dbconn, $p1BillsSql) or die('Query failed: ' . pg_last_error());


  while ($line = pg_fetch_row($result1)) {
     array_push($p1Bills, $line);
  }

  $p1BillsCount = array();
  $p1BillsCountSql = "SELECT count(*)
                 FROM sponsors, cosponsors
                 WHERE sponsor_id=
                      (SELECT id_govtrack
                       FROM persons
                       WHERE first_name = '".$rep1First."' AND last_name = '".$rep1Last."')
                       AND cosponsor_id=
                            (SELECT id_govtrack
                             FROM persons
                             WHERE first_name = '".$rep2First."' AND last_name = '".$rep2Last."') and sponsors.bill_id=cosponsors.bill_id";

  $result2 = pg_query($dbconn, $p1BillsCountSql) or die('Query failed: ' . pg_last_error());


    while ($line = pg_fetch_row($result2)) {
      array_push($p1BillsCount, intval($line[0]));
    }

  $p2Bills = array();
  $p2BillsSql = "SELECT *
                 FROM bills
                 WHERE id IN
                    (SELECT sponsors.bill_id
                     FROM sponsors, cosponsors
                     WHERE sponsor_id=
                        (SELECT id_govtrack
                         FROM persons
                         WHERE first_name = '".$rep2First."' AND last_name = '".$rep2Last."') and cosponsor_id=
                              (SELECT id_govtrack
                               FROM persons
                               WHERE first_name = '".$rep1First."' AND last_name = '".$rep1Last."') and sponsors.bill_id=cosponsors.bill_id)";

  $result3 = pg_query($dbconn, $p2BillsSql) or die('Query failed: ' . pg_last_error());


  while ($line = pg_fetch_row($result3)) {
      array_push($p2Bills, $line);
  }



  $p2BillsCount = array();
  $p2BillsCountSql = "SELECT count(*)
                      FROM sponsors, cosponsors
                      WHERE sponsor_id=
                        (SELECT id_govtrack
                         FROM persons
                         WHERE first_name = '".$rep2First."' AND last_name = '".$rep2Last."')
                         AND cosponsor_id=
                              (SELECT id_govtrack
                               FROM persons
                               WHERE first_name = '".$rep1First."' AND last_name = '".$rep1Last."') and sponsors.bill_id=cosponsors.bill_id";

  $result4 = pg_query($dbconn, $p2BillsCountSql) or die('Query failed: ' . pg_last_error());


    while ($line = pg_fetch_row($result4)) {
      array_push($p2BillsCount, intval($line[0]));
    }

  $cosponsored = array();
  $cosponsoredSql = "SELECT *
                     FROM bills
                     WHERE id IN
                        (SELECT c1.bill_id
                         FROM
                           (SELECT *
                            FROM cosponsors
                            WHERE cosponsor_id=
                              (SELECT id_govtrack
                               FROM persons
                               WHERE first_name ='".$rep1First."' AND last_name = '".$rep1Last."')) AS c1,
                           (SELECT *
                            FROM cosponsors
                            WHERE cosponsor_id=
                                (SELECT id_govtrack
                                 FROM persons
                                 WHERE first_name = '".$rep2First."' AND last_name = '".$rep2Last."')) AS c2
                        WHERE c1.bill_id=c2.bill_id)";

  $result5 = pg_query($dbconn, $cosponsoredSql) or die('Query failed: ' . pg_last_error());


  while ($line = pg_fetch_row($result5)) {
      array_push($cosponsored, $line);
  }


  $cosponsoredCount= array();
  $cosponsoredCountSql = "SELECT count(*)
                          FROM
                            (SELECT *
                             FROM cosponsors
                             WHERE cosponsor_id=
                                (SELECT id_govtrack
                                 FROM persons
                                 WHERE first_name ='".$rep1First."' AND last_name = '".$rep1Last."')) AS c1,
                                 (SELECT *
                                  FROM cosponsors
                                  WHERE cosponsor_id=
                                      (SELECT id_govtrack
                                       FROM persons
                                       WHERE first_name = '".$rep2First."' AND last_name = '".$rep2Last."')) AS c2
                                       WHERE c1.bill_id=c2.bill_id";

  $result6 = pg_query($dbconn, $cosponsoredCountSql) or die('Query failed: ' . pg_last_error());


    while ($line = pg_fetch_row($result6)) {
      array_push($cosponsoredCount, intval($line[0]));
    }


    $result_array = array($p1Bills,$p1BillsCount, $p2Bills, $p2BillsCount, $cosponsored, $cosponsoredCount);

    echo json_encode($result_array);

pg_close($dbconn);

?>
