const apiUrl = 'http://localhost/scratch/model/inventory.model.php';


// Fetch all items, optionally with a search query
async function getItems(searchQuery = '') {
    const url = searchQuery ? `${apiUrl}?search=${encodeURIComponent(searchQuery)}` : apiUrl;
    const response = await fetch(url);
    const data = await response.json();
    return data;
}

// Fetch items by category
async function getItemsByCategory(category) {
    const response = await fetch(`${apiUrl}?category=${encodeURIComponent(category)}`);
    const data = await response.json();
    return data;
}

// Fetch items by status
async function getItemsByStatus(status) {
    const response = await fetch(`${apiUrl}?status=${encodeURIComponent(status)}`);
    const data = await response.json();
    return data;
}

// Add a new item to the inventory
async function addItem(item) {
    const response = await fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(item)
    });
    const data = await response.json();
    return data;
}

// Update an existing item in the inventory
async function updateItem(itemId, updatedData) {
    const response = await fetch(apiUrl, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            item_id: itemId, 
            ...updatedData
        })
    });
    const data = await response.json();
    return data;
}

// Delete an item from the inventory
async function deleteItem(itemId) {
    const response = await fetch('http://localhost/scratch/model/inventory.model.php', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'  // Ensure the server knows we're sending JSON data
        },
        body: JSON.stringify({
            item_id: itemId  // Send the item_id in the request body as JSON
        })
    });

    const data = await response.json();
    return data;
}

// Example usage
deleteItem(123).then(response => {
    console.log(response);
});



