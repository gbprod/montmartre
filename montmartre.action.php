<?php

use GBProd\Montmartre\Application\PaintAction;
use GBProd\Montmartre\Application\PaintHandler;
use GBProd\Montmartre\Application\DrawAction;
use GBProd\Montmartre\Application\DrawHandler;
use GBProd\Montmartre\Application\SellOffAction;
use GBProd\Montmartre\Application\SellOffHandler;
use GBProd\Montmartre\Application\NextPlayerAction;
use GBProd\Montmartre\Application\NextPlayerHandler;
use GBProd\Montmartre\Application\SellAction;
use GBProd\Montmartre\Application\SellHandler;
use GBProd\Montmartre\Domain\EmptyDeck;
use GBProd\Montmartre\Domain\Exception\CantPaint2MusesIfSumMoreThan5;
use GBProd\Montmartre\Domain\Exception\CantPaintMoreThan2Muses;
use GBProd\Montmartre\Domain\Color;
use GBProd\Montmartre\Domain\Exception\HandFull;
use GBProd\Montmartre\Domain\Exception\IsBlockedByAmbroise;
use GBProd\Montmartre\Domain\Exception\MuseNotPainted;
use GBProd\Montmartre\Domain\Exception\NoCollectorLeft;
use GBProd\Montmartre\Domain\Exception\ShouldHaveMajority;
use GBProd\Montmartre\Domain\Exception\ShouldPaintAtLeastOneMuse;
use GBProd\Montmartre\Domain\Exception\ShouldSellOffAtLeastOneMuse;
use GBProd\Montmartre\Domain\Exception\TooMuchPaintingsAfterSellOff;
use GBProd\Montmartre\Domain\Muse;
use GBProd\Montmartre\Domain\Exception\MuseNotInHand;

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

        $cards = $this->createMuseCardsFromArg('cards');

        try {
            $this->game->getContainer()->get(PaintHandler::class)(
                PaintAction::fromMuses(...$cards)
            );
        } catch (MuseNotInHand $e) {
            throw new BgaUserException(_("This muse is not in your hand"));
        } catch (CantPaintMoreThan2Muses $e) {
            throw new BgaUserException(_("You can't paint more than 2 muses in one turn"));
        } catch (CantPaint2MusesIfSumMoreThan5 $e) {
            throw new BgaUserException(_("If you paint 2 muses, the sum of their values can't be more than 5"));
        } catch (ShouldPaintAtLeastOneMuse $e) {
            throw new BgaUserException(_("You should select at least one Muse"));
        }

        self::ajaxResponse();
    }

    private function createMuseCardsFromArg(string $arg)
    {
        return array_map(
            [$this, 'createMuseCardFromId'],
            array_map(
                'intval',
                explode(',', self::getArg($arg, AT_numberlist, true))
            )
        );
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
                throw new BgaUserException(_("Error Processing Request"));
        }

        return Muse::painted(Color::$color(), $value);
    }

    public function sellOff()
    {
        self::setAjaxMode();

        $this->game->checkAction('sellOffAction');

        $cards = $this->createMuseCardsFromArg('cards');

        try {
            $this->game->getContainer()->get(SellOffHandler::class)(
                SellOffAction::fromMuses(...$cards)
            );
        } catch (MuseNotPainted $e) {
            throw new BgaUserException(_('You don\'t have painted this muse'));
        } catch (ShouldSellOffAtLeastOneMuse $e) {
            throw new BgaUserException(_('You should sell off at least one painting'));
        } catch (TooMuchPaintingsAfterSellOff $e) {
            throw new BgaUserException(_('You must have a maximum of 6 paintings after sell off'));
        }

        self::ajaxResponse();
    }

    public function draw()
    {
        self::setAjaxMode();

        $this->game->checkAction('drawAction');

        $deck = self::getArg('deck', AT_posint, true);

        try {
            $this->game->getContainer()->get(DrawHandler::class)(
                DrawAction::fromDeckId($deck)
            );
        } catch (HandFull $e) {
            throw new BgaUserException(_('You\'re hand is already full, should not happens'));
        } catch (EmptyDeck $e) {
            throw new BgaUserException(_('This deck is empty, choose another one'));
        }

        self::ajaxResponse();
    }

    public function sell()
    {
        self::setAjaxMode();

        $this->game->checkAction('sellAction');

        $color = self::getArg('color', AT_alphanum, true);

        try {
            $this->game->getContainer()->get(SellHandler::class)(
                SellAction::fromColor($color)
            );
        } catch (NoCollectorLeft $e) {
            throw new BgaUserException(_('No collector left, should not happens'));
        } catch (IsBlockedByAmbroise $e) {
            throw new BgaUserException(_('You couldn\'t sell to a collector blocked by Ambroise Croizat'));
        } catch (ShouldHaveMajority $e) {
            throw new BgaUserException(_('You don\'t have majority, should not happens'));
        }

        self::ajaxResponse();
    }

    public function buyGazette()
    {
        self::setAjaxMode();

        $this->game->checkAction('buyGazetteAction');

        self::ajaxResponse();
    }
}
