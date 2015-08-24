<?php
include_once('../start/autoload.php');

$players = array("玩家1", "玩家2", "玩家3", "玩家4", "玩家5");

// 發牌
$cardObj = new Card();
$cards = $cardObj->create();
$cards = $cardObj->shuffle($cards);
$groups =$cardObj->Dealer(count($players), $cards);

//echo json_encode($groups).'<br>';

// suit
$suit = $cardObj->suit();


// testing 顯示各家手牌
foreach ($groups as $key => $val) {
    fwrite(STDOUT, '['.$players[$key].']');
    sort($val);
    $cardObj->watch_card($val, true);
}
fwrite(STDOUT, '--------------------------------------');

// 遊戲開始
$flow = new Flow();
$tab = new Table();
$flow::$members = count($players);
// 第一手
$card = $flow->fire($groups);
$tab->add($card);

fwrite(STDOUT, $players[$flow->now_player()].'出牌: '.$suit[$card]);

for ($i = 0; $i < 55; $i++) {
    $flow->move_next();
    fwrite(STDOUT, " || ".$players[$flow->now_player()]."請出牌：");

    $str = trim(fgets(STDIN));
    if (empty($str)) {
        fwrite(STDOUT, "Empty input str, try again.");
        $flow->move_back();
        $i--;
        continue;
    }
    $tmp = explode(",", $str); // (Call=1|Fold=2),SUIT(0|1|2|3),NUMBER
    $action = $tmp[0];
    $input = $tmp[1]*13 + $tmp[2];

    $card = $flow->run($tab->get_on_table(), $action, $input);
    if (isset($card['error'])) {
        fwrite(STDOUT, $card['error'].": ".$tmp[2]." try again.");
        $flow->move_back();
        $i--;
        continue;
    }

    if (! $card) {
        fwrite(STDOUT, " || ".$players[$flow->now_player()]."：PASS! ");
        continue; // 沒牌了
    }

    if ($card > 0) {
        $tab->add($card);
        $show_card = $suit[$card];
        fwrite(STDOUT, " || ".$players[$flow->now_player()]."出牌：".$show_card);
    } else {
        $tab->discard($flow->now_player(), -intval($card));
        $show_card = $suit[-intval($card)];
        fwrite(STDOUT, " || ".$players[$flow->now_player()]."蓋牌：".$show_card);
    }

    // test
    foreach ($flow->hands() as $key => $val) {
        fwrite(STDOUT, '['.$players[$key].']');
        sort($val);
        $cardObj->watch_card($val, true);
    }
    fwrite(STDOUT, '--------------------------------------');
}

// 結果
$loser = $flow->counting($tab->get_discard(), true);
echo "LOSER : ".$players[$loser];


?>