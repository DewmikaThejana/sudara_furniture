// main.js - Static version using localStorage
document.addEventListener('DOMContentLoaded', () => {
    updateCartIcon();
    checkAuthStatus();
});

// User session management
function getUser() {
    const user = localStorage.getItem('sudara_user');
    return user ? JSON.parse(user) : null;
}

function loginUser(username, email) {
    localStorage.setItem('sudara_user', JSON.stringify({ username, email }));
    checkAuthStatus();
}

function logoutUser() {
    localStorage.removeItem('sudara_user');
    checkAuthStatus();
    showNotification('Logged out successfully', 'success');
}

// Cart management
function getCart() {
    const cart = localStorage.getItem('sudara_cart');
    return cart ? JSON.parse(cart) : {};
}

function saveCart(cart) {
    localStorage.setItem('sudara_cart', JSON.stringify(cart));
}

function updateCartIcon() {
    const cart = getCart();
    const countElement = document.getElementById('cart-count');
    if (countElement) {
        let total = 0;
        for (let id in cart) {
            total += cart[id];
        }
        countElement.textContent = total;
        
        // Notification micro-animation
        countElement.style.transform = 'scale(1.3)';
        setTimeout(() => {
            countElement.style.transform = 'scale(1)';
        }, 200);
    }
}

function addToCart(productId, quantity = 1) {
    const cart = getCart();
    if (cart[productId]) {
        cart[productId] += quantity;
    } else {
        cart[productId] = quantity;
    }
    saveCart(cart);
    updateCartIcon();
    showNotification('Product added to cart', 'success');
}

function updateCartQuantity(productId, quantity) {
    const cart = getCart();
    if (quantity <= 0) {
        delete cart[productId];
    } else {
        cart[productId] = quantity;
    }
    saveCart(cart);
    updateCartIcon();
}

function removeFromCart(productId) {
    const cart = getCart();
    delete cart[productId];
    saveCart(cart);
    updateCartIcon();
}

function clearCart() {
    localStorage.removeItem('sudara_cart');
    updateCartIcon();
}

// Check if user is logged in to toggle icons
function checkAuthStatus() {
    const user = getUser();
    const userIcon = document.getElementById('user-icon');
    
    if (userIcon) {
        if (user) {
            // For a static site, we don't have a profile page yet, so we'll just log out clicking it
            userIcon.onclick = (e) => {
                e.preventDefault();
                if(confirm(`Logged in as ${user.username}. Do you want to checkout or logout? Click OK to Logout.`)) {
                    logoutUser();
                }
            };
            userIcon.innerHTML = `<i class="fas fa-user-check" title="Logged in as ${user.username}"></i>`;
        } else {
            userIcon.setAttribute('href', 'login.html');
            userIcon.onclick = null;
            userIcon.innerHTML = `<i class="far fa-user" title="Login / Register"></i>`;
        }
    }
}

// Simple toast notification system
function showNotification(message, type = 'success') {
    const toast = document.createElement('div');
    toast.style.position = 'fixed';
    toast.style.bottom = '20px';
    toast.style.right = '20px';
    toast.style.padding = '15px 25px';
    toast.style.borderRadius = '8px';
    toast.style.color = '#fff';
    toast.style.fontWeight = '500';
    toast.style.zIndex = '9999';
    toast.style.boxShadow = '0 10px 30px rgba(0,0,0,0.1)';
    toast.style.transform = 'translateY(100px)';
    toast.style.opacity = '0';
    toast.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
    
    if (type === 'success') {
        toast.style.backgroundColor = 'var(--clr-primary, #C5A059)';
        toast.innerHTML = `<i class="fas fa-check-circle" style="margin-right: 10px;"></i> ${message}`;
    } else {
        toast.style.backgroundColor = '#e74c3c';
        toast.innerHTML = `<i class="fas fa-exclamation-circle" style="margin-right: 10px;"></i> ${message}`;
    }
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';
    }, 10);
    
    // Animate out and remove
    setTimeout(() => {
        toast.style.transform = 'translateY(100px)';
        toast.style.opacity = '0';
        setTimeout(() => {
            if(document.body.contains(toast)) document.body.removeChild(toast);
        }, 300);
    }, 3000);
}
