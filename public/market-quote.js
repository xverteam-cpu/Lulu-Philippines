(() => {
  const tickers = Array.from(document.querySelectorAll('[data-market-quote-url]'));
  if (tickers.length === 0) return;

  const formatter = new Intl.DateTimeFormat('en-GB', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hourCycle: 'h23',
    timeZone: 'Asia/Dubai'
  });
  const endpoints = new Map();

  tickers.forEach((ticker) => {
    const endpoint = ticker.dataset.marketQuoteUrl;
    if (!endpoints.has(endpoint)) endpoints.set(endpoint, []);
    endpoints.get(endpoint).push(ticker);
  });

  async function updateQuote(endpoint, targets) {
    if (document.visibilityState !== 'visible') return;

    try {
      const response = await fetch(endpoint, {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin'
      });
      if (!response.ok) throw new Error('Market quote request failed with status ' + response.status);

      const quote = await response.json();
      const values = {
        price: Number(quote.price).toFixed(2),
        change: Number(quote.change).toFixed(2) + ' / ' + Number(quote.change_percent).toFixed(2) + '%',
        volume: Number(quote.volume).toLocaleString('en-US'),
        timestamp: formatter.format(new Date(Number(quote.timestamp) * 1000))
      };

      targets.forEach((ticker) => {
        Object.entries(values).forEach(([field, value]) => {
          ticker.querySelectorAll('[data-market-quote="' + field + '"]').forEach((element) => {
            element.textContent = value;
          });
        });

        ticker.querySelectorAll('[data-market-quote="change"]').forEach((element) => {
          element.classList.toggle('ticker-negative', Number(quote.change) < 0);
          element.classList.toggle('ticker-positive', Number(quote.change) >= 0);
        });
      });
    } catch (error) {
      console.error(error);
    }
  }

  function updateQuotes() {
    endpoints.forEach((targets, endpoint) => updateQuote(endpoint, targets));
  }

  updateQuotes();
  window.setInterval(updateQuotes, 60000);
  document.addEventListener('visibilitychange', updateQuotes);
})();
