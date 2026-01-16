{{-- Landing Page JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
        gsap.registerPlugin(ScrollTrigger);

        // 1. Loader
        const tlLoader = gsap.timeline({
            onComplete: () => {
                document.querySelector('.loader-overlay').style.display = 'none';
                initAnimations();
            }
        });

        let countObj = { val: 0 };
        tlLoader.to(countObj, {
            val: 100,
            duration: 1.5,
            ease: "power2.inOut",
            onUpdate: () => {
                document.querySelector('.counter').innerText = Math.floor(countObj.val) + "%";
                document.querySelector('.loader-progress').style.width = Math.floor(countObj.val) + "%";
            }
        })
        .to('.loader-overlay', {
            yPercent: -100,
            duration: 0.8,
            ease: "expo.inOut"
        });

        // 2. Animations
        function initAnimations() {
            // Hero Text Reveal
            gsap.to('.hero-text', {
                y: 0,
                duration: 1.2,
                stagger: 0.1,
                ease: "power4.out"
            });

            // Hero Fade
            gsap.to('.hero-fade', {
                opacity: 1,
                duration: 1,
                stagger: 0.1,
                delay: 0.5
            });

            // Dashboard Entry
            gsap.to('.hero-dashboard-container', {
                opacity: 1,
                y: 0,
                duration: 1.5,
                delay: 0.6,
                ease: "power3.out"
            });

            // Marquee Loop
            gsap.to(".marquee-content", {
                xPercent: -50,
                repeat: -1,
                duration: 20,
                ease: "linear"
            });

            // Bento Grid Stagger
            gsap.from(".bento-card", {
                scrollTrigger: {
                    trigger: "#features",
                    start: "top 80%"
                },
                y: 50,
                opacity: 0,
                duration: 0.8,
                stagger: 0.1,
                ease: "power2.out"
            });
        }

        // 3. 3D Tilt Effect (Enhanced with Parallax)
        const heroContainer = document.querySelector('.hero-dashboard-container');
        const tiltInner = document.querySelector('.tilt-inner');

        document.addEventListener('mousemove', (e) => {
            if (!heroContainer) return;
            
            const { clientX, clientY } = e;
            const { innerWidth, innerHeight } = window;
            
            // Calculate percentages (-1 to 1)
            const x = (clientX / innerWidth - 0.5) * 2;
            const y = (clientY / innerHeight - 0.5) * 2;

            // Dashboard Tilt
            gsap.to(tiltInner, {
                rotationY: x * 5, // Max 5deg tilt
                rotationX: -y * 5,
                duration: 0.5,
                ease: "power2.out"
            });

            // Parallax Floating Elements
            gsap.to('.hero-float', {
                x: x * 30, // Increased movement
                y: y * 30,
                duration: 0.8,
                ease: "power2.out"
            });
        });

        // 4. Magnetic Buttons & Spotlight Effect
        const buttons = document.querySelectorAll('.magnetic-btn');
        const cards = document.querySelectorAll('.bento-card, .glass');

        // Button Magnetism
        buttons.forEach(btn => {
            btn.addEventListener('mousemove', (e) => {
                const rect = btn.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                
                gsap.to(btn, {
                    x: x * 0.3,
                    y: y * 0.3,
                    duration: 0.3,
                    ease: "power2.out"
                });
            });

            btn.addEventListener('mouseleave', () => {
                gsap.to(btn, { x: 0, y: 0, duration: 0.5, ease: "elastic.out(1, 0.3)" });
            });
        });

        // Card Spotlight
        cards.forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                card.style.setProperty('--mouse-x', `${x}px`);
                card.style.setProperty('--mouse-y', `${y}px`);
            });
        });

        // 5. Workflow Pin-Scroll Logic
        const steps = document.querySelectorAll('.step-item');
        const visuals = document.querySelectorAll('.visual-item');

        gsap.set(visuals[0], { opacity: 1, y: 0 });
        gsap.set(steps[0], { opacity: 1 });

        steps.forEach((step, i) => {
            ScrollTrigger.create({
                trigger: step,
                start: "top center",
                end: "bottom center",
                onEnter: () => setActive(i),
                onEnterBack: () => setActive(i)
            });
        });

        function setActive(index) {
            steps.forEach((s, i) => {
                gsap.to(s, { opacity: i === index ? 1 : 0.2, duration: 0.3 });
            });
            visuals.forEach((v, i) => {
                if (i === index) {
                    gsap.to(v, { opacity: 1, y: 0, duration: 0.5 });
                } else {
                    gsap.to(v, { opacity: 0, y: 20, duration: 0.5 });
                }
            });
        }

        // 6. Video Modal Logic
        window.openVideoModal = function() {
            const modal = document.getElementById('demo-modal');
            modal.classList.remove('hidden');
            // Small delay to allow display:block to apply before opacity transition
            setTimeout(() => modal.classList.add('opacity-100'), 10);
        }

        window.closeVideoModal = function() {
            const modal = document.getElementById('demo-modal');
            modal.classList.remove('opacity-100');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }
    });
</script>
