<script setup lang="ts">
import { computed } from 'vue';
import type { CartItem } from '../composables/useCart';

const props = defineProps<{
  items: CartItem[];
  isOpen: boolean;
}>();

const emit = defineEmits<{
  'close': [];
  'update-quantity': [productId: number, quantity: number];
  'remove': [productId: number];
  'checkout': [];
}>();

const total = computed(() => {
  return props.items.reduce((sum, item) => {
    const price = item.product.discount
      ? item.product.price * (1 - item.product.discount / 100)
      : item.product.price;
    return sum + price * item.quantity;
  }, 0);
});

const formattedTotal = computed(() => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(total.value);
});
</script>

<template>
  <div 
    class="cart-overlay"
    :class="{ 'is-open': isOpen }"
    @click="emit('close')"
  >
    <div 
      class="cart-drawer"
      :class="{ 'is-open': isOpen }"
      @click.stop
    >
      <div class="cart-header">
        <h2>Mon panier</h2>
        <button class="close-btn" @click="emit('close')">&times;</button>
      </div>

      <div v-if="items.length" class="cart-items">
        <div 
          v-for="item in items" 
          :key="item.product.id"
          class="cart-item"
        >
          <img :src="item.product.image" :alt="item.product.title">
          <div class="item-details">
            <h3>{{ item.product.title }}</h3>
            <div class="quantity-controls">
              <button @click="emit('update-quantity', item.product.id, item.quantity - 1)">-</button>
              <span>{{ item.quantity }}</span>
              <button @click="emit('update-quantity', item.product.id, item.quantity + 1)">+</button>
            </div>
          </div>
          <button 
            class="remove-btn"
            @click="emit('remove', item.product.id)"
          >&times;</button>
        </div>
      </div>
      <div v-else class="empty-cart">
        Le panier est vide
      </div>

      <div v-if="items.length" class="cart-footer">
        <div class="total">
          <span>Total:</span>
          <span>{{ formattedTotal }}</span>
        </div>
        <button 
          class="checkout-btn"
          @click="emit('checkout')"
        >
          Valider
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.cart-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
  z-index: 40;
}

.cart-overlay.is-open {
  opacity: 1;
  visibility: visible;
}

.cart-drawer {
  position: fixed;
  top: 0;
  right: -400px;
  bottom: 0;
  width: 100%;
  max-width: 400px;
  background: white;
  padding: 1.5rem;
  transition: right 0.3s ease;
  display: flex;
  flex-direction: column;
}

.cart-drawer.is-open {
  right: 0;
}

.cart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.close-btn {
  font-size: 1.5rem;
  background: none;
  border: none;
  cursor: pointer;
}

.cart-items {
  flex: 1;
  overflow-y: auto;
}

.cart-item {
  display: flex;
  gap: 1rem;
  padding: 1rem 0;
  border-bottom: 1px solid #e5e7eb;
  position: relative;
}

.cart-item img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 0.5rem;
}

.item-details {
  flex: 1;
}

.quantity-controls {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.quantity-controls button {
  padding: 0.25rem 0.5rem;
  border: 1px solid #e5e7eb;
  background: white;
  border-radius: 0.25rem;
}

.remove-btn {
  position: absolute;
  top: 0.5rem;
  right: 0;
  color: #ef4444;
  background: none;
  border: none;
  font-size: 1.25rem;
  cursor: pointer;
}

.cart-footer {
  margin-top: auto;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
}

.total {
  display: flex;
  justify-content: space-between;
  font-weight: 600;
  margin-bottom: 1rem;
}

.checkout-btn {
  width: 100%;
  padding: 0.75rem;
  background: var(--primary);
  color: white;
  border: none;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
}

.checkout-btn:hover {
  background: var(--primary-dark);
}

.empty-cart {
  text-align: center;
  color: #6b7280;
  padding: 2rem;
}
</style>