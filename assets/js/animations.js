// Page Transition System
class PageTransitions {
    constructor() {
        this.init();
    }

    init() {
        // Add page enter animation on load
        document.addEventListener('DOMContentLoaded', () => {
            document.body.classList.add('page-enter');
            setTimeout(() => {
                document.body.classList.remove('page-enter');
                document.body.classList.add('page-loaded');
            }, 300);
        });

        // Handle page exit animations
        this.handlePageExits();
        
        // Initialize scroll animations
        this.initScrollAnimations();
        
        // Initialize element animations
        this.initElementAnimations();
    }

    handlePageExits() {
        const links = document.querySelectorAll('a[href$=".php"], a[href="/"], a[href="index.php"]');
        
        links.forEach(link => {
            link.addEventListener('click', (e) => {
                // Skip if it's an external link or has special attributes
                if (link.target === '_blank' || link.hasAttribute('data-no-transition')) {
                    return;
                }

                e.preventDefault();
                const href = link.href;
                
                // Add exit animation
                document.body.classList.add('page-exit');
                
                // Show loading indicator
                this.showLoadingIndicator();
                
                // Navigate after animation
                setTimeout(() => {
                    window.location.href = href;
                }, 400);
            });
        });
    }

    showLoadingIndicator() {
        const loader = document.createElement('div');
        loader.className = 'fixed inset-0 bg-white bg-opacity-90 flex items-center justify-center z-50 animate-fade-in';
        loader.innerHTML = `
            <div class="text-center">
                <div class="animate-spin-slow text-4xl mb-2">🌱</div>
                <p class="text-eco-green font-semibold">Loading...</p>
            </div>
        `;
        document.body.appendChild(loader);
    }

    initScrollAnimations() {
        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    const animationType = element.dataset.animate || 'fade-up';
                    
                    element.classList.add('animate-in');
                    element.classList.add(`animate-${animationType}`);
                    
                    // Stagger animations for multiple elements
                    if (element.dataset.delay) {
                        element.style.animationDelay = element.dataset.delay;
                    }
                    
                    observer.unobserve(element);
                }
            });
        }, observerOptions);

        // Observe elements with data-animate attribute
        document.querySelectorAll('[data-animate]').forEach(el => {
            el.classList.add('animate-out');
            observer.observe(el);
        });
    }

    initElementAnimations() {
        // Enhanced button interactions
        document.querySelectorAll('button, .btn').forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                btn.classList.add('animate-button-hover');
            });
            
            btn.addEventListener('mouseleave', () => {
                btn.classList.remove('animate-button-hover');
            });
        });

        // Card hover animations
        document.querySelectorAll('.product-card, .category-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.classList.add('animate-card-hover');
            });
            
            card.addEventListener('mouseleave', () => {
                card.classList.remove('animate-card-hover');
            });
        });

        // Badge pulse animation
        document.querySelectorAll('.carbon-badge, .achievement-badge').forEach(badge => {
            badge.classList.add('animate-pulse-eco');
        });

        // Mobile menu animations
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const mobileMenu = document.querySelector('.mobile-menu');
        
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                if (mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.remove('hidden');
                    mobileMenu.classList.add('animate-slide-down');
                } else {
                    mobileMenu.classList.add('animate-slide-up');
                    setTimeout(() => {
                        mobileMenu.classList.add('hidden');
                        mobileMenu.classList.remove('animate-slide-up', 'animate-slide-down');
                    }, 300);
                }
            });
        }
    }
}

// Counter Animation
class CounterAnimation {
    static animateCounter(element, target, duration = 2000) {
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current);
        }, 16);
    }
}

// Carbon Badge Animation
class CarbonBadgeAnimation {
    static init() {
        document.querySelectorAll('.carbon-counter').forEach(counter => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = parseFloat(counter.dataset.target) || 0;
                        CounterAnimation.animateCounter(counter, target);
                        observer.unobserve(counter);
                    }
                });
            });
            observer.observe(counter);
        });
    }
}

// Initialize animations when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new PageTransitions();
    CarbonBadgeAnimation.init();
});

// Utility functions for dynamic animations
window.EcoAnimations = {
    showNotification: (message, type = 'success') => {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg animate-slide-in-right ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('animate-slide-out-right');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    },
    
    showCarbonSavingsModal: (savings) => {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-fade-in';
        modal.innerHTML = `
            <div class="bg-white rounded-xl p-8 max-w-md mx-4 animate-scale-in">
                <div class="text-center">
                    <div class="animate-bounce-eco text-6xl mb-4">🎉</div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Great Choice!</h3>
                    <div class="bg-green-100 border border-green-300 rounded-lg p-4 mb-6">
                        <p class="text-green-800 font-semibold">You saved ${savings} kg CO₂!</p>
                        <p class="text-green-600 text-sm mt-1">Equivalent to planting ${Math.ceil(savings / 3.2)} tree(s) 🌳</p>
                    </div>
                    <button onclick="this.closest('.fixed').remove()" class="bg-eco-green text-white px-6 py-2 rounded-lg hover:bg-eco-dark transition-all duration-300 transform hover:scale-105">
                        Continue Shopping
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Auto-close after 5 seconds
        setTimeout(() => {
            if (document.body.contains(modal)) {
                modal.classList.add('animate-fade-out');
                setTimeout(() => modal.remove(), 300);
            }
        }, 5000);
    }
};