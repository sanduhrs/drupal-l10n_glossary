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
    'arguments' => array(
      'langcode' => 'The langcode, e.g. en, de, fr',
    ),
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
function drush_l10n_glossary_import($langcode = 'de') {
  $rows = array();
  $filename = __DIR__ . '/data/' . $langcode . '.csv';

  // Check for file existence.
  if (!file_exists($filename)) {
    drush_print(t('File not found: @file', ['@file' => $filename]));
    exit(1);
  }

  // Read data from file.
  if (($handle = fopen($filename, 'r')) !== FALSE) {
    while (($row = fgetcsv($handle)) !== FALSE) {
      $rows[] = $row;
    }
    fclose($handle);
  }

  // Import data into nodes.
  $entity_manager = \Drupal::entityTypeManager()->getStorage('node');
  foreach ($rows as $row) {
    list($title, $type, $translation, $comment) = $row;
    $node = $entity_manager->create([
      'type' => 'term',
      'status' => 1,
      'langcode' => $langcode,
      'title' => $title,
      'field_type' => $type,
      'field_translation' => $translation,
      'body' => $comment,
    ]);
    $node->save();
    drush_print('Imported Node with nid ' . $node->id() . ' and title "' . $node->getTitle() . '"');
  }
}
