document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.higallery-album-card').forEach(card => {
        card.addEventListener('click', function () {
            const images = JSON.parse(this.dataset.images || '[]');

            if (images.length > 0) {
                import('https://cdn.jsdelivr.net/npm/photoswipe@5.3.4/dist/photoswipe-lightbox.esm.min.js')
                    .then((module) => {
                        const PhotoSwipeLightbox = module.default;

                        const lightbox = new PhotoSwipeLightbox({
                            dataSource: images,
                            pswpModule: () =>
                                import('https://cdn.jsdelivr.net/npm/photoswipe@5.3.4/dist/photoswipe.esm.min.js')
                        });

                        lightbox.init();
                        lightbox.loadAndOpen(0);
                    })
                    .catch(err => {
                        console.error("❌ PhotoSwipe ESM module load failed:", err);
                    });
            }
        });
    });
});
