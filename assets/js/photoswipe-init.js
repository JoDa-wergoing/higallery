
document.addEventListener('DOMContentLoaded', function () {
    if (typeof PhotoSwipeLightbox === 'undefined' || typeof PhotoSwipe === 'undefined') {
        console.warn('PhotoSwipe not found.');
        return;
    }

    const lightbox = new PhotoSwipeLightbox({
        gallery: '.pswp-gallery',
        children: 'a',
        pswpModule: PhotoSwipe
    });

    lightbox.init();
});
