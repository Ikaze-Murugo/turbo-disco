# Internationalization Guide for Murugo Real Estate Platform

## Overview

The Murugo Real Estate Platform supports multiple languages to serve users across Rwanda and beyond. This guide explains how the internationalization (i18n) system works and how to add or modify translations.

## Supported Languages

Currently, the platform supports three languages:

1. **English (en)** - Default language
2. **French (fr)** - Fully translated
3. **Kinyarwanda (rw)** - Basic translation structure provided

## Architecture

### Language Files Structure

```
resources/lang/
├── en/
│   └── messages.php
├── fr/
│   └── messages.php
└── rw/
│   └── messages.php
```

### Key Components

1. **LanguageController** (`app/Http/Controllers/LanguageController.php`)
   - Handles language switching
   - Provides locale information
   - Manages supported languages

2. **SetLocale Middleware** (`app/Http/Middleware/SetLocale.php`)
   - Sets the application locale from session
   - Validates locale codes
   - Ensures fallback to English

3. **Language Switcher Component** (`resources/views/components/language-switcher.blade.php`)
   - UI component for language selection
   - Dropdown with current language indicator
   - Accessible design with proper ARIA labels

## Implementation Details

### Language Detection and Storage

The system uses Laravel's session to store the user's language preference:

1. User selects a language via the language switcher
2. `LanguageController@switch` validates and stores the locale in session
3. `SetLocale` middleware applies the locale on each request
4. Laravel's `App::setLocale()` sets the active language

### Translation Usage in Views

Use Laravel's translation helpers in Blade templates:

```blade
{{-- Simple translation --}}
{{ __('messages.nav.home') }}

{{-- Translation with parameters --}}
{{ __('messages.properties.bedrooms', ['count' => 3]) }}

{{-- Conditional translation --}}
@lang('messages.auth.login')
```

### Translation Usage in Controllers

```php
// In controllers
$message = __('messages.common.success');

// With parameters
$message = __('messages.properties.found', ['count' => $properties->count()]);
```

## Adding New Languages

### Step 1: Create Language Directory

```bash
mkdir resources/lang/[locale_code]
```

### Step 2: Copy Base Translation File

```bash
cp resources/lang/en/messages.php resources/lang/[locale_code]/messages.php
```

### Step 3: Translate Content

Edit the new language file and translate all strings while maintaining the array structure.

### Step 4: Update Language Controller

Add the new locale to the `supported()` method in `LanguageController`:

```php
'[locale_code]' => [
    'code' => '[locale_code]',
    'name' => '[Language Name in English]',
    'native' => '[Language Name in Native Script]',
    'flag' => '[Flag Emoji]'
]
```

### Step 5: Update Language Switcher

Add the new language option to the language switcher component.

### Step 6: Update Middleware

Add the new locale code to the `$supportedLocales` array in `SetLocale` middleware.

## Translation Guidelines

### 1. Maintain Array Structure

Always preserve the nested array structure when translating:

```php
// ✅ Correct
'nav' => [
    'home' => 'Accueil',
    'properties' => 'Propriétés',
],

// ❌ Incorrect
'nav.home' => 'Accueil',
'nav.properties' => 'Propriétés',
```

### 2. Handle Pluralization

Laravel supports pluralization for different languages:

```php
'properties_count' => '{0} No properties|{1} One property|[2,*] :count properties',
```

### 3. Use Parameters for Dynamic Content

```php
'welcome_message' => 'Welcome, :name!',
'search_results' => 'Found :count properties in :location',
```

### 4. Context-Aware Translations

Group related translations logically:

```php
'auth' => [
    'login' => 'Sign in',
    'register' => 'Sign up',
    'logout' => 'Sign out',
],
'properties' => [
    'title' => 'Properties',
    'search' => 'Search properties',
],
```

## Kinyarwanda Translation Notes

### Current Status

The Kinyarwanda translation file (`resources/lang/rw/messages.php`) contains a basic structure with common terms translated. However, it requires:

