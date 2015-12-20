<?php
class TxtLoader
{ 
    private $db;
    private $result = array(
                    'success'=>true,
                    'message'=>'Data has been added successfully!'
                    );
    
    public function __construct() 
    {
        $this->db = db::getInstance();
    }
    
    /*
     * reads data,loads it to database
     * 
     * @param $file - file to read from
     * @return result message from transaction
     */
    
    public function load($file)
    {
        $this->checkFile($file);
        if($this->result['success'])
        {
            $this->parseRows($file);
        }
        return $this->result['message'];
    }
    
    /*
     * Parses rows from given file. Generated SQL. Transacts generated SQL into database
     * 
     * @param $file - file to parse
     * 
     */
    
    private function parseRows($file)
    {   //read using generators
        $handle = fopen($file['tmp_name'], "r");
        if ($handle) 
        {
            $table_movies         = new Table('movie',array('id','title','year'));
            $table_actors         = new Table('actor',array('movieid','name','surname'));
            $table_movie_formats  = new Table('movie_format',array('movieid','format'));
            $title = $this->getCheckedRow( fgets($handle) );
            $movieid = db::getNewMovieId();  
            
            while (!empty($title)) 
            { 
                $year   = $this->getCheckedRow( fgets($handle) );
                $format = $this->getCheckedRow( fgets($handle) );
                $actors = $this->getCheckedRow( fgets($handle) );

                $table_movies->addDataRow(array($movieid,$title,$year));
                $table_movie_formats->addDataRow(array($movieid,$format));
                
                $actors_arr = explode(',', $actors);
                foreach ($actors_arr as $actorInitials)
                {
                    $actorInitialsKV = explode(' ',trim($actorInitials) );
                    $table_actors->addDataRow(array($movieid, $actorInitialsKV[0],$actorInitialsKV[1]));
                }
          
                fgets($handle);
                $title = $this->getCheckedRow( fgets($handle) );
                
                ++$movieid;
                //return, if input data has wrong format
                if(!$this->result['success'])
                {
                    fclose($handle);
                    return;
                }
            }
            fclose($handle);
            //try to load data into SQL table
            $transaction_result = Table::fillMysqlTables(array($table_movies,$table_actors,$table_movie_formats),$this->db);
            if($transaction_result === false)  $this->setFailResult ('Wrong data format - data could not be loaded');  
            
        } else $this->setFailResult ('Error - file could not be opened');               
    }

    /*
     * check rows of line,if input is valid, return trummed value from line
     * 
     * @param $row - line from input
     * @return $trimmed line from input or failed result message
     */

    private function getCheckedRow($row)
    {
        $rowKeyVal = explode(':',$row);
        //value is last,return
        if( count($rowKeyVal) <= 1 ) return;
        if(!empty($rowKeyVal))
        {   //escape html values from file
            return trim(strip_tags( htmlspecialchars( $rowKeyVal[1] ) ) );
        }
        else $this->setFailResult("Wrong data format - ':' could not be found");

    }
 
    
    /*
     * checks if file format is valid
     * 
     * @param $file - $_FILE value to check
     */
    
    private function checkFile($file)
    {
        if($file['error'] === UPLOAD_ERR_NO_FILE)
        {
            $this->setFailResult("Error - file field is empty.");
        }
        else if($file["size"] > 500000)
        {
            $this->setFailResult("File size {$file['name']} must be less then 500 kb ");
        }
        else if($file["type"] !== "text/plain")
        {
            $this->setFailResult("File {$file['name']} must be a text file");
        }
    }
    /*
     * sets result as fail
     * @param $message - message to set on fail
     */
    
    private function setFailResult($message)
    {
        $this->result['success'] = false;
        $this->result['message'] = $message;
    }
}