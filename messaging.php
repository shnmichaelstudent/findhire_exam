<?php
session_start();
include_once 'models.php';
include_once 'handleForms.php';

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$userRole = $_SESSION['user']['role'];

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $receiverId = $_POST['receiver_id'];
    $message = trim($_POST['message']);
    
    if (!empty($message)) {
        sendMessage($userId, $receiverId, $message);
    }
}

$selectedReceiverId = isset($_GET['receiver_id']) ? $_GET['receiver_id'] : null;
$conversations = getConversations($userId);
$messages = [];
if ($selectedReceiverId) {
    $messages = getMessagesBetweenUsers($userId, $selectedReceiverId);
    $selectedUser = getUserById($selectedReceiverId);
}

$hrUsers = [];
if ($userRole == 'Applicant') {
    $hrUsers = getAllHRUsers();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Base Colors and Variables */
:root {
    --bg-dark:rgb(96, 27, 207); /* Updated for pure black background */
    --bg-secondary:rgb(103, 23, 139); /* Slightly lighter black for contrast */
    --bg-card:rgb(30, 173, 192); /* Darker card background */
    --text-primary:rgb(23, 26, 167); /* Bright green for primary text */
    --text-secondary:rgb(84, 20, 233); /* Slightly muted green for secondary text */
    --text-muted:rgb(10, 13, 184); /* Even more muted green */
    --primary-color:rgb(25, 116, 235); /* Bright green for accents */
    --primary-hover:rgba(125, 173, 12, 0.66); /* Slightly darker green for hover effects */
    --error-color:rgb(121, 22, 187); /* Red for error messages */
    --success-color:rgb(11, 101, 175); /* Green for success messages */
    --link-color:rgb(31, 99, 201); /* Slightly lighter green for links */
    --link-hover:rgb(82, 25, 240); /* Hover effect for links */
    --border-color:rgb(49, 34, 184); /* Dark gray for borders */
}

/* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background-color: var(--bg-dark);
    color: var(--text-primary);
    font-family: 'Inter', sans-serif;
    line-height: 1.5;
}

/* Container */
.container {
    max-width: 1200px;
    margin: 32px auto;
    padding: 24px;
    background: var(--bg-secondary);
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(8, 21, 141, 0.3);
}

/* Typography */
h1, h2, h3, h4, h5, h6 {
    margin-bottom: 16px;
    color: var(--text-primary);
    font-weight: 700;
}

p {
    margin-bottom: 16px;
    color: var(--text-secondary);
}

.text-muted {
    color: var(--text-muted);
}

/* Buttons */
.button {
    display: inline-block;
    background-color: var(--primary-color);
    color: var(--bg-dark);
    padding: 12px 24px;
    font-size: 14px;
    font-weight: bold;
    text-align: center;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.button:hover {
    background-color: var(--primary-hover);
    transform: translateY(-2px);
}

.button.success {
    background-color: var(--success-color);
}

.button.error {
    background-color: var(--error-color);
}

/* Form Elements */
.input, .textarea, .select {
    width: 100%;
    padding: 12px;
    font-size: 14px;
    background-color: var(--bg-card);
    color: var(--text-primary);
    border: 2px solid var(--border-color);
    border-radius: 8px;
    transition: border-color 0.3s ease;
    margin-bottom: 16px;
}

.input:focus, .textarea:focus, .select:focus {
    border-color: var(--primary-color);
    outline: none;
}

.label {
    margin-bottom: 8px;
    color: var(--text-secondary);
    font-size: 13px;
    font-weight: 500;
}

/* Cards */
.card {
    background-color: var(--bg-card);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-5px);
}

/* Navigation */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: var(--bg-secondary);
    padding: 16px 24px;
    border-bottom: 1px solid var(--border-color);
}

.nav-link {
    color: var(--text-primary);
    text-decoration: none;
    margin: 0 16px;
    font-weight: bold;
    transition: color 0.3s ease;
}

.nav-link:hover {
    color: var(--primary-hover);
}

/* Sidebar */
.sidebar {
    background-color: var(--bg-secondary);
    padding: 24px;
    border-right: 1px solid var(--border-color);
}

.sidebar-item {
    display: block;
    color: var(--text-primary);
    text-decoration: none;
    padding: 12px 16px;
    margin-bottom: 8px;
    border-radius: 6px;
    transition: background-color 0.3s ease;
}

.sidebar-item:hover {
    background-color: var(--primary-color);
}

/* Alerts */
.alert {
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: bold;
}

.alert.success {
    background-color: rgba(47, 14, 233, 0.1);
    color: var(--success-color);
}

.alert.error {
    background-color: rgba(26, 12, 214, 0.1);
    color: var(--error-color);
}

/* Links */
.link {
    color: var(--link-color);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.link:hover {
    color: var(--link-hover);
}

/* Scrollbars */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background-color: var(--bg-card);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background-color: var(--primary-color);
    border-radius: 4px;
}

/* Utility */
.text-center {
    text-align: center;
}

.hidden {
    display: none;
}
    </style>
</head>
<body>
    <div class="app-container">
        <div class="top-bar">
            <div class="top-bar-title">Messages</div>
            <div class="nav-actions">
                <a href="<?php echo ($userRole == 'Applicant') ? 'applicant_Dashboard.php' : 'hr_Dashboard.php'; ?>" class="nav-link">Dashboard</a>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>

        <div class="messages-container">
            <div class="conversations-list">
                <h2 class="section-title">Conversations</h2>
                <?php if ($userRole == 'Applicant' && !empty($hrUsers)): ?>
                    <h3 class="section-title">HR Representatives</h3>
                    <?php foreach ($hrUsers as $hr): ?>
                        <div class="conversation-item" onclick="window.location.href='messaging.php?receiver_id=<?= $hr['id'] ?>'">
                            <?= htmlspecialchars($hr['username']) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (!empty($conversations)): ?>
                    <h3 class="section-title">Recent Chats</h3>
                    <?php foreach ($conversations as $conversation): ?>
                        <div class="conversation-item" onclick="window.location.href='messaging.php?receiver_id=<?= $conversation['id'] ?>'">
                            <?= htmlspecialchars($conversation['username']) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="messages-area">
                <?php if ($selectedReceiverId): ?>
                    <h2 class="section-title">Chat with <?= htmlspecialchars($selectedUser['username']) ?></h2>
                    <div class="messages-list">
                        <?php if (empty($messages)): ?>
                            <div class="no-messages">No messages yet. Start the conversation!</div>
                        <?php else: ?>
                            <?php foreach ($messages as $message): ?>
                                <div class="message-box <?= $message['sender_id'] == $userId ? 'sent-message' : 'received-message' ?>">
                                    <p><?= htmlspecialchars($message['message']) ?></p>
                                    <div class="timestamp"><?= date('M d, Y H:i', strtotime($message['timestamp'])) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <form method="POST" class="message-form">
                        <input type="hidden" name="receiver_id" value="<?= $selectedReceiverId ?>">
                        <textarea name="message" class="message-input" rows="3" placeholder="Type your message..." required></textarea>
                        <button type="submit" class="btn-send">Send Message</button>
                    </form>
                <?php else: ?>
                    <div class="no-messages">Select a conversation to start messaging</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom of messages
        const messagesList = document.querySelector('.messages-list');
        if (messagesList) {
            messagesList.scrollTop = messagesList.scrollHeight;
        }
    </script>
</body>
</html>
