@extends('layouts.app')

@section('title', 'Cookie Policy - Murugo')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Cookie Policy</h1>
            <p class="text-lg text-gray-600">Last updated: {{ date('F d, Y') }}</p>
        </div>

        <!-- Content -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-8 sm:px-8">
                <div class="prose prose-lg max-w-none">
                    
                    <!-- Introduction -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. What Are Cookies?</h2>
                        <p class="text-gray-700 leading-relaxed">
                            Cookies are small text files that are stored on your device when you visit our website. They help us 
                            provide you with a better experience by remembering your preferences and understanding how you use 
                            our platform.
                        </p>
                    </section>

                    <!-- Types of Cookies -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. Types of Cookies We Use</h2>
                        
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">2.1 Essential Cookies</h3>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Purpose:</strong> These cookies are necessary for the website to function properly. 
                                They enable basic functions like page navigation, access to secure areas, and user authentication.
                            </p>
                            <p class="text-gray-700 leading-relaxed mt-2">
                                <strong>Examples:</strong> Session cookies, security cookies, load balancing cookies
                            </p>
                            <p class="text-gray-700 leading-relaxed mt-2">
                                <strong>Retention:</strong> Session-based or up to 24 hours
                            </p>
                        </div>

                        <h3 class="text-xl font-semibold text-gray-800 mb-3">2.2 Functional Cookies</h3>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Purpose:</strong> These cookies enhance your experience by remembering your preferences 
                                and settings, such as language, region, and display preferences.
                            </p>
                            <p class="text-gray-700 leading-relaxed mt-2">
                                <strong>Examples:</strong> Language preferences, theme settings, user interface preferences
                            </p>
                            <p class="text-gray-700 leading-relaxed mt-2">
                                <strong>Retention:</strong> Up to 1 year
                            </p>
                        </div>

                        <h3 class="text-xl font-semibold text-gray-800 mb-3">2.3 Analytics Cookies</h3>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Purpose:</strong> These cookies help us understand how visitors interact with our website 
                                by collecting and reporting information anonymously.
                            </p>
                            <p class="text-gray-700 leading-relaxed mt-2">
                                <strong>Examples:</strong> Google Analytics, page views, user behavior tracking
                            </p>
                            <p class="text-gray-700 leading-relaxed mt-2">
                                <strong>Retention:</strong> Up to 2 years
                            </p>
                        </div>

                        <h3 class="text-xl font-semibold text-gray-800 mb-3">2.4 Marketing Cookies</h3>
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Purpose:</strong> These cookies are used to deliver relevant advertisements and track 
                                the effectiveness of our marketing campaigns.
                            </p>
                            <p class="text-gray-700 leading-relaxed mt-2">
                                <strong>Examples:</strong> Ad targeting, conversion tracking, remarketing
                            </p>
                            <p class="text-gray-700 leading-relaxed mt-2">
                                <strong>Retention:</strong> Up to 1 year
                            </p>
                        </div>
                    </section>

                    <!-- Third-Party Cookies -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. Third-Party Cookies</h2>
                        <div class="space-y-4">
                            <p class="text-gray-700 leading-relaxed">
                                We may use third-party services that set their own cookies:
                            </p>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li><strong>Google Analytics:</strong> For website analytics and performance monitoring</li>
                                <li><strong>Payment Processors:</strong> For secure payment processing</li>
                                <li><strong>Social Media:</strong> For social sharing and login features</li>
                                <li><strong>Maps Services:</strong> For location and mapping features</li>
                            </ul>
                        </div>
                    </section>

                    <!-- Cookie Management -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Managing Your Cookie Preferences</h2>
                        
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">4.1 Browser Settings</h3>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            You can control cookies through your browser settings. Most browsers allow you to:
                        </p>
                        <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4 mb-6">
                            <li>View and delete existing cookies</li>
                            <li>Block cookies from specific websites</li>
                            <li>Block all cookies</li>
                            <li>Set preferences for different types of cookies</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-gray-800 mb-3">4.2 Our Cookie Consent</h3>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            When you first visit our website, you'll see a cookie consent banner where you can:
                        </p>
                        <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4 mb-6">
                            <li>Accept all cookies</li>
                            <li>Reject non-essential cookies</li>
                            <li>Customize your cookie preferences</li>
                            <li>Learn more about our cookie usage</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-gray-800 mb-3">4.3 Cookie Settings Update</h3>
                        <p class="text-gray-700 leading-relaxed">
                            You can update your cookie preferences at any time by clicking the "Cookie Settings" link 
                            in our website footer or by clearing your browser cookies and revisiting our site.
                        </p>
                    </section>

                    <!-- Impact of Disabling Cookies -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Impact of Disabling Cookies</h2>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Important:</strong> Disabling certain cookies may affect the functionality of our website. 
                                Essential cookies are required for basic website functions, and disabling them may prevent 
                                you from accessing certain features or areas of our platform.
                            </p>
                        </div>
                    </section>

                    <!-- Updates to Cookie Policy -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Updates to This Cookie Policy</h2>
                        <p class="text-gray-700 leading-relaxed">
                            We may update this Cookie Policy from time to time to reflect changes in our practices or 
                            applicable laws. We will notify you of any material changes by posting the updated policy 
                            on our website and updating the "Last updated" date.
                        </p>
                    </section>

                    <!-- Contact Information -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. Contact Us</h2>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <p class="text-gray-700 leading-relaxed mb-4">
                                If you have questions about our use of cookies or this Cookie Policy, please contact us:
                            </p>
                            <div class="space-y-2">
                                <p class="text-gray-700">
                                    <strong>Email:</strong> privacy@murugo.com
                                </p>
                                <p class="text-gray-700">
                                    <strong>Address:</strong> Kigali, Rwanda
                                </p>
                                <p class="text-gray-700">
                                    <strong>Phone:</strong> +250 XXX XXX XXX
                                </p>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-8">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Back to Home
            </a>
        </div>
    </div>
</div>
@endsection
