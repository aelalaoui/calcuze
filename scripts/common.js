// Calculator state
let currentInput = '0';
let previousInput = '';
let currentOperation = null;
let resetInput = false;
let calculationHistory = [];

// DOM elements
const display = document.getElementById('display');
const historyElement = document.getElementById('history');
const calculatorSections = document.querySelectorAll('.calculator-section');
const modeButtons = document.querySelectorAll('.mode-btn');
const sidebarHistory = document.getElementById('sidebar-history');

// Initialize the calculator
function init() {
    updateDisplay();
    setupModeSwitching();

    // Keyboard support
    document.addEventListener('keydown', handleKeyboardInput);
}

// Initialize the calculator when the page loads
window.onload = function() {
    init();
    updateSidebarHistory(); // Initialize sidebar history
};

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

            // Show the selected calculator
            const calculatorId = button.id.replace('-mode', '-calculator');
            const selectedCalculator = document.getElementById(calculatorId);
            selectedCalculator.classList.remove('hidden');

            // Handle scientific mode initialization
            if (calculatorId === 'scientific-calculator') {
                initScientificMode();
                displayArea.classList.remove('hidden');
            } else {
                exitScientificMode();
                // Special handling for conversion calculator
                if (calculatorId === 'conversion-calculator') {
                    // Hide display area and normal calculator buttons for conversion mode
                    displayArea.classList.add('hidden');
                } else {
                    // Show display area for other modes
                    displayArea.classList.remove('hidden');
                }
            }
        });
    });
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
    if (calculationHistory.length > 50) calculationHistory.pop();

    currentInput = result.toString();
    currentOperation = null;
    previousInput = '';
    resetInput = true;

    updateDisplay();
    updateSidebarHistory();
    historyElement.textContent = historyEntry;
}

// Clear all
function clearAll() {
    currentInput = '0';
    previousInput = '';
    operator = '';
    
    justCalculated = false;
    document.getElementById('display').textContent = '0';
    document.getElementById('history').textContent = '';
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

// Backspace function to delete last character
function backspace() {
    // If resetInput is true, it means the last action was an operation
    // So we should cancel the operation and restore the previous state
    if (resetInput && currentOperation !== null) {
        // Cancel the operation
        currentInput = previousInput;
        previousInput = '';
        currentOperation = null;
        resetInput = false;
        historyElement.textContent = '';
        updateDisplay();
    } else {
        // Normal backspace: remove last character from current input
        if (currentInput.length > 1) {
            currentInput = currentInput.slice(0, -1);
        } else {
            currentInput = '0';
        }
        updateDisplay();
    }
}

// Handle keyboard input
function handleKeyboardInput(e) {
    // Prevent default action for Enter key to avoid clicking focused buttons
    if (e.key === 'Enter') {
        e.preventDefault();
    }
    
    if (e.key >= '0' && e.key <= '9') {
        if (isScientificMode) {
            appendNumberScientific(parseInt(e.key));
        } else {
            appendNumber(parseInt(e.key));
        }
    } else if (e.key === '.') {
        if (isScientificMode) {
            appendDecimalScientific();
        } else {
            appendDecimal();
        }
    } else if (e.key === '+' || e.key === '-' || e.key === '*' || e.key === '/') {
        if (isScientificMode) {
            operationScientific(e.key);
        } else {
            operation(e.key);
        }
    } else if (e.key === 'Enter' || e.key === '=') {
        if (isScientificMode) {
            calculateScientific();
        } else {
            calculate();
        }
    } else if (e.key === 'Escape') {
        if (isScientificMode) {
            clearScientific();
        } else {
            clearAll();
        }
    } else if (e.key === '%') {
        percentage();
    } else if (e.key === 'Backspace') {
        if (isScientificMode) {
            scientificBackspace();
        } else {
            backspace();
        }
    } else if (e.key === 'Delete') {
        if (isScientificMode) {
            clearScientific();
        } else {
            clearAll();
        }
    }
}

// Add calculation to history
function addToHistory(calculation) {
    calculationHistory.unshift(calculation);
    if (calculationHistory.length > 50) calculationHistory.pop(); // Keep max 50 items
    updateSidebarHistory();
}