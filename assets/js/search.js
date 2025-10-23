const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');

if (searchInput) {
    searchInput.addEventListener('keyup', function () {
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';
            return;
        }

        fetch(`../includes/search_suggestions.php?term=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    searchResults.innerHTML = '';
                    searchResults.style.display = 'none';
                    return;
                }

                 data = data.slice(0, 10);

                // Keep same layout — show simple text suggestions below input
                searchResults.innerHTML = data.map(item => `
                    <div class="search-suggestion" data-name="${item.name}">
                        ${item.name}
                    </div>
                `).join('');

                searchResults.style.display = 'block';

                // Click event: autofill the input with selected suggestion
                document.querySelectorAll('.search-suggestion').forEach(el => {
                    el.addEventListener('click', () => {
                        searchInput.value = el.dataset.name;
                        searchResults.innerHTML = '';
                        searchResults.style.display = 'none';
                        // Auto-submit form
                        searchInput.closest('form').submit();
                    });
                });
            })
            .catch(err => console.error(err));
    });

    // Hide when clicking outside
    document.addEventListener('click', (e) => {
        if (!searchResults.contains(e.target) && e.target !== searchInput) {
            searchResults.style.display = 'none';
        }
    });
}
