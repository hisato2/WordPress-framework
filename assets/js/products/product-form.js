document.addEventListener('DOMContentLoaded', function () {

    const productType =
        document.getElementById('product_type');

    const publicationSection =
        document.getElementById('publication_section');

    const previewPdfArea =
        document.getElementById('preview_pdf_area');

    const imageLabel =
        document.getElementById('product_image_label');

    const imageHelp =
        document.getElementById('product_image_help');


    if (
        !productType
        || !publicationSection
        || !previewPdfArea
        || !imageLabel
        || !imageHelp
    ) {
        return;
    }


    function updateProductFields() {

        const type = productType.value;


        /*
        |--------------------------------------------------------------------------
        | Software
        |--------------------------------------------------------------------------
        */

        if (type === 'software') {

            publicationSection.style.display = 'none';

            previewPdfArea.style.display = 'none';

            imageLabel.textContent =
                'ソフトウェア バナー画像';

            imageHelp.innerHTML =
                '推奨 800 × 450 px（16:9）<br>' +
                '正方形の場合：800 × 800 px（透明余白可）<br>' +
                'JPEG / PNG / WebP、最大5MB';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Book / Volume / Issue
        |--------------------------------------------------------------------------
        */

        publicationSection.style.display = '';

        previewPdfArea.style.display = '';

        imageLabel.textContent =
            '表紙・商品画像';

        imageHelp.innerHTML =
            '推奨 600 × 800 px（3:4）<br>' +
            'JPEG / PNG / WebP、最大5MB';
    }


    productType.addEventListener(
        'change',
        updateProductFields
    );


    /*
    |--------------------------------------------------------------------------
    | Initial State
    |--------------------------------------------------------------------------
    */

    updateProductFields();

});