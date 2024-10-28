<?php
function printHeader($active){
    // associate url with Title
    $sites = array(
        "index" => "Übersicht",
        "einnahmen" => "Einnahmen",
        "ausgaben" => "Ausgaben",
        "konten" => "Konten",
        "settings" => "Einstellungen",
        "static"=> "S"
    );
    echo '<div class="row w-100">
            <h1 class="py-2 px-4 col-11 pr prh pe-2">One of the Tools of all Time</h1>
            <div class="col-1 justify-content-end p-0">
                <form class="w-fit-content mx-auto" method="post" action="">
                    <input type="hidden" name="logout" value="true" /> 
                    <a class="end-0 button btn btn-outline-primary my-3 bg-pr-h bc p-1" onclick="this.parentNode.submit();"><img alt="exit pictogram" src="assets/img/exit.svg" width="30px"> </a>
                </form>
            </div>
        </div>
        <ul class="nav nav-tabs ps-2 bc">';
    // navbar with tabs
    foreach($sites as $url => $title){
        echo '<li class="nav-item">';
        if($active == $url){
            echo '<a class="nav-link active pr-h bc" aria-current="page" href="#">'.$title.'</a>';
        } else {
            echo '<a class="nav-link pr-h tc bc-h" href="'.$url.'">'.$title.'</a>';
        }
        echo '</li>';
    }
    echo '</ul>';
}

function printFooter($startTime){
    $endTime = microtime(true);
    $executionTime = ff($endTime - $startTime);
    global $auth;
    echo "<div class=\"container\">
            <div class=\"row py-2\">
                <div class=\"col tc\">
                PHP Time: $executionTime s | Auth: $auth</div>
                </div>
            </div>
        </div>
   ";
}

function head($title){
    echo '<head>
        <meta charset="utf-8">
        <meta lang="de">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="assets/scripts/3rdparty/bootstrap-5.3.2.min.css" rel="stylesheet">
        <script src="assets/scripts/3rdparty/bootstrap-5.3.2.bundle.min.js"></script>
        <script src="assets/scripts/3rdparty/jquery-3.7.1.min.js"></script>
        <title>'.$title.'</title>
        <link rel="stylesheet" href="assets/styles.css">
        <link rel="stylesheet" href="assets/theme.css">
        <link rel="icon" href="favicon.png">
    </head>';
}

function ff($float){
    // format numbers as 1.234,56
    return number_format($float, "2", ",", ".");
}



function monthlyBudgetSelector(){
    echo '<div class="form-check">
    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
    <label class="form-check-label" for="flexRadioDefault1">
        <input type="text" class="w-25 border rounded bga">
        Fester Betrag [€]
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" checked>
    <label class="form-check-label" for="flexRadioDefault2">
    <input type="text" class="w-25 border rounded bga">
      Dynamisch [%]
    </label>
  </div>';
}

