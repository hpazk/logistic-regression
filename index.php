<?php
include_once('src/LogisticRegression/LogisticRegression.php');
include_once('modules/csv_to_array.php');

$raw_data = csv_to_array('doc/Social_Network_Ads_converted.csv');


function convert_to_indexed_array($raw_data)
{
    $num_array = array();
    for ($i = 0; $i < sizeof($raw_data); $i++) {
        $tmp = array();

        foreach ($raw_data[$i] as $key => $item) {
            $t = $key;
            $t;
            array_push($tmp, $item);
        }
        array_push($num_array, $tmp);
    }
    return $num_array;
}

function getClassModel($raw_data)
{
    for ($i = 0; $i < sizeof($raw_data); $i++) {
        array_pop($raw_data[$i]);
    }
    return $raw_data;
}

function getExpectedVariable($raw_data, $label = null)
{
    $tmp = array();

    for ($i = 0; $i < sizeof($raw_data); $i++) {
        if ($label) {

            array_push($tmp, ($raw_data[$i][$label]));
        } else {
            array_push($tmp, ($raw_data[$i][sizeof($raw_data[$i]) - 1]));
        }
    }

    return $tmp;
}

$prequisites_with_label = getClassModel($raw_data);
$prequisites = convert_to_indexed_array($prequisites_with_label);
// print_r($prequisites);
$expected = getExpectedVariable($raw_data, 'Purchased');
// print_r($expected);

$raw_data = array();
$raw_data[0] = $prequisites;
$raw_data[1] = $expected;


$logit = new LogisticRegression($raw_data);

printf('Correctness = %.0f%%', $logit->correctness());
echo PHP_EOL;

echo 'Prediction:';
echo PHP_EOL;

$preqtest = array(

    array(35, 20000, 1),
    array(26, 43000, 0),
    array(27, 57000, 0),
    array(19, 76000, 1),
    array(32, 150000, 0),
    array(45, 135000, 0),
    array(28, 78000, 1),
    array(29, 65000, 0),
    array(19, 150000, 1),
);

$logit->prediction($preqtest);
