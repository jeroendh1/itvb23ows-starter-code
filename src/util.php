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
    
    function SoldierAntMove($from, $to, $board)
    {
        // Initialize an array to track visited positions
        $visited = [];

        // Initialize an array to store positions to be explored
        $positionsToExplore = [$from];
    
        while (!empty($positionsToExplore)) {
            // Get the next position to explore
            $current = array_shift($positionsToExplore);
    
            //If the current position is the destination position, return true
            if ($current == $to) {
                return true;
            }
    
            // Add the current position to the visited array
            $visited[$current] = true;
    
            // Get possible positions to slide to
            foreach ($GLOBALS['OFFSETS'] as $pq) {
                $p = explode(',', $current)[0] + $pq[0];
                $q = explode(',', $current)[1] + $pq[1];
                $position = "$p,$q";

                // Check if the position is empty, not visited, and has neighbors
                if (!isset($board[$position]) && !isset($visited[$position]) && hasNeighbour($position, $board)) {
                    // Add the position to positionsToExplore
                    $positionsToExplore[] = $position;
                }
            }
        }
        return false;
    }
    function spiderMove($from, $to, $board)
    {   unset($board[$from]);
        $visited = [];
        $positionsToExplore = [[$from, 0]]; // Include distance traveled
        $distanceLimit = 3;
    
        while (!empty($positionsToExplore)) {
            list($currentTile, $distance) = array_shift($positionsToExplore);
            
            // Add the current position to the visited array
            $visited[$currentTile] = true;
            
            // Check if the current position is the destination and distance is exactly 3
            if ($currentTile === $to && $distance === $distanceLimit) {
                return true;
            }
            
            // Get adjacent legal board positions relative to the current tile
            foreach ($GLOBALS['OFFSETS'] as $pq) {
                $p = explode(',', $currentTile)[0] + $pq[0];
                $q = explode(',', $currentTile)[1] + $pq[1];
                $position = "$p,$q";
                
                // Check if the position is not visited, not blocked, has neighbors, and within distance limit
                if (!isset($visited[$position]) && !isset($board[$position]) && hasNeighbour($position, $board) && $distance + 1 <= $distanceLimit) {
                    $positionsToExplore[] = [$position, $distance + 1]; // Update distance traveled
                }
            }
        }
    
        return false;
    }
    
    function hasLostGame($board, $player): bool
    {
        foreach ($board as $pos => $tiles) {
            $topTile = end($tiles);

            // Check if the top tile belongs to the specified player and is a queen bee
            if ($topTile[0] == $player && $topTile[1] == 'Q') {
                $neighbourCount = 0;
                $posCoordinates = explode(',', $pos);
    
                foreach ($GLOBALS['OFFSETS'] as $offset) {
                    // Calculate neighboring position coordinates
                    $neighbourX = $posCoordinates[0] + $offset[0];
                    $neighbourY = $posCoordinates[1] + $offset[1];
                    $neighbourPos = $neighbourX . ',' . $neighbourY;
    
                    // Check if the neighboring position is occupied
                    if (isset($board[$neighbourPos])) {
                        $neighbourCount++;
                    }
                }
                // Check if all six neighboring positions are occupied
                if ($neighbourCount == 6) {
                    return true; // Player has lost the game
                }

                // Stop further iterations if a queen bee belonging to the specified player is found
                break;
            }
        }
        return false; // Player has not lost the game
    }
    
?>