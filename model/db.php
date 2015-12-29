<?php
class db{

    private static $instance = NULL;

    private function __construct() 
    {
    }
    private function __clone()
    {
    }

    public static function getInstance() 
    {
        if(!self::$instance)
        {
            self::$instance = new PDO("mysql:host=myhost;dbname=mydbname", 'myname', 'mypass');
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $movie_sql= "CREATE TABLE IF NOT EXISTS 
                        movie(
                        id int(11) NOT NULL AUTO_INCREMENT,
                        title varchar(100) NOT NULL,
                        year int(11) NOT NULL,
                        PRIMARY KEY(id)
                        ) ENGINE=InnoDB;";

            $actor_sql="CREATE TABLE IF NOT EXISTS 
                        actor(
                        id int(11) NOT NULL AUTO_INCREMENT,
                        movieid int(11) NOT NULL,
                        name varchar(100) NOT NULL,
                        surname varchar(100) NOT NULL,
                        PRIMARY KEY(id),
                        FOREIGN KEY (movieid) REFERENCES movie(id)
                        ON UPDATE CASCADE
                        ON DELETE CASCADE      
                        ) ENGINE=InnoDB;";

            $movie_type_sql= "CREATE TABLE IF NOT EXISTS 
                        movie_format(
                        id int(11) NOT NULL AUTO_INCREMENT,
                        movieid int(11) NOT NULL,
                        format varchar(100) NOT NULL,
                        PRIMARY KEY(id),
                        FOREIGN KEY (movieid) REFERENCES movie(id)
                        ON UPDATE CASCADE
                        ON DELETE CASCADE
                        ) ENGINE=InnoDB;";
            
            $user_sql= "CREATE TABLE IF NOT EXISTS 
                        user(
                        id int(11) NOT NULL AUTO_INCREMENT,
                        username varchar(100) NOT NULL,
                        password varchar(255) NOT NULL,
                        email varchar(100) NOT NULL,
                        token varchar(255) NOT NULL,
                        token_expire DATETIME NOT NULL,
                        PRIMARY KEY(id)
                        ) ENGINE=InnoDB;";
            
            self::$instance->exec($movie_sql);
            self::$instance->exec($actor_sql);
            self::$instance->exec($movie_type_sql);
            self::$instance->exec($user_sql);
        }
    return self::$instance;
    }
    
    public static function getNewMovieId()
    {
        $query = self::getInstance()->prepare("SELECT IFNULL(MAX(id)+1, 1) as id FROM movie");
        $query->execute();
        return intval($query->fetch(PDO::FETCH_ASSOC)['id']);
    }
    
    public static function getEngine($table)
    {   
        $query = self::getInstance()->prepare("SHOW TABLE STATUS WHERE `Name` = :table");
        $query->bindParam(':table', $table);
        $query->execute();
        $status = $query->fetchAll(PDO::FETCH_ASSOC);
        return $status[0]['Engine'];
    }
    
    

}