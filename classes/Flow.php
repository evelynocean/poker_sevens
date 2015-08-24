<?php

class Flow
{
    public static $members; // 玩家人數
    protected static $hand_cards;  // 目前玩家手牌
    protected static $pointer; // 目前玩家

    const ACTION = 0;
    const ACTION_CALL = 1;
    const ACTION_FOLD = 2;

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
        if( $this->now_player() == static::$members -1 ) {
            static::$pointer = 0;
        } else {
            static::$pointer = $this->now_player() + 1;
        }

        return static::$pointer;
    }

    /**
     * 取得前一位玩家
     * @return int|mixed
     */
    public function move_back()
    {
        if ($this->now_player() == 0) {
            static::$pointer = static::$members -1;
        } else {
            static::$pointer = $this->now_player() - 1;
        }

        return static::$pointer;
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
     * @param      $onTables
     * @param      $action 0=Fold. 1=Call
     * @param null $input
     * @return bool|string
     */
    public function run($onTables, $action = null, $input = null)
    {
        $player = new Player();
        $role = $this->now_player();
        $cards = static::$hand_cards[$role];

        if (count($cards) === 0) return;

        if ($input && ! in_array($input, $cards)) return array("error"=>"input number illegal.");

        $card = '';
        if ($action) { // 指定行為
            if ($action == Flow::ACTION_CALL) {
                // 出牌
                $card = $player->Call($cards, $onTables, $input);
            } elseif ($action == Flow::ACTION_FOLD) {
                // 蓋牌
                $fold_card = $player->Fold($cards, $input);
                $card = ($fold_card) ? "-".$fold_card : false;
            }
        } else { // auto run
            // 出牌
            $card = $player->Call($cards, $onTables, $input);
            if ( ! $card) {
                // 蓋牌
                $fold_card = $player->Fold($cards, $input);
                $card = ($fold_card) ? "-".$fold_card : false;
            }
        }

        if ($card) {
            $this->pop_card($role, $card, static::$hand_cards[$role]);
        }

        return $card;
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
        $card = str_replace("-", "", $card);
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
    public function counting($discards, $command = false)
    {
        $ret = [];
        $cardObj = new Card();
        echo "蓋牌結算 : <br>";
        foreach ($discards as $key => $values) {
            echo '[玩家'.($key+1).'] ：';
            $cardObj->watch_card($values, $command);

            foreach ($values as $val) {
                $ret[$key] += ($val%13 == 0) ? 13 : $val%13;
            }
        }

        arsort($ret);
        $person = array_keys($ret);

        return $person[0];
    }
}