<?php

/**
 * @file
 * Contains cpmf_data\cpmf_data.views.inc..
 * Provide a custom views field data that isn't tied to any other module. */

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Render\Markup;
use Drupal\field\FieldConfigInterface;
use Drupal\field\FieldStorageConfigInterface;
use Drupal\system\ActionConfigEntityInterface;

/**
* Implements hook_views_data().
*/
function cpmf_data_views_data() {

    $data['views']['table']['group'] = t('Custom Global');
    $data['views']['table']['join'] = array(
      // #global is a special flag which allows a table to appear all the time.
      '#global' => array(),
    );


    $data['views']['city_pillars_scores'] = array(
        'title' => t('City Pillars Scores views field'),
        'help' => t('City Pillars Scores views field (Bootstrap)'),
        'field' => array(
            'id' => 'city_pillars_scores',
        ),
    );

    return $data;
}
