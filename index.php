<?php
require_once 'config.php';

// Contact Data
$query = "SELECT `logo`, `address`, `working_hours`, facebook, x, linkedin, instagram FROM ws_contact WHERE id = 1";
$result = $mysqli->query($query);
$contact_data = $result && $result->num_rows > 0 ? $result->fetch_assoc() : [
    'logo' => 'images/logo.png',
    'address' => '24 Tech Roqad st Ny 10023',
    'working_hours' => 'Mon-Sat: 9am to 6pm',
    'facebook' => '',
    'x' => '',
    'linkedin' => '',
    'instagram' => ''
];
// Ensure social media and working hours fields are not NULL
$contact_data['facebook'] = $contact_data['facebook'] ?? '';
$contact_data['x'] = $contact_data['x'] ?? '';
$contact_data['linkedin'] = $contact_data['linkedin'] ?? '';
$contact_data['instagram'] = $contact_data['instagram'] ?? '';
$contact_data['working_hours'] = $contact_data['working_hours'] ?? 'Mon-Sat: 9am to 6pm';
$logo_path = $contact_data['logo'];

// Slides
$query = "SELECT * FROM ws_slider WHERE id IN (1, 2) ORDER BY id";
$result = $mysqli->query($query);
$slides = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $slides[$row['id']] = $row;
    }
}
if (empty($slides)) {
    $slides = [
        1 => [
            'slide_key' => 'rs-3',
            'background_media' => 'images/slides/slider-mainbg-001.jpg',
            'subtitle' => 'Best Place for',
            'heading1' => 'THE BEST TIME',
            'heading2' => 'TO RELAX WITH SYLIN',
            'description' => 'Professional Beauty Center Since 1919.',
            'button_text' => 'Get An Appointment!',
            'button_url' => '#'
        ],
        2 => [
            'slide_key' => 'rs-4',
            'background_media' => 'images/slides/slider-mainbg-002.jpg',
            'subtitle' => 'Best Place for',
            'heading1' => 'THE BEST TIME',
            'heading2' => 'TO RELAX WITH SYLIN',
            'description' => 'Professional Beauty Center Since 1919.',
            'button_text' => 'Watch Video',
            'button_url' => 'https://youtu.be/7e90gBu4pas'
        ]
    ];
}

// About
$query = "SELECT * FROM ws_about WHERE id = 1";
$result = $mysqli->query($query);
$about = $result && $result->num_rows > 0 ? $result->fetch_assoc() : [
    'image' => 'images/single-img-one.jpg',
    'subtitle' => 'Wellness',
    'title' => 'WELCOME TO HOME OF RELAXE & RESPITE.',
    'description' => 'There’s nothing more luxurious and relaxing than a trip to the spa & Salon. We offer a wide variety of body spa therapies to help you heal your body naturally. Get relaxed from stressed & hectic schedule.',
    'paragraph' => 'Everybody is looking for places where to relax and get more energy. In our wellness center silence, energy, beauty and vitality meet. The treatments we offer will refresh both your body and soul. We’ll be glad to welcome you and recommend our facilities and services.',
    'box1_title' => 'Massage',
    'box1_icon' => 'flaticon-spa',
    'box2_title' => 'Therapies',
    'box2_icon' => 'flaticon-wellness',
    'box3_title' => 'Relaxation',
    'box3_icon' => 'flaticon-hammam',
    'box4_title' => 'Facial',
    'box4_icon' => 'flaticon-person-silhouette-in-sauna'
];

// Services
$query = "SELECT * FROM ws_services WHERE id IN (1, 2, 3, 4, 5) ORDER BY id";
$result = $mysqli->query($query);
$section = [];
$boxes = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $section = $row;
        } else {
            $boxes[$row['box_order']] = $row;
        }
    }
}
if (empty($section)) {
    $section = [
        'subtitle' => 'Welcome',
        'title' => 'EXPLORE Our Services',
        'description' => 'Being the pampering connoisseurs that we are, we have discovered some wonderful spa services…To relax mind & body!'
    ];
}
if (empty($boxes)) {
    $boxes = [
        1 => [
            'image' => 'images/services/01.jpg',
            'title' => 'Face Massage',
            'description' => 'To reverse the ageing effect from the skin, try our face hydration treatment to get a youthful glow',
            'icon' => 'flaticon-herbal',
            'link' => 'services-details.html',
            'box_order' => 1
        ],
        2 => [
            'image' => 'images/services/02.jpg',
            'title' => 'Back Massage',
            'description' => 'To reverse the ageing effect from the skin, try our face hydration treatment to get a youthful glow',
            'icon' => 'flaticon-spa-1',
            'link' => 'services-details.html',
            'box_order' => 2
        ],
        3 => [
            'image' => 'images/services/03.jpg',
            'title' => 'Hair Treatment',
            'description' => 'We offer the professional hair care for all hair types discover the best hair treatments, for healthy.',
            'icon' => 'flaticon-spa',
            'link' => 'services-details.html',
            'box_order' => 3
        ],
        4 => [
            'image' => 'images/services/04.jpg',
            'title' => 'Skin Care',
            'description' => 'you’ll get therapies like panchakarma treatment which can help you for living a healthy life.',
            'icon' => 'flaticon-cupping',
            'link' => 'services-details.html',
            'box_order' => 4
        ]
    ];
}

