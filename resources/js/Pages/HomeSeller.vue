<script setup>
import { ref, onMounted } from 'vue';
import ProductCard from './components/ProductCard.vue';
import QuickView from './components/QuickView.vue';
import CartDrawer from './components/CartDrawer.vue';
import OrderHistory from './components/OrderHistory.vue';
import { useCart } from './composables/useCart';
import { useOrderHistory } from './composables/useOrderHistory';
import GlobalLoader from './components/GlobalLoader.vue'
import { isTokenInLocalStorage, validateOrRefreshToken } from "../utils";
import { TEMP_PROD } from '../utils/constants';
import { json } from 'react-router-dom';
import { router as Inertia } from '@inertiajs/vue3';


const products = ref([]);

 
const isLoading = ref(false);


const errorMessage = ref(null);


const fetchProducts = async () => {
  isLoading.value = true;
  errorMessage.value = null;

  try {
    const response = await axios.get('/api/produits',  {
      headers : {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${await validateOrRefreshToken()}`
            }
    } );
    products.value = response.data;
  } catch (error) {
    errorMessage.value = 'Erreur lors de la récupération des produits.';
    console.error(error);
  } finally {
    isLoading.value = false;
  }
};

// Appeler fetchProducts au montage du composant
onMounted(() => {
  fetchProducts();
});

const {
  items: cartItems,
  isCartOpen,
  addToCart,
  removeFromCart,
  updateQuantity,
  totalItems,
  totalPrice,
  successMessage,
  errorMessage : errorMess,
  cartLoading
} = useCart();

const {
  orders,
  addOrder,
  ordLoading
} = useOrderHistory();

const selectedProduct = ref(null);
const isQuickViewOpen = ref(false);
const isHistoryOpen = ref(false);

const handleQuickView = (product) => {
  selectedProduct.value = product;
  isQuickViewOpen.value = true;
};

const handleCheckout = () => {
  if (cartItems.value.length) {
    addOrder(cartItems.value, totalPrice.value);
    cartItems.value = [];
    isCartOpen.value = false;
  }
};

const addOrModifyProd = (product, isModif ) => {
  if(isModif) {
    localStorage.setItem(TEMP_PROD, JSON.stringify(product));
  } else localStorage.removeItem(TEMP_PROD);
  Inertia.visit('/add-prod');
}

const deleteProduct = async (productId) => {
    isLoading.value = true;
  try {
    // Effectuer une requête DELETE pour supprimer le produit
    const response = await axios.delete(`/api/produits/${productId}`, {
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${await validateOrRefreshToken()}`, // Inclure le token JWT pour l'authentification
      },
    });

    console.log('Produit supprimé avec succès:', response.data);

    // Mettre à jour l'état local pour retirer le produit de la liste
    products.value = products.value.filter(item => item.product_id !== productId);
    window.location.reload();

  } catch (error) {
    // Gérer les erreurs
    alert('Erreur lors de la suppression du produit.', error);
  }
  isLoading.value = false;
};

</script>

<template>
  <main class="main-container">
    <header class="header">
      <h1>Mes Produits</h1>
      <div class="header-actions">
        <button class="history-btn" @click="isHistoryOpen = true">
          Commandes recus
        </button>
        <button class="cart-btn" @click="addOrModifyProd(null, false)">
          Ajouter
        </button>
      </div>
    </header>

    <section class="products-grid">
      <ProductCard v-for="product in products" :key="product.id" :product="product" 
        @quick-view="handleQuickView(product)" @add-to-cart="async (product) => {
          await addToCart(product);
          
        }" :is-seller="true" @modify-prod="(product) => {
          addOrModifyProd(product, true);
          
        }" @delete-prod="(product) => {
            deleteProduct(product['id']);
        }" />
    </section>

    <QuickView v-if="selectedProduct" :product="selectedProduct" :is-open="isQuickViewOpen"
      @close="isQuickViewOpen = false" @add-to-cart="addToCart" :is-seller="true" @modify-prod="(product) => {
          addOrModifyProd(product, true);
          
        }" />

    <CartDrawer :items="cartItems" :is-open="isCartOpen" @close="isCartOpen = false" @update-quantity="updateQuantity"
      @remove="removeFromCart" @checkout="handleCheckout" />

    <OrderHistory :orders="orders" :is-open="isHistoryOpen" @close="isHistoryOpen = false" />

    <div >
      <GlobalLoader :is-loading="isLoading || cartLoading || ordLoading" />
    </div>
    
  </main>
</template>

<style scoped>


.main-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 3rem;
}

.header h1 {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--text);
}

.header-actions {
  display: flex;
  gap: 1rem;
}

.cart-btn,
.history-btn {
  background: var(--primary);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: var(--radius);
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.cart-btn:hover,
.history-btn:hover {
  background: var(--primary-dark);
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
}

@media (max-width: 640px) {
  .main-container {
    padding: 1rem;
  }

  .header {
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
    text-align: center;
  }

  .header h1 {
    font-size: 1.875rem;
  }

  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1rem;
  }
}

.spinner {
  width: 50px;
  height: 50px;
  border: 5px solid #f3f3f3;
  border-top: 5px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}

.spinner-container {
  margin: 0 auto;
  width: 100%;
}
</style>