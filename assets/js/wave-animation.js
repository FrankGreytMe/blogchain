document.addEventListener("DOMContentLoaded", function() {
    const canvas = document.getElementById("wave-canvas");
    if (!canvas) {
        console.error("Canvas element not found!");
        return;
    }

    // Initialize with default size
    canvas.width = window.innerWidth;
    canvas.height = 200;

    paper.setup(canvas);
    const view = paper.project.view;
    let paths = new paper.Group();

    // Get container safely - THIS WAS MISSING BEFORE
    const container = canvas.parentElement || document.body;

    function isDesktop() {
        return window.innerWidth > 768;
    }

    function addPoints(path, quantity) {
        path.add(view.bounds.bottomLeft);
        for (let i = -1; i <= quantity + 1; i++) {
            const x = (view.viewSize.width / quantity) * i;
            const y = view.viewSize.height / (isDesktop() ? 2.618 : 4);
            path.add(new paper.Point(x, y));
        }
        path.add(view.bounds.bottomRight);
    }

    function addPath(quantity, color, opacity) {
        const path = new paper.Path();
        path.fillColor = new paper.Color(color);
        path.opacity = opacity;
        addPoints(path, quantity);
        path.smooth();
        return path;
    }

    function createPaths() {
        paths.removeChildren();
        let n = 1;
        let opacity = 0.1 / (n / 1);
        for (let i = 1; i <= n; i++) {
            const path = addPath(isDesktop() ? 36 : 26 - i, "#99999", i * opacity);
            path.position.y += 125 * i;
            paths.addChild(path);
        }

        n = 2;
        opacity = 1 / (n / 2);
        for (let i = 1; i <= n; i++) {
            const path = addPath((isDesktop() ? 22 : 14) - i, "#D0D0D0", i * opacity);
            path.position.y += (isDesktop() ? 125 : 195) * i;
            paths.addChild(path);
        }
    }

    function animatePath(path, event, index) {
        const value1 = isDesktop() ? 16 : 10;
        const value2 = isDesktop() ? 48 : 42;
        const multy = isDesktop() ? 0.8 : 1.2;
        path.segments.forEach((segment, i) => {
            if (i > 0 && i < path.segments.length - 1) {
                const sin = Math.sin(event.time * multy + i - index);
                segment.point.y = sin * value1 + view.viewSize.height / (isDesktop() ? 2.618 : 4) + index * value2;
            }
        });
        path.smooth();
    }

    function resizeCanvas() {
        // Now using the properly defined container
        const width = container.clientWidth || window.innerWidth;
        canvas.width = width;
        canvas.height = 200;
        view.viewSize = new paper.Size(width, 200);
        createPaths();
    }

    // Initial setup
    resizeCanvas();

    view.onFrame = (event) => {
        paths.children.forEach((path, i) => {
            animatePath(path, event, i);
        });
    };

    window.addEventListener("resize", function() {
        resizeCanvas();
    });
});