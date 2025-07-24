// Calculator state
let currentInput = '0';
let previousInput = '';
let currentOperation = null;
let resetInput = false;
let calculationHistory = [];
let isDegreeMode = true; // For scientific calculator

// DOM elements
const display = document.getElementById('display');
const historyElement = document.getElementById('history');
const calculatorSections = document.querySelectorAll('.calculator-section');
const modeButtons = document.querySelectorAll('.mode-btn');
const sidebarHistory = document.getElementById('sidebar-history');

// Conversion units data
const conversionUnits = {
    length: [
        { name: 'Meter', value: 1 },
        { name: 'Kilometer', value: 1000 },
        { name: 'Centimeter', value: 0.01 },
        { name: 'Millimeter', value: 0.001 },
        { name: 'Mile', value: 1609.34 },
        { name: 'Yard', value: 0.9144 },
        { name: 'Foot', value: 0.3048 },
        { name: 'Inch', value: 0.0254 }
    ],
    weight: [
        { name: 'Gram', value: 1 },
        { name: 'Kilogram', value: 1000 },
        { name: 'Milligram', value: 0.001 },
        { name: 'Pound', value: 453.592 },
        { name: 'Ounce', value: 28.3495 },
        { name: 'Ton', value: 1000000 }
    ],
    temperature: [
        { name: 'Celsius', value: 'celsius' },
        { name: 'Fahrenheit', value: 'fahrenheit' },
        { name: 'Kelvin', value: 'kelvin' }
    ],
    area: [
        { name: 'Square Meter', value: 1 },
        { name: 'Square Kilometer', value: 1000000 },
        { name: 'Square Mile', value: 2589988.11 },
        { name: 'Square Yard', value: 0.836127 },
        { name: 'Square Foot', value: 0.092903 },
        { name: 'Square Inch', value: 0.00064516 },
        { name: 'Hectare', value: 10000 },
        { name: 'Acre', value: 4046.86 }
    ],
    volume: [
        { name: 'Liter', value: 1 },
        { name: 'Milliliter', value: 0.001 },
        { name: 'Cubic Meter', value: 1000 },
        { name: 'Gallon (US)', value: 3.78541 },
        { name: 'Quart (US)', value: 0.946353 },
        { name: 'Pint (US)', value: 0.473176 },
        { name: 'Cup (US)', value: 0.236588 },
        { name: 'Fluid Ounce (US)', value: 0.0295735 }
    ],
    speed: [
        { name: 'Meter per second', value: 1 },
        { name: 'Kilometer per hour', value: 0.277778 },
        { name: 'Mile per hour', value: 0.44704 },
        { name: 'Knot', value: 0.514444 },
        { name: 'Foot per second', value: 0.3048 }
    ],
    time: [
        { name: 'Second', value: 1 },
        { name: 'Millisecond', value: 0.001 },
        { name: 'Minute', value: 60 },
        { name: 'Hour', value: 3600 },
        { name: 'Day', value: 86400 },
        { name: 'Week', value: 604800 },
        { name: 'Month', value: 2628000 },
        { name: 'Year', value: 31536000 }
    ],
    currency: [
        { name: 'US Dollar', value: 1 },
        { name: 'Euro', value: 1.18 },
        { name: 'British Pound', value: 1.38 },
        { name: 'Japanese Yen', value: 0.0091 },
        { name: 'Canadian Dollar', value: 0.79 },
        { name: 'Australian Dollar', value: 0.74 }
    ]
};

// Initialize the calculator
function init() {
    updateDisplay();
    setupModeSwitching();
    setupConversionCalculator();

    // Keyboard support
    document.addEventListener('keydown', handleKeyboardInput);
}

// Set up mode switching
function setupModeSwitching() {
    modeButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons
            modeButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            button.classList.add('active');

            // Hide all calculator sections
            calculatorSections.forEach(section => section.classList.add('hidden'));

            // Get display area and calculator buttons area
            const displayArea = document.querySelector('.p-4.bg-gray-50');
            const normalCalculatorButtons = document.getElementById('normal-calculator');

            // Show the selected calculator
            const calculatorId = button.id.replace('-mode', '-calculator');
            const selectedCalculator = document.getElementById(calculatorId);
            selectedCalculator.classList.remove('hidden');

            // Special handling for conversion calculator
            if (calculatorId === 'conversion-calculator') {
                // Hide display area and normal calculator buttons for conversion mode
                displayArea.classList.add('hidden');
            } else {
                // Show display area for other modes
                displayArea.classList.remove('hidden');
            }
        });
    });
}

