<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="todo-container">
        <!-- Login/Register Form -->
        <div id="auth-section">
            <h2 class="text-center mb-4">Login or Register</h2>
            <form id="login-form" class="mb-3">
                <input type="text" id="username" class="form-control mb-2" placeholder="Username" required>
                <input type="password" id="password" class="form-control mb-2" placeholder="Password" required>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <button id="show-register" class="btn btn-secondary w-100">Register</button>
        </div>

        <!-- To-Do List (Hidden by Default) -->
        <div id="todo-section" style="display: none;">
            <h1 class="text-center mb-4">My Personal To-Do List</h1>
            <form id="add-task-form" class="mb-4">
                <div class="input-group mb-2">
                    <input type="text" id="task-input" class="form-control" placeholder="Add a new task" required>
                    <input type="date" id="due-date" class="form-control">
                    <select id="category" class="form-control">
                        <option value="">Select Category</option>
                        <option value="Work">Work</option>
                        <option value="Personal">Personal</option>
                        <option value="Shopping">Shopping</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Add Task</button>
                </div>
            </form>
            <div class="input-group mb-4">
                <input type="text" id="search-input" class="form-control" placeholder="Search tasks">
                <select id="filter-category" class="form-control">
                    <option value="">All Categories</option>
                    <option value="Work">Work</option>
                    <option value="Personal">Personal</option>
                    <option value="Shopping">Shopping</option>
                </select>
            </div>
            <div id="task-list"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    // Register
    $('#show-register').click(function() {
        let username = prompt("Enter a username:");
        let password = prompt("Enter a password:");
        if (username && password) {
            $.post('api.php', {
                action: 'register',
                username: username,
                password: password
            }, function(response) {
                if (response.status === 'success') {
                    alert(response.message || "Registration successful! Please login.");
                } else {
                    alert(response.message || "Registration failed. Please try again.");
                }
            }).fail(function() {
                alert("An error occurred. Please try again.");
            });
        }
    });

    // Login
    $('#login-form').submit(function(e) {
        e.preventDefault();
        let username = $('#username').val();
        let password = $('#password').val();
        $.post('api.php', {
            action: 'login',
            username: username,
            password: password
        }, function(response) {
            if (response.status === 'success') {
                $('#auth-section').hide();
                $('#todo-section').show();
                fetchTasks();
            } else {
                alert(response.message || "Invalid credentials.");
            }
        }).fail(function() {
            alert("An error occurred. Please try again.");
        });
    });

    // Fetch and display tasks
    function fetchTasks() {
        let searchValue = $('#search-input').val() || '';
        let categoryValue = $('#filter-category').val() || '';

        $.ajax({
            url: 'api.php',
            method: 'GET',
            dataType: 'json',
            data: {
                action: 'search', // <--- use "search" not "fetch"
                search: searchValue,
                category: categoryValue
            },
            success: function(tasks) {
                renderTasks(tasks);
            },
            error: function() {
                alert('Failed to fetch tasks.');
            }
        });
    }

    // Re‐render tasks
    function renderTasks(tasks) {
        let taskList = $('#task-list');
        taskList.empty();

        tasks.forEach(task => {
            // Convert it to an integer or boolean first.
            // e.g., in JSON, sometimes 'completed' might be returned as a string.
            let isCompleted = (parseInt(task.completed) === 1);

            let taskItem = $(`
        <div class="task-item ${isCompleted ? 'completed' : ''}" data-id="${task.id}">
            <span>${task.task} (${task.category}) - Due: ${task.due_date}</span>
            <div class="task-actions">
                <input type="checkbox" class="form-check-input toggle-task"
                       ${isCompleted ? 'checked' : ''}>
                <button class="btn btn-danger btn-sm delete-task">Delete</button>
            </div>
        </div>
    `);
            taskList.append(taskItem);
        });
    }

    // Add a new task
    $('#add-task-form').submit(function(e) {
        e.preventDefault();
        let task = $('#task-input').val();
        let due_date = $('#due-date').val();
        let category = $('#category').val();
        $.post('api.php', {
            action: 'add',
            task: task,
            due_date: due_date,
            category: category
        }, function(response) {
            if (response.status === 'success') {
                fetchTasks();
                $('#task-input').val('');
                $('#due-date').val('');
                $('#category').val('');
            } else {
                alert(response.message || "Failed to add task.");
            }
        }).fail(function() {
            alert("An error occurred. Please try again.");
        });
    });

    // Delete a task
    $(document).on('click', '.delete-task', function() {
        let taskId = $(this).closest('.task-item').data('id');
        $.post('api.php', {
            action: 'delete',
            id: taskId
        }, function(response) {
            if (response.status === 'success') {
                fetchTasks();
            } else {
                alert(response.message || "Failed to delete task.");
            }
        }).fail(function() {
            alert("An error occurred. Please try again.");
        });
    });

    // Toggle task completion
    $(document).on('change', '.toggle-task', function() {
        let taskId = $(this).closest('.task-item').data('id');
        let completed = $(this).is(':checked') ? 1 : 0;

        console.log("TOGGLE:", {
            taskId,
            completed
        }); // Add this for debugging

        $.post('api.php', {
            action: 'toggle',
            id: taskId,
            completed: completed
        }, function(response) {
            console.log("RESPONSE:", response); // Another debug print

            if (response.status === 'success') {
                fetchTasks();
            } else {
                alert(response.message || "Failed to toggle task.");
            }
        }).fail(function() {
            alert("An error occurred. Please try again.");
        });
    });


    // Search and filter tasks
    $('#search-input, #filter-category').on('input change', function() {
        fetchTasks();
    });

    // Initial fetch
    $(document).ready(function() {
        fetchTasks();
    });
    </script>
</body>

</html>
