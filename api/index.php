<?php
header("access-control-allow-origin: *");
header("access-control-allow-headers: *");
header("access-control-allow-methods: *");

include 'Connection.php';

$database = new Connection();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
switch($method) {
    case 'GET':
        $sql = "SELECT * FROM task";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if(isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE id = :id";
            $smt = $db->prepare($sql);
            $smt->bindParam(':id', $path[3]);
            $smt->execute();
            $task = $smt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($task);
        } else {
            $smt = $db->prepare($sql);
            $smt->execute();
            $tasks = $smt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($tasks);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        $smt = $db->prepare("INSERT INTO task (id, taskName) VALUES (null, :taskName)");
        $smt->bindParam(':taskName', $data->taskName);
        if($smt->execute()) {
            echo json_encode(["message" => "Task created successfully."]);
        } else {
            echo json_encode(["message" => "Failed to create task."]);
        }
        break;
        
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        $smt = $db->prepare("UPDATE task SET taskName = :taskName WHERE id = :id");
        $smt->bindParam(':taskName', $data->taskName);
        $smt->bindParam(':id', $data->id);
        if($smt->execute()) {
            echo json_encode(["message" => "Task Edited successfully."]);
        } else {
            echo json_encode(["message" => "Failed to edit task."]);
        }
        break;
    case 'DELETE':
        $path = explode('/', $_SERVER['REQUEST_URI']); 
        if(isset($path[3]) && is_numeric($path[3])) {
            $smt = $db->prepare("DELETE FROM task WHERE id = :id");
            $smt->bindParam(':id', $path[3]);
            if($smt->execute()) {
                echo json_encode(["message" => "Task deleted successfully."]);
            } else {
                echo json_encode(["message" => "Failed to delete task."]);
            }
        }
        break

}