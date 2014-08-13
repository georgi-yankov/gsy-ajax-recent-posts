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

        $number = (!empty($instance['number']) ) ? absint($instance['number']) : 5;
        if (!$number)
            $number = 5;
        $this->posts_to_show = $number;

        $show_date = isset($instance['show_date']) ? $instance['show_date'] : false;
        $this->show_date = $show_date;

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
        $title = isset($instance['title']) ? esc_attr($instance['title']) : __('New title', 'text_domain');
        $number = isset($instance['number']) ? absint($instance['number']) : 5;
        $interval = isset($instance['interval']) ? absint($instance['interval']) : 3;
        $show_date = isset($instance['show_date']) ? (bool) $instance['show_date'] : false;
        ?>

        <style>
            .garp-widget-label {
                display: inline-block;
                width: 40%;
            }
        </style>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <p>
            <label class="garp-widget-label" for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:'); ?></label>
            <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
        </p>

        <p>
            <label class="garp-widget-label" for="<?php echo $this->get_field_id('interval'); ?>"><?php _e('Set interval time:'); ?></label>
            <input id="<?php echo $this->get_field_id('interval'); ?>" name="<?php echo $this->get_field_name('interval'); ?>" type="text" value="<?php echo $interval; ?>" size="3" /> seconds
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_date); ?> id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>" />
            <label for="<?php echo $this->get_field_id('show_date'); ?>"><?php _e('Display post date?'); ?></label>
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
        $instance = $old_instance;
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        $instance['number'] = (int) $new_instance['number'];
        $instance['interval'] = (int) $new_instance['interval'];
        $instance['show_date'] = isset($new_instance['show_date']) ? (bool) $new_instance['show_date'] : false;

        return $instance;
    }

    /**
     * Generates the most recent posts with ajax
     * 
     * @return void
     */
    private function ajax_posts() {
        $query_args = array(
            'post_type' => 'post',
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => $this->posts_to_show,
            'post_status' => 'publish',
            'ignore_sticky_posts' => true
        );

        // The Query
        $the_query = new WP_Query($query_args);
        ?>

        <?php if ($the_query->have_posts()) : ?>

            <ul>
                <?php while ($the_query->have_posts()) : $the_query->the_post(); ?>
                    <li data-garp-post-id="<?php the_ID(); ?>">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        <?php echo ($this->show_date) ? '<span class="post-date">' . get_the_time('F d, Y') . '</span>' : '' ?>
                    </li>
                <?php endwhile; ?>
            </ul>

        <?php else: ?>

            <p><?php _e('Sorry, no posts to be shown.'); ?></p>

        <?php endif; ?>
        <?php
        wp_reset_postdata();
    }

    private $posts_to_show;
    private $show_date;

}