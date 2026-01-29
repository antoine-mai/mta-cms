<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Full Width Admin Dashboard</title>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#135bec",
                        "background-light": "#f6f6f8",
                        "background-dark": "#101622",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white font-display min-h-screen flex flex-col overflow-x-hidden">
<!-- Top Navigation Bar -->
<header class="sticky top-0 z-50 w-full bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shadow-sm">
<div class="px-6 lg:px-8 h-16 flex items-center justify-between">
<!-- Left: Logo & Nav -->
<div class="flex items-center gap-10">
<div class="flex items-center gap-3">
<div class="size-8 text-primary">
<svg class="w-full h-full" fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
<path clip-rule="evenodd" d="M24 18.4228L42 11.475V34.3663C42 34.7796 41.7457 35.1504 41.3601 35.2992L24 42V18.4228Z" fill="currentColor" fill-rule="evenodd"></path>
<path clip-rule="evenodd" d="M24 8.18819L33.4123 11.574L24 15.2071L14.5877 11.574L24 8.18819ZM9 15.8487L21 20.4805V37.6263L9 32.9945V15.8487ZM27 37.6263V20.4805L39 15.8487V32.9945L27 37.6263ZM25.354 2.29885C24.4788 1.98402 23.5212 1.98402 22.646 2.29885L4.98454 8.65208C3.7939 9.08038 3 10.2097 3 11.475V34.3663C3 36.0196 4.01719 37.5026 5.55962 38.098L22.9197 44.7987C23.6149 45.0671 24.3851 45.0671 25.0803 44.7987L42.4404 38.098C43.9828 37.5026 45 36.0196 45 34.3663V11.475C45 10.2097 44.2061 9.08038 43.0155 8.65208L25.354 2.29885Z" fill="currentColor" fill-rule="evenodd"></path>
</svg>
</div>
<h2 class="text-slate-900 dark:text-white text-lg font-bold tracking-tight">AdminPanel</h2>
</div>
<nav class="hidden md:flex items-center gap-1">
<a class="px-4 py-2 text-sm font-medium text-primary bg-primary/10 rounded-lg transition-colors" href="#">Dashboard</a>
<a class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" href="#">Analytics</a>
<a class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" href="#">Projects</a>
<a class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" href="#">Users</a>
</nav>
</div>
<!-- Right: Search & Actions -->
<div class="flex items-center gap-4">
<div class="hidden lg:flex w-64 xl:w-80 h-10 bg-slate-100 dark:bg-slate-800 rounded-lg items-center px-3 border border-transparent focus-within:border-primary transition-all">
<span class="material-symbols-outlined text-slate-400 text-[20px]">search</span>
<input class="bg-transparent border-none focus:ring-0 text-sm w-full text-slate-900 dark:text-white placeholder-slate-400 h-full" placeholder="Search..." type="text"/>
</div>
<div class="flex items-center gap-2">
<button class="hidden sm:flex h-10 px-4 bg-primary hover:bg-primary/90 text-white text-sm font-bold rounded-lg items-center justify-center transition-colors">
<span class="material-symbols-outlined text-[18px] mr-2">add</span>
                        Create New
                    </button>
