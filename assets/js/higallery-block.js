(function (wp) {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls } = wp.blockEditor || wp.editor;
    const { PanelBody, CheckboxControl } = wp.components;
    const { useState, useEffect, createElement: el, Fragment } = wp.element;
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
            const { attributes, setAttributes } = props;
            const [albumsList, setAlbumsList] = useState([]);

            // Albums ophalen uit de REST endpoint
            useEffect(function () {
                wp.apiFetch({ path: '/higallery/v1/albums' })
                    .then(function (albums) {
                        if (Array.isArray(albums)) {
                            setAlbumsList(albums);
                        } else {
                            setAlbumsList([]);
                        }
                    })
                    .catch(function (err) {
                        console.error('HiGallery block: API fetch failed', err);
                        setAlbumsList([]);
                    });
            }, []);

            // Namen van alle albums
            const allNames = albumsList.map(function (album) {
                return album.name;
            });

            // Zijn alle albums geselecteerd?
            const allSelected =
                allNames.length > 0 &&
                allNames.every(function (name) {
                    return attributes.albums.indexOf(name) !== -1;
                });

            // Select all aan/uit
            function handleSelectAll(checked) {
                if (checked) {
                    setAttributes({ albums: allNames });
                } else {
                    setAttributes({ albums: [] });
                }
            }

            // Los album aan/uit
            function toggleAlbum(albumName, isChecked) {
                var current = attributes.albums.slice();

                if (isChecked) {
                    if (current.indexOf(albumName) === -1) {
                        current.push(albumName);
                    }
                } else {
                    current = current.filter(function (name) {
                        return name !== albumName;
                    });
                }

                setAttributes({ albums: current });
            }

            // Checkbox-lijst opbouwen
            var checkboxes;

            if (albumsList.length === 0) {
                checkboxes = el('p', {}, __('Loading albums...', 'higallery'));
            } else {
                checkboxes = [
                    // Select all checkbox
                    el(CheckboxControl, {
                        key: 'select_all',
                        label: __('Select all', 'higallery'),
                        checked: allSelected,
                        __nextHasNoMarginBottom: true,
                        onChange: handleSelectAll
                    })
                ].concat(
                    // Per album een checkbox
                    albumsList.map(function (album) {
                        return el(CheckboxControl, {
                            key: album.path || album.name,
                            label: album.name,
                            checked: attributes.albums.indexOf(album.name) !== -1,
                            __nextHasNoMarginBottom: true,
                            onChange: function (isChecked) {
                                toggleAlbum(album.name, isChecked);
                            }
                        });
                    })
                );
            }

            // Tekst onder het block
            var selectedCount = attributes.albums.length;
            var summaryText;

            if (selectedCount === 0) {
                summaryText = __('No albums selected', 'higallery');
            } else if (allSelected) {
                summaryText = __('All albums selected', 'higallery');
            } else {
                summaryText =
                    selectedCount + ' ' + __('albums selected', 'higallery');
            }

            return el(
                Fragment,
                {},
                el(
                    InspectorControls,
                    {},
                    el(
                        PanelBody,
                        { title: __('Select albums', 'higallery'), initialOpen: true },
                        checkboxes
                    )
                ),
                el(
                    'p',
                    { className: 'higallery-selection-summary' },
                    summaryText
                )
            );
        },

        save: function () {
            // Front-end output wordt in PHP gerenderd
            return null;
        }
    });
})(window.wp);
