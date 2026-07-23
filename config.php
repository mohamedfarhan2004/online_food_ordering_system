<?php
/**
 * =====================================================================
 * CENTRAL CONFIGURATION FILE
 * =====================================================================
 * Every setting your host / payment gateway / email needs lives here.
 * Fill these in before you go live. Everything works locally in
 * "DEV MODE" even if you leave the placeholders untouched (OTPs will
 * simply be shown on screen instead of emailed, and card payment will
 * show a friendly notice until you add real Stripe keys).
 * =====================================================================
 */

// Guard against this file ever running twice in one request (belt-and-braces
// on top of require_once, since some servers reuse the constant table).
if (!defined('DB_HOST')) {

// ---------------------------------------------------------------------
// DATABASE
// ---------------------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'food_ordering');

// ---------------------------------------------------------------------
// SITE
// ---------------------------------------------------------------------
define('SITE_NAME', 'Food Delight');
// Base URL of your site WITHOUT trailing slash, e.g. https://www.mysite.com/food_ordering
// Used to build absolute links for emails and Stripe redirect URLs.
define('SITE_URL', 'http://localhost/food_ordering');
// Currency code used for Stripe (must be a currency your Stripe account supports)
define('CURRENCY_CODE', 'usd');
// Currency symbol shown on the site (menu / cart / orders)
define('SITE_CURRENCY_SYMBOL', 'Rs.');

// ---------------------------------------------------------------------
// ADMIN LOGIN
// ---------------------------------------------------------------------
// Single hardcoded admin account as requested. Change the password here
// any time - it takes effect immediately, no database change needed.
define('ADMIN_USERNAME', 'Farhan');
define('ADMIN_PASSWORD', 'Farhan1234');

// ---------------------------------------------------------------------
// EMAIL / OTP (SMTP)
// ---------------------------------------------------------------------
// Used to send OTP codes for signup verification and password reset.
// PHPMailer is already bundled in vendor/ - no Composer install needed.
// Works out of the box with Gmail if you create an "App Password":
// https://myaccount.google.com/apppasswords
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'farhanfaleel9@gmail.com');   // <-- change me
define('SMTP_PASSWORD', 'vaqm dlfq gixr ecmg'); // <-- change me
define('SMTP_FROM_EMAIL', 'your_email@gmail.com'); // <-- change me
define('SMTP_FROM_NAME', SITE_NAME);

// Set this to true once you've filled in real SMTP details above, so OTP
// codes are actually emailed to customers instead of shown on-screen.
define('SEND_REAL_EMAILS', true);

// ---------------------------------------------------------------------
// PAYMENTS - STRIPE (Card payments)
// ---------------------------------------------------------------------
// Get test keys free at https://dashboard.stripe.com/test/apikeys
// Replace with your LIVE keys only when you are ready to accept real money.
define('STRIPE_SECRET_KEY', 'sk_test_XXXXXXXXXXXXXXXXXXXXXXXX');      // <-- change me
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_XXXXXXXXXXXXXXXXXXXXXXXX'); // <-- change me

// ---------------------------------------------------------------------
// PAYMENTS - UPI (India / direct bank transfer style QR payments)
// ---------------------------------------------------------------------
// Your business UPI ID (VPA), e.g. "yourshop@okhdfcbank"
define('UPI_VPA', 'yourbusiness@upi');   // <-- change me
define('UPI_PAYEE_NAME', SITE_NAME);

}

// ---------------------------------------------------------------------
// DATABASE CONNECTION (mysqli)
// ---------------------------------------------------------------------
if (!isset($connect) || !($connect instanceof mysqli)) {
    $connect = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if (!$connect) {
        die("Database Connection Failed: " . mysqli_connect_error());
    }
    mysqli_set_charset($connect, "utf8mb4");
}
