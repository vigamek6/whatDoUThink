/**
 * Manga Fire Engine (Single Stylized Flame Engine)
 * Anime / Manga Cel-Shaded Fire Effect
 * Inspired by Shonen Manga / Demon Slayer / Rengoku flame aura.
 */

class MangaFireEngine {
    constructor(canvasId) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) return;
        this.ctx = this.canvas.getContext('2d');
        
        this.width = 0;
        this.height = 0;
        this.time = 0;
        this.intensity = 1.0;
        this.targetIntensity = 1.0;
        
        this.embers = [];
        this.maxEmbers = 75;
        this.flameTongues = [];
        this.numTongues = 9; // Core flame tongues radiating from base
        
        this.sparks = [];
        this.mousePos = { x: 0, y: 0, active: false };

        this.init();
    }

    init() {
        this.resize();
        window.addEventListener('resize', () => this.resize());

        // Initialize flame tongues
        this.initTongues();

        // Listen for mouse movement for interactive spark trails
        window.addEventListener('mousemove', (e) => {
            this.mousePos.x = e.clientX;
            this.mousePos.y = e.clientY;
            this.mousePos.active = true;
            if (Math.random() < 0.4) {
                this.addSpark(e.clientX, e.clientY);
            }
        });

        window.addEventListener('touchmove', (e) => {
            if (e.touches.length > 0) {
                this.mousePos.x = e.touches[0].clientX;
                this.mousePos.y = e.touches[0].clientY;
                this.mousePos.active = true;
                if (Math.random() < 0.4) {
                    this.addSpark(e.touches[0].clientX, e.touches[0].clientY);
                }
            }
        }, { passive: true });

        // Start render loop
        this.render = this.render.bind(this);
        requestAnimationFrame(this.render);
    }

    resize() {
        this.width = this.canvas.width = window.innerWidth;
        this.height = this.canvas.height = window.innerHeight;
        this.initTongues();
    }

    initTongues() {
        this.flameTongues = [];
        const baseWidth = Math.min(this.width * 0.9, 850);
        const centerX = this.width / 2;

        for (let i = 0; i < this.numTongues; i++) {
            const offsetRatio = (i - (this.numTongues - 1) / 2) / (this.numTongues / 2);
            this.flameTongues.push({
                baseX: centerX + offsetRatio * (baseWidth * 0.38),
                baseWidth: (baseWidth / this.numTongues) * (1.6 - Math.abs(offsetRatio) * 0.5),
                maxHeight: this.height * (0.65 - Math.abs(offsetRatio) * 0.22),
                phase: Math.random() * Math.PI * 2,
                speed: 2.2 + Math.random() * 1.8,
                curlAmp: 25 + Math.random() * 20,
                layerOffset: i % 2 === 0 ? 0 : 0.5
            });
        }
    }

    setIntensity(level, duration = 1000) {
        this.targetIntensity = level;
    }

    burst() {
        this.intensity = 2.4;
        for (let i = 0; i < 40; i++) {
            this.addEmber(true);
        }
    }

    addEmber(isBurst = false) {
        const centerX = this.width / 2;
        const spread = this.width * 0.45;
        this.embers.push({
            x: centerX + (Math.random() - 0.5) * spread,
            y: this.height - Math.random() * 80,
            vx: (Math.random() - 0.5) * (isBurst ? 4.5 : 2.2),
            vy: -(Math.random() * (isBurst ? 9 : 4.5) + 3),
            size: Math.random() * 5 + 3,
            rot: Math.random() * Math.PI * 2,
            vRot: (Math.random() - 0.5) * 0.15,
            life: 1.0,
            decay: Math.random() * 0.012 + 0.007,
            colorType: Math.random() > 0.4 ? 'gold' : 'crimson'
        });
    }

    addSpark(x, y) {
        this.sparks.push({
            x: x,
            y: y,
            vx: (Math.random() - 0.5) * 3,
            vy: -Math.random() * 3 - 1,
            size: Math.random() * 4 + 2,
            rot: Math.random() * Math.PI * 2,
            life: 1.0,
            decay: 0.035
        });
    }

    // Render manga flame background with cel-shaded flame contours
    render() {
        this.time += 0.032;

        // Smoothly adjust intensity
        this.intensity += (this.targetIntensity - this.intensity) * 0.05;

        const ctx = this.ctx;
        const w = this.width;
        const h = this.height;

        // 1. Dark anime backdrop with deep crimson radial ambiance
        ctx.clearRect(0, 0, w, h);
        
        // Dark base
        ctx.fillStyle = '#0a0607';
        ctx.fillRect(0, 0, w, h);

        // Radial fiery aura in background
        const auraGrad = ctx.createRadialGradient(
            w / 2, h + 50, 50,
            w / 2, h * 0.6, Math.max(w, h) * 0.85
        );
        auraGrad.addColorStop(0, 'rgba(180, 20, 0, 0.45)');
        auraGrad.addColorStop(0.35, 'rgba(120, 10, 0, 0.28)');
        auraGrad.addColorStop(0.7, 'rgba(40, 5, 8, 0.2)');
        auraGrad.addColorStop(1, 'rgba(10, 6, 7, 0.95)');
        ctx.fillStyle = auraGrad;
        ctx.fillRect(0, 0, w, h);

        // 2. Manga speedline rays (subtle radial energy spikes)
        this.drawMangaSpeedlines(ctx, w, h);

        // 3. Manga Halftone Dot pattern overlay at top edges
        this.drawMangaHalftone(ctx, w, h);

        // 4. Render the Single Manga Flame (multi-tier cel-shaded)
        this.drawMangaFlame(ctx, w, h);

        // 5. Render Anime Embers (Sharp rhomboid/diamond embers)
        this.updateAndDrawEmbers(ctx);

        // 6. Interactive sparks
        this.updateAndDrawSparks(ctx);

        requestAnimationFrame(this.render);
    }

    drawMangaSpeedlines(ctx, w, h) {
        ctx.save();
        const centerX = w / 2;
        const centerY = h + 100;
        const count = 18;
        ctx.strokeStyle = 'rgba(255, 69, 0, 0.06)';
        ctx.lineWidth = 2;

        for (let i = 0; i < count; i++) {
            const angle = -Math.PI / 2 + ((i - count / 2) / count) * (Math.PI * 0.78);
            const flicker = Math.sin(this.time * 2 + i * 1.5) * 0.02;
            const rayAngle = angle + flicker;
            const length = Math.max(w, h) * 1.2;

            ctx.beginPath();
            ctx.moveTo(centerX, centerY);
            ctx.lineTo(
                centerX + Math.cos(rayAngle) * length,
                centerY + Math.sin(rayAngle) * length
            );
            ctx.stroke();
        }
        ctx.restore();
    }

    drawMangaHalftone(ctx, w, h) {
        // Subtle comic screen tone effect on the outer borders
        ctx.save();
        ctx.fillStyle = 'rgba(255, 120, 0, 0.035)';
        const step = 28;
        const rows = 6;
        for (let y = 0; y < rows * step; y += step) {
            for (let x = 0; x < w; x += step) {
                if ((x + y) % (step * 2) === 0) {
                    ctx.beginPath();
                    ctx.arc(x, y, 1.8, 0, Math.PI * 2);
                    ctx.fill();
                }
            }
        }
        ctx.restore();
    }

    drawMangaFlame(ctx, w, h) {
        const centerX = w / 2;
        const baseY = h + 20;

        // Cel-shaded flame layers from outside to inside:
        // 1. Dark inked rim & deep red aura
        // 2. Fiery scarlet / crimson
        // 3. Bright vibrant orange
        // 4. Brilliant gold
        // 5. Blinding white-hot core

        const layers = [
            { scale: 1.15, color: '#8a0a14', stroke: '#3a0206', strokeWidth: 4, blur: 20 },
            { scale: 1.0,  color: '#e51e1e', stroke: '#1a0002', strokeWidth: 3, blur: 10 },
            { scale: 0.82, color: '#ff6600', stroke: '#b31500', strokeWidth: 2, blur: 6 },
            { scale: 0.62, color: '#ffb700', stroke: '#cc4400', strokeWidth: 1.5, blur: 4 },
            { scale: 0.38, color: '#fff677', stroke: '#e69500', strokeWidth: 1, blur: 2 },
            { scale: 0.18, color: '#ffffff', stroke: '#fff0a0', strokeWidth: 0, blur: 0 }
        ];

        layers.forEach(layer => {
            ctx.save();
            ctx.fillStyle = layer.color;
            if (layer.strokeWidth > 0) {
                ctx.strokeStyle = layer.stroke;
                ctx.lineWidth = layer.strokeWidth;
            }
            if (layer.blur > 0) {
                ctx.shadowColor = layer.color;
                ctx.shadowBlur = layer.blur;
            }

            ctx.beginPath();
            ctx.moveTo(centerX - (w * 0.42 * layer.scale), baseY);

            // Construct cel-shaded flame silhouette using flame tongues
            const pts = [];
            for (let i = 0; i < this.flameTongues.length; i++) {
                const tongue = this.flameTongues[i];
                const t = this.time * tongue.speed + tongue.phase;
                
                // Turbulent flame height & curl
                const flickerH = Math.sin(t) * 35 + Math.cos(t * 1.7) * 20;
                const tongueHeight = (tongue.maxHeight + flickerH) * layer.scale * this.intensity;
                
                const sway = Math.sin(t * 1.3) * tongue.curlAmp * layer.scale;
                const tipX = tongue.baseX + sway;
                const tipY = baseY - tongueHeight;

                pts.push({
                    baseX: tongue.baseX,
                    baseW: tongue.baseWidth * layer.scale,
                    tipX: tipX,
                    tipY: tipY,
                    sway: sway
                });
            }

            // Draw organic contour connecting all tongues with sharp comic flame tips
            for (let i = 0; i < pts.length; i++) {
                const p = pts[i];
                const leftBaseX = p.baseX - p.baseW / 2;
                const rightBaseX = p.baseX + p.baseW / 2;

                // Bottom to tip curve (manga sharp flame tip)
                ctx.quadraticCurveTo(
                    leftBaseX + p.sway * 0.4,
                    baseY - (baseY - p.tipY) * 0.45,
                    p.tipX,
                    p.tipY
                );

                // Tip to right base curve
                ctx.quadraticCurveTo(
                    rightBaseX + p.sway * 0.3,
                    baseY - (baseY - p.tipY) * 0.5,
                    rightBaseX,
                    baseY
                );
            }

            ctx.lineTo(centerX + (w * 0.42 * layer.scale), baseY);
            ctx.closePath();
            ctx.fill();
            if (layer.strokeWidth > 0) {
                ctx.stroke();
            }
            ctx.restore();
        });
    }

    updateAndDrawEmbers(ctx) {
        // Maintain ember count
        if (this.embers.length < this.maxEmbers) {
            this.addEmber();
        }

        for (let i = this.embers.length - 1; i >= 0; i--) {
            const e = this.embers[i];
            e.x += e.vx + Math.sin(this.time * 2 + e.y * 0.02) * 1.2;
            e.y += e.vy;
            e.rot += e.vRot;
            e.life -= e.decay;

            if (e.life <= 0 || e.y < -50) {
                this.embers.splice(i, 1);
                continue;
            }

            // Draw manga diamond/rhombus spark
            ctx.save();
            ctx.translate(e.x, e.y);
            ctx.rotate(e.rot);

            const s = e.size * e.life;
            ctx.fillStyle = e.colorType === 'gold' 
                ? `rgba(255, 220, 80, ${e.life})`
                : `rgba(255, 70, 30, ${e.life})`;
            ctx.strokeStyle = `rgba(30, 5, 5, ${e.life * 0.8})`;
            ctx.lineWidth = 1;

            // Diamond/Rhombus shape (Manga style particle)
            ctx.beginPath();
            ctx.moveTo(0, -s * 1.6);
            ctx.lineTo(s * 0.7, 0);
            ctx.lineTo(0, s * 1.6);
            ctx.lineTo(-s * 0.7, 0);
            ctx.closePath();
            ctx.fill();
            ctx.stroke();

            ctx.restore();
        }
    }

    updateAndDrawSparks(ctx) {
        for (let i = this.sparks.length - 1; i >= 0; i--) {
            const s = this.sparks[i];
            s.x += s.vx;
            s.y += s.vy;
            s.life -= s.decay;

            if (s.life <= 0) {
                this.sparks.splice(i, 1);
                continue;
            }

            ctx.save();
            ctx.fillStyle = `rgba(255, 235, 120, ${s.life})`;
            ctx.shadowColor = '#ff6a00';
            ctx.shadowBlur = 6;
            ctx.beginPath();
            ctx.arc(s.x, s.y, s.size * s.life, 0, Math.PI * 2);
            ctx.fill();
            ctx.restore();
        }
    }
}

window.MangaFireEngine = MangaFireEngine;
