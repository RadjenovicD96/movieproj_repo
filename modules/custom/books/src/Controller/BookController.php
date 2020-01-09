<?php


namespace Drupal\books\Controller;

use Drupal\books\Service\BookService;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\books\Model\Book;

class BookController extends ControllerBase
{

  private $bookService;

  /**
   * BookController constructor.
   */
  public function __construct($bookService)
  {
    $this->bookService = $bookService;
  }


  public static function create(ContainerInterface $container)
  {
    return new static(BookService::create($container));
  }

  public function books() {

    return $this->bookService->books();

  }

  public function add(Request $request) {

    return $this->bookService->add($request);

  }


}
