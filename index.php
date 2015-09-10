<?php
header("Content-Type:text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title></title>
    <link href="asset/css/lib/jquery-ui-1.11.3.min.css" rel="stylesheet">
    <link href="asset/css/lib/bootstrap.css" rel="stylesheet">
    <link href="asset/css/lib/material.css" rel="stylesheet">
    <link href="asset/css/lib/ripples.css" rel="stylesheet">
    <link href="asset/css/seven.css" rel="stylesheet">
</head>
<body>
<div class="header-panel shadow-z-2">
    <button class="btn btn-primary start_btn">發牌</button>
</div>

<div>
    <div class="col-xs-3 table">
        <nav class="table_content">
            <?php for ($i = 1; $i <= 4; $i++): ?>
            <div class="on_table_suit<?=$i ?>"></div>
            <?php endfor; ?>
        </nav>
    </div>
</div>

<?php foreach (array(1 =>'a', 2 => 'b', 3 => 'c', 4 => 'd') as $key => $val): ?>
<div class="col-xs-3 player player1">
玩家 <?=$val?> <p>
    <div class="card_set_player<?=$key?>"></div>
    <div><input type="checkbox" id="fold_player<?=$key?>" value="<?=$key?>"/>蓋牌
        <div class="fold_player<?=$key?>"></div>
    </div>
</div>
<?php endforeach; ?>

<!--<div id="dialog" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">-->
<!--    <div class="modal-backdrop fade" style="height: 331px;"></div>-->
<!--    <div class="modal-dialog">-->
<!--        <div class="modal-content">-->
<!--            <div class="modal-body">-->
<!--                <p> Test </p>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<script src="asset/js/lib/jquery-1.11.0.min.js"></script>
<script src="asset/js/lib/bootstrap.js"></script>
<script src="asset/js/lib/material.js"></script>
<script src="asset/js/lib/math.min.js"></script>
<script src="asset/js/lib/ripples.js"></script>
<script src="asset/js/lib/underscore.js"></script>
<script src="asset/js/seven.js"></script>
</body>
</html>