<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST)) {

  $name = $_POST["name"];
  $email = $_POST["email"];
  $subject = "Nueva consulta";/* $_POST["subject"] */
  $comments = $_POST["comments"];

  $ip = $_SERVER['REMOTE_ADDR'];
  $captcha = $_POST['token'];
  $secretKey = "";

  $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha&remoteip=$ip");

  $atributos = json_decode($response, TRUE);  

  $error = "";

  if(!$name || !$email || !$comments){
    $error = "Hay campos vacios no se puede enviar el mensaje!";
  }
  
  if(!$atributos['success']){    
    $error = $atributos['error-codes'];
  }

  if($atributos['score'] < 0.7){    
    $error = "Robot";
  }


  if(!$error){
  
  $domain = $_SERVER["HTTP_HOST"];
  $to = "agusdiazgarro@gmail.com";
  $subject_mail = "Nueva consulta de $name.";
  $message = "
    <p>
      Datos enviados desde el formulario del sitio <b>$domain</b>
    </p>
    <ul>
      <li>Nombre: <b>$name</b></li>
      <li>Email: <b>$email</b></li>
      <li>Asunto: $subject</li>
      <li>Comentarios: $comments</li>
    </ul>
  ";
  $headers = "MIME-Version: 1.0\r\n" . "Content-Type: text/html; charset=utf-8\r\n" . "From: Nueva consulta <no-reply@$domain>";


  
    $send_mail = mail($to, $subject_mail, $message, $headers);
  }

  if($send_mail) {
    $res = [
      "err" => False,
      "message" => "Tus datos han sido enviados, pronto te responderemos"
    ];
  } else {
    $res = [
      "err" => True,
      "message" => $error
    ];
  }


} else {
    $res = [
      "err" => true,
      "message" => 'Metodo incorrecto'
    ];
}

  //header("Access-Control-Allow-Origin: https://jonmircha.com");
  header("Access-Control-Allow-Origin: *");
  header( 'Content-type: application/json' );
  echo json_encode($res);
  exit;