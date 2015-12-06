<?php

  $dbconn = pg_connect("dbname=us_congress host=localhost user=postgres password=Bd3nM2!Vg27aJ!0")
  or die('Could not connect: ' . pg_last_error());
  //get representative 1
  $request = file_get_contents("php://input");
  $data = json_decode($request);
  $repFirst = $data->repFirst;
  $repLast = $data->repLast;

  $allSponsored = array();
  $sponsoredSql = "SELECT *
                  FROM bills
                  WHERE id in
                    (SELECT bill_id
                     FROM sponsors
                     WHERE sponsor_id=
                                    (SELECT id_govtrack FROM persons WHERE first_name ='".$repFirst."' and last_name='".$repLast."'))";

  $result1 = pg_query($dbconn, $sponsoredSql) or die('Query failed: ' . pg_last_error());


  while ($line = pg_fetch_row($result1)) {
      array_push($allSponsored, $line);
  }

  $sponsorCount = array();
  $sponsorCountSql = "SELECT count(*)
                      FROM sponsors
                      WHERE sponsor_id=
                            (SELECT id_govtrack
                             FROM persons
                             WHERE first_name ='".$repFirst."' and last_name='".$repLast."')";
  $result2 = pg_query($dbconn, $sponsorCountSql) or die('Query failed: ' . pg_last_error());


  while ($line = pg_fetch_row($result2)) {
    array_push($sponsorCount, intval($line[0]));
  }

  $allCosponsored = array();
  $cosponsoredSql = "SELECT *
                     FROM bills
                     WHERE id in
                        (SELECT bill_id
                         FROM cosponsors
                         WHERE cosponsor_id=
                                  (SELECT id_govtrack
                                   FROM persons
                                   WHERE first_name ='".$repFirst."' and last_name='".$repLast."'))";

   $result3 = pg_query($dbconn, $cosponsoredSql) or die('Query failed: ' . pg_last_error());


   while ($line = pg_fetch_row($result3)) {
       array_push($allCosponsored, $line);
   }

  $cosponsorCount = array();
  $cosponsorCountSql = "SELECT count(*)
                        FROM cosponsors
                        WHERE cosponsor_id=
                                    (SELECT id_govtrack
                                     FROM persons
                                     WHERE first_name ='".$repFirst."' and last_name='".$repLast."')";


   $result4 = pg_query($dbconn, $cosponsorCountSql) or die('Query failed: ' . pg_last_error());
   while ($line = pg_fetch_row($result4)) {
     array_push($cosponsorCount, intval($line[0]));
   }

  $myTotalCosponsors = array();
  $totalCosponsorSql = "SELECT count(*)
                        FROM cosponsors
                        WHERE cosponsors.bill_id IN
                                                (SELECT bill_id
                                                 FROM sponsors
                                                 WHERE sponsor_id=
                                                      (SELECT id_govtrack
                                                       FROM persons
                                                       WHERE first_name ='".$repFirst."' and last_name='".$repLast."'))";

  $result5 = pg_query($dbconn, $totalCosponsorSql) or die('Query failed: ' . pg_last_error());
  while ($line = pg_fetch_row($result5)) {
    array_push($myTotalCosponsors, intval($line[0]));
  }


 $myUniqueCosponsors = array();
 $uniqueCosponsorSql = "SELECT count(*)
                        FROM
                            (SELECT distinct cosponsor_id
                             FROM cosponsors
                             WHERE cosponsors.bill_id IN
                                  (SELECT bill_id
                                   FROM sponsors
                                   WHERE sponsor_id=
                                                (SELECT id_govtrack
                                                 FROM persons
                                                 WHERE first_name ='".$repFirst."' and last_name='".$repLast."'))) as foo";
 $result6 = pg_query($dbconn, $uniqueCosponsorSql) or die('Query failed: ' . pg_last_error());
 while ($line = pg_fetch_row($result6)) {
   array_push($myUniqueCosponsors, intval($line[0]));
 }


