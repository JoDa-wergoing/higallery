(function (wp) {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls } = wp.blockEditor || wp.editor;
    const { PanelBody, CheckboxControl } = wp.components;
    const { useState, useEffect, createElement: el } = wp.element;
    const { __ } = wp.i18n;
    registerBlockType('higallery/block', {
        title: 'HiGallery',
        icon: 'format-gallery',
        category: 'widgets',
        attributes: {
            albums: {
                type: 'array',
                default: []
            }
        },
        edit: function (props) {
            const attributes = props.attributes;
            const setAttributes = props.setAttributes;
            const [albumsList, setAlbumsList] = useState([]);

            useEffect(function () {
                wp.apiFetch({ path: '/higallery/v1/albums' })
                    .then(function (albums) {
                        setAlbumsList(albums);
                    })
                    .catch(function (err) {
                        console.error('HiGallery block: API fetch failed', err);
                        setAlbumsList([]);
                    });
            }, []);

            const allNames = albumsList.map(album => decodeURIComponent(album.name));
            const allSelected = allNames.every(name => attributes.albums.includes(name));

            const checkboxes = albumsList.length === 0
                ? el('p', {}, __('Loading albums...','higallery'))
                : [
                    el(CheckboxControl, {
                        key: 'select_all',
                        label: __('Select all','higallery'),
                        checked: allSelected,
                        __nextHasNoMarginBottom: true,
                        onChange: function (checked) {
                            const newSelection = checked ? allNames : [];
                            setAttributes({ albums: newSelection });
                        }
                    }),
                    ...albumsList.map(function (album) {
                        const decodedName = decodeURIComponent(album.name);
                        return el(CheckboxControl, {
                            key: album.path,
                            label: decodedName,
                            checked: attributes.albums.includes(decodedName),
                            __nextHasNoMarginBottom: true,
                            onChange: function (isChecked) {
                                const newSelection = isChecked
                                    ? [...attributes.albums, decodedName]
                                    : attributes.albums.filter(name => name !== decodedName);
                                setAttributes({ albums: newSelection });
                            }
                        });
                    })
                ];

            return el(
                'div',
                {},
                el(
                    InspectorControls,
                    {},
                    el(PanelBody, { title: __('Select albums','higallery') }, checkboxes)
                ),
                el(
                    'p',
                    {},
                    __('HiGallery Block: Selected albums: ','higallery'),
                    attributes.albums.length > 0 ? attributes.albums.join(', ') : __('None','higallery')
                )
            );
        },
        save: function () {
            return null;
        }
    });
})(window.wp);