// Set up conversion calculator
function setupConversionCalculator() {
    const conversionType = document.getElementById('conversion-type');
    const fromUnit = document.getElementById('from-unit');
    const toUnit = document.getElementById('to-unit');
    const swapButton = document.getElementById('swap-units');

    // Populate units when conversion type changes
    conversionType.addEventListener('change', () => {
        const type = conversionType.value;
        const units = conversionUnits[type];

        // Clear existing options
        fromUnit.innerHTML = '';
        toUnit.innerHTML = '';

        // Add new options
        units.forEach(unit => {
            const option1 = document.createElement('option');
            option1.value = unit.value;
            option1.textContent = unit.name;
            fromUnit.appendChild(option1);

            const option2 = document.createElement('option');
            option2.value = unit.value;
            option2.textContent = unit.name;
            toUnit.appendChild(option2);
        });

        // Set default selection (first unit for 'from', second unit for 'to' if available)
        if (units.length > 1) {
            toUnit.selectedIndex = 1;
        }

        // Trigger conversion
        convertUnits();
    });

    // Swap units
    swapButton.addEventListener('click', () => {
        const temp = fromUnit.value;
        fromUnit.value = toUnit.value;
        toUnit.value = temp;
        convertUnits();
    });

    // Convert when units change
    fromUnit.addEventListener('change', convertUnits);
    toUnit.addEventListener('change', convertUnits);

    // Initialize with first type
    conversionType.dispatchEvent(new Event('change'));
}

// Convert units
function convertUnits() {
    const type = document.getElementById('conversion-type').value;
    const fromValue = parseFloat(document.getElementById('from-value').value) || 0;
    const fromUnit = document.getElementById('from-unit').value;
    const toUnit = document.getElementById('to-unit').value;
    let result;

    if (type === 'temperature') {
        // Temperature conversion is special
        result = convertTemperature(fromValue, fromUnit, toUnit);
    } else {
        // Standard conversion for other types
        const fromFactor = parseFloat(fromUnit);
        const toFactor = parseFloat(toUnit);
        result = (fromValue * fromFactor) / toFactor;
    }

    document.getElementById('to-value').value = result.toFixed(6).replace(/\.?0+$/, '');
}

// Convert temperature between units
function convertTemperature(value, fromUnit, toUnit) {
    let celsius;

    // Convert to Celsius first
    switch (fromUnit) {
        case 'celsius':
            celsius = value;
            break;
        case 'fahrenheit':
            celsius = (value - 32) * 5/9;
            break;
        case 'kelvin':
            celsius = value - 273.15;
            break;
    }

    // Convert from Celsius to target unit
    switch (toUnit) {
        case 'celsius':
            return celsius;
        case 'fahrenheit':
            return (celsius * 9/5) + 32;
        case 'kelvin':
            return celsius + 273.15;
    }

    return 0;
}

// Update the display
function updateDisplay() {
    display.textContent = currentInput;
}

// Append a number to the current input
function appendNumber(number) {
    if (currentInput === '0' || resetInput) {
        currentInput = number.toString();
        resetInput = false;
    } else {
        currentInput += number.toString();
    }
    updateDisplay();
}

// Append a decimal point
function appendDecimal() {
    if (resetInput) {
        currentInput = '0.';
        resetInput = false;
    } else if (!currentInput.includes('.')) {
        currentInput += '.';
    }
    updateDisplay();
}

// Set the operation
function operation(op) {
    if (currentInput === '0' && op === '-') {
        currentInput = '-';
        updateDisplay();
        return;
    }

    if (currentOperation !== null) calculate();
    previousInput = currentInput;
    currentOperation = op;
    resetInput = true;
    historyElement.textContent = `${previousInput} ${currentOperation}`;
}

// Calculate the result
function calculate() {
    let result;
    const prev = parseFloat(previousInput);
    const current = parseFloat(currentInput);

    if (isNaN(prev) || isNaN(current)) return;

    switch (currentOperation) {
        case '+':
            result = prev + current;
            break;
        case '-':
            result = prev - current;
            break;
        case '*':
            result = prev * current;
            break;
        case '/':
            result = prev / current;
            break;
        default:
            return;
    }

    // Add to history
    const historyEntry = `${previousInput} ${currentOperation} ${currentInput} = ${result}`;
    calculationHistory.unshift(historyEntry);
    if (calculationHistory.length > 50) calculationHistory.pop(); // Keep max 50 items

    currentInput = result.toString();
    currentOperation = null;
    previousInput = '';
    resetInput = true;

    updateDisplay();
    updateSidebarHistory(); // Update sidebar
    historyElement.textContent = historyEntry;
}

