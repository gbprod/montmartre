<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Montmartre implementation : © <gbprod> <contact@gb-prod.fr>.
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * states.inc.php
 *
 * Montmartre game states description
 */
$machinestates = [
    // The initial state. Please do not modify.
    1 => [
        'name' => 'gameSetup',
        'description' => '',
        'type' => 'manager',
        'action' => 'stGameSetup',
        'transitions' => ['' => 2],
    ],

    // Note: ID=2 => your first state

    2 => [
        'name' => 'playerTurn',
        'description' => clienttranslate('${actplayer} could paint or sell'),
        'descriptionmyturn' => clienttranslate('${you} could paint or sell'),
        'type' => 'activeplayer',

        'possibleactions' => [
            'sellAction',
            'paintAction',
        ],
        'transitions' => [
            'drawOrSellOffState' => 3,
            'mustSellOffState' => 4,
            'canBuyGazetteState' => 6,
            'nextPlayer' => 10,
        ],
    ],

    3 => [
        'name' => 'drawOrSellOffState',
        'description' => clienttranslate('${actplayer} could sell off paintings or draw'),
        'descriptionmyturn' => clienttranslate('${you} could sell off paintings, select cards to sell off'),
        'type' => 'activeplayer',

        'possibleactions' => [
            'sellOffAction',
            'drawAction',
        ],
        'transitions' => [
            'nextPlayer' => 10,
            'drawState' => 5,
        ],
    ],

    4 => [
        'name' => 'mustSellOffState',
        'description' => clienttranslate('${actplayer} must sell off paintings'),
        'descriptionmyturn' => clienttranslate('${you} must sell off paintings to have less than 6 paintings, select cards to sell off'),
        'type' => 'activeplayer',

        'possibleactions' => ['sellOffAction'],
        'transitions' => ['drawState' => 5],
    ],

    5 => [
        'name' => 'drawState',
        'description' => clienttranslate('${actplayer} must draw'),
        'descriptionmyturn' => clienttranslate('${you} must draw, select a deck'),
        'type' => 'activeplayer',

        'possibleactions' => [
            'drawAction',
        ],
        'transitions' => [
            'nextPlayer' => 10,
            'drawState' => 5,
        ],
    ],

    6 => [
        'name' => 'canBuyGazetteState',
        'description' => clienttranslate('${actplayer} can buy a newsletter'),
        'descriptionmyturn' => clienttranslate('${you} can buy a newsletter'),
        'type' => 'activeplayer',

        'possibleactions' => [
            'buyGazetteAction',
        ],
        'transitions' => [
            'nextPlayer' => 10,
        ],
    ],

    10 => [
        'name' => 'nextPlayer',
        'description' => '',
        'type' => 'game',
        'action' => 'nextPlayer',
        'updateGameProgression' => true,
        'transitions' => ['playerTurn' => 2],
    ],

    // Final state.
    // Please do not modify (and do not overload action/args methods).
    99 => [
        'name' => 'gameEnd',
        'description' => clienttranslate('End of game'),
        'type' => 'manager',
        'action' => 'stGameEnd',
        'args' => 'argGameEnd',
    ],
];
