<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Aplikasi Project Tracker</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 300px;
            background-color: white;
            border-right: 1px solid #ddd;
            padding: 20px;
            overflow-y: auto;
        }

        .sidebar-header {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #545b62;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-icon {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            padding: 2px 6px;
            border-radius: 3px;
        }

        .btn-icon:hover {
            background-color: #f0f0f0;
        }

        /* Projects List */
        .projects-list {
            margin-top: 10px;
        }

        .project-item {
            margin-bottom: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            background-color: #fafafa;
        }

        .project-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            cursor: pointer;
        }

        .project-header:hover {
            background-color: #f0f0f0;
        }

        .project-info {
            display: flex;
            flex-direction: column;
        }

        .project-name {
            font-weight: 500;
            color: #333;
        }

        .project-progress {
            color: #666;
            font-size: 12px;
            margin-top: 2px;
        }

        .tasks-list {
            padding: 0 15px 10px;
            border-top: 1px solid #e0e0e0;
        }

        .task-item {
            padding: 8px 12px;
            margin: 5px 0;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .task-item:hover {
            background-color: #f8f9fa;
            border-color: #007bff;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #f8f9fa;
            position: relative;
            overflow: hidden;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
            position: absolute;
            top: 20px;
            right: 20px;
            left: 20px;
            max-width: calc(100% - 40px);
        }

        .form-container.show {
            transform: translateX(0);
        }

        .welcome-message {
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
            padding: 60px 20px;
            transform: translateX(0);
            transition: transform 0.3s ease-in-out;
        }

        .welcome-message.hide {
            transform: translateX(-100%);
        }

        .welcome-message h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 2.5rem;
        }

        .welcome-message p {
            color: #666;
            font-size: 1.2rem;
            line-height: 1.6;
        }

        .form-container h2 {
            margin-bottom: 25px;
            color: #333;
            font-size: 24px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        /* Hide/Show form fields */
        .task-fields {
            display: none;
        }

        .project-fields {
            display: block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #ddd;
            }
            
            .sidebar-header {
                flex-direction: column;
            }
            
            .main-content {
                padding: 15px;
            }
            
            .form-container {
                padding: 20px;
            }
        }

        /* Animation for expand/collapse */
        .tasks-list {
            transition: all 0.3s ease;
        }

        .expand-btn {
            transition: transform 0.3s ease;
        }

        .expand-btn.expanded {
            transform: rotate(45deg);
        }

        /* Status badges */
        .status-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-draft {
            background-color: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
        }

        .status-in_progress {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-done {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .form-text {
            font-size: 0.85em;
            color: #6c757d;
            margin-top: 5px;
        }

        .text-muted {
            color: #6c757d !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar Kiri -->
        <div class="sidebar">
            <div class="sidebar-header">
                <button class="btn btn-primary" id="addProjectBtn">Add Project</button>
                <button class="btn btn-secondary" id="addTaskBtn">Add Task</button>
            </div>
            
            <div class="projects-list" id="projectsList">
                <!-- Projects will be loaded dynamically -->
            </div>
        </div>

        <!-- Area Utama -->
        <div class="main-content">
            <!-- Welcome Message -->
            <div class="welcome-message" id="welcomeMessage">
                <h1>Project Tracker</h1>
            </div>

            <!-- Form Container -->
            <div class="form-container" id="formContainer">
                <h2 id="formTitle">Add Project</h2>
                
                <form id="mainForm">
                    <input type="hidden" id="formType" name="type" value="project">

                    <div class="form-group">
                        <label for="name">Nama:</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>

                    <div class="form-group project-fields">
                        <label for="status">Status:</label>
                        <input type="text" id="status" name="status" class="form-control" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                    </div>

                    <div class="form-group task-fields" style="display: none;">
                        <label for="taskStatus">Status:</label>
                        <select id="taskStatus" name="status" class="form-control">
                            <option value="draft">Draft</option>
                            <option value="in_progress">In Progress</option>
                            <option value="done">Done</option>
                        </select>
                    </div>

                    <!-- Fields for Task -->
                    <div class="form-group task-fields" id="taskFields" style="display: none;">
                        <label for="projectSelect">Belongs to Project:</label>
                        <select id="projectSelect" name="project_id" class="form-control">
                            <option value="">Select Project</option>
                        </select>
                    </div>

                    <div class="form-group task-fields" id="weightField" style="display: none;">
                        <label for="weight">Bobot:</label>
                        <input type="number" id="weight" name="weight" class="form-control" min="1" value="1">
                    </div>

                    <!-- Fields for Project -->
                    <div class="form-group project-fields" id="projectFields">
                        <label for="description">Description Progress (%):</label>
                        <input type="number" id="description" name="description" class="form-control" min="0" max="100" value="0" readonly>
                        <small class="form-text text-muted">Progress otomatis dihitung berdasarkan task yang selesai</small>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                        <button type="button" class="btn btn-danger" id="deleteBtn" style="display: none;">Delete</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setup CSRF token for AJAX requests
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Form elements
            const formType = document.getElementById('formType');
            const taskFields = document.querySelectorAll('.task-fields');
            const projectFields = document.querySelectorAll('.project-fields');
            const mainForm = document.getElementById('mainForm');
            const cancelBtn = document.getElementById('cancelBtn');
            const formContainer = document.getElementById('formContainer');
            const welcomeMessage = document.getElementById('welcomeMessage');
            const addProjectBtn = document.getElementById('addProjectBtn');
            const addTaskBtn = document.getElementById('addTaskBtn');
            const deleteBtn = document.getElementById('deleteBtn');
            
            // Global variables
            let currentEditingProject = null;
            let currentEditingTask = null;
            let isFormVisible = false;
            
            // Initialize
            init();
            
            async function init() {
                // Setup button listeners
                addProjectBtn.addEventListener('click', () => showForm('project'));
                addTaskBtn.addEventListener('click', () => showForm('task'));
                
                // Setup form submit
                mainForm.addEventListener('submit', handleFormSubmit);
                
                // Setup cancel button
                cancelBtn.addEventListener('click', hideForm);
                
                // Setup delete button
                deleteBtn.addEventListener('click', handleDelete);
                
                // Load existing projects
                await loadProjects();
            }
            
            function showForm(type, isEdit = false) {
                formType.value = type;
                
                if (type === 'task') {
                    // Show task fields, hide project fields
                    taskFields.forEach(field => field.style.display = 'block');
                    projectFields.forEach(field => field.style.display = 'none');
                    document.getElementById('formTitle').textContent = currentEditingTask ? 'Edit Task' : 'Add Task';
                } else {
                    // Show project fields, hide task fields
                    taskFields.forEach(field => field.style.display = 'none');
                    projectFields.forEach(field => field.style.display = 'block');
                    document.getElementById('formTitle').textContent = currentEditingProject ? 'Edit Project' : 'Add Project';
                }
                
                // Show/hide delete button based on editing mode
                if (isEdit && (currentEditingProject || currentEditingTask)) {
                    deleteBtn.style.display = 'inline-block';
                } else {
                    deleteBtn.style.display = 'none';
                }
                
                // Clear form only if not editing
                if (!isEdit) {
                    mainForm.reset();
                    formType.value = type;
                    currentEditingProject = null;
                    currentEditingTask = null;
                }
                
                // Show form with animation
                if (!isFormVisible) {
                    welcomeMessage.classList.add('hide');
                    setTimeout(() => {
                        formContainer.classList.add('show');
                        isFormVisible = true;
                    }, 150);
                }
            }
            
            function showFormForProject(projectId, projectName) {
                // Set form to task mode
                formType.value = 'task';
                
                // Show task fields, hide project fields
                taskFields.forEach(field => field.style.display = 'block');
                projectFields.forEach(field => field.style.display = 'none');
                document.getElementById('formTitle').textContent = `Add Task to "${projectName}"`;
                
                // Hide delete button (this is for adding new task)
                deleteBtn.style.display = 'none';
                
                // Clear form
                mainForm.reset();
                formType.value = 'task';
                currentEditingProject = null;
                currentEditingTask = null;
                
                // Pre-select the project
                document.getElementById('projectSelect').value = projectId;
                
                // Show form with animation
                if (!isFormVisible) {
                    welcomeMessage.classList.add('hide');
                    setTimeout(() => {
                        formContainer.classList.add('show');
                        isFormVisible = true;
                    }, 150);
                }
            }
            
            function hideForm() {
                if (isFormVisible) {
                    formContainer.classList.remove('show');
                    setTimeout(() => {
                        welcomeMessage.classList.remove('hide');
                        isFormVisible = false;
                    }, 150);
                }
                
                // Clear form
                mainForm.reset();
                currentEditingProject = null;
                currentEditingTask = null;
            }
            
            function toggleFormFields() {
                const selectedType = formType.value;
                
                if (selectedType === 'task') {
                    // Show task fields, hide project fields
                    taskFields.forEach(field => field.style.display = 'block');
                    projectFields.forEach(field => field.style.display = 'none');
                    document.getElementById('formTitle').textContent = 'Add Task';
                } else {
                    // Show project fields, hide task fields
                    taskFields.forEach(field => field.style.display = 'none');
                    projectFields.forEach(field => field.style.display = 'block');
                    document.getElementById('formTitle').textContent = currentEditingProject ? 'Edit Project' : 'Add Project';
                }
            }
            
            async function handleFormSubmit(e) {
                e.preventDefault();
                
                const formData = new FormData(mainForm);
                const data = Object.fromEntries(formData.entries());
                
                // Validate form
                if (!validateForm(data)) {
                    return;
                }
                
                // Process the form data
                if (data.type === 'project') {
                    if (currentEditingProject) {
                        await updateProject(currentEditingProject.id, data);
                    } else {
                        await createProject(data);
                    }
                } else {
                    if (currentEditingTask) {
                        await updateTask(currentEditingTask.id, data);
                    } else {
                        await createTask(data);
                    }
                }
            }
            
            function validateForm(data) {
                if (!data.name || data.name.trim() === '') {
                    showMessage('Nama harus diisi!', 'error');
                    return false;
                }
                
                if (data.type === 'task' && !data.project_id) {
                    showMessage('Project harus dipilih untuk task!', 'error');
                    return false;
                }
                
                return true;
            }
            
            async function loadProjects() {
                try {
                    const response = await fetch('/projects', {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    if (response.ok) {
                        const projects = await response.json();
                        renderProjects(projects);
                        updateProjectSelect(projects);
                    }
                } catch (error) {
                    console.error('Error loading projects:', error);
                    showMessage('Error loading projects', 'error');
                }
            }
            
            async function createProject(data) {
                try {
                    const response = await fetch('/projects', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            name: data.name
                        })
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        showMessage(result.message, 'success');
                        hideForm();
                        await loadProjects();
                    } else {
                        const error = await response.json();
                        showMessage(error.message || 'Error creating project', 'error');
                    }
                } catch (error) {
                    console.error('Error creating project:', error);
                    showMessage('Error creating project', 'error');
                }
            }
            
            async function updateProject(id, data) {
                try {
                    const response = await fetch(`/projects/${id}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            name: data.name
                        })
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        showMessage(result.message, 'success');
                        hideForm();
                        await loadProjects();
                    } else {
                        const error = await response.json();
                        showMessage(error.message || 'Error updating project', 'error');
                    }
                } catch (error) {
                    console.error('Error updating project:', error);
                    showMessage('Error updating project', 'error');
                }
            }
            
            async function deleteProject(id) {
                if (!confirm('Are you sure you want to delete this project?')) {
                    return;
                }
                
                try {
                    const response = await fetch(`/projects/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        showMessage(result.message, 'success');
                        hideForm();
                        await loadProjects();
                    } else {
                        const error = await response.json();
                        showMessage(error.message || 'Error deleting project', 'error');
                    }
                } catch (error) {
                    console.error('Error deleting project:', error);
                    showMessage('Error deleting project', 'error');
                }
            }
            
            async function renderProjects(projects) {
                const projectsList = document.getElementById('projectsList');
                projectsList.innerHTML = '';
                
                for (const project of projects) {
                    const projectHTML = `
                        <div class="project-item" data-project-id="${project.id}">
                            <div class="project-header">
                                <div class="project-info">
                                    <span class="project-name">${project.name}</span>
                                    <small class="project-progress">Progress: ${project.description_progress}%</small>
                                </div>
                                <div style="display: flex; gap: 5px; align-items: center;">
                                    <span class="status-badge status-${project.status}">${project.status.toUpperCase()}</span>
                                    <button class="btn-icon add-task-btn" data-project-id="${project.id}" title="Add Task to this Project">➕</button>
                                    <button class="btn-icon expand-btn" data-project="${project.id}">+</button>
                                </div>
                            </div>
                            <div class="tasks-list" id="tasks-${project.id}" style="display: none;">
                                <div class="tasks-container"></div>
                            </div>
                        </div>
                    `;
                    
                    projectsList.insertAdjacentHTML('beforeend', projectHTML);
                    
                    // Load tasks for this project
                    await loadTasksForProject(project.id);
                }
                
                // Setup event listeners for new elements
                setupProjectEventListeners();
            }
            
            function setupProjectEventListeners() {
                // Expand/collapse functionality
                document.querySelectorAll('.expand-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const projectId = this.getAttribute('data-project');
                        const tasksList = document.getElementById(`tasks-${projectId}`);
                        
                        if (tasksList.style.display === 'none') {
                            tasksList.style.display = 'block';
                            this.textContent = '-';
                            this.classList.add('expanded');
                        } else {
                            tasksList.style.display = 'none';
                            this.textContent = '+';
                            this.classList.remove('expanded');
                        }
                    });
                });
                
                // Add task to project functionality
                document.querySelectorAll('.add-task-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const projectId = this.getAttribute('data-project-id');
                        const projectName = this.closest('.project-item').querySelector('.project-name').textContent;
                        showFormForProject(projectId, projectName);
                    });
                });
                
                // Make project-item clickable for edit (except when clicking buttons)
                document.querySelectorAll('.project-item').forEach(item => {
                    item.addEventListener('click', function(e) {
                        // Don't trigger if clicking on buttons
                        if (e.target.closest('.btn-icon')) {
                            return;
                        }
                        
                        const projectId = this.getAttribute('data-project-id');
                        editProject(projectId);
                    });
                });
            }
            
            async function editProject(id) {
                try {
                    const response = await fetch(`/projects/${id}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    if (response.ok) {
                        const project = await response.json();
                        currentEditingProject = project;
                        
                        // Fill form with project data
                        document.getElementById('formType').value = 'project';
                        document.getElementById('name').value = project.name;
                        document.getElementById('status').value = project.status;
                        document.getElementById('description').value = project.description_progress;
                        
                        // Show form for editing
                        showForm('project', true);
                    } else {
                        showMessage('Error loading project data', 'error');
                    }
                } catch (error) {
                    console.error('Error loading project:', error);
                    showMessage('Error loading project data', 'error');
                }
            }
            
            function updateProjectSelect(projects) {
                const projectSelect = document.getElementById('projectSelect');
                
                // Clear existing options except the first one
                projectSelect.innerHTML = '<option value="">Select Project</option>';
                
                projects.forEach(project => {
                    const option = document.createElement('option');
                    option.value = project.id;
                    option.textContent = project.name;
                    projectSelect.appendChild(option);
                });
            }
            
            function clearForm() {
                mainForm.reset();
                currentEditingProject = null;
                currentEditingTask = null;
                hideForm();
            }
            
            async function handleDelete() {
                if (currentEditingProject) {
                    await deleteProject(currentEditingProject.id);
                } else if (currentEditingTask) {
                    await deleteTask(currentEditingTask.id);
                }
            }
            
            async function loadTasksForProject(projectId) {
                try {
                    const response = await fetch(`/tasks?project_id=${projectId}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    if (response.ok) {
                        const tasks = await response.json();
                        renderTasksForProject(projectId, tasks);
                    }
                } catch (error) {
                    console.error('Error loading tasks:', error);
                }
            }
            
            function renderTasksForProject(projectId, tasks) {
                const tasksContainer = document.querySelector(`#tasks-${projectId} .tasks-container`);
                
                if (tasks.length === 0) {
                    tasksContainer.innerHTML = '<p style="padding: 10px; color: #666; font-style: italic;">No tasks yet</p>';
                    return;
                }
                
                tasksContainer.innerHTML = '';
                tasks.forEach(task => {
                    const taskHTML = `
                        <div class="task-item" data-task-id="${task.id}">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <span class="task-name">${task.name}</span>
                                    <small style="color: #666; margin-left: 10px;">Weight: ${task.weight}</small>
                                </div>
                                <div style="display: flex; gap: 5px; align-items: center;">
                                    <span class="status-badge status-${task.status}">${task.status}</span>
                                </div>
                            </div>
                        </div>
                    `;
                    tasksContainer.insertAdjacentHTML('beforeend', taskHTML);
                });
                
                // Setup task event listeners
                setupTaskEventListeners();
            }
            
            function setupTaskEventListeners() {
                // Make task-item clickable for edit
                document.querySelectorAll('.task-item').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.stopPropagation(); // Prevent event bubbling to parent project item
                        const taskId = this.getAttribute('data-task-id');
                        editTask(taskId);
                    });
                });
            }
            
            async function createTask(data) {
                try {
                    const response = await fetch('/tasks', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            name: data.name,
                            status: data.status,
                            project_id: data.project_id,
                            weight: data.weight || 1
                        })
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        showMessage(result.message, 'success');
                        hideForm();
                        await loadProjects(); // Reload to show new task
                    } else {
                        const error = await response.json();
                        showMessage(error.message || 'Error creating task', 'error');
                    }
                } catch (error) {
                    console.error('Error creating task:', error);
                    showMessage('Error creating task', 'error');
                }
            }
            
            async function editTask(id) {
                try {
                    const response = await fetch(`/tasks/${id}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    if (response.ok) {
                        const task = await response.json();
                        currentEditingTask = task;
                        
                        // Fill form with task data
                        document.getElementById('formType').value = 'task';
                        document.getElementById('name').value = task.name;
                        document.getElementById('taskStatus').value = task.status;
                        document.getElementById('projectSelect').value = task.project_id;
                        document.getElementById('weight').value = task.weight;
                        
                        // Show form for editing
                        showForm('task', true);
                        document.getElementById('formTitle').textContent = 'Edit Task';
                    } else {
                        showMessage('Error loading task data', 'error');
                    }
                } catch (error) {
                    console.error('Error loading task:', error);
                    showMessage('Error loading task data', 'error');
                }
            }
            
            async function updateTask(id, data) {
                try {
                    const response = await fetch(`/tasks/${id}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            name: data.name,
                            status: data.status,
                            project_id: data.project_id,
                            weight: data.weight || 1
                        })
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        showMessage(result.message, 'success');
                        hideForm();
                        await loadProjects();
                    } else {
                        const error = await response.json();
                        showMessage(error.message || 'Error updating task', 'error');
                    }
                } catch (error) {
                    console.error('Error updating task:', error);
                    showMessage('Error updating task', 'error');
                }
            }
            
            async function deleteTask(id) {
                if (!confirm('Are you sure you want to delete this task?')) {
                    return;
                }
                
                try {
                    const response = await fetch(`/tasks/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        showMessage(result.message, 'success');
                        hideForm();
                        await loadProjects();
                    } else {
                        const error = await response.json();
                        showMessage(error.message || 'Error deleting task', 'error');
                    }
                } catch (error) {
                    console.error('Error deleting task:', error);
                    showMessage('Error deleting task', 'error');
                }
            }
            
            function showMessage(message, type) {
                // Create or update message element
                let messageEl = document.getElementById('message');
                if (!messageEl) {
                    messageEl = document.createElement('div');
                    messageEl.id = 'message';
                    messageEl.style.cssText = `
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        padding: 12px 20px;
                        border-radius: 4px;
                        color: white;
                        font-weight: 500;
                        z-index: 1000;
                        transition: opacity 0.3s ease;
                    `;
                    document.body.appendChild(messageEl);
                }
                
                messageEl.textContent = message;
                let bgColor = '#6c757d'; // default
                if (type === 'success') bgColor = '#28a745';
                else if (type === 'error') bgColor = '#dc3545';
                else if (type === 'info') bgColor = '#17a2b8';
                
                messageEl.style.backgroundColor = bgColor;
                messageEl.style.opacity = '1';
                
                // Hide message after 3 seconds
                setTimeout(() => {
                    messageEl.style.opacity = '0';
                    setTimeout(() => {
                        if (messageEl.parentNode) {
                            messageEl.parentNode.removeChild(messageEl);
                        }
                    }, 300);
                }, 3000);
            }
        });
    </script>
</body>
</html>