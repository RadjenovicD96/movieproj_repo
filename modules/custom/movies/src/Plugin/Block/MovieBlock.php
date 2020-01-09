<?php
/*
 * @file
 * A custom movies block
 */
namespace Drupal\movies\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\movies\Model\Movie;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Movies Block' block.
 *
 * @Block(
 *   id = "movies_block",
 *   admin_label = @Translation("Movies Block"),
 *   category = @Translation("Blocks")
 * )
 */
class MovieBlock extends BlockBase implements ContainerFactoryPluginInterface
{

  public $entityQuery;



  public function __construct(array $configuration, $plugin_id, $plugin_definition,$entityQuery) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityQuery = $entityQuery;
  }



  /**
   * @inheritDoc
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      \Drupal::service('entity.query')
    );
  }



  /**
   * @inheritDoc
   */
  public function build()
  {
    $query = $this->entityQuery
      ->get('node')
      ->condition('type', 'movie')
      ->notExists('field_production_house');
    $entity_ids = $query->execute();
    $nodes =  \Drupal\node\Entity\Node::loadMultiple($entity_ids);
    $movies = [];

    foreach ($nodes as $node){
      $title = $node->get('title')->value;
      $description = $node->get('field_description')->value;
      $poster = !empty($node->field_poster->entity) ? $node->field_poster->entity->getFileUri() : NULL;
      $poster = file_create_url($poster);
      $movies[] = new Movie(t($title),t($description),$poster,[]);
    }

    return array(
      '#theme' => 'house',
      '#title' => $this->t('Results: '),
      '#items' => $movies,
      '#cache' => [
        'keys' => ['movies_block'],
        'contexts' => [],
        'tags' => ['node_list'],
        'max-age' => 300,
      ],
    );
  }



}
