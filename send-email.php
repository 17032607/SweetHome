<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Настройки
$to = "ketrin.alex@gmail.com";  
$subject = "Новый заказ торта с сайта";
$from_email = "ketrin.alex@gmail.com"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Получаем и очищаем данные
        $name = htmlspecialchars($_POST['name'] ?? ''); // ?? '' - значение по умолчанию, если поле отсутствует
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $phone = htmlspecialchars($_POST['phone'] ?? '');
        $delivery_date = htmlspecialchars($_POST['delivery_date'] ?? '');
        $location = htmlspecialchars($_POST['location'] ?? '');
        $comment = htmlspecialchars($_POST['comment'] ?? '');
        $biscuit = htmlspecialchars($_POST['biscuit'] ?? '');
        $cream = htmlspecialchars($_POST['cream'] ?? '');
        $filling = htmlspecialchars($_POST['filling'] ?? '');
        $decoration = htmlspecialchars($_POST['decoration'] ?? '');

        // Валидация
        if (empty($name) || empty($email) || empty($phone) || empty($delivery_date) || empty($location)) {
            throw new Exception("Пожалуйста, заполните все обязательные поля.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Неверный формат email.");
        }

        // Загрузка изображения (если есть)
        $uploadedImage = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileSize = $_FILES['image']['size'];
            $fileType = $_FILES['image']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $newFileName = md5(time() . $fileName) . '.' . $fileExtension; // Generate a unique name

            // Allow certain file formats
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

            if (in_array($fileExtension, $allowedfileExtensions)) {
                //set_error_handler(function($severity, $message, $file, $line){
                //    throw new ErrorException($message, 0, $severity, $file, $line);
                //});
                $upload_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $newFileName;

                if (move_uploaded_file($fileTmpPath, $upload_path)) {
                    $uploadedImage = $upload_path; // Save the path to the uploaded image
                } else {
                    throw new Exception("Ошибка при загрузке файла.");
                }
            } else {
                throw new Exception('Разрешены только файлы с расширениями .jpg, .jpeg, .png и .gif.');
            }
        }

        // Сообщение (HTML)
        $messageBody = "
            <html>
            <head>
                <title>Новый заказ торта</title>
            </head>
            <body>
                <p>Имя: " . htmlspecialchars($name) . "</p>
                <p>Email: " . htmlspecialchars($email) . "</p>
                <p>Телефон: " . htmlspecialchars($phone) . "</p>
                <p>Дата доставки: " . htmlspecialchars($delivery_date) . "</p>
                <p>Место доставки: " . htmlspecialchars($location) . "</p>
                <p>Комментарий: " . htmlspecialchars($comment) . "</p>
                <p>Бисквит: " . htmlspecialchars($biscuit) . "</p>
                <p>Крем: " . htmlspecialchars($cream) . "</p>
                <p>Наполнение: " . htmlspecialchars($filling) . "</p>
                <p>Украшения: " . htmlspecialchars($decoration) . "</p>";

        if ($uploadedImage) {
            $messageBody .= "<p>Изображение: <img src='cid:image' style='max-width: 200px;'></p>";
        }

        $messageBody .= "</body>
            </html>";

        // PHPMailer setup
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = 0; // Enable verbose debug output (set to 2 for detailed debugging)
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'ketrin.alex@gmail.com';                   // SMTP username
            $mail->Password   = 'your_smtp_password';                   // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_STARTTLS` encouraged
            $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom($from_email, 'Web Form');  // Your web server's email address
            $mail->addAddress($to);  // Main recipient
            $mail->addReplyTo($email, $name);

            // Content
            $mail->isHTML(true);  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $messageBody;
            $mail->AltBody = strip_tags($messageBody); // Optional: Plain text version

            if ($uploadedImage) {
                $mail->addEmbeddedImage($uploadedImage, 'image');
            }
            $mail->send();

            http_response_code(200);
            echo "success";
        } catch (Exception $e) {
            http_response_code(500);
            echo "Ошибка отправки сообщения: {$mail->ErrorInfo}";
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo "Ошибка: " . $e->getMessage();
    }
} else {
    http_response_code(405);
    echo "Метод не разрешен";
}
?>