<?php
session_start();

if ($_SESSION['user']['role'] != 'HR') {
    header("Location: index.php");
    exit();
}
include_once 'handleForms.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    createJobPost($title, $description);
    header("Location: hr_Dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Job Post</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body class="dark-theme">
    <div class="app-container">
        <header class="header">
            <div class="header-brand">
                <h1>JobConnect</h1>
            </div>
            <nav class="header-nav">
                <a href="hr_Dashboard.php" class="btn-secondary">← Back to Dashboard</a>
            </nav>
        </header>

        <main class="main-content">
            <section class="form-section">
                <h2 class="section-title">Create New Job Post</h2>
                <form action="add_JobPost.php" method="POST" class="job-form">
                    <div class="form-group">
                        <label for="title" class="form-label">Position Title</label>
                        <input type="text" id="title" name="title" class="form-input" placeholder="Enter job position title" required>
                    </div>
                    <div class="form-group">
                        <label for="description" class="form-label">Job Description</label>
                        <textarea id="description" name="description" class="form-textarea" placeholder="Enter detailed job description" rows="6" required></textarea>
                    </div>
                    <button type="submit" class="btn-primary">Create Position</button>
                </form>
            </section>
        </main>
    </div>
</body>

</html>
