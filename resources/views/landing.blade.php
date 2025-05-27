<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Ofoghe Talaei | Stay in Touch </title>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..."
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />

    <link
        type="image/x-icon"
        href="{{ vite()->asset('images/favicon.ico') }}"
        rel="shortcut icon"
        sizes="16x16"
    />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            overflow-x: hidden;

        }
        img {
            max-width: 100%;
            height: auto;
            display: block;
        }
        .content {
            max-width: 100%;
            overflow: hidden;
        }
        .hero {
            height: 100vh;
            position: relative;
            background-color: #f9f9f6;
        }

        .overlay {
            height: 100%;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center; /* Center vertically */
            color: white;
            padding-top: 0; /* Default */
            text-align: center;
        }

        @media (max-width: 768px) {
            .overlay {
                align-items: flex-start;   /* Push content up on mobile */
                padding-top: 15vh;         /* Adjust this value as needed */
            }
        }

        .heading {
            font-size: 1.5rem;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 30px;
            font-size: 2rem;
        }

        .social-icons a {
            color: #2c2c2c;
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .social-icons a:hover {
            color: #1a1a2e;
            transform: scale(1.2);
        }


        .credit a {
            color: white;
            text-decoration: underline;
        }

    </style>
</head>
<body>
<div class="hero">
    <div class="overlay">
        <div class="content">
            <p class="heading">
                <img alt="Ofoghe talaei" src="{{vite()->asset('images/landing-logo.svg')}}">
            </p>

            <div class="social-icons">
                <a href="https://www.instagram.com/aghaydubai?igsh=MTV5NW94NWYwMGJscA%3D%3D&utm_source=qr"><i class="fab fa-instagram"></i></a>
                <a href="https://wa.me/971552554688"><i class="fab fa-whatsapp"></i></a>
            </div>

        </div>
    </div>
</div>
</body>
</html>
