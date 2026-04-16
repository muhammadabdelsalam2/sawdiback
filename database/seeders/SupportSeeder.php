<?php

namespace Database\Seeders;

use App\Models\SupportItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        SupportItem::insert([

            // =========================
            // HELP CENTER (Route)
            // =========================
            [
                'title' => 'Help Center',
                'subtitle' => 'FAQs & Support articles',
                'icon' => 'headphones',

                'value' => 'help_center_screen',
                'module' => 'HELP_CENTER',

                'meta' => json_encode([
                    'module' => 'HELP_CENTER',
                ]),

                'screen_config' => json_encode([
                    'screen' => 'help_center',

                    'header' => [
                        'title' => 'Help Center',
                        'subtitle' => 'How can we help you?',
                        'icon' => 'headphones',
                    ],

                    'search' => [
                        'enabled' => true,
                        'placeholder' => 'Search help articles...',
                    ],

                    'sections' => [
                        [
                            'type' => 'grid',
                            'items' => [
                                [
                                    'title' => 'FAQs',
                                    'icon' => 'help-circle',
                                    'action' => 'navigate',
                                    'value' => 'faqs_screen',
                                ],
                                [
                                    'title' => 'Contact Us',
                                    'icon' => 'mail',
                                    'action' => 'navigate',
                                    'value' => 'contact_screen',
                                ],
                                [
                                    'title' => 'Terms',
                                    'icon' => 'file-text',
                                    'action' => 'navigate',
                                    'value' => 'terms_screen',
                                ],
                            ],
                        ],
                    ],
                ]),

                'order' => 1,
                'is_active' => true,
            ],

            // =========================
            // FAQS (SCREEN)
            // =========================
            [
                'title' => 'FAQs',
                'subtitle' => 'Find answers quickly',
                'icon' => 'help-circle',

                'value' => 'faq_screen',
                'module' => 'FAQS',

                'meta' => json_encode([
                    'module' => 'FAQS',
                ]),

                'screen_config' => json_encode([
                    'screen' => 'faq',

                    'header' => [
                        'title' => 'Frequently Asked Questions',
                        'subtitle' => 'Find answers quickly',
                        'icon' => 'help-circle',
                    ],

                    'layout' => 'accordion',

                    'search' => [
                        'enabled' => true,
                        'placeholder' => 'Search FAQs...'
                    ],

                    'sections' => [
                        [
                            'type' => 'accordion',
                            'question' => 'How can I reset my password?',
                            'answer' => 'Go to settings > account > reset password.',
                        ],
                        [
                            'type' => 'accordion',
                            'question' => 'How to contact support?',
                            'answer' => 'Use chat, email or ticket system.',
                        ],
                    ],
                ]),

                'order' => 2,
                'is_active' => true,
            ],

            // =========================
            // CONTACT US (SCREEN)
            // =========================
            [
                'title' => 'Contact Us',
                'subtitle' => 'We are here to help you',
                'icon' => 'chat',

                'value' => 'contact_support',
                'module' => 'CONTACT_US',

                'meta' => json_encode([
                    'module' => 'CONTACT_US',
                ]),

                'screen_config' => json_encode([
                    'screen' => 'contact_support',

                    'header' => [
                        'title' => 'Contact Support',
                        'subtitle' => '24/7 Support',
                        'icon' => 'chat',
                    ],

                    'layout' => 'list',

                    'sections' => [
                        [
                            'type' => 'card',
                            'title' => 'Live Chat',
                            'subtitle' => 'Instant support',
                            'icon' => 'message-circle',
                            'action' => 'open_chat',
                        ],
                        [
                            'type' => 'card',
                            'title' => 'Email Support',
                            'subtitle' => 'Reply within 24h',
                            'icon' => 'mail',
                            'action' => 'open_email',
                        ],
                    ],
                ]),

                'order' => 3,
                'is_active' => true,
            ],

            // =========================
            // TERMS (Route)
            // =========================
            [
                'title' => 'Terms & Policies',
                'subtitle' => 'Legal information',
                'icon' => 'file',

                'value' => 'support.value',
                'module' => 'TERMS_POLICIES',

                'meta' => json_encode([
                    'module' => 'TERMS_POLICIES',
                ]),

                'screen_config' => null,

                'order' => 4,
                'is_active' => true,
            ],

            // =========================
            // LOGOUT (ACTION)
            // =========================
            [
                'title' => 'Logout',
                'subtitle' => 'Sign out from account',
                'icon' => 'logout',

                'value' => 'logout',
                'module' => 'GENERAL',

                'meta' => json_encode([
                    'module' => 'GENERAL',
                ]),

                'screen_config' => json_encode([
                    'type' => 'confirmation',
                    'title' => 'Are you sure you want to logout?',
                    'buttons' => [
                        [
                            'text' => 'Cancel',
                            'action' => 'dismiss',
                        ],
                        [
                            'text' => 'Logout',
                            'action' => 'logout',
                        ],
                    ],
                ]),

                'order' => 5,
                'is_active' => true,
            ],

        ]);
    }
}