// Testimonials & Facts
$query = "SELECT * FROM ws_testimonials WHERE id IN (1, 2, 3, 4, 5, 6, 7, 8) ORDER BY id";
$result = $mysqli->query($query);
$testimonial_section = [];
$testimonials = [];
$facts = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $testimonial_section = $row;
        } elseif ($row['type'] === 'testimonial') {
            $testimonials[$row['item_order']] = $row;
        } else {
            $facts[$row['item_order']] = $row;
        }
    }
}
if (empty($testimonial_section)) {
    $testimonial_section = [
        'subtitle' => 'Testimonials',
        'title' => 'WHAT OUR CLIENTS SAYING'
    ];
}
if (empty($testimonials)) {
    $testimonials = [
        1 => [
            'quote' => 'I received a lymphatic massage, Aba was fantastic. The spa is clean, upscale decor and overall ambience are delightful. I will patronize Ecobel for a variety of services they offer.',
            'name' => 'Len Rosy Jacbos',
            'label' => 'Face make-up',
            'item_order' => 1
        ],
        2 => [
            'quote' => 'I received a lymphatic massage, Aba was fantastic. The spa is clean, upscale decor and overall ambience are delightful. I will patronize Ecobel for a variety of services they offer.',
            'name' => 'Len Rosy Jacbos',
            'label' => 'Face make-up',
            'item_order' => 2
        ],
        3 => [
            'quote' => 'I received a lymphatic massage, Aba was fantastic. The spa is clean, upscale decor and overall ambience are delightful. I will patronize Ecobel for a variety of services they offer.',
            'name' => 'Scorlett Johanson',
            'label' => 'Face make-up',
            'item_order' => 3
        ]
    ];
}
if (empty($facts)) {
    $facts = [
        1 => [
            'title' => 'Cosmetics',
            'icon' => 'flaticon-spa',
            'number' => 5684,
            'item_order' => 1
        ],
        2 => [
            'title' => 'Subscriber',
            'icon' => 'flaticon-wellness',
            'number' => 7458,
            'item_order' => 2
        ],
        3 => [
            'title' => 'Total Branches',
            'icon' => 'flaticon-hair-1',
            'number' => 7855,
            'item_order' => 3
        ],
        4 => [
            'title' => 'Campigns Done',
            'icon' => 'flaticon-herbal',
            'number' => 1458,
            'item_order' => 4
        ]
    ];
}

// Gallery
$query = "SELECT * FROM ws_gallery WHERE id IN (1, 2, 3, 4, 5) ORDER BY id";
$result = $mysqli->query($query);
$gallery_section = [];
$images = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $gallery_section = $row;
        } else {
            $images[$row['image_order']] = $row;
        }
    }
}
if (empty($gallery_section)) {
    $gallery_section = [
        'subtitle' => 'Gallery',
        'title' => 'AN INCREDIBLE SPA EXPERIENCE',
        'description' => 'Stunning treatment rooms are endowed with lush open air gardens soak bathtub and cushioned daybed built for two.'
    ];
}
if (empty($images)) {
    $images = [
        1 => ['image' => 'images/gallery/01.jpg', 'image_order' => 1],
        2 => ['image' => 'images/gallery/02.jpg', 'image_order' => 2],
        3 => ['image' => 'images/gallery/03.jpg', 'image_order' => 3],
        4 => ['image' => 'images/gallery/04.jpg', 'image_order' => 4]
    ];
}

// Processes
$query = "SELECT * FROM ws_processes WHERE id IN (1, 2, 3, 4) ORDER BY id";
$result = $mysqli->query($query);
$processes_section = [];
$processes = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $processes_section = $row;
        } else {
            $processes[$row['process_order']] = $row;
        }
    }
}
if (empty($processes_section)) {
    $processes_section = [
        'subtitle' => 'Welcome',
        'title' => 'LOOK WELNESS PROCESSES',
        'description' => 'Lorem Ipsum is simply dummy text of the printing and tdustry. Lorem Ipsum has been the industry’s standard dualley.'
    ];
}
if (empty($processes)) {
    $processes = [
        1 => [
            'icon' => 'flaticon-hammam',
            'title' => 'Get A Free Quotes',
            'description' => 'Get full details about Spa treatments & other amenities here!',
            'process_order' => 1
        ],
        2 => [
            'icon' => 'flaticon-massage-spa-body-treatment',
            'title' => 'Get A Free Quotes',
            'description' => 'Book your appointment at your suitable schedule & get notified!',
            'process_order' => 2
        ],
        3 => [
            'icon' => 'flaticon-hair-cut',
            'title' => 'Get A Free Quotes',
            'description' => 'We always appreciate your valuable feedback over our service!',
            'process_order' => 3
        ]
    ];
}

