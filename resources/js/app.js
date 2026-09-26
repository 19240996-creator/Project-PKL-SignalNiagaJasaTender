import './bootstrap';

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const landingPage = document.querySelector('[data-landing-page]');

if (landingPage) {
	const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	if (!prefersReducedMotion) {
		const context = gsap.context(() => {
			const introTimeline = gsap.timeline({ defaults: { ease: 'power3.out' } });

			introTimeline
				.from('[data-landing-header]', { y: -24, autoAlpha: 0, duration: 0.7 })
				.from('[data-hero-item]', { y: 28, autoAlpha: 0, duration: 0.7, stagger: 0.1 }, '-=0.35')
				.from('[data-stat-card]', { y: 22, autoAlpha: 0, duration: 0.55, stagger: 0.08 }, '-=0.35');

			gsap.utils.toArray('[data-reveal-section]').forEach((section) => {
				gsap.from(section.querySelectorAll('[data-reveal-item]'), {
					y: 32,
					autoAlpha: 0,
					duration: 0.7,
					stagger: 0.1,
					ease: 'power2.out',
					scrollTrigger: {
						trigger: section,
						start: 'top 78%',
						once: true,
					},
				});
			});
		}, landingPage);

		window.addEventListener('pagehide', () => context.revert(), { once: true });
	}
}
