/**
 * Main Interactive Application Scripts
 * Rajarata University of Sri Lanka - ICT 2209 Web Technologies
 * Mini Project: Travel Itinerary Planner
 */

document.addEventListener('DOMContentLoaded', () => {
    initHeroSlider();
    initDestinationFilter();
    initSmoothScrolling();
    initBackToTop();
    initDynamicActivityBuilder();
    initDayFilterTabs();
    initLiveBudgetTracker();
    initDestinationQuickViewModal();
    initBootstrapTooltips();
});

/**
 * 1. Interactive Image Slider / Carousel with Auto-play & Pause-on-hover
 */
function initHeroSlider() {
    const sliderContainer = document.querySelector('.slider-container');
    const track = document.querySelector('.slider-track');
    const slides = document.querySelectorAll('.slider-slide');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');
    const dotsContainer = document.querySelector('.slider-dots');

    if (!sliderContainer || !track || slides.length === 0) return;

    let currentIndex = 0;
    let autoPlayInterval = null;
    const totalSlides = slides.length;

    // Create dot indicators
    if (dotsContainer) {
        dotsContainer.innerHTML = '';
        for (let i = 0; i < totalSlides; i++) {
            const dot = document.createElement('button');
            dot.className = `slider-dot ${i === 0 ? 'active' : ''}`;
            dot.setAttribute('aria-label', `Slide ${i + 1}`);
            dot.addEventListener('click', () => {
                goToSlide(i);
                resetAutoPlay();
            });
            dotsContainer.appendChild(dot);
        }
    }

    const dots = document.querySelectorAll('.slider-dot');

    function updateSlider() {
        track.style.transform = `translateX(-${currentIndex * 100}%)`;
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentIndex);
        });
    }

    function goToSlide(index) {
        currentIndex = (index + totalSlides) % totalSlides;
        updateSlider();
    }

    function nextSlide() {
        goToSlide(currentIndex + 1);
    }

    function prevSlide() {
        goToSlide(currentIndex - 1);
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextSlide();
            resetAutoPlay();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevSlide();
            resetAutoPlay();
        });
    }

    function startAutoPlay() {
        if (!autoPlayInterval) {
            autoPlayInterval = setInterval(nextSlide, 5000);
        }
    }

    function stopAutoPlay() {
        if (autoPlayInterval) {
            clearInterval(autoPlayInterval);
            autoPlayInterval = null;
        }
    }

    function resetAutoPlay() {
        stopAutoPlay();
        startAutoPlay();
    }

    // Pause on hover
    sliderContainer.addEventListener('mouseenter', stopAutoPlay);
    sliderContainer.addEventListener('mouseleave', startAutoPlay);

    // Keyboard navigation
    sliderContainer.setAttribute('tabindex', '0');
    sliderContainer.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight') nextSlide();
        if (e.key === 'ArrowLeft') prevSlide();
    });

    startAutoPlay();
}

/**
 * 2. Dynamic Content Updates: Category Filtering & Live Search for Destinations
 */
function initDestinationFilter() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const destinationCards = document.querySelectorAll('.destination-item');
    const searchInput = document.getElementById('destinationSearchInput');
    const noResultsMsg = document.getElementById('noDestinationsFound');

    if (filterButtons.length === 0 && !searchInput) return;

    let currentCategory = 'all';
    let searchQuery = '';

    function applyFilters() {
        let visibleCount = 0;

        destinationCards.forEach(card => {
            const cardCat = card.getAttribute('data-category')?.toLowerCase() || '';
            const cardTitle = card.getAttribute('data-title')?.toLowerCase() || '';
            const cardCountry = card.getAttribute('data-country')?.toLowerCase() || '';

            const matchesCategory = currentCategory === 'all' || cardCat === currentCategory;
            const matchesSearch = searchQuery === '' || 
                                 cardTitle.includes(searchQuery) || 
                                 cardCountry.includes(searchQuery);

            if (matchesCategory && matchesSearch) {
                card.style.display = 'block';
                card.classList.add('animate-fade-in');
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (noResultsMsg) {
            noResultsMsg.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            filterButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentCategory = btn.getAttribute('data-filter')?.toLowerCase() || 'all';
            applyFilters();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            searchQuery = e.target.value.trim().toLowerCase();
            applyFilters();
        });
    }
}

/**
 * 3. Smooth Scrolling for Navigation Anchor Links
 */
function initSmoothScrolling() {
    const anchorLinks = document.querySelectorAll('a[href^="#"]:not([href="#"]):not([data-bs-toggle])');

    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                e.preventDefault();
                const navHeight = document.querySelector('.navbar')?.offsetHeight || 70;
                const elementPosition = targetElement.getBoundingClientRect().top + window.scrollY;
                const offsetPosition = elementPosition - navHeight;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

/**
 * 4. Back To Top Floating Action Button
 */
function initBackToTop() {
    const backBtn = document.getElementById('backToTopBtn');
    if (!backBtn) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            backBtn.classList.add('show');
        } else {
            backBtn.classList.remove('show');
        }
    });

    backBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

/**
 * 5. Dynamic Content Updates: Dynamic Activity Builder in Create Trip Form
 */
