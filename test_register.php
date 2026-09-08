<?php
$data = [
    'nama_lengkap' => 'Test User',
    'username' => 'testuser' . rand(100, 999),
    'email' => 'asdfqwerzxcv' . rand(1000, 9999) . '@gmail.com',
    'password' => 'Password123',
    'password_confirmation' => 'Password123'
];

$ch = curl_init('http://localhost:8000/api/register');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpcode\n";
echo "Response: $response\n";
