/* ============================================================================
   PREMIUM TODO APP - ULTRA SMOOTH INTERACTIONS & ANIMATIONS
   Dependencies: jQuery 3.6+
   ============================================================================ */

$(document).ready(function() {
    console.log('🚀 TODO.JS LOADED SUCCESSFULLY!'); // Debug log
    
    // ========================================================================
    // GLOBAL STATE & CONFIGURATION
    // ========================================================================
    
    const App = {
        currentFilter: 'home',
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
        console.log('🚀 Initializing TODO app...');
        
        detectMobileDevice();
        initEventListeners();
        initMobileNavigation();
        initMobileFAB();
        initTouchInteractions();
        initDragAndDrop();
        initAnimations();
        loadTodos();
        updateCounts();
        
        // Show welcome animation
        if (App.settings.animations) {
            $('.app-container').addClass('fade-in');
        }
        
        console.log('🚀 TODO app initialization complete!');
    }

    // ========================================================================
    // MOBILE DEVICE DETECTION
    // ========================================================================

    function detectMobileDevice() {
        const ua = navigator.userAgent;
        App.isTouchDevice = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);
        App.isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua) || (window.innerWidth <= 768);

        if (App.isMobile || App.isTouchDevice) {
            document.body.classList.add('is-mobile');
        }

        // Re-evaluate on resize (tablet rotation etc.)
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const wasMobile = App.isMobile;
                App.isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua) || (window.innerWidth <= 768);
                if (App.isMobile || App.isTouchDevice) {
                    document.body.classList.add('is-mobile');
                } else {
                    document.body.classList.remove('is-mobile');
                }
                // Toggle FAB visibility
                if (App.isMobile !== wasMobile) {
                    $('.mobile-fab').toggle(App.isMobile);
                }
            }, 150);
        });
    }

    // ========================================================================
    // FLOATING ACTION BUTTON (FAB)
    // ========================================================================

    function initMobileFAB() {
        if ($('.mobile-fab').length > 0) return;

        const fab = `
            <div class="mobile-fab">
                <div class="fab-option">
                    <span class="fab-option-label">Lataa tiedosto</span>
                    <button class="fab-option-btn upload" id="fab-upload" title="Lataa tiedosto">📎</button>
                </div>
                <div class="fab-option">
                    <span class="fab-option-label">Uusi tehtävä</span>
                    <button class="fab-option-btn new-todo" id="fab-new-todo" title="Uusi tehtävä">✏️</button>
                </div>
                <button class="fab-main" id="fab-toggle" title="Valikko">+</button>
            </div>
        `;
        $('body').append(fab);

        // Toggle expand
        $(document).on('click', '#fab-toggle', function(e) {
            e.stopPropagation();
            $(this).toggleClass('open');
            $('.mobile-fab').toggleClass('expanded');
        });

        // New todo from FAB
        $(document).on('click', '#fab-new-todo', function(e) {
            e.stopPropagation();
            closeFAB();
            createNewTodo();
        });

        // Upload from FAB
        $(document).on('click', '#fab-upload', function(e) {
            e.stopPropagation();
            closeFAB();
            openDirectUpload();
        });

        // Close FAB on outside tap
        $(document).on('click', function() {
            if ($('.mobile-fab').hasClass('expanded')) {
                closeFAB();
            }
        });

        // Prevent FAB area clicks from propagating
        $(document).on('click', '.mobile-fab', function(e) {
            e.stopPropagation();
        });
    }

    function closeFAB() {
        $('#fab-toggle').removeClass('open');
        $('.mobile-fab').removeClass('expanded');
    }

    // ========================================================================
    // TOUCH INTERACTIONS
    // ========================================================================

    function initTouchInteractions() {
        if (!App.isTouchDevice) return;

        let touchStartX = 0;
        let touchStartY = 0;
        let touchCard = null;
        const SWIPE_THRESHOLD = 80;

        $(document).on('touchstart', '.todo-card', function(e) {
            touchCard = this;
            const touch = e.originalEvent.touches[0];
            touchStartX = touch.clientX;
            touchStartY = touch.clientY;
        });

        $(document).on('touchmove', '.todo-card', function(e) {
            if (!touchCard) return;
            const touch = e.originalEvent.touches[0];
            const dx = touch.clientX - touchStartX;
            const dy = touch.clientY - touchStartY;

            // Only track horizontal swipes (not scroll)
            if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 20) {
                e.preventDefault();
                const $card = $(touchCard);
                if (dx < -20) {
                    $card.addClass('swiping-left').removeClass('swiping-right');
                } else if (dx > 20) {
                    $card.addClass('swiping-right').removeClass('swiping-left');
                }
                // Visual shift with limit
                const shift = Math.max(-60, Math.min(60, dx * 0.4));
                $card.css('transform', `translateX(${shift}px)`);
            }
        });

        $(document).on('touchend', '.todo-card', function(e) {
            if (!touchCard) return;
            const $card = $(touchCard);
            const touch = e.originalEvent.changedTouches[0];
            const dx = touch.clientX - touchStartX;

            $card.css('transform', '').removeClass('swiping-left swiping-right');

            if (dx < -SWIPE_THRESHOLD) {
                // Swipe left → delete
                const todoId = $card.data('id');
                if (todoId && confirm('Poistetaanko tehtävä?')) {
                    deleteTodoById(todoId);
                }
            } else if (dx > SWIPE_THRESHOLD) {
                // Swipe right → toggle status
                const $toggle = $card.find('.status-toggle');
                if ($toggle.length) $toggle.trigger('click');
            }

            touchCard = null;
        });

        // Add sheet handle to editor sidebar on mobile
        addEditorBottomSheetHandle();
    }

    function addEditorBottomSheetHandle() {
        // Adding it via JS so it only appears on touch devices
        const observer = new MutationObserver(function() {
            const $sidebar = $('.editor-sidebar');
            if ($sidebar.length && !$sidebar.find('.editor-sidebar-sheet-handle').length) {
                $sidebar.prepend('<div class="editor-sidebar-sheet-handle"></div>');
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    function deleteTodoById(todoId) {
        $.ajax({
            url: 'todo.php',
            method: 'POST',
            data: { action: 'delete', todo_id: todoId },
            success: function() {
                $(`.todo-card[data-id="${todoId}"]`).slideUp(300, function() {
                    $(this).remove();
                    App.todos = App.todos.filter(t => t.id != todoId);
                    updateCounts();
                });
                showToast('Tehtävä poistettu', 'success');
            },
            error: function() {
                showToast('Poistaminen epäonnistui', 'error');
            }
        });
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
        
        // Upload Files
        $('#btn-upload-files').on('click', openDirectUpload);
        
        // Todo Cards
        $(document).on('click', '.todo-card', handleTodoClick);
        $(document).on('click', '.status-toggle', toggleTodoStatus);
        $(document).on('click', '.card-action-btn.share', shareTodo);
        $(document).on('click', '.card-action-btn.delete', deleteTodo);
        
        // Fullscreen Editor
        $(document).on('click', '#editor-back', closeFullscreenEditor);
        $(document).on('click', '#editor-save', saveEditorTodo);
        $(document).on('click', '.panel-header', toggleEditorPanel);
        $(document).on('click', '#editor-file-zone', function() { $('#editor-file-input').click(); });
        $(document).on('change', '#editor-file-input', function() { handleFileUpload(Array.from(this.files)); });
        $(document).on('click', '#editor-delete', deleteEditorTodo);
        $(document).on('click', '#editor-copy-link', copyEditorShareLink);
        $(document).on('click', '#editor-status-toggle', function() { $(this).toggleClass('active'); });
        $(document).on('click', '#editor-public-toggle', function() { $(this).toggleClass('active'); });
        $(document).on('click', '.toolbar-btn', handleToolbarBtn);
        $(document).on('change', '#toolbar-font-size', function() { $('#editor-content').css('font-size', $(this).val() + 'px'); });
        $(document).on('change', '#toolbar-font-family', function() { $('#editor-content').css('font-family', $(this).val()); });
        $(document).on('input', '#editor-title, #editor-content', autoSaveTodo);
        
        // File handling (drag & drop)
        $(document).on('click', '.file-upload-zone', triggerFileSelect);
        $(document).on('change', '#file-input', handleFileSelect);
        $(document).on('click', '.file-action-btn.delete', deleteFile);
        
        // Logout
        $('.logout-btn').on('click', logout);
        
        // Keyboard shortcuts
        $(document).on('keydown', handleKeyboardShortcuts);
    }

    // ========================================================================
    // MOBILE NAVIGATION
    // ========================================================================
    
    function initMobileNavigation() {
        // Create mobile header if it doesn't exist
        if ($('.mobile-header').length === 0) {
            const mobileHeader = `
                <div class="mobile-header">
                    <div class="mobile-logo">
                        <img src="todo.png" alt="Todo" class="mobile-logo-img" onerror="this.style.display='none'">
                        <span class="mobile-logo-text">Todo</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:4px">
                        <button class="mobile-search-toggle" id="mobile-search-toggle" title="Haku">🔍</button>
                        <button class="hamburger-menu" id="hamburger-toggle">
                            <span class="hamburger-line"></span>
                            <span class="hamburger-line"></span>
                            <span class="hamburger-line"></span>
                        </button>
                    </div>
                </div>
                <div class="mobile-overlay" id="mobile-overlay"></div>
            `;
            $('body').prepend(mobileHeader);
        }

        // Mobile search toggle
        $(document).on('click', '#mobile-search-toggle', function() {
            const $searchBox = $('.search-box');
            $searchBox.slideToggle(200);
            if ($searchBox.is(':visible')) {
                setTimeout(() => $searchBox.find('input').focus(), 210);
            }
        });
        
        // Hamburger menu toggle
        $(document).on('click', '#hamburger-toggle', function() {
            const $hamburger = $(this);
            const $sidebar = $('.sidebar');
            const $overlay = $('#mobile-overlay');
            
            $hamburger.toggleClass('active');
            $sidebar.toggleClass('mobile-open');
            $overlay.toggleClass('active');
            
            // Prevent body scroll when menu is open
            if ($sidebar.hasClass('mobile-open')) {
                $('body').addClass('modal-open');
            } else {
                $('body').removeClass('modal-open');
            }
        });
        
        // Close mobile menu when overlay is clicked
        $(document).on('click', '#mobile-overlay', function() {
            $('#hamburger-toggle').removeClass('active');
            $('.sidebar').removeClass('mobile-open');
            $(this).removeClass('active');
            $('body').removeClass('modal-open');
        });
        
        // Close mobile menu when navigation item is clicked
        $(document).on('click', '.nav-item', function() {
            if (window.innerWidth <= 768) {
                setTimeout(() => {
                    $('#hamburger-toggle').removeClass('active');
                    $('.sidebar').removeClass('mobile-open');
                    $('#mobile-overlay').removeClass('active');
                    $('body').removeClass('modal-open');
                }, 300);
            }
        });
        
        // Handle window resize
        $(window).on('resize', function() {
            if (window.innerWidth > 768) {
                $('#hamburger-toggle').removeClass('active');
                $('.sidebar').removeClass('mobile-open');
                $('#mobile-overlay').removeClass('active');
                $('body').removeClass('modal-open');
            }
        });
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
        
        // Load todos with new filter (server-side filtering)
        loadTodos(filter);
        
        // Add ripple effect
        addRippleEffect($item[0], e);
    }
    


    // ========================================================================
    // SEARCH FUNCTIONALITY
    // ========================================================================
    
    function handleSearch() {
        const query = $('#search-input').val();
        const currentFilter = App.currentFilter || 'all';
        
        // Load todos with search and current filter
        loadTodos(currentFilter, query);
    }

    // ========================================================================
    // TODO MANAGEMENT
    // ========================================================================
    
    function loadTodos(filter = null, search = '') {
        showLoading();
        
        const currentFilter = filter || App.currentFilter;
        
        $.ajax({
            url: 'todo.php',
            method: 'POST',
            data: { 
                action: 'load_todos',
                filter: currentFilter,
                search: search
            },
            dataType: 'json',
            success: function(response) {
                hideLoading();
                
                if (response.success) {
                    App.todos = response.todos;
                    App.currentFilter = response.filter;
                    renderTodos(response.todos);
                    updateCounts(); // This will fetch fresh counts from server
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
                
                <div class="todo-title">${escapeHtml((todo.title || 'Uusi tehtävä').replace(/<[^>]*>/g, ''))}</div>
                <div class="todo-content">${escapeHtml((todo.content || '').replace(/<[^>]*>/g, '').substring(0, 150))}${todo.content && todo.content.replace(/<[^>]*>/g, '').length > 150 ? '...' : ''}</div>
                
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
        $.ajax({
            url: 'todo.php',
            method: 'POST',
            data: { action: 'create_todo', title: 'Uusi tehtävä', content: '', is_done: 0, is_public: 0 },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    openFullscreenEditor({
                        id: response.todo_id,
                        title: 'Uusi tehtävä',
                        content: '',
                        is_done: 0,
                        is_public: 0,
                        files: []
                    });
                } else {
                    showToast('error', 'Virhe luotaessa tehtävää');
                }
            },
            error: function() { showToast('error', 'Verkkovirhe'); }
        });
    }
    
    function openDirectUpload() {
        $.ajax({
            url: 'todo.php',
            method: 'POST',
            data: { action: 'create_todo', title: 'Tiedostot', content: '', is_done: 0, is_public: 0 },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    openFullscreenEditor({
                        id: response.todo_id,
                        title: 'Tiedostot',
                        content: '',
                        is_done: 0,
                        is_public: 0,
                        files: []
                    }, 'files');
                } else {
                    showToast('error', 'Virhe');
                }
            },
            error: function() { showToast('error', 'Verkkovirhe'); }
        });
    }

    // ========================================================================
    // FULLSCREEN EDITOR
    // ========================================================================
    
    function handleTodoClick(event) {
        if ($(event.target).closest('.card-action-btn, .status-toggle').length) return;
        const todoId = $(this).data('id');
        const todoData = getTodoData(todoId);
        if (!todoData) return;
        openFullscreenEditor(todoData);
    }
    
    function openFullscreenEditor(todo, openPanel) {
        App.modal.isOpen = true;
        App.modal.currentTodo = todo;
        
        $('.top-bar').addClass('hidden');
        $('#todos-grid').addClass('hidden');
        
        renderFullscreenEditor(todo);
        $('#fullscreen-editor').removeClass('hidden');
        
        if (openPanel) {
            setTimeout(function() {
                const $panel = $(`.editor-panel[data-panel="${openPanel}"]`);
                $panel.addClass('open');
                $panel.find('.panel-body').slideDown(300);
                if (openPanel === 'files') {
                    setTimeout(function() { $('#editor-file-input').click(); }, 400);
                }
            }, 200);
        }
        
        setTimeout(function() { $('#editor-title').focus(); }, 300);
    }
    
    function closeFullscreenEditor() {
        App.modal.isOpen = false;
        App.modal.currentTodo = null;
        
        $('#fullscreen-editor').addClass('hidden').empty();
        $('.top-bar').removeClass('hidden');
        $('#todos-grid').removeClass('hidden');
        
        loadTodos();
    }
    
    function renderFullscreenEditor(todo) {
        const isNew = !todo.id || todo.id === 'new';
        const html = `
            <div class="editor-header">
                <button class="editor-back" id="editor-back">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Takaisin
                </button>
                <div class="editor-header-actions">
                    <span class="editor-autosave" id="editor-autosave"></span>
                    <button class="btn-primary" id="editor-save">💾 Tallenna</button>
                </div>
            </div>
            
            <div class="editor-layout">
                <div class="editor-main">
                    <input type="hidden" id="editor-todo-id" value="${todo.id || ''}">
                    <input type="text" class="editor-title-input" id="editor-title"
                           value="${escapeHtml(todo.title || '')}"
                           placeholder="Tehtävän nimi...">
                    
                    <div class="editor-toolbar" id="editor-toolbar">
                        <button class="toolbar-btn" data-cmd="bold" title="Lihavointi"><b>B</b></button>
                        <button class="toolbar-btn" data-cmd="italic" title="Kursiivi"><i>I</i></button>
                        <button class="toolbar-btn" data-cmd="underline" title="Alleviivaus"><u>U</u></button>
                        <div class="toolbar-sep"></div>
                        <select class="toolbar-select" id="toolbar-font-size" title="Fonttikoko">
                            <option value="14">14px</option>
                            <option value="16" selected>16px</option>
                            <option value="18">18px</option>
                            <option value="22">22px</option>
                            <option value="28">28px</option>
                            <option value="36">36px</option>
                        </select>
                        <select class="toolbar-select" id="toolbar-font-family" title="Fontti">
                            <option value="inherit">Oletus</option>
                            <option value="Georgia, serif">Georgia</option>
                            <option value="'Courier New', monospace">Monospace</option>
                            <option value="'Trebuchet MS', sans-serif">Trebuchet</option>
                        </select>
                        <div class="toolbar-sep"></div>
                        <button class="toolbar-btn" data-cmd="insertUnorderedList" title="Lista">☰</button>
                        <button class="toolbar-btn" data-cmd="insertOrderedList" title="Numerolista">1.</button>
                    </div>
                    
                    <div class="editor-content" id="editor-content" contenteditable="true"
                         data-placeholder="Kirjoita sisältö tähän...">${todo.content || ''}</div>
                </div>
                
                <aside class="editor-sidebar" id="editor-sidebar">
                    <div class="editor-panel" data-panel="status">
                        <div class="panel-header">
                            <span>⚡ Tila</span>
                            <span class="panel-chevron">›</span>
                        </div>
                        <div class="panel-body">
                            <div class="panel-toggle-row">
                                <span>Valmis</span>
                                <div class="status-toggle ${todo.is_done ? 'active' : ''}" id="editor-status-toggle"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="editor-panel" data-panel="privacy">
                        <div class="panel-header">
                            <span>🌐 Julkisuus</span>
                            <span class="panel-chevron">›</span>
                        </div>
                        <div class="panel-body">
                            <div class="panel-toggle-row">
                                <span>Julkinen</span>
                                <div class="status-toggle ${todo.is_public ? 'active' : ''}" id="editor-public-toggle"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="editor-panel" data-panel="share">
                        <div class="panel-header">
                            <span>🔗 Jako</span>
                            <span class="panel-chevron">›</span>
                        </div>
                        <div class="panel-body">
                            <button class="panel-action-btn" id="editor-copy-link">📋 Kopioi jakolinkki</button>
                        </div>
                    </div>
                    
                    <div class="editor-panel" data-panel="files">
                        <div class="panel-header">
                            <span>📁 Tiedostot <span class="panel-count" id="editor-file-count">${(todo.files||[]).length || ''}</span></span>
                            <span class="panel-chevron">›</span>
                        </div>
                        <div class="panel-body">
                            <div class="file-upload-zone editor-upload-zone" id="editor-file-zone">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="17 8 12 3 7 8"/>
                                    <line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                                <span>Raahaa tai klikkaa</span>
                            </div>
                            <input type="file" id="editor-file-input" multiple accept="image/*,video/*,audio/*" style="display:none;">
                            <div class="files-list" id="editor-files-list">
                                ${renderFilesList(todo.files || [])}
                            </div>
                        </div>
                    </div>
                    
                    <div class="editor-panel" data-panel="images">
                        <div class="panel-header">
                            <span>🖼️ Kuvat</span>
                            <span class="panel-chevron">›</span>
                        </div>
                        <div class="panel-body">
                            <div class="panel-placeholder">Kuvatoiminto tulossa pian</div>
                        </div>
                    </div>
                    
                    <div class="editor-panel" data-panel="comments">
                        <div class="panel-header">
                            <span>💬 Kommentit</span>
                            <span class="panel-chevron">›</span>
                        </div>
                        <div class="panel-body">
                            <div class="panel-placeholder">Kommenttitoiminto tulossa pian</div>
                        </div>
                    </div>
                    
                    ${!isNew ? `
                    <div class="editor-panel danger" data-panel="delete">
                        <div class="panel-header">
                            <span>🗑️ Poista tehtävä</span>
                            <span class="panel-chevron">›</span>
                        </div>
                        <div class="panel-body">
                            <button class="panel-action-btn danger" id="editor-delete">Poista pysyvästi</button>
                        </div>
                    </div>
                    ` : ''}
                </aside>
            </div>
        `;
        
        $('#fullscreen-editor').html(html);
    }
    
    function toggleEditorPanel() {
        const $panel = $(this).closest('.editor-panel');
        $panel.toggleClass('open');
        $panel.find('.panel-body').slideToggle(250);
    }
    
    function handleToolbarBtn() {
        const cmd = $(this).data('cmd');
        document.execCommand(cmd, false, null);
        $(this).toggleClass('active');
        $('#editor-content').focus();
    }
    
    function saveEditorTodo(e) {
        if (e) e.preventDefault();
        
        const todoId = $('#editor-todo-id').val();
        const title = $('#editor-title').val().trim();
        const content = $('#editor-content').html().trim();
        const isDone = $('#editor-status-toggle').hasClass('active') ? 1 : 0;
        const isPublic = $('#editor-public-toggle').hasClass('active') ? 1 : 0;
        
        if (!title) {
            showToast('error', 'Otsikko on pakollinen');
            $('#editor-title').focus();
            return;
        }
        
        const isNew = !todoId || todoId === 'new';
        const data = {
            action: isNew ? 'create_todo' : 'update_todo',
            title: title,
            content: content,
            is_done: isDone,
            is_public: isPublic
        };
        if (!isNew) data.todo_id = todoId;
        
        $.ajax({
            url: 'todo.php',
            method: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (isNew && response.todo_id) {
                        $('#editor-todo-id').val(response.todo_id);
                        App.modal.currentTodo.id = response.todo_id;
                    }
                    showToast('success', 'Tallennettu! ✨');
                } else {
                    showToast('error', response.error || 'Virhe tallennettaessa');
                }
            },
            error: function() { showToast('error', 'Verkkovirhe'); }
        });
    }
    
    function deleteEditorTodo() {
        const todoId = $('#editor-todo-id').val();
        if (!todoId) return;
        
        if (confirm('Haluatko varmasti poistaa tehtävän?')) {
            $.ajax({
                url: 'todo.php',
                method: 'POST',
                data: { action: 'delete_todo', todo_id: todoId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showToast('success', 'Tehtävä poistettu');
                        closeFullscreenEditor();
                    }
                }
            });
        }
    }
    
    function copyEditorShareLink() {
        const todoId = $('#editor-todo-id').val();
        if (!todoId) return;
        
        $.ajax({
            url: 'todo.php',
            method: 'POST',
            data: { action: 'get_share_link', todo_id: todoId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const url = window.location.origin + window.location.pathname + '?share=' + response.share_token;
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(url).then(function() {
                            showToast('success', 'Jakolinkki kopioitu! 📋');
                        });
                    } else {
                        prompt('Kopioi linkki:', url);
                    }
                }
            }
        });
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
        var $input = $('#editor-file-input');
        if (!$input.length) $input = $('#file-input');
        $input.click();
    }
    
    function handleFileSelect() {
        const files = Array.from(this.files);
        if (files.length > 0) {
            handleFileUpload(files);
        }
    }
    
    function handleFileUpload(files) {
        var todoId = $('#editor-todo-id').val() || $('#todo-id').val();
        
        if (!todoId || todoId === 'new') {
            var title = ($('#editor-title').val() || '').trim() || 'Tiedostot';
            $.ajax({
                url: 'todo.php',
                method: 'POST',
                data: { action: 'create_todo', title: title, content: '', is_done: 0, is_public: 0 },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#editor-todo-id').val(response.todo_id);
                        doFileUpload(response.todo_id, files);
                    } else {
                        showToast('error', 'Virhe luotaessa tehtävää');
                    }
                },
                error: function() { showToast('error', 'Verkkovirhe'); }
            });
            return;
        }
        
        doFileUpload(todoId, files);
    }
    
    function doFileUpload(todoId, files) {
        const formData = new FormData();
        formData.append('action', 'upload_files');
        formData.append('todo_id', todoId);
        
        files.forEach((file, index) => {
            if (file.size > 10 * 1024 * 1024) { // 10MB limit
                showToast('error', `Tiedosto ${file.name} on liian suuri (max 10MB)`);
                return;
            }
formData.append('files[]', file);
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
        if (!App.settings.autoSave || !App.modal.isOpen) return;
        var todoId = $('#editor-todo-id').val();
        if (!todoId || todoId === 'new') return;
        
        clearTimeout(App.autoSaveTimer);
        App.autoSaveTimer = setTimeout(function() {
            saveTodoSilent();
        }, 2000);
    }
    
    function saveTodoSilent() {
        var todoId = $('#editor-todo-id').val();
        var title = ($('#editor-title').val() || '').trim();
        var content = ($('#editor-content').html() || '').trim();
        
        if (!title || !todoId || todoId === 'new') return;
        
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
    // MISSING HELPER FUNCTIONS
    // ========================================================================

    function showFileUploadProgress() {
        var $zone = $('#editor-file-zone').length ? $('#editor-file-zone') : $('#file-upload-zone');
        $zone.addClass('uploading');
        $zone.html(`
            <div class="upload-progress-container">
                <div class="upload-progress-ring">
                    <svg viewBox="0 0 80 80">
                        <circle class="progress-bg" cx="40" cy="40" r="35" />
                        <circle class="progress-fill" cx="40" cy="40" r="35" id="upload-progress-circle" />
                    </svg>
                    <span class="progress-percent" id="upload-percent">0%</span>
                </div>
                <div class="upload-progress-text">Ladataan tiedostoja...</div>
            </div>
        `);
    }

    function updateFileUploadProgress(percent) {
        var circle = document.getElementById('upload-progress-circle');
        if (circle) {
            var circumference = 2 * Math.PI * 35;
            var offset = circumference - (percent / 100) * circumference;
            circle.style.strokeDasharray = circumference;
            circle.style.strokeDashoffset = offset;
        }
        $('#upload-percent').text(Math.round(percent) + '%');
    }

    function hideFileUploadProgress() {
        var $zone = $('#editor-file-zone').length ? $('#editor-file-zone') : $('#file-upload-zone');
        $zone.removeClass('uploading');
        if ($zone.attr('id') === 'editor-file-zone') {
            $zone.html(`
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                <span>Raahaa tai klikkaa</span>
            `);
        } else {
            $zone.html(`
                <div class="upload-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                </div>
                <div class="upload-text">Raahaa tiedostoja tähän tai klikkaa valitaksesi</div>
                <div class="upload-hint">Kuvat, videot, äänitiedostot &mdash; max 10 MB</div>
            `);
        }
    }

    function loadTodoFiles(todoId) {
        $.ajax({
            url: 'todo.php',
            method: 'POST',
            data: { action: 'load_todos', filter: 'all' },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.todos) {
                    var todo = response.todos.find(function(t) { return t.id == todoId; });
                    if (todo && todo.files) {
                        var html = renderFilesList(todo.files);
                        $('#files-list').html(html);
                        $('#editor-files-list').html(html);
                        $('#editor-file-count').text(todo.files.length || '');
                    }
                }
            }
        });
    }

    function updateTodoFileCount(todoId, count) {
        var $card = $('.todo-card[data-id="' + todoId + '"]');
        $card.attr('data-file-count', count);
        if (count > 0) {
            var $footer = $card.find('.card-footer');
            $footer.find('.file-count').remove();
            $footer.append(
                '<div class="file-count">' +
                    '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">' +
                        '<path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>' +
                    '</svg>' +
                    '<span class="file-badge">' + count + '</span>' +
                '</div>'
            );
        }
    }

    function showAutoSaveIndicator() {
        var $indicator = $('#editor-autosave');
        if ($indicator.length) {
            $indicator.text('✓ Tallennettu').addClass('show');
            setTimeout(function() { $indicator.removeClass('show'); }, 2000);
        }
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
            'home': {
                icon: '🏠',
                title: 'Tervetuloa!',
                message: 'Ei tämän päivän tehtäviä — luo uusi aloittaaksesi!'
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
        $.ajax({
            url: 'todo.php',
            method: 'POST',
            data: { action: 'get_counts' },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.counts) {
                    const counts = response.counts;
                    
                    // Update navigation badges with animation
                    $('.nav-item[data-filter="all"] .nav-badge').text(counts.all);
                    $('.nav-item[data-filter="home"] .nav-badge').text(counts.home || counts.today);
                    $('.nav-item[data-filter="today"] .nav-badge').text(counts.today);
                    $('.nav-item[data-filter="tomorrow"] .nav-badge').text(counts.tomorrow || 0);
                    $('.nav-item[data-filter="completed"] .nav-badge').text(counts.completed);
                    $('.nav-item[data-filter="pending"] .nav-badge').text(counts.pending);
                    $('.nav-item[data-filter="deleted"] .nav-badge').text(counts.deleted);
                    $('.nav-item[data-filter="public"] .nav-badge').text(counts.public);
                    $('.nav-item[data-filter="files"] .nav-badge').text(counts.files);
                    
                    // Animate badge updates
                    $('.nav-badge').addClass('updated');
                    setTimeout(() => $('.nav-badge').removeClass('updated'), 300);
                }
            },
            error: function() {
                console.warn('Failed to update counts');
            }
        });
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
        
        // Escape = Close editor
        if (e.which === 27 && App.modal.isOpen) {
            closeFullscreenEditor();
        }
        
        // Ctrl/Cmd + S = Save todo (when editor is open)
        if ((e.ctrlKey || e.metaKey) && e.which === 83 && App.modal.isOpen) {
            e.preventDefault();
            saveEditorTodo();
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
            var $form = $('<form>', { method: 'POST', action: 'todo.php' })
                .append($('<input>', { type: 'hidden', name: 'action', value: 'logout' }));
            $('body').append($form);
            $form.submit();
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
    $('#btn-new-todo').trigger('click');
};