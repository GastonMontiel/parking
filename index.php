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

if (!isset($_GET["id"])) {
  $counts = $dbh->query("SELECT (SELECT COUNT(*) FROM brands) AS brands, (SELECT 0) AS colors, (SELECT COUNT(*) FROM models) AS models")->fetch(PDO::FETCH_OBJ);
  $hasInfo = $counts->brands > 0 && $counts->colors > 0 && $counts->models > 0;
  if ($hasInfo) {
    $spaces = $dbh->query("SELECT * FROM spaces")->fetchAll(PDO::FETCH_OBJ);
  }
}

// try {
//   $brands = $dbh->query("SELECT * FROM brands")->fetchAll();
//   $models = $dbh->query("SELECT * FROM models")->fetch();
//   $colors = $dbh->query("SELECT * FROM colors")->fetchAll();
//   $spaces = $dbh->query("SELECT * FROM spaces")->fetchAll();
// } catch (PDOException $e) {
//   print "Error!: " . $e->getMessage() . "</br>";
// }
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
    body {
      margin: 0;
      overflow: hidden;
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

    .background {
      background-image: url("parking_image.jpg");
      object-fit: cover;
      height: 90vh;
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center center;
      padding-left: 20px;
      padding-right: 20px;
      padding-top: 80px;
      position: relative;
      overflow: scroll;
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
  <div class="background">
    <?php if (empty($brands) || empty($models) || empty($colors)) : ?>
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
    <?php elseif (isset($_GET["id"])) : ?>

      <div id="reserve-space-container" class="card-style">


        <p class="mb-3 fs-1" id="space-title">Reservar lugar: <?= $_GET["id"] ?></p>

        <form action="" method="post">
          <div class="mb-2 flex flex-column">
            <label for="licencePlate">Matrícula:</label>
            <input name="licencePlate" required id="licence-plate" class="mt-2 mb-2" type="text">
            <span id="licence-plate-error" style="color: red;"></span>
            <input type="submit" class="button-submit input-radius" name="submitLicencePlate" value="Reservar">
          </div>
        </form>

        <!-- <form id="reserve-space-form" action="" method="post">
  <input name="spaceId" required id="input-space-id" type="hidden" value="">

  <div class="mb-3 flex flex-column">
    <label for="colorSelect">seleccione un color:</label>

    <select required name="colorId" id="input-color" class="mt-2" id="colorSelect">
      <?php foreach ($colors as $key) { ?>
        <option value="<?= $key['id']; ?>"> <?= $key['color'] ?> </option>
      <?php } ?>
    </select>
  </div>


  <div class="mb-3 flex flex-column">
    <label for="brandSelect">seleccione una marca:</label>
    <select required name="brandId" class="mt-2" id="brandSelect" onchange="modelsByBrandId()">
      <?php foreach ($brands as $key) { ?>
        <option value="<?= $key['id']; ?>"> <?= $key['brandName']; ?></option>
      <?php } ?>
    </select>
  </div>

  <div class="mb-3 flex flex-column">
    <label for="brandSelect">fetch para cargar los modelos de la marca:</label>
    <select required name="modelName" id='models-by-brand' class="mt-2" id="brandSelect">
      <?php foreach ($brands as $key) { ?>
        <option value="<?= $key['id']; ?>"> <?= $key['brandName']; ?></option>
      <?php } ?>
    </select>
  </div>
</form> -->
      </div>
    <?php else : ?>
      <div class="card-style">
        <p>PARKING</p>

        <?php $variableToCalculateFloors = 0 ?>

        <?php foreach ($spaces as $key => $value) : ?>
          <?php if ($variableToCalculateFloors != $value['floor']) : ?>
            <?php $variableToCalculateFloors = $value['floor'] ?>

            <details>
              <summary class="cursor-pinter">Piso: <?= $value['floor'] ?></summary>
              <div class="floor">
              <?php endif ?>

              <?php if ($key % 5 == 0) : ?>
                <div>
                <?php endif ?>

                <div class="p-1">
                  <p> Lugar: <?= $value['id'] ?> </p>
                  <a href="?id=<?= $value["id"] ?>" class="cursor-pinter" tabindex="0" role="button" aria-pressed="false"> Reservar </a>
                </div>

                <?php if ($key + 1  % 5 == 0) : ?>
                </div>
              <?php endif ?>


              <?php $nextSpaceFloor = (array_key_exists($key + 1, $spaces)) ? ($spaces[($key + 1)]['floor']) : (6) ?>
              <?php if ($variableToCalculateFloors != $nextSpaceFloor) : ?>
              </div>
            </details>
          <?php endif ?>
        <?php endforeach ?>

      </div>
    <?php endif ?>

  </div>
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

    const modelsByBrandId = () => {
      try {
        const response = fetch('/api.php', {
          headers: {
            'Content-Type': 'application/json'
          }
        })
      } catch (error) {

      }
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