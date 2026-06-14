<?php
require_once __DIR__ . '/config.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Kumaraswamy Tax Consultancy offers ITR filing, GST registration, GST returns, PAN services, business registration, audit and CA services in Boduppal, Hyderabad.">
    <meta name="keywords" content="tax consultant Hyderabad, GST filing Boduppal, ITR filing, PAN card services, Kumaraswamy Tax Consultancy">
    <title>Kumaraswamy Tax Consultancy | Tax, GST and Business Services</title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="#home"><i class="bi bi-shield-check me-2"></i>Kumaraswamy Tax Consultancy</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#testimonials">Testimonials</a></li>
                <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
            </ul>
            <div class="d-flex gap-2 ms-lg-3">
                <a class="btn btn-outline-primary btn-sm" href="login.php">Client Login</a>
                <a class="btn btn-primary btn-sm" href="tel:+919494990637"><i class="bi bi-telephone-fill me-1"></i>Call</a>
            </div>
        </div>
    </div>
</nav>

<main>
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 reveal">
                    <span class="eyebrow">Trusted Tax Consultant in Hyderabad</span>
                    <h1>Professional tax, GST and business compliance services.</h1>
                    <p class="lead">Kumaraswamy Tax Consultancy helps individuals, professionals and businesses manage tax filings, registrations and compliance with reliable guidance.</p>
                    <div class="hero-actions">
                        <a class="btn btn-primary btn-lg" href="#contact"><i class="bi bi-calendar-check me-2"></i>Book Consultation</a>
                        <a class="btn btn-outline-primary btn-lg" href="https://wa.me/919494990637" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>WhatsApp</a>
                    </div>
                    <div class="hero-stats">
                        <div><strong>10+</strong><span>Core Services</span></div>
                        <div><strong>100%</strong><span>Client Focus</span></div>
                        <div><strong>Fast</strong><span>Documentation</span></div>
                    </div>
                </div>
                <div class="col-lg-6 reveal">
                    <img class="hero-img" src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=1100&q=80" alt="Tax consultant reviewing financial documents">
                </div>
            </div>
        </div>
    </section>

    <section class="section bg-light-blue">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6 reveal">
                    <h2>Welcome to Kumaraswamy Tax Consultancy</h2>
                    <p>Owned by <strong>Eerasarapu. Kumaraswamy</strong>, our consultancy provides dependable assistance for income tax, GST, business registration and essential statutory services from Boduppal, Hyderabad.</p>
                </div>
                <div class="col-lg-6 reveal">
                    <div class="info-panel">
                        <i class="bi bi-file-earmark-check"></i>
                        <div>
                            <h3>Clear documentation. Confident compliance.</h3>
                            <p>We simplify processes, explain requirements clearly and help you complete filings accurately and on time.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="services">
        <div class="container">
            <div class="section-title reveal">
                <span class="eyebrow">Services</span>
                <h2>Complete tax and business support</h2>
            </div>
            <div class="row g-4">
                <?php
                $services = [
                    ['Income Tax Returns (ITR)', 'bi-receipt', 'Accurate ITR filing for salaried, professional and business income.'],
                    ['GST Registration', 'bi-building-add', 'GST application support with document checklist and follow-up.'],
                    ['GST Returns Filing', 'bi-journal-text', 'Monthly, quarterly and annual GST return filing assistance.'],
                    ['PAN Card Services', 'bi-person-vcard', 'New PAN, correction and document submission support.'],
                    ['Aadhaar-PAN Linking', 'bi-link-45deg', 'Quick help with Aadhaar and PAN linking requirements.'],
                    ['Udyam Registration', 'bi-award', 'MSME/Udyam registration for eligible businesses.'],
                    ['Trade License', 'bi-briefcase', 'Guidance for trade license application and renewals.'],
                    ['Audit Services', 'bi-search', 'Professional audit coordination and compliance support.'],
                    ['CA Services', 'bi-person-check', 'CA-backed advisory and statutory service coordination.'],
                    ['Business Registration', 'bi-shop', 'Support for starting and registering your business entity.'],
                ];
                foreach ($services as $service): ?>
                    <div class="col-md-6 col-lg-4 reveal">
                        <article class="service-card h-100">
                            <i class="bi <?= e($service[1]) ?>"></i>
                            <h3><?= e($service[0]) ?></h3>
                            <p><?= e($service[2]) ?></p>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section bg-light-blue" id="about">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6 reveal">
                    <img class="rounded-img" src="https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=1000&q=80" alt="Professional financial paperwork">
                </div>
                <div class="col-lg-6 reveal">
                    <span class="eyebrow">About Us</span>
                    <h2>Practical advice with professional care</h2>
                    <p>Kumaraswamy Tax Consultancy provides tax and registration services for clients who want clear process guidance, accurate filing and responsive communication.</p>
                    <div class="mission-grid">
                        <div>
                            <h3>Mission</h3>
                            <p>To make tax compliance simple, transparent and accessible for every client.</p>
                        </div>
                        <div>
                            <h3>Vision</h3>
                            <p>To become a trusted local partner for tax, GST and business compliance services.</p>
                        </div>
                    </div>
                    <p class="mb-0">Our service approach focuses on document readiness, timely filings, confidentiality and customer satisfaction.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-title reveal">
                <span class="eyebrow">Why Choose Us</span>
                <h2>Reliable support from start to finish</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4 reveal"><div class="feature-box"><i class="bi bi-clock-history"></i><h3>Timely Service</h3><p>Fast response and careful follow-up for filings and registrations.</p></div></div>
                <div class="col-md-4 reveal"><div class="feature-box"><i class="bi bi-lock"></i><h3>Confidential</h3><p>Your financial documents and personal details are handled responsibly.</p></div></div>
                <div class="col-md-4 reveal"><div class="feature-box"><i class="bi bi-chat-dots"></i><h3>Clear Guidance</h3><p>Simple explanations so you understand every step of the process.</p></div></div>
            </div>
        </div>
    </section>

    <section class="section satisfaction-band">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-8 reveal">
                    <h2>Customer satisfaction is our priority</h2>
                    <p>From initial consultation to final submission, we focus on accuracy, clarity and dependable communication.</p>
                </div>
                <div class="col-lg-4 text-lg-end reveal">
                    <a class="btn btn-light btn-lg" href="#contact">Get Started</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="testimonials">
        <div class="container">
            <div class="section-title reveal">
                <span class="eyebrow">Testimonials</span>
                <h2>What clients appreciate</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4 reveal"><div class="testimonial"><p>"Quick response and very clear guidance for GST filing."</p><strong>Small Business Owner</strong></div></div>
                <div class="col-md-4 reveal"><div class="testimonial"><p>"My ITR was handled smoothly with proper document support."</p><strong>Individual Client</strong></div></div>
                <div class="col-md-4 reveal"><div class="testimonial"><p>"Professional service and helpful reminders for compliance."</p><strong>Retail Entrepreneur</strong></div></div>
            </div>
        </div>
    </section>

    <section class="section bg-light-blue" id="faq">
        <div class="container">
            <div class="section-title reveal">
                <span class="eyebrow">FAQ</span>
                <h2>Common questions</h2>
            </div>
            <div class="accordion reveal" id="faqAccordion">
                <div class="accordion-item">
                    <h3 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne">Which documents are needed for ITR filing?</button></h3>
                    <div id="faqOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion"><div class="accordion-body">PAN, Aadhaar, bank statement, Form 16 if applicable, investment proofs and income details are commonly required.</div></div>
                </div>
                <div class="accordion-item">
                    <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqTwo">Can you help with GST registration and returns?</button></h3>
                    <div id="faqTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Yes. We assist with GST registration, return filing and related compliance support.</div></div>
                </div>
                <div class="accordion-item">
                    <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqThree">Do clients get a dashboard?</button></h3>
                    <div id="faqThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Yes. Registered clients can log in, view profile details, upload documents and download files shared by the office.</div></div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="contact">
        <div class="container">
            <div class="section-title reveal">
                <span class="eyebrow">Contact</span>
                <h2>Visit or message us</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-5 reveal">
                    <div class="contact-card">
                        <h3>Office Details</h3>
                        <p><i class="bi bi-person"></i> Eerasarapu. Kumaraswamy</p>
                        <p><i class="bi bi-telephone"></i> <a href="tel:+919494990637">+91 9494990637</a> / <a href="tel:+919908090020">9908090020</a></p>
                        <p><i class="bi bi-envelope"></i> <a href="mailto:infotax1008@gmail.com">infotax1008@gmail.com</a></p>
                        <p><i class="bi bi-geo-alt"></i> 1-1, Bhavani Nagar, Boduppal, Hyderabad - 500092</p>
                        <a class="btn btn-success w-100" href="https://wa.me/919494990637" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>Contact on WhatsApp</a>
                    </div>
                </div>
                <div class="col-lg-7 reveal">

                  <form class="contact-form" id="contactForm">
    <div class="col-md-6">
        <label class="form-label">Name</label>
        <input class="form-control" name="name" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Mobile</label>
        <input class="form-control" name="mobile" required>
    </div>

    <div class="col-12">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" required>
    </div>

    <div class="col-12">
        <label class="form-label">Service Needed</label>
        <select class="form-select" name="service">
            <option>Income Tax Returns</option>
            <option>GST Services</option>
            <option>PAN Services</option>
            <option>Business Registration</option>
            <option>Other</option>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label">Message</label>
        <textarea class="form-control" name="message" rows="4" required></textarea>
    </div>

    <div class="col-12">
        <button class="btn btn-primary btn-lg" type="submit">
            Send Enquiry
        </button>
    </div>
