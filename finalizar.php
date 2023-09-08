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

    $vehicle_stmt = $dbh->prepare("SELECT id FROM vehicles WHERE id = ? AND spaceId IS NULL");
    $vehicle_stmt->execute([$_POST['vehicleId']]);
    $vehicle = $vehicle_stmt->fetch(PDO::FETCH_OBJ);


    if (!empty($space) && !empty($vehicle)) {
        $vehicle_updated =  $dbh->prepare("UPDATE vehicles SET spaceId = ? WHERE id = ?")->execute([$_POST['spaceId'], $_POST['vehicleId']]);
        $space_updated =  $dbh->prepare("UPDATE spaces SET isFree = ? WHERE id = ?")->execute([0, $_POST['spaceId']]);
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
        <!-- <?php if (false) : ?>

        <?php endif ?> -->
        <p class="mb-3 fs-1" id="space-title"></p>
    </div>
</body>

</html>