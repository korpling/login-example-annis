<?php

include_once 'Config.php';

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserCreator
 *
 * @author Thomas Krause <krauseto@hu-berlin.de>
 */
class UserCreator {

  const userTemplate = <<<XML
            <user>
              <name>myusername</name>
              <passwordHash></passwordHash>
              <expires></expires>
            </user>
XML;

  function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }
  
  private function createShiroHash($password) {
    $result = '$shiro1$SHA-256$1$' . hash('sha256', $password);
    
    return $result;
  }
  
  private function createUserXML($name, $password, $expirationHours = 72) {
    $xml = new SimpleXMLElement(self::userTemplate);
    $xml->name = $name;
    $xml->passwordHash = self::createShiroHash($password);

    // calculate expiration date from relative hours
    $expirationTimestamp = time() + ($expirationHours * 60 * 60);
    $xml->expires = date("c", $expirationTimestamp);
    
    $data = $xml->asXML();
    return $data;
  }
  
  private function sendUserCreationData($data, $name) {
    $curlConfig = array(
        CURLOPT_VERBOSE => true,
        CURLOPT_URL => Config::annisServiceURL . "/admin/users/" . urlencode($name),
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPAUTH => CURLAUTH_ANY,
        CURLOPT_USERPWD => Config::serviceUser . ":" . Config::servicePassword,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_POST => 1
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/xml',
        'Content-Length: ' . strlen($data)
    ));
    curl_setopt_array($ch, $curlConfig);

    $output = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $httpcode;
  }

  public function createTemporaryUser($name) {
    $password = self::generateRandomString(64);
    $data = self::createUserXML($name, $password);
    $httpCode = self::sendUserCreationData($data, $name);
    if($httpCode != 200) {
      trigger_error("Could not send user creation request, HTTP code is " . $httpCode);
    }
    
    
    return $password;
  }

}
