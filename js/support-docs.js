jQuery(function ($) {

    function getFileExtension(filename) {
        return (/[.]/.exec(filename)) ? /[^.]+$/.exec(filename) : undefined;
    }

    // Set all variables to be used in scope
    var docs_upload_frame,
        metaBox = $('#supporting_docs_meta.postbox'), // ID of Supporting docs meta box
        addDocsLink = metaBox.find('.upload-supporting-docs'),
        docsContainer = metaBox.find('.supporting-docs-container');

    //Make the docs container sortable
    docsContainer.sortable({
        items: '.supporting-document',
        cursor: 'move',
        parent: 'parent',
        placeholder: 'docs-placeholder'
    });

    docsContainer.on('click', 'a.remove-doc', function (e) {
        e.preventDefault();
        $(this).parent().fadeOut('slow');
    });

    // ADD DOCS LINK
    addDocsLink.on('click', function (e) {

        e.preventDefault();

        // If the media frame already exists, reopen it.
        if (docs_upload_frame) {
            docs_upload_frame.open();
        }

        // Create a new media frame was frame = wp.media
        docs_upload_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select or Upload Supporting Document',
            button: {
                text: 'Use this media'
            },
            multiple: true  // Set to true to allow multiple files to be selected
        });


        // When an image is selected in the media frame...
        docs_upload_frame.on('select', function () {

            // Get media attachment details from the frame state
            var attachments = docs_upload_frame.state().get('selection');

            attachments.map(function (attachment, index) {
                attachment = attachment.toJSON();
                var content = '';

                content += '<div class="supporting-document doc-icon-' + getFileExtension(attachment.filename) + '">';
                content += '<input type="hidden" name="attachments[' + attachment.id + '][id]" value="' + attachment.id + '" />';
                content += '<input type="hidden" name="attachments[' + attachment.id + '][extension]" value="' + getFileExtension(attachment.filename) + '" />';
                content += '<input type="hidden" name="attachments[' + attachment.id + '][url]" value="' + attachment.url + '" />';
                content += '<input type="hidden" name="attachments[' + attachment.id + '][filename]" value="' + attachment.filename + '" />';
                content += '<input type="hidden" name="attachments[' + attachment.id + '][size]" value="' + attachment.filesizeHumanReadable + '" />';
                content += '<a href="' + attachment.url + '">' + attachment.filename + '</a><br>';
                content += '<i>' + attachment.filesizeHumanReadable + '</i>';
                content += '<a class="remove-doc" href="#">Remove</a>';
                content += '</div>';
                docsContainer.append(content);
            });
        });

        // Finally, open the modal on click
        docs_upload_frame.open();
    });

});