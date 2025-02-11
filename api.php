<?php

require 'db.php';

header('Content-Type: application/json');

session_start();

//user login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt     = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password);
    if ($stmt->fetch() && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
    }
    exit();
}

//user registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $stmt     = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Registration failed']);
    }
    exit();
}

// check if user is logged in
if (! isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

//Add a new task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $task     = $_POST['task'];
    $due_date = $_POST['due_date'];
    $category = $_POST['category'];
    $stmt     = $conn->prepare("INSERT INTO tasks (tasks, due_date, category, user_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $task, $due_date, $category, $user_id);
    $stmt->execute();
}

// Fetch all tasks for the logged-in user
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $result = $conn->query("SELECT * FROM tasks WHERE user_id = $user_id");
    $tasks  = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    echo json_encode($tasks);
    exit();
}

// Delete a task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) & $_POST['action'] === 'delete') {
    $id   = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    echo json_encode(['status' => 'success']);
    exit();
}

// Toggle task completion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle') {
    $id        = $_POST['id'];
    $completed = $_POST['completed'] ? 1 : 0;
    $stmt      = $conn->prepare("UPDATE tasks SET completed = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("iii", $completed, $id, $user_id);
    $stmt->execute();
    echo json_encode(['status' => 'success']);
    exit();
}