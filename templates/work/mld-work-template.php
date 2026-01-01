<?php

/**
 * Template to display [mld_student_dashboard] shortcode staff content
 */
if (! defined('ABSPATH')) exit;
?>
<h3 class="exms-std-dashboard-heading">
  <?php echo __('My Work', 'myrtle-learning-dashboard'); ?>
</h3>
<div class="mld-std-work-content">
    <div class="mld-std-work-filters">
        <div class="mld-std-work-filters-fields">
            <h4> <?php echo __( 'Select Group', 'mld' ) ?></h4>
            <select name="" id="mld-courses-page-group-dropdown">
                <option value=""><?php echo __('Select a Group', 'mld'); ?></option>
                <?php 
                if( is_array( $group_ids ) && ! empty( $group_ids ) ) {
                    foreach( $group_ids as $group_id ) {
                        ?>
                        <option value="<?php echo $group_id; ?>"><?php echo get_the_title( $group_id ); ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        <div class="mld-std-work-filters-fields">
            <h4> <?php echo __( 'Select User', 'mld' ) ?></h4>
            <select name="" id="mld-courses-page-user-dropdown">
            </select>
        </div>
    </div>
    <div class="mld-std-work-filters">
        <div class="mld-std-work-filters-fields">
            <h4> <?php echo __( 'Select Course', 'mld' ) ?></h4>
            <select name="" id="mld-courses-page-group-dropdown">
                <option value=""><?php echo __('Select a Group', 'mld'); ?></option>
                <?php 
                if( is_array( $group_ids ) && ! empty( $group_ids ) ) {
                    foreach( $group_ids as $group_id ) {
                        ?>
                        <option value="<?php echo $group_id; ?>"><?php echo get_the_title( $group_id ); ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        <div class="mld-std-work-filters-fields">
            <h4> <?php echo __( 'Select Lesson', 'mld' ) ?></h4>
            <select name="" id="mld-courses-page-user-dropdown">
            </select>
        </div>
    </div>
    <div class="mld-std-work-filters">
        <div class="mld-std-work-filters-fields">
            <h4> <?php echo __( 'Select Topic', 'mld' ) ?></h4>
            <select name="" id="mld-courses-page-group-dropdown">
                <option value=""><?php echo __('Select a Group', 'mld'); ?></option>
                <?php 
                if( is_array( $group_ids ) && ! empty( $group_ids ) ) {
                    foreach( $group_ids as $group_id ) {
                        ?>
                        <option value="<?php echo $group_id; ?>"><?php echo get_the_title( $group_id ); ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        <div class="mld-std-work-filters-fields">
            <h4> <?php echo __( 'Select Test/Quiz', 'mld' ) ?></h4>
            <select name="" id="mld-courses-page-user-dropdown">
            </select>
        </div>
    </div>
    <button class="mld-std-apply-btn mld-course-tab-apply-btn">
        <?php echo __('Proceed', 'mld'); ?>
        <span class="mld-loader"></span>
    </button>
</div>