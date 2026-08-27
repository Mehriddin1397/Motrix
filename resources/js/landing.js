/**
 * Motrix.uz landing page — canvas scrollytelling + scroll-reveal animations.
 *
 * #story-scroll is a tall (multi-viewport) container with a `position:
 * sticky` canvas pinned inside it. As the user scrolls through that whole
 * container — hero copy first, then the About copy that crossfades in next
 * to it — scroll progress (0..1) is mapped to a frame index (1..N) and the
 * matching preloaded frame image is drawn onto the canvas with a "cover"
 * fit. The motorcycle keeps animating continuously across both phases
 * instead of stopping once the hero copy fades out.
 */

document.addEventListener('DOMContentLoaded', () => {
    initScrollytelling();
    initScrollReveal();
    initScrollCue();
});

function initScrollytelling() {
    const section = document.getElementById('story-scroll');
    const canvas = document.getElementById('frame-canvas');
    const dataEl = document.getElementById('frame-data');

    if (!section || !canvas || !dataEl) return;

    const frameUrls = JSON.parse(dataEl.textContent);
    const frameCount = frameUrls.length;
    const ctx = canvas.getContext('2d');

    /** @type {HTMLImageElement[]} */
    const images = new Array(frameCount);
    let currentFrame = 1;
    let loadedCount = 0;

    // Draw a given 1-indexed frame onto the canvas using a "cover" fit,
    // matching the CSS object-fit: cover behaviour of a real <video>.
    function drawFrame(frameIndex) {
        const img = images[frameIndex - 1];
        if (!img || !img.complete || img.naturalWidth === 0) return;

        const canvasWidth = canvas.width;
        const canvasHeight = canvas.height;
        const imageRatio = img.naturalWidth / img.naturalHeight;
        const canvasRatio = canvasWidth / canvasHeight;

        let drawWidth = canvasWidth;
        let drawHeight = canvasHeight;

        if (canvasRatio > imageRatio) {
            drawHeight = canvasWidth / imageRatio;
        } else {
            drawWidth = canvasHeight * imageRatio;
        }

        const offsetX = (canvasWidth - drawWidth) / 2;
        const offsetY = (canvasHeight - drawHeight) / 2;

        ctx.clearRect(0, 0, canvasWidth, canvasHeight);
        ctx.drawImage(img, offsetX, offsetY, drawWidth, drawHeight);
    }

    // Size the canvas backing store to its own rendered box (not the
    // window) — on desktop it only occupies part of the viewport width
    // once the About copy splits the screen — scaled by device pixel
    // ratio so frames stay crisp on high-DPI screens, then redraw.
    function resizeCanvas() {
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        const box = canvas.getBoundingClientRect();
        canvas.width = box.width * dpr;
        canvas.height = box.height * dpr;
        drawFrame(currentFrame);
    }

    // Map vertical scroll progress through #hero-scroll to a frame index,
    // falling back to the nearest already-loaded frame while preloading.
    function updateFrameFromScroll() {
        const rect = section.getBoundingClientRect();
        const scrollableHeight = section.offsetHeight - window.innerHeight;
        const progress = scrollableHeight > 0
            ? clamp(-rect.top / scrollableHeight, 0, 1)
            : 0;

        section.style.setProperty('--progress', progress.toFixed(4));

        let targetFrame = 1 + Math.round(progress * (frameCount - 1));
        targetFrame = findNearestLoadedFrame(targetFrame);

        if (targetFrame !== currentFrame) {
            currentFrame = targetFrame;
            drawFrame(currentFrame);
        }
    }

    function findNearestLoadedFrame(preferredFrame) {
        if (images[preferredFrame - 1]?.complete) return preferredFrame;

        for (let offset = 1; offset < frameCount; offset++) {
            const below = preferredFrame - offset;
            const above = preferredFrame + offset;
            if (below >= 1 && images[below - 1]?.complete) return below;
            if (above <= frameCount && images[above - 1]?.complete) return above;
        }

        return preferredFrame;
    }

    // Throttle scroll handling to one update per animation frame.
    let ticking = false;
    function onScroll() {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => {
            updateFrameFromScroll();
            ticking = false;
        });
    }

    // Preload every frame. The first frame is prioritised so the hero
    // renders immediately; the rest stream in for smooth scrubbing.
    function preloadFrames() {
        frameUrls.forEach((url, i) => {
            const img = new Image();
            img.decoding = 'async';
            img.onload = img.onerror = () => {
                loadedCount++;
                if (i === 0) {
                    resizeCanvas();
                }
            };
            img.src = url;
            images[i] = img;
        });
    }

    window.addEventListener('resize', resizeCanvas, { passive: true });
    window.addEventListener('scroll', onScroll, { passive: true });

    resizeCanvas();
    preloadFrames();
    updateFrameFromScroll();
}

function clamp(value, min, max) {
    return Math.min(Math.max(value, min), max);
}

// Fade + slide in `.reveal` elements as they enter the viewport.
function initScrollReveal() {
    const targets = document.querySelectorAll('.reveal');
    if (!targets.length) return;

    if (!('IntersectionObserver' in window)) {
        targets.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.15, rootMargin: '0px 0px -10% 0px' }
    );

    targets.forEach((el) => observer.observe(el));
}

// Clicking the scroll cue smooth-scrolls one viewport down.
function initScrollCue() {
    const cue = document.getElementById('scroll-cue');
    if (!cue) return;

    cue.addEventListener('click', () => {
        window.scrollTo({ top: window.innerHeight * 0.9, behavior: 'smooth' });
    });
}
