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
    $theme_settings = theme_get_settings();

    //add a display-submitted class
    if ($theme_settings['toggle_node_info_' . $vars['node']->type]) {
      $vars['body_classes'] .= ' display-submitted';
      //show the created date
      $vars['created'] = format_date($vars['node']->created, 'custom', 'M \<\s\p\a\n\>j\<\/\s\p\a\n\>');
    }
    
  }

}

function xmasdev_preprocess_node(&$vars, $hook) {
  $vars['user_picture'] = NULL;

  $node = $vars['node'];

  $theme_settings = theme_get_settings();
  $vars['display_submitted'] = $theme_settings['toggle_node_info_' . $node->type];

  if ($vars['display_submitted']) {
    $vars['date'] = format_date($vars['created'], 'custom', 'M \<\s\p\a\n\>j\<\/\s\p\a\n\>');
    $vars['submitted_text'] = t('Posted by !user', array('!user' => $vars['name']));
    if (!empty($vars['taxonomy'])) {
      $taxonomy_links = array();
      foreach ($vars['taxonomy'] as $term_link) {
        $taxonomy_links[] = l($term_link['title'], $term_link['href'], $term_link);
      }
      $vars['submitted_text'] = t('Posted in !terms by !user', array('!terms' => implode(', ', $taxonomy_links), '!user' => $vars['name']));
      $vars['terms'] = FALSE;
    }
  }

  if (!empty($node->links['node_read_more'])) {
    $node->links['node_read_more']['title'] .= ' »';
  }
  $vars['links'] = !empty($node->links) ? theme('links', $node->links, array('class' => 'links inline')) : '';

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

