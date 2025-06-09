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

      
    });
</script>