// Clear all
function clearAll() {
    currentInput = '0';
    previousInput = '';
    currentOperation = null;
    resetInput = false;
    updateDisplay();
    historyElement.textContent = '';
}

// Toggle sign
function toggleSign() {
    currentInput = (parseFloat(currentInput) * -1).toString();
    updateDisplay();
}

// Percentage
function percentage() {
    currentInput = (parseFloat(currentInput) / 100).toString();
    updateDisplay();
}

// Scientific operations
function scientificOperation(func) {
    let result;
    const value = parseFloat(currentInput);

    switch (func) {
        case 'sin':
            result = isDegreeMode ? Math.sin(value * Math.PI / 180) : Math.sin(value);
            break;
        case 'cos':
            result = isDegreeMode ? Math.cos(value * Math.PI / 180) : Math.cos(value);
            break;
        case 'tan':
            result = isDegreeMode ? Math.tan(value * Math.PI / 180) : Math.tan(value);
            break;
        case 'asin':
            result = isDegreeMode ? Math.asin(value) * 180 / Math.PI : Math.asin(value);
            break;
        case 'acos':
            result = isDegreeMode ? Math.acos(value) * 180 / Math.PI : Math.acos(value);
            break;
        case 'atan':
            result = isDegreeMode ? Math.atan(value) * 180 / Math.PI : Math.atan(value);
            break;
        case 'log':
            result = Math.log10(value);
            break;
        case 'ln':
            result = Math.log(value);
            break;
        case 'sqrt':
            result = Math.sqrt(value);
            break;
        case 'pow':
            previousInput = currentInput;
            currentOperation = '^';
            resetInput = true;
            historyElement.textContent = `${previousInput} ${currentOperation}`;
            return;
        case 'fact':
            result = factorial(value);
            break;
        case 'pi':
            result = Math.PI;
            break;
        case 'e':
            result = Math.E;
            break;
        case 'exp':
            previousInput = currentInput;
            currentOperation = 'e^';
            resetInput = true;
            historyElement.textContent = `${previousInput} ${currentOperation}`;
            return;
        case 'mod':
            previousInput = currentInput;
            currentOperation = 'mod';
            resetInput = true;
            historyElement.textContent = `${previousInput} ${currentOperation}`;
            return;
        case '(':
        case ')':
            // For future implementation of expression parsing
            return;
        case 'deg':
            isDegreeMode = true;
            return;
        case 'rad':
            isDegreeMode = false;
            return;
        default:
            return;
    }

    currentInput = result.toString();
    updateDisplay();
}

// Factorial function
function factorial(n) {
    if (n < 0) return NaN;
    if (n === 0 || n === 1) return 1;
    let result = 1;
    for (let i = 2; i <= n; i++) {
        result *= i;
    }
    return result;
}

// Economic operations
function economicOperation(func) {
    const principal = parseFloat(document.getElementById('principal').value) || 0;
    const rate = parseFloat(document.getElementById('rate').value) || 0;
    const time = parseFloat(document.getElementById('time').value) || 0;
    const periods = parseFloat(document.getElementById('periods').value) || 1;
    const payment = parseFloat(document.getElementById('payment').value) || 0;
    let result;

    switch (func) {
        case 'fv': // Future Value
            if (payment > 0) {
                // Future value of an annuity
                const r = rate / 100 / periods;
                const n = time * periods;
                result = payment * ((Math.pow(1 + r, n) - 1) / r);
            } else {
                // Future value of a single sum
                result = principal * Math.pow(1 + (rate / 100 / periods), time * periods);
            }
            break;
        case 'pv': // Present Value
            if (payment > 0) {
                // Present value of an annuity
                const r = rate / 100 / periods;
                const n = time * periods;
                result = payment * ((1 - Math.pow(1 + r, -n)) / r);
            } else {
                // Present value of a single sum
                result = principal / Math.pow(1 + (rate / 100 / periods), time * periods);
            }
            break;
        case 'pmt': // Payment
            const r = rate / 100 / periods;
            const n = time * periods;
            result = principal * (r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1);
            break;
        case 'npv': // Net Present Value (simplified)
            // For simplicity, we'll just calculate PV of a single payment
            result = payment / Math.pow(1 + (rate / 100), time);
            break;
        case 'irr': // Internal Rate of Return (simplified)
            // Simplified calculation (not actual IRR algorithm)
            result = (Math.pow(payment / principal, 1 / time) - 1) * 100;
            break;
        case 'roi': // Return on Investment
            result = ((payment - principal) / principal) * 100;
            break;
        default:
            return;
    }

    currentInput = result.toString();
    updateDisplay();

    // Add to history
    const historyEntry = `${func.toUpperCase()}: ${result}`;
    calculationHistory.unshift(historyEntry);
    if (calculationHistory.length > 50) calculationHistory.pop();
    updateSidebarHistory(); // Update sidebar
    historyElement.textContent = historyEntry;
}

