<?php
/**
 * Plugin Name: WP Basic Widgets 
 * Plugin URI: https://www.besserdich-redmann.com
 * Description: Some useful, basic widgets.
 * Version: 0.1
 * Author: Nina Taberski-Besserdich
 * Author URI: https://www.besserdich-redmann.com/team/nina-taberski-besserdich/
 * License: GPL2
 */

/*  Copyright 2013 Nina Taberski-Besserdich  (email: nt@besserdich-redmann.com   )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/





add_action('widgets_init', create_function('', 'return register_widget("BICCurrentCategoryPosts");'));
add_action('widgets_init', create_function('', 'return register_widget("BICCategoryPosts");'));
add_action('widgets_init', create_function('', 'return register_widget("CategoryTextWidget");'));




function wp_basic_widgets_scripts(){
    
    //css
    wp_enqueue_style('wp-basic-widgets-css', plugins_url('/wp-basic-widgets/wp-basic-widgets.css'));

}

add_action( 'wp_enqueue_scripts', 'wp_basic_widgets_scripts' );







/* 
 * Shows posts of a specific category.
 * options: number of posts, with / without thumbnail 
 */

class BICCategoryPosts extends WP_Widget {
    
    /** constructor */
    
    function BICCategoryPosts() {
        parent::WP_Widget(false, $name = 'BIC-CategoryPosts-Widget');
    }
    

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        
        extract( $args );
        
        $title 	= apply_filters('widget_title', $instance['title']);         
       
        $category = esc_attr($instance['category']);
        
        $num_posts = esc_attr($instance['num_posts']);
        
        $txt_show_all = esc_attr($instance['txt_show_all']);

        
        if ($num_posts==''){
            
            $num_posts = 5;
            
        }
        
        $show_thumb = esc_attr($instance['show_thumb']);
        
        $thumb_width = esc_attr($instance['thumb_width']);
        
        if ($thumb_width==''){
            
            $thumb_width = 50;
            
        }
        
        
         
        $thumb_height = esc_attr($instance['thumb_height']);
        
        if ($thumb_height==''){
            
            $thumb_height = 50;
            
        }
        
        
        $layout_horizontal = esc_attr($instance['layout_horizontal']);
        
        
          
        global $post;
        
        //$post_type = get_post_type($post->ID);
          
          
        $cat = wp_get_post_categories( $post->ID );
	
        $_query = array('category' => $category, 'numberposts' => $num_posts);
            
        $all_posts = get_posts($_query);
          
