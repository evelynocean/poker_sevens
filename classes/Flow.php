<?php

class Flow
{
    public static $members; // 玩家人數
    protected static $hand_cards;  // 目前玩家手牌
    protected static $pointer; // 目前玩家

    /**
     * 取得目前玩家
     * @return mixed
     */
    public function now_player()
    {
        return static::$pointer;
    }

    /**
     * 取得下一位玩家
     * @return int|mixed
     */
    public function move_next()
    {
        $next = $this->now_player() + 1;
        if( $next > (static::$members -1 ) ) {
            static::$pointer = 0;

            return 0;
        } else {
            static::$pointer = $next;

            return $next;
        }
    }

    /**
     * 開始遊戲
     * @param $groups
     * @return mixed
     */
    public function fire($groups)
    {
        $player = new Player();
        static::$hand_cards = $groups;
        foreach ($groups as $role => $val) {
            if ($player->first_pop($groups[$role])) {
                static::$pointer = $role;
                // 記住開始值, pop之後該位子就會被清掉了
                $start_value = $player->first_pop($groups[$role]);
                $this->pop_card($role, $start_value, static::$hand_cards[$role]);

                return $start_value;
            }
        }
    }

    /**
     * 輪流出牌
     * @param $onTables
     * @return bool|string
     */
    public function run($onTables)
    {
        $player = new Player();
        $role = $this->now_player();
        $cards = static::$hand_cards[$role];

        if (count($cards) == 0) return;

        // 出牌
        if ($player->Call($cards, $onTables)) {
            $this->pop_card($role, $player->Call($cards, $onTables), static::$hand_cards[$role]);

            return $player->Call($cards, $onTables);
        } else {
            // 蓋牌
            $this->pop_card($role, $player->Fold($cards), static::$hand_cards[$role]);

            return ($player->Fold($cards)) ? "-".$player->Fold($cards) : false;
        }
    }

    /**
     * 紀錄各家手牌
     * @return mixed
     */
    public function hands()
    {
        return static::$hand_cards;
    }

    /**
     * 移除手牌
     */
    private function pop_card($role, $card, $hands)
    {
        $key = array_search($card, $hands);
        unset(static::$hand_cards[$role][$key]);

        if (count(static::$hand_cards[$role]) == 0) {
            unset(static::$hand_cards[$role]);
        }
    }

    /**
     * 結算
     * @param $discards
     */
    public function counting($discards)
    {
        $ret = array();
        $cardObj = new Card();
        echo "蓋牌結算 : <br>";
        foreach ($discards as $key => $values) {
            echo '['.($key+1).'] > ';
            $cardObj->watch_card($values);

            foreach ($values as $val) {
                $ret[$key] += ($val%13 == 0) ? 13 : $val%13;
            }
        }

        arsort($ret);
        $person = array_keys($ret);

        return $person[0];
    }
}