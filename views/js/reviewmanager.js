document.addEventListener('DOMContentLoaded', function () {
    const sourceOptions = document.querySelectorAll('input[name="form[source]"]');
    const csvFileInput = document.querySelector('input[name="form[csv_file]"]').closest('.form-group');

    toggleCsvFileInput();

    sourceOptions.forEach(option => {
        option.addEventListener('change', toggleCsvFileInput);
    });

    function toggleCsvFileInput() {
        const selectedSource = document.querySelector('input[name="form[source]"]:checked').value;
        csvFileInput.style.display = selectedSource === 'csv' ? 'flex' : 'none';
    }
});