1. **Professional Review**: Native speakers should review and refine translations
2. **Cultural Adaptation**: Ensure terms are culturally appropriate for Rwanda
3. **Technical Terms**: Some real estate terms may need localization or explanation
4. **Completeness**: All strings need proper translation

### Recommended Approach for Kinyarwanda

1. **Collaborate with Native Speakers**: Work with Kinyarwanda speakers familiar with real estate terminology
2. **Use Standard Terminology**: Follow official Rwandan government terminology where applicable
3. **Consider Mixed Language**: Some technical terms might be better left in English or French if commonly used
4. **Test with Users**: Conduct user testing with Kinyarwanda speakers

### Example Improvements Needed

```php
// Current (basic)
'properties' => 'Imitungo',

// Better (more specific)
'properties' => 'Amazu no Imitungo', // Houses and Properties

// Current (literal)
'search_properties' => 'Shakisha Imitungo',

// Better (natural)
'search_properties' => 'Shakisha Inzu', // Search for Houses
```

## Best Practices

### 1. Consistent Terminology

Maintain consistent terminology across the application:
- Property = Propriété (FR) / Umutungo (RW)
- Landlord = Propriétaire (FR) / Nyir'umutungo (RW)
- Rent = Loyer (FR) / Ubukode (RW)

### 2. Cultural Sensitivity

Consider cultural context when translating:
- Address formats may differ
- Currency display (RWF positioning)
- Date formats (DD/MM/YYYY vs MM/DD/YYYY)

### 3. SEO Considerations

For public-facing content:
- Translate meta descriptions
- Consider URL localization
- Translate alt text for images

### 4. Testing

- Test all languages thoroughly
- Verify text doesn't break layouts
- Check for missing translations (fallback to English)
- Test with different text lengths

## Technical Implementation

### Route Registration

Add language switching route to `web.php`:

```php
Route::get('/language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch')
    ->where('locale', 'en|fr|rw');
```

### Middleware Registration

Register the middleware in `app/Http/Kernel.php`:

```php
protected $middlewareGroups = [
    'web' => [
        // ... other middleware
        \App\Http\Middleware\SetLocale::class,
    ],
];
```

### Configuration

Update `config/app.php` for locale settings:

```php
'locale' => 'en',
'fallback_locale' => 'en',
'available_locales' => ['en', 'fr', 'rw'],
```

## Future Enhancements

### 1. Database-Driven Translations

For dynamic content (property descriptions, user-generated content):

```php
// Migration example
Schema::create('property_translations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('property_id');
    $table->string('locale', 2);
    $table->string('title');
    $table->text('description');
    $table->timestamps();
});
```

### 2. Translation Management Interface

Consider implementing an admin interface for managing translations:
- Add/edit translations without code changes
- Track translation completeness
- Version control for translations

### 3. Automatic Translation

For initial translations, consider:
- Google Translate API for draft translations
- Professional translation services
- Community-driven translation platforms

### 4. Right-to-Left (RTL) Support

If adding Arabic or other RTL languages:
- CSS direction support
- Layout adjustments
- Icon and image mirroring

## Deployment Considerations

### 1. Cache Management

Clear translation cache after updates:

```bash
php artisan config:clear
php artisan cache:clear
```

### 2. Performance

- Consider caching translated content
- Minimize translation file size
- Use lazy loading for large translation sets

### 3. Monitoring

- Monitor for missing translations
- Track language usage analytics
- Set up alerts for translation errors

## Conclusion

The internationalization system provides a solid foundation for multi-language support. The current implementation supports English and French fully, with a basic Kinyarwanda structure ready for professional translation. 

For production deployment, prioritize:
1. Professional Kinyarwanda translation review
2. User testing with native speakers
3. Performance optimization
4. Comprehensive testing across all languages

This system can easily be extended to support additional languages as the platform grows across East Africa and beyond.
