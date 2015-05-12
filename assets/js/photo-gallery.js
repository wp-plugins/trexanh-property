(function ($) {
    $(document).ready(function() {
        var galleryContainer = $(".photo-gallery-container");
        var customUploadButton = $(".custom-upload-button");
        var galleryEmptyMessage = galleryContainer.find(".gallery-message");
        customUploadButton.find("[type=file]").click(function() {
            var clonedInput = $(this).clone();
            clonedInput
                .addClass("untouchable")
                .attr("name", "gallery_images[]");
            clonedInput.trigger("click");
            clonedInput.change(function() {
                var addingImage = null;
                if (FileReader) {
                    // show image preview
                    addingImage = $("<div class='adding-image square150 photo-gallery-thumbnail'></div>");
                    var oFReader = new FileReader();
                    oFReader.readAsDataURL(this.files[0]);

                    oFReader.onload = function (oFREvent) {
                        addingImage.append("<img class='image-preview' src='" + oFREvent.target.result + "' />");
                        customUploadButton.before(addingImage);
                    };
                } else {
                    var fileName = this.files[0].name;
                    var maybeAlreadyAdded = false;
                    var addingImages = $(".adding-image");
                    if (addingImages.length) {
                        for (var i = 0; i < addingImages.length; i++) {
                            if (addingImages.eq(i).find(".file-name").text() === fileName) {
                                maybeAlreadyAdded = true;
                                break;
                            }
                        }
                    }
                    addingImage = $("<p class='adding-image'><span class='file-name'>" + fileName + "</span></p>");
                    if (addingImages.length) {
                        addingImages.last().after(addingImage);
                    } else {
                        galleryContainer.after(addingImage);
                    }
                }
                addingImage
                    .append(clonedInput)
                    .append(" <a href='#' class='remove-image'>x</a>");
                if (maybeAlreadyAdded) {
                    addingImage.append(" <span class='text-danger'><em>(File with the same name was previously added)</em></span>");
                }
                galleryEmptyMessage.hide();
            });
            return false;
        });

        var photoIdsInput = $("[name=gallery_photo_ids]");
        var galleryImages = photoIdsInput.val() ? photoIdsInput.val().split(",") : [];
        var shouldShowEmptyGalleryMessage = function() {
            if ( $(".photo-gallery-thumbnail").length === 0 && $(".adding-image").length === 0) {
                return true;
            }
            return false;
        };
        galleryContainer.delegate(".remove-image", "click", function() {
            var $this = $(this);
            var imageId = $this.data("id");
            if (imageId) {
                for (var i = 0; i < galleryImages.length; i++) {
                    if (galleryImages[i] == imageId) {
                        galleryImages.splice(i, 1);
                        break;
                    }
                }
                photoIdsInput.val(galleryImages.join(","));
            }
            $this.closest(".photo-gallery-thumbnail").remove();
            if (shouldShowEmptyGalleryMessage()) {
                galleryEmptyMessage.show();
            }
            return false;
        });
    });
}(jQuery));