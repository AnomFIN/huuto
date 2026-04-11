<?php
/*
============================================================================
PREMIUM TODO APP - MAIN APPLICATION
Ultra-smooth SaaS-level task management with premium design
============================================================================
*/

// Error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Load configuration
$configFile = __DIR__ . '/todo_config.php';
if (!file_exists($configFile)) {
    header('Location: todo_install.php');
    exit;
}
require_once $configFile;

// Check if installed
if (!defined('TODO_INSTALLED') || !TODO_INSTALLED) {
    header('Location: todo_install.php');
    exit;
}

// Database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die('Tietokantayhteys epäonnistui: ' . $e->getMessage());
}

// ============================================================================
// SECURITY & AUTHENTICATION
// ============================================================================

function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        if (isAjaxRequest()) {
            jsonResponse(['success' => false, 'error' => 'Kirjautuminen vaaditaan']);
        } else {
            showLoginPage();
            exit;
        }
    }
}

function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function jsonResponse($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function sanitizeInput($input) {
    return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
}

function generateShareToken() {
    return bin2hex(random_bytes(32));
}

// ============================================================================
// FILE HANDLING
// ============================================================================

function getAllowedMimeTypes() {
    return [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'video/mp4', 'video/webm', 'video/ogg',
        'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/mpeg'
    ];
}

function isAllowedFileType($mimeType) {
    return in_array($mimeType, getAllowedMimeTypes());
}

function generateSafeFileName($originalName) {
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
    $timestamp = time();
    $random = bin2hex(random_bytes(4));
    
    return $safeName . '_' . $timestamp . '_' . $random . '.' . $extension;
}

// ============================================================================
// AJAX HANDLERS
// ============================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    // Handle logout
    if ($action === 'logout') {
        session_destroy();
        header('Location: todo.php');
        exit;
    }
    
    // Handle login
    if ($action === 'login') {
        $password = $_POST['password'] ?? '';
        
        if ($password === LOGIN_PASSWORD) {
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            jsonResponse(['success' => true]);
        } else {
            jsonResponse(['success' => false, 'error' => 'Väärä salasana']);
        }
    }
    
    // All other actions require login
    requireLogin();
    
    try {
        switch ($action) {
            case 'load_todos':
                handleLoadTodos();
                break;
                
            case 'create_todo':
                handleCreateTodo();
                break;
                
            case 'update_todo':
                handleUpdateTodo();
                break;
                
            case 'delete_todo':
                handleDeleteTodo();
                break;
                
            case 'toggle_status':
                handleToggleStatus();
                break;
                
            case 'upload_files':
                handleUploadFiles();
                break;
                
            case 'delete_file':
                handleDeleteFile();
                break;
                
            case 'get_share_link':
                handleGetShareLink();
                break;
                
            default:
                jsonResponse(['success' => false, 'error' => 'Tuntematon toiminto']);
        }
    } catch (Exception $e) {
        error_log('Todo App Error: ' . $e->getMessage());
        jsonResponse(['success' => false, 'error' => 'Palvelinvirhe']);
    }
}

function handleLoadTodos() {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT t.*, 
               COUNT(f.id) as file_count
        FROM todos t 
        LEFT JOIN todo_files f ON t.id = f.todo_id 
        WHERE t.is_deleted = 0
        GROUP BY t.id
        ORDER BY t.updated_at DESC
    ");
    $stmt->execute();
    $todos = $stmt->fetchAll();
    
    // Load files for each todo
    foreach ($todos as &$todo) {
        $fileStmt = $pdo->prepare("SELECT * FROM todo_files WHERE todo_id = ? ORDER BY created_at DESC");
        $fileStmt->execute([$todo['id']]);
        $todo['files'] = $fileStmt->fetchAll();
    }
    
    jsonResponse(['success' => true, 'todos' => $todos]);
}

function handleCreateTodo() {
    global $pdo;
    
    $title = sanitizeInput($_POST['title'] ?? '');
    $content = sanitizeInput($_POST['content'] ?? '');
    $isDone = (int)($_POST['is_done'] ?? 0);
    $isPublic = (int)($_POST['is_public'] ?? 0);
    
    if (empty($title)) {
        jsonResponse(['success' => false, 'error' => 'Otsikko on pakollinen']);
    }
    
    $shareToken = $isPublic ? generateShareToken() : null;
    
    $stmt = $pdo->prepare("
        INSERT INTO todos (title, content, is_done, is_public, share_token) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$title, $content, $isDone, $isPublic, $shareToken]);
    
    $todoId = $pdo->lastInsertId();
    
    jsonResponse(['success' => true, 'todo_id' => $todoId]);
}

