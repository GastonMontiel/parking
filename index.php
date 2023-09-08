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
    $spaces = $dbh->query("SELECT * FROM spaces")->fetchAll(PDO::FETCH_OBJ);
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

    summary {
      display: block;
      list-style: none;
    }

    summary::after {
      display: block;
      list-style: none;
    }

    summary::-webkit-details-marker {
      display: none;
    }

    .floor {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-evenly;
    }

    .card-style {
      background: white;
      border-radius: 8px;
      border: solid 1px gray;
      padding: 10px;
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
      <p>PARKING</p>

      <?php $variableToCalculateFloors = 0; ?>

      <?php foreach ($spaces as $key => $value) : ?>
        <?php if ($variableToCalculateFloors != $value->floor) : ?>
          <?php $variableToCalculateFloors = $value->floor ?>

          <details>
            <summary class="cursor-pinter">Piso: <?= $value->floor ?></summary>
            <div class="floor">
            <?php endif ?>

            <div class="p-1">
              <p> Lugar: <?= $value->id ?> </p>
              <?php if ($value->isFree) : ?>
                <a href="reservar?id=<?= $value->id ?>" class="cursor-pinter" tabindex="0" role="button" aria-pressed="false"> Reservar </a>
              <?php else : ?>
                <form action="" method="post">
                  <input type="hidden" name="freeUpId" value=<?= $value->id ?>>
                  <button>Liberar</button>
                </form>
              <?php endif ?>

            </div>

            <?php $nextSpaceFloor = (array_key_exists($key + 1, $spaces)) ? ($spaces[($key + 1)]->floor) : (6) ?>
            <?php if ($variableToCalculateFloors != $nextSpaceFloor) : ?>
            </div>
          </details>
        <?php endif ?>
      <?php endforeach ?>

    </div>
  <?php endif ?>

  <!-- <script>
    function openFormWithSpaceId(id, floor, e) {
      const formsContainer = document.getElementById("reserve-space-container")
      formsContainer.classList.remove("display-none")

      document.querySelector('details[open=""]').removeAttribute('open')
      document.getElementById("input-space-id").value = id * 1
      document.getElementById("space-title").innerHTML = `Reservar lugar: ${ (id * 1)}, en el piso: ${ parseInt(floor)}`
    }

    const selectedColor = () => {
      const selectColorHex = document.getElementById("input-color").selectedOptions[0].dataset.value
      const divSelectedColor = document.getElementById("selected-color").setAttribute('style', `background:${selectColorHex};`)
    }



    const handleSubmitLicensePlate = (e) => {
      e.preventDefault();

      document.getElementById("licence-plate-error").innerHTML = ""
      const formValues = new FormData(e.currentTarget)

      const licencePlate = formValues.get('licencePlate').trim()

      if (!licencePlate) {
        document.getElementById("licence-plate-error").innerHTML = "No puede estar vacío"
      }

      //fetch a la api para obtener mediante find
    }

    const firstLoad = () => {
      modelsByBrandId()
    }

    document.addEventListener("DOMContentLoaded", firstLoad);
  </script> -->

</body>

</html>