<?php

namespace Drupal\l10n_glossary;

/**
 * Interface GlossaryInterface.
 *
 * @package Drupal\l10n_glossary
 */
interface GlossaryInterface {

  /**
   * Set the language code.
   *
   * @param string $langcode
   *   A language code, e.g. en, de, fr.
   */
  public function setLangcode($langcode);

  /**
   * Get glossary terms for string.
   *
   * @param string $string
   *   The string to get the glossary terms for.
   *
   * @return array
   *   An array of glossary terms.
   */
  public function getTerms($string);

  /**
   * Get glossary term usage.
   *
   * @param string $string
   *   The string to check against the glossary terms.
   * @param array $terms
   *   An array of glossary terms.
   *
   * @return array
   *   An array of term usage percentages.
   */
  public function getTermUsage($string, array $terms);

}
