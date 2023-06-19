<?php
	include_once('env.php');

	// Авторизация
	$params = [];
	$params['login'] = $_ENV['login'];
	$params['password'] = $_ENV['password'];
	$wsdl = $_ENV['host'];
	
	$client = new SoapClient($wsdl,$params);
	
	//
	$data = [];	
	$data['applicationId'] = '12345678';
	$data['productId']='3523309775';
	
	//Данные страхователя
	$person = [];
	$person['INSURER_FIRSTNAME'] = 'Тест FIRSTNAME';
	$person['INSURER_LASTNAME'] = 'Тест LASTNAME';
	$person['INSURER_SURNAME'] = 'Тест SURNAME';
	$person['INSURER_EMAIL'] = 'test@test.ru';
	$person['INSURER_BIRTHDAY'] = '01.01.1980';
	$person['PASSPORT_NUMBER'] = '1010 123456';
	$person['INSURER_PHONE'] = '79501111111';
	$person['INSURER_ADDRESS'] = 'Тестовый адрес';
	$person['PASSPORT_DATE'] = '01.01.2000';
	
	$data['person'] = $person;
	
	try {
		$response = $client->obtainCertificate($data);
		if($response->result->code == 'OK'){
			$file = 'file.pdf';
			$pdf_decoded = base64_decode ($response->cert->certFile);
			file_put_contents($file, $pdf_decoded);
			if (ob_get_level()) {
			  ob_end_clean();
			}
			
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . basename($file));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			
			if ($fd = fopen($file, 'rb')) {
			  while (!feof($fd)) {
				print fread($fd, 1024);
			  }
			  fclose($fd);
			}
			exit;
		}else{
			print_r($response->result);
		}
	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
	