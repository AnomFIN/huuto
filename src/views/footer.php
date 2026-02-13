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
                const endTime = new Date(element.dataset.endtime).getTime();
                const now = new Date().getTime();
                const distance = endTime - now;

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
