/**
 * Created by evelyn on 2015/9/8.
 */
"use strict";

var $now_player = '';
var $start = "20";
var $table = [];

var $folder = {
    player1 : [],
    player2 : [],
    player3 : [],
    player4 : []
};

// 取得花色
var _suit = function(i){
    var $suit = (math.floor(i/13) == 0) ? '♠' : (math.floor(i/13) == 1) ? '♥' : (math.floor(i/13) == 2) ? '♦' : '♣';
    var $name = (math.mod(i, 13) == 0) ? 13 : math.mod(i, 13);
    var $color = ($.inArray(math.floor(i/13), [0, 3, 4]) > -1) ? '#000' : 'red';

    return '<font color='+ $color +'>' + $suit + ' ' + $name + '</font>';
};


// 左邊補0
function padLeft(str, length){
    return (str.length >= length) ? str : padLeft('0' + str,length);
}

// 取得 牌
var _template_card = "<button class='btn_card btn_<%= key %>' value='<%= key %>' <%= disabled %> onClick=Call('<%=key%>','<%=owner%>');><%= name %></font></button>";

function get_card_suit(key, owner, disabled){
    var $template = _.template(_template_card);
    var templateData = {
        key: key,
        name: _suit(key),
        owner: owner,
        disabled: disabled
    };

    return $template(templateData);
}

function move_next(number, owner) {
    $('.card_set_player'+ owner).find('.btn_'+ number).remove();
    $("#fold_player"+ owner).prop('checked', false);

    $now_player = ($now_player == 4) ? 1 : $now_player + 1;

    $("div[class^='card_set_']").parent().removeClass('now_player');
    $('.card_set_player' + $now_player).parent().addClass('now_player');
}

function you_can_call() {
    var ok_number = ["07", "33", "46"];
    for (var i = 0 ; i < $table.length; i++) {
        var $val = parseInt($table[i]);
        if (($val - 1) > 0) ok_number.push(padLeft(($val - 1).toString(), 2));
        if (($val + 1) <= 52 ) ok_number.push(padLeft(($val + 1).toString(), 2));
    }

    return ok_number;
}

function Fold(number, owner) {
    if (owner == 1) $folder.player1.push(number);
    if (owner == 2) $folder.player2.push(number);
    if (owner == 3) $folder.player3.push(number);
    if (owner == 4) $folder.player4.push(number);

    move_next(number, owner);
}

function Call(number, owner) {
    if ($table.length == 0) { // 第一手 只能出 紅心 7
        if (number == 20) {
            $table.push(number);
            move_next(number, owner);
        } else {
            console.log('only 20 can call.');
        }
    } else {
        if (owner == $now_player) {
            if ($("#fold_player"+owner).prop('checked')) {
                // 蓋牌
                Fold(number, owner);
            } else {
                var $oks = you_can_call();
                if ($.inArray(number, $oks) > -1) {
                    $table.push(number);
                    move_next(number, owner);
                } else {
                    console.log('this card cant throw');
                }
            }
        } else {
            console.log('not your turn.')
        }
    }

    var tab = new Table();
    tab.onTable();
    tab.underTable();
}

function Table() {
    this.onTable = function() {
        $table.sort();
        $("div[class^='on_table_suit']").find('label').remove();

        for (var i = 0 ; i < $table.length; i++) {
            $('.on_table_suit' + math.ceil($table[i]/13)).append('<label>'+ _suit(parseInt($table[i]))+'</label>');
        }
    };

    this.underTable = function() {
        for (var key in $folder){
            if ($folder.hasOwnProperty(key)) {
                var $target = $('.fold_'+key);
                $target.find('button').remove();
                for (var i = 0; i < $folder[key].length ; i++) {
                    $target.append(get_card_suit($folder[key][i], parseInt(key.substr(-1)), 'disabled'));
                }
            }
        }
    };
}

function Card() {
    var cardObj = this;
    cardObj.init();

    $(".start_btn").click(function() {
        var cards = cardObj.create();
        cards = cardObj.shuffle(cards);
        var players = cardObj.dealer(cards);
        cardObj.assign_to(players);
    });
}

Card.prototype = {
    init: function() {
        $(".player").hide();
    },
    create: function() {
        var cards = [];
        for (var i = 1; i <= 52; i ++) {
            cards.push(padLeft(i.toString(),2));
        }

        return cards;
    },
    shuffle: function(cards){
        for(var i = 0; i < cards.length; i++) {
            var j = parseInt(Math.random() * cards.length - 1);
            var tmp = cards[i];
            cards[i] = cards[j];
            cards[j] = tmp;
        }
        return cards.slice(0, cards.length);
    },
    dealer: function(cards) {
        var players = {
            player1: [],
            player2: [],
            player3: [],
            player4: []
        };

        for (var j = 0; j < cards.length; j++) {
            if (math.mod(j, 4) == 0) {
                players.player1.push(cards[j]);
            } else if (math.mod(j, 4) == 1) {
                players.player2.push(cards[j]);
            } else if (math.mod(j, 4) == 2) {
                players.player3.push(cards[j]);
            } else if (math.mod(j, 4) == 3) {
                players.player4.push(cards[j]);
            }
        }

        return players;
    },
    assign_to: function(players) {
        for (var key in players){
            if (players.hasOwnProperty(key)) {
                var target = $('.card_set_'+ key);
                target.find('button').remove();
                target.parent().removeClass('now_player');
                for (var i = 0; i < players[key].length ; i++) {
                    if (players[key][i] === $start) {
                        $now_player = parseInt(key.substr(-1));
                        target.parent().addClass('now_player');
                    }
                    target.append(get_card_suit(players[key][i], parseInt(key.substr(-1)), ''));
                }
            }
        }
        $(".player").show();
    }
};

new Card();
new Table();