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
  "ebg/zone",
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
      this.ambroise = null;
      window.game = this; // for debug
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

      window.gamedatas = gamedatas; // for debug
      this.paintingsStocks = Array();

      this.setupCollectors(gamedatas);
      this.setupGazettes(gamedatas);
      this.setupDecks(gamedatas);
      this.setupPlayerHand(gamedatas);
      this.setupPlayers(gamedatas);
      if (this.isCurrentPlayerActive() && this.checkAction("paintAction")) {
        this.setupSellButtons(gamedatas.current_player.majorities);
      }

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

      for (var color of ["green", "yellow", "blue", "pink"]) {
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

      for (var color of ["green", "yellow", "blue", "pink"]) {
        for (var value of [2, 4, 6, 8, 10]) {
          if (gamedatas.collectors[color] <= value) {
            var cardId = this.collectorCardId(color, value);
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

      if (!!gamedatas.ambroise) {
        this.moveAmbroise(gamedatas.ambroise);
      }
    },

    moveAmbroise: function (color) {
      console.log("mosve to" + color);
      if (this.ambroise == null) {
        dojo.place(this.format_block("jstpl_ambroise", {}), "collectors");
        this.placeOnObject("ambroise", "collectors-" + color);
      } else {
        this.slideToObject("ambroise", "collectors-" + color).play();
      }

      this.ambroise = color;
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

      for (var index = 2; index < [2, 3, 4].length + 2; index++) {
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

      for (var index = 0; index < gamedatas.gazettes.length; index++) {
        var gazette = gamedatas.gazettes[index];
        var cardId = this.gazetteCardId(gazette.nbDiff, gazette.value);
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
      var that = this;

      for (var index = 1; index < [1, 2, 3].length + 1; index++) {
        this.setMuseInDeck(index, gamedatas.decks[index]);

        dojo.query("#button-deck-" + index).onclick(function (event) {
          that.onDraw(event);
          return false;
        });
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

    museFromCardId: function (id) {
      var value = id % 9;
      var colorId = (id - value) / 9;

      switch (colorId) {
        case 0:
          return { value: value, color: "green" };

        case 1:
          return { value: value, color: "blue" };

        case 2:
          return { value: value, color: "pink" };

        case 3:
          return { value: value, color: "yellow" };
      }

      return undefined;
    },

    setupPlayerHand: function (gamedatas) {
      this.playerHand = this.createMusesStock("player-hand");
      this.playerHand.setSelectionAppearance("class");
      for (
        var index = 0;
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

    createMusesStock: function (target) {
      var stock = new ebg.stock();
      stock.create(this, $(target), this.deckCardWidth, this.deckCardHeight);

      stock.image_items_per_row = 9;

      for (var color of ["green", "blue", "pink", "yellow"]) {
        for (var value of [0, 1, 2, 3, 4, 5, 6, 7, 8]) {
          var cardId = this.museCardId(color, value);

          stock.addItemType(
            cardId,
            1,
            g_gamethemeurl + "img/muses.png",
            cardId
          );
        }
      }

      stock.addItemType("back", 0, g_gamethemeurl + "img/back.png", 0);

      return stock;
    },

    setupPlayers: function (gamedatas) {
      for (var id in gamedatas.players) {
        this.setupPlayerPaintings(gamedatas.players[id]);
      }
    },

    setupPlayerPaintings: function (player) {
      this.paintingsStocks[player.id] = Array();

      for (var color of ["green", "yellow", "blue", "pink"]) {
        this.paintingsStocks[player["id"]][color] = this.createMusesStock(
          "player-" + player.id + "-paintings-" + color
        );

        this.paintingsStocks[player.id][color].item_margin = 0;
        this.paintingsStocks[player.id][color].autowidth = true;
        this.paintingsStocks[player.id][color].setOverlap(40, 0);
        this.paintingsStocks[player.id][color].setSelectionMode(0);
        this.paintingsStocks[player.id][color].setSelectionAppearance("class");
      }

      for (var index = 0; index < player.paintings.length; index++) {
        var painting = player.paintings[index];

        this.paintingsStocks[player.id][painting.color].addToStock(
          this.museCardId(painting.color, painting.value)
        );
      }
    },

    setMuseInDeck: function (deck, muse) {
      document.querySelector("#muse-deck-" + deck).classList =
        "muse " + muse.color + "-" + muse.value;
    },

    setupSellButtons: function (majorities) {
      var that = this;

      for (var color of majorities) {
        if (color != this.ambroise) {
          dojo.place(
            this.format_block("jstpl_sell_button", {
              color: color,
            }),
            "player-" + this.player_id + "-paintings-" + color,
            "after"
          );
        }
      }

      dojo.query(".sell-button").onclick(function (event) {
        dojo.stopEvent(event);
        that.onSell(event);
        return false;
      });
    },

    /**
     * Game & client states
     */

    // onEnteringState: this method is called each time we are entering into a new game state.
    //                  You can use this method to perform some user interface changes at this moment.
    //
    onEnteringState: function (stateName, args) {
      console.log("Entering state: " + stateName);
      if (!this.isCurrentPlayerActive()) {
        return;
      }

      switch (stateName) {
        case "playerTurn":
          this.playerHand.setSelectionMode(2);
          break;

        case "drawOrSellOffState":
          for (var color of ["green", "yellow", "blue", "pink"]) {
            this.paintingsStocks[this.getCurrentPlayerId()][
              color
            ].setSelectionMode(2);
          }
          break;

        case "mustSellOffState":
          for (var color of ["green", "yellow", "blue", "pink"]) {
            this.paintingsStocks[this.getCurrentPlayerId()][
              color
            ].setSelectionMode(2);
          }
          break;

        case "drawState":
          dojo.query(".draw-button").style("display", "inline-block");
          break;
      }
    },

    // onLeavingState: this method is called each time we are leaving a game state.
    //                 You can use this method to perform some user interface changes at this moment.
    //
    onLeavingState: function (stateName) {
      console.log("Leaving state: " + stateName);

      if (!this.isCurrentPlayerActive()) {
        return;
      }

      switch (stateName) {
        case "playerTurn":
          this.playerHand.setSelectionMode(0);
          dojo.query(".sell-button").forEach(dojo.destroy);
          break;

        case "drawOrSellOffState":
          for (var color of ["green", "yellow", "blue", "pink"]) {
            this.paintingsStocks[this.getCurrentPlayerId()][
              color
            ].setSelectionMode(0);
          }
          dojo.query(".draw-button").style("display", "none");
          break;

        case "mustSellOffState":
          for (var color of ["green", "yellow", "blue", "pink"]) {
            this.paintingsStocks[this.getCurrentPlayerId()][
              color
            ].setSelectionMode(0);
          }
          break;

        case "drawState":
          dojo.query(".draw-button").style("display", "none");
          break;
      }
    },

    // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
    //                        action status bar (ie: the HTML links in the status bar).
    //
    onUpdateActionButtons: function (stateName, args) {
      console.log("onUpdateActionButtons: " + stateName);

      if (this.isCurrentPlayerActive()) {
        switch (stateName) {
          case "playerTurn":
            this.addActionButton(
              "paint_action_button",
              _("Paint"),
              "paintAction"
            );
            break;

          case "drawOrSellOffState":
            this.addActionButton(
              "selloff_action_button",
              _("Sell off"),
              "sellOffAction"
            );
            this.addActionButton(
              "skip_selloff_action_button",
              _("No, thanks"),
              "skipSellOffAction",
              null,
              false,
              "gray"
            );
            break;

          case "mustSellOffState":
            this.addActionButton(
              "selloff_action_button",
              _("Sell off"),
              "sellOffAction"
            );
            break;
        }
      }
    },

    /**
     * Player's action
     */

    paintAction: function (event) {
      dojo.stopEvent(event);
      console.log("paintAction");

      if (!this.checkAction("paintAction")) {
        return;
      }

      var items = this.playerHand.getSelectedItems();
      if (items.length <= 0) {
        this.showMessage(
          _("You should select at least one Muse from your hand"),
          "info"
        );

        return;
      }

      var cards = items.map(function (item) {
        return item.type;
      });

      this.ajaxcall(
        "/montmartre/montmartre/paint.html",
        {
          cards: cards.join(","),
          lock: true,
        },
        this,
        function (result) {},
        function (isError) {}
      );
    },

    sellOffAction: function (event) {
      console.log("sellOffAction");

      if (!this.checkAction("sellOffAction")) {
        return;
      }

      var selected = undefined;
      for (var color of ["green", "yellow", "blue", "pink"]) {
        var s = this.paintingsStocks[this.getCurrentPlayerId()][
          color
        ].getSelectedItems();
        if (s.length > 0 && selected !== undefined) {
          this.showMessage(
            _("You should select only same color muses"),
            "info"
          );

          return;
        }

        if (s.length > 0) {
          selected = s;
        }
      }

      if (selected === undefined) {
        this.showMessage(
          _("You should select at least one Muse to sell off"),
          "info"
        );

        return;
      }

      var cards = selected.map(function (item) {
        return item.type;
      });

      this.ajaxcall(
        "/montmartre/montmartre/selloff.html",
        {
          cards: cards.join(","),
          lock: true,
        },
        this,
        function (result) {},
        function (isError) {}
      );
    },

    skipSellOffAction: function (event) {
      console.log("skipSellOffAction");
      dojo.stopEvent(event);

      if (!this.checkAction("drawAction")) {
        return;
      }

      this.gamedatas.gamestate.descriptionmyturn = _(
        "You must draw, select a deck"
      );
      this.updatePageTitle();
      this.removeActionButtons();
      dojo.query(".draw-button").style("display", "inline-block");
    },

    drawAction: function (event) {
      console.log("drawAction");
      dojo.stopEvent(event);

      if (!this.checkAction("drawAction")) {
        return;
      }
    },

    /**
     * UI Events
     */

    onDraw: function (event) {
      dojo.stopEvent(event);
      var index = event.target.getAttribute("data-index");

      this.ajaxcall(
        "/montmartre/montmartre/draw.html",
        {
          deck: index,
          lock: true,
        },
        this,
        function (result) {},
        function (isError) {}
      );
    },

    onSell: function (event) {
      dojo.stopEvent(event);
      var color = event.target.getAttribute("data-color");

      this.ajaxcall(
        "/montmartre/montmartre/sell.html",
        {
          color: color,
          lock: true,
        },
        this,
        function (result) {},
        function (isError) {}
      );
    },

    /**
     * Reaction to cometD notifications
     */

    setupNotifications: function () {
      console.log("notifications subscriptions setup");

      dojo.subscribe("PlayerHasPaint", this, "onPlayerHasPaint");
      this.notifqueue.setSynchronous("PlayerHasPaint", 3000);

      dojo.subscribe("PlayerHasSoldOff", this, "onPlayerHasSoldOff");
      this.notifqueue.setSynchronous("PlayerHasSoldOff", 3000);

      dojo.subscribe("PlayerHasDrawed", this, "onPlayerHasDrawed");
      this.notifqueue.setSynchronous("PlayerHasDrawed", 3000);

      dojo.subscribe("PlayerHasChanged", this, "onPlayerHasChanged");
      this.notifqueue.setSynchronous("PlayerHasChanged", 3000);

      dojo.subscribe("PlayerHasSold", this, "onPlayerHasSold");
      this.notifqueue.setSynchronous("PlayerHasSold", 3000);
    },

    onPlayerHasPaint: function (event) {
      console.log("Event onPlayerHasPaint");
      for (var index = 0; index < event.args.muses.length; ++index) {
        var id = this.museCardId(
          event.args.muses[index].color,
          event.args.muses[index].value
        );
        this.paintingsStocks[event.args.player_id][
          event.args.muses[index].color
        ].addToStock(id);
        if (event.args.player_id == this.getCurrentPlayerId()) {
          this.playerHand.removeFromStock(id);
        }
      }
    },

    onPlayerHasSoldOff: function (event) {
      console.log("Event onPlayerHasSoldOff");
      for (var index = 0; index < event.args.muses.length; ++index) {
        var id = this.museCardId(
          event.args.muses[index].color,
          event.args.muses[index].value
        );
        this.paintingsStocks[event.args.player_id][
          event.args.muses[index].color
        ].removeFromStock(id);
      }

      this.scoreCtrl[event.args.player_id].toValue(event.args.player_score);
    },

    onPlayerHasDrawed: function (event) {
      console.log("Event onPlayerHasDrawed");
      var that = this;

      for (var index = 0; index < event.args.muses.length; ++index) {
        if (this.isCurrentPlayerActive()) {
          var id = this.museCardId(
            event.args.muses[index].color,
            event.args.muses[index].value
          );
          this.playerHand.addToStock(id, "deck-" + event.args.deck_number);
          this.setMuseInDeck(event.args.deck_number, event.args.next_muse);
        } else {
          setTimeout(
            function (index, event, that) {
              dojo.place(
                that.format_block("jstpl_muse_card", {
                  id: "drawed-" + index,
                  value: event.args.muses[index].value,
                  color: event.args.muses[index].color,
                }),
                "deck-" + event.args.deck_number
              );

              that.slideToObjectAndDestroy(
                "drawed-" + index,
                "player_board_" + event.args.player_id
              );

              if (index + 1 === event.args.muses.length) {
                that.setMuseInDeck(
                  event.args.deck_number,
                  event.args.next_muse
                );
              } else {
                that.setMuseInDeck(
                  event.args.deck_number,
                  event.args.muses[index + 1]
                );
              }
            },
            index === 0 ? 0 : 1000,
            index,
            event,
            that
          );
        }
      }
    },

    onPlayerHasSold: function (event) {
      console.log("Event onPlayerHasSold");
      var id = this.museCardId(event.args.muse.color, event.args.muse.value);
      this.paintingsStocks[event.args.player_id][
        event.args.color
      ].removeFromStock(id, "collectors-" + event.args.muse.color);

      this.collectors[event.args.color].removeFromStock(
        this.collectorCardId(event.args.color, event.args.attractedCollector),
        "player_name_" + event.args.player_id
      );

      this.scoreCtrl[event.args.player_id].toValue(event.args.player_score);

      this.moveAmbroise(event.args.muse.color);
    },

    onPlayerHasChanged: function (event) {
      console.log("Event onPlayerHasChanged");
      if (this.isCurrentPlayerActive()) {
        this.setupSellButtons(event.args.majorities);
      }
    },
  });
});