</div>

</form>
                </div>
            </div>
            <div class="map-wrap reveal">
                <iframe title="Kumaraswamy Tax Consultancy location" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d283.8597127002645!2d78.584937102903!3d17.416118084803717!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bcb9ebe9e656d7d%3A0x38f0271d49555ebc!2sRapha%20Pharmacy!5e0!3m2!1sen!2sin!4v1781426447470!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>
</main>

<a class="floating-whatsapp" href="https://wa.me/919494990637" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"><i class="bi bi-whatsapp"></i></a>
<a class="floating-call" href="tel:+919494990637" aria-label="Call Kumaraswamy Tax Consultancy"><i class="bi bi-telephone-fill"></i></a>

<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <h3>Kumaraswamy Tax Consultancy</h3>
                <p>Professional tax, GST, PAN, audit and business registration support in Boduppal, Hyderabad.</p>
            </div>
            <div class="col-lg-3">
                <h4>Quick Links</h4>
                <a href="#services">Services</a>
                <a href="#about">About</a>
                <a href="#contact">Contact</a>
                <a href="login.php">Client Login</a>
            </div>
            <div class="col-lg-4">
                <h4>Connect</h4>
                <div class="socials">
                    <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    <a href="mailto:infotax1008@gmail.com" aria-label="Email"><i class="bi bi-envelope"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">© <?= date('Y') ?> Kumaraswamy Tax Consultancy. All rights reserved.</div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>

<script>
emailjs.init({
    publicKey: "h3YImMYvQE5gAjDKK"
});

document.getElementById("contactForm").addEventListener("submit", function(e) {
    e.preventDefault();

    emailjs.sendForm(
        "service_cgivbl6",
        "template_e7z8tes",
        this
    )
    .then(function() {
        alert("Enquiry Sent Successfully!");
    })
    .catch(function(error) {
        alert(JSON.stringify(error));
    });
});
</script>

</body>
</html>
