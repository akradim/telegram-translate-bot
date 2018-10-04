<?php
define('API_KEY','698429738:AAEbrhpMUPdSiNKiESiBkPDLSrSKNKo_Z3g'); 
$telegram = json_decode(file_get_contents('php://input'),true);
$user_id = $telegram['message']['chat']['id'];
$text = $telegram['message']['text'];
if($text == "/start")
		message($user_id , 'سلام به ربات مترجم طوبی خوش امدید. ');	
else{
		$translation = translate('en', 'fa', $text);
		message($user_id , $translation);	
	}
//Send Method
function bot($method,$datas=[]){
	 $url = "https://api.telegram.org/bot".API_KEY."/".$method; $ch = curl_init();
	  curl_setopt($ch,CURLOPT_URL,$url); 
	  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
	  curl_setopt($ch,CURLOPT_POSTFIELDS,$datas); 
	  $res = curl_exec($ch); 
	  if(curl_error($ch)){
		var_dump(curl_error($ch)); 
	  }else{ 
		return json_decode($res); 
	  } 
}
// Send Message 		
function message($user_id , $text){
	bot(
	'sendMessage', [
		'chat_id'=> $user_id,
		'text'=> $text,
		'reply_markup' => json_encode( ['keyboard' => [["فارسی به انگلیسی","انگلیسی به فارسی"
]],'one_time_keyboard'=>true,'resize_keyboard'=>true ] )
	]);				
}
//translate from google
function translate($source, $target, $text) {	
    $response 		= requestTranslation($source, $target, $text);
    $translation 	= getSentencesFromJSON($response);
    return $translation;
}
function requestTranslation($source, $target, $text) {
    $url = "https://translate.google.com/translate_a/single?client=at&dt=t&dt=ld&dt=qca&dt=rm&dt=bd&dj=1&hl=es-ES&ie=UTF-8&oe=UTF-8&inputm=2&otf=2&iid=1dd3b944-fa62-4b55-b330-74909a99969e";
    $fields = array(
        'sl' => urlencode($source),
        'tl' => urlencode($target),
        'q' => urlencode($text)
    );
    $fields_string = "";
    foreach($fields as $key=>$value) {
        $fields_string .= $key.'='.$value.'&';
    }
    rtrim($fields_string, '&');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'AndroidTranslate/5.3.0.RC02.130475354-53000263 5.1 phone TRANSLATE_OPM5_TEST_1');
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
function getSentencesFromJSON($json) {
    $sentencesArray = json_decode($json, true);
    $sentences = "";
foreach ($sentencesArray["sentences"] as $s) {
        $sentences .= $s["trans"];
    }
    return $sentences;
}
