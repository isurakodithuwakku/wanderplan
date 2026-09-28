<?php
/**
 * Contact Us Page (contact.php)
 * Rajarata University of Sri Lanka - ICT 2209 Web Technologies
 * Mini Project: Travel Itinerary Planner
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = "Contact Us & Support - WanderPlan";
$success_message = false;
$errors = [];
$name = '';
$email = '';
$subject = '';
$message = '';

// Pre-fill name and email if logged in
if (is_logged_in()) {
    $user_data = get_current_user_data($pdo);
    if ($user_data) {
        $name = $user_data['full_name'] ?: $user_data['username'];
        $email = $user_data['email'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? 'General Inquiry');
    $message = trim($_POST['message'] ?? '');

    // Server-side validation
    if (empty($name) || strlen($name) < 2) {
        $errors[] = "Please enter your full name (at least 2 characters).";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please provide a valid email address.";
    }

    if (empty($message) || strlen($message) < 10) {
        $errors[] = "Your message must be at least 10 characters long.";
    }

    // Insert into MySQL messages table (Requirement 3.4)
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        try {
            $stmt->execute([$name, $email, $subject, $message]);
            $inserted_id = $pdo->lastInsertId();
            $success_message = true;

            // Clear inputs on success
            $subject = '';
            $message = '';
        } catch (PDOException $e) {
            $errors[] = "Database error: Could not submit your message. " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Contact Header -->
<div class="py-5 bg-dark text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
    <div class="container py-4 text-center">
        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold mb-3">
            <i class="bi bi-chat-dots-fill me-1"></i> Get In Touch
        </span>
        <h1 class="display-5 fw-bold mb-3">We'd Love To Hear From You</h1>
        <p class="text-white-50 fs-5 max-w-xl mx-auto" style="max-width: 650px;">
            Have questions about itinerary customization, budgeting tools, or feature suggestions? Send us a message and our team will get back to you promptly.
        </p>
    </div>
</div>

<div class="container py-5 my-3">
    <div class="row g-5">
        <!-- Contact Information & Campus Info -->
        <div class="col-lg-5">
            <div class="glass-card p-4 p-md-5 h-100 border-0 shadow-sm">
                <h3 class="fw-bold mb-4">Contact Information</h3>
                <p class="text-secondary mb-4">
                    Feel free to reach out to us regarding the Travel Itinerary Planner system or support inquiries.
                </p>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 fs-5">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Location</h6>
                        <p class="text-muted small mb-0">
                            Sri Lanka
                        </p>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="rounded-circle bg-success-subtle text-success p-3 fs-5">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Support Email</h6>
                        <p class="text-muted small mb-0">
                            support@wanderplan.local<br>
                            ict2209@tech.rjt.ac.lk
                        </p>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="rounded-circle bg-warning-subtle text-warning p-3 fs-5">
                        <i class="bi bi-telephone-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Phone Number</h6>
                        <p class="text-muted small mb-0">+94 (25) 226-6644 / +94 (77) 123-4567</p>
                    </div>
                </div>

                <hr class="my-4">

                <div class="alert alert-light border small text-muted">
                    <i class="bi bi-info-circle text-primary me-1"></i>
                    <strong>Submission Notice:</strong> All inquiries are securely stored in the <code>messages</code> MySQL table with automatic timestamps.
                </div>
            </div>
        </div>

        <!-- Contact Form (Section 3.4) -->
        <div class="col-lg-7">
            <div class="glass-card p-4 p-md-5 border-0 shadow-lg">
                <h3 class="fw-bold mb-2">Send Us A Message</h3>
                <p class="text-muted small mb-4">Fill out the form below. Client-side & server-side validation is active.</p>

                <?php if ($success_message): ?>
                    <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 mb-4">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <i class="bi bi-check-circle-fill text-success fs-2"></i>
                            <div>
                                <h5 class="fw-bold mb-0 text-success">Message Submitted Successfully!</h5>
                                <span class="small text-muted">Inquiry Ticket #<?= $inserted_id ?> logged to MySQL database.</span>
                            </div>
                        </div>
                        <p class="mb-0 text-secondary small">
                            Thank you, <strong><?= htmlspecialchars($name) ?></strong>. Your query has been recorded. Our travel advisory team will respond to <code><?= htmlspecialchars($email) ?></code> shortly.
                        </p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger rounded-3 mb-4">
                        <ul class="mb-0 small ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="contact.php" method="POST" id="contactForm" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" value="<?= htmlspecialchars($name) ?>" required>
                                <label for="name"><i class="bi bi-person me-1"></i> Your Name *</label>
                                <div class="validation-error"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Your Email" value="<?= htmlspecialchars($email) ?>" required>
                                <label for="email"><i class="bi bi-envelope me-1"></i> Email Address *</label>
                                <div class="validation-error"></div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" value="<?= htmlspecialchars($subject) ?>" required>
                                <label for="subject"><i class="bi bi-chat-left-text me-1"></i> Subject</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" id="message" name="message" placeholder="Leave a comment here" style="height: 150px" required><?= htmlspecialchars($message) ?></textarea>
                                <label for="message"><i class="bi bi-pencil-square me-1"></i> Your Message *</label>
                                <div class="validation-error"></div>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 py-3 fw-bold shadow">
                                <i class="bi bi-send-fill me-2"></i> Submit Inquiry
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Interactive FAQ Section (Accordion Event Handling) -->
    <div class="mt-5 pt-4">
        <div class="text-center mb-4">
            <h3 class="fw-bold">Frequently Asked Questions</h3>
            <p class="text-muted">Quick answers to common questions about WanderPlan.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion shadow-sm rounded-4 overflow-hidden" id="faqAccordion">
                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                <i class="bi bi-question-circle-fill text-primary me-2"></i> How do I create a new day-by-day travel itinerary?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                Simply sign up or log in, then click the "+ Create Trip" button on the navigation bar. Choose your destination, arrival and departure dates, and your allocated budget. You can then add chronological stops and activities for every day of your trip.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <i class="bi bi-calculator-fill text-success me-2"></i> How does the live budget calculation work?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                Each activity you add can include an estimated cost (e.g. entry tickets, transportation, dining, hotel). WanderPlan automatically tallies all expenditures, compares them against your overall budget, and dynamically highlights your remaining balance with visual progress bars.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                <i class="bi bi-printer-fill text-info me-2"></i> Can I print my travel schedule or save it as PDF?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                Yes! WanderPlan features a dedicated "Print / Export" button in your itinerary viewer. It automatically applies a tailored print stylesheet that hides web menus and formats your schedule into a clean, pocket-friendly document ready to print or save as a PDF.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
