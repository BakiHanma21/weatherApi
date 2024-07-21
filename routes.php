<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin, Authorization');
header('Content-Type: application/json');

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Method not allowed"]);
    http_response_code(405);
    exit();
}

// Parse the request path
$request = isset($_REQUEST['request']) ? explode('/', $_REQUEST['request']) : null;
if (!$request) {
    echo json_encode(["error" => "Not Found"]);
    http_response_code(404);
    exit();
}

require_once 'services/weather.php'; // Make sure the path is correct
$weather = new Weather();

// Handle the request based on the endpoint
switch ($request[0]) {
    case 'current-weather':
        if (isset($request[1])) {
            $param = $request[1];

            // Check if the parameter is lat/lon or city
            if (preg_match('/^\d+(\.\d+)?$/', $param)) {
                // Handle latitude
                $lat = $param;
                $lon = isset($request[2]) ? $request[2] : null;
                if ($lon) {
                    $result = $weather->getCurrentWeatherByLatLon($lat, $lon);
                    echo json_encode($result ? $result : ["error" => "Weather data not found"]);
                    http_response_code($result ? 200 : 404);
                } else {
                    echo json_encode(["error" => "Longitude not specified"]);
                    http_response_code(400);
                }
            } else {
                // Handle city name
                $city = $param;
                $result = $weather->getCurrentWeather($city);
                echo json_encode($result ? $result : ["error" => "City not found"]);
                http_response_code($result ? 200 : 404);
            }
        } else {
            echo json_encode(["error" => "City or coordinates not specified"]);
            http_response_code(400);
        }
        break;

    case '5-day-forecast':
        if (isset($request[1])) {
            $city = $request[1];
            $result = $weather->get5DayForecast($city);
            echo json_encode($result ? $result : ["error" => "City not found"]);
            http_response_code($result ? 200 : 404);
        } else {
            echo json_encode(["error" => "City not specified"]);
            http_response_code(400);
        }
        break;

    case 'coordinates':
        if (isset($request[1])) {
            $city = $request[1];
            $result = $weather->getCoordinates($city);
            echo json_encode($result ? $result : ["error" => "City not found"]);
            http_response_code($result ? 200 : 404);
        } else {
            echo json_encode(["error" => "City not specified"]);
            http_response_code(400);
        }
        break;

    default:
        echo json_encode(["error" => "Endpoint not available"]);
        http_response_code(404);
        break;
}

?>
