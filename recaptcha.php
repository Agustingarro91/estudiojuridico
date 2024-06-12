<?php
require 'vendor/autoload.php';

// Incluye las dependencias de Google Cloud con Composer
use Google\Cloud\RecaptchaEnterprise\V1\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason;

/**
  * Crea una evaluación para analizar el riesgo de una acción de la IU.
  * @param string $recaptchaKey La clave reCAPTCHA asociada con el sitio o la aplicación
  * @param string $token El token generado obtenido del cliente.
  * @param string $project El ID del proyecto de Google Cloud.
  * @param string $action El nombre de la acción que corresponde al token.
  */
function create_assessment(
  string $recaptchaKey,
  string $token,
  string $project,
  string $action
): void {
  // Crea el cliente de reCAPTCHA.
  // TODO: almacena en caché el código de generación de clientes (recomendado) o llama a client.close() antes de salir del método.
  $client = new RecaptchaEnterpriseServiceClient();
  $projectName = $client->projectName($project);

  // Establece las propiedades del evento para realizar un seguimiento.
  $event = (new Event())
    ->setSiteKey($recaptchaKey)
    ->setToken($token);

  // Crea la solicitud de evaluación.
  $assessment = (new Assessment())
    ->setEvent($event);

  try {
    $response = $client->createAssessment(
      $projectName,
      $assessment
    );

    // Verifica si el token es válido.
    if ($response->getTokenProperties()->getValid() == false) {
      printf('The CreateAssessment() call failed because the token was invalid for the following reason: ');
      printf(InvalidReason::name($response->getTokenProperties()->getInvalidReason()));
      return;
    }

    // Verifica si se ejecutó la acción esperada.
    if ($response->getTokenProperties()->getAction() == $action) {
      // Obtén la puntuación de riesgo y los motivos.
      // Para obtener más información sobre cómo interpretar la evaluación, consulta:
      // https://cloud.google.com/recaptcha-enterprise/docs/interpret-assessment
      printf('The score for the protection action is:');
      printf($response->getRiskAnalysis()->getScore());
    } else {
      printf('The action attribute in your reCAPTCHA tag does not match the action you are expecting to score');
    }
  } catch (exception $e) {
    printf('CreateAssessment() call failed with the following error: ');
    printf($e);
  }
}

// PENDIENTE: Reemplaza el token y las variables de acción de reCAPTCHA antes de ejecutar la muestra.
create_assessment(
   '6LcP4fUpAAAAAHtgMeeA9-2NxLT8KjqOSsx4s8K1',
   'YOUR_USER_RESPONSE_TOKEN',
   'estudio-antunez-1718214521074',
   'YOUR_RECAPTCHA_ACTION'
);
?>