         if(!empty($all_posts)){
             
             
             foreach($all_posts as $p) {

                $r = array();
                $r['ID']        = $p->ID;
                $r['url']       = get_permalink($p->ID);
                $r['title']     = $p->post_title;
                
                $thumb_id = get_post_thumbnail_id($p->ID);
                $thumb_url = wp_get_attachment_image_src($thumb_id,'thumbnail', true);
                $r['thumb']     = $thumb_url[0];

                $results[] = $r;
             }
         
         
         echo $before_widget;
        
            if ( $title == '') {
                
                
                $curr_cat_name = get_cat_name( $category );
                
                $title = $curr_cat_name; 
                
            }
                
   

$cat_link = get_category_link( $category ); 
       
echo $before_title . $title . $after_title;

          
          
          
          
          
          echo '<div id="bic-cp"><ul';
          
            if($show_thumb == true){
                
                echo ' class="bic-cp-thumb-ul"';
                
            }
            
          echo '>';  
          
          foreach(array_slice($results,0,10) as $result) {
          
            echo '<li';
              
              if($layout_horizontal == true){
                  
                  echo ' class="bic-cp-li-horizontal"';
                  
              }
              
              echo '>';
            
            if(($show_thumb == true) && (has_post_thumbnail( $result['ID'] ))){
                
                echo '<a href="'.$result['url'].'"><img src="'.$result['thumb'].'" width="'.$thumb_width.'" height="'.$thumb_height.'"></a>';
            
            }
            
            
            if($layout_horizontal == false){
            
                echo '<a href="'.$result['url'].'">'.$result['title'] . '</a>';
            
            }
            
            echo '</li>';
            
          }  
          
          echo '</ul></div>';
          
          
           if ($txt_show_all != '') {

           
        echo '<a class="btn btn-primary btn-lg bic-cp-btn" href="'.$cat_link.'">'.$txt_show_all.'</a>';
        
           }

        }
        
            
                echo $after_widget;
          }
           

 
    
    
    
    

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
	     $instance = $old_instance;
             $instance['category'] = strip_tags($new_instance['category']);
             $instance['num_posts'] = strip_tags($new_instance['num_posts']);
              $instance['txt_show_all'] = strip_tags($new_instance['txt_show_all']);
             $instance['show_thumb'] = strip_tags($new_instance['show_thumb']);
	     $instance['title'] = strip_tags($new_instance['title']);
             $instance['thumb_width'] = strip_tags($new_instance['thumb_width']);
             $instance['thumb_height'] = strip_tags($new_instance['thumb_height']);
             $instance['layout_horizontal'] = strip_tags($new_instance['layout_horizontal']);
             
    
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        $title = esc_attr($instance['title']);
        $category = esc_attr($instance['category']);  
        $num_posts = esc_attr($instance['num_posts']);
        $txt_show_all = esc_attr($instance['txt_show_all']);
        $show_thumb = esc_attr($instance['show_thumb']);
        $thumb_width = esc_attr($instance['thumb_width']);
        $thumb_height = esc_attr($instance['thumb_height']);
        $layout_horizontal = esc_attr($instance['layout_horizontal']);
    
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br/>
        
           <br/>
            <!-- catagory -->
          <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Posts aus Kategorie:'); ?></label> 
          
          <?php wp_dropdown_categories( 'selected='.$category.'&echo=1&name='.$this->get_field_name('category').'&id='.$this->get_field_id('category'));?> 
          
           <br/><br/>
           <!-- Show Thumbnail -->
           <label for="<?php echo $this->get_field_id('show_thumb'); ?>"><?php _e('Thumbnail anzeigen:'); ?></label> 
          <input  id="<?php echo $this->get_field_id('show_thumb'); ?>" name="<?php echo $this->get_field_name('show_thumb'); ?>" type="checkbox" <?php if($show_thumb==true){echo "checked";} ?> />
       
          
           <br/><br/>
           <!-- Thumbnail Size  -->
           Thumbnail Size in px:<br/>
           <label for="<?php echo $this->get_field_id('thumb_width'); ?>"><?php _e('Width:'); ?></label> 
          <input  id="<?php echo $this->get_field_id('thumb_width'); ?>" name="<?php echo $this->get_field_name('thumb_width'); ?>" type="text" size="5" value="<?php echo $thumb_width; ?>"  />
       
           <label for="<?php echo $this->get_field_id('thumb_height'); ?>"><?php _e('Height:'); ?></label> 
          <input  id="<?php echo $this->get_field_id('thumb_height'); ?>" name="<?php echo $this->get_field_name('thumb_height'); ?>" type="text" size="5" value="<?php echo $thumb_height; ?>"  />
       
          
          <br/><br/>
           <!-- Number of Posts  -->
           <label for="<?php echo $this->get_field_id('num_posts'); ?>"><?php _e('Anzahl Posts:'); ?></label> 
          <input id="<?php echo $this->get_field_id('num_posts'); ?>" name="<?php echo $this->get_field_name('num_posts'); ?>" size="5" type="text" value="<?php echo $num_posts; ?>" />
        
           <br/><br/>
            <!-- Text for Show all  -->
           <label for="<?php echo $this->get_field_id('txt_show_all'); ?>"><?php _e('Text Show All:'); ?></label> 
          <input id="<?php echo $this->get_field_id('txt_show_all'); ?>" name="<?php echo $this->get_field_name('txt_show_all'); ?>" size="30" type="text" value="<?php echo $txt_show_all; ?>" />
        
           <br/><br/>
           
           <!-- Layout Horizontal  -->
           <label for="<?php echo $this->get_field_id('layout_horizontal'); ?>"><?php _e('Horizontal Layout:'); ?></label> 
          <input  id="<?php echo $this->get_field_id('layout_horizontal'); ?>" name="<?php echo $this->get_field_name('layout_horizontal'); ?>" type="checkbox" <?php if($layout_horizontal==true){echo "checked";} ?> />
       
          
        </p>
        
        <?php 
    }

}


/* Shows Entries in current (sub)category.
 * is is archive shows all, on a single it shows all other entries except the acutual one. 
 */

class BICCurrentCategoryPosts extends WP_Widget {
    
    /** constructor */
    
    function BICCurrentCategoryPosts() {
        parent::WP_Widget(false, $name = 'BIC-CurrentCategoryPosts-Widget');
    }
    

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        
        extract( $args );
        
        
           global $post;
          
          $category = get_the_category(); 
          //var_dump($category);
          $catname = $category[0]->cat_name;
          $category_url = get_category_link( $category[0]->term_id );
          
          
          $num_posts = esc_attr($instance['num_posts']);
          
          if ($num_posts == ''){
              
              $num_posts = 100;
              
          }
        
        
          $post_type = get_post_type($post->ID);
          
          
          $cats = wp_get_post_categories( $post->ID );
          
          
          if (is_archive()){
              
            $_query = array('category' => $cats[0],'post_type' => $post_type, 'numberposts' => $num_posts);
            
          }elseif(is_single()){
              
            $_query = array('category' => $cats[0],'post_type' => $post_type, 'numberposts' => $num_posts, 'exclude' => $post->ID);
            
          }
          
