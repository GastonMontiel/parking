<?php 
if(isset($_POST['action'])){
    echo "perdi";
}
?>

<!-- Simple pop-up dialog box, containing a form -->
<dialog id="favDialog">
    <form action="" method="post">
        <label for="name">Your name:</label>
        <input name="name" id="name" type="text">

        <label for="age">Your age:</label>
        <input name="age" id="age" type="number">

        <button type="submit" name="action" value="submit">Submit</button>
    </form>
</dialog>

<menu>
  <button id="updateDetails">Update details</button>
</menu>

<script>
  (function () {
    var updateButton = document.getElementById("updateDetails");
    var cancelButton = document.getElementById("cancel");
    var favDialog = document.getElementById("favDialog");

    // Update button opens a modal dialog
    updateButton.addEventListener("click", function () {
      favDialog.showModal();
    });

    // Form cancel button closes the dialog box
    cancelButton.addEventListener("click", function () {
      favDialog.close();
    });
  })();
</script>
