<?php
/**
 * User: Andrew Seymour
 * Date: 04/02/13
 * Time: 22:57
 */

class PHPSphinxQL {
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
    // close
    $this->objSphinxConnection->close();
  }

  public function __sphinx_connection()
  {

    //
    if ($this->objSphinxConnection->multi_query(implode('; ', $this->arrQueries))) {
      do {
        /* store first result set */
        if ($result = $this->objSphinxConnection->store_result()) {
          while ($row = $result->fetch_row()) {
            printf("%s\n", $row[0]);
          }
          $result->free();
        }
        /* print divider */
        if ($this->objSphinxConnection->more_results()) {
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

    if(!$this->objSphinxConnection->connect_error)
    {
      // sphinx is up and responding - update our status
      $this->blnLiveConnection = true;
    }
  }

  public function set_limit()
  {

  }

  public function set_select()
  {

  }

  public function set_field_weights()
  {

  }

  public function set_filter()
  {

  }

  public function set_filter_range()
  {

  }

  public function set_group_by()
  {

  }

  public function set_query()
  {

  }

  public function query()
  {

  }






}