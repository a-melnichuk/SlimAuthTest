<?php

class Table
{
    public $data = array();
    
    private $name;
    private $col_names;
    private $num_cols;
    private $questions = array();
            
     
    /*
     * @param $name - table name
     * @param $col_names - column names of SQL table
     */
    
    function __construct($name,$col_names) {
        $this->name = $name;
        $this->col_names = $col_names;
        $this->num_cols = count($col_names);
    }
    
    /*
     * Fills mysql in one transation
     * 
     * @param $tables - array of tables to add
     * @param $db - db to transact from
     * @return true if commit is successful, false otherwise
     * 
     */
    
    public static function fillMysqlTables($tables,$db)
    {
        if(count($tables) < 1) throw new Exception ('Number of tables added must be bigger, then zero');
        $db->beginTransaction();
        
        try {
            foreach($tables as $table)
            {
                $stmt = $db->prepare ($table->getSql());
                $stmt->execute($table->data);
            }  
        } catch (PDOException $e){
            return false;
        }
        $db->commit();   
        return true;
    }
    
    /*
     * insert into given table columns with values filled with question marks
     * 
     * @return sql to call in transaction
     */
    
    function getSql()
    {
        return "INSERT INTO {$this->name} (" . implode(',', $this->col_names) . ") VALUES " . implode(',', $this->questions);
    }
    
    /*
     * add data to table and equivalent number of values
     * 
     * @param $row - array of data to add
     */
    
    function addDataRow($row)
    {
        $num_cells = count($row);
        if($num_cells !== $this->num_cols) throw new Exception ('Number of rows added and number of table columns must match');
        
        foreach($row as $cell)
        {
            $this->data[] = $cell;
        }
        $this->addQuestionmarks($num_cells); 
    }
    
    /*
     * adds questionsmarks needed for further transaction
     * 
     * @param $num_marks - number of marks 
     */
    
    private function addQuestionmarks($num_marks)
    {
        if($num_marks === 0) 
            throw new Exception ('Number of question marks must be bigger, then zero');
        
        $questions = $num_marks -1 !== 0 ? str_repeat('?,', $num_marks -1) . '?' : '?';

        $questions = '('.$questions.')';
        $this->questions[] = $questions;
    } 
}
