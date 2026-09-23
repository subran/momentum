<?php
    // Only process POST requests.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form fields and remove whitespace.
        $name    = strip_tags(trim($_POST["name"] ?? ''));
        $name    = str_replace(array("\r","\n"), array(" "," "), $name);
        $email   = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
        $phone   = trim($_POST["phone"] ?? '');
        $subject = trim($_POST["subject"] ?? 'Contact Form Inquiry');
        $message = trim($_POST["message"] ?? '');

        // Check that data was sent to the mailer.
        if (empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Set a 400 (bad request) response code and exit.
            http_response_code(400);
            echo "Please complete the form and try again.";
            exit;
        }

        // Set the recipient email address.
        $recipient = "info@momentumprojects.in";

        // Set the email subject.
        $email_subject = "New Contact from $name: $subject";

        // Build the email content.
        $email_content = "Name: $name\n";
        $email_content .= "Email: $email\n";
        $email_content .= "Phone: $phone\n\n";
        $email_content .= "Message:\n$message\n";

        // Build the email headers.
        $email_headers = "From: $name <$email>";

        // Log to submissions.log for local testing
        $log_entry = date('[Y-m-d H:i:s]') . " " . json_encode([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message
        ]) . PHP_EOL;
        @file_put_contents(__DIR__ . '/contact_submissions.log', $log_entry, FILE_APPEND);

        // Send the email.
        if (@mail($recipient, $email_subject, $email_content, $email_headers)) {
            http_response_code(200);
            echo "Thank You! Your message has been sent successfully.";
        } else {
            // Fallback for local development environments where mail() is not configured
            http_response_code(200);
            echo "Thank You! Your message has been recorded successfully.";
        }

    } else {
        // Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo "There was a problem with your submission, please try again.";
    }
?>
