(function () {

  function pickSrc(item) {
    const vw = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
    const dpr = window.devicePixelRatio || 1;
    const target = vw * dpr;

    const s = item.sources || {};
    if (s.medium && target <= s.medium.w) return s.medium.src;
    if (s.large) return s.large.src;
    return item.src;
  }

  function buildSrcset(item) {
    const s = item.sources || {};
    const out = [];
    if (s.medium) out.push(`${s.medium.src} ${s.medium.w}w`);
    if (s.large)  out.push(`${s.large.src} ${s.large.w}w`);
    if (s.orig)   out.push(`${s.orig.src} ${s.orig.w}w`);
    return out.join(', ');
  }

  function initHiGallery() {
    if (!window.PhotoSwipeLightbox || !window.PhotoSwipe) return;

    const lightbox = new PhotoSwipeLightbox({
      gallery: '.higallery',
      children: 'a.higallery-item',
      pswpModule: window.PhotoSwipe
    });

    // 🔹 data-* → PhotoSwipe item
    lightbox.addFilter('domItemData', (itemData, el) => {
      const d = el.dataset;
      itemData.sources = {};

      if (d.hgMedium && d.hgMediumW)
        itemData.sources.medium = { src: d.hgMedium, w: +d.hgMediumW };

      if (d.hgLarge && d.hgLargeW)
        itemData.sources.large = { src: d.hgLarge, w: +d.hgLargeW };

      if (d.hgOrig && d.hgOrigW)
        itemData.sources.orig = { src: d.hgOrig, w: +d.hgOrigW };

      itemData.src = pickSrc(itemData);

      const srcset = buildSrcset(itemData);
      if (srcset) {
        itemData.srcset = srcset;
        itemData.sizes = '100vw';
      }

      return itemData;
    });

    // ❌ Altijd zichtbare close-knop
    lightbox.on('uiRegister', () => {
      lightbox.pswp.ui.registerElement({
        name: 'higallery-close',
        isButton: true,
        tagName: 'button',
        className: 'pswp__button pswp__button--close higallery-close',
        html: '✕',
        onClick: (_, __, pswp) => pswp.close()
      });
    });

    lightbox.init();
  }

  document.addEventListener('DOMContentLoaded', initHiGallery);

})();
