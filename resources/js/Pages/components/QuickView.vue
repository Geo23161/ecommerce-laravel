<script setup lang="ts">
import { computed } from 'vue';
import type { Product } from '../types';

const props = defineProps<{
  product: Product;
  isOpen: boolean;
  isSeller : {
    type: Boolean,
    required: false,
  }
}>();

const emit = defineEmits<{
  'close': [];
  'add-to-cart': [product: Product];
  'modify-prod' : [product: Product];
   'delete-prod' : [product: Product];
}>();

const formattedPrice = computed(() => {
  const price = props.product.discount
    ? props.product.price * (1 - props.product.discount / 100)
    : props.product.price;
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(price);
});
</script>

<template>
  <div v-if="isOpen" class="quick-view-overlay" @click="emit('close')">
    <div class="quick-view-modal" @click.stop>
      <button class="close-button" @click="emit('close')">&times;</button>
      
      <div class="modal-content">
        <div class="product-image">
          <img :src="product.image" :alt="product.title">
          <span v-if="product.discount" class="discount-badge">
            -{{ product.discount }}%
          </span>
        </div>
        
        <div class="product-info">
          <h2>{{ product.title }}</h2>
          
          <div class="rating">
            <div class="stars" :style="{ '--rating': product.rating }">★★★★★</div>
            <span>{{ product.rating }}</span>
          </div>
          
          <p class="description">{{ product.description }}</p>
          
          <div class="price">{{ formattedPrice }}</div>
          
          <button v-if="!isSeller" class="add-to-cart" @click="emit('add-to-cart', product)">
            Add to Cart
          </button>
          <button v-else class="add-to-cart" @click="emit('modify-prod', product)">
            Modifier
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.quick-view-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
}

.quick-view-modal {
  background: white;
  border-radius: var(--radius);
  width: 90%;
  max-width: 900px;
  position: relative;
  max-height: 90vh;
  overflow-y: auto;
}

.close-button {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  z-index: 1;
}

.modal-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  padding: 2rem;
}

.product-image {
  position: relative;
}

.product-image img {
  width: 100%;
  height: auto;
  border-radius: var(--radius);
}

.product-info {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.description {
  color: var(--text-light);
  line-height: 1.6;
}

.price {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--primary);
}

.add-to-cart {
  background: var(--primary);
  color: white;
  border: none;
  padding: 1rem;
  border-radius: var(--radius);
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s ease;
}

.add-to-cart:hover {
  background: var(--primary-dark);
}

@media (max-width: 768px) {
  .modal-content {
    grid-template-columns: 1fr;
    gap: 1rem;
    padding: 1rem;
  }
}
</style>