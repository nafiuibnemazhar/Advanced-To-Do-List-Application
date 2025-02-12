<h1>Advanced To-Do List</h1>
<p>
  A full‐featured to‐do list application with user authentication, 
  search/filter capabilities, and completion toggling for tasks. 
  Built with <strong>HTML</strong>, <strong>CSS</strong>, <strong>Bootstrap</strong>, 
  <strong>jQuery</strong>, and <strong>PHP</strong> (with MySQL).
</p>

<hr />

<h2>Features</h2>
<ul>
    <li><strong>User Authentication</strong>
        <ul>
            <li>Register new users (passwords hashed with <code>password_hash</code>)</li>
            <li>Secure login with sessions</li>
        </ul>
    </li>
    <li><strong>Add &amp; Delete Tasks</strong>
        <ul>
            <li>Tasks tied to the logged‐in user</li>
            <li>Categories (Work, Personal, Shopping, etc.)</li>
            <li>Optional due date</li>
        </ul>
    </li>
    <li><strong>Search &amp; Category Filtering</strong>
        <ul>
            <li>Search tasks by text</li>
            <li>Filter tasks by category in real time</li>
        </ul>
    </li>
    <li><strong>Toggle Completion</strong>
        <ul>
            <li>Mark tasks as completed or not completed</li>
            <li>Visually see which tasks are finished</li>
        </ul>
    </li>
    <li><strong>Responsive UI</strong> (Bootstrap 5)</li>
</ul>

<hr />

<hr />

<h2>Prerequisites</h2>
<ul>
    <li><strong>PHP 7+</strong> (or higher)</li>
    <li><strong>MySQL</strong> (or MariaDB)</li>
    <li>A web server that runs PHP (e.g., Apache, Nginx)</li>
</ul>

<hr />

<h2>Installation</h2>
<ol>
    <li><strong>Clone the repository</strong>:
        <pre><code>git clone https://github.com/YourUsername/advanced-todo-list.git
cd advanced-todo-list
</code></pre>
    </li>
    <li><strong>Create a database</strong> (e.g., <code>todo_db</code>) in MySQL:
        <pre><code>CREATE DATABASE todo_db;
</code></pre>
    </li>
    <li><strong>Import the SQL schema</strong> (e.g., <code>schema.sql</code> if included) or run queries manually:
        <pre><code>USE todo_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    task VARCHAR(255) NOT NULL,
    due_date DATE,
    category VARCHAR(50),
    completed TINYINT(1) NOT NULL DEFAULT 0
);
</code></pre>
    </li>
    <li><strong>Edit your DB credentials</strong> in <code>db.php</code>:
        <pre><code>$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "todo_db";

$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection...
</code></pre>
    </li>
    <li><strong>Place the project</strong> in your web root 
        (e.g., <code>htdocs</code> or <code>public_html</code>).
    </li>
</ol>

<hr />

<h2>Usage</h2>
<ol>
    <li><strong>Start your local server</strong> and navigate to the project&#39;s URL:
        <pre><code>http://localhost/advanced-todo-list/
</code></pre>
    </li>
    <li><strong>Register a new user</strong> by clicking &quot;Register&quot; 
        (enter a username &amp; password when prompted).
    </li>
    <li><strong>Log in</strong> with your new account.</li>
    <li><strong>Add tasks</strong> using the input fields (description, due date, category).</li>
    <li><strong>Search</strong> for tasks using the search bar.</li>
    <li><strong>Filter</strong> tasks by category from the dropdown.</li>
    <li><strong>Toggle completion</strong> by checking/unchecking the box next to each task.</li>
</ol>

<hr />

<h2>Project Structure</h2>
<pre><code>.
├── index.php        # Main frontend file for UI
├── style.css         # Custom styling
├── db.php            # Database connection
├── api.php           # Server-side logic (login, register, CRUD, search, etc.)
├── ...
└── README.md         # (This file)
</code></pre>

<hr />

<h2>Contributing</h2>
<ol>
    <li>Fork this repository</li>
    <li>Create a feature branch:
        <pre><code>git checkout -b my-new-feature
</code></pre>
    </li>
    <li>Commit your changes:
        <pre><code>git commit -am "Add some feature"
</code></pre>
    </li>
    <li>Push to the branch:
        <pre><code>git push origin my-new-feature
</code></pre>
    </li>
    <li>Create a new Pull Request</li>
</ol>

<hr />

<h2>License</h2>
<p>
Optionally include a license here. For instance:
</p>


</body>
</html>
