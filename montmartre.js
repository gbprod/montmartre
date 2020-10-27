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

            this.paintingsStocks = Array();

            this.setupCollectors(gamedatas);
            this.setupGazettes(gamedatas);
            this.setupDecks(gamedatas);
            this.setupPlayerHand(gamedatas);
            this.setupPlayersPaintings(gamedatas);

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
                this.decks[index] = this.createMusesStock("deck-" + index);
                this.decks[index].item_margin = 0;
                this.decks[index].setOverlap(1.5, 0);

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

        museFromCardId: function (id) {
            var value = id % 9;
            var colorId = (id - value) / 9;

            switch (colorId) {
                case 0:
                    return {
                        value: value,
                        color: "green",
                    };

                case 1:
                    return {
                        value: value,
                        color: "blue",
                    };

                case 2:
                    return {
                        value: value,
                        color: "pink",
                    };

                case 3:
                    return {
                        value: value,
                        color: "yellow",
                    };
            }

            return undefined;
        },

        setupPlayerHand: function (gamedatas) {
            this.playerHand = this.createMusesStock("player-hand");
            this.playerHand.setSelectionAppearance("class");
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

        createMusesStock: function (target) {
            var stock = new ebg.stock();
            stock.create(this, $(target), this.deckCardWidth, this.deckCardHeight);

            stock.image_items_per_row = 9;

            for (const color of ["green", "blue", "pink", "yellow"]) {
                for (const value of [0, 1, 2, 3, 4, 5, 6, 7, 8]) {
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

        setupPlayersPaintings: function (gamedatas) {
            this.setupCurrentPlayerPaintings(gamedatas.current_player.paintings);

            for (let index = 0; index < gamedatas.others_players.length; ++index) {
                this.setupOtherPlayerPaintings(gamedatas.others_players[index]);
            }
        },

        setupCurrentPlayerPaintings: function (paintings) {
            this.paintingsStocks["current"] = Array();

            for (const color of ["green", "yellow", "blue", "pink"]) {
                this.paintingsStocks["current"][color] = this.createMusesStock(
                    "player-paintings-" + color
                );

                this.paintingsStocks['current'][color].item_margin = 0;
                this.paintingsStocks['current'][color].setOverlap(40, 0);
                this.paintingsStocks['current'][color].setSelectionAppearance("class");

                var that = this;
                dojo.connect(
                    this.paintingsStocks['current'][color], 
                    'onChangeSelection', 
                    function (controlName) {
                        for (const c of ["green", "yellow", "blue", "pink"]) {
                            if ("player-paintings-" + c !== controlName) {
                                that.paintingsStocks['current'][c].unselectAll();
                            }
                        }
                    }
                );
            }

            for (let index = 0; index < paintings.length; index++) {
                const painting = paintings[index];

                this.paintingsStocks["current"][painting.color].addToStock(
                    this.museCardId(painting.color, painting.value)
                );
            }
        },

        setupOtherPlayerPaintings: function (player) {
            this.paintingsStocks[player.id] = Array();

            for (const color of ["green", "yellow", "blue", "pink"]) {
                this.paintingsStocks[player.id][color] = this.createMusesStock(
                    "player-" + player.id + "-paintings-" + color
                );

                this.paintingsStocks[player.id][color].item_margin = 0;
                this.paintingsStocks[player.id][color].setOverlap(40, 0);
            }

            for (let index = 0; index < player.paintings.length; index++) {
                const painting = player.paintings[index];

                this.paintingsStocks[player.id][painting.color].addToStock(
                    this.museCardId(painting.color, painting.value)
                );
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
                case "playerTurn":
                    this.playerHand.setSelectionMode(2);
                    break;

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
                case "paintAction":
                    this.playerHand.setSelectionMode(0);
                    break;

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
                switch (stateName) {
                    case "playerTurn":
                        this.addActionButton(
                            "paint_action_button",
                            _("Paint"),
                            "paintAction"
                        );
                        this.addActionButton(
                            "sell_action_button", 
                            _("Sell"), 
                            "sellAction"
                        );
                        break;
                     case "sellOffState":
                        this.addActionButton(
                            "selloff_action_button",
                            _("Sell off"),
                            "sellOffAction"
                        );
                        break;
                }
            }
        },

        ///////////////////////////////////////////////////
        //// Player's action

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

            this.ajaxcall("/montmartre/montmartre/paint.html", {
                    cards: cards.join(","),
                    lock: true,
                },
                this,
                function (result) {},
                function (isError) {}
            );
        },

        sellAction: function () {
            console.log("sellAction");
            dojo.stopEvent(event);

            if (!this.checkAction("sellAction")) {
                return;
            }

            // TODO
        },

        sellOffAction: function () {
            console.log("sellOffAction");
            dojo.stopEvent(event);

            if (!this.checkAction("sellOffAction")) {
                return;
            }

            var selected = undefined;
            for (const color of ["green", "yellow", "blue", "pink"]) {
                let s = this.paintingsStocks["current"][color].getSelectedItems();
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

            this.ajaxcall("/montmartre/montmartre/selloff.html", {
                    cards: cards.join(","),
                    lock: true,
                },
                this,
                function (result) {},
                function (isError) {}
            );
 
        },

        pickAction: function () {
            console.log("pickAction");
            dojo.stopEvent(event);

            if (!this.checkAction("pickAction")) {
                return;
            }
        },

        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        setupNotifications: function () {
            console.log("notifications subscriptions setup");

            dojo.subscribe("PlayerHasPaint", this, "onPlayerHasPaint");
            this.notifqueue.setSynchronous("PlayerHasPaint", 3000);
            
            dojo.subscribe("PlayerHasSoldOff", this, "onPlayerHasSoldOff");
            this.notifqueue.setSynchronous("PlayerHasSoldOff", 3000);
        },

        onPlayerHasPaint: function (event) {
            if (this.isCurrentPlayerActive()) {
                for (let index = 0; index < event.args.muses.length; ++index) {
                    const id = this.museCardId(
                        event.args.muses[index].color,
                        event.args.muses[index].value
                    );
                    this.paintingsStocks["current"][event.args.muses[index].color].addToStock(id);
                    this.playerHand.removeFromStock(id);
                }
            } else {
                for (let index = 0; index < event.args.muses.length; ++index) {
                    const id = this.museCardId(
                        event.args.muses[index].color,
                        event.args.muses[index].value
                    );
                    this.paintingsStocks[event.args.player_id][event.args.muses[index].color].addToStock(id);
                }
            }
        },
        
        onPlayerHasSoldOff: function (event) {
            const playerIndex = this.isCurrentPlayerActive() 
                ? "current" 
                : event.args.player_id;

            for (let index = 0; index < event.args.muses.length; ++index) {
                const id = this.museCardId(
                    event.args.muses[index].color,
                    event.args.muses[index].value
                );
                this.paintingsStocks[playerIndex][event.args.muses[index].color].removeFromStock(id);
            }
        },
    });
});