<button class="size-10 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors relative">
<span class="material-symbols-outlined text-[20px]">notifications</span>
<span class="absolute top-2.5 right-2.5 size-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-900"></span>
</button>
<button class="size-10 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors">
<span class="material-symbols-outlined text-[20px]">settings</span>
</button>
<div class="w-10 h-10 rounded-full bg-cover bg-center ml-2 border-2 border-white dark:border-slate-800 shadow-sm cursor-pointer" data-alt="User profile picture of a man" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDrD_iJ1-bP05wnsg8FP-w326PNIl2qPhrD6wBMBxT0PGBSrSljEiEsWs0pxZxvBErkfe0Eu45ivCshjbtugjSN-wUJxbcr3UL6Xu0shHOZ9Ks33FAQq-7eBy8BSge_G5811Ibds61NAqBV6VeaVegzcodZlek3zu25iVMdyLxJKLbDiAgnSqE79lvDnavQR9TzKH7vlwRosGKWlzJsFqDYH8rkUyJsD6wp0WWA-K_5tsITtTu67f6SLanwtTdle5hX3N951athqlI2");'>
</div>
</div>
</div>
</div>
</header>
<!-- Main Content -->
<main class="flex-1 w-full max-w-[1920px] mx-auto p-6 lg:p-8 flex flex-col gap-6">
<!-- Header & Quick Filters -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
<div>
<h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Dashboard Overview</h1>
<p class="text-slate-500 dark:text-slate-400 mt-1">Welcome back, here is your daily performance summary.</p>
</div>
<div class="flex flex-wrap gap-3">
<button class="flex items-center gap-2 h-9 px-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
<span>Last 30 Days</span>
<span class="material-symbols-outlined text-[16px]">keyboard_arrow_down</span>
</button>
<button class="flex items-center gap-2 h-9 px-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
<span class="material-symbols-outlined text-[16px]">download</span>
<span>Export</span>
</button>
<button class="flex items-center gap-2 h-9 px-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
<span class="material-symbols-outlined text-[16px]">refresh</span>
<span>Refresh</span>
</button>
</div>
</div>
<!-- KPI Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
<!-- Stat Card 1 -->
<div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between h-full group hover:border-primary/50 transition-colors">
<div class="flex justify-between items-start mb-4">
<div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-primary">
<span class="material-symbols-outlined">payments</span>
</div>
<span class="flex items-center text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-1 rounded text-xs font-bold">
<span class="material-symbols-outlined text-[14px] mr-1">trending_up</span>
                        12%
                    </span>
</div>
<div>
<p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Total Revenue</p>
<h3 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">$124,500</h3>
</div>
</div>
<!-- Stat Card 2 -->
<div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between h-full group hover:border-primary/50 transition-colors">
<div class="flex justify-between items-start mb-4">
<div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg text-purple-600 dark:text-purple-400">
<span class="material-symbols-outlined">folder_open</span>
</div>
<span class="flex items-center text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-1 rounded text-xs font-bold">
<span class="material-symbols-outlined text-[14px] mr-1">trending_up</span>
                        2%
                    </span>
</div>
<div>
<p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Active Projects</p>
<h3 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">45</h3>
</div>
</div>
<!-- Stat Card 3 -->
<div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between h-full group hover:border-primary/50 transition-colors">
<div class="flex justify-between items-start mb-4">
<div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg text-orange-600 dark:text-orange-400">
<span class="material-symbols-outlined">group_add</span>
</div>
<span class="flex items-center text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-1 rounded text-xs font-bold">
<span class="material-symbols-outlined text-[14px] mr-1">trending_up</span>
                        5%
                    </span>
</div>
<div>
<p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">New Clients</p>
<h3 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">12</h3>
</div>
</div>
<!-- Stat Card 4 -->
<div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between h-full group hover:border-primary/50 transition-colors">
<div class="flex justify-between items-start mb-4">
<div class="p-2 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg text-cyan-600 dark:text-cyan-400">
<span class="material-symbols-outlined">health_and_safety</span>
</div>
<span class="flex items-center text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-1 rounded text-xs font-bold">
<span class="material-symbols-outlined text-[14px] mr-1">check_circle</span>
                        Good
                    </span>
