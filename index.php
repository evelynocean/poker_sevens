<?php

include_once('start/autoload.php');

// 玩家
$players = array("玩家1", "玩家2", "玩家3", "玩家4", "玩家5");

// 發牌
$cardObj = new Card();
$cards = $cardObj->create();
$cards = $cardObj->shuffle($cards);
$groups =$cardObj->Dealer(count($players), $cards);
// suit
$suit = $cardObj->suit();

// testing
foreach ($groups as $key => $val) {
    echo '['.$players[$key].'] &emsp;';
    sort($val);
    $cardObj->watch_card($val);
}
echo '<hr>';

// 遊戲開始
$flow = new Flow();
$tab = new Table();
$flow::$members = count($players);
// 第一手
$card = $flow->fire($groups);
$tab->add($card);
echo $players[$flow->now_player()].' : '.$suit[$card].'<br>';

//$card = $flow->run($tab->onTable());
//if ($card > 0) {
//    $tab->add($card);
//} else {
//    $tab->add_underTable($flow->now_player(), $card);
//}

for ($i = 0; $i < 55; $i++) {
    $flow->move_next();
    $card = $flow->run($tab->get_on_table());

    if (! $card) {
        echo '<pre>'; print_r($card);
        continue; // 沒牌了
    }

    if ($card > 0) {
        $tab->add($card);
        $show_card = $suit[$card];
    } else {
        $tab->discard($flow->now_player(), -intval($card));
        $show_card = "<font color='blue'>".$suit[-intval($card)]."</font>";
    }

    echo '['.$i.'] '.$players[$flow->now_player()].' : '.$show_card.'<br>';
    // test
    foreach ($flow->hands() as $key => $val) {
        echo '['.$players[$key].'] &emsp;';
        sort($val);
        $cardObj->watch_card($val);
    }
    echo '<hr>';
}

// 結果
$loser = $flow->counting($tab->get_discard());
echo "LOSER : ".$players[$loser];