<?php

header("Content-Type:application/json");

if (isset($_GET['brandId']) && !empty($_GET['brandId'])) {

  $dbh = new PDO('mysql:host=localhost;dbname=parking', 'root', '');

  try {
    $stmt = $dbh->prepare("SELECT id, modelName FROM models WHERE brandId = ?");
    $stmt->execute([$_GET['brandId']]);
    $models = $stmt->fetchAll(PDO::FETCH_OBJ);
    echo json_encode($models);
    exit;
  } catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => $e->getMessage()]);
    exit;
  }
}

http_response_code(400);
echo json_encode(["message" => "Bad request"]);
exit;
