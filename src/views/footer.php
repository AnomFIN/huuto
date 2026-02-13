    </main>

    <footer class="bg-gray-800 text-white mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Huuto</h3>
                    <p class="text-gray-300">Suomalainen verkkohuutokauppa-alusta</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Linkit</h3>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-gray-300 hover:text-white">Etusivu</a></li>
                        <li><a href="/auctions.php" class="text-gray-300 hover:text-white">Kohteet</a></li>
                        <li><a href="/categories.php" class="text-gray-300 hover:text-white">Kategoriat</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Tietoa</h3>
                    <p class="text-gray-300">© 2026 Huuto. Kaikki oikeudet pidätetään.</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Countdown timer functionality
        function updateCountdowns() {
            document.querySelectorAll('.countdown').forEach(element => {
                const rawEndTime = element.dataset.endtime;

                let endTimeMs = NaN;

                if (rawEndTime) {
                    // If the value is numeric, treat it as epoch milliseconds.
                    if (/^\d+$/.test(rawEndTime.trim())) {
                        endTimeMs = parseInt(rawEndTime.trim(), 10);
                    } else {
                        // Normalize common "YYYY-MM-DD HH:MM:SS" format to ISO-8601.
                        let normalized = rawEndTime.trim();
                        if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(normalized)) {
                            normalized = normalized.replace(' ', 'T') + 'Z';
                        }
                        const parsed = Date.parse(normalized);
                        if (!isNaN(parsed)) {
                            endTimeMs = parsed;
                        }
                    }
                }

                if (isNaN(endTimeMs)) {
                    element.innerHTML = "Päättynyt";
                    return;
                }

                const now = Date.now();
                const distance = endTimeMs - now;
                if (distance < 0) {
                    element.innerHTML = "Päättynyt";
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                if (days > 0) {
                    element.innerHTML = `${days}pv ${hours}h`;
                } else if (hours > 0) {
                    element.innerHTML = `${hours}h ${minutes}min`;
                } else {
                    element.innerHTML = `${minutes}min ${seconds}s`;
                }
            });
        }

        // Update countdowns every second
        setInterval(updateCountdowns, 1000);
        updateCountdowns();
    </script>
</body>
</html>