          if (isset($mysort) && $mysort == 'true') {
          
            $_query['orderby'] = 'date';
            $_query['order']   = 'ASC';
            
          }
                    
          $all_posts = get_posts($_query);
          
          $results = array();
          
          //var_dump(count($all_posts));
          
             if(!empty($all_posts)){
                 
                 
                 
                 
                 
             
        
        $title 	= apply_filters('widget_title', $instance['title']);
        
        $mysort = $instance['mysort'];
        
      
        
        echo $before_widget;
        
        if ( $title =='' ) {
        
              $title = "".$catname."";
            
        }
          
          
          
          
          
          
          echo $before_title . $title . $after_title;
        
          
          
          
          foreach($all_posts as $p) {
            
            $r = array();
            $r['ID']        = $p->ID;
            $r['url']       = get_permalink($p->ID);
            $r['title']     = $p->post_title;
            
          
            $results[] = $r;
            
          }
          
          
          
          echo '<ul>';
          
          foreach($results as $result) {
          
            echo '<li>';
            
            echo '<a href="'.$result['url'].'">'.$result['title'] . '</a>';
            
            echo '</li>';
            
          }  
          
          echo '</ul>';
          
           
        
        
        
        echo $after_widget;
 
    }
    
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
	     $instance = $old_instance;
	     
	     #print_r($new_instance);
	
	     $instance['title'] = strip_tags($new_instance['title']);
	     $instance['mysort'] = strip_tags($new_instance['mysort']);
              $instance['num_posts'] = strip_tags($new_instance['num_posts']);
    
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        $title = esc_attr($instance['title']);
        $num_posts= esc_attr($instance['num_posts']);
      
        
        #var_dump($sort);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (wenn leer, dann Kategorie):'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
          
          <?php
          $sel = '';
          
                    
          if ($mysort == 'true') $sel = ' checked="checked" ';
          ?>
          
          
            <br/><br/>
           <!-- Number of Posts  -->
           <label for="<?php echo $this->get_field_id('num_posts'); ?>"><?php _e('Anzahl Posts:'); ?></label> 
          <input id="<?php echo $this->get_field_id('num_posts'); ?>" name="<?php echo $this->get_field_name('num_posts'); ?>" size="5" type="text" value="<?php echo $num_posts; ?>" /><br/>
        
          
          <input <?php echo $sel; ?> type="checkbox" id="<?php echo $this->get_field_id('mysort'); ?>" name="<?php echo $this->get_field_name('mysort'); ?>" value="true"/> Neueste zuerst
        </p>
        
        <?php 
    }

}



/*
 * CategoryTextWidget
 * Text widget that is shown only in selected categories and all subcategories 
 * on archive and single template
 * Version 0.2 
 */

class CategoryTextWidget extends WP_Widget {
    
    /** constructor */
    
    function CategoryTextWidget() {
        parent::WP_Widget(false, $name = 'CategoryTextWidget');
    }
    

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        
        extract( $args );
        
        $title 	= apply_filters('widget_title', $instance['title']);         
        $text_content = $instance['text_content'];
        $category = esc_attr($instance['category']);
        
        
        // get actual category 
        
        if(is_archive()){
            $ccat = get_query_var('cat');
        }
        elseif (is_single()) {
            $the_cat = get_the_category();
            $ccat = $the_cat[0]->cat_ID;
        }
       
        
        $this_cat = get_category($ccat); 
        $parent_cat = $this_cat->parent; 
        
        
        if ($ccat == $category || $parent_cat == $category){
              
            echo $before_widget;
        
            if ( $title ) {
                echo $before_title . $title . $after_title;
                
                echo $text_content; 
                                
                echo $after_widget;
           }
    }
    
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
	     $instance = $old_instance;
	
	     $instance['title'] = strip_tags($new_instance['title']);
             $instance['text_content'] = $new_instance['text_content'];
             $instance['category'] = strip_tags($new_instance['category']);
    
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        
        $title = esc_attr($instance['title']);
        $text_content = esc_attr($instance['text_content']);
        $category = esc_attr($instance['category']);
        
        ?>
         <p>
          
          <!-- title -->
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
          
          <!-- text_content -->
          <label for="<?php echo $this->get_field_id('text_content'); ?>"><?php _e('Text:'); ?></label> 
          <textarea class="widefat" id="<?php echo $this->get_field_id('text_content'); ?>" name="<?php echo $this->get_field_name('text_content'); ?>" cols="50" rows="10"><?php echo $text_content; ?></textarea>
          
          <!-- catagory -->
          <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('in Kategorie anzeigen:'); ?></label> 
          
          <?php wp_dropdown_categories( 'selected='.$category.'&echo=1&name='.$this->get_field_name('category').'&id='.$this->get_field_id('category'));?> 
        </p>
        
        <?php 
    }

 
}



?>
