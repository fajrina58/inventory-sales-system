<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    <defs>
        <!-- Gradients -->
        <linearGradient id="sp-box-top" x1="12" y1="14" x2="52" y2="34" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stop-color="#60A5FA" />
            <stop offset="100%" stop-color="#3B82F6" />
        </linearGradient>
        <linearGradient id="sp-box-left" x1="12" y1="23" x2="32" y2="54" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stop-color="#1D4ED8" />
            <stop offset="100%" stop-color="#1E3A8A" />
        </linearGradient>
        <linearGradient id="sp-box-right" x1="32" y1="34" x2="52" y2="54" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stop-color="#2563EB" />
            <stop offset="100%" stop-color="#1D4ED8" />
        </linearGradient>
        <linearGradient id="sp-arrow-grad" x1="22" y1="46" x2="54" y2="10" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stop-color="#38BDF8" />
            <stop offset="50%" stop-color="#60A5FA" />
            <stop offset="100%" stop-color="#FFFFFF" />
        </linearGradient>
        <filter id="sp-glow" x="-20%" y="-20%" width="140%" height="140%" filterUnits="userSpaceOnUse">
            <feGaussianBlur stdDeviation="1.5" result="glow" />
            <feComposite in="SourceGraphic" in2="glow" operator="over" />
        </filter>
    </defs>

    <!-- Base Shadow -->
    <ellipse cx="32" cy="54" rx="18" ry="4.5" fill="#1E3A8A" fill-opacity="0.25" />

    <!-- Isometric Package: Left Face -->
    <path d="M12 23L32 34V54L12 43V23Z" fill="url(#sp-box-left)" />

    <!-- Isometric Package: Right Face -->
    <path d="M32 34L52 23V43L32 54V34Z" fill="url(#sp-box-right)" />

    <!-- Isometric Package: Top Face -->
    <path d="M32 12L52 23L32 34L12 23L32 12Z" fill="url(#sp-box-top)" />

    <!-- Package Tape / Seam Detail -->
    <path d="M32 12V23M32 23L42 28.5M32 23L22 28.5" stroke="#93C5FD" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" opacity="0.85" />

    <!-- Left Face: Real-time Inventory Bars -->
    <rect x="16" y="32" width="2.5" height="7" rx="1" fill="#93C5FD" opacity="0.8" />
    <rect x="21" y="29" width="2.5" height="12" rx="1" fill="#BAE6FD" opacity="0.9" />
    <rect x="26" y="35" width="2.5" height="9" rx="1" fill="#E0F2FE" opacity="0.9" />

    <!-- Right Face & Surge: Rising Sales Trend Line & Arrow -->
    <path d="M22 47L30 39L38 41L53 15" stroke="url(#sp-arrow-grad)" stroke-width="3.25" stroke-linecap="round" stroke-linejoin="round" filter="url(#sp-glow)" />
    <!-- Arrow Head -->
    <path d="M43 14H53.5V24.5" stroke="url(#sp-arrow-grad)" stroke-width="3.25" stroke-linecap="round" stroke-linejoin="round" />

    <!-- Gleaming Star/Circle at Summit (Sales Peak) -->
    <circle cx="53" cy="14" r="2.25" fill="#FFFFFF" />
    <circle cx="53" cy="14" r="4" fill="#38BDF8" fill-opacity="0.35" />
</svg>
