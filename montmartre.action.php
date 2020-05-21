<?php

use GBProd\Montmartre\Application\PaintAction;
use GBProd\Montmartre\Application\PaintHandler;
use GBProd\Montmartre\Domain\Color;
use GBProd\Montmartre\Domain\Muse;

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Montmartre implementation : © <gbprod> <contact@gb-prod.fr>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 */

class action_montmartre extends APP_GameAction
{
    // Constructor: please do not modify
    public function __default()
    {
        if (self::isArg('notifwindow')) {
            $this->view = "common_notifwindow";
            $this->viewArgs['table'] = self::getArg("table", AT_posint, true);
        } else {
            $this->view = "montmartre_montmartre";
            self::trace("Complete reinitialization of board game");
        }
    }

    public function paint()
    {
        self::setAjaxMode();

        $this->game->checkAction('paintAction');

        $cards = array_map(
            [$this, 'createMuseCardFromId'],
            array_filter(
                array_map(
                    'intval',
                    explode(',', self::getArg("cards", AT_numberlist, true))
                )
            )
        );

        $this->game->getContainer()->get(PaintHandler::class)(
            PaintAction::fromMuses(...$cards)
        );

        self::ajaxResponse();
    }

    private function createMuseCardFromId(int $id)
    {
        $value = $id % 9;

        switch (floor($id / 9)) {
            case 0:
                $color = 'green';
                break;
            case 1:
                $color = 'blue';
                break;
            case 2:
                $color = 'pink';
                break;
            case 3:
                $color = 'yellow';
                break;
            default:
                throw new BgaUserException(self::_("Error Processing Request"));
        }

        return Muse::painted(Color::$color(), $value);
    }
}
