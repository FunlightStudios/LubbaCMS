/**
 * Theme Toggle Script
 * Dark/Light Mode Switcher with LocalStorage
 */

(function() {
    'use strict';
    
    // Get saved theme or default to light
    const savedTheme = localStorage.getItem('theme') || 'light';
    
    // Apply theme on page load
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        // Create toggle button if it doesn't exist
        if (!document.querySelector('.theme-toggle')) {
            createToggleButton();
        }
        
        // Add click event to toggle button
        const toggleBtn = document.querySelector('.theme-toggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', toggleTheme);
        }
        
        // Update button icon
        updateToggleIcon();
    });
    
    /**
     * Create the theme toggle button
     */
    function createToggleButton() {
        const button = document.createElement('button');
        button.className = 'theme-toggle';
        button.setAttribute('aria-label', 'Toggle Dark Mode');
        button.setAttribute('title', 'Theme wechseln');
        
        button.innerHTML = `
            <span class="sun-icon icon">☀️</span>
            <span class="moon-icon icon">🌙</span>
        `;
        
        document.body.appendChild(button);
    }
    
    /**
     * Toggle between light and dark theme
     */
    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        // Apply new theme
        document.documentElement.setAttribute('data-theme', newTheme);
        
        // Save to localStorage
        localStorage.setItem('theme', newTheme);
        
        // Update icon
        updateToggleIcon();
        
        // Add animation class
        const button = document.querySelector('.theme-toggle');
        button.style.transform = 'rotate(360deg)';
        setTimeout(() => {
            button.style.transform = '';
        }, 300);
        
        // Show notification
        showThemeNotification(newTheme);
    }
    
    /**
     * Update toggle button icon
     */
    function updateToggleIcon() {
        const theme = document.documentElement.getAttribute('data-theme');
        const button = document.querySelector('.theme-toggle');
        
        if (button) {
            if (theme === 'dark') {
                button.setAttribute('title', 'Zu Light Mode wechseln');
            } else {
                button.setAttribute('title', 'Zu Dark Mode wechseln');
            }
        }
    }
    
    /**
     * Show theme change notification
     */
    function showThemeNotification(theme) {
        // Remove existing notification
        const existing = document.querySelector('.theme-notification');
        if (existing) {
            existing.remove();
        }
        
        // Create notification
        const notification = document.createElement('div');
        notification.className = 'theme-notification';
        notification.innerHTML = `
            <span>${theme === 'dark' ? '🌙 Dark Mode aktiviert' : '☀️ Light Mode aktiviert'}</span>
        `;
        
        // Style notification
        notification.style.cssText = `
            position: fixed;
            bottom: 100px;
            right: 30px;
            background: ${theme === 'dark' ? '#1e293b' : 'white'};
            color: ${theme === 'dark' ? '#f1f5f9' : '#1e293b'};
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            z-index: 9999;
            animation: slideInRight 0.3s ease-out;
            font-weight: 600;
            border: 2px solid ${theme === 'dark' ? '#334155' : '#e2e8f0'};
        `;
        
        document.body.appendChild(notification);
        
        // Remove after 2 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }, 2000);
    }
    
    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        
        .theme-toggle {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
    `;
    document.head.appendChild(style);
    
})();
