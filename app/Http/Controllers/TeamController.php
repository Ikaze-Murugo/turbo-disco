<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display the team page.
     */
    public function index()
    {
        // Mock team data - in a real application, this would come from a database
        $teamMembers = [
            [
                'id' => 1,
                'name' => 'Jean Baptiste',
                'position' => 'Founder & CEO',
                'bio' => 'Passionate about revolutionizing real estate in Rwanda. With over 10 years of experience in property development and technology.',
                'image' => '/images/team/ceo.png',
                'linkedin' => 'https://linkedin.com/in/jean-baptiste',
                'twitter' => 'https://twitter.com/jean_baptiste',
                'email' => 'jean@murugo.com'
            ],
            [
                'id' => 2,
                'name' => 'Marie Claire',
                'position' => 'CTO',
                'bio' => 'Technology leader with expertise in building scalable platforms. Former software engineer at leading tech companies.',
                'image' => '/images/team/cto.png',
                'linkedin' => 'https://linkedin.com/in/marie-claire',
                'twitter' => 'https://twitter.com/marie_claire',
                'email' => 'marie@murugo.com'
            ],
            [
                'id' => 3,
                'name' => 'Paul Mugenzi',
                'position' => 'Head of Operations',
                'bio' => 'Operations expert with deep knowledge of the Rwandan real estate market. Ensures smooth operations and customer satisfaction.',
                'image' => '/images/team/operations.png',
                'linkedin' => 'https://linkedin.com/in/paul-mugenzi',
                'twitter' => 'https://twitter.com/paul_mugenzi',
                'email' => 'paul@murugo.com'
            ],
            [
                'id' => 4,
                'name' => 'Grace Uwimana',
                'position' => 'Head of Marketing',
                'bio' => 'Marketing strategist with a passion for connecting people with their dream homes. Expert in digital marketing and brand building.',
                'image' => '/images/team/marketing.png',
                'linkedin' => 'https://linkedin.com/in/grace-uwimana',
                'twitter' => 'https://twitter.com/grace_uwimana',
                'email' => 'grace@murugo.com'
            ],
            [
                'id' => 5,
                'name' => 'David Nkurunziza',
                'position' => 'Lead Developer',
                'bio' => 'Full-stack developer with expertise in modern web technologies. Passionate about creating user-friendly applications.',
                'image' => '/images/team/developer.png',
                'linkedin' => 'https://linkedin.com/in/david-nkurunziza',
                'twitter' => 'https://twitter.com/david_nkurunziza',
                'email' => 'david@murugo.com'
            ],
            [
                'id' => 6,
                'name' => 'Sarah Mukamana',
                'position' => 'Customer Success Manager',
                'bio' => 'Dedicated to ensuring every user has an exceptional experience. Expert in customer relations and problem-solving.',
                'image' => '/images/team/customer-success.png',
                'linkedin' => 'https://linkedin.com/in/sarah-mukamana',
                'twitter' => 'https://twitter.com/sarah_mukamana',
                'email' => 'sarah@murugo.com'
            ]
        ];

        $companyStats = [
            'properties_listed' => 2500,
            'happy_customers' => 15000,
            'cities_covered' => 8,
            'years_experience' => 5
        ];

        return view('team.index', compact('teamMembers', 'companyStats'));
    }

    /**
     * Display a specific team member.
     */
    public function show($id)
    {
        // Mock team member data - in a real application, this would come from a database
        $teamMembers = [
            1 => [
                'id' => 1,
                'name' => 'Jean Baptiste',
                'position' => 'Founder & CEO',
                'bio' => 'Passionate about revolutionizing real estate in Rwanda. With over 10 years of experience in property development and technology.',
                'full_bio' => 'Jean Baptiste founded Murugo with a vision to transform the real estate landscape in Rwanda. With over 10 years of experience in property development and technology, he has been instrumental in building partnerships with major property developers and creating innovative solutions for property seekers. Jean holds a Master\'s degree in Business Administration and has previously worked with leading real estate companies in East Africa.',
                'image' => '/images/team/ceo.png',
                'linkedin' => 'https://linkedin.com/in/jean-baptiste',
                'twitter' => 'https://twitter.com/jean_baptiste',
                'email' => 'jean@murugo.com',
                'achievements' => [
                    'Led the company from startup to market leader',
                    'Established partnerships with 50+ property developers',
                    'Featured in Forbes Africa 30 Under 30',
                    'Speaker at international real estate conferences'
                ]
            ],
            // Add more team members as needed
        ];

        $member = $teamMembers[$id] ?? null;

        if (!$member) {
            abort(404, 'Team member not found');
        }

        return view('team.show', compact('member'));
    }
}
