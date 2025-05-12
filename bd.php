<?php
// 1. Подключение к базе данных
$servername = "localhost"; // Или IP-адрес вашего сервера базы данных
$username = "root"; // Ваше имя пользователя для доступа к базе данных
$password = "root"; // Ваш пароль
$dbname = "mydatabase"; // Имя вашей базы данных

// Создаем соединение
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверяем соединение
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. SQL-запрос
$sql = "SELECT id, name, image_url FROM desserts"; // Замените "desserts" на имя вашей таблицы
$result = $conn->query($sql);

// 3. Обработка результатов запроса
$desserts = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $desserts[] = array(
            "id" => $row["id"],
            "name" => $row["name"],
            "image" => $row["image_url"]
        );
    }
} else {
    echo "0 results"; // Если нет данных
}

// 4. Форматирование в JSON
header('Content-Type: application/json'); // Устанавливаем заголовок для JSON

// 5. Отправка JSON-ответа
echo json_encode($desserts); // Преобразуем массив PHP в JSON и отправляем

// Закрываем соединение
$conn->close();
?>