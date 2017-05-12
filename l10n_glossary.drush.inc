<?php

/**
 * @file
 * Drush l10n_glossary commands.
 */

/**
 * Implements hook_drush_command().
 */
function l10n_glossary_drush_command() {
  $items = array();
  $items['l10n-glossary-import'] = array(
    'description' => "Import glossary terms.",
    'examples' => array(
      'drush lgi',
    ),
    'aliases' => array('lgi'),
  );
  return $items;
}

/**
 * Glossary import command.
 */
function drush_l10n_glossary_import() {
  $rows = array();
  $filename = __DIR__ . '/data/german-drupal-glossary-begriffe.csv';

  if (($handle = fopen($filename, 'r')) !== FALSE) {
    while (($row = fgetcsv($handle)) !== FALSE) {
      $rows[] = $row;
    }
    fclose($handle);
  }

  foreach ($rows as $row) {
    list($source, $type, $translation, $comment) = $row;
    $node = new stdClass();
    $node->type = 'lemma';
    $node->language = 'en';
    $node->is_new = TRUE;
    $node->title = $translation;
    $node->field_lemma_source[LANGUAGE_NONE][0]['value'] = $source;
    $node->field_lemma_type[LANGUAGE_NONE][0]['value'] = $type;
    $node->og_group_ref[LANGUAGE_NONE][0]['target_id'] = 1;
    node_save($node);

    drush_print('Imported Node with nid ' . $node->nid . ' and title "' . $node->title . '"');
  }
}
