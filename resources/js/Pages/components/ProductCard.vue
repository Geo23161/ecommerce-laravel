<script setup>
import { computed } from 'vue';

const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  isAdded : {
    type: Boolean,
    required: false,
  },
  isSeller : {
    type: Boolean,
    required: false,
  }
});

const emit = defineEmits(['quick-view', 'add-to-cart', 'modify-prod', 'delete-prod']);

const formattedPrice = computed(() => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(props.product.price);
});

const discountedPrice = computed(() => {
  if (!props.product.discount) return null;
  const discounted = props.product.price * (1 - props.product.discount / 100);
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(discounted);
});
</script>

<template>
  <div class="product-card">
    <div class="image-container">
      <img :src="product.image" :alt="product.title">
      <div class="overlay">
        <button 
          class="quick-view"
          @click="emit('quick-view', product)"
        >
          Voir le produit
        </button>
      </div>
      <span v-if="product.discount" class="discount-badge">
        -{{ product.discount }}%
      </span>
    </div>
    <div class="content">
      <h3>{{ product.title }}</h3>
      <div class="rating">
        <div class="stars" :style="{ '--rating': product.rating }">★★★★★</div>
        <span>{{ product.rating }}</span>
      </div>
      <p>{{ product.description }}</p>
      <div class="price-container">
        <div class="price" :class="{ 'has-discount': product.discount }">
          <span class="original">{{ formattedPrice }}</span>
          <span v-if="discountedPrice" class="discounted">{{ discountedPrice }}</span>
        </div>
        <div v-if="!isSeller" >
          <button v-if="!isAdded"
          class="add-to-cart"
          @click="emit('add-to-cart', product)"
        >
          Ajouter au panier
        </button>
        <button v-else
          class="add-to-cart-done"
          @click="emit('add-to-cart', product)"
        >
          Ajouté
        </button>
        </div>

      </div>
      <div v-if="isSeller" class="price-container">
        <button
          class="add-to-cart"
          @click="emit('modify-prod', product)"
        >
          Modifier
        </button>
        <button 
          class="add-to-cart-delete"
          @click="emit('delete-prod', product)"
        >
          Supprimer
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.product-card {
  background: var(--background);
  border-radius: var(--radius);
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  box-shadow: var(--shadow);
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
}

.image-container {
  position: relative;
  padding-top: 100%;
  background: #f1f5f9;
  overflow: hidden;
}

.image-container img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.product-card:hover .image-container img {
  transform: scale(1.05);
}

.overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.product-card:hover .overlay {
  opacity: 1;
}

.quick-view {
  background: white;
  color: var(--text);
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 2rem;
  font-weight: 500;
  transform: translateY(20px);
  transition: transform 0.3s ease, background-color 0.3s ease;
  cursor: pointer;
}

.product-card:hover .quick-view {
  transform: translateY(0);
}

.quick-view:hover {
  background: var(--primary);
  color: white;
}

.discount-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: var(--accent);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 2rem;
  font-weight: 600;
  font-size: 0.875rem;
}

.content {
  padding: 1.5rem;
}

h3 {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: var(--text);
}

.rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
}

.stars {
  --percent: calc(var(--rating) / 5 * 100%);
  display: inline-block;
  font-size: 1.25rem;
  font-family: Times;
  line-height: 1;
  background: linear-gradient(90deg, #fbbf24 var(--percent), #e5e7eb var(--percent));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

p {
  color: var(--text-light);
  font-size: 0.875rem;
  margin-bottom: 1.5rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.price-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.price {
  display: flex;
  flex-direction: column;
}

.original {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text);
}

.has-discount .original {
  font-size: 0.875rem;
  text-decoration: line-through;
  color: var(--text-light);
}

.discounted {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--accent);
}

.add-to-cart {
  background: var(--primary);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.add-to-cart-done {
  color: var(--primary);
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.add-to-cart-delete {
  background: red;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.add-to-cart:hover {
  background: var(--primary-dark);
}

@media (max-width: 640px) {
  .content {
    padding: 1rem;
  }

  h3 {
    font-size: 1rem;
  }

  .price-container {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }

  .add-to-cart {
    width: 100%;
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
</style>