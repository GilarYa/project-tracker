<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mini Aplikasi Project Tracker</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
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
                <p>Kelola project dan task Anda dengan mudah.</p>
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
                        <input type="number" id="description" name="description" class="form-control" min="0" max="100" value="0" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
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

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>