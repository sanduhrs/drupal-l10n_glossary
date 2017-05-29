<?php

namespace Drupal\l10n_glossary\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController.
 *
 * @package Drupal\l10n_glossary\Controller
 */
class DefaultController extends ControllerBase {

  /**
   * Glossary.
   *
   * @return string
   *   Return Hello string.
   */
  public function glossary(Request $request) {
    $string = $request->get('string');
    $langcode = $request->get('langcode');

    /** @var \Drupal\l10n_glossary\GlossaryService $glossary */
    $glossary = \Drupal::service('l10n_glossary.glossary');
    $glossary->setLangcode($langcode);

    $data = [
      'string' => $string,
      'langcode' => $langcode,
      'terms' => $glossary->getTerms($string),
    ];
    return new JsonResponse(
      $data,
      200
    );
  }

}
