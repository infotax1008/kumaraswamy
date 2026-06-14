(function () {
  const header = document.querySelector("[data-header]");
  const nav = document.querySelector("[data-nav]");
  const navToggle = document.querySelector("[data-nav-toggle]");
  const yearTargets = document.querySelectorAll("[data-year]");
  const contactForm = document.querySelector("[data-contact-form]");
  const formStatus = document.querySelector("[data-form-status]");

  yearTargets.forEach((target) => {
    target.textContent = new Date().getFullYear();
  });

  if (window.lucide) {
    window.lucide.createIcons();
  } else {
    window.addEventListener("load", () => {
      if (window.lucide) {
        window.lucide.createIcons();
      }
    });
  }

  const syncHeader = () => {
    if (!header) return;
    header.classList.toggle("is-scrolled", window.scrollY > 8);
  };

  syncHeader();
  window.addEventListener("scroll", syncHeader, { passive: true });

  if (navToggle && nav) {
    navToggle.addEventListener("click", () => {
      const isOpen = nav.classList.toggle("is-open");
      navToggle.setAttribute("aria-expanded", String(isOpen));
    });

    nav.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", () => {
        nav.classList.remove("is-open");
        navToggle.setAttribute("aria-expanded", "false");
      });
    });
  }

  const observer = "IntersectionObserver" in window
    ? new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12 })
    : null;

  document.querySelectorAll(".reveal").forEach((item) => {
    if (observer) {
      observer.observe(item);
    } else {
      item.classList.add("is-visible");
    }
  });

  if (contactForm) {
    contactForm.addEventListener("submit", (event) => {
      event.preventDefault();

      const formData = new FormData(contactForm);
      const name = String(formData.get("name") || "").trim();
      const phone = String(formData.get("phone") || "").trim();
      const service = String(formData.get("service") || "").trim();
      const message = String(formData.get("message") || "").trim();

      if (!name || !phone || !service || !message) {
        if (formStatus) formStatus.textContent = "Please complete all fields before sending.";
        return;
      }

      const enquiryText = encodeURIComponent(
        `Hello Kumaraswamy Tax Consultancy,\n\n` +
        `I would like to send an enquiry.\n\n` +
        `Name: ${name}\n` +
        `Mobile: ${phone}\n` +
        `Service Required: ${service}\n\n` +
        `Message:\n${message}`
      );
      const whatsappUrl = `https://wa.me/919494990637?text=${enquiryText}`;
      const emailSubject = encodeURIComponent(`Service enquiry from ${name}`);
      const emailBody = encodeURIComponent(
        `Name: ${name}\nMobile: ${phone}\nService: ${service}\n\nMessage:\n${message}`
      );
      const emailUrl = `mailto:infotax1008@gmail.com?subject=${emailSubject}&body=${emailBody}`;

      if (formStatus) {
        formStatus.innerHTML = `Opening WhatsApp with your enquiry. <a href="${emailUrl}">Send by email instead</a>.`;
      }

      const whatsappWindow = window.open(whatsappUrl, "_blank", "noopener");
      if (!whatsappWindow) {
        window.location.href = whatsappUrl;
      }
    });
  }
})();
