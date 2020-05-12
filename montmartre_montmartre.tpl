{OVERALL_GAME_HEADER}

<!--
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- Montmartre implementation : © <gbprod> <contact@gb-prod.fr>
--
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-------

    montmartre_montmartre.tpl

    This is the HTML template of your game.

    Everything you are writing in this file will be displayed in the HTML page of your game user interface,
    in the "main game zone" of the screen.

    You can use in this template:
    _ variables, with the format {MY_VARIABLE_ELEMENT}.
    _ HTML block, with the BEGIN/END format

    See your "view" PHP file to check how to set variables and control blocks

    Please REMOVE this comment before publishing your game on BGA
-->

<div id="board">
  <div id="gazettes">
    <div id="gazettes2"></div>
    <div id="gazettes3"></div>
    <div id="gazettes4"></div>
  </div>
  <div id="collectors">
    <div class="collector" id="collectors-green"></div>
    <div class="collector" id="collectors-yellow"></div>
    <div class="collector" id="collectors-blue"></div>
    <div class="collector" id="collectors-pink"></div>
  </div>
  </div>
  <div class="decks">
    <div id="deck1"></div>
    <div id="deck2"></div>
    <div id="deck3"></div>
  </div>
</div>

<script type="text/javascript">
var collectorTemplate ='<div class="collector" id="collectors-${color}"></div>';
</script>

{OVERALL_GAME_FOOTER}
