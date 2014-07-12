<?php

function getCORS($config)
{


  if ($config->dev_mode) {
    // Requests from the same server don't have a HTTP_ORIGIN header
    $_SERVER['HTTP_ORIGIN'] = 'http://'.$config->access_url;
  }

  if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
  }

  switch ($_SERVER['HTTP_ORIGIN']) {
    case 'http://'.$config->access_url:
    case 'https://'.$config->access_url:

      header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
      header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
      header('Access-Control-Max-Age: 1000');
      header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
      return true;
      break;

    default:
      return false;
      break;
  }

  return;

}
