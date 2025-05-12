<?php

$dbPath = 'sweet_home.db';

try {

    $db = new SQLite3($dbPath);
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}


$id_dessert = isset($_GET['id_dessert']) ? intval($_GET['id_dessert']) : 1; // Если ID не указан, используем ID 1


$sql = "SELECT cakes.*, category.name AS category_name
        FROM cakes
        INNER JOIN category ON cakes.id_category = category.id_category
        WHERE cakes.id_dessert = " . $id_dessert;
$result = $db->query($sql);


if ($row = $result->fetchArray(SQLITE3_ASSOC)) {

    $dessert = $row;
} else {
    echo "Торт с ID " . $id_dessert . " не найден.";
    exit;
}


$db->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $dessert['name']; ?></title>
</head>
<body>
    <h1><?php echo $dessert['name']; ?></h1>
    <p>Категория: <?php echo $dessert['category_name']; ?></p>
    <img src="" alt="<?php echo $dessert['name']; ?>">
    <p>Бисквит: <?php echo $dessert['biscuit']; ?></p>
    <p>Крем: <?php echo $dessert['cream']; ?></p>
    <p>Начинка: <?php echo $dessert['filling']; ?></p>
    <p>Украшения: <?php echo $dessert['decoration']; ?></p>
    <p>Стоимость торта: <?php echo $dessert['price']; ?> р.</p>
</body>
</html>