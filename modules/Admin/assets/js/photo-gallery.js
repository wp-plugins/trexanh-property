// @link: http://themeforest.net/forums/thread/storing-multiple-images-in-a-metabox-field-using-a-custom-wordpress-media-uploader-instance/164594
(function ($) {
    "use strict";
    /*global wp,jQuery */

    var CustomGalleryEdit, CustomFrame;

    function customClasses() {
        var media = wp.media;
        var l10n = media.view.l10n;

        CustomGalleryEdit = wp.media.controller.GalleryEdit.extend({
            gallerySettings: function (browser) {
                if (!this.get('displaySettings')) {
                    return;
                }

                var library = this.get('library');

                if (!library || !browser) {
                    return;
                }

                browser.toolbar.set('reverse', {
                    text: l10n.reverseOrder,
                    priority: 80,
                    click: function () {
                        library.reset(library.toArray().reverse());
                    }
                });
            }
        });

        CustomFrame = wp.media.view.MediaFrame.Post.extend({
            galleryMenu: function (view) {
            },
            createStates: function () {
                var options = this.options;
                // custom frame has only 2 states: gallery edit/add
                this.states.add([
                    new CustomGalleryEdit({
                        library: options.selection,
                        editing: true,
                        requires: {selection: false},
                        menu: 'gallery'
                    }),
                    new media.controller.GalleryAdd()
                ]);
            },
            galleryEditToolbar: function () {
                try {
                    var updateGallery = l10n.updateGallery;
                    // change the button label
                    l10n.updateGallery = 'Save Gallery';
                    // call parent method
                    media.view.MediaFrame.Post.prototype.galleryEditToolbar.apply(this, arguments);
                    // change the button behaviour so that it would allow us to save an empty gallery
                    this.toolbar.get().options.items.insert.requires.library = false;
                    l10n.updateGallery = updateGallery;
                } catch (x) {

                }
            }
        });

    }

    function getWorkFlow(selection) {
        var attributes = {
            state: 'gallery-edit',
            editing: true,
            multiple: true
        };

        if (typeof selection != 'undefined' && selection) {
            attributes.selection = selection;
        }
        return new CustomFrame(attributes);
    }

    function init() {
        if (window.wp && window.wp.media) {
            customClasses();
        }
    }

    $(init);


    // the following functions can be used to interact with the custom media uploader

    // input field where the gallery images ids are store as comma separated list
    var store = $('[name=gallery_photo_ids]');
    function get_store() {
        return $('[name=gallery_photo_ids]');
    };
    var photoGalleryContainer = $('.photo-gallery-container');
    function get_photo_gallery_container() {
        return $('.photo-gallery-container');
    };
    // the media uploader
    var workflow = false;

    // selection object which list gallery images as collection
    var selection;

    // opens the dialog
    function open() {
        if (!selection) {
            fetch();
        }

        if (workflow) {
            workflow.off('update', update);
            workflow.dispose();
        }

        workflow = getWorkFlow(selection);
        workflow.on('update', update);
        workflow.open();
    }

    // create selection collection
    function fetch() {
        if (!store.length) {
            store = $(get_store());
        }
        var value = store.val();

        if (!value) {
            selection = [];
            return;
        }

        var shortcode = new wp.shortcode({
            tag: "gallery",
            attrs: {ids: value},
            type: "single"
        });

        var attachments = wp.media.gallery.attachments(shortcode);

        selection = new wp.media.model.Selection(attachments.models, {
            props: attachments.props.toJSON(),
            multiple: true
        });

        selection.gallery = attachments.gallery;
        selection.more().done(function () {
            // Break ties with the query.
            selection.props.set({query: false});
            selection.unmirror();
            selection.props.unset("orderby");
        });

    }

    // retrieve list of gallery images and stores them
    function update() {
        var library = workflow.states.get('gallery-edit').get('library');
        // render photos for gallery
        if (!photoGalleryContainer.length) {
            photoGalleryContainer = get_photo_gallery_container();
        }
        photoGalleryContainer.find(".photo-gallery-thumbnail").remove();
        var message = photoGalleryContainer.find(".gallery-message");
        if (library.models.length === 0) {
            message.show();
        } else {
            message.hide();
            $.each(library.models, function(i, model) {
                var url = model.attributes.url.split(".");
                url[url.length - 2] += "-150x150";
                url = url.join(".");
                photoGalleryContainer.append("<div style='display:inline-block' class='photo-gallery-thumbnail'><img src='" + url + "' width='150' height='150' /></div>");
            });
        }
        var ids = library.pluck('id');
        var value = (typeof ids === 'object') ? ids.join(',') : '';
        store.val(value);
        selection = false;
    }

    $(document).ready(function() {
        $("#open_uploader").click(function() {
            $(open);
            return false;
        });
    });

}(jQuery));