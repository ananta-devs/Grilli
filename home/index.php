<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Grilli - Amazing & Delicious Food</title>
        <meta name="title" content="Grilli - Amazing & Delicious Food" />
        <link rel="shortcut icon" href="./favicon.svg" type="image/svg+xml" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&family=Forum&display=swap"
            rel="stylesheet"
        />
        <link rel="stylesheet" href="./assets/css/style.css" />
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        />
        <link
            rel="preload"
            as="image"
            href="./assets/images/hero-slider-1.jpg"
        />
        <link
            rel="preload"
            as="image"
            href="./assets/images/hero-slider-2.jpg"
        />
        <link
            rel="preload"
            as="image"
            href="./assets/images/hero-slider-3.jpg"
        />
        <style>
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(45deg, #28a745, #20c997);
                color: white;
                padding: 15px 25px;
                border-radius: 8px;
                box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
                transform: translateX(400px);
                transition: transform 0.3s ease;
                z-index: 1000;
                opacity: 0;
                visibility: hidden;
            }

            .notification.show {
                transform: translateX(0);
                opacity: 1;
                visibility: visible;
            }

            .notification.error {
                background: linear-gradient(45deg, #dc3545, #e74c3c);
                box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
            }
            .profile-dropdown {
                position: relative;
            }

            .profile-btn {
                display: flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                background: none;
                border: none;
                padding: 0;
                font-family: inherit;
                text-decoration: none;
                color: inherit;
            } 
        </style>
    </head>

    <body id="top">

        <div class="preload" data-preaload>
            <div class="circle"></div>
            <p class="text">Grilli</p>
        </div>

        <div class="topbar">
            <div class="container">
                <address class="topbar-item">
                    <div class="icon">
                        <ion-icon
                            name="location-outline"
                            aria-hidden="true"
                        ></ion-icon>
                    </div>

                    <span class="span"> Salt Lake, Kolkata-700001 </span>
                </address>

                <div class="separator"></div>

                <div class="topbar-item item-2">
                    <div class="icon">
                        <ion-icon
                            name="time-outline"
                            aria-hidden="true"
                        ></ion-icon>
                    </div>

                    <span class="span">Daily : 8.00 am to 10.00 pm</span>
                </div>

                <a href="tel:+11234567890" class="topbar-item link">
                    <div class="icon">
                        <ion-icon
                            name="call-outline"
                            aria-hidden="true"
                        ></ion-icon>
                    </div>

                    <span class="span">8637399648</span>
                </a>

                <div class="separator"></div>

                <a
                    href="mailto:booking@restaurant.com"
                    class="topbar-item link"
                >
                    <div class="icon">
                        <ion-icon
                            name="mail-outline"
                            aria-hidden="true"
                        ></ion-icon>
                    </div>

                    <span class="span">booking@restaurantgrilli.com</span>
                </a>
            </div>
        </div>

        <header class="header" data-header>
            <div class="container">
                <a href="#" class="logo">
                    <img
                        src="./assets/images/logo.svg"
                        width="160"
                        height="50"
                        alt="Grilli - Home"
                    />
                </a>

            <?php include './nav-bar.php';?>


                <a href="#" class="btn btn-secondary">
                    <span class="text text-1">Find A Table </span>

                    <span class="text text-2" aria-hidden="true">
                        Find A Table</span
                    >
                </a>

                <button
                    class="nav-open-btn"
                    aria-label="open menu"
                    data-nav-toggler
                >
                    <span class="line line-1"></span>
                    <span class="line line-2"></span>
                    <span class="line line-3"></span>
                </button>

                <div class="overlay" data-nav-toggler data-overlay></div>
            </div>
        </header>
        

        <main>
            <article>
                <section class="hero text-center" aria-label="home" id="home">
                    <ul class="hero-slider" data-hero-slider>
                        <li class="slider-item active" data-hero-slider-item>
                            <div class="slider-bg">
                                <img
                                    src="./assets/images/hero-slider-1.jpg"
                                    width="1880"
                                    height="950"
                                    alt=""
                                    class="img-cover"
                                />
                            </div>

                            <p class="label-2 section-subtitle slider-reveal">
                                Tradational & Hygine
                            </p>

                            <h1 class="display-1 hero-title slider-reveal">
                                For the love of <br />
                                delicious food
                            </h1>

                            <p class="body-2 hero-text slider-reveal">
                                Come with family & feel the joy of mouthwatering
                                food
                            </p>
                        </li>

                        <li class="slider-item" data-hero-slider-item>
                            <div class="slider-bg">
                                <img
                                    src="./assets/images/hero-slider-2.jpg"
                                    width="1880"
                                    height="950"
                                    alt=""
                                    class="img-cover"
                                />
                            </div>

                            <p class="label-2 section-subtitle slider-reveal">
                                delightful experience
                            </p>

                            <h1 class="display-1 hero-title slider-reveal">
                                Flavors Inspired by <br />
                                the Seasons
                            </h1>

                            <p class="body-2 hero-text slider-reveal">
                                Come with family & feel the joy of mouthwatering
                                food
                            </p>

                        </li>

                        <li class="slider-item" data-hero-slider-item>
                            <div class="slider-bg">
                                <img
                                    src="./assets/images/hero-slider-3.jpg"
                                    width="1880"
                                    height="950"
                                    alt=""
                                    class="img-cover"
                                />
                            </div>

                            <p class="label-2 section-subtitle slider-reveal">
                                amazing & delicious
                            </p>

                            <h1 class="display-1 hero-title slider-reveal">
                                Where every flavor <br />
                                tells a story
                            </h1>

                            <p class="body-2 hero-text slider-reveal">
                                Come with family & feel the joy of mouthwatering
                                food
                            </p>
                        </li>
                    </ul>

                    <button
                        class="slider-btn prev"
                        aria-label="slide to previous"
                        data-prev-btn
                    >
                        <ion-icon name="chevron-back"></ion-icon>
                    </button>

                    <button
                        class="slider-btn next"
                        aria-label="slide to next"
                        data-next-btn
                    >
                        <ion-icon name="chevron-forward"></ion-icon>
                    </button>

                    <a href="#" class="hero-btn has-after">
                        <img
                            src="./assets/images/hero-icon.png"
                            width="48"
                            height="48"
                            alt="booking icon"
                        />

                        <span class="label-2 text-center span"
                            >Book A Table</span
                        >
                    </a>
                </section>

                <section class="section service bg-black-10 text-center"aria-label="service">
                    <div class="container">
                        <p class="section-subtitle label-2">
                            Flavors For Royalty
                        </p>

                        <h2 class="headline-1 section-title">
                            We Offer Top Notch
                        </h2>

                        <p class="section-text">
                            We serve different kinds of foods and three time's
                            courses. Go and check our menu to know more.
                        </p>

                        <ul class="grid-list">
                            <li>
                                <div class="service-card">
                                    <a href="http://localhost/grilli/home/menu.php?type=break-fast"
                                        class="has-before hover:shine">
                                        <figure
                                            class="card-banner img-holder"
                                            style="--width: 285; --height: 336"
                                        >
                                            <img
                                                src="./assets/images/service-1.jpg"
                                                width="285"
                                                height="336"
                                                loading="lazy"
                                                alt="Breakfast"
                                                class="img-cover"
                                            />
                                        </figure>
                                    </a>

                                    <div class="card-content">
                                        <h3 class="title-4 card-title">
                                            Breakfast
                                        </h3>

                                        <a
                                            href="http://localhost/grilli/home/menu.php?type=break-fast"
                                            class="btn-text hover-underline label-2"
                                            >View Menu</a
                                        >
                                    </div>
                                </div>
                            </li>

                            <li>
                                <div class="service-card">
                                    <a
                                        href="http://localhost/grilli/home/menu.php?type=lunch"
                                        class="has-before hover:shine"
                                    >
                                        <figure
                                            class="card-banner img-holder"
                                            style="--width: 285; --height: 336"
                                        >
                                            <img
                                                src="./assets/images/service-2.jpg"
                                                width="285"
                                                height="336"
                                                loading="lazy"
                                                alt="Appetizers"
                                                class="img-cover"
                                            />
                                        </figure>
                                    </a>

                                    <div class="card-content">
                                        <h3 class="title-4 card-title">
                                            Lunch
                                        </h3>

                                        <a
                                            href="http://localhost/grilli/home/menu.php?type=lunch"
                                            class="btn-text hover-underline label-2"
                                            >View Menu</a
                                        >
                                    </div>
                                </div>
                            </li>

                            <li>
                                <div class="service-card">
                                    <a
                                        href="http://localhost/grilli/home/menu.php?type=dinner"
                                        class="has-before hover:shine"
                                    >
                                        <figure
                                            class="card-banner img-holder"
                                            style="--width: 285; --height: 336"
                                        >
                                            <img
                                                src="./assets/images/service-3.jpg"
                                                width="285"
                                                height="336"
                                                loading="lazy"
                                                alt="Drinks"
                                                class="img-cover"
                                            />
                                        </figure>
                                    </a>

                                    <div class="card-content">
                                        <h3 class="title-4 card-title">
                                           Dinner                                            
                                        </h3>

                                        <a href="http://localhost/grilli/home/menu.php?type=dinner"
                                            class="btn-text hover-underline label-2">
                                            View Menu
                                        </a>
                                    </div>
                                </div>
                            </li>
                        </ul>

                        <img
                            src="./assets/images/shape-1.png"
                            width="246"
                            height="412"
                            loading="lazy"
                            alt="shape"
                            class="shape shape-1 move-anim"
                        />
                        <img
                            src="./assets/images/shape-2.png"
                            width="343"
                            height="345"
                            loading="lazy"
                            alt="shape"
                            class="shape shape-2 move-anim"
                        />
                    </div>
                </section>

                <section class="section about text-center" aria-labelledby="about-label" id="about">
                    <div class="container">
                        <div class="about-content">
                            <p
                                class="label-2 section-subtitle"
                                id="about-label"
                            >
                                Our Story
                            </p>

                            <h2 class="headline-1 section-title">
                                Every Fla vor Tells a Story
                            </h2>

                            <p class="section-text">
                                A Food Hotel producetion involves chefs and
                                kitchen stuff following standerilized receipes
                                with attention to detail and consistency to
                                maintain quality. Hotel kitchens utalize
                                high-quality tools and specialied equipment
                                ,like sous vide machines, to enhance cooking
                                results . Food safety protocols are also
                                crucial,encompassing receiving ,storing
                                ,preparing , cooking , and serving , according
                                to the Process Approach.
                            </p>

                            <div class="contact-label">Book Through Call</div>

                            <a
                                href="#"
                                class="body-1 contact-number hover-underline"
                                >+918637399648</a
                            >

                        </div>

                        <figure class="about-banner">
                            <img
                                src="./assets/images/about-banner.jpg"
                                width="570"
                                height="570"
                                loading="lazy"
                                alt="about banner"
                                class="w-100"
                                data-parallax-item
                                data-parallax-speed="1"
                            />

                            <div
                                class="abs-img abs-img-1 has-before"
                                data-parallax-item
                                data-parallax-speed="1.75"
                            >
                                <img
                                    src="./assets/images/about-abs-image.jpg"
                                    width="285"
                                    height="285"
                                    loading="lazy"
                                    alt=""
                                    class="w-100"
                                />
                            </div>

                            <div class="abs-img abs-img-2 has-before">
                                <img
                                    src="./assets/images/badge-2.png"
                                    width="133"
                                    height="134"
                                    loading="lazy"
                                    alt=""
                                />
                            </div>
                        </figure>

                        <img
                            src="./assets/images/shape-3.png"
                            width="197"
                            height="194"
                            loading="lazy"
                            alt=""
                            class="shape"
                        />
                    </div>
                </section>

                <section class="special-dish text-center" aria-labelledby="dish-label">
                    <div class="special-dish-banner">
                        <img
                            src="./assets/images/special-dish-banner.jpg"
                            width="940"
                            height="900"
                            loading="lazy"
                            alt="special dish"
                            class="img-cover"
                        />
                    </div>

                    <div class="special-dish-content bg-black-10">
                        <div class="container">
                            <img
                                src="./assets/images/badge-1.png"
                                width="28"
                                height="41"
                                loading="lazy"
                                alt="badge"
                                class="abs-img"
                            />

                            <p class="section-subtitle label-2">Special Dish</p>

                            <h2 class="headline-1 section-title">
                                Fried Chicken
                            </h2>

                            <p class="section-text">
                                Fried chicken, also called Southern fried
                                chicken, is a dish consisting of chicken pieces
                                that have been coated with seasoned flour or
                                batter and pan-fried, deep fried, pressure
                                fried, or air fried. The breading adds a crisp
                                coating or crust to the exterior of the chicken
                                while retaining juices in the meat.
                            </p>

                            <div class="wrapper">
                                <del
                                    class="del body-3"
                                    style="text-decoration-line: line-through"
                                    >₹799.00</del
                                >

                                <span class="span body-1">₹399.00</span>
                            </div>
                        </div>
                    </div>

                    <img
                        src="./assets/images/shape-4.png"
                        width="179"
                        height="359"
                        loading="lazy"
                        alt=""
                        class="shape shape-1"
                    />

                    <img
                        src="./assets/images/shape-9.png"
                        width="351"
                        height="462"
                        loading="lazy"
                        alt=""
                        class="shape shape-2"
                    />
                </section>

                <section class="section menu" aria-label="menu-label" id="menu">
                    <div class="container">
                        <p class="section-subtitle text-center label-2">
                            Special Selection
                        </p>

                        <h2 class="headline-1 section-title text-center">
                            Delicious Menu
                        </h2>

                        <ul class="grid-list">
                            <li>
                                <div class="menu-card hover:card">
                                    <figure
                                        class="card-banner img-holder"
                                        style="--width: 100; --height: 100"
                                    >
                                        <img
                                            src="./assets/images/menu-1.png"
                                            width="100"
                                            height="100"
                                            loading="lazy"
                                            alt="Greek Salad"
                                            class="img-cover"
                                        />
                                    </figure>

                                    <div>
                                        <div class="title-wrapper">
                                            <h3 class="title-3">
                                                <a href="#" class="card-title"
                                                    >Greek Salad</a
                                                >
                                            </h3>

                                            <span class="badge label-1"
                                                >Seasonal</span
                                            >

                                            <span class="span title-2"
                                                >₹99.00</span
                                            >
                                        </div>

                                        <p class="card-text label-1">
                                            Tomatoes, green bell pepper, sliced
                                            cucumber onion, olives, and feta
                                            cheese.
                                        </p>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <div class="menu-card hover:card">
                                    <figure
                                        class="card-banner img-holder"
                                        style="--width: 100; --height: 100"
                                    >
                                        <img
                                            src="./assets/images/menu-2.png"
                                            width="100"
                                            height="100"
                                            loading="lazy"
                                            alt="Lasagne"
                                            class="img-cover"
                                        />
                                    </figure>

                                    <div>
                                        <div class="title-wrapper">
                                            <h3 class="title-3">
                                                <a href="#" class="card-title"
                                                    >Lasagne</a
                                                >
                                            </h3>

                                            <span class="span title-2"
                                                >₹129.00</span
                                            >
                                        </div>

                                        <p class="card-text label-1">
                                            Vegetables, cheeses, ground meats,
                                            tomato sauce, seasonings and spices
                                        </p>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <div class="menu-card hover:card">
                                    <figure
                                        class="card-banner img-holder"
                                        style="--width: 100; --height: 100"
                                    >
                                        <img
                                            src="./assets/images/menu-3.png"
                                            width="100"
                                            height="100"
                                            loading="lazy"
                                            alt="Butternut Pumpkin"
                                            class="img-cover"
                                        />
                                    </figure>

                                    <div>
                                        <div class="title-wrapper">
                                            <h3 class="title-3">
                                                <a href="#" class="card-title"
                                                    >Butternut Pumpkin</a
                                                >
                                            </h3>

                                            <span class="span title-2"
                                                >₹139.00</span
                                            >
                                        </div>

                                        <p class="card-text label-1">
                                            Typesetting industry lorem Lorem
                                            Ipsum is simply dummy text of the
                                            priand.
                                        </p>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <div class="menu-card hover:card">
                                    <figure
                                        class="card-banner img-holder"
                                        style="--width: 100; --height: 100"
                                    >
                                        <img
                                            src="./assets/images/menu-4.png"
                                            width="100"
                                            height="100"
                                            loading="lazy"
                                            alt="Tokusen Wagyu"
                                            class="img-cover"
                                        />
                                    </figure>

                                    <div>
                                        <div class="title-wrapper">
                                            <h3 class="title-3">
                                                <a href="#" class="card-title"
                                                    >Tokusen Wagyu</a
                                                >
                                            </h3>

                                            <span class="badge label-1"
                                                >New</span
                                            >

                                            <span class="span title-2"
                                                >₹159.00</span
                                            >
                                        </div>

                                        <p class="card-text label-1">
                                            Vegetables, cheeses, ground meats,
                                            tomato sauce, seasonings and spices.
                                        </p>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <div class="menu-card hover:card">
                                    <figure
                                        class="card-banner img-holder"
                                        style="--width: 100; --height: 100"
                                    >
                                        <img
                                            src="./assets/images/menu-5.png"
                                            width="100"
                                            height="100"
                                            loading="lazy"
                                            alt="Olivas Rellenas"
                                            class="img-cover"
                                        />
                                    </figure>

                                    <div>
                                        <div class="title-wrapper">
                                            <h3 class="title-3">
                                                <a href="#" class="card-title"
                                                    >Olivas Rellenas</a
                                                >
                                            </h3>

                                            <span class="span title-2"
                                                >₹199.00</span
                                            >
                                        </div>

                                        <p class="card-text label-1">
                                            Avocados with crab meat, red onion,
                                            crab salad stuffed red bell pepper
                                            and green bell pepper.
                                        </p>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <div class="menu-card hover:card">
                                    <figure
                                        class="card-banner img-holder"
                                        style="--width: 100; --height: 100"
                                    >
                                        <img
                                            src="./assets/images/menu-6.png"
                                            width="100"
                                            height="100"
                                            loading="lazy"
                                            alt="Opu Fish"
                                            class="img-cover"
                                        />
                                    </figure>

                                    <div>
                                        <div class="title-wrapper">
                                            <h3 class="title-3">
                                                <a href="#" class="card-title"
                                                    >Opu Fish</a
                                                >
                                            </h3>

                                            <span class="span title-2"
                                                >₹299.00</span
                                            >
                                        </div>

                                        <p class="card-text label-1">
                                            Vegetables, cheeses, ground meats,
                                            tomato sauce, seasonings and spices
                                        </p>
                                    </div>
                                </div>
                            </li>
                        </ul>

                        <p class="menu-text text-center">
                            During winter daily from
                            <span class="span">7:00 pm</span> to
                            <span class="span">9:00 pm</span>
                        </p>
                        <img
                            src="./assets/images/shape-5.png"
                            width="921"
                            height="1036"
                            loading="lazy"
                            alt="shape"
                            class="shape shape-2 move-anim"
                        />
                        <img
                            src="./assets/images/shape-6.png"
                            width="343"
                            height="345"
                            loading="lazy"
                            alt="shape"
                            class="shape shape-3 move-anim"
                        />
                    </div>
                </section>

                <section class="section testi text-center has-bg-image" style="background-image: url('./assets/images/testimonial-bg.jpg');" aria-label="testimonials">
                    <div class="container">
                        <div class="quote">”</div>

                        <p class="headline-2 testi-text">
                            We wanted to thank you for choosing us.
                            Your trust means everything to us, and we're committed to serving you with excellence.
                            We look forward to continuing this journey together and exceeding your expectations.
                        </p>

                        <div class="wrapper">
                            <div class="separator"></div>
                            <div class="separator"></div>
                            <div class="separator"></div>
                        </div>

                        
                    </div>
                </section>

                <section class="reservation">
                    <div class="container">
                        <div class="form reservation-form bg-black-10">
                            <form id="reservationForm" class="form-left">
                                <h2 class="headline-1 text-center">Online Reservation</h2>

                                <p class="form-text text-center">
                                    Booking request
                                    <a href="#" class="link">8637399648 </a>
                                    or fill out the order form
                                </p>

                                <div class="input-wrapper">
                                    <input
                                        type="text"
                                        name="name"
                                        placeholder="Your Name"
                                        class="input-field"
                                        required
                                    />
                                    <input
                                        type="tel"
                                        name="phone"
                                        placeholder="Phone Number"
                                        class="input-field"
                                        required
                                    />
                                </div>

                                <div class="input-wrapper">
                                    <div class="icon-wrapper">
                                        <ion-icon
                                            name="person-outline"
                                            aria-hidden="true"
                                        ></ion-icon>
                                        <select
                                            name="persons"
                                            id="personsSelect"
                                            class="input-field"
                                            required
                                        >
                                            <option value="">Number of Persons</option>
                                        </select>
                                        <ion-icon
                                            name="chevron-down"
                                            aria-hidden="true"
                                        ></ion-icon>
                                    </div>

                                    <div class="icon-wrapper">
                                        <ion-icon
                                            name="calendar-clear-outline"
                                            aria-hidden="true"
                                        ></ion-icon>
                                        <input
                                            type="date"
                                            name="reservation_date"
                                            class="input-field"
                                            required
                                        />
                                        <ion-icon
                                            name="chevron-down"
                                            aria-hidden="true"
                                        ></ion-icon>
                                    </div>

                                    <div class="icon-wrapper">
                                        <ion-icon
                                            name="time-outline"
                                            aria-hidden="true"
                                        ></ion-icon>
                                        <select name="time_slot" class="input-field" required>
                                            <option value="">Select Time Slot</option>
                                            <option value="09:00 - 10:00 AM">
                                                09:00 - 10:00 AM
                                            </option>
                                            <option value="10:00 - 11:00 AM">
                                                10:00 - 11:00 AM
                                            </option>
                                            <option value="11:00 - 12:00 PM">
                                                11:00 - 12:00 PM
                                            </option>
                                            <option value="12:00 - 01:00 PM">
                                                12:00 - 01:00 PM
                                            </option>
                                            <option value="01:00 - 02:00 PM">
                                                01:00 - 02:00 PM
                                            </option>
                                            <option value="02:00 - 03:00 PM">
                                                02:00 - 03:00 PM
                                            </option>
                                            <option value="03:00 - 04:00 PM">
                                                03:00 - 04:00 PM
                                            </option>
                                            <option value="05:00 - 06:00 PM">
                                                05:00 - 06:00 PM
                                            </option>
                                            <option value="06:00 - 07:00 PM">
                                                06:00 - 07:00 PM
                                            </option>
                                            <option value="07:00 - 08:00 PM">
                                                07:00 - 08:00 PM
                                            </option>
                                            <option value="08:00 - 09:00 PM">
                                                08:00 - 09:00 PM
                                            </option>
                                            <option value="09:00 - 10:00 PM">
                                                09:00 - 10:00 PM
                                            </option>
                                        </select>
                                        <ion-icon
                                            name="chevron-down"
                                            aria-hidden="true"
                                        ></ion-icon>
                                    </div>
                                </div>

                                <textarea
                                    name="message"
                                    placeholder="Special requests or dietary requirements"
                                    autocomplete="off"
                                    class="input-field"
                                ></textarea>

                                <button type="submit" class="btn btn-secondary">
                                    <span class="text text-1">Book A Table</span>
                                    <span class="text text-2" aria-hidden="true"
                                        >Book A Table</span
                                    >
                                </button>
                            </form>

                            <div
                                class="form-right text-center"
                                style="
                                    background-image: url('./assets/images/form-pattern.png');
                                "
                            >
                                <h2 class="headline-1 text-center">Contact Us</h2>

                                <p class="contact-label">Booking Request</p>
                                <a
                                    href="tel:+88123123456"
                                    class="body-1 contact-number hover-underline"
                                >
                                    +918637399648 Santanu
                                </a>

                                <div class="separator"></div>

                                <p class="contact-label">Location</p>
                                <address class="body-4">Salt Lake,Kolkata-700001</address>

                                <p class="contact-label">Lunch Time</p>
                                <p class="body-4">
                                    Monday to Sunday <br />
                                    11.00 am - 2.30pm
                                </p>

                                <p class="contact-label">Dinner Time</p>
                                <p class="body-4">
                                    Monday to Sunday <br />
                                    05.00 pm - 10.00pm
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
                
                <div id="notification" class="notification">
                    <span id="notificationText"></span>
                </div>

                <section class="section features text-center" aria-label="features">
                    <div class="container">
                        <p class="section-subtitle label-2">Why Choose Us</p>

                        <h2 class="headline-1 section-title">Our Strength</h2>

                        <ul class="grid-list">
                            <li class="feature-item">
                                <div class="feature-card">
                                    <div class="card-icon">
                                        <img
                                            src="./assets/images/features-icon-1.png"
                                            width="100"
                                            height="80"
                                            loading="lazy"
                                            alt="icon"
                                        />
                                    </div>

                                    <h3 class="title-2 card-title">
                                        Hygienic Food
                                    </h3>

                                    <p class="label-1 card-text">
                                        We don't Compromize about Hygine in any
                                        kind of food avaliable in our hotel
                                    </p>
                                </div>
                            </li>

                            <li class="feature-item">
                                <div class="feature-card">
                                    <div class="card-icon">
                                        <img
                                            src="./assets/images/features-icon-2.png"
                                            width="100"
                                            height="80"
                                            loading="lazy"
                                            alt="icon"
                                        />
                                    </div>

                                    <h3 class="title-2 card-title">
                                        Fresh Environment
                                    </h3>

                                    <p class="label-1 card-text">
                                        A wholesome homely experienced, Fresh
                                        Flavours , authentic spices.
                                    </p>
                                </div>
                            </li>

                            <li class="feature-item">
                                <div class="feature-card">
                                    <div class="card-icon">
                                        <img
                                            src="./assets/images/features-icon-3.png"
                                            width="100"
                                            height="80"
                                            loading="lazy"
                                            alt="icon"
                                        />
                                    </div>

                                    <h3 class="title-2 card-title">
                                        Skilled Chefs
                                    </h3>

                                    <p class="label-1 card-text">
                                        We have the best SKILLED chefs from all
                                        over INDIA just for Good Food .
                                    </p>
                                </div>
                            </li>

                            <li class="feature-item">
                                <div class="feature-card">
                                    <div class="card-icon">
                                        <img
                                            src="./assets/images/features-icon-4.png"
                                            width="100"
                                            height="80"
                                            loading="lazy"
                                            alt="icon"
                                        />
                                    </div>

                                    <h3 class="title-2 card-title">
                                        Event & Party
                                    </h3>

                                    <p class="label-1 card-text">
                                        We Organized Parties and Events as well
                                        as We give Our Best Food .
                                    </p>
                                </div>
                            </li>
                        </ul>

                        <img
                            src="./assets/images/shape-7.png"
                            width="208"
                            height="178"
                            loading="lazy"
                            alt="shape"
                            class="shape shape-1"
                        />

                        <img
                            src="./assets/images/shape-8.png"
                            width="120"
                            height="115"
                            loading="lazy"
                            alt="shape"
                            class="shape shape-2"
                        />
                    </div>
                </section>
            </article>
        </main>

        <footer class="footer section has-bg-image text-center" style="background-image: url('./assets/images/footer-bg.jpg')">
            <div class="container">
                <div class="footer-top grid-list">
                    <div class="footer-brand has-before has-after">
                        <a href="#" class="logo">
                            <img
                                src="./assets/images/logo.svg"
                                width="160"
                                height="50"
                                loading="lazy"
                                alt="grilli home"
                            />
                        </a>

                        <address class="body-4">
                            Salt Lake , Kolkata-700001
                        </address>

                        <a
                            href="mailto:booking@grilli.com"
                            class="body-4 contact-link"
                            >booking@grilli.com</a
                        >

                        <a href="#" class="body-4 contact-link"
                            >Booking Request : 8637399648</a
                        >

                        <p class="body-4">Open : 09:00 am - 01:00 pm</p>

                        <div class="wrapper">
                            <div class="separator"></div>
                            <div class="separator"></div>
                            <div class="separator"></div>
                        </div>

                        <p class="title-1">Get News & Offers</p>

                        <p class="label-1">
                            Subscribe us & Get
                            <span class="span">25% Off.</span>
                        </p>

                        <form action="" class="input-wrapper">
                            <div class="icon-wrapper">
                                <ion-icon
                                    name="mail-outline"
                                    aria-hidden="true"
                                ></ion-icon>

                                <input
                                    type="email"
                                    name="email_address"
                                    placeholder="Your email"
                                    autocomplete="off"
                                    class="input-field"
                                />
                            </div>

                            <button type="submit" class="btn btn-secondary">
                                <span class="text text-1">Subscribe</span>

                                <span class="text text-2" aria-hidden="true"
                                    >Subscribe</span
                                >
                            </button>
                        </form>
                    </div>

                    <ul class="footer-list">
                        <li>
                            <a
                                href="https://www.facebook.com/share/18sKdYRq6Y/"
                                class="label-2 footer-link hover-underline"
                                >Facebook</a
                            >
                        </li>
                    </ul>
                </div>

                <div class="footer-bottom">
                    <p class="copyright">
                        &copy; 2025@GRILLI (All Rights Reserved)
                    </p>
                </div>
            </div>
        </footer>

        <a
            href="#top"
            class="back-top-btn active"
            aria-label="back to top"
            data-back-top-btn
        >
            <ion-icon name="chevron-up" aria-hidden="true"></ion-icon>
        </a>


        <script src="script.js"></script>

        <script src="./assets/js/script.js"></script>

        <!-- Script to handle "Find A Table" and "Book A Table" button clicks
        This will smoothly scroll to the reservation section -->
        <script>
             document.addEventListener('DOMContentLoaded', function() {
                // Function to smooth scroll to reservation section
                function scrollToReservation() {
                    const reservationSection = document.querySelector('.reservation');
                    if (reservationSection) {
                        reservationSection.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }

                // Get all "Find A Table" buttons
                const findTableButtons = document.querySelectorAll('a[href="index.php"]');
                
                // Get all "Book A Table" buttons (in hero section and reservation form)
                const bookTableButtons = document.querySelectorAll('.hero-btn');
                
                // Add click event listeners to "Find A Table" buttons
                findTableButtons.forEach(button => {
                    // Check if the button contains "Find A Table" text
                    if (button.textContent.includes('Find A Table')) {
                        button.addEventListener('click', function(e) {
                            e.preventDefault(); // Prevent default link behavior
                            scrollToReservation();
                        });
                    }
                });

                // Add click event listeners to "Book A Table" buttons in hero section
                bookTableButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault(); // Prevent default link behavior
                        scrollToReservation();
                    });
                });

                // Also handle any other "Book A Table" text elements
                const allButtons = document.querySelectorAll('a, button');
                allButtons.forEach(element => {
                    if (element.textContent.includes('Book A Table') || element.textContent.includes('Find A Table')) {
                        element.addEventListener('click', function(e) {
                            // Only prevent default if it's not the form submit button
                            if (!element.closest('form') || element.type !== 'submit') {
                                e.preventDefault();
                                scrollToReservation();
                            }
                        });
                    }
                });

                // Optional: Add a subtle highlight effect when scrolling to reservation
                function highlightReservation() {
                    const reservationSection = document.querySelector('.reservation');
                    if (reservationSection) {
                        reservationSection.style.transition = 'box-shadow 0.3s ease';
                        reservationSection.style.boxShadow = '0 0 20px rgba(255, 255, 255, 0.3)';
                        
                        // Remove highlight after 2 seconds
                        setTimeout(() => {
                            reservationSection.style.boxShadow = 'none';
                        }, 2000);
                    }
                }

                // Enhanced scroll function with highlight
                function scrollToReservationWithHighlight() {
                    scrollToReservation();
                    setTimeout(highlightReservation, 800); // Delay to let scroll finish
                }

                // Update the event listeners to use the enhanced function
                const enhancedButtons = document.querySelectorAll('a, button');
                enhancedButtons.forEach(element => {
                    if (element.textContent.includes('Book A Table') || element.textContent.includes('Find A Table')) {
                        // Remove previous listeners and add new ones
                        element.removeEventListener('click', scrollToReservation);
                        element.addEventListener('click', function(e) {
                            if (!element.closest('form') || element.type !== 'submit') {
                                e.preventDefault();
                                scrollToReservationWithHighlight();
                            }
                        });
                    }
                });
            });
            
        </script>



        <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    </body>
</html>