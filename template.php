<?php

function xmasdev_preprocess_page(&$vars, $hook) {
  $vars['mission'] = NULL;
  $vars['search_box'] = NULL;
  $vars['primary_links'] = NULL;
  $vars['secondary_links'] = NULL;
  $vars['breadcrumb'] = NULL;
  $vars['feed_icons'] = NULL;
  $vars['messages'] = NULL; /* WARNING: turning off messages is a sign of bad judgement */
  $vars['tabs'] = NULL; /* WARNING: local tasks won't have any links to it */

  //Checking whether 'submitted by' data is displayed
  if ($vars['node']) {
    $theme_settings = variable_get('theme_settings', array());

    //add a display-submitted class
    if ($theme_settings['toggle_node_info_' . $vars['node']->type]) {
      $vars['classes_array'][] = 'display-submitted';
    }
    
    //show the created date
    $vars['created'] = format_date($vars['node']->created, 'custom', 'M \<\s\p\a\n\>j\<\/\s\p\a\n\>');
  }

}

function xmasdev_preprocess_node(&$vars, $hook) {
  $vars['user_picture'] = NULL;
  $vars['display_submitted'] = NULL;
  //$vars['links'] = NULL; /* WARNING: comments may not have any links to it */
  
  if ($vars['teaser']) {
    $vars['date'] = format_date($vars['created'], 'custom', 'M \<\s\p\a\n\>j\<\/\s\p\a\n\>');
  }

  // Optionally, run node-type-specific preprocess functions, like
  // xmas_preprocess_node_page() or xmas_preprocess_node_story().
  $function = __FUNCTION__ . '_' . $vars['node']->type;
  if (function_exists($function)) {
    $function($vars, $hook);
  }
}

function xmasdev_preprocess_comment(&$vars, $hook) {
  $vars['picture'] = NULL;
}

function xmasdev_pager($tags = array(), $limit = 10, $element = 0, $parameters = array(), $quantity = 9) {
  global $pager_total;

  $li_previous = theme('pager_previous', (isset($tags[1]) ? $tags[1] : t('Newer')), $limit, $element, 1, $parameters);
  $li_next = theme('pager_next', (isset($tags[3]) ? $tags[3] : t('Older')), $limit, $element, 1, $parameters);

  if ($pager_total[$element] > 1) {

    if ($li_previous) {
      $items[] = array(
        'class' => 'pager-previous', 
        'data' => $li_previous,
      );
    }

    // End generation.
    if ($li_next) {
      $items[] = array(
        'class' => 'pager-next', 
        'data' => $li_next,
      );
    }
    return theme('item_list', $items, NULL, 'ul', array('class' => 'pager'));
  }
}
