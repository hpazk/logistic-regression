<?php
include_once('src/LogisticRegression/LogisticRegression.php');
// data training
$prequisites = array(
    array(-11., 2.6,  1.),
    array(8., 0.78, 1.),
    array(15., 4.2,  0.),
    array(-16., 0.18, 0.),
    array(3., 1.1,  0.),
    array(7., 1.4,  1.),
    array(-3., 1.44, 1.),
    array(-7., 0.52, 0.),
    array(30., 0.82, 1.),
    array(20., 1.32, 0.),
);

$expected = array(
    0.,
    1.,
    0.,
    0.,
    1.,
    1.,
    1.,
    0.,
    0.,
    1.0
);

$raw_data = array();
$raw_data[0] = $prequisites;
$raw_data[1] = $expected;


$prediction = new LogisticRegression($raw_data);

printf("Correctness = %.0f%%\n", $prediction->correctness());
