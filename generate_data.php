<?php

function generateData(): array
{
    $data = [];
    for ($day = 0; $day < 20; $day++) {
        $dayPredictions = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $dayPredictions[] = [
                'time' => sprintf("%02d:00", $hour),
                'value' => rand(15, 30),
            ];
        }
        $data['predictions'][] = [
            '-scale' => 'Celsius',
            'city' => 'Amsterdam',
            'date' => date('Ymd', strtotime('now') + $day*24*60*60),
            'prediction' => $dayPredictions,
        ];
    }

    return $data;
}

$data = generateData();

$json = json_encode($data, JSON_PRETTY_PRINT);
file_put_contents(__DIR__ . '/files/iamsterdam.json', $json);

$file = fopen(__DIR__ . '/files/weathercom.csv', 'w');
fputcsv($file, ['-scale', 'city', 'date', 'prediction__time', 'prediction__value'], ',');
foreach ($data['predictions'] as $row) {
    foreach($row['prediction'] as $prediction) {
        $line = [
            $row['-scale'],
            $row['city'],
            $row['date'],
            $prediction['time'],
            $prediction['value'],
        ];
        fputcsv($file, $line, ',');
    }
}
fclose($file);

$xml = new SimpleXMLElement('<predictions/>');

foreach ($data['predictions'] as $row) {
    $predictions = $xml->addChild('prediction');
    $predictions->addAttribute('scale', $row['-scale']);
    $predictions->addChild('city', $row['city']);
    $predictions->addChild('date', $row['date']);
    foreach ($row['prediction'] as $prediction) {
        $predictionXml = $predictions->addChild('prediction');
        $predictionXml->addChild('time', $prediction['time']);
        $predictionXml->addChild('value', $prediction['value']);
    }

}
$xml->asXML(__DIR__ . '/files/bbc.xml');