function handleUpdateTodo() {
    global $pdo;
    
    $todoId = (int)($_POST['todo_id'] ?? 0);
    $title = sanitizeInput($_POST['title'] ?? '');
    $content = sanitizeInput($_POST['content'] ?? '');
    $isDone = (int)($_POST['is_done'] ?? 0);
    $isPublic = (int)($_POST['is_public'] ?? 0);
    
    if (empty($title)) {
        jsonResponse(['success' => false, 'error' => 'Otsikko on pakollinen']);
    }
    
    // Generate share token if making public
    $shareTokenUpdate = '';
    if ($isPublic) {
        $stmt = $pdo->prepare("SELECT share_token FROM todos WHERE id = ?");
        $stmt->execute([$todoId]);
        $currentToken = $stmt->fetchColumn();
        
        if (!$currentToken) {
            $shareTokenUpdate = ', share_token = ?';
            $shareToken = generateShareToken();
        }
    }
    
    $sql = "UPDATE todos SET title = ?, content = ?, is_done = ?, is_public = ?" . $shareTokenUpdate . " WHERE id = ?";
    $params = [$title, $content, $isDone, $isPublic];
    
    if ($shareTokenUpdate) {
        $params[] = $shareToken;
    }
    
    $params[] = $todoId;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    jsonResponse(['success' => true]);
}

function handleDeleteTodo() {
    global $pdo;
    
    $todoId = (int)($_POST['todo_id'] ?? 0);
    
    // Soft delete
    $stmt = $pdo->prepare("UPDATE todos SET is_deleted = 1 WHERE id = ?");
    $stmt->execute([$todoId]);
    
    jsonResponse(['success' => true]);
}

function handleToggleStatus() {
    global $pdo;
    
    $todoId = (int)($_POST['todo_id'] ?? 0);
    $isDone = (int)($_POST['is_done'] ?? 0);
    
    $stmt = $pdo->prepare("UPDATE todos SET is_done = ? WHERE id = ?");
    $stmt->execute([$isDone, $todoId]);
    
    jsonResponse(['success' => true]);
}

function handleUploadFiles() {
    global $pdo;
    
    $todoId = (int)($_POST['todo_id'] ?? 0);
    
    if (!isset($_FILES['files'])) {
        jsonResponse(['success' => false, 'error' => 'Ei tiedostoja']);
    }
    
    $uploadDir = UPLOAD_DIR;
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $uploadedCount = 0;
    $files = $_FILES['files'];
    
    // Handle multiple files
    if (is_array($files['name'])) {
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $originalName = $files['name'][$i];
                $tmpPath = $files['tmp_name'][$i];
                $mimeType = $files['type'][$i];
                $fileSize = $files['size'][$i];
                
                if (uploadSingleFile($pdo, $todoId, $originalName, $tmpPath, $mimeType, $fileSize, $uploadDir)) {
                    $uploadedCount++;
                }
            }
        }
    }
    
    // Get total file count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM todo_files WHERE todo_id = ?");
    $stmt->execute([$todoId]);
    $totalFiles = $stmt->fetchColumn();
    
    jsonResponse([
        'success' => true, 
        'uploaded_count' => $uploadedCount,
        'total_files' => $totalFiles
    ]);
}

