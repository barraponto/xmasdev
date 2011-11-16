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

function xmasdev_pager($tags = array(), $limit = 10, $element = 0, $parameters = array(), $quantity = 3) {
  global $pager_page_array, $pager_total;

  // Calculate various markers within this pager piece:
  // Middle is used to "center" pages around the current page.
  $pager_middle = ceil($quantity / 2);
  // current is the page we are currently paged to
  $pager_current = $pager_page_array[$element] + 1;
  // first is the first page listed by this pager piece (re quantity)
  $pager_first = $pager_current - $pager_middle + 1;
  // last is the last page listed by this pager piece (re quantity)
  $pager_last = $pager_current + $quantity - $pager_middle;
  // max is the maximum page number
  $pager_max = $pager_total[$element];
  // End of marker calculations.

  // Prepare for generation loop.
  $i = $pager_first;
  if ($pager_last > $pager_max) {
    // Adjust "center" if at end of query.
    $i = $i + ($pager_max - $pager_last);
    $pager_last = $pager_max;
  }
  if ($i <= 0) {
    // Adjust "center" if at start of query.
    $pager_last = $pager_last + (1 - $i);
    $i = 1;
  }
  // End of generation loop preparation.

  //$li_first = theme('pager_first', (isset($tags[0]) ? $tags[0] : t('« first')), $limit, $element, $parameters);
  $li_previous = theme('pager_previous', (isset($tags[1]) ? $tags[1] : t('«')), $limit, $element, 1, $parameters);
  $li_next = theme('pager_next', (isset($tags[3]) ? $tags[3] : t('»')), $limit, $element, 1, $parameters);
  //$li_last = theme('pager_last', (isset($tags[4]) ? $tags[4] : t('last »')), $limit, $element, $parameters);

  if ($pager_total[$element] > 1) {
    if ($li_first) {
      $items[] = array(
        'class' => 'pager-first', 
        'data' => $li_first,
      );
    }
    if ($li_previous) {
      $items[] = array(
        'class' => 'pager-previous', 
        'data' => $li_previous,
      );
    }

    // When there is more than one page, create the pager list.
    if ($i != $pager_max) {
      if ($i > 1) {
        $items[] = array(
          'class' => 'pager-ellipsis', 
          'data' => '…',
        );
      }
      // Now generate the actual pager piece.
      for (; $i <= $pager_last && $i <= $pager_max; $i++) {
        if ($i < $pager_current) {
          $items[] = array(
            'class' => 'pager-item', 
            'data' => theme('pager_previous', $i, $limit, $element, ($pager_current - $i), $parameters),
          );
        }
        if ($i == $pager_current) {
          $items[] = array(
            'class' => 'pager-current', 
            'data' => $i,
          );
        }
        if ($i > $pager_current) {
          $items[] = array(
            'class' => 'pager-item', 
            'data' => theme('pager_next', $i, $limit, $element, ($i - $pager_current), $parameters),
          );
        }
      }
      //if ($i < $pager_max) {
      //  $items[] = array(
      //    'class' => 'pager-ellipsis', 
      //    'data' => '…',
      //  );
      //}
    }
    // End generation.
    if ($li_next) {
      $items[] = array(
        'class' => 'pager-next', 
        'data' => $li_next,
      );
    }
    if ($li_last) {
      $items[] = array(
        'class' => 'pager-last', 
        'data' => $li_last,
      );
    }
    $pager_info = array(
      'class' => 'pager-info',
      'data' => t('Page !current of !total', array('!current' => $pager_current, '!total' => $pager_max)),
    );
    array_unshift($items, $pager_info);
    return theme('item_list', $items, NULL, 'ul', array('class' => 'pager'));
  }
}
