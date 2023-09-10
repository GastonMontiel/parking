<?php
$dbh = new PDO('mysql:host=localhost;dbname=parking', 'root', '');

if (isset($_POST['color'])) {
  $selected_color = $_POST['color'];

  try {
    $stmt = $dbh->prepare("INSERT INTO colors (color) VALUES (?)");
    $stmt->execute(array($_POST['color']));
  } catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "</br>";
  }
} else if (isset($_POST["submitLicencePlate"])) {
  $licence_plate = $_POST["licencePlate"];

  if (empty($licence_plate)) {
    $error = [
      "field" => "licencePlate",
      "details" => "required"
    ];
  } else {
    $stmt = $dbh->prepare("SELECT * FROM vehicles WHERE licencePlate = ?");
    $stmt->execute([$licence_plate]);
    $vehicle = $stmt->fetch(PDO::FETCH_OBJ);
  }
}

if (isset($vehicle)) {
  echo "<pre>";
  var_dump($vehicle);
  echo "</pre>";
}

if (isset($error)) {
  echo "<pre>";
  var_dump($error);
  echo "</pre>";
}

if (isset($_POST["freeUpId"])) {

  $vehicle_stmt = $dbh->prepare("SELECT id FROM vehicles WHERE spaceId = ?");
  $vehicle_stmt->execute([$_POST["freeUpId"]]);
  $vehicle_out = $vehicle_stmt->fetch(PDO::FETCH_OBJ);
  if (!empty($vehicle_out)) {
    $space_updated = $dbh->prepare("UPDATE spaces SET isFree = ? WHERE id = ?")->execute([1,  $_POST["freeUpId"]]);
    $vehicle_updated =  $dbh->prepare("UPDATE vehicles SET spaceId = ? WHERE id = ?")->execute([null, $vehicle_out->id]);
  }
}

if (!isset($_GET["id"])) {
  $counts = $dbh->query("SELECT (SELECT COUNT(*) FROM brands) AS brands, (SELECT COUNT(*) FROM colors) AS colors, (SELECT COUNT(*) FROM models) AS models")->fetch(PDO::FETCH_OBJ);
  $hasInfo = $counts->brands > 0 && $counts->colors > 0 && $counts->models > 0;
  if ($hasInfo) {
    $spaces = $dbh->query(
      "SELECT
        s.id, s.isFree, s.floor, v.licensePlate
        FROM spaces s
        LEFT JOIN vehicles v
        ON s.id = v.spaceId"
    )->fetchAll(PDO::FETCH_OBJ);
  }

  $freeSpacesByFloor = [];
  foreach ($spaces as $space) {
    if ($space->isFree) {
      if (!isset($freeSpacesByFloor[$space->floor])) {
        $freeSpacesByFloor[$space->floor] = 0;
      }
      $freeSpacesByFloor[$space->floor]++;
    }
  }
} else {

  $licence_plate = $_GET["id"];

  if (empty($licence_plate)) {
    $error = [
      "field" => "id",
      "details" => "required"
    ];
  } else {
    $stmt = $dbh->prepare("SELECT * FROM vehicles WHERE licencePlate = ?");
    $stmt->execute([$licence_plate]);
    $vehicle = $stmt->fetch(PDO::FETCH_OBJ);
  }


  $selected_space = $dbh->query("SELECT * FROM spaces")->fetchAll(PDO::FETCH_OBJ);
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
      width: 100%;
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

    .div-circle {
      border-radius: 50px;
      height: 18px;
      width: 18px;
      margin-left: 18px;
    }

    .display-none {
      display: none;
    }
  </style>
</head>

<body>
  <?php if (!isset($spaces) || empty($spaces)) : ?>
    <div class="container-form">
      <div class="result">
        <p>
          Para poder ingresar un vehiculo al parking debe generar caracteristicas.
        </p>
        <?php if (empty($brands)) : ?>
          <p> Debe tener al menos una Marca. </p>
        <?php endif ?>

        <?php if (empty($models)) : ?>
          <p> Debe tener al menos un modelo. </p>
        <?php endif ?>

        <?php if (empty($colors)) : ?>
          <p> Debe tener al menos un color. </p>
        <?php endif ?>

        <a href="/characteristics">Generar caracteristicas</a>
      </div>
    </div>
  <?php else : ?>
    <div class="card-style">
      <p class="text-center fs-2">PARKING</p>

      <?php $variableToCalculateFloors = 0; ?>
      <div class='floors'>
        <?php foreach ($spaces as $key => $value) : ?>

          <?php if ($variableToCalculateFloors != $value->floor) : ?>
            <?php $variableToCalculateFloors = $value->floor ?>
            <?php $freeSpaces = isset($freeSpacesByFloor[$value->floor]) ? $freeSpacesByFloor[$value->floor] : 0; ?>
            <details>
              <summary class="cursor-pinter">Piso: (<?= $value->floor ?>) lugares libres: (<?= $freeSpaces ?>)</summary>
              <div class="floor">
              <?php endif ?>

              <div class="p-1 space">
                <p> Lugar: <?= $value->id ?> </p>
                <p> </p>

                <?php if ($value->isFree) : ?>
                  <a href="reservar?id=<?= $value->id ?>" class="cursor-pinter button-style button-green clean-styles" tabindex="0" role="button" aria-pressed="false"> Reservar </a>

                  <strong class=""> - </strong>
                <?php else : ?>
                  <form action="" method="post">
                    <input type="hidden" name="freeUpId" value=<?= $value->id ?>>
                    <button class="cursor-pinter button-style clean-styles button-red">
                      Liberar
                    </button>
                  </form>


                  <strong class=""><?= $value->licensePlate ?></strong>

                <?php endif ?>

              </div>

              <?php $nextSpaceFloor = (array_key_exists($key + 1, $spaces)) ? ($spaces[($key + 1)]->floor) : (6) ?>
              <?php if ($variableToCalculateFloors != $nextSpaceFloor) : ?>
              </div>
            </details>

          <?php endif ?>
        <?php endforeach ?>
      </div>
    </div>
  <?php endif ?>
</body>

</html>