# Task Management API (ٍsecurity)

This project is an advanced Task Management API built using Laravel. It includes features such as task dependencies, advanced security mechanisms, and user role management. This API allows users to manage tasks efficiently with support for various task types, priorities, and dependencies between tasks.

## Features

- **Task Management**: Create, update, and manage tasks with types like Bug, Feature, and Improvement.
- **Task Dependencies**: Automatically block tasks that depend on incomplete tasks and unblock them when dependencies are resolved.
- **User Management & Roles**: Assign tasks to users and manage user permissions based on roles.
- **Task Status Tracking**: Track task status updates with history logging.
- **File Attachments**: Upload and manage attachments related to tasks.
- **Daily Reports**: Generate daily task reports with performance insights.
- **Security**: Implement JWT authentication, rate limiting, and protection against common attacks like XSS, SQL Injection, and CSRF.
- **Error Handling & Logging**: Comprehensive error logging and custom exception handling.

## API Endpoints

### Task Endpoints

- **Create Task**:  
  `POST /api/tasks`  
  Create a new task with required details.

- **Update Task Status**:  
  `PUT /api/tasks/{id}/status`  
  Update the status of a specific task.

- **Reassign Task**:  
  `PUT /api/tasks/{id}/reassign`  
  Reassign the task to a different user.

- **Add Comment to Task**:  
  `POST /api/tasks/{id}/comments`  
  Add a comment to a specific task.

- **Add Attachment to Task**:  
  `POST /api/tasks/{id}/attachments`  
  Attach a file to a specific task.

- **View Task Details**:  
  `GET /api/tasks/{id}`  
  Get detailed information of a specific task.

- **View All Tasks**:  
  `GET /api/tasks?type=Bug&status=Open&assigned_to=2&due_date=2024-09-30&priority=High&depends_on=null`  
  List tasks with advanced filters.

### Report Endpoints

- **Daily Task Report**:  
  `GET /api/reports/daily-tasks`  
  Generate a daily task report.

- **View Blocked Tasks**:  
  `GET /api/tasks?status=Blocked`  
  List all tasks blocked by dependencies.

### User Management

- **Assign Task to User**:  
  `POST /api/tasks/{id}/assign`  
  Assign a task to a specific user.

## Security

- **JWT Authentication**: Secures the API with JWT tokens for user authentication.
- **Rate Limiting**: Protects against DDoS attacks by limiting the number of requests.
- **CSRF Protection**: Ensures protection against Cross-Site Request Forgery attacks.
- **XSS & SQL Injection Protection**: Laravel's built-in mechanisms are used to sanitize user input and prevent attacks.


### Steps to Run the System


- [Installation](#installation)
 1. **Clone the repository:**
 
     ```bash
     git clone https://github.com/HusseinIte/security-task.git
     cd security-task
     ```
 
 2. **Install dependencies:**
 
     ```bash
     composer install
     npm install
     ```
 
 3. **Copy the `.env` file:**
 
     ```bash
     cp .env.example .env
     ```
 
 4. **Generate an application key:**
 
     ```bash
     php artisan key:generate
     ```
 
 5. **Configure the database:**
 
     Update your `.env` file with your database credentials.
 
 6. **Run the migrations:**
 
     ```bash
     php artisan migrate --seed
     ```
 7. **Run the seeders:**
 
     If you want to populate the database with sample data, use the seeder command:
 
     ```bash
     php artisan db:seed
     ```
 8. **Serve the application:**
 
     ```bash
     php artisan serve
     ```
