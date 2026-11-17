# Community Portal for Village Democracy
# VillageConnect – Community Portal for Village Democracy

A full-stack, beginner-friendly web application developed using **Core PHP, MySQL, HTML5, CSS3, and JavaScript**. This project is built as a second-year B.Tech CSE college demonstration project to facilitate transparent digital communication between rural citizens and village local authorities (Gram Panchayat).
A full-stack, beginner-friendly college web application developed using **Core PHP, MySQL, HTML5, CSS3, and minimal JavaScript**. This project is built as a second-year B.Tech CSE college demonstration project to facilitate transparent digital communication between rural citizens and village local authorities (Gram Panchayat).

---

## 📌 Project Overview & Resume Description
## Project Overview & Resume Description

> **Resume Entry:**  
> **Community Portal for Village Democracy** | *PHP, MySQL, HTML5, CSS3, JavaScript*  
> *"Developed a community web portal to strengthen communication between citizens and local authorities by enabling grievance reporting, public announcements, government scheme awareness, and citizen feedback through a centralized digital platform."*

---

## 🚀 Live Environment & Access Details
## Live Environment & Access Details

- **Application URL:** [http://localhost/csp/](http://localhost/csp/)
- **Project Directory:** `C:\xampp\htdocs\csp\` (linked with VS Code workspace)
- **Database Name:** `csp`
- **Database Tool:** phpMyAdmin ([http://localhost/phpmyadmin/](http://localhost/phpmyadmin/))

### 🔑 Demonstration Credentials for Viva / Interview
### Demonstration Credentials for Viva / Interview

| Role | Username / Email | Password | Access URL |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin` *(or `admin@csp.local`)* | `admin123` | [http://localhost/csp/admin/login.php](http://localhost/csp/admin/login.php) |
| **Citizen (Sample)** | `ramesh@example.com` | `password123` | [http://localhost/csp/login.php](http://localhost/csp/login.php) |

---

## 🛠️ Technology Stack (Strictly Implemented)
## Technology Stack (Strictly Implemented)

- **Backend:** Core PHP (PHP 8.x, Procedural, Session Authentication, MySQLi Prepared Statements)
- **Database:** MySQL / MariaDB (`csp` database with relational schema)
- **Frontend:** Semantic HTML5 & Responsive CSS3 (Green, White, and Light-Blue civic theme)
- **Frontend:** HTML5 & CSS3 (Simple Green and White Design)
- **Client Scripting:** Pure Vanilla JavaScript (Client-side form validation & delete confirmation dialogs)
- **Zero Heavy Dependencies:** No Bootstrap, No Frameworks, No Composer packages, No external CDNs/APIs.

---

## 📂 Project Folder Structure

```
csp/
├── index.php                 # Clean homepage with announcements, schemes, & quick links
├── register.php              # Citizen registration with server & client validation
├── login.php                 # Citizen login with password_verify() and session start
├── logout.php                # Citizen logout (destroys active session)
├── announcements.php         # Public view of all village notices and meetings
├── schemes.php               # Public awareness guide on government welfare schemes
├── database.sql              # Complete database schema and demonstration seed data
├── README.md                 # Project documentation and interview explanation guide
│
├── config/
│   └── db.php                # Database connection configuration (MySQLi with port auto-detection)
│
├── citizen/
│   ├── dashboard.php         # Citizen dashboard with status counters and recent activity
│   ├── submit_grievance.php  # Form to file complaints (Water, Roads, Power, Sanitation, etc.)
│   ├── my_grievances.php     # History of citizen's submitted complaints & status tracking
│   └── feedback.php          # Citizen feedback and suggestions submission form
│
├── admin/
│   ├── login.php             # Admin authentication portal
│   ├── logout.php            # Admin session termination
│   ├── dashboard.php         # Admin metrics dashboard (citizens, grievances, notices, feedback)
│   ├── manage_citizens.php   # View and search registered citizens
│   ├── manage_grievances.php # Filter and manage all citizen grievances
│   ├── update_grievance.php  # Update grievance status (Pending/In Progress/Resolved) & remarks
│   ├── manage_announcements.php # List all announcements with edit & delete actions
│   ├── add_announcement.php  # Add announcement form
│   ├── edit_announcement.php # Edit announcement form
│   ├── delete_announcement.php # Delete announcement handler
│   ├── manage_schemes.php    # List government schemes with edit & delete actions
│   ├── add_scheme.php        # Add government scheme form
│   ├── edit_scheme.php       # Edit government scheme form
│   ├── delete_scheme.php     # Delete scheme handler
│   └── manage_feedback.php   # Review citizen feedback and suggestions
│
└── assets/
    ├── css/
    │   └── style.css         # Complete custom styling (green/white/light-blue responsive design)
    └── js/
        └── script.js         # JavaScript form validation & deletion confirmation popup
```

---

## 🗄️ Database Schema (`csp`)

The project utilizes 6 relational tables inside the `csp` database:

1. **`citizens`**: Stores registered residents (`id`, `full_name`, `email`, `phone`, `village_name`, `password`, `created_at`).
2. **`admins`**: Stores village administrators (`id`, `username`, `full_name`, `email`, `password`, `created_at`).
3. **`grievances`**: Stores citizen grievances with foreign key to `citizens` (`id`, `citizen_id`, `title`, `category`, `description`, `status`, `admin_remark`, `created_at`, `updated_at`).
4. **`announcements`**: Stores official village notices (`id`, `title`, `description`, `published_date`, `created_at`).
5. **`government_schemes`**: Stores welfare scheme details (`id`, `scheme_name`, `description`, `eligibility`, `procedure_info`, `additional_info`, `created_at`).
6. **`feedback`**: Stores citizen suggestions linked to `citizens` (`id`, `citizen_id`, `subject`, `message`, `created_at`).

---

## 🔒 Security & Best Practices Implemented

1. **Password Hashing:** Uses `password_hash($password, PASSWORD_DEFAULT)` with Bcrypt and `password_verify()`. Passwords are never stored in plain text.
2. **SQL Injection Defense:** All user input queries use MySQLi Prepared Statements (`mysqli_prepare`, `mysqli_stmt_bind_param`, `mysqli_stmt_execute`).
3. **Cross-Site Scripting (XSS) Prevention:** All dynamic outputs rendered via `htmlspecialchars()`.
4. **Access Control:** Protected citizen and admin pages enforce strict session validation (`isset($_SESSION['...'])`), redirecting unauthorized users immediately.
5. **Data Isolation:** Regular citizens can only view their own submitted grievances and personal feedback history.

---

## 🎓 Viva / GET Interview Explanation Tips

When explaining this project to an interviewer or college examiner:
1. **Explain the Motivation:** Traditional village governance requires residents to travel in person to Panchayat offices for basic issues like water leaks or street light outages. This portal digitizes the process so citizens can file complaints from mobile phones and receive real-time status updates.
2. **Explain the Architecture:** Built using Core PHP without heavy frameworks to demonstrate foundational full-stack concepts: relational database modeling, session management, secure credential hashing, and prepared statements.
3. **Walk Through the Lifecycle:** Show how a Citizen submits a complaint (`Pending`), the Admin inspects it on the Admin Dashboard, marks it as `In Progress` with an official note, and the Citizen instantly sees the update on their personal tracking dashboard.

