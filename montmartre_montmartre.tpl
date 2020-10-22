{OVERALL_GAME_HEADER}

<!--
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- Montmartre implementation : © <gbprod> <contact@gb-prod.fr>
--
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-------
-->

<div id="board">
  <div class="table">
    <div class="gazettes">
      <div class="gazette" id="gazette-2"></div>
      <div class="gazette" id="gazette-3"></div>
      <div class="gazette" id="gazette-4"></div>
    </div>
    <div class="collectors">
      <div class="collector" id="collectors-green"></div>
      <div class="collector" id="collectors-yellow"></div>
      <div class="collector" id="collectors-blue"></div>
      <div class="collector" id="collectors-pink"></div>
    </div>
  </div>
  <div class="decks">
    <div class="deck" id="deck-1"></div>
    <div class="deck" id="deck-2"></div>
    <div class="deck" id="deck-3"></div>
  </div>
  <div class="whiteblock">
    <h3>{trans_your_hand}</h3>
    <div id="player-hand"></div>
  </div>
  <div class="whiteblock">
    <h3>{trans_your_paintings}</h3>
    <div id="player-paintings" class="paintings">
      <div id="player-paintings-green" class="painting"></div>
      <div id="player-paintings-blue" class="painting"></div>
      <div id="player-paintings-pink" class="painting"></div>
      <div id="player-paintings-yellow" class="painting"></div>
    </div>
  </div>
  <!-- BEGIN plaintings_block -->
  <div class="whiteblock">
    <h3>{trans_player_paintings}</h3>
    <div id="player-{player_id}-paintings" class="paintings">
      <div id="player-{player_id}-paintings-green" class="painting"></div>
      <div id="player-{player_id}-paintings-blue" class="painting"></div>
      <div id="player-{player_id}-paintings-pink" class="painting"></div>
      <div id="player-{player_id}-paintings-yellow" class="painting"></div>
    </div>
  </div>
  <!-- END plaintings_block -->
</div>

<script type="text/javascript"></script>

{OVERALL_GAME_FOOTER}
