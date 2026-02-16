// Security-first. Creator-ready. Future-proof.
(() => {
    const imageInput = document.getElementById('imageInput');
    const gallery = document.getElementById('imageGallery');
    const emptyState = document.getElementById('emptyImageState');
    const errorEl = document.getElementById('imageUploadError');

    if (!imageInput || !gallery) {
        return;
    }

    const auctionId = Number.parseInt(gallery.dataset.auctionId || '0', 10);
    const MAX_IMAGES_PER_AUCTION = 8;

    const showError = (message) => {
        if (!errorEl) {
            alert(message);
            return;
        }
        errorEl.textContent = message;
        errorEl.classList.remove('hidden');
    };

    const clearError = () => {
        if (!errorEl) {
            return;
        }
        errorEl.textContent = '';
        errorEl.classList.add('hidden');
    };

    const toggleEmptyState = () => {
        if (!emptyState) {
            return;
        }
        const hasImages = gallery.querySelectorAll('.auction-image-item').length > 0;
        emptyState.classList.toggle('hidden', hasImages);
    };

    const markPrimary = (imageId) => {
        gallery.querySelectorAll('.auction-image-item').forEach((card) => {
            const label = card.querySelector('.js-primary-label');
            if (!label) {
                return;
            }
            const cardImageId = Number.parseInt(card.dataset.imageId || '0', 10);
            label.classList.toggle('hidden', cardImageId !== imageId);
        });
    };

    const createUploadingCard = (fileName) => {
        const card = document.createElement('div');
        card.className = 'border border-gray-200 rounded-lg p-4 flex items-center space-x-4 opacity-70';

        const thumb = document.createElement('div');
        thumb.className = 'w-20 h-20 rounded-lg bg-gray-100 flex items-center justify-center text-xs text-gray-500';
        thumb.textContent = 'Uploading…';

        const textWrap = document.createElement('div');
        textWrap.className = 'flex-1';

        const fileNameEl = document.createElement('p');
        fileNameEl.className = 'text-sm text-gray-600';
        fileNameEl.textContent = fileName;

        const statusEl = document.createElement('p');
        statusEl.className = 'text-xs text-gray-500';
        statusEl.textContent = 'Uploading...';

        textWrap.append(fileNameEl, statusEl);
        card.append(thumb, textWrap);

        gallery.prepend(card);
        return card;
    };

    const createImageCard = (image) => {
        const card = document.createElement('div');
        card.className = 'border border-gray-200 rounded-lg p-4 flex items-center space-x-4 auction-image-item';
        card.dataset.imageId = String(image.id);

        const safeUrl = String(image.url || '').trim();
        const fileName = safeUrl.split('/').pop() || 'Kuva';
        const isPrimary = Number.parseInt(String(image.is_primary || 0), 10) === 1;

        const imageEl = document.createElement('img');
        imageEl.className = 'w-20 h-20 object-cover rounded-lg cursor-pointer js-set-primary';
        imageEl.alt = 'Kuva';
        imageEl.src = safeUrl;

        const content = document.createElement('div');
        content.className = 'flex-1';

        const fileNameEl = document.createElement('p');
        fileNameEl.className = 'text-sm text-gray-600';
        fileNameEl.textContent = fileName;

        const primaryLabel = document.createElement('p');
        primaryLabel.className = `text-xs text-green-700 mt-1 js-primary-label ${isPrimary ? '' : 'hidden'}`;
        primaryLabel.textContent = '(Pääkuva)';

        content.append(fileNameEl, primaryLabel);

        const actions = document.createElement('div');
        actions.className = 'flex space-x-2';

        const setPrimaryButton = document.createElement('button');
        setPrimaryButton.type = 'button';
        setPrimaryButton.className = 'text-blue-600 hover:text-blue-800 text-sm js-set-primary';
        setPrimaryButton.textContent = 'Aseta pääkuvaksi';

        const deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'text-red-600 hover:text-red-800 text-sm js-delete-image';
        deleteButton.textContent = 'Poista';

        actions.append(setPrimaryButton, deleteButton);
        card.append(imageEl, content, actions);

        return card;
    };

    const postJson = async (url, payload) => {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json'
            },
            body: JSON.stringify(payload),
            credentials: 'same-origin'
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.ok) {
            throw new Error(data.error || 'Toiminto epäonnistui.');
        }

        return data;
    };

    imageInput.addEventListener('change', async (event) => {
        clearError();

        const files = Array.from(event.target.files || []);
        if (!files.length) {
            return;
        }

        const existingCount = gallery.querySelectorAll('.auction-image-item').length;
        if (existingCount + files.length > MAX_IMAGES_PER_AUCTION) {
            showError(`Voit lisätä enintään ${MAX_IMAGES_PER_AUCTION} kuvaa per kohde.`);
            imageInput.value = '';
            return;
        }

        const placeholders = files.map((file) => createUploadingCard(file.name));

        try {
            const formData = new FormData();
            formData.append('auction_id', String(auctionId));
            files.forEach((file) => {
                formData.append('images[]', file);
            });

            const response = await fetch('/api/upload_auction_images.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });

            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.ok || !Array.isArray(data.images)) {
                throw new Error(data.error || 'Kuvien lataus epäonnistui.');
            }

            placeholders.forEach((node) => node.remove());
            data.images.forEach((image) => {
                const card = createImageCard(image);
                gallery.prepend(card);
                if (Number.parseInt(String(image.is_primary || 0), 10) === 1) {
                    markPrimary(Number.parseInt(String(image.id), 10));
                }
            });
            toggleEmptyState();
        } catch (error) {
            placeholders.forEach((node) => node.remove());
            showError(error.message || 'Kuvien lataus epäonnistui.');
        } finally {
            imageInput.value = '';
        }
    });

    gallery.addEventListener('click', async (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) {
            return;
        }

        const card = target.closest('.auction-image-item');
        if (!card) {
            return;
        }

        const imageId = Number.parseInt(card.dataset.imageId || '0', 10);
        if (!imageId) {
            return;
        }

        if (target.closest('.js-delete-image')) {
            event.preventDefault();
            clearError();

            if (!window.confirm('Haluatko varmasti poistaa tämän kuvan?')) {
                return;
            }

            try {
                const data = await postJson('/api/delete_auction_image.php', {
                    auction_id: auctionId,
                    image_id: imageId
                });

                card.remove();
                if (data.primary_image_id) {
                    markPrimary(Number.parseInt(String(data.primary_image_id), 10));
                }
                toggleEmptyState();
            } catch (error) {
                showError(error.message || 'Kuvan poisto epäonnistui.');
            }
            return;
        }

        if (target.closest('.js-set-primary')) {
            event.preventDefault();
            clearError();

            try {
                const data = await postJson('/api/set_primary_image.php', {
                    auction_id: auctionId,
                    image_id: imageId
                });
                markPrimary(Number.parseInt(String(data.primary_image_id), 10));
            } catch (error) {
                showError(error.message || 'Pääkuvan vaihto epäonnistui.');
            }
        }
    });

    toggleEmptyState();
})();
