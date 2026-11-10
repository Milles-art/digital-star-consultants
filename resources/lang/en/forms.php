<?php

/**
 * Printable forms — English translation.
 *
 * Kiswahili (resources/lang/sw/forms.php) is the source of truth; missing keys
 * here fall back to the Kiswahili text rather than printing a raw key.
 */

return [

    'common' => [
        'back' => 'Back to forms',
        'print' => 'Print this form',
        'download_hint' => 'Tap "Print" then choose "Save as PDF" in the print dialog.',
        'sections' => 'Sections',
        'fields' => 'Details',
        'optional' => 'optional',
        'stamp' => 'OFFICE STAMP',
        'signature' => 'Signature',
        'date' => 'Date',
        'line' => '________________________',
        'check_here' => 'Tick (✓)',
        'footer_tagline' => 'Live & Enjoy your Digital Life',
    ],

    'index' => [
        'eyebrow' => 'Application assistance forms',
        'title' => 'Application forms',
        'lead' => 'Pick a form, print it and fill it in by hand, then bring it to our Mbagala Rangi 3 office. You can also apply online through the matching service.',
        'open' => 'Open form',
        'services_link' => 'See related services',
        'empty' => 'No forms available yet.',
    ],

    'tracking-simu' => [
        'card' => [
            'title' => 'Lost or Stolen Phone Tracking Service Request Form',
            'summary' => 'Submit what you have. We help prepare reports and follow up through Police/the network provider, and will guide you on missing documents. Device location or recovery is not guaranteed.',
            'meta' => ['From TSh 100,000/=', 'Mbagala Rangi 3', 'Reporting and follow-up assistance'],
        ],
        'title' => 'LOST OR STOLEN PHONE TRACKING SERVICE REQUEST FORM',
        'office' => [
            ['icon' => 'map-pin', 'text' => 'Digital Star Consultants – Mbagala Rangi 3, Dar es Salaam'],
            ['icon' => 'phone', 'text' => 'Phone: 0783257716 / 0757257716 / +255 760 637 716'],
        ],
        'sections' => [
            [
                'heading' => '1. REPORTING STAGE',
                'blocks' => [
                    ['type' => 'note', 'text' => 'Missing information or documents? Continue and we will contact you. Do not share passwords or OTPs. This is a Digital Star assistance form, not a police report.'],
                    ['type' => 'lines', 'columns' => 1, 'items' => ['Have you reported to Police? We can guide you if not', 'RB / report number and station, if available', 'Investigator and contact, if assigned', 'Provider and device/SIM blocking status']],
                ],
            ],
            [
                'heading' => '2. APPLICANT DETAILS',
                'blocks' => [
                    ['type' => 'lines', 'columns' => 1, 'items' => [
                        'Full Name',
                        'NIDA Number',
                        'Contact Phone Number',
                        'Email Address',
                        'Region',
                        'District',
                        'Ward / Street / Village',
                        'Full Address',
                    ]],
                ],
            ],
            [
                'heading' => '3. LOST / STOLEN PHONE DETAILS',
                'blocks' => [
                    ['type' => 'lines', 'columns' => 1, 'items' => [
                        'Phone Make / Model',
                        'Phone Colour',
                        'IMEI No. 1',
                        'IMEI No. 2',
                        'Phone Number That Was In Use',
                        'Date Lost / Stolen',
                        'Place Where It Was Lost / Stolen',
                        'Incident Description',
                    ]],
                ],
            ],
            [
                'heading' => '4. SUPPORTING DOCUMENTS SUBMITTED',
                'blocks' => [
                    ['type' => 'note', 'text' => '(Tick every item you are submitting)'],
                    ['type' => 'checks', 'items' => [
                        'Copy of National Identification (NIDA)',
                        'Phone Box showing the IMEI',
                        'Purchase Receipt',
                        'Google Account (Android)',
                        'iCloud Account (iPhone)',
                        'Email Linked to the Phone',
                        'Phone Number That Was In Use',
                    ], 'other' => 'Other (specify)'],
                ],
            ],
            [
                'heading' => '5. APPLICANT OATH',
                'blocks' => [
                    ['type' => 'declaration', 'paragraphs' => [
                        'I, ................................................................, holder of NIDA number ........................................, do swear and confirm that the phone I am requesting to be tracked is my lawful property and that I have the legal authority to submit this request.',
                        'I understand that all information I have provided in this form together with the supporting documents submitted is true and correct to the best of my knowledge.',
                        'I understand that giving false information, forging documents or submitting a request to track a phone that is not mine may result in legal action being taken against me under the laws of the United Republic of Tanzania.',
                        'I accept full responsibility for the accuracy of the information I have provided, and I hold Digital Star Consultants harmless against any claim arising from incorrect information I have given.',
                    ]],
                ],
            ],
            [
                'heading' => '6. IMPORTANT NOTICE AND DISCLAIMER OF LIABILITY',
                'blocks' => [
                    ['type' => 'lead', 'text' => 'I understand and accept that:'],
                    ['type' => 'terms', 'items' => [
                        'Digital Star Consultants helps prepare reports and follow up through Police and the network provider. Access to location data or recovery of the phone is not promised.',
                        'Digital Star Consultants has no legal authority to arrest, question or take any enforcement action against any person selling the phone being tracked.',
                        'If the phone is recovered or its location is established, every step to recover the phone, investigate, arrest suspects or lay charges will be carried out by the relevant authorities under the laws of the country.',
                        'Digital Star Consultants shall not be liable for any loss arising from a customer or any other person taking the law into their own hands without the consent of the relevant authority, or from tracking a phone that is not lawfully theirs.',
                        'Digital Star Consultants gives no guarantee that the phone in question will be found, as the outcome of the service depends on the availability of information and the technical circumstances involved.',
                        'All information provided will be used for lawful purposes and in accordance with the laws of the United Republic of Tanzania.',
                    ]],
                ],
            ],
            [
                'heading' => '7. APPLICANT DECLARATION',
                'blocks' => [
                    ['type' => 'declaration', 'paragraphs' => [
                        'I, ................................................................, confirm that I have read, understood and agree with all the information contained in this form.',
                    ]],
                    ['type' => 'signatures', 'items' => ['Applicant Signature', 'Date']],
                ],
            ],
            [
                'heading' => '8. FOR OFFICE USE ONLY',
                'blocks' => [
                    ['type' => 'office', 'fields' => ['Name of Officer Receiving the Request', 'Signature', 'Date', 'Officer Remarks']],
                ],
            ],
            [
                'heading' => '9. TRACKING SERVICE FEES',
                'blocks' => [
                    [
                        'type' => 'pricing',
                        'columns' => ['No.', 'SERVICE PACKAGE', 'DETAILS', 'FEE (TZS)'],
                        'rows' => [
                            ['no' => '1.', 'package' => 'BASIC TRACK', 'price' => '100,000/=', 'details' => [
                                'Help preparing incident information',
                                'Summary of steps taken',
                            ]],
                            ['no' => '2.', 'package' => 'STANDARD TRACK', 'price' => '200,000/=', 'details' => [
                                'Help following up with the relevant authorities',
                                'Summary of steps taken',
                                'Advice on the next steps to take',
                            ]],
                            ['no' => '3.', 'package' => 'PREMIUM TRACK', 'price' => '300,000/=', 'details' => [
                                'Dedicated tracking support',
                                'Summary of steps taken',
                                'Advice on recovery steps',
                                'Regular follow-up support',
                            ]],
                        ],
                    ],
                    ['type' => 'note', 'text' => 'PLEASE NOTE: Fees exclude legal fees and any charges connected with Government authorities (such as the Police).'],
                ],
            ],
        ],
    ],

    'pasipoti' => [
        'card' => [
            'title' => 'Travel Passport Application Form',
            'summary' => 'This is an assistance form. Provide what you know and bring the documents you have; we will contact you to help complete the remaining steps. Immigration decides and issues passports.',
            'meta' => ['TSh 200,000/=', 'Mbagala Rangi 3', 'Help completing documents'],
        ],
        'title' => 'TRAVEL PASSPORT APPLICATION FORM',
        'office' => [
            ['icon' => 'map-pin', 'text' => 'Digital Star Consultants – Mbagala Rangi 3, Dar es Salaam'],
            ['icon' => 'phone', 'text' => 'Phone: 0783257716 / 0757257716 / +255 760 637 716'],
        ],
        'sections' => [
            [
                'heading' => 'SECTION A: APPLICANT PERSONAL DETAILS',
                'blocks' => [
                    ['type' => 'lines', 'columns' => 2, 'items' => [
                        'Full Name (First, Middle, Surname)',
                        'Common Name (if used)',
                        'Date of Birth (dd/mm/yyyy)',
                        'Place of Birth (Region / District)',
                        'Gender (Male / Female)',
                        'Nationality',
                        'NIDA Number',
                        'Phone Number',
                        'Residential Address',
                        'Postal Address (if different)',
                        'Occupation / Position',
                        'Employer / Company Name (if any)',
                        'Marital Status (Single / Married / Separated / Widowed)',
                    ]],
                ],
            ],
            [
                'heading' => 'SECTION B: PREVIOUS PASSPORT DETAILS (IF YOU HAVE HELD A PASSPORT)',
                'blocks' => [
                    ['type' => 'lines', 'columns' => 2, 'items' => [
                        'Have you held a Passport before? (Yes / No)',
                        'Previous Passport Number',
                        'Date of Issue (dd/mm/yyyy)',
                        'Date of Expiry (dd/mm/yyyy)',
                        'Place of Issue',
                        'Reason for Applying Again',
                    ]],
                ],
            ],
            [
                'heading' => 'SECTION C: PARENTS\' DETAILS',
                'blocks' => [
                    ['type' => 'groups', 'items' => [
                        ['label' => 'FATHER', 'lines' => [
                            'Full Name',
                            'Date of Birth',
                            'Place of Birth (Region / District)',
                            'Nationality',
                            'Occupation / Position',
                            'Phone Number',
                        ]],
                        ['label' => 'MOTHER', 'lines' => [
                            'Full Name',
                            'Date of Birth',
                            'Place of Birth (Region / District)',
                            'Nationality',
                            'Occupation / Position',
                            'Phone Number',
                        ]],
                    ]],
                ],
            ],
            [
                'heading' => 'SECTION D: ADDITIONAL DETAILS',
                'blocks' => [
                    ['type' => 'lines', 'columns' => 2, 'items' => [
                        'Country You Are Travelling To',
                        'How Long You Will Stay Abroad',
                        'Purpose of Travel',
                        'Do You Have Other Visas? (Yes / No)',
                        'If Yes, State the Countries',
                    ]],
                ],
            ],
            [
                'heading' => 'SECTION E: GUARANTORS AND PREPARATION DOCUMENTS',
                'blocks' => [
                    ['type' => 'note', 'text' => 'Provide what you have; missing information or documents do not stop an assistance request. Each guarantor must personally sign where Immigration requires. Our team will guide you on qualifications and signing steps.'],
                    ['type' => 'lines', 'columns' => 1, 'items' => [
                        'Guarantor 1: full name, address, phone and relationship',
                        'Guarantor 2: full name, address, phone and relationship',
                        'Parent/legal guardian of an applicant below 16: name and contact',
                        'Information / documents you need help obtaining',
                    ]],
                    ['type' => 'checks', 'items' => [
                        'Accepted applicant birth/citizenship evidence: birth certificate, affidavit or naturalisation certificate as applicable',
                        'Accepted parent birth/citizenship evidence',
                        'Applicant photographs',
                        'Written parent/legal guardian consent for an applicant below 16',
                    ]],
                ],
            ],
            [
                'heading' => 'APPLICANT DECLARATION',
                'blocks' => [
                    ['type' => 'declaration', 'paragraphs' => [
                        'I declare that all the information given above is true and correct.',
                    ]],
                    ['type' => 'signatures', 'items' => ['Applicant Signature', 'Date']],
                ],
            ],
        ],
        'footer_box' => [
            'title' => 'Digital Star assists with preparation and follow-up. Immigration decides applications and passport issuance times. Our team will help you with missing information or documents.',
            'fee_label' => 'SERVICE FEES:',
            'fee' => '10-Year Passport — Tsh 200,000/=',
            'contact_label' => 'For more information contact:',
            'phones' => ['0783257716', '0757257716', '+255 760 637 716'],
            'banner' => 'WELCOME TO DIGITAL STAR CONSULTANTS — WE HELP YOU QUICKLY AND RELIABLY!',
        ],
    ],
];
