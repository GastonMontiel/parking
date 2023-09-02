
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

    if(isset($_POST['brandName'])){ 
        $brand_name= $_POST['brandName'];
         try {
             $dbh = new PDO('mysql:host=localhost;dbname=parking', 'root', '');

             $stmt = $dbh->prepare("INSERT INTO brands (brandName) VALUES(?)");

             try {
                 $dbh->beginTransaction();
                 $stmt->execute(array($_POST['brandName']));
                 $dbh->commit();
             } catch(PDOExecption $e) {
                 $dbh->rollback();
                 print "Error!: " . $e->getMessage() . "</br>";
             }
         } catch( PDOExecption $e ) {
             print "Error!: " . $e->getMessage() . "</br>";
         }
    };

    if(isset($_POST['modelName'])){ 
        $modelName= $_POST['modelName'];

         try {
             $dbh = new PDO('mysql:host=localhost;dbname=parking', 'root', '');

             $stmt = $dbh->prepare("INSERT INTO models (modelName, brandId) VALUES(?, ?)");

             try {
                 $dbh->beginTransaction();
                 $stmt->execute(array($_POST['modelName'], $_POST['brandId']));
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

         $stmt = $dbh->query("SELECT * FROM brands");

         try {
            $allBrands = array();
            while($row = $stmt->fetch()) {
                array_push($allBrands, $row);
            };
         } catch(PDOExecption $e) {
             print "Error!: " . $e->getMessage() . "</br>";
         }
     } catch( PDOExecption $e ) {
         print "Error!: " . $e->getMessage() . "</br>";
     }
     
    if(isset($_POST['modelName']) && isset($_POST['brandId'])) {
        $stmt = $dbh->prepare("SELECT brandName FROM brands WHERE id=? LIMIT 1"); 
        $stmt->execute([ $_POST['brandId']]); 
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
    </head>
<body>
<div class="container-form">
    <form name="color_form" class="" action="" method="post">
        <p class="mb-3 fs-1">Crear un color</p>
        
        <div class="mb-2 flex flex-column">
        <label class="" for="input-color">Selecciona un color:</label> 
            <div class="flex  mt-2 items-center">
                <input name="color" class="unstyle " id="input-color" required type="color">
                <input type="submit" class="button-submit submit-color" value="Crear color" name="submit">
            </div>
        </div>
        <div class="result">
            <?php if(isset($_POST['color'])): ?>
                <div class="flex">
                    <p>Se creo el color: </p> <div class="div-circle" style="background: <?= $_POST['color']?>;"></div>
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
            <input type="submit" class="button-submit input-radius" name="submit">
        </div>
        <div class="result">
            <?php if(isset($_POST['brandName'])): ?>
                <div class="flex">
                    <p>Se creo la marca: <?= $_POST['brandName']?></p>
                </div>
            <?php endif ?>
        </div>
     </form>
     <hr>    
     <form name="model_to_brand_form" class="" action="" method="post">
            <p class="mb-3 fs-1">Crear un modelo para una marca</p>

                
          
            <?php if(isset($_POST['modelName'])): ?>
                <div class="result">
                    <?php if(isset($_POST['modelName'])): ?>
                        <div class="flex">
                            <p>Se creo el modelo:  <strong><?= $_POST['modelName']?></strong> para: <?= $brandNameAsociated['brandName'] ?> </p>
                            
                        </div>
                    <?php endif ?>
                </div>
            <?php else: ?>

                <div class="mb-3 flex flex-column"> 
                    <label for="brandSelect">seleccione una marca:</label>
                    <select required name="brandId" class="mt-2" id="brandSelect">
                        <?php foreach($allBrands as $key ){ ?>
                            <option  value="<?= $key['id'];?>"> <?= $key['brandName'];?></option> 

                        <?php } ?>
                    </select>
                </div>

                <div class="mb-2 flex flex-column"> 
                    <label for="modelName">ingrese un modelo:</label> 
                    <input name="modelName" required id="modelName" class="mt-2 mb-2" type="text">
                    <input type="submit" class="button-submit input-radius" name="submit">
                </div>
            <?php endif; ?>
     </form>
</div>


<style>
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

    .items-center {
        align-items: center;
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

    #input-color {
        height: 34px;
        width: 80px;
        height: 40px;
    }

    #input-color::-webkit-color-swatch-wrapper {
        padding: 0; 
    }

    #input-color::-webkit-color-swatch {
        border: none;
        border-radius: 20px 0 0 20px;
    }

    .submit-color{
        margin-left: -3px;
        height: 40px;
        border-radius: 0 4px 4px 0;
    }

    input[type=color] {
        border:none;
        background-image:none;
        background-color:transparent;
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
        box-shadow: none;
    }

    input[type=submit] {
        background: #b6e3ff;
        border: solid 1px #0a49bd;
    }

    input[type=text], input[type=submit],  select {
        height: 40px;
    }

    .unstyle {
     all: unset;
    }

    .container-all-forms {
        width: fit-content;
        padding: 15px;
        border-radius: 8px;
        border: solid 1px black;
    }

    body {
        display: flex;
        justify-content: center;
        font-family: 'Inter', sans-serif;
    }

    .result {
        height: 18px;
    }

    p {
        margin: 0px;
    }

    .input-radius { 
        border-radius: 4px;
    }

    .div-circle {
        border-radius: 50px;
        height: 18px;
        width: 18px;
        margin-left: 18px;
    }

    .container-form {
        padding: 10px;
        border: solid 1px gray;
        border-radius: 4px;
    }

</style>



</body>
</html>

