<?php require_once __DIR__ . '/includes/bootstrap.php'; if (empty($_SESSION['admin_id'])) { header('Location: admin_login.php'); exit; } ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#10100f">
  <meta name="description" content="Manage ELITE CUTS appointments.">
  <title>Appointments | ELITE CUTS</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="js/main.js" defer></script>
  <script src="js/admin.js" defer></script>
</head>
<body>
  <header class="site-header">
    <a class="brand" href="index.php" aria-label="Elite Cuts home"><span class="brand-mark">EC</span><span>ELITE <b>CUTS</b></span></a>
    <button class="menu-toggle" type="button" aria-expanded="false" aria-label="Open navigation"><span></span><span></span></button>
    <nav class="site-nav" aria-label="Main navigation"><a href="index.php">Home</a><a href="services.php">Services</a><a href="booking.php">Book a visit</a><a class="active" href="admin.php">Appointments</a></nav>
    <a class="header-cta" href="logout.php">Log out <span aria-hidden="true">↗</span></a>
  </header>
  <main class="dashboard-page">
    <section class="dashboard-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> ELITE CUTS / MANAGEMENT</p><h1>Appointment <em>book.</em></h1><p>Keep track of every visit, all in one place.</p></div><a class="button button-gold" href="booking.php">Create booking <span aria-hidden="true">↗</span></a></section>
    <section class="stats-grid" aria-label="Appointment statistics">
      <article class="stat-card"><span class="stat-label">TOTAL APPOINTMENTS</span><strong data-stat="total">0</strong><span class="stat-note">All records</span></article>
      <article class="stat-card"><span class="stat-label">PENDING</span><strong data-stat="pending">0</strong><span class="stat-note"><i class="status-dot pending-dot"></i> Awaiting visit</span></article>
      <article class="stat-card"><span class="stat-label">CONFIRMED</span><strong data-stat="confirmed">0</strong><span class="stat-note"><i class="status-dot confirmed-dot"></i> Ready to go</span></article>
      <article class="stat-card"><span class="stat-label">COMPLETED</span><strong data-stat="completed">0</strong><span class="stat-note"><i class="status-dot completed-dot"></i> Visits finished</span></article>
      <article class="stat-card"><span class="stat-label">CANCELLED</span><strong data-stat="cancelled">0</strong><span class="stat-note"><i class="status-dot cancelled-dot"></i> Cancelled records</span></article>
    </section>
    <section class="appointments-panel panel">
      <div class="appointments-heading"><div><p class="eyebrow">YOUR SCHEDULE</p><h2>All appointments <span id="results-count">(0)</span></h2></div><label class="search-control"><span aria-hidden="true">⌕</span><input id="appointment-search" type="search" placeholder="Search name or reference" aria-label="Search appointments"></label></div>
      <div class="filters-row">
        <label class="filter-control"><span>DATE</span><input id="filter-date" type="date" aria-label="Filter by date"></label>
        <label class="filter-control"><span>STATUS</span><select id="filter-status" aria-label="Filter by status"><option value="">All statuses</option><option value="Pending">Pending</option><option value="Confirmed">Confirmed</option><option value="Completed">Completed</option><option value="Cancelled">Cancelled</option></select></label>
        <button class="clear-filters" id="clear-filters" type="button">Clear filters <span aria-hidden="true">↺</span></button>
      </div>
      <div class="table-wrap">
        <table class="appointment-table">
          <thead><tr><th>CLIENT</th><th>SERVICE</th><th>DATE &amp; TIME</th><th>REFERENCE</th><th>STATUS</th><th>REQUEST</th><th><span class="visually-hidden">Actions</span></th></tr></thead>
          <tbody id="appointment-rows"></tbody>
        </table>
      </div>
      <div class="empty-state" id="empty-state" hidden><span class="empty-icon" aria-hidden="true">✦</span><h3>No appointments found</h3><p>Try changing your filters or create a new booking.</p><a class="text-link dark-link" href="booking.php">Book an appointment <span aria-hidden="true">↗</span></a></div>
    </section>
  </main>
  <footer class="site-footer"><a class="brand footer-brand" href="index.php"><span class="brand-mark">EC</span><span>ELITE <b>CUTS</b></span></a><p>Good cuts. Great conversations.</p><div class="footer-links"><a href="services.php">Services</a><a href="booking.php">Book a visit</a><a href="admin.php">Appointments</a></div><span class="copyright">© <span data-current-year></span> ELITE CUTS</span></footer>
  <div class="toast-region" aria-live="polite" aria-atomic="true"></div>
</body>
</html>
