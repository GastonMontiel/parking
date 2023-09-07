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
  <div id="reserve-space-container" class="card-style">
    <p class="mb-3 fs-1" id="space-title">Reservar lugar: <?= $_GET["id"] ?> (piso: <?= $space->floor ?>)</p>

    <?php if ($vehicle) { ?>
      <form id="reserve-space-form" action="/finalizar.php" method="post">
        <input name="spaceId" type="hidden" value="<?= $_GET["id"] ?>">
        <input name="vehicleId" type="hidden" value="<?= $vehicle->id ?>">
        <p><?= $vehicle->licensePlate ?></p>
        <p><?= $vehicle->brand ?></p>
        <p><?= $vehicle->model ?></p>
        <p><?= $vehicle->color ?></p>
        <input type="submit" class="button-submit input-radius" name="submitLicensePlate" value="Reservar">
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
        <div class="mb-2 flex flex-column">
          <label for="licensePlate">Matrícula:</label>
          <input name="licensePlate" required id="license-plate" class="mt-2 mb-2" type="text">
          <span id="license-plate-error" style="color: red;"></span>
          <input type="submit" class="button-submit input-radius" name="submitLicensePlate" value="Buscar">
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