document.addEventListener('DOMContentLoaded', function () {

    const productType =
        document.getElementById('product_type');

    const seriesSelect =
        document.getElementById('series_id');

    const publicationSection =
        document.getElementById('publication_section');

    const softwareSection =
        document.getElementById('software_section');

    const previewPdfArea =
        document.getElementById('preview_pdf_area');

    const imageLabel =
        document.getElementById('product_image_label');

    const imageHelp =
        document.getElementById('product_image_help');


    if (
        !productType
        || !seriesSelect
        || !publicationSection
        || !softwareSection
        || !previewPdfArea
        || !imageLabel
        || !imageHelp
    ) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Initial Series ID
    |--------------------------------------------------------------------------
    |
    | 編集画面では、現在の商品に設定されている
    | series_id を初期値として保持する。
    |
    | 新規登録画面では空文字になる。
    |
    */

    const initialSeriesId =
        seriesSelect.value;


    /*
    |--------------------------------------------------------------------------
    | Series Options
    |--------------------------------------------------------------------------
    */

    const seriesOptions =
        Array.from(seriesSelect.options).map(function (option) {

            return {
                value: option.value,
                label: option.textContent.trim(),
                seriesType:
                    option.dataset.seriesType || ''
            };
        });


    /*
    |--------------------------------------------------------------------------
    | Series Type Mapping
    |--------------------------------------------------------------------------
    */

    const seriesTypeMap = {
        volume: 'book_series',
        quarterly: 'quarterly',
        monthly: 'monthly'
    };


    /*
    |--------------------------------------------------------------------------
    | Update Series Select
    |--------------------------------------------------------------------------
    */

    function updateSeriesSelect(
        preserveCurrentValue = false
    ) {

        const type = productType.value;

        const requiredSeriesType =
            seriesTypeMap[type] || null;

        const currentSeriesId =
            preserveCurrentValue
                ? initialSeriesId
                : '';


        /*
        |--------------------------------------------------------------------------
        | Clear Options
        |--------------------------------------------------------------------------
        */

        seriesSelect.innerHTML = '';


        /*
        |--------------------------------------------------------------------------
        | Book / Software
        |--------------------------------------------------------------------------
        */

        if (!requiredSeriesType) {

            const option =
                document.createElement('option');

            option.value = '';
            option.textContent = 'シリーズなし';

            seriesSelect.appendChild(option);

            seriesSelect.value = '';
            seriesSelect.disabled = true;
            seriesSelect.required = false;

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Volume / Quarterly / Monthly
        |--------------------------------------------------------------------------
        */

        seriesSelect.disabled = false;
        seriesSelect.required = true;


        const placeholder =
            document.createElement('option');

        placeholder.value = '';

        if (type === 'volume') {

            placeholder.textContent =
                '書籍シリーズを選択してください';

        } else if (type === 'quarterly') {

            placeholder.textContent =
                '季刊誌を選択してください';

        } else if (type === 'monthly') {

            placeholder.textContent =
                '月刊誌を選択してください';
        }

        seriesSelect.appendChild(placeholder);


        /*
        |--------------------------------------------------------------------------
        | Matching Series
        |--------------------------------------------------------------------------
        */

        seriesOptions.forEach(function (series) {

            if (
                series.value === ''
                || series.seriesType !== requiredSeriesType
            ) {
                return;
            }

            const option =
                document.createElement('option');

            option.value = series.value;
            option.textContent = series.label;

            option.dataset.seriesType =
                series.seriesType;

            seriesSelect.appendChild(option);
        });


        /*
        |--------------------------------------------------------------------------
        | Restore Existing Series
        |--------------------------------------------------------------------------
        */

        if (
            currentSeriesId !== ''
            && Array.from(seriesSelect.options).some(
                function (option) {
                    return option.value === currentSeriesId;
                }
            )
        ) {
            seriesSelect.value =
                currentSeriesId;
        } else {
            seriesSelect.value = '';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Update Product Fields
    |--------------------------------------------------------------------------
    */

    function updateProductFields(
        preserveCurrentSeries = false
    ) {

        const type = productType.value;


        /*
        |--------------------------------------------------------------------------
        | Series
        |--------------------------------------------------------------------------
        */

        updateSeriesSelect(
            preserveCurrentSeries
        );


        /*
        |--------------------------------------------------------------------------
        | Software
        |--------------------------------------------------------------------------
        */

        if (type === 'software') {

            publicationSection.style.display = 'none';
            softwareSection.style.display = '';
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
        | Book / Volume / Quarterly / Monthly
        |--------------------------------------------------------------------------
        */

        publicationSection.style.display = '';
        softwareSection.style.display = 'none';
        previewPdfArea.style.display = '';

        imageLabel.textContent =
            '表紙・商品画像';

        imageHelp.innerHTML =
            '推奨 600 × 800 px（3:4）<br>' +
            'JPEG / PNG / WebP、最大5MB';
    }


    /*
    |--------------------------------------------------------------------------
    | Product Type Change
    |--------------------------------------------------------------------------
    |
    | 商品種別をユーザーが変更した場合は、
    | 以前のシリーズ選択を引き継がない。
    |
    */

    productType.addEventListener(
        'change',
        function () {
            updateProductFields(false);
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Initial State
    |--------------------------------------------------------------------------
    |
    | 編集画面では現在の series_id を保持する。
    |
    */

    updateProductFields(true);

});