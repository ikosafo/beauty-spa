
<?php
// cms/index.php

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

        <!-- Dashboard -->
        <h4 class="text-md font-semibold mb-2">Dashboard</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Index</h4>
                <p class="text-sm text-gray-600 mb-4">Manage the dashboard overview.</p>
                <a href="<?php echo URLROOT; ?>cms/index.php" class="text-blue-600 hover:underline">Go to Index</a>
            </div>
        </div>

        <!-- Home Sections -->
        <h4 class="text-md font-semibold mb-2">Home Sections</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Hero/Slider</h4>
                <p class="text-sm text-gray-600 mb-4">Manage the homepage hero content.</p>
                <a href="<?php echo URLROOT; ?>cms/home_slider.php" class="text-blue-600 hover:underline">Go to Hero/Slider</a>
            </div>
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">About</h4>
                <p class="text-sm text-gray-600 mb-4">Edit the homepage about section.</p>
                <a href="<?php echo URLROOT; ?>cms/home_about.php" class="text-blue-600 hover:underline">Go to About</a>
            </div>
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Services</h4>
                <p class="text-sm text-gray-600 mb-4">Manage the services section.</p>
                <a href="<?php echo URLROOT; ?>cms/home_services.php" class="text-blue-600 hover:underline">Go to Services</a>
            </div>
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Testimonials</h4>
                <p class="text-sm text-gray-600 mb-4">Manage homepage testimonials.</p>
                <a href="<?php echo URLROOT; ?>cms/home_testimonials.php" class="text-blue-600 hover:underline">Go to Testimonials</a>
            </div>
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Gallery</h4>
                <p class="text-sm text-gray-600 mb-4">Manage the homepage gallery.</p>
                <a href="<?php echo URLROOT; ?>cms/home_gallery.php" class="text-blue-600 hover:underline">Go to Gallery</a>
            </div>
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Processes</h4>
                <p class="text-sm text-gray-600 mb-4">Edit the processes section.</p>
                <a href="<?php echo URLROOT; ?>cms/home_processes.php" class="text-blue-600 hover:underline">Go to Processes</a>
            </div>
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Contact</h4>
                <p class="text-sm text-gray-600 mb-4">Manage the homepage contact section.</p>
                <a href="<?php echo URLROOT; ?>cms/home_contact.php" class="text-blue-600 hover:underline">Go to Contact</a>
            </div>
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Blog</h4>
                <p class="text-sm text-gray-600 mb-4">Manage the homepage blog section.</p>
                <a href="<?php echo URLROOT; ?>cms/home_blog.php" class="text-blue-600 hover:underline">Go to Blog</a>
            </div>
        </div>

        <!-- About Us Sections -->
        <h4 class="text-md font-semibold mb-2">About Us Sections</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">About</h4>
                <p class="text-sm text-gray-600 mb-4">Manage the About page content.</p>
                <a href="<?php echo URLROOT; ?>cms/about.php" class="text-blue-600 hover:underline">Go to About</a>
            </div>
        </div>

        <!-- Contact Us Sections -->
        <h4 class="text-md font-semibold mb-2">Contact Us Sections</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Contact</h4>
                <p class="text-sm text-gray-600 mb-4">Manage contact page content.</p>
                <a href="<?php echo URLROOT; ?>cms/contact.php" class="text-blue-600 hover:underline">Go to Contact</a>
            </div>
        </div>

        <!-- Training School -->
        <h4 class="text-md font-semibold mb-2">Training School</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Training School</h4>
                <p class="text-sm text-gray-600 mb-4">Manage training school content.</p>
                <a href="<?php echo URLROOT; ?>cms/training_school.php" class="text-blue-600 hover:underline">Go to Training School</a>
            </div>
        </div>

        <!-- Shop -->
        <h4 class="text-md font-semibold mb-2">Shop</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white border rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="text-md font-semibold mb-2">Shop</h4>
                <p class="text-sm text-gray-600 mb-4">Manage shop content.</p>
                <a href="<?php echo URLROOT; ?>cms/shop.php" class="text-blue-600 hover:underline">Go to Shop</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>