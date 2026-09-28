# WanderPlan – Interactive Travel Itinerary Planner Web Application

**Rajarata University of Sri Lanka**  
**Faculty of Technology | Department of ICT**  
**Course:** ICT 2209 – Web Technologies  
**Mini Project:** Interactive Web Application Development  
**Theme:** Travel Itinerary Planner  

---

## 1. Project Overview

**WanderPlan** is a comprehensive, full-stack travel itinerary planner and trip budgeting web application built using **HTML5, CSS3, Bootstrap 5, JavaScript (ES6+), PHP 8, and MySQL**. 

It allows travelers to discover curated global destinations, design custom day-by-day travel schedules, dynamically schedule morning, afternoon, and evening activities, track expenditures against a set budget in real-time, and print/export offline itinerary sheets for their journeys.

---

## 2. Key Features & Rubric Mapping

### 2.1 HTML5 & CSS3 Responsive Layout (20% Weightage)
- **Modern Design Aesthetics:** Tailored color palette with glassmorphism effects (`backdrop-filter: blur`), subtle gradients, and typography powered by Google Fonts (*Outfit* and *Plus Jakarta Sans*).
- **Responsive Grid:** Full mobile, tablet, and desktop responsiveness implemented with Bootstrap 5 and custom media queries.
- **Dedicated Print Stylesheet (`@media print`):** Formats itineraries into a clean, pocket-friendly printable schedule (hides navigation bars, ads, and buttons).

### 2.2 JavaScript Features (15% Weightage - All 6 Implemented!)
1. **Dynamic Content Updates:**
   - Real-time destination category filtering (Beach, Mountain, Cultural Heritage, City Breaks) with instant DOM updates.
   - Dynamic trip activity builder: Users can add/remove activity rows dynamically in the trip creation form (`create_itinerary.php`).
   - Dynamic Day-by-day timeline switcher (filter by All Days, Day 1, Day 2, etc.) and category filtering (Sightseeing, Dining, Transport, etc.).
   - Live budget calculator: Computes total expenses, compares against total budget, and visually changes progress bar color (Green → Amber → Red).
2. **Interactive Image Slider / Carousel:**
   - Custom hero destination slider with manual previous/next controls, dot indicators, auto-play rotation every 5 seconds, and pause-on-hover functionality.
3. **Form Validation:**
   - Real-time client-side validation in `js/validation.js` for registration, login, contact forms, and trip creation.
   - Live password strength calculation meter (Weak, Medium, Strong) with visual progress feedback.
   - Real-time password match confirmation.
   - Date logical constraints: Start date must not be in the past; End date must be on or after Start date; Automatic trip duration calculation badge ("5 Days / 4 Nights").
4. **Smooth Scrolling:**
   - Navigation links with `#` anchors scroll smoothly with automatic navbar height offset calculation.
5. **Event Handling & Pop-up Modals:**
   - Quick-view destination details modal with dynamic data attributes (`data-title`, `data-img`, `data-cost`, etc.) and one-click trip planning CTA.
   - Add Activity modal dialog directly on `view_itinerary.php`.
   - FAQ interactive accordion dropdowns on `contact.php`.
   - Interactive hover lift effects on cards.
6. **Custom Animations & Micro-interactions:**
   - Smooth CSS fade-in animations, timeline node glow effects, button hover micro-elevations, and floating Back-to-Top button that triggers on scroll.

### 2.3 User Authentication (20% Weightage)
- **Registration (`auth/register.php`):** Sign-up form with username, email, full name, and password. Stores passwords securely using PHP `password_hash($password, PASSWORD_DEFAULT)` (BCrypt).
- **Login (`auth/login.php`):** Validates credentials using `password_verify()`, regenerates session ID to prevent session fixation, and starts an authenticated session. Includes one-click demo credentials autofill for evaluation convenience.
- **Logout (`auth/logout.php`):** Destroys session, deletes session cookies, and flashes a logout notice.
- **Access Guarding:** Unauthorized users are redirected to login if attempting to access protected pages (`dashboard.php`, `create_itinerary.php`, `view_itinerary.php`).

### 2.4 Database Integration & CRUD (20% Weightage)
- **Database Name:** `travel_planner` (MySQL / MariaDB via PDO).
- **Tables Implemented:**
  1. `users`: Stores user credentials (`id`, `username`, `email`, `password`, `full_name`, `bio`, `created_at`).
  2. `itineraries`: Stores trip plans (`id`, `user_id`, `title`, `destination`, `start_date`, `end_date`, `budget`, `currency`, `travel_type`, `cover_image`, `notes`, `status`, `created_at`).
  3. `itinerary_items`: Stores day-by-day activities (`id`, `itinerary_id`, `day_number`, `activity_time`, `activity_title`, `location`, `category`, `cost`, `notes`).
  4. `messages`: Stores inquiries submitted through `contact.php` (`id`, `name`, `email`, `subject`, `message`, `created_at`).
  5. `destinations`: Curated exploration catalog (`id`, `name`, `country`, `category`, `description`, `image_url`, `estimated_cost_per_day`, `best_season`, `rating`).
