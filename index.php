<?php
    include_once 'database.php';
    include_once 'model.php';
    include_once 'view.php';
    include_once 'controller.php';

    $db = new Database();
    $model = new Model($db);
    $view = new View();
    $controller = new Controller($db, $model, $view);

    $controller->processRequest();

    if (isset($_GET['action'])) {
        $participant = $_GET['participant'];
        switch ($_GET['action']) {
            case 'startNewStream':
                $controller->startNewStream();
                header('Location: index.php');
                exit;
            case 'stopStream':
                $controller->stopStream();
                header('Location: index.php');
                exit;
            case 'startParticipate':
                $controller->startParticipate($participant);
                header('Location: index.php');
                exit;
            case 'stopParticipate':
                $controller->stopParticipate($participant);
                header('Location: index.php');
                exit;
            case 'sendLike':
                $controller->sendLike();
                header('Location: index.php');
                exit;
            case 'getAllData':
                header('Content-Type: application/json');
                echo json_encode($controller->getAllData());
                exit;
        }
    }

    $controller->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/styles.css">
    <link rel="icon" href="./assets/img/sql.ico" type="image/x-icon">
    <title>ForceWanTest</title>
</head>
<body>
    <?php $controller->globalPage(); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="./assets/script.js"></script>
</body>
</html>
