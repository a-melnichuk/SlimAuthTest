<?php

class moviesC extends \app\BaseController 
{    
    /*
     * loads movies
     * 
     * @param $message - message to add to page
     */
    
    public function index($message = '') 
    {
        $movie = new Movie(); 
        $movies = $movie->getAllMovies();
        $this->registry->template->show('movies',array(
                                        'movies'=>$movies,
                                        'type'=>'title',
                                        'val'=>'',
                                        'message'=>$message)
        );
    }
    
    /*
     * loads movie
     */
    
    public function movie() 
    {    

        if(isset($_GET['id']) )
        {  
            $movie = new Movie(); 
            $id = $_GET['id'];
            $movie_data = $movie->getMovie($id);
            //no movie is found. Redirect to movies page
            if($movie_data['id'] === null) $this->index("Wrong movie index.");
            else $this->registry->template->show('movie',$movie_data);
        } 
    }

    /*
     * seach movie by type and value requested
     */
    
    public function search() 
    {  
        if(isset($_GET['val']) && isset($_GET['type'])) {
            
            $movie = new Movie();
            $type = $_GET['type'];
            $val = trim($_GET['val']);
            if($type==='title' || $type==='name')
            {
                if($type==='title')
                {
                    $movies = $movie->searchTitle($val);
                }
                else if($type==='name')
                {
                    $movies = $movie->searchActor($val);
                }
                if(count($movies)===0)$movies = -1;
                $this->registry->template->show('movies',array(
                                                'movies'=>$movies,
                                                'type'=>$type,
                                                'val'=>$val,
                                                'message'=>'')
                );
            }
        }
    }
    
    /*
     * adds movie
     */
    
    public function add()
    {
        $movie = new Movie();
        $this->registry->template->show('new_movie');
        if(isset($_POST['submit']))
        {
            $names = $_POST['actor_name'];
            $surnames = $_POST['actor_surname'];
            $unique_initials = array();
            foreach($names as $key=>$name)
            {
                //remove repeating name-surname key-values
                if(array_key_exists($name, $unique_initials) 
                && $surnames[$key] === $unique_initials[$name])
                {
                    unset($names[$key]);
                    unset($surnames[$key]);
                }
                else
                { 
                   $unique_initials[$name] = $surnames[$key];
                   $names[$key] = trim($names[$key]);
                   $surnames[$key] = trim($surnames[$key]);
                }
            }
            //add trimmed values
            $movie->add( trim($_POST['title']),trim($_POST['year']),trim($_POST['format']), $names, $surnames);
            header('Location:' . route('movies/') );
        }
    }
    
    /*
     * removes movie
     */
    
    public function remove()
    {
        if(isset($_POST['remove']))
        {
            $movie = new Movie();
            $movie->remove($_POST['remove']);
            header('Location:'. route('movies/') );
        }
    }
    

}