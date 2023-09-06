<?php
header("Content-Type:application/json");
if (isset($_GET['brandId']) && $_GET['brandId'] != "") {

  $dbh = new PDO('mysql:host=localhost;dbname=parking', 'root', '');

  try {

    $sql = "SELECT modelName, id FROM models WHERE brandId = :brandId";

    try {
      $statement = $dbh->prepare($sql);
      $statement->bindParam(':brandId', $_GET['brandId'], PDO::PARAM_INT);
      $statement->execute();
      $models = $statement->fetchAll(PDO::FETCH_ASSOC);
      if ($models) {
        response($models, 200);
      } else {
        response([], 200);
      }
    } catch (PDOException $e) {
      $dbh->rollback();
      print "Error!: " . $e->getMessage() . "</br>";
    }
  } catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "</br>";
  }
} else {
  response(NULL, NULL, 400, "Invalid Request");
}

function response($models_list, $response_code)
{
  $response['models'] = $models_list;
  $response['response_code'] = $response_code;

  $json_response = json_encode($response);
  echo $json_response;
}
