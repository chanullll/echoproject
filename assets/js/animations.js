// Modern Animation System
class ModernAnimations {
    constructor() {
        this.init();
        this.setupScrollAnimations();
        this.setupParticles();
        this.setupMagneticEffect();
        this.setupScrollIndicator();
        this.setupCounterAnimations();
        this.setupParallax();
    }

    init() {
        // Add page transition on load
        document.body.classList.add('page-transition');
        
        // Trigger load animation
        requestAnimationFrame(() => {
            document.body.classList.add('loaded');
        });

        // Setup intersection observer for reveal animations
        this.setupIntersectionObserver();
        
        // Setup stagger animations
        this.setupStaggerAnimations();
        
        // Setup modern navigation
        this.setupModernNavigation();
    }

    setupIntersectionObserver() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    
                    // Add reveal classes based on data attributes
                    if (element.classList.contains('reveal')) {
                        element.classList.add('revealed');
                    }
                    if (element.classList.contains('reveal-left')) {
                        element.classList.add('revealed');
                    }
                    if (element.classList.contains('reveal-right')) {
                        element.classList.add('revealed');
                    }
                    
                    // Trigger stagger animation
                    if (element.classList.contains('stagger-container')) {
                        this.triggerStaggerAnimation(element);
                    }
                    
                    observer.unobserve(element);
                }
            });
        }, observerOptions);

        // Observe all elements with animation classes
        document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .stagger-container').forEach(el => {
            observer.observe(el);
        });
    }

    setupStaggerAnimations() {
        const staggerContainers = document.querySelectorAll('.stagger-container');
        staggerContainers.forEach(container => {
            const items = container.querySelectorAll('.stagger-item');
            items.forEach((item, index) => {
                item.style.transitionDelay = `${index * 0.1}s`;
            });
        });
    }

    triggerStaggerAnimation(container) {
        const items = container.querySelectorAll('.stagger-item');
        items.forEach((item, index) => {
            setTimeout(() => {
                item.classList.add('animate');
            }, index * 100);
        });
    }

    setupScrollAnimations() {
        let ticking = false;

        const updateScrollAnimations = () => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;

            // Parallax effect for hero elements
            const parallaxElements = document.querySelectorAll('.parallax');
            parallaxElements.forEach(element => {
                const speed = element.dataset.speed || 0.5;
                element.style.transform = `translateY(${scrolled * speed}px)`;
            });

            ticking = false;
        };

        const requestScrollUpdate = () => {
            if (!ticking) {
                requestAnimationFrame(updateScrollAnimations);
                ticking = true;
            }
        };

        window.addEventListener('scroll', requestScrollUpdate);
    }

    setupParticles() {
        const particlesContainer = document.createElement('div');
        particlesContainer.className = 'particles-bg';
        document.body.appendChild(particlesContainer);

        const createParticle = () => {
            const particle = document.createElement('div');
            particle.className = 'particle';
            
            const size = Math.random() * 4 + 2;
            const left = Math.random() * 100;
            const animationDuration = Math.random() * 10 + 10;
            const opacity = Math.random() * 0.3 + 0.1;
            
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            particle.style.left = `${left}%`;
            particle.style.animationDuration = `${animationDuration}s`;
            particle.style.opacity = opacity;
            
            particlesContainer.appendChild(particle);
            
            // Remove particle after animation
            setTimeout(() => {
                if (particle.parentNode) {
                    particle.parentNode.removeChild(particle);
                }
            }, animationDuration * 1000);
        };

        // Create particles periodically
        setInterval(createParticle, 2000);
        
        // Create initial particles
        for (let i = 0; i < 5; i++) {
            setTimeout(createParticle, i * 400);
        }
    }

    setupMagneticEffect() {
        const magneticElements = document.querySelectorAll('.magnetic');
        
        magneticElements.forEach(element => {
            element.addEventListener('mousemove', (e) => {
                const rect = element.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                
                const moveX = x * 0.1;
                const moveY = y * 0.1;
                
                element.style.transform = `translate(${moveX}px, ${moveY}px) scale(1.05)`;
            });
            
            element.addEventListener('mouseleave', () => {
                element.style.transform = 'translate(0px, 0px) scale(1)';
            });
        });
    }

    setupScrollIndicator() {
        const indicator = document.createElement('div');
        indicator.className = 'scroll-indicator';
        document.body.appendChild(indicator);

        const updateScrollIndicator = () => {
            const scrollTop = window.pageYOffset;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrollPercent = (scrollTop / docHeight) * 100;
            
            indicator.style.width = `${scrollPercent}%`;
        };

        window.addEventListener('scroll', updateScrollIndicator);
    }

    setupCounterAnimations() {
        const counters = document.querySelectorAll('.counter');
        
        const animateCounter = (counter) => {
            const target = parseInt(counter.dataset.target || counter.textContent);
            const duration = 2000;
            const start = 0;
            const increment = target / (duration / 16);
            let current = start;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = Math.floor(current);
            }, 16);
        };

        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        });

        counters.forEach(counter => {
            counterObserver.observe(counter);
        });
    }

    setupParallax() {
        const parallaxElements = document.querySelectorAll('.parallax');
        
        if (parallaxElements.length === 0) return;

        const handleScroll = () => {
            const scrolled = window.pageYOffset;
            
            parallaxElements.forEach(element => {
                const speed = parseFloat(element.dataset.speed) || 0.5;
                const yPos = -(scrolled * speed);
                element.style.transform = `translateY(${yPos}px)`;
            });
        };

        window.addEventListener('scroll', handleScroll);
    }

    setupModernNavigation() {
        const nav = document.querySelector('header nav');
        if (!nav) return;

        nav.classList.add('nav-modern');

        const handleScroll = () => {
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        };

        window.addEventListener('scroll', handleScroll);
    }

    // Utility function to add ripple effect
    addRippleEffect(element) {
        element.classList.add('ripple');
        
        element.addEventListener('click', (e) => {
            const ripple = document.createElement('span');
            const rect = element.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple-effect');
            
            element.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    }

    // Smooth scroll to element
    smoothScrollTo(target) {
        const element = document.querySelector(target);
        if (element) {
            element.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }

    // Add loading animation
    showLoading(element) {
        const loader = document.createElement('div');
        loader.className = 'loading-dots';
        loader.innerHTML = '<span></span><span></span><span></span>';
        element.appendChild(loader);
        return loader;
    }

    hideLoading(loader) {
        if (loader && loader.parentNode) {
            loader.parentNode.removeChild(loader);
        }
    }

    // Add success animation
    showSuccess(message, duration = 3000) {
        const notification = document.createElement('div');
        notification.className = 'success-notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--gradient-primary);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            z-index: 9999;
            transform: translateX(100%);
            transition: transform var(--transition-normal);
        `;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Trigger animation
        requestAnimationFrame(() => {
            notification.style.transform = 'translateX(0)';
        });
        
        // Remove after duration
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, duration);
    }

    // Add tilt effect to elements
    addTiltEffect(elements) {
        elements.forEach(element => {
            element.classList.add('tilt');
            
            element.addEventListener('mousemove', (e) => {
                const rect = element.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = (y - centerY) / 10;
                const rotateY = (centerX - x) / 10;
                
                element.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            });
            
            element.addEventListener('mouseleave', () => {
                element.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
            });
        });
    }
}

// Initialize animations when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const animations = new ModernAnimations();
    
    // Make animations globally available
    window.ModernAnimations = animations;
    
    // Add modern classes to existing elements
    document.querySelectorAll('.product-card').forEach(card => {
        card.classList.add('glass-card', 'magnetic', 'reveal');
        animations.addRippleEffect(card);
    });
    
    document.querySelectorAll('button, .btn').forEach(btn => {
        btn.classList.add('btn-modern', 'ripple');
        animations.addRippleEffect(btn);
    });
    
    // Add tilt effect to cards
    animations.addTiltEffect(document.querySelectorAll('.product-card, .glass-card'));
    
    // Add reveal animations to sections
    document.querySelectorAll('section').forEach((section, index) => {
        if (index % 2 === 0) {
            section.classList.add('reveal-left');
        } else {
            section.classList.add('reveal-right');
        }
    });
});

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ModernAnimations;
}