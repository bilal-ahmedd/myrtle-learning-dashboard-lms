<div class="mld-resources-wrapper">
    <div class="mld-resource-top-main-section">
        <div class="mld-resource-top-section">
            <div class="mld-top-section-heading">
                <?php echo __( 'Online Resources', 'myrtle-learning-dashboard' ); ?>
            </div>
            <div class="mld-top-inner-section">
                <div class="mld-primary-section mld-resource-btn">
                    <a href="https://www.cgpbooks.co.uk/secondary-books/online-editions" target="_blank"><?php echo __( 'CGP Resources', 'myrtle-learning-dashboard' ); ?></a>
                </div>
                <div class="mld-secondary-section mld-resource-btn">
                    <a href="https://www.pearsonactivelearn.com/app/home" target="_blank"><?php echo __( 'OCR Resources', 'myrtle-learning-dashboard' ); ?></a>
                </div>
                <div class="mld-secondary-section mld-resource-btn">
                    <a href="https://www.pearsonactivelearn.com/app/home" target="_blank"><?php echo __( 'AQA Resources', 'myrtle-learning-dashboard' ); ?></a>
                </div>
                    <div class="mld-secondary-section mld-resource-btn">
                        <a href="https://www.pearsonactivelearn.com/app/home" target="_blank"><?php echo __( 'Pearson Resources', 'myrtle-learning-dashboard' ); ?></a>
                    </div>
            </div>
        </div>
    </div>
    <div class="mld-resourse-content-section">
        <div class="mld-resource-program">
            <div class="mld-program"><?php echo __( 'Select Program *', 'myrtle-learning-dashboard' ); ?></div>
            <select name="" id="mld-program-dropdown">
                <option value=""><?php echo __( 'Select Program', 'myrtle-learning-dashboard' ); ?></option>
                <?php
                if( $tags && is_array( $tags ) ) {
                    foreach( $tags as $tag ) {

                        if( $tag->name == 'Available Course' || $tag->name == 'available course' ) {
                            continue;
                        }
                        ?>
                        <option value="<?php echo $tag->term_id;?>"><?php echo $tag->name;?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        <div class="mld-resource-subject-wrapper">
            <div class="mld-resource-subject">
                <div class="mld-subject"><?php echo __( 'Select Subject *', 'myrtle-learning-dashboard' ); ?></div>
                <select name="" id="mld-subject-dropdown">
                    <option value=""><?php echo __( 'Select a Subject', 'myrtle-learning-dashboard' ); ?></option>
                </select>
            </div>
            <div class="mld-resource-type">
                <div class="mld-type"><?php echo __( 'Select Type', 'myrtle-learning-dashboard' ); ?></div>
                <select id="mld-type-dropdown">
                    <option value=""><?php echo __( 'Select a Type', 'myrtle-learning-dashboard' ); ?></option>
                    <?php 
                    $resource_types = self::get_type_dropdown();
                    foreach( $resource_types as $key => $resource_type ) {
                        ?>
                        <option value="<?php echo $key; ?>"><?php echo $resource_type; ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <div class="mld-clear-both"></div>
        </div>
        <div class="mld-exam-wrapper">
            <div class="mld-resource-exam">
                <div class="mld-exam"><?php echo __( 'Select Examboard', 'myrtle-learning-dashboard' ); ?></div>
                <select name="" id="mld-exam-dropdown">
                    <option value=""><?php echo __( 'Select Examboard', 'myrtle-learning-dashboard' ); ?></option>
                </select>
            </div>

            <div class="mld-resource-paper">
                <div class="mld-paper"><?php echo __( 'Select Paper', 'myrtle-learning-dashboard' ); ?></div>
                <select name="" id="mld-paper-dropdown">
                    <option value=""><?php echo __( 'Select a paper', 'myrtle-learning-dashboard' ); ?></option>
                    <option value="<?php echo __( 'mark-scheme', 'myrtle-learning-dashboard' ); ?>"><?php echo __( 'Mark Scheme', 'myrtle-learning-dashboard' ); ?></option>
                    <option value="<?php echo __( 'question', 'myrtle-learning-dashboard' ); ?>"><?php echo __( 'Question Papers', 'myrtle-learning-dashboard' ); ?></option>
                </select>
            </div>
            <div class="mld-clear-both"></div>
        </div>
        <div class="mld-exam-wrapper">
            <div class="mld-resource-exam">
                <div class="mld-exam"><?php echo __( 'Select Tier', 'myrtle-learning-dashboard' ); ?></div>
                <select id="mld-tier-dropdown">
                    <option value=""><?php echo __( 'Select a Tier', 'myrtle-learning-dashboard' ); ?></option>
                    <option value="foundation"><?php echo __( 'Foundation', 'myrtle-learning-dashboard' ); ?></option>
                    <option value="higher"><?php echo __( 'Higher', 'myrtle-learning-dashboard' ); ?></option>
                </select>
            </div>

            <div class="mld-resource-paper">
                <div class="mld-paper"><?php echo __( 'Select Year', 'myrtle-learning-dashboard' ); ?></div>
                <select id="mld-year-dropdown">
                    <option value=""><?php echo __( 'Select a Year', 'myrtle-learning-dashboard' ); ?></option>
                    <?php
                    if( $resource_year && is_array( $resource_year ) ) {
                        foreach( $resource_year as $year ) {

                            if( empty ( $year->resource_year ) ) {
                                continue;
                            }
                            ?>
                            <option value="<?php echo $year->resource_year; ?>"><?php echo $year->resource_year; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="mld-clear-both"></div>
        </div>
    </div>
    <div class="mld-resource-answer-main-wrapper">
        <div class="mld-resource-answer-wrapper">
            <div class="mld-resource-answer-title"><?php echo __( 'Choose Appropriate Document', 'myrtle-learning-dashboard' ); ?></div>
            <select id="resource-answer-dropdown">
                <option value=""><?php echo __( 'Choose Document', 'myrtle-learning-dashboard' ); ?></option>
            </select>
            <div class="mld-proceed-btn">
                <?php echo __( 'PROCEED', 'myrtle-learning-dashboard' ); ?>
            </div>
        </div>
    </div>
    <div style="display: none;"class="mld-pop-outer">
        <div class="mld-pop-inner mld-resource-popup-inner">
            <div class="mld-popup-header">
               <div class="mld-close"><div class="dashicons dashicons-no"></div></div>
               <div class="resource-mld-header-title"></div>
               <div class="mld-clear-both"></div>
            </div>
            <div class="mld-resource-content"></div>
            <div class="mld-download-wrapper">
                <a class="mld-download-link" download><?php echo __( 'DOWNLOAD', 'myrtle-learning-dashboard' ); ?> <span class="mld-download-icon dashicons dashicons-download"></span> </a>
            </div>
        </div>
    </div>
</div>