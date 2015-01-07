<?php
/*
 * Copyright 2015 Corpuslinguistic working group Humboldt University Berlin.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

include_once 'Config.php';


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
    // TODO: generate random salt
    $salt = 'aDsfSft%&';
    // hash with sha256
    $hash = hash('sha256', $salt . $password, true);

    $result = '$shiro1$SHA-256$1$' . base64_encode($salt) . '$' . base64_encode($hash);
    
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
        CURLOPT_URL => Config::annisServiceURL . '/admin/users/' . urlencode($name),
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPAUTH => CURLAUTH_ANY,
        CURLOPT_USERPWD => Config::serviceUser . ':' . Config::servicePassword,
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
      trigger_error('Could not send user creation request, HTTP code is ' . $httpCode);
    }
    
    
    return $password;
  }

}
