@extends('layouts.app')

@section('title', 'Terms of Service - Murugo')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Terms of Service</h1>
            <p class="text-lg text-gray-600">Last updated: {{ date('F d, Y') }}</p>
        </div>

        <!-- Content -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-8 sm:px-8">
                <div class="prose prose-lg max-w-none">
                    
                    <!-- Introduction -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. Introduction</h2>
                        <p class="text-gray-700 leading-relaxed">
                            Welcome to Murugo, a real estate platform connecting property owners, renters, and real estate professionals. 
                            These Terms of Service ("Terms") govern your use of our website and services. By accessing or using Murugo, 
                            you agree to be bound by these Terms.
                        </p>
                    </section>

                    <!-- Acceptance of Terms -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. Acceptance of Terms</h2>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            By creating an account, browsing properties, or using any of our services, you acknowledge that you have read, 
                            understood, and agree to be bound by these Terms and our Privacy Policy.
                        </p>
                        <p class="text-gray-700 leading-relaxed">
                            If you do not agree to these Terms, please do not use our services.
                        </p>
                    </section>

                    <!-- User Accounts -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. User Accounts</h2>
                        <div class="space-y-4">
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Account Creation:</strong> You must provide accurate and complete information when creating an account. 
                                You are responsible for maintaining the confidentiality of your account credentials.
                            </p>
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Account Types:</strong> We offer different account types including Renter, Landlord, and Admin accounts 
                                with varying permissions and features.
                            </p>
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Account Security:</strong> You are responsible for all activities that occur under your account. 
                                Notify us immediately of any unauthorized use.
                            </p>
                        </div>
                    </section>

                    <!-- Property Listings -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Property Listings</h2>
                        <div class="space-y-4">
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Accurate Information:</strong> Property owners must provide accurate and up-to-date information 
                                about their properties, including pricing, availability, and features.
                            </p>
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Property Verification:</strong> We reserve the right to verify property information and may 
                                request additional documentation.
                            </p>
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Listing Fees:</strong> Some listing features may require payment. All fees are clearly 
                                disclosed before purchase.
                            </p>
                        </div>
                    </section>

                    <!-- User Conduct -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. User Conduct</h2>
                        <div class="space-y-4">
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Prohibited Activities:</strong> You agree not to:
                            </p>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li>Post false, misleading, or fraudulent information</li>
                                <li>Harass, abuse, or harm other users</li>
                                <li>Violate any applicable laws or regulations</li>
                                <li>Attempt to gain unauthorized access to our systems</li>
                                <li>Use automated tools to scrape or collect data</li>
                                <li>Interfere with the proper functioning of our services</li>
                            </ul>
                        </div>
                    </section>

                    <!-- Privacy and Data -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Privacy and Data Protection</h2>
                        <p class="text-gray-700 leading-relaxed">
                            Your privacy is important to us. Please review our Privacy Policy to understand how we collect, 
                            use, and protect your personal information. We comply with applicable data protection laws 
                            including GDPR and local privacy regulations.
                        </p>
                    </section>

                    <!-- Payment Terms -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. Payment Terms</h2>
                        <div class="space-y-4">
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Payment Processing:</strong> All payments are processed securely through our payment partners. 
                                We accept major credit cards and other approved payment methods.
                            </p>
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Refunds:</strong> Refund policies vary by service. Please review specific refund terms 
                                before making a purchase.
                            </p>
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Taxes:</strong> You are responsible for any applicable taxes on transactions.
                            </p>
                        </div>
                    </section>

                    <!-- Intellectual Property -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">8. Intellectual Property</h2>
                        <div class="space-y-4">
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Our Content:</strong> All content on Murugo, including text, graphics, logos, and software, 
                                is owned by us or our licensors and protected by copyright and trademark laws.
                            </p>
                            <p class="text-gray-700 leading-relaxed">
                                <strong>User Content:</strong> You retain ownership of content you post, but grant us a license 
                                to use, display, and distribute it in connection with our services.
                            </p>
                        </div>
                    </section>

                    <!-- Limitation of Liability -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">9. Limitation of Liability</h2>
                        <p class="text-gray-700 leading-relaxed">
                            To the maximum extent permitted by law, Murugo shall not be liable for any indirect, incidental, 
                            special, or consequential damages arising from your use of our services. Our total liability 
                            shall not exceed the amount you paid us in the 12 months preceding the claim.
                        </p>
                    </section>

                    <!-- Termination -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">10. Termination</h2>
                        <div class="space-y-4">
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Account Termination:</strong> You may terminate your account at any time. We may suspend 
                                or terminate accounts that violate these Terms.
                            </p>
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Effect of Termination:</strong> Upon termination, your right to use our services 
                                ceases immediately, but certain provisions of these Terms survive termination.
                            </p>
                        </div>
                    </section>

                    <!-- Changes to Terms -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">11. Changes to Terms</h2>
                        <p class="text-gray-700 leading-relaxed">
                            We may update these Terms from time to time. We will notify users of significant changes via email 
                            or through our platform. Continued use of our services after changes constitutes acceptance of 
                            the new Terms.
                        </p>
                    </section>

                    <!-- Contact Information -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">12. Contact Information</h2>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <p class="text-gray-700 leading-relaxed mb-4">
                                If you have questions about these Terms of Service, please contact us:
                            </p>
                            <div class="space-y-2">
                                <p class="text-gray-700">
                                    <strong>Email:</strong> legal@murugo.com
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
