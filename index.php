<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>
    <div class="todo-container">
        <!-- Login/Register Form -->
        <div id="auth-section">
            <h2 class="text-center mb-4">Login or Register</h2>
            <form id="login=form" class="mb-3">
                <input type="text" id="username" class="form-control mb-2" placeholder="Username" required>
                <input type="password" id="password" class="form-control mb-2" placeholder="Password" required>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <button id="show-register" class="btn btn-secondary w-100">Register</button>
        </div>
    </div>
</body>

</html>