<?php
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO contacts (name, mobile, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $mobile, $email);

    if ($stmt->execute()) {
        echo "New record created successfully";
        // Send email notification
        require 'PHPMailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;

        $mail->isSMTP();
        $mail->Host = 'smtp.example.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@example.com';
        $mail->Password = 'your-email-password';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your-email@example.com', 'Real Estate');
        $mail->addAddress('recipient@example.com');

        $mail->isHTML(true);

        $mail->Subject = 'New Contact Form Submission';
        $mail->Body    = "Name: $name<br>Mobile: $mobile<br>Email: $email";

        if (!$mail->send()) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