$namedGroupCosponsors = array();
$namedGroupCosponsorSql = "SELECT first_name, last_name, count
                           FROM
                            (SELECT cosponsor_id, count(*)
                             FROM cosponsors
                             WHERE cosponsors.bill_id IN
                                  (SELECT bill_id
                                   FROM sponsors
                                   WHERE sponsor_id=
                                      (SELECT id_govtrack
                                       FROM persons
                                       WHERE first_name ='".$repFirst."' and last_name='".$repLast."'))
                                      GROUP BY cosponsor_id ORDER BY count DESC) as foo, persons
                                       WHERE id_govtrack=cosponsor_id";

$result7 = pg_query($dbconn, $namedGroupCosponsorSql) or die('Query failed: ' . pg_last_error());


  while ($line = pg_fetch_row($result7)) {
      array_push($namedGroupCosponsors, $line);
  }

  $myTopCosponsors = array();
  $myTopCosponsorsSql = "SELECT first_name, last_name
               FROM persons
               WHERE id_govtrack in
                (SELECT cosponsor_id
                 FROM
                    (SELECT cosponsor_id, count(*)
                     FROM cosponsors
                     WHERE cosponsors.bill_id IN
                        (SELECT bill_id
                         FROM sponsors
                         WHERE sponsor_id=
                            (SELECT id_govtrack
                             FROM persons
                             WHERE first_name ='".$repFirst."' and last_name='".$repLast."'))
                             GROUP BY cosponsor_id ORDER BY count DESC LIMIT 3) AS foo)";

    $result8 = pg_query($dbconn, $myTopCosponsorsSql) or die('Query failed: ' . pg_last_error());


      while ($line = pg_fetch_row($result8)) {
          array_push($myTopCosponsors, $line);
      }

    $myTopCosponsored = array();
    $myTopConsponsoredSql = "SELECT first_name, last_name
                             FROM
                                  (SELECT sponsor_id, count(*)
                                   FROM sponsors
                                   WHERE bill_id in
                                      (SELECT bill_id
                                       FROM cosponsors
                                       WHERE cosponsor_id=
                                            (SELECT id_govtrack
                                             FROM persons
                                             WHERE first_name ='".$repFirst."' and last_name='".$repLast."'))
                                             GROUP BY sponsor_id ORDER BY count DESC LIMIT 3) AS foo, persons WHERE id_govtrack=sponsor_id";

   $result9 = pg_query($dbconn, $myTopConsponsoredSql) or die('Query failed: ' . pg_last_error());


   while ($line = pg_fetch_row($result9)) {
       array_push($myTopCosponsored, $line);
   }
    $myAllCosponsored = array();
    $myAllCosponsoredSql = "SELECT first_name, last_name, count
                             FROM
                                  (SELECT sponsor_id, count(*)
                                   FROM sponsors
                                   WHERE bill_id in
                                      (SELECT bill_id
                                       FROM cosponsors
                                       WHERE cosponsor_id=
                                            (SELECT id_govtrack
                                             FROM persons
                                             WHERE first_name ='".$repFirst."' and last_name='".$repLast."'))
                                             GROUP BY sponsor_id ORDER BY count DESC) AS foo, persons WHERE id_govtrack=sponsor_id";

  $result10 = pg_query($dbconn, $myAllCosponsoredSql) or die('Query failed: ' . pg_last_error());


    while ($line = pg_fetch_row($result10)) {
        array_push($myAllCosponsored, $line);
    }

    $result_array = array($allSponsored,$sponsorCount, $allCosponsored, $cosponsorCount, $myTotalCosponsors, $myUniqueCosponsors, $namedGroupCosponsors, $myTopCosponsors, $myTopCosponsored, $myAllCosponsored);
    echo json_encode($result_array);
    pg_close($dbconn);

  ?>
