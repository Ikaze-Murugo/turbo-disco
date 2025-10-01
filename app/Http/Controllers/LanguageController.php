<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     *
     * @param Request $request
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request, $locale)
    {
        // Define supported locales
        $supportedLocales = ['en', 'fr', 'rw'];
        
        // Validate the locale
        if (!in_array($locale, $supportedLocales)) {
            $locale = 'en'; // Default to English if invalid locale
        }
        
        // Set the application locale
        App::setLocale($locale);
        
        // Store the locale in session for persistence
        Session::put('locale', $locale);
        
        // Redirect back to the previous page
        return redirect()->back()->with('success', 'Language changed successfully');
    }
    
    /**
     * Get the current locale
     *
     * @return string
     */
    public function current()
    {
        return App::getLocale();
    }
    
    /**
     * Get all supported locales
     *
     * @return array
     */
    public function supported()
    {
        return [
            'en' => [
                'code' => 'en',
                'name' => 'English',
                'native' => 'English',
                'flag' => '🇺🇸'
            ],
            'fr' => [
                'code' => 'fr',
                'name' => 'French',
                'native' => 'Français',
                'flag' => '🇫🇷'
            ],
            'rw' => [
                'code' => 'rw',
                'name' => 'Kinyarwanda',
                'native' => 'Kinyarwanda',
                'flag' => '🇷🇼'
            ]
        ];
    }
}
