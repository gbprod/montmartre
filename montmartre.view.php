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
 * montmartre.view.php
 *
 * This is your "view" file.
 *
 * The method "build_page" below is called each time the game interface is displayed to a player, ie:
 * _ when the game starts
 * _ when a player refreshes the game page (F5)
 *
 * "build_page" method allows you to dynamically modify the HTML generated for the game interface. In
 * particular, you can set here the values of variables elements defined in montmartre_montmartre.tpl (elements
 * like {MY_VARIABLE_ELEMENT}), and insert HTML block elements (also defined in your HTML template file)
 *
 * Note: if the HTML of your game interface is always the same, you don't have to place anything here.
 *
 */

use GBProd\Montmartre\Infrastructure\BoardRepository;

require_once(APP_BASE_PATH . 'view/common/game.view.php');

class view_montmartre_montmartre extends game_view
{
    public function getGameName()
    {
        return 'montmartre';
    }

    public function build_page($viewArgs)
    {
        $this->tpl['trans_your_hand'] = self::_('Your hand');
        $this->tpl['trans_your_paintings'] = self::_('Your paintings');
        $this->tpl['current_player_id'] = $this->game->currentPlayerId();

        $this->page->begin_block('montmartre_montmartre', 'plaintings_block');

        $players = $this->game->players();
        $currentId = $this->game->currentPlayerId();
        foreach ($players as $id => $player) {
            if ($id === $currentId) {
                continue;
            }

            $this->page->insert_block('plaintings_block', [
                'trans_player_paintings' => self::_(sprintf('%s paintings', $player['player_name'])),
                'player_id' => $player['player_id'],
            ]);
        }
    }
}
