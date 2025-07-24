// Scientific calculator functionality

// Scientific calculator specific state
let isDegreeMode = true; // For scientific calculator

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