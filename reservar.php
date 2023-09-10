<?php
if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: /");
  die();
}

$dbh = new PDO('mysql:host=localhost;dbname=parking', 'root', '');

$stmt = $dbh->prepare("SELECT * FROM spaces WHERE id = ? AND isFree");
$stmt->execute([$_GET['id']]);
$space = $stmt->fetch(PDO::FETCH_OBJ);

if (empty($space)) {
  header("Location: /");
  die();
}


if (isset($_POST['licensePlate'])) {
  $stmt = $dbh->prepare(
    "SELECT 
      v.id,
      v.licensePlate,
      b.brandName AS brand,
      c.color,
      m.modelName AS model	
    FROM vehicles v
    INNER JOIN brands b ON v.brandId = b.id
    INNER JOIN colors c ON v.colorId = c.id
    INNER JOIN models m ON v.modelId = m.id
    WHERE licensePlate = ?"
  );
  $stmt->execute([$_POST['licensePlate']]);
  $vehicle = $stmt->fetch(PDO::FETCH_OBJ);

  if (!$vehicle) {
    $brands = $dbh->query("SELECT * FROM brands")->fetchAll(PDO::FETCH_OBJ);

    $models_stmt = $dbh->prepare("SELECT * FROM models WHERE brandId = ?");
    $models_stmt->execute([$brands[0]->id]);
    $models = $models_stmt->fetchAll(PDO::FETCH_OBJ);

    $colors = $dbh->query("SELECT * FROM colors")->fetchAll(PDO::FETCH_OBJ);
  }
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

    select {
      width: 100%;
      padding: 16px 20px;
      border: none;
      border-radius: 4px;
      background-color: #f1f1f1;
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

    .justify-center {
      justify-content: center;
    }

    .justify-between {
      justify-content: space-between;
    }

    .align-end {
      align-items: end;
    }

    .px-4 {
      padding-left: 20px;
      padding-right: 20px;
    }
  </style>
</head>

<body>
  <div id="reserve-space-container" class="card-style">
    <p class="mb-3  fs-1 text-center" id="space-title">Reservar lugar: <?= $_GET["id"] ?> (piso: <?= $space->floor ?>)</p>

    <?php if (!empty($vehicle)) { ?>
      <form id="reserve-space-form" action="/finalizar.php" method="post">
        <input name="spaceId" type="hidden" value="<?= $_GET["id"] ?>">
        <input name="vehicleId" type="hidden" value="<?= $vehicle->id ?>">
        <div class="flex justify-between px-4">
          <div>
            <p>Matrícula:</p>
            <p>Marca:</p>
            <p>Modelo:</p>
            <p>Color:</p>
          </div>
          <div class="">
            <p> <?= $vehicle->licensePlate ?></p>
            <p><?= $vehicle->brand ?></p>
            <p><?= $vehicle->model ?></p>
            <p> <?= $vehicle->color ?></p>
          </div>
        </div>
        <div class="flex justify-center">
          <input type="submit" class="button-style button-green clean-styles text-center search-button-height" name="submitLicensePlate" value="Reservar">
        </div>
      </form>
    <?php } elseif (isset($_POST['licensePlate'])) { ?>
      <form id="reserve-space-form" action="/finalizar.php" method="post">
        <input name="spaceId" type="hidden" value="<?= $_GET["id"] ?>">
        <input name="licensePlate" type="hidden" value="<?= $_POST['licensePlate'] ?>">

        <div class="mb-3 flex flex-column">
          <label for="colorSelect">seleccione un color:</label>

          <select required name="colorId" id="input-color" class="mt-2" id="colorSelect">
            <?php foreach ($colors as $key) { ?>
              <option value="<?= $key->id; ?>"> <?= $key->color ?> </option>
            <?php } ?>
          </select>
        </div>

        <div class="mb-3 flex flex-column">
          <label for="brandSelect">Seleccione una marca:</label>
          <select required name="brandId" class="mt-2" id="brandSelect" onchange="handleChangeBrand(event)">
            <?php foreach ($brands as $key) { ?>
              <option value="<?= $key->id; ?>"> <?= $key->brandName; ?></option>
            <?php } ?>
          </select>
        </div>

        <div class="mb-3 flex flex-column">
          <label for="modelSelect">Seleccione un modelo:</label>
          <select required name="modelId" class="mt-2" id="modelSelect">
            <?php foreach ($models as $value) { ?>
              <option value="<?= $value->id; ?>"> <?= $value->modelName; ?></option>
            <?php } ?>
          </select>
        </div>

        <input type="submit" class="button-submit input-radius" name="submitLicensePlate" value="Reservar">
      </form>
    <?php } else { ?>
      <form action="" method="post">
        <div class="mb-2 flex align-end">
          <div class="flex flex-column ">
            <label for="licensePlate">Matrícula:</label>
            <input name="licensePlate" required id="license-plate" class=" clean-styles input-styles" type="text">
          </div>

          <span id="license-plate-error" style="color: red;"></span>
          <input type="submit" class="button-style button-blue clean-styles text-center search-button-height" name="submitLicensePlate" value="Buscar">
        </div>
      </form>
    <?php } ?>
  </div>

  <script>
    const handleChangeBrand = async (e) => {
      const brandId = e.target.value
      document.getElementById("modelSelect").innerHTML = `<option disabled value="">Cargando</option>`
      document.getElementById("modelSelect").value = ""
      try {
        const url = new URL("/api.php", window.location.href)
        url.searchParams.set("brandId", brandId)
        const response = await fetch(url, {
          headers: {
            'Content-Type': 'application/json'
          }
        })
        if (!response.ok) {
          return;
        }
        const models = await response.json()

        document.getElementById("modelSelect").innerHTML = models.map(model => `<option value="${model.id}">${model.modelName}</option>`).join("")
      } catch (error) {
        // empty
      }
    }
  </script>
</body>

</html>