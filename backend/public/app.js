document.addEventListener('DOMContentLoaded', () => {
    const WIDGET_CONFIG = {
        currencies: ['USD', 'EUR', 'CNY'], 
        updateInterval: 30000 
    };

    const widget = document.getElementById('currency-widget');
    const intervalDisplay = document.getElementById('interval-display');
    intervalDisplay.textContent = WIDGET_CONFIG.updateInterval / 1000;

    async function fetchRates() {
        try {
            const response = await fetch('/api/rates');
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const data = await response.json();
            renderWidget(data);
        } catch (error) {
            widget.innerHTML = '<p>Не удалось загрузить курсы.</p>';
            console.error('Fetch error:', error);
        }
    }

    function renderWidget(rates) {
        widget.innerHTML = ''; 
        
        const filteredRates = rates.filter(rate => WIDGET_CONFIG.currencies.includes(rate.char_code));

        filteredRates.forEach(rate => {
            const diff = parseFloat(rate.diff);
            let diffClass = 'diff-none';
            let diffSymbol = '●';

            if (diff > 0) {
                diffClass = 'diff-up';
                diffSymbol = '▲';
            } else if (diff < 0) {
                diffClass = 'diff-down';
                diffSymbol = '▼';
            }

            const item = document.createElement('div');
            item.className = 'currency-item';
            item.innerHTML = `
                <div class="currency-name">${rate.char_code} (${rate.name})</div>
                <div class="currency-value">
                    <div class="rate">${rate.rate} ₽</div>
                    <div class="diff ${diffClass}">${diffSymbol} ${Math.abs(diff).toFixed(4)}</div>
                </div>
            `;
            widget.appendChild(item);
        });
    }

    fetchRates(); 
    setInterval(fetchRates, WIDGET_CONFIG.updateInterval);
});