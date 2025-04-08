<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Add Itinerary
        document.getElementById('add-itinerary').addEventListener('click', function () {
            const container = document.getElementById('itinerary-container');
            const textarea = document.createElement('textarea');
            textarea.name = 'itinerary[]';
            textarea.classList.add('form-control', 'mb-2');
            textarea.rows = 2;
            container.appendChild(textarea);
        });

        // Add Include
        document.getElementById('add-include').addEventListener('click', function () {
            const container = document.getElementById('include-container');
            const textarea = document.createElement('textarea');
            textarea.name = 'include[]';
            textarea.classList.add('form-control', 'mb-2');
            textarea.rows = 2;
            container.appendChild(textarea);
        });

        // Add Exclude
        document.getElementById('add-exclude').addEventListener('click', function () {
            const container = document.getElementById('exclude-container');
            const textarea = document.createElement('textarea');
            textarea.name = 'exclude[]';
            textarea.classList.add('form-control', 'mb-2');
            textarea.rows = 2;
            container.appendChild(textarea);
        });

        // Add Available Date
        document.getElementById('add-available-date').addEventListener('click', function () {
            const container = document.getElementById('available-dates-container');
            const input = document.createElement('input');
            input.type = 'date';
            input.name = 'available_dates[]';
            input.classList.add('form-control', 'mb-2');
            container.appendChild(input);
        });
    });
</script>
