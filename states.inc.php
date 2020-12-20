<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Montmartre implementation : © <gbprod> <contact@gb-prod.fr>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * states.inc.php
 *
 * Montmartre game states description
 *
 */

$machinestates = [

    // The initial state. Please do not modify.
    1 => [
        'name' => 'gameSetup',
        'description' => '',
        'type' => 'manager',
        'action' => 'stGameSetup',
        'transitions' => ['' => 2]
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
            'pickOrSellOffState' => 3,
            'mustSellOffState' => 4,
            'nextPlayer' => 5,
        ]
    ],

    3 => [
        'name' => 'pickOrSellOffState',
        'description' => clienttranslate('${actplayer} could sell off paintings or pick'),
        'descriptionmyturn' => clienttranslate('${you} could sell off paintings, select cards to sell off'),
        'type' => 'activeplayer',

        'possibleactions' => [
            'sellOffAction',
            'pickAction'
        ],
        'transitions' => [
            'pickState' => 6,
            'nextPlayer' => 5
        ]
    ],

    4 => [
        'name' => 'mustSellOffState',
        'description' => clienttranslate('${actplayer} must sell off paintings'),
        'descriptionmyturn' => clienttranslate('${you} must sell off paintings to have less than 6 paintings, select cards to sell off'),
        'type' => 'activeplayer',

        'possibleactions' => ['sellOffAction'],
        'transitions' => ['pickState' => 6]
    ],

    5 => [
        'name' => 'nextPlayer',
        'description' => '',
        'type' => 'game',
        'action' => 'nextPlayer',
        'updateGameProgression' => true,
        'transitions' => ['playerTurn' => 2],
    ],

    6 => [
        'name' => 'pickState',
        'description' => clienttranslate('${actplayer} must pick'),
        'descriptionmyturn' => clienttranslate('${you} must pick, select a deck'),
        'type' => 'activeplayer',

        'possibleactions' => [
            'pickAction'
        ],
        'transitions' => [
            'nextPlayer' => 5,
        ]
    ],

   /*
        Examples:

        2 => array(
            'name' => 'nextPlayer',
            'description' => '',
            'type' => 'game',
            'action' => 'stNextPlayer',
            'updateGameProgression' => true,
            'transitions' => array( 'endGame' => 99, 'nextPlayer' => 10 )
        ),

        10 => array(
            'name' => 'playerTurn',
            'description' => clienttranslate('${actplayer} must play a card or pass'),
            'descriptionmyturn' => clienttranslate('${you} must play a card or pass'),
            'type' => 'activeplayer',
            'possibleactions' => array( 'playCard', 'pass' ),
            'transitions' => array( 'playCard' => 2, 'pass' => 2 )
        ),

    */

    // Final state.
    // Please do not modify (and do not overload action/args methods).
    99 => [
        'name' => 'gameEnd',
        'description' => clienttranslate('End of game'),
        'type' => 'manager',
        'action' => 'stGameEnd',
        'args' => 'argGameEnd'
    ]

];