</div>
<div>
<p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">System Health</p>
<h3 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">99.9%</h3>
</div>
</div>
</div>
<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-auto">
<!-- Main Chart -->
<div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col">
<div class="flex justify-between items-start mb-6">
<div>
<h3 class="text-lg font-bold text-slate-900 dark:text-white">Annual Growth Overview</h3>
<div class="flex items-center gap-2 mt-1">
<span class="text-3xl font-bold text-slate-900 dark:text-white">$1.2M</span>
<span class="text-emerald-600 dark:text-emerald-400 text-sm font-medium bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded">+8.5%</span>
</div>
</div>
<select class="bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm rounded-lg focus:ring-primary focus:border-primary block p-2">
<option>Yearly</option>
<option>Monthly</option>
<option>Weekly</option>
</select>
</div>
<div class="flex-1 w-full relative min-h-[300px]">
<!-- Simulated Chart Visualization -->
<svg class="w-full h-full" preserveaspectratio="none" viewbox="0 0 800 300">
<defs>
<lineargradient id="chartGradient" x1="0" x2="0" y1="0" y2="1">
<stop offset="0%" stop-color="#135bec" stop-opacity="0.2"></stop>
<stop offset="100%" stop-color="#135bec" stop-opacity="0"></stop>
</lineargradient>
</defs>
<!-- Grid Lines -->
<line class="text-slate-100 dark:text-slate-700" stroke="currentColor" stroke-width="1" x1="0" x2="800" y1="250" y2="250"></line>
<line class="text-slate-100 dark:text-slate-700" stroke="currentColor" stroke-width="1" x1="0" x2="800" y1="190" y2="190"></line>
<line class="text-slate-100 dark:text-slate-700" stroke="currentColor" stroke-width="1" x1="0" x2="800" y1="130" y2="130"></line>
<line class="text-slate-100 dark:text-slate-700" stroke="currentColor" stroke-width="1" x1="0" x2="800" y1="70" y2="70"></line>
<!-- Area -->
<path d="M0,250 Q100,200 200,220 T400,150 T600,100 T800,50 V250 H0 Z" fill="url(#chartGradient)"></path>
<!-- Line -->
<path d="M0,250 Q100,200 200,220 T400,150 T600,100 T800,50" fill="none" stroke="#135bec" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"></path>
<!-- Dots -->
<circle cx="200" cy="220" fill="#ffffff" r="4" stroke="#135bec" stroke-width="2"></circle>
<circle cx="400" cy="150" fill="#ffffff" r="4" stroke="#135bec" stroke-width="2"></circle>
<circle cx="600" cy="100" fill="#ffffff" r="4" stroke="#135bec" stroke-width="2"></circle>
</svg>
<!-- X-Axis Labels -->
<div class="flex justify-between mt-4 text-xs font-medium text-slate-400">
<span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span><span>Jun</span><span>Jul</span><span>Aug</span><span>Sep</span><span>Oct</span><span>Nov</span><span>Dec</span>
</div>
</div>
</div>
<!-- Secondary Chart -->
<div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col">
<div class="flex justify-between items-start mb-2">
<h3 class="text-lg font-bold text-slate-900 dark:text-white">Traffic Sources</h3>
<button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
<span class="material-symbols-outlined text-[20px]">more_horiz</span>
</button>
</div>
<div class="mb-6">
<span class="text-2xl font-bold text-slate-900 dark:text-white">150k</span>
<span class="text-sm text-slate-500 dark:text-slate-400 ml-1">Visits</span>
</div>
<div class="flex-1 flex flex-col justify-center gap-6">
<!-- Source Item 1 -->
<div class="space-y-2">
<div class="flex justify-between text-sm font-medium">
<span class="text-slate-700 dark:text-slate-300">Direct</span>
<span class="text-slate-900 dark:text-white">45%</span>
</div>
<div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
<div class="bg-primary h-2 rounded-full" style="width: 45%"></div>
</div>
</div>
<!-- Source Item 2 -->
<div class="space-y-2">
<div class="flex justify-between text-sm font-medium">
<span class="text-slate-700 dark:text-slate-300">Social Media</span>
<span class="text-slate-900 dark:text-white">32%</span>
</div>
<div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
<div class="bg-indigo-500 h-2 rounded-full" style="width: 32%"></div>
</div>
</div>
<!-- Source Item 3 -->
<div class="space-y-2">
<div class="flex justify-between text-sm font-medium">
<span class="text-slate-700 dark:text-slate-300">Organic Search</span>
<span class="text-slate-900 dark:text-white">18%</span>
</div>
<div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
<div class="bg-teal-500 h-2 rounded-full" style="width: 18%"></div>
</div>
</div>
<!-- Source Item 4 -->
<div class="space-y-2">
<div class="flex justify-between text-sm font-medium">
<span class="text-slate-700 dark:text-slate-300">Referral</span>
<span class="text-slate-900 dark:text-white">5%</span>
</div>
<div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
<div class="bg-orange-400 h-2 rounded-full" style="width: 5%"></div>
</div>
</div>
</div>
</div>
</div>
<!-- Wide Data Table -->
<div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden flex flex-col">
<div class="p-6 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
<h3 class="text-lg font-bold text-slate-900 dark:text-white">Project Status Overview</h3>
<div class="flex gap-2 w-full sm:w-auto">
<div class="relative flex-1 sm:w-64">
<span class="material-symbols-outlined absolute left-2.5 top-2.5 text-slate-400 text-[18px]">search</span>
<input class="pl-9 w-full h-9 text-sm bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg focus:ring-primary focus:border-primary placeholder-slate-400 text-slate-900 dark:text-white" placeholder="Search projects..." type="text"/>
</div>
<button class="h-9 px-3 flex items-center gap-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
<span class="material-symbols-outlined text-[18px]">filter_list</span>
                        Filter
                    </button>