// Contact Slots
$query = "SELECT * FROM ws_contact1 WHERE id IN (1, 2, 3, 4) ORDER BY id";
$result = $mysqli->query($query);
$contact1_section = [];
$timeslots = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $contact1_section = $row;
        } else {
            $timeslots[$row['slot_order']] = $row;
        }
    }
}
if (empty($contact1_section)) {
    $contact1_section = [
        'image' => 'images/bg-image/col-bgimage-4.jpg',
        'subtitle' => 'Contactus',
        'title' => 'GET A FREE QUOTES'
    ];
}
if (empty($timeslots)) {
    $timeslots = [
        1 => ['time_range' => '9:00 am – 11:00 am', 'spaces_available' => '10 spaces available', 'slot_order' => 1],
        2 => ['time_range' => '11:00 am – 1:00 am', 'spaces_available' => '10 spaces available', 'slot_order' => 2],
        3 => ['time_range' => '4:00 am – 6:00 am', 'spaces_available' => '10 spaces available', 'slot_order' => 3]
    ];
}

// Blog
$query = "SELECT * FROM ws_blog WHERE id IN (1, 2, 3, 4, 5, 6, 7, 8, 9, 10) ORDER BY id";
$result = $mysqli->query($query);
$blog_section = [];
$posts = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $blog_section = $row;
        } else {
            $posts[$row['post_order']] = $row;
        }
    }
}
if (empty($blog_section)) {
    $blog_section = [
        'subtitle' => 'Welcome',
        'title' => 'DEFINITIVE SPA COLLECTION',
        'description' => 'You can choose the various type of massage you want from kinds of massages our team has expertise in!'
    ];
}
if (empty($posts)) {
    $posts = [
        1 => [
            'image' => 'images/blog/01.jpg',
            'post_date' => 'January 13, 2020',
            'category' => 'Bath & Body',
            'title' => 'Maintaining Health and Beauty Through Spas',
            'link' => 'blog-single.html',
            'post_order' => 1
        ],
        2 => [
            'image' => 'images/blog/02.jpg',
            'post_date' => 'January 13, 2020',
            'category' => 'Make-up',
            'title' => 'A Relaxation of the Senses with Their Help',
            'link' => 'blog-single.html',
            'post_order' => 2
        ],
        3 => [
            'image' => 'images/blog/03.jpg',
            'post_date' => 'January 13, 2020',
            'category' => 'Natural',
            'title' => 'Differences Between a Sauna and a Turkish Bath',
            'link' => 'blog-single.html',
            'post_order' => 3
        ],
        4 => [
            'image' => 'images/blog/04.jpg',
            'post_date' => 'January 13, 2020',
            'category' => 'Hair care',
            'title' => 'Do Massages Have Real Health Benefits?',
            'link' => 'blog-single.html',
            'post_order' => 4
        ],
        5 => [
            'image' => 'images/blog/05.jpg',
            'post_date' => 'January 13, 2020',
            'category' => 'Bath & Body',
            'title' => 'Massage Therapy for Anxiety and Stress',
            'link' => 'blog-single.html',
            'post_order' => 5
        ],
        6 => [
            'image' => 'images/blog/06.jpg',
            'post_date' => 'January 4, 2020',
            'category' => 'Bath & Body',
            'title' => 'Main Responsibilities in Beauty Industry',
            'link' => 'blog-single.html',
            'post_order' => 6
        ],
        7 => [
            'image' => 'images/blog/07.jpg',
            'post_date' => 'January 4, 2020',
            'category' => 'Make-up',
            'title' => 'Turkish Bathroom Benefits for Your Health',
            'link' => 'blog-single.html',
            'post_order' => 7
        ],
        8 => [
            'image' => 'images/blog/08.jpg',
            'post_date' => 'January 4, 2020',
            'category' => 'Hair care',
            'title' => 'How To Straighten Hair To Using Home Remedies.',
            'link' => 'blog-single.html',
            'post_order' => 8
        ],
        9 => [
            'image' => 'images/blog/09.jpg',
            'post_date' => 'January 4, 2020',
            'category' => 'Special Product',
            'title' => 'Effects of Indian Head Massage and Benefits',
            'link' => 'blog-single.html',
            'post_order' => 9
        ]
    ];
}
?>

<style>
    /* Custom styles for the gallery section */
    .gallery-section .featured-imagebox-portfolio {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    .gallery-section .featured-imagebox-portfolio:hover {
        transform: scale(1.05);
    }
    .gallery-section .featured-imagebox-portfolio img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        display: block;
    }
    .gallery-section .featured-content-portfolio {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.3);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .gallery-section .featured-imagebox-portfolio:hover .featured-content-portfolio {
        opacity: 1;
    }
    .gallery-section .featured-content-portfolio a {
        font-size: 24px;
        color: #fff;
        background: #ff6f61;
        padding: 10px;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .gallery-section .row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }
    .gallery-section .col-md-4 {
        flex: 1 1 calc(33.333% - 20px);
        max-width: calc(33.333% - 20px);
    }
    @media (max-width: 767px) {
        .gallery-section .col-md-4 {
            flex: 1 1 calc(50% - 20px);
            max-width: calc(50% - 20px);
        }
    }
    @media (max-width: 575px) {
        .gallery-section .col-md-4 {
            flex: 1 1 100%;
            max-width: 100%;
        }
    }
</style>

<?php include 'includes/header.php'; ?>