function initDynamicActivityBuilder() {
    const addActivityBtn = document.getElementById('addDynamicActivityBtn');
    const activitiesContainer = document.getElementById('dynamicActivitiesList');

    if (!addActivityBtn || !activitiesContainer) return;

    let activityIndex = activitiesContainer.children.length;

    addActivityBtn.addEventListener('click', () => {
        activityIndex++;
        const row = document.createElement('div');
        row.className = 'card mb-3 p-3 dynamic-activity-item border border-gray-200 shadow-sm';
        row.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-primary">Activity #${activityIndex}</span>
                <button type="button" class="btn btn-outline-danger btn-sm remove-activity-btn" title="Remove activity">
                    <i class="bi bi-trash"></i> Remove
                </button>
            </div>
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Day Number</label>
                    <input type="number" name="activities[${activityIndex}][day_number]" class="form-control form-control-sm" value="1" min="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Time</label>
                    <input type="time" name="activities[${activityIndex}][activity_time]" class="form-control form-control-sm" value="10:00">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Activity Title *</label>
                    <input type="text" name="activities[${activityIndex}][activity_title]" class="form-control form-control-sm" placeholder="e.g. Visit Temple / Museum" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Category</label>
                    <select name="activities[${activityIndex}][category]" class="form-select form-select-sm">
                        <option value="Sightseeing">Sightseeing</option>
                        <option value="Dining">Dining</option>
                        <option value="Transport">Transport</option>
                        <option value="Accommodation">Accommodation</option>
                        <option value="Activity">Activity</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Location</label>
                    <input type="text" name="activities[${activityIndex}][location]" class="form-control form-control-sm" placeholder="e.g. Downtown Central">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Est. Cost ($)</label>
                    <input type="number" step="0.01" name="activities[${activityIndex}][cost]" class="form-control form-control-sm" value="0.00">
                </div>
            </div>
        `;

        activitiesContainer.appendChild(row);

        // Bind remove button
        row.querySelector('.remove-activity-btn').addEventListener('click', () => {
            row.remove();
        });
    });
}

/**
 * 6. Interactive Day Filter Tabs in view_itinerary.php
 */
function initDayFilterTabs() {
    const dayFilterBtns = document.querySelectorAll('.day-filter-btn');
    const dayContainers = document.querySelectorAll('.day-timeline-group');
    const categoryFilterSelect = document.getElementById('categoryFilterSelect');
    const activityItems = document.querySelectorAll('.timeline-item');

    if (dayFilterBtns.length === 0) return;

    dayFilterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            dayFilterBtns.forEach(b => b.classList.remove('active', 'btn-primary'));
            dayFilterBtns.forEach(b => b.classList.add('btn-outline-secondary'));
            
            btn.classList.add('active', 'btn-primary');
            btn.classList.remove('btn-outline-secondary');

            const selectedDay = btn.getAttribute('data-day');

            dayContainers.forEach(group => {
                if (selectedDay === 'all' || group.getAttribute('data-day') === selectedDay) {
                    group.style.display = 'block';
                } else {
                    group.style.display = 'none';
                }
            });
        });
    });

    if (categoryFilterSelect) {
        categoryFilterSelect.addEventListener('change', (e) => {
            const cat = e.target.value.toLowerCase();
            activityItems.forEach(item => {
                const itemCat = item.getAttribute('data-category')?.toLowerCase();
                if (cat === 'all' || itemCat === cat) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
}

/**
 * 7. Live Budget Tracker & Dynamic Calculations
 */
function initLiveBudgetTracker() {
    const totalBudgetEl = document.getElementById('displayTotalBudget');
    const totalSpentEl = document.getElementById('displayTotalSpent');
    const remainingEl = document.getElementById('displayRemainingBudget');
    const progressBar = document.getElementById('budgetProgressBar');

    if (!totalBudgetEl || !totalSpentEl || !progressBar) return;

    const totalBudget = parseFloat(totalBudgetEl.getAttribute('data-budget') || 0);
    const totalSpent = parseFloat(totalSpentEl.getAttribute('data-spent') || 0);

    const remaining = totalBudget - totalSpent;
    const percentage = totalBudget > 0 ? Math.min(100, Math.round((totalSpent / totalBudget) * 100)) : 0;

    progressBar.style.width = `${percentage}%`;

    if (percentage > 90) {
        progressBar.style.backgroundColor = '#f43f5e'; // Danger red
    } else if (percentage > 70) {
        progressBar.style.backgroundColor = '#f59e0b'; // Amber warning
    } else {
        progressBar.style.backgroundColor = '#10b981'; // Healthy green
    }

    if (remainingEl) {
        if (remaining < 0) {
            remainingEl.classList.add('text-danger');
            remainingEl.innerText = `-$${Math.abs(remaining).toFixed(2)} (Over Budget)`;
        } else {
            remainingEl.innerText = `$${remaining.toFixed(2)}`;
        }
    }
}

/**
 * 8. Destination Quick View Modal (Event Handling & Pop-ups)
 */
function initDestinationQuickViewModal() {
    const modalEl = document.getElementById('destinationModal');
    if (!modalEl) return;

    modalEl.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        if (!button) return;

        const title = button.getAttribute('data-title');
        const country = button.getAttribute('data-country');
        const category = button.getAttribute('data-category');
        const desc = button.getAttribute('data-desc');
        const img = button.getAttribute('data-img');
        const cost = button.getAttribute('data-cost');
        const season = button.getAttribute('data-season');

        modalEl.querySelector('#modalDestTitle').innerText = title;
        modalEl.querySelector('#modalDestCountry').innerText = country;
        modalEl.querySelector('#modalDestCategory').innerText = category;
        modalEl.querySelector('#modalDestDesc').innerText = desc;
        modalEl.querySelector('#modalDestImg').src = img;
        modalEl.querySelector('#modalDestCost').innerText = `$${cost}/day`;
        modalEl.querySelector('#modalDestSeason').innerText = season;

        const planBtn = modalEl.querySelector('#modalPlanTripBtn');
        if (planBtn) {
            const baseUrl = planBtn.getAttribute('data-base-url') || '';
            planBtn.href = `${baseUrl}create_itinerary.php?dest=${encodeURIComponent(title + ', ' + country)}`;
        }
    });
}

/**
 * 9. Initialize Bootstrap Tooltips
 */
function initBootstrapTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}
