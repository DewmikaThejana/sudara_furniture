const categoriesData = [
    { id: 1, name: 'Living Room', description: 'Comfortable and stylish furniture for your living space.', image_url: 'images/living-room.jpg' },
    { id: 2, name: 'Bedroom', description: 'Create your perfect sanctuary with our bedroom collection.', image_url: 'images/bedroom.jpg' },
    { id: 3, name: 'Dining Room', description: 'Gather around beautiful dining tables and chairs.', image_url: 'images/dining-room.jpg' }
];

const productsData = [
    { id: 1, category_id: 1, name: 'Luxe Velvet Sofa', description: 'A stunning coffee-colored velvet sofa with gold-finished legs.', price: 899.99, image_url: 'images/sofa.jpg', stock: 10, category_name: 'Living Room' },
    { id: 2, category_id: 1, name: 'Gold Accent Coffee Table', description: 'Modern glass top coffee table with a geometric gold base.', price: 249.50, image_url: 'images/coffee-table.jpg', stock: 15, category_name: 'Living Room' },
    { id: 3, category_id: 2, name: 'Rich Espresso Bed Frame', description: 'Queen size bed frame in a rich espresso finish with subtle gold accents.', price: 599.00, image_url: 'images/bed.jpg', stock: 8, category_name: 'Bedroom' },
    { id: 4, category_id: 2, name: 'Golden Glow Nightstand', description: 'Elegant nightstand with brass hardware and a deep coffee finish.', price: 129.99, image_url: 'images/nightstand.jpg', stock: 20, category_name: 'Bedroom' },
    { id: 5, category_id: 3, name: 'Walnut Dining Table', description: 'Spacious dining table for six, crafted from rich walnut wood.', price: 649.00, image_url: 'images/dining-table.jpg', stock: 5, category_name: 'Dining Room' },
    { id: 6, category_id: 3, name: 'Velvet Dining Chair', description: 'Set of two plush velvet dining chairs with slender gold legs.', price: 199.99, image_url: 'images/dining-chair.jpg', stock: 24, category_name: 'Dining Room' }
];

// Helper functions to simulate API responses
function getCategories() {
    return new Promise(resolve => setTimeout(() => resolve(categoriesData), 100));
}

function getProducts(categoryId = 0, limit = 50) {
    return new Promise(resolve => {
        setTimeout(() => {
            let filtered = productsData;
            if (categoryId > 0) {
                filtered = productsData.filter(p => p.category_id == categoryId);
            }
            resolve(filtered.slice(0, limit));
        }, 100);
    });
}

function getProduct(id) {
    return new Promise((resolve, reject) => {
        setTimeout(() => {
            const product = productsData.find(p => p.id == id);
            if (product) resolve(product);
            else reject(new Error('Product not found'));
        }, 100);
    });
}
