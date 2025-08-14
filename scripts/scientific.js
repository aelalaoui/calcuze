// Scientific calculator functionality with expression-based calculation

// Scientific calculator specific state
let isDegreeMode = true;
let scientificExpression = '';
let isScientificMode = false;

// Initialize scientific mode
function initScientificMode() {
    isScientificMode = true;
    scientificExpression = '';
    updateScientificDisplay();
}

// Exit scientific mode
function exitScientificMode() {
    isScientificMode = false;
    scientificExpression = '';
}

// Update scientific display with visual parentheses handling
function updateScientificDisplay() {
    if (!isScientificMode) return;
    
    const historyElement = document.getElementById('history');
    const display = document.getElementById('display');
    
    let displayExpression = '';
    let openCount = 0;
    
    for (let i = 0; i < scientificExpression.length; i++) {
        const char = scientificExpression[i];
        if (char === '(') {
            displayExpression += `<span style="color: #4ade80; font-weight: bold;">(</span>`;
            openCount++;
        } else if (char === ')') {
            displayExpression += `<span style="color: #f87171; font-weight: bold;">)</span>`;
            openCount = Math.max(0, openCount - 1);
        } else {
            displayExpression += char;
        }
    }
    
    // Add visual indicators for unmatched parentheses
    if (openCount > 0) {
        displayExpression += `<span style="color: #fbbf24;"> [${openCount} unmatched]</span>`;
    }
    
    historyElement.innerHTML = displayExpression;
    
    // Show current expression or 0 if empty
    if (scientificExpression === '') {
        display.textContent = '0';
    } else {
        display.textContent = scientificExpression;
    }
}

// Append character to scientific expression
function appendScientificChar(char) {
    if (!isScientificMode) return;
    
    scientificExpression += char;
    updateScientificDisplay();
}

// Scientific backspace
function scientificBackspace() {
    if (!isScientificMode) return;
    
    scientificExpression = scientificExpression.slice(0, -1);
    updateScientificDisplay();
}

// Clear scientific calculator
function clearScientific() {
    if (!isScientificMode) return;
    
    scientificExpression = '';
    updateScientificDisplay();
}

// Calculate scientific expression
function calculateScientific() {
    if (!isScientificMode || scientificExpression === '') return;
    
    try {
        // Replace display symbols with calculation symbols
        let calcExpression = scientificExpression
            .replace(/π/g, 'Math.PI')
            .replace(/e/g, 'Math.E')
            .replace(/√/g, 'Math.sqrt')
            .replace(/sin/g, 'Math.sin')
            .replace(/cos/g, 'Math.cos')
            .replace(/tan/g, 'Math.tan')
            .replace(/asin/g, 'Math.asin')
            .replace(/acos/g, 'Math.acos')
            .replace(/atan/g, 'Math.atan')
            .replace(/log/g, 'Math.log10')
            .replace(/ln/g, 'Math.log')
            .replace(/\^/g, '**')
            .replace(/×/g, '*')
            .replace(/÷/g, '/')
            .replace(/−/g, '-');
        
        // Handle factorial
        calcExpression = calcExpression.replace(/(\d+)!/g, function(match, num) {
            return factorial(parseInt(num));
        });
        
        // Convert degrees to radians for trigonometric functions if in degree mode
        if (isDegreeMode) {
            calcExpression = calcExpression.replace(/Math\.(sin|cos|tan)\(/g, function(match, func) {
                return `Math.${func}((Math.PI/180)*`;
            });
        }
        
        // Evaluate the expression
        const result = eval(calcExpression);
        
        // Format result
        if (isNaN(result) || !isFinite(result)) {
            document.getElementById('display').textContent = 'Error';
        } else {
            const formattedResult = parseFloat(result.toFixed(10)).toString();
            document.getElementById('display').textContent = formattedResult;
            
            // Add to history
            addToHistory(`${scientificExpression} = ${formattedResult}`);
        }
    } catch (error) {
        document.getElementById('display').textContent = 'Error';
    }
}

// Scientific operations for button clicks
function scientificOperation(func) {
    if (!isScientificMode) {
        // Fallback to old behavior for non-scientific modes
        return scientificOperationLegacy(func);
    }
    
    switch (func) {
        case 'sin':
            appendScientificChar('sin(');
            break;
        case 'cos':
            appendScientificChar('cos(');
            break;
        case 'tan':
            appendScientificChar('tan(');
            break;
        case 'asin':
            appendScientificChar('asin(');
            break;
        case 'acos':
            appendScientificChar('acos(');
            break;
        case 'atan':
            appendScientificChar('atan(');
            break;
        case 'log':
            appendScientificChar('log(');
            break;
        case 'ln':
            appendScientificChar('ln(');
            break;
        case 'sqrt':
            appendScientificChar('√(');
            break;
        case 'pow':
            appendScientificChar('^');
            break;
        case 'fact':
            appendScientificChar('!');
            break;
        case 'pi':
            appendScientificChar('π');
            break;
        case 'e':
            appendScientificChar('e');
            break;
        case 'exp':
            appendScientificChar('e^');
            break;
        case 'mod':
            appendScientificChar('%');
            break;
        case '(':
            appendScientificChar('(');
            break;
        case ')':
            appendScientificChar(')');
            break;
        case 'deg':
            isDegreeMode = true;
            document.querySelector('[onclick="scientificOperation(\'deg\')"]').style.backgroundColor = '#3b82f6';
            document.querySelector('[onclick="scientificOperation(\'rad\')"]').style.backgroundColor = '';
            break;
        case 'rad':
            isDegreeMode = false;
            document.querySelector('[onclick="scientificOperation(\'rad\')"]').style.backgroundColor = '#3b82f6';
            document.querySelector('[onclick="scientificOperation(\'deg\')"]').style.backgroundColor = '';
            break;
        default:
            break;
    }

    updateDisplay();
}

// Legacy scientific operations for backward compatibility
function scientificOperationLegacy(func) {
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

// Override number input for scientific mode
function appendNumberScientific(number) {
    if (isScientificMode) {
        appendScientificChar(number.toString());
    } else {
        appendNumber(number);
    }
}

// Override decimal input for scientific mode
function appendDecimalScientific() {
    if (isScientificMode) {
        appendScientificChar('.');
    } else {
        appendDecimal();
    }
}

// Override operation input for scientific mode
function operationScientific(op) {
    if (isScientificMode) {
        let symbol = op;
        switch (op) {
            case '*':
                symbol = '×';
                break;
            case '/':
                symbol = '÷';
                break;
            case '-':
                symbol = '−';
                break;
            case '+':
                symbol = '+';
                break;
        }
        appendScientificChar(symbol);
    } else {
        operation(op);
    }
    updateDisplay();
}

// Override clear for scientific mode
function clearAllScientific() {
    if (isScientificMode) {
        clearScientific();
    } else {
        clearAll();
    }
}

// Override calculate for scientific mode
function calculateScientificMode() {
    if (isScientificMode) {
        calculateScientific();
    } else {
        calculate();
    }
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