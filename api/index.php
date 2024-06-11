<?php 
// require __DIR__ . '../index.php'; 
// echo 'mateus';

header('content-type: application/json');
echo json_encode(['time' => time(), 'date' => date('d.m.Y'), 'tech' => 'Vercel']);
?>