</div>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 dark:bg-slate-700/30 border-b border-slate-200 dark:border-slate-700 text-xs uppercase text-slate-500 dark:text-slate-400 font-semibold tracking-wider">
<th class="px-6 py-4">Project Name</th>
<th class="px-6 py-4">Lead</th>
<th class="px-6 py-4">Due Date</th>
<th class="px-6 py-4">Status</th>
<th class="px-6 py-4 w-48">Progress</th>
<th class="px-6 py-4">Budget</th>
<th class="px-6 py-4 text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
<!-- Row 1 -->
<tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group">
<td class="px-6 py-4">
<div class="font-medium text-slate-900 dark:text-white">Website Redesign</div>
<div class="text-xs text-slate-500">Marketing</div>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="size-6 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-xs font-bold text-indigo-700 dark:text-indigo-300">JD</div>
<span class="text-slate-700 dark:text-slate-300">John Doe</span>
</div>
</td>
<td class="px-6 py-4 text-slate-600 dark:text-slate-400">Oct 24, 2023</td>
<td class="px-6 py-4">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    In Progress
                                </span>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
<div class="h-full bg-blue-500 w-[60%] rounded-full"></div>
</div>
<span class="text-xs text-slate-500">60%</span>
</div>
</td>
<td class="px-6 py-4 font-medium text-slate-900 dark:text-white">$12,000</td>
<td class="px-6 py-4 text-right">
<button class="p-1 rounded text-slate-400 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
<span class="material-symbols-outlined text-[20px]">more_vert</span>
</button>
</td>
</tr>
<!-- Row 2 -->
<tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group">
<td class="px-6 py-4">
<div class="font-medium text-slate-900 dark:text-white">Mobile App Launch</div>
<div class="text-xs text-slate-500">Development</div>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="size-6 rounded-full bg-teal-100 dark:bg-teal-900/50 flex items-center justify-center text-xs font-bold text-teal-700 dark:text-teal-300">AS</div>
<span class="text-slate-700 dark:text-slate-300">Alice Smith</span>
</div>
</td>
<td class="px-6 py-4 text-slate-600 dark:text-slate-400">Nov 01, 2023</td>
<td class="px-6 py-4">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                                    Completed
                                </span>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
