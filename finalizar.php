<?php
$dbh = new PDO('mysql:host=localhost;dbname=parking', 'root', '');

if (isset($_POST['spaceId']) && isset($_POST['vehicleId'])) {

  $space_stmt = $dbh->prepare(
    "SELECT
        s.id
        FROM spaces s
        LEFT JOIN vehicles v
        ON s.id = v.spaceId
        WHERE s.id = ? AND v.spaceId IS NULL"
  );
  $space_stmt->execute([$_POST['spaceId']]);
  $space = $space_stmt->fetch(PDO::FETCH_OBJ);

  $vehicle_stmt = $dbh->prepare("SELECT id, licensePlate FROM vehicles WHERE id = ? AND spaceId IS NULL");
  $vehicle_stmt->execute([$_POST['vehicleId']]);
  $vehicle = $vehicle_stmt->fetch(PDO::FETCH_OBJ);


  if (!empty($space) && !empty($vehicle)) {
    $vehicle_updated =  $dbh->prepare("UPDATE vehicles SET spaceId = ? WHERE id = ?")->execute([$_POST['spaceId'], $_POST['vehicleId']]);
    $space_updated =  $dbh->prepare("UPDATE spaces SET isFree = ? WHERE id = ?")->execute([0, $_POST['spaceId']]);
    $success = "Se reservo el lugar {$_POST['spaceId']}, para la matrícula: {$vehicle->licensePlate}";
  } else {
    $error = 'El lugar no puede estar ocupado o el vehículo no debe estar en otro lugar.';
  }
} else if (isset($_POST['licensePlate']) && isset($_POST['colorId']) && isset($_POST['brandId']) && isset($_POST['modelId']) && isset($_POST['spaceId'])) {

  $stmt = $dbh->prepare(
    "SELECT
        (SELECT id FROM vehicles WHERE licensePlate = ?) AS vehicle,
        (SELECT id FROM colors WHERE id = ?) AS color,
        (SELECT id FROM brands WHERE id = ?) AS brand,
        (SELECT id FROM models WHERE id = ?) AS model,
        (SELECT
                s.id
                FROM spaces s
                LEFT JOIN vehicles v
                ON s.id = v.spaceId
                WHERE s.id = ? AND v.spaceId IS NULL) AS space"
  );
  $stmt->execute([$_POST['licensePlate'], $_POST['colorId'], $_POST['brandId'], $_POST['modelId'], $_POST['spaceId']]);
  $all_to_validate = $stmt->fetch(PDO::FETCH_OBJ);


  if (empty($all_to_validate->vehicle) && !empty($all_to_validate->color) && !empty($all_to_validate->brand) && !empty($all_to_validate->model) && !empty($all_to_validate->space)) {
    $dbh->prepare("INSERT INTO vehicles (licensePlate, brandId, colorId, modelId, spaceId) VALUES (?, ?, ?, ?, ?)")->execute([$_POST['licensePlate'], $_POST['colorId'], $_POST['brandId'], $_POST['modelId'], $_POST['spaceId']]);
    $space_updated = $dbh->prepare("UPDATE spaces SET isFree = ? WHERE id = ?")->execute([0,  $_POST['spaceId']]);
    $success = "Se creo el vehículo con matrícula: {$_POST['licensePlate']} y se reservo el lugar {$_POST['spaceId']}";
  } else {
    $error = 'El vehículo no debe existir, debe tener un color, marca, modelo que existan';
  }
} else {
  header("Location: /");
  die();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300&display=swap" rel="stylesheet">
  <title>PARKING</title>
  <style>
    *,
    *::after,
    *::before {
      box-sizing: border-box;
    }

    html,
    body {
      height: 100%;
      overflow-y: auto;
      display: flex;
      justify-content: center;
    }

    body {
      margin: 0;
      font-family: "Inter", sans-serif;
      background-image: url("parking_image.jpg");
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center center;
      padding-left: 20px;
      padding-right: 20px;
      padding-top: 80px;
    }

    details {
      width: 285px;
    }


    details[open]>summary {
      background-color: #dcdcdc;
      border-bottom: solid 1px gray;
    }

    summary {
      display: block;
      list-style: none;
      padding: 10px;
      border: solid 1px gray;
      border-bottom: none;
    }

    summary:hover {
      background-color: #f2f2f2;
    }

    .clean-styles {
      all: unset;
    }

    .floors {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    details:last-of-type {
      border-bottom: solid 1px gray;
    }


    summary::after {
      display: block;
      list-style: none;
    }

    summary::-webkit-details-marker {
      display: none;
    }

    .space {
      text-align: center;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .space p {
      margin: 0;
      margin-bottom: 5px;
    }

    .button-style {
      padding: 5px;
      border-radius: 4px;
      width: 70px;
    }

    .button-green {
      background: #2e9e4b;
      color: white;
      font-weight: 400;
    }

    .button-green:hover {
      background: #008030;
    }

    .button-red {
      background: #ff3616;
      color: white;
      font-weight: 400;
    }

    .button-red:hover {
      background: #ec0001;
    }

    .button-blue {
      background: #5eafe9;
      color: white;
      font-weight: 400;
    }

    .button-blue:hover {
      background: #4491c8;
    }

    .search-button-height {
      height: 37px;
    }

    .input-styles {
      margin-right: 10px;
      box-sizing: border-box;
      border: 2px solid #ccc;
      border-radius: 4px;
      font-size: 16px;
      background-color: white;
      background-position: 10px 10px;
      background-repeat: no-repeat;
      padding: 12px 20px 12px 40px;
    }

    .floor {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-evenly;
      margin: 0 5px 5px 5px;
      border: solid 1px gray;
      border-top: none;
      background: #f2f2f2;
      gap: 10px;
    }

    .card-style {
      background: white;
      border-radius: 8px;
      border: solid 1px gray;
      padding: 10px;
      margin-bottom: 20px;
      width: 400px;
      height: fit-content;
    }

    .tooltip {
      position: relative;
      display: inline-block;
    }

    .tooltip .tooltiptext {
      visibility: hidden;
      width: 120px;
      background-color: #555;
      color: #fff;
      text-align: center;
      border-radius: 6px;
      padding: 5px 0;
      position: absolute;
      z-index: 1;
      bottom: 125%;
      left: 50%;
      margin-left: -60px;
      opacity: 0;
      transition: opacity 0.3s;
    }

    .tooltip .tooltiptext::after {
      content: "";
      position: absolute;
      top: 100%;
      left: 50%;
      margin-left: -5px;
      border-width: 5px;
      border-style: solid;
      border-color: #555 transparent transparent transparent;
    }

    .tooltip:hover .tooltiptext {
      visibility: visible;
      opacity: 1;
    }

    .cursor-pinter {
      cursor: pointer;
    }

    .p-1 {
      padding: 5px;
    }

    .mb-2 {
      margin-bottom: 10px
    }

    .mb-3 {
      margin-bottom: 15px
    }

    .mt-2 {
      margin-top: 10px
    }

    .fs-1 {
      font-size: 1.5rem;
    }

    .fs-2 {
      font-size: 1.3rem;
    }

    .fs-3 {
      font-size: 1.2rem;
    }

    .text-center {
      text-align: center;
    }

    .card-error {
      background: #ffa372;
      border: solid 1px #ec0001;
    }

    .card-success {
      background: #8bf1a3;
      border: solid 1px #2e9e4b;

    }

    .div-circle {
      border-radius: 50px;
      height: 18px;
      width: 18px;
      margin-left: 18px;
    }

    .display-none {
      display: none;
    }

    .flex {
      display: flex;
    }

    .flex-column {
      flex-direction: column;
    }

    .justify-content {
      justify-content: center;
    }

    .align-end {
      align-items: end;
    }

    .navbar {
      overflow: hidden;
      background-color: #333;
      position: fixed;
      top: 0;
      width: 100%;
    }

    .navbar a {
      float: left;
      display: block;
      color: #f2f2f2;
      text-align: center;
      padding: 14px 16px;
      text-decoration: none;
      font-size: 17px;
    }

    .navbar a:hover {
      background: #ddd;
      color: black;
    }
  </style>
</head>

<body>

  <div class="navbar">
    <a href="/">Inicio</a>
    <a href="/characteristics">Agregar caracteristicas</a>
  </div>

  <?php if (!empty($error)) : ?>
    <div id="reserve-space-container" class="card-style card-error">
      <p class="mb-3 fs-1" id="space-title">
        <?= $error ?>
      </p>
    </div>
  <?php endif ?>

  <?php if (!empty($success)) : ?>
    <div id="reserve-space-container" class="card-style card-success">
      <p class="mb-3 fs-1" id="space-title">
        <?= $success ?>
      </p>
    </div>
  <?php endif ?>

</body>

</html>