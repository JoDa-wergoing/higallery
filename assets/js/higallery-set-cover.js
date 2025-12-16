(function () {
    // Zorg dat we alleen draaien als de data beschikbaar is
    if (!window.higallerySetCover) {
        return;
    }

    var restUrl = window.higallerySetCover.restUrl;
    var nonce   = window.higallerySetCover.nonce;

    function handleClick(event) {
        var button = event.currentTarget;

        var albumPath = button.getAttribute('data-album-path');
        var imagePath = button.getAttribute('data-image-path');

        if (!albumPath || !imagePath) {
            alert('Album of afbeelding ontbreekt.');
            return;
        }

        button.disabled = true;
        button.textContent = button.getAttribute('data-label-working') || 'Bezig...';

        fetch(restUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': nonce
            },
            body: JSON.stringify({
                album_path: albumPath,
                image_path: imagePath
            })
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (data && data.success) {
                    button.textContent = button.getAttribute('data-label-done') || 'Omslag ingesteld';
                } else {
                    console.error('HiGallery: fout bij opslaan cover', data);
                    button.disabled = false;
                    button.textContent = button.getAttribute('data-label-error') || 'Fout, probeer opnieuw';
                }
            })
            .catch(function (err) {
                console.error('HiGallery: fetch error', err);
                button.disabled = false;
                button.textContent = button.getAttribute('data-label-error') || 'Fout, probeer opnieuw';
            });
    }

    function init() {
        var buttons = document.querySelectorAll('.higallery-set-cover');
        buttons.forEach(function (btn) {
            btn.addEventListener('click', handleClick);
        });
    }

    document.addEventListener('DOMContentLoaded', init);
})();
