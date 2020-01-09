<?php

namespace Drupal\books\Service;

use Drupal\books\Model\Book;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BookService
{

  public $httpClient;
  public $entityManager;
  public $entityQuery;


  /**
   * BookService constructor.
   */
  public function __construct($httpClient, $entityManager,$entityQuery)
  {
    $this->httpClient = $httpClient;
    $this->entityManager = $entityManager;
    $this->entityQuery = $entityQuery;
  }



  public static function create(ContainerInterface $container)
  {
    return new static(\Drupal::httpClient(),\Drupal::entityTypeManager(),\Drupal::entityQuery('node'));
  }



  public function books() {
    $myBooks = $this->loadAllBooks();

    try {
      //dobavljanje xml-a sa url-a
      $response = $this->httpClient->get('http://chilkatsoft.com/xml-samples/bookstore.xml');
      $data = (string) $response->getBody();
      $xml = simplexml_load_string($data);
      $books = $xml->xpath('//book');

      // kreirati node za svaku knjigu
      foreach ($books as $book) {
        $title = (string) $book->title;
        $isbn =  (string) $book->attributes()->ISBN;
        $price = (string) $book->price;

        $comments = $book->comments->xpath('userComment');
        $myComments = [];
        foreach ($comments as $comment) {
          $myComments[] = (string) $comment;
        }

        // ako ne postoji knjiga, ubaciti u bazu podataka
        if(!$this->checkIfExists($isbn)) {
          $newBook = new Book(t($title),$isbn,$price,$myComments);
          $node = Node::create([
            'type'        => 'book',
            'title'       => $newBook->getTitle(),
            'field_isbn' => $newBook->getIsbn(),
            'field_book_title' => $newBook->getTitle(),
            'field_price' => $newBook->getPrice(),
            'field_comments' => $newBook->getComments(),
          ]);

          $node->save();
          $myBooks[] = $newBook;
        }
      }
    }
    catch (RequestException $e) {
      return false;
    }

    return array(
      '#theme' => 'books',
      '#items' => $myBooks,
      '#title' => t('Books: '),
    );
  }



  private function loadAllBooks() {
    $query = $this->entityQuery->condition('type', 'book');
    $entity_ids = $query->execute();
    $nodes = $this->entityManager->getStorage('node')->loadMultiple($entity_ids);
    $books = [];

    foreach ($nodes as $node) {
      $isbn = $node->get('field_isbn')->value;
      $title = $node->get('title')->value;
      $price = $node->get('field_price')->value;

      $comments = $node->get('field_comments')->getValue();
      $myComments = [];
      foreach ($comments as $comment) {
        $myComments[] = $comment['value'];
      }

      $book = new Book($title,$isbn,$price,$myComments);
      $books[] = $book;
    }

    return $books;
  }



  private function checkIfExists($bookIsbn) {
    $books = $this->loadAllBooks();

    foreach ($books as $book){
      if($bookIsbn === $book->getIsbn())
        return true;
    }

    return false;
  }



  public function add(Request $request) {
    $bookAdded = 'false';

    // This condition checks the `Content-type` and makes sure to decode JSON string from the request body into array.
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $request->request->replace( is_array( $data ) ? $data : [] );
    }

    $title = $data['title'];
    $price = $data['price'];
    $isbn = $data['isbn'];
    $comments = $data['comments'];

    // ako ne postoji knjiga, ubaciti u bazu podataka
    if(!$this->checkIfExists($isbn)) {
      $node = Node::create([
        'type'        => 'book',
        'title'       => $title,
        'field_isbn' => $isbn,
        'field_book_title' => $title,
        'field_price' => $price,
        'field_comments' => $comments,
      ]);
      $node->save();
      $bookAdded = 'true';
    }

    $response['success'] = $bookAdded;

    return new JsonResponse( $response );
  }

}
