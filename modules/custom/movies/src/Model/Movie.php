<?php

namespace Drupal\movies\Model;

class Movie
{
  private $title;
  private $description;
  private $image;
  private $genres;

  /**
   * Movie constructor.
   */
  public function __construct($title, $description, $image, array $genres){
    $this->title = $title;
    $this->description = $description;
    $this->image = $image;
    $this->genres = $genres;
  }

  /**
   * @return array
   */
  public function getGenres()
  {
    return $this->genres;
  }

  /**
   * @param array $genres
   */
  public function setGenres($genres)
  {
    $this->genres = $genres;
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


  public function getImage()
  {
    return $this->image;
  }

  public function setImage($image)
  {
    $this->image = $image;
  }

  /**
   * @return mixed
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @param mixed $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }




}
