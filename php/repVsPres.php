<?php


//get representative 1
$rep1First = $_POST['rep1First'];
$rep1Last = $_POST['rep1Last'];


$dbconn = pg_connect("dbname=us_congress host=localhost user=postgres password=kushal941")
or die('Could not connect: ' . pg_last_error());
$chamber = array();
$agreeWithPres_arr = array();
$disagreeWithPres_arr = array();

$chamberSql = "SELECT type
             FROM persons, person_roles
             WHERE persons.id = person_roles.person_id AND first_name = '".$rep1First."' and last_name = '".$rep1Last."' AND (EXTRACT(YEAR from start_date) >= 2011 AND EXTRACT(YEAR from start_date) <= 2014)";

$chamberRes = pg_query($dbconn, $chamberSql) or die('Query failed: ' . pg_last_error());
while($line = pg_fetch_row($chamberRes)){
  array_push($chamber,$line);
}

$presAgreeSQL = "SELECT
EXTRACT(year FROM date) as year, first_name,last_name,count(id) as agreeCount
FROM
(SELECT president_person_vote.id, position, vote, person_id, first_name, middle_name, last_name,date
  FROM
  (SELECT presidential_vote.id, position, person_id, vote, date
    FROM
    (SELECT *
      FROM presidential_support JOIN votes
      ON votes.chamber = presidential_support.chamber and votes.number = presidential_support.vote_number and presidential_support.session = votes.session
    )AS presidential_vote
    JOIN person_votes
    ON id = vote_id AND (((vote = 'Yea' or vote = 'Aye') and position = 'support') OR (((vote = 'No' or vote = 'Nay') and position = 'against')))
  ) AS president_person_vote
  JOIN persons
  ON person_id = persons.id
) AS find_name
WHERE first_name = '".$rep1First."' and last_name = '".$rep1Last."'
GROUP BY first_name, middle_name, last_name, year";

$result1 = pg_query($dbconn, $presAgreeSQL) or die('Query failed: ' . pg_last_error());

while ($line = pg_fetch_row($result1)) {
  array_push($agreeWithPres_arr, intval($line[3]));
}

pg_free_result($result1);

$presDisagreeSQL = "SELECT
EXTRACT(YEAR FROM date) AS year, first_name,last_name,count(id) as disagreeCount
FROM
(SELECT president_person_vote.id, position, vote, person_id, first_name, middle_name, last_name, date
  FROM
  (SELECT presidential_vote.id, position, person_id, vote, date
    FROM
    (SELECT *
      FROM presidential_support JOIN votes
      ON votes.chamber = presidential_support.chamber and votes.number = presidential_support.vote_number and presidential_support.session = votes.session
    )AS presidential_vote
    JOIN person_votes
    ON id = vote_id AND (((vote = 'Yea' or vote = 'Aye') and position = 'against') OR ((vote = 'No' or vote = 'Nay') and position = 'support'))
  ) AS president_person_vote
  JOIN persons
  ON person_id = persons.id
) AS find_name
WHERE first_name = '".$rep1First."' and last_name = '".$rep1Last."'
GROUP BY first_name, middle_name, last_name, EXTRACT(YEAR FROM date)";

$result2 = pg_query($dbconn, $presDisagreeSQL) or die('Query failed: ' . pg_last_error());

while ($line = pg_fetch_row($result2)) {
  array_push($disagreeWithPres_arr, intval($line[3]));
}


pg_free_result($result2);

$resultArray = array($agreeWithPres_arr, $disagreeWithPres_arr, $chamber);

echo json_encode($resultArray);

pg_close($dbconn);

?>
