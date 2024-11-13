const apiUrl = 'inventory.php';

// Fetch all items, optionally with a search query
async function getItems(searchQuery = '') {
    const url = searchQuery ? `${apiUrl}?search=${encodeURIComponent(searchQuery)}` : apiUrl;
    const response = await fetch(url);
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
        body: new URLSearchParams({ item_id: itemId, ...updatedData })
    });
    const data = await response.json();
    return data;
}

// Delete an item from the inventory
async function deleteItem(itemId) {
    const response = await fetch(`${apiUrl}?item_id=${itemId}`, {
        method: 'DELETE'
    });
    const data = await response.json();
    return data;
}

// Example usage:

// Get and display items
getItems().then(items => {
    console.log("All Items:", items);
});

// Search for items with "example" in their name
getItems("example").then(items => {
    console.log("Search Results:", items);
});

// Add a new item
addItem({
    item_name: 'New Item',
    brand: 'BrandX',
    category: 'CategoryY',
    quantity: 50
}).then(response => {
    console.log("Add Item Response:", response);
});

// Update an item with ID 1
updateItem(1, {
    item_name: 'Updated Item',
    brand: 'BrandX Updated',
    category: 'CategoryY Updated',
    quantity: 35
}).then(response => {
    console.log("Update Item Response:", response);
});

// Delete an item with ID 1
deleteItem(1).then(response => {
    console.log("Delete Item Response:", response);
});
