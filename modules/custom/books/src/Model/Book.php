<?php


namespace Drupal\books\Model;


class Book
{

  private $title;
  private $isbn;
  private $price;
  private $comments;

  /**
   * Movie constructor.
   */
  public function __construct($title, $isbn, $price, array $comments){
    $this->title = $title;
    $this->isbn = $isbn;
    $this->price = $price;
    $this->comments = $comments;
  }

  /**
   * @return mixed
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * @param mixed $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }

  /**
   * @return mixed
   */
  public function getIsbn()
  {
    return $this->isbn;
  }

  /**
   * @param mixed $isbn
   */
  public function setIsbn($isbn)
  {
    $this->isbn = $isbn;
  }

  /**
   * @return mixed
   */
  public function getPrice()
  {
    return $this->price;
  }

  /**
   * @param mixed $price
   */
  public function setPrice($price)
  {
    $this->price = $price;
  }

  /**
   * @return array
   */
  public function getComments()
  {
    return $this->comments;
  }

  /**
   * @param array $comments
   */
  public function setComments($comments)
  {
    $this->comments = $comments;
  }



}
