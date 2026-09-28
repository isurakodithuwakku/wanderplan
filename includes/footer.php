    </main>

    <!-- Destination Quick View Modal -->
    <div class="modal fade" id="destinationModal" tabindex="-1" aria-labelledby="modalDestTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
                <div class="position-relative">
                    <img id="modalDestImg" src="" alt="Destination Preview" class="w-100" style="height: 300px; object-fit: cover;">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3 p-2 bg-white rounded-circle shadow-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                    <span id="modalDestCategory" class="dest-category-badge position-absolute bottom-0 start-0 m-3"></span>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h3 id="modalDestTitle" class="fw-bold mb-0"></h3>
                            <p id="modalDestCountry" class="text-muted mb-0"><i class="bi bi-geo-alt-fill text-danger me-1"></i></p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary-subtle text-primary fs-6 px-3 py-2 rounded-pill" id="modalDestCost"></span>
                        </div>
                    </div>
                    <hr class="my-3">
                    <p id="modalDestDesc" class="text-secondary"></p>
                    <div class="d-flex align-items-center gap-3 text-muted small mt-3">
                        <div><i class="bi bi-sun-fill text-warning me-1"></i> <strong>Best Season:</strong> <span id="modalDestSeason"></span></div>
                        <div><i class="bi bi-shield-check text-success me-1"></i> Verified Route</div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3 border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <a id="modalPlanTripBtn" href="#" data-base-url="<?= $base_url ?>" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-calendar-plus me-1"></i> Plan Itinerary With This
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Top Button -->
    <button type="button" id="backToTopBtn" class="back-to-top" aria-label="Back to top">
        <i class="bi bi-arrow-up"></i>
    </button>

    <!-- Site Footer -->
    <footer class="site-footer mt-auto">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-4 col-md-6">
                    <div class="navbar-brand text-white mb-3">
                        <span class="logo-icon"><i class="bi bi-compass-fill"></i></span>
                        <span>Wander<span class="text-primary">Plan</span></span>
                    </div>
                    <p class="text-secondary small mb-3">
                        WanderPlan is an intuitive, intelligent travel itinerary planner designed to help travelers map schedules, manage budgets, and discover breathtaking destinations effortlessly.
</p>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?= $base_url ?>index.php" class="footer-link">Home</a></li>
                        <li><a href="<?= $base_url ?>explore.php" class="footer-link">Destinations</a></li>
                        <li><a href="<?= $base_url ?>create_itinerary.php" class="footer-link">Trip Creator</a></li>
                        <li><a href="<?= $base_url ?>contact.php" class="footer-link">Contact Us</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5>Features</h5>
                    <ul class="list-unstyled">
                        <li class="footer-link"><i class="bi bi-check2 text-primary me-1"></i> Interactive Day Planner</li>
                        <li class="footer-link"><i class="bi bi-check2 text-primary me-1"></i> Live Budget Calculator</li>
                        <li class="footer-link"><i class="bi bi-check2 text-primary me-1"></i> Curated Destinations</li>
                        <li class="footer-link"><i class="bi bi-check2 text-primary me-1"></i> Print & PDF Export</li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5>Travel Newsletter</h5>
                    <p class="text-secondary small">Get weekly travel itinerary inspirations and budget hacks.</p>
                    <form onsubmit="event.preventDefault(); alert('Thank you for subscribing to WanderPlan travel inspirations!'); this.reset();" class="d-flex gap-2">
                        <input type="email" class="form-control form-control-sm rounded-pill" placeholder="Enter your email" required>
                        <button class="btn btn-primary btn-sm rounded-pill px-3" type="submit">Join</button>
                    </form>
                </div>
            </div>

            <hr class="border-secondary opacity-25">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small text-secondary">
                <p class="mb-0">© <?= date('Y') ?> WanderPlan. Developed for ICT 2209 Web Technologies Mini Project.</p>
                <div class="d-flex gap-3 mt-2 mt-md-0">
                    <span class="text-muted"><i class="bi bi-code-slash text-primary"></i> HTML5 • CSS3 • Bootstrap • JS • PHP • MySQL</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript Validations -->
    <script src="<?= $base_url ?>js/validation.js"></script>
    <!-- Main Interactivity -->
    <script src="<?= $base_url ?>js/main.js"></script>
</body>
</html>
