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

  private function __sphinx_connection($strQueries)
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

  /**
   * clean up the string for the attribute name
   *
   * @param $strAttribute
   * @return null|string
   */
  private function __clean_attribute($strAttribute)
  {
    $strAttribute = trim($strAttribute);

    if(empty($strAttribute))
    {
      return null;
    }
    else
    {
      return $strAttribute;
    }
  }

  /**
   * connect to our sphinx server or distributed handler
   *
   * @param $strServer hostname or ip address (only one)
   * @param $intPort the port at which sphinx is running on for :mysql41
   */
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

  /**
   * sets attributes that we want to select in our query
   *
   * @param $mixSelect an array or string of the attributes we want to select
   */
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

  /**
   * filter down our results by asking for documents with particular attributes
   *
   * @param $strAttribute the attribute to filter by
   * @param $mixValue the value that attribute should have
   * @param $blnExclude are we including or excluding all documents matching this criteria
   */
  public function set_filter($strAttribute, $mixValue, $blnExclude)
  {
    $strAttribute = $this->__clean_attribute($strAttribute);

    if(!empty($strAttribute) && !empty($mixValue) && is_string($strAttribute))
    {
      // add
      $this->arrStorage['filters'][$strAttribute] = array('value' => $mixValue,
                                                          'exclude' => $blnExclude);
    }
  }

  public function set_filter_range($strAttribute, $intMin, $intMax)
  {
    
  }

  public function set_group_by($strAttribute, $arrSort = array())
  {
    $strAttribute = $this->__clean_attribute($strAttribute);

    if(!empty($strAttribute))
    {
      $this->arrStorage['group_by'] = array('attribute' => $strAttribute,
                                            'sort' => $arrSort);
    }
  }

  /**
   * sets options for this sql statement
   *
   * @param $strOption name of the option
   * @param $mixValue the value of the option (depending on the option)
   * @usage http://sphinxsearch.com/docs/manual-2.1.1.html#sphinxql-select
   */
  public function set_option($strOption, $mixValue)
  {
    switch($strOption)
    {
      case 'agent_query_timeout':
        if(is_int($mixValue))
        {
          $this->arrStorage['option']['agent_query_timeout'] = $mixValue;
        }

        break;
      case 'boolean_simplify':
        if(is_int($mixValue) && ($mixValue === 1 || $mixValue === 0))
        {
          $this->arrStorage['option']['boolean_simplify'] = $mixValue;
        }

        break;
      case 'comment':
        if(is_string($mixValue))
        {
          $this->arrStorage['option']['comment'] = $mixValue;
        }

        break;
      case 'cutoff':
        if(is_int($mixValue))
        {
          $this->arrStorage['option']['cutoff'] = $mixValue;
        }

        break;
      case 'field_weights':
        // @TODO write this in properly later
      case 'global_idf':
        // @TODO write this, no documentation on this
      case 'idf':
        if(is_string($mixValue) && ($mixValue === 'normalized' || $mixValue === 'plain'))
        {
          $this->arrStorage['option']['idf'] = $mixValue;
        }

        break;
      case 'index_weights':
        // @TODO write this in - like field_weights
      case 'max_matches':
        if(is_int($mixValue))
        {
          $this->arrStorage['option']['max_matches'] = $mixValue;
        }

        break;
      case 'max_query_time':
        if(is_int($mixValue))
        {
          $this->arrStorage['option']['max_query_time'] = $mixValue;
        }

        break;
      case 'ranker':
        if(is_string($mixValue) && in_array($mixValue, array('proximity_bm25', 'bm25', 'none', 'wordcount', 'proximity', 'matchany', 'fieldmask', 'sph04', 'expr', 'export')))
        {
          $this->arrStorage['option']['ranker'] = $mixValue;
        }

        break;
      case 'retry_count':
        if(is_int($mixValue))
        {
          $this->arrStorage['option']['retry_count'] = $mixValue;
        }

        break;
      case 'retry_delay':
        if(is_int($mixValue))
        {
          $this->arrStorage['option']['retry_delay'] = $mixValue;
        }

        break;
      case 'reverse_scan':
        if(is_int($mixValue) && ($mixValue === 0 || $mixValue === 1))
        {
          $this->arrStorage['option']['reverse_scan'] = $mixValue;
        }

        break;
      case 'sort_method':
        if(is_string($mixValue) && ($mixValue === 'pq' || $mixValue == 'kbuffer'))
        {
          $this->arrStorage['option']['sort_method'] = $mixValue;
        }

        break;
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