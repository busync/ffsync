document.addEventListener('DOMContentLoaded', function () {
    const range = document.getElementById('columns-range');
    const valueDisplay = document.getElementById('columns-value');
    const panel = document.getElementById('panel-item');

    function updateColumns() {
        const columns = range.value;
        valueDisplay.textContent = columns;
        panel.style.gridTemplateColumns = `repeat(${columns}, 1fr)`;
    }

    range.addEventListener('input', updateColumns);

    updateColumns();
});