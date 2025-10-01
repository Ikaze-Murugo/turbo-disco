# Murugo Styling Quick Reference

## 🎨 Color Classes

### Primary Colors
```css
bg-primary-50    text-primary-50    border-primary-50
bg-primary-100   text-primary-100   border-primary-100
bg-primary-200   text-primary-200   border-primary-200
bg-primary-300   text-primary-300   border-primary-300
bg-primary-400   text-primary-400   border-primary-400
bg-primary-500   text-primary-500   border-primary-500
bg-primary-600   text-primary-600   border-primary-600
bg-primary-700   text-primary-700   border-primary-700
bg-primary-800   text-primary-800   border-primary-800
bg-primary-900   text-primary-900   border-primary-900
```

### Status Colors
```css
bg-success-500   text-success-500   border-success-500
bg-warning-500   text-warning-500   border-warning-500
bg-error-500     text-error-500     border-error-500
bg-info-500      text-info-500      border-info-500
```

## 🔘 Button Classes

### Base Button
```html
<button class="btn">Base Button</button>
```

### Button Variants
```html
<button class="btn btn-primary">Primary</button>
<button class="btn btn-secondary">Secondary</button>
<button class="btn btn-outline">Outline</button>
<button class="btn btn-ghost">Ghost</button>
<button class="btn btn-danger">Danger</button>
<button class="btn btn-success">Success</button>
<button class="btn btn-warning">Warning</button>
```

### Button Sizes
```html
<button class="btn btn-primary btn-sm">Small</button>
<button class="btn btn-primary btn-md">Medium</button>
<button class="btn btn-primary btn-lg">Large</button>
<button class="btn btn-primary btn-xl">Extra Large</button>
```

## 📦 Card Classes

### Base Card
```html
<div class="card">
    <div class="p-6">
        <h3 class="text-lg font-semibold mb-2">Card Title</h3>
        <p class="text-gray-600">Card content</p>
    </div>
</div>
```

### Card Variants
```html
<div class="card card-default">Default Card</div>
<div class="card card-elevated">Elevated Card</div>
<div class="card card-subtle">Subtle Card</div>
```

## 📝 Form Classes

### Input Fields
```html
<input type="text" class="form-input" placeholder="Enter text">
<input type="email" class="form-input" placeholder="Enter email">
<input type="password" class="form-input" placeholder="Enter password">
```

### Labels
```html
<label class="form-label">Field Label</label>
```

### Error States
```html
<input type="text" class="form-input error" placeholder="Error state">
<p class="text-red-600 text-sm mt-1">Error message</p>
```

## 🏷️ Badge Classes

### Badge Variants
```html
<span class="badge badge-primary">Primary</span>
<span class="badge badge-success">Success</span>
<span class="badge badge-warning">Warning</span>
<span class="badge badge-error">Error</span>
<span class="badge badge-gray">Gray</span>
```

## 📱 Responsive Classes

### Mobile-First Spacing
```html
<div class="mobile-padding">Mobile padding</div>
<div class="mobile-margin">Mobile margin</div>
```

### Touch-Friendly
```html
<button class="touch-target">Touch-friendly button</button>
```

## ✨ Animation Classes

### Hover Effects
```html
<div class="hover-lift">Lifts on hover</div>
<div class="hover-scale">Scales on hover</div>
```

### Animations
```html
<div class="animate-fade-in">Fades in</div>
<div class="animate-slide-up">Slides up</div>
<div class="animate-bounce-in">Bounces in</div>
```

## 🎯 Typography Classes

### Headings
```html
<h1 class="text-anthropic-heading">Main Heading</h1>
<h2 class="text-anthropic-subheading">Subheading</h2>
<p class="text-anthropic-body">Body text</p>
```

### Font Weights
```html
<p class="font-light">Light text</p>
<p class="font-normal">Normal text</p>
<p class="font-medium">Medium text</p>
<p class="font-semibold">Semibold text</p>
<p class="font-bold">Bold text</p>
```

