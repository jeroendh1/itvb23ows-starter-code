<?php
include_once 'game.php';
include_once 'databaseHandler.php';

$db = new mysqli('db', 'root', '', 'hive');
$dbHandler = new DbHandler($db);
$game = new Game($dbHandler);

// Get possible positions
$to = $game->getPossiblePositions();

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['play'])) {
        $game->play($_POST['piece'], $_POST['to']);
    } elseif (isset($_POST['from']) && isset($_POST['to'])) {
        $game->move($_POST['from'], $_POST['to']);
    } elseif (isset($_POST['pass'])) {
        $game->pass();
    } elseif (isset($_POST['restart'])) {
        $game->restart();
        header('index.php');
    } elseif (isset($_POST['undo'])) {
        $game->undo();
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Hive</title>
        <link rel="stylesheet" href="/css/main.css">
    </head>
    <body>
    <div class="board">
        <?php
            // PHP code to render the game board
            $min_p = 1000;
            $min_q = 1000;
            foreach ($game->board as $pos => $tile) {
                $pq = explode(',', $pos);
                if ($pq[0] < $min_p) $min_p = $pq[0];
                if ($pq[1] < $min_q) $min_q = $pq[1];
            }
            foreach (array_filter($game->board) as $pos => $tile) {
                $pq = explode(',', $pos);
                $pq[0];
                $pq[1];
                $h = count($tile);
                echo '<div class="tile player';
                echo $tile[$h-1][0];
                if ($h > 1) echo ' stacked';
                echo '" style="left: ';
                echo ($pq[0] - $min_p) * 4 + ($pq[1] - $min_q) * 2;
                echo 'em; top: ';
                echo ($pq[1] - $min_q) * 4;
                echo "em;\">($pq[0],$pq[1])<span>";
                echo $tile[$h-1][1];
                echo '</span></div>';
            }
        ?>
    </div>
    <div class="hand">
        White:
        <?php
            // PHP code to render White player's hand
            foreach ($game->hand[0] as $tile => $ct) {
                for ($i = 0; $i < $ct; $i++) {
                    echo '<div class="tile player0"><span>'.$tile."</span></div> ";
                }
            }
        ?>
    </div>
    <div class="hand">
        Black:
        <?php
        // PHP code to render Black player's hand
        foreach ($game->hand[1] as $tile => $ct) {
            for ($i = 0; $i < $ct; $i++) {
                echo '<div class="tile player1"><span>'.$tile."</span></div> ";
            }
        }
        ?>
    </div>
    <div class="turn">
        Turn: <?php echo $game->getCurrentPlayerColor(); ?>
    </div>
    <form method="post" action="index.php">
        <select name="piece">
            <?php
                // PHP code to render the player's available pieces
                foreach ($game->hand[$game->player] as $tile => $ct) {
                    if ($ct){
                        echo "<option value=\"$tile\">$tile</option>";
                    }
                }
            ?>
        </select>
        <select name="to">
            <?php
                // PHP code to render the available positions
                foreach ($game->getPossiblePositions() as $pos) {
                    echo "<option value=\"$pos\">$pos</option>";
                }
            ?>
        </select>
        <input type="submit" name='play' value="Play">
    </form>
    <form method="post" action="index.php">
        <select name="from">
            <?php
                // PHP code to render the player's pieces
                foreach (array_keys($game->board) as $pos) {
                    echo "<option value=\"$pos\">$pos</option>";
                }
            ?>
        </select>
        <select name="to">
            <?php
                // PHP code to render the available positions for moving
                foreach ($game->getPossiblePositions() as $pos) {
                    echo "<option value=\"$pos\">$pos</option>";
                }
            ?>
        </select>
        <input type="submit" value="Move">
    </form>
    <form method="post" action="index.php">
        <input type="submit" name="pass" value="Pass">
    </form>
    <form method="post" action="index.php">
        <input type="submit" name="restart" value="Restart">
    </form>
    <strong><?php if (isset($_SESSION['error'])) echo($_SESSION['error']); unset($_SESSION['error']); ?></strong>
    <ol>
        <?php
            // PHP code to render the list of moves
            $result = $dbHandler->getGameMoves($_SESSION['game_id']);
            while ($row = $result->fetch_array()) {
                echo '<li>'.$row[2].' '.$row[3].' '.$row[4].'</li>';
            }
        ?>
    </ol>
    <form method="post" action="index.php">
        <input type="submit" name='undo'value="Undo">
    </form>
    </body>
</html>