- **Data Protection:** Prepared statements throughout all queries to prevent SQL Injection attacks.

### 2.5 Contact Form & Data Handling (10% Weightage)
- Accessible at `contact.php`.
- Collects `name`, `email`, `subject`, and `message`.
- Dual client-side (JavaScript) and server-side (PHP) sanitization and validation.
- Submissions are permanently saved to the MySQL `messages` table with automated inquiry ticket generation.

---

## 3. Folder Structure

```
project/
├── auth/
│   ├── login.php              # User login authentication page & logic
│   ├── logout.php             # Session destruction & logout handler
│   └── register.php           # User sign-up with password hashing & validation
├── css/
│   └── style.css              # Custom styling, glassmorphism, responsive layout & print CSS
├── images/
│   ├── hero_banner.jpg        # Cinematic hero landscape banner
│   ├── dest_srilanka.jpg      # Sri Lanka Nine Arch Bridge destination image
│   ├── dest_bali.jpg          # Bali Uluwatu cliff destination image
│   ├── dest_japan.jpg         # Kyoto Mount Fuji cherry blossom image
│   ├── dest_switzerland.jpg   # Swiss Alps Zermatt Matterhorn image
│   ├── dest_santorini.jpg     # Santorini caldera blue dome image
│   ├── dest_paris.jpg         # Parisian Eiffel Tower street image
│   └── default_avatar.jpg     # Default profile avatar
├── includes/
│   ├── db.php                 # PDO database connection configuration
│   ├── functions.php          # Helper functions (sanitization, auth guard, calculations)
│   ├── header.php             # Reusable navigation bar, meta tags, and flash notices
│   └── footer.php             # Reusable footer, modal dialogs, and JS script tags
├── js/
│   ├── main.js                # Slider, destination filter, smooth scroll, budget tracker
│   └── validation.js          # Real-time form validators, password strength & date checks
├── contact.php                # Contact page with MySQL message saving and FAQ
├── create_itinerary.php       # Interactive trip creator with date pickers & dynamic stops
├── dashboard.php              # User itinerary dashboard and summary statistics
├── database.sql               # Complete MySQL database export file with sample data
├── delete_activity.php        # Activity stop removal handler
├── delete_itinerary.php       # Itinerary cascade deletion handler
├── edit_itinerary.php         # Itinerary metadata & date modification page
├── explore.php                # Destination catalog and feature highlights page
├── index.php                  # Main home page with hero, slider, filters, and walkthrough
├── view_itinerary.php         # Day-by-day itinerary schedule view, live budget, and print
└── README.md                  # Comprehensive setup and project documentation
```

---

## 4. Setup and Installation Instructions (XAMPP / WAMP)

Follow these simple steps to run the project locally on your machine:

### Prerequisites:
- **XAMPP** or **WAMP** installed on your system (with Apache, PHP 7.4+ or PHP 8+, and MySQL).

### Step 1: Place the Project in XAMPP
1. Copy the entire `project` directory into your XAMPP web root:
   - For Windows: `C:\xampp\htdocs\project`
2. Alternatively, you can open a terminal in this project folder and run PHP's built-in server:
   ```bash
   php -S localhost:8000
   ```

### Step 2: Start Apache and MySQL
1. Open the **XAMPP Control Panel**.
2. Click **Start** next to **Apache**.
3. Click **Start** next to **MySQL**.

### Step 3: Import the Database (`database.sql`)
1. Open your web browser and go to: [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Click on the **Import** tab at the top.
3. Click **Choose File** and select `database.sql` from the project directory.
4. Click **Import** (or **Go**) at the bottom.
5. *Note:* The SQL script will automatically create the database named `travel_planner` along with all required tables and sample seed records.

### Step 4: Launch the Application
- If using XAMPP Apache:  
  Open your browser and navigate to: **`http://localhost/project/`**
- If using PHP built-in server:  
  Open your browser and navigate to: **`http://localhost:8000/`**

---

## 5. Pre-Configured Test Credentials

For quick evaluation and grading, sample accounts and pre-built rich itineraries are already seeded in `database.sql`:

- **Demo Username:** `demo_user`
- **Demo Password:** `password123`
- *Tip:* You can also click the **"Auto-Fill"** button on the Sign-In page (`auth/login.php`) to test immediately.
- You can also register any new account on `auth/register.php`.

---

## 6. Technologies Used

- **Frontend:** HTML5, CSS3, JavaScript (ES6+), Bootstrap 5.3, Bootstrap Icons
- **Backend:** PHP 8.0+
- **Database:** MySQL / MariaDB (PDO API with Prepared Statements)
- **Fonts & Assets:** Google Fonts (*Outfit*, *Plus Jakarta Sans*), Custom AI Travel Photography

---

## 7. Author Information

- **Student:** Individual Submission
- **Institution:** Rajarata University of Sri Lanka (RUSL)
- **Faculty:** Faculty of Technology
- **Department:** Department of Information and Communication Technology (ICT)
- **Course Module:** ICT 2209 – Web Technologies
