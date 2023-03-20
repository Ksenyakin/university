<?php

header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  if (!empty($_GET['save'])) {
    print('
      <p>
        Спасибо, результаты сохранены.
      </p>
      ');
  }
  include('form.php');
  exit();
}

$name = $_POST['name'];
$email = $_POST['email'];
$year = $_POST['year'];
$sex = $_POST['sex'];
$hand = $_POST['hand'];
if(isset($_POST["abilities"]))
  $abilities = $_POST["abilities"];
$biography = $_POST['biography'];
$checkboxContract = isset($_POST['checkboxContract']);

$errors = FALSE;

if (empty($name)) {
  print('
    <h1>
      Заполните имя.
    </h1>
  <br/>');
  $errors = TRUE;
} else if (empty($email)) {
  print('
    <h1>
      Заполните email.
    </h1>
  <br/>');
  $errors = TRUE;
} else if ((2023 - $year) < 14) {
  print('
    <h1>
      Извините, вам должно быть 14 лет.
    </h1>
  <br/>');
  $errors = TRUE;
} else if (empty($abilities)) {
  print('
    <h1>
      Выберите хотя бы одну сверхспособность.
    </h1>
  <br/>');
  $errors = TRUE;
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  print('
    <h1>
      Корректно* заполните email.
    </h1>
  <br/>');
  $errors = TRUE;
} else if (empty($biography)) {
  print('
    <h1>
      Расскажи о себе что-нибудь.
    </h1>
  <br/>');
  $errors = TRUE;
} else if ($checkboxContract == '') {
  print('
    <h1>
      Ознакомьтесь с контрактом.
    </h1>
  <br/>');
  $errors = TRUE;
}

if ($errors) {
  exit();
}

$user = 'u52851';
$pass = '1022160';
$db = new PDO('mysql:host=localhost;dbname=u52855', $user, $pass, array(PDO::ATTR_PERSISTENT => true));

try {
  $stmt = $db->prepare("INSERT INTO application (name, email, year, sex, hand, biography) VALUES ('$name', '$email', '$year', '$sex', '$hand', '$biography')");
  $stmt->execute(['name', 'email', 'year', 'sex', 'hand', 'biography']);
  $application_id = mysqli_fetch_assoc(mysqli_query(mysqli_connect("localhost", $user, $pass, "u52855"), "SELECT MAX(application_id) AS application_id FROM `abilities`"))['application_id'] + 1;
  foreach ($abilities as $superpower_id) {
    $stmt = $db->prepare("INSERT INTO abilities (application_id, superpower_id) VALUES ('$application_id', '$superpower_id')");
    $stmt->execute(['application_id', 'superpower_id']);
  }
} catch (PDOException $e) {
  print('Error : ' . $e->getMessage());
  exit();
}
header('Location: ?save=1');
?>
