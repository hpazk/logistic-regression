<?php

class LogisticRegression
{

    public $data_training = array();
    public $labels;
    public $len_samples;
    public $len_features;
    public $scaling;
    public $correct = 0;
    public $steps = 20000;
    public $learning_rate = 0.05;

    // public $weights;

    function __construct($raw_data)
    {
        $this->data_training = $raw_data[0];
        $this->len_samples = sizeof($raw_data[0]);
        $this->len_features = sizeof($raw_data[0][0]);
        $this->labels = $raw_data[1];

        $this->scaling = $this->calc_features_scaling($raw_data[0]);
    }


    public function get_weights()
    {
        $weights = array();

        for ($i = 0; $i < $this->len_features + 1; $i++) {
            $weights[$i] = mt_rand() / mt_getrandmax() * 5.0;
        }

        return $weights;
    }

    // to hold updates for weights
    function gradient_descent()
    {
        $weights = $this->get_weights();
        $temp = array();
        for ($n = 0; $n < $this->steps; $n++) {

            for ($j = 0; $j < $this->len_features + 1; $j++) {
                $sum_m = 0.0;
                for ($i = 0; $i < $this->len_samples; $i++) {
                    $scaled_data = $this->scale($this->data_training[$i], $this->scaling);
                    $h = $this->hypothesis($scaled_data, $weights);
                    $part = ($h - $this->labels[$i]) * ($j == 0 ? 1.0 : $scaled_data[$j - 1]);
                    $sum_m = $sum_m + $part;
                }
                $temp[$j] = $weights[$j] - $this->learning_rate * $sum_m / $this->len_samples;
            }

            $weights = $temp;
        }
        // $this->weights = $temp;
        return $temp;
    }

    public function correctness()
    {
        $weights = $this->gradient_descent();

        for ($i = 0; $i < $this->len_samples; $i++) {
            $predict = $this->predict($this->scale($this->data_training[$i], $this->scaling), $weights);
            // printf('Input: %-16s actual: %d, predict: %d', $this->vector_to_str($this->data_training[$i]), $this->labels[$i], $predict);
            // if ($this->labels[$i] != $predict) {
            //     print(' - miss');
            // }
            // print('\n');

            if ($predict == $this->labels[$i]) {
                $this->correct++;
            }
        }

        return $this->correct / $this->len_samples * 100.0;
    }

    public function prediction($preqtest)
    {
        $weights = $this->gradient_descent();
        // $input = array();
        $P = array();

        for ($i = 0; $i < sizeof($preqtest); $i++) {
            $predict = $this->predict($this->scale($preqtest[$i], $this->scaling), $weights);

            // printf('Input: %-16s predict: %d', $this->vector_to_str($preqtest[$i]), $this->labels[$i], $predict);

            // array_push($input, sprintf('%-16s', $this->vector_to_str($preqtest[$i])));
            array_push($P, sprintf('%d', $predict));
            // if ($predict == $this->labels[$i]) {
            //     $this->correct++;
            // }
        }
        print_r($P);

        // return $this->correct / $this->len_samples * 100.0;
    }

    public function predict($input, $weights)
    {
        $output = $this->hypothesis($input, $weights);
        if ($output >= 0.50) {
            $predict = 1;
        } else {
            $predict = 0;
        }

        return $predict;
    }

    function scale($input, $scaling)
    {
        foreach ($input as $f => &$value) {
            $value = ($value - $scaling['mean'][$f]) /
                $scaling['variance'][$f];
        }
        return $input;
    }



    function hypothesis($x, $weights)
    {
        $score = $weights[0];
        $k = sizeof($x);

        for ($i = 0; $i < $k; $i++) {
            $score += $weights[$i + 1] * $x[$i];
        }

        return 1.0 / (1.0 + exp(-$score));
    }

    public function calc_features_scaling($data)
    {

        $mins = array_fill(0, $this->len_features, INF);
        $maxs = array_fill(0, $this->len_features, -INF);
        $sums = array_fill(0, $this->len_features, 0);
        $scalings = array(
            'mean' => array(),
            'variance' => array()
        );
        $N = sizeof($data);
        foreach ($data as $i => $row) {
            foreach ($row as $f => $value) {
                if ($value > $maxs[$f]) {
                    $maxs[$f] = $value;
                }
                if ($value < $mins[$f]) {
                    $mins[$f] = $value;
                }

                $sums[$f] += $value;
            }
        }

        for ($f = 0; $f < $this->len_features; $f++) {
            $scalings['mean'][$f] = $sums[$f] / $N;
            $scalings['variance'][$f] = $maxs[$f] - $mins[$f];
            if ($scalings['variance'][$f] == 0) {
                throw new Exception('Feature #$f has the same value in all the samples, invalid data');
            }
        }

        return $scalings;
    }

    public function vector_to_str($x)
    {
        return '[' . implode(', ', $x) . ']';
    }
}
