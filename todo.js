/* ============================================================================
   PREMIUM TODO APP - ULTRA SMOOTH INTERACTIONS & ANIMATIONS
   Dependencies: jQuery 3.6+
   ============================================================================ */

$(document).ready(function() {
    // ========================================================================
    // GLOBAL STATE & CONFIGURATION
    // ========================================================================
    
    const App = {
        currentFilter: 'all',
        todos: [],
        modal: {
            isOpen: false,
            currentTodo: null
        },
        settings: {
            autoSave: true,
            animations: true,
            notifications: true
        }
    };

    // ========================================================================
    // INITIALIZATION
    // ========================================================================
    
    function init() {
        initEventListeners();
        initDragAndDrop();
        initAnimations();
        loadTodos();
        updateCounts();
        
        // Show welcome animation
        if (App.settings.animations) {
            $('.app-container').addClass('fade-in');
        }
    }

    // ========================================================================
    // EVENT LISTENERS
    // ========================================================================
    
    function initEventListeners() {
        // Navigation
        $('.nav-item').on('click', handleNavigation);
        
        // Search
        $('#search-input').on('input', debounce(handleSearch, 300));
        
        // New Todo
        $('#btn-new-todo').on('click', createNewTodo);
        
        // Todo Cards
        $(document).on('click', '.todo-card', openTodoModal);
        $(document).on('click', '.status-toggle', toggleTodoStatus);
        $(document).on('click', '.card-action-btn.share', shareTodo);
        $(document).on('click', '.card-action-btn.delete', deleteTodo);
        
        // Modal
        $(document).on('click', '.modal-overlay', closeModal);
        $(document).on('click', '.modal-close', closeModal);
        $(document).on('click', '.modal-content', function(e) {
            e.stopPropagation();
        });
        
        // Form handling
        $(document).on('submit', '#todo-form', saveTodo);
        $(document).on('change', '#todo-title, #todo-content', autoSaveTodo);
        
        // File handling
        $(document).on('click', '.file-upload-zone', triggerFileSelect);
        $(document).on('change', '#file-input', handleFileSelect);
        $(document).on('click', '.file-action-btn.delete', deleteFile);
        
        // Settings
        $(document).on('change', '.status-toggle', saveTodoStatus);
        $(document).on('click', '#btn-make-public', togglePublicStatus);
        $(document).on('click', '#btn-copy-link', copyPublicLink);
        $(document).on('click', '#btn-delete-todo', deleteTodoModal);
        
        // Logout
        $('.logout-btn').on('click', logout);
        
        // Keyboard shortcuts
        $(document).on('keydown', handleKeyboardShortcuts);
    }

    // ========================================================================
    // NAVIGATION & FILTERING
    // ========================================================================
    
    function handleNavigation(e) {
        e.preventDefault();
        
        const $item = $(this);
        const filter = $item.data('filter');
        
        // Update active state with smooth transition
        $('.nav-item').removeClass('active');
        $item.addClass('active');
        
        // Update current filter
        App.currentFilter = filter;
        
        // Update page title
        const title = $item.find('.nav-text').text();
        $('.page-title').text(title);
        
        // Filter todos with animation
        filterTodos(filter);
        updateCounts();
        
        // Add ripple effect
        addRippleEffect($item[0], e);
    }
    
    function filterTodos(filter) {
        const $grid = $('.todos-grid');
        const $cards = $('.todo-card');
        
        if (!App.settings.animations) {
            showFilteredTodos(filter);
            return;
        }
        
        // Smooth fade out
        $cards.addClass('fade-out');
        
        setTimeout(() => {
            showFilteredTodos(filter);
            
            // Staggered fade in
            $('.todo-card:visible').each(function(index) {
                const $card = $(this);
                setTimeout(() => {
                    $card.removeClass('fade-out').addClass('fade-in');
                }, index * 50);
            });
        }, 200);
    }
    
    function showFilteredTodos(filter) {
        $('.todo-card').hide();
        
        switch(filter) {
            case 'today':
                $('.todo-card').filter(function() {
                    const createdAt = $(this).data('created-at');
                    return isToday(new Date(createdAt));
                }).show();
                break;
            case 'tomorrow':
                $('.todo-card').filter(function() {
                    const dueDate = $(this).data('due-date');
                    return dueDate && isTomorrow(new Date(dueDate));
                }).show();
                break;
            case 'completed':
                $('.todo-card.completed').show();
                break;
            case 'pending':
                $('.todo-card').not('.completed').show();
                break;
            case 'deleted':
                $('.todo-card.deleted').show();
                break;
            case 'public':
                $('.todo-card').filter(function() {
                    return $(this).data('is-public') === 1;
                }).show();
                break;
            case 'files':
                $('.todo-card').filter(function() {
                    return $(this).data('file-count') > 0;
                }).show();
                break;
            default:
                $('.todo-card').not('.deleted').show();
        }
        
        // Show empty state if no todos
        if ($('.todo-card:visible').length === 0) {
            showEmptyState(filter);
        } else {
            hideEmptyState();
        }
    }

    // ========================================================================
    // SEARCH FUNCTIONALITY
    // ========================================================================
    
    function handleSearch() {
        const query = $('#search-input').val().toLowerCase();
        
        if (!query) {
            filterTodos(App.currentFilter);
            return;
        }
        
        $('.todo-card').each(function() {
            const $card = $(this);
            const title = $card.find('.todo-title').text().toLowerCase();
            const content = $card.find('.todo-content').text().toLowerCase();
            
            if (title.includes(query) || content.includes(query)) {
                $card.show().addClass('search-highlight');
            } else {
                $card.hide();
            }
        });
        
        // Remove highlight after animation
        setTimeout(() => {
            $('.search-highlight').removeClass('search-highlight');
        }, 1000);
    }

    // ========================================================================
    // TODO MANAGEMENT
    // ========================================================================
    
    function loadTodos() {
        showLoading();
        
        $.ajax({
            url: 'todo.php',
            method: 'POST',
            data: { action: 'load_todos' },
            dataType: 'json',
            success: function(response) {
                hideLoading();
                
                if (response.success) {
                    App.todos = response.todos;
                    renderTodos(response.todos);
                    updateCounts();
                } else {
                    showToast('error', 'Virhe ladattaessa tehtäviä');
                }
            },
            error: function() {
                hideLoading();
                showToast('error', 'Verkkovirhe. Yritä uudelleen.');
            }
        });
    }
    
    function renderTodos(todos) {
        const $grid = $('.todos-grid');
        $grid.empty();
        
        if (!todos || todos.length === 0) {
            showEmptyState(App.currentFilter);
            return;
        }
        
        todos.forEach(function(todo, index) {
            const $card = createTodoCard(todo);
            
            if (App.settings.animations) {
                $card.css({
                    opacity: 0,
                    transform: 'translateY(20px)'
                });
                
                setTimeout(() => {
                    $card.css({
                        opacity: 1,
                        transform: 'translateY(0)'
                    });
                }, index * 50);
            }
            
            $grid.append($card);
        });
        
        hideEmptyState();
        filterTodos(App.currentFilter);
    }
    
    function createTodoCard(todo) {
        const completedClass = todo.is_done ? 'completed' : '';
        const deletedClass = todo.is_deleted ? 'deleted' : '';
        const publicBadge = todo.is_public ? '<span class="public-badge">Julkinen</span>' : '';
        
        return $(`
            <div class="todo-card ${completedClass} ${deletedClass}" 
                 data-id="${todo.id}"
                 data-created-at="${todo.created_at}"
                 data-due-date="${todo.due_date || ''}"
                 data-is-public="${todo.is_public}"
                 data-file-count="${todo.file_count || 0}">
                
                <div class="card-header">
                    <div class="card-actions">
                        <button class="card-action-btn share" title="Jaa tehtävä">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z"/>
                            </svg>
                        </button>
                        <button class="card-action-btn delete" title="Poista tehtävä">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="todo-title">${escapeHtml(todo.title || 'Uusi tehtävä')}</div>
                <div class="todo-content">${escapeHtml(todo.content || '').substring(0, 150)}${todo.content && todo.content.length > 150 ? '...' : ''}</div>
                
                ${publicBadge}
                
                <div class="card-footer">
                    <div class="todo-status">
                        <div class="status-toggle ${todo.is_done ? 'active' : ''}" 
                             data-id="${todo.id}" 
                             title="${todo.is_done ? 'Merkitse keskeneräiseksi' : 'Merkitse tehdyksi'}">
                        </div>
                        <span class="status-text">${todo.is_done ? 'Valmis' : 'Kesken'}</span>
                    </div>
                    
                    ${todo.file_count > 0 ? `
                        <div class="file-count">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                            </svg>
                            <span class="file-badge">${todo.file_count}</span>
                        </div>
                    ` : ''}
                </div>
            </div>
        `);
    }
    
    function createNewTodo() {
        const newTodo = {
            id: 'new',
            title: '',
            content: '',
            is_done: false,
            is_public: false,
            files: []
        };
        
        openTodoModal(null, newTodo);
    }

    // ========================================================================
    // MODAL FUNCTIONALITY
    // ========================================================================
    
    function openTodoModal(event, todo = null) {
        if (event) {
            event.stopPropagation();
            
            // Don't open modal if clicking on action buttons
            if ($(event.target).closest('.card-action-btn, .status-toggle').length) {
                return;
            }
        }
        
        const todoData = todo || getTodoData($(this).data('id'));
        if (!todoData) return;
        
        App.modal.currentTodo = todoData;
        App.modal.isOpen = true;
        
        renderModal(todoData);
        
        // Smooth modal animation
        const $overlay = $('.modal-overlay');
        $overlay.addClass('active');
        
        if (App.settings.animations) {
            setTimeout(() => {
                $('.modal-content').addClass('modal-enter');
            }, 50);
        }
        
        // Focus first input
        setTimeout(() => {
            $('#todo-title').focus();
        }, 300);
        
        // Prevent body scroll
        $('body').addClass('modal-open');
    }
    
    function closeModal() {
        if (!App.modal.isOpen) return;
        
        const $overlay = $('.modal-overlay');
        
        if (App.settings.animations) {
            $('.modal-content').removeClass('modal-enter');
            
            setTimeout(() => {
                $overlay.removeClass('active');
                $('.modal-content').remove();
                App.modal.isOpen = false;
                App.modal.currentTodo = null;
            }, 300);
        } else {
            $overlay.removeClass('active');
            $('.modal-content').remove();
            App.modal.isOpen = false;
            App.modal.currentTodo = null;
        }
        
        // Restore body scroll
        $('body').removeClass('modal-open');
    }
    
    function renderModal(todo) {
        const isNewTodo = todo.id === 'new';
        const modalTitle = isNewTodo ? 'Uusi tehtävä' : 'Muokkaa tehtävää';
        
        const modalHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">${modalTitle}</h2>
                    <button class="modal-close" type="button">×</button>
                </div>
                
                <div class="modal-body">
                    <form id="todo-form">
                        <input type="hidden" id="todo-id" value="${todo.id}">
                        
                        <div class="form-row">
                            <div class="form-col">
                                <label for="todo-title" class="form-label">Otsikko</label>
                                <input type="text" 
                                       id="todo-title" 
                                       class="form-input" 
                                       value="${escapeHtml(todo.title || '')}" 
                                       placeholder="Anna tehtävälle kuvaava nimi..."
                                       required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-col">
                                <label for="todo-content" class="form-label">Kuvaus</label>
                                <textarea id="todo-content" 
                                          class="form-input-large" 
                                          placeholder="Kirjoita tarkempi kuvaus tehtävästä...">${escapeHtml(todo.content || '')}</textarea>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-col">
                                <label class="form-label">Tiedostot</label>
                                <div class="file-upload-zone" id="file-upload-zone">
                                    <div class="upload-icon">📁</div>
                                    <div class="upload-text">Raahaa tiedostoja tähän tai klikkaa valitaksesi</div>
                                    <div class="upload-hint">Tuetut: Kuvat, videot, äänitiedostot (max 10MB)</div>
                                </div>
                                <input type="file" id="file-input" multiple accept="image/*,video/*,audio/*" style="display:none;">
                                
                                <div class="files-list" id="files-list">
                                    ${renderFilesList(todo.files || [])}
                                </div>
                            </div>
                        </div>
                        
                        <div class="settings-section">
                            <h3 class="settings-title">
                                ⚙️ Asetukset
                            </h3>
                            
                            <div class="settings-row">
                                <label>Tila</label>
                                <div class="status-toggle ${todo.is_done ? 'active' : ''}" id="modal-status-toggle">
                                </div>
                            </div>
                            
                            <div class="settings-row">
                                <label>Julkinen tehtävä</label>
                                <div class="status-toggle ${todo.is_public ? 'active' : ''}" id="modal-public-toggle">
                                </div>
                            </div>
                            
                            ${todo.is_public ? `
                                <div class="settings-row">
                                    <label>Julkinen linkki</label>
                                    <button type="button" class="btn-primary" id="btn-copy-link">
                                        📋 Kopioi linkki
                                    </button>
                                </div>
                            ` : ''}
                            
                            ${!isNewTodo ? `
                                <div class="settings-row">
                                    <label>Poista tehtävä</label>
                                    <button type="button" class="btn-primary" id="btn-delete-todo" style="background: var(--gradient-danger);">
                                        🗑️ Poista
                                    </button>
                                </div>
                            ` : ''}
                        </div>
                        
                        <div class="form-row">
                            <button type="submit" class="btn-primary">
                                💾 Tallenna tehtävä
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        
        $('.modal-overlay .modal-content').remove();
        $('.modal-overlay').append(modalHTML);
    }
    
    function renderFilesList(files) {
        if (!files || files.length === 0) {
            return '<div class="empty-state-small">Ei tiedostoja</div>';
        }
        
        return files.map(file => `
            <div class="file-item" data-file-id="${file.id}">
                <div class="file-icon">${getFileIcon(file.mime_type)}</div>
                <div class="file-info">
                    <div class="file-name">${escapeHtml(file.original_name)}</div>
                    <div class="file-size">${formatFileSize(file.file_size)}</div>
                </div>
                <div class="file-actions">
                    <button class="file-action-btn delete" data-file-id="${file.id}" title="Poista tiedosto">
                        🗑️
                    </button>
                </div>
            </div>
        `).join('');
    }

    // ========================================================================
    // FILE HANDLING
    // ========================================================================
    
    function initDragAndDrop() {
        $(document).on('dragover dragenter', '.file-upload-zone', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('dragover');
        });
        
        $(document).on('dragleave dragend drop', '.file-upload-zone', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
        });
        
        $(document).on('drop', '.file-upload-zone', function(e) {
            e.preventDefault();
            const files = e.originalEvent.dataTransfer.files;
            
            if (files.length > 0) {
                handleFileUpload(Array.from(files));
            }
        });
    }
    
    function triggerFileSelect() {
        $('#file-input').click();
    }
    
    function handleFileSelect() {
        const files = Array.from(this.files);
        if (files.length > 0) {
            handleFileUpload(files);
        }
    }
    
    function handleFileUpload(files) {
        const todoId = $('#todo-id').val();
        
        if (todoId === 'new') {
            showToast('warning', 'Tallenna tehtävä ensin ennen tiedostojen lisäämistä');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'upload_files');
        formData.append('todo_id', todoId);
        
        files.forEach((file, index) => {
            if (file.size > 10 * 1024 * 1024) { // 10MB limit
                showToast('error', `Tiedosto ${file.name} on liian suuri (max 10MB)`);
                return;
            }
            formData.append(`files[${index}]`, file);
        });
        
        showFileUploadProgress();
        
        $.ajax({
            url: 'todo.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        updateFileUploadProgress(percentComplete);
                    }
                });
                return xhr;
            },
            success: function(response) {
                hideFileUploadProgress();
                
                if (response.success) {
                    showToast('success', `${response.uploaded_count} tiedostoa ladattu`);
                    
                    // Refresh file list
                    loadTodoFiles(todoId);
                    
                    // Update todo card file count
                    updateTodoFileCount(todoId, response.total_files);
                    
                } else {
                    showToast('error', response.message || 'Virhe ladattaessa tiedostoja');
                }
            },
            error: function() {
                hideFileUploadProgress();
                showToast('error', 'Verkkovirhe tiedostoja ladatessa');
            }
        });
    }
    
    function deleteFile() {
        const fileId = $(this).data('file-id');
        const $fileItem = $(this).closest('.file-item');
        
        if (confirm('Haluatko varmasti poistaa tiedoston?')) {
            $.ajax({
                url: 'todo.php',
                method: 'POST',
                data: {
                    action: 'delete_file',
                    file_id: fileId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $fileItem.fadeOut(300, function() {
                            $(this).remove();
                        });
                        showToast('success', 'Tiedosto poistettu');
                    } else {
                        showToast('error', 'Virhe poistettaessa tiedostoa');
                    }
                },
                error: function() {
                    showToast('error', 'Verkkovirhe');
                }
            });
        }
    }

    // ========================================================================
    // TODO ACTIONS
    // ========================================================================
    
    function toggleTodoStatus(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const todoId = $(this).data('id') || $('#todo-id').val();
        const $toggle = $(this);
        const isCompleted = $toggle.hasClass('active');
        
        // Optimistic UI update
        $toggle.toggleClass('active');
        
        $.ajax({
            url: 'todo.php',
            method: 'POST',
            data: {
                action: 'toggle_status',
                todo_id: todoId,
                is_done: !isCompleted ? 1 : 0
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update card state
                    const $card = $(`.todo-card[data-id="${todoId}"]`);
                    $card.toggleClass('completed', !isCompleted);
                    
                    // Update status text
                    $card.find('.status-text').text(!isCompleted ? 'Valmis' : 'Kesken');
                    
                    // Show completion animation
                    if (!isCompleted && App.settings.animations) {
                        addCompletionAnimation($card);
                    }
                    
                    updateCounts();
                    showToast('success', !isCompleted ? 'Tehtävä merkitty valmiiksi! 🎉' : 'Tehtävä merkitty keskeneräiseksi');
                } else {
                    // Revert optimistic update
                    $toggle.toggleClass('active');
                    showToast('error', 'Virhe päivittäessä tehtävää');
                }
            },
            error: function() {
                // Revert optimistic update
                $toggle.toggleClass('active');
                showToast('error', 'Verkkovirhe');
            }
        });
    }
    
    function saveTodo(e) {
        e.preventDefault();
        
        const todoId = $('#todo-id').val();
        const title = $('#todo-title').val().trim();
        const content = $('#todo-content').val().trim();
        const isDone = $('#modal-status-toggle').hasClass('active');
        const isPublic = $('#modal-public-toggle').hasClass('active');
        
        if (!title) {
            showToast('error', 'Otsikko on pakollinen');
            $('#todo-title').focus();
            return;
        }
        
        const data = {
            action: todoId === 'new' ? 'create_todo' : 'update_todo',
            title: title,
            content: content,
            is_done: isDone ? 1 : 0,
            is_public: isPublic ? 1 : 0
        };
        
        if (todoId !== 'new') {
            data.todo_id = todoId;
        }
        
        showLoading();
        
        $.ajax({
            url: 'todo.php',
            method: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                hideLoading();
                
                if (response.success) {
                    showToast('success', todoId === 'new' ? 'Tehtävä luotu! 🎉' : 'Tehtävä päivitetty! ✨');
                    
                    // Update current todo id if it was new
                    if (todoId === 'new') {
                        $('#todo-id').val(response.todo_id);
                        App.modal.currentTodo.id = response.todo_id;
                    }
                    
                    // Refresh todos
                    loadTodos();
                    
                    // Close modal after a short delay
                    setTimeout(() => {
                        closeModal();
                    }, 1000);
                    
                } else {
                    showToast('error', response.message || 'Virhe tallentaessa tehtävää');
                }
            },
            error: function() {
                hideLoading();
                showToast('error', 'Verkkovirhe tallentaessa');
            }
        });
    }
    
    function deleteTodo(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const todoId = $(this).closest('.todo-card').data('id');
        
        if (confirm('Haluatko varmasti poistaa tehtävän?')) {
            $.ajax({
                url: 'todo.php',
                method: 'POST',
                data: {
                    action: 'delete_todo',
                    todo_id: todoId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Animate card removal
                        const $card = $(`.todo-card[data-id="${todoId}"]`);
                        $card.addClass('delete-animation');
                        
                        setTimeout(() => {
                            $card.remove();
                            updateCounts();
                        }, 300);
                        
                        showToast('success', 'Tehtävä poistettu');
                    } else {
                        showToast('error', 'Virhe poistettaessa tehtävää');
                    }
                },
                error: function() {
                    showToast('error', 'Verkkovirhe');
                }
            });
        }
    }
    
    function shareTodo(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const todoId = $(this).closest('.todo-card').data('id');
        
        $.ajax({
            url: 'todo.php',
            method: 'POST',
            data: {
                action: 'get_share_link',
                todo_id: todoId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const shareUrl = window.location.origin + window.location.pathname + '?share=' + response.share_token;
                    
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(shareUrl).then(() => {
                            showToast('success', 'Jakolinkki kopioitu leikepöydälle! 📋');
                        });
                    } else {
                        // Fallback for older browsers
                        prompt('Kopioi tämä linkki:', shareUrl);
                    }
                } else {
                    showToast('error', 'Virhe luotaessa jakolinkkiä');
                }
            },
            error: function() {
                showToast('error', 'Verkkovirhe');
            }
        });
    }

    // ========================================================================
    // AUTO SAVE & FORM HANDLING
    // ========================================================================
    
    function autoSaveTodo() {
        if (!App.settings.autoSave || $('#todo-id').val() === 'new') {
            return;
        }
        
        clearTimeout(App.autoSaveTimer);
        App.autoSaveTimer = setTimeout(() => {
            saveTodoSilent();
        }, 2000);
    }
    
    function saveTodoSilent() {
        const todoId = $('#todo-id').val();
        const title = $('#todo-title').val().trim();
        const content = $('#todo-content').val().trim();
        
        if (!title || todoId === 'new') return;
        
        $.ajax({
            url: 'todo.php',
            method: 'POST',
            data: {
                action: 'update_todo',
                todo_id: todoId,
                title: title,
                content: content
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAutoSaveIndicator();
                }
            }
        });
    }

    // ========================================================================
    // UI HELPERS & ANIMATIONS
    // ========================================================================
    
    function addRippleEffect(element, event) {
        if (!App.settings.animations) return;
        
        const rect = element.getBoundingClientRect();
        const ripple = document.createElement('span');
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;
        
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s ease-out;
            pointer-events: none;
        `;
        
        element.style.position = 'relative';
        element.style.overflow = 'hidden';
        element.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    }
    
    function addCompletionAnimation($card) {
        $card.addClass('completion-celebration');
        
        // Add floating particles
        for (let i = 0; i < 5; i++) {
            setTimeout(() => {
                createFloatingParticle($card);
            }, i * 100);
        }
        
        setTimeout(() => {
            $card.removeClass('completion-celebration');
        }, 2000);
    }
    
    function createFloatingParticle($card) {
        const particle = $('<div class="particle">✨</div>');
        const rect = $card[0].getBoundingClientRect();
        
        particle.css({
            position: 'fixed',
            left: rect.left + Math.random() * rect.width,
            top: rect.top + rect.height / 2,
            fontSize: '20px',
            pointerEvents: 'none',
            zIndex: 9999,
            animation: 'float-up 2s ease-out forwards'
        });
        
        $('body').append(particle);
        
        setTimeout(() => {
            particle.remove();
        }, 2000);
    }
    
    function showLoading(text = 'Ladataan...') {
        const $loading = $(`
            <div class="loading-overlay">
                <div class="loading-content">
                    <div class="loading-spinner"></div>
                    <div class="loading-text">${text}</div>
                </div>
            </div>
        `);
        
        $('body').append($loading);
        
        setTimeout(() => {
            $loading.addClass('show');
        }, 10);
    }
    
    function hideLoading() {
        $('.loading-overlay').removeClass('show');
        setTimeout(() => {
            $('.loading-overlay').remove();
        }, 300);
    }
    
    function showToast(type, message, duration = 4000) {
        if (!App.settings.notifications) return;
        
        const toastId = 'toast-' + Date.now();
        const $toast = $(`
            <div class="toast ${type}" id="${toastId}">
                ${escapeHtml(message)}
            </div>
        `);
        
        let $container = $('.toast-container');
        if (!$container.length) {
            $container = $('<div class="toast-container"></div>');
            $('body').append($container);
        }
        
        $container.append($toast);
        
        // Animate in
        setTimeout(() => {
            $toast.addClass('show');
        }, 10);
        
        // Auto remove
        setTimeout(() => {
            $toast.removeClass('show');
            setTimeout(() => {
                $toast.remove();
                
                // Remove container if empty
                if ($container.children().length === 0) {
                    $container.remove();
                }
            }, 300);
        }, duration);
    }
    
    function showEmptyState(filter) {
        const emptyStates = {
            'all': {
                icon: '📝',
                title: 'Ei tehtäviä',
                message: 'Luo ensimmäinen tehtäväsi aloittaaksesi!'
            },
            'today': {
                icon: '📅',
                title: 'Ei tämän päivän tehtäviä',
                message: 'Kaikki tämän päivän työt tehty!'
            },
            'completed': {
                icon: '✅', 
                title: 'Ei valmistuneita tehtäviä',
                message: 'Merkitse tehtäviä valmiiksi nähdäksesi ne täällä.'
            },
            'files': {
                icon: '📁',
                title: 'Ei tiedostoja',
                message: 'Lataa tiedostoja tehtäviisi nähdäksesi ne täällä.'
            }
        };
        
        const state = emptyStates[filter] || emptyStates['all'];
        
        const $emptyState = $(`
            <div class="empty-state">
                <div class="empty-state-icon">${state.icon}</div>
                <h3>${state.title}</h3>
                <p>${state.message}</p>
                <button class="btn-primary" onclick="createNewTodo()">
                    ➕ Luo uusi tehtävä
                </button>
            </div>
        `);
        
        $('.todos-grid').html($emptyState);
    }
    
    function hideEmptyState() {
        $('.empty-state').remove();
    }
    
    function updateCounts() {
        const counts = {
            all: $('.todo-card').not('.deleted').length,
            today: $('.todo-card').filter(function() {
                return isToday(new Date($(this).data('created-at')));
            }).length,
            completed: $('.todo-card.completed').not('.deleted').length,
            pending: $('.todo-card').not('.completed, .deleted').length,
            deleted: $('.todo-card.deleted').length,
            public: $('.todo-card').filter('[data-is-public="1"]').not('.deleted').length,
            files: $('.todo-card').filter(function() {
                return $(this).data('file-count') > 0;
            }).not('.deleted').length
        };
        
        // Update navigation badges
        $('.nav-item[data-filter="all"] .nav-badge').text(counts.all);
        $('.nav-item[data-filter="today"] .nav-badge').text(counts.today);
        $('.nav-item[data-filter="completed"] .nav-badge').text(counts.completed);
        $('.nav-item[data-filter="pending"] .nav-badge').text(counts.pending);
        $('.nav-item[data-filter="deleted"] .nav-badge').text(counts.deleted);
        $('.nav-item[data-filter="public"] .nav-badge').text(counts.public);
        $('.nav-item[data-filter="files"] .nav-badge').text(counts.files);
    }

    // ========================================================================
    // KEYBOARD SHORTCUTS
    // ========================================================================
    
    function handleKeyboardShortcuts(e) {
        // Ctrl/Cmd + N = New todo
        if ((e.ctrlKey || e.metaKey) && e.which === 78) {
            e.preventDefault();
            createNewTodo();
        }
        
        // Ctrl/Cmd + F = Focus search
        if ((e.ctrlKey || e.metaKey) && e.which === 70) {
            e.preventDefault();
            $('#search-input').focus();
        }
        
        // Escape = Close modal
        if (e.which === 27 && App.modal.isOpen) {
            closeModal();
        }
        
        // Ctrl/Cmd + S = Save todo (when modal is open)
        if ((e.ctrlKey || e.metaKey) && e.which === 83 && App.modal.isOpen) {
            e.preventDefault();
            $('#todo-form').submit();
        }
    }

    // ======================================================================== 
    // UTILITY FUNCTIONS
    // ========================================================================
    
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        
        return text ? text.replace(/[&<>"']/g, function(m) { return map[m]; }) : '';
    }
    
    function isToday(date) {
        const today = new Date();
        return date.toDateString() === today.toDateString();
    }
    
    function isTomorrow(date) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        return date.toDateString() === tomorrow.toDateString();
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }
    
    function getFileIcon(mimeType) {
        if (!mimeType) return '📄';
        
        if (mimeType.startsWith('image/')) return '🖼️';
        if (mimeType.startsWith('video/')) return '🎥';
        if (mimeType.startsWith('audio/')) return '🎵';
        if (mimeType.includes('pdf')) return '📕';
        if (mimeType.includes('word')) return '📝';
        if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return '📊';
        
        return '📄';
    }
    
    function getTodoData(todoId) {
        return App.todos.find(todo => todo.id == todoId);
    }
    
    function logout() {
        if (confirm('Haluatko varmasti kirjautua ulos?')) {
            window.location.href = 'todo.php?action=logout';
        }
    }

    // ========================================================================
    // INITIALIZATION & ANIMATIONS
    // ========================================================================
    
    function initAnimations() {
        // Add CSS keyframes dynamically
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                0% { transform: scale(0); opacity: 1; }
                100% { transform: scale(4); opacity: 0; }
            }
            
            @keyframes float-up {
                0% { transform: translateY(0) scale(1); opacity: 1; }
                100% { transform: translateY(-100px) scale(0); opacity: 0; }
            }
            
            .fade-out {
                opacity: 0 !important;
                transform: scale(0.95) !important;
            }
            
            .delete-animation {
                animation: deleteSlide 0.3s ease-out forwards;
            }
            
            @keyframes deleteSlide {
                0% { transform: translateX(0) scale(1); opacity: 1; }
                100% { transform: translateX(-100px) scale(0.8); opacity: 0; }
            }
            
            .completion-celebration {
                animation: celebrationPulse 0.6s ease-out;
            }
            
            @keyframes celebrationPulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
            }
            
            .search-highlight {
                animation: searchHighlight 1s ease-out;
            }
            
            @keyframes searchHighlight {
                0%, 100% { background: var(--bg-card); }
                50% { background: rgba(0, 212, 170, 0.1); }
            }
            
            .modal-enter {
                animation: modalSlideIn 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            }
            
            @keyframes modalSlideIn {
                0% { 
                    transform: translate(-50%, -50%) scale(0.9);
                    opacity: 0;
                }
                100% { 
                    transform: translate(-50%, -50%) scale(1);
                    opacity: 1;
                }
            }
            
            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.7);
                backdrop-filter: blur(5px);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            
            .loading-overlay.show {
                opacity: 1;
            }
            
            .loading-content {
                background: var(--bg-secondary);
                border: 1px solid var(--glass-border);
                border-radius: var(--radius-lg);
                padding: var(--space-xl);
                text-align: center;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: var(--space-md);
            }
            
            .loading-text {
                color: var(--text-secondary);
                font-weight: 500;
            }
        `;
        document.head.appendChild(style);
    }

    // Start the application
    init();
});

// ============================================================================
// GLOBAL FUNCTIONS (accessible from HTML)
// ============================================================================

window.createNewTodo = function() {
    $(document).trigger('click', '#btn-new-todo');
};