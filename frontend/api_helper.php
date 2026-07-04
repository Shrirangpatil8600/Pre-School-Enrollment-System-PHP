<?php
require_once 'config.php';

// Helper function to send requests to Java Backend API
function call_api($method, $endpoint, $data = false) {
    $url = API_BASE_URL . $endpoint;
    $curl = curl_init();

    switch (strtoupper($method)) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data) {
                $json_data = json_encode($data);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($json_data)
                ));
            }
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data) {
                $json_data = json_encode($data);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($json_data)
                ));
            }
            break;
        default:
            if ($data) {
                $url = sprintf("%s?%s", $url, http_build_query($data));
            }
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 5); // Timeout of 5 seconds

    $result = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if (curl_errno($curl)) {
        $error_msg = curl_error($curl);
        curl_close($curl);
        return [
            'status' => 500,
            'error' => 'API Connection Timeout/Error: ' . $error_msg
        ];
    }

    curl_close($curl);
    
    return [
        'status' => $http_code,
        'data' => json_decode($result, true)
    ];
}
?>
