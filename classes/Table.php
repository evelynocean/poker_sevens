<?php

class Table
{
    protected $onTablesCards = array();
    protected $underTableCards = array();

    /**
     * 桌上的牌
     * @param $card
     */
    public function add($card)
    {
        $this->onTablesCards[] = $card;
    }

    /**
     * 桌上的牌
     * @return array
     */
    public function get_on_table()
    {
        return $this->onTablesCards;
    }

    /**
     * 蓋牌
     * @param $role
     * @param $card
     */
    public function discard($role, $card)
    {
        $this->underTableCards[$role][] = $card;
    }

    /**
     * 取得蓋牌
     * @return array
     */
    public function get_discard()
    {
        return $this->underTableCards;
    }

}