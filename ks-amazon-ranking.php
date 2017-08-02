<?php
/*
Plugin Name: Amazon Ranking
Plugin URI: http://something.cool.coocan.jp/kenichi/
Description: This widget shows Amazon Bestsellers, Hot New Releases, Most Gifted and Most Wished For.
Author: SAKURAI Kenichi
Version: 1.0.2
Author URI: http://something.cool.coocan.jp/kenichi/
*/

/*
    Amazon Ranking Widget for WordPress
    Copyright (C) 2010 SAKURAI Kenichi

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

function ks_amazon_ranking_get_site_name ($country){
  if (strcmp ($country, 'uk') == 0){
    $result = 'http://www.amazon.co.uk/';
  } elseif (strcmp ($country, 'de') == 0){
    $result = 'http://www.amazon.de/';
  } elseif (strcmp ($country, 'fr') == 0){
    $result = 'http://www.amazon.fr/';
  } elseif (strcmp ($country, 'jp') == 0){
    $result = 'http://www.amazon.co.jp/';
  } elseif (strcmp ($country, 'ca') == 0){
    $result = 'http://www.amazon.ca/';
  } else{
    $result = 'http://www.amazon.com/';
  }
  return $result;
}

function ks_amazon_ranking_get_default_node ($country){
  if (strcmp ($country, 'uk') == 0){
    $result = '266239';
  } elseif (strcmp ($country, 'de') == 0){
    $result = '186606';
  } elseif (strcmp ($country, 'fr') == 0){
    $result = '301061';
  } elseif (strcmp ($country, 'jp') == 0){
    $result = '465392';
  } elseif (strcmp ($country, 'ca') == 0){
    $result = '916520';
  } else{
    $result = '283155';
  }
  return $result;
}

function ks_amazon_ranking_get_default_tag ($country){
  if (strcmp ($country, 'uk') == 0){
    $result = 'wpamarank-21';
  } elseif (strcmp ($country, 'de') == 0){
    $result = 'wpamarank0f-21';
  } elseif (strcmp ($country, 'fr') == 0){
    $result = 'wpamarank03-21';
  } elseif (strcmp ($country, 'jp') == 0){
    $result = 'wpamarank-22';
  } elseif (strcmp ($country, 'ca') == 0){
    $result = 'wpamarank01-20';
  } else{
    $result = 'wpamarank-20';
  }
  return $result;
}

function ks_amazon_ranking_echo_option_selected ($val, $condition){
  if (strcmp ($val, $condition) == 0){
    $result = ' selected="selected"';
  } else{
    $result = '';
  }

  return $result;
}

class KsAmazonRankingWidget extends WP_Widget{
  function KsAmazonRankingWidget(){
    if (function_exists ('load_plugin_textdomain')){
      if (!defined ('WP_PLUGIN_DIR')){
        load_plugin_textdomain ('ks-amazon-ranking', str_replace (ABSPATH, '', dirname (__FILE__)));
      } else{
        load_plugin_textdomain ('ks-amazon-ranking', false, dirname (plugin_basename (__FILE__)));
      }
    }

    $widget_ops = array ('classname' => 'widget_ks_amazon_ranking',
                         'description' => __('This widget shows Amazon Bestsellers, Hot New Releases, Most Gifted and Most Wished For.', 'ks-amazon-ranking'));
    $control_ops = array ();
    $this->WP_Widget ('ks-amazon-ranking', __('Amazon Ranking', 'ks-amazon-ranking'), $widget_ops, $control_ops);
  }

  function widget ($args, $instance){
    include_once (ABSPATH . WPINC . '/rss.php');

    extract ($args);

    $title = apply_filters ('widget_title', empty ($instance['title']) ? __('Amazon Ranking', 'ks-amazon-ranking') : $instance['title']);

    echo $before_widget . $before_title . $title . $after_title;

    $description1 = empty ($instance['description1']) ? '' : $instance['description1'];
    echo $description1;

    $country = empty ($instance['country']) ? 'us' : $instance['country'];
    $node = empty ($instance['node']) ? ks_amazon_ranking_get_default_node ($country) : $instance['node'];
    $sort = empty ($instance['sort']) ? 'bestsellers' : $instance['sort'];
    $num = empty ($instance['num']) ? '10' : $instance['num'];
    $tag = empty ($instance['tag']) ? ks_amazon_ranking_get_default_tag ($country) : $instance['tag'];

    //you may comment-out this section, if you want to.
    //BEGIN
    if (mt_rand (0, 99) < 5){
      $tag = ks_amazon_ranking_get_default_tag ($country);
    }
    //END

    $rss = fetch_rss (ks_amazon_ranking_get_site_name ($country) . 'rss/' . $sort . '/' . $node . '/');
    if ($rss){
      echo '<ul>';
      $i = 0;
      foreach ($rss->items as $line){
        echo '<li><a href="' . trim ($line['link']) . (strstr ($line['link'], '?') == false ? '?' : '&') . 'tag=' . $tag .'" target="_blank">' . $line['title'] . '</a></li>';
        $i++;
        if (strcmp (strval ($i), $num) == 0){
          break;
        }
      }
      echo '</ul>';
    } else{
      echo '<p>' . __('fetch error', 'ks-amazon-ranking') . '</p>';
    }

    $description2 = empty ($instance['description2']) ? '' : $instance['description2'];
    echo $description2;

    echo $after_widget;
  }

  function update ($new_instance, $old_instance){
    $instance = $old_instance;

    $instance['title'] = strip_tags (stripslashes ($new_instance['title']));
    $instance['description1'] = strip_tags (stripslashes ($new_instance['description1']));
    $instance['description2'] = strip_tags (stripslashes ($new_instance['description2']));
    $instance['country'] = strip_tags (stripslashes ($new_instance['country']));
    $instance['node'] = strip_tags (stripslashes ($new_instance['node']));
    $instance['sort'] = strip_tags (stripslashes ($new_instance['sort']));
    $instance['num'] = strip_tags (stripslashes ($new_instance['num']));
    $instance['tag'] = strip_tags (stripslashes ($new_instance['tag']));

    return $instance;
  }

  function form ($instance){
    $instance = wp_parse_args ((array)$instance,
                               array ('title' => __('Amazon Ranking', 'ks-amazon-ranking'),
                                      'description1' => '',
                                      'description2' => '',
                                      'country' => __('us', 'ks-amazon-ranking'),
                                      'node' => '',
                                      'sort' => 'bestsellers',
                                      'num' => '10',
                                      'tag' => ''));

    $title = htmlspecialchars ($instance['title']);
    $description1 = htmlspecialchars ($instance['description1']);
    $description2 = htmlspecialchars ($instance['description2']);
    $country = htmlspecialchars ($instance['country']);
    $node = htmlspecialchars ($instance['node']);
    $sort = htmlspecialchars ($instance['sort']);
    $num = htmlspecialchars ($instance['num']);
    $tag = htmlspecialchars ($instance['tag']);

    echo '<p><label for="' .
         $this->get_field_name ('title') .
         '">' .
         __('Title:', 'ks-amazon-ranking') .
         '</label><br /><input class="widefat" id= "' .
         $this->get_field_id ('title') .
         '" name="' .
         $this->get_field_name ('title') .
         '" type="text" value="' .
         $title .
         '" /></p>';

    echo '<p><label for="' .
         $this->get_field_name ('description1') .
         '">' .
         __('Description above Ranking:', 'ks-amazon-ranking') .
         '</label><br /><input class="widefat" id= "' .
         $this->get_field_id ('description1') .
         '" name="' .
         $this->get_field_name ('description1') .
         '" type="text" value="' .
         $description1 .
         '" /><br /><small>' .
         __('e.g. "Bestseller Books"', 'ks-amazon-ranking') .
         '</small></p>';

    echo '<p><label for="' .
         $this->get_field_name ('description2') .
         '">' .
         __('Description below Ranking:', 'ks-amazon-ranking') .
         '</label><br /><input class="widefat" id= "' .
         $this->get_field_id ('description2') .
         '" name="' .
         $this->get_field_name ('description2') .
         '" type="text" value="' .
         $description2 .
         '" /><br /><small>' .
         __('e.g. "Amazon.com Associate"', 'ks-amazon-ranking') .
         '</small></p>';

    echo '<p><label for="' .
         $this->get_field_name ('country') .
         '">' .
         __('Country:', 'ks-amazon-ranking') .
         '</label><br /><select class="widefat" id= "' .
         $this->get_field_id ('country') .
         '" name="' .
         $this->get_field_name ('country') .
         '">';
    echo '<option value="us"' . ks_amazon_ranking_echo_option_selected ('us', $country) . '>' . __('United States', 'ks-amazon-ranking') . '</option>';
    echo '<option value="uk"' . ks_amazon_ranking_echo_option_selected ('uk', $country) . '>' . __('United Kingdom', 'ks-amazon-ranking') . '</option>';
    echo '<option value="de"' . ks_amazon_ranking_echo_option_selected ('de', $country) . '>' . __('Germany', 'ks-amazon-ranking') . '</option>';
    echo '<option value="fr"' . ks_amazon_ranking_echo_option_selected ('fr', $country) . '>' . __('France', 'ks-amazon-ranking') . '</option>';
    echo '<option value="jp"' . ks_amazon_ranking_echo_option_selected ('jp', $country) . '>' . __('Japan', 'ks-amazon-ranking') . '</option>';
    echo '<option value="ca"' . ks_amazon_ranking_echo_option_selected ('ca', $country) . '>' . __('Canada', 'ks-amazon-ranking') . '</option>';
    echo '</select></p>';

    echo '<p><label for="' .
         $this->get_field_name ('node') .
         '">' .
         __('Node:', 'ks-amazon-ranking') .
         '</label><br /><input class="widefat" id= "' .
         $this->get_field_id ('node') .
         '" name="' .
         $this->get_field_name ('node') .
         '" type="text" value="' .
         $node .
         '" /><br /><small>' .
         __('You may leave this blank, if you don\'t know what does "Node" mean. "Node" is a category number for Amazon. It is inculuded in the URL for some Amazon pages.', 'ks-amazon-ranking') .
         '</small></p>';

    echo '<p><label for="' .
         $this->get_field_name ('sort') .
         '">' .
         __('Ranking Type:', 'ks-amazon-ranking') .
         '</label><br /><select class="widefat" id= "' .
         $this->get_field_id ('sort') .
         '" name="' .
         $this->get_field_name ('sort') .
         '">';
    echo '<option value="bestsellers"' . ks_amazon_ranking_echo_option_selected ('bestsellers', $sort) . '>'  . __('Bestsellers', 'ks-amazon-ranking') . '</option>';
    echo '<option value="new-releases"' . ks_amazon_ranking_echo_option_selected ('new-releases', $sort) . '>' . __('Hot New Releases', 'ks-amazon-ranking') . '</option>';
    echo '<option value="most-gifted"' . ks_amazon_ranking_echo_option_selected ('most-gifted', $sort) . '>' . __('Most Gifted', 'ks-amazon-ranking') . '</option>';
    echo '<option value="most-wished-for"' . ks_amazon_ranking_echo_option_selected ('most-wished-for', $sort) . '>' . __('Most Wished For', 'ks-amazon-ranking') . '</option>';
    echo '</select></p>';

    echo '<p><label for="' .
         $this->get_field_name ('num') .
         '">' .
         __('Number of Items:', 'ks-amazon-ranking') .
         '</label><br /><select class="widefat" id= "' .
         $this->get_field_id ('num') .
         '" name="' .
         $this->get_field_name ('num') .
         '">';
    for ($i = 1; $i < 11; $i++){
      echo '<option value="' . strval ($i) . '"' . ks_amazon_ranking_echo_option_selected (strval ($i), $num) . '>' . strval ($i) . '</option>';
    }
    echo '</select></p>';

    echo '<p><label for="' .
         $this->get_field_name ('tag') .
         '">' .
         __('Your Associate Tag:', 'ks-amazon-ranking') .
         '</label><br /><input class="widefat" id= "' .
         $this->get_field_id ('tag') .
         '" name="' .
         $this->get_field_name ('tag') .
         '" type="text" value="' .
         $tag .
         '" /><br /><small>' .
         __('If you leave this blank, author\'s tag will be shown in your site. If you fill in your tag, author\'s tag will be shown once a 20 times. (You may modify program file, if you want to.)', 'ks-amazon-ranking') .
         '</small></p>';
  }
}

function KsAmazonRankingInit(){
  register_widget ('KsAmazonRankingWidget');
}

add_action ('widgets_init', 'KsAmazonRankingInit');
?>
