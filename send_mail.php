<?php
if (isset($_POST)) {
  error_reporting(0);

  $name = $_POST["name"];
  $email = $_POST["email"];
  $subject = "Nueva consulta";/* $_POST["subject"] */
  $comments = $_POST["comments"];

  $ip = $_SERVER['REMOTE_ADDR'];
  $captcha = $_POST['g-recaptcha-response'];
  $secretKey = "";

  $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha&remoteip=$ip");

  $atributos = json_decode($response, TRUE);  

  $error = "";

  if(!$name || $email || $comments){
    $error = "Hay campos vacios no se puede enviar el mensaje!"
  }

  if(!$atributos['succes']){
    $error = "Captcha no marcado"
  }




  
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

  if($send_mail && !$error) {
    $res = [
      "err" => false,
      "message" => "Tus datos han sido enviados, pronto te responderemos"
    ];
  } else {
    $res = [
      "err" => true,
      "message" => $error
    ];
  }

  //header("Access-Control-Allow-Origin: https://jonmircha.com");
  header("Access-Control-Allow-Origin: *");
  header( 'Content-type: application/json' );
  echo json_encode($res);
  exit;
}
