document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
    checkAuthStatus();
});

// Update the cart icon count
async function updateCartCount() {
    try {
        const response = await fetch('api/cart.php?action=view');
        const cart = await response.json();
        
        const countElement = document.getElementById('cart-count');
        if (countElement && cart) {
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
    } catch (error) {
        console.error('Error fetching cart:', error);
    }
}

// Add a product to the cart
async function addToCart(productId, quantity = 1) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    try {
        const response = await fetch('api/cart.php?action=add', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.success) {
            updateCartCount();
            showNotification(result.message, 'success');
        } else {
            showNotification(result.error || 'Failed to add to cart', 'error');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showNotification('Something went wrong.', 'error');
    }
}

// Check if user is logged in to toggle icons
async function checkAuthStatus() {
    try {
        const response = await fetch('api/auth.php?action=status');
        const result = await response.json();
        const userIcon = document.getElementById('user-icon');
        
        if (userIcon) {
            if (result.logged_in) {
                userIcon.setAttribute('href', 'profile.php');
                userIcon.innerHTML = `<i class="fas fa-user-check" title="Logged in as ${result.username}"></i>`;
            } else {
                userIcon.setAttribute('href', 'login.php');
                userIcon.innerHTML = `<i class="far fa-user" title="Login / Register"></i>`;
            }
        }
    } catch (error) {
        console.error('Error checking auth status:', error);
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
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}
