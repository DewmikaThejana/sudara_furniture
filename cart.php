<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart | Sudara Furniture</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .page-header {
            padding: 8rem 0 4rem;
            text-align: center;
            background-color: var(--clr-bg);
            border-bottom: 1px solid var(--clr-border);
        }

        .cart-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 3rem;
        }

        .cart-items {
            background: var(--clr-surface);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            padding: 2rem;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 1.5rem 0;
            border-bottom: 1px solid var(--clr-border);
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-img {
            width: 100px;
            height: 100px;
            background-color: #f5f0ea;
            border-radius: var(--radius-sm);
            margin-right: 1.5rem;
        }

        .cart-details {
            flex-grow: 1;
        }

        .cart-title {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .cart-price {
            color: var(--clr-primary);
            font-weight: 600;
        }

        .cart-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .qty-input {
            width: 60px;
            padding: 5px;
            border: 1px solid var(--clr-border);
            border-radius: 4px;
            text-align: center;
        }

        .remove-btn {
            background: none;
            border: none;
            color: #d32f2f;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .cart-summary {
            background: var(--clr-surface);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            padding: 2rem;
            height: fit-content;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .summary-total {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--clr-secondary);
            border-top: 1px solid var(--clr-border);
            padding-top: 1rem;
            margin-top: 1rem;
        }

        @media (max-width: 900px) {
            .cart-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="nav-brand">Sudara <span>Furniture</span></a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="products.php">Shop</a>
            </div>
            <div class="nav-icons">
                <a href="login.php" class="nav-icon" id="user-icon"><i class="far fa-user"></i></a>
                <a href="cart.php" class="nav-icon active">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="cart-count">0</span>
                </a>
            </div>
        </div>
    </nav>

    <header class="page-header">
        <div class="container">
            <h1 class="section-title">Your Cart</h1>
        </div>
    </header>

    <section class="section pt-0">
        <div class="container">
            <div class="cart-layout" id="cart-container">
                <div class="cart-items" id="cart-items-list">
                    <p>Loading your cart...</p>
                </div>
                <div class="cart-summary">
                    <h3 style="margin-bottom: 1.5rem; color: var(--clr-secondary);">Order Summary</h3>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal">$0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span id="total">$0.00</span>
                    </div>
                    <button class="btn btn-primary" style="width: 100%; margin-top: 1.5rem;" id="checkout-btn">Proceed
                        to Checkout</button>
                    <p id="auth-warning"
                        style="color: #d32f2f; font-size: 0.9rem; margin-top: 1rem; text-align: center; display: none;">
                        You must be logged in to checkout. <a href="login.php" style="text-decoration: underline;">Login
                            here</a>.</p>
                </div>
            </div>
        </div>
    </section>

    <script src="js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            await loadCart();

            document.getElementById('checkout-btn').addEventListener('click', async () => {
                const btn = document.getElementById('checkout-btn');
                const warningMsg = document.getElementById('auth-warning');

                // First check auth
                const authRes = await fetch('api/auth.php?action=status');
                const authStatus = await authRes.json();

                if (!authStatus.logged_in) {
                    warningMsg.style.display = 'block';
                    return;
                }

                btn.textContent = 'Processing...';
                btn.disabled = true;

                try {
                    const response = await fetch('api/checkout.php', { method: 'POST' });
                    const result = await response.json();

                    if (result.success) {
                        showNotification('Order placed successfully!', 'success');
                        setTimeout(() => {
                            window.location.href = 'index.php';
                        }, 2000);
                    } else {
                        showNotification(result.message || 'Checkout failed', 'error');
                        btn.textContent = 'Proceed to Checkout';
                        btn.disabled = false;
                    }
                } catch (error) {
                    console.error('Checkout error:', error);
                    showNotification('An error occurred during checkout', 'error');
                    btn.textContent = 'Proceed to Checkout';
                    btn.disabled = false;
                }
            });
        });

        async function loadCart() {
            try {
                const cartRes = await fetch('api/cart.php?action=view');
                const cartData = await cartRes.json();

                const container = document.getElementById('cart-items-list');
                const subtotalEl = document.getElementById('subtotal');
                const totalEl = document.getElementById('total');

                if (Object.keys(cartData).length === 0) {
                    container.innerHTML = '<div style="text-align:center; padding: 2rem;"><p style="font-size:1.2rem; color:var(--clr-text-light);">Your cart is empty.</p><a href="products.php" class="btn btn-primary" style="margin-top:1rem;">Continue Shopping</a></div>';
                    document.querySelector('.cart-summary').style.display = 'none';
                    return;
                }

                // Fetch full product details for each item in cart
                const productsRes = await fetch('api/products.php?action=list&limit=100');
                const products = await productsRes.json();

                let html = '';
                let totalAmount = 0;

                for (const [productId, quantity] of Object.entries(cartData)) {
                    const product = products.find(p => p.id == productId);
                    if (product) {
                        const itemTotal = parseFloat(product.price) * quantity;
                        totalAmount += itemTotal;

                        html += `
                            <div class="cart-item" id="item-${productId}">
                                <div class="cart-img" style="background-image: url('${product.image_url}'); background-size: cover; background-position: center;"></div>
                                <div class="cart-details">
                                    <h4 class="cart-title">${product.name}</h4>
                                    <div class="cart-price">$${product.price}</div>
                                </div>
                                <div class="cart-actions">
                                    <input type="number" class="qty-input" value="${quantity}" min="1" max="${product.stock}" onchange="updateQuantity(${productId}, this.value)">
                                    <button class="remove-btn" onclick="removeItem(${productId})"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        `;
                    }
                }

                container.innerHTML = html;
                subtotalEl.textContent = `$${totalAmount.toFixed(2)}`;
                totalEl.textContent = `$${totalAmount.toFixed(2)}`;
                updateCartCount();

            } catch (error) {
                console.error('Error loading cart:', error);
                document.getElementById('cart-items-list').innerHTML = '<p>Error loading cart data.</p>';
            }
        }

        async function updateQuantity(productId, quantity) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);

            try {
                await fetch('api/cart.php?action=update', { method: 'POST', body: formData });
                loadCart(); // Reload everything to get updated totals
            } catch (error) {
                console.error('Error updating quantity:', error);
            }
        }

        async function removeItem(productId) {
            const formData = new FormData();
            formData.append('product_id', productId);

            try {
                await fetch('api/cart.php?action=remove', { method: 'POST', body: formData });
                loadCart(); // Reload everything
            } catch (error) {
                console.error('Error removing item:', error);
            }
        }
    </script>
</body>

</html>