<!-- START homebanner -->
<rs-module-wrap id="rev_slider_2_1_wrapper" data-source="gallery">
    <rs-module id="rev_slider_2_1" data-version="6.1.3" class="rev_slider_1_1_height">
        <rs-slides>
            <!-- Slide 1 -->
            <rs-slide data-key="<?php echo htmlspecialchars($slides[1]['slide_key']); ?>" data-title="Slide" data-thumb="<?php echo htmlspecialchars(URLROOT . '/cms/' . $slides[1]['background_media']); ?>" data-anim="ei:d;eo:d;s:d;r:0;t:slidingoverlayhorizontal;sl:d;">
                <img src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $slides[1]['background_media']); ?>" title="home-mainslider-bg001" width="1920" height="790" class="rev-slidebg" data-no-retina>
                <!-- Dark Overlay Layer -->
                <rs-layer
                    id="slider-2-slide-3-layer-overlay"
                    data-type="shape"
                    data-rsp_ch="on"
                    data-xy="x:c;y:c;"
                    data-text="w:normal;s:20,20,12,7;l:0,0,15,9;"
                    data-dim="w:100%;h:100%;"
                    data-frame_0="sX:0.8;sY:0.8;"
                    data-frame_1="st:100;sp:500;sR:100;"
                    data-frame_999="o:0;st:w;sR:8400;"
                    style="z-index:6;background-color:rgba(0,0,0,0.5);"
                ></rs-layer>
                <rs-layer
                    id="slider-2-slide-3-layer-0"
                    data-type="text"
                    data-rsp_ch="on"
                    data-xy="x:l,l,c,c;xo:55px,55px,0,0;y:m;yo:80px,80px,-103px,-62px;"
                    data-text="w:normal;s:60,60,40,30;l:100,100,62,38;"
                    data-frame_0="sX:0.9;sY:0.9;"
                    data-frame_1="st:160;sp:500;sR:160;"
                    data-frame_999="o:0;st:w;sR:8340;"
                    style="z-index:9;font-family:Herr Von Muellerhoff;"
                ><span class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($slides[1]['subtitle']); ?></span>
                </rs-layer>
                <rs-layer
                    id="slider-2-slide-3-layer-1"
                    data-type="shape"
                    data-rsp_ch="on"
                    data-xy="xo:55px,55px,-490px,-302px;y:m;yo:272px,272px,-60px,-37px;"
                    data-text="w:normal;s:20,20,12,7;l:0,0,15,9;"
                    data-dim="w:30px,30px,18px,11px;h:1px;"
                    data-vbility="t,t,f,f"
                    data-frame_0="sX:0.8;sY:0.8;"
                    data-frame_1="e:Power4.easeOut;st:50;sp:500;sR:50;"
                    data-frame_999="o:0;st:w;sR:8450;"
                    style="z-index:8;background-color:#ffffff;font-family:Roboto;"
                ></rs-layer>
                <rs-layer
                    id="slider-2-slide-3-layer-2"
                    data-type="text"
                    data-rsp_ch="on"
                    data-xy="x:l,l,c,c;xo:55px,55px,0,0;y:m;yo:212px,212px,5px,21px;"
                    data-text="w:normal;s:60,60,47,33;l:90,90,56,34;"
                    data-frame_0="sX:0.8;sY:0.8;"
                    data-frame_1="e:Power4.easeOut;st:750;sp:1000;sR:750;"
                    data-frame_999="o:0;st:w;sR:7250;"
                    style="z-index:11;font-family:Nimbus Roman No9 L;"
                ><span class="slider-heding-title-font"><?php echo htmlspecialchars($slides[1]['heading2']); ?></span>
                </rs-layer>
                <rs-layer
                    id="slider-2-slide-3-layer-3"
                    data-type="text"
                    data-rsp_ch="on"
                    data-xy="x:l,l,c,c;xo:55px,55px,0,0;y:m;yo:146px,146px,-49px,-20px;"
                    data-text="w:normal;s:60,60,47,33;l:90,90,56,34;"
                    data-frame_0="sX:0.8;sY:0.8;"
                    data-frame_1="e:Power4.easeOut;st:510;sp:1000;sR:510;"
                    data-frame_999="o:0;st:w;sR:7490;"
                    style="z-index:10;font-family:Nimbus Roman No9 L;"
                ><span class="slider-heding-title-font"><?php echo htmlspecialchars($slides[1]['heading1']); ?></span>
                </rs-layer>
                <rs-layer
                    id="slider-2-slide-3-layer-4"
                    data-type="text"
                    data-rsp_ch="on"
                    data-xy="x:l,l,c,c;xo:98px,98px,0,430px;y:m;yo:275px,275px,50px,32px;"
                    data-text="w:normal;s:18,18,16,9;l:28,28,17,10;"
                    data-vbility="t,t,t,f"
                    data-frame_0="sX:0.8;sY:0.8;"
                    data-frame_1="e:Power4.easeOut;st:1020;sp:1000;sR:1020;"
                    data-frame_999="o:0;st:w;sR:6980;"
                    style="z-index:12;font-family:Poppins;font-style:italic;"
                ><?php echo htmlspecialchars($slides[1]['description']); ?>
                </rs-layer>
                <a
                    id="slider-2-slide-3-layer-7"
                    class="rs-layer ttm-btn ttm-btn-style-border ttm-btn-color-skincolor"
                    href="<?php echo htmlspecialchars($slides[1]['button_url']); ?>" target="_self" rel="nofollow"
                    data-type="text"
                    data-color="#fc84b4"
                    data-rsp_ch="on"
                    data-xy="x:r,r,c,c;xo:40px,40px,0,0;y:m;yo:163px,163px,105px,73px;"
                    data-text="w:normal;s:15,15,14,12;l:20,20,12,10;fw:500;"
                    data-padding="t:15,15,12,10;r:35,35,25,20;b:15,15,12,10;l:35,35,25,20;"
                    data-border="bos:solid;boc:#fc84b4;bow:1px,1px,1px,1px;"
                    data-frame_0="sX:0.8;sY:0.8;"
                    data-frame_1="e:Power4.easeOut;st:1100;sp:600;sR:1100;"
                    data-frame_999="o:0;st:w;sR:7300;"
                    data-frame_hover="c:#fff;bgc:#fc84b4;boc:#fc84b4;bos:solid;bow:1px,1px,1px,1px;"
                    style="z-index:13;font-family:Poppins;margin-right: 15px;"
                ><?php echo htmlspecialchars($slides[1]['button_text']); ?>
                </a>
            </rs-slide>
            <!-- Slide 2 -->
            <rs-slide data-key="<?php echo htmlspecialchars($slides[2]['slide_key']); ?>" data-title="Slide" data-thumb="<?php echo htmlspecialchars(URLROOT . '/cms/' . $slides[2]['background_media']); ?>" data-anim="ei:d;eo:d;s:d;r:0;t:slidingoverlayhorizontal;sl:d;">
                <img src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $slides[2]['background_media']); ?>" title="home-mainslider-bg002" width="1920" height="790" class="rev-slidebg" data-no-retina>
                <!-- Dark Overlay Layer -->
                <rs-layer
                    id="slider-2-slide-4-layer-overlay"
                    data-type="shape"
                    data-rsp_ch="on"
                    data-xy="x:c;y:c;"
                    data-text="w:normal;s:20,20,12,7;l:0,0,15,9;"
                    data-dim="w:100%;h:100%;"
                    data-frame_0="sX:0.8;sY:0.8;"
                    data-frame_1="st:100;sp:500;sR:100;"
                    data-frame_999="o:0;st:w;sR:8400;"
                    style="z-index:6;background-color:rgba(0,0,0,0.5);"
                ></rs-layer>
                <rs-layer
                    id="slider-2-slide-4-layer-0"
                    data-type="text"
                    data-rsp_ch="on"
                    data-xy="x:l,l,c,c;xo:55px,55px,0,0;y:m;yo:90px,90px,-108px,-69px;"
                    data-text="w:normal;s:60,60,40,30;l:100,100,62,38;"
                    data-frame_0="sX:0.9;sY:0.9;"
                    data-frame_1="st:160;sp:500;sR:160;"
                    data-frame_999="o:0;st:w;sR:8340;"
                    style="z-index:10;font-family:Herr Von Muellerhoff;"
                ><span class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($slides[2]['subtitle']); ?></span>
                </rs-layer>
                <rs-layer
                    id="slider-2-slide-4-layer-1"
                    data-type="shape"
                    data-rsp_ch="on"
                    data-xy="xo:55px,55px,-490px,-302px;y:m;yo:285px,285px,-60px,-37px;"
                    data-text="w:normal;s:20,20,12,7;l:0,0,15,9;"
                    data-dim="w:30px,30px,18px,11px;h:1px;"
                    data-vbility="t,t,f,f"
                    data-frame_0="sX:0.8;sY:0.8;"
                    data-frame_1="e:Power4.easeOut;st:1010;sp:500;sR:1010;"
                    data-frame_999="o:0;st:w;sR:7490;"
                    style="z-index:8;background-color:#ffffff;font-family:Roboto;"
                ></rs-layer>
                <rs-layer
                    id="slider-2-slide-4-layer-2"
                    data-type="text"
                    data-rsp_ch="on"
                    data-xy="x:l,l,c,c;xo:55px,55px,0,0;y:m;yo:222px,222px,0,14px;"
                    data-text="w:normal;s:60,60,47,33;l:90,90,56,34;"
                    data-frame_0="sX:0.8;sY:0.8;"
                    data-frame_1="e:Power4.easeOut;st:750;sp:1000;sR:750;"
                    data-frame_999="o:0;st:w;sR:7250;"
                    style="z-index:12;font-family:Nimbus Roman No9 L;"
                ><span class="slider-heding-title-font"><?php echo htmlspecialchars($slides[2]['heading2']); ?></span>
                </rs-layer>
                <rs-layer
                    id="slider-2-slide-4-layer-3"
                    data-type="text"
                    data-rsp_ch="on"
                    data-xy="x:l,l,c,c;xo:55px,55px,0,0;y:m;yo:156px,156px,-54px,-27px;"
                    data-text="w:normal;s:60,60,47,33;l:90,90,56,34;"
                    data-frame_0="sX:0.8;sY:0.8;"
                    data-frame_1="e:Power4.easeOut;st:510;sp:1000;sR:510;"
                    data-frame_999="o:0;st:w;sR:7490;"
                    style="z-index:11;font-family:Nimbus Roman No9 L;"
                ><span class="slider-heding-title-font"><?php echo htmlspecialchars($slides[2]['heading1']); ?></span>
                </rs-layer>
                <rs-layer
                    id="slider-2-slide-4-layer-4"
                    data-type="text"
                    data-rsp_ch="on"
                    data-xy="x:l,l,c,c;xo:98px,98px,0,430px;y:m;yo:285px,285px,45px,32px;"
                    data-text="w:normal;s:18,18,16,9;l:28,28,17,10;"
                    data-vbility="t,t,t,f"
                    data-frame_0="sX:0.8;sY:0.8;"
                    data-frame_1="e:Power4.easeOut;st:1020;sp:1000;sR:1020;"
                    data-frame_999="o:0;st:w;sR:6980;"
                    style="z-index:12;font-family:Poppins;font-style:italic;"
                ><?php echo htmlspecialchars($slides[2]['description']); ?>
                </rs-layer>
                <a
                    id="slider-2-slide-4-layer-7"
                    class="rs-layer ttm-btn ttm-btn-style-border ttm-btn-color-skincolor"
                    href="<?php echo htmlspecialchars($slides[2]['button_url']); ?>" target="_blank" rel="nofollow"
                    data-type="text"
                    data-color="#fc84b4"
                    data-rsp_ch="on"
                    data-xy="x:r,r,c,c;xo:40px,40px,0,0;y:m;yo:163px,163px,105px,73px;"
                    data-text="w:normal;s:15,15,14,12;l:20,20,12,10;fw:500;"
                    data-padding="t:15,15,12,10;r:35,35,25,20;b:15,15,12,10;l:35,35,25,20;"
                    data-border="bos:solid;boc:#fc84b4;bow:1px,1px,1px,1px;"
                    data-frame_0="sX:0.8;sY:0.8;"
                    data-frame_1="e:Power4.easeOut;st:1100;sp:600;sR:1100;"
                    data-frame_999="o:0;st:w;sR:7300;"
                    data-frame_hover="c:#fff;bgc:#fc84b4;boc:#fc84b4;bos:solid;bow:1px,1px,1px,1px;"
                    style="z-index:13;font-family:Poppins;margin-right: 15px;"
                ><?php echo htmlspecialchars($slides[2]['button_text']); ?>
                </a>
            </rs-slide>
        </rs-slides>
        <rs-static-layers></rs-static-layers>
        <rs-progress class="rs-bottom" style="visibility: hidden !important;"></rs-progress>
    </rs-module>
