document.addEventListener('DOMContentLoaded', () => {
    const nav = document.querySelector('.navbar-collapse');

    document.querySelectorAll('.navbar .nav-link').forEach((link) => {
        link.addEventListener('click', () => {
            if (nav && nav.classList.contains('show')) {
                bootstrap.Collapse.getOrCreateInstance(nav).hide();
            }
        });
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });

    document.querySelectorAll('.reveal').forEach((item) => observer.observe(item));
});
