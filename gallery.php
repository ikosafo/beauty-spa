<?php
require_once 'config.php';

// Fetch Gallery Section & All Images
$query = "SELECT * FROM ws_gallery ORDER BY image_order, id";
$result = $mysqli->query($query);

$gallery_section = [];
$images = [];
$categories = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $gallery_section = $row;
        } else {
            $images[] = $row;
            if (!empty($row['category']) && !in_array($row['category'], $categories)) {
                $categories[] = $row['category'];
            }
        }
    }
}

// Default section if not in DB
if (empty($gallery_section)) {
    $gallery_section = [
        'subtitle' => 'Our Gallery',
        'title' => 'CAPTURED MOMENTS OF RELAXATION',
        'description' => 'Explore our spa, treatments, and happy clients in stunning visuals.'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gallery - Spa & Wellness</title>

    <!-- Lightbox CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simplelightbox@2.14.2/dist/simple-lightbox.min.css">

    <!-- Bootstrap 5 (optional, for spacing) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --skin: #ff6f61;
            --dark: #2c3e50;
            --light: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f9f9f9;
        }

        /* Page Title (your theme) */
        .ttm-page-title-row {
            background: var(--skin);
            color: white;
            padding: 60px 0;
            text-align: center;
        }

        .ttm-page-title-row h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }

        .breadcrumb-wrapper {
            margin-top: 10px;
            font-size: 0.9rem;
        }

        .breadcrumb-wrapper a, .breadcrumb-wrapper span {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }

        .breadcrumb-wrapper a:hover {
            color: white;
            text-decoration: underline;
        }

        .ttm-bread-sep {
            margin: 0 8px;
            color: rgba(255,255,255,0.6);
        }

        /* Filter Buttons */
        .filter-buttons {
            text-align: center;
            margin: 40px 0 30px;
        }

        .filter-btn {
            background: transparent;
            border: 2px solid #ddd;
            color: #666;
            padding: 8px 20px;
            margin: 5px;
            border-radius: 50px;
            font-size: 14px;
            transition: all 0.3s;
            cursor: pointer;
            font-weight: 500;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: var(--skin);
            border-color: var(--skin);
            color: white;
        }

        /* Gallery Grid - 4 PER ROW */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 0 15px;
            margin: 0 auto;
            max-width: 1400px;
        }

        /* Responsive */
        @media (max-width: 1199px) {
            .gallery-grid { grid-template-columns: repeat(3, 1fr); }
        }
        @media (max-width: 991px) {
            .gallery-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 575px) {
            .gallery-grid { grid-template-columns: 1fr; gap: 15px; }
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            background: white;
            transition: transform 0.3s, box-shadow 0.3s;
            opacity: 0;
            transform: translateY(20px);
        }

        .gallery-item.visible {
            opacity: 1;
            transform: translateY(0);
            transition: all 0.4s ease;
        }

        .gallery-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.18);
        }

        .gallery-item img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            display: block;
        }

        .gallery-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
            opacity: 0;
            transition: opacity 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 20px;
            color: white;
            text-align: center;
        }

        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }

        .gallery-overlay h5 {
            margin: 0 0 5px;
            font-size: 16px;
            font-weight: 600;
        }

        .gallery-overlay p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }

        .gallery-overlay i {
            font-size: 26px;
            background: var(--skin);
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 12px auto 0;
            box-shadow: 0 4px 10px rgba(255,111,97,0.4);
        }

        .load-more {
            text-align: center;
            margin: 60px 0 40px;
        }

        .load-more button {
            background: var(--skin);
            color: white;
            border: none;
            padding: 14px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 4px 15px rgba(255,111,97,0.3);
        }

        .load-more button:hover {
            background: #e55a50;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255,111,97,0.4);
        }

        .no-results {
            grid-column: 1 / -1;
            text-align: center;
            padding: 80px 20px;
            color: #888;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- Page Title -->
<div class="ttm-page-title-row">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-box text-center">
                    <div class="page-title-heading">
                        <h1>GALLERY</h1>
                    </div>
                    <div class="breadcrumb-wrapper">
                        <span><a title="Homepage" href="/">home</a></span>
                        <span class="ttm-bread-sep">&nbsp; / &nbsp;</span>
                        <span>Gallery</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Page Title -->

<!-- Filter Buttons -->
<div class="filter-buttons">
    <button class="filter-btn active" data-filter="all">All</button>
    <?php foreach ($categories as $cat): ?>
        <button class="filter-btn" data-filter="<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $cat))); ?>">
            <?php echo htmlspecialchars($cat); ?>
        </button>
    <?php endforeach; ?>
</div>

<!-- Gallery Grid -->
<div class="container" style="margin-bottom: 60px;">
    <div class="gallery-grid" id="galleryGrid">
        <?php if (!empty($images)): ?>
            <?php foreach ($images as $img): ?>
                <?php
                $catClass = !empty($img['category']) ? strtolower(str_replace(' ', '-', $img['category'])) : 'uncategorized';
                $caption = $img['caption'] ?? 'Spa Moment';
                $category = $img['category'] ?? 'General';
                ?>
                <div class="gallery-item" data-category="<?php echo $catClass; ?>">
                    <a href="<?php echo URLROOT . '/cms/' . $img['image']; ?>"
                       data-lightbox="spa-gallery"
                       data-title="<?php echo htmlspecialchars($caption); ?>"
                       class="gallery-lightbox">
                        <img src="<?php echo URLROOT . '/cms/' . $img['image']; ?>" alt="<?php echo htmlspecialchars($caption); ?>">
                        <div class="gallery-overlay">
                            <h5><?php echo htmlspecialchars($caption); ?></h5>
                            <p><?php echo htmlspecialchars($category); ?></p>
                            <i class="fa fa-camera"></i>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-results">No images in the gallery yet.</p>
        <?php endif; ?>
    </div>

    <!-- Load More Button -->
    <?php if (count($images) > 12): ?>
    <div class="load-more">
        <button id="loadMore">Load More Images</button>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simplelightbox@2.14.2/dist/simple-lightbox.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Lightbox
        if (typeof lightbox !== 'undefined') {
            lightbox.option({
                resizeDuration: 200,
                wrapAround: true,
                disableScrolling: true,
                albumLabel: "Image %1 of %2"
            });
        }

        const items = document.querySelectorAll('.gallery-item');
        const filterBtns = document.querySelectorAll('.filter-btn');
        const loadMoreBtn = document.getElementById('loadMore');

        let visibleCount = 12;

        // Initial hide + animate in
        items.forEach((item, i) => {
            if (i >= visibleCount) {
                item.style.display = 'none';
            } else {
                setTimeout(() => item.classList.add('visible'), i * 100);
            }
        });

        // Filter
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const filter = btn.getAttribute('data-filter');
                let delay = 0;

                items.forEach(item => {
                    const matches = filter === 'all' || item.getAttribute('data-category') === filter;
                    if (matches && parseInt(item.style.display !== 'none' || 0) < visibleCount) {
                        item.style.display = 'block';
                        setTimeout(() => item.classList.add('visible'), delay);
                        delay += 80;
                    } else {
                        item.classList.remove('visible');
                        setTimeout(() => item.style.display = 'none', 300);
                    }
                });
            });
        });

        // Load More
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', () => {
                const hidden = Array.from(items).filter(item => item.style.display === 'none');
                const nextBatch = hidden.slice(0, 9);

                nextBatch.forEach((item, i) => {
                    item.style.display = 'block';
                    setTimeout(() => item.classList.add('visible'), i * 100);
                });

                if (hidden.length <= 9) {
                    loadMoreBtn.style.display = 'none';
                }
            });
        }
    });
</script>

</body>
</html>