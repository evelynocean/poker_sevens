<?php

class Player
{
    /**
     * 第一手牌
     * @param $cards
     * @return mixed
     */
    public function first_pop($cards)
    {
        $section = array_intersect($cards, array(20));
        sort($section);

        return ($section) ? $section[0] : false;
    }

    /**
     * 出牌
     * @param $group
     * @param $onTable
     * @return bool
     */
    public function Call($group, $onTable, $input = null)
    {
        $on_tables = $this->hitTable($onTable);

        if ($input) {
            if (in_array($input, $on_tables) || in_array($input, array(7, 33, 46))) {
                return $input;
            } else {
                return array("error" => "Call : illegal input.");
            }
        }
        // 與桌上牌有交集的先出
        $section = [];
        if (array_intersect($group, $on_tables)) {
            $section = array_intersect($group,  $on_tables);
        // 7 先出
        } elseif (array_intersect($group, array(7, 33, 46)) ) {
            $section = array_intersect($group, array(7, 33, 46));
        }

        rsort($section);

        return $section[0];
    }

    /**
     * 蓋牌
     * @param $group
     * @return mixed
     */
    public function Fold($group, $input)
    {
        if ($input) return $input;

        if ($input) {
            return (in_array($input, $group)) ? $input : array("error" => "Fold : illegal input.");
        }

        sort($group);
        if(count($group) == 1) return $group[0];

        arsort($group);

        $new = [];
        foreach ($group as $val) {
            $section = ($val%13 > 7 || $val%13 == 0) ? 'upper' : 'lower';
            $idx = ($val%13 === 0) ? (floor($val/13) - 1) : floor($val/13);
            $new[$idx][$section][] = ($val%13 == 0) ? $val : $val%13;
        }

        // 計算權重
        $result = [];
        for($i = 0 ; $i < count($new); $i++) {
            $result += $this->weight($i, $new[$i]);
        }
        asort($result);
        $fold = array_keys($result);

        return $fold[0];
    }

    /**
     * 可出牌範圍
     * @param $onTables
     * @return array
     */
    private function hitTable($onTables)
    {
        $ret = [];
        foreach ($onTables as $val) {
            if (($val - 1) > 0) $ret[] = $val - 1;
            if (($val + 1) <= 52 ) $ret[] = $val + 1;
        }

        return array_unique($ret);
    }


    /**
     * 計算蓋牌權重
     * @param $tag
     * @param $cards
     * @return array
     */
    private function weight($tag, $cards)
    {
        $ret = [];
        if (isset($cards['upper']) && is_array($cards['upper'])) {
            $ret += $this->weight_process($tag, $cards['upper']);
        }

        if (isset($cards['lower']) && is_array($cards['lower'])) {
            asort($cards['lower']);
            $ret += $this->weight_process($tag, $cards['lower']);
        }

        return $ret;
    }

    /**
     * 計算蓋牌權重
     * @param $tag
     * @param $cards
     * @return array
     */
    private function weight_process($tag, $cards)
    {
        $ret = [];
        $sum = 0;

        foreach ($cards as $key => $val) {
            if ($sum == 0) {
                $ret[$val + ($tag*13)] = $val * count($cards);
                $sum += $val;
                continue;
            }
            $sum += $val;
            $ret[$val + ($tag*13)] = $sum * count($cards);
        }

        return $ret;
    }
}