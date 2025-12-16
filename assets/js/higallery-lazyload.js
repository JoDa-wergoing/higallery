document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.higallery-wrapper').forEach(wrapper => {
        const button = wrapper.querySelector('.higallery-load-more');
        const gallery = wrapper.querySelector('.pswp-gallery');
        let remaining = JSON.parse(wrapper.getAttribute('data-images'));

        if (button) {
            button.addEventListener('click', function () {
                const batch = remaining.splice(0, 25);

                batch.forEach(image => {
                    const link = document.createElement('a');
                    link.href = image.url;
                    link.setAttribute('data-pswp-width', '1600');
                    link.setAttribute('data-pswp-height', '1067');

                    const img = document.createElement('img');
                    img.src = image.url;
                    img.alt = image.name;
                    img.style.maxWidth = '300px';
                    img.style.height = 'auto';
                    img.style.display = 'block';

                    link.appendChild(img);
                    gallery.appendChild(link);
                });

                if (remaining.length === 0) {
                    button.remove();
                }

                if (typeof PhotoSwipeLightbox !== 'undefined') {
                    // lightbox.destroy();
                    // lightbox = new PhotoSwipeLightbox({ ... }); 
                    // lightbox.init();
                }
            });
        }
    });
});
