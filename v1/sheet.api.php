<?php

/**
 * class document
 *
 * retrieves the document from google
 */
class Sheet
{

  /**
   * @var string The URL to the file
   */
  public $url = null;

  /**
   * @var string The contents of the file
   */
  public $document = null;

  /**
   * @var object json data of document
   */
  private $documentJson = null;

  /**
   * @var string Title of the sheet
   */
  public $title = null;

  /**
   * @var object entries in sheet
   */
  public $entries = null;

  /**
   * @var object settings of column names
   */
  public $settings = null;



  /**
   * Contstruct function
   * the function "__construct()" automatically starts whenever an object of this class is created
   * $document = new Document();
   */
  public function __construct()
  {

  }

  /**
   * getDocument
   *
   * use cURl to get document
   */
  public function getDocument()
  {
    //URL of targeted site
    $ch = curl_init();

    // set URL and other appropriate options
    curl_setopt($ch, CURLOPT_URL, $this->url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


    // grab URL and pass it to the browser

    $this->document = curl_exec($ch);

    //echo $output;

    // close curl resource, and free up system resources
    curl_close($ch);

  }

  /**
   * parseDocument
   *
   * turn document into json data
   */
  public function parseDocument()
  {
   $this->documentJson = json_decode($this->document);
  }

  /**
   * readTitle
   *
   * get title of sheet for game
   */
  public function readTitle()
  {


   $this->title = $this->documentJson->feed->title->{'$t'};

   return $this->title;
  }

  /**
   * readSettings
   *
   * get settings from first row
   */
  public function readSettings()
  {
    $settings = $this->documentJson->feed->entry[0]->content->{'$t'};

    unset($this->documentJson->feed->entry[0]);

    $settings = explode(',', $settings);

    foreach ($settings as $key => $setting) {
      $setting = explode(':', $setting);
      $settings[$key] = $setting[1];
    }

    $this->settings['lastTournament'] = trim($settings[0]);
    $this->settings['lastMajor'] = trim($settings[1]);
    $this->settings['lastChampionship'] = trim($settings[2]);

    return $this->settings;
  }

  /**
   * readEntries
   *
   * get data and process into a usable array
   * cleaning out the unecessary data
   */
  public function readEntries()
  {
    // get entries in document
    $entries = $this->documentJson->feed->entry;

    // new group array
    $groups = array();

    // iterate through each entry
    foreach($entries as $key => $entry)
    {

      // get group title from entry
      $group = $entry->title->{'$t'};

      // check for previously existing group
      // found: skip
      // not found: create
      if (!array_key_exists($group, $groups)) {
        $groups[$group] = array();
      }

      // expand row to array
      $row = explode(',',$entry->content->{'$t'});

      // flatten array into an associated
      foreach($row as $key => $data) {
        $explodedData = explode(':', $data);
        $arrayKey = trim($explodedData[0]);
        $arrayValue = trim($explodedData[1]);

        $row[$arrayKey] = $arrayValue;

        // remove key => value entries in favour of associated keys
        unset($row[$key]);
      }

      // add row to associated group (from title)
      $groups[$group][] = $row;
    }

    // save data to variable
    $this->entries = $groups;

    return $this->entries;

  }
}