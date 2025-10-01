# Murugo Design System Documentation

## Overview

The Murugo Design System is a comprehensive, Webflow-inspired design system built with Tailwind CSS. It provides a consistent, modern, and accessible foundation for the entire application.

## Table of Contents

1. [Color Palette](#color-palette)
2. [Typography](#typography)
3. [Spacing & Layout](#spacing--layout)
4. [Components](#components)
5. [CSS Classes](#css-classes)
6. [Usage Examples](#usage-examples)
7. [Best Practices](#best-practices)

## Color Palette

### Primary Colors
```css
--primary-50: #f0f4ff    /* Lightest blue */
--primary-100: #e0e7ff   /* Very light blue */
--primary-200: #c7d2fe   /* Light blue */
--primary-300: #a5b4fc   /* Medium light blue */
--primary-400: #818cf8   /* Medium blue */
--primary-500: #6366f1   /* Base blue */
--primary-600: #4f46e5   /* Medium dark blue */
--primary-700: #4338ca   /* Dark blue */
--primary-800: #3730a3   /* Very dark blue */
--primary-900: #312e81   /* Darkest blue */
```

### Secondary Colors
```css
--secondary-50: #f8fafc   /* Lightest gray */
--secondary-100: #f1f5f9  /* Very light gray */
--secondary-200: #e2e8f0  /* Light gray */
--secondary-300: #cbd5e1  /* Medium light gray */
--secondary-400: #94a3b8  /* Medium gray */
--secondary-500: #64748b  /* Base gray */
--secondary-600: #475569  /* Medium dark gray */
--secondary-700: #334155  /* Dark gray */
--secondary-800: #1e293b  /* Very dark gray */
--secondary-900: #0f172a  /* Darkest gray */
```

### Accent Colors
```css
--accent-50: #fef3c7     /* Lightest yellow */
--accent-100: #fde68a    /* Very light yellow */
--accent-200: #fcd34d    /* Light yellow */
--accent-300: #fbbf24    /* Medium light yellow */
--accent-400: #f59e0b    /* Medium yellow */
--accent-500: #d97706    /* Base yellow */
--accent-600: #b45309    /* Medium dark yellow */
--accent-700: #92400e    /* Dark yellow */
--accent-800: #78350f    /* Very dark yellow */
--accent-900: #451a03    /* Darkest yellow */
```

### Status Colors
```css
--success: #22c55e        /* Green for success states */
--warning: #d97706        /* Orange for warning states */
--error: #ef4444          /* Red for error states */
--info: #6366f1           /* Blue for info states */
```

## Typography

### Font Family
- **Primary**: Inter (Google Fonts)
- **Fallback**: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif

### Font Weights
- **Light**: 300
- **Regular**: 400
- **Medium**: 500
- **Semibold**: 600
- **Bold**: 700

### Typography Scale
```css
/* Headings */
.text-anthropic-heading {
    font-size: 1.875rem;    /* 30px */
    font-weight: 600;
    line-height: 1.2;
    color: #0f172a;
}

.text-anthropic-subheading {
    font-size: 1.125rem;    /* 18px */
    font-weight: 500;
    line-height: 1.5;
    color: #475569;
}

.text-anthropic-body {
    font-size: 1rem;        /* 16px */
    line-height: 1.6;
    color: #64748b;
}
```

## Spacing & Layout

### Spacing Scale
```css
--spacing-xs: 0.25rem;     /* 4px */
--spacing-sm: 0.5rem;      /* 8px */
--spacing: 1rem;           /* 16px */
--spacing-lg: 1.5rem;      /* 24px */
--spacing-xl: 2rem;        /* 32px */
--spacing-2xl: 3rem;       /* 48px */
```

### Border Radius
```css
--radius-sm: 0.375rem;     /* 6px */
--radius: 0.5rem;          /* 8px */
--radius-md: 0.75rem;      /* 12px */
--radius-lg: 1rem;         /* 16px */
--radius-xl: 1.5rem;       /* 24px */
```

### Shadows
```css
--shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
--shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
--shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
--shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
--shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
```

## Components

### Button System

#### Base Button Classes
```css
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    border: 1px solid transparent;
    border-radius: 0.5rem;
    font-weight: 500;
    text-align: center;
    transition: all 0.2s ease-in-out;
    cursor: pointer;
    min-height: 44px; /* Touch-friendly */
}

.btn:focus {
    outline: 2px solid var(--primary-500);
    outline-offset: 2px;
}

.btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
```

#### Button Variants
```css
/* Primary Button */
.btn-primary {
    background-color: var(--primary-600);
    color: white;
    border-color: var(--primary-600);
}

.btn-primary:hover {
    background-color: var(--primary-700);
    border-color: var(--primary-700);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
    transform: translateY(-1px);
}

/* Secondary Button */
.btn-secondary {
    background-color: var(--secondary-600);
    color: white;
    border-color: var(--secondary-600);
}

.btn-secondary:hover {
    background-color: var(--secondary-700);
    border-color: var(--secondary-700);
}

/* Outline Button */
.btn-outline {
    background-color: transparent;
    color: var(--primary-600);
    border-color: var(--primary-600);
}

.btn-outline:hover {
    background-color: var(--primary-50);
    border-color: var(--primary-700);
}

/* Ghost Button */
.btn-ghost {
    background-color: transparent;
    color: var(--primary-600);
    border-color: transparent;
}

.btn-ghost:hover {
    background-color: var(--primary-50);
}
```

#### Button Sizes
```css
.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.btn-md {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
}

.btn-xl {
    padding: 1rem 2rem;
    font-size: 1.125rem;
}
```

### Card System

#### Base Card Classes
```css
.card {
    background-color: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    border: 1px solid #e5e7eb;
    overflow: hidden;
    transition: all 0.2s ease-in-out;
}

.card:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
```

#### Card Variants
```css
/* Default Card */
.card-default {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
}

/* Elevated Card */
.card-elevated {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Subtle Card */
.card-subtle {
    background-color: #f9fafb;
    border-color: #f3f4f6;
}
```

### Form Elements

#### Input Fields
```css
.form-input {
    display: block;
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    line-height: 1.25rem;
    color: #111827;
    background-color: white;
    transition: all 0.2s ease-in-out;
    min-height: 44px; /* Touch-friendly */
}

.form-input:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-input::placeholder {
    color: #9ca3af;
}

/* Error State */
.form-input.error {
    border-color: #ef4444;
    color: #991b1b;
}

.form-input.error:focus {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}
```

#### Labels
```css
.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.25rem;
}
```

### Badge System

#### Base Badge Classes
```css
.badge {
    display: inline-flex;
    align-items: center;
    padding: 0.125rem 0.625rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    line-height: 1;
}
```

#### Badge Variants
```css
.badge-primary {
    background-color: #dbeafe;
    color: #1e40af;
}

.badge-success {
    background-color: #dcfce7;
    color: #166534;
}

.badge-warning {
    background-color: #fef3c7;
    color: #92400e;
}

.badge-error {
    background-color: #fee2e2;
    color: #991b1b;
}

.badge-gray {
    background-color: #f3f4f6;
    color: #374151;
}
```

## CSS Classes

### Utility Classes

#### Text Utilities
```css
.text-balance {
    text-wrap: balance;
}

.text-anthropic-heading {
    font-size: 1.875rem;
    font-weight: 600;
    line-height: 1.2;
    color: #0f172a;
}

.text-anthropic-subheading {
    font-size: 1.125rem;
    font-weight: 500;
    line-height: 1.5;
    color: #475569;
}

.text-anthropic-body {
    font-size: 1rem;
    line-height: 1.6;
    color: #64748b;
}
```

#### Animation Utilities
```css
.animate-fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

.animate-slide-up {
    animation: slideUp 0.5s ease-out;
}

.animate-bounce-in {
    animation: bounceIn 0.6s ease-out;
}

.hover-lift {
    transition: transform 0.2s ease-in-out;
}

.hover-lift:hover {
    transform: translateY(-4px);
}

.hover-scale {
    transition: transform 0.2s ease-in-out;
}

.hover-scale:hover {
    transform: scale(1.05);
}
```

#### Touch-Friendly Classes
```css
.touch-target {
    min-height: 44px;
    min-width: 44px;
}
```

#### Responsive Classes
```css
.mobile-padding {
    padding-left: 1rem;
    padding-right: 1rem;
}

@media (min-width: 640px) {
    .mobile-padding {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
}

@media (min-width: 1024px) {
    .mobile-padding {
        padding-left: 2rem;
        padding-right: 2rem;
    }
}
```

## Usage Examples

### Basic Button Usage
```html
<!-- Primary Button -->
<button class="btn btn-primary btn-md">Click Me</button>

<!-- Secondary Button -->
<button class="btn btn-secondary btn-lg">Secondary Action</button>

<!-- Outline Button -->
<button class="btn btn-outline btn-sm">Cancel</button>

<!-- Ghost Button -->
<button class="btn btn-ghost btn-md">Skip</button>
```

### Card Usage
```html
<!-- Default Card -->
<div class="card card-default">
    <div class="p-6">
        <h3 class="text-lg font-semibold mb-2">Card Title</h3>
        <p class="text-gray-600">Card content goes here.</p>
    </div>
</div>

<!-- Elevated Card -->
<div class="card card-elevated">
    <div class="p-6">
        <h3 class="text-lg font-semibold mb-2">Featured Content</h3>
        <p class="text-gray-600">This card has more emphasis.</p>
    </div>
</div>
```

### Form Usage
```html
<!-- Basic Form -->
<form class="space-y-4">
    <div>
        <label class="form-label">Email Address</label>
        <input type="email" class="form-input" placeholder="Enter your email">
    </div>
    
    <div>
        <label class="form-label">Password</label>
        <input type="password" class="form-input" placeholder="Enter your password">
    </div>
    
    <button type="submit" class="btn btn-primary btn-md">Sign In</button>
</form>

<!-- Form with Error State -->
<div>
    <label class="form-label">Username</label>
    <input type="text" class="form-input error" placeholder="Enter username">
    <p class="text-red-600 text-sm mt-1">Username is required</p>
</div>
```

### Badge Usage
```html
<!-- Status Badges -->
<span class="badge badge-success">Active</span>
<span class="badge badge-warning">Pending</span>
<span class="badge badge-error">Error</span>
<span class="badge badge-primary">New</span>
<span class="badge badge-gray">Draft</span>
```

## Best Practices

### 1. Color Usage
- Use primary colors for main actions and branding
- Use secondary colors for neutral elements
- Use accent colors sparingly for highlights
- Use status colors consistently for their intended purposes

### 2. Typography
- Use Inter font for all text elements
- Maintain proper hierarchy with heading sizes
- Ensure sufficient contrast ratios (4.5:1 minimum)
- Use line-height of 1.6 for body text

### 3. Spacing
- Use the defined spacing scale consistently
- Maintain 44px minimum touch targets
- Use generous white space for breathing room
- Follow the 8px grid system

### 4. Components
- Use semantic HTML elements
- Include proper ARIA labels for accessibility
- Test components across different screen sizes
- Maintain consistent hover and focus states

### 5. Performance
- Use CSS custom properties for theming
- Minimize custom CSS by leveraging Tailwind utilities
- Use transform and opacity for animations
- Optimize images and assets

### 6. Accessibility
- Ensure keyboard navigation works
- Provide sufficient color contrast
- Include screen reader support
- Test with assistive technologies

## Customization

### Adding New Colors
```css
:root {
    --custom-50: #f0f9ff;
    --custom-500: #0ea5e9;
    --custom-900: #0c4a6e;
}
```

### Adding New Components
1. Create the component HTML structure
2. Add CSS classes following the naming convention
3. Include hover and focus states
4. Test across different screen sizes
5. Document usage examples

### Theming
The design system supports theming through CSS custom properties. To create a new theme:

1. Override the CSS custom properties
2. Ensure all components use the custom properties
3. Test color contrast ratios
4. Validate accessibility compliance

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Resources

- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Inter Font](https://fonts.google.com/specimen/Inter)
- [Webflow Design Principles](https://webflow.com/design)
- [WCAG 2.1 Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)

---

*This design system is maintained by the Murugo development team. For questions or contributions, please refer to the project documentation.*
