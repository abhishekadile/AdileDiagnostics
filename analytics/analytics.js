// Adile Analytics Tracking Script
class AdileAnalytics {
    constructor() {
        this.sessionId = this.generateSessionId();
        this.trackingEndpoint = '/analytics/track.php';
        this.initialized = false;
        this.pageLoadTime = Date.now();
    }

    init() {
        if (this.initialized) return;
        
        // Track page view on load
        this.trackPageView();
        
        // Set up form tracking
        this.setupFormTracking();
        
        // Set up behavior tracking
        this.setupBehaviorTracking();
        
        // Track page unload
        window.addEventListener('beforeunload', () => {
            this.trackPageUnload();
        });

        this.initialized = true;
    }

    generateSessionId() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    async sendData(data) {
        try {
            const response = await fetch(this.trackingEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    ...data,
                    sessionId: this.sessionId,
                    timestamp: new Date().toISOString()
                })
            });
            return await response.json();
        } catch (error) {
            console.error('Analytics tracking error:', error);
            return null;
        }
    }

    trackPageView() {
        this.sendData({
            type: 'pageview',
            pageUrl: window.location.href,
            referrerUrl: document.referrer
        });
    }

    trackPageUnload() {
        const timeOnPage = Date.now() - this.pageLoadTime;
        this.sendData({
            type: 'pageview',
            pageUrl: window.location.href,
            timeOnPage: timeOnPage
        });
    }

    trackEvent(category, action, label = null, value = null) {
        this.sendData({
            type: 'event',
            eventType: 'custom',
            category,
            action,
            label,
            value,
            pageUrl: window.location.href
        });
    }

    setupFormTracking() {
        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!form || !form.id) return;

            const formData = new FormData(form);
            const data = {};
            for (let [key, value] of formData.entries()) {
                // Exclude sensitive fields
                if (!key.toLowerCase().includes('password')) {
                    data[key] = value;
                }
            }

            this.sendData({
                type: 'form',
                formType: form.id,
                formData: data,
                pageUrl: window.location.href
            });
        });
    }

    setupBehaviorTracking() {
        // Track clicks
        document.addEventListener('click', (event) => {
            const element = event.target;
            this.trackBehavior('click', element);
        });

        // Track scroll depth
        let maxScroll = 0;
        document.addEventListener('scroll', this.throttle(() => {
            const scrollDepth = Math.round((window.scrollY + window.innerHeight) / document.documentElement.scrollHeight * 100);
            if (scrollDepth > maxScroll) {
                maxScroll = scrollDepth;
                this.sendData({
                    type: 'behavior',
                    behaviorType: 'scroll',
                    additionalData: { scrollDepth },
                    pageUrl: window.location.href
                });
            }
        }, 2000));
    }

    trackBehavior(type, element) {
        this.sendData({
            type: 'behavior',
            behaviorType: type,
            elementId: element.id,
            elementClass: element.className,
            elementText: element.textContent?.trim().substring(0, 100),
            pageUrl: window.location.href
        });
    }

    throttle(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
}

// Initialize analytics
const analytics = new AdileAnalytics();
document.addEventListener('DOMContentLoaded', () => analytics.init()); 