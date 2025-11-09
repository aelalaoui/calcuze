<?php
// Root template configuration
if (!isset($cssPath)) $cssPath = 'css/styles.css';
if (!isset($includesPath)) $includesPath = __DIR__ . '/../includes/';
if (!isset($scriptsPath)) $scriptsPath = 'scripts/';

// Include common header with absolute path
include $includesPath . 'header.php';
?>
<body class="bg-gray-100 min-h-screen">
<!-- Main Container with Grid Layout -->
<div class="min-h-screen grid grid-rows-[auto_1fr_auto] grid-cols-1 md:grid-cols-[250px_1fr_250px] lg:grid-cols-[300px_1fr_300px] gap-4 p-4">

    <!-- Top Header with Logo and Top Ad -->
    <div class="col-span-full grid grid-cols-1 md:grid-cols-[250px_1fr] lg:grid-cols-[300px_1fr] gap-4">
        <!-- Logo Section -->
        <div class="logo-section rounded-xl p-4 flex items-center justify-center">
            <div class="text-white text-center">
                <i class="fas fa-calculator text-4xl mb-2"></i>
                <h1 class="text-xl font-bold"><?php _e('logo.title'); ?></h1>
                <p class="text-sm opacity-80"><?php _e('logo.subtitle'); ?></p>
                <?php include $includesPath . 'country-selector.php'; ?>
            </div>
        </div>

        <!-- Top Ad Banner -->
        <div class="ad-banner rounded-xl p-6 flex items-center justify-center">
            <div class="text-white text-center">
                <h2 class="text-2xl font-bold mb-2"><?php _e('ads.premium_title'); ?></h2>
                <p class="text-lg opacity-90"><?php _e('ads.premium_subtitle'); ?></p>
                <p class="text-sm opacity-75"><?php _e('ads.premium_contact'); ?></p>
            </div>
        </div>
    </div>

    <!-- Left Sidebar Ad -->
    <div class="ad-banner rounded-xl p-4 flex-col items-center justify-center hidden md:flex">
        <div class="text-white text-center">
            <i class="fas fa-bullhorn text-3xl mb-3"></i>
            <h3 class="text-lg font-bold mb-2"><?php _e('ads.sidebar_title'); ?></h3>
            <p class="text-sm mb-4 opacity-90"><?php _e('ads.sidebar_subtitle'); ?></p>
            <div class="space-y-2 text-xs opacity-75">
                <?php foreach(__('ads.sidebar_features') as $feature): ?>
                <p>• <?php echo htmlspecialchars($feature); ?></p>
                <?php endforeach; ?>
            </div>
            <button class="mt-4 bg-white text-purple-600 px-3 py-1 rounded text-xs font-semibold hover:bg-gray-100 transition">
                <?php _e('ads.learn_more'); ?>
            </button>
        </div>
    </div>

    <!-- Calculator Section (Main Content) -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden col-span-1">
        <!-- Header with mode selection -->
        <div class="bg-blue-600 p-4 text-white">
            <h1 class="text-xl font-bold text-center"><?php _e('calculator.title'); ?></h1>
            <div class="flex justify-between mt-3 space-x-1">
                <button id="normal-mode" class="mode-btn active flex-1 py-2 rounded-lg text-sm font-medium"><?php _e('calculator.modes.normal'); ?></button>
                <button id="scientific-mode" class="mode-btn flex-1 py-2 rounded-lg text-sm font-medium"><?php _e('calculator.modes.scientific'); ?></button>
                <button id="economic-mode" class="mode-btn flex-1 py-2 rounded-lg text-sm font-medium"><?php _e('calculator.modes.economic'); ?></button>
                <button id="conversion-mode" class="mode-btn flex-1 py-2 rounded-lg text-sm font-medium"><?php _e('calculator.modes.conversion'); ?></button>
            </div>
        </div>

        <!-- Display area -->
        <div class="p-4 bg-gray-50">
            <div id="history" class="text-gray-500 text-right text-sm h-6 overflow-hidden whitespace-nowrap"></div>
            <div id="display" class="display text-3xl font-semibold text-right py-2 overflow-x-auto">0</div>
        </div>

        <!-- Normal Calculator -->
        <div id="normal-calculator" class="calculator-section p-4 grid grid-cols-4 gap-3">
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-4 text-lg font-medium" onclick="clearAll()">AC</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-4 text-lg font-medium" onclick="toggleSign()">+/-</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-4 text-lg font-medium" onclick="percentage()">%</button>
            <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-4 text-lg font-medium" onclick="operation('/')">÷</button>

            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-4 text-lg font-medium" onclick="appendNumber(7)">7</button>
            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-4 text-lg font-medium" onclick="appendNumber(8)">8</button>
            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-4 text-lg font-medium" onclick="appendNumber(9)">9</button>
            <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-4 text-lg font-medium" onclick="operation('*')">×</button>

            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-4 text-lg font-medium" onclick="appendNumber(4)">4</button>
            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-4 text-lg font-medium" onclick="appendNumber(5)">5</button>
            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-4 text-lg font-medium" onclick="appendNumber(6)">6</button>
            <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-4 text-lg font-medium" onclick="operation('-')">-</button>

            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-4 text-lg font-medium" onclick="appendNumber(1)">1</button>
            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-4 text-lg font-medium" onclick="appendNumber(2)">2</button>
            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-4 text-lg font-medium" onclick="appendNumber(3)">3</button>
            <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-4 text-lg font-medium" onclick="operation('+')">+</button>

            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-4 text-lg font-medium col-span-2" onclick="appendNumber(0)">0</button>
            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-4 text-lg font-medium" onclick="appendDecimal()"><?php _e('decimal_separator'); ?></button>
            <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-4 text-lg font-medium" onclick="calculate()">=</button>
        </div>

        <!-- Scientific Calculator (hidden by default) -->
        <div id="scientific-calculator" class="calculator-section hidden p-4 grid grid-cols-5 gap-2">
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="scientificOperation('sin')">sin</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="scientificOperation('cos')">cos</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="scientificOperation('tan')">tan</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="scientificOperation('log')">log</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="scientificOperation('ln')">ln</button>

            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="scientificOperation('asin')">sin⁻¹</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="scientificOperation('acos')">cos⁻¹</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="scientificOperation('atan')">tan⁻¹</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="scientificOperation('sqrt')">√</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="scientificOperation('pow')">x^y</button>

            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="scientificOperation('pi')">π</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="scientificOperation('e')">e</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="scientificOperation('fact')">x!</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="scientificOperation('exp')">EXP</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="scientificOperation('mod')">mod</button>

            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="clearAll()">AC</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="toggleSign()">+/-</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="percentage()">%</button>
            <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-3 text-sm font-medium" onclick="operation('/')">÷</button>
            <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-3 text-sm font-medium" onclick="operation('*')">×</button>

            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(7)">7</button>
            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(8)">8</button>
            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(9)">9</button>
            <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-3 text-sm font-medium" onclick="operation('-')">-</button>
            <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-3 text-sm font-medium" onclick="operation('+')">+</button>

            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(4)">4</button>
            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(5)">5</button>
            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(6)">6</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="scientificOperation('(')">(</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="scientificOperation(')')">)</button>

            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(1)">1</button>
            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(2)">2</button>
            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(3)">3</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="scientificOperation('deg')">DEG</button>
            <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="scientificOperation('rad')">RAD</button>

            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium col-span-2" onclick="appendNumber(0)">0</button>
            <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendDecimal()"><?php _e('decimal_separator'); ?></button>
            <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-3 text-sm font-medium col-span-2" onclick="calculate()">=</button>
        </div>

        <!-- Economic Calculator (hidden by default) -->
        <div id="economic-calculator" class="calculator-section hidden p-4">
            <div class="grid grid-cols-3 gap-3 mb-3">
                <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="economicOperation('fv')"><?php _e('economic.buttons.fv'); ?></button>
                <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="economicOperation('pv')"><?php _e('economic.buttons.pv'); ?></button>
                <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="economicOperation('pmt')"><?php _e('economic.buttons.pmt'); ?></button>
                <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="economicOperation('npv')"><?php _e('economic.buttons.npv'); ?></button>
                <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="economicOperation('irr')"><?php _e('economic.buttons.irr'); ?></button>
                <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="economicOperation('roi')"><?php _e('economic.buttons.roi'); ?></button>
            </div>

            <div class="bg-gray-100 p-3 rounded-lg mb-3">
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1"><?php _e('economic.labels.principal'); ?></label>
                        <input type="number" id="principal" class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1"><?php _e('economic.labels.rate'); ?></label>
                        <input type="number" id="rate" class="w-full p-2 border rounded">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1"><?php _e('economic.labels.time'); ?></label>
                        <input type="number" id="time" class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1"><?php _e('economic.labels.periods'); ?></label>
                        <input type="number" id="periods" class="w-full p-2 border rounded">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1"><?php _e('economic.labels.payment'); ?></label>
                    <input type="number" id="payment" class="w-full p-2 border rounded">
                </div>
            </div>

            <div class="grid grid-cols-4 gap-3">
                <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="clearAll()">AC</button>
                <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="toggleSign()">+/-</button>
                <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="percentage()">%</button>
                <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-3 text-sm font-medium" onclick="operation('/')">÷</button>

                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(7)">7</button>
                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(8)">8</button>
                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(9)">9</button>
                <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-3 text-sm font-medium" onclick="operation('*')">×</button>

                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(4)">4</button>
                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(5)">5</button>
                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(6)">6</button>
                <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-3 text-sm font-medium" onclick="operation('-')">-</button>

                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(1)">1</button>
                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(2)">2</button>
                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(3)">3</button>
                <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-3 text-sm font-medium" onclick="operation('+')">+</button>

                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium col-span-2" onclick="appendNumber(0)">0</button>
                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendDecimal()"><?php _e('decimal_separator'); ?></button>
                <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-3 text-sm font-medium" onclick="calculate()">=</button>
            </div>
        </div>

        <!-- Conversion Calculator (hidden by default) -->
        <div id="conversion-calculator" class="calculator-section hidden p-4">
            <div class="mb-4">
                <div class="flex items-center mb-2">
                    <select id="conversion-type" class="conversion-select flex-1 p-2 border rounded-lg mr-2">
                        <?php foreach(__('conversion.types') as $key => $label): ?>
                        <option value="<?php echo $key; ?>"><?php echo htmlspecialchars($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button id="swap-units" class="p-2 bg-gray-200 hover:bg-gray-300 rounded-lg">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <select id="from-unit" class="conversion-select w-full p-2 border rounded-lg mb-2">
                            <!-- <?php _e('conversion.comment'); ?> -->
                        </select>
                        <input type="number" id="from-value" class="w-full p-3 border rounded-lg text-lg" value="1" oninput="convertUnits()">
                    </div>
                    <div>
                        <select id="to-unit" class="conversion-select w-full p-2 border rounded-lg mb-2">
                            <!-- <?php _e('conversion.comment'); ?> -->
                        </select>
                        <input type="number" id="to-value" class="w-full p-3 border rounded-lg text-lg" readonly>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-4 gap-3">
                <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="clearAll()">AC</button>
                <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="toggleSign()">+/-</button>
                <button class="calculator-btn bg-gray-200 hover:bg-gray-300 rounded-lg p-3 text-sm font-medium" onclick="percentage()">%</button>
                <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-3 text-sm font-medium" onclick="operation('/')">÷</button>

                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(7)">7</button>
                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(8)">8</button>
                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(9)">9</button>
                <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-3 text-sm font-medium" onclick="operation('*')">×</button>

                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(4)">4</button>
                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(5)">5</button>
                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(6)">6</button>
                <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-3 text-sm font-medium" onclick="operation('-')">-</button>

                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(1)">1</button>
                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(2)">2</button>
                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendNumber(3)">3</button>
                <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-3 text-sm font-medium" onclick="operation('+')">+</button>

                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium col-span-2" onclick="appendNumber(0)">0</button>
                <button class="calculator-btn bg-gray-100 hover:bg-gray-200 rounded-lg p-3 text-sm font-medium" onclick="appendDecimal()"><?php _e('decimal_separator'); ?></button>
                <button class="calculator-btn bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-3 text-sm font-medium" onclick="calculate()">=</button>
            </div>
        </div>

        <!-- History panel -->
        <div id="history-panel" class="hidden absolute top-0 left-0 w-full h-full bg-white bg-opacity-95 z-10 p-4 overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold"><?php _e('history.title'); ?></h2>
                <button onclick="toggleHistory()" class="p-2 rounded-full hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="history-list" class="space-y-2">
                <!-- <?php _e('history.history_comment'); ?> -->
            </div>
        </div>
    </div>

    <!-- Right Sidebar - History -->
    <div class="history-sidebar rounded-xl p-4 hidden md:block">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-history mr-2 text-blue-600"></i>
                <span><?php _e('history.sidebar_title'); ?></span>
            </h3>
            <button onclick="clearHistory()" class="text-red-500 hover:text-red-700 text-sm">
                <i class="fas fa-trash"></i>
            </button>
        </div>

        <div id="sidebar-history" class="space-y-2 max-h-96 overflow-y-auto">
            <div class="text-gray-500 text-sm text-center py-8">
                <i class="fas fa-calculator text-2xl mb-2 opacity-50"></i>
                <p><?php _e('history.no_calculations'); ?></p>
                <p class="text-xs"><?php _e('history.start_calculating'); ?></p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 pt-4 border-t border-gray-300">
            <h4 class="text-sm font-semibold text-gray-700 mb-3"><?php _e('history.quick_actions'); ?></h4>
            <div class="space-y-2">
                <button onclick="exportHistory()" class="w-full text-left text-sm text-blue-600 hover:text-blue-800 flex items-center">
                    <i class="fas fa-download mr-2"></i>
                    <span><?php _e('history.export'); ?></span>
                </button>
                <button onclick="importHistory()" class="w-full text-left text-sm text-green-600 hover:text-green-800 flex items-center">
                    <i class="fas fa-upload mr-2"></i>
                    <span><?php _e('history.import'); ?></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Bottom SEO Section -->
    <div class="col-span-full seo-footer rounded-xl p-6">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2"><?php _e('seo.main_title'); ?></h2>
            <p class="text-gray-600"><?php _e('seo.main_subtitle'); ?></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
            <div>
                <h3 class="font-semibold text-gray-800 mb-3"><?php _e('seo.features.title'); ?></h3>
                <ul class="space-y-1 text-gray-600">
                    <?php foreach(__('seo.features.items') as $item): ?>
                    <li>• <?php echo htmlspecialchars($item); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 mb-3"><?php _e('seo.operations.title'); ?></h3>
                <ul class="space-y-1 text-gray-600">
                    <?php foreach(__('seo.operations.items') as $item): ?>
                    <li>• <?php echo htmlspecialchars($item); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 mb-3"><?php _e('seo.benefits.title'); ?></h3>
                <ul class="space-y-1 text-gray-600">
                    <?php foreach(__('seo.benefits.items') as $item): ?>
                    <li>• <?php echo htmlspecialchars($item); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-gray-300 text-center text-xs text-gray-500">
            <p>&copy; <span id="current-year"></span> <?php _e('seo.footer.copyright'); ?></p>
            <p>
                <a href="#" class="hover:text-gray-700"><?php _e('seo.footer.privacy'); ?></a> |
                <a href="#" class="hover:text-gray-700"><?php _e('seo.footer.terms'); ?></a> |
                <a href="contact.html" class="hover:text-gray-700"><?php _e('seo.footer.contact'); ?></a>
            </p>
        </div>
    </div>
</div>

<script>
document.getElementById('current-year').textContent = new Date().getFullYear();
</script>
<script src="<?php echo $scriptsPath; ?>common.js"></script>
<script src="<?php echo $scriptsPath; ?>normal.js"></script>
<script src="<?php echo $scriptsPath; ?>scientific.js"></script>
<script src="<?php echo $scriptsPath; ?>economic.js"></script>
<script src="<?php echo $scriptsPath; ?>conversion.js"></script>
<?php include $includesPath . 'country-selector-script.php'; ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-N8S3ZX8V"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
</body>
</html>

