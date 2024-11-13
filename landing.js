
    function searchDestination() {
        var searchTerm = document.getElementById('search').value.toLowerCase();

        if (searchTerm) {
            window.location.href = 'people2.php?destination=' + encodeURIComponent(searchTerm);
        } else {
            alert("Please enter a destination.");
        }

        return false; 
    }
