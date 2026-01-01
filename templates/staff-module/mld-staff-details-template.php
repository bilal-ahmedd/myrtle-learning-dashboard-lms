<?php

/**
 * Template to display [mld_student_dashboard] shortcode staff content
 */
if (! defined('ABSPATH')) exit;
?>
<div class="mld-std-staff-content">
  <div class="mld-std-staff-btn-header">
  <button class="mld-std-back-btn" type="button">
    <span class="mld-btn-ic dashicons dashicons-arrow-left-alt"></span>
    Back
  </button>

  <button class="mld-std-download-btn" type="button">
    <span class="mld-btn-ic dashicons dashicons-download"></span>
    Download
  </button>
</div>

  <!-- ===== Staff Profile Card ===== -->
<div class="mld-staff-card">
  <div class="mld-staff-top">
    <div class="mld-staff-details-avatar">
      <img src="https://i.pravatar.cc/150?img=32" alt="Staff Image">
    </div>

    <div class="mld-staff-meta">
      <div class="mld-meta-row">
        <div class="mld-field">
          <label>Name</label>
          <div class="mld-value">Maryum Shafique</div>
        </div>

        <div class="mld-field">
          <label>Year of Teaching</label>
          <div class="mld-value">8</div>
        </div>
      </div>

      <div class="mld-subjects">
        <strong>Subjects</strong>
        <p>
          English Literature, Primary Teaching, English Language,
          11Plus Verbal Reasoning, 11Plus Non Verbal Reasoning, 11Plus English
        </p>
      </div>

      <div class="mld-status">
        <span class="mld-dot active"></span> Available
        <span class="mld-dot inactive"></span> Unavailable
      </div>
    </div>

    <div class="mld-dbs-toggle">
      <span>DBS</span>
      <label class="mld-switch">
        <input type="checkbox">
        <span class="slider"></span>
      </label>
    </div>
  </div>
</div>

<!-- ===== Personal Information ===== -->
<div class="mld-info-card">
  <h3>Personal Information</h3>
  <p>
    I am an enthusiastic and passionate educator with over 5 years experience.
    I started as a EYFS Teaching Assistant and then completed my PGCE at the
    prestigious Roehampton University in Primary Education with a specialism
    in EYFS and KS1. I have taught across KS1 and KS2 and have experience with
    Year 1, Year 3, Year 5 and booster club for Year 6. Prior to this I studied
    English Literature BA (Hons) at the University of Greenwich and reading is
    a passion of mine. I have also completed a higher education certification
    in Women’s Health Studies.
  </p>
</div>

<!-- ===== Educational Details ===== -->
<div class="mld-info-card mld-info-table-card">
  <h3>Educational details</h3>

  <table class="mld-table">
    <thead>
      <tr>
        <th>Date</th>
        <th>University</th>
        <th>Subjects</th>
        <th>Qualification</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>24th February, 2024</td>
        <td>University of Greenwich</td>
        <td>Primary PGCE</td>
        <td>Masters equivalent</td>
      </tr>
      <tr>
        <td>24th February, 2024</td>
        <td>University of Greenwich</td>
        <td>Primary PGCE</td>
        <td>Masters equivalent</td>
      </tr>
    </tbody>
  </table>
</div>


</div>