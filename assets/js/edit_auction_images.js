// Security-first. Creator-ready. Future-proof.
(() => {
    const imageInput = document.getElementById('imageInput');
    const gallery = document.getElementById('imageGallery');
    const emptyState = document.getElementById('emptyImageState');
    const errorEl = document.getElementById('imageUploadError');
    const freeImageQueryInput = document.getElementById('freeImageQuery');
    const freeImageSearchButton = document.getElementById('searchFreeImagesButton');
    const freeImageStatus = document.getElementById('freeImageStatus');
    const freeImageResults = document.getElementById('freeImageResults');

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

    const setFreeImageStatus = (message, isError = false) => {
        if (!freeImageStatus) {
            return;
        }
        freeImageStatus.textContent = message;
        freeImageStatus.style.color = isError ? '#b91c1c' : 'var(--text-700)';
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

    const markCaptionSaved = (card) => {
        const status = card.querySelector('.js-caption-status');
        if (!status) {
            return;
        }
        status.textContent = 'Tallennettu';
        setTimeout(() => {
            status.textContent = '';
        }, 1400);
    };

    const createUploadingCard = (fileName) => {
        const card = document.createElement('div');
        card.className = 'image-item auction-image-item';
        card.style.opacity = '0.7';
        card.dataset.imageId = '0';

        const thumb = document.createElement('div');
        thumb.style.height = '170px';
        thumb.style.display = 'grid';
        thumb.style.placeItems = 'center';
        thumb.style.background = '#eef2f7';
        thumb.style.color = '#6b7280';
        thumb.style.fontSize = '0.8rem';
        thumb.textContent = 'Uploading…';

        const textWrap = document.createElement('div');
        textWrap.className = 'image-controls';

        const fileNameEl = document.createElement('span');
        fileNameEl.className = 'image-filename';
        fileNameEl.textContent = fileName;

        const statusEl = document.createElement('p');
        statusEl.style.fontSize = '0.75rem';
        statusEl.style.color = '#6b7280';
        statusEl.style.margin = '0';
        statusEl.textContent = 'Uploading...';

        textWrap.append(fileNameEl, statusEl);
        card.append(thumb, textWrap);

        gallery.prepend(card);
        return card;
    };

    const createImageCard = (image) => {
        const card = document.createElement('div');
        card.className = 'image-item auction-image-item';
        card.dataset.imageId = String(image.id);

        const safeUrl = String(image.url || '').trim();
        const fileName = safeUrl.split('/').pop() || 'Kuva';
        const isPrimary = Number.parseInt(String(image.is_primary || 0), 10) === 1;

        const imageEl = document.createElement('img');
        imageEl.className = 'js-set-primary';
        imageEl.alt = 'Kuva';
        imageEl.src = safeUrl;

        const content = document.createElement('div');
        content.className = 'image-controls';

        const fileNameEl = document.createElement('span');
        fileNameEl.className = 'image-filename';
        fileNameEl.textContent = fileName;

        const primaryLabel = document.createElement('span');
        primaryLabel.className = `primary-badge js-primary-label ${isPrimary ? '' : 'hidden'}`;
        primaryLabel.textContent = 'Pääkuva';

        const captionInput = document.createElement('input');
        captionInput.type = 'text';
        captionInput.className = 'form-input js-caption-input';
        captionInput.placeholder = 'Kuvateksti (näkyy kohdesivulla)';
        captionInput.value = String(image.caption || '');

        const captionStatus = document.createElement('p');
        captionStatus.className = 'js-caption-status';
        captionStatus.style.fontSize = '.75rem';
        captionStatus.style.color = '#15803d';
        captionStatus.style.marginTop = '.25rem';
        captionStatus.textContent = '';

        content.append(fileNameEl, primaryLabel, captionInput, captionStatus);

        const actions = document.createElement('div');
        actions.style.display = 'flex';
        actions.style.gap = '.35rem';
        actions.style.flexWrap = 'wrap';
        actions.style.marginTop = '.55rem';

        const setPrimaryButton = document.createElement('button');
        setPrimaryButton.type = 'button';
        setPrimaryButton.className = 'image-button js-set-primary';
        setPrimaryButton.textContent = 'Aseta pääkuvaksi';

        const deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'image-button js-delete-image';
        deleteButton.style.background = 'rgba(239,68,68,0.9)';
        deleteButton.style.color = '#fff';
        deleteButton.textContent = 'Poista';

        const saveCaptionButton = document.createElement('button');
        saveCaptionButton.type = 'button';
        saveCaptionButton.className = 'image-button js-save-caption';
        saveCaptionButton.textContent = 'Tallenna teksti';

        actions.append(setPrimaryButton, saveCaptionButton, deleteButton);
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

    const getJson = async (url) => {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                Accept: 'application/json'
            },
            credentials: 'same-origin'
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.ok) {
            throw new Error(data.error || 'Toiminto epäonnistui.');
        }

        return data;
    };

    const renderFreeImageResults = (results) => {
        if (!freeImageResults) {
            return;
        }

        if (!Array.isArray(results) || results.length === 0) {
            freeImageResults.innerHTML = '<p class="free-image-note">Hakusanalla ei löytynyt kuvia.</p>';
            return;
        }

        freeImageResults.innerHTML = results.map((result) => {
            const thumbUrl = String(result.thumb_url || '').replace(/"/g, '&quot;');
            const fullUrl = String(result.full_url || '').replace(/"/g, '&quot;');
            const title = String(result.title || 'Kuva').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            const license = String(result.license || 'Commons-lisensoitu').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            const sourcePage = String(result.source_page || '#').replace(/"/g, '&quot;');

            return `
                <div class="free-image-item">
                    <img src="${thumbUrl}" alt="${title}">
                    <div class="free-image-item-body">
                        <p class="free-image-item-title">${title}</p>
                        <p class="free-image-item-meta">Lisenssi: ${license}</p>
                        <div style="display:flex; gap:.35rem; flex-wrap:wrap;">
                            <button type="button" class="image-button js-import-free-image" data-url="${fullUrl}" data-title="${title}">Käytä tätä kuvaa</button>
                            <a href="${sourcePage}" target="_blank" rel="noopener noreferrer" class="image-button" style="text-decoration:none; display:inline-flex; align-items:center;">Lähde</a>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    };

    const searchWikimediaImages = async (query) => {
        const params = new URLSearchParams({
            action: 'query',
            format: 'json',
            origin: '*',
            generator: 'search',
            gsrnamespace: '6',
            gsrlimit: '12',
            gsrsearch: query,
            prop: 'imageinfo',
            iiprop: 'url|extmetadata',
            iiurlwidth: '500'
        });

        const response = await fetch(`https://commons.wikimedia.org/w/api.php?${params.toString()}`, {
            method: 'GET',
            headers: { Accept: 'application/json' }
        });

        if (!response.ok) {
            throw new Error('Kuvahaku epäonnistui.');
        }

        const decoded = await response.json().catch(() => ({}));
        const pages = decoded?.query?.pages || {};

        const results = [];
        Object.values(pages).forEach((page) => {
            const imageInfo = page?.imageinfo?.[0];
            if (!imageInfo) {
                return;
            }

            const fullUrl = String(imageInfo.url || '').trim();
            const thumbUrl = String(imageInfo.thumburl || fullUrl).trim();
            if (!fullUrl) {
                return;
            }

            const meta = imageInfo.extmetadata || {};
            const license = String(meta?.LicenseShortName?.value || 'Commons-lisensoitu').replace(/<[^>]*>/g, '');
            const title = String(page?.title || 'Kuva');

            results.push({
                title,
                thumb_url: thumbUrl,
                full_url: fullUrl,
                source_page: `https://commons.wikimedia.org/wiki/${encodeURIComponent(title)}`,
                license,
            });
        });

        return results;
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

            const response = await fetch('api/upload_auction_images.php', {
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

    if (freeImageSearchButton && freeImageQueryInput) {
        freeImageSearchButton.addEventListener('click', async () => {
            const query = freeImageQueryInput.value.trim();
            if (query.length < 2) {
                setFreeImageStatus('Anna vähintään 2 merkkiä hakua varten.', true);
                return;
            }

            try {
                freeImageSearchButton.disabled = true;
                setFreeImageStatus('Haetaan kuvia…');
                if (freeImageResults) {
                    freeImageResults.innerHTML = '';
                }

                const results = await searchWikimediaImages(query);
                renderFreeImageResults(results);
                setFreeImageStatus(`Hakutuloksia: ${results.length} kpl.`);
            } catch (error) {
                setFreeImageStatus(error.message || 'Kuvahaku epäonnistui.', true);
            } finally {
                freeImageSearchButton.disabled = false;
            }
        });
    }

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
                const data = await postJson('api/delete_auction_image.php', {
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
                const data = await postJson('api/set_primary_image.php', {
                    auction_id: auctionId,
                    image_id: imageId
                });
                markPrimary(Number.parseInt(String(data.primary_image_id), 10));
            } catch (error) {
                showError(error.message || 'Pääkuvan vaihto epäonnistui.');
            }
            return;
        }

        if (target.closest('.js-save-caption')) {
            event.preventDefault();
            clearError();

            const captionInput = card.querySelector('.js-caption-input');
            const captionValue = captionInput instanceof HTMLInputElement ? captionInput.value : '';

            try {
                await postJson('api/update_auction_image_caption.php', {
                    auction_id: auctionId,
                    image_id: imageId,
                    caption: captionValue,
                });
                markCaptionSaved(card);
            } catch (error) {
                showError(error.message || 'Kuvatekstin tallennus epäonnistui.');
            }
            return;
        }

        const importButton = target.closest('.js-import-free-image');
        if (importButton instanceof HTMLElement) {
            event.preventDefault();

            const imageUrl = importButton.dataset.url || '';
            const title = importButton.dataset.title || '';
            if (!imageUrl) {
                setFreeImageStatus('Valitussa tuloksessa ei ollut kuvalinkkiä.', true);
                return;
            }

            try {
                importButton.setAttribute('disabled', 'disabled');
                setFreeImageStatus('Tuodaan kuva kohteelle…');

                const data = await postJson('api/import_free_image.php', {
                    auction_id: auctionId,
                    image_url: imageUrl,
                    title,
                });

                if (data.image) {
                    const card = createImageCard(data.image);
                    gallery.prepend(card);
                    if (Number.parseInt(String(data.image.is_primary || 0), 10) === 1) {
                        markPrimary(Number.parseInt(String(data.image.id), 10));
                    }
                    toggleEmptyState();
                }

                setFreeImageStatus('Kuva tuotu onnistuneesti.');
            } catch (error) {
                setFreeImageStatus(error.message || 'Kuvan tuonti epäonnistui.', true);
            } finally {
                importButton.removeAttribute('disabled');
            }
        }
    });

    toggleEmptyState();
})();
