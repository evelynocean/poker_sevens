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
echo json_encode($groups).'<br>';
//$groups = json_decode('[[14,3,47,6,20,33,17,51,9,11,29],[21,13,7,5,34,8,40,10,30,12,31],[32,16,43,28,45,42,15,36,26,25],[23,38,52,22,39,27,2,44,24,49],[35,41,48,4,37,1,19,46,50,18]]');
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

for ($i = 0; $i < 55; $i++) {
    $flow->move_next();
    $card = $flow->run($tab->get_on_table());

    if (! $card) {
        echo '['.$i.'] '.$players[$flow->now_player()].' : PASS ! <br>';
        continue; // 沒牌了
    }

    if ($card > 0) {
        $tab->add($card);
        $show_card = $suit[$card];
    } else {
        $tab->discard($flow->now_player(), -intval($card));
        $show_card = "<font color='blue'>蓋牌 ".$suit[-intval($card)]."</font>";
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