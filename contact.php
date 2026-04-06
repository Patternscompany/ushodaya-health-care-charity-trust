<?php
// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './vendor/autoload.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $subject_val = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    $terms = isset($_POST['terms']) ? 'Accepted' : 'Not Accepted';

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration for Microsoft 365 Email (GoDaddy)
        $mail->isSMTP();
        $mail->Host = 'localhost'; // Replace with your SMTP server address
        $mail->SMTPAuth = false;
        $mail->Username = 'noreply@ushodayacharity.org'; // Replace with your SMTP username
        $mail->Password = 'pass'; // Replace with your SMTP password
        $mail->Port = 25;




        // Email settings
        $mail->setFrom('noreply@ushodayacharity.org', 'Ushodaya Charity Laser Eye Hospital Contact Form'); // Replace with your email and name
        $mail->addAddress('ushodayacharitylasereyehospital@gmail.com'); // Add recipient's email

        $mail->Subject = "Message from $name";
        $mail->isHTML(true);
        $mailContent = "<div style='font-family: Arial, sans-serif; background:#f4f6f8; padding:20px;'>

    <div style='max-width:600px; margin:auto; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.1);'>

        <div style='background:#BE2A4B; color:#ffffff; padding:15px; text-align:center; font-size:20px; font-weight:bold;'>
            Ushodaya Charity Laser Eye Hospital - contact form
        </div>

        <div style='padding:20px;'>

            <table style='width:100%; border-collapse:collapse; font-size:15px;'>

                <tr>
                    <td style='padding:10px; border-bottom:1px solid #eee; font-weight:bold;'>Name :</td>
                    <td style='padding:10px; border-bottom:1px solid #eee;'>$name</td>
                </tr>

                <tr>
                    <td style='padding:10px; border-bottom:1px solid #eee; font-weight:bold;'>Email :</td>
                    <td style='padding:10px; border-bottom:1px solid #eee;'>$email</td>
                </tr>

                <tr>
                    <td style='padding:10px; border-bottom:1px solid #eee; font-weight:bold;'>Phone :</td>
                    <td style='padding:10px; border-bottom:1px solid #eee;'>$phone</td>
                </tr>

                <tr>
                    <td style='padding:10px; border-bottom:1px solid #eee; font-weight:bold;'>Subject :</td>
                    <td style='padding:10px; border-bottom:1px solid #eee;'>$subject_val</td>
                </tr>
                <tr>
                    <td style='padding:10px; border-bottom:1px solid #eee; font-weight:bold;'>Terms Accepted :</td>
                    <td style='padding:10px; border-bottom:1px solid #eee;'>$terms</td>
                </tr>
                <tr>
                    <td style='padding:10px; font-weight:bold;'>Message :</td>
                    <td style='padding:10px;'>$message</td>
                </tr>

            </table>

        </div>

        <div style='background:#f1f1f1; text-align:center; padding:12px; font-size:12px; color:#666;'>
            This enquiry was submitted from Ushodaya Charity website contact form.
        </div>

    </div>

</div>";
        $mail->Body = $mailContent;

        // Send the email
        if ($mail->send()) {
            echo "Email has been sent successfully.";
            header('Location: thank-you.html'); // Redirect to 'thank-you.html'
            exit;
        } else {
            echo "Email could not be sent.";
        }
    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
} else {
    // Redirect to 'index.html' if accessed without POST
    header('Location: index.html');
    exit;
}
