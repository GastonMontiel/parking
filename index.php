
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
                print $dbh->lastInsertId();
                echo "Color code be saved, code:  " . $selected_color;
            } catch(PDOExecption $e) {
                $dbh->rollback();
                print "Error!: " . $e->getMessage() . "</br>";
            }
        } catch( PDOExecption $e ) {
            print "Error!: " . $e->getMessage() . "</br>";
        }

        
    }
?>

<form action="" method="post">
     Select you favorite color: 
        <?php if(isset($_POST['clr'])): ?>
            <input name="clr" type="color" value="<?= $_POST['clr']?>">
        <?php else: ?>
            <input name="clr" type="color">
        <?php endif; ?>
     <input type="submit" name="submit">
 </form>
