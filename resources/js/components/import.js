$(document).ready(function () {
    const $select = $('#import_type');
    const $container = $('#file-inputs-container');

    const importTypesData = $('#import-data').data('import-types');
    const importTypes = importTypesData || {};

    $select.on('change', handleImportTypeChange);

    function handleImportTypeChange() {
        const type = $select.val();
        $container.empty();

        if (!type || !importTypes[type]) return;

        const files = importTypes[type].files;

        $.each(files, function (fileKey, fileData) {
            const $section = createFileSection(fileKey, fileData);
            $container.append($section);
        });
    }

    function createFileSection(fileKey, fileData) {
        const $section = $('<div>').addClass('mb-4');

        $section.append(
            $('<label>').addClass('form-label').text(`${fileData.label}`)
        );

        $section.append(
            $('<input>').addClass('form-control').attr({
                type: 'file',
                name: `files[${fileKey}]`
            })
        );

        const headersText = extractHeadersText(fileData.headers);

        $section.append(
            $('<p>').text(`Required Headers: ${headersText}`)
        );

        return $section;
    }

    function extractHeadersText(headers) {
        return Object.values(headers).map(h => h.label).join(', ');
    }
});
