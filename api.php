<?php
require 'db.php';

header('Content-Type: application/json');

session_start();

// User login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt     = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    if (! $stmt) {
        die("Prepare failed: " . $conn->error);
    }
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

// User registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if the username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    if (! $stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username already exists']);
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        if (! $stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Registration failed: ' . $stmt->error]);
        }
    }
    exit();
}

// Check if user is logged in
if (! isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Add a new task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $task     = $_POST['task'];
    $due_date = $_POST['due_date'];
    $category = $_POST['category'];
    $stmt     = $conn->prepare("INSERT INTO tasks (task, due_date, category, user_id) VALUES (?, ?, ?, ?)");
    if (! $stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sssi", $task, $due_date, $category, $user_id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add task: ' . $stmt->error]);
    }
    exit();
}

// Fetch all tasks for the logged-in user
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $user_id = $_SESSION['user_id'];
    $result  = $conn->query("SELECT * FROM tasks WHERE user_id = $user_id");
    if (! $result) {
        die("Query failed: " . $conn->error);
    }
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    echo json_encode($tasks);
    exit();
}

// Delete a task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id   = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    if (! $stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ii", $id, $user_id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete task: ' . $stmt->error]);
    }
    exit();
}

//toggle a task

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['action']) && $_POST['action'] === 'toggle') {
    $id = $_POST['id'];

    // This should become 1 if $_POST['completed'] == '1'
    $completed = $_POST['completed'] ? 1 : 0;

    // or safer:
    // $completed = $_POST['completed'] === '1' ? 1 : 0;

    $stmt = $conn->prepare("UPDATE tasks SET completed = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("iii", $completed, $id, $_SESSION['user_id']);
    $stmt->execute();

    if ($stmt->affected_rows >= 0) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Failed to toggle task: ' . $stmt->error,
        ]);
    }
    exit;
}

// Search and filter tasks
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'search') {
    $search   = $_GET['search'];
    $category = $_GET['category'];
    $query    = "SELECT * FROM tasks WHERE user_id = $user_id";
    if (! empty($search)) {
        $query .= " AND task LIKE '%$search%'";
    }
    if (! empty($category)) {
        $query .= " AND category = '$category'";
    }
    $result = $conn->query($query);
    if (! $result) {
        die("Query failed: " . $conn->error);
    }
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    echo json_encode($tasks);
    exit();
}