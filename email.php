<?php

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.farjan.dev/email-send.php?method=fetch',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));
$response = curl_exec($curl);
curl_close($curl);

if($response) {
	$data = json_decode($response, true);
	
	$api_key = '7107741e21fdc34489ae9c75b0565295-3af52e3b-5c0310b0';
	$domain = 'my.zsshopbd.com';
	$recipient = $data['email'];

	$url = "https://api.mailgun.net/v3/$domain/messages";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_USERPWD, "api:$api_key");
	curl_setopt($ch, CURLOPT_POSTFIELDS, [
		'from' => 'Roasted Gaming <roasted-gaming@my.zsshopbd.com>',
		'to' => $recipient,
		'subject' => 'Hello ' . $data['username'] . '!',
		'text' => 'This will be the text-only version',
		'o:tracking-opens' => 'yes',
		'html' => '<!doctypehtml><meta charset=UTF-8><meta content="width=device-width,initial-scale=1"name=viewport><title>🔥 বিশেষ অফার - সীমিত সময়ের জন্য! 🔥</title><body style=font-family:Arial,sans-serif;background-color:#f7f7f7;margin:10px 0;padding:0><div style="max-width:600px;background-color:#fff;margin:20px auto;padding:20px;border-radius:10px;box-shadow:0 0 10px rgba(0,0,0,.1);text-align:center"><div style=font-size:24px;font-weight:700;color:#ff5722;margin-bottom:10px>🔥✨ সীমিত সময়ের বিশেষ অফার! ✨🔥</div><div style="font-size:18px;color:#333;margin:15px 0">📢 <strong>হোলসেল সেল – স্টক আনলিমিটেড! 🎉</strong></div><div style="font-size:16px;background:#ffeb3b;padding:10px;border-radius:5px;display:inline-block;margin:10px 0">✅ <strong>Weekly & Level Up – ১৩৯ টাকা</strong><br>✅ <strong>Monthly & 1240 Diamonds – ৭৩৯ টাকা</strong><br>✅ <strong>2530 Diamonds – ১৪৯৯ টাকা</strong></div><div style="font-size:18px;color:#333;margin:15px 0">🚀 শেয়ার করে ২৫ Diamonds ফ্রি নিন! 💥</div><a href=https://zsshopbd.com style="background-color:#ff5722;color:#fff;text-decoration:none;padding:12px 20px;border-radius:5px;font-size:18px;display:inline-block;margin-top:20px">📲 অর্ডার করুন এখনই!</a><div style=margin-top:20px;font-size:14px;color:#777>📅 <strong>অফার শেষ হওয়ার আগে অর্ডার করুন!</strong><br>🔥 <strong>ROASTED GAMING</strong></div></div>'
	]);

	$response2 = curl_exec($ch);
	curl_close($ch);

	echo json_encode(['status' => true, 'message' => 'Mail sent successfully!', 'data' => ['email' => $response2]]);


}