// Toggle history panel
function toggleHistory() {
    const historyPanel = document.getElementById('history-panel');
    const historyList = document.getElementById('history-list');

    if (historyPanel.classList.contains('hidden')) {
        // Show history
        historyList.innerHTML = '';
        calculationHistory.forEach(item => {
            const div = document.createElement('div');
            div.className = 'history-item p-2 rounded hover:bg-gray-100';
            div.textContent = item;
            div.onclick = () => {
                // When a history item is clicked, use its result
                const parts = item.split(' = ');
                if (parts.length > 1) {
                    currentInput = parts[parts.length - 1];
                    updateDisplay();
                }
                toggleHistory();
            };
            historyList.appendChild(div);
        });

        historyPanel.classList.remove('hidden');
    } else {
        // Hide history
        historyPanel.classList.add('hidden');
    }
}

// Update sidebar history display
function updateSidebarHistory() {
    if (!sidebarHistory) return;

    if (calculationHistory.length === 0) {
        sidebarHistory.innerHTML = `
            <div class="text-gray-500 text-sm text-center py-8">
                <i class="fas fa-calculator text-2xl mb-2 opacity-50"></i>
                <p>No calculations yet</p>
                <p class="text-xs">Start calculating to see history</p>
            </div>
        `;
        return;
    }

    sidebarHistory.innerHTML = '';
    calculationHistory.slice(0, 10).forEach((item, index) => {
        const div = document.createElement('div');
        div.className = 'history-item p-2 rounded hover:bg-gray-200 cursor-pointer text-sm border border-gray-200 mb-2 transition-colors';
        div.innerHTML = `
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">#${calculationHistory.length - index}</span>
                <button onclick="removeHistoryItem(${index})" class="text-red-400 hover:text-red-600 text-xs">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="text-gray-800 font-mono text-xs break-all">${item}</div>
        `;
        div.onclick = (e) => {
            if (e.target.tagName !== 'BUTTON' && !e.target.classList.contains('fas')) {
                const parts = item.split(' = ');
                if (parts.length > 1) {
                    currentInput = parts[parts.length - 1];
                    updateDisplay();
                }
            }
        };
        sidebarHistory.appendChild(div);
    });
}

// Remove specific history item
function removeHistoryItem(index) {
    calculationHistory.splice(index, 1);
    updateSidebarHistory();
}

// Clear all history
function clearHistory() {
    if (confirm('Are you sure you want to clear all calculation history?')) {
        calculationHistory = [];
        updateSidebarHistory();
        historyElement.textContent = '';
    }
}

// Export history functionality
function exportHistory() {
    if (calculationHistory.length === 0) {
        alert('No history to export!');
        return;
    }

    const historyText = calculationHistory.join('\n');
    const blob = new Blob([historyText], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `calculator-history-${new Date().toISOString().split('T')[0]}.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// Import history functionality
function importHistory() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.txt';
    input.onchange = (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                try {
                    const content = e.target.result;
                    const importedHistory = content.split('\n').filter(line => line.trim());
                    if (confirm(`Import ${importedHistory.length} calculation(s)? This will add to your existing history.`)) {
                        calculationHistory = [...importedHistory, ...calculationHistory];
                        updateSidebarHistory();
                    }
                } catch (error) {
                    alert('Error reading file. Please make sure it\'s a valid text file.');
                }
            };
            reader.readAsText(file);
        }
    };
    input.click();
}

// Handle keyboard input
function handleKeyboardInput(e) {
    if (e.key >= '0' && e.key <= '9') {
        appendNumber(parseInt(e.key));
    } else if (e.key === '.') {
        appendDecimal();
    } else if (e.key === '+' || e.key === '-' || e.key === '*' || e.key === '/') {
        operation(e.key);
    } else if (e.key === 'Enter' || e.key === '=') {
        calculate();
    } else if (e.key === 'Escape') {
        clearAll();
    } else if (e.key === '%') {
        percentage();
    }
}

// Initialize the calculator when the page loads
window.onload = function() {
    init();
    updateSidebarHistory(); // Initialize sidebar history
};
