// Economic calculator functionality

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