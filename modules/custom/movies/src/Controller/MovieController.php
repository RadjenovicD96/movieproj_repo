<?php

namespace Drupal\movies\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\movies\Service\MovieService;

class MovieController extends ControllerBase
{

  public $movieService;



  /**
   * MovieController constructor.
   */
  public function __construct($movieService)
  {
    $this->movieService = $movieService;
  }



  public static function create(ContainerInterface $container)
  {
    return new static(MovieService::create($container));
  }



  public function movies() {
    return $this->movieService->movies();
  }



  public function filterMovies() {
    return $this->movieService->filterMovies();
   }



}
