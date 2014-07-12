<?php

require_once 'API.class.php';

class MyAPI extends API
{
    var $config;

    public function __construct($request, $origin) {
      parent::__construct($request);

      require_once 'configuration.php';
      $this->config = new Configuration;

      if (!array_key_exists('apiKey', $this->request)) {
        throw new Exception('No API Key provided');
      } else if (!$APIKey->verifyKey($this->request['apiKey'], $origin)) {
        throw new Exception('Invalid API Key');
      } else if (array_key_exists('token', $this->request) &&
        !$User->get('token', $this->request['token'])) {

        throw new Exception('Invalid User Token');
      }

    }

    /**
     * Example of an Endpoint
     */
     protected function example() {
        if ($this->method == 'GET') {
            return "Hello World";
        } else {
            return "Only accepts GET requests";
        }
     }

     /**
     * Example of an Endpoint
     */
     protected function getData() {
        if ($this->method == 'GET') {
          require_once 'sheet.api.php';

          $request = $_GET;

          $document = new Sheet();
          $document->url = 'https://spreadsheets.google.com/feeds/list/'.$request['doc'].'/'.$request['key'].'/public/basic?hl=en_US&alt=json';

          $document->getDocument();
          $document->parseDocument();

          $title = $document->readTitle();
          $settings = $document->readSettings();
          $entries = $document->readEntries();

          $entries = array("groups" => $entries);

          $data = json_encode(array_merge($settings, $entries));

          return $data;

        } else {
          return "Only accepts GET requests";
        }
     }
 }
