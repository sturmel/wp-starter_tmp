export function initScrollAnimations() {
    console.log('Initializing scroll animations');
    const scrollAnimateElements = document.querySelectorAll(".scroll-animate");

    if (scrollAnimateElements.length === 0) {
        return;
    }

    const scrollObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                } else {
                    entry.target.classList.remove("is-visible");
                }
            });
        },
        { threshold: 0.2 }
    );

    scrollAnimateElements.forEach((element) => {
        scrollObserver.observe(element);
    });

}