</rs-module-wrap>
<!-- END homebanner -->

<!--site-main start-->
<div class="site-main">
    <!-- about us-section -->
    <section class="ttm-row aboutus-section clearfix">
        <div class="container">
            <div class="row">
                <div class="col-md-6 res-767-center">
                    <img src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $about['image']); ?>" class="img-fluid" alt="about-image">
                </div>
                <div class="col-md-6 res-767-pt-30">
                    <div class="spacing-1">
                        <div class="section-title with-desc clearfix res-1199-mb-0">
                            <div class="title-header">
                                <h5 class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($about['subtitle']); ?></h5>
                                <h2 class="title"><?php echo htmlspecialchars($about['title']); ?></h2>
                            </div>
                            <div class="title-desc"><?php echo htmlspecialchars($about['description']); ?></div>
                        </div>
                        <div class="row justify-content-space-between">
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <div class="col-lg-3 col-md-6 col-sm-3 col-6 featured-icon-box style1 text-center res-575-w-50">
                                    <div class="featured-icon">
                                        <div class="ttm-icon ttm-icon_element-size-lg">
                                            <i class="<?php echo htmlspecialchars($about["box{$i}_icon"]); ?> ttm-textcolor-skincolor"></i>
                                        </div>
                                    </div>
                                    <div class="featured-content">
                                        <div class="featured-title">
                                            <h5><?php echo htmlspecialchars($about["box{$i}_title"]); ?></h5>
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                        <p class="mt-40 res-1199-mt-20 res-1199-mb-0"><?php echo htmlspecialchars($about['paragraph']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- about us-section end -->

    <!-- service-section -->
    <section class="ttm-row service-section bg-img1 clearfix">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-7 col-sm-9 m-auto">
                    <div class="section-title with-desc text-center clearfix">
                        <div class="title-header">
                            <h5 class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($section['subtitle']); ?></h5>
                            <h2 class="title"><?php echo htmlspecialchars($section['title']); ?></h2>
                        </div>
                        <div class="title-desc"><?php echo htmlspecialchars($section['description']); ?></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="services-slide owl-carousel" data-item="4" data-nav="false" data-dots="false" data-auto="false">
                    <?php foreach ($boxes as $box): ?>
                        <div class="featured-imagebox featured-imagebox-services text-center style1">
                            <div class="ttm-post-thumbnail featured-thumbnail">
                                <img class="img-fluid" src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $box['image']); ?>" alt="service-image">
                                <div class="featured-icon">
                                    <div class="ttm-icon ttm-icon_element-fill ttm-icon_element-color-white ttm-icon_element-size-md ttm-icon_element-style-rounded">
                                        <i class="<?php echo htmlspecialchars($box['icon']); ?>"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="featured-content">
                                <div class="featured-title">
                                    <h5><a href="<?php echo htmlspecialchars($box['link']); ?>"><?php echo htmlspecialchars($box['title']); ?></a></h5>
                                </div>
                                <div class="featured-desc">
                                    <p><?php echo htmlspecialchars($box['description']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <!-- service-section end -->

    <!-- testimonial-section -->
    <section class="ttm-row testimonial-section clearfix">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-end pt-80 res-767-pt-0">
                    <div class="testimonial-wrapper ttm-quotestyle-row">
                        <div class="spacing-2 col-bg-img-one ttm-col-bgimage-yes ttm-bg res-767-h-auto">
                            <div class="ttm-col-wrapper-bg-layer ttm-bg-layer"></div>
                            <div class="layer-content h-100">
                                <div class="section-title with-desc clearfix">
                                    <div class="title-header">
                                        <h5 class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($testimonial_section['subtitle']); ?></h5>
                                        <h2 class="title"><?php echo htmlspecialchars($testimonial_section['title']); ?></h2>
                                    </div>
                                </div>
                                <div class="testimonial-slide style2 owl-carousel" data-item="1" data-nav="false" data-dots="false" data-auto="false">
                                    <?php foreach ($testimonials as $testimonial): ?>
                                        <div class="testimonials style2">
                                            <div class="testimonial-content">
                                                <p><?php echo htmlspecialchars($testimonial['quote']); ?></p>
                                                <div class="testimonial-caption">
                                                    <h6><?php echo htmlspecialchars($testimonial['name']); ?></h6>
                                                    <label><?php echo htmlspecialchars($testimonial['label']); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <div class="ttm-fid-wrapper">
                        <div class="spacing-3 res-767-mb-15 ttm-bgcolor-darkgrey ttm-bg ttm-col-bgimage-yes col-bg-img-two res-767-h-auto">
                            <div class="ttm-col-wrapper-bg-layer ttm-bg-layer"></div>
                            <div class="layer-content h-100">
                                <div class="row">
                                    <?php foreach ($facts as $index => $fact): ?>
                                        <div class="col-md-6 col-sm-6 col-6 p-0 <?php echo $index <= 2 ? 'border-bottom' : ''; ?> <?php echo $index % 2 == 0 ? 'border-left' : ''; ?> text-center">
                                            <div class="ttm-fid inside style1 <?php echo $index > 2 ? 'pb-0' : 'pt-10'; ?>">
                                                <div class="ttm-fid-center">
                                                    <div class="ttm-fid-icon-wrapper ttm-textcolor-skincolor">
                                                        <i class="<?php echo htmlspecialchars($fact['icon']); ?>"></i>
                                                    </div>
                                                    <h4 class="ttm-fid-inner ttm-textcolor-white">
                                                        <span data-appear-animation="animateDigits"
                                                            data-from="0"
                                                            data-to="<?php echo htmlspecialchars($fact['number']); ?>"
                                                            data-interval="100"
                                                            data-before=""
                                                            data-before-style="sup"
                                                            data-after=""
                                                            data-after-style="sub"
                                                        ><?php echo htmlspecialchars($fact['number']); ?></span>
                                                    </h4>
                                                </div>
                                                <div class="ttm-fid-contents">
                                                    <h3 class="ttm-fid-title"><?php echo htmlspecialchars($fact['title']); ?></h3>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- testimonial section end -->

    <!-- gallery-section -->
    <!-- Include Lightbox2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">

    <section class="ttm-row gallery-section ttm-bgcolor-grey clearfix">
        <div class="gallery-title-section ttm-bgcolor-skincolor">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-md-8 col-sm-9 m-auto">
                        <div class="section-title with-desc text-center clearfix">
                            <div class="title-header">
                                <h5><?php echo htmlspecialchars($gallery_section['subtitle']); ?></h5>
                                <h2 class="title"><?php echo htmlspecialchars($gallery_section['title']); ?></h2>
                            </div>
                            <div class="title-desc ttm-textcolor-white pl-50 pr-50 res-767-p-0"><?php echo htmlspecialchars($gallery_section['description']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container mt_70 res-991-mt_50">
            <div class="row">
                <?php foreach ($images as $index => $image): ?>
                    <div class="col-md-4">
                        <div class="featured-imagebox-portfolio">
                            <a href="<?php echo htmlspecialchars(URLROOT . '/cms/' . $image['image']); ?>" data-lightbox="gallery" data-caption="<?php echo htmlspecialchars($gallery_section['title']); ?>">
                                <img class="img-fluid" src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $image['image']); ?>" alt="gallery-image">
                                <div class="featured-content-portfolio">
                                    <i class="fa fa-camera"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <!-- gallery-section end -->

    <!-- Include Lightbox2 JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script>
        jQuery(document).ready(function($) {
            lightbox.option({
                'resizeDuration': 200,
                'wrapAround': true,
                'albumLabel': 'Image %1 of %2'
            });
        });
    </script>

    <!-- process-section -->
    <section class="ttm-row ttm-bgcolor-grey process-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-7 col-sm-9 m-auto">
                    <div class="section-title with-desc text-center clearfix">
                        <div class="title-header">
                            <h5 class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($processes_section['subtitle']); ?></h5>
                            <h2 class="title"><?php echo htmlspecialchars($processes_section['title']); ?></h2>
                        </div>
                        <div class="title-desc"><?php echo htmlspecialchars($processes_section['description']); ?></div>
                    </div>
                </div>
            </div>
            <div class="row featured-icon-row-wrapper pb-50">
                <?php foreach ($processes as $index => $process): ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="featured-icon-box icon-ver_align-top text-center style2">
                            <div class="featured-icon">
                                <div class="ttm-icon ttm-icon_element-fill ttm-icon_element-color-white ttm-icon_element-size-lg ttm-icon_element-style-rounded">
                                    <i class="flaticon <?php echo htmlspecialchars($process['icon']); ?>"></i>
                                    <div class="process-num"><span class="number"><?php echo sprintf('%02d', $index); ?></span></div>
                                </div>
                            </div>
                            <div class="featured-content">
                                <div class="featured-title">
                                    <h5><?php echo htmlspecialchars($process['title']); ?></h5>
                                </div>
                                <div class="featured-desc">
                                    <p><?php echo htmlspecialchars($process['description']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <!-- process-section end -->

    <!-- contact1-section -->
    <section class="ttm-row contact1-section mt_100 clearfix">
        <div class="container">
            <div class="row g-0">
                <div class="col-md-5 pr-0 res-767-pr-15 res-767-pb-15 text-center">
                    <div class="col-bg-img-four ttm-col-bgimage-yes ttm-bg res-767-h-auto">
                        <div class="ttm-col-wrapper-bg-layer ttm-bg-layer"></div>
                    </div>
                    <img src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $contact1_section['image']); ?>" class="ttm-equal-height-image img-fluid" alt="bg-image">
                </div>
                <div class="col-md-7 p-0">
                    <div class="spacing-5 ttm-bgcolor-darkgrey">
                        <div class="section-title with-desc clearfix">
                            <div class="title-header">
                                <h5 class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($contact1_section['subtitle']); ?></h5>
                                <h2 class="title ttm-textcolor-white"><?php echo htmlspecialchars($contact1_section['title']); ?></h2>
                            </div>
                        </div>
                        <ul class="appointment-list ttm-textcolor-white p-0">
                            <?php foreach ($timeslots as $timeslot): ?>
                                <li>
                                    <div class="time-slot">
                                        <span class="timeslot-range"><i class="fa fa-clock-o ttm-textcolor-skincolor"></i>&nbsp;&nbsp;<?php echo htmlspecialchars($timeslot['time_range']); ?></span>
                                        <span><?php echo htmlspecialchars($timeslot['spaces_available']); ?></span>
                                    </div>
                                    <div class="appointment-time">
                                        <a href="<?php echo htmlspecialchars(URLROOT . '/appointment'); ?>" class="ttm-btn ttm-btn-size-xs ttm-btn-style-fill ttm-btn-color-skincolor" role="button">Book Appointment</a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- contact1-section end -->

    <!-- blog section -->
    <section class="ttm-row blog-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-7 col-sm-9 m-auto">
                    <div class="section-title with-desc text-center clearfix">
                        <div class="title-header">
                            <h5 class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($blog_section['subtitle']); ?></h5>
                            <h2 class="title"><?php echo htmlspecialchars($blog_section['title']); ?></h2>
                        </div>
                        <div class="title-desc"><?php echo htmlspecialchars($blog_section['description']); ?></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="blog-slide owl-carousel" data-item="3" data-nav="false" data-dots="false" data-auto="false">
                    <?php foreach ($posts as $post): ?>
                        <?php if (!empty($post['title']) && !empty($post['image'])): ?>
                            <div class="featured-imagebox featured-imagebox-post style3">
                                <div class="ttm-post-thumbnail featured-thumbnail">
                                    <img class="img-fluid" src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $post['image']); ?>" alt="image">
                                </div>
                                <div class="featured-content featured-content-post">
                                    <div class="post-meta mb-10">
                                        <span class="ttm-meta-line"><i class="fa fa-calendar"></i><?php echo htmlspecialchars($post['post_date']); ?></span>
                                        <span class="ttm-meta-line"><i class="fa fa-folder-open"></i><?php echo htmlspecialchars($post['category']); ?></span>
                                    </div>
                                    <div class="post-title featured-title">
                                        <h5><a href="<?php echo htmlspecialchars($post['link']); ?>"><?php echo htmlspecialchars($post['title']); ?></a></h5>
                                    </div>
                                    <div class="post-footer">
                                        <span class="ttm-meta-line"><a class="ttm-textcolor-skincolor" href="<?php echo htmlspecialchars($post['link']); ?>">read more&nbsp;&nbsp;<i class="fa fa-angle-right"></i></a></span>
                                        <span class="ttm-meta-line"><i class="fa fa-comment-o"></i>&nbsp;0</span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <!-- blog section end -->
</div><!--site-main end-->

<?php include 'includes/footer.php'; ?>