document.addEventListener('DOMContentLoaded', function () {
    const sourceOptions = document.querySelectorAll('input[name="reviewmanager_configuration[source]"]');
    const csvFileInput = document.querySelector('input[name="reviewmanager_configuration[csv_file]"]').closest('.form-group');

    toggleCsvFileInput();

    sourceOptions.forEach(option => {
        option.addEventListener('change', toggleCsvFileInput);
    });

    function toggleCsvFileInput() {
        const selectedSource = document.querySelector('input[name="reviewmanager_configuration[source]"]:checked').value;
        csvFileInput.style.display = selectedSource === 'csv' ? 'block' : 'none';
    }
});
