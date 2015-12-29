<?php

class Movie
{
    private $db;
    public function __construct($db) { 
        $this->db =$db;
    }
    
    /*
     * @param $id - id to get from database
     * @return array with movie data
     */
    
    public function getMovie($id)
    {
        $query = $this->db->prepare("SELECT movie.id,title,year,format, GROUP_CONCAT(CONCAT(name,' ' ,surname) SEPARATOR ', ') as actors
                                    FROM movie 
                                    JOIN actor ON movie.id = actor.movieid 
                                    JOIN movie_format ON movie.id = movie_format.movieid
                                    WHERE movie.id = ?");
        
        $query->bindValue(1, $id, PDO::PARAM_INT);
        $query->execute();
        $movie = $query->fetch(PDO::FETCH_ASSOC);

        return array(
            'id'=>$movie['id'],
            'year'=>$movie['year'],
            'title'=>$movie['title'],
            'format'=>$movie['format'],
            'actors'=>$movie['actors']
        );
    }
    
    /*
     * @return array of all movies
     */
    
    public function getAllMovies()
    {
        $query = $this->db->prepare("SELECT * FROM movie ORDER BY title");
        $query->execute();  
        if($query->rowCount() <= 0)  return false;
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
 
    /*
     * @param $val - title to search
     * @return array matched movies
     */
    
    public function searchTitle($val)
    {
        $query = $this->db->prepare("SELECT movie.id,title
                            FROM movie 
                            WHERE title LIKE ?
                            ORDER BY title");
        //before and after $val can be any char
        $val = "%$val%";
        $query->bindValue(1, $val);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
 
    /*
     * @param $val - actor to search
     * @return array matched movies
     */
    
    public function searchActor($val)
    {
        $query = $this->db->prepare("SELECT movie.id,title
                            FROM movie 
                            JOIN actor ON movie.id = actor.movieid 
                            WHERE actor.name LIKE :val
                            OR actor.surname LIKE :val
                            GROUP BY title
                            ORDER BY title");
        $val = "%$val%";
        $query->bindParam(':val', $val);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    /*
     * add movie,actors and format to database
     * 
     * @param $title - title of movie
     * @param $year - year of movie
     * @param $format - movie format
     * @param $names - array of actor names
     * @param $surnames - array of actor surnames
     * 
     */  
    
    public function add($title,$year,$format,$names,$surnames)
    {
       
        $movieid = db::getNewMovieId();

        $table_movies         = new Table('movie',array('id','title','year'));
        $table_actors         = new Table('actor',array('movieid','name','surname'));
        $table_movie_formats  = new Table('movie_format',array('movieid','format'));
        
        $table_movies->addDataRow(array($movieid,$title,$year));
        $table_movie_formats->addDataRow(array($movieid,$format));

        foreach ($names as $key=>$name)
        {
            $table_actors->addDataRow(array($movieid, $name, $surnames[$key]));
        }       
        Table::fillMysqlTables(array($table_movies,$table_actors,$table_movie_formats),$this->db);      
    }
    
     
    /*
     * removes movie from database
     * 
     * @param $id - id from movie table to remove
     */
    
    public function remove($id)
    {
        $query = $this->db->prepare("DELETE FROM movie
                                    WHERE id = :id");
        $query->bindParam(':id', $id);
        $query->execute();
        
        //if no foreign keys exist,remove related tables manually
        if(db::getEngine('actor') !== 'InnoDB')
        { 
            $query = $this->db->prepare("DELETE FROM actor
                                    WHERE movieid = :id");
            $query->bindParam(':id', $id);
            $query->execute();
        }
        if(db::getEngine('movie_format') !== 'InnoDB')
        { 
            $query = $this->db->prepare("DELETE FROM movie_format
                                    WHERE movieid = :id");
            $query->bindParam(':id', $id);
            $query->execute();
        }   
    }
    
}