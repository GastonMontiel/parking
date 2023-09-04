
<?php 
    if(isset($_POST['color'])){ 
        $selected_color= $_POST['color'];

        try {
            $dbh = new PDO('mysql:host=localhost;dbname=parking', 'root', '');

            $stmt = $dbh->prepare("INSERT INTO colors (color) VALUES(?)");

            try {
                $dbh->beginTransaction();
                $stmt->execute( array($_POST['color']));
                $dbh->commit();
            } catch(PDOExecption $e) {
                $dbh->rollback();
                print "Error!: " . $e->getMessage() . "</br>";
            }
        } catch( PDOExecption $e ) {
            print "Error!: " . $e->getMessage() . "</br>";
        }
    };

    try {
        $dbh = new PDO('mysql:host=localhost;dbname=parking', 'root', '');

        try {
            $stmt_brands = $dbh->query("SELECT * FROM brands")->fetchAll();
            $stmt_models = $dbh->query("SELECT * FROM models")->fetch();
            $stmt_colors = $dbh->query("SELECT * FROM colors")->fetchAll();
            $stmt_spaces = $dbh->query("SELECT * FROM spaces")->fetchAll();
        } catch(PDOExecption $e) {
            print "Error!: " . $e->getMessage() . "</br>";
        }
    } catch( PDOExecption $e ) {
        print "Error!: " . $e->getMessage() . "</br>";
    }
?>