<div class="h-full bg-emerald-500 w-[100%] rounded-full"></div>
</div>
<span class="text-xs text-slate-500">100%</span>
</div>
</td>
<td class="px-6 py-4 font-medium text-slate-900 dark:text-white">$45,000</td>
<td class="px-6 py-4 text-right">
<button class="p-1 rounded text-slate-400 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
<span class="material-symbols-outlined text-[20px]">more_vert</span>
</button>
</td>
</tr>
<!-- Row 3 -->
<tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group">
<td class="px-6 py-4">
<div class="font-medium text-slate-900 dark:text-white">Q4 Marketing Campaign</div>
<div class="text-xs text-slate-500">Marketing</div>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="size-6 rounded-full bg-orange-100 dark:bg-orange-900/50 flex items-center justify-center text-xs font-bold text-orange-700 dark:text-orange-300">MR</div>
<span class="text-slate-700 dark:text-slate-300">Mike Ross</span>
</div>
</td>
<td class="px-6 py-4 text-slate-600 dark:text-slate-400">Dec 15, 2023</td>
<td class="px-6 py-4">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                    Pending
                                </span>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
<div class="h-full bg-yellow-400 w-[15%] rounded-full"></div>
</div>
<span class="text-xs text-slate-500">15%</span>
</div>
</td>
<td class="px-6 py-4 font-medium text-slate-900 dark:text-white">$8,500</td>
<td class="px-6 py-4 text-right">
<button class="p-1 rounded text-slate-400 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
<span class="material-symbols-outlined text-[20px]">more_vert</span>
</button>
</td>
</tr>
<!-- Row 4 -->
<tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group">
<td class="px-6 py-4">
<div class="font-medium text-slate-900 dark:text-white">Internal Dashboard</div>
<div class="text-xs text-slate-500">Product</div>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="size-6 rounded-full bg-pink-100 dark:bg-pink-900/50 flex items-center justify-center text-xs font-bold text-pink-700 dark:text-pink-300">SL</div>
<span class="text-slate-700 dark:text-slate-300">Sarah Lee</span>
</div>
</td>
<td class="px-6 py-4 text-slate-600 dark:text-slate-400">Jan 20, 2024</td>
<td class="px-6 py-4">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                    Review
                                </span>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
<div class="h-full bg-purple-500 w-[85%] rounded-full"></div>
</div>
<span class="text-xs text-slate-500">85%</span>
</div>
</td>
<td class="px-6 py-4 font-medium text-slate-900 dark:text-white">$22,000</td>
<td class="px-6 py-4 text-right">
<button class="p-1 rounded text-slate-400 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
<span class="material-symbols-outlined text-[20px]">more_vert</span>
</button>
</td>
</tr>
<!-- Row 5 -->
<tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group">
<td class="px-6 py-4">
<div class="font-medium text-slate-900 dark:text-white">Cloud Migration</div>
<div class="text-xs text-slate-500">Infrastructure</div>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="size-6 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-700 dark:text-gray-300">DK</div>
<span class="text-slate-700 dark:text-slate-300">David Kim</span>
</div>
</td>
<td class="px-6 py-4 text-slate-600 dark:text-slate-400">Feb 10, 2024</td>
<td class="px-6 py-4">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                    Delayed
                                </span>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
<div class="h-full bg-red-500 w-[40%] rounded-full"></div>
</div>
<span class="text-xs text-slate-500">40%</span>
</div>
</td>
<td class="px-6 py-4 font-medium text-slate-900 dark:text-white">$60,000</td>
<td class="px-6 py-4 text-right">
<button class="p-1 rounded text-slate-400 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
<span class="material-symbols-outlined text-[20px]">more_vert</span>
</button>
</td>
</tr>
</tbody>
</table>
</div>
<div class="p-4 border-t border-slate-200 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
<p class="text-sm text-slate-500 dark:text-slate-400">Showing 5 of 24 results</p>
<div class="flex gap-2">
<button class="px-3 py-1.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-700 disabled:opacity-50" disabled="">Previous</button>
<button class="px-3 py-1.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-700">Next</button>
</div>
</div>
</div>
</main>
</body></html>