@extends('layouts.app')

@section('title', 'Privacy Policy - Murugo')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Privacy Policy</h1>
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
                            At Murugo, we are committed to protecting your privacy and personal information. This Privacy Policy 
                            explains how we collect, use, disclose, and safeguard your information when you use our real estate 
                            platform and services.
                        </p>
                    </section>

                    <!-- Information We Collect -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. Information We Collect</h2>
                        
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">2.1 Personal Information</h3>
                        <div class="space-y-4 mb-6">
                            <p class="text-gray-700 leading-relaxed">
                                We collect information you provide directly to us, including:
                            </p>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li>Name, email address, and phone number</li>
                                <li>Account credentials and profile information</li>
                                <li>Property information and listings</li>
                                <li>Payment information (processed securely by third parties)</li>
                                <li>Communications and messages with other users</li>
                                <li>Reviews, ratings, and feedback</li>
                            </ul>
                        </div>

                        <h3 class="text-xl font-semibold text-gray-800 mb-3">2.2 Usage Information</h3>
                        <div class="space-y-4 mb-6">
                            <p class="text-gray-700 leading-relaxed">
                                We automatically collect certain information about your use of our services:
                            </p>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li>Device information and IP address</li>
                                <li>Browser type and version</li>
                                <li>Pages visited and time spent on our platform</li>
                                <li>Search queries and property views</li>
                                <li>Location data (with your permission)</li>
                            </ul>
                        </div>

                        <h3 class="text-xl font-semibold text-gray-800 mb-3">2.3 Cookies and Tracking</h3>
                        <p class="text-gray-700 leading-relaxed">
                            We use cookies and similar technologies to enhance your experience, analyze usage patterns, 
                            and provide personalized content. You can control cookie settings through your browser preferences.
                        </p>
                    </section>

                    <!-- How We Use Information -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. How We Use Your Information</h2>
                        <div class="space-y-4">
                            <p class="text-gray-700 leading-relaxed">
                                We use your information to:
                            </p>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li>Provide and maintain our services</li>
                                <li>Process transactions and payments</li>
                                <li>Communicate with you about your account and our services</li>
                                <li>Send you relevant property recommendations and updates</li>
                                <li>Improve our platform and develop new features</li>
                                <li>Ensure platform security and prevent fraud</li>
                                <li>Comply with legal obligations</li>
                                <li>Analyze usage patterns and trends</li>
                            </ul>
                        </div>
                    </section>

                    <!-- Information Sharing -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Information Sharing and Disclosure</h2>
                        
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">4.1 With Other Users</h3>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            We may share certain information with other users as part of our services:
                        </p>
                        <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4 mb-6">
                            <li>Property listings are visible to all users</li>
                            <li>Contact information may be shared between interested parties</li>
                            <li>Reviews and ratings are publicly visible</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-gray-800 mb-3">4.2 With Service Providers</h3>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            We may share information with trusted third-party service providers who assist us in:
                        </p>
                        <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4 mb-6">
                            <li>Payment processing</li>
                            <li>Email communications</li>
                            <li>Data analytics</li>
                            <li>Customer support</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-gray-800 mb-3">4.3 Legal Requirements</h3>
                        <p class="text-gray-700 leading-relaxed">
                            We may disclose information when required by law, to protect our rights, or to ensure user safety.
                        </p>
                    </section>

                    <!-- Data Security -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Data Security</h2>
                        <div class="space-y-4">
                            <p class="text-gray-700 leading-relaxed">
                                We implement appropriate technical and organizational measures to protect your personal information:
                            </p>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li>Encryption of data in transit and at rest</li>
                                <li>Regular security assessments and updates</li>
                                <li>Access controls and authentication</li>
                                <li>Secure data centers and infrastructure</li>
                                <li>Employee training on data protection</li>
                            </ul>
                        </div>
                    </section>

                    <!-- Your Rights -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Your Rights and Choices</h2>
                        <div class="space-y-4">
                            <p class="text-gray-700 leading-relaxed">
                                You have the following rights regarding your personal information:
                            </p>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li><strong>Access:</strong> Request a copy of your personal information</li>
                                <li><strong>Correction:</strong> Update or correct inaccurate information</li>
                                <li><strong>Deletion:</strong> Request deletion of your personal information</li>
                                <li><strong>Portability:</strong> Receive your data in a structured format</li>
                                <li><strong>Objection:</strong> Object to certain processing activities</li>
                                <li><strong>Withdrawal:</strong> Withdraw consent where applicable</li>
                            </ul>
                        </div>
                    </section>

                    <!-- Data Retention -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. Data Retention</h2>
                        <p class="text-gray-700 leading-relaxed">
                            We retain your personal information for as long as necessary to provide our services, comply with 
                            legal obligations, resolve disputes, and enforce our agreements. When information is no longer 
                            needed, we securely delete or anonymize it.
                        </p>
                    </section>

                    <!-- International Transfers -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">8. International Data Transfers</h2>
                        <p class="text-gray-700 leading-relaxed">
                            Your information may be transferred to and processed in countries other than your country of residence. 
                            We ensure appropriate safeguards are in place to protect your information in accordance with 
                            applicable data protection laws.
                        </p>
                    </section>

                    <!-- Children's Privacy -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">9. Children's Privacy</h2>
                        <p class="text-gray-700 leading-relaxed">
                            Our services are not intended for children under 16 years of age. We do not knowingly collect 
                            personal information from children under 16. If we become aware that we have collected such 
                            information, we will take steps to delete it promptly.
                        </p>
                    </section>

                    <!-- Changes to Privacy Policy -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">10. Changes to This Privacy Policy</h2>
                        <p class="text-gray-700 leading-relaxed">
                            We may update this Privacy Policy from time to time. We will notify you of any material changes 
                            by posting the new Privacy Policy on our website and updating the "Last updated" date. 
                            We encourage you to review this Privacy Policy periodically.
                        </p>
                    </section>

                    <!-- Contact Information -->
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">11. Contact Us</h2>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <p class="text-gray-700 leading-relaxed mb-4">
                                If you have questions about this Privacy Policy or our data practices, please contact us:
                            </p>
                            <div class="space-y-2">
                                <p class="text-gray-700">
                                    <strong>Data Protection Officer:</strong> privacy@murugo.com
                                </p>
                                <p class="text-gray-700">
                                    <strong>General Inquiries:</strong> support@murugo.com
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
