<?php
// includes/header.php

if (!isset($_SESSION)) {
    session_start();
}

// Prevent caching of authenticated pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . URLROOT . 'cms/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? 'Admin Panel'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .submenu { display: none; }
        .submenu.open { display: block; }
        .submenu a.active { background-color: #3b82f6; color: white; }
    </style>
</head>
<body class="bg-gray-200 font-sans">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white p-4">
            <h1 class="text-2xl font-bold mb-6">Admin Panel</h1>
            <nav>
                <ul>
                    <li class="mb-2">
                        <a href="#" class="sidebar-link block p-2 rounded hover:bg-gray-700">Dashboard</a>
                        <ul class="submenu pl-4 text-sm">
                            <li><a href="<?php echo URLROOT; ?>cms/index.php" class="block p-2 hover:text-blue-300 <?php echo (basename($_SERVER['PHP_SELF']) === 'index.php') ? 'active' : ''; ?>">Index</a></li>
                        </ul>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="sidebar-link block p-2 rounded hover:bg-gray-700">Home Sections</a>
                        <ul class="submenu pl-4 text-sm">
                            <li><a href="<?php echo URLROOT; ?>cms/home_slider.php" class="block p-2 hover:text-blue-300 <?php echo (basename($_SERVER['PHP_SELF']) === 'home_slider.php') ? 'active' : ''; ?>">Hero/Slider</a></li>
                            <li><a href="<?php echo URLROOT; ?>cms/home_about.php" class="block p-2 hover:text-blue-300 <?php echo (basename($_SERVER['PHP_SELF']) === 'home_about.php') ? 'active' : ''; ?>">About</a></li>
                            <li><a href="<?php echo URLROOT; ?>cms/home_services.php" class="block p-2 hover:text-blue-300 <?php echo (basename($_SERVER['PHP_SELF']) === 'home_services.php') ? 'active' : ''; ?>">Services</a></li>
                            <li><a href="<?php echo URLROOT; ?>cms/home_testimonials.php" class="block p-2 hover:text-blue-300 <?php echo (basename($_SERVER['PHP_SELF']) === 'home_testimonials.php') ? 'active' : ''; ?>">Testimonials</a></li>
                            <li><a href="<?php echo URLROOT; ?>cms/home_gallery.php" class="block p-2 hover:text-blue-300 <?php echo (basename($_SERVER['PHP_SELF']) === 'home_gallery.php') ? 'active' : ''; ?>">Gallery</a></li>
                            <li><a href="<?php echo URLROOT; ?>cms/home_processes.php" class="block p-2 hover:text-blue-300 <?php echo (basename($_SERVER['PHP_SELF']) === 'home_processes.php') ? 'active' : ''; ?>">Processes</a></li>
                            <li><a href="<?php echo URLROOT; ?>cms/home_contact.php" class="block p-2 hover:text-blue-300 <?php echo (basename($_SERVER['PHP_SELF']) === 'home_contact.php') ? 'active' : ''; ?>">Contact</a></li>
                            <li><a href="<?php echo URLROOT; ?>cms/home_blog.php" class="block p-2 hover:text-blue-300 <?php echo (basename($_SERVER['PHP_SELF']) === 'home_blog.php') ? 'active' : ''; ?>">Blog</a></li>
                        </ul>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="sidebar-link block p-2 rounded hover:bg-gray-700">About Us Sections</a>
                        <ul class="submenu pl-4 text-sm">
                            <li><a href="<?php echo URLROOT; ?>cms/about.php" class="block p-2 hover:text-blue-300 <?php echo (basename($_SERVER['PHP_SELF']) === 'about.php') ? 'active' : ''; ?>">About</a></li>
                           <!--  <li><a href="<?php echo URLROOT; ?>cms/clients.php" class="block p-2 hover:text-blue-300 <?php echo (basename($_SERVER['PHP_SELF']) === 'clients.php') ? 'active' : ''; ?>">Clients</a></li>
                            <li><a href="<?php echo URLROOT; ?>cms/team.php" class="block p-2 hover:text-blue-300 <?php echo (basename($_SERVER['PHP_SELF']) === 'team.php') ? 'active' : ''; ?>">Team</a></li>
                            <li><a href="<?php echo URLROOT; ?>cms/our_story.php" class="block p-2 hover:text-blue-300 <?php echo (basename($_SERVER['PHP_SELF']) === 'our_story.php') ? 'active' : ''; ?>">Our Story</a></li>
                            <li><a href="<?php echo URLROOT; ?>cms/testimonials.php" class="block p-2 hover:text-blue-300 <?php echo (basename($_SERVER['PHP_SELF']) === 'testimonials.php') ? 'active' : ''; ?>">Testimonials</a></li> -->
                        </ul>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="sidebar-link block p-2 rounded hover:bg-gray-700">Contact Us Sections</a>
                        <ul class="submenu pl-4 text-sm">
                            <li><a href="<?php echo URLROOT; ?>cms/contact.php" class="block p-2 hover:text-blue-300 <?php echo (basename($_SERVER['PHP_SELF']) === 'contact.php') ? 'active' : ''; ?>">Contact</a></li>
                        </ul>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="sidebar-link block p-2 rounded hover:bg-gray-700">Training School</a>
                        <ul class="submenu pl-4 text-sm">
                            <li><a href="<?php echo URLROOT; ?>cms/training_school.php" class="block p-2 hover:text-blue-300 <?php echo (basename($_SERVER['PHP_SELF']) === 'training_school.php') ? 'active' : ''; ?>">Training School</a></li>
                        </ul>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="sidebar-link block p-2 rounded hover:bg-gray-700">Shop</a>
                        <ul class="submenu pl-4 text-sm">
                            <li><a href="<?php echo URLROOT; ?>cms/shop.php" class="block p-2 hover:text-blue-300 <?php echo (basename($_SERVER['PHP_SELF']) === 'shop.php') ? 'active' : ''; ?>">Shop</a></li>
                        </ul>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="sidebar-link block p-2 rounded hover:bg-gray-700">Users</a>
                        <ul class="submenu pl-4 text-sm">
                            <li><a href="<?php echo URLROOT; ?>cms/users.php" class="block p-2 hover:text-blue-300 <?php echo (basename($_SERVER['PHP_SELF']) === 'users.php') ? 'active' : ''; ?>">Users</a></li>
                        </ul>
                    </li>
                    <li class="mt-6">
                        <a href="<?php echo URLROOT; ?>cms/login.php" class="block p-2 rounded hover:bg-red-600">Logout</a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <header class="flex justify-between items-center bg-white p-4 rounded shadow mb-6">
                <h2 class="text-xl font-semibold"><?php echo htmlspecialchars($page_title ?? 'Admin Panel'); ?></h2>
                <div>
                    <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['user_email'] ?? 'Admin'); ?></span>
                </div>
            </header>