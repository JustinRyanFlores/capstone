dashboard<!-- Sidebar for larger screens -->
<div class="sidebar p-3 d-none d-md-block">
    <div class="d-flex align-items-center">
        <a href="dashboard.php">
            <img src="/capstone/src/assets/kayanlog-logo-removebg-preview.png" alt="Logo" style="width: 80px; height: 80px; margin-right: 10px;">
        </a>
        <h5 class="mb-0">Barangay Kay-Anlog</h5>
    </div>
    <ul class="nav flex-column mt-5">
        <li class="nav-item">
            <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex" data-bs-toggle="collapse" href="#residentRecords" aria-expanded="<?php echo (basename($_SERVER['PHP_SELF']) == 'resident_list.php' || basename($_SERVER['PHP_SELF']) == 'add_records.php') ? 'true' : 'false'; ?>" aria-controls="residentRecords">
                <i class="fas fa-users me-2"></i>Resident Records
                <i class="fas fa-chevron-down" style="margin-left: auto;"></i>
            </a>
            <div class="collapse <?php echo (basename($_SERVER['PHP_SELF']) == 'resident_list.php' || basename($_SERVER['PHP_SELF']) == 'add_records.php') ? 'show' : ''; ?>" id="residentRecords">
                <ul class="nav flex-column ms-3 mt-2 white-box">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'resident_list.php') ? 'active' : ''; ?>" href="resident_list.php">
                            <i class="fas fa-list me-2"></i>Resident List
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'add_records.php') ? 'active' : ''; ?>" href="add_records.php">
                            <i class="fas fa-plus me-2"></i>Add Records
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="report.php"><i class="fas fa-file-alt me-2"></i> Reports</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="blotter_records.php"><i class="fas fa-book me-2"></i> Blotter Records</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="faqs.php"><i class="fas fa-question-circle me-2"></i> F.A.Qs</a>
        </li>
        <li class="nav-item">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
                <a class="nav-link" href="admin_panel.php"><i class="fas fa-user-cog me-2"></i> Admin Panel</a>
            <?php endif; ?>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/capstone/website/login/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Sign out</a>
        </li>
    </ul>
</div>

<!-- Top Navbar for smaller screens -->
<nav class="navbar navbar-expand-md bg-primary d-md-none fixed-top">
    <a class="navbar-brand" href="dashboard.php">
        <img src="/capstone/src/assets/kayanlog-logo-removebg-preview.png" alt="Logo" style="width: 40px; height: 40px;">
        Barangay Kay-Anlog
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php" style="padding-left: 15px;"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding-left: 15px;">
                    <i class="fas fa-users me-2"></i> Resident Records
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="resident_list.php"><i class="fas fa-list me-2"></i> Resident List</a></li>
                    <li><a class="dropdown-item" href="add_records.php"><i class="fas fa-plus me-2"></i> Add Records</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="report.php" style="padding-left: 15px;"><i class="fas fa-file-alt me-2"></i> Reports</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="blotter_records.php" style="padding-left: 15px;"><i class="fas fa-book me-2"></i> Blotter Records</a>
            </li>
            <li class="nav-item">
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin'): ?>
                    <a class="nav-link" href="admin_panel.php"><i class="fas fa-user-cog me-2"></i> Admin Panel</a>
                <?php endif; ?>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="faqs.php" style="padding-left: 15px;"><i class="fas fa-question-circle me-2"></i> F.A.Qs</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/capstone/website/login/logout.php" style="padding-left: 15px;"><i class="fas fa-sign-out-alt me-2"></i> Sign out</a>
            </li>
        </ul>
    </div>
</nav>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

<script>
    const residentRecordsLink = document.querySelector('[href="#residentRecords"]');
    residentRecordsLink.addEventListener('click', function() {
        const icon = residentRecordsLink.querySelector('i:last-child');
        if ($(residentRecordsLink).attr('aria-expanded') === 'true') {
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    });

    // Close other dropdowns when another is clicked
    $('.nav-link[data-bs-toggle="collapse"]').on('click', function() {
        const targetCollapse = $($(this).attr('href'));
        if (targetCollapse.hasClass('show')) {
            targetCollapse.collapse('hide');
        } else {
            $('.collapse').collapse('hide');
            targetCollapse.collapse('show');
        }
    });

    // Toggle white-box class when collapse is shown
    $('#residentRecords').on('show.bs.collapse', function() {
        $(this).siblings('a.nav-link').addClass('active');
    }).on('hide.bs.collapse', function() {
        $(this).siblings('a.nav-link').removeClass('active');
    });
</script>

<!-- JavaScript to prevent navbar collapse when clicking inside the dropdown -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var dropdownLinks = document.querySelectorAll('.dropdown-menu .dropdown-item');

        dropdownLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                var parent = this.closest('.dropdown');
                if (parent) {
                    e.stopPropagation();
                    var toggleButton = parent.querySelector('[data-bs-toggle="dropdown"]');
                    if (toggleButton) {
                        toggleButton.click(); // Close the dropdown
                        toggleButton.click(); // Re-open the dropdown
                    }
                }
            });
        });
    });
</script>