<?php 
class HiveAI {
    private $aiEndpoint = 'ai:5000';

    public function suggestMove(int $moveNumber, array $hand, array $board) {
        // Prepare the request payload
        $payload = [
            "move_number" => $moveNumber,
            "hand" => $hand,
            "board" => $board
        ];

        // Send the POST request to the AI endpoint
        $curl = curl_init($this->aiEndpoint);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        curl_close($curl);
        
        // Parse the response and return the suggested move
        return json_decode($response, true);
    }
}
?>