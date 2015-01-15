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
include_once '../lib/httpful.phar';

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

  private function addGroup($xml, $groupName) {
    // check if group already exists
    foreach ($xml->group as $g) {
      if((string) $g == $groupName ) {
        return;
      }
    }
    // add the new group since it does not exist
    $xml->addChild("group", $groupName);
  }

  private function createUserXML($name, $password,
          $oldGroups = array(),
          $expirationHours = 72) {
    $xml = new SimpleXMLElement(self::userTemplate);
    $xml->name = $name;
    $xml->passwordHash = self::createShiroHash($password);

    // calculate expiration date from relative hours
    $expirationTimestamp = time() + ($expirationHours * 60 * 60);
    $xml->expires = date("c", $expirationTimestamp);

    self::addGroup($xml, "anonymous");
    // add all groups that were given manually before
    foreach($oldGroups as $g)
    {
      self::addGroup($xml, trim($g));
    }
    // TODO: add more groups depending on the identity provider
    
    $data = $xml->asXML();
    return $data;
  }
  
  private function getGroupsForUser($name) {    
    $response = Httpful\Request::get(Config::annisServiceURL . '/admin/users/' . urlencode($name))
            ->authenticateWith(Config::serviceUser, Config::servicePassword)
            ->send();
    if($response->code == 200 && isset($response->body->group))
    {
      return $response->body->group;
    }
    // return empty array per default
    return array();
  }

  private function sendUserCreationData($data, $name) {    
    $response = Httpful\Request::put(Config::annisServiceURL . '/admin/users/' . urlencode($name))
            ->body($data)
            ->sendsXml()
            ->authenticateWith(Config::serviceUser, Config::servicePassword)
            ->send();

    return $response->code;
  }

  public function createTemporaryUser($name) {
    
    $oldGroups = self::getGroupsForUser($name);
    
    $password = self::generateRandomString(64);
    $data = self::createUserXML($name, $password,$oldGroups);
    $httpCode = self::sendUserCreationData($data, $name);
    if ($httpCode != 200) {
      trigger_error('Could not send user creation request, HTTP code is ' . $httpCode);
    }


    return $password;
  }

}
