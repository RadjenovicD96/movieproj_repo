<?php

namespace Drupal\movies\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\movies\Model\Movie;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class MovieController extends ControllerBase
{

  public $entityManager;
  public $entityQuery;
  public $config;



  /**
   * MovieController constructor.
   */
  public function __construct($entityManager,$entityQuery,$config)
  {
    $this->entityManager = $entityManager;
    $this->entityQuery = $entityQuery;
    $this->config = $config;
  }



  public static function create(ContainerInterface $container)
  {
    return new static(\Drupal::entityTypeManager(),\Drupal::entityQuery('node'), \Drupal::config('movie.settings'));
  }



  public function movies() {

    $movies = $this->loadAllMovies();
    $moviesSize = count($movies);
    $movies = $this->getMoviesPerPage($movies,1);

    return array(
      '#theme' => 'movies',
      '#items' => $movies,
      '#title' => t('Movies: '),
      '#selectedGenre' => '',
      '#selectedFilter' => '',
      '#selectedPage' => '',
      '#numOfPages' => $this->calculateNumOfPages($moviesSize),
      '#genres' => $this->loadGenres(),
    );
  }



  private function loadAllMovies() {
    $query = $this->entityQuery->condition('type', 'movie');
    $entity_ids = $query->execute();
    $movies = [];
    $nodes = $this->entityManager->getStorage('node')->loadMultiple($entity_ids);

    foreach ($nodes as $node){
      $title = $node->get('title')->value;

      $description = $node->get('field_description')->value;

      $genreNodes = $node->get('field_genre')->getValue();
      $genres = [];
      foreach ($genreNodes as $genre) {
        $id = $genre['target_id'];
        $term = $this->entityManager->getStorage('taxonomy_term')->load($id);
        $genres[] = $term->name->value;
      }

      $poster = !empty($node->field_poster->entity) ? $node->field_poster->entity->getFileUri() : NULL;
      $poster = file_create_url($poster);
      $movies[] = new Movie(t($title),t($description),$poster,$genres);  //t function used to manage translations of movie titles
    }

    return $movies;
  }



  public function getMoviesPerPage($movies, $requestedPage) {

    $resultPP = $this->config->get('movies_per_page');
    $moviesPerPage = [];

    if($resultPP>count($movies))
      return $movies;

    for($i = ($requestedPage-1)*$resultPP ; $i < ($requestedPage-1)*$resultPP + $resultPP ; $i++) {
      if(count($movies)===$i)
        break;
      $moviesPerPage[] = $movies[$i];
    }

    return $moviesPerPage;
  }



  public function filterMovies() {
    $filter = $_POST['title'];
    $genre = $_POST['genre'];
    $page = $_POST['search'];
    $movies = $this->loadAllMovies();
    $filteredMovies = [];

    if($filter !== "") {
      foreach ($movies as $movie)
        if(stripos($movie->getTitle(),$filter) !== false)
          $filteredMovies[] = $movie;
    } else {
      $filteredMovies = $movies;
    }

    if($genre !== "None") {
      $filteredByGenre = [];
      foreach ($filteredMovies as $movie){
        foreach ($movie->getGenres() as $currentGenre) {
          if($genre === $currentGenre) {
            $filteredByGenre[] = $movie;
            break;
          }
        }
      }
      $filteredMovies = $filteredByGenre;
    }

    $moviesSize = count($filteredMovies);
    $filteredMovies = $this->getMoviesPerPage($filteredMovies,$page);

    return array(
      '#theme' => 'movies',
      '#items' => $filteredMovies,
      '#title' => t('Movies: '),
      '#selectedGenre' => $genre,
      '#selectedPage' => $page,
      '#selectedFilter' => $filter,
      '#numOfPages' => $this->calculateNumOfPages($moviesSize),
      '#genres' => $this->loadGenres(),
    );
   }



  public function  loadGenres() {
    $vid = 'movie_type';
    $terms = $this->entityManager->getStorage('taxonomy_term')->loadTree($vid);
    $term_data = [];

    foreach ($terms as $term) {
      $term_data[] = array(
        'id' => $term->tid,
        'name' => $term->name
      );
    }

    return $term_data;
  }



  public function  calculateNumOfPages($moviesSize) {
    $resultPP = $this->config->get('movies_per_page');

    if($resultPP >= $moviesSize)
      return 1;
    else
      return ceil($moviesSize/$resultPP);

  }

}
