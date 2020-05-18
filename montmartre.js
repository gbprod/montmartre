/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Montmartre implementation : © <gbprod> <contact@gb-prod.fr>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 */

define([
  "dojo",
  "dojo/_base/declare",
  "ebg/core/gamegui",
  "ebg/counter",
  "ebg/stock",
], function (dojo, declare) {
  return declare("bgagame.montmartre", ebg.core.gamegui, {
    constructor: function () {
      console.log("montmartre constructor");
      this.collectorCardWidth = 120;
      this.collectorCardHeight = 221;
      this.gazetteCardWidth = 200;
      this.gazetteCardHeight = 109;
      this.deckCardWidth = 120;
      this.deckCardHeight = 220;
    },

    /**
     * setup:
     *   This method must set up the game user interface according to current game situation specified
     *   in parameters.
     *  The method is called each time the game interface is displayed to a player, ie:
     *  _ when the game starts
     *  _ when a player refreshes the game page (F5)
     *  "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
     */
    setup: function (gamedatas) {
      console.log("Starting game setup");
      console.log(gamedatas);

      this.setupCollectors(gamedatas);
      this.setupGazettes(gamedatas);
      this.setupDecks(gamedatas);
      this.setupPlayerHand(gamedatas);

      this.setupNotifications();

      console.log("Ending game setup");
    },

    setupCollectors: function (gamedatas) {
      this.collectors = {
        green: new ebg.stock(),
        yellow: new ebg.stock(),
        blue: new ebg.stock(),
        pink: new ebg.stock(),
      };

      for (const color of ["green", "yellow", "blue", "pink"]) {
        this.collectors[color].create(
          this,
          $("collectors-" + color),
          this.collectorCardWidth,
          this.collectorCardHeight
        );

        this.collectors[color].image_items_per_row = 5;
        this.collectors[color].item_margin = 0;
        this.collectors[color].setSelectionMode(0);
        this.collectors[color].setOverlap(1.5, 0);
      }

      for (const color of ["green", "yellow", "blue", "pink"]) {
        for (const value of [2, 4, 6, 8, 10]) {
          if (gamedatas.collectors[color] <= value) {
            const cardId = this.collectorCardId(color, value);
            this.collectors[color].addItemType(
              cardId,
              10 - value,
              g_gamethemeurl + "img/collectors.png",
              cardId
            );

            this.collectors[color].addToStock(cardId);
          }
        }
      }
    },

    collectorCardId: function (color, value) {
      switch (color) {
        case "green":
          return value / 2 - 1;
        case "yellow":
          return 5 + value / 2 - 1;
        case "blue":
          return 10 + value / 2 - 1;
        case "pink":
          return 15 + value / 2 - 1;
      }
    },

    setupGazettes: function (gamedatas) {
      this.gazettes = {};

      for (let index = 2; index < [2, 3, 4].length + 2; index++) {
        this.gazettes[index] = new ebg.stock();
        this.gazettes[index].create(
          this,
          $("gazette-" + index),
          this.gazetteCardWidth,
          this.gazetteCardHeight
        );

        this.gazettes[index].image_items_per_row = 2;
        this.gazettes[index].item_margin = 0;

        this.gazettes[index].setSelectionMode(0);
        this.gazettes[index].setOverlap(0.1, 30);
      }

      for (let index = 0; index < gamedatas.gazettes.length; index++) {
        const gazette = gamedatas.gazettes[index];
        const cardId = this.gazetteCardId(gazette.nbDiff, gazette.value);
        this.gazettes[gazette.nbDiff].addItemType(
          cardId,
          -gazette.value,
          g_gamethemeurl + "img/gazettes.png",
          cardId
        );

        this.gazettes[gazette.nbDiff].addToStock(cardId);
      }
    },

    gazetteCardId: function (nbDiff, value) {
      return (nbDiff - 2) * 2 + (value > 5 ? 1 : 0);
    },

    setupDecks: function (gamedatas) {
      this.decks = {};

      for (let index = 1; index < [1, 2, 3].length + 1; index++) {
        this.decks[index] = new ebg.stock();
        this.decks[index].create(
          this,
          $("deck-" + index),
          this.deckCardWidth,
          this.deckCardHeight
        );

        this.decks[index].image_items_per_row = 9;
        this.decks[index].item_margin = 0;
        this.decks[index].setOverlap(1.5, 0);

        for (const color of ["green", "blue", "pink", "yellow"]) {
          for (const value of [0, 1, 2, 3, 4, 5, 6, 7, 8]) {
            var cardId = this.museCardId(color, value);

            this.decks[index].addItemType(
              cardId,
              1,
              g_gamethemeurl + "img/muses.png",
              cardId
            );
          }

          this.decks[index].addItemType(
            "back",
            0,
            g_gamethemeurl + "img/back.png",
            0
          );
        }

        var cardId = this.museCardId(
          gamedatas.decks[index].color,
          gamedatas.decks[index].value
        );

        this.decks[index].addToStock(cardId);

        for (
          let countBacks = 1;
          countBacks < Math.min(gamedatas.decks[index].count, 6);
          countBacks++
        ) {
          this.decks[index].addToStock("back");
        }
      }
    },

    museCardId: function (color, value) {
      switch (color) {
        case "green":
          return value;
        case "blue":
          return 9 + value;
        case "pink":
          return 2 * 9 + value;
        case "yellow":
          return 3 * 9 + value;
      }
    },

    setupPlayerHand: function (gamedatas) {
      this.playerHand = new ebg.stock();
      this.playerHand.create(
        this,
        $("player-hand"),
        this.deckCardWidth,
        this.deckCardHeight
      );

      this.playerHand.image_items_per_row = 9;

      for (const color of ["green", "blue", "pink", "yellow"]) {
        for (const value of [0, 1, 2, 3, 4, 5, 6, 7, 8]) {
          var cardId = this.museCardId(color, value);

          this.playerHand.addItemType(
            cardId,
            1,
            g_gamethemeurl + "img/muses.png",
            cardId
          );
        }
      }

      for (
        let index = 0;
        index < gamedatas.current_player.hand.length;
        index++
      ) {
        var cardId = this.museCardId(
          gamedatas.current_player.hand[index].color,
          gamedatas.current_player.hand[index].value
        );

        this.playerHand.addToStock(cardId);
      }
    },

    ///////////////////////////////////////////////////
    //// Game & client states

    // onEnteringState: this method is called each time we are entering into a new game state.
    //                  You can use this method to perform some user interface changes at this moment.
    //
    onEnteringState: function (stateName, args) {
      console.log("Entering state: " + stateName);

      switch (stateName) {
        /* Example:

                    case 'myGameState':

                        // Show some HTML block at this game state
                        dojo.style( 'my_html_block_id', 'display', 'block' );

                        break;
                   */

        case "dummmy":
          break;
      }
    },

    // onLeavingState: this method is called each time we are leaving a game state.
    //                 You can use this method to perform some user interface changes at this moment.
    //
    onLeavingState: function (stateName) {
      console.log("Leaving state: " + stateName);

      switch (stateName) {
        /* Example:

                    case 'myGameState':

                        // Hide the HTML block we are displaying only during this game state
                        dojo.style( 'my_html_block_id', 'display', 'none' );

                        break;
                   */

        case "dummmy":
          break;
      }
    },

    // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
    //                        action status bar (ie: the HTML links in the status bar).
    //
    onUpdateActionButtons: function (stateName, args) {
      console.log("onUpdateActionButtons: " + stateName);

      if (this.isCurrentPlayerActive()) {
        switch (
          stateName
          /*
                                         Example:

                                         case 'myGameState':

                                            // Add 3 action buttons in the action status bar:

                                            this.addActionButton( 'button_1_id', _('Button 1 label'), 'onMyMethodToCall1' );
                                            this.addActionButton( 'button_2_id', _('Button 2 label'), 'onMyMethodToCall2' );
                                            this.addActionButton( 'button_3_id', _('Button 3 label'), 'onMyMethodToCall3' );
                                            break;
                        */
        ) {
        }
      }
    },

    ///////////////////////////////////////////////////
    //// Utility methods

    /*

                Here, you can defines some utility methods that you can use everywhere in your javascript
                script.

            */

    ///////////////////////////////////////////////////
    //// Player's action

    /*

                Here, you are defining methods to handle player's action (ex: results of mouse click on
                game objects).

                Most of the time, these methods:
                _ check the action is possible at this game state.
                _ make a call to the game server

            */

    /* Example:

            onMyMethodToCall1: function( evt )
            {
                console.log( 'onMyMethodToCall1' );

                // Preventing default browser reaction
                dojo.stopEvent( evt );

                // Check that this action is possible (see "possibleactions" in states.inc.php)
                if( ! this.checkAction( 'myAction' ) )
                {   return; }

                this.ajaxcall( "/montmartre/montmartre/myAction.html", {
                                                                        lock: true,
                                                                        myArgument1: arg1,
                                                                        myArgument2: arg2,
                                                                        ...
                                                                     },
                             this, function( result ) {

                                // What to do after the server call if it succeeded
                                // (most of the time: nothing)

                             }, function( is_error) {

                                // What to do after the server call in anyway (success or failure)
                                // (most of the time: nothing)

                             } );
            },

            */

    ///////////////////////////////////////////////////
    //// Reaction to cometD notifications

    /*
                setupNotifications:

                In this method, you associate each of your game notifications with your local method to handle it.

                Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                      your montmartre.game.php file.

            */
    setupNotifications: function () {
      console.log("notifications subscriptions setup");

      // TODO: here, associate your game notifications with local methods

      // Example 1: standard notification handling
      // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );

      // Example 2: standard notification handling + tell the user interface to wait
      //            during 3 seconds after calling the method in order to let the players
      //            see what is happening in the game.
      // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
      // this.notifqueue.setSynchronous( 'cardPlayed', 3000 );
      //
    },

    // TODO: from this point and below, you can write your game notifications handling methods

    /*
            Example:

            notif_cardPlayed: function( notif )
            {
                console.log( 'notif_cardPlayed' );
                console.log( notif );

                // Note: notif.args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call

                // TODO: play the card in the user interface.
            },

            */
  });
});
