<?php
/**
 * User: Andrew Seymour
 * Date: 04/02/13
 * Time: 22:57
 */

class PHPSphinxQL
{
  // held data
  protected $objSphinxConnection;
  protected $blnLiveConnection = false;
  private $arrStorage = array();
  private $arrQueries = array();

  /**
   *
   */
  public function __construct()
  {

  }

  /**
   *
   */
  public function __destruct()
  {
    // close our connection to Sphinx
    $this->objSphinxConnection->close();
  }

  public function __sphinx_connection($strQueries)
  {

    //
    if ($this->objSphinxConnection->multi_query($strQueries))
    {
      do
      {
        /* store first result set */
        if ($result = $this->objSphinxConnection->store_result())
        {
          while ($row = $result->fetch_row())
          {
            printf("%s\n", $row[0]);
          }
          $result->free();
        }

        /* print divider */
        if ($this->objSphinxConnection->more_results())
        {
          printf("-----------------\n");
        }
      } while ($this->objSphinxConnection->next_result());
    }

  }

  public function connect($strServer, $intPort)
  {
    // create connection
    $this->objSphinxConnection = new MySQLi();
    $this->objSphinxConnection->init();
    $this->objSphinxConnection->options(MYSQLI_OPT_CONNECT_TIMEOUT, 500);
    $this->objSphinxConnection->real_connect($strServer, 'PHPSphinxQL', '', '', $intPort);

    if (!$this->objSphinxConnection->connect_error)
    {
      // sphinx is up and responding - update our status
      $this->blnLiveConnection = true;
    }
  }

  public function set_limit($intOffset = null, $intLimit = null)
  {

  }

  public function set_select($mixSelect)
  {
    // do we have an array?
    if(is_array($mixSelect))
    {
      $this->arrStorage['select'] = $mixSelect;
    }

    // just a string
    if(is_string($mixSelect))
    {
      if(strpos(' ', $mixSelect) !== false)
      {
        // seperated with spaces
        $this->arrStorage['select'] = explode(' ', $mixSelect);
      }

      if(strpos(',', $mixSelect) !== false)
      {
        // seperated with commas
        $this->arrStorage['select'] = explode(',', $mixSelect);
      }
    }
  }

  public function set_field_weights($arrFields)
  {
    if(is_array($arrFields))
    {

    }
  }

  public function set_filter($strAttribute, $mixValue, $blnExclude)
  {

  }

  public function set_filter_range($strAttribute, $intMin, $intMax)
  {
    
  }

  public function set_group_by()
  {

  }

  public function set_option($strOption, $mixValue)
  {
    switch($strOption)
    {
      case 'max_query_time':
      case 'sort_mode':
    }
  }

  public function set_query($strName)
  {

  }

  public function run_query()
  {

  }

  public function clean()
  {

  }




}