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
    <div id="collectors" class="collectors">
      <div class="collector" id="collectors-green"></div>
      <div class="collector" id="collectors-yellow"></div>
      <div class="collector" id="collectors-blue"></div>
      <div class="collector" id="collectors-pink"></div>
    </div>
  </div>
  <div class="decks" id="decks">
    <div class="deck">
      <div id="deck-1">
        <div id="muse-deck-1"></div>
      </div>
      <a href="#" id="button-deck-1" data-index='1' class="action-button bgabutton bgabutton_blue draw-button" onclick="return false;">Draw</a>
    </div>
    <div class="deck">
      <div id="deck-2">
        <div id="muse-deck-2"></div>
      </div>
      <a href="#" id="button-deck-2" data-index='2' class="action-button bgabutton bgabutton_blue draw-button" onclick="return false;">Draw</a>
    </div>
    <div class="deck">
      <div id="deck-3">
        <div id="muse-deck-3"></div>
      </div>
      <a href="#" id="button-deck-3" data-index='3' class="action-button bgabutton bgabutton_blue draw-button" onclick="return false;">Draw</a>
    </div>
  </div>
  <div class="whiteblock">
    <h3>{trans_your_hand}</h3>
    <div id="player-hand"></div>
  </div>
  <div class="whiteblock">
    <h3>{trans_your_paintings}</h3>
    <div id="player-{current_player_id}-paintings" class="paintings">
      <div class="painting-wrapper">
        <div id="player-{current_player_id}-paintings-green" class="painting"></div>
      </div>
      <div class="paintings-wrapper">
          <div id="player-{current_player_id}-paintings-blue" class="painting"></div>
      </div>
      <div class="paintings-wrapper">
          <div id="player-{current_player_id}-paintings-pink" class="painting"></div>
      </div>
      <div class="paintings-wrapper">
          <div id="player-{current_player_id}-paintings-yellow" class="painting"></div>
      </div>
    </div>
  </div>
  <!-- BEGIN plaintings_block -->
  <div class="whiteblock">
    <h3>{trans_player_paintings}</h3>
    <div id="player-{player_id}-paintings" class="paintings">
      <div class="paintings-wrapper">
        <div id="player-{player_id}-paintings-green" class="painting"></div>
      </div><div class="paintings-wrapper">
        <div id="player-{player_id}-paintings-blue" class="painting"></div>
      </div><div class="paintings-wrapper">
        <div id="player-{player_id}-paintings-pink" class="painting"></div>
      </div><div class="paintings-wrapper">
        <div id="player-{player_id}-paintings-yellow" class="painting"></div>
      </div>
    </div>
  </div>
  <!-- END plaintings_block -->
</div>

<script type="text/javascript">
var jstpl_muse_card='<div class="muse absolute ${color}-${value}" id="${id}"></div>';
var jstpl_sell_button='<a href="#" id="sell-${color}" data-color="${color}" class="action-button bgabutton bgabutton_blue sell-button" onclick="return false;">Sell</a>';
var jstpl_ambroise='<div id="ambroise" class="ambroise"></div>';
</script>

{OVERALL_GAME_FOOTER}
