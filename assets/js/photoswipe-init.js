document.addEventListener('DOMContentLoaded', function () {
  if (typeof PhotoSwipeLightbox === 'undefined' || typeof PhotoSwipe === 'undefined') {
    console.warn('PhotoSwipe not found.');
    return;
  }

  const gallerySelector = '.pswp-gallery.higallery';
  const galleries = document.querySelectorAll(gallerySelector);
  if (!galleries.length) return;

  function pickBestSrc(item) {
    const vw = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
    const dpr = window.devicePixelRatio || 1;
    const target = vw * dpr;

    const s = item.sources || {};
    if (s.medium && target <= s.medium.w) return s.medium.src;
    if (s.large) return s.large.src;
    return (s.orig && s.orig.src) || item.src;
  }

  // ✅ srcset zonder orig (orig alleen bij zoom/fallback)
  function buildSrcset(item) {
    const s = item.sources || {};
    const out = [];
    if (s.medium?.src && s.medium?.w) out.push(`${s.medium.src} ${s.medium.w}w`);
    if (s.large?.src && s.large?.w)  out.push(`${s.large.src} ${s.large.w}w`);
    return out.join(', ');
  }

  galleries.forEach(function (galleryEl) {
    const lightbox = new PhotoSwipeLightbox({
      gallery: galleryEl,
      children: 'a.higallery-item',
      pswpModule: PhotoSwipe
    });

    lightbox.addFilter('domItemData', function (itemData, element) {
      const d = element.dataset;
      itemData.sources = {};

      if (d.hgMedium && d.hgMediumW) {
        itemData.sources.medium = { src: d.hgMedium, w: parseInt(d.hgMediumW, 10) };
      }
      if (d.hgLarge && d.hgLargeW) {
        itemData.sources.large = { src: d.hgLarge, w: parseInt(d.hgLargeW, 10) };
      }
      if (d.hgOrig) {
        itemData.sources.orig = { src: d.hgOrig, w: d.hgOrigW ? parseInt(d.hgOrigW, 10) : 99999 };
      }

      itemData.src = pickBestSrc(itemData);

      const srcset = buildSrcset(itemData);
      if (srcset) {
        itemData.srcset = srcset;
        itemData.sizes = '100vw';
      } else {
        delete itemData.srcset;
        delete itemData.sizes;
      }

      return itemData;
    });

    // fallback: medium/large faalt → orig
    lightbox.on('contentLoadImage', function (e) {
      const content = e?.content;
      const img = content?.element;
      const data = content?.data;
      const orig = data?.sources?.orig?.src || data?.src;

      if (!img || !orig) return;

      img.addEventListener('error', function () {
        if (img.dataset.hgTriedOrig === '1') return;
        img.dataset.hgTriedOrig = '1';

        img.removeAttribute('srcset');
        img.removeAttribute('sizes');
        img.src = orig;
      }, { once: true });
    });

    // orig pas laden bij echte zoom
    lightbox.on('change', function () {
      const pswp = lightbox.pswp;
      if (!pswp || !pswp.currSlide) return;

      const slide = pswp.currSlide;
      const data = slide.data;
      const orig = data?.sources?.orig?.src;
      if (!orig) return;

      const zoom = slide.currZoomLevel || 1;
      if (zoom > 1.2 && data.src !== orig) {
        data.src = orig;
        slide.content?.load?.();
      }
    });

    // SVG close button
    lightbox.on('uiRegister', function () {
      lightbox.pswp.ui.registerElement({
        name: 'higallery-close',
        isButton: true,
        tagName: 'button',
        className: 'pswp__button pswp__button--close higallery-close',
        title: 'Close',
        ariaLabel: 'Close',
        html: `
          <svg aria-hidden="true" class="pswp__icn" viewBox="0 0 60 60" width="60" height="60">
            <use class="pswp__icn-shadow" xlink:href="#pswp__icn-close"></use>
            <path id="pswp__icn-close"
                  d="M35.7 24.3l-1.4-1.4L30 27.2l-4.3-4.3-1.4 1.4
                     4.3 4.3-4.3 4.3 1.4 1.4
                     4.3-4.3 4.3 4.3 1.4-1.4
                     -4.3-4.3z"/>
          </svg>
        `,
        onClick: function (e, el, pswp) {
          pswp.close();
        }
      });
    });

    lightbox.init();
  });
});
