<?php
// cms/team.php

require_once '../config.php';

$page_title = 'Edit Team Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Fetch team data
$query = "SELECT * FROM ws_team WHERE id = 1";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
    error_log('Fetch query failed: ' . $mysqli->error);
}
$team = $result->fetch_assoc();
if (!$team) {
    $team = [
        'member1_name' => 'MR. JOSHUA M.K. KPAKPAH (Jnr.)',
        'member1_role' => 'DIRECTOR',
        'member1_email' => 'info@taymac.net',
        'member1_phone' => '+ 233 (0) 266-107-130',
        'member1_description1' => 'Mr. Kpakpah holds a Bachelor of Arts degree in Geography and Resource Development. He is a licensed Customs House Agent and a trained Project Management Professional, currently pursuing certification and licensing. Additionally, he possesses the NEBOSH International General Certificate in Safety and Health and is a registered IOSH-approved trainer, which enables him to deliver Managing Safely courses globally. Mr. Kpakpah is also undergoing training to become a Lead Auditor for OHSAS 18001 and ISO 14001 standards.',
        'member1_description2' => 'He has over 17 years of work experience, including four years in logistics handling, port clearing, warehousing, and supply chain management for a multinational company in Ghana.',
        'member1_description3' => 'Joshua has over six years of experience as a Local Operations and Safety Manager at Independent Commodity Supplies Limited, a UK-based firm. In this role, he was responsible for overseeing the company\'s local operations, which included material sourcing, the transportation of supplies—encompassing hazardous cargo—warehousing, safety compliance, and stock control. His expertise in these areas contributed to the efficient and safe management of operations within the organization. Joshua is currently a Director at a salt works in Ghana and serves as a Health, Safety, and Environment (HSE) Tutor, recognized for his engaging, straightforward, and interactive training sessions. He is passionate about guiding organizations to adopt preventative rather than reactionary safety measures, believing this approach enhances business performance while ensuring a safe working environment. In addition, Joshua leads Taymac, collaborating with both local and international partners to drive safety initiatives and operational excellence.',
        'member2_name' => 'MR. MAXMILLAN KOFI MADDY',
        'member2_role' => 'CONSULTANT',
        'member2_email' => 'info@taymac.net',
        'member2_phone' => '00447860271867',
        'member2_description1' => 'Maxmillan Kofi Maddy is a qualified Technician Safety and Health Practitioner who holds the NEBOSH Construction and Risk Management Certificate. In addition to this, he possesses several specialized qualifications, including Risk Assessor and COSHH (Control of Substances Hazardous to Health) Assessor. He is also certified as a City and Guilds Train the Trainer, enabling him to effectively deliver training programs in safety and health practices.',
        'member2_description2' => 'He has 10 years of experience in the Health and Safety industry, including 6 years as a Health and Safety Systems Owner for Boots Alliance Plc, a multinational manufacturing company specializing in pharmaceuticals and wellness products. Additionally, he has spent 4 years working as a Health and Safety Advisor within local government. During this period, Max has acquired extensive experience in various areas, including workplace and manual handling risk assessments, interpreting relevant legislation, conducting accident investigations, and coaching and assessing staff against Standard Operating Procedures. He has successfully implemented and maintained Health and Safety Management Systems (OHSAS 18001), developed and reviewed safety policies, conducted internal audits, and provided general Health and Safety training. As an IOSH-approved trainer, Max delivers the Managing Safely Course and holds a NEBOSH Diploma in Occupational Safety and Health, further enhancing his qualifications in the field.',
        'member2_description3' => 'Max\'s commitment to safety is unparalleled, and he is dedicated to fostering a safe and productive working environment. He served as the Health Safety Advisor for Nottinghamshire Council in the United Kingdom from 2012 to 2015. Currently, he holds the position of Senior HSSE Advisor at S&T (UK), a leading global player in the construction industry. His expertise and proactive approach to health, safety, security, and environmental management significantly contribute to organizational success and employee well-being.'
    ];
}
error_log('Fetched team data: ' . print_r($team, true));

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('Form submitted with POST method');
    error_log('$_POST: ' . print_r($_POST, true));
    error_log('Request headers: ' . print_r(getallheaders(), true));

    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
        error_log('CSRF validation failed');
    } else {
        // Use raw POST values (prepared statements handle escaping)
        $member1_name = $_POST['member1_name'] ?? '';
        $member1_role = $_POST['member1_role'] ?? '';
        $member1_email = $_POST['member1_email'] ?? '';
        $member1_phone = $_POST['member1_phone'] ?? '';
        $member1_description1 = $_POST['member1_description1'] ?? '';
        $member1_description2 = $_POST['member1_description2'] ?? '';
        $member1_description3 = $_POST['member1_description3'] ?? '';
        $member2_name = $_POST['member2_name'] ?? '';
        $member2_role = $_POST['member2_role'] ?? '';
        $member2_email = $_POST['member2_email'] ?? '';
        $member2_phone = $_POST['member2_phone'] ?? '';
        $member2_description1 = $_POST['member2_description1'] ?? '';
        $member2_description2 = $_POST['member2_description2'] ?? '';
        $member2_description3 = $_POST['member2_description3'] ?? '';

        // Validate required fields
        if (empty($member1_name) || empty($member1_role) || empty($member1_email) || empty($member1_phone) ||
            empty($member1_description1) || empty($member1_description2) || empty($member1_description3) ||
            empty($member2_name) || empty($member2_role) || empty($member2_email) || empty($member2_phone) ||
            empty($member2_description1) || empty($member2_description2) || empty($member2_description3)) {
            $_SESSION['error'] = 'All fields are required';
            error_log('Validation failed: Missing required fields');
        } else {
            // Check if row exists
            $query = "SELECT COUNT(*) AS count FROM ws_team WHERE id = 1";
            $result = $mysqli->query($query);
            if (!$result) {
                $_SESSION['error'] = 'Check query failed: ' . $mysqli->error;
                error_log('Check query failed: ' . $mysqli->error);
            }
            $row_exists = $result->fetch_assoc()['count'] > 0;
            error_log('Row exists: ' . ($row_exists ? 'Yes' : 'No'));

            if ($row_exists) {
                // Update existing row
                $query = "UPDATE ws_team SET 
                    member1_name = ?, member1_role = ?, member1_email = ?, member1_phone = ?, 
                    member1_description1 = ?, member1_description2 = ?, member1_description3 = ?,
                    member2_name = ?, member2_role = ?, member2_email = ?, member2_phone = ?, 
                    member2_description1 = ?, member2_description2 = ?, member2_description3 = ? 
                    WHERE id = 1";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                    error_log('Prepare failed: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('ssssssssssssss', 
                        $member1_name, $member1_role, $member1_email, $member1_phone, 
                        $member1_description1, $member1_description2, $member1_description3,
                        $member2_name, $member2_role, $member2_email, $member2_phone, 
                        $member2_description1, $member2_description2, $member2_description3);
                    if (!$stmt->execute()) {
                        $_SESSION['error'] = 'Update failed: ' . $stmt->error;
                        error_log('Update failed: ' . $stmt->error);
                    } else {
                        error_log('Update successful, affected rows: ' . $stmt->affected_rows);
                    }
                    $stmt->close();
                }
            } else {
                // Insert new row
                $query = "INSERT INTO ws_team (
                    id, member1_name, member1_role, member1_email, member1_phone, 
                    member1_description1, member1_description2, member1_description3,
                    member2_name, member2_role, member2_email, member2_phone, 
                    member2_description1, member2_description2, member2_description3
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                    error_log('Prepare failed: ' . $mysqli->error);
                } else {
                    $id = 1;
                    $stmt->bind_param('issssssssssssss', 
                        $id, $member1_name, $member1_role, $member1_email, $member1_phone, 
                        $member1_description1, $member1_description2, $member1_description3,
                        $member2_name, $member2_role, $member2_email, $member2_phone, 
                        $member2_description1, $member2_description2, $member2_description3);
                    if (!$stmt->execute()) {
                        $_SESSION['error'] = 'Insert failed: ' . $stmt->error;
                        error_log('Insert failed: ' . $stmt->error);
                    } else {
                        error_log('Insert successful');
                    }
                    $stmt->close();
                }
            }

            // Refresh data
            $query = "SELECT * FROM ws_team WHERE id = 1";
            $result = $mysqli->query($query);
            if (!$result) {
                $_SESSION['error'] = 'Refresh query failed: ' . $mysqli->error;
                error_log('Refresh query failed: ' . $mysqli->error);
            } else {
                $team = $result->fetch_assoc();
                error_log('Team data refreshed: ' . print_r($team, true));
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- Team Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4" id="team-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div>
                <h3 class="text-lg font-medium mb-2">Team Member 1</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="member1_name">Name</label>
                    <input type="text" name="member1_name" id="member1_name" value="<?php echo htmlspecialchars($team['member1_name']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="member1_role">Role</label>
                    <input type="text" name="member1_role" id="member1_role" value="<?php echo htmlspecialchars($team['member1_role']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="member1_email">Email</label>
                    <input type="email" name="member1_email" id="member1_email" value="<?php echo htmlspecialchars($team['member1_email']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="member1_phone">Phone</label>
                    <input type="text" name="member1_phone" id="member1_phone" value="<?php echo htmlspecialchars($team['member1_phone']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="member1_description1">Description 1</label>
                    <textarea name="member1_description1" id="member1_description1" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="5" required><?php echo htmlspecialchars($team['member1_description1']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="member1_description2">Description 2</label>
                    <textarea name="member1_description2" id="member1_description2" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="5" required><?php echo htmlspecialchars($team['member1_description2']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="member1_description3">Description 3</label>
                    <textarea name="member1_description3" id="member1_description3" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="5" required><?php echo htmlspecialchars($team['member1_description3']); ?></textarea>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Team Member 2</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="member2_name">Name</label>
                    <input type="text" name="member2_name" id="member2_name" value="<?php echo htmlspecialchars($team['member2_name']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="member2_role">Role</label>
                    <input type="text" name="member2_role" id="member2_role" value="<?php echo htmlspecialchars($team['member2_role']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="member2_email">Email</label>
                    <input type="email" name="member2_email" id="member2_email" value="<?php echo htmlspecialchars($team['member2_email']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="member2_phone">Phone</label>
                    <input type="text" name="member2_phone" id="member2_phone" value="<?php echo htmlspecialchars($team['member2_phone']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="member2_description1">Description 1</label>
                    <textarea name="member2_description1" id="member2_description1" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="5" required><?php echo htmlspecialchars($team['member2_description1']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="member2_description2">Description 2</label>
                    <textarea name="member2_description2" id="member2_description2" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="5" required><?php echo htmlspecialchars($team['member2_description2']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="member2_description3">Description 3</label>
                    <textarea name="member2_description3" id="member2_description3" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="5" required><?php echo htmlspecialchars($team['member2_description3']); ?></textarea>
                </div>
            </div>
            <div>
                <button type="submit" id="submit-button" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Changes</button>
                <a href="<?php echo URLROOT; ?>cms/index.php" class="ml-4 text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</section>

<script>
    console.log('Form initialized');

    document.getElementById('team-form').addEventListener('submit', function(e) {
        console.log('Form submit triggered');
        document.getElementById('submit-button').disabled = true;
    });
</script>

<?php
include 'includes/footer.php';
?>