<?php
$rawData = fopen('data.json', 'r');
$parsedData = fopen('result.csv', 'a');
$currentLine = 0;
$chunkCounter = 0;
$chunk = '';

if (!$rawData || !$parsedData)
    die('failed to read the raw data');

// First line is settings, let's pass it
fgets($rawData);

while (($line = fgets($rawData)) !== false) {
    $currentLine++;

    if ($currentLine > 1000)
        break;

    // Only odd line numbers - except first line - contains data
    if ($currentLine % 2 !== 0)
        continue;

    $data = json_decode(json_decode($line)->message);
    $username = $data->username ?? '""';

    $chunk .= "$data->id,$data->phone,$username" . PHP_EOL;
    $chunkCounter++;

    if ($chunkCounter === 500000) {
        fwrite($parsedData, $chunk);
        $chunk = '';
        $chunkCounter = 0;
        echo $currentLine . PHP_EOL;
    }
}

fwrite($parsedData, $chunk);
$chunk = '';
$chunkCounter = 0;
echo $currentLine . PHP_EOL;

fclose($rawData);
