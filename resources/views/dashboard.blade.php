<x-layouts.app :title="__('Dashboard')">
    <div class="min-h-screen bg-white dark:bg-zinc-800 transition-colors duration-300">
        <!-- Header -->
        <div class="border-b border-gray-200 dark:border-gray-800 px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-light text-black dark:text-white">Transaction Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <!-- Date Range Selector -->
                    <div class="relative">
                        <select class="bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 text-black dark:text-white appearance-none focus:outline-none focus:ring-1 focus:ring-black dark:focus:ring-white">
                            <option class="bg-white dark:bg-black text-black dark:text-white">Daily</option>
                            <option class="bg-white dark:bg-black text-black dark:text-white">Weekly</option>
                            <option class="bg-white dark:bg-black text-black dark:text-white">Monthly</option>
                            <option class="bg-white dark:bg-black text-black dark:text-white">Custom Range</option>
                        </select>
                    </div>
                    
                    <!-- Custom Date Range (hidden by default) -->
                    <div class="hidden md:flex space-x-2">
                        <input type="date" class="bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-black dark:text-white text-sm focus:outline-none focus:ring-1 focus:ring-black dark:focus:ring-white">
                        <span class="text-black dark:text-white self-center">to</span>
                        <input type="date" class="bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-black dark:text-white text-sm focus:outline-none focus:ring-1 focus:ring-black dark:focus:ring-white">
                    </div>
                </div>
            </div>
        </div>


        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
            <!-- Completed Repairs Total -->
            <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Income from completed repairs [ ETB ]</p>
                        <p class="text-3xl font-light text-black dark:text-white mt-2">{{ number_format($totalCompletedAmount, 2) }}</p>
                        <p class="text-green-600 dark:text-green-400 text-sm mt-1">Total revenue from completed repairs</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-900 flex items-center justify-center">
                        <svg class="w-6 h-6 text-black dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Average Transaction -->
            <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Avg. Daily Transactions</p>
                        <p class="text-3xl font-light text-black dark:text-white mt-2">ETB 47</p>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Across all sources</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-900 flex items-center justify-center">
                        <svg class="w-6 h-6 text-black dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Transactions -->
            <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Transactions</p>
                        <p class="text-3xl font-light text-black dark:text-white mt-2">47</p>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">This period</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-900 flex items-center justify-center">
                        <svg class="w-6 h-6 text-black dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 px-6 pb-6">
            <!-- Income Chart -->
            <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-medium text-black dark:text-white">Income Overview</h2>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 text-xs border border-gray-300 dark:border-gray-700 rounded-lg text-black dark:text-white">Week</button>
                        <button class="px-3 py-1 text-xs bg-black dark:bg-white text-white dark:text-black rounded-lg">Month</button>
                        <button class="px-3 py-1 text-xs border border-gray-300 dark:border-gray-700 rounded-lg text-black dark:text-white">Year</button>
                    </div>
                </div>
                <div class="h-80">
                    <!-- Chart container - you would integrate with Chart.js or similar here -->
                    <div class="w-full h-full flex items-end justify-between pt-10">
                        <!-- These would be dynamic bars in a real chart -->
                        <div class="flex flex-col items-center flex-1 mx-1">
                            <div class="w-full bg-gray-300 dark:bg-gray-700 rounded-t" style="height: 60%"></div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-2">Mon</span>
                        </div>
                        <div class="flex flex-col items-center flex-1 mx-1">
                            <div class="w-full bg-gray-300 dark:bg-gray-700 rounded-t" style="height: 80%"></div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-2">Tue</span>
                        </div>
                        <div class="flex flex-col items-center flex-1 mx-1">
                            <div class="w-full bg-gray-300 dark:bg-gray-700 rounded-t" style="height: 45%"></div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-2">Wed</span>
                        </div>
                        <div class="flex flex-col items-center flex-1 mx-1">
                            <div class="w-full bg-gray-300 dark:bg-gray-700 rounded-t" style="height: 90%"></div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-2">Thu</span>
                        </div>
                        <div class="flex flex-col items-center flex-1 mx-1">
                            <div class="w-full bg-black dark:bg-white rounded-t" style="height: 75%"></div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-2">Fri</span>
                        </div>
                        <div class="flex flex-col items-center flex-1 mx-1">
                            <div class="w-full bg-gray-300 dark:bg-gray-700 rounded-t" style="height: 50%"></div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-2">Sat</span>
                        </div>
                        <div class="flex flex-col items-center flex-1 mx-1">
                            <div class="w-full bg-gray-300 dark:bg-gray-700 rounded-t" style="height: 30%"></div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-2">Sun</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Income Sources -->
            <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                <h2 class="text-lg font-medium text-black dark:text-white mb-6">Income Sources</h2>
                <div class="space-y-4">
                    <!-- Source 1 -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-black dark:bg-white mr-3"></div>
                            <span class="text-black dark:text-white">FRP [USB]</span>
                        </div>
                        <div class="text-right">
                            <p class="text-black dark:text-white font-medium">$5,240</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">42% of total</p>
                        </div>
                    </div>
                    
                    <!-- Source 2 -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-gray-400 dark:bg-gray-600 mr-3"></div>
                            <span class="text-black dark:text-white">FRP [ISP]</span>
                        </div>
                        <div class="text-right">
                            <p class="text-black dark:text-white font-medium">$3,890</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">31% of total</p>
                        </div>
                    </div>
                    
                    <!-- Source 3 -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-gray-300 dark:bg-gray-700 mr-3"></div>
                            <span class="text-black dark:text-white">MDM</span>
                        </div>
                        <div class="text-right">
                            <p class="text-black dark:text-white font-medium">$2,150</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">17% of total</p>
                        </div>
                    </div>
                    
                    <!-- Source 4 -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-gray-200 dark:bg-gray-800 mr-3"></div>
                            <span class="text-black dark:text-white">Other Services</span>
                        </div>
                        <div class="text-right">
                            <p class="text-black dark:text-white font-medium">$1,178</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">10% of total</p>
                        </div>
                    </div>
                </div>
                
                <!-- Simple pie chart visualization -->
                <div class="mt-8 flex justify-center">
                    <div class="relative w-40 h-40 rounded-full" 
                         style="background: conic-gradient(
                             black 0% 42%, 
                             #9ca3af 42% 73%, 
                             #d1d5db 73% 90%, 
                             #e5e7eb 90% 100%
                         );">
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="px-6 pb-6">
            <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-medium text-black dark:text-white">Recent Transactions</h2>
                    <button class="text-sm text-black dark:text-white border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                        View All
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-800">
                                <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-normal">Description</th>
                                <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-normal">Date</th>
                                <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-normal">Source</th>
                                <th class="text-right py-3 text-gray-500 dark:text-gray-400 font-normal">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-100 dark:border-gray-900">
                                <td class="py-4 text-black dark:text-white">Consulting Project A</td>
                                <td class="py-4 text-gray-500 dark:text-gray-400">Today</td>
                                <td class="py-4 text-gray-500 dark:text-gray-400">Consulting</td>
                                <td class="py-4 text-right text-black dark:text-white font-medium">$1,200</td>
                            </tr>
                            <tr class="border-b border-gray-100 dark:border-gray-900">
                                <td class="py-4 text-black dark:text-white">Product Sale - Premium</td>
                                <td class="py-4 text-gray-500 dark:text-gray-400">Yesterday</td>
                                <td class="py-4 text-gray-500 dark:text-gray-400">Product Sales</td>
                                <td class="py-4 text-right text-black dark:text-white font-medium">$450</td>
                            </tr>
                            <tr class="border-b border-gray-100 dark:border-gray-900">
                                <td class="py-4 text-black dark:text-white">Monthly Service Retainer</td>
                                <td class="py-4 text-gray-500 dark:text-gray-400">Oct 12</td>
                                <td class="py-4 text-gray-500 dark:text-gray-400">Services</td>
                                <td class="py-4 text-right text-black dark:text-white font-medium">$800</td>
                            </tr>
                            <tr>
                                <td class="py-4 text-black dark:text-white">Consulting Project B</td>
                                <td class="py-4 text-gray-500 dark:text-gray-400">Oct 10</td>
                                <td class="py-4 text-gray-500 dark:text-gray-400">Consulting</td>
                                <td class="py-4 text-right text-black dark:text-white font-medium">$950</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>