// Conversion calculator functionality

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

// Add initialization to the main init function
const originalInit = init;
init = function() {
    originalInit();
    setupConversionCalculator();
};