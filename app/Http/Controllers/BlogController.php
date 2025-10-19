<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display the blog index page.
     */
    public function index(Request $request)
    {
        // Mock blog data - in a real application, this would come from a database
        $categories = [
            ['id' => 1, 'name' => 'Market Trends', 'slug' => 'market-trends'],
            ['id' => 2, 'name' => 'Property Tips', 'slug' => 'property-tips'],
            ['id' => 3, 'name' => 'Investment Guide', 'slug' => 'investment-guide'],
            ['id' => 4, 'name' => 'Neighborhood Spotlight', 'slug' => 'neighborhood-spotlight'],
            ['id' => 5, 'name' => 'Legal & Finance', 'slug' => 'legal-finance'],
        ];

        $featuredPosts = [
            [
                'id' => 1,
                'title' => 'Rwanda Real Estate Market Outlook 2024',
                'slug' => 'rwanda-real-estate-market-outlook-2024',
                'excerpt' => 'Discover the latest trends and opportunities in Rwanda\'s growing real estate market.',
                'content' => 'The Rwandan real estate market continues to show strong growth potential...',
                'image' => '/images/blog/market-outlook-2024.png',
                'category' => 'Market Trends',
                'category_slug' => 'market-trends',
                'author' => 'Jean Baptiste',
                'author_image' => '/images/team/ceo.png',
                'published_at' => '2024-01-15',
                'read_time' => '5 min read',
                'featured' => true
            ],
            [
                'id' => 2,
                'title' => 'First-Time Home Buyer\'s Complete Guide',
                'slug' => 'first-time-home-buyers-complete-guide',
                'excerpt' => 'Everything you need to know when buying your first home in Rwanda.',
                'content' => 'Buying your first home is an exciting milestone...',
                'image' => '/images/blog/first-time-buyer-guide.png',
                'category' => 'Property Tips',
                'category_slug' => 'property-tips',
                'author' => 'Grace Uwimana',
                'author_image' => '/images/team/marketing.png',
                'published_at' => '2024-01-10',
                'read_time' => '8 min read',
                'featured' => true
            ]
        ];

        $recentPosts = [
            [
                'id' => 3,
                'title' => 'Kigali\'s Best Neighborhoods for Young Professionals',
                'slug' => 'kigali-best-neighborhoods-young-professionals',
                'excerpt' => 'Explore the top areas in Kigali that offer the best lifestyle for young professionals.',
                'content' => 'Kigali offers diverse neighborhoods each with unique characteristics...',
                'image' => '/images/blog/kigali-neighborhoods.png',
                'category' => 'Neighborhood Spotlight',
                'category_slug' => 'neighborhood-spotlight',
                'author' => 'Paul Mugenzi',
                'author_image' => '/images/team/operations.png',
                'published_at' => '2024-01-08',
                'read_time' => '6 min read',
                'featured' => false
            ],
            [
                'id' => 4,
                'title' => 'Property Investment Strategies in Rwanda',
                'slug' => 'property-investment-strategies-rwanda',
                'excerpt' => 'Learn about profitable property investment opportunities in Rwanda.',
                'content' => 'Rwanda\'s stable economy and growing population make it attractive...',
                'image' => '/images/blog/investment-strategies.png',
                'category' => 'Investment Guide',
                'category_slug' => 'investment-guide',
                'author' => 'Marie Claire',
                'author_image' => '/images/team/cto.png',
                'published_at' => '2024-01-05',
                'read_time' => '7 min read',
                'featured' => false
            ],
            [
                'id' => 5,
                'title' => 'Understanding Property Taxes in Rwanda',
                'slug' => 'understanding-property-taxes-rwanda',
                'excerpt' => 'A comprehensive guide to property taxes and legal requirements.',
                'content' => 'Property ownership in Rwanda comes with certain tax obligations...',
                'image' => '/images/blog/property-taxes.png',
                'category' => 'Legal & Finance',
                'category_slug' => 'legal-finance',
                'author' => 'Sarah Mukamana',
                'author_image' => '/images/team/customer-success.png',
                'published_at' => '2024-01-03',
                'read_time' => '4 min read',
                'featured' => false
            ],
            [
                'id' => 6,
                'title' => 'Home Staging Tips for Quick Sales',
                'slug' => 'home-staging-tips-quick-sales',
                'excerpt' => 'Learn how to stage your home to attract buyers and sell faster.',
                'content' => 'Proper home staging can significantly impact your sale price...',
                'image' => '/images/blog/home-staging.png',
                'category' => 'Property Tips',
                'category_slug' => 'property-tips',
                'author' => 'David Nkurunziza',
                'author_image' => '/images/team/developer.png',
                'published_at' => '2024-01-01',
                'read_time' => '5 min read',
                'featured' => false
            ]
        ];

        $selectedCategory = $request->get('category');
        $searchQuery = $request->get('search');

        // Filter posts based on category and search
        $allPosts = array_merge($featuredPosts, $recentPosts);
        
        if ($selectedCategory) {
            $allPosts = array_filter($allPosts, function($post) use ($selectedCategory) {
                return $post['category_slug'] === $selectedCategory;
            });
        }

        if ($searchQuery) {
            $allPosts = array_filter($allPosts, function($post) use ($searchQuery) {
                return stripos($post['title'], $searchQuery) !== false || 
                       stripos($post['excerpt'], $searchQuery) !== false;
            });
        }

        return view('blog.index', compact('categories', 'featuredPosts', 'recentPosts', 'allPosts', 'selectedCategory', 'searchQuery'));
    }

    /**
     * Display a specific blog post.
     */
    public function show($slug)
    {
        // Mock blog post data - in a real application, this would come from a database
        $posts = [
            'rwanda-real-estate-market-outlook-2024' => [
                'id' => 1,
                'title' => 'Rwanda Real Estate Market Outlook 2024',
                'slug' => 'rwanda-real-estate-market-outlook-2024',
                'excerpt' => 'Discover the latest trends and opportunities in Rwanda\'s growing real estate market.',
                'content' => '<p>The Rwandan real estate market continues to show strong growth potential in 2024, driven by several key factors including urbanization, economic stability, and government initiatives.</p>

                <h2>Market Growth Drivers</h2>
                <p>Several factors are contributing to the positive outlook for Rwanda\'s real estate market:</p>
                <ul>
                    <li><strong>Urbanization:</strong> Rapid urbanization is driving demand for housing in major cities, particularly Kigali.</li>
                    <li><strong>Economic Stability:</strong> Rwanda\'s stable economic growth and political environment attract both local and international investors.</li>
                    <li><strong>Government Support:</strong> Various government initiatives support the real estate sector, including infrastructure development and housing programs.</li>
                    <li><strong>Population Growth:</strong> A growing middle class with increasing purchasing power is driving demand for quality housing.</li>
                </ul>

                <h2>Key Market Trends</h2>
                <p>Several trends are shaping the market in 2024:</p>
                <ul>
                    <li><strong>Affordable Housing:</strong> There\'s a significant focus on developing affordable housing solutions for the growing urban population.</li>
                    <li><strong>Sustainable Development:</strong> Green building practices and sustainable development are becoming increasingly important.</li>
                    <li><strong>Technology Integration:</strong> PropTech solutions are being adopted to improve the buying and selling process.</li>
                    <li><strong>Mixed-Use Developments:</strong> Integrated developments combining residential, commercial, and retail spaces are gaining popularity.</li>
                </ul>

                <h2>Investment Opportunities</h2>
                <p>For investors, several opportunities exist in the Rwandan real estate market:</p>
                <ul>
                    <li><strong>Residential Properties:</strong> High demand for quality residential properties in urban areas.</li>
                    <li><strong>Commercial Real Estate:</strong> Growing demand for office and retail spaces.</li>
                    <li><strong>Tourism Properties:</strong> Opportunities in hospitality and tourism-related real estate.</li>
                    <li><strong>Industrial Properties:</strong> Growing manufacturing sector driving demand for industrial spaces.</li>
                </ul>

                <h2>Challenges and Considerations</h2>
                <p>While the outlook is positive, investors should be aware of certain challenges:</p>
                <ul>
                    <li><strong>Land Availability:</strong> Limited land availability in prime locations can drive up costs.</li>
                    <li><strong>Infrastructure:</strong> While improving, infrastructure development needs to keep pace with growth.</li>
                    <li><strong>Regulatory Environment:</strong> Understanding local regulations and legal requirements is crucial.</li>
                    <li><strong>Financing:</strong> Access to financing can be a challenge for some investors.</li>
                </ul>

                <h2>Conclusion</h2>
                <p>The Rwandan real estate market presents significant opportunities for growth in 2024. With proper research, due diligence, and understanding of local market conditions, investors can find attractive opportunities in this emerging market.</p>',
                'image' => '/images/blog/market-outlook-2024.png',
                'category' => 'Market Trends',
                'category_slug' => 'market-trends',
                'author' => 'Jean Baptiste',
                'author_image' => '/images/team/ceo.png',
                'published_at' => '2024-01-15',
                'read_time' => '5 min read',
                'tags' => ['market trends', 'investment', 'rwanda', 'real estate']
            ],
            // Add more posts as needed
        ];

        $post = $posts[$slug] ?? null;

        if (!$post) {
            abort(404, 'Blog post not found');
        }

        // Get related posts
        $relatedPosts = [
            [
                'id' => 2,
                'title' => 'First-Time Home Buyer\'s Complete Guide',
                'slug' => 'first-time-home-buyers-complete-guide',
                'excerpt' => 'Everything you need to know when buying your first home in Rwanda.',
                'image' => '/images/blog/first-time-buyer-guide.png',
                'category' => 'Property Tips',
                'published_at' => '2024-01-10',
                'read_time' => '8 min read'
            ],
            [
                'id' => 4,
                'title' => 'Property Investment Strategies in Rwanda',
                'slug' => 'property-investment-strategies-rwanda',
                'excerpt' => 'Learn about profitable property investment opportunities in Rwanda.',
                'image' => '/images/blog/investment-strategies.png',
                'category' => 'Investment Guide',
                'published_at' => '2024-01-05',
                'read_time' => '7 min read'
            ]
        ];

        return view('blog.show', compact('post', 'relatedPosts'));
    }

    /**
     * Display posts by category.
     */
    public function category($slug)
    {
        // Mock category data
        $categories = [
            'market-trends' => [
                'name' => 'Market Trends',
                'description' => 'Stay updated with the latest trends and insights in Rwanda\'s real estate market.',
                'color' => 'blue'
            ],
            'property-tips' => [
                'name' => 'Property Tips',
                'description' => 'Expert advice and tips for buying, selling, and managing properties.',
                'color' => 'green'
            ],
            'investment-guide' => [
                'name' => 'Investment Guide',
                'description' => 'Comprehensive guides for real estate investment opportunities.',
                'color' => 'purple'
            ],
            'neighborhood-spotlight' => [
                'name' => 'Neighborhood Spotlight',
                'description' => 'Explore different neighborhoods and areas in Rwanda.',
                'color' => 'orange'
            ],
            'legal-finance' => [
                'name' => 'Legal & Finance',
                'description' => 'Legal and financial aspects of real estate transactions.',
                'color' => 'red'
            ]
        ];

        $category = $categories[$slug] ?? null;

        if (!$category) {
            abort(404, 'Category not found');
        }

        // Mock posts for this category
        $posts = [
            'market-trends' => [
                [
                    'id' => 1,
                    'title' => 'Rwanda Real Estate Market Outlook 2024',
                    'slug' => 'rwanda-real-estate-market-outlook-2024',
                    'excerpt' => 'Discover the latest trends and opportunities in Rwanda\'s growing real estate market.',
                    'image' => '/images/blog/market-outlook-2024.png',
                    'author' => 'Jean Baptiste',
                    'author_image' => '/images/team/ceo.png',
                    'published_at' => '2024-01-15',
                    'read_time' => '5 min read'
                ]
            ],
            'property-tips' => [
                [
                    'id' => 2,
                    'title' => 'First-Time Home Buyer\'s Complete Guide',
                    'slug' => 'first-time-home-buyers-complete-guide',
                    'excerpt' => 'Everything you need to know when buying your first home in Rwanda.',
                    'image' => '/images/blog/first-time-buyer-guide.png',
                    'author' => 'Grace Uwimana',
                    'author_image' => '/images/team/marketing.png',
                    'published_at' => '2024-01-10',
                    'read_time' => '8 min read'
                ],
                [
                    'id' => 6,
                    'title' => 'Home Staging Tips for Quick Sales',
                    'slug' => 'home-staging-tips-quick-sales',
                    'excerpt' => 'Learn how to stage your home to attract buyers and sell faster.',
                    'image' => '/images/blog/home-staging.png',
                    'author' => 'David Nkurunziza',
                    'author_image' => '/images/team/developer.png',
                    'published_at' => '2024-01-01',
                    'read_time' => '5 min read'
                ]
            ]
        ];

        $categoryPosts = $posts[$slug] ?? [];

        return view('blog.category', compact('category', 'categoryPosts', 'slug'));
    }
}
