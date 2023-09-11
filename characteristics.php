<?php
if (isset($_POST['color'])) {
    $selected_color = $_POST['color'];

    try {
        $dbh = new PDO('mysql:host=localhost;dbname=parking', 'root', '');

        $stmt = $dbh->prepare("INSERT INTO colors (color) VALUES(?)");

        try {
            $dbh->beginTransaction();
            $stmt->execute(array($_POST['color']));
            $dbh->commit();
        } catch (PDOException $e) {
            $dbh->rollback();
            print "Error!: " . $e->getMessage() . "</br>";
        }
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "</br>";
    }
};

if (isset($_POST['brandName'])) {
    $brand_name = $_POST['brandName'];
    try {
        $dbh = new PDO('mysql:host=localhost;dbname=parking', 'root', '');

        $stmt = $dbh->prepare("INSERT INTO brands (brandName) VALUES(?)");

        try {
            $dbh->beginTransaction();
            $stmt->execute(array($_POST['brandName']));
            $dbh->commit();
        } catch (PDOException $e) {
            $dbh->rollback();
            print "Error!: " . $e->getMessage() . "</br>";
        }
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "</br>";
    }
};

if (isset($_POST['modelName'])) {
    $modelName = $_POST['modelName'];

    try {
        $dbh = new PDO('mysql:host=localhost;dbname=parking', 'root', '');

        $stmt = $dbh->prepare("INSERT INTO models (modelName, brandId) VALUES(?, ?)");

        try {
            $dbh->beginTransaction();
            $stmt->execute(array($_POST['modelName'], $_POST['brandId']));
            $dbh->commit();
        } catch (PDOException $e) {
            $dbh->rollback();
            print "Error!: " . $e->getMessage() . "</br>";
        }
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "</br>";
    }
};

try {
    $dbh = new PDO('mysql:host=localhost;dbname=parking', 'root', '');

    $stmt = $dbh->query("SELECT * FROM brands");

    try {
        $allBrands = array();
        while ($row = $stmt->fetch()) {
            array_push($allBrands, $row);
        };
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "</br>";
    }
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "</br>";
}

if (isset($_POST['modelName']) && isset($_POST['brandId'])) {
    $stmt = $dbh->prepare("SELECT brandName FROM brands WHERE id=? LIMIT 1");
    $stmt->execute([$_POST['brandId']]);
    $brandNameAsociated = $stmt->fetch();
}
?>



<!DOCTYPE html>

<html>

<head>
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

        .justify-content {
            justify-content: center;
        }

        .align-end {
            align-items: end;
        }

        .navbar {
            overflow: hidden;
            background-color: #333;
            position: absolute;
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

        .main {
            padding: 16px;
            margin-top: 30px;
            height: 1500px;
            /* Used in this example to enable scrolling */
        }
    </style>
</head>

<body>
    <div class="navbar">
        <a href="/">Inicio</a>
    </div>
    <div class="card-style">
        <form name="color_form" class="" action="" method="post">
            <p class="mb-3 fs-1">Crear un color</p>

            <div class="mb-2 flex flex-column">
                <label class="" for="input-color">Selecciona un color:</label>
                <input name="color" class="mb-2 mt-2" id="input-color" required type="text">
                <input type="submit" value="Crear color" class="button-style button-blue clean-styles text-center" name="submit">
            </div>

            <div class="result">
                <?php if (isset($_POST['color'])) : ?>
                    <div class="flex">
                        <p>Se creo el color: <?= $_POST['color'] ?></p>
                    </div>
                <?php endif ?>
            </div>
        </form>
        <hr>
        <form name="brandName_form" class="" action="" method="post">
            <p class="mb-3 fs-1">Crear una marca</p>
            <div class="mb-2 flex flex-column">
                <label class="" for="input-brand-name">ingrese una marca:</label>
                <input name="brandName" id="input-brand-name" class="mb-2 mt-2" required type="text">
                <input type="submit" value="Crear marca " class="button-style button-blue clean-styles text-center" name="submit">
            </div>
            <div class="result">
                <?php if (isset($_POST['brandName'])) : ?>
                    <div class="flex">
                        <p>Se creo la marca: <?= $_POST['brandName'] ?></p>
                    </div>
                <?php endif ?>
            </div>
        </form>
        <hr>
        <form name="model_to_brand_form" class="" action="" method="post">
            <p class="mb-3 fs-1">Crear un modelo para una marca</p>


            <div class="mb-3 flex flex-column">
                <label for="brandSelect">seleccione una marca:</label>
                <select required name="brandId" class="mt-2" id="brandSelect">
                    <?php foreach ($allBrands as $key) { ?>
                        <option value="<?= $key['id']; ?>"> <?= $key['brandName']; ?></option>

                    <?php } ?>
                </select>
            </div>

            <div class="mb-2 flex flex-column">
                <label for="modelName">ingrese un modelo:</label>
                <input name="modelName" required id="modelName" class="mt-2 mb-2" type="text">
                <input type="submit" value="Crear modelo " class="button-style button-blue clean-styles text-center" name="submit">
            </div>
            <?php if (isset($_POST['modelName'])) : ?>
                <div class="result">
                    <?php if (isset($_POST['modelName'])) : ?>
                        <div class="flex">
                            <p>Se creo el modelo: <strong><?= $_POST['modelName'] ?></strong> para: <?= $brandNameAsociated['brandName'] ?> </p>

                        </div>
                    <?php endif ?>
                </div>
            <?php endif ?>
        </form>
    </div>




</body>

</html>