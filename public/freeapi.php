<?php

    //This fucntion can be used in any PHP framework like laravel, wordpress, drupal, cakephp etc.

  /*   function aztro($sign, $day) {
        $aztro = curl_init('https://aztro.sameerkumar.website/?sign='.$sign.'&day='.$day);
        curl_setopt_array($aztro, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            )
        ));
        $response = curl_exec($aztro);
        if($response === FALSE){
            die(curl_error($aztro));
        }
        $responseData = json_decode($response, TRUE);
        return $responseData;
    }

    $ObjData = aztro('aries', 'today');
	echo "<pre>";
	print_r($ObjData);
   // var_dump($ObjData); */
$sign = 'aries';
$day = 'today';
$aztro = curl_init();
curl_setopt_array($aztro, array(
  CURLOPT_URL => "https://aztro.sameerkumar.website/?sign=$sign&day=$day",
  CURLOPT_POST => TRUE,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => array(
	'Content-Type: application/json'
  )
));

$response = curl_exec($aztro);
if($response === FALSE){
	die(curl_error($aztro));
}
$responseData = json_decode($response, TRUE);

echo "<pre>";
	print_r($responseData);
	echo $responseData['description'];

?>