<?php
/**
 * @file
 * A custom movies block
 */
namespace Drupal\books\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Book' B lock.
 *
 * @Block(
 *   id = "books_block",
 *   admin_label = @Translation("Book Block")
 * )
 */
class BookBlock extends BlockBase
{

  /**
   * {@inheritDoc}
   */
  public function build()
  {
    return array(
      '#theme' => 'housess',
      '#title' => $this->t('Naslov'),
      '#mica' => $this->t('Test Value'),
    );
  }
}
