<?php

$GLOBALS['OFFSETS'] = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];

function isNeighbour($a, $b) {
    $a = explode(',', $a);
    $b = explode(',', $b);
    if ($a[0] == $b[0] && abs($a[1] - $b[1]) == 1) return true;
    if ($a[1] == $b[1] && abs($a[0] - $b[0]) == 1) return true;
    if ($a[0] + $a[1] == $b[0] + $b[1]) return true;
    return false;
}

function hasNeighBour($a, $board) {
    foreach (array_keys($board) as $b) {
        if (isNeighbour($a, $b)) return true;
    }
}

function neighboursAreSameColor($player, $a, $board) {
    foreach ($board as $b => $st) {
        if (!$st) continue;
        $c = $st[count($st) - 1][0];
        if ($c != $player && isNeighbour($a, $b)) return false;
    }
    return true;
}

function len($tile) {
    return $tile ? count($tile) : 0;
}

function slide($board, $from, $to) {
    if (!hasNeighbour($to, $board)) return false;
    if (!isNeighbour($from, $to)) return false;

    $b = explode(',', $to);
    $common = [];

    foreach ($GLOBALS['OFFSETS'] as $pq) {
        $p = $b[0] + $pq[0];
        $q = $b[1] + $pq[1];
        if (isNeighbour($from, $p.",".$q)) $common[] = $p.",".$q;
    }

    if (
        (!isset($board[$common[0]]) || !$board[$common[0]]) &&
        (!isset($board[$common[1]]) || !$board[$common[1]]) &&
        (!isset($board[$from]) || !$board[$from]) &&
        (!isset($board[$to]) || !$board[$to])
    ) {
        return false;
    }
    return min(len($board[$common[0]]), len($board[$common[1]])) <= max(len($board[$from]), len($board[$to]));
}
    function GrasshopperMove($from, $to, $board)
    {
        $fromExploded = explode(',', $from);
        $toExploded = explode(',', $to);
    
        // Calculate the direction of movement
        $xDiff = $toExploded[0] - $fromExploded[0];
        $yDiff = $toExploded[1] - $fromExploded[1];
        
        // Validate that the movement is either horizontal, vertical, or diagonal
        if (!($xDiff == 0 || $yDiff == 0 || $xDiff == $yDiff || $xDiff == -$yDiff)) {
            return false;
        }
    
        // Determine the direction of movement based on differences
        if ($xDiff > 0) {
            $xDirection = 1;
        } elseif ($xDiff < 0) {
            $xDirection = -1;
        } else {
            $xDirection = 0;
        }

        if ($yDiff > 0) {
            $yDirection = 1;
        } elseif ($yDiff < 0) {
            $yDirection = -1;
        } else {
            $yDirection = 0;
        }

        $p = $fromExploded[0] + $xDirection;
        $q = $fromExploded[1] + $yDirection;
    
        $jumpedOver = false; 
        // Check if the Grasshopper moves to a neighboring position without jumping over a stone
        if (!isset($board["$p,$q"])) {
            return false;
        }
    
        while ($p != $toExploded[0] || $q != $toExploded[1]) {
            if (isset($board["$p,$q"])) {
               
                // Grasshopper jumps over at least one stone
                $jumpedOver = true;
            }
            $p += $xDirection;
            $q += $yDirection;
        }
    
        return $jumpedOver;
    }


?>