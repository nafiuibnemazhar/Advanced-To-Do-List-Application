<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced To-Do List</title>
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
            <h1 class="text-center mb-4">Advanced To-Do List</h1>
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
                alert(response.message);
            }
        });
    });

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
                    alert("Registration successful! Please login.");
                } else {
                    alert(response.message);
                }
            });
        }
    });

    // Fetch and display tasks
    function fetchTasks() {
        let search = $('#search-input').val();
        let category = $('#filter-category').val();
        $.get('api.php', {
            action: 'search',
            search: search,
            category: category
        }, function(data) {
            let tasks = JSON.parse(data);
            let taskList = $('#task-list');
            taskList.empty();
            tasks.forEach(task => {
                let taskItem = $(`
                        <div class="task-item ${task.completed ? 'completed' : ''}" data-id="${task.id}">
                            <span>${task.task} (${task.category}) - Due: ${task.due_date}</span>
                            <div class="task-actions">
                                <input type="checkbox" class="form-check-input toggle-task" ${task.completed ? 'checked' : ''}>
                                <button class="btn btn-danger btn-sm delete-task">Delete</button>
                            </div>
                        </div>
                    `);
                taskList.append(taskItem);
            });
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
            fetchTasks();
            $('#task-input').val('');
            $('#due-date').val('');
            $('#category').val('');
        });
    });

    // Delete a task
    $(document).on('click', '.delete-task', function() {
        let taskId = $(this).closest('.task-item').data('id');
        $.post('api.php', {
            action: 'delete',
            id: taskId
        }, function(response) {
            fetchTasks();
        });
    });

    // Toggle task completion
    $(document).on('change', '.toggle-task', function() {
        let taskId = $(this).closest('.task-item').data('id');
        let completed = $(this).is(':checked') ? 1 : 0;
        $.post('api.php', {
            action: 'toggle',
            id: taskId,
            completed: completed
        }, function(response) {
            fetchTasks();
        });
    });

    // Search and filter tasks
    $('#search-input, #filter-category').on('input change', function() {
        fetchTasks();
    });
    </script>
</body>

</html>