function uploadSingleFile($pdo, $todoId, $originalName, $tmpPath, $mimeType, $fileSize, $uploadDir) {
    // Validate file
    if ($fileSize > UPLOAD_MAX_SIZE) {
        return false;
    }
    
    if (!isAllowedFileType($mimeType)) {
        return false;
    }
    
    // Generate safe filename
    $storedName = generateSafeFileName($originalName);
    $fullPath = $uploadDir . $storedName;
    
    // Move uploaded file
    if (!move_uploaded_file($tmpPath, $fullPath)) {
        return false;
    }
    
    // Save to database
    $stmt = $pdo->prepare("
        INSERT INTO todo_files (todo_id, original_name, stored_name, mime_type, file_size) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    return $stmt->execute([$todoId, $originalName, $storedName, $mimeType, $fileSize]);
}

function handleDeleteFile() {
    global $pdo;
    
    $fileId = (int)($_POST['file_id'] ?? 0);
    
    // Get file info
    $stmt = $pdo->prepare("SELECT stored_name FROM todo_files WHERE id = ?");
    $stmt->execute([$fileId]);
    $storedName = $stmt->fetchColumn();
    
    if ($storedName) {
        // Delete physical file
        $filePath = UPLOAD_DIR . $storedName;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM todo_files WHERE id = ?");
        $stmt->execute([$fileId]);
    }
    
    jsonResponse(['success' => true]);
}

function handleGetShareLink() {
    global $pdo;
    
    $todoId = (int)($_POST['todo_id'] ?? 0);
    
    // Get or create share token
    $stmt = $pdo->prepare("SELECT share_token, is_public FROM todos WHERE id = ?");
    $stmt->execute([$todoId]);
    $todo = $stmt->fetch();
    
    if (!$todo) {
        jsonResponse(['success' => false, 'error' => 'Tehtävää ei löytynyt']);
    }
    
    $shareToken = $todo['share_token'];
    
    if (!$shareToken) {
        $shareToken = generateShareToken();
        $stmt = $pdo->prepare("UPDATE todos SET share_token = ?, is_public = 1 WHERE id = ?");
        $stmt->execute([$shareToken, $todoId]);
    } else if (!$todo['is_public']) {
        $stmt = $pdo->prepare("UPDATE todos SET is_public = 1 WHERE id = ?");
        $stmt->execute([$todoId]);
    }
    
    jsonResponse(['success' => true, 'share_token' => $shareToken]);
}

// ============================================================================
// PUBLIC SHARING
// ============================================================================

if (isset($_GET['share'])) {
    $shareToken = $_GET['share'];
    
    $stmt = $pdo->prepare("
        SELECT t.*, COUNT(f.id) as file_count
        FROM todos t 
        LEFT JOIN todo_files f ON t.id = f.todo_id 
        WHERE t.share_token = ? AND t.is_public = 1 AND t.is_deleted = 0
        GROUP BY t.id
    ");
    $stmt->execute([$shareToken]);
    $todo = $stmt->fetch();
    
    if (!$todo) {
        http_response_code(404);
        echo '<!DOCTYPE html>
        <html><head><title>Ei löytynyt</title><meta charset="UTF-8">
        <link rel="stylesheet" href="todo.css"></head><body>
        <div class="login-container">
            <div class="login-panel">
                <h1>❌ Tehtävää ei löytynyt</h1>
                <p>Linkki on virheellinen tai tehtävä on poistettu.</p>
            </div>
        </div></body></html>';
        exit;
    }
    
    // Load files
    $fileStmt = $pdo->prepare("SELECT * FROM todo_files WHERE todo_id = ? ORDER BY created_at DESC");
    $fileStmt->execute([$todo['id']]);
    $files = $fileStmt->fetchAll();
    
    showPublicTodo($todo, $files);
    exit;
}

function showPublicTodo($todo, $files) {
    ?>
    <!DOCTYPE html>
    <html lang="fi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($todo['title']); ?> - Premium Todo</title>
        <link rel="stylesheet" href="todo.css">
    </head>
    <body>
        <div class="login-container">
            <div class="login-panel" style="max-width: 800px;">
                <div class="login-logo">
                    <h1 style="font-size: 2rem;">📋</h1>
                    <h2 style="margin: 1rem 0; color: var(--text-primary);">
                        <?php echo htmlspecialchars($todo['title']); ?>
                    </h2>
                    <?php if ($todo['is_done']): ?>
                        <div style="color: var(--accent); font-weight: 600; margin-bottom: 1rem;">
                            ✅ Valmis
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($todo['content']): ?>
                    <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem; border: 1px solid rgba(255,255,255,0.1);">
                        <p style="color: var(--text-secondary); line-height: 1.6; margin: 0;">
                            <?php echo nl2br(htmlspecialchars($todo['content'])); ?>
                        </p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($files)): ?>
                    <div style="margin-bottom: 2rem;">
                        <h3 style="color: var(--text-secondary); margin-bottom: 1rem; font-size: 1rem;">
                            📁 Liitteet (<?php echo count($files); ?>)
                        </h3>
                        <div class="files-list"><?php foreach ($files as $file): ?>
                            <div class="file-item">
                                <div class="file-icon"><?php echo getFileIconForType($file['mime_type']); ?></div>
                                <div class="file-info">
                                    <div class="file-name"><?php echo htmlspecialchars($file['original_name']); ?></div>
                                    <div class="file-size"><?php echo formatBytes($file['file_size']); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?></div>
                    </div>
                <?php endif; ?>
                
                <div style="text-align: center; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1);">
                    <small style="color: var(--text-tertiary);">
                        Jaettu Premium Todo -sovelluksesta<br>
                        Luotu: <?php echo date('d.m.Y H:i', strtotime($todo['created_at'])); ?>
                    </small>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
}

function getFileIconForType($mimeType) {
    if (strpos($mimeType, 'image/') === 0) return '🖼️';
    if (strpos($mimeType, 'video/') === 0) return '🎥';
    if (strpos($mimeType, 'audio/') === 0) return '🎵';
    return '📄';
}

function formatBytes($size, $precision = 1) {
    $units = ['B', 'KB', 'MB', 'GB'];
    for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    return round($size, $precision) . ' ' . $units[$i];
}

// ============================================================================
// MAIN APPLICATION
// ============================================================================

// Check if user is logged in
if (!isLoggedIn()) {
    showLoginPage();
    exit;
}

function showLoginPage() {
    ?>
    <!DOCTYPE html>
    <html lang="fi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Kirjaudu sisään - Premium Todo</title>
        <link rel="stylesheet" href="todo.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body>
        <div class="login-container">
            <div class="login-panel">
                <div class="login-logo">
                    <h1>Premium Todo</h1>
                    <p>Ultra-smooth task management</p>
                </div>
                
                <form class="login-form" id="loginForm">
                    <div class="form-group">
                        <label for="password">Salasana</label>
                        <input type="password" id="password" name="password" class="form-input" 
                               placeholder="Anna salasana..." required autofocus>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        Kirjaudu sisään
                    </button>
                </form>
                
                <div id="loginError" class="error-message" style="display: none;"></div>
            </div>
        </div>
        
        <script>
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                
                const password = $('#password').val();
                const $btn = $('.btn-login');
                const originalText = $btn.text();
                
                $btn.text('Kirjaudutaan...').prop('disabled', true);
                $('#loginError').hide();
                
                $.post('todo.php', {
                    action: 'login',
                    password: password
                })
                .done(function(response) {
                    if (response.success) {
                        window.location.reload();
                    } else {
                        $('#loginError').text(response.error || 'Kirjautuminen epäonnistui').show();
                        $btn.text(originalText).prop('disabled', false);
                        $('#password').focus().select();
                    }
                })
                .fail(function() {
                    $('#loginError').text('Verkkovirhe. Yritä uudelleen.').show();
                    $btn.text(originalText).prop('disabled', false);
                });
            });
        </script>
    </body>
    </html>
    <?php
}

