<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
    exit(0);
}

if (isset($_REQUEST['request'])) {
    $request = explode('/', $_REQUEST['request']);
} else {
    echo json_encode(["error" => "Not Found"]);
    http_response_code(404);
    exit();
}

require_once 'services/weather.php';
$weather = new Weather();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        switch ($request[0]) {
            case 'current-weather':
                if (isset($request[1])) {
                    $city = $request[1];
                    $result = $weather->getCurrentWeather($city);
                    if ($result) {
                        echo json_encode($result);
                    } else {
                        echo json_encode(["error" => "City not found"]);
                        http_response_code(404);
                    }
                } else {
                    echo json_encode(["error" => "City not specified"]);
                    http_response_code(400);
                }
                break;

            case '5-day-forecast':
                if (isset($request[1])) {
                    $city = $request[1];
                    $result = $weather->get5DayForecast($city);
                    if ($result) {
                        echo json_encode($result);
                    } else {
                        echo json_encode(["error" => "City not found"]);
                        http_response_code(404);
                    }
                } else {
                    echo json_encode(["error" => "City not specified"]);
                    http_response_code(400);
                }
                break;
                
                case 'coordinates':
                    if (isset($request[1])) {
                        $city = $request[1];
                        $result = $weather->getCoordinates($city);
                        if ($result) {
                            echo json_encode($result);
                        } else {
                            echo json_encode(["error" => "City not found"]);
                            http_response_code(404);
                        }
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
        break;
        
    default:
        echo json_encode(["error" => "Method not available"]);
        http_response_code(405);
        break;
}

?>