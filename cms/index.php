<?php
// cms/dashboard.php

require_once '../config.php';

$page_title = 'Admin Dashboard';

// Include header
include 'includes/header.php';
?>

<!-- Main Content -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-lg font-medium mb-4">Admin Dashboard</h3>
        <p class="mb-6 text-gray-600">Manage all CMS sections from here.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Home Sections -->
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Hero Section</h4>
                <p class="text-sm text-gray-600 mb-4">Manage the homepage hero content.</p>
                <a href="<?php echo URLROOT; ?>cms/hero.php" class="text-blue-600 hover:underline">Go to Hero</a>
            </div>
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Our Approach</h4>
                <p class="text-sm text-gray-600 mb-4">Edit the "Our Approach" section.</p>
                <a href="<?php echo URLROOT; ?>cms/approach.php" class="text-blue-600 hover:underline">Go to Approach</a>
            </div>
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Why Choose</h4>
                <p class="text-sm text-gray-600 mb-4">Update the "Why Choose Us" content.</p>
                <a href="<?php echo URLROOT; ?>cms/why_choose.php" class="text-blue-600 hover:underline">Go to Why Choose</a>
            </div>
            <!-- About Us Sections -->
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">About Section</h4>
                <p class="text-sm text-gray-600 mb-4">Manage the About page content.</p>
                <a href="<?php echo URLROOT; ?>cms/about.php" class="text-blue-600 hover:underline">Go to About</a>
            </div>
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Clients</h4>
                <p class="text-sm text-gray-600 mb-4">Manage client listings.</p>
                <a href="<?php echo URLROOT; ?>cms/clients.php" class="text-blue-600 hover:underline">Go to Clients</a>
            </div>
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Team</h4>
                <p class="text-sm text-gray-600 mb-4">Edit team member details.</p>
                <a href="<?php echo URLROOT; ?>cms/team.php" class="text-blue-600 hover:underline">Go to Team</a>
            </div>
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Our Story</h4>
                <p class="text-sm text-gray-600 mb-4">Update the "Our Story" section.</p>
                <a href="<?php echo URLROOT; ?>cms/our_story.php" class="text-blue-600 hover:underline">Go to Our Story</a>
            </div>
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Testimonials</h4>
                <p class="text-sm text-gray-600 mb-4">Manage customer testimonials.</p>
                <a href="<?php echo URLROOT; ?>cms/testimonials.php" class="text-blue-600 hover:underline">Go to Testimonials</a>
            </div>
            <!-- Contact Us Sections -->
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Contact</h4>
                <p class="text-sm text-gray-600 mb-4">Manage contact page content.</p>
                <a href="<?php echo URLROOT; ?>cms/contact.php" class="text-blue-600 hover:underline">Go to Contact</a>
            </div>
            <!-- Property Management -->
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Property</h4>
                <p class="text-sm text-gray-600 mb-4">Manage property listings.</p>
                <a href="<?php echo URLROOT; ?>cms/property.php" class="text-blue-600 hover:underline">Go to Property</a>
            </div>
            <!-- User Management -->
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">User Management</h4>
                <p class="text-sm text-gray-600 mb-4">Add or manage CMS users.</p>
                <a href="<?php echo URLROOT; ?>cms/add_user.php" class="text-blue-600 hover:underline">Go to User Management</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>