// Show main application
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="todo.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <h2>Premium Todo</h2>
                    <p>Task Management</p>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <h3>Tehtävät</h3>
                    
                    <div class="nav-item active" data-filter="all">
                        <div class="nav-item-content">
                            <span class="nav-icon">📝</span>
                            <span class="nav-text">Kaikki tehtävät</span>
                        </div>
                        <span class="nav-badge">0</span>
                    </div>
                    
                    <div class="nav-item" data-filter="today">
                        <div class="nav-item-content">
                            <span class="nav-icon">📅</span>
                            <span class="nav-text">Tänään</span>
                        </div>
                        <span class="nav-badge">0</span>
                    </div>
                    
                    <div class="nav-item" data-filter="tomorrow">
                        <div class="nav-item-content">
                            <span class="nav-icon">⏰</span>
                            <span class="nav-text">Huomenna</span>
                        </div>
                        <span class="nav-badge">0</span>
                    </div>
                    
                    <div class="nav-item" data-filter="pending">
                        <div class="nav-item-content">
                            <span class="nav-icon">🔄</span>
                            <span class="nav-text">Ei tehty</span>
                        </div>
                        <span class="nav-badge">0</span>
                    </div>
                    
                    <div class="nav-item" data-filter="completed">
                        <div class="nav-item-content">
                            <span class="nav-icon">✅</span>
                            <span class="nav-text">Tehdyt</span>
                        </div>
                        <span class="nav-badge">0</span>
                    </div>
                </div>
                
                <div class="nav-section">
                    <h3>Sisältö</h3>
                    
                    <div class="nav-item" data-filter="files">
                        <div class="nav-item-content">
                            <span class="nav-icon">📁</span>
                            <span class="nav-text">Tiedostot</span>
                        </div>
                        <span class="nav-badge">0</span>
                    </div>
                    
                    <div class="nav-item" data-filter="public">
                        <div class="nav-item-content">
                            <span class="nav-icon">🌐</span>
                            <span class="nav-text">Julkiset</span>
                        </div>
                        <span class="nav-badge">0</span>
                    </div>
                </div>
                
                <div class="nav-section">
                    <h3>Hallinta</h3>
                    
                    <div class="nav-item" data-filter="deleted">
                        <div class="nav-item-content">
                            <span class="nav-icon">🗑️</span>
                            <span class="nav-text">Poistetut</span>
                        </div>
                        <span class="nav-badge">0</span>
                    </div>
                </div>
            </nav>
            
            <div class="sidebar-footer">
                <button class="logout-btn">
                    🚪 Kirjaudu ulos
                </button>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1 class="page-title">Kaikki tehtävät</h1>
                
                <div class="top-actions">
                    <div class="search-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                        <input type="search" id="search-input" placeholder="Hae tehtäviä...">
                    </div>
                    
                    <button class="btn-primary" id="btn-new-todo">
                        ➕ Uusi tehtävä
                    </button>
                </div>
            </header>
            
            <div class="content-area">
                <div class="todos-grid">
                    <!-- Todos will be loaded here by JavaScript -->
                </div>
            </div>
        </main>
    </div>
    
    <!-- Modal Overlay (will be populated by JavaScript) -->
    <div class="modal-overlay"></div>
    
    <!-- Loading & Toast containers will be created by JavaScript -->
    
    <script src="todo.js"></script>
</body>
</html>