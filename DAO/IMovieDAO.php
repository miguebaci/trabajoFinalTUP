<?php
    namespace DAO;
    use Models\Movie as Movie;
    interface IMovieDAO
    {
        function Add(Movie $movie);
        function MXG(Movie $movie);
        function GetAll();
        function GetById($idMovie);
        function UpdateAll();
        function RetrieveRuntime($id);
    }
?>