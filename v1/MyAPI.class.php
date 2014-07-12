<?php

require_once 'API.class.php';

class MyAPI extends API
{
    var $config;

    public function __construct($request, $origin) {
      parent::__construct($request);

      require_once 'configuration.php';
      $this->config = new Configuration;

      switch($origin)
      {
        case 'http://'.$this->config->access_url:
        case 'https://'.$this->config->access_url:
        case $this->config->access_url:
          break;
          
        default:
          throw new Exception('Invalid access domain');
      }

    }

     /**
      * getData Endpoint
      *
      * /getData/document_id?key
      */
     protected function getData() {
        if ($this->method == 'GET') {
          require_once 'sheet.api.php';

          // http://stackoverflow.com/questions/5262857/5-minute-file-cache-in-php  
          
          $cache_file = '../cache/'.$this->verb.'.json';
          
          $cache_time = time() - 60 * $this->config->cache_time;
          
          if ($this->config->dev_mode == false
              && file_exists($cache_file)
              && (filemtime($cache_file) > $cache_time)) {
            // Cache file is less than five minutes old. 
            // Don't bother refreshing, just use the file as-is.
            $data = file_get_contents($cache_file);
          } else {
            // Our cache is out-of-date, so load the data from our remote server,
            // and also save it over our cache for next time.
            $document = new Sheet();
            $document->url = 'https://spreadsheets.google.com/feeds/list/'.$this->verb.'/'.$this->args[0].'/public/basic?hl=en_US&alt=json';
            
            $document->getDocument();
            $document->parseDocument();
            
            $title = $document->readTitle();
            $settings = $document->readSettings();
            $entries = $document->readEntries();
            
            $entries = array("groups" => $entries);
            
            $data = json_encode(array_merge($settings, $entries));
            
            file_put_contents($cache_file, $data, LOCK_EX);
          }

          return $data;

        } else {
          return "Only accepts GET requests";
        }
     }
 }