<!DOCTYPE html>
<html class="   ">
    <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300&display=swap" rel="stylesheet"> 
        <title>PARKING</title>
    </head>
    <body>
        <div class="background">       
         <?php if(empty($stmt_brands) || empty($stmt_models) || empty($stmt_colors)) :?>
                <div class="container-form">
                    <div class="result">
                    <p>
                            Para poder ingresar un vehiculo al parking debe generar caracteristicas.
                    </p> 

                    <?php if(empty($stmt_brands)): ?>
                        <p> Debe tener al menos una Marca. </p> 
                        <?php endif ?>

                        <?php if(empty($stmt_models)): ?>
                        <p> Debe tener al menos un modelo. </p> 
                        <?php endif ?>

                        <?php if(empty($stmt_colors)): ?>
                        <p> Debe tener al menos un color. </p>  
                        <?php endif ?>

                        <a href="/characteristics">Generar caracteristicas</a>
                    </div>
                </div>
            <?php else: ?>

                <div class="card-style">
                    <p>PARKING</p>
                        <?php foreach( $stmt_spaces as $key=> $value){ ?>
                            <?php
                                $id_to_calculate = ($value['id'] - 1)
                            ?>
                            <?php if( $id_to_calculate % 10 == 0): ?>
                                <details>
                                    <summary class="cursor-pinter">Piso: <?=  ($id_to_calculate / 10) + 1?></summary>
                                    <div class="floor">
                            <?php endif ?>

                                <?php if($id_to_calculate % 5 == 0): ?>
                                        <div>
                                <?php endif ?>
                            
                                    <div class="p-1">
                                        <p> Lugar: <?= $value['id'] ?> </p> 
                                        <div class="cursor-pinter"  tabindex="0" role="button" aria-pressed="false" onclick="openFormWithSpaceId('<?php echo $value['id'];?>','<?php echo ($id_to_calculate / 10) + 1?>', this)" > Reservar </div> 
                                    </div>

                                <?php if(($id_to_calculate + 1) % 5 == 0): ?>
                                        </div>
                                <?php endif ?>
                            
                            <?php if(($id_to_calculate + 1) % 10 == 0): ?>
                                    </div>
                                </details>
                            <?php endif ?>
                        <?php } ?>

                </div>
            <?php endif ?>

            <div id="reserve-space-container" class="card-style mt-2 display-none">

                <form action="" onsubmit="vehicleDataByLicencePlate(event)">
                    <div class="mb-2 flex flex-column"> 
                        <label for="licencePlate">ingrese la matricula:</label> 
                        <input name="licencePlate" required id="licence-plate" class="mt-2 mb-2" type="text">
                        <span id="licence-plate-error" style="color: red;"></span>
                        <input type="submit" class="button-submit input-radius" name="submit">
                    </div>
                </form>

                <form id="reserve-space-form"  action="" method="post">
                    <p class="mb-3 fs-1" id="space-title"></p>
                    <input name="spaceId" required id="input-space-id" type="hidden" value="">

                        <div class="mb-3 flex flex-column"> 
                            <label for="colorSelect">seleccione un color:</label>

                            <select required name="colorId" id="input-color" class="mt-2" id="colorSelect" onchange="selectedColor()">
                                <?php foreach($stmt_colors as $key ){ ?>
                                    <option data-value="<?= $key['color'];?>" class="div-circle" style="background:<?= $key['color'];?>;" value="<?= $key['id'];?>"></option> 
                                <?php } ?>
                            </select>

                            <div class="div-circle" style="background: transparent" id="selected-color"></div>
                        </div>


                        <div class="mb-3 flex flex-column"> 
                            <label for="brandSelect">seleccione una marca:</label>
                            <select required name="brandId" class="mt-2" id="brandSelect">
                                <?php foreach($stmt_brands as $key ){ ?>
                                    <option  value="<?= $key['id'];?>"> <?= $key['brandName'];?></option> 
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3 flex flex-column"> 
                            <label for="brandSelect">fetch para cargar los modelos de la marca:</label>
                            <select required name="brandId" class="mt-2" id="brandSelect">
                                <?php foreach($stmt_brands as $key ){ ?>
                                    <option value="<?= $key['id'];?>"> <?= $key['brandName'];?></option> 
                                <?php } ?>
                            </select>
                        </div>
                </form>
            </div>
        </div>



        <style>
            .background  {
                background-image: url("parking_image.jpg");
                object-fit: cover; 
                height: 90vh;
                background-size: cover;           
                background-repeat:   no-repeat;
                background-position: center center; 
                padding-left: 20px;
                padding-right: 20px;
                padding-top: 80px;
                position: relative;
                overflow: scroll;
            }

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

            .cursor-pinter{
                cursor: pointer;
            }

            .p-1 {
                padding: 5px;
            }

            .mb-2{
                margin-bottom: 10px
            }

            .mb-3{
                margin-bottom: 15px
            }

            .mt-2{
                margin-top: 10px
            }

            .fs-1 {
                font-size: 1.5rem ;
            }

            .fs-2 {
                font-size: 1.3rem ;
            }

            .fs-3 {
                font-size: 1.2rem ;
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

        <script>
            function openFormWithSpaceId(id, floor, e){
                const formsContainer = document.getElementById("reserve-space-container")
                formsContainer.classList.remove("display-none")

                document.querySelector('details[open=""]').removeAttribute('open')
                document.getElementById("input-space-id").value = (id * 1)
                document.getElementById("space-title").innerHTML = `Reservar lugar: ${ (id * 1)}, en el piso: ${ parseInt(floor)}`
            }

            const selectedColor = () => {
                const selectColorHex = document.getElementById("input-color").selectedOptions[0].dataset.value
                const divSelectedColor = document.getElementById("selected-color").setAttribute('style', `background:${selectColorHex};`)
            }

            const modelsByBrandId = () => {
                console.log("recupere  los modelos");
            }

            const vehicleDataByLicencePlate = (e) => {
                e.preventDefault();

                document.getElementById("licence-plate-error").innerHTML = ""
                const formValues = new FormData(e.currentTarget)

                const licencePlate = formValues.get('licencePlate').trim()

                if(!licencePlate){
                    document.getElementById("licence-plate-error").innerHTML = "No puede estar vácio"
                }

                //fetch a la api para obtener mediante find
            }

            const firstLoad = () => {
                selectedColor()
                modelsByBrandId()
            }

            document.addEventListener("DOMContentLoaded", firstLoad);
        </script>

    </body>
</html>

