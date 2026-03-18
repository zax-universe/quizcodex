<?php
header('Content-Type: application/json');

$apiUrl = 'https://ikyyzyyrestapi.my.id/games/pilihanganda';

$ctx = stream_context_create([
    'http' => [
        'timeout' => 8,
        'method'  => 'GET',
        'header'  => "User-Agent: QuizCode/1.0\r\n",
    ]
]);

$response = @file_get_contents($apiUrl, false, $ctx);

if ($response === false) {
    // Fallback soal jika API error
    $fallback = [
        ['id'=>1,'category'=>'Teknologi','question'=>'Perangkat yang digunakan untuk menyimpan data secara permanen disebut?','options'=>['a'=>'RAM','b'=>'CPU','c'=>'Hard Disk','d'=>'Monitor'],'answer'=>'c'],
        ['id'=>2,'category'=>'Sains','question'=>'Planet terbesar di tata surya adalah?','options'=>['a'=>'Saturnus','b'=>'Jupiter','c'=>'Uranus','d'=>'Neptunus'],'answer'=>'b'],
        ['id'=>3,'category'=>'Matematika','question'=>'Hasil dari 15 x 8 adalah?','options'=>['a'=>'110','b'=>'112','c'=>'120','d'=>'128'],'answer'=>'c'],
        ['id'=>4,'category'=>'Teknologi','question'=>'HTML adalah singkatan dari?','options'=>['a'=>'Hyper Text Markup Language','b'=>'High Text Machine Language','c'=>'Hyper Transfer Markup Link','d'=>'Hyper Text Modern Layout'],'answer'=>'a'],
        ['id'=>5,'category'=>'Umum','question'=>'Ibu kota Indonesia adalah?','options'=>['a'=>'Surabaya','b'=>'Bandung','c'=>'Jakarta','d'=>'Medan'],'answer'=>'c'],
    ];
    $pick = $fallback[array_rand($fallback)];
    echo json_encode(['status'=>true,'creator'=>'QuizCode-Fallback','result'=>$pick]);
    exit;
}

$data = json_decode($response, true);
if (!$data || !isset($data['status']) || !$data['status']) {
    echo json_encode(['status'=>false,'message'=>'API error']);
    exit;
}

echo $response;
