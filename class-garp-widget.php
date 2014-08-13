<?php

/**
 * Adds GARP_Widget widget.
 */
class GARP_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
                'garp_widget', // Base ID
                __('Ajax Recent Posts', 'text_domain'), // Name
                array('description' => __('Your site’s most recent Posts with Ajax.', 'text_domain'),) // Args
        );
    }

    /**
     * Front-end display of widget.
     * 
     * @see WP_Widget::widget()
     *
     * @param array $args Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);

        echo $args['before_widget'];
        
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        self::ajax_posts();

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     * 
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('New title', 'text_domain');
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
    }

    /**
     * Processing widget options on save.
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     * 
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';

        return $instance;
    }

    /**
     * Generates the most recent posts with ajax
     * 
     * @return void
     */
    private function ajax_posts() {
        require 'inc/the-query.php';
        ?>

        <?php if ($the_query->have_posts()) : ?>

            <ul>
                <?php while ($the_query->have_posts()) : $the_query->the_post(); ?>
                    <li data-garp-post-id="<?php the_ID(); ?>">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        <span class="post-date"><?php the_time('F d, Y'); ?></span>
                    </li>
                <?php endwhile; ?>
            </ul>

        <?php else: ?>

            <p><?php _e('Sorry, no posts to be shown.'); ?></p>

        <?php endif; ?>
        <?php
        wp_reset_postdata();
    }

}