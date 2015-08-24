<?php

class Card
{
    const SPADE = '♠';  // 1～13
    const HEART = '♥';  // 14～26
    const DIAMOND = '♦'; // 27～39
    const CLUD = '♣';    // 40 ～ 52

    /**
     * 花色內容對照表
     * @return array
     */
    public function suit()
    {
        $style = array(
            Card::SPADE,
            Card::HEART,
            Card::DIAMOND,
            Card::CLUD
        );

        $poker = [];
        foreach ($style as $key => $val) {
            for ($i = 1 ; $i <= 13; $i++) {
                $poker[($key*13)+ $i] = $val.' '.$i;
            }
        }

        return $poker;
    }

    /**
     * 產生所有牌
     * @return array
     */
    public function create()
    {
        for ($i = 1 ; $i <= 52; $i++) {
            $poker[] = $i;
        }

        return $poker;
    }

    /**
     * 洗牌
     * @param $cards
     * @return bool
     */
    public function shuffle($cards)
    {
        shuffle($cards);
        // 隨機排序
        return $cards;
    }

    /**
     * 發牌
     * @param int $players
     * @param     $cards
     * @return array
     */
    public function Dealer($players = 4, $cards)
    {
        $ret = [];
        // 取最近滿足的整數
        $round = floor(count($cards) / $players);

        $i = 0;
        $tail = [];
        for ($j = 0; $j < count($cards); $j++) {
            if ($i >= $players) {
                $tail[] = $cards[$j];
                continue;
            }
            $ret[$i][] = $cards[$j];
            if (count($ret[$i]) == $round) $i++;
        }

        foreach ($tail as $key => $val) {
            $ret[$key][] = $val;
        }

        return $ret;
    }

    /**
     * 列出花色牌面
     * @param $cards
     */
    public function watch_card($cards, $command = null)
    {
        $mapping = $this->suit();

        if ( ! $command) {
            foreach ($cards as $kk) {
                echo '<label style="background-color: lightgray;padding: 2px;">';
                if ($kk >= 14 && $kk <= 26) {
                    echo '<font color="red">' . $mapping[$kk] . '</font>';
                } elseif ($kk >= 27 && $kk <= 39) {
                    echo '<font color="orange">'.$mapping[$kk].'</font>';
                } else {
                    echo $mapping[$kk];
                }
                echo '</label> &emsp;';
            }
            echo '<br>';
        } else {
            foreach ($cards as $kk) {
                fwrite(STDOUT, $mapping[$kk]);
            }
        }


    }
}