<?php

/**
 * Template to display [mld_student_dashboard] shortcode staff content
 */
if (! defined('ABSPATH')) exit;
?>
<h3 class="exms-std-dashboard-heading">
  <?php echo __('My Staff Profile', 'myrtle-learning-dashboard'); ?>
</h3>
<div class="mld-std-staff-content">
  <div class="mld-std-staff-filters">
    <div class="mld-std-staff-filters-fields">
      <h4> <?php echo __('Select Group', 'mld') ?></h4>
      <select name="" id="mld-staffs-page-group-dropdown">
        <option value=""><?php echo __('Select a Group', 'mld'); ?></option>
        <?php
        if (is_array($group_ids) && ! empty($group_ids)) {
          foreach ($group_ids as $group_id) {
        ?>
            <option value="<?php echo $group_id; ?>"><?php echo get_the_title($group_id); ?></option>
        <?php
          }
        }
        ?>
      </select>
    </div>
    <div class="mld-std-staff-filters-fields">
      <h4> <?php echo __('Select User', 'mld') ?></h4>
      <select name="" id="mld-staffs-page-user-dropdown">
      </select>
    </div>
  </div>
  <div class="mld-std-staff-wrap">
    <div class="mld-staff-shell">

      <!-- Card 1 -->
      <div class="mld-staff-card">
        <button class="mld-staff-eye" type="button" aria-label="Preview profile">
          <!-- eye icon -->
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 5c5.2 0 9.6 3.3 11 7-1.4 3.7-5.8 7-11 7S2.4 15.7 1 12c1.4-3.7 5.8-7 11-7zm0 2c-4.1 0-7.7 2.4-9 5 1.3 2.6 4.9 5 9 5s7.7-2.4 9-5c-1.3-2.6-4.9-5-9-5zm0 2.2A2.8 2.8 0 1 1 12 15a2.8 2.8 0 0 1 0-5.6z" />
          </svg>
        </button>

        <div class="mld-staff-avatar">
          <img src="https://i.pravatar.cc/150?img=32" alt="Robert Whistable">
        </div>

        <h3 class="mld-staff-name">Robert Whistable</h3>
        <p class="mld-staff-role">Group leader</p>

        <div class="mld-staff-stats">
          <div class="mld-stat">
            <span class="mld-stat-label">Groups</span>
            <span class="mld-stat-value">20</span>
          </div>

          <span class="mld-stat-divider" aria-hidden="true"></span>

          <div class="mld-stat">
            <span class="mld-stat-label">Subjects</span>
            <span class="mld-stat-value">33</span>
          </div>

          <span class="mld-stat-divider" aria-hidden="true"></span>

          <div class="mld-stat">
            <span class="mld-stat-label">Experience</span>
            <span class="mld-stat-value">5 years</span>
          </div>
        </div>

        <a class="mld-staff-btn" href="#" aria-label="View profile for Robert Whistable">View profile</a>
      </div>

      <!-- Duplicate cards (change img/text as needed) -->
      <div class="mld-staff-card">
        <button class="mld-staff-eye" type="button" aria-label="Preview profile">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 5c5.2 0 9.6 3.3 11 7-1.4 3.7-5.8 7-11 7S2.4 15.7 1 12c1.4-3.7 5.8-7 11-7zm0 2c-4.1 0-7.7 2.4-9 5 1.3 2.6 4.9 5 9 5s7.7-2.4 9-5c-1.3-2.6-4.9-5-9-5zm0 2.2A2.8 2.8 0 1 1 12 15a2.8 2.8 0 0 1 0-5.6z" />
          </svg>
        </button>

        <div class="mld-staff-avatar">
          <img src="https://i.pravatar.cc/150?img=12" alt="Robert Whistable">
        </div>

        <h3 class="mld-staff-name">Robert Whistable</h3>
        <p class="mld-staff-role">Group leader</p>

        <div class="mld-staff-stats">
          <div class="mld-stat">
            <span class="mld-stat-label">Groups</span>
            <span class="mld-stat-value">20</span>
          </div>
          <span class="mld-stat-divider" aria-hidden="true"></span>
          <div class="mld-stat">
            <span class="mld-stat-label">Subjects</span>
            <span class="mld-stat-value">33</span>
          </div>
          <span class="mld-stat-divider" aria-hidden="true"></span>
          <div class="mld-stat">
            <span class="mld-stat-label">Experience</span>
            <span class="mld-stat-value">5 years</span>
          </div>
        </div>

        <a class="mld-staff-btn" href="#">View profile</a>
      </div>

      <div class="mld-staff-card">
        <button class="mld-staff-eye" type="button" aria-label="Preview profile">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 5c5.2 0 9.6 3.3 11 7-1.4 3.7-5.8 7-11 7S2.4 15.7 1 12c1.4-3.7 5.8-7 11-7zm0 2c-4.1 0-7.7 2.4-9 5 1.3 2.6 4.9 5 9 5s7.7-2.4 9-5c-1.3-2.6-4.9-5-9-5zm0 2.2A2.8 2.8 0 1 1 12 15a2.8 2.8 0 0 1 0-5.6z" />
          </svg>
        </button>

        <div class="mld-staff-avatar">
          <img src="https://i.pravatar.cc/150?img=47" alt="Robert Whistable">
        </div>

        <h3 class="mld-staff-name">Robert Whistable</h3>
        <p class="mld-staff-role">Group leader</p>

        <div class="mld-staff-stats">
          <div class="mld-stat">
            <span class="mld-stat-label">Groups</span>
            <span class="mld-stat-value">20</span>
          </div>
          <span class="mld-stat-divider" aria-hidden="true"></span>
          <div class="mld-stat">
            <span class="mld-stat-label">Subjects</span>
            <span class="mld-stat-value">33</span>
          </div>
          <span class="mld-stat-divider" aria-hidden="true"></span>
          <div class="mld-stat">
            <span class="mld-stat-label">Experience</span>
            <span class="mld-stat-value">5 years</span>
          </div>
        </div>

        <a class="mld-staff-btn" href="#">View profile</a>
      </div>

      <div class="mld-staff-card">
        <button class="mld-staff-eye" type="button" aria-label="Preview profile">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 5c5.2 0 9.6 3.3 11 7-1.4 3.7-5.8 7-11 7S2.4 15.7 1 12c1.4-3.7 5.8-7 11-7zm0 2c-4.1 0-7.7 2.4-9 5 1.3 2.6 4.9 5 9 5s7.7-2.4 9-5c-1.3-2.6-4.9-5-9-5zm0 2.2A2.8 2.8 0 1 1 12 15a2.8 2.8 0 0 1 0-5.6z" />
          </svg>
        </button>

        <div class="mld-staff-avatar">
          <img src="https://i.pravatar.cc/150?img=11" alt="Robert Whistable">
        </div>

        <h3 class="mld-staff-name">Robert Whistable</h3>
        <p class="mld-staff-role">Group leader</p>

        <div class="mld-staff-stats">
          <div class="mld-stat">
            <span class="mld-stat-label">Groups</span>
            <span class="mld-stat-value">20</span>
          </div>
          <span class="mld-stat-divider" aria-hidden="true"></span>
          <div class="mld-stat">
            <span class="mld-stat-label">Subjects</span>
            <span class="mld-stat-value">33</span>
          </div>
          <span class="mld-stat-divider" aria-hidden="true"></span>
          <div class="mld-stat">
            <span class="mld-stat-label">Experience</span>
            <span class="mld-stat-value">5 years</span>
          </div>
        </div>

        <a class="mld-staff-btn" href="#">View profile</a>
      </div>

    </div>
  </div>

</div>