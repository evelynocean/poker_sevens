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

        return $section[0];
    }

    /**
     * 出牌
     * @param $group
     * @param $onTable
     * @return bool
     */
    public function Call($group, $onTable)
    {
        // 與桌上牌有交集的先出
        if (array_intersect($group, $this->hitTable($onTable))) {
            $section = array_intersect($group,  $this->hitTable($onTable));
            rsort($section);

            return $section[0];
        // 7 先出
        } elseif (array_intersect($group, array(7, 33, 46))) {
            $section = array_intersect($group, array(7, 33, 46));
            rsort($section);

            return $section[0];
        } else {
            // 無牌可出
            return false;
        }
    }

    /**
     * 蓋牌
     * @param $group
     * @return mixed
     */
    public function Fold($group)
    {
        sort($group);
        if(count($group) == 1) return $group[0];

        arsort($group);
        $new = array();
        foreach ($group as $val) {
            $section = ($val%13 > 7 || $val%13 == 0) ? 'upper' : 'lower';
            $new[floor($val/13)][$section][] = $val%13;
        }

        // 計算權重
        $result = array();
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
        $ret = array();
        foreach ($onTables as $val) {
            if (($val - 1) > 0) $ret[] = $val - 1;
            if (($val + 1) <= 52 ) $ret[] = $val + 1;
        }

        return array_unique($ret);
    }


    /**
     * 計算蓋牌權重
     *
     * @param $tag
     * @param $cards
     * @return array
     */
    private function weight($tag, $cards)
    {
        $ret = array();
        if (isset($cards['upper']) && is_array($cards['upper'])) {
            $ret += $this->weight_process($tag, $cards['upper']);
        }

        if (isset($cards['lower']) && is_array($cards['lower'])) {
            asort($cards['lower']);
            $ret += $this->weight_process($tag, $cards['lower']);
        }

        return $ret;
    }

    private function weight_process($tag, $cards)
    {
        $ret = array();
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