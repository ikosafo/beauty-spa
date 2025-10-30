<?php
require_once 'config.php';

// Fetch services for the dropdown
$query = "SELECT id, title FROM ws_services WHERE type = 'box' ORDER BY title";
$result = $mysqli->query($query);
$services = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}
if (empty($services)) {
    $services = [
        ['id' => 1, 'title' => 'Face Massage'],
        ['id' => 2, 'title' => 'Back Massage'],
        ['id' => 3, 'title' => 'Hair Treatment'],
        ['id' => 4, 'title' => 'Skin Care']
    ];
}

// Fetch available time slots
$query = "SELECT time_range FROM ws_contact1 WHERE type = 'timeslot' ORDER BY slot_order";
$result = $mysqli->query($query);
$timeslots = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $timeslots[] = $row['time_range'];
    }
}
if (empty($timeslots)) {
    $timeslots = [
        '9:00 AM - 11:00 AM',
        '11:00 AM - 1:00 PM',
        '4:00 PM - 6:00 PM'
    ];
}

// Handle form submission
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = trim($_POST['user_name'] ?? '');
    $user_email = trim($_POST['user_email'] ?? '');
    $user_phone = trim($_POST['user_phone'] ?? '');
    $service_id = $_POST['service_id'] ?? '';
    $appointment_date = $_POST['appointment_date'] ?? '';
    $time_slot = $_POST['time_slot'] ?? '';
    $notes = trim($_POST['notes'] ?? '');
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown'; // Capture IP address

    // Basic validation
    if (empty($user_name) || empty($user_phone) || empty($service_id) || empty($appointment_date) || empty($time_slot)) {
        $error_message = 'All fields are required.';
    } elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Invalid email address.';
    } elseif (!in_array($time_slot, $timeslots)) {
        $error_message = 'Invalid time slot selected.';
    } else {
        // Sanitize inputs
        $user_name = $mysqli->real_escape_string($user_name);
        $user_email = $mysqli->real_escape_string($user_email);
        $user_phone = $mysqli->real_escape_string($user_phone);
        $service_id = (int)$service_id;
        $appointment_date = $mysqli->real_escape_string($appointment_date);
        $time_slot = $mysqli->real_escape_string($time_slot);
        $notes = $mysqli->real_escape_string($notes);
        $ip_address = $mysqli->real_escape_string($ip_address);

        // Insert into database
        $query = "INSERT INTO ws_appointments (user_name, user_email, user_phone, service_id, appointment_date, time_slot, notes, ip_address) 
                  VALUES ('$user_name', '$user_email', '$user_phone', $service_id, '$appointment_date', '$time_slot', '$notes', '$ip_address')";
        if ($mysqli->query($query)) {
            $success_message = 'Your appointment has been booked successfully! We will contact you to confirm.';
        } else {
            $error_message = 'An error occurred while booking your appointment. Please try again.';
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- Add Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    .appointment-section {
        background: url('<?php echo htmlspecialchars(URLROOT . '/cms/images/bg-image/light-spa-bg.jpg'); ?>') no-repeat center center;
        background-size: cover;
        padding: 80px 0;
        background-color: #f8f6f4; /* Fallback color */
    }
    .appointment-form-container {
        background: #ffffff;
        border-radius: 12px;
        padding: 50px;
        max-width: 700px;
        margin: 0 auto;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #f0e9e3;
    }
    .appointment-form h2 {
        font-family: 'Nimbus Roman No9 L', serif;
        color: #333;
        font-size: 32px;
        margin-bottom: 15px;
        text-align: center;
    }
    .appointment-form .form-control {
        border-radius: 6px;
        border: 1px solid #e0e0e0;
        padding: 14px;
        font-size: 16px;
        font-family: 'Poppins', sans-serif;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .appointment-form .form-control:focus {
        border-color: #ff6f61;
        box-shadow: 0 0 8px rgba(255, 111, 97, 0.2);
        outline: none;
    }
    .appointment-form .form-group {
        margin-bottom: 25px;
    }
    .appointment-form label {
        font-family: 'Poppins', sans-serif;
        font-weight: 500;
        color: #555;
        font-size: 14px;
        margin-bottom: 10px;
        display: block;
    }
    .appointment-form .ttm-btn {
        width: 100%;
        padding: 14px;
        font-size: 16px;
        font-weight: 500;
        font-family: 'Poppins', sans-serif;
        background-color: #ff6f61;
        border: none;
        border-radius: 6px;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }
    .appointment-form .ttm-btn:hover {
        background-color: #e65a50;
        transform: translateY(-2px);
    }
    .appointment-form .alert {
        margin-bottom: 25px;
        font-size: 14px;
        border-radius: 6px;
        padding: 15px;
    }
    .section-title .title-header h5 {
        color: #ff6f61;
        font-family: 'Herr Von Muellerhoff', cursive;
        font-size: 24px;
    }
    .section-title .title {
        font-family: 'Nimbus Roman No9 L', serif;
        color: #333;
    }
    .section-title .title-desc {
        font-family: 'Poppins', sans-serif;
        color: #666;
    }
    /* Flatpickr Customization */
    .flatpickr-calendar {
        font-family: 'Poppins', sans-serif;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        background: #fff;
    }
    .flatpickr-day.selected, .flatpickr-day.selected:hover {
        background: #ff6f61;
        border-color: #ff6f61;
        color: #fff;
    }
    .flatpickr-day.today {
        border-color: #ff6f61;
        color: #ff6f61;
    }
    .flatpickr-day:hover {
        background: #f8f6f4;
    }
    @media (max-width: 767px) {
        .appointment-form-container {
            padding: 30px;
        }
        .appointment-section {
            padding: 50px 0;
        }
        .appointment-form h2 {
            font-size: 28px;
        }
    }
    @media (max-width: 575px) {
        .appointment-form-container {
            padding: 20px;
        }
        .appointment-form .form-control {
            font-size: 14px;
            padding: 12px;
        }
        .appointment-form .ttm-btn {
            font-size: 14px;
            padding: 12px;
        }
    }
</style>

<!--site-main start-->
<div class="site-main">
    <!-- appointment-section -->
    <section class="ttm-row appointment-section clearfix">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title with-desc text-center clearfix">
                        <div class="title-header">
                            <h5 class="ttm-textcolor-skincolor">Book Your Treatment</h5>
                            <h2 class="title">Schedule Your Spa Experience</h2>
                        </div>
                        <div class="title-desc">Indulge in a moment of tranquility at Golden View Therapeutic Clinique & Spa. Book your appointment today.</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="appointment-form-container">
                        <div class="appointment-form">
                            <?php if ($success_message): ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                            <?php elseif ($error_message): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                            <?php endif; ?>
                            <form id="appointment-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                <div class="form-group">
                                    <label for="user_name">Full Name</label>
                                    <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Enter your full name" value="<?php echo isset($_POST['user_name']) ? htmlspecialchars($_POST['user_name']) : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="user_email">Email Address</label>
                                    <input type="email" class="form-control" id="user_email" name="user_email" placeholder="Enter your email" value="<?php echo isset($_POST['user_email']) ? htmlspecialchars($_POST['user_email']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="user_phone">Phone Number</label>
                                    <input type="tel" class="form-control" id="user_phone" name="user_phone" placeholder="Enter your phone number" value="<?php echo isset($_POST['user_phone']) ? htmlspecialchars($_POST['user_phone']) : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="service_id">Select Service</label>
                                    <select class="form-control" id="service_id" name="service_id" required>
                                        <option value="">Choose a service</option>
                                        <?php foreach ($services as $service): ?>
                                            <option value="<?php echo htmlspecialchars($service['id']); ?>" <?php echo (isset($_POST['service_id']) && $_POST['service_id'] == $service['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($service['title']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="appointment_date">Appointment Date</label>
                                    <input type="text" class="form-control flatpickr" id="appointment_date" name="appointment_date" placeholder="Select a date" value="<?php echo isset($_POST['appointment_date']) ? htmlspecialchars($_POST['appointment_date']) : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="time_slot">Time Slot</label>
                                    <select class="form-control" id="time_slot" name="time_slot" required>
                                        <option value="">Choose a time slot</option>
                                        <?php foreach ($timeslots as $timeslot): ?>
                                            <option value="<?php echo htmlspecialchars($timeslot); ?>" <?php echo (isset($_POST['time_slot']) && $_POST['time_slot'] == $timeslot) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($timeslot); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="notes">Additional Notes (Optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Any special requests or notes"><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
                                </div>
                                <button type="submit" class="ttm-btn ttm-btn-size-md ttm-btn-style-fill ttm-btn-color-skincolor">Book Appointment</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- appointment-section end -->
</div><!--site-main end-->

<!-- Add Flatpickr JS and Form Reset Script -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Initialize Flatpickr
    flatpickr("#appointment_date", {
        minDate: "today",
        dateFormat: "Y-m-d",
        disableMobile: true,
        onReady: function(selectedDates, dateStr, instance) {
            instance.element.value = dateStr || '<?php echo isset($_POST['appointment_date']) ? htmlspecialchars($_POST['appointment_date']) : ''; ?>';
        }
    });

    // Reset form after successful submission
    <?php if ($success_message): ?>
        document.getElementById('appointment-form').reset();
    <?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>