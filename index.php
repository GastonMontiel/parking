
<?php 
    if(isset($_POST['clr'])){ 
        $selected_color= $_POST['clr'];

        try {
            $dbh = new PDO('mysql:host=localhost;dbname=parking', 'root', '');

            $stmt = $dbh->prepare("INSERT INTO colors (color) VALUES(?)");

            try {
                $dbh->beginTransaction();
                $stmt->execute( array($_POST['clr']));
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
?>

<div>
    <form name="color_form" action="" method="post">
         Crear un color: 
            <?php if(isset($_POST['clr'])): ?>
                <input name="clr" type="color" value="<?= $_POST['clr']?>">
            <?php else: ?>
                <input name="clr" type="color">
            <?php endif; ?>
         <input type="submit" name="submit">
     </form>

     <form name="brandName_form" action="" method="post">
         Crear una marca: 
            <?php if(isset($_POST['brandName'])): ?>
                <input name="brandName" disabled type="text" value="<?= $_POST['brandName']?>">
            <?php else: ?>
                <input name="brandName" type="text">
            <?php endif; ?>
         <input type="submit" name="submit">
     </form>

     <form name="model_to_brand_form" action="" method="post">
         Crear una modelo para una marca: 
            <?php if(isset($_POST['modelName'])): ?>
                <input name="modelName" type="text" value="<?= $_POST['modelName']?>">
                <input name="brandId" type="text" value="<?= $_POST['brandName']?>">
                //hacer el join para obetener la marca a la qeu se asocio el modelo
            <?php else: ?>

                <div>
                    <label for="brandSelect">seleccione una marca:</label>
                    <select required name="brandId" id="brandSelect">
                        <?php foreach($allBrands as $key ){ ?>
                            <option  value="<?= $key['id'];?>"> <?= $key['brandName'];?></option> 

                        <?php } ?>
                    </select>
                </div>

                <div>
                    <label for="modelName">ingrese un modelo:</label> 
                    <input name="modelName" required id="modelName" type="text">
                </div>
            <?php endif; ?>
         <input type="submit" name="submit">
     </form>

</div>
