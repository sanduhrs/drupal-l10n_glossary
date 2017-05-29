<?php

namespace Drupal\l10n_glossary;

use Drupal\Core\Cache\DatabaseBackend;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use GuzzleHttp\Client;

/**
 * Class GlossaryService.
 *
 * @package Drupal\l10n_glossary
 */
class GlossaryService implements GlossaryInterface {

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * Drupal\Core\Cache\DatabaseBackend definition.
   *
   * @var \Drupal\Core\Cache\DatabaseBackend
   */
  protected $cache;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * GuzzleHttp\Client definition.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * The language code, e.g. en, de, fr.
   *
   * @var string
   */
  protected $langcode;

  /**
   * GlossaryService constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   * @param \Drupal\Core\Cache\DatabaseBackend $cache
   * @param \Drupal\Core\Database\Connection $connection
   * @param \GuzzleHttp\Client $http_client
   */
  public function __construct(
      ConfigFactoryInterface $config,
      DatabaseBackend $cache,
      Connection $connection,
      Client $http_client
  ) {
    $this->config = $config;
    $this->cache = $cache;
    $this->connection = $connection;
    $this->httpClient = $http_client;

    $this->langcode = 'en';
  }

  /**
   * {@inheritdoc}
   */
  public function setLangcode($langcode) {
    $this->langcode = $langcode;
  }

  /**
   * {@inheritdoc}
   */
  public function getTerms($string) {
    $single_word_terms = $this->getSingleWordTerms($string);
    $multi_word_terms = $this->getMultiWordTerms($string);
    return array_merge($single_word_terms, $multi_word_terms);
  }

  /**
   * {@inheritdoc}
   */
  public function getTermUsage($string, $terms) {
    $usage = [];
    foreach ($terms as $index => $needle) {
      if (strpos(strtolower($string), strtolower($needle->title)) === FALSE) {
        unset($result[$index]);
      }
    }
    return $usage;
  }

  /**
   * Get single word glossary terms from a string.
   *
   * @param $string
   *   The original string to check for terms.
   *
   * @return array
   *   An array of glossary terms.
   */
  private function getSingleWordTerms($string) {
    $strings = explode(' ', $string);
    $query = $this->connection->query(
      'SELECT n.nid, nfd.title, nft.field_translation_value
         FROM node n
         INNER JOIN node_field_data nfd ON n.nid = nfd.nid
         INNER JOIN node__field_translation nft ON n.nid = nft.entity_id
         WHERE n.type = :type
           AND nfd.status = :status
           AND nfd.langcode = :langcode
           AND nfd.title IN (:strings[])',
      [
        ':type' => 'term',
        ':status' => 1,
        ':langcode' => $this->langcode,
        ':strings[]' => $strings,
      ]
    );
    $result = $query->fetchAllAssoc('nid');
    return $result;
  }

  /**
   * Get mutli word glossary terms from a string.
   *
   * @param $string
   *   The original string to check for terms.
   *
   * @return array
   *   An array of glossary terms.
   */
  private function getMultiWordTerms($string) {
    $query = $this->connection->query(
      'SELECT n.nid, nfd.title, nft.field_translation_value
         FROM node n
         INNER JOIN node_field_data nfd ON n.nid = nfd.nid
         INNER JOIN node__field_translation nft ON n.nid = nft.entity_id
         WHERE n.type = :type 
           AND nfd.status = :status
           AND nfd.langcode = :langcode
           AND nfd.title LIKE \'% %\'',
      [
        ':type' => 'term',
        ':status' => 1,
        ':langcode' => $this->langcode,
      ]
    );
    $result = $query->fetchAllAssoc('nid');
    foreach ($result as $index => $needle) {
      if (strpos(strtolower($string), strtolower($needle->title)) === FALSE) {
        unset($result[$index]);
      }
    }
    return $result;
  }

}
