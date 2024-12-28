<?php
session_start();
// CSRF token validation
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token.");
}

// Math CAPTCHA: Validate the CAPTCHA answer
if (isset($_POST['captcha'])) {
    $user_captcha_answer = trim($_POST['captcha']);
    $correct_answer = $_SESSION['captcha_num1'] + $_SESSION['captcha_num2']; // result

    if ($user_captcha_answer != $correct_answer) {
        die("Incorrect CAPTCHA answer.");
    }
}

// Sanitize and validate form inputs
$name = htmlspecialchars(trim($_POST['name']));
$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
$message = htmlspecialchars(trim($_POST['message']));


// Prevent email injection by checking for malicious characters
function sanitize_email_header($input)
{
    return preg_replace('/[\r\n\t]/', '', $input);
}

$email = sanitize_email_header($email);
$message = sanitize_email_header($message);

// Validate required fields
if (empty($name) || empty($email) || empty($message)) {
    die("All fields are required.");
}

if (!$email) {
    die("Invalid email address.");
}


// Email configuration
$to = "info@japneet.online"; // Replace with your email address
$subject = "New Contact Form Submission";

// Construct email content
$email_message = "You have a new message from your website contact form:\n\n";
$email_message .= "Name: $name\n";
$email_message .= "Email: $email\n\n";
$email_message .= "Message:\n$message\n";

// Email headers
$headers = "From: Your Website <$to>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";


// Send email
if (mail($to, $subject, $email_message, $headers)) {
    echo "Your message has been sent successfully.";
} else {
    echo "Failed to send your message. Please try again later.";
}

// Remove CSRF token and CAPTCHA numbers after submission
unset($_SESSION['csrf_token']);
unset($_SESSION['captcha_num1']);
unset($_SESSION['captcha_num2']);
?>


