<?php
    namespace DAO;

    use DAO\IMovieDAO as IMovieDAO;
    use Models\Movie as Movie;

    class MovieDAO implements IMovieDAO
    {
        private $movieList = array();
        private $newMovieList=array();

        public function Add(Movie $movie)
        {
            $this->RetrieveData();
            
            array_push($this->movieList, $movie);

            $this->SaveData();
        }

        public function GetAll()
        {
            $this->RetrieveData();

            return $this->movieList;
        }

        public function Delete($idMovie)
        {
            $this->retrieveData();
		    $newList = array();
            foreach ($this->movieList as $Movie) 
            {
                if($movie->getIdMovie() != $idMovie)
                {
				array_push($newList, $movie);
			    }
		    }  

		    $this->movieList = $newList;
		    $this->SaveData();
        }

        private function SaveData()
        {
            $arrayToEncode = array();

            foreach($this->movieList as $movie)
            {
                $valuesArray["idMovie"] = $movie->getIdMovie();
                $valuesArray["movieName"] = $movie->getMovieName();
                $valuesArray["language"] = $movie->getLanguage();
                $valuesArray["duration"] = $movie->getDuration();
                $valuesArray["image"] = $movie->getImage();

                array_push($arrayToEncode, $valuesArray);
            }

            $jsonContent = json_encode($arrayToEncode, JSON_PRETTY_PRINT);
            
            file_put_contents(ROOT . 'Data/Movies.json', $jsonContent);
        }

        private function RetrieveData()
        {
            $this->movieList = array();

            if(file_exists(ROOT . 'Data/Movies.json'))
            {
                $jsonContent = file_get_contents(ROOT . 'Data/Movies.json');

                $arrayToDecode = ($jsonContent) ? json_decode($jsonContent, true) : array();

                foreach($arrayToDecode as $valuesArray)
                {
                    $movie = new Movie($valuesArray["idMovie"], $valuesArray["movieName"],$valuesArray["language"],$valuesArray["duration"],$valuesArray["image"]);
                    array_push($this->movieList, $movie);
                }
            }
        }

        public function UpdateAll(){
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.themoviedb.org/3/movie/now_playing?page=1&language=en-US&api_key=f9b934d767d65140edaa81c51e8a4111",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10000,
                CURLOPT_TIMEOUT => 10000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "{}",
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            $arrayToDecode=json_decode($response,true);

            $array=$arrayToDecode["results"];

            $newMovieList=array();

            foreach($array as $thing=>$movie){

                $movies=new Movie($movie['id'],$movie['title'],$movie['original_language'],$this->RetrieveRuntime($movie['id']),$movie['poster_path']);
                array_push($newMovieList,$movies);
            }
            $this->movieList=$newMovieList;
            $this->SaveData();
        }
        
        private function RetrieveRuntime($id){
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.themoviedb.org/3/movie/".$id."?language=en-US&api_key=f9b934d767d65140edaa81c51e8a4111",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10000,
                CURLOPT_TIMEOUT => 10000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "{}",
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            $arrayToDecode=json_decode($response,true);
        
            return $arrayToDecode['runtime'];
        }
    }
?>