## 📐 Spacing Classes

### Padding
```html
<div class="p-4">Padding all sides</div>
<div class="px-4">Padding horizontal</div>
<div class="py-4">Padding vertical</div>
<div class="pt-4">Padding top</div>
<div class="pr-4">Padding right</div>
<div class="pb-4">Padding bottom</div>
<div class="pl-4">Padding left</div>
```

### Margin
```html
<div class="m-4">Margin all sides</div>
<div class="mx-4">Margin horizontal</div>
<div class="my-4">Margin vertical</div>
<div class="mt-4">Margin top</div>
<div class="mr-4">Margin right</div>
<div class="mb-4">Margin bottom</div>
<div class="ml-4">Margin left</div>
```

## 🎨 Shadow Classes

### Shadow Variants
```html
<div class="shadow-sm">Small shadow</div>
<div class="shadow">Default shadow</div>
<div class="shadow-md">Medium shadow</div>
<div class="shadow-lg">Large shadow</div>
<div class="shadow-xl">Extra large shadow</div>
```

## 🔄 Border Radius Classes

### Radius Variants
```html
<div class="rounded-sm">Small radius</div>
<div class="rounded">Default radius</div>
<div class="rounded-md">Medium radius</div>
<div class="rounded-lg">Large radius</div>
<div class="rounded-xl">Extra large radius</div>
<div class="rounded-full">Full radius (circle)</div>
```

## 📱 Grid Classes

### Grid Layouts
```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <div>Item 1</div>
    <div>Item 2</div>
    <div>Item 3</div>
</div>
```

### Flexbox
```html
<div class="flex items-center justify-between">
    <div>Left content</div>
    <div>Right content</div>
</div>
```

## 🎯 Common Patterns

### Card with Button
```html
<div class="card">
    <div class="p-6">
        <h3 class="text-lg font-semibold mb-2">Card Title</h3>
        <p class="text-gray-600 mb-4">Card description</p>
        <button class="btn btn-primary btn-sm">Action</button>
    </div>
</div>
```

### Form Group
```html
<div class="space-y-4">
    <div>
        <label class="form-label">Email</label>
        <input type="email" class="form-input" placeholder="Enter email">
    </div>
    <div>
        <label class="form-label">Password</label>
        <input type="password" class="form-input" placeholder="Enter password">
    </div>
    <button type="submit" class="btn btn-primary btn-md">Submit</button>
</div>
```

### Status Row
```html
<div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
    <div class="flex items-center space-x-3">
        <span class="badge badge-success">Active</span>
        <span class="font-medium">Item Name</span>
    </div>
    <button class="btn btn-outline btn-sm">Edit</button>
</div>
```

### Hero Section
```html
<div class="bg-gradient-to-r from-primary-600 to-primary-800 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold mb-4">Welcome to Murugo</h1>
        <p class="text-xl mb-8">Find your perfect home in Rwanda</p>
        <button class="btn btn-secondary btn-lg">Get Started</button>
    </div>
</div>
```

## 🚀 Quick Tips

1. **Always use semantic HTML** - Use proper `<button>`, `<input>`, `<label>` elements
2. **Touch-friendly sizing** - Use `touch-target` class for interactive elements
3. **Consistent spacing** - Use the spacing scale (4, 8, 16, 24, 32px)
4. **Color consistency** - Stick to the defined color palette
5. **Responsive design** - Use mobile-first approach with responsive classes
6. **Accessibility** - Include proper labels and ARIA attributes
7. **Performance** - Use transform and opacity for animations

## 📚 Additional Resources

- [Full Design System Documentation](./DESIGN_SYSTEM.md)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Inter Font](https://fonts.google.com/specimen/Inter)
- [Webflow Design Principles](https://webflow.com/design)

---

*This quick reference covers the most commonly used styling classes. For complete documentation, see the full Design System guide.*
